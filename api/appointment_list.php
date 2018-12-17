<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db.php";

$user = $_REQUEST['user'];

?>
<?php
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `customer_appointment` WHERE `sales`='$user' ORDER BY `index` DESC LIMIT 0,10";
	//echo $sql;
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));
?>
<?php
    //create an array
    $emparray[] = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $emparray[] = $row;
    }
?>
<?php 
   echo json_encode($emparray);
?>