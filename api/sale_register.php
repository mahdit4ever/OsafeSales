<?php
date_default_timezone_set("Asia/Kuala_Lumpur"); 
$today = date("Y-m-d H:i:s"); 
include "db.php";





$sql = "INSERT INTO `order` (`telemarketer`, `sales_id`, `date`, `customer`, `name`, `contact`, `product_quantity`, `discount`, `total`, `remarks`,`delivery_address`,`delivery_date`,`status`,`color_1`,`color_2`,`appoinment_id`) 
		VALUES ('0','$_REQUEST[sales]','$today','$_REQUEST[appoinment_id]','$_REQUEST[name]','$_REQUEST[contact]', '$_REQUEST[product_quantity]', '$_REQUEST[discount]', '$_REQUEST[total]', '$_REQUEST[remarks]','$_REQUEST[adres]','$_REQUEST[deliv_date]', 'new','$_REQUEST[color_1_1]','$_REQUEST[color_1_2]','$_REQUEST[appoinment_id]')";


if ($connection->query($sql) === TRUE) {
    echo "0";
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
}

if ($_REQUEST['product_quantity2']<>"")
{
$sql = "INSERT INTO `order` (`telemarketer`, `sales_id`, `date`, `customer`, `name`, `contact`, `product_quantity`, `discount`, `total`, `remarks`,`delivery_address`,`delivery_date`,`status`,`color_1`,`color_2`,`appoinment_id`) ;
		VALUES ('0','$_REQUEST[sales]','$today','$_REQUEST[appoinment_id]','$_REQUEST[name]','$_REQUEST[contact]', '$_REQUEST[product_quantity2]', '$_REQUEST[discount]', '$_REQUEST[total]', '$_REQUEST[remarks]','$_REQUEST[adres]','$_REQUEST[deliv_date]', 'new','$_REQUEST[color_2_1]','$_REQUEST[color_2_2]','$_REQUEST[appoinment_id]')";
$connection->query($sql);
}
if ($_REQUEST['product_quantity3']<>"")
{
$sql = "INSERT INTO `order` (`telemarketer`, `sales_id`, `date`, `customer`, `name`, `contact`, `product_quantity`, `discount`, `total`, `remarks`,`delivery_address`,`delivery_date`,`status`,`color_1`,`color_2`,`appoinment_id`) ;
		VALUES ('0','$_REQUEST[sales]','$today','$_REQUEST[appoinment_id]','$_REQUEST[name]','$_REQUEST[contact]', '$_REQUEST[product_quantity3]', '$_REQUEST[discount]', '$_REQUEST[total]', '$_REQUEST[remarks]','$_REQUEST[adres]','$_REQUEST[deliv_date]', 'new','$_REQUEST[color_3_1]','$_REQUEST[color_3_2]','$_REQUEST[appoinment_id]')";
$connection->query($sql);
}
if ($_REQUEST['product_quantity4']<>"")
{
$sql = "INSERT INTO `order` (`telemarketer`, `sales_id`, `date`, `customer`, `name`, `contact`, `product_quantity`, `discount`, `total`, `remarks`,`delivery_address`,`delivery_date`,`status`,`color_1`,`color_2`,`appoinment_id`) ;
		VALUES ('0','$_REQUEST[sales]','$today','$_REQUEST[appoinment_id]','$_REQUEST[name]','$_REQUEST[contact]', '$_REQUEST[product_quantity4]', '$_REQUEST[discount]', '$_REQUEST[total]', '$_REQUEST[remarks]','$_REQUEST[adres]','$_REQUEST[deliv_date]', 'new','$_REQUEST[color_4_1]','$_REQUEST[color_4_2]','$_REQUEST[appoinment_id]')";
$connection->query($sql);
}
if ($_REQUEST['product_quantity5']<>"")
{
$sql = "INSERT INTO `order` (`telemarketer`, `sales_id`, `date`, `customer`, `name`, `contact`, `product_quantity`, `discount`, `total`, `remarks`,`delivery_address`,`delivery_date`,`status`,`color_1`,`color_2`,`appoinment_id`) ;
		VALUES ('0','$_REQUEST[sales]','$today','$_REQUEST[appoinment_id]','$_REQUEST[name]','$_REQUEST[contact]', '$_REQUEST[product_quantity5]', '$_REQUEST[discount]', '$_REQUEST[total]', '$_REQUEST[remarks]','$_REQUEST[adres]','$_REQUEST[deliv_date]', 'new','$_REQUEST[color_5_1]','$_REQUEST[color_5_2]','$_REQUEST[appoinment_id]')";
$connection->query($sql);
}


$connection->close();
?> 