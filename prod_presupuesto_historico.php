	<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php'); 

setlocale(LC_MONETARY, 'es_MX');
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
$desfase = $row_variables['dias_desfase'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDusuario = $row_usuario['IDusuario'];
$las_matrizes = $row_usuario['IDmatrizes'];
$la_fecha = date("Y-m-d"); // la fecha actual

$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$semana_previa = $semana - 1;
if ($semana == 1) {$semana_previa = 52;}

$anio = $row_variables['anio'];
$el_anio = $row_variables['anio'];
$anio_previo = $anio;
if ($semana == 1) {$anio_previo = $anio - 1;}

$nivel = $_SESSION['kt_login_level'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz  WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

// Filtros
mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM prod_meses_presupesto WHERE visible = 1";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

if (isset($_POST['IDmatriz'])) {$IDmatriz =  $_POST['IDmatriz'];} 

?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
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
	<script src="global_assets/js/plugins/tables/datatables/xdatatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
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
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		<?php require_once('assets/pheader.php'); ?>
<!-- Content area -->
				<div class="content">

					<!-- Colored tabs -->
					<div class="row">
						
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Presupuesto semanal de productividad</h5>
								</div>
								
								
				<form method="POST" action="prod_presupuesto_historico.php">
                	<table class="table">
						<tbody>							  
							<tr>
							<td>
                     <div class="col-lg-12">
                             <select class="form-control" name="IDmatriz">
                            <?php do { ?>
                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $IDmatriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
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


								<div class="panel-body">
									<div class="tabbable">

										<div class="tab-content">
											<div class="tab-pane active" id="colored-justified-tab1">
                                            <p>Presupuesto asignado:</p>
																		
											
										<table class="table table-condensed datatable-button-html5-columns">
      							              <thead> 
                                                <tr class="bg-teal-400">
                                                  <th>Matriz</th>
                                                  <th>Semana</th>
                                                  <th>Semana</th>
                                                  <th>Autorizados</th>
                                                  <th>Presupuesto</th>
                                                  <th>Gasto</th>
                                                  <th>Diferencia</th>
                                                </tr>
                                                </thead>
                                                <tbody>
												<?php
											$fecha_filtro = date('Y/m/d', strtotime('2024-01-01 monday'));
											$semana_pr = date("W", strtotime($fecha_filtro));

											do { 											
												$Total0 = 0; 
												$autorizads = 0; 

												//consiera pull en presupuesto
												if($row_matriz['incluye_pull'] == 1) {$pulls = '1,2';} else { $pulls = '1'; }

												mysql_select_db($database_vacantes, $vacantes);
												$query_presupuesto = "SELECT Count(prod_plantilla.IDplantilla) AS Autorizada, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_matriz.matriz, prod_garantias.garantia, prod_garantias.monto_asistencia, vac_tabulador.sueldo_diario FROM prod_plantilla LEFT JOIN vac_tabulador ON prod_plantilla.IDpuesto = vac_tabulador.IDpuesto AND vac_tabulador.IDmatriz = '$IDmatriz' LEFT JOIN prod_garantias ON prod_plantilla.IDpuesto = prod_garantias.IDpuesto AND prod_garantias.IDmatriz = '$IDmatriz' LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz WHERE prod_garantias.IDpresupuesto = 1 AND prod_plantilla.IDmatriz = '$IDmatriz' AND vac_puestos.IDaplica_PROD = 1 AND vac_puestos.IDarea IN (1,2,3,4) AND prod_plantilla.IDtipo_plaza in (".$pulls.") AND prod_plantilla.IDestatus = 1 AND (DATE(fecha_inicio) <= '$fecha_filtro') AND ( DATE(fecha_fin) > '$fecha_filtro' OR DATE(fecha_fin) = '0000-00-00' OR DATE(fecha_fin) IS NULL) AND ( DATE(fecha_congelada) > '$fecha_filtro' OR DATE(fecha_congelada) = '0000-00-00' OR DATE(fecha_congelada) IS NULL) GROUP BY prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDtipo_plaza ORDER BY vac_puestos.denominacion ASC";
												$presupuesto = mysql_query($query_presupuesto, $vacantes) or die(mysql_error());
												$row_presupuesto = mysql_fetch_assoc($presupuesto);
																								
												do { 
												$Total0 = $Total0 + ( ($row_presupuesto['garantia'] * ($row_presupuesto['sueldo_diario'] * 7) / 100 ) + $row_presupuesto['monto_asistencia']) * $row_presupuesto['Autorizada'];  
												$autorizads = $autorizads + $row_presupuesto['Autorizada']; 
																								
												} while ($row_presupuesto = mysql_fetch_assoc($presupuesto)); 
												
												//total gastado
												mysql_select_db($database_vacantes, $vacantes);
												$query_monto1 = "SELECT Sum(prod_captura.pago_total) As Monto, prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS Monto2, Sum(prod_captura.bono_asistencia) AS Monto5,  Sum(prod_captura.adicional) AS Monto4, Sum(prod_captura.sueldo_total) AS Monto3, prod_captura.semana FROM prod_captura WHERE  prod_captura.anio = $anio AND prod_captura.semana = $semana_pr AND prod_captura.IDmatriz = $IDmatriz GROUP BY prod_captura.IDmatriz, prod_captura.semana ";
												$monto1 = mysql_query($query_monto1, $vacantes) or die(mysql_error());
												$row_monto1 = mysql_fetch_assoc($monto1);
												$totalRows_monto1 = mysql_num_rows($monto1);
												$el_monto1 = $row_monto1['Monto'] + $row_monto1['Monto2'] + $row_monto1['Monto4'] + $row_monto1['Monto5'];		

												mysql_select_db($database_vacantes, $vacantes);
												$query_matriz_c = "SELECT * FROM vac_matriz  WHERE IDmatriz = '$IDmatriz'";
												$matriz_c = mysql_query($query_matriz_c, $vacantes) or die(mysql_error());
												$row_matriz_c = mysql_fetch_assoc($matriz_c);
												$totalRows_matriz_c = mysql_num_rows($matriz_c);											
												?>												
                                                  <tr>
                                                    <td><?php echo $row_matriz_c['matriz']; ?></td>
                                                    <td><?php echo sprintf("%02d", $semana_pr); ;?></td>
                                                    <td><?php echo $fecha_filtro; ?></td>
                                                    <td><?php echo $autorizads; ?></td>
                                                    <td>$<?php echo number_format($Total0); ?></td>
                                                    <td>$<?php echo number_format($el_monto1); ?></td>
                                                    <td>$<?php echo number_format($Total0 - $el_monto1); ?></td>
                                                  </tr>
											<?php 	$semana_pr = $semana_pr + 1;
													$fecha_filtro = date('Y/m/d', strtotime($fecha_filtro.' + 7 days'));
													
													} while ($semana_pr <= $semana); ?>                                        
												</tbody>
                                              </table>
                                              <br>
                                              
                                              </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /colored tabs -->




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