<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1"); 
$restrict->addLevel("2");
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
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 
$desfase = $row_variables['dias_desfase'];

if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$IDllave = $row_usuario['IDllave'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];
$la_matriz = $row_usuario['IDmatriz'];
$fecha = date("Y-m-d");


if ($anio == '2021'){$el_mes = '12';} else {$el_mes = date("m");}
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if (isset($_SESSION['el_mesg'])){  $otro_mes = $_SESSION['el_mesg']; } else { $otro_mes = $el_mes;} 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_semaforo = "SELECT * FROM sem_semaforo_data WHERE IDmatriz = '$IDmatriz' AND IDempleado = '$el_usuario' AND IDruta > 0";
mysql_query("SET NAMES 'utf8'");
$semaforo = mysql_query($query_semaforo, $vacantes) or die(mysql_error());
$row_semaforo = mysql_fetch_assoc($semaforo);
$totalRows_semaforo = mysql_num_rows($semaforo);

switch ($el_mes) {
    case 3: $mesess = '3'; break;    
    case 4: $mesess = '3, 4'; break;    
    case 5: $mesess = '3, 4, 5'; break;    
    case 6: $mesess = '3, 4, 5, 6'; break;    
    case 7: $mesess = '3, 4, 5, 6, 7'; break;    
    case 8: $mesess = '3, 4, 5, 6, 7, 8'; break;    
    case 9: $mesess = '3, 4, 5, 6, 7, 8, 9'; break;    
    case 10: $mesess = '3, 4, 5, 6, 7, 8, 9, 10'; break;    
    case 11: $mesess = '3, 4, 5, 6, 7, 8, 9, 10, 11'; break;    
    case 12: $mesess = '3, 4, 5, 6, 7, 8, 9, 10, 11, 12'; break;    
    case 13: $mesess = '3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13'; break;    
    case 14: $mesess = '3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14'; break;    
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
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

	<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/natural_sort.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    
    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/tasks_list4.js"></script>
    <script type="text/javascript">  var mesess = '<?php echo $mesess;?>'</script>

	<!-- /theme JS files -->
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	

<?php require_once('assets/f_mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/f_pheader.php'); ?>

<!-- Content area -->
<div class="content">
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Semáforo - Representantes de Ventas</h5>
						</div>

					<div class="panel-body">
            <p>Con el objetivo de evaluar de forma periódica los resultados de la fuerza de ventas y establecer estrategias para asegurar el cumplimiento de sus objetivos, a continuación te mostramos el Semáforo de Ventas.</br>
                    <div class="media-body">
                    <p><strong>Instrucciones</strong>. Considera los siguientes detalles:</p>
						<ul>
						<li>El porcentaje indica el cubirmiento del objetivo de ventas.</li>
						<li>En caso de vacantes, el resultado se muestra con el simbolo "-".</li>
						<li>La primer estrella indica el cubrimiento del acelerador de ventas:</li>
							<ul>
                            <li><i class="icon-star-full2 text-size-base text-success-300"></i> Mayor al 120%</li>
                            <li><i class="icon-star-half text-size-base text-success-300"></i> Mayor al 100%</li>
                            <li><i class="icon-star-empty3 text-size-base text-danger-300"></i> Menor del 100%</li>
                            </ul>
                        <li>La segunda estrella indica el cubrimiento del acelerador de renglones:</li>
							<ul>
                            <li><i class="icon-star-full2 text-size-base text-success-300"></i> Lo cubre</li>
                            <li><i class="icon-star-empty3 text-size-base text-danger-300"></i> No lo cubre</li>
                            </ul></ul>
                    <p>Puedes cambiar la sucursal dando clic <a href="mi_matriz.php">aqui.</a></p>
					</div>
                                      
					<div class="table-responsive content-group">
                   <table class="table tasks-list table-condensed">
						<thead>
							<tr>
                                  <th>Ruta</th>
                                  <th>Ruta Supervisión</th>
                                  <th>Empleado</th>
   				    <?php if ($el_mes >= 1) {?>  <th>Ene</th> <?php } ?> 
   				    <?php if ($el_mes > 2) {?>  <th>Feb</th> <?php } ?> 
   				    <?php if ($el_mes > 3) {?>  <th>Mar</th> <?php } ?> 
   				    <?php if ($el_mes > 4) {?>  <th>Abr</th> <?php } ?> 
   				    <?php if ($el_mes > 5) {?>  <th>May</th> <?php } ?> 
   				    <?php if ($el_mes > 6) {?>  <th>Jun</th> <?php } ?> 
   				    <?php if ($el_mes > 7) {?>  <th>Jul</th> <?php } ?> 
   				    <?php if ($el_mes > 8) {?>  <th>Ags</th> <?php } ?> 
   				    <?php if ($el_mes > 9) {?>  <th>Sep</th> <?php } ?> 
   				    <?php if ($el_mes > 10) {?>  <th>Oct</th> <?php } ?> 
   				    <?php if ($el_mes > 11) {?>  <th>Nov</th> <?php } ?> 
   				    <?php if ($el_mes >= 12) {?>  <th>Dic</th> <?php } ?> 
						    </tr>
					    </thead>
						<tbody>										  
   				    <?php do {?>
							<tr>
                            
							<td><?php echo $row_semaforo['IDruta']; ?></td>								
							<td>SUPERVISIÓN: <?php echo $row_semaforo['nombre_empleado_s']; ?> - <?php echo $row_semaforo['IDruta_s']; ?></td>
							<td><?php echo $row_semaforo['nombre_empleado']; ?></td>								
							<td><?php 
									  if ($row_semaforo['Ene2'] == 2 && $row_semaforo['Ene'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ene2'] == 1 && $row_semaforo['Ene'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ene2'] == 0 && $row_semaforo['Ene'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Ene3'] == 1 && $row_semaforo['Ene'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ene3'] == 0 && $row_semaforo['Ene'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Ene'] != 0) { 
														if ($row_semaforo['Ene'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Ene'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Ene'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Ene'] . "%</span></br>"; 
														} else {echo '-';}
														?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php if ($el_mes > 2) { ?>
							<td><?php 
									  if ($row_semaforo['Feb2'] == 2 && $row_semaforo['Feb'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Feb2'] == 1 && $row_semaforo['Feb'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Feb2'] == 0 && $row_semaforo['Feb'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Feb3'] == 1 && $row_semaforo['Feb'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Feb3'] == 0 && $row_semaforo['Feb'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Feb'] != 0) { 
														if ($row_semaforo['Feb'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Feb'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Feb'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Feb'] . "%</span></br>"; 
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 3) { ?>
							<td><?php 
									  if ($row_semaforo['Mar2'] == 2 && $row_semaforo['Mar'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Mar2'] == 1 && $row_semaforo['Mar'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Mar2'] == 0 && $row_semaforo['Mar'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Mar3'] == 1 && $row_semaforo['Mar'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Mar3'] == 0 && $row_semaforo['Mar'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Mar'] != 0) {
														if ($row_semaforo['Mar'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Mar'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Mar'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Mar'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 4) { ?>
							<td><?php 
									  if ($row_semaforo['Abr2'] == 2 && $row_semaforo['Abr'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Abr2'] == 1 && $row_semaforo['Abr'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Abr2'] == 0 && $row_semaforo['Abr'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Abr3'] == 1 && $row_semaforo['Abr'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Abr3'] == 0 && $row_semaforo['Abr'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Abr'] != 0) { 
														if ($row_semaforo['Abr'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Abr'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Abr'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Abr'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 5) { ?>
							<td><?php 
									  if ($row_semaforo['May2'] == 2 && $row_semaforo['May'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['May2'] == 1 && $row_semaforo['May'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['May2'] == 0 && $row_semaforo['May'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['May3'] == 1 && $row_semaforo['May'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['May3'] == 0 && $row_semaforo['May'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['May'] != 0) { 
														if ($row_semaforo['May'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['May'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['May'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['May'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 6) { ?>
							<td><?php 
									  if ($row_semaforo['Jun2'] == 2 && $row_semaforo['Jun'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jun2'] == 1 && $row_semaforo['Jun'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jun2'] == 0 && $row_semaforo['Jun'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Jun3'] == 1 && $row_semaforo['Jun'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jun3'] == 0 && $row_semaforo['Jun'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Jun'] != 0) { 
														if ($row_semaforo['Jun'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Jun'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Jun'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Jun'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 7) { ?>
							<td><?php 
									  if ($row_semaforo['Jul2'] == 2 && $row_semaforo['Jul'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jul2'] == 1 && $row_semaforo['Jul'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jul2'] == 0 && $row_semaforo['Jul'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Jul3'] == 1 && $row_semaforo['Jul'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jul3'] == 0 && $row_semaforo['Jul'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Jul'] != 0) { 
														if ($row_semaforo['Jul'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Jul'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Jul'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Jul'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 8) { ?>
							<td><?php 
									  if ($row_semaforo['Ags2'] == 2 && $row_semaforo['Ags'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ags2'] == 1 && $row_semaforo['Ags'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ags2'] == 0 && $row_semaforo['Ags'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Ags3'] == 1 && $row_semaforo['Ags'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ags3'] == 0 && $row_semaforo['Ags'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Ags'] != 0) { 
														if ($row_semaforo['Ags'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Ags'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Ags'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Ags'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 9) { ?>
							<td><?php 
									  if ($row_semaforo['Sep2'] == 2 && $row_semaforo['Sep'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Sep2'] == 1 && $row_semaforo['Sep'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Sep2'] == 0 && $row_semaforo['Sep'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Sep3'] == 1 && $row_semaforo['Sep'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Sep3'] == 0 && $row_semaforo['Sep'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Sep'] != 0) { 
														if ($row_semaforo['Sep'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Sep'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Sep'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Sep'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 10) { ?>
							<td><?php 
									  if ($row_semaforo['Oct2'] == 2 && $row_semaforo['Oct'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Oct2'] == 1 && $row_semaforo['Oct'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Oct2'] == 0 && $row_semaforo['Oct'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Oct3'] == 1 && $row_semaforo['Oct'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Oct3'] == 0 && $row_semaforo['Oct'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Nov'] != 0) { 
														if ($row_semaforo['Nov'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Nov'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Nov'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Nov'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 11) { ?>
							<td><?php 
									  if ($row_semaforo['Nov2'] == 2 && $row_semaforo['Nov'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Nov2'] == 1 && $row_semaforo['Nov'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Nov2'] == 0 && $row_semaforo['Nov'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Nov3'] == 1 && $row_semaforo['Nov'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Nov3'] == 0 && $row_semaforo['Nov'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Nov'] != 0) { 
														if ($row_semaforo['Nov'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Nov'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Nov'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Nov'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes >= 12) { ?>
                            <td><?php 
									  if ($row_semaforo['Dic2'] == 2 && $row_semaforo['Dic'] != 0)
							{$venta100 = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Dic2'] == 1 && $row_semaforo['Dic'] != 0)
						    {$venta100 = '<i class="icon-star-half  text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Dic2'] == 0 && $row_semaforo['Dic'] != 0)
						    {$venta100 = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$venta100 = '';}
									  if ($row_semaforo['Dic3'] == 1 && $row_semaforo['Dic'] != 0)
							{$renglones = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Dic3'] == 0 && $row_semaforo['Dic'] != 0)
							{$renglones = '<i class="icon-star-empty3 text-size-base text-danger-300"></i>';} 
								  else
							{$renglones = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Dic'] != 0) { 
														if ($row_semaforo['Dic'] > 100) {echo "<span class='text text-primary'>"; }
														else if($row_semaforo['Dic'] > 84) { echo "<span class='text text-success'>";}
														else if($row_semaforo['Dic'] < 85) { echo  "<span class='text text-danger'>";}
														echo $row_semaforo['Dic'] . "%</span></br>"; 															
														} else {echo '-';}?></li>
														<li>
															<?php echo $venta100; ?>
															<?php echo $renglones; ?>
														</li>
													</ul>
												</div></td>								
							<?php } ?>
							</tr>
			   		<?php } while ($row_semaforo = mysql_fetch_assoc($semaforo)); ?>
                        </tbody>
                   </table> 
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