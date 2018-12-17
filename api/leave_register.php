<?php
date_default_timezone_set("Asia/Kuala_Lumpur"); 
include "db.php";


if(get_magic_quotes_gpc()){
	$sales = mysqli_real_escape_string($connection, stripslashes($_REQUEST[sales]));
	$date_from = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[date_from])));
	$date_to = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[date_to])));
}
else{
	$sales = mysqli_real_escape_string($connection, $_REQUEST[sales]);
	$date_from = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[date_from]));
	$date_to = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[date_to]));
}

$sql = "INSERT INTO `leave` (`sales`, `date_from`,`date_to`,`status`) 
		VALUES ('$sales','$date_from','$date_to','Pending')";

if ($connection->query($sql) === TRUE) {
    echo "0";
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
}
$connection->close();
?> 