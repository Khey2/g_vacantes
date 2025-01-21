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
if(isset($_GET['el_anio'])) { $anio = $_GET['el_anio'];} else {$anio = $row_variables['anio'];}
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

$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_puestro = "SELECT IDpuesto FROM vac_puestos WHERE IDaplica_PROD = 1 AND IDpuesto NOT IN (2,9,18) ORDER BY vac_puestos.denominacion ASC";
$puestro = mysql_query($query_puestro, $vacantes) or die(mysql_error());
$row_puestro = mysql_fetch_assoc($puestro);
$totalRows_puestro = mysql_num_rows($puestro);

// recorremos todos los puestos
$IDpuesto = "";
while ($row_puestro = mysql_fetch_assoc($puestro)) {
    $IDpuesto.= $row_puestro['IDpuesto'] . ", "; 
}
$IDpuesto.= "1";

//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

if(isset($_GET['la_semana'])) { $la_semana = $_GET['la_semana'];} else {$la_semana = $semana;}
//$la_matriz = $_GET['IDmatriz'];

//las variables de sesion para el filtrado
if(isset($_GET['IDmatriz'])) {
$_SESSION['la_matriz'] = $_GET['IDmatriz']; } 
else if(isset($_POST['la_matriz']) && ($_POST['la_matriz'] > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; } 
else { $_SESSION['la_matriz'] = $IDmatriz; }

//las variables de sesion para el filtrado
if(isset($_GET['IDpuesto'])) {
$_SESSION['el_puesto'] = $_GET['IDpuesto']; } 
else if(isset($_POST['el_puesto']) && ($_POST['el_puesto'] > 0)) {
$_SESSION['el_puesto'] = $_POST['el_puesto']; } 
else { $_SESSION['el_puesto'] = $IDpuesto; }

$IDarea = 1;
if(isset($_GET['IDarea'])) {
$_SESSION['el_area'] = $_GET['IDarea']; } 
else if(isset($_POST['el_area']) && ($_POST['el_area'] > 0)) {
$_SESSION['el_area'] = $_POST['el_area']; } 
else { $_SESSION['el_area'] = $IDarea; }

$la_matriz = $_SESSION['la_matriz'];
$el_area = $_SESSION['el_area'];
$el_puesto = $_SESSION['el_puesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_semanas = "SELECT * FROM prod_semanas";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);


mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.tipo, vac_puestos.IDaplica_PROD, vac_puestos.modal, prod_activos.IDpuesto, vac_matriz.IDmatriz FROM vac_puestos INNER JOIN prod_activos ON prod_activos.IDpuesto = vac_puestos.IDpuesto INNER JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE vac_puestos.IDaplica_PROD = 1 AND vac_puestos.IDpuesto NOT IN (2, 9, 18) AND vac_matriz.IDmatriz = '$la_matriz' ORDER BY vac_puestos.denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if(isset($_GET['el_anio']) && $_GET['el_anio'] == '2020') { 

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_POST['IDcaptura'];
	
  $updateSQL = sprintf("UPDATE prod_captura_2020 SET IDempleado=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, IDmatriz=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, autorizador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s WHERE IDcaptura=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['a1'], "text"),
                       GetSQLValueString($_POST['a2'], "text"),
                       GetSQLValueString($_POST['a3'], "text"),
                       GetSQLValueString($_POST['a4'], "text"),
                       GetSQLValueString($_POST['a5'], "text"),
                       GetSQLValueString($_POST['autorizador'], "text"),
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

  $updateGoTo = "productividad_autoriza_puesto_uptdate.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_activos.emp_paterno, prod_activos.IDempleado, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_total, prod_activos.sueldo_diario, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado, prod_activos.descripcion_nomina, prod_captura_2020.IDcaptura, prod_captura_2020.pago, prod_captura_2020.pago_total, prod_captura_2020.garantizado, prod_captura_2020.semana,  prod_captura_2020.lun, prod_captura_2020.mar, prod_captura_2020.mie, prod_captura_2020.jue, prod_captura_2020.vie, prod_captura_2020.sab, prod_captura_2020.dom, prod_captura_2020.observaciones, prod_captura_2020.a1, prod_captura_2020.a2, prod_captura_2020.a3, prod_captura_2020.a4, prod_captura_2020.a5, prod_captura_2020.adicional, prod_captura_2020.adicional2, prod_captura_2020.semana, prod_captura_2020.autorizador, prod_captura_2020.fecha_captura, vac_puestos.denominacion, vac_puestos.modal FROM prod_activos LEFT JOIN prod_captura_2020 ON prod_captura_2020.IDempleado = prod_activos.IDempleado AND prod_captura_2020.semana = '$la_semana' LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = '$la_matriz' AND prod_activos.IDpuesto IN ($el_puesto)"; 
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

} else {
	
// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_POST['IDcaptura'];
	
  $updateSQL = sprintf("UPDATE prod_captura SET IDempleado=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, IDmatriz=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, autorizador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s WHERE IDcaptura=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['a1'], "text"),
                       GetSQLValueString($_POST['a2'], "text"),
                       GetSQLValueString($_POST['a3'], "text"),
                       GetSQLValueString($_POST['a4'], "text"),
                       GetSQLValueString($_POST['a5'], "text"),
                       GetSQLValueString($_POST['autorizador'], "text"),
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

  $updateGoTo = "productividad_autoriza_puesto_uptdate.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_activos.emp_paterno, prod_activos.IDempleado, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_total, prod_activos.sueldo_diario, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado, prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.semana,  prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.observaciones, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.adicional, prod_captura.adicional2, prod_captura.semana, prod_captura.autorizador, prod_captura.fecha_captura, vac_puestos.denominacion, vac_puestos.modal FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$la_semana' AND prod_captura.anio = '$anio' LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = '$la_matriz' AND prod_activos.IDpuesto IN ($el_puesto)"; 
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);
	
}

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script><body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		<?php require_once('assets/pheader.php'); ?>
<!-- Content area -->
				<div class="content">
				
				        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 1)) { ?>
					    <div class="alert bg-success-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han <span class="text-semibold">Autorizado</span> los registros de forma correcta. 
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte Productividad</h5></br>
							
										<div class="form-group">
											<label class="control-label no-margin text-semibold">Sucursal:</label>
											<?php echo $row_lmatriz['matriz']; ?>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<?php echo $row_puestos['denominacion']; ?>
										</div>							
							
										<div class="form-group">
											<label class="control-label no-margin text-semibold">Semana:</label>
											<?php echo $semana; ?>
										</div>							
						</div>


						<div class="panel-body"> 
                    <p>Selecciona el empelado para validar su productividad.</p>
                    
                    
                    
                       <form method="GET" action="productividad_reporte_empleado.php">

					<table class="table">
						<tbody>							  
							<tr>
                            <td>
                             <select name="el_anio" class="form-control">
                               <option value="2020"<?php if (!(strcmp($anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
                               <option value="2021"<?php if (!(strcmp($anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
                               <option value="2022"<?php if (!(strcmp($anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
                               <option value="2023"<?php if (!(strcmp($anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
                               <option value="2024"<?php if (!(strcmp($anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
                               <option value="2025"<?php if (!(strcmp($anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
                             </select>
                            </td>
							<td> <div class="col-lg-9 no-prints">
										<select name="la_semana" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_semana))) {echo "selected=\"selected\"";} ?>>Semana</option>
                                          <?php do {  ?>
                                           <option value="<?php echo $row_semanas['IDsemana']?>"<?php if (!(strcmp($row_semanas['IDsemana'], $la_semana)))
										   {echo "selected=\"selected\"";} ?>><?php echo $row_semanas['semana']?></option>
											<?php
                                            } while ($row_semanas = mysql_fetch_assoc($semanas));
                                              $rows = mysql_num_rows($semanas);
                                              if($rows > 0) {
                                                  mysql_data_seek($semanas, 0);
                                                  $row_semanas = mysql_fetch_assoc($semanas);
                                              } ?></select>
										</div>
                                    </td>
							<td> <div class="col-lg-9 no-prints">
										<select name="la_matriz" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz: Todas</option>
                                          <?php do {  ?>
                                           <option value="<?php echo $row_matriz['IDmatriz']?>"<?php if (!(strcmp($row_matriz['IDmatriz'], $la_matriz)))
										   {echo "selected=\"selected\"";} ?>><?php echo $row_matriz['matriz']?></option>
											<?php
                                            } while ($row_matriz = mysql_fetch_assoc($matriz));
                                              $rows = mysql_num_rows($matriz);
                                              if($rows > 0) {
                                                  mysql_data_seek($matriz, 0);
                                                  $row_matriz = mysql_fetch_assoc($matriz);
                                              } ?></select>
										</div>
                                    </td>
							<td><div class="col-lg-9">
                                             <select name="el_puesto" class="form-control">
                                               <option value="" <?php if (!(strcmp("", $el_puesto))) {echo "selected=\"selected\"";} ?>>Puesto: Todos</option>
											<?php do { ?>
                                               <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $el_puesto))) {echo "selected=\"selected\"";} ?>><?php echo $row_puesto['denominacion']?></option>
                                               <?php
											  } while ($row_puesto = mysql_fetch_assoc($puesto));
											  $rows = mysql_num_rows($puesto);
											  if($rows > 0) {
												  mysql_data_seek($puesto, 0);
												  $row_puesto = mysql_fetch_assoc($puesto);
											  } ?> </select>
						    </div></td>
									<td>
                                <button type="submit" class="btn btn-success">Filtrar <i class="icon-filter3  position-right"></i></button>	
                            <button type="button" class="btn btn-info" onClick="window.location.href='productividad_reporte.php?IDmatriz=<?php echo $la_matriz; ?>&la_semana=<?php echo $semana; ?>'"><i class="icon-arrow-left52"></i> Regresar</button>
                            <button type="button" class="btn btn-info" onClick="window.location.href='productividad_reporte_empleado.php?IDmatriz=<?php echo $la_matriz; ?>&la_semana=<?php echo $semana; ?>'"><i class="icon-collaboration"></i> Otros Puestos</button>
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	
                    
                    
                    
                    
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-slate">
                          <th>Acciones</th>
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Puesto</th>
                          <th>Sueldo Semanal</th>
                          <th>Calculado (%)</th>
                          <th>Pago ($)</th>
                          <th>Garantizado</th>
                          <th>Adicional (%)</th>
                          <th>Adicional ($)</th>
                          <th>Total ($)</th>
                          <th>Autorizado</th>
                        </tr>
						</thead>
						<tbody>							  

                        <?php do { 
						$el_puesto = $row_puestos['IDpuesto'];?>
                          <tr>
                          <td>
                           <?php if ($row_puestos['IDcaptura'] == "") { ?>
							Sin captura
                           <?php } else { ?>
                          <button type="button" data-target="#modal_form_inline<?php echo $row_puestos['IDcaptura']; ?>"  data-toggle="modal" class="btn btn-primary btn-icon">Ver Detalles</button>
                           <?php } ?>
                           </td>  
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><a href="prod_empleado_detalle.php?IDempleado=<?php echo $row_puestos['IDempleado']; ?>">
							<?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></a></td>
                            <td><?php echo $row_puestos['denominacion']; ?></td>
                            <td><?php echo  "$" .number_format(($row_puestos['sueldo_total'] / 30) * 7); ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo $row_puestos['pago']. "%";} ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo   "$" .number_format($row_puestos['pago_total']);} ?></td>
                            <td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo $row_puestos['adicional'] . "%";} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo   "$" .number_format($row_puestos['adicional2']);} ?></td>
                            <td><?php $total = $row_puestos['pago_total'] + $row_puestos['adicional2']; echo   "$" .number_format($total); ?></td>
                            <td><?php if ($row_puestos['autorizador'] != 0) 	{ echo "Si"; } else { echo   "No"; } ?></td>
                           </tr>
                            <?php // agregamos el modal especifico
                           		  $modal = "assets/modals/100000.php";
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
