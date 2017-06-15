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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO employees (empid, firstname, lastname, Email, password) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['EmpID'], "int"),
                       GetSQLValueString($_POST['FirstName'], "text"),
                       GetSQLValueString($_POST['LastName'], "text"),
                       GetSQLValueString($_POST['Email'], "text"),
                       GetSQLValueString($_POST['Password'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

  $insertGoTo = "login.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_localhost, $localhost);
$query_Recordset1 = "SELECT * FROM employees";
$Recordset1 = mysql_query($query_Recordset1, $localhost) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<p><a href="login.php">Login</a></p>
<form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">
  <table width="600" border="1">
    <tbody>
      <tr>
        <td><label for="EmpID">Emp ID:</label>
        <input type="text" name="EmpID" id="EmpID"></td>
      </tr>
      <tr>
        <td><label for="FirstName">First Name:</label>
        <input type="text" name="FirstName" id="FirstName"></td>
      </tr>
      <tr>
        <td><label for="LastName">Last Name:</label>
        <input type="text" name="LastName" id="LastName"></td>
      </tr>
      <tr>
        <td><label for="Email">Email:</label>
        <input type="text" name="Email" id="Email"></td>
      </tr>
      <tr>
        <td><label for="Password">Password:</label>
        <input type="password" name="Password" id="Password"></td>
      </tr>
      <tr>
        <td><input name="submit" type="submit" id="submit" value="Register"></td>
      </tr>
    </tbody>
  </table>
  <input type="hidden" name="MM_insert" value="form1">
</form>
</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
