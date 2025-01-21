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
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM vac_puestos LEFT JOIN prod_activos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE prod_activos.IDmatriz = $IDmatriz AND prod_activos.IDaplica_INC = 1 ORDER BY vac_puestos.denominacion ASC";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 8";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);
$totalRows_areas = mysql_num_rows($areas);

$query_semanas = "SELECT DISTINCT inc_captura.semana FROM inc_captura";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

if(isset($_POST['el_area'])) { $_SESSION['el_area'] = $_POST['el_area']; } 
if(!isset($_SESSION['el_area'])) { $_SESSION['el_area'] = "1";}

$el_area = $_SESSION['el_area'];

$c1 = "";
if($_SESSION['el_area'] > 0) {
$c1 = " AND prod_activos.IDarea = '$el_area'"; }

if (isset($_POST['la_semana']) && $_POST['la_semana'] > 0) {$_SESSION['la_semana'] = $_POST['la_semana']; } 
if (!isset($_SESSION['la_semana'])) {$_SESSION['la_semana'] = $semana - 1;} 
$la_semana = $_SESSION['la_semana'];


mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.IDarea,prod_activos.denominacion,  inc_captura.IDcaptura, inc_captura.inc1 AS INC1, inc_captura.inc2 AS INC2, inc_captura.inc3 AS INC3, inc_captura.inc4 AS INC4, inc_captura.inc5 AS INC5, inc_captura.inc6 AS INC6 FROM prod_activos LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$la_semana' AND inc_captura.anio = '$anio'  WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDaplica_INC = 1 " . $c1 ;  
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_costos = "SELECT Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc6) AS INC6, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5 FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
$row_costos = mysql_fetch_assoc($costos);
$totalRows_costos = mysql_num_rows($costos);

$semana_anterior = $semana - 1;
mysql_select_db($database_vacantes, $vacantes);
$query_costos_a = "SELECT Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc6) AS INC6, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5 FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana_anterior' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos_a = mysql_query($query_costos_a, $vacantes) or die(mysql_error());
$row_costos_a = mysql_fetch_assoc($costos_a);
$totalRows_costos_a = mysql_num_rows($costos_a);
$este = $row_costos['INC3'] + $row_costos['INC6'];
$otro = $row_costos_a['INC3'] + $row_costos_a['INC6'];

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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte Histórico de Incidencias</h5>
						</div>

					<div class="panel-body">

                  <form method="POST" action="inc_cap.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                             <div class="col-lg-9">
							<strong>Semana: </strong>  <?php echo $la_semana; ?>
						     </div>
                            </td>
							<td>
                             <div class="col-lg-9">
                                 <select name="la_semana" class="form-control">
                                   <option value="" <?php if (!(strcmp("", $la_semana))) {echo "selected=\"selected\"";} ?>>Semana: Actual</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_semanas['semana']?>"<?php if (!(strcmp($row_semanas['semana'], $la_semana)))
                                   {echo "selected=\"selected\"";} ?>>Semana <?php echo $row_semanas['semana']; if ($row_semanas['semana'] == $la_semana) { echo " (actual)";}?></option>
                                   <?php
                                  } while ($row_semanas = mysql_fetch_assoc($semanas));
                                  $rows = mysql_num_rows($semanas);
                                  if($rows > 0) {
                                      mysql_data_seek($semanas, 0);
                                      $row_semanas = mysql_fetch_assoc($semanas);
                                  } ?> </select>
						     </div>
                            </td>
							<td>
                             <div class="col-lg-9">
                                 <select name="el_area" class="form-control">
                                   <option value="" <?php if (!(strcmp("", $el_area))) {echo "selected=\"selected\"";} ?>>Area: Todas</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_areas['IDarea']?>"<?php if (!(strcmp($row_areas['IDarea'], $el_area)))
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_areas['area']?></option>
                                   <?php
                                  } while ($row_areas = mysql_fetch_assoc($areas));
                                  $rows = mysql_num_rows($areas);
                                  if($rows > 0) {
                                      mysql_data_seek($areas, 0);
                                      $row_areas = mysql_fetch_assoc($areas);
                                  } ?> </select>
						     </div>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button> &nbsp;  
                            <a class="btn btn-warning" href="inc_reporte_Xglobal.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio; ?>">Descargar Reporte</a>										
                            <a class="btn btn-success" href="PXV/inc_reporte_semana_pxv.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio; ?>">Descargar Excel PxV</a>										
                            </td>
					      </tr>
					    </tbody>
				    </table>
				</form>



				<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>H.Extra</th>
                      <th>Suplencia</th>
                      <th>PxV</th>
                      <th>Incentivos</th>
                      <th>Festivos</th>
                      <th>Domingos</th>
                      <th>Total sin PXV</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php do { 
						
						$total_pago_ = $row_detalle['INC1'] + $row_detalle['INC2'] + $row_detalle['INC3'] + $row_detalle['INC4'] + $row_detalle['INC6'];
						$sueldo_semanal = $row_detalle['sueldo_diario'] * 7;
						if ($total_pago_ > 0) {$pago_porcentaje = round(($total_pago_ / $sueldo_semanal * 100),0);} else {$pago_porcentaje = 0;}

						?>
                        <tr>
                            <td><?php echo $row_detalle['IDempleado']; ?>&nbsp;  </td>
                            <td><a href="inc_detalle_empleado.php?IDempleado=<?php echo $row_detalle['IDempleado']?>">
							<?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?></a>&nbsp; </td>
                            <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
                            <td>
                            <?php if ($row_detalle['INC1'] != '') {?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>', 'a1')" class="btn btn-warning btn-icon">
                            <?php echo "$".$row_detalle['INC1']; ?></div>
                            <?php } else { ?>
                            <?php echo "$0.00"; ?>
                            <?php }?>
                             </td>                                           
                            <td>
                            <?php if ($row_detalle['INC2'] != '') {?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>', 'a2')" class="btn btn-warning btn-icon">
                            <?php echo "$".$row_detalle['INC2']; ?></div>
                            <?php } else { ?>
                            <?php echo "$0.00"; ?>
                            <?php }?>
                             </td>                                           
                            <td>
                            <?php if ($row_detalle['INC5'] != '') {?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>', 'a5')" class="btn btn-warning btn-icon">
                            <?php echo "$".$row_detalle['INC5']; ?></div>
                            <?php } else { ?>
                            <?php echo "$0.00"; ?>
                            <?php }?>
                             </td>                                           
                            <td>
                            <?php if ($row_detalle['INC3'] != '') {?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>', 'a3')" class="btn btn-warning btn-icon">
                            <?php echo "$".$row_detalle['INC3']; ?></div>
                            <?php } else { ?>
                            <?php echo "$0.00"; ?>
                            <?php }?>
                             </td>                                           
                            <td>
                            <?php if ($row_detalle['INC6'] != '') {?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>', 'a6')" class="btn btn-warning btn-icon">
                            <?php echo "$".$row_detalle['INC6']; ?></div>
                            <?php } else { ?>
                            <?php echo "$0.00"; ?>
                            <?php }?>
                             </td>                                           
                            <td>
                            <?php if ($row_detalle['INC4'] != '') {?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>', 'a4')" class="btn btn-warning btn-icon">
                            <?php echo "$".$row_detalle['INC4']; ?></div>
                            <?php } else { ?>
                            <?php echo "$0.00"; ?>
                            <?php }?>
                             </td>        
                            <td>
							<?php if($pago_porcentaje > 30){ ?><div class="text text-danger"><?php echo "$" . number_format($total_pago_); ?></div>
							<?php } else { ?>      
							<div><?php echo "$" . number_format($total_pago_); ?></div>
							<?php } ?>      
                            </td>        
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
               					  <h5 class="modal-title">Captura de Incidencias Semanales</h5>
								</div>
							<div class="modal-body">
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->

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
<script>
    var semana = <?php echo $semana; ?>;

function loadDynamicContentModal(modal, Tipo){
	var options = {
			modal: true
		};
	$('#conte-modal').load('incXT.php?Tipo=' + Tipo + '&IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>