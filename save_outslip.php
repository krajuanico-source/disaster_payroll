<?php  
date_default_timezone_set('Asia/Manila');
include "php/db_emp.php";
session_start();
$date_today		= date("m-d-Y H:i:s");
$date_applied	=$_POST['date_slip'];
$empid			=$_POST['empid_slip'];
$destination	=$_POST['dest_slip'];
$sliptype		=$_POST['slip_type'];
$purpose		=$_POST['purpose_slip'];
				$sql1 = "SELECT empid FROM tbl_outslip  where empid = '$empid' AND date_applied = '$date_applied' and purpose='$purpose'";
				$result1 = mysqli_query($con,$sql1);
				$resultcheck = mysqli_num_rows($result1);
				if ($resultcheck==0){ 
				$save_newuser="Insert into tbl_outslip(empid,destination,purpose,slip_type,date_applied,status) values
				('$empid','$destination','$purpose','$sliptype','$date_applied','PENDING')";//update query  
				$run2=mysqli_query($con,$save_newuser); 
				 
				$sqld = "SELECT qr_code FROM tbl_employee as e
				inner join dtr_new as d on qr_code=idemployee
				where empid = '$empid' and datetoday ='$date_applied' ";
				$resultd = mysqli_query($con,$sqld);
				$resultcheckd = mysqli_num_rows($resultd);
				if($resultcheckd>0){
					 if($rowd=  mysqli_fetch_assoc($resultd)){	
					$idemployee=$rowd['qr_code'];
					$delete_emp1="update dtr_new set remarks='$sliptype' where  idemployee='$idemployee' and datetoday ='$date_applied'";
					 $run3=mysqli_query($con,$delete_emp1);}
					 
				}
				else{	
					$sqle = "SELECT qr_code FROM tbl_employee where empid = '$empid' ";
				$resulte = mysqli_query($con,$sqle);		
				 if($rowe=  mysqli_fetch_assoc($resulte)){	
					$idemployee=$rowe['qr_code'];	
				//$save_newusere="Insert into dtr_new(idemployee,employee_id,datetoday,remarks) values
				//('$idemployee','$empid','$date_applied','$sliptype')";//update query  
				// $run3=mysqli_query($con,$save_newusere);
				}
			}
		}
			else{
				$delete_emp1="update tbl_outslip set destination='$destination',purpose='$purpose',slip_type='$sliptype',date_applied='$date_applied',status='Pending' 
				where empid='$empid' and date_applied ='$date_applied' and purpose='$purpose'";
				$run2=mysqli_query($con,$delete_emp1);		
				
				$sqld = "SELECT qr_code FROM tbl_employee as e
				inner join dtr_new as d on qr_code=idemployee
				where empid = '$empid' and datetoday ='$date_applied' ";
				$resultd = mysqli_query($con,$sqld);
				$resultcheckd = mysqli_num_rows($resultd);
				if($resultcheckd>0){
					 if($rowd=  mysqli_fetch_assoc($resultd)){	
					$idemployee=$rowd['qr_code'];
					$delete_emp1="update dtr_new set remarks='$sliptype',employee_id='$empid' where  idemployee='$idemployee' and datetoday ='$date_applied'";
					 $run3=mysqli_query($con,$delete_emp1);}
					 
				}
				else{	
					$sqle = "SELECT qr_code FROM tbl_employee where empid = '$empid' ";
				$resulte = mysqli_query($con,$sqle);		
				 if($rowe=  mysqli_fetch_assoc($resulte)){	
					$idemployee=$rowe['qr_code'];	
				$save_newusere="Insert into dtr_new(employee_id,datetoday,remarks) values
				('$empid','$date_applied','$sliptype')";//update query  
				 $run3=mysqli_query($con,$save_newusere);
				}	
			}				
		}
						
if($run2)  
{  
	echo "<script>alert('Succesfully Updated!')</script>";
	echo "<script>window.open('print_outslip.php?id=$empid&id2=$date_applied','_self')</script>"; 
}
else{
	echo "<script>alert('Invalid Details!')</script>";
	error_reporting(E_ALL);
	echo "<script>window.open('main_form.php','_self')</script>";
	
}
?>