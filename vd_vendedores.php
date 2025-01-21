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
$mi_fecha =  date('Y/m/d');


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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$IDmes = $el_mes = date("m") - 1;
set_time_limit(0);

$query_amatriz = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

if (isset($_POST['el_mes'])) {$_SESSION['el_mes'] =  $_POST['el_mes'];} 
if (!isset($_SESSION['el_mes'])) {$_SESSION['el_mes'] =  date("m")-1;} 
$el_mes =  $_SESSION['el_mes']; 

$meses = array("1" => "enero", "2" => "febrero", "3" => "marzo", "4" => "abril", "5" => "mayo", "6" => "junio", "7" => "julio", "8" => "agosto", "9" => "septiembre", "10" => "octubre", "11" => "noviembre", "12" => "diciembre");

$mes_actual = $meses[$el_mes];

if (isset($_POST['el_anio'])) {$anio =  $_POST['el_anio'];} 

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT com_vd.*, vac_matriz.matriz, Jefes.IDempleado AS jefe_IDempleado, Jefes.emp_paterno AS jefe_paterno, Jefes.emp_materno AS jefe_materno, Jefes.emp_nombre AS jefe_nombre, Jefes.denominacion AS jefe_denominacion, Jefes.IDpuesto  AS jefe_IDpuesto FROM com_vd LEFT JOIN prod_activos AS Empleados ON com_vd.IDempleado = Empleados.IDempleado LEFT JOIN prod_activos AS Jefes ON com_vd.IDempleadoS = Jefes.IDempleado LEFT JOIN vac_matriz ON com_vd.IDmatriz = vac_matriz.IDmatriz WHERE com_vd.IDmes = '$el_mes' AND anio = '$anio'";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$query_meses = "SELECT * FROM vac_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);


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
							Se ha agregado correctamente el ajuste.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 6))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el empleado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Cálculo de Comisiones Ventas a Detalle</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">
									Instrucciones.
									<ul>
									<li>Utiliza el filtro para filtrar por mes y por año.</li>
									<li>Da clic en el nombre del empleado para ver sus resultados del mes.</li>
									<li>Mes actual <b><?php echo $mes_actual; ?></b>.</li>
									<li>Para ajustar el Bono de Transporte, de Productividad, Premios y Comsisiones, da clic en <b>"Ajustes"</b>. Los criterios ajustados se muestran con un <span class="text text-danger">*</span>.</li>
									<li>Para acutalizar los datos del empleado asignado a la ruta, da clic en <b>"Actualizar"</b>. Los Empleados actualizados se muestran con un <span class="text text-danger">*</span>.</li>
									<li>Puedes descargar el reporte detallado del mes dando clic en <b>"Descargar Rerporte"</b>.</li>
									</ul>
						</div>
						</div>

							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Resultado</h6>
								</div>
							
								<div class="panel-body">

                    <form method="POST" action="vd_vendedores.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td><div class="col-lg-6">
								<select name="el_mes"  class="form-control" >
								<?php do { ?>
								   <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $el_mes))) 
								   {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
								   <?php
								  } while ($row_meses = mysql_fetch_assoc($meses));
								  $rows = mysql_num_rows($meses);
								  if($rows > 0) {
									mysql_data_seek($meses, 0);
									$row_meses = mysql_fetch_assoc($meses); } ?> 
								</select>
						    </div>
							</td>
							<td>
                            <div class="col-lg-6">
								<select name="el_anio" class="form-control" >
								<option value="2025"<?php if (!(strcmp(2025, $anio))) {echo "selected=\"selected\"";} ?>>2025</option>
								<option value="2024"<?php if (!(strcmp(2024, $anio))) {echo "selected=\"selected\"";} ?>>2024</option>
								<option value="2023"<?php if (!(strcmp(2023, $anio))) {echo "selected=\"selected\"";} ?>>2023</option>
								<option value="2022"<?php if (!(strcmp(2022, $anio))) {echo "selected=\"selected\"";} ?>>2022</option>
								</select>
						    </div>
                            </td>
							<td>
							<button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>
							<button type="button" onClick="window.location.href='vd_reporte.php?mes=<?php echo $row_puestos['IDmes'] ?>&anio=<?php echo $anio; ?>'" class="btn btn-success">Reporte RV y Sups</button>
							<button type="button" onClick="window.location.href='vd_reporte2.php?mes=<?php echo $row_puestos['IDmes'] ?>&anio=<?php echo $anio; ?>'" class="btn btn-success">Reporte otros puestos</button>
                             </td>
					      </tr>
					    </tbody>
				    </table>
					</form>

					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-indigo-600">
                          <th>Acciones</th>
                          <th>No. Emp.</th>
                          <th>No. Sup.</th>
                          <th>Garant.</th>
                          <th>Empleado</th>
                          <th>Mes</th>
						  <th>Fecha Ant.</th>
                          <th>Clave</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
                          <th>Bono Transporte</th>
                          <th>Bono Productividad</th>
                          <th>Premios&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                          <th>Comisiones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { $nombre = $row_puestos['emp_paterno']." ".$row_puestos['emp_materno']." ".$row_puestos['emp_nombre']; ?>
                          <tr>
							</td>
							 <td>
							<button type="button" class="btn btn-info btn-icon" onClick="window.location.href='vd_vendedores_update.php?IDvd=<?php echo $row_puestos['IDvd']; ?>'">Actualizar</button>
							<button type="button" class="btn btn-primary btn-icon" onClick="window.location.href='vd_vendedores_edit.php?IDvd=<?php echo $row_puestos['IDvd']; ?>'">Ajustes</button>
							</td>
                            <td><?php if($row_puestos['IDempleado'] != 0) {echo $row_puestos['IDempleado']; } else {echo "-";} ?></td>
                            <td><?php if($row_puestos['IDempleadoS'] != 0) {echo $row_puestos['IDempleadoS']; } else {echo "-";} ?></td>
                            <td><?php if($row_puestos['IDgarantizado'] == 1) {echo "SI"; } else {echo "NO";} ?></td>
                            <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>"><?php if($row_puestos['actualizado'] == 1) {echo "<span class='text-danger'>*</span>"; } ?> <?php if($row_puestos['IDempleado'] != 0) {echo $nombre; } else {echo "VACANTE";} ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul>
								<li><strong>Venta Neta: </strong><?php echo "$" . number_format($row_puestos['VentaNeta']); ?>&nbsp;</li>
								<li><strong>Venta Neta Cajas: </strong><?php echo "$" . number_format($row_puestos['VentaNetaCajas']); ?>&nbsp;</li>
								<li><strong>Venta Neta Piezas: </strong><?php echo "$" . number_format($row_puestos['VentaNetaPieza']); ?>&nbsp;</li>
								<li><strong>Clientes con Venta: </strong><?php echo $row_puestos['ClientesVenta']; ?>&nbsp;</li>
								<li><strong>Número de Pedidos: </strong><?php echo $row_puestos['NoPedidos']; ?>&nbsp;</li>
								<li><strong>Visitas: </strong><?php echo $row_puestos['Visitas']; ?>&nbsp;</li>
								<li><strong>Devoluciones $: </strong><?php echo "$" . number_format($row_puestos['DevImporte']); ?>&nbsp;</li>
								<li><strong>Devoluciones %: </strong><?php echo round($row_puestos['DevPorc'] * 100, 2) ."%"; ?>&nbsp;</li>
								<li><strong>Presupuesto: </strong><?php echo "$" . number_format($row_puestos['Presupuesto']); ?>&nbsp;</li>
								<li><strong>Cubrimiento %: </strong><?php echo round($row_puestos['Cubrimiento'] * 100, 2) ."%"; ?>&nbsp;</li>
								<li><strong>Margen Bruto: </strong><?php echo round($row_puestos['MargenBruto'] * 100, 2) ."%"; ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							<td><?php 
								$le_mes = $row_puestos['IDmes'];
								$query_mesess = "SELECT * FROM vac_meses WHERE IDmes = '$le_mes'";
								$mesess = mysql_query($query_mesess, $vacantes) or die(mysql_error());
								$row_mesess = mysql_fetch_assoc($mesess);
								$totalRows_mesess = mysql_num_rows($mesess);
								echo $row_mesess['mes']; ?></td>
							<td><?php echo date('d/m/Y', strtotime($row_puestos['fecha_antiguedad'])); ?></td>
                            <td><?php echo $row_puestos['Clave']; ?></td>
                            <td><?php if($row_puestos['IDempleado'] != 0) {echo $row_puestos['denominacion']; } else {echo "VACANTE";} ?></td>
                            <td><?php echo $row_puestos['matriz']; ?></td>
							 <td>
							<?php $monto_transporte = $row_puestos['bt_01'] + $row_puestos['bt_02'] + $row_puestos['bt_03'] + $row_puestos['bt_04'] + $row_puestos['bt_05'] + $row_puestos['bt_01_ad'] + $row_puestos['bt_02_ad'] + $row_puestos['bt_03_ad'] + $row_puestos['bt_04_ad'] + $row_puestos['bt_05_ad']; ?>
							<?php $extra_transporte = $row_puestos['bt_01_ad'] + $row_puestos['bt_02_ad'] + $row_puestos['bt_03_ad'] + $row_puestos['bt_04_ad'] + $row_puestos['bt_05_ad'];
							if ( $extra_transporte != 0) {echo "<span class='text text-danger'>*</span>";} ?>
						  <a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>BT"><?php echo "$" . number_format($monto_transporte); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>BT" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_01_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_01'] + $row_puestos['bt_01_ad']); ?>&nbsp;</li>
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_02_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_02'] + $row_puestos['bt_02_ad']); ?>&nbsp;</li>
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_03_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_03'] + $row_puestos['bt_03_ad']); ?>&nbsp;</li>
								<li><strong><?php if ($row_puestos['bt_04_fecha'] != '') { echo date( 'd/m/Y', strtotime($row_puestos['bt_04_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_04'] + $row_puestos['bt_04_ad']); }?>&nbsp;</li>
								<li><strong><?php if ($row_puestos['bt_05_fecha'] != '') { echo date( 'd/m/Y', strtotime($row_puestos['bt_05_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_05'] + $row_puestos['bt_05_ad']); } ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<?php $extra_productividad = $row_puestos['BonoClientesVenta_ad'] + $row_puestos['BonoVentaNeta_ad'] + $row_puestos['BonoDevPorc_ad'];
							if ( $extra_productividad != 0) {echo "<span class='text text-danger'>*</span>";} ?>
						  <a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>BP"><?php echo "$" . number_format($row_puestos['BonoProductividad'] + $row_puestos['BonoClientesVenta_ad'] +$row_puestos['BonoVentaNeta_ad'] + $row_puestos['BonoDevPorc_ad']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>BP" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Venta Neta : </strong><?php echo "$" . number_format($row_puestos['BonoVentaNeta'] + $row_puestos['BonoVentaNeta_ad']); ?>&nbsp;</li>
								<li><strong>Clientes con Venta : </strong><?php echo "$" . number_format($row_puestos['BonoClientesVenta'] + $row_puestos['BonoClientesVenta_ad']); ?>&nbsp;</li>
								<li><strong>Devoluciones : </strong><?php echo "$" . number_format($row_puestos['BonoDevPorc'] + $row_puestos['BonoDevPorc_ad']); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<?php $extra_premios = $row_puestos['Premio_1_ad'] + $row_puestos['Premio_2_ad'];
							if ( $extra_premios != 0) {echo "<span class='text text-danger'>*</span>";} ?>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>PR"><?php echo "$" . number_format($row_puestos['Premios'] + $row_puestos['Premio_1_ad'] + $row_puestos['Premio_2_ad']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>PR" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Venta Cajas : </strong><?php echo "$" . number_format($row_puestos['Premio_1'] + $row_puestos['Premio_1_ad']); ?>&nbsp;</li>
								<li><strong>Venta Neta : </strong><?php echo "$" . number_format($row_puestos['Premio_2'] + $row_puestos['Premio_2_ad']); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<?php $extra_comisiones = $row_puestos['Comisiones_pieza_ad'] + $row_puestos['Comisiones_caja_ad'];
							if ( $extra_comisiones != 0) {echo "<span class='text text-danger'>*</span>";} ?>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>CO"><?php echo "$" . number_format($row_puestos['Comisiones'] + $row_puestos['Comisiones_pieza_ad'] + $row_puestos['Comisiones_caja_ad']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>CO" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Pieza: </strong><?php echo "$" . number_format($row_puestos['Comisiones_pieza'] + $row_puestos['Comisiones_pieza_ad']); ?>&nbsp;</li>
								<li><strong>Caja : </strong><?php echo "$" . number_format($row_puestos['Comisiones_caja'] + $row_puestos['Comisiones_caja_ad']); ?>&nbsp;</li>
								</ul>
							</div>
                           </tr>                         
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
                         <?php } else { ?>
                         <td colspan="6">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
				    </table>
				</div>                   
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

</div>
	<!-- /page container -->
</body>
</html>