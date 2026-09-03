<?php
declare(strict_types=1);
session_start();
require_once "dbconnect.php";

header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

$response = ["success" => false, "message" => ""];

/**
 * Returns the ordered list of column names for a table, read live from the
 * database. Used to build an explicit column list instead of relying on
 * SELECT * (which breaks with "column count doesn't match" if the archive
 * table's structure ever drifts from the live table's).
 */
function getColumnNames(mysqli $conn, string $tableName): array
{
    $escapedTable = $conn->real_escape_string($tableName);
    $result = $conn->query("SHOW COLUMNS FROM `$escapedTable`");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

/**
 * Verifies the given password against the Team Leader's stored hash, with
 * basic brute-force throttling (5 wrong attempts -> 60s lockout). Used to
 * gate every delete mode (selected / date / all), not just "delete all".
 *
 * Returns ["ok" => bool, "message" => string, "http_code" => int].
 */
function verifyDeletePassword(mysqli $conn, string $empid, string $password): array
{
    if ($password === '') {
        return ["ok" => false, "message" => "Password is required to delete records.", "http_code" => 400];
    }

    $_SESSION['delete_attempts'] = $_SESSION['delete_attempts'] ?? 0;
    $_SESSION['delete_locked_until'] = $_SESSION['delete_locked_until'] ?? 0;

    if (time() < $_SESSION['delete_locked_until']) {
        $waitSeconds = $_SESSION['delete_locked_until'] - time();
        return ["ok" => false, "message" => "Too many incorrect attempts. Try again in $waitSeconds seconds.", "http_code" => 429];
    }

    $pwStmt = $conn->prepare("SELECT password FROM lib_users WHERE empid = ?");
    $pwStmt->bind_param("s", $empid);
    $pwStmt->execute();
    $pwRow = $pwStmt->get_result()->fetch_assoc();
    $pwStmt->close();

    $storedHash = $pwRow['password'] ?? '';

    if ($storedHash === '' || !password_verify($password, $storedHash)) {
        $_SESSION['delete_attempts']++;
        if ($_SESSION['delete_attempts'] >= 5) {
            $_SESSION['delete_locked_until'] = time() + 60; // 60s lockout
            $_SESSION['delete_attempts'] = 0;
        }
        return ["ok" => false, "message" => "Incorrect password. No records were deleted.", "http_code" => 403];
    }

    // Password confirmed — reset throttle counters
    $_SESSION['delete_attempts'] = 0;
    $_SESSION['delete_locked_until'] = 0;

    return ["ok" => true, "message" => "", "http_code" => 200];
}

/**
 * Moves rows matching $whereSql/$params from the live table into the archive
 * table, inside a transaction. $whereSql must be a valid "WHERE ..." clause
 * (or empty string for "all rows"). Returns the number of rows moved.
 */
function moveRowsToArchive(mysqli $conn, string $whereSql, string $paramTypes, array $params, string $deletedBy): int
{
    $conn->begin_transaction();

    // Build an explicit column list from the live table so the INSERT always
    // matches column-for-column, regardless of how the archive table's
    // structure may have drifted (extra/missing/reordered columns).
    // Exclude any leftover tracking columns (from an earlier is_deleted-based
    // design) so they don't collide with the deleted_at/deleted_by we add below.
    $sourceColumns = getColumnNames($conn, 'ect_served_database');
    $sourceColumns = array_values(array_diff($sourceColumns, ['is_deleted', 'deleted_at', 'deleted_by']));
    if (empty($sourceColumns)) {
        throw new RuntimeException("Could not read columns from ect_served_database.");
    }
    $columnList = '`' . implode('`, `', $sourceColumns) . '`';

    // STEP 1: Copy matching rows into the archive table.
    $insertSql = "INSERT INTO ect_served_database_archived ($columnList, deleted_at, deleted_by)
                   SELECT $columnList, NOW(), ? FROM ect_served_database
                   $whereSql";
    $insertStmt = $conn->prepare($insertSql);
    if ($insertStmt === false) {
        throw new RuntimeException("Failed to prepare archive insert: " . $conn->error);
    }

    $insertTypes = 's' . $paramTypes;
    $insertParams = array_merge([$deletedBy], $params);
    $insertRefs = [];
    foreach ($insertParams as $key => $value) {
        $insertRefs[$key] = &$insertParams[$key];
    }
    array_unshift($insertRefs, $insertTypes);
    call_user_func_array([$insertStmt, 'bind_param'], $insertRefs);
    $insertStmt->execute();
    $copiedCount = $insertStmt->affected_rows;
    $insertStmt->close();

    if ($copiedCount === 0) {
        $conn->rollback();
        return 0;
    }

    // STEP 2: Remove the same rows from the live table.
    // Re-uses the identical WHERE clause/params so the two statements stay in sync.
    $deleteSql = "DELETE FROM ect_served_database $whereSql";
    $deleteStmt = $conn->prepare($deleteSql);
    if ($paramTypes !== '') {
        $deleteRefs = [];
        foreach ($params as $key => $value) {
            $deleteRefs[$key] = &$params[$key];
        }
        array_unshift($deleteRefs, $paramTypes);
        call_user_func_array([$deleteStmt, 'bind_param'], $deleteRefs);
    }
    $deleteStmt->execute();
    $deletedCount = $deleteStmt->affected_rows;
    $deleteStmt->close();

    $conn->commit();

    return $deletedCount;
}

try {
    $emm = $_SESSION['userid'] ?? '';
    $user_type = $_SESSION['user_type'] ?? '';

    if ($emm === '' || $emm === null) {
        http_response_code(401);
        $response["message"] = "Not authenticated.";
        echo json_encode($response);
        exit;
    }

    // Only Team Leader may delete served list records
    if (!in_array($user_type, ['Team Leader'], true)) {
        http_response_code(403);
        $response["message"] = "You are not authorized to delete records.";
        echo json_encode($response);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        $response["message"] = "Invalid request method.";
        echo json_encode($response);
        exit;
    }

    // Every delete mode (selected / date / all) requires the TL's password.
    // Verified up front, before any mode-specific logic runs.
    $password = (string)($_POST['password'] ?? '');
    $pwCheck = verifyDeletePassword($conn, $emm, $password);
    if (!$pwCheck['ok']) {
        http_response_code($pwCheck['http_code']);
        $response["message"] = $pwCheck['message'];
        echo json_encode($response);
        exit;
    }

    $mode = $_POST['mode'] ?? 'selected';
    $whereSql = "";
    $paramTypes = '';
    $params = [];

    switch ($mode) {

        // ---------------------------------------------------
        // MODE: selected — delete specific row(s) by id
        // ---------------------------------------------------
        case 'selected': {
            $rawIds = [];
            if (isset($_POST['ids']) && is_array($_POST['ids'])) {
                $rawIds = $_POST['ids'];
            } elseif (isset($_POST['id'])) {
                $rawIds = [$_POST['id']];
            }

            $ids = [];
            foreach ($rawIds as $rawId) {
                $intId = intval($rawId);
                if ($intId > 0) {
                    $ids[$intId] = $intId;
                }
            }
            $ids = array_values($ids);

            if (empty($ids)) {
                http_response_code(400);
                $response["message"] = "No valid record ID(s) provided.";
                echo json_encode($response);
                exit;
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $whereSql = "WHERE id IN ($placeholders)";
            $paramTypes = str_repeat('i', count($ids));
            $params = $ids;
            break;
        }

        // ---------------------------------------------------
        // MODE: date — delete rows within a date range
        // (inclusive), matched against date_last_served
        // ---------------------------------------------------
        case 'date': {
            $dateFrom = trim($_POST['date_from'] ?? '');
            $dateTo   = trim($_POST['date_to'] ?? '');

            $dateFormatOk = fn($d) => (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);

            if ($dateFrom === '' || $dateTo === '' || !$dateFormatOk($dateFrom) || !$dateFormatOk($dateTo)) {
                http_response_code(400);
                $response["message"] = "Please provide a valid date range (YYYY-MM-DD).";
                echo json_encode($response);
                exit;
            }

            if ($dateFrom > $dateTo) {
                // Swap so a reversed range still works predictably
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }

            $whereSql = "WHERE date_last_served BETWEEN ? AND ?";
            $paramTypes = 'ss';
            $params = [$dateFrom, $dateTo];
            break;
        }

        // ---------------------------------------------------
        // MODE: all — delete every row in the live table
        // ---------------------------------------------------
        case 'all': {
            $whereSql = "";
            $paramTypes = '';
            $params = [];
            break;
        }

        default:
            http_response_code(400);
            $response["message"] = "Unknown delete mode.";
            echo json_encode($response);
            exit;
    }

    try {
        $deletedCount = moveRowsToArchive($conn, $whereSql, $paramTypes, $params, $emm);

        if ($deletedCount > 0) {
            $response["success"] = true;
            $response["deleted_count"] = $deletedCount;
            $response["message"] = $deletedCount === 1
                ? "1 record moved to archive successfully."
                : "$deletedCount records moved to archive successfully.";
        } else {
            $response["success"] = false;
            $response["message"] = "No matching records found.";
        }
    } catch (Throwable $inner) {
        $conn->rollback();
        throw $inner;
    }

    $conn->close();
} catch (Throwable $e) {
    http_response_code(500);
    $response["success"] = false;
    $response["message"] = "Server error: " . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;