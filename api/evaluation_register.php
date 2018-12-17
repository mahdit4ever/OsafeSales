<?php
date_default_timezone_set("Asia/Kuala_Lumpur"); 
$today = date("Y-m-d H:i:s"); 
include "db.php";


if(get_magic_quotes_gpc()){
    $sales = mysqli_real_escape_string($connection, stripslashes($_REQUEST[user]));
    $e_status = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[evaluation_status])));
	$pax_number = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[evaluation_pax])));
	$meet_remark = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[evaluation_remark])));
}
else{
    $sales = mysqli_real_escape_string($connection, $_REQUEST[user]);
	$e_status = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[evaluation_status]));
	$pax_number = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[evaluation_pax]));
    $meet_remark = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[evaluation_remark]));
}

$sql = "INSERT INTO `meeting_evaluation_status` (`evaluation_status`, `pax_actual`, `remarks`) 
		VALUES ('$e_status', '$pax_number', '$meet_remark')";

if ($connection->query($sql) === TRUE) {
    echo "0";
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
}
$connection->close();
?>