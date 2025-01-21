<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
$restrict->addLevel("3");
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->Execute();
//End Restrict Access To Page

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

if(isset($_GET['q'])) {
	
$q = $_GET['q'];
$s_diario = $q;
$s_diario_int = number_format($q * $factor_integracion, 2, '.', '');
$s_mensual = number_format($q * 30, 2, '.', ''); 
} else {
$q = 0.00;
$s_diario = 0.00;
$s_diario_int = 0.00;
$s_mensual = 0.00;
}


?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Diario Integrado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="number" step='0.01' min="0.01" max="500000.01" name="b_sueldo_diario_int" id="b_sueldo_diario_int" 
                                          class="form-control" placeholder="Sueldo diario integrado con decimales" value="<?php echo $s_diario_int; ?>" required="required">
										<span class="help-block">000.00 | <?php echo "Factor de Integración: ". $factor_integracion; ?> </span>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Mensual:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="number" step='0.01' min="0.01" max="500000.01" name="b_sueldo_mensual" id="b_sueldo_mensual" class="form-control" placeholder="Sueldo mensual con decimales" value="<?php echo $s_mensual; ?>" required="required">
										<span class="help-block">000.00</span>
										</div>
									</div>
									<!-- /basic text input -->
</body>
</html>