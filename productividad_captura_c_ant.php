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

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_POST['IDcaptura'];
	
  $updateSQL = sprintf("UPDATE prod_captura SET IDempleado=%s, emp_paterno=%s,  emp_materno=%s,  emp_nombre=%s, denominacion=%s, sueldo_total=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, anio=%s, IDmatriz=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, a6=%s, a7=%s, capturador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s WHERE IDcaptura=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['a1'], "text"),
                       GetSQLValueString($_POST['a2'], "text"),
                       GetSQLValueString($_POST['a3'], "text"),
                       GetSQLValueString($_POST['a4'], "text"),
                       GetSQLValueString($_POST['a5'], "text"),
                       GetSQLValueString($_POST['a6'], "text"),
                       GetSQLValueString($_POST['a7'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['garantizado'], "int"),
                       GetSQLValueString($_POST['adicional'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString(isset($_POST['lun']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "productividad_captura_puesto_uptdate_c.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
   $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO prod_captura (IDempleado, emp_paterno, emp_materno, emp_nombre, denominacion, sueldo_total, IDpuesto, fecha_captura, semana, anio, IDmatriz, a1, a2, a3, a4, a5, a6, a7, capturador, garantizado, adicional, observaciones, lun, mar, mie, jue, vie, sab, dom) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['a1'], "text"),
                       GetSQLValueString($_POST['a2'], "text"),
                       GetSQLValueString($_POST['a3'], "text"),
                       GetSQLValueString($_POST['a4'], "text"),
                       GetSQLValueString($_POST['a5'], "text"),
                       GetSQLValueString($_POST['a6'], "text"),
                       GetSQLValueString($_POST['a7'], "text"),
                       GetSQLValueString($_POST['capturador'], "text"),
                       GetSQLValueString($_POST['garantizado'], "int"),
                       GetSQLValueString($_POST['adicional'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString(isset($_POST['lun']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "productividad_captura_puesto_uptdate_c.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
 header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (27,7)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
//filtrado por sucursal

if (isset($_POST['la_matriz'])) {	foreach ($_POST['la_matriz'] as $matris)
	{	$_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);}	}  else { $_SESSION['la_matriz'] = $IDmatriz;}
$la_matriz = $_SESSION['la_matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado,  prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.adicional, prod_captura.adicional2, prod_captura.semana, prod_captura.capturador, prod_captura.observaciones, prod_captura.fecha_captura, vac_puestos.denominacion, vac_puestos.modal, vac_matriz.matriz FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio' LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE vac_puestos.prod_captura_tipo = 2 AND prod_activos.IDmatriz IN ($la_matriz)";
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
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect4.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
	
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
							<h5 class="panel-title">Captura de Productividad - Coroporativo. </h5></br>
                        </div>

					<div class="panel-body"> 
                    <p>Selecciona el nombre del empelados para ver su histórico de pago de productividad. Da clic en el botón para capturar su productividad.<br/>
                    Utiliza el filtro para mostrar resultados por Sucursal.</p>

                    <p><a class="btn btn-warning" href="productividad_captura_c2.php">Captura por Puesto</a></p>
                    

 				<form method="POST" action="productividad_captura_c.php">
                	<table class="table">
						<tbody>							  
							<tr>
							<td>
                     <div class="col-lg-12">
                             <select class="multiselect" multiple="multiple" name="la_matriz[]">
                            <?php do { ?>
                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                               <?php
                              } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                              $rows = mysql_num_rows($lmatriz);
                              if($rows > 0) {
                                  mysql_data_seek($lmatriz, 0);
                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
                              } ?> 
                              </select>
                      </div>
                            </td>
							<td>
                          <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
							<td>
                             </tr>
					    </tbody>
				    </table>
				</form>
                    
                    
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>Acciones</th>
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Sueldo Semanal</th>
                          <th>Matriz</th>
                          <th>Puesto</th>
                          <th>Calculado (%)</th>
                          <th>Pago ($)</th>
                          <th>Garantizado</th>
                          <th>Adicional (%)</th>
                          <th>Adicional ($)</th>
                          <th>Total ($)</th>
                        </tr>
						</thead>
						<tbody>							  

                        <?php do { 
						$el_puesto =  $row_puestos['IDpuesto']; ?>
                          <tr>
                          <td>
                           <?php if ($row_puestos['IDcaptura'] == "") { ?>
                          <button type="button" data-target="#modal_form_inline<?php echo $row_puestos['IDempleado']; ?>"  data-toggle="modal" class="btn btn-success btn-icon"><i class="icon-arrow-right6"></i> Capturar</button>
						   <?php } else {  ?>  
                          <button type="button" data-target="#modal_form_inline<?php echo $row_puestos['IDempleado']; ?>"  data-toggle="modal" class="btn btn-primary btn-icon"><i class="icon-arrow-right6"></i> Actualizar</button>
                           <?php } ?>
                           </td>  
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><a href="prod_empleado_detalle.php?IDempleado=<?php echo $row_puestos['IDempleado']; ?>">
							<?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></a></td>
                            <td><?php echo "$" . number_format(($row_puestos['sueldo_total'] / 30) * 7); ?></td>
                            <td><?php echo $row_puestos['matriz']; ?></td>
                            <td><?php echo $row_puestos['denominacion']; ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo $row_puestos['pago']. "%";} ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['pago_total']);} ?></td>
                            <td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo $row_puestos['adicional'] . "%";} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional2']);} ?></td>
                            <td><?php $total = $row_puestos['pago_total'] + $row_puestos['adicional2']; echo  number_format($total); ?></td>
                           </tr>
                            <?php // agregamos el modal especifico
                           		  $modal = "assets/modals/" . $row_puestos['modal'] . "c.php";
								  require($modal); ?>

                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
					    </tbody>
				    </table>
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
<?php
mysql_free_result($variables);

mysql_free_result($puestos);
?>
