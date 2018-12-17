<?php session_start(); ?>
<?php ob_start(); ?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Cache-Control: private");
header("Pragma: no-cache"); // HTTP/1.0 
?>
<?php
if (@phpversion() >= '5.0.0' && (!@ini_get('register_long_arrays') || @ini_get('register_long_arrays') == '0' || strtolower(@ini_get('register_long_arrays')) == 'off')) // PHP5 with register_long_arrays off
{
	$HTTP_POST_VARS = $_POST;
	$HTTP_GET_VARS = $_GET;
	$HTTP_SERVER_VARS = $_SERVER;
	$HTTP_COOKIE_VARS = $_COOKIE;
	$HTTP_ENV_VARS = $_ENV;
	$HTTP_POST_FILES = $_FILES;
	if (isset($_SESSION)) $HTTP_SESSION_VARS = $_SESSION;
}
?>
<?php include ("db.php") ?>
<?php

// Get key
$x_index = @$HTTP_GET_VARS["index"];
if (($x_index == "") || ($x_index == NULL)) { 
	ob_end_clean();
	header("Location: reminderlist.php");
	exit();
}
$x_index = (get_magic_quotes_gpc()) ? stripslashes($x_index) : $x_index;
$conn = phpmkr_db_connect(HOST, USER, PASS, DB, PORT);
$sSql = "SELECT * FROM `reminder`";
$sWhere = "";
$sGroupBy = "";
$sHaving = "";
$sOrderBy = "";
if ($sWhere <> "") { $sWhere .= " AND "; }
$sTmp = (!get_magic_quotes_gpc()) ? addslashes($x_index) : $x_index;
$sWhere .= "(`index` = " . $sTmp . ")";
$sSql .= " WHERE " . $sWhere;
if ($sGroupBy != "") {
	$sSql .= " GROUP BY " . $sGroupBy;
}
if ($sHaving != "") {
	$sSql .= " HAVING " . $sHaving;
}
if ($sOrderBy != "") {
	$sSql .= " ORDER BY " . $sOrderBy;
}
$rs = phpmkr_query($sSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $sSql);
if (phpmkr_num_rows($rs) > 0) {
	$row = phpmkr_fetch_array($rs);
	if ($row["thumb"]<> "") {
		header("Content-Disposition: attachment; filename=" . $row["thumb"]);
	}
	echo $row["thumb"];
}
phpmkr_free_result($rs);
phpmkr_db_close($conn);
?>
