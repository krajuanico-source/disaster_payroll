<?php
declare(strict_types=1);
require_once "dbconnect.php";

// -------------------------
// HEADERS & CONFIG
// -------------------------
header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors', '0'); // hide PHP notices/warnings from JSON output
error_reporting(E_ALL);

// -------------------------
// START OUTPUT BUFFER (to prevent stray output)
// -------------------------
ob_start();

$response = [];

try {
    // 1. READ DATATABLE PARAMS
    $draw   = intval($_POST['draw'] ?? 1);
    $start  = intval($_POST['start'] ?? 0);
    $length = intval($_POST['length'] ?? 10);
    $search = trim($_POST['search']['value'] ?? '');

    // 2. SEARCH FILTER
    $where = "";
    $params = [];
    $paramTypes = '';

    if ($search !== '') {
        $where = "WHERE CONCAT_WS(' ', 
                    CONVERT(first_name USING utf8mb4), 
                    CONVERT(middle_name USING utf8mb4), 
                    CONVERT(last_name USING utf8mb4), 
                    CONVERT(IFNULL(extension_name, '') USING utf8mb4)
                 ) COLLATE utf8mb4_general_ci LIKE ?";
        $params[] = "%$search%";
        $paramTypes .= "s";
    }
    
    // 3. TOTAL RECORD COUNT
    $totalQuery = "SELECT COUNT(*) AS total FROM ect_served_database";
    $totalResult = $conn->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'] ?? 0;

    // 4. FILTERED RECORD COUNT
    if ($where !== '') {
        $countSql = "SELECT COUNT(*) AS total FROM ect_served_database $where";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param($paramTypes, ...$params);
        $countStmt->execute();
        $countResult = $countStmt->get_result()->fetch_assoc();
        $filteredRecords = $countResult['total'] ?? 0;
        $countStmt->close();
    } else {
        $filteredRecords = $totalRecords;
    }

    // 5. FETCH PAGINATED DATA
    $dataSql = "
        SELECT 
            id,
            CONCAT_WS(' ', first_name, middle_name, last_name, IFNULL(extension_name, '')) AS fullname,
            CONCAT(LPAD(birth_month,2,'0'), '-', LPAD(birth_day,2,'0'), '-', birth_year) AS dob,
            province,
            city_municipality,date_last_served
        FROM ect_served_database
        $where
        ORDER BY id ASC
        LIMIT ?, ?
    ";

    $dataStmt = $conn->prepare($dataSql);

    // Bind parameters
    if ($where !== '') {
        $paramTypes .= "ii";
        $params[] = $start;
        $params[] = $length;
        $dataStmt->bind_param($paramTypes, ...$params);
    } else {
        $dataStmt->bind_param("ii", $start, $length);
    }

    $dataStmt->execute();
    $result = $dataStmt->get_result();

    // 6. BUILD DATA ARRAY
    $data = [];
    $ctr = $start + 1;

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $ctr++,
            strtoupper($row['fullname']),
            $row['dob'],
            htmlspecialchars($row['province']),
            htmlspecialchars($row['city_municipality']),
            $row['date_last_served']
        ];
    }

    $dataStmt->close();
    $conn->close();

    // 7. BUILD JSON RESPONSE
    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ];
} catch (Throwable $e) {
    // Catch any fatal error and return JSON-friendly message
    $response = [
        "error" => "Server error: " . $e->getMessage()
    ];
}

// -------------------------
// CLEAN OUTPUT
// -------------------------
ob_end_clean(); // clear any accidental output
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
