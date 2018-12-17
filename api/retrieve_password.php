<?php 
include "db.php";

if(get_magic_quotes_gpc()){
	$email = mysqli_real_escape_string($connection, stripslashes($_REQUEST[email]));
}
else{
	$email = mysqli_real_escape_string($connection, $_REQUEST[email]);
}
?>
<?php
	
	$sql = "SELECT `password`, `mobile`, `email` FROM `sales` WHERE `email` = '$email' LIMIT 0,1";
	
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