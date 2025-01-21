<?php require_once('Connections/vacas2.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');

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

mysql_select_db($database_vacas2, $vacas2);
$query_Recordset1 = "SELECT  IDcuenta, cuenta  FROM con_cuentas ORDER BY cuenta";
$Recordset1 = mysql_query($query_Recordset1, $vacas2) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

mysql_select_db($database_vacas2, $vacas2);
$query_Recordset2 = "SELECT  IDsubcuenta, IDcuenta, subcuenta FROM con_subcuentas ORDER BY subcuenta";
$Recordset2 = mysql_query($query_Recordset2, $vacas2) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);
?>
<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);


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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$factor_integracion = $row_variables['factor_integracion'];


$query_subcuenta = "SELECT * FROM con_subcuentas";
$subcuenta = mysql_query($query_subcuenta, $vacantes) or die(mysql_error());
$row_subcuenta = mysql_fetch_assoc($subcuenta);

$query_cuenta = "SELECT * FROM con_cuentas";
$cuenta = mysql_query($query_cuenta, $vacantes) or die(mysql_error());
$row_cuenta = mysql_fetch_assoc($cuenta);


if (isset($_GET["IDempleado"])) {
$_SESSION['IDempleado'] = $_GET['IDempleado'];
}else{
$_SESSION['IDempleado'] = 0;
}

if(isset($_GET['q'])) {
	
$q = intval($_GET['q']);
$s_diario = $q;
$s_diario_int = number_format($q * $factor_integracion, 2, '.', ',');
$s_mensual = number_format($q * 30, 2, '.', ',');
} else {
$q = 0;
$s_diario = 0;
$s_diario_int = 0;
$s_mensual = 0;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:wdg="http://ns.adobe.com/addt">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<script src="includes/common/js/base.js" type="text/javascript"></script>
<script src="includes/common/js/utility.js" type="text/javascript"></script>
<script type="text/javascript" src="includes/common/js/sigslot_core.js"></script>
<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js"></script>
<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js.php"></script>
<script type="text/javascript" src="includes/wdg/classes/JSRecordset.js"></script>
<script type="text/javascript" src="includes/wdg/classes/DependentDropdown.js"></script>
<?php
//begin JSRecordset
$jsObject_Recordset2 = new WDG_JsRecordset("Recordset2");
echo $jsObject_Recordset2->getOutput();
//end JSRecordset
?>
</head>

<body>

<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                                
                                  	<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cuenta:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDcuenta" id="IDcuenta" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_cuenta['IDcuenta']?>"<?php if (!(strcmp($row_cuenta['IDcuenta'], 1))) {echo "SELECTED";} ?>><?php echo $row_cuenta['cuenta']?></option>
												  <?php
												 } while ($row_cuenta = mysql_fetch_assoc($cuenta));
												   $rows = mysql_num_rows($cuenta);
												   if($rows > 0) {
												   mysql_data_seek($cuenta, 0);
												   $row_cuenta = mysql_fetch_assoc($cuenta);
												 } ?>
											</select>
										</div>
									</div>
                                
                                  	<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Subcuenta:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsubcuenta" id="IDsubcuenta" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_subcuenta['IDsubcuenta']?>"<?php if (!(strcmp($row_subcuenta['IDsubcuenta'], 2))) {echo "SELECTED";} ?>><?php echo $row_subcuenta['subcuenta']?></option>
												  <?php
												 } while ($row_subcuenta = mysql_fetch_assoc($subcuenta));
												   $rows = mysql_num_rows($subcuenta);
												   if($rows > 0) {
												   mysql_data_seek($subcuenta, 0);
												   $row_subcuenta = mysql_fetch_assoc($subcuenta);
												 } ?>
											</select>
										</div>
									</div>


                          <div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                         <button type="button" onClick="window.location.href='empleados_consulta.php'" class="btn btn-default btn-icon">Cancelar</button>
                            </div>
                          </div>

                      <input type="hidden" name="MM_update" value="form1">
                      <input type="hidden" name="IDempleado" value="<?php echo $row_contratos['IDempleado']; ?>">
                       </fieldset>
                      </form>
                      
                      
<select name="select" id="select">
  <?php
do {  
?>
  <option value="<?php echo $row_Recordset1['IDcuenta']?>"><?php echo $row_Recordset1['cuenta']?></option>
  <?php
} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
  $rows = mysql_num_rows($Recordset1);
  if($rows > 0) {
      mysql_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysql_fetch_assoc($Recordset1);
  }
?>
</select>
<select wdg:subtype="DependentDropdown" name="select1" id="select1" wdg:type="widget" wdg:recordset="Recordset2" wdg:displayfield="subcuenta" wdg:valuefield="IDsubcuenta" wdg:fkey="IDcuenta" wdg:triggerobject="select" wdg:selected="0">
</select>
</body>
</html>
<?php
mysql_free_result($Recordset1);

mysql_free_result($Recordset2);
?>
