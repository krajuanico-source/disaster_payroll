<?php
// Database connection parameters
$hostname = 'localhost';
$username = 'root';
$password = 'root';
$databaseName = 'disaster_db';
$tableName = 'tbl_bene';

// Create a database connection
$conn = new mysqli($hostname, $username, $password, $databaseName);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$payroll_date 		= $_POST['payroll_date'];
$payroll_staff 		= $_POST['payroll_staff'];
$project_name 		= $_POST['project_name'];
$target_province 	= $_POST['target_province'];
$program_title 		= $_POST['program_title'];
$amount 		= $_POST['amount'];
$ctr				=1;

preg_match_all('/\b\w/', $target_province, $matches);
echo $firstLetters = implode('', $matches[0]);

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Get the uploaded file
      /*  $file = $_FILES['file']['tmp_name'];
	
	
				
    // Open and read the CSV file
if (($handle = fopen($file, "r")) !== FALSE) {
        // Loop through each row in the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Sanitize data and prepare for insertion
            $column1 = $conn->real_escape_string($data[5]);
            $column2 = $conn->real_escape_string($data[4]);
            //$column3 = $conn->real_escape_string($data[2]);
			
            // Create and execute the SQL INSERT query
            $sql = "INSERT INTO tbl_bene (fname, lname, barangay,payroll_no) VALUES ('$column1', '$column2', '$series','$resultcheck')";
            if ($conn->query($sql) !== TRUE) {
                echo "Error: " . $conn->error;
            }
			$ctr++;
        }
		
        // Close the CSV file
        fclose($handle);
    }*/
	$sql1 = "INSERT INTO tbl_payroll_list (province, project_title, created_by, date_created, program, amount) VALUES ('$target_province', '$project_name', '$payroll_staff', '$payroll_date', '$program_title', '$amount')";
            if ($conn->query($sql1) !== TRUE) {
                echo "Error: " . $conn->error;
            }
	$sql2 = "SELECT payroll_no FROM tbl_payroll_list";
	$result2 = mysqli_query($conn,$sql2);
	$resultcheck = mysqli_num_rows($result2);
	
	$req_party			= count($_POST['fname']);
	for ($i=0; $i <$req_party; $i++) { 
    $fname=$_POST['fname'][$i];
    $mname=$_POST['mname'][$i];
    $lname=$_POST['lname'][$i];
    $ename=$_POST['ename'][$i];
    $series	 = $ctr."-".$firstLetters;
			$save_newservice="INSERT INTO tbl_bene (fname, mname, lname, barangay,payroll_no) VALUES ('$fname', '$mname', '$lname','$series','$resultcheck')";
			$run3=mysqli_query($conn,$save_newservice);
	$ctr++;
	}
    // Close the database connection
    $conn->close();

    echo "<script>window.location.href='payroll_form.php?payroll_no='+$resultcheck; </script>";
}
?>
