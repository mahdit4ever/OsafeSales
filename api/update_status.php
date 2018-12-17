<?php 
include "db.php";

$user = $_REQUEST[user];

$sql = "UPDATE `order`
		SET `status`='Active'
		WHERE `index`='$user'";

if ($connection->query($sql) === TRUE) {
    echo "0";
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
}
$connection->close();

?>