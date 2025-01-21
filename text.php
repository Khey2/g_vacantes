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
  $updateSQL = sprintf("UPDATE con_cursos SET nombre=%s, tipo=%s, c_fecha_inicio=%s, c_fecha_termino=%s, fecha_cambio=%s, estatus=%s, IDcliente=%s, IDtema=%s WHERE IDcurso=%s",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['c_fecha_inicio'], "date"),
                       GetSQLValueString($_POST['c_fecha_termino'], "date"),
                       GetSQLValueString($_POST['fecha_cambio'], "date"),
                       GetSQLValueString($_POST['estatus'], "int"),
                       GetSQLValueString($_POST['IDcliente'], "int"),
                       GetSQLValueString($_POST['IDtema'], "int"),
                       GetSQLValueString($_POST['IDcurso'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "ok";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_cursos = "-1";
if (isset($_GET['IDcurso'])) {
  $colname_cursos = $_GET['IDcurso'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_cursos = sprintf("SELECT * FROM con_cursos WHERE IDcurso = %s", GetSQLValueString($colname_cursos, "int"));
$cursos = mysql_query($query_cursos, $vacantes) or die(mysql_error());
$row_cursos = mysql_fetch_assoc($cursos);
$totalRows_cursos = mysql_num_rows($cursos);
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
      <td nowrap="nowrap" align="right">IDcurso:</td>
      <td><?php echo $row_cursos['IDcurso']; ?></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Nombre:</td>
      <td><input type="text" name="nombre" value="<?php echo htmlentities($row_cursos['nombre'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Tipo:</td>
      <td><input type="text" name="tipo" value="<?php echo htmlentities($row_cursos['tipo'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">C_fecha_inicio:</td>
      <td><input type="text" name="c_fecha_inicio" value="<?php echo htmlentities($row_cursos['c_fecha_inicio'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">C_fecha_termino:</td>
      <td><input type="text" name="c_fecha_termino" value="<?php echo htmlentities($row_cursos['c_fecha_termino'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha_cambio:</td>
      <td><input type="text" name="fecha_cambio" value="<?php echo htmlentities($row_cursos['fecha_cambio'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Estatus:</td>
      <td><input type="text" name="estatus" value="<?php echo htmlentities($row_cursos['estatus'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDcliente:</td>
      <td><input type="text" name="IDcliente" value="<?php echo htmlentities($row_cursos['IDcliente'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDtema:</td>
      <td><input type="text" name="IDtema" value="<?php echo htmlentities($row_cursos['IDtema'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="IDcurso" value="<?php echo $row_cursos['IDcurso']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($cursos);
?>
