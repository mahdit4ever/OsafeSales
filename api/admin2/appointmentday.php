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
<?php
$ewCurSec = 0; // Initialise

// User levels
define("ewAllowadd", 1, true);
define("ewAllowdelete", 2, true);
define("ewAllowedit", 4, true);
define("ewAllowview", 8, true);
define("ewAllowlist", 8, true);
define("ewAllowreport", 8, true);
define("ewAllowsearch", 8, true);
define("ewAllowadmin", 16, true);
?>
<?php
if (@$HTTP_SESSION_VARS["osafe_status"] <> "login") {
	header("Location:  login.php");
	exit();
}
?>
<?php

// Initialize common variables
$x_index = Null; 
$ox_index = Null;
$x_telemarketer = Null; 
$ox_telemarketer = Null;
$x_sales = Null; 
$ox_sales = Null;
$x_customer = Null; 
$ox_customer = Null;
$x_date = Null; 
$ox_date = Null;
$x_time = Null; 
$ox_time = Null;
$x_remarks = Null; 
$ox_remarks = Null;
$x_status = Null; 
$ox_status = Null;
?>
<?php include ("db.php") ?>
<?php include ("phpmkrfn.php") ?>
<?php
$nStartRec = 0;
$nStopRec = 0;
$nTotalRecs = 0;
$nRecCount = 0;
$nRecActual = 0;
$sKeyMaster = "";
$sDbWhereMaster = "";
$sSrchAdvanced = "";
$sDbWhereDetail = "";
$sSrchBasic = "";
$sSrchWhere = "";
$sDbWhere = "";
$sDefaultOrderBy = "";
$sDefaultFilter = "";
$sWhere = "";
$sGroupBy = "";
$sHaving = "";
$sOrderBy = "";
$sSqlMasterBase = "";
$sSqlMaster = "";
$sListTrJs = "";
$bEditRow = "";
$nEditRowCnt = "";
$sDeleteConfirmMsg = "";
$nDisplayRecs = 20;
$nRecRange = 10;

// Open connection to the database
$conn = phpmkr_db_connect(HOST, USER, PASS, DB, PORT);
?>
<?php include ("header.php") ?>
<div class="widget">
<div class="widget-header">
	<i class="icon-th-large"></i>
	<h3>Appointments </h3>
</div>
<div class="widget-content">
<script type="text/javascript" src="ew.js"></script>
<script type="text/javascript">
<!--
EW_dateSep = "/"; // set date separator	

//-->
</script>
<script type="text/javascript">
<!--
var firstrowoffset = 1; // first data row start at
var tablename = 'ewlistmain'; // table name
var lastrowoffset = 0; // footer row
var usecss = false; // use css
var rowclass = 'ewTableRow'; // row class
var rowaltclass = 'ewTableAltRow'; // row alternate class
var rowmoverclass = 'ewTableHighlightRow'; // row mouse over class
var rowselectedclass = 'ewTableSelectRow'; // row selected class
var roweditclass = 'ewTableEditRow'; // row edit class
var rowcolor = '#FFFFFF'; // row color
var rowaltcolor = '#F5F5F5'; // row alternate color
var rowmovercolor = '#FFCCFF'; // row mouse over color
var rowselectedcolor = '#CCFFFF'; // row selected color
var roweditcolor = '#FFFF99'; // row edit color

//-->
</script>
<script type="text/javascript">
<!--
var EW_HTMLArea;

//-->
</script>

<?php
$calendarDate = date("Y-m-d",strtotime($_GET['date']));
$fullDate = $_GET['date']." 0900";

$sSql = "SELECT * FROM `sales` WHERE status='Active' ORDER BY name ASC";
$rs = phpmkr_query($sSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $sSql);
$nSales = phpmkr_num_rows($rs);

$appointmentSql = "SELECT * FROM `appointment` WHERE `date`='$calendarDate'";
$appointmentResults = phpmkr_query($appointmentSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $appointmentSql);
$nAppointment = phpmkr_num_rows($appointmentResults);


echo '<h3 style="text-align:center; margin-bottom:10px;">'.date("j F Y, l",strtotime($calendarDate)).'</h3>';
echo '<table class="table table-striped table-bordered">';
	echo '<thead>';
		echo '<tr>';
			echo '<th width="100px">Sales </th>';
			for($x=0; $x<=18;$x++){
				$calendarTime = date("Hi", strtotime($fullDate . " +". $x*30 ." minutes"));
				echo '<th style="text-align:center;">'.$calendarTime.'</th>';
			}
		echo '</tr>';
	echo '</thead>';
	
	echo '<tbody>';
		while($row = @phpmkr_fetch_array($rs)){
			echo '<tr>';
				echo '<td width="100px">'.$row["name"].'</td>';
				
				/////////////////////////////
				for($x=0; $x<=18;$x++){
					$calendarTime = date("Hi", strtotime($fullDate . " +". $x*30 ." minutes"));
					
					$appointmentCount = 0;
					while($appointmentRow = @phpmkr_fetch_array($appointmentResults)){
						$appointTimeStart = date("Hi",strtotime($_GET['date'].$appointmentRow["time_start"]));
						$appointTimeEnd = date("Hi",strtotime($_GET['date'].$appointmentRow["time_end"]));
						if($appointmentRow["sales"]==$row["index"] && $calendarTime >= $appointTimeStart && $calendarTime < $appointTimeEnd){
							$appointmentCount++;
						}
					}
					if($nAppointment > 0) mysql_data_seek($appointmentResults, 0);
					
					
					if($appointmentCount == 0){
						echo '<td onclick="alert(\'hi\');" style="text-align:center;">-</td>';
					} else {
						echo '<td onclick="alert(\'hi\');" style="background:#ffff99; text-align:center;">-</td>';
					}
				}
			
			echo '</tr>';
		}
	echo '</tbody>';
	
echo '</table>';
?>



<?php

// Close recordset and connection
phpmkr_free_result($rs);
phpmkr_db_close($conn);
?>

<?php include ("footer.php") ?>
<?php

//-------------------------------------------------------------------------------
// Function SetUpAdvancedSearch
// - Set up Advanced Search parameter based on querystring parameters from Advanced Search Page
// - Variables setup: sSrchAdvanced

function SetUpAdvancedSearch()
{
	global $HTTP_GET_VARS;
	global $sSrchAdvanced;

	// Field index
	$x_index = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_index"]) : @$HTTP_GET_VARS["x_index"];
	$arrFldOpr = "";
	$z_index = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_index"]) : @$HTTP_GET_VARS["z_index"];
	$arrFldOpr = split(",",$z_index);
	if ($x_index <> "") {
		$sSrchAdvanced .= "`index` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_index) : $x_index; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}

	// Field telemarketer
	$x_telemarketer = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_telemarketer"]) : @$HTTP_GET_VARS["x_telemarketer"];
	$arrFldOpr = "";
	$z_telemarketer = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_telemarketer"]) : @$HTTP_GET_VARS["z_telemarketer"];
	$arrFldOpr = split(",",$z_telemarketer);
	if ($x_telemarketer <> "") {
		$sSrchAdvanced .= "`telemarketer` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_telemarketer) : $x_telemarketer; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}

	// Field sales
	$x_sales = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_sales"]) : @$HTTP_GET_VARS["x_sales"];
	$arrFldOpr = "";
	$z_sales = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_sales"]) : @$HTTP_GET_VARS["z_sales"];
	$arrFldOpr = split(",",$z_sales);
	if ($x_sales <> "") {
		$sSrchAdvanced .= "`sales` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_sales) : $x_sales; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}

	// Field customer
	$x_customer = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_customer"]) : @$HTTP_GET_VARS["x_customer"];
	$arrFldOpr = "";
	$z_customer = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_customer"]) : @$HTTP_GET_VARS["z_customer"];
	$arrFldOpr = split(",",$z_customer);
	if ($x_customer <> "") {
		$sSrchAdvanced .= "`customer` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_customer) : $x_customer; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}

	// Field date
	$x_date = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_date"]) : @$HTTP_GET_VARS["x_date"];
	$arrFldOpr = "";
	$z_date = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_date"]) : @$HTTP_GET_VARS["z_date"];
	$arrFldOpr = split(",",$z_date);
	if ($x_date <> "") {
		$sSrchAdvanced .= "`date` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_date) : $x_date; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}

	// Field time
	$x_time = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_time"]) : @$HTTP_GET_VARS["x_time"];
	$arrFldOpr = "";
	$z_time = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_time"]) : @$HTTP_GET_VARS["z_time"];
	$arrFldOpr = split(",",$z_time);
	if ($x_time <> "") {
		$sSrchAdvanced .= "`time` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_time) : $x_time; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}

	// Field remarks
	$x_remarks = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_remarks"]) : @$HTTP_GET_VARS["x_remarks"];
	$arrFldOpr = "";
	$z_remarks = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_remarks"]) : @$HTTP_GET_VARS["z_remarks"];
	$arrFldOpr = split(",",$z_remarks);
	if ($x_remarks <> "") {
		$sSrchAdvanced .= "`remarks` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_remarks) : $x_remarks; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}

	// Field status
	$x_status = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["x_status"]) : @$HTTP_GET_VARS["x_status"];
	$arrFldOpr = "";
	$z_status = (get_magic_quotes_gpc()) ? stripslashes(@$HTTP_GET_VARS["z_status"]) : @$HTTP_GET_VARS["z_status"];
	$arrFldOpr = split(",",$z_status);
	if ($x_status <> "") {
		$sSrchAdvanced .= "`status` "; // Add field
		$sSrchAdvanced .= @$arrFldOpr[0] . " "; // Add operator
		if (count($arrFldOpr) >= 1) {
			$sSrchAdvanced .= @$arrFldOpr[1]; // Add search prefix
		}
		$sSrchAdvanced .= (!get_magic_quotes_gpc()) ? addslashes($x_status) : $x_status; // Add input parameter
		if (count($arrFldOpr) >=2) {
			$sSrchAdvanced .= @$arrFldOpr[2]; // Add search suffix
		}
		$sSrchAdvanced .= " AND ";
	}
	if (strlen($sSrchAdvanced) > 4) {
		$sSrchAdvanced = substr($sSrchAdvanced, 0, strlen($sSrchAdvanced)-4);
	}
}

//-------------------------------------------------------------------------------
// Function BasicSearchSQL
// - Build WHERE clause for a keyword

function BasicSearchSQL($Keyword)
{
	$sKeyword = (!get_magic_quotes_gpc()) ? addslashes($Keyword) : $Keyword;
	$BasicSearchSQL = "";
	$BasicSearchSQL.= "`remarks` LIKE '%" . $sKeyword . "%' OR ";
	$BasicSearchSQL.= "`status` LIKE '%" . $sKeyword . "%' OR ";
	if (substr($BasicSearchSQL, -4) == " OR ") { $BasicSearchSQL = substr($BasicSearchSQL, 0, strlen($BasicSearchSQL)-4); }
	return $BasicSearchSQL;
}

//-------------------------------------------------------------------------------
// Function SetUpBasicSearch
// - Set up Basic Search parameter based on form elements pSearch & pSearchType
// - Variables setup: sSrchBasic

function SetUpBasicSearch()
{
	global $HTTP_GET_VARS;
	global $sSrchBasic;
	$sSearch = (!get_magic_quotes_gpc()) ? addslashes(@$HTTP_GET_VARS["psearch"]) : @$HTTP_GET_VARS["psearch"];
	$sSearchType = @$HTTP_GET_VARS["psearchtype"];
	if ($sSearch <> "") {
		if ($sSearchType <> "") {
			while (strpos($sSearch, "  ") != false) {
				$sSearch = str_replace("  ", " ",$sSearch);
			}
			$arKeyword = split(" ", trim($sSearch));
			foreach ($arKeyword as $sKeyword)
			{
				$sSrchBasic .= "(" . BasicSearchSQL($sKeyword) . ") " . $sSearchType . " ";
			}
		}
		else
		{
			$sSrchBasic = BasicSearchSQL($sSearch);
		}
	}
	if (substr($sSrchBasic, -4) == " OR ") { $sSrchBasic = substr($sSrchBasic, 0, strlen($sSrchBasic)-4); }
	if (substr($sSrchBasic, -5) == " AND ") { $sSrchBasic = substr($sSrchBasic, 0, strlen($sSrchBasic)-5); }
}

//-------------------------------------------------------------------------------
// Function SetUpSortOrder
// - Set up Sort parameters based on Sort Links clicked
// - Variables setup: sOrderBy, Session("Table_OrderBy"), Session("Table_Field_Sort")

function SetUpSortOrder()
{
	global $HTTP_SESSION_VARS;
	global $HTTP_GET_VARS;
	global $sOrderBy;
	global $sDefaultOrderBy;

	// Check for an Order parameter
	if (strlen(@$HTTP_GET_VARS["order"]) > 0) {
		$sOrder = @$HTTP_GET_VARS["order"];

		// Field index
		if ($sOrder == "index") {
			$sSortField = "`index`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_index_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_index_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_index_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_index_Sort"] = ""; }
		}

		// Field telemarketer
		if ($sOrder == "telemarketer") {
			$sSortField = "`telemarketer`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_telemarketer_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_telemarketer_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_telemarketer_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_telemarketer_Sort"] = ""; }
		}

		// Field sales
		if ($sOrder == "sales") {
			$sSortField = "`sales`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_sales_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_sales_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_sales_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_sales_Sort"] = ""; }
		}

		// Field customer
		if ($sOrder == "customer") {
			$sSortField = "`customer`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_customer_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_customer_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_customer_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_customer_Sort"] = ""; }
		}

		// Field date
		if ($sOrder == "date") {
			$sSortField = "`date`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_date_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_date_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_date_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_date_Sort"] = ""; }
		}

		// Field time
		if ($sOrder == "time") {
			$sSortField = "`time`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_time_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_time_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_time_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_time_Sort"] = ""; }
		}

		// Field remarks
		if ($sOrder == "remarks") {
			$sSortField = "`remarks`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_remarks_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_remarks_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_remarks_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_remarks_Sort"] = ""; }
		}

		// Field status
		if ($sOrder == "status") {
			$sSortField = "`status`";
			$sLastSort = @$HTTP_SESSION_VARS["appointment_x_status_Sort"];
			if ($sLastSort == "ASC") { $sThisSort = "DESC"; } else{  $sThisSort = "ASC"; }
			$HTTP_SESSION_VARS["appointment_x_status_Sort"] = $sThisSort;
		}
		else
		{
			if (@$HTTP_SESSION_VARS["appointment_x_status_Sort"] <> "") { @$HTTP_SESSION_VARS["appointment_x_status_Sort"] = ""; }
		}
		$HTTP_SESSION_VARS["appointment_OrderBy"] = $sSortField . " " . $sThisSort;
		$HTTP_SESSION_VARS["appointment_REC"] = 1;
	}
	$sOrderBy = @$HTTP_SESSION_VARS["appointment_OrderBy"];
	if ($sOrderBy == "") {
		$sOrderBy = $sDefaultOrderBy;
		$HTTP_SESSION_VARS["appointment_OrderBy"] = $sOrderBy;
	}
}

//-------------------------------------------------------------------------------
// Function SetUpStartRec
//- Set up Starting Record parameters based on Pager Navigation
// - Variables setup: nStartRec

function SetUpStartRec()
{

	// Check for a START parameter
	global $HTTP_SESSION_VARS;
	global $HTTP_GET_VARS;
	global $nStartRec;
	global $nDisplayRecs;
	global $nTotalRecs;
	if (strlen(@$HTTP_GET_VARS["start"]) > 0) {
		$nStartRec = @$HTTP_GET_VARS["start"];
		$HTTP_SESSION_VARS["appointment_REC"] = $nStartRec;
	}elseif (strlen(@$HTTP_GET_VARS["pageno"]) > 0) {
		$nPageNo = @$HTTP_GET_VARS["pageno"];
		if (is_numeric($nPageNo)) {
			$nStartRec = ($nPageNo-1)*$nDisplayRecs+1;
			if ($nStartRec <= 0) {
				$nStartRec = 1;
			}elseif ($nStartRec >= (($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1) {
				$nStartRec = (($nTotalRecs-1)/$nDisplayRecs)*$nDisplayRecs+1;
			}
			$HTTP_SESSION_VARS["appointment_REC"] = $nStartRec;
		}else{
			$nStartRec = @$HTTP_SESSION_VARS["appointment_REC"];
			if  (!(is_numeric($nStartRec)) || ($nStartRec == "")) {
				$nStartRec = 1; // Reset start record counter
				$HTTP_SESSION_VARS["appointment_REC"] = $nStartRec;
			}
		}
	}else{
		$nStartRec = @$HTTP_SESSION_VARS["appointment_REC"];
		if (!(is_numeric($nStartRec)) || ($nStartRec == "")) {
			$nStartRec = 1; //Reset start record counter
			$HTTP_SESSION_VARS["appointment_REC"] = $nStartRec;
		}
	}
}

//-------------------------------------------------------------------------------
// Function ResetCmd
// - Clear list page parameters
// - RESET: reset search parameters
// - RESETALL: reset search & master/detail parameters
// - RESETSORT: reset sort parameters

function ResetCmd()
{
		global $HTTP_SESSION_VARS;
		global $HTTP_GET_VARS;

	// Get Reset Cmd
	if (strlen(@$HTTP_GET_VARS["cmd"]) > 0) {
		$sCmd = @$HTTP_GET_VARS["cmd"];

		// Reset Search Criteria
		if (strtoupper($sCmd) == "RESET") {
			$sSrchWhere = "";
			$HTTP_SESSION_VARS["appointment_searchwhere"] = $sSrchWhere;

		// Reset Search Criteria & Session Keys
		}elseif (strtoupper($sCmd) == "RESETALL") {
			$sSrchWhere = "";
			$HTTP_SESSION_VARS["appointment_searchwhere"] = $sSrchWhere;

		// Reset Sort Criteria
		}
		elseif (strtoupper($sCmd) == "RESETSORT") {
			$sOrderBy = "";
			$HTTP_SESSION_VARS["appointment_OrderBy"] = $sOrderBy;
			if (@$HTTP_SESSION_VARS["appointment_x_index_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_index_Sort"] = ""; }
			if (@$HTTP_SESSION_VARS["appointment_x_telemarketer_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_telemarketer_Sort"] = ""; }
			if (@$HTTP_SESSION_VARS["appointment_x_sales_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_sales_Sort"] = ""; }
			if (@$HTTP_SESSION_VARS["appointment_x_customer_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_customer_Sort"] = ""; }
			if (@$HTTP_SESSION_VARS["appointment_x_date_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_date_Sort"] = ""; }
			if (@$HTTP_SESSION_VARS["appointment_x_time_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_time_Sort"] = ""; }
			if (@$HTTP_SESSION_VARS["appointment_x_remarks_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_remarks_Sort"] = ""; }
			if (@$HTTP_SESSION_VARS["appointment_x_status_Sort"] <> "") { $HTTP_SESSION_VARS["appointment_x_status_Sort"] = ""; }
		}

		// Reset Start Position (Reset Command)
		$nStartRec = 1;
		$HTTP_SESSION_VARS["appointment_REC"] = $nStartRec;
	}
}
?>
