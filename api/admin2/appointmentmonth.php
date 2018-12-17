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

// Handle Reset Command
//ResetCmd();

// Get Search Criteria for Advanced Search
//SetUpAdvancedSearch();

// Get Search Criteria for Basic Search
//SetUpBasicSearch();

// Build Search Criteria
if ($sSrchAdvanced != "") {
	$sSrchWhere = $sSrchAdvanced; // Advanced Search
}
elseif ($sSrchBasic != "") {
	$sSrchWhere = $sSrchBasic; // Basic Search
}

// Save Search Criteria
if ($sSrchWhere != "") {
	$HTTP_SESSION_VARS["appointment_searchwhere"] = $sSrchWhere;

	// Reset start record counter (new search)
	$nStartRec = 1;
	$HTTP_SESSION_VARS["appointment_REC"] = $nStartRec;
}
else
{
	$sSrchWhere = @$HTTP_SESSION_VARS["appointment_searchwhere"];
}

// Build SQL
$sSql = "SELECT * FROM `appointment`";

// Load Default Filter
$sDefaultFilter = "";
$sGroupBy = "";
$sHaving = "";

// Load Default Order
$sDefaultOrderBy = "";

// Build WHERE condition
$sDbWhere = "";
if ($sDbWhereDetail <> "") {
	$sDbWhere .= "(" . $sDbWhereDetail . ") AND ";
}
if ($sSrchWhere <> "") {
	$sDbWhere .= "(" . $sSrchWhere . ") AND ";
}
if (strlen($sDbWhere) > 5) {
	$sDbWhere = substr($sDbWhere, 0, strlen($sDbWhere)-5); // Trim rightmost AND
}
$sWhere = "";
if ($sDefaultFilter <> "") {
	$sWhere .= "(" . $sDefaultFilter . ") AND ";
}
if ($sDbWhere <> "") {
	$sWhere .= "(" . $sDbWhere . ") AND ";
}
if (substr($sWhere, -5) == " AND ") {
	$sWhere = substr($sWhere, 0, strlen($sWhere)-5);
}
if ($sWhere != "") {
	$sSql .= " WHERE " . $sWhere;
}
if ($sGroupBy != "") {
	$sSql .= " GROUP BY " . $sGroupBy;
}
if ($sHaving != "") {
	$sSql .= " HAVING " . $sHaving;
}

// Set Up Sorting Order
$sOrderBy = "";
//SetUpSortOrder();
if ($sOrderBy != "") {
	$sSql .= " ORDER BY " . $sOrderBy;
}

//echo $sSql; // Uncomment to show SQL for debugging
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
$sSql = "SELECT `index`,`name` FROM `sales` WHERE status='Active' ORDER BY name ASC";
$rs = phpmkr_query($sSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $sSql);
$nSales = phpmkr_num_rows($rs);

	//Create Sales Json for JS
	$salesArray[] = array();
    while($row = @phpmkr_fetch_array($rs))
    {
        $salesArray[] = $row;
    }
	if($nSales > 0) mysql_data_seek($rs, 0);

$appointmentSql = "SELECT * FROM `appointment`";
$appointmentResults = phpmkr_query($appointmentSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $appointmentSql);
$nAppointment = phpmkr_num_rows($appointmentResults);

	//Create Appointment Json for JS
	$appointmentArray[] = array();
    while($row = @phpmkr_fetch_array($appointmentResults))
    {
        $appointmentArray[] = $row;
    }
	if($nAppointment > 0) mysql_data_seek($appointmentResults, 0);

$customerSql = "SELECT * FROM `customer`";
$customerResults = phpmkr_query($customerSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $customerSql);
$nCustomer = phpmkr_num_rows($customerResults);

	//Create customer Json for JS
	$customerArray[] = array();
    while($row = @phpmkr_fetch_array($customerResults))
    {
        $customerArray[] = $row;
    }
	if($nCustomer > 0) mysql_data_seek($customerResults, 0);
	
for($x=0; $x<=2; $x++){
	$calendarMonthName = date('F Y', strtotime('+'.$x.' month'));
	
	$calendarMonth = date('m', strtotime('+'.$x.' month'));
	$calendarYear = date('Y', strtotime('+'.$x.' month'));

	$calendarNumberDay = cal_days_in_month(CAL_GREGORIAN, $calendarMonth, $calendarYear);
	$dayArray = array("M", "T", "W", "T", "F", "S", "S");
	
	echo '<h3 style="text-align:center; margin-bottom:10px;">'.$calendarMonthName.'</h3>';
	echo '<table class="table table-striped table-bordered">';
		echo '<thead>';
			echo '<tr>';
				echo '<th width="100px"></th>';
				$calendarDayCount = 1;
				while($calendarDayCount <= $calendarNumberDay){
					$calendarDay = date("N",strtotime($calendarYear.'-'.$calendarMonth.'-'.$calendarDayCount));
					
					if($dayArray[$calendarDay-1]=='S'){
						echo '<th style="background:#ffff99; text-align:center;">'.$dayArray[$calendarDay-1].'</th>';
					} else {
						echo '<th style="text-align:center;">'.$dayArray[$calendarDay-1].'</th>';
					}
					$calendarDayCount++;
				}
			echo '</tr>';
			
			echo '<tr>';
				echo '<th width="100px">Sales </th>';
				$calendarDayCount = 1;
				while($calendarDayCount <= $calendarNumberDay){
					echo '<th style="text-align:center;"><a href="appointmentday.php?date='.$calendarYear.'-'.$calendarMonth.'-'.$calendarDayCount.'">'.$calendarDayCount.'</a></th>';
					$calendarDayCount++;
				}
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			while($row = @phpmkr_fetch_array($rs)){
				echo '<tr>';
					echo '<td width="100px">'.$row["name"].'</td>';
					$calendarDayCount = 1;
					while($calendarDayCount <= $calendarNumberDay){
						
						//Check number of appointment
						$appointmentCount = 0;
						while($appointmentRow = @phpmkr_fetch_array($appointmentResults)){
							$appointmentDate = $appointmentRow["date"];
							if($appointmentRow["sales"]==$row["index"] && date("j",strtotime($appointmentDate))==$calendarDayCount && date("m",strtotime($appointmentDate))==$calendarMonth && date("Y",strtotime($appointmentDate))==$calendarYear){
								$appointmentCount++;
							}
						}
						if($nAppointment > 0) mysql_data_seek($appointmentResults, 0);
						//Check appointment ends
						
						if($appointmentCount == 0){
							if($x==0 && $calendarDayCount<date('j')){ 
								echo '<td style="background:#ccc; text-align:center;">-</td>';
							} else {
								echo '<td onclick="newAppointment('. $row["index"] . ',' . '\'' . date("Y-m-d",strtotime($calendarYear.'-'.$calendarMonth.'-'.$calendarDayCount)) . '\'' . ')" style="text-align:center;">-</td>';
							}
						} else {
							if($x==0 && $calendarDayCount<date('j')){ 
								echo '<td style="background:#ccc; text-align:center;">'.$appointmentCount.'</td>';
							} else {
								echo '<td style="background:#00ff00; text-align:center;">'.$appointmentCount.'</td>';
							}
						}
						$calendarDayCount++;
					}
				echo '</tr>';
			}
			mysql_data_seek($rs, 0);
		echo '</tbody>';
	echo '</table>';
}
?>

<script>

var submitEnable = false;
$(document).ready(function() {
	var customerJson = <?php echo json_encode($customerArray); ?>;
	
	var html = '';
	for(var i=1; i<customerJson.length;i++){
		html += '<option value="' + customerJson[i].index + '">' + customerJson[i].company_name + '</option>';
	}
	$("#new-appointment #customer").html(html);
	
	$("#new-appointment #customer").select2();
});

function newAppointment(index, date){
	submitEnable = true;
	var json = <?php echo json_encode($salesArray); ?>;
	for(var i=1; i<json.length; i++){
		if(json[i].index==index){
			index=i;
			break;
		}
	}
	$('#new-appointment #sales').val(json[index].name);
	$('#new-appointment #salesid').val(json[index].index);
	$('#new-appointment #date').val(date);
	$('#fc_create').click();
}

function submitAppointment(){
	if(!submitEnable) return;
	
	var telemarketer = 1;
	var sales = $('#new-appointment #salesid').val();
	var customer = $('#new-appointment #customer').val();
	var date = $('#new-appointment #date').val();
	var timeStart = $('#new-appointment #time-start').val();
	var timeEnd = $('#new-appointment #time-end').val();
	var remarks = $('#new-appointment #remarks').val();
	
	$.ajax(
	{
		url: 'api/new-appointment.php?',
		method: 'post',
		cache: false,
		data:
		{
			telemarketer: telemarketer,
			sales: sales,
			customer: customer,
			date: date,
			timeStart: timeStart,
			timeEnd: timeEnd,
			remarks: remarks
		}
	})
	.done(function(response){
		if($.trim(response) == '0')
		{
			alert('The appointment has been successfully added.');
			$('#new-appointment #salesid').val('');
			$('#new-appointment #customer').val('');
			$('#new-appointment #date').val('');
			$('#new-appointment #time-start').val('');
			$('#new-appointment #time-end').val('');
			$('#new-appointment #remarks').val('');
			submitEnable = false;
		} else if($.trim(response) == '1')
		{
			alert('The time slot is crashed with other appointment. Please change the appointment time');
		}
		else
		{
			alert('Error has occurred.');
		}
	})
	.fail(function(){
		alert('Please check your internet connection and try again later.');
	});
}
</script>

	<!-- calendar modal -->
    <div id="CalenderModalNew" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel">New Appointment</h4>
          </div>
          <div class="modal-body">
			<div id="testmodal" style="padding: 5px 20px;">
			 <form id="new-appointment" class="form-horizontal calender" role="form">
                
			<!-- //////////////////// -->
				
				<div class="control-group">											
					<label class="control-label" for="sales">Sales</label>
					<div class="controls">
						<input type="hidden" class="col-sm-3 disabled" id="salesid" disabled>
						<input type="text" class="col-sm-3 disabled" id="sales" disabled>
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
				<div class="control-group">											
					<label class="control-label" for="customer">Customer</label>
					<div class="controls">
						<select class="js-example-basic-single col-sm-3" id="customer">
							<option value="AL">Alabama</option>
							...
							<option value="WY">Wyoming</option>
						</select>
						<!--<input type="text" class="col-sm-3" id="customer" value="" autocomplete="off">-->
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
				<div class="control-group">											
					<label class="control-label" for="customer">Date</label>
					<div class="controls">
						<input type="text" class="col-sm-3 disabled" id="date" value="" disabled>
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
				<div class="control-group">											
					<label class="control-label" for="time-start">Time Start</label>
					<div class="controls">
						<input type="time" class="col-sm-3" id="time-start" value="">
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
				<div class="control-group">											
					<label class="control-label" for="time-end">Time End</label>
					<div class="controls">
						<input type="time" class="col-sm-3" id="time-end" value="">
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
				<div class="control-group">											
					<label class="control-label" for="remarks">Remarks</label>
					<div class="controls">
						<textarea class="col-sm-3" rows="3" id="remarks"></textarea>
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
			<!-- //////////////////// -->
				
              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
            <button type="button" onClick="submitAppointment()" class="btn btn-primary" data-dismiss="modal">Submit</button>
          </div>
        </div>
      </div>
    </div>

    <div id="fc_create" data-toggle="modal" data-target="#CalenderModalNew"></div>
    <!-- /calendar modal -->

<?php

// Close recordset and connection
phpmkr_free_result($rs);
phpmkr_db_close($conn);
?>

<?php include ("footer.php") ?>