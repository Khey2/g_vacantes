<?php require_once('Connections/vacantes.php'); ?>
<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 

$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$semana_previa = $semana - 1;
if ($semana == 1) {$semana_previa = 52;}

$anio = $row_variables['anio'];
$anio_previo = $anio;
if ($semana == 1) {$anio_previo = $anio - 1;}

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
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDaplica_PROD = 1  AND prod_activos.IDaplica_SED = 0";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);


//filtrado por sucursal
	 if(isset($_POST['la_sucursal']) && ($_POST['la_sucursal']   > 0)) {
$_SESSION['la_sucursal'] = $_POST['la_sucursal']; } 
else if (isset($_POST['la_sucursal']) && ($_POST['la_sucursal'] == 0)) {
$_SESSION['la_sucursal'] = ""; } 

if(!isset($_SESSION['la_sucursal'])) {$_SESSION['la_sucursal'] = 0;}

$la_sucursal = $_SESSION['la_sucursal'];
if($la_sucursal > 0) {
$s1 = " AND prod_activos.IDsucursal = $la_sucursal"; 
} else {
$s1 = " "; } 

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = '$la_matriz'";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT DISTINCT Count( prod_activos.IDempleado ) AS Ocupados, prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.modal, vac_areas.area, prod_activos.IDpuesto, vac_puestos.prod_captura_tipo FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea WHERE vac_puestos.IDaplica_PROD = 1 AND prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDarea IN ($mis_areas) ".$s1." GROUP BY vac_puestos.denominacion"; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

//Autorizados, Capturados y Validados
mysql_select_db($database_vacantes, $vacantes);
$query_empleados = "SELECT Count(prod_captura.capturador) AS TCapturados, Count(prod_captura.validador) AS TValidados, Count(prod_captura.autorizador) AS TAutorizados FROM prod_captura WHERE prod_captura.anio = '$anio' AND prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$IDmatriz'";
$empleados = mysql_query($query_empleados, $vacantes) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);
$totalRows_empleados = mysql_num_rows($empleados);

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_monto1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.adicional3) AS Monto6, Sum(prod_captura.bono_asistencia) AS Monto5,  Sum(prod_captura.adicional) AS Monto4, Sum(prod_captura.horas_extra_monto) AS MontoHE, Sum(prod_captura.sueldo_total) AS Monto3, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$anio' AND prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$IDmatriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
$monto1 = mysql_query($query_monto1, $vacantes) or die(mysql_error());
$row_monto1 = mysql_fetch_assoc($monto1);
$totalRows_monto1 = mysql_num_rows($monto1);
$el_monto1 = $row_monto1['Monto'] + $row_monto1['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'] + $row_monto1['Monto6'] + $row_monto1['MontoHE'];

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_montoX1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.adicional3) AS Monto6, Sum(prod_captura.bono_asistencia) AS Monto5, Sum(prod_captura.adicional) AS Monto4,  Sum(prod_captura.horas_extra_monto) AS MontoHE, Sum(prod_captura.sueldo_total) AS Monto3, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$anio_previo' AND prod_captura.semana = '$semana_previa' AND prod_captura.IDmatriz = '$IDmatriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
$montoX1 = mysql_query($query_montoX1, $vacantes) or die(mysql_error());
$row_montoX1 = mysql_fetch_assoc($montoX1);
$totalRows_montoX1 = mysql_num_rows($montoX1);
$el_montoX1 = $row_montoX1['Monto'] + $row_montoX1['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'] + $row_monto1['Monto6'] + $row_monto1['MontoHE'];

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_montoX2 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.adicional3) AS Monto6, Sum(prod_captura.bono_asistencia) AS Monto5, Sum(prod_captura.adicional) AS Monto4,  Sum(prod_captura.horas_extra_monto) AS MontoHE, Sum(prod_captura.sueldo_total) AS Monto3, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$anio' AND prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$IDmatriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
$montoX2 = mysql_query($query_montoX2, $vacantes) or die(mysql_error());
$row_montoX2 = mysql_fetch_assoc($montoX2);
$totalRows_montoX2 = mysql_num_rows($montoX2);
$el_montoX2 = $row_montoX2['Monto'] + $row_montoX2['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'] + $row_monto1['Monto6'] + $row_monto1['MontoHE'];

//consiera pull en presupuesto
if($row_matriz['incluye_pull'] == 1) {$pulls = '1,2';} else { $pulls = '1'; }

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_plantilla = "SELECT Count(prod_plantilla.IDplantilla) As autorizados, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_puestos.IDaplica_PROD FROM prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE prod_plantilla.IDmatriz = '$la_matriz' AND prod_plantilla.IDtipo_plaza in (".$pulls.") AND prod_plantilla.IDestatus = 1 AND vac_puestos.IDarea in (1,2,3,4) GROUP BY prod_plantilla.IDpuesto ORDER BY vac_puestos.denominacion ASC ";
$plantilla = mysql_query($query_plantilla, $vacantes) or die(mysql_error());
$row_plantilla = mysql_fetch_assoc($plantilla);
$totalRows_plantilla = mysql_num_rows($plantilla);

mysql_select_db($database_vacantes, $vacantes);
$query_cierre = "SELECT * FROM prod_semana_cierre WHERE IDmatriz = '$la_matriz' AND semana = '$semana' AND anio = '$anio'";
$cierre = mysql_query($query_cierre, $vacantes) or die(mysql_error());
$row_cierre = mysql_fetch_assoc($cierre);
$totalRows_cierre = mysql_num_rows($cierre);

//repetidos
mysql_select_db($database_vacantes, $vacantes);
$query_repetidos = "SELECT Count(prod_captura.IDempleado) AS Repetidos FROM prod_captura WHERE prod_captura.IDmatriz = '$la_matriz' AND prod_captura.anio = '$anio' AND prod_captura.semana = '$semana'  GROUP BY prod_captura.IDempleado HAVING Repetidos > 1";
$repetidos = mysql_query($query_repetidos, $vacantes) or die(mysql_error());
$row_repetidos = mysql_fetch_assoc($repetidos);
$totalRows_repetidos = mysql_num_rows($repetidos);


mysql_select_db($database_vacantes, $vacantes);
$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as resultadoP FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$IDmatriz' AND IDanio = '$anio' AND IDsemana = $semana AND IDestatus = 1";
$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
$row_adicional = mysql_fetch_assoc($adicional);
$totalRows_adicional = mysql_num_rows($adicional);
$aplica_adicional = $row_adicional['resultadoP'];


// Load the tNG classes
require_once('productividad_presupuesto.php');
// presupuesto directo
$Total0 = $MontoZ; 

if ($aplica_adicional != 0) { $Total0 = $Total0 + $aplica_adicional; }

$rebase = 0;
$monto_rebase = $el_monto1 - $Total0;
if($el_monto1 > $Total0){ $rebase = 1;} 

$_diario = 0;
$_semanal = 0;
$_mensual = 0;
$_puestos = 0;

$_semanal = ($_diario / 30 ) * 7;
$_mensual = $_diario;

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
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
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
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>	
	<!-- /theme JS files -->

	<?php if ($rebase == 1) { ?> 
<script>
      function load() {
       swal({
            title: "Presupuesto semanal rebasado por <?php echo "$".number_format($monto_rebase, 2); ?>",
            text: "Por favor revisa y ajusta. ",
            confirmButtonColor: "#EF5350",
            type: "error",
            confirmButtonText: "OK",
            timer: 2000
        });
      }
      window.onload = load;
    </script>
	<?php } ?>

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
                        <?php if($totalRows_repetidos > 0) { ?>
					    <div class="alert bg-danger-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
                            <a href="productividad_captura_puesto_repetidos.php">Existen Empelados repetidos, da clic aqui para corregir.</a>
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 1)) { ?>
					    <div class="alert bg-success-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han <span class="text-semibold">Capturado</span> los registros de forma correcta. 
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
				
						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 2)) { ?>
					    <div class="alert bg-success-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han <span class="text-semibold">Enviado</span> la solicitud de Validación. 
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if($row_cierre['estatus'] == 1) { ?>
					    <div class="alert bg-success-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha <span class="text-semibold">Capturado</span> la semana de forma correcta. 
					    </div>
                        <?php } else if($row_cierre['estatus'] != 1) { ?>
					    <div class="alert bg-success-300 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Debes cerrar la <span class="text-semibold">Captura</span> de la semana, dando clic en "Solicitar Validación". 
					    </div>
                        <?php } ?>


						
					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Captura semanal de productividad</h5>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
								<ul>
								<li>Selecciona el puesto para mostrar la lista de empleados. La lista de empleados se actualiza los días jueves.</li>
								<li>Da clic en <strong>“Descargar Reporte”</strong> para descargar el detalle de pago de productividad de la semana actual.</li>
								<li><i class='icon-info3 icon-xs text-danger'></i>Puestos que se capturan desde Corporativo.</li>
								<li>El presupuesto se calcula de acuerdo con la Plantilla Autorizada y monto topado de Productividad por puesto.</li>
								<li><strong></strong></li>
								</ul>
                        
                      <p>&nbsp;</p> 
                                    
				 <!-- Statistics with progress bar -->
					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Presupuesto Semanal Productividad</h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cash3 icon-2x text-primary-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
				              	<span class="text-muted"><strong>Presupuesto: </strong><?php echo "$" . number_format(round($Total0, 2)) ."<br/>";?></span> 
								<span class="text-muted  <?php if( $rebase == 1){ echo "text text-danger"; } ?>"><strong>Gasto: </strong> <?php echo "$" . number_format(round($el_monto1, 2));?></span>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Gasto Sueldo y Productividad</h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cash2 icon-2x text-primary-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
				              	<span class="text-muted"><strong>Semana Anterior ($):</strong> <?php echo "$" . number_format($el_montoX1) ."<br/>"; ?></span>
				              	<span class="text-muted"><strong>Semana Actual ($):</strong> <?php echo "$" . number_format($el_montoX2); ?></span>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">

							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-calendar2 icon-2x text-primary-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Periodo</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Año:</strong> <?php echo $anio ."<br/>"; ?></span>
										<span class="text-muted"><strong>Semana: </strong><?php echo $semana; ?></span>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-users2  icon-2x text-primary-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Empleados</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Capturados:</strong> <?php echo $row_empleados['TCapturados'] ."<br/>"; ?></span>
							</div>
						</div>
					</div>

					<!-- /statistics with progress bar -->
					

		                       <form method="POST" action="productividad_captura.php">
								<div class="panel-body text-center alpha-grey">


					<?php if ($rebase == 1) { ?> 
					<a href="productividad_autoriza_adicionales.php" class="btn btn-danger"><i class="icon-alert position-left"></i> Ver pagos adicionales</a>
    				<?php }  ?> 

					<?php if ( $IDmatriz != 25 ) { ?>							
                            <a href="productividad_reportes_new_captura.php?IDmatriz=<?php echo $la_matriz; ?>&anio=<?php echo $anio; ?>&semana=<?php echo $semana; ?>&areas=0&tipo=0" class="btn bg-info-400"><i class="icon-file-excel position-left"></i>Descargar Reporte</a>
					<?php } else { ?> 
							<a href="productividad_reportes_new_t.php?IDmatriz=<?php echo $la_matriz; ?>&anio=<?php echo $anio; ?>&semana=<?php echo $semana; ?>&areas=0&tipo=0" class="btn bg-info-400"><i class="icon-file-excel position-left"></i>Descargar Reporte</a>
					<?php }  ?> 


    				<?php if ($rebase == 0) { ?> 
						<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn bg-primary-400"><i class="icon-notification2 position-left"></i> Solicitar Validación</button>
    				<?php } else { ?> 
								<button type="button" class="btn bg-primary-400" id="sweet_error"><i class="icon-notification2 position-left"></i> Solicitar Validación</button></td>
    				<?php }  ?> 


					<div class="form-group col-md-2">
                                             <select name="la_sucursal" class="form-control ">
                                               <option value="" <?php if (!(strcmp("", $la_sucursal))) {echo "selected=\"selected\"";} ?>>Sucursal: Todas</option>
											<?php do { ?>
                                               <option value="<?php echo $row_sucursal['IDsucursal']?>"<?php if (!(strcmp($row_sucursal['IDsucursal'], $la_sucursal))) 
											   {echo "selected=\"selected\"";} ?>><?php echo $row_sucursal['sucursal']?></option>
                                               <?php
											  } while ($row_sucursal = mysql_fetch_assoc($sucursal));
											  $rows = mysql_num_rows($sucursal);
											  if($rows > 0) {
												  mysql_data_seek($sucursal, 0);
												  $row_sucursal = mysql_fetch_assoc($sucursal);
											  } ?> </select>
                                                </div>
                                              <div class="form-group col-md-1">
                              <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                                              </div>   
								 </div>                                    
								</form>

					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Acciones</th>
                      <th>Denominacion</th>
                      <th>Sucursal</th>
                      <th>Area</th>
                      <th>Activos</th>
                      <th>Capturados</th>
							 </tr>
							</thead>
							<tbody>
									    <?php 
										$Total_a = 0;
										$Total_b = 0;
										do { 
										$IDpuesto_c = $row_detalle['IDpuesto'];
										$query_empleados_c = "SELECT Count(prod_captura.capturador) AS TCapturados, Count(prod_captura.validador) AS TValidados, Count(prod_captura.autorizador) AS TAutorizados FROM prod_captura WHERE prod_captura.semana = $semana AND prod_captura.IDmatriz = $IDmatriz AND prod_captura.anio = $anio AND IDpuesto = $IDpuesto_c";
										$empleados_c = mysql_query($query_empleados_c, $vacantes) or die(mysql_error());
										$row_empleados_c = mysql_fetch_assoc($empleados_c);
										?>
									      <tr>
                                            <td>
                                            <?php if ( $row_cierre['estatus'] != 3) { ?>


									<?php if ( $IDmatriz == 25 ) { ?>

										<?php if ( $row_detalle['modal'] == 100 ) { ?>
                                            <button type="button" class="btn btn-primary btn-xs" 
                                            onClick="window.location.href='productividad_captura_puesto_t.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>'">
                                            <i class="icon-checkmark-circle2"></i> Capturar</button></td>
                                            <?php } else {?>
                                            <button type="button" class="btn btn-primary btn-xs" 
                                            onClick="window.location.href='productividad_captura_puesto_a_t.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>'">
                                            <i class="icon-checkmark-circle2"></i> Capturar</button>
                                            <?php } ?>  

									<?php } else { ?>

										<?php if ( $row_detalle['modal'] == 100 ) { ?>
                                            <button type="button" class="btn btn-primary btn-xs" 
                                            onClick="window.location.href='productividad_captura_puesto.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>'">
                                            <i class="icon-checkmark-circle2"></i> Capturar</button></td>
                                            <?php } else {?>
                                            <button type="button" class="btn btn-primary btn-xs" 
                                            onClick="window.location.href='productividad_captura_puesto_a.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>'">
                                            <i class="icon-checkmark-circle2"></i> Capturar</button>
                                            <?php } ?>  

									<?php } ?>

									
                                            <?php } else { echo "Semana Cerrada"; } ?>											
                                            </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp;<?php if ($row_detalle['prod_captura_tipo'] == 2){echo "<i class='icon-info3 icon-xs text-danger'></i>";} ?> </td>
									        <td><?php echo $row_matriz['matriz']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['area']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['Ocupados']; ?></td>
										<td><?php echo $row_empleados_c['TCapturados'];?><?php if(($row_empleados_c['TCapturados'] == $row_detalle['Ocupados']) AND $row_empleados_c['TCapturados'] > 0){ ?>&nbsp;<i class='icon-checkmark5 icon-xs text-success'></i><?php } ?></td>
								</tr>
									      <?php 
										  $Total_a = $Total_a + $row_detalle['Ocupados'];
										  $Total_b = $Total_b + $row_empleados_c['TCapturados'];
										  } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
								</tbody>
									<tfoot> 
									<tr> 
									  <th colspan="4">Total</th>
									  <th><?php echo $Total_a; ?></th>
									  <th><?php echo $Total_b; ?></th>
									 </tr>
									</tfoot>
                   </table> 

				</div>

					<!-- /Contenido -->

					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de término de Captura</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres solicitar la validación?. Una vez solicitada, no deberás cambiar ningun dato.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="mail_valida.php?IDmatriz=<?php echo $la_matriz; ?>">Si solicitar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


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