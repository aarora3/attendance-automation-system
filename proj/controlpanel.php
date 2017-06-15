<?php require_once('Connections/localhost.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "0,1";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO timesheet (empid, in_time, `date`) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['empid'], "int"),
                       GetSQLValueString($_POST['intime'], "date"),
                       GetSQLValueString($_POST['date'], "date"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

  $insertGoTo = "clock_in.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_User = "-1";
if (isset($_SESSION['empid'])) {
  $colname_User = $_SESSION['empid'];
}
mysql_select_db($database_localhost, $localhost);
$query_User = sprintf("SELECT * FROM employees WHERE empid = %s", GetSQLValueString($colname_User, "int"));
$User = mysql_query($query_User, $localhost) or die(mysql_error());
$row_User = mysql_fetch_assoc($User);
$totalRows_User = mysql_num_rows($User);

mysql_select_db($database_localhost, $localhost);
$query_timesheet = "SELECT * FROM timesheet";
$timesheet = mysql_query($query_timesheet, $localhost) or die(mysql_error());
$row_timesheet = mysql_fetch_assoc($timesheet);
$totalRows_timesheet = mysql_num_rows($timesheet);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<p><a href="update.php">Update</a></p>
<p>&nbsp;</p>
<form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">
  <p>
    <input type="submit" name="submit" id="submit" value="Clock In">
    <input name="intime" type="hidden" id="intime" value="<?php echo $date = date('H:i:s'); ?>">
    <input name="date" type="hidden" id="date" value="<?php echo $date = date('Y-m-d'); ?>">
    <input name="empid" type="hidden" id="empid" value="<?php echo $row_User['empid']; ?>">
    <br>
  </p>
  <input type="hidden" name="MM_insert" value="form1">
</form>
<form id="form2" name="form2" method="post">
  <input type="submit" name="submit2" id="submit2" value="Clock Out">
  <input type="hidden" name="empid" id="empid">
  <input name="date" type="hidden" id="date" value="<?php echo $date = date('Y-m-d'); ?>">
  <input name="outtime" type="hidden" id="outtime" value="<?php echo $date = date('H:i:s'); ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><a href="logout.php">Logout</a>
</p>
<p><a href="admin.php">Admin</a></p>
</body>
</html>
<?php
mysql_free_result($User);

mysql_free_result($timesheet);
?>
