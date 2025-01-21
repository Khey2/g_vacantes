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
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 

if (isset($_POST['la_matriz'])) {$_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = $IDmatriz;} 
$la_matriz = $_SESSION['la_matriz']; 

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$el_usuario = $row_usuario['IDusuario'];

$query_matrizz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matrizz = mysql_query($query_matrizz, $vacantes) or die(mysql_error());
$row_matrizz = mysql_fetch_assoc($matrizz);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


// ESTADISTICOS
// Total Autorizada
$query_autorizados = "SELECT Count(prod_plantilla.IDplantilla) AS Autorizada, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_matriz.matriz FROM prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz WHERE vac_puestos.IDarea IN (1,2,3,4) AND prod_plantilla.IDmatriz = $la_matriz AND prod_plantilla.IDestatus = 1 AND (DATE(fecha_inicio) <= '$fecha') AND ( DATE(fecha_fin) > '$fecha' OR DATE(fecha_fin) = '0000-00-00' OR DATE(fecha_fin) IS NULL) AND ( DATE(fecha_congelada) > '$fecha' OR DATE(fecha_congelada) = '0000-00-00' OR DATE(fecha_congelada) IS NULL) GROUP BY prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDtipo_plaza ORDER BY vac_puestos.denominacion ASC";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

$totales = 0;
do { $totales = $totales + $row_autorizados['Autorizada']; } while ($row_autorizados = mysql_fetch_assoc($autorizados)); 

//Total de Activos
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE IDmatriz = '$la_matriz' AND IDarea IN (1,2,3,4)";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

// Inventario
$query_inventario = "SELECT prod_activos.*, pc_semaforo.*, vac_puestos.plan_carrera FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto WHERE prod_activos.IDmatriz = '$la_matriz'  AND pc_semaforo.reqb = 1 AND pc_semaforo.reqe = 1";   
$inventario = mysql_query($query_inventario, $vacantes) or die(mysql_error());
$row_inventario = mysql_fetch_assoc($inventario);
$totalRows_inventario = mysql_num_rows($inventario);
$el_puest = $row_inventario['IDpuesto'];

// Vacantes
$query_vacantes1 = "SELECT * FROM vac_vacante  WHERE vac_vacante.IDmatriz = $la_matriz AND vac_vacante.IDestatus = 1 AND IDpuesto IN (42,43,44,45)";
$vacantes1 = mysql_query($query_vacantes1, $vacantes) or die(mysql_error());
$row_vacantes1 = mysql_fetch_assoc($vacantes1);
$totalRows_vacantes1 = mysql_num_rows($vacantes1);

// Puestos disponibles
$query_puestos = "SELECT * FROM vac_puestos WHERE IDpuesto IN (42,43,44,45) ORDER BY vac_puestos.denominacion ASC";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

if (isset($_POST['el_puesto'])) {foreach ($_POST['el_puesto'] as $el_pueste)
	{	$_SESSION['el_puesto'] = implode(",", $_POST['el_puesto']);}}  else { $_SESSION['el_puesto'] = '42,43,44,45';} 
$el_puesto = $_SESSION['el_puesto'];

if (isset($_POST['estatus_pc'])) {$_SESSION['estatus_pc'] = $_POST['estatus_pc']; } else { $_SESSION['estatus_pc'] = '0';} 
$estatus_pc =  $_SESSION['estatus_pc']; 

if (isset($_POST['la_licencia'])) {$_SESSION['la_licencia'] = $_POST['la_licencia']; } else { $_SESSION['la_licencia'] = '0';} 
$la_licencia =  $_SESSION['la_licencia']; 

	 if ($la_licencia == 0) {$c2 = " "; } 
else if ($la_licencia == 1) {$c2 = " AND pc_semaforo.reqd > 0"; } 
else if ($la_licencia == 2) {$c2 = " AND pc_semaforo.reqd = 0"; } 

$query_pcarrera = "SELECT pc_semaforo.*, prod_activos.*, vac_puestos.denominacion AS denominacionPC FROM pc_semaforo left JOIN prod_activos ON pc_semaforo.IDempleado = prod_activos.IDempleado left JOIN vac_puestos ON pc_semaforo.IDpuestoPC = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = '$la_matriz' AND pc_semaforo.reqb = 1 AND pc_semaforo.reqe = 1 AND pc_semaforo.estatus_pc = $estatus_pc AND pc_semaforo.IDpuestoPC IN ($el_puesto) ".$c2; 
mysql_query("SET NAMES 'utf8'");
$pcarrera = mysql_query($query_pcarrera, $vacantes) or die(mysql_error());
$row_pcarrera = mysql_fetch_assoc($pcarrera);
$totalRows_pcarrera = mysql_num_rows($pcarrera);

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
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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

	                <!-- Content area -->
				<div class="content">
                
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 1) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente la justificacion.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


							<!-- Simple text stats with icons -->
							<div class="panel panel-body">
								<div class="row text-center">
									<div class="col-xs-3">
										<p><i class="icon-user-check icon-2x display-inline-block text-primary"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totales; ?></h5>
										<span class="text-muted text-size-small"><a href="plantilla.php">Plantilla Autorizada Operaciones</a></span>
									</div>

									<div class="col-xs-3">
										<p><i class="icon-user-plus icon-2x display-inline-block text-info"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $row_activos['TActivos']; ?></h5>
										<span class="text-muted text-size-small"><a href="plantilla_activos.php">Empleados Activos Operaciones</a></span>
									</div>

									<div class="col-xs-3">
										<p><i class="icon-user-block icon-2x display-inline-block text-warning"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totalRows_vacantes1; ?></h5>
										<span class="text-muted text-size-small"><a href="vacantes_activas.php">Vacantes de Chofer</a></span>
									</div>

									<div class="col-xs-3">									
										<p><i class="icon-user-check icon-2x display-inline-block text-success"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totalRows_inventario; ?></h5>
										<span class="text-muted text-size-small"><a href="plan_carrera_inv.php">Talento Disponible</a></span>
									</div>

								</div>
							</div>
							<!-- /simple text stats with icons -->

						<!-- Simple text stats with icons -->
				<div class="panel panel-body">
				<div class="row text-center">

					<!-- FILTROS -->
						<form method="POST" action="plan_carrera_tablero.php" class="form-horizontal">
						<fieldset class="content-group">
							<div class="col-lg-2">Matriz
								<select name="la_matriz" id="la_matriz" class="form-control">
								<?php do { ?>
									<option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php  if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo 
									"selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
									<?php
									} while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
									$rows = mysql_num_rows($lmatriz);
									if($rows > 0) { mysql_data_seek($lmatriz, 0);
									$row_lmatriz = mysql_fetch_assoc($lmatriz); 
									} ?>  
								</select>
							</div>
							<div class="col-lg-3">Puesto Promoción
								<select name="el_puesto[]" class="multiselect" multiple="multiple" >
								<?php $cadena2 = $el_puesto; $array = explode(",", $cadena2);
								do { ?>
									<option value="<?php echo $row_puestos['IDpuesto']?>"<?php foreach ($array as $el_puesto) { if (!(strcmp($row_puestos['IDpuesto'], $el_puesto))) {echo 
									"selected=\"selected\"";} } ?>><?php echo $row_puestos['denominacion']?></option>
									<?php
									} while ($row_puestos = mysql_fetch_assoc($puestos));
									$rows = mysql_num_rows($puestos);
									if($rows > 0) { mysql_data_seek($puestos, 0);
									$row_puestos = mysql_fetch_assoc($puestos); 
									} ?> 
								</select>
							</div>
							<div class="col-lg-2">Licencia Manejo
								<select class="form-control" name="la_licencia">
									<option value="0" <?php if ( $la_licencia == 0 ) {echo "selected=\"selected\"";} ?>>Todos</option>
									<option value="1" <?php if ( $la_licencia == 1 ) {echo "selected=\"selected\"";} ?>>Si cuenta</option>
									<option value="2" <?php if ( $la_licencia == 2 ) {echo "selected=\"selected\"";} ?>>No cuenta</option>
									</select>
							</div>
							<div class="col-lg-2">Estatus
								<select class="form-control" name="estatus_pc">
									<option value="0" <?php if ( $estatus_pc == 0 ) {echo "selected=\"selected\"";} ?>>En proceso</option>
									<option value="1" <?php if ( $estatus_pc == 1 ) {echo "selected=\"selected\"";} ?>>Promovido</option>
									</select>
							</div>
							<div class="col-lg-3">
								<button type="submit" class="btn btn-primary">Filtrar</button>										
								<a class="btn btn-default" href="plan_carrera_tablero.php?borrar=1">Borrar</a>
							</div>
						</fieldset>
					</form>

					</div>
				</div>
							<!-- /simple text stats with icons -->

					<!-- /simple statistics -->							
                	<div class="panel panel-flat">

					<div class="panel-body">	


					<div class="content-group-lg">
										<h6 class="text-semibold">Tablero Plan de Carrera:</h6>
										<ul class="list">
											<li>
												<strong class="display-block">1. Requisitos de Política.</strong>
												Se describe el cumplimiento de los requisitos de política: No contar con disciplina progresiva en los últimos 6 meses; no presentar más de tres faltas en un periodo de 3 meses; contar con al menos dos meses continuos de buen desempeño según indicadores de productividad y contar con al menos 6 meses de antigüedad en la empresa y 3 meses en la posición actual. <br/>Se genera con base en la información capturada en las secciones de Productividad, Asistencia, Disciplina Progresiva y Plantilla por parte de Operaciones y JRH.
											</li>
											<li>
												<strong class="display-block">2. Licencia.</strong>
												Se indica si el empleado cuenta con Licencia. <br/>Se genera con base en la información capturada en la sección de Inventario del Plan de Carrera por parte de JRH.
											</li>
											<li>
												<strong class="display-block">3. Capacitación Módulos del I al VI.</strong>
												Describe el avance en el cumplimiento de los cursos de capacitación y de manejo práctico, tanto presenciales, como virtuales necesarios en el Plan de Carrera. <br/>Los cursos se reportan por JRH y el área de capacitación a través del reporte de avance mensual. 
											</li>
											<li>
												<strong class="display-block">4. Capacitación Módulo VII.</strong>
												Indican el cumplimiento de la última fase de capacitación y empoderamiento por parte del área de Operaciones necesarios para asegurar que el empleado domine las funciones del puesto, previo a la promoción definitiva. <br/>Se reporta directamente en el sistema por parte del JRH, con base en las evaluaciones del Jefe de Operaciones, Supervisor de Tráfico y Operador a cargo. 
											</li>
										</ul>
									</div>

					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
							  <th>IDempleado</th>
							  <th>Nombre</th>
							  <th>Matriz</th>
							  <th>Puesto a Promover</th>
							  <th>Estatus</th>
							  <th class="text text-center">1</th>
							  <th class="text text-center">2</th>
							  <th class="text text-center">3</th>
							  <th class="text text-center">4</th>
							  <th></th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_pcarrera > 0) { ?>

                        <?php do { 
						$IDempleado = $row_pcarrera['IDempleado'];

						$query_pcarreras = "SELECT pc_semaforo.*, prod_activos.*, vac_puestos.denominacion AS denominacionPC FROM pc_semaforo left JOIN prod_activos ON pc_semaforo.IDempleado = prod_activos.IDempleado left JOIN vac_puestos ON pc_semaforo.IDpuestoPC = vac_puestos.IDpuesto WHERE prod_activos.IDempleado = $IDempleado"; 
						mysql_query("SET NAMES 'utf8'");
						$pcarreras = mysql_query($query_pcarreras, $vacantes) or die(mysql_error());
						$row_pcarreras = mysql_fetch_assoc($pcarreras);
						$totalRows_pcarreras = mysql_num_rows($pcarreras);
						
						$fecha_antiguedad = $row_pcarreras['fecha_antiguedad'];
						$fecha_alta = $row_pcarreras['fecha_alta'];
						
						$date_a1 = new DateTime(date("Y-m-d"));
						$date_b1 = new DateTime($fecha_antiguedad); 
						$diff_c1 = $date_a1->diff($date_b1);
						$periodo_d1 =  $diff_c1->m;
						
						$date_a2 = new DateTime(date("Y-m-d"));
						$date_b2 = new DateTime($fecha_alta); 
						$diff_c2 = $date_a2->diff($date_b2);
						$periodo_d2 =  $diff_c2->m;

						//asesorias mejora
						$query_L1a = "SELECT * FROM rel_lab_asesorias  WHERE IDempleado = $IDempleado AND fecha_captura < DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 6 MONTH)"; 
						$L1a = mysql_query($query_L1a, $vacantes) or die(mysql_error());
						$row_L1a = mysql_fetch_assoc($L1a);
						$totalRows_L1a = mysql_num_rows($L1a);
						
						//asistencia
						$query_L1b = "SELECT ind_asistencia_tipos.tipo, ind_asistencia.* FROM ind_asistencia LEFT JOIN ind_asistencia_tipos ON ind_asistencia.IDtipo = ind_asistencia_tipos.IDtipo  WHERE IDempleado = $IDempleado AND ind_asistencia_tipos.tipo = 101 AND fecha_captura < DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 6 MONTH)"; 
						$L1b = mysql_query($query_L1b, $vacantes) or die(mysql_error());
						$row_L1b = mysql_fetch_assoc($L1b);
						$totalRows_L1b = mysql_num_rows($L1b);
						
						//Politica
						$fase1 = 1;
						if (($periodo_d1 >= 6 AND $periodo_d2 >= 3) OR $row_pcarreras['a_antig'] == 1) {$fase1++;}
						if ($totalRows_L1b == 0) {$fase1++;}		
						if ($totalRows_L1a == 0 OR $row_pcarreras['a_discprog'] == 1) {$fase1++;}

						$fase2 = 0;
						if ($row_pcarreras['reqd'] > 0 ) {$fase2 = 1;}
						
						//modulo  Capacitación
						$query_M1a = "SELECT DISTINCT *	FROM capa_avance WHERE IDempleado = $IDempleado AND IDC_capa_cursos IN (185,249,3,4,182,267,62,258,257,59,203,58,148,250,251,252,253) GROUP BY IDC_capa_cursos"; 
						$M1a = mysql_query($query_M1a, $vacantes) or die(mysql_error());
						$row_M1a = mysql_fetch_assoc($M1a);
						$totalRows_M1a = mysql_num_rows($M1a);
						
						$fase3 = round(($totalRows_M1a / 17) * 100,0);
										
						//modulo 7 JO
						$query_M1g = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado AND viable = 1"; 
						$M1g = mysql_query($query_M1g, $vacantes) or die(mysql_error());
						$row_M1g = mysql_fetch_assoc($M1g);
						$totalRows_M1g = mysql_num_rows($M1g);
						
						$fase4 = 0;
						if ($totalRows_M1g == 3){$fase4 = 1;}
						
						?>
                          <tr>
                            <td><?php echo $row_pcarrera['IDempleado']; ?></td>
							<td>
							<a class="collapsed text-default" data-toggle="collapse" href="#collapse-group<?php echo $row_pcarrera['IDempleado']; ?>A"><?php echo $row_pcarrera['emp_paterno']." ".$row_pcarrera['emp_materno']." ".$row_pcarrera['emp_nombre']; ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_pcarrera['IDempleado']; ?>A" class="panel-collapse collapse">
							<b>Fecha Alta:</b> <?php $la_fecha = date("d/m/Y", strtotime($row_pcarrera['fecha_antiguedad'])); echo $la_fecha; ?><br/>
							<b>Puesto Actual:</b> <?php echo $row_pcarrera['denominacion']; ?><?php ?>
							</div>
							</td>
                            <td><?php echo $row_matrizz['matriz']; ?></td>
                            <td><?php echo $row_pcarrera['denominacionPC']; ?></td>
                            <td><?php if ($row_pcarrera['estatus_pc'] == 1) { echo "Promovido";} else { echo "En proceso";} ?></td>
							<td>
								<div class="progress">
									<div class="progress-bar progress-bar-<?php if($fase1 == 4) {echo "success";} else {echo "danger";}?>" style="width: 100%">
										<span><?php if($fase1 == 4) {echo "Si";} else {echo "No";}?></span>
									</div>
								</div>
							</td>
							<td>
								<div class="progress">
									<div class="progress-bar progress-bar-<?php if($fase2 == 1) {echo "success";} else {echo "danger";}?>" style="width: 100%">
										<span><?php if($fase2 == 1) {echo "Si";} else {echo "No";}?></span>
									</div>
								</div>
							</td>
							<td>
							<div class="progress">
							<div class="progress-bar progress-bar-<?php if($fase3 == 100) {echo "success";} else {echo "danger";}?>" style="width: 100%">
										<span><?php echo $fase3; ?>%</span>
									</div>
								</div>
							</td>
							</td>
							<td>
							<div class="progress">
									<div class="progress-bar progress-bar-<?php if($fase4 == 1) {echo "success";} else {echo "danger";}?>" style="width: 100%">
										<span><?php echo round(($totalRows_M1g/3) * 100,0);?>%</span>
									</div>
								</div>
							</td>
							 <td>
							 <a class="btn btn-primary btn-xs" href="plan_carrera_tablero_edit.php?IDempleado=<?php echo $row_pcarrera['IDempleado']; ?>">Ver detalle</a>
							</td>
                           </tr>						   
                          <?php } while ($row_pcarrera = mysql_fetch_assoc($pcarrera)); ?>
                         <?php } else { ?>
                         <tr><td colspan="9">Sin empleados con el filtro seleccionado.</td></tr>
                         <?php } ?>
					    </tbody>
				    </table>
				</div>                   


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