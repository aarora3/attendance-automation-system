<?php require_once('Connections/localhost.php'); ?>
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

mysql_select_db($database_localhost, $localhost);
$query_Recordset2 = "SELECT * FROM employees";
$Recordset2 = mysql_query($query_Recordset2, $localhost) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['EmpID'])) {
  $loginUsername=$_POST['EmpID'];
  $password=$_POST['Password'];
  $MM_fldUserAuthorization = "UserLevel";
  $MM_redirectLoginSuccess = "controlpanel.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_localhost, $localhost);
  	
  $LoginRS__query=sprintf("SELECT empid, password, UserLevel FROM employees WHERE empid=%s AND password=%s",
  GetSQLValueString($loginUsername, "int"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $localhost) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'UserLevel');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<p><a href="index.php">Register</a></p>
<form ACTION="<?php echo $loginFormAction; ?>" id="form1" name="form1" method="POST">
  <table width="600" border="1">
    <tbody>
      <tr>
        <td><label for="EmpID">Emp ID:</label>
        <input type="text" name="EmpID" id="EmpID"></td>
      </tr>
      <tr>
        <td><label for="Password">Password:</label>
        <input type="password" name="Password" id="Password"></td>
      </tr>
      <tr>
        <td><input type="submit" name="submit" id="submit" value="Login"></td>
      </tr>
    </tbody>
  </table>
</form>
</body>
</html>
<?php
mysql_free_result($Recordset2);
?>
