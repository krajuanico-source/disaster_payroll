<?php
include "dbconnect.php";  // Database connection
    if (!empty($_POST["psgc"])) {
        $cid = $_POST["psgc"]; 
        $subValue = substr($cid,0,6);
$query="select * from tbl_brgy
      where  psgc like '".$subValue."%'";
        $results = mysqli_query($conn, $query);
        foreach ($results as $city){
?>
            <option value="<?php echo $city["psgc"];?>"><?php echo $city["col_brgy"];?>
    </option>       
<?php
        }
    }
?>