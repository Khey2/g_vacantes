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
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
if(isset($_POST['el_anio'])) { $anio = $_POST['el_anio'];} else {$anio = $row_variables['anio'];}
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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

$fecha = date("Y-m-d");


if ($anio == '2021'){$el_mes = '12';} else {$el_mes = date("m");}
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if (isset($_SESSION['el_mesg'])){  $otro_mes = $_SESSION['el_mesg']; } else { $otro_mes = $el_mes;} 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_semaforo = "SELECT * FROM sem_semaforo_data_s WHERE IDmatriz = '$IDmatriz' AND IDruta_s > 0";
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
	<script src="global_assets/js/demo_pages/tasks_list5.js"></script>
    <script type="text/javascript">  var mesess = '<?php echo $mesess;?>'</script>

	<!-- /theme JS files -->
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Semáforo - Supervisores de Ventas</h5>
						</div>

					<div class="panel-body">
                    <div class="media-body">
                    <p><strong>Instrucciones</strong>. Considera los siguientes detalles:</p>
						<ul>
						<li>El porcenaje indica el cubirmiento del objetivo de ventas.</li>
						<li>En caso de vacantes, el resultado se muestra con el simbolo "-".</li>
						<li>La primer estrella indica el cubrimiento de visitas:</li>
							<ul>
                            <li><i class="icon-star-full2 text-size-base text-success-300"></i> Mayor al 90%</li>
                            <li><i class="icon-star-empty3 text-size-base text-warning-300"></i> Menor del 90%</li>
                            </ul>
                        <li>La segunda estrella indica el cubrimiento de visitas con pedido:</li>
							<ul>
                            <li><i class="icon-star-full2 text-size-base text-success-300"></i> Mayor al 55%</li>
                            <li><i class="icon-star-empty3 text-size-base text-warning-300"></i> Menor al 55%</li>
                            </ul>
                            </ul>
                    <p>Puedes cambiar la sucursal dando clic <a href="mi_matriz.php">aqui.</a></p>
					</div>

					<div class="table-responsive content-group">
                   <table class="table tasks-list table-condensed">
						<thead>
							<tr>
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
                            
							<td><?php echo $row_semaforo['IDruta_s']; ?></td>								
							<td><?php echo $row_semaforo['nombre_empleado_s']; ?></td>								
							<td><?php 
									  if ($row_semaforo['Ene2'] > 90 && $row_semaforo['Ene'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ene2'] <= 90 && $row_semaforo['Ene'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Ene3'] > 55 && $row_semaforo['Ene'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ene3'] <= 55 && $row_semaforo['Ene'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Ene'] != 0) { echo $row_semaforo['Ene'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php if ($el_mes > 2) { ?>
							<td><?php 
									  if ($row_semaforo['Feb2'] > 90 && $row_semaforo['Feb'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Feb2'] <= 90 && $row_semaforo['Feb'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Feb3'] > 55 && $row_semaforo['Feb'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Feb3'] <= 55 && $row_semaforo['Feb'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Feb'] != 0) { echo $row_semaforo['Feb'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 3) { ?>
							<td><?php 
									  if ($row_semaforo['Mar2'] > 90 && $row_semaforo['Mar'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Mar2'] <= 90 && $row_semaforo['Mar'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Mar3'] > 55 && $row_semaforo['Mar'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Mar3'] <= 55 && $row_semaforo['Mar'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Mar'] != 0) { echo $row_semaforo['Mar'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 4) { ?>
							<td><?php 
									  if ($row_semaforo['Abr2'] > 90 && $row_semaforo['Abr'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Abr2'] <= 90 && $row_semaforo['Abr'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Abr3'] > 55 && $row_semaforo['Abr'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Abr3'] <= 55 && $row_semaforo['Abr'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Abr'] != 0) { echo $row_semaforo['Abr'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 5) { ?>
							<td><?php 
									  if ($row_semaforo['May2'] > 90 && $row_semaforo['May'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['May2'] <= 90 && $row_semaforo['May'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['May3'] > 55 && $row_semaforo['May'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['May3'] <= 55 && $row_semaforo['May'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['May'] != 0) { echo $row_semaforo['May'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 6) { ?>
							<td><?php 
									  if ($row_semaforo['Jun2'] > 90 && $row_semaforo['Jun'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jun2'] <= 90 && $row_semaforo['Jun'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Jun3'] > 55 && $row_semaforo['Jun'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jun3'] <= 55 && $row_semaforo['Jun'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Jun'] != 0) { echo $row_semaforo['Jun'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 7) { ?>
							<td><?php 
									  if ($row_semaforo['Jul2'] > 90 && $row_semaforo['Jul'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jul2'] <= 90 && $row_semaforo['Jul'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Jul3'] > 55 && $row_semaforo['Jul'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Jul3'] <= 55 && $row_semaforo['Jul'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Jul'] != 0) { echo $row_semaforo['Jul'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 8) { ?>
							<td><?php 
									  if ($row_semaforo['Ags2'] > 90 && $row_semaforo['Ags'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ags2'] <= 90 && $row_semaforo['Ags'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Ags3'] > 55 && $row_semaforo['Ags'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Ags3'] <= 55 && $row_semaforo['Ags'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Ags'] != 0) { echo $row_semaforo['Ags'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 9) { ?>
							<td><?php 
									  if ($row_semaforo['Sep2'] > 90 && $row_semaforo['Sep'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Sep2'] <= 90 && $row_semaforo['Sep'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Sep3'] > 55 && $row_semaforo['Sep'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Sep3'] <= 55 && $row_semaforo['Sep'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Sep'] != 0) { echo $row_semaforo['Sep'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 10) { ?>
							<td><?php 
									  if ($row_semaforo['Oct2'] > 90 && $row_semaforo['Oct'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Oct2'] <= 90 && $row_semaforo['Oct'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Oct3'] > 55 && $row_semaforo['Oct'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Oct3'] <= 55 && $row_semaforo['Oct'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Oct'] != 0) { echo $row_semaforo['Oct'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes > 11) { ?>
							<td><?php 
									  if ($row_semaforo['Nov2'] > 90 && $row_semaforo['Nov'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Nov2'] <= 90 && $row_semaforo['Nov'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Nov3'] > 55 && $row_semaforo['Nov'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Nov3'] <= 55 && $row_semaforo['Nov'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Nov'] != 0) { echo $row_semaforo['Nov'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
														</li>
													</ul>
												</div></td>								
							<?php }  if ($el_mes >= 12) { ?>
                            <td><?php 
									  if ($row_semaforo['Dic2'] > 90 && $row_semaforo['Dic'] != 0)
							{$visita = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Dic2'] <= 90 && $row_semaforo['Dic'] != 0)
							{$visita = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$visita = '';}
									  if ($row_semaforo['Dic3'] > 55 && $row_semaforo['Dic'] != 0)
							{$pedido = '<i class="icon-star-full2 text-size-base text-success-300"></i>';} 
								  elseif ($row_semaforo['Dic3'] <= 55 && $row_semaforo['Dic'] != 0)
							{$pedido = '<i class="icon-star-empty3 text-size-base text-warning-300"></i>';} 
								  else
							{$pedido = '';}
							?>
                            <div class="media-body">
													<ul class="list-inline no-margin-bottom">
														<li><?php if ($row_semaforo['Dic'] != 0) { echo $row_semaforo['Dic'] . "%</br>"; } else {echo '-';}?></li>
														<li>
															<?php echo $visita; ?>
															<?php echo $pedido; ?>
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