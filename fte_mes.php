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
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (10,17,7,27,29,5)";
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
							<p class="content-group">A continuación, se muestra el FTE por Sucursal y año.</p>


                    <form method="POST" action="fte_mes.php">
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
$fini_ms1 = new DateTime($anio_actual . '-01-01');
$fini_ms1->modify('first day of this month');
$fini_ms1k = $fini_ms1->format('Y/m/d'); 

$fter_ms1 = new DateTime($anio_actual . '-01-01');
$fter_ms1->modify('last day of this month');
$fter_ms1k = $fter_ms1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms1k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms1k')";
$res_ms1 = mysql_query($query_res_ms1, $vacantes) or die(mysql_error());
$row_res_ms1 = mysql_fetch_assoc($res_ms1);
$totalRows_res_ms1 = mysql_num_rows($res_ms1);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 1 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms1 = mysql_query($query_bja_ms1, $vacantes) or die(mysql_error());
$row_bja_ms1 = mysql_fetch_assoc($bja_ms1);
$totalRows_bja_ms1 = mysql_num_rows($bja_ms1);

if($row_bja_ms1['TOTAL'] > 0 and $row_res_ms1['TOTAL'] > 0) {$RotTotalM1 =  $row_res_ms1['TOTAL'];} else {$RotTotalM1 = 0;}
if($mes_actual == 1) {$RotTotalPREVBB =  $row_res_ms1['TOTAL'];}


// Resultado Mes 2 año actual
$fini_ms2 = new DateTime($anio_actual . '-02-01');
$fini_ms2->modify('first day of this month');
$fini_ms2k = $fini_ms2->format('Y/m/d'); 

$fter_ms2 = new DateTime($anio_actual . '-02-01');
$fter_ms2->modify('last day of this month');
$fter_ms2k = $fter_ms2->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms2k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms2k')";
$res_ms2 = mysql_query($query_res_ms2, $vacantes) or die(mysql_error());
$row_res_ms2 = mysql_fetch_assoc($res_ms2);
$totalRows_res_ms2 = mysql_num_rows($res_ms2);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 2 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms2 = mysql_query($query_bja_ms2, $vacantes) or die(mysql_error());
$row_bja_ms2 = mysql_fetch_assoc($bja_ms2);
$totalRows_bja_ms2 = mysql_num_rows($bja_ms2);

if($row_bja_ms2['TOTAL'] > 0 and $row_res_ms2['TOTAL'] > 0) {$RotTotalM2 = $row_res_ms2['TOTAL'];} else {$RotTotalM2 = 0;}
if($mes_actual == 2) {$RotTotalPREVBB =  $row_res_ms2['TOTAL'];}

// Resultado Mes 3 año actual
$fini_ms3 = new DateTime($anio_actual . '-03-01');
$fini_ms3->modify('first day of this month');
$fini_ms3k = $fini_ms3->format('Y/m/d'); 

$fter_ms3 = new DateTime($anio_actual . '-03-01');
$fter_ms3->modify('last day of this month');
$fter_ms3k = $fter_ms3->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms3 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms3k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms3k')";
$res_ms3 = mysql_query($query_res_ms3, $vacantes) or die(mysql_error());
$row_res_ms3 = mysql_fetch_assoc($res_ms3);
$totalRows_res_ms3 = mysql_num_rows($res_ms3);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms3 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 3 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms3 = mysql_query($query_bja_ms3, $vacantes) or die(mysql_error());
$row_bja_ms3 = mysql_fetch_assoc($bja_ms3);
$totalRows_bja_ms3 = mysql_num_rows($bja_ms3);

if($row_bja_ms3['TOTAL'] > 0 and $row_res_ms3['TOTAL'] > 0) {$RotTotalM3 =  $row_res_ms3['TOTAL'];} else {$RotTotalM3 = 0;}
if($mes_actual == 3) {$RotTotalPREVBB = $row_res_ms3['TOTAL'];}

// Resultado Mes 4 año actual
$fini_ms4 = new DateTime($anio_actual . '-04-01');
$fini_ms4->modify('first day of this month');
$fini_ms4k = $fini_ms4->format('Y/m/d'); 

$fter_ms4 = new DateTime($anio_actual . '-04-01');
$fter_ms4->modify('last day of this month');
$fter_ms4k = $fter_ms4->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms4 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms4k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms4k')";
$res_ms4 = mysql_query($query_res_ms4, $vacantes) or die(mysql_error());
$row_res_ms4 = mysql_fetch_assoc($res_ms4);
$totalRows_res_ms4 = mysql_num_rows($res_ms4);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms4 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 4 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms4 = mysql_query($query_bja_ms4, $vacantes) or die(mysql_error());
$row_bja_ms4 = mysql_fetch_assoc($bja_ms4);
$totalRows_bja_ms4 = mysql_num_rows($bja_ms4);

if($row_bja_ms4['TOTAL'] > 0 and $row_res_ms4['TOTAL'] > 0) {$RotTotalM4 =  $row_res_ms4['TOTAL'];} else {$RotTotalM4 = 0;}
if($mes_actual == 4) {$RotTotalPREVBB =  $row_res_ms4['TOTAL'];}

// Resultado Mes 5 año actual
$fini_ms5 = new DateTime($anio_actual . '-05-01');
$fini_ms5->modify('first day of this month');
$fini_ms5k = $fini_ms5->format('Y/m/d'); 

$fter_ms5 = new DateTime($anio_actual . '-05-01');
$fter_ms5->modify('last day of this month');
$fter_ms5k = $fter_ms5->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms5 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms5k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms5k')";
$res_ms5 = mysql_query($query_res_ms5, $vacantes) or die(mysql_error());
$row_res_ms5 = mysql_fetch_assoc($res_ms5);
$totalRows_res_ms5 = mysql_num_rows($res_ms5);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms5 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 5 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms5 = mysql_query($query_bja_ms5, $vacantes) or die(mysql_error());
$row_bja_ms5 = mysql_fetch_assoc($bja_ms5);
$totalRows_bja_ms5 = mysql_num_rows($bja_ms5);

if($row_bja_ms5['TOTAL'] > 0 and $row_res_ms5['TOTAL'] > 0) {$RotTotalM5 = $row_res_ms5['TOTAL'];} else {$RotTotalM5 = 0;}
if($mes_actual == 5) {$RotTotalPREVBB =  $row_res_ms5['TOTAL'];}

// Resultado Mes 6 año actual
$fini_ms6 = new DateTime($anio_actual . '-06-01');
$fini_ms6->modify('first day of this month');
$fini_ms6k = $fini_ms6->format('Y/m/d'); 

$fter_ms6 = new DateTime($anio_actual . '-06-01');
$fter_ms6->modify('last day of this month');
$fter_ms6k = $fter_ms6->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms6 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms6k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms6k')";
$res_ms6 = mysql_query($query_res_ms6, $vacantes) or die(mysql_error());
$row_res_ms6 = mysql_fetch_assoc($res_ms6);
$totalRows_res_ms6 = mysql_num_rows($res_ms6);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms6 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 6 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms6 = mysql_query($query_bja_ms6, $vacantes) or die(mysql_error());
$row_bja_ms6 = mysql_fetch_assoc($bja_ms6);
$totalRows_bja_ms6 = mysql_num_rows($bja_ms6);

if($row_bja_ms6['TOTAL'] > 0 and $row_res_ms6['TOTAL'] > 0) {$RotTotalM6 = $row_res_ms6['TOTAL'];} else {$RotTotalM6 = 0;}
if($mes_actual == 6) {$RotTotalPREVBB =  $row_res_ms6['TOTAL'];}

// Resultado Mes 7 año actual
$fini_ms7 = new DateTime($anio_actual . '-07-01');
$fini_ms7->modify('first day of this month');
$fini_ms7k = $fini_ms7->format('Y/m/d'); 

$fter_ms7 = new DateTime($anio_actual . '-07-01');
$fter_ms7->modify('last day of this month');
$fter_ms7k = $fter_ms7->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms7 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms7k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms7k')";
$res_ms7 = mysql_query($query_res_ms7, $vacantes) or die(mysql_error());
$row_res_ms7 = mysql_fetch_assoc($res_ms7);
$totalRows_res_ms7 = mysql_num_rows($res_ms7);

$row_res_ms7['TOTAL'] = $row_res_ms7['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms7 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 7 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms7 = mysql_query($query_bja_ms7, $vacantes) or die(mysql_error());
$row_bja_ms7 = mysql_fetch_assoc($bja_ms7);
$totalRows_bja_ms7 = mysql_num_rows($bja_ms7);

if($row_bja_ms7['TOTAL'] > 0 and $row_res_ms7['TOTAL'] > 0) {$RotTotalM7 =  $row_res_ms7['TOTAL'];} else {$RotTotalM7 = 0;}
if($mes_actual == 7) {$RotTotalPREVBB = $row_res_ms7['TOTAL'];}

// Resultado Mes 8 año actual
$fini_ms8 = new DateTime($anio_actual . '-08-01');
$fini_ms8->modify('first day of this month');
$fini_ms8k = $fini_ms8->format('Y/m/d'); 

$fter_ms8 = new DateTime($anio_actual . '-08-01');
$fter_ms8->modify('last day of this month');
$fter_ms8k = $fter_ms8->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms8 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms8k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms8k')";
$res_ms8 = mysql_query($query_res_ms8, $vacantes) or die(mysql_error());
$row_res_ms8 = mysql_fetch_assoc($res_ms8);
$totalRows_res_ms8 = mysql_num_rows($res_ms8);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms8 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 8 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms8 = mysql_query($query_bja_ms8, $vacantes) or die(mysql_error());
$row_bja_ms8 = mysql_fetch_assoc($bja_ms8);
$totalRows_bja_ms8 = mysql_num_rows($bja_ms8);

if($row_bja_ms8['TOTAL'] > 0 and $row_res_ms8['TOTAL'] > 0) {$RotTotalM8 = $row_res_ms8['TOTAL'];} else {$RotTotalM8 = 0;}
if($mes_actual == 8) {$RotTotalPREVBB = $row_res_ms8['TOTAL'];}

// Resultado Mes 9 año actual
$fini_ms9 = new DateTime($anio_actual . '-09-01');
$fini_ms9->modify('first day of this month');
$fini_ms9k = $fini_ms9->format('Y/m/d'); 

$fter_ms9 = new DateTime($anio_actual . '-09-01');
$fter_ms9->modify('last day of this month');
$fter_ms9k = $fter_ms9->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms9 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms9k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms9k')";
$res_ms9 = mysql_query($query_res_ms9, $vacantes) or die(mysql_error());
$row_res_ms9 = mysql_fetch_assoc($res_ms9);
$totalRows_res_ms9 = mysql_num_rows($res_ms9);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms9 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 9 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms9 = mysql_query($query_bja_ms9, $vacantes) or die(mysql_error());
$row_bja_ms9 = mysql_fetch_assoc($bja_ms9);
$totalRows_bja_ms9 = mysql_num_rows($bja_ms9);

if($row_bja_ms9['TOTAL'] > 0 and $row_res_ms9['TOTAL'] > 0) {$RotTotalM9 =  $row_res_ms9['TOTAL'];} else {$RotTotalM9 = 0;}
if($mes_actual == 9) {$RotTotalPREVBB = $row_res_ms9['TOTAL'];}

// Resultado Mes 10 año actual
$fini_ms10 = new DateTime($anio_actual . '-10-01');
$fini_ms10->modify('first day of this month');
$fini_ms10k = $fini_ms10->format('Y/m/d'); 

$fter_ms10 = new DateTime($anio_actual . '-10-01');
$fter_ms10->modify('last day of this month');
$fter_ms10k = $fter_ms10->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms10 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms10k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms10k')";
$res_ms10 = mysql_query($query_res_ms10, $vacantes) or die(mysql_error());
$row_res_ms10 = mysql_fetch_assoc($res_ms10);
$totalRows_res_ms10 = mysql_num_rows($res_ms10);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms10 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 10 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms10 = mysql_query($query_bja_ms10, $vacantes) or die(mysql_error());
$row_bja_ms10 = mysql_fetch_assoc($bja_ms10);
$totalRows_bja_ms10 = mysql_num_rows($bja_ms10);

if($row_bja_ms10['TOTAL'] > 0 and $row_res_ms10['TOTAL'] > 0) {$RotTotalM10 = $row_res_ms10['TOTAL'];} else {$RotTotalM10 = 0;}
if($mes_actual == 10) {$RotTotalPREVBB = $row_res_ms10['TOTAL'];}

// Resultado Mes 11 año actual
$fini_ms11 = new DateTime($anio_actual . '-11-01');
$fini_ms11->modify('first day of this month');
$fini_ms11k = $fini_ms11->format('Y/m/d'); 

$fter_ms11 = new DateTime($anio_actual . '-11-01');
$fter_ms11->modify('last day of this month');
$fter_ms11k = $fter_ms11->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms11 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms11k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms11k')";
$res_ms11 = mysql_query($query_res_ms11, $vacantes) or die(mysql_error());
$row_res_ms11 = mysql_fetch_assoc($res_ms11);
$totalRows_res_ms11 = mysql_num_rows($res_ms11);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms11 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 11 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms11 = mysql_query($query_bja_ms11, $vacantes) or die(mysql_error());
$row_bja_ms11 = mysql_fetch_assoc($bja_ms11);
$totalRows_bja_ms11 = mysql_num_rows($bja_ms11);

if($row_bja_ms11['TOTAL'] > 0 and $row_res_ms11['TOTAL'] > 0) {$RotTotalM11 =  $row_res_ms11['TOTAL'];} else {$RotTotalM11 = 0;}
if($mes_actual == 11) {$RotTotalPREVBB = $row_res_ms11['TOTAL'];}

// Resultado Mes 12 año actual
$fini_ms12 = new DateTime($anio_actual . '-12-01');
$fini_ms12->modify('first day of this month');
$fini_ms12k = $fini_ms12->format('Y/m/d'); 

$fter_ms12 = new DateTime($anio_actual . '-12-01');
$fter_ms12->modify('last day of this month');
$fter_ms12k = $fter_ms12->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms12 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms12k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms12k') ";
$res_ms12 = mysql_query($query_res_ms12, $vacantes) or die(mysql_error());
$row_res_ms12 = mysql_fetch_assoc($res_ms12);
$totalRows_res_ms12 = mysql_num_rows($res_ms12);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms12 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 12 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms12 = mysql_query($query_bja_ms12, $vacantes) or die(mysql_error());
$row_bja_ms12 = mysql_fetch_assoc($bja_ms12);
$totalRows_bja_ms12 = mysql_num_rows($bja_ms12);

if($row_bja_ms12['TOTAL'] > 0 and $row_res_ms12['TOTAL'] > 0) {$RotTotalM12 =  $row_res_ms12['TOTAL'];} else {$RotTotalM12 = 0;}
if($mes_actual == 12) {$RotTotalPREVBB = $row_res_ms12['TOTAL'];} 

// acumualdo total año
$Acumulado = $RotTotalM1 + $RotTotalM2 + $RotTotalM3 + $RotTotalM4 + $RotTotalM5 + $RotTotalM6 + $RotTotalM7 + $RotTotalM8 + $RotTotalM9 + $RotTotalM10 + $RotTotalM11 + $RotTotalM12;


if ($Acumulado > 0) {
?>
                 	<tr>
                    <td>
					<?php echo $cada_matriz; ?>
                    </td>
                    <td> <?php echo $RotTotalM1; ?> </td>
                    <td> <?php echo $RotTotalM2; ?> </td>
                    <td> <?php echo $RotTotalM3; ?> </td>
                    <td> <?php echo $RotTotalM4; ?> </td>
                    <td> <?php echo $RotTotalM5; ?> </td>
                    <td> <?php echo $RotTotalM6; ?> </td>
                    <td> <?php echo $RotTotalM7; ?> </td>
                    <td> <?php echo $RotTotalM8; ?> </td>
                    <td> <?php echo $RotTotalM9; ?> </td>
                    <td> <?php echo $RotTotalM10; ?></td>
                    <td> <?php echo $RotTotalM11; ?></td>
                    <td> <?php echo $RotTotalM12; ?></td>
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