<?php
include "dbconnect.php"; // Connect to your database
error_reporting(0);

// Get and sanitize POST inputs
$pageNumber = isset($_POST['pageNumber']) ? intval($_POST['pageNumber']) : 1;
$benePerPage = isset($_POST['benePerPage']) ? intval($_POST['benePerPage']) : 10;
$payrollNo = mysqli_real_escape_string($conn, $_POST['payrollNo']);
// Calculate offset
$offset = ($pageNumber - 1) * $benePerPage;

// Query to get beneficiaries by payroll number and status, ordered by payroll_id
$sql_bene = "
    SELECT * 
    FROM ect_clean_list 
    WHERE payroll_no = '$payrollNo' 
    AND payroll_id is not null
    ORDER BY payroll_id ASC 
    LIMIT $offset, $benePerPage
";

$result_bene = $conn->query($sql_bene);

if ($result_bene->num_rows > 0) {
    echo "<br><table width='100%' id='bene_list1' class='table table-bordered table-striped'>";
    echo "<thead>
            <tr>
                <th>No</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date of Birth</th>
            </tr>
          </thead><tbody>";

    while ($bene = $result_bene->fetch_assoc()) {
        $dob = $bene['birth_day'] . '-' . $bene['birth_month'] . '-' . $bene['birth_year'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($bene['payroll_id']) . "</td>";
        echo "<td>" . htmlspecialchars($bene['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($bene['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($dob) . "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No beneficiaries found for this payroll.</p>";
}
?>
<!-- Initialize DataTable -->
<script>
$(document).ready(function() {
    $('#bene_list1').DataTable({
        "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
        "iDisplayLength": 10
    });
});
</script>
