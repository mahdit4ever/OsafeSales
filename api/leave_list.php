<?php 
include "db.php";


if(get_magic_quotes_gpc()){
	$page = mysqli_real_escape_string($connection, stripslashes($_REQUEST[page]));
	$user = mysqli_real_escape_string($connection, stripslashes($_REQUEST[user]));
}
else{
	$page = mysqli_real_escape_string($connection, $_REQUEST[page]);
	$user = mysqli_real_escape_string($connection, $_REQUEST[user]);
}

$page = $page - 1;
$pageStart = $page * 10;
$pageEnd = $pageStart + 10;
?>
<?php
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `leave` WHERE `sales`='$user' ORDER BY `index` DESC LIMIT $pageStart,$pageEnd";
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