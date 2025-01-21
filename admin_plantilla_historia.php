<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


if(isset($_POST['el_tipo'])) {$el_tipo = $_POST['el_tipo'];} else {$el_tipo = 1;}
if(isset($_POST['la_matriz'])) {$la_matriz = $_POST['la_matriz'];} else {$la_matriz = $IDmatriz;}
if(isset($_POST['el_estatus']) AND $_POST['el_estatus'] == 0) {$el_estatus = '';}
else if(isset($_POST['el_estatus']) AND $_POST['el_estatus'] == 1) {$el_estatus = ' AND prod_plantilla.fecha_fin IS NULL AND prod_plantilla.fecha_congelada IS NULL ';}
else if(isset($_POST['el_estatus']) AND $_POST['el_estatus'] == 2) {$el_estatus = ' AND (prod_plantilla.fecha_fin IS NOT NULL OR prod_plantilla.fecha_congelada IS NOT NULL) ';}



if(isset($_GET['fecha_filtro'])) {$fecha_filtro = $_GET['fecha_filtro'];} else {$fecha_filtro = date("Y-m-d");}

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = '$la_matriz'";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz  WHERE IDmatriz = '$la_matriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz2 = "SELECT * FROM vac_matriz ";
$lmatriz2 = mysql_query($query_lmatriz2, $vacantes) or die(mysql_error());
$row_lmatriz2 = mysql_fetch_assoc($lmatriz2);
$totalRows_lmatriz2 = mysql_num_rows($lmatriz2);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos ORDER BY denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);


//$y1 = substr( $fecha_filtro, 6, 4 );
//$m1 = substr( $fecha_filtro, 3, 2 );
//$d1 = substr( $fecha_filtro, 0, 2 );
//$fecha_inicio =  $y1."-".$m1."-".$d1;

//$fini_mes1 = new DateTime($fecha_inicio);
//$fini_mes1->modify('first day of this month');
//$fini_mes1k = $fini_mes1->format('Y/m/d'); 

//$fter_mes1 = new DateTime($fecha_inicio);
//$fter_mes1->modify('last day of this month');
//$fter_mes1k = $fter_mes1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT prod_plantilla.IDplantilla, prod_plantilla.IDpuesto, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz,  prod_plantilla.IDmotivo,  prod_plantilla.observaciones, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion,  vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_matriz.matriz FROM  prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz WHERE prod_plantilla.IDmatriz = $la_matriz ".$el_estatus." ORDER BY prod_plantilla.fecha_inicio DESC";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

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

$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);



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

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/natural_sort.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    
    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
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

							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Cambios Plantilla </h6>
								</div>

							<div class="panel-body">
								<p>A continuación se muestran los cambios de plantilla.</br>  
  
  				<form method="POST" action="admin_plantilla_historia.php">
                	<table class="table">
						<tbody>							  
							<tr>
							<td>
                             <select class="form-control" name="la_matriz">
                            <?php do { ?>
                               <option value="<?php echo $row_lmatriz2['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz2['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz2['matriz']?></option>
                               <?php
                              } while ($row_lmatriz2 = mysql_fetch_assoc($lmatriz2));
                              $rows = mysql_num_rows($lmatriz2);
                              if($rows > 0) {
                                  mysql_data_seek($lmatriz2, 0);
                                  $row_lmatriz2 = mysql_fetch_assoc($lmatriz2);
                              } ?> 
                              </select>
                            </td>
							<td><select class="form-control" name="el_tipo">
                                               <option value="0"<?php if (!(strcmp($el_tipo, '1,2'))) {echo "selected=\"selected\"";} ?>>Todas</option>
                                               <option value="1"<?php if (!(strcmp($el_tipo, 1))) {echo "selected=\"selected\"";} ?>>Planta</option>
                                               <option value="2"<?php if (!(strcmp($el_tipo, 2))) {echo "selected=\"selected\"";} ?>>Temporal</option>
                                            </select>
                             </td>
							<td><select class="form-control" name="el_estatus">
                                               <option value="0"<?php if (!(strcmp($el_estatus, 0))) {echo "selected=\"selected\"";} ?>>Todas</option>
                                               <option value="1"<?php if (!(strcmp($el_estatus, 1))) {echo "selected=\"selected\"";} ?>>Activa</option>
                                               <option value="2"<?php if (!(strcmp($el_estatus, 2))) {echo "selected=\"selected\"";} ?>>Cerrada</option>
                                            </select>
                             </td>
                          <td><button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button></td>										
                             </tr>
					    </tbody>
				    </table>
				</form>

 					<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                  <tr class="bg-primary"> 
                                    <th>IDPuesto</th>
                                    <th>Matriz</th>
                                    <th>Área</th>
                                    <th>Denominación</th>
                                    <th>Estatus</th>
                                    <th>Tipo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Cierre</th>
                                    <th>Fecha Congelada</th>
                                    <th>Detalles</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_autorizados['IDplantilla']; ?>&nbsp;</td>
                                      <td><?php echo $row_autorizados['matriz']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['area']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['denominacion']; ?>&nbsp; </td>
                                      <td><?php if($row_autorizados['fecha_fin'] == '' ) { echo "Activa"; } else { echo "Cerrada"; } ?></td>
                                      <td><?php if($row_autorizados['IDtipo_plaza'] == 1 ) { echo "Planta"; }
									  	   else if($row_autorizados['IDtipo_plaza'] == 2 ) { echo "Temporal"; } ?></td>
                                      <td><?php echo date( 'd/m/Y', strtotime($row_autorizados['fecha_inicio']));  ?>&nbsp; </td>
                                      <td><?php if($row_autorizados['fecha_fin'] == '') { echo "Sin fecha";} else { echo date( 'd/m/Y', strtotime($row_autorizados['fecha_fin'])); } ?>&nbsp; </td>
                                      <td><?php if($row_autorizados['fecha_congelada'] == '') { echo "Sin fecha";} else { echo date( 'd/m/Y', strtotime($row_autorizados['fecha_congelada'])); } ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['IDmotivo']; ?> <?php echo $row_autorizados['observaciones']; ?>&nbsp;</td>
                                      </tr>                    
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados));   ?>
                                  </tbody>
                                </table>

						</div>
					</div>
                    
                    
                     
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