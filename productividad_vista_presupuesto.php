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
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));


//las variables de sesion para el filtrado
if(isset($_POST['la_semana'])) { $_SESSION['la_semana'] = $_POST['la_semana']; } 
else { $_SESSION['la_semana'] = $semana; }

$la_semana = $_SESSION['la_semana'];

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

$query_semanas = "SELECT * FROM prod_semanas";
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


//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT Count( prod_activos.IDempleado ) AS Ocupados, Count( prod_captura.capturador ) AS TCapturados, Count( prod_captura.validador ) AS TValidados, Count( prod_captura.autorizador ) AS TAutorizados, Sum( prod_captura.pago_total ) AS TTotal, Sum( prod_captura.adicional2 ) AS TAdicional, Sum( prod_captura.sueldo_total ) AS TSueldo, vac_matriz.matriz, vac_matriz.IDmatriz, vac_puestos.IDpuesto FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado WHERE vac_puestos.IDaplica_PROD = 1 AND prod_captura.semana = '$la_semana'  AND prod_captura.anio = '$anio' GROUP BY prod_activos.IDmatriz";  
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
	




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

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Autorización semanal de productividad</h6>
								</div>

								<div class="panel-body">
                                <p><b>Semana: </b><?php echo $la_semana; ?></p>
                                <p><b>Año: </b><?php echo $anio; ?></p>
                                </div>                                    
                                    

				<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>Sucursal</th>
                      <th>Activos</th>
                      <th>Capturados</th>
                      <th>Validados</th>
                      <th>Autorizados</th>
                      <th>Gasto Prod.</th>
                      <th>Presupuesto</th>
               		</tr>
                    </thead>
                    <tbody>
								<?php do { 	?>
								  <tr>
									<?php 
									$matriz = $row_detalle['IDmatriz'];
									mysql_select_db($database_vacantes, $vacantes);
									$query_cierre = "SELECT * FROM prod_semana_cierre WHERE IDmatriz = '$matriz' AND semana = '$la_semana' AND anio = '$anio'";
									$cierre = mysql_query($query_cierre, $vacantes) or die(mysql_error());
									$row_cierre = mysql_fetch_assoc($cierre);
									$totalRows_cierre = mysql_num_rows($cierre);
									$cerrar = $row_cierre['autoriza'];

									mysql_select_db($database_vacantes, $vacantes);
									$query_activos = "SELECT Count(prod_activos.IDempleado) AS Ocupados FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE prod_activos.IDmatriz = '$matriz' AND vac_puestos.IDaplica_PROD = 1 GROUP BY prod_activos.IDmatriz";
									$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
									$row_activos = mysql_fetch_assoc($activos);

									mysql_select_db($database_vacantes, $vacantes);
									$query_monto1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.adicional) AS Monto4, Sum(prod_captura.sueldo_total) AS Monto3, Sum(prod_captura.bono_asistencia) AS Monto5, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = '$anio' AND prod_captura.semana = '$la_semana' AND prod_captura.IDmatriz = '$matriz' GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
									$monto1 = mysql_query($query_monto1, $vacantes) or die(mysql_error());
									$row_monto1 = mysql_fetch_assoc($monto1);
									$totalRows_monto1 = mysql_num_rows($monto1);
									$el_monto1 = $row_monto1['Monto'] + $row_monto1['Monto2'] + $row_monto1['Monto4'] +  $row_monto1['Monto5'];

									//consiera pull en presupuesto
									if($row_matriz['incluye_pull'] == 1) {$pulls = '1,2';} else { $pulls = '1'; }

									$fecha_filtro = date('Y/m/d', strtotime('monday -1 week'));
									mysql_select_db($database_vacantes, $vacantes);
									$query_presupuesto = "
									SELECT Count(prod_plantilla.IDplantilla) AS Autorizada, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_matriz.matriz, prod_garantias.garantia, prod_garantias.monto_asistencia, vac_tabulador.sueldo_diario FROM prod_plantilla LEFT JOIN vac_tabulador ON prod_plantilla.IDpuesto = vac_tabulador.IDpuesto AND vac_tabulador.IDmatriz = '$matriz' LEFT JOIN prod_garantias ON prod_plantilla.IDpuesto = prod_garantias.IDpuesto AND prod_garantias.IDmatriz = '$matriz' LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz WHERE prod_plantilla.IDmatriz = '$matriz' AND vac_puestos.IDaplica_PROD = 1 AND vac_puestos.IDarea IN (1,2,3,4) AND prod_plantilla.IDtipo_plaza in (".$pulls.") AND prod_plantilla.IDestatus = 1 AND (DATE(fecha_inicio) <= '$fecha_filtro') AND ( DATE(fecha_fin) > '$fecha_filtro' OR DATE(fecha_fin) = '0000-00-00' OR DATE(fecha_fin) IS NULL) AND ( DATE(fecha_congelada) > '$fecha_filtro' OR DATE(fecha_congelada) = '0000-00-00' OR DATE(fecha_congelada) IS NULL) GROUP BY prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDtipo_plaza ORDER BY vac_puestos.denominacion ASC";
									$presupuesto = mysql_query($query_presupuesto, $vacantes) or die(mysql_error());
									$row_presupuesto = mysql_fetch_assoc($presupuesto);
									$totalRows_presupuesto = mysql_num_rows($presupuesto);

									mysql_select_db($database_vacantes, $vacantes);
									$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as resultadoP FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$matriz' AND IDanio = '$anio' AND IDsemana = $la_semana AND IDestatus = 1";
									$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
									$row_adicional = mysql_fetch_assoc($adicional);
									$totalRows_adicional = mysql_num_rows($adicional);
									$aplica_adicional = $row_adicional['resultadoP'];

									// presupuesto directo
									$Total0 = 0; 
									do {
												$Total0 = $Total0 + ( ($row_presupuesto['garantia'] * ($row_presupuesto['sueldo_diario'] * 7) / 100 ) + $row_presupuesto['monto_asistencia']) * $row_presupuesto['Autorizada'];  
												$autorizads = $autorizads + $row_presupuesto['Autorizada']; 
										} while ($row_presupuesto = mysql_fetch_assoc($presupuesto)); 

									if ($aplica_adicional != 0) { $Total0 = $Total0 + $aplica_adicional; }

									$rebase = 0;
									if($el_monto1 > $Total0){ $rebase = 1;} 											
									?>
									<td><?php echo $row_detalle['matriz'];  ?>&nbsp; </td>
									<td><?php echo $row_activos['Ocupados'];  ?>&nbsp; </td>
									<td><?php echo $row_detalle['TCapturados']; ?></td>
									<td><?php echo $row_detalle['TValidados']; ?>&nbsp; <?php if($cerrar > 1) { ?><i class="icon-flag3"></i><?php } ?></td>
									<td><?php echo $row_detalle['TAutorizados']; ?></td>
									<td><a href="prod_comparativo_s1.php?IDmatriz=<?php echo $matriz; ?>"><?php echo "$" . number_format($el_monto1); ?></a></td>
									<td><span class="text <?php if( $rebase == 1){ echo "text-danger"; } ?>"><strong></strong> <?php echo "$" . number_format(round($Total0, 2));?></span>
								</td>
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