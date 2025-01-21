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

$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$mmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$mmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT * FROM ztar_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }


$la_matriz = $IDmatriz;
$_SESSION['el_anio'] = $anio;

if (isset($_POST['el_anio'])) {$el_anio = $_POST['el_anio'];} 
else {$el_anio = '2024';}

if (isset($_POST['mi_mes'])) {	foreach ($_POST['mi_mes'] as $mis_meses)
	{	$_SESSION['mi_mes'] = implode(", ", $_POST['mi_mes']);}	}  else { $_SESSION['mi_mes'] = "1,2,3,4,5,6";}
$mi_mes = $_SESSION['mi_mes'];

mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT ztar_avances.IDavance, ztar_avances.IDresultado, ztar_tareas.descripcion, vac_matriz.IDmatriz, vac_matriz.matriz, ztar_avances.fecha_esperada, ztar_areas_rh.area_rh, ztar_valor_areas.valor_area, ztar_valor_areas.anio FROM ztar_avances INNER JOIN ztar_tareas ON ztar_tareas.IDtarea = ztar_avances.IDtarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = ztar_avances.IDmatriz INNER JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh INNER JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_areas_rh.IDarea_rh WHERE ztar_avances.IDmatriz = '$la_matriz' AND MONTH(ztar_avances.fecha_esperada) IN ($mi_mes) AND ztar_tareas.anio = '$el_anio'";
mysql_query("SET NAMES 'utf8'");
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);

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

   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect3.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/tasks_list8.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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
                

					<!-- Detailed task -->
					<div class="row">
						<div class="col-lg-12">

							<!-- Task overview -->
							<div class="panel panel-flat">
								<div class="panel-heading mt-5">
									<h4 class="panel-title">Ranking - Desempeño RH Sucursal</h4>
								</div>
								
								<div class="panel-body">
                                <p>A continuación se muestan los resultados de la Evaluación a Recursos Humanos Sucursal del año <?php echo $el_anio; ?>.</p>
                                <p><a href="objetivos_y.php" class="btn btn-primary"><i class="icon icon-circle-right2"></i> Ver resultados por Objetivo </a></p>
                                  
                                  
                         <div class="row">
					<table class="table table-condensed datatable-button-html5-columns">
											<thead>
												<tr  class="bg-blue">
													<th>Sucursal</th>
													<th>D.O.</th>
													<th>Capa</th>
													<th>Nómina</th>
													<th>S.e H.</th>
													<th>M. y P.</th>
													<th>C Interna</th>
													<th>R. Lab</th>
													<th>Regional RH</th>
													<th>Total</th>
													<th>Rango</th>
												</tr>
											</thead>
											<tbody>
                                           <tr>
										   <?php

										   
										    mysql_select_db($database_vacantes, $vacantes);
											$query_mats = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (10, 27, 17, 29, 7, 5, 31)";
											$mats = mysql_query($query_mats, $vacantes) or die(mysql_error());
											$row_mats = mysql_fetch_assoc($mats);

										 do {

											$es_matriz = $row_mats['IDmatriz'];		
											
											?>
                                          <td><?php echo  $row_mats['matriz']; ?></td>
											<?php
											 
                                        
										$area_r = 1;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev41 = round($prev3 / 100, 0);
										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=2024" ><?php echo  $prev2;?></a><?php } ?></td>
											<?php
											 
                                        
										$area_r = 2;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev42 = round($prev3 / 100, 0);

										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=<?php echo $el_anio; ?>" ><?php echo  $prev2;?></a><?php } ?></td>
											<?php
											 
                                        
										$area_r = 3;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev43 = round($prev3 / 100, 0);

										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=<?php echo $el_anio; ?>" ><?php echo  $prev2;?></a><?php } ?></td>
											<?php
											 
                                        
										$area_r = 4;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev44 = round($prev3 / 100, 0);
										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=<?php echo $el_anio; ?>" ><?php echo  $prev2;?></a><?php } ?></td>
											<?php
											 
                                        
										$area_r = 6;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev46 = round($prev3 / 100, 0);

										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=<?php echo $el_anio; ?>" ><?php echo  $prev2;?></a><?php } ?></td>
											<?php
											 
                                        
										$area_r = 7;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev47 = round($prev3 / 100, 0);

										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=<?php echo $el_anio; ?>" ><?php echo  $prev2;?></a><?php } ?></td>
											<?php
											 
                                        
										$area_r = 8;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev48 = round($prev3 / 100, 0);

										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=<?php echo $el_anio; ?>" ><?php echo  $prev2;?></a><?php } ?></td>
											<?php
											 
                                        
										$area_r = 9;		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.IDsob, ztar_tareas.IDsat, ztar_tareas.IDdef,
												ztar_tareas.ponderacion AS Ponderacion,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '$el_anio' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$es_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev49 = round($prev3 / 100, 0);

										?>
                                          <td><?php if ($prev2 == 0) { echo "-";} else { ?><a href="objetivos_d_resultados.php?IDmatriz=<?php echo $es_matriz;?>&IDarea=<?php echo $area_r; ?>&anio=<?php echo $el_anio; ?>" ><?php echo  $prev2;?></a><?php } ?></td>
                                          
                                          <?php
										$total1 =  $prev41 + $prev42 + $prev43 + $prev44 + $prev46 + $prev47 + $prev48 + $prev49;

											?>

                                          <td><?php if ($total1 > 100) { echo "<div class='text text-primary text-bold'>". $total1."</div>";} 
										  else if ($total1 >= 80) { echo "<div class='text text-success text-bold'>". $total1."</div>";} 
										  else if ($total1 < 80) { echo "<div class='text text-danger text-bold'>". $total1."</div>";} ?></td>
                                          <td><?php if ($total1 > 100) { echo "<div class='text text-primary text-bold'>Sobresaliente</div>";} 
										  else if ($total1 >= 80) { echo "<div class='text text-success text-bold'>Satisfactorio</div>";} 
										  else if ($total1 < 80) { echo "<div class='text text-muted text-bold'>Deficiente</div>";} ?></td>
										</tr>
                                    <?php  } while ($row_mats = mysql_fetch_assoc($mats)); ?>
                                   </tbody>
                                   </table>
                            </div>
								</div>

							</div>
							<!-- /task overview -->

						</div>

					</div>
					<!-- /detailed task -->



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