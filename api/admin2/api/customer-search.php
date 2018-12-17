<?php 
include "db.php";
$query = $_REQUEST[query];
?>
<?php
    //fetch table rows from mysql db
    //$sql = "SELECT `index`,`title`,date,`message` FROM `message` ORDER BY `index` DESC LIMIT 0,10";
	
	$sql = "SELECT `company_name`, `index` FROM `customer` WHERE `company_name` LIKE '%{$query}%'";
			
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));
?>
<?php
    //create an array
    $emparray[] = array();
    while($row =mysqli_fetch_assoc($result))
    {	
        $emparray[] = array (
            'label' => $row['company_name'],
            'value' => $row['index'],
        );
    }
?>
<?php 
    echo json_encode($emparray);
?>