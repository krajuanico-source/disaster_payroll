<!doctype html>
<?php
session_start();
error_reporting(0);
    include "dbconnect.php";
	
	$emm=$_SESSION['userid'];
	$user_type = $_SESSION['user_type']; // get the user type from the database or session
	if($emm==''||$emm==NULL){
		echo "<script>window.open('index.php','_self')</script>";
	}else{
	$sql1 = "SELECT user_type, empname FROM lib_users WHERE empid='$emm'";
	$result1 = $conn->query($sql1);
	$row1 = $result1->fetch_assoc();
	$user_type=$row1['user_type'];
	$empname   = $row1['empname'];
	
?>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>DSWD FO XI - ECT Payroll System</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />


    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>

    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="assets/css/demo.css" rel="stylesheet" />

    <link href='css/googleapis.css' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />	
	<style>
		body {
		  font-size: 16px;
		}
	</style>
</head>


</script> 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script>
        // Disable right-click context menu
        document.addEventListener("contextmenu", (event) => event.preventDefault());

        // Disable specific key combinations and F12
        document.addEventListener("keydown", (event) => {
            if (
                event.shiftKey || // Disable Shift key
                event.ctrlKey ||  // Disable Ctrl key
                event.key === "F12" // Disable F12
            ) {
                event.preventDefault();
                //alert("This action is disabled!");
            }
        });

        // Prevent Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
        document.addEventListener("keydown", (event) => {
            if (event.ctrlKey && event.shiftKey && ["I", "J", "C"].includes(event.key.toUpperCase())) {
                event.preventDefault();
                //alert("Developer tools shortcuts are disabled!");
            }
        });
    </script>
<body oncontextmenu="return false">

<div class="wrapper">
    <div class="sidebar" data-color="blue" data-image="assets/img/sidebar-6.jpg">

    <!--   you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" -->
	<div class="sidebar-wrapper">
             <div class="logo">
			
                <a href="#" class="simple-text">
                      <img src="images/icons/dswd_logo_white_2.png" style="width:82%; height:82%; margin-left:-3%" alt="..."/>
                    </a>
            </div>
			<ul class="nav">
				<?php if($user_type=='Team Leader'){ ?>
                <li>
					<a href="dashboard.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Dashboard</span></b> </a>
				</li>
				<?php }
				 if($user_type=='Team Leader'){ ?>
                <li class="active">
					<a href="export_excel.php?userid=<?php echo $emm;?>"><i class="pe-7s-file"></i><span><b>Import CSV File</span></b> </a>
				</li>
				<?php }?>
					<input hidden id="user_id" value="<?php echo $emm;?>"/>
				<?php if($user_type=='Team Leader'||$user_type=='Validator'){ ?>
				<li>
					<a href="validation.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Masterlist</span></b> </a>
				</li>
				<?php }
				if($user_type=='Team Leader'||$user_type=='Tagger'){ ?>
				<li>
					<a href="tagger.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Tagging</span></b> </a>
				</li>
                <li>
					<a href="claimed.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>Claimed</span></b> </a>
				</li>
				<?php }
				 if($user_type=='Team Leader'||$user_type=='Payroll'){ ?>
				<li>
					<a href="payroll_list.php?userid=<?php echo $emm;?>"><i class="pe-7s-folder"></i><span><b>List of Payroll</span></b> </a>
				</li>
				<?php }
				 if($user_type=='Team Leader'){ ?>
				 <li>
					<a href="#subPages6" data-toggle="collapse" class="collapsed"><i class="pe-7s-user"></i><span><b>Team Leader</span></b> </a>
					<div id="subPages6" class="collapse ">
						<ul>		
							<li >
								<a href="user.php?userid=<?php echo $emm;?>">
									<i class="pe-7s-angle-right"></i>
									<p>User</p>
								</a>
								<a href="fund_source.php?userid=<?php echo $emm;?>">
									<i class="pe-7s-angle-right"></i>
									<p>Partner</p>
								</a>
								
							</li>
						</ul>						
					</div>
				</li>
				<!-- <li>
					<a href="https://172.31.176.49/ect/public"><i class="pe-7s-folder"></i><span><b>Liquidation</span></b> </a>
				</li> -->
				<?php } ?>
			</ul>			
    </div>
</div>

    <div class="main-panel">
		<nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
						<?= strtoupper(htmlspecialchars($empname ?? '')) ?> - <?=  strtoupper(htmlspecialchars($user_type ?? '')) ?>
					</a>
                </div>
                <div class="collapse navbar-collapse">
                     <ul class="nav navbar-nav navbar-right">
                        
                        <li>
                            <a href="logout.php">
                                <p>Log out</p>
                            </a>
                        </li>
						<li class="separator hidden-lg hidden-md"></li>
                    </ul>
                </div>
            </div>
        </nav>


         <div class="main-panel" style="width:80%">
		<div class="content" style="width:100%">
            <div class="container-fluid">
			
				<form action="import_excel.php" method="post" target="_self" enctype="multipart/form-data">
       
      <div class="table-responsive">  
	  <br>
	  <br>
	  
           <table class="table table-bordered" style="width:80%">		   
				<tr hidden>  
                     <td width="30%"><label  style="font-size: 16px;font-family:Arial Narrow;">Created By</label></td>  
                     <td width="70%" ><input type="text" name="payroll_staff" class="form-control" value="<?php echo $emm;?>" readonly required /></td>   
                </tr> 
				<tr>  
                     <td width="20%"><label  style="font-size: 20px;font-family:Arial Narrow;">PAYROLL DATE</label></td>  
                     <td width="80%" ><input type="date" name="payroll_date" class="form-control" required/> </td>  
                </tr> 
				<tr >  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">SDO</label></td>  
                     <td width="70%" ><input type="text" name="sdo_name" class="form-control" required/></input>
					</td>  
                </tr>				
				<tr hidden>  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Project Name</label></td>  
                     <td width="70%" ><textarea  name="project_name"  class="form-control" placeholder=""  > </textarea> </td> 
				 </tr> 	
				<tr hidden>  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">TEAM LEADER</label></td>  
                     <td width="70%" ><input type="text" name="team_leader" class="form-control" value="<?php echo $emm;?>"/></input>
					</td>  
                </tr> 
		
				<tr hidden>  
                    <td width="30%">
						<label  style="font-size: 20px;font-family:Arial Narrow;">Program</label>
					</td>  
                    <td width="70%">
						 <select class="form-control selectpicker" name="program_title" id="program_title" hidden>
							<option disabled selected>----------</option>
							<?php
								$sql = "SELECT * FROM lib_program WHERE prog_status = 1";
								$result = mysqli_query($conn, $sql);

								while ($row = mysqli_fetch_assoc($result)) {
									echo '<option value="' . $row['id'] . '">' . $row['prog_name'] . '</option>';
								}
							?>
						</select>

					</td> 
				</tr>
				 <tr hidden >  
                     <td width="20%"><label  style="font-size: 20px;font-family:Arial Narrow;">AMOUNT</label></td>  
                     <td width="80%" ><input type="number"  class="form-control" name="amount" placeholder="0.00" value="0"> </td> 
				 </tr> 
				  <tr>  
                     <td width="20%"><label  style="font-size: 20px;font-family:Arial Narrow;">IMPORT FILE</label></td>  
                     <td width="80%" > <input type="file" name="file" accept=".csv" required></td> 
				 </tr> 
				<tr hidden >
					<td colspan="2">
						<div align="center " class=" input_party_wrap2">
							<div class="row ">
								<div class="col-md-3">
									<input type="text" name="fname[]" class="form-control" placeholder="First Name" >
								</div>
								<div class="col-md-3">								
									 <input type="text" name="mname[]" class="form-control" placeholder="Middle Name" >									
								</div>
								<div class="col-md-3">								
									 <input type="text" name="lname[]" class="form-control" placeholder="Last Name" >									
								</div>
								<div class="col-md-2">	
									<select class="form-control"  name='ename[]' id='ename' class="selectpicker" >
										<option disabled selected>Ext. Name</option>
										<option value=""> None</option>
										<option value="Jr">Jr</option>
										<option value="Sr">Sr</option>
										<option value="I">I</option>
										<option value="II">II</option>
										<option value="III">III</option>
										<option value="IV">IV</option>
                                    </select>																
								</div>
						   
								<button style="width:40px" class="btn btn-primary  add_party_button2"  >+</button><br>
							</div>
						</div>
					</td>
				</tr>
           </table>  
      </div>  
		<div class="modal-footer" style="width:80%">  		 
			<center><input type="submit" name="submit" class="btn btn-primary" value="IMPORT LIST"><center>
		</div>
	  </form>
</div>
</div>


</body>

    <!--   Core JS Files   -->
    <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

	<!--  Charts Plugin -->
	<script src="assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

	<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
	<script src="assets/js/demo.js"></script>
	

</html>
 <script>
  $(document).ready(function() {
    var max_fields      = 100; //maximum input boxes allowed
    var wrapper         = $(".input_party_wrap2"); //Fields wrapper
    var add_button      = $(".add_party_button2"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="row"><br>'+
									'<div class="col-md-3">'+
										'<input type="text" name="fname[]" class="form-control" placeholder="First Name" required>'+
									'</div>'+
									'<div class="col-md-3">'+								
										 '<input type="text" name="mname[]" class="form-control" placeholder="Middle Name" >'+									
									'</div>'+
									'<div class="col-md-3">'+								
										 '<input type="text" name="lname[]" class="form-control" placeholder="Last Name" required>'+									
									'</div>'+
									'<div class="col-md-2">'+								
										 '<select class="form-control"  name="ename[]" id="ename" class="selectpicker"  >'+
											'<option disabled selected>Ext. Name</option>'+
											'<option value=""> None</option>'+
											'<option value="Jr">Jr</option>'+
											'<option value="Sr">Sr</option>'+
											'<option value="I">I</option>'+
											'<option value="II">II</option>'+
											'<option value="III">III</option>'+
											'<option value="IV">IV</option>'+
										'</select>'+								
									'</div>'+
									 '<button style="width:40px" class="btn btn-danger  remove_party2"  >-</button>'+
								 '</div>'); //add input box
						//alert($('input[name="mytext1[]"]').length);      
	  }
    });

    $(wrapper).on("click",".remove_party2", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
  </script>
	<?php }?>

