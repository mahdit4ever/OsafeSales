<?php 
include "db.php";
?>
<?php

	$sql ="DELETE FROM `order` WHERE `index` = '$_REQUEST[cid]'";
	$connection->query($sql);
if ($connection->query($sql) === TRUE) {
    echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . $conn->error;
}
?>