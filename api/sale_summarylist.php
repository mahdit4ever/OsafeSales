<?php 
include "db.php";

if(get_magic_quotes_gpc()){
	$page = mysqli_real_escape_string($connection, stripslashes($_REQUEST[page]));
	$sales = mysqli_real_escape_string($connection, stripslashes($_REQUEST[sales]));
	
}
else{
	$page = mysqli_real_escape_string($connection, $_REQUEST[page]);
	$sales = mysqli_real_escape_string($connection, $_REQUEST[sales]);
	
}

$page = $page - 1;
$pageStart = $page * 30;
$pageEnd = $pageStart + 30;

?>
<?php
	
    //$sql = "SELECT a.*, z.company_name FROM `order` AS a INNER JOIN `customer` AS z ON a.customer=z.index WHERE `sales`='$sales' ORDER BY `index` DESC LIMIT $pageStart,$pageEnd";
    //$sql ="SELECT * FROM customer_appointment WHERE `sales`='$sales'";
	

	$sql = "SELECT a.index, a.name,a.date, COUNT(b.status) AS total, SUM(b.status='delivered') AS delivered, SUM(b.status='pending' OR b.status='new') AS pending, SUM(b.status='cancel') AS cancel
			FROM `customer_appointment` AS a
    		left join `order` AS b on a.index = b.appoinment_id
    		WHERE `sales`='$sales'
			GROUP BY a.name ORDER BY a.index DESC";
			
			
			
	

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
	//echo $temp;
    echo str_replace(array("\\r\\n","\\r","\\n"),"<br/>",$temp);
   
?>