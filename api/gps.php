<?php 
include "db.php";
?>
<?php
	$category = $_REQUEST['c'];
	$page = $_REQUEST['p'];
	if ($page=="1")
	{
	$page="0";
	}
	else
	{
	$page = ($page*6)-6;
	}
    $sql = "SELECT * FROM `listing` WHERE `status` = 'Active' AND `type`='$category' LIMIT $page,6";
	
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));
?>
<?php
    //create an array
    $emparray = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $emparray[] = $row;
    }
?>
<?php 
    echo json_encode($emparray);
	
?>