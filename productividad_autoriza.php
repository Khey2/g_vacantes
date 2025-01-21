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
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 


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
$query_matriz = "SELECT * FROM vac_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//las variables de sesion para el filtrado
if(isset($_POST['la_matriz']) && ($_POST['la_matriz'] > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = ""; }

if(isset($_POST['el_area']) && ($_POST['el_area']  > 0)) {
$_SESSION['el_area'] = $_POST['el_area']; } else { $_SESSION['el_area'] = ""; }

if(isset($_POST['el_puesto']) && ($_POST['el_puesto']  > 0)) {
$_SESSION['el_puesto'] = $_POST['el_puesto']; } else { $_SESSION['el_puesto'] = ""; }

$la_matriz = $_SESSION['la_matriz'];
$el_puesto = $_SESSION['el_puesto'];
$el_area = $_SESSION['el_area'];

$b1	= "";
$c1 = "";
$d1 = "";

if($la_matriz > 0) {
$b1 = " AND prod_activos.IDmatriz = '$la_matriz'"; }
if($el_area > 0) {
$c1 = " AND prod_activos.IDarea = '$el_area'"; }
if($el_puesto > 0) {
$d1 = " AND prod_activos.IDpuesto = '$el_puesto'"; }

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
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT DISTINCT Count( prod_activos.IDempleado ) AS Ocupados, prod_activos.IDarea, vac_sucursal.sucursal, vac_puestos.denominacion, vac_puestos.modal, vac_matriz.matriz, vac_matriz.IDmatriz, vac_areas.area, Count( prod_captura.capturador ) AS TCapturados, Count( prod_captura.validador ) AS TValidados, Count( prod_captura.autorizador ) AS TAutorizados, prod_activos.IDpuesto, Sum( prod_captura.pago_total ) AS TTotal, Sum( prod_captura.adicional2 ) AS TAdicional  FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz RIGHT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio' WHERE vac_puestos.IDaplica_PROD = 1   " . $b1 . $c1. $d1. "  GROUP BY vac_puestos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea, vac_matriz.matriz, vac_areas.area, vac_sucursal.sucursal, prod_activos.IDpuesto, vac_puestos.IDaplica_PROD, vac_puestos.modal";  
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
									<h6 class="panel-title">Autorización semanal de productividad</h6>
								</div>

								<div class="panel-body">

                       <form method="POST" action="productividad_autoriza.php">
					<table class="table">
						<tbody>							  
							<tr>
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
							<td><div class="col-lg-9">
                                             <select name="el_area" class="form-control">
                                               <option value="" <?php if (!(strcmp("", $el_area))) {echo "selected=\"selected\"";} ?>>Área: Todas</option>
											<?php do { ?>
                                               <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $el_area)))
											   {echo "selected=\"selected\"";} ?>><?php echo $row_area['area']?></option>
                                               <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) {
												  mysql_data_seek($area, 0);
												  $row_area = mysql_fetch_assoc($area);
											  } ?> </select>
						    </div></td>
							<td>
                            <div class="col-lg-9">
                                             <select name="el_puesto" class="form-control">
                                               <option value="" <?php if (!(strcmp("", $el_puesto))) {echo "selected=\"selected\"";} ?>>Puesto: Todos</option>
                                               <?php do {  ?>
                                               <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $el_puesto))) 
											   {echo "selected=\"selected\"";} ?>><?php echo $row_puesto['denominacion']?></option>
                                               <?php
											  } while ($row_puesto = mysql_fetch_assoc($puesto));
											  $rows = mysql_num_rows($puesto);
											  if($rows > 0) {
												  mysql_data_seek($puesto, 0);
												  $row_puesto = mysql_fetch_assoc($puesto);
											  } ?></select>
						    </div>
                            </td>
                              <td>
                            <button type="submit" class="btn btn-success">Filtrar <i class="icon-arrow-right14 position-right"></i></button>	
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	


					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>Acciones</th>
                      <th>Denominacion</th>
                      <th>Sucursal</th>
                      <th>Area</th>
                      <th>Capturados</th>
                      <th>Validados</th>
                      <th>Monto Total</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php do { 	?>
									      <tr>
                                            <td>
                                            <?php if ( $row_detalle['modal'] == 100 ) { ?>
                                            <button type="button" class="btn btn-success btn-xs" onClick="window.location.href='productividad_autoriza_puesto.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'">
                                            Ver Detalle</button>
                                            <?php } else {?>
                                            <button type="button" class="btn btn-success btn-xs" onClick="window.location.href='productividad_autoriza_puesto_a.php?IDpuesto=<?php echo $row_detalle['IDpuesto']; ?>&IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'">
                                            Ver Detalle</button>
                                            <?php } ?> 
											 <button type="button" class="btn btn-success btn-xs" onClick="window.location.href='#'">
                                            Autorizar</button>                                            
                                            </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['sucursal']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['area']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['TCapturados']; ?></td>
									        <td><?php echo $row_detalle['TValidados']; ?></td>
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
<?php
mysql_free_result($variables);

mysql_free_result($activos);

mysql_free_result($monto1);
?>
