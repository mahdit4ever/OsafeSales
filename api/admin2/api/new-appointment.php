<?php
date_default_timezone_set("Asia/Kuala_Lumpur"); 
$today = date("Y-m-d H:i:s"); 
include "db.php";

$telemarketer = $_REQUEST[telemarketer];
$sales = $_REQUEST[sales];
$customer = $_REQUEST[customer];
$date = $_REQUEST[date];
$timeStart = $_REQUEST[timeStart];
$timeEnd = $_REQUEST[timeEnd];
$remarks = $_REQUEST[remarks];

$sql = "SELECT * FROM `appointment` WHERE (`time_start` < '$timeEnd' AND `time_end` > '$timeStart' AND `sales`='$sales' AND `date`='$date')
		OR (`time_start` = '$timeStart' AND `time_end` = '$timeEnd' AND `sales`='$sales' AND `date`='$date')";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "1";
} else {
	$sql = "INSERT INTO `appointment` (`telemarketer`, `sales`, `customer`,`date`,`time_start`,`time_end`,`remarks`,`status`) 
	VALUES ('$telemarketer', '$sales','$customer','$date','$timeStart','$timeEnd','$remarks','Active')";
	
	if ($connection->query($sql) === TRUE) {
		echo "0";
	} else {
		echo "2";
	}
	
	$connection->query($sql);
}

$connection->close();
?> 