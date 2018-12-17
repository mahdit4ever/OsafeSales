<?php 
include "db.php";
?>
<?php
    //fetch table rows from mysql db
    $sql = "SELECT * FROM `color`";
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
    echo json_encode($emparray);
?>