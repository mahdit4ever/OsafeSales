<?php
date_default_timezone_set("Asia/Kuala_Lumpur"); 
$today = date("Y-m-d H:i:s"); 
include "db.php";


if(get_magic_quotes_gpc()){
	$telemarketer = mysqli_real_escape_string($connection, stripslashes($_REQUEST[telemarketer]));
	$sales = mysqli_real_escape_string($connection, stripslashes($_REQUEST[sales]));
	$customer = mysqli_real_escape_string($connection, stripslashes($_REQUEST[customer]));
	$name = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[name])));
	$contact = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[contact])));
	$product_quantity = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[product_quantity])));
	$discount = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[discount])));
	$total = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[total])));
	$remarks = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[remarks])));
	$address = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[adres])));
	$dev_date = mysqli_real_escape_string($connection, stripslashes(rawurldecode($_REQUEST[deliv_date])));
}
else{
	$telemarketer = mysqli_real_escape_string($connection, $_REQUEST[telemarketer]);
	$sales = mysqli_real_escape_string($connection, $_REQUEST[sales]);
	$customer = mysqli_real_escape_string($connection, $_REQUEST[customer]);
	$name = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[name]));
	$contact = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[contact]));
	$product_quantity = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[product_quantity]));
	$discount = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[discount]));
	$remarks = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[remarks]));
	$address = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[adres]));
	$dev_date = mysqli_real_escape_string($connection, rawurldecode($_REQUEST[deliv_date]));
	
}

$sql = "INSERT INTO `order` (`telemarketer`, `sales`, `date`, `customer`, `name`, `contact`, `product_quantity`, `discount`, `total`, `remarks`,`delivery_address`,`delivery_date`,`status`) 
		VALUES ('$telemarketer','$sales','$today','$customer','$name','$contact', '$product_quantity', '$discount', '$total', '$remarks','$address','$dev_date', 'New')";

if ($connection->query($sql) === TRUE) {
    echo "0";
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
}
$connection->close();
?> 