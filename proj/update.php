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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE employees SET empid=%s, firstname=%s, lastname=%s, Email=%s, password=%s WHERE id=%s",
                       GetSQLValueString($_POST['EmpID'], "int"),
                       GetSQLValueString($_POST['FirstName'], "text"),
                       GetSQLValueString($_POST['LastName'], "text"),
                       GetSQLValueString($_POST['Email'], "text"),
                       GetSQLValueString($_POST['Password'], "text"),
                       GetSQLValueString($_POST['hiddenField'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());

  $updateGoTo = "update.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

$colname_User = "-1";
if (isset($_GET['empid'])) {
  $colname_User = $_GET['empid'];
}
mysql_select_db($database_localhost, $localhost);
$query_User = sprintf("SELECT * FROM employees WHERE empid = %s", GetSQLValueString($colname_User, "int"));
$User = mysql_query($query_User, $localhost) or die(mysql_error());
$row_User = mysql_fetch_assoc($User);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">
  <table width="600" border="1">
    <tbody>
      <tr>
        <td><label for="EmpID">Emp ID:</label>
        <input name="EmpID" type="text" id="EmpID" value="<?php echo $row_User['empid']; ?>"></td>
      </tr>
      <tr>
        <td><label for="FirstName">First Name:</label>
        <input name="FirstName" type="text" id="FirstName" value="<?php echo $row_User['firstname']; ?>"></td>
      </tr>
      <tr>
        <td><label for="LastName">Last Name:</label>
        <input name="LastName" type="text" id="LastName" value="<?php echo $row_User['lastname']; ?>"></td>
      </tr>
      <tr>
        <td><label for="Email">Email:</label>
        <input name="Email" type="text" id="Email" value="<?php echo $row_User['Email']; ?>"></td>
      </tr>
      <tr>
        <td><label for="Password">Password:</label>
        <input name="Password" type="password" id="Password" value="<?php echo $row_User['password']; ?>">
        <input name="hiddenField" type="hidden" id="hiddenField" value="<?php echo $row_User['id']; ?>"></td>
      </tr>
      <tr>
        <td><input name="submit" type="submit" id="submit" value="Register"></td>
      </tr>
    </tbody>
  </table>
  <input type="hidden" name="MM_update" value="form1">
</form>
<p><a href="controlpanel.php">Back to Control Panel</a></p>
</body>
</html>
<?php
mysql_free_result($User);
?>
