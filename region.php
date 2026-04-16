<?php
include "dbconnect.php";  // Database connection
    if (!empty($_POST["psgc"])) {
        $cid = $_POST["psgc"]; 
		$subValue = substr($cid,0,2);
$query="select * from tbl_province
      where  psgc like '".$subValue."%'";
        $results = mysqli_query($conn, $query);
        foreach ($results as $city){
?>
            <option value="<?php echo $city["psgc"];?>"><?php echo $city["col_province"];?>
    </option>       
<?php
        }
    }
?>



