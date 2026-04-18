<?php 
    error_reporting(0);
    ini_set('max_execution_time', 0); 
    $emm=$_SESSION['userid'];
    $date_val = date("Y-m-d H:i:s");
    $payroll_no = $_GET['payroll_no'];
    $page_no = $_GET['page_no'];
    $isLastPage = $_GET['isLastPage'];
    $benePerPage = 10;
    
    $search_service = "SELECT * FROM tbl_payroll_list as a 
                        INNER JOIN lib_fund_source as b on fund_source=b.id
                        WHERE payroll_no='$payroll_no'";
    $result = mysqli_query($conn, $search_service);
            
    if ($row = mysqli_fetch_assoc($result)) {
        $project_title = strtoupper($row["project_title"]);  
        $program       = strtoupper($row["program"]); 
        $fund_source   = strtoupper($row["fs_name"]); 
        $province      = strtoupper($row["province"]); 
        $team_leader   = $row["team_leader"]; 
        $city_muni     = strtoupper($row["city_muni"]);
        $province     = strtoupper($row["province"]);
    }

    if ($page_no > 0) {
        $pagectr = (($page_no - 1) * 10) + 1;
    } else {
        $pagectr = 1;
    }
    $ctr = $pagectr;
    $grandtotal = 0;

    $sql_bene = "SELECT * FROM ect_clean_list 
                 WHERE payroll_no = '$payroll_no' 
                 AND payroll_id IS NOT NULL
                 ORDER BY payroll_id ASC 
                 LIMIT " . (($page_no - 1) * $benePerPage) . ", $benePerPage";
    $result_bene = mysqli_query($conn, $sql_bene);
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8" />
    <title>DSWD FO XI - CCAM Payroll</title>
    <style>
        .table01 { border-collapse: collapse; border: 1px solid black; font-size: 14px; }
        .td01, .th01 { font-size: 100%; border: 1px solid black; padding-top: 10px; padding-bottom: 10px; }
        .th01 { font-weight: bold; }
        @media print {
            .page-break { display: block; page-break-before: always; }
            .no-print { visibility: hidden; }
        }
            .claimed-row { background-color: rgba(200, 200, 200, 0.2); }
            .claimed-watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-30deg);
                font-size: 18px;
                font-weight: bold;
                color: rgba(255, 0, 0, 0.35);
                white-space: nowrap;
                pointer-events: none;
                z-index: 10;
            }
    </style>
</head>
<body oncontextmenu="return false">
<div id="printableArea">
    <div style="page-break-before: always">
        <table width="100%">
            <tr>
                <th width="25%">&emsp;</th>
                <th width="50%" align="center"><label>Republic of the Philippines</label></th>
                <th width="25%" align="right"><label>Page <?php echo $page_no; ?></label></th>
            </tr>
            <tr>
                <th width="25%">&emsp;</th>
                <th width="50%" align="center"><label>DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</label></th>
                <th width="25%">&emsp;</th>
            </tr>
            <tr>
                <th width="25%">&emsp;</th>
                <th width="50%" align="center"><label>Field Office XI, Davao City</label></th>
                <th width="25%">&emsp;</th>
            </tr>
            <tr>
                <th width="25%">&emsp;</th>
                <th width="50%" align="center"><h3>CASH ASSISTANCE PAYROLL</h3></th>
                <th width="25%">&emsp;</th>
            </tr>
        </table>

        <p style="font-size:12px; text-align:justify;">FOR PAYMENT OF CASH FOR RISK RESILIENCY PROGRAM ON CLIMATE CHANGE ADAPTATION THRU CASH-FOR-TRAINING AND WORK (CFTW) - PROJECT LAWA AT BINHI IN <b><?php echo $province; ?></b> FOR THE PERIOD OF ______________, 2025</p>

        <h4>CITY/MUNICIPALITY: <?php echo $city_muni; ?></h4>

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
                $subtotal = 0;
                while ($row_bene = mysqli_fetch_assoc($result_bene)) {
                    $lname      = str_replace('Ã', 'Ñ', $row_bene['last_name']);
                    $mname      = str_replace('Ã', 'Ñ', $row_bene['middle_name']);
                    $fname      = mb_strtoupper(mb_convert_encoding($row_bene['first_name'], 'UTF-8', 'auto'), 'UTF-8');
                    $ename      = preg_replace('/[^a-zA-Z0-9\s]/', '', $row_bene['extension_name']);
                    $ename      = mb_strtoupper(mb_convert_encoding($ename, 'UTF-8', 'auto'), 'UTF-8');
                    $amount     = $row_bene["amount"];
                    $payroll_id = $row_bene['payroll_id'];
                    $barangay   = strtoupper($row_bene['barangay']);
                    $subtotal  += $amount;

                    // Check if already printed on a previous day
                    $date_printed    = $row_bene['date_printed'];
                    $printed_by      = $row_bene['printed_by'];
                    $already_printed = !empty($date_printed) &&
                                    !empty($printed_by) &&
                                    date('Y-m-d', strtotime($date_printed)) < date('Y-m-d');

                    $save_special = "UPDATE ect_clean_list SET printed_by='$emm', date_printed='$date_val' WHERE payroll_id='$payroll_id' AND payroll_no='$payroll_no'";
                    mysqli_query($conn, $save_special);
                ?>
                    <tr class="td01 <?php echo $already_printed ? 'claimed-row' : ''; ?>">
                        <td class="td01" align="center"><?php echo $ctr; ?></td>
                        <td class="td01" align="center"><?php echo strtoupper($lname); ?></td>
                        <td class="td01" align="center"><?php echo strtoupper($fname . ' ' . $ename); ?></td>
                        <td class="td01" align="center"><?php echo strtoupper($mname); ?></td>
                        <td class="td01" align="center"><?php echo $barangay; ?></td>
                        <td class="td01" align="center"><?php echo number_format($amount, 2); ?></td>
                        <td class="td01" style="position:relative;" align="center">
                            <?php if ($already_printed) : ?>
                                <span class="claimed-watermark">CLAIMED</span>
                            <?php endif; ?>
                            &emsp;
                        </td>
                        <td class="td01">&emsp;</td>
                    </tr>
                <?php $ctr++; } ?>
                <tr>
                    <td colspan="5" align="right"><h3>Subtotal</h3></td>
                    <td align="center"><h3><?php echo number_format($subtotal, 2); ?></h3></td>
                    <td colspan="2">&emsp;</td>
                </tr>
                <?php if ($isLastPage == 1) {
                    $sqlTotal = "SELECT count(no) as beneTotal FROM ect_clean_list WHERE payroll_no='$payroll_no' AND status IN ('Claimed','Validated')";
                    $resultTotal = mysqli_query($conn, $sqlTotal);
                    if ($rowTotal = mysqli_fetch_assoc($resultTotal)) {
                        $beneTotal  = $rowTotal['beneTotal'];
                        $grandtotal = $beneTotal * $amount;
                    }
                ?>
                <tr>
                    <td colspan="4" align="right"><h3>Grand Total</h3></td>
                    <td align="center"><h3><?php echo number_format($grandtotal, 2); ?></h3></td>
                    <td colspan="2">&emsp;</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <br><br>

    <?php if ($isLastPage == 1) : ?>
    <div class="table-blue-footer">
        <table width="100%">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><label>Recommending Approval:</label></td>
                    <td colspan="4" align="center"><label>Approved By:</label></td>
                </tr>
                <tr>
                    <!-- MODIFY SIGNATORIES HERE FOR CCAM -->
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
    <?php endif; ?>

</div>
<center>
    <button class="no-print" onClick="window.print()">Print Payroll</button>
</center>
</body>
</html>