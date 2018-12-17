<?php
date_default_timezone_set("Asia/Kuala_Lumpur"); 
$today = date("Y-m-d H:i:s"); 
include "db.php";

if(get_magic_quotes_gpc()){
	$email = mysqli_real_escape_string($connection, stripslashes($_REQUEST[email]));
	$password = mysqli_real_escape_string($connection, stripslashes($_REQUEST[password]));
}
else{
	$email = mysqli_real_escape_string($connection, $_REQUEST[email]);
	$password = mysqli_real_escape_string($connection, $_REQUEST[password]);
}
?>
<?php
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `telemarketer` WHERE `email` = '$email' AND `password`='$password' LIMIT 0,1";
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));
?>
<?php
    //create an array
    $emparray[] = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $emparray[] = $row;
    }
?>
<?php 
   $temp = htmlspecialchars(json_encode($emparray), ENT_NOQUOTES);
    echo str_replace(array("\\r\\n","\\r","\\n"),"<br/>",$temp);
?>