<?php require_once('Connections/vacantes.php'); ?>
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
  $updateSQL = sprintf("UPDATE cov_casos SET IDempleado=%s, IDmotivo=%s, IDdoc_oficial=%s, IDestatus=%s, enfermedad_general=%s, fecha_inicio=%s, fecha_final=%s, IDreemplazo=%s, observaciones=%s, capturador=%s, fecha_captura=%s WHERE IDcovid=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDmotivo'], "text"),
                       GetSQLValueString($_POST['IDdoc_oficial'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['enfermedad_general'], "text"),
                       GetSQLValueString($_POST['fecha_inicio'], "date"),
                       GetSQLValueString($_POST['fecha_final'], "date"),
                       GetSQLValueString($_POST['IDreemplazo'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($_POST['capturador'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['IDcovid'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "detealles.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT * FROM cov_casos";
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDempleado:</td>
      <td><input type="text" name="IDempleado" value="<?php echo htmlentities($row_detalle['IDempleado'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDmotivo:</td>
      <td><input type="text" name="IDmotivo" value="<?php echo htmlentities($row_detalle['IDmotivo'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDdoc_oficial:</td>
      <td><input type="text" name="IDdoc_oficial" value="<?php echo htmlentities($row_detalle['IDdoc_oficial'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDestatus:</td>
      <td><input type="text" name="IDestatus" value="<?php echo htmlentities($row_detalle['IDestatus'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Enfermedad_general:</td>
      <td><input type="text" name="enfermedad_general" value="<?php echo htmlentities($row_detalle['enfermedad_general'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha_inicio:</td>
      <td><input type="text" name="fecha_inicio" value="<?php echo htmlentities($row_detalle['fecha_inicio'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha_final:</td>
      <td><input type="text" name="fecha_final" value="<?php echo htmlentities($row_detalle['fecha_final'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDreemplazo:</td>
      <td><input type="text" name="IDreemplazo" value="<?php echo htmlentities($row_detalle['IDreemplazo'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Observaciones:</td>
      <td><input type="text" name="observaciones" value="<?php echo htmlentities($row_detalle['observaciones'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Capturador:</td>
      <td><input type="text" name="capturador" value="<?php echo htmlentities($row_detalle['capturador'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha_captura:</td>
      <td><input type="text" name="fecha_captura" value="<?php echo htmlentities($row_detalle['fecha_captura'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="IDcovid" value="<?php echo $row_detalle['IDcovid']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($detalle);
?>
