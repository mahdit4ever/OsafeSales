<?php

define("DEFAULT_CURRENCY_SYMBOL", "$");
define("DEFAULT_MON_DECIMAL_POINT", ".");
define("DEFAULT_MON_THOUSANDS_SEP", ",");
define("DEFAULT_POSITIVE_SIGN", "");
define("DEFAULT_NEGATIVE_SIGN", "-");
define("DEFAULT_FRAC_DIGITS", 2);
define("DEFAULT_P_CS_PRECEDES", true);
define("DEFAULT_P_SEP_BY_SPACE", false);
define("DEFAULT_N_CS_PRECEDES", true);
define("DEFAULT_N_SEP_BY_SPACE", false);
define("DEFAULT_P_SIGN_POSN", 3);
define("DEFAULT_N_SIGN_POSN", 3);


define("DEFAULT_DATE_FORMAT", "yyyy/mm/dd");
define("EW_DATE_SEPARATOR","/");

//-------------------------------------------------------------------------------
// Functions for default date format
// FormatDateTime
/*
Format a timestamp, datetime, date or time field from MySQL
$namedformat:
0 - General Date,
1 - Long Date,
2 - Short Date (Default),
3 - Long Time,
4 - Short Time,
5 - Short Date (yyyy/mm/dd),
6 - Short Date (mm/dd/yyyy),
7 - Short Date (dd/mm/yyyy)
*/

// Convert a date to MySQL format
function ConvertDateToMysqlFormat($dateStr)
{
	@list($datePt, $timePt) = explode(" ", $dateStr);
	$arDatePt = explode(EW_DATE_SEPARATOR, $datePt);
	if (count($arDatePt) == 3) {
		switch (DEFAULT_DATE_FORMAT) {
		case "yyyy" . EW_DATE_SEPARATOR . "mm" . EW_DATE_SEPARATOR . "dd":
		    list($year, $month, $day) = $arDatePt;
		    break;
		case "mm" . EW_DATE_SEPARATOR . "dd" . EW_DATE_SEPARATOR . "yyyy":
		    list($month, $day, $year) = $arDatePt;
		    break;
		case "dd" . EW_DATE_SEPARATOR . "mm" . EW_DATE_SEPARATOR . "yyyy":
		    list($day, $month, $year) = $arDatePt;
		    break;
		}
		return trim($year . "-" . $month . "-" . $day . " " . $timePt);
	} else {
		return $dateStr;
	}
}

function FormatDateTime($ts, $namedformat)
{
  $DefDateFormat = str_replace("yyyy", "%Y", DEFAULT_DATE_FORMAT);
	$DefDateFormat = str_replace("mm", "%m", $DefDateFormat);
	$DefDateFormat = str_replace("dd", "%d", $DefDateFormat);
	if (is_numeric($ts)) // timestamp
	{
		switch (strlen($ts)) {
			case 14:
			    $patt = '/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
			    break;
			case 12:
			    $patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
			    break;
			case 10:
			    $patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
			    break;
			case 8:
			    $patt = '/(\d{4})(\d{2})(\d{2})/';
			    break;
			case 6:
			    $patt = '/(\d{2})(\d{2})(\d{2})/';
			    break;
			case 4:
			    $patt = '/(\d{2})(\d{2})/';
			    break;
			case 2:
			    $patt = '/(\d{2})/';
			    break;
			default:
					return $ts;
		}
		if ((isset($patt))&&(preg_match($patt, $ts, $matches)))
		{
			$year = $matches[1];
			$month = @$matches[2];
			$day = @$matches[3];
			$hour = @$matches[4];
			$min = @$matches[5];
			$sec = @$matches[6];
		}
		if (($namedformat==0)&&(strlen($ts)<10)) $namedformat = 2;
	}
	elseif (is_string($ts))
	{
		if (preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // datetime
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			$hour = $matches[4];
			$min = $matches[5];
			$sec = $matches[6];
		}
		elseif (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $ts, $matches)) // date
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			if ($namedformat==0) $namedformat = 2;
		}
		elseif (preg_match('/(^|\s)(\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // time
		{
			$hour = $matches[2];
			$min = $matches[3];
			$sec = $matches[4];
			if (($namedformat==0)||($namedformat==1)) $namedformat = 3;
			if ($namedformat==2) $namedformat = 4;
		}
		else
		{
			return $ts;
		}
	}
	else
	{
		return $ts;
	}
	if (!isset($year)) $year = 0; // dummy value for times
	if (!isset($month)) $month = 1;
	if (!isset($day)) $day = 1;
	if (!isset($hour)) $hour = 0;
	if (!isset($min)) $min = 0;
	if (!isset($sec)) $sec = 0;
	$uts = @mktime($hour, $min, $sec, $month, $day, $year);
	if ($uts < 0) { // failed to convert
		$year = substr_replace("0000", $year, -1 * strlen($year));
		$month = substr_replace("00", $month, -1 * strlen($month));
		$day = substr_replace("00", $day, -1 * strlen($day));
		$hour = substr_replace("00", $hour, -1 * strlen($hour));
		$min = substr_replace("00", $min, -1 * strlen($min));
		$sec = substr_replace("00", $sec, -1 * strlen($sec));
		$DefDateFormat = str_replace("yyyy", $year, DEFAULT_DATE_FORMAT);
		$DefDateFormat = str_replace("mm", $month, $DefDateFormat);
		$DefDateFormat = str_replace("dd", $day, $DefDateFormat);
		switch ($namedformat) {
			case 0:
			    return $DefDateFormat." $hour:$min:$sec";
			    break;
			case 1://unsupported, return general date
			    return $DefDateFormat." $hour:$min:$sec";
			    break;
			case 2:
			    return $DefDateFormat;
			    break;
			case 3:
					if (intval($hour)==0)
						return "12:$min:$sec AM";
					elseif (intval($hour)>0 && intval($hour)<12)
						return "$hour:$min:$sec AM";
					elseif (intval($hour)==12)
						return "$hour:$min:$sec PM";
					elseif (intval($hour)>12 && intval($hour)<=23)
						return (intval($hour)-12).":$min:$sec PM";
					else
						return "$hour:$min:$sec";
			    break;
			case 4:
			    return "$hour:$min:$sec";
			    break;
			case 5:
			    return "$year". EW_DATE_SEPARATOR . "$month" . EW_DATE_SEPARATOR . "$day";
			    break;
			case 6:
			    return "$month". EW_DATE_SEPARATOR ."$day" . EW_DATE_SEPARATOR . "$year";
			    break;
			case 7:
			    return "$day" . EW_DATE_SEPARATOR ."$month" . EW_DATE_SEPARATOR . "$year";
			    break;
		}
	} else {
		switch ($namedformat) {
			case 0:
			    return strftime($DefDateFormat." %H:%M:%S", $uts);
			    break;
			case 1:
			    return strftime("%A, %B %d, %Y", $uts);
			    break;
			case 2:
			    return strftime($DefDateFormat, $uts);
			    break;
			case 3:
			    return strftime("%I:%M:%S %p", $uts);
			    break;
			case 4:
			    return strftime("%H:%M:%S", $uts);
			    break;
			case 5:
			    return strftime("%Y" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%d", $uts);
			    break;
			case 6:
			    return strftime("%m" . EW_DATE_SEPARATOR . "%d" . EW_DATE_SEPARATOR . "%Y", $uts);
			    break;
			case 7:
			    return strftime("%d" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%Y", $uts);
			    break;
		}
	}
}
//-------------------------------------------------------------------------------
// Function for debug
function Trace($aMsg)
{
	$ts = fopen ("debug.txt","a+");
	$ts.file_put_contents($aMsg);
	$ts.fclose;
}
?>
<?php
// FormatCurrency
/*
FormatCurrency(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
 [,UseParensForNegativeNumbers [,GroupDigits]]]])
NumDigitsAfterDecimal is the numeric value indicating how many places to the
right of the decimal are displayed
-1 Use Default
The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
arguments have the following settings:
-1 True
0 False
-2 Use Default
*/
function FormatCurrency($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit, $UseParensForNegativeNumbers, $GroupDigits)
{

	// export the values returned by localeconv into the local scope
	if (function_exists("localeconv")) extract(localeconv());

	// set defaults if locale is not set
	if (empty($currency_symbol)) $currency_symbol = DEFAULT_CURRENCY_SYMBOL;
	if (empty($mon_decimal_point)) $mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
	if (empty($mon_thousands_sep)) $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	if (empty($positive_sign)) $positive_sign = DEFAULT_POSITIVE_SIGN;
	if (empty($negative_sign)) $negative_sign = DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

	// check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// start by formatting the unsigned number
	$number = number_format(abs($amount),
	                        $frac_digits,
	                        $mon_decimal_point,
	                        $mon_thousands_sep);

	// check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;

		// "extracts" the boolean value as an integer
		$n_cs_precedes  = intval($n_cs_precedes  == true);
		$n_sep_by_space = intval($n_sep_by_space == true);
		$key = $n_cs_precedes . $n_sep_by_space . $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$p_cs_precedes  = intval($p_cs_precedes  == true);
		$p_sep_by_space = intval($p_sep_by_space == true);
		$key = $p_cs_precedes . $p_sep_by_space . $p_sign_posn;
	}
	$formats = array(

      // currency symbol is after amount

      // no space between amount and sign
      '000' => '(%s' . $currency_symbol . ')',
      '001' => $sign . '%s ' . $currency_symbol,
      '002' => '%s' . $currency_symbol . $sign,
      '003' => '%s' . $sign . $currency_symbol,
      '004' => '%s' . $sign . $currency_symbol,

      // one space between amount and sign
      '010' => '(%s ' . $currency_symbol . ')',
      '011' => $sign . '%s ' . $currency_symbol,
      '012' => '%s ' . $currency_symbol . $sign,
      '013' => '%s ' . $sign . $currency_symbol,
      '014' => '%s ' . $sign . $currency_symbol,

      // currency symbol is before amount

      // no space between amount and sign
      '100' => '(' . $currency_symbol . '%s)',
      '101' => $sign . $currency_symbol . '%s',
      '102' => $currency_symbol . '%s' . $sign,
      '103' => $sign . $currency_symbol . '%s',
      '104' => $currency_symbol . $sign . '%s',

      // one space between amount and sign
      '110' => '(' . $currency_symbol . ' %s)',
      '111' => $sign . $currency_symbol . ' %s',
      '112' => $currency_symbol . ' %s' . $sign,
      '113' => $sign . $currency_symbol . ' %s',
      '114' => $currency_symbol . ' ' . $sign . '%s');

  // lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// FormatNumber
/*
FormatNumber(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
	[,UseParensForNegativeNumbers [,GroupDigits]]]])
NumDigitsAfterDecimal is the numeric value indicating how many places to the
right of the decimal are displayed
-1 Use Default
The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
arguments have the following settings:
-1 True
0 False
-2 Use Default
*/
function FormatNumber($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit, $UseParensForNegativeNumbers, $GroupDigits)
{

  // export the values returned by localeconv into the local scope
  if (function_exists("localeconv")) extract(localeconv());

	// set defaults if locale is not set
	if (empty($currency_symbol)) $currency_symbol = DEFAULT_CURRENCY_SYMBOL;
	if (empty($mon_decimal_point)) $mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
	if (empty($mon_thousands_sep)) $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	if (empty($positive_sign)) $positive_sign = DEFAULT_POSITIVE_SIGN;
	if (empty($negative_sign)) $negative_sign = DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

	// check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

  // start by formatting the unsigned number
  $number = number_format(abs($amount),
                          $frac_digits,
                          $mon_decimal_point,
                          $mon_thousands_sep);

	// check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;
		$key = $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$key = $p_sign_posn;
	}
	$formats = array(
		'0' => '(%s)',
		'1' => $sign . '%s',
		'2' => $sign . '%s',
		'3' => $sign . '%s',
		'4' => $sign . '%s');

	// lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// FormatPercent
/*
FormatPercent(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
	[,UseParensForNegativeNumbers [,GroupDigits]]]])
NumDigitsAfterDecimal is the numeric value indicating how many places to the
right of the decimal are displayed
-1 Use Default
The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
arguments have the following settings:
-1 True
0 False
-2 Use Default
*/
function FormatPercent($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit, $UseParensForNegativeNumbers, $GroupDigits)
{

  // export the values returned by localeconv into the local scope
  if (function_exists("localeconv")) extract(localeconv());

	// set defaults if locale is not set
	if (empty($currency_symbol)) $currency_symbol = DEFAULT_CURRENCY_SYMBOL;
	if (empty($mon_decimal_point)) $mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
	if (empty($mon_thousands_sep)) $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	if (empty($positive_sign)) $positive_sign = DEFAULT_POSITIVE_SIGN;
	if (empty($negative_sign)) $negative_sign = DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

	// check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// start by formatting the unsigned number
	$number = number_format(abs($amount)*100,
	                        $frac_digits,
	                        $mon_decimal_point,
	                        $mon_thousands_sep);

	// check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;
		$key = $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$key = $p_sign_posn;
	}
	$formats = array(
		'0' => '(%s%%)',
		'1' => $sign . '%s%%',
		'2' => $sign . '%s%%',
		'3' => $sign . '%s%%',
		'4' => $sign . '%s%%');

  // lookup the key in the above array
	return sprintf($formats[$key], $number);
}

function ewUploadPath($parm)
{

	global $HTTP_SERVER_VARS;
	global $HTTP_ENV_VARS;


$delim = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "\\" : "/";


	if ($parm == 0) {
		$ewUploadPath = "";
	} else {
		$ewUploadPath = ewScriptFileName();
		$ewUploadPath = str_replace("\\\\","\\",dirname($ewUploadPath));
	}

	// Customize the upload path here
	// Check the last delimiter
	if ($parm == 0) {
		if ($ewUploadPath <> "" && substr($ewUploadPath, -1) <> "/") { $ewUploadPath .= "/"; } 
	} else {
		if (substr($ewUploadPath, -1) <> $delim) { $ewUploadPath .= $delim; }
	}

	return $ewUploadPath;
}

function ewUploadFileName($sFileName)
{

	// Amend your logic here
	$sOutFileName = $sFileName;

	// Return computed output file name
	return $sOutFileName;
}

function ewScriptFileName() {

	global $HTTP_SERVER_VARS;
	global $HTTP_ENV_VARS;


	$sScriptFileName = @$HTTP_ENV_VARS["SCRIPT_FILENAME"];
	if (empty($sScriptFileName)) {$sScriptFileName = @$HTTP_SERVER_VARS["SCRIPT_FILENAME"];}
	if (empty($sScriptFileName)) {$sScriptFileName = @$HTTP_ENV_VARS["PATH_TRANSLATED"];}
	if (empty($sScriptFileName)) {$sScriptFileName = @$HTTP_SERVER_VARS["PATH_TRANSLATED"];}
	if (empty($sScriptFileName)) {$sScriptFileName = @$HTTP_ENV_VARS["ORIG_PATH_TRANSLATED"];}
	if (empty($sScriptFileName)) {$sScriptFileName = @$HTTP_SERVER_VARS["ORIG_PATH_TRANSLATED"];}
	if (empty($sScriptFileName)) {die("Path of script not found. You can use phpinfo() to find the correct environment/server variable on your server and modify the function ewScriptFileName() in phpmkrfn.php. The variable should return the full path of the script.");}
	return $sScriptFileName;
}
?>
<?php
// Function to Load Email Content from input file name
// - Content Loaded to the following variables
// - Subject: sEmailSubject
// - From: sEmailFrom
// - To: sEmailTo
// - Cc: sEmailCc
// - Bcc: sEmailBcc
// - Format: sEmailFormat
// - Content: sEmailContent
//
function LoadEmail($fn)
{

	global $sEmailSubject;
	global $sEmailFrom;
	global $sEmailTo;
	global $sEmailCc;
	global $sEmailBcc;
	global $sEmailFormat;
	global $sEmailContent;

	$sWrk = LoadTxt($fn); // Load text file content
	if ($sWrk <> "") {
		// Locate Header & Mail Content
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$i = strpos($sWrk, "\r\n\r\n");
		}else {
			$i = strpos($sWrk, "\n\n");
			if ($i === false) $i = strpos($sWrk, "\r\n\r\n");
		}
		if ($i > 0) {
			$sHeader = substr($sWrk, 0, $i);
			$sEmailContent = trim(substr($sWrk, $i, strlen($sWrk)));
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				$arrHeader = split("\r\n",$sHeader);
			} else {
				$arrHeader = split("\n",$sHeader);
			}
			for ($j = 0; $j < count($arrHeader); $j++)
			{
				$i = strpos($arrHeader[$j], ":");
				if ($i > 0) {
					$sName = trim(substr($arrHeader[$j], 0, $i));
					$sValue = trim(substr($arrHeader[$j], $i+1, strlen($arrHeader[$j])));
					switch (strtolower($sName))
					{
						case "subject": $sEmailSubject = $sValue;
												break;
						case "from": $sEmailFrom = $sValue;
												break;
						case "to": $sEmailTo = $sValue;
												break;
						case "cc": $sEmailCc = $sValue;
												break;
						case "bcc": $sEmailBcc = $sValue;
												break;
						case "format": $sEmailFormat = $sValue;
												break;
					}
				}
			}
		}
	}

}

// Function to Load a Text File
function LoadTxt($fn)
{

	$filepath = str_replace("\\\\","\\",dirname(ewScriptFileName()));
	// Get text file content
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$filepath .= "\\" . $fn;
	} else {
		$filepath .= "/" . $fn;		
	}
	$fobj = fopen ($filepath , "r");
	return fread ($fobj, filesize ($filepath));

}

// Function to Send out Email
function Send_Email($sFrEmail, $sToEmail, $sCcEmail, $sBccEmail, $sSubject, $sMail, $sFormat)
{
	/* recipients */
	$to  = $sToEmail;

	/* subject */
	$subject = $sSubject;

	$headers = "";

	if ($sFormat == "html") {
		$content_type = "text/html";
	} else {
		$content_type = "text/plain";
	}

	$headers = "Content-type: " . $content_type . "\r\n";

	$message = $sMail;

	/* additional headers */
	$headers .= "From: " . $sFrEmail . "\r\n";
	if ($sCcEmail <> "") {
		$headers .= "Cc: " . $sCcEmail . "\r\n";
	}
	if ($sBccEmail <>"") {
		$headers .= "Bcc: " . $sBccEmail . "\r\n";
	}

	/* and now mail it */
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		ini_set("SMTP","localhost");
		ini_set("smtp_port","25");
	}
	ini_set("sendmail_from",$sFrEmail);
	mail($to, $subject, $message, $headers);

}
?>
<?php
// Function to generate Value Separator based on current row count
// rowcnt - zero based row count
//
function ValueSeparator($rowcnt)
{
	return ", ";
}

// Function to generate View Option Separator based on current row count (Multi-Select / CheckBox)
// rowcnt - zero based row count
//
function ViewOptionSeparator($rowcnt)
{
	return  ", ";
}

// Function to generate Edit Option Separator based on current row count (Radio / CheckBox)
// rowcnt - zero based row count
//
function EditOptionSeparator($rowcnt)
{
	return "&nbsp;";
}

// Function to truncate Memo Field based on specified length, string truncated to nearest space or CrLf
//
function TruncateMemo($str, $ln)
{
	if (strlen($str) > 0 && strlen($str) > $ln) {
		$k = 0;
		while ($k >= 0 && $k < strlen($str)){
			$i = strpos($str, " ", $k);
			$j = strpos($str,chr(10), $k);
			if ($i === false && $j === false) { // Not able to truncate
				return $str;
			} else {
				// Get nearest space or CrLf
				if ($i > 0 && $j > 0) {
					if ($i < $j) {
						$k = $i;
					}else{
						$k = $j;
					}
				}elseif ($i > 0) {
					$k = $i;
				}elseif ($j > 0) {
					$k = $j;
				}
				// Get truncated text
				if ($k >= $ln) {
					return substr($str, 0, $k) . "...";
				} else {
					$k ++;
				}
			}
		}
	} else {
		return $str;
	}
}

// Function to check if the MySQL server supports subquery
/* $conn must be set */
function isSubquerySupported() {
	global $conn;
	$sSql = "SHOW VARIABLES LIKE 'version'";
	$rs = phpmkr_query($sSql,$conn) or die("Failed to execute query: " . phpmkr_error() . '<br>SQL: ' . $sSql);
	if ($field = mysql_fetch_array($rs)) $version = $field["Value"];
	$match = explode('.', @$version);
	return (int)sprintf('%d%02d%02d', $match[0], @$match[1], intval(@$match[2])) >= 40100;
}
?>
