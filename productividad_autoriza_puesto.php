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

$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio =  $_SESSION['el_anio'];
$anio_previo = $anio;
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$semana_previa = $semana - 1;
if ($semana == 1) {$semana_previa = 52;}
if ($semana == 1) {$anio_previo = $anio - 1;}

if(!isset($_SESSION['la_semana'])) { $_SESSION['la_semana'] = $semana; } 
$la_semana = $_SESSION['la_semana'];

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
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

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

//las variables de sesion para el filtrado
if(isset($_GET['IDmatriz'])) {
$_SESSION['la_matriz'] = $_GET['IDmatriz']; } 
else if(isset($_POST['la_matriz']) && ($_POST['la_matriz'] > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; } 
else { $_SESSION['la_matriz'] = $IDmatriz; }


$IDarea = '1,2,3,4';
if(isset($_GET['IDarea'])) {
$_SESSION['el_area'] = $_GET['IDarea']; } 
else if(isset($_POST['el_area']) && ($_POST['el_area'] > 0)) {
$_SESSION['el_area'] = $_POST['el_area']; } 
else { $_SESSION['el_area'] = $IDarea; }

$la_matriz = $_SESSION['la_matriz'];
$el_area = $_SESSION['el_area'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$la_matriz' AND prod_activos.IDaplica_PROD = 1  AND prod_activos.IDaplica_SED = 0";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT DISTINCT Count( prod_activos.IDempleado ) AS Ocupados, prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.modal, vac_matriz.matriz, vac_matriz.IDmatriz, vac_areas.area, Count( prod_captura.capturador ) AS TCapturados, Count( prod_captura.validador ) AS TValidados, Count( prod_captura.autorizador ) AS TAutorizados, prod_activos.IDpuesto, Sum( prod_captura.pago_total ) AS TTotal, Sum( prod_captura.adicional2 ) AS TAdicional, Sum( prod_captura.bono_asistencia ) AS TAdicional3, vac_puestos.prod_captura_tipo  FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$la_semana'  AND prod_captura.anio = '$anio' WHERE vac_puestos.IDaplica_PROD = 1 AND prod_activos.IDmatriz = '$la_matriz' AND prod_activos.IDarea IN ($mis_areas) GROUP BY vac_puestos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea, vac_matriz.matriz, vac_areas.area, prod_activos.IDpuesto, vac_puestos.IDaplica_PROD, vac_puestos.modal "; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

//Autorizados, Capturados y Validados
mysql_select_db($database_vacantes, $vacantes);
$query_empleados = "SELECT Count(prod_captura.capturador) AS TCapturados, Count(prod_captura.validador) AS TValidados, Count(prod_captura.autorizador) AS TAutorizados FROM prod_captura WHERE prod_captura.semana = '$la_semana'  AND prod_captura.anio = '$anio' AND prod_captura.IDmatriz = '$la_matriz'";
$empleados = mysql_query($query_empleados, $vacantes) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);
$totalRows_empleados = mysql_num_rows($empleados);


//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_monto1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.bono_asistencia) AS Monto5,  Sum(prod_captura.adicional3) AS Monto6, Sum(prod_captura.adicional) AS Monto4, Sum(prod_captura.sueldo_total) AS Monto3, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$anio' AND prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$la_matriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
$monto1 = mysql_query($query_monto1, $vacantes) or die(mysql_error());
$row_monto1 = mysql_fetch_assoc($monto1);
$totalRows_monto1 = mysql_num_rows($monto1);
$el_monto1 = $row_monto1['Monto'] + $row_monto1['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'] + $row_monto1['Monto6'];


//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_montoX1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.bono_asistencia) AS Monto5, Sum(prod_captura.adicional3) AS Monto6, Sum(prod_captura.adicional) AS Monto4, Sum(prod_captura.sueldo_total) AS Monto3, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$anio_previo' AND prod_captura.semana = '$semana_previa' AND prod_captura.IDmatriz = '$la_matriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
$montoX1 = mysql_query($query_montoX1, $vacantes) or die(mysql_error());
$row_montoX1 = mysql_fetch_assoc($montoX1);
$totalRows_montoX1 = mysql_num_rows($montoX1);
$el_montoX1 = $row_montoX1['Monto'] + $row_montoX1['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'] + $row_monto1['Monto6'];

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_montoX2 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.bono_asistencia) AS Monto5, Sum(prod_captura.adicional3) AS Monto6, Sum(prod_captura.adicional) AS Monto4, Sum(prod_captura.sueldo_total) AS Monto3, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$anio' AND prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$la_matriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
$montoX2 = mysql_query($query_montoX2, $vacantes) or die(mysql_error());
$row_montoX2 = mysql_fetch_assoc($montoX2);
$totalRows_montoX2 = mysql_num_rows($montoX2);
$el_montoX2 = $row_montoX2['Monto'] + $row_montoX2['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'] + $row_monto1['Monto6'];

//consiera pull en presupuesto
if($row_matriz['incluye_pull'] == 1) {$pulls = '1,2';} else { $pulls = '1'; }

//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_plantilla = "SELECT Count(prod_plantilla.IDplantilla) As autorizados, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_puestos.IDaplica_PROD FROM prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE prod_plantilla.IDmatriz = '$la_matriz' AND prod_plantilla.IDtipo_plaza in (".$pulls.") AND prod_plantilla.IDestatus = 1 AND vac_puestos.IDarea in (1,2,3,4) GROUP BY prod_plantilla.IDpuesto ORDER BY vac_puestos.denominacion ASC ";
$plantilla = mysql_query($query_plantilla, $vacantes) or die(mysql_error());
$row_plantilla = mysql_fetch_assoc($plantilla);
$totalRows_plantilla = mysql_num_rows($plantilla);

mysql_select_db($database_vacantes, $vacantes);
$query_cierre = "SELECT * FROM prod_semana_cierre WHERE IDmatriz = '$la_matriz' AND semana = '$la_semana'  AND anio = '$anio'";
$cierre = mysql_query($query_cierre, $vacantes) or die(mysql_error());
$row_cierre = mysql_fetch_assoc($cierre);
$totalRows_cierre = mysql_num_rows($cierre);

$_diario = 0;
$_semanal = 0;
$_mensual = 0;
$_puestos = 0;

$_diario = 0;
$_semanal = 0;
$_mensual = 0;
$_puestos = 0;

do { 
$IDpuesto = $row_plantilla['IDpuesto'];
$autorizados = $row_plantilla['autorizados'];

$query_costo = "SELECT * FROM vac_tabulador WHERE IDpuesto = '$IDpuesto' AND IDmatriz = '$la_matriz'";
$costo = mysql_query($query_costo, $vacantes) or die(mysql_error());
$row_costo = mysql_fetch_assoc($costo);
$sueldo_diario = $row_costo['sueldo_diario'];

$_diario = $_diario + ($sueldo_diario * $autorizados); 
$_puestos = $_puestos + $row_plantilla['autorizados'];

} while ($row_plantilla = mysql_fetch_assoc($plantilla));

$_semanal = ($_diario / 30 ) * 7;
$_mensual = $_diario;


//presupuesto
$fecha_filtro = date('Y/m/d', strtotime('monday -1 week'));
// Load the tNG classes
require_once('productividad_presupuesto.php');
// presupuesto directo
$Total0 = $MontoZ; 

mysql_select_db($database_vacantes, $vacantes);
$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as resultadoP FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$la_matriz' AND IDanio = '$anio' AND IDsemana = $semana AND IDestatus = 1";
$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
$row_adicional = mysql_fetch_assoc($adicional);
$totalRows_adicional = mysql_num_rows($adicional);
$aplica_adicional = $row_adicional['resultadoP'];


if ($aplica_adicional != 0) { $Total0 = $Total0 + $aplica_adicional; }

$rebase = 0;
if($el_monto1 > $Total0){ $rebase = 1;} 


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
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /theme JS files -->

    <?php if ($rebase == 1) { ?> 
	<script>
      function load() {
       swal({
            title: "Presupuesto semanal rebasado",
            text: "Por favor revisa y ajusta.",
            confirmButtonColor: "#EF5350",
            type: "error",
            confirmButtonText: "OK"
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
									<h6 class="panel-title">Autorización semanal de productividad</h6>
								</div>

                                                      <!-- Statistics with progress bar -->
					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Presupuesto Semanal</h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cash3 icon-2x text-success-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-success-400" style="width: 100%">
									</div>
								</div>
				              	<span class="text-muted"><strong>Presupuesto: </strong><?php echo "$" . number_format(round($Total0, 2)) ."<br/>";?></span> 
								<span class="text-muted <?php if( $rebase == 1){ echo "text text-danger"; } ?>"><strong>Gasto: </strong> <?php echo "$" . number_format(round($el_monto1, 2));?></span>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Gasto Sueldo y Productividad</h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cash2 icon-2x text-success-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-success-400" style="width: 100%">
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
										<i class="icon-calendar2 icon-2x text-success-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Periodo</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-success" style="width: 100%">
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
										<i class="icon-users2  icon-2x text-success-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Empleados</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-success-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Capturados:</strong> <?php echo $row_empleados['TCapturados'] ."<br/>"; ?></span>
										<span class="text-muted"><strong>Validados:</strong> <?php echo $row_empleados['TValidados'] ."<br/>"; ?></span>
										<span class="text-muted"><strong>Autorizados:</strong> <?php echo $row_empleados['TAutorizados']; ?></span>
							</div>
						</div>
					</div>

					<!-- /statistics with progress bar -->


								<div class="panel-body">
                                <p>Seleccione el Área para ver el detalle por Puesto.</br>
                                De clic en Autorizar para autorizar el monto para todos los puestos de la Sucursal.</p>
                        <p><i class='icon-info3 icon-xs text-danger'></i>Puestos que se capturan desde Corporativo.</p>
                        <p><?php if ( $row_cierre['estatus'] == 3)  { echo "Semana Cerrada"; } ?></p>

								<div class="panel-body text-center alpha-grey">
									<a href="prod_autoriza_puesto2.php?IDmatriz=<?php echo $la_matriz; ?>" class="btn bg-success-400">
										<i class="icon-checkmark-circle2 position-left"></i>Autorizar Puestos</a>
					<?php if ( $IDmatriz  !=  25 ) { ?>							
                            <a href="productividad_reportes_new.php?IDmatriz=<?php echo $la_matriz; ?>&anio=<?php echo $anio; ?>&semana=<?php echo $semana; ?>&areas=0&tipo=0" class="btn bg-info-400"><i class="icon-file-excel position-left"></i>Descargar Reporte</a>
					<?php } else { ?> 
							<a href="productividad_reportes_new.php?IDmatriz=<?php echo $la_matriz; ?>&anio=<?php echo $anio; ?>&semana=<?php echo $semana; ?>&areas=0&tipo=0" class="btn bg-info-400"><i class="icon-file-excel position-left"></i>Descargar Reporte</a>
					<?php }  ?> 

								</div>                                    

								
								
                       <form method="POST" action="productividad_autoriza_puesto.php">

					<table class="table">
						<tbody>							  
							<tr>
							<td> <div class="col-lg-9 no-prints">
										<select name="la_matriz" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz: Todas</option>
                                          <?php do {  ?>
                                           <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
										   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
											<?php
                                            } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                                              $rows = mysql_num_rows($lmatriz);
                                              if($rows > 0) {
                                                  mysql_data_seek($lmatriz, 0);
                                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
                                              } ?></select>
										</div>
                                    </td>
									<td>
                                <button type="submit" class="btn btn-success">Filtrar <i class="icon-filter3  position-right"></i></button>	
                            <button type="button" class="btn btn-info" onClick="window.location.href='productividad_autoriza_sucursal.php?IDmatriz=<?php echo $la_matriz; ?>'"><i class="icon-arrow-left52"></i> Regresar</button>
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	



					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
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


											<?php if ( $row_detalle['IDmatriz'] == 25 ) { 	
											$puesto = $row_detalle['IDpuesto']; ?>
										
                                            <?php if  ($row_detalle['modal'] != 100) { ?>
                                            <button type="button" class="btn btn-info btn-xs" onClick="window.location.href='productividad_autoriza_puesto_a_t.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'"><i class="icon-arrow-right6"></i> Ver Empleados</button>
                                            <?php } else { 	?>
                                            <button type="button" class="btn btn-info btn-xs" onClick="window.location.href='productividad_autoriza_puesto_t.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'"><i class="icon-arrow-right6"></i> Ver Empleados</button>
                                            <?php } ?>

											<?php } else { ?>
																						
											<?php if  ($row_detalle['modal'] != 100) { ?>
                                            <button type="button" class="btn btn-info btn-xs" onClick="window.location.href='productividad_autoriza_empleado_a.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'"><i class="icon-arrow-right6"></i> Ver Empleados</button>
                                            <?php } else { 	?>
                                            <button type="button" class="btn btn-info btn-xs" onClick="window.location.href='productividad_autoriza_empleado.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'"><i class="icon-arrow-right6"></i> Ver Empleados</button>
                                            <?php } ?>

											<?php } ?>
                                            
											<?php
											mysql_select_db($database_vacantes, $vacantes);
											$query_detalle2 = "SELECT Count( prod_captura.capturador ) AS TCapturados, Count( prod_captura.validador ) AS TValidados, Count( prod_captura.autorizador ) AS TAutorizados FROM prod_captura WHERE prod_captura.IDpuesto = '$puesto' AND prod_captura.semana = '$la_semana'  AND prod_captura.anio = '$anio' AND prod_captura.IDmatriz = '$IDmatriz'"; 
											$detalle2 = mysql_query($query_detalle2, $vacantes) or die(mysql_error());
											$row_detalle2 = mysql_fetch_assoc($detalle2);
											$totalRows_detalle2 = mysql_num_rows($detalle2);
											
											if(($row_detalle2['TAutorizados'] == $row_detalle2['TCapturados']) AND ($row_detalle2['TAutorizados'] == $row_detalle2['TValidados']) AND $row_detalle2['TCapturados'] != 0) { ?>
                                            <button type="button" class="btn btn-success btn-xs" 
                                            onClick="window.location.href='prod_autoriza_puesto.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>'">
                                            Autorizada <i class="icon-checkmark-circle2"></i></button>
											<?php } else  { ?>
												<button type="button" class="btn btn-warning btn-xs" 
                                            onClick="window.location.href='prod_autoriza_puesto.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>'">
                                            Autorizar <i class="icon-checkmark-circle2"></i></button>
											<?php } ?>      
                                            
                                            <?php
                                            mysql_select_db($database_vacantes, $vacantes);
                                            $query_activos = "SELECT Count(prod_activos.IDempleado) AS Ocupados FROM prod_activos 
											LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto 
											LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE 
											prod_activos.IDmatriz = '$la_matriz' AND vac_puestos.IDaplica_PROD = 1  AND prod_activos.IDpuesto = '$puesto'
											GROUP BY prod_activos.IDmatriz";
                                            $activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
                                            $row_activos = mysql_fetch_assoc($activos);
											?>

                                          </td>
									        <td><?php echo $row_detalle['matriz']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['area']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp; <?php if ($row_detalle['prod_captura_tipo'] == 2){echo "<i class='icon-info3 icon-xs text-danger'></i>";} ?></td>
									        <td><?php echo $row_activos['Ocupados']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['TCapturados']; ?></td>
									        <td><?php echo $row_detalle['TValidados']; ?></td>
									        <td><?php echo $row_detalle['TAutorizados']; ?><?php if(($row_detalle['TAutorizados'] == $row_detalle['TValidados']) AND $row_detalle['TAutorizados'] > 0){ ?>&nbsp;<i class='icon-checkmark5 icon-xs text-success'></i><?php } ?></td>
									        <td><?php echo "$" . number_format($row_detalle['TTotal'] + $row_detalle['TAdicional'] + $row_detalle['TAdicional3']); ?></td>
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