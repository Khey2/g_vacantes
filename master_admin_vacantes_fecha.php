<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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
$mis_matrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

$el_estatus  = 0;
    
mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($mis_matrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

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
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_apoyo = "SELECT * FROM vac_apoyo";
$apoyo = mysql_query($query_apoyo, $vacantes) or die(mysql_error());
$row_apoyo = mysql_fetch_assoc($apoyo);
$totalRows_apoyo = mysql_num_rows($apoyo);

mysql_select_db($database_vacantes, $vacantes);
$query_estato = "SELECT * FROM vac_estatus";
$estato = mysql_query($query_estato, $vacantes) or die(mysql_error());
$row_estato = mysql_fetch_assoc($estato);
$totalRows_estato = mysql_num_rows($estato);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 
$fecha_filtro = $_GET['fecha_filtro'];

mysql_select_db($database_vacantes, $vacantes);
$query_vacantes = "SELECT vac_vacante.IDvacante, vac_vacante.fecha_usr4, vac_matriz.matriz, vac_puestos.denominacion, vac_areas.area, vac_vacante.IDmotivo_v,  vac_vacante.IDrequi, vac_puestos.dias, vac_vacante.ajuste_dias, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_estatus.estatus, vac_tipo_vacante.tipo_vacante, vac_sucursal.sucursal, vac_apoyo.apoyo FROM vac_vacante LEFT JOIN vac_estatus ON vac_vacante.IDestatus = vac_estatus.IDestatus LEFT JOIN vac_matriz ON vac_vacante.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_tipo_vacante ON vac_vacante.IDtipo_vacante = vac_tipo_vacante.IDtipo_vacante LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz AND vac_vacante.IDsucursal = vac_sucursal.IDsucursal LEFT JOIN vac_apoyo ON vac_vacante.IDapoyo = vac_apoyo.IDapoyo WHERE vac_vacante.IDvacante > 0 AND vac_vacante.anio IN ( 2024 ) AND denominacion LIKE '%CHOFER%'
AND  DATE(fecha_requi) > '$fecha_filtro' ";
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
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

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Vacantes</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona la Matriz, el area, el mes, estatus específico y si la vacante está vencida para aplicar un filtro avanzado. También puedes utilizar el Filtrado rápido.</p>
					        <p>Puedes exportar el resultado a Excel, así como seleccionar las columnas a exportar.</p>
			     </div>
                    
   <p>&nbsp;</p>                 

					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Folio</th>
							    <th>Matriz - Sucursal</th>
							    <th>Denominación</th>
							    <th>Área</th>
							    <th>Con Requi</th>
							    <th>Temporal</th>
							    <th>Fecha Requi o Solicitud</th>
							    <th>Días Trans.</th>
							    <th>Estatus Gral.</th>
							    <th>Estatus Esp.</th>
							    <th></th>
						    </tr>
					    </thead>
						<tbody>							  

						<?php do { $dias4 = $row_vacantes['fecha_usr4']; ?>
							<?php  $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));
							           if (($row_vacantes['fecha_ocupacion'] != 0) && ($row_vacantes['IDestatus'] != 1)) { $end_date =  date('Y/m/d', strtotime($row_vacantes['fecha_ocupacion']));
									} else { $end_date = date('Y/m/d'); }
                                       $resultado = getWorkingDays($startdate, $end_date, $holidays);
                               
							            // aplicamos ajuste de dias;
									   $ajuste_dias = $row_vacantes['ajuste_dias'];
                                           if ($ajuste_dias != 0) { $resultado = $resultado - $ajuste_dias; }  
			       						  if ($resultado > ($row_vacantes['dias']) || $el_estatus == 0) {?>

							<tr>
							<td><?php echo $row_vacantes['IDvacante']; ?></td>
							<td><?php echo $row_vacantes['matriz'] . " - " . $row_vacantes['sucursal']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['denominacion']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['area']; ?>&nbsp; </td>
							<td><?php if ($row_vacantes['IDrequi'] == 1) {echo "No";} else {echo "Si";} ?>&nbsp; </td>
							<td><?php if ($row_vacantes['IDmotivo_v'] == 3) {echo "Si";} else {echo "No";} ?>&nbsp; </td>
							<td><?php if ($row_vacantes['fecha_requi'] != 0) { echo date( 'd/m/Y', strtotime($row_vacantes['fecha_requi'])); }?></td>
                            <td><?php if ($row_vacantes['IDrequi'] == 1) {echo "<div class='label label-default'>N/A</div>";} else {

									   $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));
							           if (($row_vacantes['fecha_ocupacion'] != 0) && ($row_vacantes['IDestatus'] != 1)) { $end_date2 =  date('Y/m/d', strtotime($row_vacantes['fecha_ocupacion'])); 
									   $resultado = getWorkingDays($startdate, $end_date2, $holidays);} else {
                                       $resultado = getWorkingDays($startdate, $end_date, $holidays);}
                             
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
							<td><?php switch ($row_vacantes['IDestatus']) {
                             case 1: echo "EN PROCESO"; break;
                             case 2: echo "CUBIERTA"; break;
                             case 3: echo "SUSPENDIDA"; break;
                           } ?>&nbsp; </td>
                            <td><?php echo $row_vacantes['apoyo']; ?>&nbsp; </td>
							<td>
                         <button type="button" class="btn btn-primary btn-icon" onClick="window.location.href='master_admin_vacante_edit.php?IDvacante=<?php echo $row_vacantes['IDvacante']; ?>'">Editar</button>
						 <button type="button" data-target="#modal_theme_danger<?php echo $row_vacantes['IDvacante']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>                            </td>
						    </tr>
                        <?php } ?>
                        
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
                                    <a class="btn btn-danger" href="master_admin_vacante_edit.php?IDvacante_borrar=<?php echo $row_vacantes['IDvacante']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                        
					    <?php } while ($row_vacantes = mysql_fetch_assoc($vacantes)); ?>
					    </tbody>
				    </table>
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