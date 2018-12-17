<?php 
include "db.php";

if(get_magic_quotes_gpc()){
	$page = mysqli_real_escape_string($connection, stripslashes($_REQUEST[page]));
	$sales = mysqli_real_escape_string($connection, stripslashes($_REQUEST[sales]));
	
}
else{
	$page = mysqli_real_escape_string($connection, $_REQUEST[page]);
	$sales = mysqli_real_escape_string($connection, $_REQUEST[sales]);
	
}

$page = $page - 1;
$pageStart = $page * 10;
$pageEnd = $pageStart + 10;

?>
<?php
	
	$sql = "SELECT a.*, 
	               z.name 
	        FROM `order` AS a 
	        INNER JOIN `customer_appointment` AS z 
	        ON a.customer_appointment=z.index 
	        WHERE `sales`='$sales' 
	        ORDER BY `index` DESC";

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
	$temp = htmlspecialchars(json_encode($emparray), ENT_NOQUOTES);
    echo str_replace(array("\\r\\n","\\r","\\n"),"<br/>",$temp);
   
?>