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
$desfase = $row_variables['dias_desfase'];
$anio = $row_variables['anio'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));


//las variables de sesion para el filtrado
if(isset($_POST['la_semana'])) { $_SESSION['la_semana'] = $_POST['la_semana']; } 
if(!isset($_SESSION['la_semana'])) { $_SESSION['la_semana'] = $semana; } 
$la_semana = $_SESSION['la_semana'];

if(isset($_POST['la_region'])) { $_SESSION['la_region'] = $_POST['la_region']; } 
if(!isset($_SESSION['la_region'])) { $_SESSION['la_region'] = '1,2,3'; } 
$la_region = $_SESSION['la_region'];

if(isset($_POST['el_anio'])) { $_SESSION['el_anio'] = $_POST['el_anio']; } 
if(!isset($_SESSION['el_anio'])) { $_SESSION['el_anio'] = $anio; } 
//if(!isset($_SESSION['el_anio'])) { $_SESSION['el_anio'] = $row_variables['anio']; } 
$el_anio = $_SESSION['el_anio'];


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
$la_region_u = $row_usuario['user_operaciones_regional'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$query_semanas = "SELECT * FROM prod_semanas WHERE anio = $el_anio";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];
$chofers = array(42, 43, 44, 45, 57, 372);


//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT vac_matriz.region_op, Count( prod_activos.IDempleado ) AS Ocupados, Count( prod_captura.capturador ) AS TCapturados, Count( prod_captura.validador ) AS TValidados, Count( prod_captura.autorizador ) AS TAutorizados, Sum( prod_captura.pago_total ) AS TTotal, Sum( prod_captura.adicional2 ) AS TAdicional, Sum( prod_captura.horas_extra_monto ) AS HEM, Sum( prod_captura.adicional3 ) AS TAdicional3,  Sum( prod_captura.sueldo_total ) AS TSueldo, vac_matriz.matriz, vac_matriz.IDmatriz, vac_matriz.incluye_pull, vac_puestos.IDpuesto FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado WHERE vac_puestos.IDaplica_PROD = 1 AND prod_captura.semana = '$la_semana' AND prod_captura.anio = '$el_anio' AND vac_matriz.region_op IN ($la_region) GROUP BY prod_activos.IDmatriz";  
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

if ($la_region == '1,2,3') {
$query_matriz_R = "SELECT * FROM vac_matriz";
$matriz_R = mysql_query($query_matriz_R, $vacantes) or die(mysql_error());
$row_matriz_R = mysql_fetch_assoc($matriz_R);
$totalRows_matriz_R = mysql_num_rows($matriz_R);
$rebase_R = 0;
$la_amtriz = 0;
} else { 
$query_matriz_R = "SELECT * FROM vac_matriz WHERE vac_matriz.region_op IN ($la_region)";
$matriz_R = mysql_query($query_matriz_R, $vacantes) or die(mysql_error());
$row_matriz_R = mysql_fetch_assoc($matriz_R);
$totalRows_matriz_R = mysql_num_rows($matriz_R);
$rebase_R = 0;
$la_amtriz = 0;
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

<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
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
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 1)) { ?>
					    <div class="alert bg-success-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han <span class="text-semibold">Autorizado</span> los registros de forma correcta. 
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


               			<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 2)) { ?>
					    <div class="alert bg-success-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han <span class="text-semibold">Autorizado y Cerrado</span> los registros de forma correcta. 
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Recuerda que las Sucursales con rebase se deben ajustar antes de autorizar.
					    </div>
					    <!-- /basic alert -->

					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Autorización semanal de productividad</h6>
								</div>

								<div class="panel-body">
                                <ul>
								<li><b>Semana: </b><?php echo $la_semana; ?></li>
                                <li><b>Año: </b><?php echo $anio; ?></li>
                                <li>Da clic en "Detalle" para ver la información por área o puesto.</li>
                                <li>Dando clic en Autorizar se autoriza el monto para todos los puestos de la Sucursal que han sido capturados y validados.</li>
                                <li>Iconos: <i class="icon-flag3 text-primary"></i> Capturada <i class="icon-flag3 text-info"></i> Validada <i class="icon-flag3 text-success"></i> Autorizada </li>
                                <li>Dando clic en <strong>Cerrar Sucursal</strong>, se bloquea la captura y validación.</li>
								<li><span class="text text-strong text-danger">Las Sucursales con rebase se deben ajustar antes de autorizar.</span></li>
								<li><span class="text text-strong text-warning">Se debe cerrar el proceso de captura y validación para poder descargar el reporte final.</span></li>
								</ul>


                    <form method="POST" action="productividad_autoriza_sucursal.php">
                            <div class="col-lg-2">
                                <select name="la_semana" class="form-control">
										<?php do {  ?>
                                           <option value="<?php echo $row_semanas['semana']?>"<?php if (!(strcmp($row_semanas['semana'], $la_semana)))
										   {echo "selected=\"selected\"";} ?>>Semana <?php echo $row_semanas['semana']?><?php if ($row_semanas['semana'] == $semana) { echo " (actual)";} ?></option>
											<?php
                                            } while ($row_semanas = mysql_fetch_assoc($semanas));
                                             $rows = mysql_num_rows($semanas);
                                            if($rows > 0) {
                                             mysql_data_seek($semanas, 0);
                                             $row_semanas = mysql_fetch_assoc($semanas);
                                            } ?>
								</select>
						    </div>
                            <div class="col-lg-3">
                                <select name="la_region" class="form-control">
									<option value="1,2,3" <?php if (!(strcmp("1,2,3", $la_region))) {echo "selected=\"selected\"";} ?>>Todas</option>
									<option value="3" <?php if (!(strcmp("3", $la_region))) {echo "selected=\"selected\"";} ?>>Región Sur</option>
									<option value="2" <?php if (!(strcmp("2", $la_region))) {echo "selected=\"selected\"";} ?>>Región Centro</option>
									<option value="1" <?php if (!(strcmp("1", $la_region))) {echo "selected=\"selected\"";} ?>>Región Norte</option>
								</select>
						    </div>
                            <div class="col-lg-3">
                                <select name="el_anio" class="form-control">
									<option value="2025" <?php if (!(strcmp("2025", $el_anio))) {echo "selected=\"selected\"";} ?>>2025</option>
									<option value="2024" <?php if (!(strcmp("2024", $el_anio))) {echo "selected=\"selected\"";} ?>>2024</option>
									<option value="2023" <?php if (!(strcmp("2023", $el_anio))) {echo "selected=\"selected\"";} ?>>2023</option>
									<option value="2022" <?php if (!(strcmp("2022", $el_anio))) {echo "selected=\"selected\"";} ?>>2022</option>
								</select>
						    </div>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>
							<button type="button" class="btn btn-success" onClick="window.location.href='productividad_reportes_new.php?anio=<?php echo $el_anio; ?>&semana=<?php echo $la_semana; ?>&areas=1&tipo=0'">Descargar Reporte</button>
					</form>

				<p>&nbsp;</p>

				<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>Acciones</th>
                      <th>Sucursal</th>
                      <th>Activos</th>
                      <th>Capturados</th>
                      <th>Validados</th>
                      <th>Autorizados</th>
                      <th>Gasto Prod.</th>
                      <th class="bg-warning">Presupuesto</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php if ($totalRows_detalle > 0) { ?>
									    <?php do { 	?>
									      <tr>
                                            <td>

                                            <?php 
                                            $matriz = $row_detalle['IDmatriz'];
                                            mysql_select_db($database_vacantes, $vacantes);
                                            $query_cierre = "SELECT * FROM prod_semana_cierre WHERE IDmatriz = '$matriz' AND semana = '$la_semana' AND anio = '$el_anio'";
                                            $cierre = mysql_query($query_cierre, $vacantes) or die(mysql_error());
                                            $row_cierre = mysql_fetch_assoc($cierre);
                                            $totalRows_cierre = mysql_num_rows($cierre);
											$cerrar = $row_cierre['estatus'];

                                            mysql_select_db($database_vacantes, $vacantes);
                                            $query_activos = "SELECT Count(prod_activos.IDempleado) AS Ocupados FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE prod_activos.IDmatriz = '$matriz' AND vac_puestos.IDaplica_PROD = 1 GROUP BY prod_activos.IDmatriz";
                                            $activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
                                            $row_activos = mysql_fetch_assoc($activos);

											mysql_select_db($database_vacantes, $vacantes);
											$query_monto1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.adicional3) AS Monto6, Sum(prod_captura.bono_asistencia) AS Monto5, Sum(prod_captura.adicional) AS Monto4, Sum(prod_captura.sueldo_total) AS Monto3, Sum(prod_captura.horas_extra_monto) AS HEM, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$el_anio' AND prod_captura.semana = '$la_semana' AND prod_captura.IDmatriz = '$matriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
											$monto1 = mysql_query($query_monto1, $vacantes) or die(mysql_error());
											$row_monto1 = mysql_fetch_assoc($monto1);
											$totalRows_monto1 = mysql_num_rows($monto1);
											$el_monto1 = $row_monto1['Monto'] + $row_monto1['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'] + $row_monto1['Monto6'] + $row_monto1['HEM'];

											//consiera pull en presupuesto
											if($row_detalle['incluye_pull'] == 1) {$pulls = '1,2';} else { $pulls = '1'; }

											$fecha_filtro = date('Y/m/d', strtotime('monday -1 week'));											

											$MontoZ = 0;
											$AutorizadosZ = 0;
											
											
											///////////////////////////////////////////////////////////////////////////////RESTO DE PUESTOS/////////////////////////////////////////////////////////////////
											
											// RECORRER TODOS LOS PUESTOS 
											$query_puestos_aplicablesB = "SELECT vac_puestos.denominacion, vac_puestos.IDaplica_PROD, prod_plantilla.IDplantilla, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDpuesto, prod_plantilla.IDestatus, prod_plantilla.IDtipo_plaza  FROM prod_plantilla LEFT JOIN vac_puestos ON prod_plantilla.IDpuesto = vac_puestos.IDpuesto  WHERE prod_plantilla.IDmatriz = $matriz AND vac_puestos.IDaplica_PROD = 1  AND prod_plantilla.IDestatus = 1  AND ( DATE ( fecha_inicio ) <= '$fecha_filtro' )  AND ( DATE ( fecha_fin ) > '$fecha_filtro' OR DATE ( fecha_fin ) = '0000-00-00' OR DATE ( fecha_fin ) IS NULL )  AND ( DATE ( fecha_congelada ) > '$fecha_filtro' OR DATE ( fecha_congelada ) = '0000-00-00' OR DATE ( fecha_congelada ) IS NULL ) GROUP BY prod_plantilla.IDpuesto";
											$puestos_aplicablesB = mysql_query($query_puestos_aplicablesB, $vacantes) or die(mysql_error());
											$row_puestos_aplicablesB = mysql_fetch_assoc($puestos_aplicablesB);
											$totalRows_puestos_aplicablesB = mysql_num_rows($puestos_aplicablesB);
											
											do {
											
											$IDpuestoB = $row_puestos_aplicablesB['IDpuesto'];
																						
											// detalles del puesto para pintar el área
											$query_el_puestoB = "SELECT vac_puestos.*, vac_areas.area FROM vac_puestos LEFT JOIN vac_areas ON  vac_puestos.IDarea = vac_areas.IDarea WHERE IDpuesto = $IDpuestoB";
											$el_puestoB = mysql_query($query_el_puestoB, $vacantes) or die(mysql_error());
											$row_el_puestoB = mysql_fetch_assoc($el_puestoB);
											$totalRows_el_puestoB = mysql_num_rows($el_puestoB);
											$el_areaB = $row_el_puestoB['area'];

											// activos para mostrar activos
											$query_activosB = "SELECT prod_activos.*  FROM prod_activos WHERE IDmatriz = $matriz AND IDpuesto IN ($IDpuestoB)";
											$activosB = mysql_query($query_activosB, $vacantes) or die(mysql_error());
											$row_activosB = mysql_fetch_assoc($activosB);
											$totalRows_activosB = mysql_num_rows($activosB);

											// plantilla autorizada
											$query_plantillaB = "SELECT Count(prod_plantilla.IDplantilla) AS Plantilla FROM prod_plantilla WHERE IDmatriz = $matriz AND IDpuesto IN ($IDpuestoB) AND prod_plantilla.IDestatus = 1 AND ( DATE ( fecha_inicio ) <= '$fecha_filtro' ) AND ( DATE ( fecha_fin ) > '$fecha_filtro' OR DATE ( fecha_fin ) = '0000-00-00' OR DATE ( fecha_fin ) IS NULL ) AND ( DATE ( fecha_congelada ) > '$fecha_filtro' OR DATE ( fecha_congelada ) = '0000-00-00' OR DATE ( fecha_congelada ) IS NULL )";
											$plantillaB = mysql_query($query_plantillaB, $vacantes) or die(mysql_error());
											$row_plantillaB = mysql_fetch_assoc($plantillaB);
											
											// monto asistencia
											$query_monto_asistenciaB = "SELECT prod_garantias.*  FROM prod_garantias WHERE IDmatriz = $matriz AND IDpuesto IN ($IDpuestoB)";
											$monto_asistenciaB = mysql_query($query_monto_asistenciaB, $vacantes) or die(mysql_error());
											$row_monto_asistenciaB = mysql_fetch_assoc($monto_asistenciaB);
											$totalRows_monto_asistenciaB = mysql_num_rows($monto_asistenciaB);
											$Monto_asistenciaB = $row_monto_asistenciaB['monto_asistencia'];
											
											// monto segun activos y garantia
											$query_presupuesto_cajasB = "SELECT SUM(vac_tabulador.variable_mensual / 30 ) * 7 As MontoA, SUM(vac_tabulador.asistencia_mensual / 30 ) * 7 As MontoB FROM prod_activos LEFT JOIN vac_tabulador ON prod_activos.IDpuesto = vac_tabulador.IDpuesto AND prod_activos.IDmatriz = vac_tabulador.IDmatriz AND prod_activos.IDnivel_antiguedad = vac_tabulador.IDnivel WHERE prod_activos.IDmatriz = $matriz AND prod_activos.IDpuesto = $IDpuestoB";
											$presupuesto_cajasB = mysql_query($query_presupuesto_cajasB, $vacantes) or die(mysql_error()); 
											$row_presupuesto_cajasB = mysql_fetch_assoc($presupuesto_cajasB);
											$totalRows_presupuesto_cajasB = mysql_num_rows($presupuesto_cajasB);
											$Monto_sueldosB = $row_presupuesto_cajasB['MontoA'];
											$Monto_asistenciaB = $row_presupuesto_cajasB['MontoB'];
												
											// nivel minimo para la sucursal
											$query_minimo_tabuladorB = "SELECT * FROM prod_valor_antiguedad WHERE IDmatriz = $matriz AND IDpuesto = $IDpuestoB AND meses_inicio = 0";
											$minimo_tabuladorB = mysql_query($query_minimo_tabuladorB, $vacantes) or die(mysql_error());
											$row_minimo_tabuladorB = mysql_fetch_assoc($minimo_tabuladorB);
											$totalRows_minimo_tabuladorB = mysql_num_rows($minimo_tabuladorB);
											$Nivel_minimoB = $row_minimo_tabuladorB['IDnivel'];

											// Monto de la garantia en porcentaje para recien ingresos
											$query_garantia_tabuladorB = "SELECT * FROM prod_garantias WHERE IDmatriz = $matriz AND IDpuesto = $IDpuestoB AND IDnivel = '$Nivel_minimoB'";
											$garantia_tabuladorB = mysql_query($query_garantia_tabuladorB, $vacantes) or die(mysql_error());
											$row_garantia_tabuladorB = mysql_fetch_assoc($garantia_tabuladorB);
											$totalRows_garantia_tabuladorB = mysql_num_rows($garantia_tabuladorB);
											//$Monto_garantiaB = $row_garantia_tabuladorB['garantia']; 
											
											// Tabulador autorizado
											$query_tabuladorB = "SELECT * FROM vac_tabulador WHERE IDmatriz = $matriz AND IDpuesto = $IDpuestoB";
											$tabuladorB = mysql_query($query_tabuladorB, $vacantes) or die(mysql_error()); 
											$row_tabuladorB = mysql_fetch_assoc($tabuladorB);
											$totalRows_tabuladorB = mysql_num_rows($tabuladorB);
											$Monto_garantiaB = ($row_tabuladorB['variable_mensual']/30)*7;
											$Monto_sueldo_tabuladorB = $row_tabuladorB['sueldo_diario'] * 7;
											$Monto_asistencia_tabuladorB =  ($row_tabuladorB['asistencia_mensual']/30)*7;
																																	
											// diferencia de plazas
											if ($row_plantillaB['Plantilla'] > $totalRows_activosB ) {
												$diferencia_plazasB = $row_plantillaB['Plantilla'] - $totalRows_activosB; 
												$Monto_2B = $diferencia_plazasB * $Monto_garantiaB; 
												$Monto_3B = $diferencia_plazasB * $Monto_asistencia_tabuladorB;
												} else {
												$Monto_2B = 0;
												$Monto_3B = 0;
												}
	
													
												$Monto_4B = $Monto_sueldosB + $Monto_asistenciaB + $Monto_2B + $Monto_3B;
												$MontoZ = $MontoZ + $Monto_4B;
												$AutorizadosZ = $AutorizadosZ + $row_plantillaB['Plantilla'];
																																		
											} while ($row_puestos_aplicablesB = mysql_fetch_assoc($puestos_aplicablesB)); 
											

											mysql_select_db($database_vacantes, $vacantes);
											$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as resultadoP FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$matriz' AND IDanio = '$el_anio' AND IDsemana = $la_semana AND IDestatus = 1";
											$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
											$row_adicional = mysql_fetch_assoc($adicional);
											$totalRows_adicional = mysql_num_rows($adicional);
											$aplica_adicional = $row_adicional['resultadoP'];

											// presupuesto directo
											$Total0 = $MontoZ; 

											if ($aplica_adicional != 0) { $Total0 = $Total0 + $aplica_adicional; }

											$rebase = 0;
											if($el_monto1 > $Total0){ $rebase = 1; } 											
											?>
											
                                            <button type="button" class="btn btn-success btn-xs" 
                                            onClick="window.location.href='productividad_autoriza_puesto.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'">
                                            </i>Detalle</button> 
											
											
											<?php if( $rebase == 0){ ?>
											<button type="button" class="btn btn-primary btn-xs" 
                                            onClick="window.location.href='prod_autoriza_sucursalAlm.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&anio=<?php echo $el_anio; ?>&semana=<?php echo $la_semana; ?>'">
                                            </i>Aut. Alma.</button>
											
                                            <button type="button" class="btn btn-primary btn-xs" 
                                            onClick="window.location.href='prod_autoriza_sucursalDis.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&anio=<?php echo $el_anio; ?>&semana=<?php echo $la_semana; ?>'">
                                            </i>Aut. Dist.</button>
											<?php } ?>
                                            
											<?php if($rebase == 1){ ?>		
											<button type="button" class="btn btn-warning btn-xs" onClick="#">Rebasado</button>
											<?php } else if($row_cierre['captura'] != 1) { ?>
                                            <button type="button" class="btn btn-warning btn-xs" onClick="#">Sin Captura</button>
											<?php } else if($row_cierre['valida']  != 1) { ?>
                                            <button type="button" class="btn btn-warning btn-xs" onClick="#">Sin Validación</button>
											<?php } elseif ($row_cierre['autoriza'] == 1) { ?>
											<button type="button" class="btn btn-info btn-xs" onClick="#">Cerrada</button>
											<?php } else if($row_cierre['captura'] == 1 AND $row_cierre['valida'] == 1 AND $row_cierre['autoriza'] != 1) { ?>
											<button type="button" class="btn btn-info btn-xs"  onClick="window.location.href='prod_autoriza_cierre.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'">Cerrar</button>
											<?php } ?>

                                        	</td>
									        <td><?php echo $row_detalle['matriz'];  ?>&nbsp; </td>
									        <td><?php echo $row_activos['Ocupados'];  ?>&nbsp; </td>
									        <td><?php echo $row_detalle['TCapturados']; ?>  <?php if($row_cierre['captura']  == 1) { ?><i class="icon-flag3 text-primary"></i><?php } ?></td>
									        <td><?php echo $row_detalle['TValidados']; ?>   <?php if($row_cierre['valida']   == 1) { ?><i class="icon-flag3 text-info"></i><?php } ?></td>
									        <td><?php echo $row_detalle['TAutorizados']; ?> <?php if($row_cierre['autoriza'] == 1) { ?><i class="icon-flag3 text-success"></i><?php } ?></td>
									        <td><a href="prod_comparativo_s2.php?IDmatriz=<?php echo $matriz; ?>"><?php echo "$" . number_format($el_monto1); ?></a></td>
									        <td><span class="text <?php if( $rebase == 1){ echo "text-danger"; } ?>"><strong></strong> <?php echo "$" . number_format(round($Total0, 2));?></span>
										</td>
                    					</tr>
										
									      <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
									      <?php } else { ?>
									        <tr><td colspan="7">Sin captura / Selecciona una Regional.</td></tr>
									      <?php } ?>
                                    </tbody>
                                   </table> 
								</div>
							</div>
						</div>
                                    
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Presupuesto Rebasado</h6>
								</div>

								<div class="modal-body">
									<p>Existen Sucursales con presupuesto rebasado, favor de validar.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


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