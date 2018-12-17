<?php
function phpmkr_db_connect($HOST,$USER,$PASS,$DB,$PORT)
{
	$conn = mysql_connect($HOST . ":" . $PORT , $USER, $PASS);
	mysql_select_db($DB);
	return $conn;
}
function phpmkr_db_close($conn)
{
	mysql_close($conn);
}
function phpmkr_query($strsql,$conn)
{
	$rs = mysql_query($strsql,$conn);
	return $rs;
}
function phpmkr_num_rows($rs)
{
	return @mysql_num_rows($rs); 
}
function phpmkr_fetch_array($rs)
{
	return mysql_fetch_array($rs);
}
function phpmkr_free_result($rs)
{
	@mysql_free_result($rs);
}
function phpmkr_data_seek($rs,$cnt)
{
	@mysql_data_seek($rs, $cnt);
}
function phpmkr_error()
{
	return mysql_error();
}
?>
<?php
define("HOST", "110.4.45.141");
define("PORT", 3306);
define("USER", "sundayte_osafe");
define("PASS", "osafe01234");
define("DB", "sundayte_osafe");
?>
