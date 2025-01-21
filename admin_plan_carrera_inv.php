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
mysql_query("SET NAMES 'utf8'");
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
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];


if(isset($_GET['IDmatriz'])){$la_matriz = $_GET['IDmatriz'];} else {$la_matriz = $row_usuario['IDmatriz'];}
if(isset($_POST['la_matriz']) and $_POST['la_matriz'] == 0) {$la_matriz = $IDmatrizes;} else if(isset($_POST['la_matriz']) and $_POST['la_matriz'] != 0) {$la_matriz = $_POST['la_matriz'];} else {$la_matriz = $row_usuario['IDmatriz'];}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$semana_inicio = $semana - 8;
if ($semana_inicio < 1){$semana_inicio = 1;}
$semana_fin = $semana;


mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea, prod_activos.IDaplica_SED, pc_semaforo.IDplan, pc_semaforo.reqa, pc_semaforo.reqb, pc_semaforo.reqc, pc_semaforo.reqd, pc_semaforo.reqe, pc_semaforo.reqf, pc_semaforo.estatus, vac_puestos.plan_carrera, vac_matriz.matriz FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE prod_activos.IDmatriz in ($la_matriz) AND vac_puestos.plan_carrera = 1 ";   
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

$query_area = "SELECT * FROM vac_areas WHERE IDarea IN (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$query_puestos = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.tipo, vac_puestos.IDaplica_PROD, vac_puestos.modal, vac_puestos.dias, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.tab, vac_puestos.prod_captura_tipo, vac_puestos.plan_carrera, prod_activos.IDmatriz FROM vac_puestos INNER JOIN prod_activos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE vac_puestos.plan_carrera = 1 AND prod_activos.IDmatriz in ($la_matriz) ORDER BY vac_puestos.denominacion ASC";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$chofers = array(42, 43, 44, 47);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

if (!isset($_POST['reqa'])) {$reqa = '';} else { $reqa = $_POST['reqa'];}
if (!isset($_POST['reqb'])) {$reqb = '';} else { $reqb = $_POST['reqb'];}
if (!isset($_POST['reqc'])) {$reqc = '';} else { $reqc = $_POST['reqc'];}
if (!isset($_POST['reqd'])) {$reqd = '';} else { $reqd = $_POST['reqd'];}
if (!isset($_POST['reqe'])) {$reqe = '';} else { $reqe = $_POST['reqe'];}
if (!isset($_POST['reqf'])) {$reqf = '';} else { $reqf = $_POST['reqf'];}
if (!isset($_POST['observaciones'])) {$observaciones = '';} else { $observaciones = $_POST['observaciones'];}
$el_empleado = $_GET['IDempleado'];

$updateSQL = "UPDATE pc_semaforo SET reqa = '$reqa', reqb = '$reqb', reqc = '$reqc', reqd = '$reqd', reqe = '$reqe',  reqf = '$reqf', observaciones = '$observaciones' WHERE IDempleado = '$el_empleado'";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "plan_carrera_inv_upd.php?IDempleado=$el_empleado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
if (!isset($_POST['reqa'])) {$reqa = '';} else { $reqa = $_POST['reqa'];}
if (!isset($_POST['reqb'])) {$reqb = '';} else { $reqb = $_POST['reqb'];}
if (!isset($_POST['reqc'])) {$reqc = '';} else { $reqc = $_POST['reqc'];}
if (!isset($_POST['reqd'])) {$reqd = '';} else { $reqd = $_POST['reqd'];}
if (!isset($_POST['reqe'])) {$reqe = '';} else { $reqe = $_POST['reqe'];}
if (!isset($_POST['IDpuesto'])) {$IDpuesto = '';} else { $IDpuesto = $_POST['IDpuesto'];}
if (in_array($_POST['IDpuesto'], $chofers)) {$reqa = 1; $reqb = 1;}
if (!isset($_POST['estatus'])) {$estatus = '';} else { $estatus = $_POST['estatus'];}
if (!isset($_POST['observaciones'])) {$observaciones = '';} else { $observaciones = $_POST['observaciones'];}
$el_empleado = $_GET['IDempleado'];

$insertSQL = "INSERT INTO pc_semaforo (IDempleado, IDpuesto, estatus, reqa, reqb, reqc, reqd, reqe, reqf, observaciones) VALUES ('$el_empleado', '$IDpuesto', '$estatus', '$reqa', '$reqb', '$reqc', '$reqd', '$reqe', '$reqf', '$observaciones')";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "plan_carrera_inv_upd.php?IDempleado=$el_empleado";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
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


	                <!-- Content area -->
				<div class="content">
                
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 1) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente la información. Si el empelado es viable, aparecerá en la sección de En proceso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Plan de Carrera- Inventario</h5>
						</div>

					<div class="panel-body">
							<p>Matriz:  <?php echo $row_matriz['matriz']; ?></p>
                            <p><a href="admin_plan_carr_reporte.php?IDmatriz=<?php echo $la_matriz; ?>" class="btn btn-success">Descargar reporte detallado</a>
                            <a href="admin_plan_carr_reporte.php" class="btn btn-warning">Descargar reporte Global</a></p>

                   <form method="POST" action="admin_plan_carrera_inv.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <div class="col-lg-9">
                             <select name="la_matriz" class="form-control">
                               <option value="0" <?php if (!(strcmp("0", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz: Todas</option>
                               <?php do {  ?>
                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
							   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                               <?php
                              } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                              $rows = mysql_num_rows($lmatriz);
                              if($rows > 0) {
                                  mysql_data_seek($lmatriz, 0);
                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
                              } ?></select>
						    </div>
                            </td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                             </td>
					      </tr>
					    </tbody>
				    </table>
				</form>



				<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Matriz</th>
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>Fecha de Alta</th>
                      <th>Estatus</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php do {  ?>
                        <tr>
                            <td><?php echo $row_detalle['matriz']; ?></td>
                            <td><?php echo $row_detalle['IDempleado'];  ?></td>
                            <td><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?></td>
                            <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
                            <td><?php echo date('d/m/Y', strtotime($row_detalle['fecha_alta'])); ?></td>
                            <td><?php 
									if ($row_detalle['IDplan'] == '') {echo "Sin caputura";}
								elseif ($row_detalle['reqb'] == 1 AND $row_detalle['reqe'] == 1) {echo "Viable";} 
								elseif ($row_detalle['reqe'] == 0 OR $row_detalle['reqb'] == 0) {echo "No viable";} 
								else {echo "Sin caputura";}
							?></td>
                            <?php if ($row_detalle['IDplan'] > 0 ) {  ?>
                            <td><div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>')" class="btn btn-info btn-icon">Actualizar</div></td>
                        	<?php } else {  ?>
                            <td><div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>')" class="btn btn-info btn-icon">Capturar</div></td>
                        	<?php } ?>
                        </tr>
                          <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 
					  </div>


                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Inventario de Plan de Carrera</h5>
								</div>
							<div class="modal-body">
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->


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
<script>
function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('plan_carrera_mdl.php?IDempleado=' + modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>