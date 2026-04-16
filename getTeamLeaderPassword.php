<?php
// Database connection
include 'dbconnect.php';

date_default_timezone_set('Asia/Manila');
$currentDateTime = date("Y-m-d H:i:s");

// Get query parameters securely
$enteredPassword    = $_GET['password'] ?? '';
$beneId                     = $_GET['beneId'] ?? '';
$teamLeader             = $_GET['teamLeader'] ?? '';
$userType                  = 'Team Leader';
$actionDescription     = "Unclaimed the Status for " . htmlspecialchars($beneId);

// Fetch hashed password first
$sql = "SELECT empid, emppassword FROM lib_users WHERE emppassword = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $enteredPassword);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userRow = $result->fetch_assoc();
    $empid = $userRow['empid'];


    // Check if this user is a team leader
    $sqlTeamCheck = "SELECT empid FROM lib_users WHERE team_leader = ?";
    $stmtTeam = $conn->prepare($sqlTeamCheck);
    $stmtTeam->bind_param("s", $empid);
    $stmtTeam->execute();
    $resultTeam = $stmtTeam->get_result();

    if ($resultTeam->num_rows > 0) {
        // Get the next audit number
        $resultAudit = $conn->query("SELECT MAX(id) AS max_id FROM tbl_audit_log");
        $auditRow = $resultAudit->fetch_assoc();
        $nextAuditId = $auditRow['max_id'] + 1;

        // Update beneficiary status
        $updateStatus = $conn->prepare("UPDATE `ect_clean_list` SET status = 'Validated' WHERE id = ?");
        $updateStatus->bind_param("s", $beneId);

        if ($updateStatus->execute()) {
            // Insert audit log
            $insertAudit = $conn->prepare("INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) VALUES (?, ?, ?, ?)");
            $insertAudit->bind_param("sssi", $actionDescription, $empid, $currentDateTime, $nextAuditId);
            $insertAudit->execute();

            echo "Ok";
        } else {
            echo "Error updating status: " . $conn->error;
        }
    } else {
        echo json_encode(["error" => "Team Leader not found"]);
    }
} else {
    echo json_encode(["error" => "Incorrect password. Please try again."]);
}

$conn->close();
?>
