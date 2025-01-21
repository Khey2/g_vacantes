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

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

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
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM vac_puestos LEFT JOIN prod_activos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE prod_activos.IDmatriz = $IDmatriz AND prod_activos.IDaplica_INC = 1 ORDER BY vac_puestos.denominacion ASC";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);


// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));



if(isset($_POST['el_puesto'])) { $_SESSION['el_puesto'] = $_POST['el_puesto']; } 
if(!isset($_SESSION['el_puesto'])) { $_SESSION['el_puesto'] = "";}

$el_puesto = $_SESSION['el_puesto'];

$c1 = "";
if($_SESSION['el_puesto'] > 0) {
$c1 = " AND prod_activos.IDpuesto = '$el_puesto'"; }


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


// actualizar 1
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_POST['IDcaptura'];

$updateSQL = sprintf("UPDATE inc_captura SET dias1=%s, horas1=%s, IDmotivo1=%s, obs1=%s, capturador=%s WHERE IDcaptura='$captura'",
                       GetSQLValueString($_POST['dias1'], "int"),
                       GetSQLValueString($_POST['horas1'], "int"),
                       GetSQLValueString($_POST['IDmotivo1'], "int"),
                       GetSQLValueString($_POST['obs1'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar 1
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO inc_captura (IDempleado, IDpuesto, fecha_captura, semana, anio, IDmatriz, capturador, dias1, horas1, IDmotivo1, obs1) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['dias1'], "int"),
                       GetSQLValueString($_POST['horas1'], "int"),
                       GetSQLValueString($_POST['IDmotivo1'], "int"),
                       GetSQLValueString($_POST['obs1'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

// actualizar 2
else if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	
$captura = $_POST['IDcaptura'];
	
  $updateSQL = sprintf("UPDATE inc_captura SET dias2=%s, horas2=%s, IDmotivo2=%s, obs2=%s, capturador=%s WHERE IDcaptura='$captura'",
                       GetSQLValueString($_POST['dias2'], "int"),
                       GetSQLValueString($_POST['horas2'], "int"),
                       GetSQLValueString($_POST['IDmotivo2'], "int"),
                       GetSQLValueString($_POST['obs2'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar 2
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO inc_captura (IDempleado, IDpuesto, fecha_captura, semana, anio, IDmatriz, capturador, dias2, horas2, IDmotivo2, obs2) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['dias2'], "int"),
                       GetSQLValueString($_POST['horas2'], "int"),
                       GetSQLValueString($_POST['IDmotivo2'], "int"),
                       GetSQLValueString($_POST['obs2'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

// actualizar 3
else if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
	
$captura = $_POST['IDcaptura'];
	
  $updateSQL = sprintf("UPDATE inc_captura SET IDmotivo3=%s, inc3=%s, diasf=%s, obs3=%s, capturador=%s WHERE IDcaptura='$captura'",
                       GetSQLValueString($_POST['IDmotivo3'], "int"),
                       GetSQLValueString($_POST['inc3'], "double"),
                       GetSQLValueString($_POST['diasf'], "double"),
                       GetSQLValueString($_POST['obs3'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=3";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar 3
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO inc_captura (IDempleado, IDpuesto, fecha_captura, semana, anio, IDmatriz, capturador, IDmotivo3, inc3, diasf, obs3) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['IDmotivo3'], "int"),
                       GetSQLValueString($_POST['inc3'], "double"),
                       GetSQLValueString($_POST['diasf'], "double"),
                       GetSQLValueString($_POST['obs2'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=3";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


// actualizar 4
else if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form4")) {
	
$captura = $_POST['IDcaptura'];

  $updateSQL = sprintf("UPDATE inc_captura SET perc=%s, prima=%s, obs4=%s, capturador=%s WHERE IDcaptura='$captura'",
                       GetSQLValueString($_POST['perc'], "text"),
                       GetSQLValueString($_POST['prima'], "text"),
                       GetSQLValueString($_POST['obs4'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=4";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar 4
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
  $insertSQL = sprintf("INSERT INTO inc_captura (IDempleado, IDpuesto, fecha_captura, semana, anio, IDmatriz, capturador, perc, prima, obs4) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['perc'], "text"),
                       GetSQLValueString($_POST['prima'], "text"),
                       GetSQLValueString($_POST['obs4'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=4";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

// actualizar 5
else if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
	
$captura = $_POST['IDcaptura'];

  $updateSQL = sprintf("UPDATE inc_captura SET lul=%s, mal=%s, mil=%s, jul=%s, vil=%s, sal=%s, dol=%s, luf=%s, maf=%s, mif=%s, juf=%s, vif=%s, saf=%s, dof=%s, pprueba=%s, obs5=%s, capturador=%s WHERE IDcaptura='$captura'",
                       GetSQLValueString($_POST['lul'], "int"),
                       GetSQLValueString($_POST['mal'], "int"),
                       GetSQLValueString($_POST['mil'], "int"),
                       GetSQLValueString($_POST['jul'], "int"),
                       GetSQLValueString($_POST['vil'], "int"),
                       GetSQLValueString($_POST['sal'], "int"),
                       GetSQLValueString($_POST['dol'], "int"),
                       GetSQLValueString($_POST['luf'], "int"),
                       GetSQLValueString($_POST['maf'], "int"),
                       GetSQLValueString($_POST['mif'], "int"),
                       GetSQLValueString($_POST['juf'], "int"),
                       GetSQLValueString($_POST['vif'], "int"),
                       GetSQLValueString($_POST['saf'], "int"),
                       GetSQLValueString($_POST['dof'], "int"),
                       GetSQLValueString($_POST['pprueba'], "int"),
                       GetSQLValueString($_POST['obs5'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=5";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar 5
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form5")) {
  $insertSQL = sprintf("INSERT INTO inc_captura (IDempleado, IDpuesto, fecha_captura, semana, anio, IDmatriz, capturador, lul, mal, mil, jul, vil, sal, dol, luf, maf, mif, juf, vif, saf, dof, pprueba, obs5) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['lul'], "int"),
                       GetSQLValueString($_POST['mal'], "int"),
                       GetSQLValueString($_POST['mil'], "int"),
                       GetSQLValueString($_POST['jul'], "int"),
                       GetSQLValueString($_POST['vil'], "int"),
                       GetSQLValueString($_POST['sal'], "int"),
                       GetSQLValueString($_POST['dol'], "int"),
                       GetSQLValueString($_POST['luf'], "int"),
                       GetSQLValueString($_POST['maf'], "int"),
                       GetSQLValueString($_POST['mif'], "int"),
                       GetSQLValueString($_POST['juf'], "int"),
                       GetSQLValueString($_POST['vif'], "int"),
                       GetSQLValueString($_POST['saf'], "int"),
                       GetSQLValueString($_POST['dof'], "int"),
                       GetSQLValueString($_POST['pprueba'], "int"),
                       GetSQLValueString($_POST['obs5'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "inc_cap_puesto_cal.php?IDcaptura=$captura&tipo=5";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDarea, prod_activos.denominacion, prod_activos.IDpuesto, inc_captura.IDcaptura, inc_captura.perc, inc_captura.prima, inc_captura.dias1, inc_captura.dias2, inc_captura.horas1, inc_captura.horas2, inc_captura.pprueba, inc_captura.obs1, inc_captura.obs2, inc_captura.obs3, inc_captura.obs4, inc_captura.obs5, inc_captura.IDmotivo1, inc_captura.IDmotivo2, inc_captura.IDmotivo3, inc_captura.inc1 AS INC1, inc_captura.inc2 AS INC2, inc_captura.inc3 AS INC3, inc_captura.inc6 AS INC6, inc_captura.inc3, inc_captura.inc6, inc_captura.diasf, inc_captura.inc4 AS INC4, inc_captura.inc5 AS INC5, prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, inc_captura.lul, inc_captura.mal, inc_captura.mil, inc_captura.jul, inc_captura.vil, inc_captura.sal, inc_captura.dol, inc_captura.luf, inc_captura.maf, inc_captura.mif, inc_captura.juf, inc_captura.vif, inc_captura.saf, inc_captura.dof, prod_activos.IDmatriz FROM prod_activos LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDaplica_INC = 1 " . $c1 ; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_costos = "SELECT Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc6) AS INC6, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5 FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
$row_costos = mysql_fetch_assoc($costos);
$totalRows_costos = mysql_num_rows($costos);

//repetidos
mysql_select_db($database_vacantes, $vacantes);
$query_repetidos = "SELECT Count(inc_captura.IDempleado) AS Repetidos FROM inc_captura WHERE inc_captura.IDmatriz = '$IDmatriz' AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio' GROUP BY inc_captura.IDempleado HAVING Repetidos > 1";
$repetidos = mysql_query($query_repetidos, $vacantes) or die(mysql_error());
$row_repetidos = mysql_fetch_assoc($repetidos);
$totalRows_repetidos = mysql_num_rows($repetidos);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
	<!-- /theme JS files -->
	<script>
      function load() {
       new Noty({
            text: 'Recuerda que debes justificar todas las incidencias por INCENTIVOS.',
            type: 'warning'
        }).show();
    }
	 window.onload = load;
     </script></head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/pheader.php'); ?>
			<!-- Content area -->
				<div class="content">


	                <!-- Content area -->
				<div class="content">
                
                        <!-- Basic alert -->
                        <?php if($totalRows_repetidos > 0) { ?>
					    <div class="alert bg-danger-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
                            <a href="inc_captura_puesto_repetidos.php">Existen Capturas repetidas, da clic aqui para corregir.</a>
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte semanal de incidencias</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido. En esta sección, podrás reportar las incidencias semanales de la Sucursal. Para cualquier duda con la información capturada, contacta con Guadalupe Mendiola, a la Ext. 1219 o al correo <a href="mailto:GEMendiola@sahuayo.mx">GEMendiola@sahuayo.mx</a></p>
							<p><strong>Horas Extra:</strong> Captura los días y horas extras trabajadas. No puedes exceder de 9 horas y 3 días consecutivos. No aplica a todos los puestos.</p>
							<p><strong>Compensación por Suplencia:</strong> Captura los días y horas extras trabajadas. No puedes exceder de 9 horas y 3 días consecutivos. No aplica a todos los puestos.</p>
							<p><strong>Premios por Viaje.</strong> Captura premios por día, tanto locales como foráneos. En el apartado de captura, se muesta el monto y tope autorizado para cada puesto. Solo aplica a puestos de Distribución.</p>
							<p><strong>Incentivos:</strong> Captura el monto del incentivo y asegurate de capturar la justificación. Sujeto a revisión.</p>
							<p><strong>Domingos Laborados:</strong> El concepto de Prima Dominical aplica en todos los casos, pero no así la percepción. Sujeto a revisión y autorización.</p>
							<p>Utiliza el siguiente filtro para mostrar a los empleados por puesto o captura el nombre del empleado en el filtro rápido. Los empleados que se muestran, son los empleados activos en Nómina; al igual que la captura de productividad, la base se actualiza los jueves. Da clic en el nombre del empleado, para ver el histórico de pago.</p>


									<div class="row">
                                    <div class="col-sm-2">
                                    <div class="alert alert-info">
										<a href="PXV/inc_reporte_semana_pxv.php?IDmatriz=<?php echo $IDmatriz; ?>">
                                        <span class="text-semibold">Descarga</span></a> CEDULA PxV con personal activo.</div>
									</div>
									<div class="col-sm-2">
									<div class="alert alert-info">
										<a href="inc_importar_pxv.php?IDmatriz=<?php echo $IDmatriz; ?>">
                                        <span class="text-semibold">Importa</span></a> CEDULA de PxV.</div>
									</div>


                                    <div class="col-sm-2">
                                    <div class="alert alert-success">
										<a href="PXV/inc_reporte_semana_domingos.php?IDmatriz=<?php echo $IDmatriz; ?>">
                                        <span class="text-semibold">Descarga</span></a> CEDULA Domingos Laborados.</div>
									</div>
									<div class="col-sm-2">
									<div class="alert alert-success">
										<a href="inc_importar_domingos.php?IDmatriz=<?php echo $IDmatriz; ?>">
                                        <span class="text-semibold">Importa</span></a> CEDULA  Domingos Laborados.</div>
									</div>

                                    <div class="col-sm-2">
                                    <div class="alert alert-warning">
										<a href="PXV/inc_reporte_semana_incentivos.php?IDmatriz=<?php echo $IDmatriz; ?>">
                                        <span class="text-semibold">Descarga</span></a> CEDULA Incentivos.</div>
									</div>
									<div class="col-sm-2">
									<div class="alert alert-warning">
										<a href="inc_importar_incentivos.php?IDmatriz=<?php echo $IDmatriz; ?>">
                                        <span class="text-semibold">Importa</span></a> CEDULA Incentivos.</div>
									</div>

									</div>


				<!-- Statistics with progress bar -->
					<div class="row">

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Horas Extra</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC1']; ?>
                                        <a href="inc_reporte_semana1.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $semana; ?>&anio=<?php echo $anio; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">x Suplencia</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC2']; ?>
                                        <a href="inc_reporte_semana2.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $semana; ?>&anio=<?php echo $anio; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">PxV</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC5']; ?>
                                        <a href="inc_reporte_semana5.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $semana; ?>&anio=<?php echo $anio; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Incentivos</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC3'] + $row_costos['INC6']; ?>
                                        <a href="inc_reporte_semana3.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $semana; ?>&anio=<?php echo $anio; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Domingos</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:</strong> <?php echo $row_costos['INC4']; ?>
                                        <a href="inc_reporte_semana4.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $semana; ?>&anio=<?php echo $anio; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
						  <div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Semana</h6>
									</div>

									<div class="media-right media-middle">
									</div>
								</div>

							<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
						    </div>
									  <span class="text-muted"><?php echo $semana; ?></span>
						  </div>
						</div>

					</div>

					<!-- /statistics with progress bar -->

                                            <p>&nbsp;</p>

                  <form method="POST" action="inc_cap_puesto.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                             <div class="col-lg-9">
                                 <select name="el_puesto" class="form-control">
                                   <option value="" <?php if (!(strcmp("", $el_puesto))) {echo "selected=\"selected\"";} ?>>Puesto: Todos</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if (!(strcmp($row_puestos['IDpuesto'], $el_puesto)))
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_puestos['denominacion']?> (<?php echo $row_puestos['area']?>)</option>
                                   <?php
                                  } while ($row_puestos = mysql_fetch_assoc($puestos));
                                  $rows = mysql_num_rows($puestos);
                                  if($rows > 0) {
                                      mysql_data_seek($puestos, 0);
                                      $row_puestos = mysql_fetch_assoc($puestos);
                                  } ?> </select>
						     </div>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                            </td>
					      </tr>
					    </tbody>
				    </table>
				</form>



				<div class="table-responsive">
                    <table class="table datatable-show-all">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>H.Extra</th>
                      <th>Suplencia</th>
                      <th>PxV</th>
                      <th>Inc/Festivos</th>
                      <th>Domingos</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php do { 
										
										$IDempleado = $row_detalle['IDempleado'];
										$query_costos = "SELECT * FROM pp_prueba WHERE IDempleado = $IDempleado AND IDestatus <> 6";
										$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
										$row_costos = mysql_fetch_assoc($costos);
										$totalRows_costos = mysql_num_rows($costos); echo "qqui".$totalRows_costos;

										?>
									    <tr>
									        <td><?php echo $row_detalle['IDempleado']; ?>&nbsp; </td>
										<td><a href="inc_detalle_empleado.php?IDempleado=<?php echo $row_detalle['IDempleado']?>"><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?><?php if ($totalRows_costos > 0){ echo "1 <i class='icon icon-warning'></i>";} ?></a>&nbsp; </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
									        <td>
											<?php if($row_detalle['IDcaptura'] == NULL) { ?>
											<button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a1" 
                                             data-toggle="modal" class="btn btn-info btn-icon">$0.00</button>
											<?php } else {?>
                                            <button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a1"  
                                             data-toggle="modal" class="btn btn-info btn-icon"><?php if ($row_detalle['INC1'] == '') {echo "$0.00";} else {echo "$".$row_detalle['INC1'];} ?></button><?php } ?>
											</td>
									        <td>
											<?php if($row_detalle['IDcaptura'] == NULL) { ?>
											<button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a2" 
                                             data-toggle="modal" class="btn btn-info btn-icon">$0.00</button>
											<?php } else {?>
                                            <button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a2"  
                                             data-toggle="modal" class="btn btn-info btn-icon"><?php if ($row_detalle['INC2'] == '') {echo "$0.00";} else {echo "$".$row_detalle['INC2'];} ?></button><?php } ?>
											</td>                                           
									        <td><?php if($row_detalle['IDcaptura'] == NULL) { ?>
											<button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a5"  
                                             data-toggle="modal" class="btn btn-info btn-icon">$0.00</button>
											<?php } else {?>
                                            <button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a5" 
                                             data-toggle="modal" class="btn btn-info btn-icon"><?php if ($row_detalle['INC5'] == '') {echo "$0.00";} else {echo "$".$row_detalle['INC5'];} ?></button><?php } ?>
											</td>  
									        <td><?php if($row_detalle['IDcaptura'] == NULL and $row_detalle['INC6'] == NULL) { ?>
											<button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a3" 
                                             data-toggle="modal" class="btn btn-info btn-icon">$0.00</button>
											<?php } else {?>
                                            <button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a3"  
                                             data-toggle="modal" class="btn btn-info btn-icon">$<?php echo $row_detalle['INC3'] + $row_detalle['INC6']; ?></button><?php } ?>
											</td>                                           
									        <td><?php if($row_detalle['IDcaptura'] == NULL) { ?>
											<button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a4" 
                                             data-toggle="modal" class="btn btn-info btn-icon">$0.00</button>
											<?php } else {?>
                                            <button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>a4"  
                                             data-toggle="modal" class="btn btn-info btn-icon"><?php if ($row_detalle['INC4'] == '') {echo "$0.00";} else {echo "$".$row_detalle['INC4'];} ?></button><?php } ?>
											 </td>                                           
										</tr>
            						     <?php  $moda4 = "assets/modals/inc4.php"; 	require($moda4); ?>
           						         <?php  $modal = "assets/modals/inc1.php"; 	require($modal); ?>
           						         <?php  $moda3 = "assets/modals/inc3.php"; 	require($moda3); ?>
           						         <?php  $moda5 = "assets/modals/inc5.php"; 	require($moda5); ?>
                                        
									      <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 
					  </div>

					</div>
					</div>
					<!-- /panel heading options -->


					<!-- Footer -->
					<div class="footer text-muted">
						&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->


		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>