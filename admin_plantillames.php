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
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

     if (isset($_POST['el_tipo']) && ($_POST['el_tipo'] == 0)) {$_SESSION['el_tipo'] = '1,2'; }
else if (isset($_POST['el_tipo']) && ($_POST['el_tipo'] == 1)) {$_SESSION['el_tipo'] = 1; } 
else if (isset($_POST['el_tipo']) && ($_POST['el_tipo'] == 2)) {$_SESSION['el_tipo'] = 2; } 
else  { $_SESSION['el_tipo'] = '1,2';}

//las variables de sesion para el filtrado
if (isset($_POST['la_matriz'])) { foreach ($_POST['la_matriz'] as $matrizes)
	{	$_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);} }  else { $_SESSION['la_matriz'] = $IDmatrizes;}

if (isset($_POST['el_area'])) {	foreach ($_POST['el_area'] as $areas)
	{	$_SESSION['el_area'] = implode(", ", $_POST['el_area']);}	}  else { $_SESSION['el_area'] = '1,2,3,4,5,6,7,8,9,10';}

if(isset($_POST['el_estatus']) && ($_POST['el_estatus']  > 0)) { $_SESSION['el_estatus'] = $_POST['el_estatus']; } else { $_SESSION['el_estatus'] = "1";}

$el_tipo = $_SESSION['el_tipo'];
$el_estatus = $_SESSION['el_estatus'];
$la_matriz = $_SESSION['la_matriz'];
$el_area = $_SESSION['el_area'];

//echo "Tipo: ".$el_tipo."<br>";
//echo "Matriz: ".$la_matriz."<br>";
//echo "Estatus: ".$el_estatus."<br>";
//echo "Area: ".$el_area."<br>";


// Filtros
$filtro_mes = date("m-Y");
if (isset($_POST['fecha_filtro'])) { $_SESSION['fecha_filtro'] = $_POST['fecha_filtro'];} else {$_SESSION['fecha_filtro'] = $filtro_mes;}
$fecha_filtro = $_SESSION['fecha_filtro'];

$y1 = substr( $fecha_filtro, 3, 4 );
$m1 = substr( $fecha_filtro, 0, 2 );
$fecha_inicio =  $y1."-".$m1."-01";

$fini_mes1 = new DateTime($fecha_inicio);
$fini_mes1->modify('first day of this month');
$fini_mes1k = $fini_mes1->format('Y/m/d'); 

$fter_mes1 = new DateTime($fecha_inicio);
$fter_mes1->modify('last day of this month');
$fter_mes1k = $fter_mes1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT
Count(prod_plantilla.IDplantilla) AS Autorizada,
prod_plantilla.IDpuesto,
prod_plantilla.IDmatriz,
prod_plantilla.IDsucursal,
prod_plantilla.IDtipo_plaza,
prod_plantilla.IDestatus,
prod_plantilla.fecha_inicio,
prod_plantilla.fecha_fin,
prod_plantilla.fecha_congelada,
vac_puestos.denominacion,
vac_puestos.dias,
vac_areas.IDarea,
vac_areas.area,
vac_matriz.matriz
FROM
prod_plantilla
LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto
LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea
LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz
WHERE
prod_plantilla.IDmatriz IN ($la_matriz) AND
prod_plantilla.IDtipo_plaza IN ($el_tipo) AND
vac_puestos.IDarea IN ($el_area) AND
prod_plantilla.IDestatus = 1 AND
(DATE(fecha_inicio) <= '$fter_mes1k') AND
( DATE(fecha_fin) > '$fini_mes1k' OR DATE(fecha_fin) = '0000-00-00' OR DATE(fecha_fin) IS NULL) AND
( DATE(fecha_congelada) > '$fini_mes1k' OR DATE(fecha_congelada) = '0000-00-00' OR DATE(fecha_congelada) IS NULL)
GROUP BY
prod_plantilla.IDpuesto,
prod_plantilla.IDmatriz,
prod_plantilla.IDtipo_plaza
ORDER BY
vac_puestos.denominacion ASC";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

//echo $query_autorizados;

//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$IDmatriz'";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

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

mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
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
									<h6 class="panel-title">Plantilla Autorizada</h6>
								</div>

							<div class="panel-body">
								<p>A continuación se muestra la plantilla autorizada de la Sucursal.</br>


                <form method="POST" action="admin_plantilla.php">
					<table class="table">
							<tr>
							<td>Tipo Plaza:<select class="form-control" name="el_tipo">
                                               <option value="0"<?php if (!(strcmp($el_tipo, '1,2'))) {echo "selected=\"selected\"";} ?>>Todas</option>
                                               <option value="1"<?php if (!(strcmp($el_tipo, 1))) {echo "selected=\"selected\"";} ?>>Planta</option>
                                               <option value="2"<?php if (!(strcmp($el_tipo, 2))) {echo "selected=\"selected\"";} ?>>Temporal</option>
                                            </select>
                             </td>
							<td>
                            Área: <select name="el_area[]"  class="multiselect" multiple="multiple" >
											<?php do { ?>
                                               <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_area['area']?></option>
                                               <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) {
												  mysql_data_seek($area, 0);
												  $row_area = mysql_fetch_assoc($area);
											  } ?> </select>
                            </td>
                           <td> Matriz: <select name="la_matriz[]" class="multiselect" multiple="multiple">
                                          <?php do {  ?>
                                           <option value="<?php echo $row_amatriz['IDmatriz']?>"<?php if (!(strcmp($row_amatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>> <?php echo $row_amatriz['matriz']?></option>
											<?php
                                            } while ($row_amatriz = mysql_fetch_assoc($amatriz));
                                              $rows = mysql_num_rows($amatriz);
                                              if($rows > 0) {
                                                  mysql_data_seek($amatriz, 0);
                                                  $row_amatriz = mysql_fetch_assoc($amatriz);
                                              } ?></select>
                            </td>
                            <td>Mes/Año: 
                            <div class="input-group">
											<span class="input-group-addon"><i class="icon-calendar5"></i></span>
											<input type="text" class="form-control  daterange-single" name="fecha_filtro" id="fecha_filtro" value="<?php if (isset($_SESSION['fecha_filtro'])) {echo '';} else { echo "";} ?>">
										</div>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
							</td>
					      </tr>
				    </table>
				</form>

					<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                  <tr class="bg-primary"> 
                                    <th>IDPuesto</th>
                                    <th>Matriz</th>
                                    <th>Área</th>
                                    <th>Denominación</th>
                                    <th>Tipo</th>
                                    <th>Plazas</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php $totales = 0;
								  if($totalRows_autorizados > 0) { 
								  do { $totales = $totales + $row_autorizados['Autorizada'];  ?>
                                    <tr>
                                      <td><?php echo $row_autorizados['IDpuesto']; ?>&nbsp;</td>
                                      <td><?php echo $row_autorizados['matriz']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['area']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['denominacion']; ?>&nbsp; </td>
                                      <td><?php if($row_autorizados['IDtipo_plaza'] == 1 ) { echo "Planta"; }
									  	   else if($row_autorizados['IDtipo_plaza'] == 2 ) { echo "Temporal"; }
										   else if($row_autorizados['IDtipo_plaza'] == 3 ) { echo "Congelada"; } ?></td>
                                      <td><?php echo $row_autorizados['Autorizada']; ?></td>
                                      <td><a href="admin_plantilla_detalle.php?IDpuesto=<?php echo $row_autorizados['IDpuesto']; ?>&IDmatriz=<?php echo $row_autorizados['IDmatriz']; ?>&IDtipo_plaza=<?php echo $row_autorizados['IDtipo_plaza']; ?>&fecha_filtro=<?php echo $fecha_filtro; ?>" class="btn btn-primary">Ver detalles</a></tr>
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados));  } else { ?>
                                    <tr>
                                    <td colspan="7">No se cuentan con plazas registrada para el criterio seleccionado.</td>
                                    </tr>
								 <?php } ?>
                                  </tbody>
                                    <tfoot>
                                    <tr>
                                    <td colspan="4"><strong>Total</strong></td>
                                    <td><strong><?php echo $totales; ?></strong></td>
                                    <td colspan="2">&nbsp;</td>
                                    </tr>
                                    </tfoot>                                  
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