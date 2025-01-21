<?php require_once('Connections/vacantes.php'); ?>
<?php
error_reporting(0);
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
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
if(isset($_POST['el_anio'])) { $anio = $_POST['el_anio'];} else {$anio = $row_variables['anio'];}
$desfase = $row_variables['dias_desfase'];
$fecha = date("Y-m-d"); // la fecha actual
$mes_actual = date("m");
if ($mes_actual == 01) {$mes_actual = 12;} else {$mes_actual = $mes_actual - 1;}
if(isset($_POST['el_anio'])) { $anio_actual = $_POST['el_anio'];} else {$anio_actual = $row_variables['anio'];}
$anio_anterior = $anio_actual - 1; // la fecha actual
//echo $fecha_inicio_mes_ok; 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$mamatriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

//echo $_SESSION['la_matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $mamatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$activa = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz  IN (3,12,13,14,16,18,20,23,25,26)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_anio = "SELECT * FROM vac_anios ORDER BY vac_anios.IDanio DESC";
$anio = mysql_query($query_anio, $vacantes) or die(mysql_error());
$row_anio = mysql_fetch_assoc($anio);
$totalRows_anio = mysql_num_rows($anio);

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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html59.js"></script>
    <!-- /theme JS files -->
</head>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Indicadores de Recursos Humanos.</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">A continuación, se muestra el ranking de rotación por mes y sucursal del año actual.</p>


                    <form method="POST" action="indicadors_mes_vd.php">
                	<table class="table">
						<tbody>							  
							<tr>
                            <td>
                             <select name="el_anio" class="form-control">
                               <option value="2020"<?php if (!(strcmp($anio_actual, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
                               <option value="2021"<?php if (!(strcmp($anio_actual, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
                               <option value="2022"<?php if (!(strcmp($anio_actual, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
                               <option value="2023"<?php if (!(strcmp($anio_actual, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
                               <option value="2024"<?php if (!(strcmp($anio_actual, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
                               <option value="2025"<?php if (!(strcmp($anio_actual, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
                             </select>
                            </td>
							<td>
                          <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
							<td>
                             </tr>
					    </tbody>
				    </table>
				</form>

				<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th class="col-lg-2">Matriz</th>
                      <th class="col-lg-1">Ene</th>
                      <th class="col-lg-1">Feb</th>
                      <th class="col-lg-1">Mar</th>
                      <th class="col-lg-1">Abr</th>
                      <th class="col-lg-1">May</th>
                      <th class="col-lg-1">Jun</th>
                      <th class="col-lg-1">Jul</th>
                      <th class="col-lg-1">Ags</th>
                      <th class="col-lg-1">Sep</th>
                      <th class="col-lg-1">Oct</th>
                      <th class="col-lg-1">Nov</th>
                      <th class="col-lg-1">Dic</th>
               		 </tr>
                    </thead>
                    <tbody>					
					<?php do {
					$IDmatriz = $row_lmatriz['IDmatriz'];
					$cada_matriz = $row_lmatriz['matriz'];
					$el_mes = $mes_actual;
					$el_anio = $anio_actual;




/////////////////////////////////////////////////////////////




// Resultado Mes 1 año actual
$fini2_ms1 = new DateTime($anio_anterior . '-01-01');
$fini2_ms1->modify('first day of this month');
$fini2_ms1k = $fini2_ms1->format('Y/m/d'); 

$fter2_ms1 = new DateTime($anio_anterior . '-01-01');
$fter2_ms1->modify('last day of this month');
$fter2_ms1k = $fter2_ms1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms21 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms1k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms1k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms21 = mysql_query($query_res_ms21, $vacantes) or die(mysql_error());
$row_res_ms21 = mysql_fetch_assoc($res_ms21);
$totalRows_res_ms21 = mysql_num_rows($res_ms21);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms21 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 1 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms21 = mysql_query($query_bja_ms21, $vacantes) or die(mysql_error());
$row_bja_ms21 = mysql_fetch_assoc($bja_ms21);
$totalRows_bja_ms21 = mysql_num_rows($bja_ms21);

if($mes_actual == 1) {$RotTotalPREVBA =  $row_bja_ms21['TOTAL'] / $row_res_ms21['TOTAL'];} 

// Resultado Mes 2 año actual
$fini2_ms2 = new DateTime($anio_anterior . '-02-01');
$fini2_ms2->modify('first day of this month');
$fini2_ms2k = $fini2_ms2->format('Y/m/d'); 

$fter2_ms2 = new DateTime($anio_anterior . '-02-01');
$fter2_ms2->modify('last day of this month');
$fter2_ms2k = $fter2_ms2->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms22 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms2k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms2k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms22 = mysql_query($query_res_ms22, $vacantes) or die(mysql_error());
$row_res_ms22 = mysql_fetch_assoc($res_ms22);
$totalRows_res_ms22 = mysql_num_rows($res_ms22);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms22 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 2 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms22 = mysql_query($query_bja_ms22, $vacantes) or die(mysql_error());
$row_bja_ms22 = mysql_fetch_assoc($bja_ms22);
$totalRows_bja_ms22 = mysql_num_rows($bja_ms22);

if($mes_actual == 2) {$RotTotalPREVBA =  $row_bja_ms22['TOTAL'] / $row_res_ms22['TOTAL'];} 

// Resultado Mes 3 año actual
$fini2_ms3 = new DateTime($anio_anterior . '-03-01');
$fini2_ms3->modify('first day of this month');
$fini2_ms3k = $fini2_ms3->format('Y/m/d'); 

$fter2_ms3 = new DateTime($anio_anterior . '-03-01');
$fter2_ms3->modify('last day of this month');
$fter2_ms3k = $fter2_ms3->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms23 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms3k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms3k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms23 = mysql_query($query_res_ms23, $vacantes) or die(mysql_error());
$row_res_ms23 = mysql_fetch_assoc($res_ms23);
$totalRows_res_ms23 = mysql_num_rows($res_ms23);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms23 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 3 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms23 = mysql_query($query_bja_ms23, $vacantes) or die(mysql_error());
$row_bja_ms23 = mysql_fetch_assoc($bja_ms23);
$totalRows_bja_ms23 = mysql_num_rows($bja_ms23);

if($mes_actual == 3) {$RotTotalPREVBA =  $row_bja_ms23['TOTAL'] / $row_res_ms23['TOTAL'];} 

// Resultado Mes 4 año actual
$fini2_ms4 = new DateTime($anio_anterior . '-04-01');
$fini2_ms4->modify('first day of this month');
$fini2_ms4k = $fini2_ms4->format('Y/m/d'); 

$fter2_ms4 = new DateTime($anio_anterior . '-04-01');
$fter2_ms4->modify('last day of this month');
$fter2_ms4k = $fter2_ms4->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms24 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms4k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms4k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms24 = mysql_query($query_res_ms24, $vacantes) or die(mysql_error());
$row_res_ms24 = mysql_fetch_assoc($res_ms24);
$totalRows_res_ms24 = mysql_num_rows($res_ms24);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms24 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 4 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms24 = mysql_query($query_bja_ms24, $vacantes) or die(mysql_error());
$row_bja_ms24 = mysql_fetch_assoc($bja_ms24);
$totalRows_bja_ms24 = mysql_num_rows($bja_ms24);

if($mes_actual == 4) {$RotTotalPREVBA =  $row_bja_ms24['TOTAL'] / $row_res_ms24['TOTAL'];} 

// Resultado Mes 5 año actual
$fini2_ms5 = new DateTime($anio_anterior . '-05-01');
$fini2_ms5->modify('first day of this month');
$fini2_ms5k = $fini2_ms5->format('Y/m/d'); 

$fter2_ms5 = new DateTime($anio_anterior . '-05-01');
$fter2_ms5->modify('last day of this month');
$fter2_ms5k = $fter2_ms5->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms25 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms5k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms5k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms25 = mysql_query($query_res_ms25, $vacantes) or die(mysql_error());
$row_res_ms25 = mysql_fetch_assoc($res_ms25);
$totalRows_res_ms25 = mysql_num_rows($res_ms25);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms25 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 5 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms25 = mysql_query($query_bja_ms25, $vacantes) or die(mysql_error());
$row_bja_ms25 = mysql_fetch_assoc($bja_ms25);
$totalRows_bja_ms25 = mysql_num_rows($bja_ms25);

if($mes_actual == 5) {$RotTotalPREVBA =  $row_bja_ms25['TOTAL'] / $row_res_ms25['TOTAL'];} 

// Resultado Mes 6 año actual
$fini2_ms6 = new DateTime($anio_anterior . '-06-01');
$fini2_ms6->modify('first day of this month');
$fini2_ms6k = $fini2_ms6->format('Y/m/d'); 

$fter2_ms6 = new DateTime($anio_anterior . '-06-01');
$fter2_ms6->modify('last day of this month');
$fter2_ms6k = $fter2_ms6->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms26 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms6k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms6k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms26 = mysql_query($query_res_ms26, $vacantes) or die(mysql_error());
$row_res_ms26 = mysql_fetch_assoc($res_ms26);
$totalRows_res_ms26 = mysql_num_rows($res_ms26);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms26 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 6 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms26 = mysql_query($query_bja_ms26, $vacantes) or die(mysql_error());
$row_bja_ms26 = mysql_fetch_assoc($bja_ms26);
$totalRows_bja_ms26 = mysql_num_rows($bja_ms26);

if($mes_actual == 6) {$RotTotalPREVBA =  $row_bja_ms26['TOTAL'] / $row_res_ms26['TOTAL'];} 

// Resultado Mes 7 año actual
$fini2_ms7 = new DateTime($anio_anterior . '-07-01');
$fini2_ms7->modify('first day of this month');
$fini2_ms7k = $fini2_ms7->format('Y/m/d'); 

$fter2_ms7 = new DateTime($anio_anterior . '-07-01');
$fter2_ms7->modify('last day of this month');
$fter2_ms7k = $fter2_ms7->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms27 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms7k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms7k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms27 = mysql_query($query_res_ms27, $vacantes) or die(mysql_error());
$row_res_ms27 = mysql_fetch_assoc($res_ms27);
$totalRows_res_ms27 = mysql_num_rows($res_ms27);

$row_res_ms27['TOTAL'] = $row_res_ms27['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms27 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 7 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms27 = mysql_query($query_bja_ms27, $vacantes) or die(mysql_error());
$row_bja_ms27 = mysql_fetch_assoc($bja_ms27);
$totalRows_bja_ms27 = mysql_num_rows($bja_ms27);

if($mes_actual == 7) {$RotTotalPREVBA =  $row_bja_ms27['TOTAL'] / $row_res_ms27['TOTAL'];} 

// Resultado Mes 8 año actual
$fini2_ms8 = new DateTime($anio_anterior . '-08-01');
$fini2_ms8->modify('first day of this month');
$fini2_ms8k = $fini2_ms8->format('Y/m/d'); 

$fter2_ms8 = new DateTime($anio_anterior . '-08-01');
$fter2_ms8->modify('last day of this month');
$fter2_ms8k = $fter2_ms8->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms28 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms8k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms8k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms28 = mysql_query($query_res_ms28, $vacantes) or die(mysql_error());
$row_res_ms28 = mysql_fetch_assoc($res_ms28);
$totalRows_res_ms28 = mysql_num_rows($res_ms28);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms28 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 8 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms28 = mysql_query($query_bja_ms28, $vacantes) or die(mysql_error());
$row_bja_ms28 = mysql_fetch_assoc($bja_ms28);
$totalRows_bja_ms28 = mysql_num_rows($bja_ms28);

if($mes_actual == 8) {$RotTotalPREVBA =  $row_bja_ms28['TOTAL'] / $row_res_ms28['TOTAL'];} 

// Resultado Mes 9 año actual
$fini2_ms9 = new DateTime($anio_anterior . '-09-01');
$fini2_ms9->modify('first day of this month');
$fini2_ms9k = $fini2_ms9->format('Y/m/d'); 

$fter2_ms9 = new DateTime($anio_anterior . '-09-01');
$fter2_ms9->modify('last day of this month');
$fter2_ms9k = $fter2_ms9->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms29 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms9k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms9k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms29 = mysql_query($query_res_ms29, $vacantes) or die(mysql_error());
$row_res_ms29 = mysql_fetch_assoc($res_ms29);
$totalRows_res_ms29 = mysql_num_rows($res_ms29);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms29 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 9 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms29 = mysql_query($query_bja_ms29, $vacantes) or die(mysql_error());
$row_bja_ms29 = mysql_fetch_assoc($bja_ms29);
$totalRows_bja_ms29 = mysql_num_rows($bja_ms29);

if($mes_actual == 9) {$RotTotalPREVBA =  $row_bja_ms29['TOTAL'] / $row_res_ms29['TOTAL'];} 

// Resultado Mes 10 año actual
$fini2_ms10 = new DateTime($anio_anterior . '-10-01');
$fini2_ms10->modify('first day of this month');
$fini2_ms10k = $fini2_ms10->format('Y/m/d'); 

$fter2_ms10 = new DateTime($anio_anterior . '-10-01');
$fter2_ms10->modify('last day of this month');
$fter2_ms10k = $fter2_ms10->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms210 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms10k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms10k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms210 = mysql_query($query_res_ms210, $vacantes) or die(mysql_error());
$row_res_ms210 = mysql_fetch_assoc($res_ms210);
$totalRows_res_ms210 = mysql_num_rows($res_ms210);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms210 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 10 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms210 = mysql_query($query_bja_ms210, $vacantes) or die(mysql_error());
$row_bja_ms210 = mysql_fetch_assoc($bja_ms210);
$totalRows_bja_ms210 = mysql_num_rows($bja_ms210);

if($mes_actual == 10) {$RotTotalPREVBA =  $row_bja_ms210['TOTAL'] / $row_res_ms210['TOTAL'];} 

// Resultado Mes 11 año actual
$fini2_ms11 = new DateTime($anio_anterior . '-11-01');
$fini2_ms11->modify('first day of this month');
$fini2_ms11k = $fini2_ms11->format('Y/m/d'); 

$fter2_ms11 = new DateTime($anio_anterior . '-11-01');
$fter2_ms11->modify('last day of this month');
$fter2_ms11k = $fter2_ms11->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms211 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms11k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms11k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms211 = mysql_query($query_res_ms211, $vacantes) or die(mysql_error());
$row_res_ms211 = mysql_fetch_assoc($res_ms211);
$totalRows_res_ms211 = mysql_num_rows($res_ms211);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms211 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 11 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms211 = mysql_query($query_bja_ms211, $vacantes) or die(mysql_error());
$row_bja_ms211 = mysql_fetch_assoc($bja_ms211);
$totalRows_bja_ms211 = mysql_num_rows($bja_ms211);

if($mes_actual == 11) {$RotTotalPREVBA =  $row_bja_ms211['TOTAL'] / $row_res_ms211['TOTAL'];} 

// Resultado Mes 12 año actual
$fini2_ms12 = new DateTime($anio_anterior . '-12-01');
$fini2_ms12->modify('first day of this month');
$fini2_ms12k = $fini2_ms12->format('Y/m/d'); 

$fter2_ms12 = new DateTime($anio_anterior . '-12-01');
$fter2_ms12->modify('last day of this month');
$fter2_ms12k = $fter2_ms12->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms212 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter2_ms12k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini2_ms12k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms212 = mysql_query($query_res_ms212, $vacantes) or die(mysql_error());
$row_res_ms212 = mysql_fetch_assoc($res_ms212);
$totalRows_res_ms212 = mysql_num_rows($res_ms212);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms212 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 12 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_ms212 = mysql_query($query_bja_ms212, $vacantes) or die(mysql_error());
$row_bja_ms212 = mysql_fetch_assoc($bja_ms212);
$totalRows_bja_ms212 = mysql_num_rows($bja_ms212);

if($mes_actual == 12) {$RotTotalPREVBA =  $row_bja_ms212['TOTAL'] / $row_res_ms212['TOTAL'];}

// acumualdo total año
$Acumulado2 = $RotTotal2M1 + $RotTotal2M2 + $RotTotal2M3 + $RotTotal2M4 + $RotTotal2M5 + $RotTotal2M6 + $RotTotal2M7 + $RotTotal2M8 + $RotTotal2M9 + $RotTotal2M10 + $RotTotal2M11 + $RotTotal2M12;

/////////////////////////////////////////////////////////////



					
// Resultado Mes 1 año actual
$fini_ms1 = new DateTime($anio_actual . '-01-01');
$fini_ms1->modify('first day of this month');
$fini_ms1k = $fini_ms1->format('Y/m/d'); 

$fter_ms1 = new DateTime($anio_actual . '-01-01');
$fter_ms1->modify('last day of this month');
$fter_ms1k = $fter_ms1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms1k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms1k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms1 = mysql_query($query_res_ms1, $vacantes) or die(mysql_error());
$row_res_ms1 = mysql_fetch_assoc($res_ms1);
$totalRows_res_ms1 = mysql_num_rows($res_ms1);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 1 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms1 = mysql_query($query_bja_ms1, $vacantes) or die(mysql_error());
$row_bja_ms1 = mysql_fetch_assoc($bja_ms1);
$totalRows_bja_ms1 = mysql_num_rows($bja_ms1);

if($row_bja_ms1['TOTAL'] > 0 and $row_res_ms1['TOTAL'] > 0) {$RotTotalM1 =  $row_bja_ms1['TOTAL'] / $row_res_ms1['TOTAL'];} else {$RotTotalM1 = 0;}
if($mes_actual == 1) {$RotTotalPREVBB =  $row_bja_ms1['TOTAL'] / $row_res_ms1['TOTAL'];}


// Resultado Mes 2 año actual
$fini_ms2 = new DateTime($anio_actual . '-02-01');
$fini_ms2->modify('first day of this month');
$fini_ms2k = $fini_ms2->format('Y/m/d'); 

$fter_ms2 = new DateTime($anio_actual . '-02-01');
$fter_ms2->modify('last day of this month');
$fter_ms2k = $fter_ms2->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms2k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms2k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms2 = mysql_query($query_res_ms2, $vacantes) or die(mysql_error());
$row_res_ms2 = mysql_fetch_assoc($res_ms2);
$totalRows_res_ms2 = mysql_num_rows($res_ms2);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 2 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms2 = mysql_query($query_bja_ms2, $vacantes) or die(mysql_error());
$row_bja_ms2 = mysql_fetch_assoc($bja_ms2);
$totalRows_bja_ms2 = mysql_num_rows($bja_ms2);

if($row_bja_ms2['TOTAL'] > 0 and $row_res_ms2['TOTAL'] > 0) {$RotTotalM2 =  $row_bja_ms2['TOTAL'] / $row_res_ms2['TOTAL'];} else {$RotTotalM2 = 0;}
if($mes_actual == 2) {$RotTotalPREVBB =  $row_bja_ms2['TOTAL'] / $row_res_ms2['TOTAL'];}

// Resultado Mes 3 año actual
$fini_ms3 = new DateTime($anio_actual . '-03-01');
$fini_ms3->modify('first day of this month');
$fini_ms3k = $fini_ms3->format('Y/m/d'); 

$fter_ms3 = new DateTime($anio_actual . '-03-01');
$fter_ms3->modify('last day of this month');
$fter_ms3k = $fter_ms3->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms3 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms3k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms3k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms3 = mysql_query($query_res_ms3, $vacantes) or die(mysql_error());
$row_res_ms3 = mysql_fetch_assoc($res_ms3);
$totalRows_res_ms3 = mysql_num_rows($res_ms3);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms3 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 3 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms3 = mysql_query($query_bja_ms3, $vacantes) or die(mysql_error());
$row_bja_ms3 = mysql_fetch_assoc($bja_ms3);
$totalRows_bja_ms3 = mysql_num_rows($bja_ms3);

if($row_bja_ms3['TOTAL'] > 0 and $row_res_ms3['TOTAL'] > 0) {$RotTotalM3 =  $row_bja_ms3['TOTAL'] / $row_res_ms3['TOTAL'];} else {$RotTotalM3 = 0;}
if($mes_actual == 3) {$RotTotalPREVBB =  $row_bja_ms3['TOTAL'] / $row_res_ms3['TOTAL'];}

// Resultado Mes 4 año actual
$fini_ms4 = new DateTime($anio_actual . '-04-01');
$fini_ms4->modify('first day of this month');
$fini_ms4k = $fini_ms4->format('Y/m/d'); 

$fter_ms4 = new DateTime($anio_actual . '-04-01');
$fter_ms4->modify('last day of this month');
$fter_ms4k = $fter_ms4->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms4 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms4k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms4k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms4 = mysql_query($query_res_ms4, $vacantes) or die(mysql_error());
$row_res_ms4 = mysql_fetch_assoc($res_ms4);
$totalRows_res_ms4 = mysql_num_rows($res_ms4);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms4 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 4 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms4 = mysql_query($query_bja_ms4, $vacantes) or die(mysql_error());
$row_bja_ms4 = mysql_fetch_assoc($bja_ms4);
$totalRows_bja_ms4 = mysql_num_rows($bja_ms4);

if($row_bja_ms4['TOTAL'] > 0 and $row_res_ms4['TOTAL'] > 0) {$RotTotalM4 =  $row_bja_ms4['TOTAL'] / $row_res_ms4['TOTAL'];} else {$RotTotalM4 = 0;}
if($mes_actual == 4) {$RotTotalPREVBB =  $row_bja_ms4['TOTAL'] / $row_res_ms4['TOTAL'];}

// Resultado Mes 5 año actual
$fini_ms5 = new DateTime($anio_actual . '-05-01');
$fini_ms5->modify('first day of this month');
$fini_ms5k = $fini_ms5->format('Y/m/d'); 

$fter_ms5 = new DateTime($anio_actual . '-05-01');
$fter_ms5->modify('last day of this month');
$fter_ms5k = $fter_ms5->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms5 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms5k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms5k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms5 = mysql_query($query_res_ms5, $vacantes) or die(mysql_error());
$row_res_ms5 = mysql_fetch_assoc($res_ms5);
$totalRows_res_ms5 = mysql_num_rows($res_ms5);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms5 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 5 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms5 = mysql_query($query_bja_ms5, $vacantes) or die(mysql_error());
$row_bja_ms5 = mysql_fetch_assoc($bja_ms5);
$totalRows_bja_ms5 = mysql_num_rows($bja_ms5);

if($row_bja_ms5['TOTAL'] > 0 and $row_res_ms5['TOTAL'] > 0) {$RotTotalM5 =  $row_bja_ms5['TOTAL'] / $row_res_ms5['TOTAL'];} else {$RotTotalM5 = 0;}
if($mes_actual == 5) {$RotTotalPREVBB =  $row_bja_ms5['TOTAL'] / $row_res_ms5['TOTAL'];}

// Resultado Mes 6 año actual
$fini_ms6 = new DateTime($anio_actual . '-06-01');
$fini_ms6->modify('first day of this month');
$fini_ms6k = $fini_ms6->format('Y/m/d'); 

$fter_ms6 = new DateTime($anio_actual . '-06-01');
$fter_ms6->modify('last day of this month');
$fter_ms6k = $fter_ms6->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms6 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms6k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms6k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms6 = mysql_query($query_res_ms6, $vacantes) or die(mysql_error());
$row_res_ms6 = mysql_fetch_assoc($res_ms6);
$totalRows_res_ms6 = mysql_num_rows($res_ms6);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms6 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 6 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms6 = mysql_query($query_bja_ms6, $vacantes) or die(mysql_error());
$row_bja_ms6 = mysql_fetch_assoc($bja_ms6);
$totalRows_bja_ms6 = mysql_num_rows($bja_ms6);

if($row_bja_ms6['TOTAL'] > 0 and $row_res_ms6['TOTAL'] > 0) {$RotTotalM6 =  $row_bja_ms6['TOTAL'] / $row_res_ms6['TOTAL'];} else {$RotTotalM6 = 0;}
if($mes_actual == 6) {$RotTotalPREVBB =  $row_bja_ms6['TOTAL'] / $row_res_ms6['TOTAL'];}

// Resultado Mes 7 año actual
$fini_ms7 = new DateTime($anio_actual . '-07-01');
$fini_ms7->modify('first day of this month');
$fini_ms7k = $fini_ms7->format('Y/m/d'); 

$fter_ms7 = new DateTime($anio_actual . '-07-01');
$fter_ms7->modify('last day of this month');
$fter_ms7k = $fter_ms7->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms7 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms7k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms7k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms7 = mysql_query($query_res_ms7, $vacantes) or die(mysql_error());
$row_res_ms7 = mysql_fetch_assoc($res_ms7);
$totalRows_res_ms7 = mysql_num_rows($res_ms7);

$row_res_ms7['TOTAL'] = $row_res_ms7['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms7 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 7 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms7 = mysql_query($query_bja_ms7, $vacantes) or die(mysql_error());
$row_bja_ms7 = mysql_fetch_assoc($bja_ms7);
$totalRows_bja_ms7 = mysql_num_rows($bja_ms7);

if($row_bja_ms7['TOTAL'] > 0 and $row_res_ms7['TOTAL'] > 0) {$RotTotalM7 =  $row_bja_ms7['TOTAL'] / $row_res_ms7['TOTAL'];} else {$RotTotalM7 = 0;}
if($mes_actual == 7) {$RotTotalPREVBB =  $row_bja_ms7['TOTAL'] / $row_res_ms7['TOTAL'];}

// Resultado Mes 8 año actual
$fini_ms8 = new DateTime($anio_actual . '-08-01');
$fini_ms8->modify('first day of this month');
$fini_ms8k = $fini_ms8->format('Y/m/d'); 

$fter_ms8 = new DateTime($anio_actual . '-08-01');
$fter_ms8->modify('last day of this month');
$fter_ms8k = $fter_ms8->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms8 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms8k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms8k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms8 = mysql_query($query_res_ms8, $vacantes) or die(mysql_error());
$row_res_ms8 = mysql_fetch_assoc($res_ms8);
$totalRows_res_ms8 = mysql_num_rows($res_ms8);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms8 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 8 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms8 = mysql_query($query_bja_ms8, $vacantes) or die(mysql_error());
$row_bja_ms8 = mysql_fetch_assoc($bja_ms8);
$totalRows_bja_ms8 = mysql_num_rows($bja_ms8);

if($row_bja_ms8['TOTAL'] > 0 and $row_res_ms8['TOTAL'] > 0) {$RotTotalM8 =  $row_bja_ms8['TOTAL'] / $row_res_ms8['TOTAL'];} else {$RotTotalM8 = 0;}
if($mes_actual == 8) {$RotTotalPREVBB =  $row_bja_ms8['TOTAL'] / $row_res_ms8['TOTAL'];}

// Resultado Mes 9 año actual
$fini_ms9 = new DateTime($anio_actual . '-09-01');
$fini_ms9->modify('first day of this month');
$fini_ms9k = $fini_ms9->format('Y/m/d'); 

$fter_ms9 = new DateTime($anio_actual . '-09-01');
$fter_ms9->modify('last day of this month');
$fter_ms9k = $fter_ms9->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms9 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms9k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms9k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms9 = mysql_query($query_res_ms9, $vacantes) or die(mysql_error());
$row_res_ms9 = mysql_fetch_assoc($res_ms9);
$totalRows_res_ms9 = mysql_num_rows($res_ms9);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms9 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 9 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms9 = mysql_query($query_bja_ms9, $vacantes) or die(mysql_error());
$row_bja_ms9 = mysql_fetch_assoc($bja_ms9);
$totalRows_bja_ms9 = mysql_num_rows($bja_ms9);

if($row_bja_ms9['TOTAL'] > 0 and $row_res_ms9['TOTAL'] > 0) {$RotTotalM9 =  $row_bja_ms9['TOTAL'] / $row_res_ms9['TOTAL'];} else {$RotTotalM9 = 0;}
if($mes_actual == 9) {$RotTotalPREVBB =  $row_bja_ms9['TOTAL'] / $row_res_ms9['TOTAL'];}

// Resultado Mes 10 año actual
$fini_ms10 = new DateTime($anio_actual . '-10-01');
$fini_ms10->modify('first day of this month');
$fini_ms10k = $fini_ms10->format('Y/m/d'); 

$fter_ms10 = new DateTime($anio_actual . '-10-01');
$fter_ms10->modify('last day of this month');
$fter_ms10k = $fter_ms10->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms10 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms10k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms10k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms10 = mysql_query($query_res_ms10, $vacantes) or die(mysql_error());
$row_res_ms10 = mysql_fetch_assoc($res_ms10);
$totalRows_res_ms10 = mysql_num_rows($res_ms10);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms10 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 10 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms10 = mysql_query($query_bja_ms10, $vacantes) or die(mysql_error());
$row_bja_ms10 = mysql_fetch_assoc($bja_ms10);
$totalRows_bja_ms10 = mysql_num_rows($bja_ms10);

if($row_bja_ms10['TOTAL'] > 0 and $row_res_ms10['TOTAL'] > 0) {$RotTotalM10 =  $row_bja_ms10['TOTAL'] / $row_res_ms10['TOTAL'];} else {$RotTotalM10 = 0;}
if($mes_actual == 10) {$RotTotalPREVBB =  $row_bja_ms10['TOTAL'] / $row_res_ms10['TOTAL'];}

// Resultado Mes 11 año actual
$fini_ms11 = new DateTime($anio_actual . '-11-01');
$fini_ms11->modify('first day of this month');
$fini_ms11k = $fini_ms11->format('Y/m/d'); 

$fter_ms11 = new DateTime($anio_actual . '-11-01');
$fter_ms11->modify('last day of this month');
$fter_ms11k = $fter_ms11->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms11 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms11k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms11k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms11 = mysql_query($query_res_ms11, $vacantes) or die(mysql_error());
$row_res_ms11 = mysql_fetch_assoc($res_ms11);
$totalRows_res_ms11 = mysql_num_rows($res_ms11);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms11 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 11 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms11 = mysql_query($query_bja_ms11, $vacantes) or die(mysql_error());
$row_bja_ms11 = mysql_fetch_assoc($bja_ms11);
$totalRows_bja_ms11 = mysql_num_rows($bja_ms11);

if($row_bja_ms11['TOTAL'] > 0 and $row_res_ms11['TOTAL'] > 0) {$RotTotalM11 =  $row_bja_ms11['TOTAL'] / $row_res_ms11['TOTAL'];} else {$RotTotalM11 = 0;}
if($mes_actual == 11) {$RotTotalPREVBB =  $row_bja_ms11['TOTAL'] / $row_res_ms11['TOTAL'];}

// Resultado Mes 12 año actual
$fini_ms12 = new DateTime($anio_actual . '-12-01');
$fini_ms12->modify('first day of this month');
$fini_ms12k = $fini_ms12->format('Y/m/d'); 

$fter_ms12 = new DateTime($anio_actual . '-12-01');
$fter_ms12->modify('last day of this month');
$fter_ms12k = $fter_ms12->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms12 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms12k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms12k') AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 ";
$res_ms12 = mysql_query($query_res_ms12, $vacantes) or die(mysql_error());
$row_res_ms12 = mysql_fetch_assoc($res_ms12);
$totalRows_res_ms12 = mysql_num_rows($res_ms12);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms12 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDarea IN (2,3,6) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 12 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms12 = mysql_query($query_bja_ms12, $vacantes) or die(mysql_error());
$row_bja_ms12 = mysql_fetch_assoc($bja_ms12);
$totalRows_bja_ms12 = mysql_num_rows($bja_ms12);

if($row_bja_ms12['TOTAL'] > 0 and $row_res_ms12['TOTAL'] > 0) {$RotTotalM12 =  $row_bja_ms12['TOTAL'] / $row_res_ms12['TOTAL'];} else {$RotTotalM12 = 0;}
if($mes_actual == 12) {$RotTotalPREVBB =  $row_bja_ms12['TOTAL'] / $row_res_ms12['TOTAL'];} 

// acumualdo total año
$Acumulado = $RotTotalM1 + $RotTotalM2 + $RotTotalM3 + $RotTotalM4 + $RotTotalM5 + $RotTotalM6 + $RotTotalM7 + $RotTotalM8 + $RotTotalM9 + $RotTotalM10 + $RotTotalM11 + $RotTotalM12;

//objetivo y total
mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT * FROM ind_objetivo WHERE IDmatriz = $IDmatriz AND anio = $el_anio";
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados_anterior = "SELECT * FROM ind_objetivo WHERE IDmatriz = $IDmatriz AND anio = $anio_anterior";
$resultados_anterior = mysql_query($query_resultados_anterior, $vacantes) or die(mysql_error());
$row_resultados_anterior = mysql_fetch_assoc($resultados_anterior);
$totalRows_resultados_anterior = mysql_num_rows($resultados_anterior);

if ($Acumulado > 0) {
?>
                 	<tr>
                    <td>
					<?php echo $cada_matriz; ?>
                    </td>
                    <td><?php $row_bja_ms1['TOTAL'] ." | ". $row_res_ms1['TOTAL'];  ?>
					<?php 
					if($RotTotalM1 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM1 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM1 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM1 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms2['TOTAL'] ." | ". $row_res_ms2['TOTAL'];  ?>
					<?php 
					if($RotTotalM2 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM2 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM2 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM2 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms3['TOTAL'] ." | ". $row_res_ms3['TOTAL'];  ?>
					<?php 
					if($RotTotalM3 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM3 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM3 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM3 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms4['TOTAL'] ." | ". $row_res_ms4['TOTAL'];  ?>
					<?php 
					if($RotTotalM4 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM4 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM4 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM4 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms5['TOTAL'] ." | ". $row_res_ms5['TOTAL'];  ?>
					<?php 
					if($RotTotalM5 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM5 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM5 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM5 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms6['TOTAL'] ." | ". $row_res_ms6['TOTAL'];  ?>
					<?php 
					if($RotTotalM6 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM6 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM6 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM6 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms7['TOTAL'] ." | ". $row_res_ms7['TOTAL'];  ?>
					<?php 
					if($RotTotalM7 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM7 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM7 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM7 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms8['TOTAL'] ." | ". $row_res_ms8['TOTAL'];  ?>
					<?php 
					if($RotTotalM8 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM8 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM8 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM8 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms9['TOTAL'] ." | ". $row_res_ms9['TOTAL'];  ?>
					<?php 
					if($RotTotalM9 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM9 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM9 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM9 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms10['TOTAL'] ." | ". $row_res_ms10['TOTAL'];  ?>
					<?php 
					if($RotTotalM10 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM10 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM10 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM10 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms11['TOTAL'] ." | ". $row_res_ms11['TOTAL'];  ?>
					<?php 
					if($RotTotalM11 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM11 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM11 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM11 * 100, 1);}
					?>
                    </td>
                    <td><?php $row_bja_ms12['TOTAL'] ." | ". $row_res_ms12['TOTAL'];  ?>
					<?php 
					if($RotTotalM12 == 0)
					{ echo " | "; }
					else if(( round($RotTotalM12 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo  " | ". round($RotTotalM12 * 100, 1);}
					else
					{echo  " | ". round($RotTotalM12 * 100, 1);}
					?>
                    </td>
					 <?php }	} while ($row_lmatriz = mysql_fetch_assoc($lmatriz)); ?>
                    </tr>
                    </tbody>
                   </table> 

                   </div>
				</div>
			</div>

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