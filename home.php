<!doctype html>
<?php
session_start();
//error_reporting(0);
    include "php/db.php";
	
	$emm=$_SESSION['userid'];
	if($emm==''){
		//echo "<script>window.open('index.php?','_self')</script>";
	}
	$sql1= "select empnum from tbl_employment where empid='$emm'";
	$result1 = $con->query($sql1);
	$row1 = $result1->fetch_assoc();
	$id1=$row1['empnum'];
	$sql = "SELECT  distinct e.empnum,a.empid,e.empfname,e.empmname,e.emplname,e.empext,e.empdob,e.empuser,e.emppass,e.emp_telnum,emp_status,
								TIMESTAMPDIFF(YEAR,e.empdob, CURDATE()) AS ageInYears,e.empsex,e.empstatus
								FROM tbl_employment a 
                                inner join employee_info e using (empnum)
                                inner join remit r using (empid)
                                inner join emp_family p using (empid)
                                where a.empnum= '$id1'";
	$result = $con->query($sql);
	$row = $result->fetch_assoc();
	$id=$row['empnum'];
$status=$row['emp_status'];
?>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="images/icons/dswd_icon.jpg">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>DSWD FO XI - Disaster Payroll System</title>

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


    <!--     Fonts and icons     -->
     <link href="css/fontawesome.css" rel="stylesheet">
    <link href='css/googleapis.css' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
</head>


</script> 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<body>

<div class="wrapper">
    <div class="sidebar" data-color="blue" data-image="assets/img/sidebar-6.jpg">

    <!--   you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" -->
	<div class="sidebar-wrapper">
             <div class="logo">
			
                <a href="https://172.26.126.102" class="simple-text">
                      <img src="images/icons/dswd_logo_white_2.png" style="width:82%; height:82%; margin-left:-3%" alt="..."/>
                    </a>
            </div>
			<ul class="nav">
                <li >
					<a href="#subPages6" data-toggle="collapse" class="collapsed"><i class="pe-7s-folder"></i><span><b>Payroll</span></b> </a>
					<div id="subPages6" class="collapse ">
						<ul>		
							<li >
								<a href="home.php?userid=<?php echo $emm;?>">
									<i class="pe-7s-angle-right"></i>
									<p>Data Entry Form</p>
								</a>
								<a href="export_excel.php?userid=<?php echo $emm;?>">
									<i class="pe-7s-angle-right"></i>
									<p>Import CSV FIle</p>
								</a>
							</li>
						</ul>						
					</div>
				</li>
				<li >
					<a href="#subPages7" data-toggle="collapse" class="collapsed"><i class="pe-7s-folder"></i><span><b>List of Payroll</span></b> </a>
					<div id="subPages7" class="collapse ">
						<ul>	
							<li>
								<a href="payroll_list.php?userid=<?php echo $emm;?>">
									<i class="pe-7s-angle-right"></i>
									<p>List of Payroll</p>
								</a>
							</li>
						</ul>						
					</div>
				</li>
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
                    <a class="navbar-brand" href="#"><?php echo strtoupper($row["emplname"]).", ".strtoupper($row["empfname"])." ".strtoupper($row["empmname"]); ?> </a>
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
			
				<form action="import.php" method="post" enctype="multipart/form-data">
       
      <div class="table-responsive">  
	  <br>
	  <br>
	  
           <table class="table table-bordered" style="width:80%">
				<tr>  
                     <td width="30%"><label  style="font-size: 20px;">Payroll Date</label></td>  
                     <td width="70%" ><input type="date" name="payroll_date" class="form-control"  required /> </td>  
                </tr> 
			
				<tr>  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Created By</label></td>  
                     <td width="70%" ><input type="text" name="payroll_staff" class="form-control" value="<?php echo $emm;?>" readonly required /></td>   
                </tr> 
				
				<tr>  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Project Name</label></td>  
                     <td width="70%" ><textarea  name="project_name"  class="form-control" placeholder="" required> </textarea> </td> 
				 </tr> 
				 
				<tr>  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Province</label></td>  
                     <td width="70%" >   <select class="form-control"  name='target_province' id='target_province' class="selectpicker" hidden >
										<option disabled selected >----------</option>
										<option value="DC">Davao City</option>
										<option value="DDS">Davao Del Sur</option>
										<option value="DDN">Davao Del Norte</option>
										<option value="DOCCI">Davao Occidental</option>
										<option value="DDO">Davao De Oro</option>
										<option value="DORIENTAL">Davao Oriental</option>
                                        </select></td> 
				</tr> 
				<tr>  
                    <td width="30%">
						<label  style="font-size: 20px;font-family:Arial Narrow;">Program</label>
					</td>  
                    <td width="70%">
						 <select class="form-control"  name='program_title' id='program_title' class="selectpicker" hidden >
							<option disabled selected >----------</option>
							<option value="CCAM">CCAM</option>
						</select>
					</td> 
				</tr>
				 <tr>  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Amount</label></td>  
                     <td width="70%" ><input type="number"  class="form-control" name="amount" placeholder="Amount" required> </td> 
				 </tr> 
				  <tr hidden >  
                     <td width="30%"><label  style="font-size: 20px;font-family:Arial Narrow;">Import File</label></td>  
                     <td width="70%" > <input type="file" name="file" accept=".csv" ></td> 
				 </tr> 
				<tr>
					<td colspan="2">
						<div align="center " class=" input_party_wrap2">
							<div class="row ">
								<div class="col-md-3">
									<input type="text" name="fname[]" class="form-control" placeholder="First Name" required>
								</div>
								<div class="col-md-3">								
									 <input type="text" name="mname[]" class="form-control" placeholder="Middle Name" >									
								</div>
								<div class="col-md-3">								
									 <input type="text" name="lname[]" class="form-control" placeholder="Last Name" required>									
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
			<center><input type="submit" name="submit" class="btn btn-primary" value="Generate Payroll"><center>
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

    <!--  Google Maps Plugin    -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>

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

