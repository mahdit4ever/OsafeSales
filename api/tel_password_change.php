<?php 
include "db.php";

$password = $_REQUEST[password];
$user = $_REQUEST[user];

$sql = "UPDATE `telemarketer`
		SET `password`='$password'
		WHERE `index`='$user'";

if ($connection->query($sql) === TRUE) {
    echo "0";
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
}
$connection->close();

?> 
