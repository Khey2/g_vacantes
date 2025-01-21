<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');


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
$IDmatriz = $_GET['IDmatriz'];
$IDusuario = $row_usuario['IDusuario'];
$las_matrizes = $row_usuario['IDmatrizes'];

$la_fecha =  date("Y-m-d");
$semana = date("W", strtotime($la_fecha)) -1; //la semana empieza ayer 
$semana_previa = $semana - 1;
if ($semana == 1) {$semana_previa = 52;}

$anio = $row_variables['anio'];
$anio_previo = $anio;
if ($semana == 1) {$anio_previo = $anio - 1;}

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];
$nivel = $_SESSION['kt_login_level'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_seman = "SELECT DISTINCT prod_captura.semana,  prod_captura.anio, prod_captura.IDmatriz FROM prod_captura WHERE prod_captura.IDmatriz = '$IDmatriz' AND semana > 0 GROUP BY prod_captura.semana, prod_captura.anio";
$seman = mysql_query($query_seman, $vacantes) or die(mysql_error());
$row_seman = mysql_fetch_assoc($seman);
$totalRows_seman = mysql_num_rows($seman);

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


// AND prod_captura.anio = '$anio'
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
									<h6 class="panel-title">Comparativo Productividad</h6>
								</div>

								<div class="panel-body">
									<div class="tabbable">

										<div class="tab-content">
											<div class="tab-pane active" id="colored-justified-tab1">
                                            
                                            A continuación se muestan el comprativo de gasto:<br/>
											<p>&nbsp;</p>
											<button type="button" class="btn btn-info" onClick="window.location.href='productividad_vista_presupuesto.php'"> Regresar</button>
											<p>&nbsp;</p>
                                            
											<table class="table table-condensed datatable-button-html5-columns">
      							              <thead> 
                                                <tr class="bg-teal-400">
                                                  <th>Matriz</th>
                                                  <th>Año</th>
                                                  <th>Semana</th>
                                                  <th>Empleados</th>
                                                  <th>Cajas</th>
                                                  <th>Estiba</th>
                                                  <th>Gasto</th>
                                                  <th>Gasto Prod</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php do { 
												
												$semana = $row_seman['semana'];
												$anioo = $row_seman['anio'];

												mysql_select_db($database_vacantes, $vacantes);
												$query_anterior = "SELECT vac_matriz.matriz, sum(prod_captura.pago_total + ((prod_captura.sueldo_total / 30 ) * 7)) AS Total, SUM(prod_captura.pago_total) AS TotalS, SUM(prod_captura.adicional) AS adicionales, SUM(prod_captura.adicional2) AS adicionales2, sum(prod_captura.reci + prod_captura.carg + prod_captura.dist) AS Cajas, sum(prod_captura.esti) As Ests, COUNT(prod_captura.IDempleado) As Emples FROM prod_captura INNER JOIN vac_matriz ON  prod_captura.IDmatriz = vac_matriz.IDmatriz WHERE prod_captura.anio = '$anioo' AND prod_captura.semana = '$semana' AND prod_captura.IDmatriz = '$IDmatriz' GROUP BY prod_captura.IDmatriz"; 
												$anterior = mysql_query($query_anterior, $vacantes) or die(mysql_error());
												$row_anterior = mysql_fetch_assoc($anterior);
												$totalRows_anterior = mysql_num_rows($anterior);
												?>
                                                  <tr>
                                                    <td><?php echo $row_anterior['matriz']; ?>&nbsp; </td>
                                                    <td><?php echo $row_seman['anio']; ?>&nbsp; </td>
                                                    <td><?php echo $row_seman['semana']; ?>&nbsp; </td>
                                                    <td><?php echo $row_anterior['Emples']; ?>&nbsp; </td>
                                                    <td><?php echo number_format($row_anterior['Cajas']); ?>&nbsp;</td>
                                                    <td><?php echo number_format($row_anterior['Ests']); ?>&nbsp; </td>
                                                    <td>$<?php echo number_format($row_anterior['Total'] + $row_anterior['adicionales'] +$row_anterior['adicionales2']); ?></td>
                                                    <td>$<?php echo number_format($row_anterior['TotalS'] + $row_anterior['adicionales'] +$row_anterior['adicionales2']); ?></td>
                                                  </tr>
                                                  <?php } while ($row_seman = mysql_fetch_assoc($seman)); ?>
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