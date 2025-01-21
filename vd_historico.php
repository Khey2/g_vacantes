<?php require_once('Connections/vacantes.php'); ?>
<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m") -1;
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar bono transporte
if ((isset($_POST["IDtipo"])) AND ($_POST["IDtipo"] == "a1")) {
$updateSQL = sprintf("UPDATE com_vd SET bt_01=%s, bt_02=%s,  bt_03=%s,  bt_04=%s, bt_05=%s, bt_adicional=%s, bt_observaciones=%s, bt_capturador=%s, bt_fecha_captura=%s WHERE IDvd=%s",
                       GetSQLValueString($_POST['bt_01'], "text"),
                       GetSQLValueString($_POST['bt_02'], "text"),
                       GetSQLValueString($_POST['bt_03'], "text"),
                       GetSQLValueString($_POST['bt_04'], "text"),
                       GetSQLValueString($_POST['bt_05'], "text"),
                       GetSQLValueString($_POST['bt_adicional'], "text"),
                       GetSQLValueString($_POST['bt_observaciones'], "text"),
                       GetSQLValueString($_POST['bt_capturador'], "text"),
                       GetSQLValueString($_POST['bt_fecha_captura'], "text"),
                       GetSQLValueString($_POST['IDvd'], "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location: vd_captura.php?info=1");
}


//filtrado por sucursal
if(isset($_SESSION['la_sucursal']) && $_SESSION['la_sucursal'] > 0) { $la_sucursal = $_SESSION['la_sucursal']; }  else {$la_sucursal = 0;}

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT com_vd.IDvd, com_vd.IDmatriz, com_vd.IDempleadoS, com_vd.IDempleado, com_vd.Clave, com_vd.VentaNeta, com_vd.VentaNetaCajas, com_vd.VentaNetaPieza, com_vd.ClientesVenta, com_vd.NoPedidos, com_vd.Visitas, com_vd.DevImporte, com_vd.DevPorc, com_vd.Presupuesto, com_vd.Cubrimiento, com_vd.MargenBruto, com_vd.IDsemana, com_vd.bt_01, com_vd.bt_02, com_vd.bt_03, com_vd.bt_04, com_vd.bt_05, com_vd.bt_garantizado, com_vd.bt_adicional,  com_vd.bt_observaciones, com_vd.bt_capturador, com_vd.bt_fecha_captura, com_vd.BonoProductividad, com_vd.Premios, com_vd.Comisiones, vac_matriz.matriz, Empleados.IDempleado, Empleados.emp_paterno AS emp_paterno, Empleados.emp_materno AS emp_materno, Empleados.emp_nombre AS emp_nombre, Empleados.denominacion AS emp_denominacion, Empleados.IDpuesto AS emp_IDpuesto, Jefes.IDempleado AS jefe_IDempleado, Jefes.emp_paterno AS jefe_paterno, Jefes.emp_materno AS jefe_materno, Jefes.emp_nombre AS jefe_nombre, Jefes.denominacion AS jefe_denominacion, Jefes.IDpuesto  AS jefe_IDpuesto FROM com_vd LEFT JOIN prod_activos AS Empleados ON com_vd.IDempleado = Empleados.IDempleado LEFT JOIN prod_activos AS Jefes ON com_vd.IDempleadoS = Jefes.IDempleado LEFT JOIN vac_matriz ON com_vd.IDmatriz = vac_matriz.IDmatriz WHERE IDmes = 11 AND anio = $anio";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);
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
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>
	<script src="global_assets/js/demo_pages/components_navs.js"></script>	<!-- /Theme JS files -->
    

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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Comisiones - Ventas a Detalle.</h5>
                         </div>   

                        <!-- Basic alert -->
                        <?php if(1 == 2) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							La productividad de éste puesto, se reporta desde Corporativo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<div class="panel-body"> 
                    <p>Instrucciones. <br/>
					Selecciona el concepto a capturar.<br/>
					Debes capturar justificación en cada caso.</p>
                    <p>
					<ul>
					<li><strong>Mes:</strong> <?php echo $el_mes; ?>.</li>
                    <li><strong>Cantidad de Empleados: </strong> 41.</li>
					</ul>
					</p>
					<p>&nbsp;</p>


					<div class="table-responsive">
					<table class="table">
						<thead>
						  <tr class="bg-indigo-600">
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Clave</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
                          <th>Bono Transporte</th>
                          <th>Bono Productividad</th>
                          <th>Premios</th>
                          <th>Comisiones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { ?>
                          <tr>
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDempleado']; ?>"><?php echo $row_puestos['emp_paterno']." ".$row_puestos['emp_materno']." ".$row_puestos['emp_nombre']; ?>
							<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDempleado']; ?>" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul>
								<li><strong>Venta Neta: </strong><?php echo "$" . number_format($row_puestos['VentaNeta']); ?></li>
								<li><strong>Venta Neta Cajas: </strong><?php echo "$" . number_format($row_puestos['VentaNetaCajas']); ?></li>
								<li><strong>Venta Neta Piezas: </strong><?php echo "$" . number_format($row_puestos['VentaNetaPieza']); ?></li>
								<li><strong>Clientes con Venta: </strong><?php echo $row_puestos['ClientesVenta']; ?></li>
								<li><strong>Número de Pedidos: </strong><?php echo $row_puestos['NoPedidos']; ?></li>
								<li><strong>Visitas: </strong><?php echo $row_puestos['Visitas']; ?></li>
								<li><strong>Devoluciones $: </strong><?php echo "$" . number_format($row_puestos['DevImporte']); ?></li>
								<li><strong>Devoluciones %: </strong><?php echo round($row_puestos['DevPorc'] * 100, 2) ."%"; ?></li>
								<li><strong>Presupuesto: </strong><?php echo "$" . number_format($row_puestos['Presupuesto']); ?></li>
								<li><strong>Cubrimiento %: </strong><?php echo round($row_puestos['Cubrimiento'] * 100, 2) ."%"; ?></li>
								<li><strong>Margen Bruto: </strong><?php echo round($row_puestos['MargenBruto'] * 100, 2) ."%"; ?></li>
								</ul>
							</div>
							</td>
                            <td><?php echo $row_puestos['Clave']; ?></td>
                            <td><?php echo $row_puestos['emp_denominacion']; ?></td>
                            <td><?php echo $row_puestos['matriz']; ?></td>
							
							<?php $monto_transporte = $row_puestos['bt_01'] + $row_puestos['bt_02'] + $row_puestos['bt_03'] + $row_puestos['bt_04'] + $row_puestos['bt_05']; ?>
							
							<td><div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>', 'a1')" class="btn bg-indigo-600">$<?php echo $monto_transporte; ?></div></td>
							<td><div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>', 'a2')" class="btn bg-indigo-600">$<?php echo $row_puestos['BonoProductividad']; ?></div></td>
							<td><div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>', 'a3')" class="btn bg-indigo-600">$<?php echo $row_puestos['Premios']; ?></div></td>
							<td><div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>', 'a4')" class="btn bg-indigo-600">$<?php echo $row_puestos['Comisiones']; ?></div></td>
                           </tr>                         
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
                         <?php } else { ?>
                         <td colspan="6">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
					    </tbody>
				    </table>
				</div>                   
                   
                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-indigo-600">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Captura - Ventas a Detalle</h5>
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

function loadDynamicContentModal(modal, tipo){
	var options = {
			modal: true
		};
	$('#conte-modal').load('vd_captura_mdl.php?tipo=' + tipo + '&IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>