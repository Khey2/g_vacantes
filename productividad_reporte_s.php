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
if(isset($_POST['el_anio'])) { $anio = $_POST['el_anio'];} else {$anio = $row_variables['anio'];}
$desfase = $row_variables['dias_desfase'];
// mes y semana
$el_mes = date("m");

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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
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

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT * FROM prod_semanas";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);
$totalRows_semanas = mysql_num_rows($semanas);


//las variables de sesion para el filtrado
if(isset($_GET['IDmatriz'])) {
$_SESSION['la_matriz'] = $_GET['IDmatriz']; } 
else if(isset($_POST['la_matriz']) && ($_POST['la_matriz'] > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; } 
else { $_SESSION['la_matriz'] = $IDmatriz; }

$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

if(isset($_GET['la_semana'])) {
$_SESSION['la_semana'] = $_GET['la_semana']; } 
else if(isset($_POST['la_semana']) && ($_POST['la_semana'] > 0)) {
$_SESSION['semana'] = $_POST['la_semana']; } 
else { $_SESSION['semana'] = $semana; }

$la_matriz = $_SESSION['la_matriz'];
$la_semana = $_SESSION['semana'];

if(isset($_POST['el_anio']) && $_POST['el_anio'] == '2020') { 

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT Count(prod_activos.IDempleado) AS Ocupados, Count(prod_captura_2020.capturador) AS TCapturados, Count(prod_captura_2020.validador) AS TValidados, Count(prod_captura_2020.autorizador) AS TAutorizados, Sum(prod_captura_2020.pago_total) AS TTotal, Sum(prod_captura_2020.adicional2) AS TAdicional, vac_matriz.matriz, vac_matriz.IDmatriz, vac_puestos.modal, vac_puestos.IDpuesto, vac_puestos.denominacion, vac_areas.area, vac_areas.IDarea FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN prod_captura_2020 ON prod_captura_2020.IDempleado = prod_activos.IDempleado AND prod_captura_2020.semana = '$la_semana' WHERE vac_puestos.IDaplica_PROD = 1 AND vac_matriz.IDmatriz = '$la_matriz' AND  prod_activos.IDarea IN ($mis_areas) GROUP BY vac_puestos.denominacion "; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);


mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];

//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$IDmatriz' AND  prod_activos.IDarea IN ($mis_areas)";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

//Autorizados, Capturados y Validados
mysql_select_db($database_vacantes, $vacantes);
$query_empleados = "SELECT Count(prod_captura_2020.capturador) AS TCapturados, Count(prod_captura_2020.validador) AS TValidados, Count(prod_captura_2020.autorizador) AS TAutorizados FROM prod_captura_2020 WHERE prod_captura_2020.semana = '$semana' AND prod_captura_2020.IDmatriz = '$IDmatriz'";
$empleados = mysql_query($query_empleados, $vacantes) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);
$totalRows_empleados = mysql_num_rows($empleados);

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_monto1 = "SELECT Sum(prod_captura_2020.pago_total) As Monto, prod_captura_2020.IDmatriz, prod_captura_2020.adicional2, prod_captura_2020.semana FROM prod_captura_2020 WHERE prod_captura_2020.semana = '$semana' AND prod_captura_2020.IDmatriz = '$IDmatriz' GROUP BY prod_captura_2020.IDmatriz, prod_captura_2020.semana ";
$monto1 = mysql_query($query_monto1, $vacantes) or die(mysql_error());
$row_monto1 = mysql_fetch_assoc($monto1);
$totalRows_monto1 = mysql_num_rows($monto1);
$el_monto1 = $row_monto1['Monto'] + $row_monto1['adicional2'];

} else {

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT Count(prod_activos.IDempleado) AS Ocupados, Count(prod_captura.capturador) AS TCapturados, Count(prod_captura.validador) AS TValidados, Count(prod_captura.autorizador) AS TAutorizados, Sum(prod_captura.pago_total) AS TTotal, Sum(prod_captura.adicional2) AS TAdicional, vac_matriz.matriz, vac_matriz.IDmatriz, vac_puestos.modal, vac_puestos.IDpuesto, vac_puestos.denominacion, vac_areas.area, vac_areas.IDarea FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$la_semana' AND prod_captura.anio = '$anio' WHERE vac_puestos.IDaplica_PROD = 1 AND vac_matriz.IDmatriz = '$la_matriz' AND  prod_activos.IDarea IN ($mis_areas) GROUP BY vac_puestos.denominacion "; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);


mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];

//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$IDmatriz' AND  prod_activos.IDarea IN ($mis_areas)";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

//Autorizados, Capturados y Validados
mysql_select_db($database_vacantes, $vacantes);
$query_empleados = "SELECT Count(prod_captura.capturador) AS TCapturados, Count(prod_captura.validador) AS TValidados, Count(prod_captura.autorizador) AS TAutorizados FROM prod_captura WHERE prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$IDmatriz' AND prod_captura.anio = '$anio'";
$empleados = mysql_query($query_empleados, $vacantes) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);
$totalRows_empleados = mysql_num_rows($empleados);

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_monto1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, prod_captura.adicional2, prod_captura.semana FROM prod_captura WHERE prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$IDmatriz' AND prod_captura.anio = '$anio'GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
$monto1 = mysql_query($query_monto1, $vacantes) or die(mysql_error());
$row_monto1 = mysql_fetch_assoc($monto1);
$totalRows_monto1 = mysql_num_rows($monto1);
$el_monto1 = $row_monto1['Monto'] + $row_monto1['adicional2'];

}

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_plantilla = "SELECT prod_plantilla.IDplantilla, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.autorizados, prod_plantilla.sueldo_diario FROM prod_plantilla WHERE prod_plantilla.IDmatriz = '$la_matriz'";
$plantilla = mysql_query($query_plantilla, $vacantes) or die(mysql_error());
$row_plantilla = mysql_fetch_assoc($plantilla);
$totalRows_plantilla = mysql_num_rows($plantilla);

$_diario = 0;
$_semanal = 0;
$_mensual = 0;
$_puestos = 0;
do { 

$a = $row_plantilla['autorizados'] * $row_plantilla['sueldo_diario'];
$_diario = $_diario + $a; 
$_puestos = $_puestos + $row_plantilla['autorizados'];
} while ($row_plantilla = mysql_fetch_assoc($plantilla));
$_semanal = $_diario * 7;
$_mensual = $_diario * 30;


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
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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

					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Reporte semanal de productividad</h5></br>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Sucursal:</label>
											<?php echo $lmatriz; ?>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Semana:</label>
											<?php echo $la_semana; ?>
										</div>							

								<div class="panel-body">						
                                <p>Seleccione el Área para ver el detalle por Puesto.</p>

								<!-- Statistics with progress bar -->
					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Costo Plantilla Autorizada</h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cash3 icon-2x text-slate-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-slate-400" style="width: 100%">
									</div>
								</div>
				              	<span class="text-muted"><strong>Mensual:</strong> <?php echo "$" . number_format($_mensual);?></span> |
								<span class="text-muted"><strong>Semanal: </strong> <?php echo "$" . number_format($_semanal);?></span>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Gasto Productividad</h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cash2 icon-2x text-slate-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-slate-400" style="width: 100%">
									</div>
								</div>
				              	<span class="text-muted"><strong>Monto($)</strong>: <?php echo "$" . number_format($el_monto1); ?></span>  | 
				              	<span class="text-muted"><strong>Porcentaje(%):</strong> <?php echo round(($el_monto1 / $_mensual) * 1000,2); ?>%</span> 
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-calendar2 icon-2x text-slate-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Periodo</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-slate" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Mes:</strong> <?php echo $elmes; ?></span>
										<span class="text-muted"><strong>Semana: </strong><?php echo $la_semana; ?></span>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-users2  icon-2x text-slate-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Empleados</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-slate-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Totales: </strong> <?php echo $row_activos['TActivos']; ?></span> | 
										<span class="text-muted"><strong>Capturados:</strong> <?php echo $row_empleados['TCapturados']; ?></span> | 
										<span class="text-muted"><strong>Validados:</strong> <?php echo $row_empleados['TValidados']; ?></span>
							</div>
						</div>
					</div>

					<!-- /statistics with progress bar -->
								
						 	

								<div class="panel-body text-center alpha-grey">
									<a href="productividad_reportes_all.php?IDmatriz=<?php echo $la_matriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio; ?>" class="btn bg-success-400">
										<i class="icon-file-excel position-left"></i>
										Descargar Reporte
									</a>
								</div>                                    


                       <form method="POST" action="productividad_reporte_s.php">

					<table class="table">
						<tbody>							  
							<tr>
                            <td>
                             <select name="el_anio" class="form-control">
                               <option value="2020"<?php if (!(strcmp($anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
                               <option value="2021"<?php if (!(strcmp($anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
                             </select>
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
							<td> <div class="col-lg-9 no-prints">
										<select name="la_semana" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_semana))) {echo "selected=\"selected\"";} ?>>Semana: Actual</option>
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
									<td>
                                <button type="submit" class="btn btn-success">Filtrar <i class="icon-filter3  position-right"></i></button>	
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	



					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-slate"> 
                      <th>Acciones</th>
                      <th>Sucursal</th>
                      <th>Área</th>
                      <th>Puesto</th>
                      <th>Activos</th>
                      <th>Capturados</th>
                      <th>Validados</th>
                      <th>Autorizados</th>
                      <th>Monto Total</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php do { 	?>
									      <tr>
                                            <td>
                                            <?php if  ($row_detalle['modal'] != 100) { ?>
                                            <button type="button" class="btn btn-default btn-xs" onClick="window.location.href='productividad_reporte_empleado_a.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&la_semana=<?php echo $la_semana; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&anio=<?php echo $anio; ?>'"><i class="icon-arrow-right6"></i> Ver Empleados</button>
                                            <?php } else { 	?>
                                            <button type="button" class="btn btn-default btn-xs" onClick="window.location.href='productividad_reporte_empleado.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&la_semana=<?php echo $la_semana; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&anio=<?php echo $anio; ?>'"><i class="icon-arrow-right6"></i> Ver Empleados</button>
                                            <?php } ?>
                                          </td>
									        <td><?php echo $row_detalle['matriz']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['area']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['Ocupados']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['TCapturados']; ?></td>
									        <td><?php echo $row_detalle['TValidados']; ?></td>
									        <td><?php echo $row_detalle['TAutorizados']; ?></td>
									        <td><?php echo "$" . number_format($row_detalle['TTotal'] + $row_detalle['TAdicional']); ?></td>
                    					</tr>
									      <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                                    </tbody>
                                   </table> 

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