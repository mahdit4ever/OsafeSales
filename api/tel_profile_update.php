<?php 
include "db.php";

$phone = $_REQUEST[phone];
$email = $_REQUEST[email];
$user = $_REQUEST[user];

$sql = "UPDATE `telemarketer`
		SET `mobile`='$phone', `email`='$email'
		WHERE `index`='$user'";

if ($connection->query($sql) === TRUE) {
    echo "0";
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
}
$connection->close();

?> 
