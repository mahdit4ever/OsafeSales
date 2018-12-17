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
<?php include ("phpmkrfn.php") ?>
<?php

// User levels
define("ewAllowAdd", 1, true);
define("ewAllowDelete", 2, true);
define("ewAllowEdit", 4, true);
define("ewAllowView", 8, true);
define("ewAllowList", 8, true);
define("ewAllowReport", 8, true);
define("ewAllowSearch", 8, true);
define("ewAllowAdmin", 16, true);
if (@$HTTP_POST_VARS["submit"] <> "") {
	$bValidPwd = false;

	// Setup variables
	$sUserId = @$HTTP_POST_VARS["userid"];
	$sPassWd = @$HTTP_POST_VARS["passwd"];
	if (!($bValidPwd)) {
			$conn = phpmkr_db_connect(HOST, USER, PASS, DB, PORT);
			$sUserId = (!get_magic_quotes_gpc()) ? addslashes($sUserId) : $sUserId;
			$sSql = "SELECT * FROM `admin`";
			$sSql .= " WHERE `username` = '" . $sUserId . "'";
			$rs = phpmkr_query($sSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $sSql);
			if (phpmkr_num_rows($rs) > 0) {
			$row = phpmkr_fetch_array($rs);
				if (strtoupper($row["password"]) == strtoupper($sPassWd)) {
					$HTTP_SESSION_VARS["osafe_status_User"] = $row["username"];
					$HTTP_SESSION_VARS["osafe_SysAdmin"] = 0; // non System Administrator
					$bValidPwd = true;
				}
			}
	phpmkr_free_result($rs);
	phpmkr_db_close($conn);
	}
	if ($bValidPwd) {

		// Write cookies
		if (@$HTTP_POST_VARS["rememberme"] <> "") {
			setCookie("osafe_userid", $sUserId, time()+365*24*60*60); // change cookie expiry time here
		}
		$HTTP_SESSION_VARS["osafe_status"] = "login";
		ob_end_clean();
		header("Location: index.php");
		exit();
	} else {
		$HTTP_SESSION_VARS["ewmsg"] = "Incorrect user ID or password";
	}
}
?>
<?php include ("header.php") ?>
<script type="text/javascript" src="ew.js"></script>
<script type="text/javascript">
<!--
function EW_checkMyForm(EW_this) {
	if (!EW_hasValue(EW_this.userid, "TEXT" )) {
		if  (!EW_onError(EW_this, EW_this.userid, "TEXT", "Please enter user ID"))
			return false;
	}
	if (!EW_hasValue(EW_this.passwd, "PASSWORD" )) {
		if (!EW_onError(EW_this, EW_this.passwd, "PASSWORD", "Please enter password"))
			return false;
	}
	return true;
}

//-->
</script>
<p><span class="phpmaker">Login Page</span></p>
<?php
if (@$HTTP_SESSION_VARS["ewmsg"] <> "") {
?>
<p><span class="phpmaker" style="color: Red;"><?php echo $HTTP_SESSION_VARS["ewmsg"]; ?></span></p>
<?php
	$HTTP_SESSION_VARS["ewmsg"] = ""; // Clear message
}
?>
<form action="login.php" method="post" onSubmit="return EW_checkMyForm(this);">
<table border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td><span class="phpmaker">User Name</span></td>
		<td><span class="phpmaker"><input type="text" name="userid" size="20" value="<?php echo @$HTTP_COOKIE_VARS["osafe_userid"]; ?>"></span></td>
	</tr>
	<tr>
		<td><span class="phpmaker">Password</span></td>
		<td><span class="phpmaker"><input type="password" name="passwd" size="20"></span></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><span class="phpmaker"><input type="checkbox" name="rememberme" value="true">Remember me</span></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><span class="phpmaker"><input type="submit" name="submit" value="Login"></span></td>
	</tr>
</table>
</form>
<br>
<p><span class="phpmaker">
</span></p>
<?php include ("footer.php") ?>
