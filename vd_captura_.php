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
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_POST['IDcaptura'];
if ($_POST['a1'] == ''){$_a1 = 0;} else {$_a1 = $_POST['a1'];}
if ($_POST['a28'] == ''){$_a28 = 0;} else {$_a28 = $_POST['a28'];}

  $updateSQL = sprintf("UPDATE prod_captura SET IDempleado=%s, emp_paterno=%s,  emp_materno=%s,  emp_nombre=%s, denominacion=%s, sueldo_total=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, IDmatriz=%s, IDsucursal=%s,  IDarea=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, a6=%s, a7=%s, a25=%s, a26=%s, a27=%s, a28=%s, capturador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s WHERE IDcaptura=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "productividad_captura_puesto_uptdate.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
if (!isset($_POST['a1'])) {$_a1 = 0;} else {$_a1 = $_POST['a1'];}
if (!isset($_POST['a2'])) {$_a2 = 0;} else {$_a2 = $_POST['a2'];}
if (!isset($_POST['a28'])) {$_a28 = 0;} else {$_a28 = $_POST['a28'];}
if (!isset($_POST['adicional'])) {$_adicional = 0;} else {$_adicional = $_POST['adicional'];}
if (!isset($_POST['observaciones'])) {$_observaciones = 0;} else {$_observaciones = $_POST['observaciones'];}
	
  $insertSQL = sprintf("INSERT INTO prod_captura (IDempleado, emp_paterno, emp_materno, emp_nombre, denominacion, sueldo_total, IDpuesto, fecha_captura, semana, IDmatriz, IDsucursal, IDarea, a1, a2, a3, a4, a5, a6, a7, a25, a26, a27, a28, capturador, garantizado, adicional, observaciones, lun, mar, mie, jue, vie, sab, dom) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "text"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString(isset($_POST['jue']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "productividad_captura_puesto_uptdate.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


//filtrado por sucursal
if(isset($_SESSION['la_sucursal']) && $_SESSION['la_sucursal'] > 0) { $la_sucursal = $_SESSION['la_sucursal']; }  else {$la_sucursal = 0;}

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT com_vd.capturador, com_vd.validador, com_vd.autorizador, com_vd.IDvd, com_vd.IDmatriz, com_vd.IDempleadoS, com_vd.IDempleado, com_vd.Clave, com_vd.VentaNeta, com_vd.VentaNetaCajas, com_vd.VentaNetaPieza, com_vd.ClientesVenta, com_vd.NoPedidos, com_vd.Visitas, com_vd.DevImporte, com_vd.DevPorc, com_vd.Presupuesto, com_vd.Cubrimiento, com_vd.MargenBruto, com_vd.IDsemana, com_vd.BonoTransporte, com_vd.BonoProductividad, com_vd.Premios, com_vd.Comisiones, com_vd.Observaciones, vac_matriz.matriz, Empleados.IDempleado, Empleados.emp_paterno AS emp_paterno, Empleados.emp_materno AS emp_materno, Empleados.emp_nombre AS emp_nombre, Empleados.denominacion AS emp_denominacion, Empleados.IDpuesto AS emp_IDpuesto, Jefes.IDempleado AS jefe_IDempleado, Jefes.emp_paterno AS jefe_paterno, Jefes.emp_materno AS jefe_materno, Jefes.emp_nombre AS jefe_nombre, Jefes.denominacion AS jefe_denominacion, Jefes.IDpuesto  AS jefe_IDpuesto FROM com_vd LEFT JOIN prod_activos AS Empleados ON com_vd.IDempleado = Empleados.IDempleado LEFT JOIN prod_activos AS Jefes ON com_vd.IDempleadoS = Jefes.IDempleado LEFT JOIN vac_matriz ON com_vd.IDmatriz = vac_matriz.IDmatriz WHERE IDmes = $el_mes AND anio = $anio";mysql_query("SET NAMES 'utf8'");
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
							<h5 class="panel-title">Validación de Comisiones Ventas a Detalle. </h5>
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
                    <p>Instrucciones. Selecciona el concepto a capturar.</p>

                    <p>
					<ul>
					<li>Mes:</li>
                    <li>Cantidad de Empleados:</li>
					</ul>
					</p>



									<a class="collapsed" data-toggle="collapse" href="#collapse-group2">Collapsible Item #2</a>
									<div id="collapse-group2" class="panel-collapse collapse">
											Тon cupidatat skateboard dolor brunch. Тesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda.
									</div>






					<div class="table-responsive">
					<table class="table">
						<thead>
						  <tr class="bg-blue">
                          <th>Acciones</th>
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Venta Neta</th>
                          <th>Venta Neta Cajas</th>
                          <th>Venta Neta Piezas</th>
                          <th>Clientes con Venta</th>
                          <th>Número de Pedidos</th>
                          <th>Visitas</th>
                          <th>Dev$</th>
                          <th>Dev%</th>
                          <th>Presup</th>
                          <th>Cubr%</th>
                          <th>Margen Bruto</th>
                          <th>Bono Tra</th>
                          <th>Bono Prod</th>
                          <th>Premios</th>
                          <th>Comis</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { ?>
                          <tr>
                          <td>
                           <?php if ($row_puestos['capturador'] == "") { ?>
                         <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-success btn-icon">Validar</div>
                        <?php } else { ?>
                         <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-primary btn-icon">Actualizar</div>
                        <?php } ?>
                          </td>  
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><a href="comvd_detalle.php?IDempleado=<?php echo $row_puestos['IDempleado']; ?>"><?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?>
							<?php echo $row_puestos['emp_nombre']; ?></a></td>
                            <td><?php echo "$" . number_format($row_puestos['VentaNeta']); ?></td>
                            <td><?php echo "$" . number_format($row_puestos['VentaNetaCajas']); ?></td>
                            <td><?php echo "$" . number_format($row_puestos['VentaNetaPieza']); ?></td>
                            <td><?php echo $row_puestos['ClientesVenta']; ?></td>
                            <td><?php echo $row_puestos['NoPedidos']; ?></td>
                            <td><?php echo $row_puestos['Visitas']; ?></td>
                            <td><?php echo "$" . number_format($row_puestos['DevImporte']); ?></td>
                            <td><?php echo $row_puestos['DevPorc']; ?>%</td>
                            <td><?php echo "$" . number_format($row_puestos['Presupuesto']); ?></td>
                            <td><?php echo $row_puestos['Cubrimiento']; ?>%</td>
                            <td><?php echo $row_puestos['MargenBruto']; ?>%</td>
							<td><?php echo $row_puestos['BonoTransporte']; ?></td>
                            <td><?php echo $row_puestos['BonoProductividad']; ?></td>
                            <td><?php echo $row_puestos['Premios']; ?></td>
                            <td><?php echo $row_puestos['Comisiones']; ?></td>
                           </tr>                         
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
                         <?php } else { ?>
                         <td colspan="17">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
					    </tbody>
				    </table>
				</div>                   
                   
                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Captura de indicadores de productividad</h5>
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
    var IDpuesto = <?php echo $el_puesto; ?>;
    var IDmatriz = <?php echo $IDmatriz; ?>;
    var semana = <?php echo $semana; ?>;

function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('productividad_captura_puesto_mdl.php?IDpuesto=' + IDpuesto + '&semana=' + semana + '&IDmatriz=' + IDmatriz + '&IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>