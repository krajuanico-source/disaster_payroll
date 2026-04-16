<?php 
    header('Content-Type: text/html; charset=utf-8');
    session_start();
    include "dbconnect.php";
    error_reporting(0);
    ini_set('max_execution_time', 0); 

    $date_today = date("Y-m-d");
    $payroll_no = $_GET['payroll_no'];

    // Get payroll header info
    $search_service = "SELECT * FROM tbl_payroll_list WHERE payroll_no='$payroll_no'";
    $result = mysqli_query($conn, $search_service);
    if ($row = mysqli_fetch_assoc($result)) {
        $project_title = strtoupper($row["project_title"]);  
        $program = strtoupper($row["program"]); 
        $province = strtoupper($row["province"]); 
        $amount = $row["amount"]; 
    }

    // Fetch all beneficiaries
    $sql = "SELECT * FROM ect_clean_list WHERE payroll_no='$payroll_no' AND STATUS IN ('Validated','Claimed') ORDER BY payroll_id ASC";
    $result_all = mysqli_query($conn, $sql);
    $total_records = mysqli_num_rows($result_all);
    $total_pages = ceil($total_records / 10);

    $page_number = 1;
    $offset = 0;
    $grandtotal = 0;
?>

<html>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>DSWD FO XI - Payroll</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

<body>
<div id="printableArea">
<style>
    .table01 {
        border-collapse: collapse;
        border: 1px solid black;
        font-size: 14px;
    }
    .td01, .th01 {
        font-size: 100%;
        border: 1px solid black;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .th01 {
        font-weight: bold;
    }
    @media print {
        .page-break { display: block; page-break-before: always; }
        .no-print {
            visibility: hidden;
        }
    }
</style>

<?php
while ($offset < $total_records) {
    $sql_bene = "SELECT * FROM ect_clean_list WHERE payroll_no='$payroll_no' AND STATUS IN ('Validated','Claimed') ORDER BY payroll_id ASC LIMIT 10 OFFSET $offset";
    $result_bene = mysqli_query($conn, $sql_bene);

    $subtotal = 0;
    $counter = $offset + 1;
?>
    <div style="page-break-before: always">
        <table width="100%">
            <tr>
                <th width="25%">&emsp;</th>
                <th width="50%" align="center"><label>Republic of the Philippines</label></th>
                <th width="25%" align="right"><label>Page <?php echo $page_number . ' of ' . $total_pages; ?></label></th>
            </tr>
            <tr>
                <th>&emsp;</th>
                <th align="center"><label>DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</label></th>
                <th>&emsp;</th>
            </tr>
            <tr>
                <th>&emsp;</th>
                <th align="center"><label>Field Office XI, Davao City</label></th>
                <th>&emsp;</th>
            </tr>
            <tr>
                <th>&emsp;</th>
                <th align="center"><h3>CASH ASSISTANCE PAYROLL</h3></th>
                <th>&emsp;</th>
            </tr>
        </table>

        <h5>Project Name: <?php echo ucwords(strtolower($project_title)); ?></h5>    
        <table class="table01" width="100%">
            <thead>
                    <tr class="th01">
                        <th width="5%">NO</th>
                        <th width="10%">LAST NAME</th>
                        <th width="10%">FIRST NAME</th>
                        <th width="10%">MIDDLE NAME</th>
                        <th width="26%">BARANGAY</th>
                        <th width="10%">AMOUNT</th>
                        <th width="20%">SIGNATURE</th>
                        <th width="10%">DATE PAID</th>
                    </tr>
            </thead>
            <tbody>
                <?php
                while ($row_bene = mysqli_fetch_assoc($result_bene)) {
                    $lname = strtoupper($row_bene['last_name']);
                    $fname = strtoupper($row_bene['first_name']);
                    $mname =str_replace('Ã', 'Ñ', $row_bene['middle_name']);
                    $barangay = strtoupper($row_bene['barangay']);
                    $ename = preg_replace('/[^a-zA-Z0-9\s]/', '', $row_bene['extension_name']);
                    $subtotal += $amount;
                ?>
                <tr class="td01">
                    <td align="center" class="td01"><?php echo $counter; ?></td>
                    <td align="center" class="td01"><?php echo $lname; ?></td>
                    <td class="td01" align="center"><?php echo strtoupper($fname . ' ' . $ename); ?></td>
                    <td class="td01" align="center"><?php echo strtoupper($mname); ?></td>
                    <td align="center" class="td01"><?php echo $barangay; ?></td>
                    <td align="center" class="td01"><?php echo number_format($amount, 2); ?></td>
                    <td align="left" class="td01">&emsp;</td>
                    <!-- <td align="center" class="td01"><?php echo $row_bene['date_validated']; ?></td> -->
                     <td align="center" class="td01">
                        <?php echo date("m-d-Y", strtotime($row_bene['date_validated'])); ?>
                    </td>
                </tr>
                <?php $counter++; } ?>
                <tr>
                    <td colspan="4" align="right"><h3>Subtotal</h3></td>
                    <td align="center"><h3><?php echo number_format($subtotal, 2); ?></h3></td>
                    <td colspan="2">&emsp;</td>
                </tr>
            </tbody>
        </table>
    </div>
<?php
    $grandtotal += $subtotal;
    $offset += 10;
    $page_number++;
}

// Final footer
?>
<div class="table-blue-fotter"> 
    <table width="100%">
        <tbody>
            <tr>
                <td colspan="4" align="right"><h2>Grand Total</h2></td>
                <td align="right"><h2><?php echo number_format($grandtotal, 2); ?></h2></td>
                <td colspan="2">&emsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="center"><label>Recommending Approval:</label></td>
                <td colspan="4" align="center"><label>Approved By:</label></td>
            </tr>
                        <tr>
                            <td colspan="4" align="center"><b>_________<u>MERLINDA A. PARAGAMAC, RSW MSDA</u>_________</b></td>
                            <td colspan="4" align="center"><b>_________<u>Dir. RHUELO D. ARADANAS, Ph.D</u>_________</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><b>Assistant Regional Director for Operations</b></td>
                            <td colspan="4" align="center"><b>Regional Director</b></td>
                        </tr>
        </tbody>
    </table>
</div>

</div>

<center>
    <button class="no-print" onClick="window.print()">Print Payroll</button>
</center>

<script>
function homeDiv() {
    window.location.href = 'home.php';
}
</script>

</body>
</html>
