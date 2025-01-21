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

$IDmatrizes = $row_usuario['IDmatrizes'];
$el_usuario = $row_usuario['IDusuario'];

//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizl = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)";
$matrizl = mysql_query($query_matrizl, $vacantes) or die(mysql_error());
$row_matrizl = mysql_fetch_assoc($matrizl);
$totalRows_matrizl = mysql_num_rows($matrizl);

//las variables de sesion para el filtrado
if (isset($_POST['la_matriz'])) {	foreach ($_POST['la_matriz'] as $matrizes)
	{	$_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);}	}  else { $_SESSION['la_matriz'] = $IDmatrizes;}
$la_matriz = $_SESSION['la_matriz'];

//las variables de sesion para el filtrado
if (isset($_POST['la_semana'])) {	$_SESSION['la_semana'] = $_POST['la_semana'];}  else { $_SESSION['la_semana'] = $semana;}
$la_semana = $_SESSION['la_semana'];

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_captura.bono_asistencia, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDempleado, prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.adicional, prod_captura.adicional2, prod_captura.adicional3, prod_captura.pago_resto, prod_captura.semana, prod_captura.capturador, prod_captura.observaciones, prod_captura.fecha_captura, prod_activos.denominacion, vac_matriz.matriz FROM prod_captura LEFT JOIN prod_activos ON prod_activos.IDempleado = prod_captura.IDempleado LEFT JOIN vac_matriz ON prod_captura.IDmatriz = vac_matriz.IDmatriz WHERE prod_activos.IDmatriz IN ($la_matriz) AND ( prod_captura.adicional3 > 0 OR prod_captura.adicional2 > 0 OR prod_captura.adicional > 0 ) AND prod_captura.semana = $la_semana AND prod_captura.anio = $anio";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$query_tipo_captura = "SELECT * FROM vac_puestos WHERE IDpuesto in (426, 281, 282, 313, 388, 9, 336, 18, 2)";
$tipo_captura = mysql_query($query_tipo_captura, $vacantes) or die(mysql_error());
$row_tipo_captura = mysql_fetch_assoc($tipo_captura);
$prod_captura_tipo = $row_tipo_captura['prod_captura_tipo'];

$query_semanas = "SELECT DISTINCT semana FROM prod_captura WHERE anio = $anio AND semana > 0";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);

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
	<!-- /theme JS files -->

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
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /Theme JS files -->
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
							<h6 class="panel-title">Consulta de Excedentes y Adicionales de Productividad.</h6></br>
						</div>

					<div class="panel-body"> 
                    <p>Selecciona el nombre del empleado para ver su histórico de pago de productividad.</p>
					<p><div class='label label-default'>V</div> = Empleados que eran de Villosa.</p>
					<p>&nbsp;</p>
					<p>&nbsp;</p>

										<form method="POST" action="productividad_autoriza_adicionales.php">
									<div class="row">
									<div class="col-xs-3 col-md-3">
												<select name="la_matriz[]" class="multiselect" multiple="multiple">
												  <?php $cadena2 = $la_matriz; $array = explode(", ", $cadena2);
												  do { ?>
												   <option value="<?php echo $row_matrizl['IDmatriz']?>"<?php foreach ($array as $lamatriz) { if (!(strcmp($row_matrizl['IDmatriz'], $lamatriz))) {echo "SELECTED";} } ?>>
												   <?php echo $row_matrizl['matriz']?></option>
													<?php
													} while ($row_matrizl = mysql_fetch_assoc($matrizl));
													  $rows = mysql_num_rows($matrizl);
													  if($rows > 0) {
														  mysql_data_seek($matrizl, 0);
														  $row_matrizl = mysql_fetch_assoc($matrizl);
													} ?>
												</select>
									</div>		
									<div class="col-xs-3 col-md-3">
												<select name="la_semana" class="form-control">
												  <?php do {  ?>
												   <option value="<?php echo $row_semanas['semana']?>" <?php if (!(strcmp($la_semana, $row_semanas['semana']))) {echo "SELECTED";} ?>> Semana 
												   <?php echo $row_semanas['semana']?></option>
													<?php
													} while ($row_semanas = mysql_fetch_assoc($semanas));
													  $rows = mysql_num_rows($semanas);
													  if($rows > 0) {
														  mysql_data_seek($matrizl, 0);
														  $row_semanas = mysql_fetch_assoc($semanas);
													} ?>
												</select>
									</div>		
									<div class="col-xs-3 col-md-3">
											 <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>
									</div>		
									</div>		
										</form>
					<p>&nbsp;</p>


					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>No. Emp.</th>
                          <th>Matriz</th>
                          <th>Empleado</th>
                          <th>Puesto</th>
                          <th>Sueldo Sem.</th>
                          <th>Pago ($)</th>
                          <th>Garant.</th>
                          <th>Asist.</th>
                          <th>Adicional ($)</th>
                          <th>Excedido ($)</th>
                          <th>Total ($)</th>
                          <th>Difer. (%)</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { 
						$semanal = (($row_puestos['sueldo_total_productividad'] /30 ) *7 );
						$total = $semanal + $row_puestos['pago_total'] + $row_puestos['adicional2'] + $row_puestos['adicional']  + $row_puestos['bono_asistencia'] + $row_puestos['adicional3']; 
						?>
                          <tr>
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><?php echo $row_puestos['matriz']; ?></td>
                            <td><a href="prod_empleado_detalle.php?IDempleado=<?php echo $row_puestos['IDempleado']; ?>">
							<?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></a></td>
                            <td><?php echo $row_puestos['denominacion']; ?></td>
                            <td><?php echo "$" . number_format($semanal); ?>
							<?php if ($row_puestos['sueldo_total_productividad'] != $row_puestos['sueldo_total'] AND $row_puestos['sueldo_total_productividad'] != 0) 	{ echo "<div class='label label-default'>V</div>"; } ?></td>
                            <td><?php if ($row_puestos['pago_total'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['pago_total']);} ?></td>
                            <td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
							<td><?php if ($row_puestos['bono_asistencia'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['bono_asistencia']);} ?></td>
                            <td><?php if (($row_puestos['adicional'] == 0 OR $row_puestos['adicional'] == '') AND ($row_puestos['adicional2'] == 0 OR $row_puestos['adicional2'] == ''))
									{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional'] + $row_puestos['adicional2']);} ?></td>
                            <td><?php if ($row_puestos['adicional3'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional3']);} ?></td>
                            <td><?php echo "$". number_format($total); ?></td>
                            <td>+<?php echo (round(($total / $semanal),2) * 100) -100; ?>%</td>
                           </tr>                         
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
                         <?php } else { ?>
                         <td colspan="10">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
				    </table>
                    
                   
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
</body>
</html>