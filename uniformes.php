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
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


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
$IDusuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
// cambiar periodo cada semestre?
$IDperiodo = 1;

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

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

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT sed_uniformes.IDempleado AS Uniforme, sed_uniformes.T_pantalon_ventas, sed_uniformes.T_pantalon_operaciones, sed_uniformes.T_camisa_ventas, sed_uniformes.T_playera_polo_distribucion, sed_uniformes.T_playera_roja_almacen, sed_uniformes.T_faja, sed_uniformes.T_botas, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc13, prod_activos.fecha_alta, prod_activos.descripcion_nomina, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDarea, vac_areas.area FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN sed_uniformes ON prod_activos.IDempleado = sed_uniformes.IDempleado  WHERE prod_activos.IDmatriz = '$la_matriz' AND prod_activos.IDarea in (1,2,3,4,5,6) ORDER BY prod_activos.IDpuesto ASC";
mysql_query("SET NAMES 'utf8'");
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$insertSQL = sprintf("INSERT INTO sed_uniformes (IDempleado, IDmatriz, IDperiodo, T_pantalon_ventas, T_pantalon_operaciones, T_camisa_ventas, T_playera_polo_distribucion, T_playera_roja_almacen, T_faja, T_botas, F_pantalon_ventas, F_pantalon_operaciones, F_camisa_ventas, F_playera_polo_distribucion, F_playera_roja_almacen, F_faja, F_botas, Licencia, Licencia_vigencia, Licencia2, Licencia_vigencia2, Sexo, Observaciones, IDusuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		GetSQLValueString($_POST['IDempleado'], "text"),
		GetSQLValueString($IDmatriz, "text"),
		GetSQLValueString($IDperiodo, "text"),
		GetSQLValueString($_POST['T_pantalon_ventas'], "text"),
		GetSQLValueString($_POST['T_pantalon_operaciones'], "text"),
		GetSQLValueString($_POST['T_camisa_ventas'], "text"),
		GetSQLValueString($_POST['T_playera_polo_distribucion'], "text"),
		GetSQLValueString($_POST['T_playera_roja_almacen'], "text"),
		GetSQLValueString($_POST['T_faja'], "text"),
		GetSQLValueString($_POST['T_botas'], "text"),
		GetSQLValueString($_POST['F_pantalon_ventas'], "text"),
		GetSQLValueString($_POST['F_pantalon_operaciones'], "text"),
		GetSQLValueString($_POST['F_camisa_ventas'], "text"),
		GetSQLValueString($_POST['F_playera_polo_distribucion'], "text"),
		GetSQLValueString($_POST['F_playera_roja_almacen'], "text"),
		GetSQLValueString($_POST['F_faja'], "text"),
		GetSQLValueString($_POST['F_botas'], "text"),
		GetSQLValueString($_POST['Licencia'], "text"),
		GetSQLValueString($_POST['Licencia_vigencia'], "text"),
		GetSQLValueString($_POST['Licencia2'], "text"),
		GetSQLValueString($_POST['Licencia_vigencia2'], "text"),
		GetSQLValueString($_POST['Sexo'], "text"),
		GetSQLValueString($_POST['Observaciones'], "text"),
		GetSQLValueString($_POST['IDusuario'], "text"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
header("Location: uniformes.php?info=1");
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$updateSQL = sprintf("UPDATE sed_uniformes SET IDusuario=%s, T_pantalon_ventas=%s, T_pantalon_operaciones=%s, T_camisa_ventas=%s, T_playera_polo_distribucion=%s, T_playera_roja_almacen=%s, T_faja=%s, T_botas=%s, F_pantalon_ventas=%s, F_pantalon_operaciones=%s, F_camisa_ventas=%s, F_playera_polo_distribucion=%s, F_playera_roja_almacen=%s, F_faja=%s, F_botas=%s, Licencia=%s, Licencia_vigencia=%s, Licencia2=%s, Licencia_vigencia2=%s, Sexo=%s, Observaciones=%s WHERE IDempleado=%s",
		GetSQLValueString($_POST['IDusuario'], "text"),
		GetSQLValueString($_POST['T_pantalon_ventas'], "text"),
		GetSQLValueString($_POST['T_pantalon_operaciones'], "text"),
		GetSQLValueString($_POST['T_camisa_ventas'], "text"),
		GetSQLValueString($_POST['T_playera_polo_distribucion'], "text"),
		GetSQLValueString($_POST['T_playera_roja_almacen'], "text"),
		GetSQLValueString($_POST['T_faja'], "text"),
		GetSQLValueString($_POST['T_botas'], "text"),
		GetSQLValueString($_POST['F_pantalon_ventas'], "text"),
		GetSQLValueString($_POST['F_pantalon_operaciones'], "text"),
		GetSQLValueString($_POST['F_camisa_ventas'], "text"),
		GetSQLValueString($_POST['F_playera_polo_distribucion'], "text"),
		GetSQLValueString($_POST['F_playera_roja_almacen'], "text"),
		GetSQLValueString($_POST['F_faja'], "text"),
		GetSQLValueString($_POST['F_botas'], "text"),
		GetSQLValueString($_POST['Licencia'], "text"),
		GetSQLValueString($_POST['Licencia_vigencia'], "text"),
		GetSQLValueString($_POST['Licencia2'], "text"),
		GetSQLValueString($_POST['Licencia_vigencia2'], "text"),
		GetSQLValueString($_POST['Sexo'], "text"),
		GetSQLValueString($_POST['Observaciones'], "text"),
		GetSQLValueString($_POST['IDempleado'], "int"));

	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	header("Location: uniformes.php?info=2");
}

if ((isset($_POST["MM_updated"])) && ($_POST["MM_updated"] == "form2")) {
	$updateSQL = sprintf("UPDATE vac_matriz SET licencia_costo_estatal=%s, licencia_costo_federal=%s, licencia_observaciones=%s WHERE IDmatriz=$IDmatriz",
			GetSQLValueString($_POST['licencia_costo_estatal'], "text"),
			GetSQLValueString($_POST['licencia_costo_federal'], "text"),
			GetSQLValueString($_POST['licencia_observaciones'], "text"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes.php?info=5");
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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_inputs.js"></script>
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Uniforme.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 5))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la información de licencias.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el Uniforme.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Uniformes</h6>
								</div>

								<div class="panel-body">
								<p>A continuación se muestra la plantilla activa de la Sucursal <strong><?php echo $row_matriz['matriz']; ?></strong>. Da clic en Uniformes, para cargar la información de tallas y última entrega.<br/>
								 Los empleados marcados con <i class="icon-alert text text-danger"></i> se deben capturar de nuevo, ya que no tienen datos guardados.<br/>
								 Los empleados marcados con <i class="icon-truck text text-warning"></i> se deben capturar su licencia.
								</p>

								<a href="uniformes_reporte2.php" class="btn btn-primary">Descargar Reporte Detallado</a>
								<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-info">Costo Licencias</button>


								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-warning"> 
                                    <th>Acciones</th>
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Fecha Alta</th>
                                    <th>Vigencia Licencia</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php do { 
							  
								$IDempleado = $row_autorizados['IDempleado']; 
								$query_autorizados3 = "SELECT* FROM sed_uniformes WHERE sed_uniformes.IDempleado = '$IDempleado' AND IDperiodo = $IDperiodo";
								$autorizados3 = mysql_query($query_autorizados3, $vacantes) or die(mysql_error());
								$row_autorizados3 = mysql_fetch_assoc($autorizados3);
								$totalRows_autorizados3 = mysql_num_rows($autorizados3);

								if (empty($row_autorizados['T_pantalon_ventas']) AND empty($row_autorizados['T_pantalon_operaciones']) AND empty($row_autorizados['T_camisa_ventas']) AND empty($row_autorizados['T_playera_polo_distribucion']) AND empty($row_autorizados['T_playera_roja_almacen']) AND empty($row_autorizados['T_faja']) AND empty($row_autorizados['T_botas'])) {$error = 1;} else {$error = 0;}

								?>
                                    <tr>
                                      <td>
									  
										<?php if ($totalRows_autorizados3 == 0) { ?>
										<div onClick="loadDynamicContentModal2('<?php echo $row_autorizados['IDempleado']; ?>')" class="btn btn-xs btn-default btn-icon">Capturar</div> &nbsp;
										<?php } else { ?>
										<div onClick="loadDynamicContentModal2('<?php echo $row_autorizados['IDempleado']; ?>')" class="btn btn-xs btn-warning btn-icon">Actualizar</div>
										 <?php if ($error == 1) { echo '<i class="icon-alert text text-danger"></i>'; } ?> &nbsp; 
										<?php }  ?>
										<?php if ($row_autorizados3['Licencia'] == '') { ?><i class="icon-truck text text-warning"></i><?php }  ?>
									  </td>
                                      <td><?php echo $row_autorizados['IDempleado']; ?>&nbsp;</td>
                                      <td><?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?></td>
                                      <td><?php echo date('d/m/Y', strtotime($row_autorizados['fecha_alta'])); ?></td>
                                      <td><?php if ($row_autorizados3['Licencia_vigencia'] != '') {echo date('d/m/Y', strtotime($row_autorizados3['Licencia_vigencia']));} else { echo "-";} ?></td>
                                      <td><?php echo $row_autorizados['area']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['denominacion']; ?>
									  
									</td>
                                    </tr>
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados)); ?>
                                  </tbody>
                                </table>
								</div>
							</div>
						</div>
                                    
					<!-- /Contenido -->


					<!-- Inline form modal -->
					<div id="bootstrap-modal2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h5 class="modal-title">Formulario Uniformes</h5>
								</div>
								   <div id="conte-modal2">
								   </div>
							</div>
						</div>
					<!-- /inline form modal -->
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



                     <!-- danger modal -->
					 <div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-info">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Costo de Licencias</h6>
								</div>

								<form method="post" class="form-horizontal form-validate-jquery" name="form2" action="uniformes.php">

								<div class="modal-body">

									<label class="control-label col-lg-4">Licencia Federal:</label>
									<div class="col-lg-8">
									<input type="text" class="form-control" name="licencia_costo_estatal" id="licencia_costo_estatal"  value="<?php echo htmlentities($row_matriz['licencia_costo_estatal'], ENT_COMPAT, 'utf-8'); ?>">
									</div>
									<p>&nbsp;</p>

									<label class="control-label col-lg-4">Licencia Estatal:</label>
									<div class="col-lg-8">
									<input type="text" class="form-control" name="licencia_costo_federal" id="licencia_costo_federal"  value="<?php echo htmlentities($row_matriz['licencia_costo_federal'], ENT_COMPAT, 'utf-8'); ?>">
									</div>
									<p>&nbsp;</p>

									<label class="control-label col-lg-4">Observaciones:</label>
									<div class="col-lg-8">
									<textarea rows="2" cols="3" name="licencia_observaciones" id="licencia_observaciones" class="form-control"><?php echo htmlentities($row_matriz['licencia_observaciones'], ENT_COMPAT, ''); ?></textarea>
									</div>
									<p>&nbsp;</p>

								</div>

								<div class="modal-footer">
									<input type="hidden" name="MM_updated" value="form2">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-info">Capturar</button>
								</div>

								</form>

							</div>
						</div>
					</div>
					<!-- /danger modal -->



</body>
</html>
<script>
function loadDynamicContentModal2(modal){
	var options = { modal: true };
	$('#conte-modal2').load('uniforme.php?IDempleado='+ modal, function() {
		$('#bootstrap-modal2').modal({show:true});
  });  
}
</script> 
