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
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); 
$fecha_limite = date("Y-m-d", strtotime($fecha . '- 1 month')); 
//$fecha_limite = date("2024/01/01");
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
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

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
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


//las variables de sesion para el filtrado
if (isset($_POST['el_puesto'])) { foreach ($_POST['el_puesto'] as $puestos)
	{	$_SESSION['el_puesto'] = implode(", ", $_POST['el_puesto']);} } 
if (!isset($_SESSION['el_puesto'])) { $_SESSION['el_puesto'] = 0;}

if (isset($_POST['el_area'])) {	foreach ($_POST['el_area'] as $areas)
	{	$_SESSION['el_area'] = implode(", ", $_POST['el_area']);}	}  
if (!isset($_SESSION['el_area'])) {$_SESSION['el_area'] = 0;}

if ((isset($_GET['filtro'])) && ($_GET['filtro'] == 0)) {
 $_SESSION['el_puesto'] = 0;
 $_SESSION['el_area'] = 0;
}


if (isset($_POST['el_anio'])) {$_SESSION['el_anio'] = $_POST['el_anio'];}
if (!isset($_SESSION['el_anio'])) {$_SESSION['el_anio'] = $anio;}

$el_area = $_SESSION['el_area'];
$el_puesto = $_SESSION['el_puesto']; 
$el_anio = $_SESSION['el_anio']; 

if ($el_puesto != 0) { $llave1 = ' AND prod_activos.IDpuesto IN ('.$el_puesto.') ';} else {$llave1 = '';}
if ($el_area != 0) { $llave2 = ' AND prod_activos.IDarea in ('.$el_area.') ';} else {$llave2 = ' AND prod_activos.IDarea in (1,2,3,4,5,6)';}

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.descripcion_nomina, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDarea, vac_areas.area FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea WHERE prod_activos.IDmatriz = '$la_matriz' ".$llave1.$llave2." ORDER BY prod_activos.IDpuesto ASC";
mysql_query("SET NAMES 'utf8'");
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

$query_puestos = "SELECT * FROM vac_puestos";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 12";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);
$totalRows_areas = mysql_num_rows($areas);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$y1 = substr( $_POST['fecha_inicio'], 6, 4 );
$m1 = substr( $_POST['fecha_inicio'], 3, 2 );
$d1 = substr( $_POST['fecha_inicio'], 0, 2 );
$fecha_inicio = $y1."-".$m1."-".$d1;

$y2 = substr( $_POST['fecha_inicio'], 19, 4 );
$m2 = substr( $_POST['fecha_inicio'], 16, 2 );
$d2 = substr( $_POST['fecha_inicio'], 13, 2 );
$fecha_fin = $y2."-".$m2."-".$d2;

require 'assets/dias_vacaciones.php';
$startdate = date('Y/m/d', strtotime($fecha_inicio));
$end_date =  date('Y/m/d', strtotime($fecha_fin));
$IDdias_asignados = getWorkingDays($startdate, $end_date, $holidays);
 
if ($IDdias_asignados < 1) { header("Location: vacaciones.php?info=4"); } else {

  
$insertSQL = sprintf("INSERT INTO inc_vacaciones (IDempleado, fecha_inicio, fecha_fin, IDperiodo, IDmatriz, IDpuesto, IDarea, IDsucursal, denominacion, anio, IDdias_asignados) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($fecha_inicio, "date"),
                       GetSQLValueString($fecha_fin, "date"),
                       GetSQLValueString($_POST['IDperiodo'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['IDarea'], "text"),
                       GetSQLValueString($_POST['IDsucursal'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($el_anio, "text"),
                       GetSQLValueString($IDdias_asignados, "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

   header('Location: vacaciones.php?info=2');
} }

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDvacaciones'];
  $deleteSQL = "DELETE FROM inc_vacaciones WHERE IDvacaciones ='$borrado'";

mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
header("Location: vacaciones.php?info=3");
}

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
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
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
				
				       <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha guardado correctamente el registro de vacaciones.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				       <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro de vacaciones.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				       <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Error al capturar las fechas, intentalo de nuevo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Captura Vacaciones</h6>
								</div>

								<div class="panel-body">
								<p>Selecciona el nombre del empelado para capturar las vacaciones.</br>
									La fecha fin, es la fecha en la que se presenta a laborar.
								


                <form method="POST" action="vacaciones.php">
					<table class="table">
							<tr>
							<td>
                            Año: <select name="el_anio"  class="form-control" >
							<option value="2025"<?php if ($el_anio == 2025) {echo "selected=\"selected\"";} ?>>2025</option>
							<option value="2024"<?php if ($el_anio == 2024) {echo "selected=\"selected\"";} ?>>2024</option>
											<option value="2023"<?php if ($el_anio == 2023) {echo "selected=\"selected\"";} ?>>2023</option>
										</select>
                            </td>
							<td>
                            Área: 		<select name="el_area[]"  class="multiselect" multiple="multiple" >
											<?php do { ?>
                                               <option value="<?php echo $row_areas['IDarea']?>"<?php if (!(strcmp($row_areas['IDarea'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_areas['area']?></option>
                                               <?php
											  } while ($row_areas = mysql_fetch_assoc($areas));
											  $rows = mysql_num_rows($areas);
											  if($rows > 0) {
												  mysql_data_seek($areas, 0);
												  $row_areas = mysql_fetch_assoc($areas);
											  } ?>
										</select>
                            </td>
                           <td> Puesto: <select name="el_puesto[]" class="multiselect" multiple="multiple">
                                          <?php do {  ?>
                                           <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if (!(strcmp($row_puestos['IDpuesto'], $el_puesto))) {echo "selected=\"selected\"";} ?>> <?php echo $row_puestos['denominacion']?></option>
											<?php
                                            } while ($row_puestos = mysql_fetch_assoc($puestos));
                                              $rows = mysql_num_rows($puestos);
                                              if($rows > 0) {
                                                  mysql_data_seek($puestos, 0);
                                                  $row_puestos = mysql_fetch_assoc($puestos);
                                              } ?>
										</select>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar</button> 
							<a href="vacaciones.php?filtro=0" class="btn btn-default">Borrar filtro</a>	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="vacaciones_reporte2.php?IDmatriz=<?php echo $IDmatriz; ?>&anio=<?php echo $el_anio; ?>" class="btn btn-success">Reporte</a>							
							<a href="vacaciones_reportedetalle.php?IDmatriz=<?php echo $IDmatriz; ?>&anio=<?php echo $el_anio; ?>" class="btn btn-warning">Reporte detallado</a>							
							</td>
					      </tr>
				    </table>
				</form>


					<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>Acciones</th>
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Fecha Antig.</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                    <th>Días Capturados</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php 
									do { 
									$IDempleado = $row_autorizados['IDempleado'];
									mysql_select_db($database_vacantes, $vacantes);
									$query_detalle = "SELECT SUM(IDdias_asignados) AS Total FROM inc_vacaciones WHERE inc_vacaciones.IDempleado = '$IDempleado' AND  inc_vacaciones.anio = $el_anio"; 
									$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
									$row_detalle = mysql_fetch_assoc($detalle);
									$fecha_antiguedad = date('Y/m/d', strtotime($row_autorizados['fecha_antiguedad']));
								  ?>
                                    <tr>
                                      <td>
									   <?php if ($row_autorizados['fecha_antiguedad'] < $fecha_limite) { ?> 
									  <div onClick="loadDynamicContentModal('<?php echo $row_autorizados['IDempleado']; ?>')" class="btn btn-primary">Capturar</div></td>
									   <?php } else { ?> 
									   -
									   <?php }?> 
									  <td><?php echo $row_autorizados['IDempleado']; ?></td>
                                      <td><?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?></td>
                                      <td><?php echo date('d/m/Y', strtotime($row_autorizados['fecha_antiguedad'])); ?></td>
                                      <td><?php echo $row_autorizados['area']; ?></td>
                                      <td><?php echo $row_autorizados['denominacion']; ?></td>
                                      <td><?php if ($row_detalle['Total'] > 0) { ?>
									  <div onClick="loadDynamicContentModal2('<?php echo $row_autorizados['IDempleado']; ?>')"><a class="btn btn-success"><?php echo $row_detalle['Total']; ?></a></div>
										  <?php } else { echo "0";} ?></td>
                                    </tr>
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados)); ?>
                                  </tbody>
                                </table>
								</div>
							</div>
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
		
		
		                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Captura de Vacaciones</h5>
								  
								  
								</div>
							<div class="modal-body">
							
							
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->



</div>
	<!-- /page container -->
<script>
function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('vacacion_mdl.php?IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
<script>
function loadDynamicContentModal2(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('vacacion_mdl2.php?IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 

</body>
</html>