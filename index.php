<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>DSWD FO XI - ECT Payroll System</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
	<link rel="stylesheet" href="css/maxdn.css">
<!--===============================================================================================-->  
	<script src="js/javascript1.js"></script>
	<script src="js/javascript2.js"></script>
	<style>
		body {
			background-image: url('images/bg.jpg');
			background-size: cover;
			background-repeat: no-repeat;
			background-position: center;
		}
	</style>
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form"  role="form" method="post" action="index.php">
					
					<span class="login100-form-title p-b-48">
						<div class="logo text-center"><img src="images/icons/logo-dswd.png" style="width: 100%" alt="DSWD Logo"></div>
					</span>

					<div class="wrap-input100 validate-input" >
						<input class="input100" type="text" name="empid" id="empid">
						<span class="focus-input100" data-placeholder="Employee ID" ></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
						<input class="input100" type="password" name="pass">
						<span class="focus-input100" data-placeholder="Password"></span>
					</div>
					
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" type="submit"  name="login" >
								Login
							</button>
						</div>
					</div>

					<div class="text-center p-t-115">
						<span class="txt1">
							<a name="add_data" hidden onclick=" myFunction()" id="add_data">Forgot Password?
						</span></a>
					</div>
				</form>
			</div>
		</div>
	</div>
	

	<script src="assets/js/main.js"></script>

</body>
</html>

<?php  
error_reporting(0);
date_default_timezone_set('Asia/Manila');
$date_today = date("Y-m-d H:i:s");
$date_today1 = date("Y-m-d");
include("dbconnect.php"); 

if (isset($_POST['login'])){ 		
	$empid=$_POST['empid']; 
	$user_pass=utf8_encode($_POST['pass']);  

		$sql1 = "SELECT * from lib_users where empid ='$empid' and emppassword = '$user_pass'";
		$result1 = mysqli_query($conn,$sql1);
		
		if($row1=  mysqli_fetch_assoc($result1)){ 	
			$user_type = $row1['user_type'];
			
			// Generate a token
			$token = bin2hex(random_bytes(32));

			// Save the generated token to the lib_users table
			$sql_update_token = "UPDATE lib_users SET token = '$token' WHERE empid = '$empid' ";
			$execute = mysqli_query($conn, $sql_update_token);
			
			$_SESSION['token'] = $token; // token session
			$_SESSION['userid']		= $empid;
			$_SESSION['user_type']	= $user_type;
			$action = 'Login ' . $empid;
			if($user_type=='Validator'){
				echo "<script>window.open('validation.php?userid=$empid','_self')</script>";
			}elseif($user_type=='Team Leader'){
				echo "<script>window.open('dashboard.php?userid=$empid','_self')</script>";
			}elseif($user_type=='Tagger'){
				echo "<script>window.open('tagger.php?userid=$empid','_self')</script>";
			}elseif($user_type=='Payroll'){
				echo "<script>window.open('payroll_list.php?userid=$empid','_self')</script>";
			}elseif($user_type=='GO'){
				echo "<script>window.open('validation.php?userid=$empid','_self')</script>";
			}else{				
				echo "<script>alert(Invalid Usertype!')</script>";
				}
			$sql1 = "SELECT MAX(id) as ctr_audit FROM tbl_audit_log";
				$result1 = $conn->query($sql1);
				$row1 = $result1->fetch_assoc();
				$ctr_audit = $row1['ctr_audit'];

				// Increment the audit log ID if audit_no is not empty
				if ($audit_no <> '' || $audit_no <> null) {
					$ctr_audit = $audit_no;
				} else {
					$ctr_audit = $ctr_audit + 1;
					// Save the audit log
					
				}

			$save_to_dtr1 = "INSERT INTO tbl_audit_log (description, updated_by, date_updated, audit_no) VALUES ('$action', '$empid', '$date_today', '$ctr_audit')";
			$rundtr1 = mysqli_query($conn, $save_to_dtr1);				
			}
		else{
			echo "<script>alert('Invalid Credentials!');</script>";
		}
	}
?>
