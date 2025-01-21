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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

$fecha_filtro = date("Y-m-d");
mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT Count(prod_plantilla.IDplantilla) AS Autorizada, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_matriz.matriz FROM prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz WHERE prod_plantilla.IDmatriz = $la_matriz AND prod_plantilla.IDestatus = 1 AND (DATE(fecha_inicio) <= '$fecha_filtro') AND ( DATE(fecha_fin) > '$fecha_filtro' OR DATE(fecha_fin) = '0000-00-00' OR DATE(fecha_fin) IS NULL) AND ( DATE(fecha_congelada) > '$fecha_filtro' OR DATE(fecha_congelada) = '0000-00-00' OR DATE(fecha_congelada) IS NULL) GROUP BY prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDtipo_plaza
ORDER BY vac_puestos.denominacion ASC";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

$totales = 0;
do { $totales = $totales + $row_autorizados['Autorizada']; } while ($row_autorizados = mysql_fetch_assoc($autorizados)); 


//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$IDmatriz'";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

mysql_select_db($database_vacantes, $vacantes);
$query_ulima = "SELECT * FROM vac_vacante WHERE ((vac_vacante.IDusuario = '$el_usuario' OR  vac_vacante.IDusuario2 = '$el_usuario' OR vac_vacante.IDusuario3 = '$el_usuario' OR vac_vacante.IDusuario4 = '$el_usuario' OR vac_vacante.IDusuario5 = '$el_usuario') OR vac_vacante.IDmatriz = '$la_matriz') ORDER BY DATE DESC LIMIT 1";
$ulima = mysql_query($query_ulima, $vacantes) or die(mysql_error());
$row_ulima = mysql_fetch_assoc($ulima);
$totalRows_ulima = mysql_num_rows($ulima);

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 

	 if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 3) {	$_SESSION['el_estatus'] = 3; $a1 = ' AND vac_vacante.IDmotivo_v IN (3) '; } 
else if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 2) {	$_SESSION['el_estatus'] = 2; $a1 = ' AND vac_vacante.IDmotivo_v IN (1,2,4) ';}
else if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 5) {	$_SESSION['el_estatus'] = 5; $a1 = ' AND vac_vacante.IDmotivo_v IN (5) ';}
else if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 1) {	$_SESSION['el_estatus'] = 1; $a1 = ' AND vac_vacante.IDmotivo_v IN (1,2,3,4,5) ';}
else  {	$_SESSION['el_estatus'] = 1; $a1 = ' AND vac_vacante.IDmotivo_v IN (1,2,3,4,5) '; }
$el_estatus = $_SESSION['el_estatus'];

	 if (isset($_POST['el_estatus2']) && $_POST['el_estatus2'] == 3) {	$_SESSION['el_estatus2'] = 3; $a2 = ' AND vac_vacante.IDrequi = 1 '; } 
else if (isset($_POST['el_estatus2']) && $_POST['el_estatus2'] == 2) {	$_SESSION['el_estatus2'] = 2; $a2 = ' AND vac_vacante.IDrequi = 0 ';}
else if (isset($_POST['el_estatus2']) && $_POST['el_estatus2'] == 1) {	$_SESSION['el_estatus2'] = 1; $a2 = '  ';}
else  {	$_SESSION['el_estatus2'] = 1; $a2 = '  '; }
$el_estatus2 = $_SESSION['el_estatus2'];

mysql_select_db($database_vacantes, $vacantes);
$query_vacantes = "SELECT vac_vacante.IDvacante, vac_matriz.matriz, vac_vacante.date, vac_puestos.denominacion, vac_areas.area, vac_puestos.dias, vac_vacante.ajuste_dias, vac_vacante.IDmotivo_v, vac_vacante.IDrequi, vac_vacante.IDestatus, vac_vacante.IDapoyo, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_estatus.estatus, vac_tipo_vacante.tipo_vacante, vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.IDusuario4, vac_vacante.IDusuario5,  vac_vacante.fecha_usr4, vac_sucursal.sucursal FROM vac_vacante LEFT JOIN vac_estatus ON vac_vacante.IDestatus = vac_estatus.IDestatus LEFT JOIN vac_matriz ON vac_vacante.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_tipo_vacante ON vac_vacante.IDtipo_vacante = vac_tipo_vacante.IDtipo_vacante LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz AND vac_vacante.IDsucursal = vac_sucursal.IDsucursal WHERE ((vac_vacante.IDusuario = '$el_usuario' OR  vac_vacante.IDusuario2 = '$el_usuario' OR vac_vacante.IDusuario3 = '$el_usuario' OR vac_vacante.IDusuario4 = '$el_usuario' OR vac_vacante.IDusuario5 = '$el_usuario') OR vac_vacante.IDmatriz = '$la_matriz') AND vac_vacante.IDestatus = 1 ".$a1.$a2;
mysql_query("SET NAMES 'utf8'");
$vacantes = mysql_query($query_vacantes, $vacantes) or die(mysql_error());
$row_vacantes = mysql_fetch_assoc($vacantes);
$totalRows_vacantes = mysql_num_rows($vacantes);



//fechas
require_once('assets/dias.php');


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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
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
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

							<!-- Simple text stats with icons -->
							<div class="panel panel-body">
								<div class="row text-center">
									<div class="col-xs-3">
										<p><i class="icon-user-check icon-2x display-inline-block text-primary"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totales; ?></h5>
										<span class="text-muted text-size-small"><a href="plantilla.php">Plantilla Autorizada</a></span>
									</div>

									<div class="col-xs-3">
										<p><i class="icon-user-plus icon-2x display-inline-block text-info"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $row_activos['TActivos']; ?></h5>
										<span class="text-muted text-size-small"><a href="plantilla_activos.php">Empleados Activos</a></span>
									</div>

									<div class="col-xs-3">
										<p><i class="icon-user-minus icon-2x display-inline-block text-warning"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totalRows_vacantes; ?></h5>
										<span class="text-muted text-size-small">Vacantes Capturadas</span>
									</div>


									<div class="col-xs-3">									
									<?php $diferencia = $totales - $row_activos['TActivos'] - $totalRows_vacantes; if ($diferencia >= 1) { ?>
										<p><i class="icon-cancel-circle2 icon-2x display-inline-block text-danger"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $diferencia; ?></h5>
										<span class="text-muted text-size-small">Diferencia</span>
									<?php } else { ?>
										<p><i class="icon-checkmark-circle icon-2x display-inline-block text-success"></i></p>
										<h5 class="text-semibold no-margin"><?php echo "-"; ?></h5>
										<span class="text-muted text-size-small">Correcto</span>
									<?php } ?>

									</div>

								</div>
							</div>
							<!-- /simple text stats with icons -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Vacantes Activas</h5>
						</div>

					<div class="panel-body">

						<p>Ultima actualización: <?php echo date( 'd/m/Y' , strtotime($row_ulima['date'])); ?></p>

						<p>A continuación se muestran las vacantes activas. Selecciona la vacante que requieras editar.</br>
							Utilizar el buscador para filtrar las vacantes por cualquiera de criterio existente en la tabla.</br>
                            Las plazas asignadas a seguimiento corporativo, tienen un indicador especial: <i class='icon-pushpin'></i></br>
							Las vacantes que no tienen Requi autorizadas, muestran la fecha de solicitud y son informativas.</br>
							<a class="text text-bold" href="proced/anexos/18062024112016_359_185.xls" target="_blank"><i class="icon icon-file-excel"></i> Descarga aqui el formato de Requisición.</a></p>
							
<p>&nbsp; </p>
                            

                  <form method="POST" action="vacantes_activas.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td><div class="col-md-9">
                                             Temporales o Pull: <select class="form-control"  name="el_estatus">
                                               <option value="1" <?php if ($el_estatus == 1) {echo "selected=\"selected\"";} ?>>Todas</option>
                                               <option value="2" <?php if ($el_estatus == 2) {echo "selected=\"selected\"";} ?>>Permanente</option>
                                               <option value="3" <?php if ($el_estatus == 3) {echo "selected=\"selected\"";} ?>>Temporales</option>
                                               <option value="5" <?php if ($el_estatus == 5) {echo "selected=\"selected\"";} ?>>Pull Vacaciones</option>
                                               </select>
						    </div></td>
							<td><div class="col-md-9">
                                             Con Requi: <select class="form-control"  name="el_estatus2">
                                               <option value="1" <?php if ($el_estatus2 == 1) {echo "selected=\"selected\"";} ?>>Todas</option>
                                               <option value="2" <?php if ($el_estatus2 == 2) {echo "selected=\"selected\"";} ?>>Si</option>
                                               <option value="3" <?php if ($el_estatus2 == 3) {echo "selected=\"selected\"";} ?>>No</option>
                                               </select>
						    </div></td>
							<td><button type="submit" class="btn btn-primary">Filtrar</button>
							<a class="btn btn-success" href="vacante_edit.php">Agregar Vacante<i class="icon-arrow-right14 position-right"></i></a>
							</td>	
					      </tr>
					    </tbody>
				    </table>
			</form>
<p>&nbsp; </p>
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Folio</th>
							    <th>Matriz</th>
							    <th>Denominación</th>
							    <th>Área</th>
							    <th>Fecha Requi o Solicitud</th>
							    <th>Días Transcurridos</th>
							    <th>Con Requi</th>
							    <th>Temporal</th>
							    <th>Estatus</th>
							    <th class="text-center">Acciones</th>
						    </tr>
					    </thead>
						<tbody>							  
						<?php if ($totalRows_vacantes > 0){ ?>
						<?php do { $dias4 = $row_vacantes['fecha_usr4']; ?>
							<tr>
							<td><?php echo $row_vacantes['IDvacante']; ?></td>
							<td><?php echo $row_vacantes['matriz'] . " - " . $row_vacantes['sucursal']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['denominacion']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['area']; ?>&nbsp; </td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_vacantes['fecha_requi'])) ?></td>
                            <td><?php if ($row_vacantes['IDrequi'] == 1) {echo "<div class='label label-default'>Sin Requi</div>";} else {
									   $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));
							           $end_date = date('Y/m/d'); 
                                       $resultado = getWorkingDays($startdate, $end_date, $holidays);
                              
							            // aplicamos ajuste de dias;
									   		   $ajuste_dias = $row_vacantes['ajuste_dias'];
                                           if ($ajuste_dias != 0) { $resultado = $resultado - $ajuste_dias; } 
                                           if ($resultado <= 0) {  
						            echo "<div class='label label-primary'>0 DÍAS</div>";
									} else if ($resultado < 4) {  
									echo "<div class='label label-success'>". round($resultado) . " DÍAS</div>"; 
									} else if ($resultado < ($row_vacantes['dias'])) {  
									echo "<div class='label label-success'>". round($resultado) . " DÍAS</div>"; 
									} else if ($resultado < ($row_vacantes['dias'] + 4)) {  
									echo "<div class='label label-warning'>". round($resultado) . " DÍAS</div>";
									} else if ($dias4 > 0) {
									echo "<div class='label label-danger'>". round($resultado) . " DÍAS <i class='icon-pushpin'></i></div>"; 									
									} else if ($resultado > ($row_vacantes['dias'] + 1)) {
									echo "<div class='label label-danger'>". round($resultado) . " DÍAS</div>"; } }?>
                                </td>
							<td><?php if ($row_vacantes['IDrequi'] == 1) {echo "No";} else {echo "Si";} ?>&nbsp; </td>
							<td><?php if ($row_vacantes['IDmotivo_v'] == 3) {echo "Temporal";}  if ($row_vacantes['IDmotivo_v'] == 5) {echo "Pull Vacac.";} else {echo "No";} ?>&nbsp; </td>
							<td><?php switch ($row_vacantes['IDapoyo']) {
                             case 1: echo "RECLUTAMIENTO"; break;
                             case 2: echo "PREFILTRO DE CANDIDATOS"; break;
                             case 3: echo "ENTREVISTA RH";  break;
                             case 4: echo "ENTREVISTA JEFE INMEDIATO";  break;
                             case 5: echo "PSICOMETRICOS";  break;
                             case 6: echo "EXAMEN MEDICO";  break;
                             case 7: echo "ESTUDIO SOCIOECONOMICO";  break;
                             case 8: echo "PRUEBAS DE HONESTIDAD";  break;
                             case 9: echo "PRUEBA DE MANEJO";  break;
                             case 10: echo "ENTREGA DE DOCUMENTOS";  break;
                             case 11: echo "VACANTE CUBIERTA";  break;
                             case 12: echo "VACANTE SUSPENDIDA";  break;
                             case 13: echo "VACANTE CANCELADA";  break;
                             case 15: echo "PENDIENTE DE RECIBIR DOCUMENTO (DP, REQUI)";  break;
                             case 16: echo "OTRO";  break;
                           } ?>&nbsp; </td>
							<td>
                         <button type="button" class="btn btn-primary btn-icon" onClick="window.location.href='vacante_edit.php?IDvacante=<?php echo $row_vacantes['IDvacante']; ?>'">Editar</button>
                            </td>
						    </tr>
                            
                  <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_vacantes['IDvacante']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la vacante?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="vacante_edit.php?IDvacante_borrar=<?php echo $row_vacantes['IDvacante']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

					    <?php } while ($row_vacantes = mysql_fetch_assoc($vacantes)); ?>
						<?php } else { ?>
                        
							<tr>
							<td>No se tienen vacantes registradas en el Sistema.</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
                            <td></td>
							<td></td>
							<td></td>
							<td></td>
						    </tr>     
                                               
						<?php }  ?>
					    </tbody>
				    </table>
                    
                  <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-success" href="vacante_edit.php">Agregar Vacante<i class="icon-arrow-right14 position-right"></i></a>
                    </div>
					</div>
					<!-- /colored button -->

                    </div>
					<!-- /Contenido -->

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