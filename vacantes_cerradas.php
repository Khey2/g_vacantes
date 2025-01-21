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

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 


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
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM vac_puestos ORDER BY denominacion ASC";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

if (isset($_POST['la_matriz'])) {foreach ($_POST['la_matriz'] as $matris)
	{	$_SESSION['la_matriz'] = implode(",", $_POST['la_matriz']);}}  else { $_SESSION['la_matriz'] = $IDmatriz;} 

if (isset($_POST['el_area'])) {foreach ($_POST['el_area'] as $earea)
  {	$_SESSION['el_area'] = implode(",", $_POST['el_area']);}}  else { $_SESSION['el_area'] = '1,2,3,4,5,6,7,8,9,10,11,12,13';} 


	 if (isset($_POST['el_estatus2']) && $_POST['el_estatus2'] == 3) {	$_SESSION['el_estatus2'] = 3; $a2 = ' AND vac_vacante.IDrequi = 1 '; } 
else if (isset($_POST['el_estatus2']) && $_POST['el_estatus2'] == 2) {	$_SESSION['el_estatus2'] = 2; $a2 = ' AND vac_vacante.IDrequi = 0 ';}
else if (isset($_POST['el_estatus2']) && $_POST['el_estatus2'] == 1) {	$_SESSION['el_estatus2'] = 1; $a2 = '  ';}
else  {	$_SESSION['el_estatus2'] = 1; $a2 = '  '; }
$el_estatus2 = $_SESSION['el_estatus2'];

// Filtros
$filtro_fin = date("Y-m-d");
$filtro_inicio =  date("Y-m-d",strtotime($filtro_fin." - 15 days")); 

if (isset($_POST['fecha_inicio'])) {
	$y1 = substr( $_POST['fecha_inicio'], 6, 4 );
	$m1 = substr( $_POST['fecha_inicio'], 3, 2 );
	$d1 = substr( $_POST['fecha_inicio'], 0, 2 );
	$fecha_inicio =  $y1."-".$m1."-".$d1;
$_SESSION['fecha_inicio'] = $fecha_inicio;
} else {
$_SESSION['fecha_inicio'] = $filtro_inicio;
}

if (isset($_POST['fecha_fin'])) {
	$y2 = substr( $_POST['fecha_fin'], 6, 4 );
	$m2 = substr( $_POST['fecha_fin'], 3, 2 );
	$d2 = substr( $_POST['fecha_fin'], 0, 2 );
	$fecha_fin =  $y2."-".$m2."-".$d2;
$_SESSION['fecha_fin'] = $fecha_fin;
} else {
$_SESSION['fecha_fin'] = $filtro_fin;
}


$la_matriz = $_SESSION['la_matriz'];
$el_area = $_SESSION['el_area']; 
$fecha_inicio = $_SESSION['fecha_inicio'];
$fecha_fin = $_SESSION['fecha_fin'];


mysql_select_db($database_vacantes, $vacantes);
$query_vacantes = "SELECT vac_vacante.IDvacante, vac_matriz.matriz, vac_puestos.denominacion, vac_areas.area, vac_puestos.dias, vac_vacante.IDmotivo_v, vac_vacante.ajuste_dias, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_estatus.estatus, vac_tipo_vacante.tipo_vacante, vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_sucursal.sucursal FROM vac_vacante LEFT JOIN vac_estatus ON vac_vacante.IDestatus = vac_estatus.IDestatus LEFT JOIN vac_matriz ON vac_vacante.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_tipo_vacante ON vac_vacante.IDtipo_vacante = vac_tipo_vacante.IDtipo_vacante LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz AND vac_vacante.IDsucursal = vac_sucursal.IDsucursal WHERE vac_vacante.IDarea IN ($el_area) AND vac_vacante.IDmatriz IN ($la_matriz) AND vac_vacante.IDestatus IN (2, 3) AND vac_vacante.fecha_requi BETWEEN '$fecha_inicio' AND '$fecha_fin' "; 
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
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
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html59.js"></script>
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

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Vacantes Cerradas</h5>
						</div>

					<div class="panel-body">
							A continuación se muestran las vacantes cerradas. Selecciona la vacante que requieras editar.</br>
							Utilizar el buscador para filtrar las vacantes por cualquiera de criterio existente en la tabla.
							<p><div class='label label-warning'>!</div> Plaza Temporal.</p>
					</div>

                       <form method="POST" action="vacantes_cerradas.php">

					<table class="table">
						<tbody>							  
							<tr>
							<td width="20%">Área: <select name="el_area[]" class="multiselect" multiple="multiple" >
							<?php $cadena2 = $el_area; $array = explode(",", $cadena2);
											do { ?>
											   <option value="<?php echo $row_area['IDarea']?>"<?php foreach ($array as $learea) { if (!(strcmp($row_area['IDarea'], $learea))) {echo 
												"selected=\"selected\"";} } ?>><?php echo $row_area['area']?></option>
											   <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) { mysql_data_seek($area, 0);
											  $row_area = mysql_fetch_assoc($area); 
											  } ?> 
									</select>
                            </td>
                           <td width="20%"> Matriz: <select name="la_matriz[]" class="multiselect" multiple="multiple">
						   <?php $cadena = $la_matriz; $array = explode(",", $cadena);
											do { ?>
											   <option value="<?php echo $row_amatriz['IDmatriz']?>"<?php foreach ($array as $lematriz) { if (!(strcmp($row_amatriz['IDmatriz'], $lematriz))) {echo "selected=\"selected\"";} } ?>><?php echo $row_amatriz['matriz']?></option>
											   <?php
											  } while ($row_amatriz = mysql_fetch_assoc($amatriz));
											  $rows = mysql_num_rows($amatriz);
											  if($rows > 0) { mysql_data_seek($amatriz, 0);
											  $row_amatriz = mysql_fetch_assoc($amatriz); 
											  } ?> 
                                             </select>
                            </td>
							<td>
                            <td width="15%">Fecha inicio: 
                            <div class="input-group">
											<span class="input-group-addon"><i class="icon-calendar5"></i></span>
											<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="<?php if (isset($_SESSION['fecha_inicio'])) {echo date('d/m/Y', strtotime($_SESSION['fecha_inicio']));} else { echo "";} ?>">
							</div>
                            </td>
                            <td width="15%">Fecha fin: 
                            <div class="input-group">
											<span class="input-group-addon"><i class="icon-calendar5"></i></span>
											<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" value="<?php if (isset($_SESSION['fecha_fin'])) {echo date('d/m/Y', strtotime($_SESSION['fecha_fin']));} else { echo "";} ?>">
							</div>
                            </td>
                            </td>
							<td  width="20%">
								Requi: <select class="form-control"  name="el_estatus2">
								<option value="1" <?php if ($el_estatus2 == 1) {echo "selected=\"selected\"";} ?>>Todas</option>
								<option value="2" <?php if ($el_estatus2 == 2) {echo "selected=\"selected\"";} ?>>Si</option>
								<option value="3" <?php if ($el_estatus2 == 3) {echo "selected=\"selected\"";} ?>>No</option>
								</select>
						    </td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                             </td>
					      </tr>
					    </tbody>
				    </table>
</form>


					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Folio</th>
							    <th>Matriz</th>
							    <th>Denominación</th>
							    <th>Área</th>
							    <th>Fecha Requi</th>
							    <th>Fecha Ocupacion</th>
							    <th>Temporal</th>
							    <th>Días Transcurridos</th>
							    <th>Estatus</th>
							    <th>Acciones</th>
						    </tr>
					    </thead>
						<tbody>							  
						<?php if ($totalRows_vacantes > 0){ ?>
						<?php do { ?>
							<tr>
							<td><?php if ($row_vacantes['IDmotivo_v'] == 3) {echo "<div class='label label-warning'>!</div>"; }?> <?php echo $row_vacantes['IDvacante']; ?></td>
							<td><?php echo $row_vacantes['matriz'] . " - " . $row_vacantes['sucursal']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['denominacion']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['area']; ?>&nbsp; </td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_vacantes['fecha_requi'])) ?></td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_vacantes['fecha_ocupacion'])) ?></td>
							<td><?php if ($row_vacantes['IDmotivo_v'] == 3) {echo "Si";} else {echo "No";} ?>&nbsp; </td>
                            <td><?php  $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));
							           if ($row_vacantes['fecha_ocupacion'] > 0) { $end_date = date('Y/m/d', strtotime($row_vacantes['fecha_ocupacion']));
									   } else { $end_date = date('Y/m/d'); }
                                       $resultado = getWorkingDays($startdate, $end_date, $holidays);
                              ?><?php 
							            // aplicamos ajuste de dias;
									   $ajuste_dias = $row_vacantes['ajuste_dias'];
                                           if ($ajuste_dias != 0) { $resultado = $resultado - $ajuste_dias; } 
                                           if ($resultado < 4) {  
						            echo "<div class='label label-primary'>". round($resultado) . " DÍAS</div>";
									} else if ($resultado < ($row_vacantes['dias'])) {  
									echo "<div class='label label-success'>". round($resultado) . " DÍAS</div>"; 
									} else if ($resultado < ($row_vacantes['dias'] + 4)) {  
									echo "<div class='label label-warning'>". round($resultado) . " DÍAS</div>"; 
									} else if ($resultado > ($row_vacantes['dias'] + 1)) {
									echo "<div class='label label-danger'>". round($resultado) . " DÍAS</div>"; }?>
                                </td>
							<td><?php switch ($row_vacantes['IDestatus']) {
                             case 1:
                             echo "EN PROCESO";
                             break;
                             case 2:
                             echo "CUBIERTA";
                             break;
                             case 3:
                             echo "SUSPENDIDA";
                             break;
                           } ?>&nbsp; </td>
							<td><a href="vacante_edit_no.php?IDvacante=<?php echo $row_vacantes['IDvacante']; ?>" class="btn btn-info">Ver</a></td>
						    </tr>
					    <?php } while ($row_vacantes = mysql_fetch_assoc($vacantes)); ?>
						<?php } else { ?>
                        
							<tr>
							<td>No se tienen vacantes registradas con el filtro seleccionado.</td>
							<td></td>
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
                    <a class="btn btn-primary" href="vacante_edit.php">Agregar Vacante<i class="icon-arrow-right14 position-right"></i></a>
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
<?php
mysql_free_result($vacantes);
?>
