<?php 
    header('Content-Type: text/html; charset=UTF-8');
    session_start();
	error_reporting(0);
    include "dbconnect.php";
    ini_set('max_execution_time', 0); 
	$emm=$_SESSION['userid'];
	$date_val = date("Y-m-d H:i:s");
    $payroll_no = $_GET['payroll_no'];
    $page_no = $_GET['page_no'];
    $isLastPage = $_GET['isLastPage'];
	$benePerPage =10;
	
    $search_service = "SELECT * FROM tbl_payroll_list as a 
						INNER JOIN lib_fund_source as b on fund_source=b.id
						WHERE payroll_no='$payroll_no'";
    $result = mysqli_query($conn, $search_service);
				
    if ($row = mysqli_fetch_assoc($result)) {
        $project_title = strtoupper($row["project_title"]);  
        $program = strtoupper($row["program"]); 
        $fund_source = strtoupper($row["fs_name"]); 
        $province = strtoupper($row["province"]); 
        $team_leader = $row["team_leader"]; 
		
		
	
    }
$pagectr=0;
?>

<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>DSWD FO XI - ECT Payroll System</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

<body oncontextmenu="return false">
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
$ctr_page = 1;

?>
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
                    <th width="25%" align="right">&emsp;</th>
                </tr>
                <tr>
                    <th width="25%">&emsp;</th>
                    <th width="50%" align="center"><label>Field Office XI, Davao City</label></th>
                    <th width="25%" align="right">&emsp;</th>
                </tr>
                <tr>
                    <th width="25%">&emsp;</th>
                    <th width="50%" align="center"><h3>CASH ASSISTANCE PAYROLL</h3></th>
                    <th width="25%" align="right">&emsp;</th>
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
					if ($page_no > 0) {
						$pagectr = (($page_no - 1) * 10) + 1;
					} else {
						$pagectr = 1; // Default value if $page_no is invalid
					}

					$ctr=$pagectr;
					$grandtotal=0;
                    // Fetch beneficiaries, 10 at a time (pagination per barangay)
                    $sql_bene = "
					SELECT * 
				FROM ect_clean_list 
				WHERE payroll_no = '$payroll_no' 
				AND payroll_id is not null
				ORDER BY payroll_id ASC 
				LIMIT " . (($page_no - 1) * $benePerPage) . ", $benePerPage
				
					";
                    $result_bene = mysqli_query($conn, $sql_bene);
                    $subtotal = 0;
                    $counter = $pagectr;
                    //$counter = $offset + 1; // Adjust counter to display proper numbering

                    while ($row_bene = mysqli_fetch_assoc($result_bene)) {
                        $lname =str_replace('Ã', 'Ñ', $row_bene['last_name']);
                        $mname =str_replace('Ã', 'Ñ', $row_bene['middle_name']);
						$fname = mb_strtoupper(mb_convert_encoding($row_bene['first_name'], 'UTF-8', 'auto'), 'UTF-8');
                       
                        $amount = $row_bene["amount"]; 
                        $ename = preg_replace('/[^a-zA-Z0-9\s]/', '', $row_bene['extension_name']);
                        $ename = mb_convert_encoding($ename, 'UTF-8', 'auto'); 
                        $ename = mb_strtoupper($ename, 'UTF-8');

                        $payroll_id = $row_bene['payroll_id'];
                        $barangay = strtoupper($row_bene['barangay']);
                        $subtotal += $amount;
						$grandtotal +=$subtotal;
						
						$save_special="UPDATE ect_clean_list set printed_by='$emm', date_printed='$date_val' where payroll_id='$payroll_id' and payroll_no='$payroll_no'";//update query  
						$run2=mysqli_query($conn,$save_special);
                    ?>
                        <tr class="td01">
                            <td class="td01" align="center"><?php echo $ctr; ?></td>
                            <td class="td01" align="center"><?php echo strtoupper($lname); ?></td>
                            <td class="td01" align="center"><?php echo strtoupper($fname . ' ' . $ename); ?></td>
                            <td class="td01" align="center"><?php echo strtoupper($mname); ?></td>
                            <td class="td01" align="center"><?php echo strtoupper($barangay); ?></td>
                            <td class="td01" align="center"><?php echo strtoupper(number_format($amount, 2)); ?></td>
                            <td class="td01" align="left">&emsp;</td>
                            <!--<td class="td01" align="center"><?php echo strtoupper($row_bene['date_validated']); ?></td> -->
                            <td align="center" class="td01">
                                <?php echo strtoupper(date("m-d-Y", strtotime($row_bene['date_validated']))); ?>
                            </td>
                        </tr>
                    <?php $counter++; $ctr++; } ?>
                    <tr>
                        <td colspan="5" align="right"><h3>Subtotal</h3></td>
                        <td align="center"><h3><?php echo number_format($subtotal, 2); ?></h3></td>
                        <td colspan="2">&emsp;</td>
                    </tr>
					<?php 
					if ($isLastPage == 1) {
						$sqlTotal = "SELECT count(no) as beneTotal
							FROM ect_clean_list 
							WHERE payroll_no = '$payroll_no' 
							AND status in ('Claimed','Validated')";
							$resultTotal = mysqli_query($conn, $sqlTotal);
							
							if ($rowTotal = mysqli_fetch_assoc($resultTotal)) {
								$beneTotal = $rowTotal['beneTotal'];
								$grandtotal=$beneTotal * $amount;	
							}
						?>
						<tr>
							<td colspan="4" align="right"><h3>Grand Total</h3></td>
							<td align="center"><h3><?php echo number_format($grandtotal, 2); ?></h3></td>
							<td colspan="2">&emsp;</td>
						</tr>
					<?php $ctr++; }?>
                </tbody>
            </table>
        </div>
<br><br>
<?php 
 
        // Check if it's the last page for this barangay
        if ($isLastPage == 1) {
?>
            <div class="table-blue-fotter"> 
                <table width="100%">
                    <tbody>
                      
                        <!-- Approval section -->
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
<?php
        }
    

?>
   

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
