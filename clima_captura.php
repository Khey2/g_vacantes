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
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $IDmatriz"; 
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos); 
$periodo23 = $row_periodos['IDperiodo'];

if(isset($_POST['IDperiodo'])) {$_SESSION['IDperiodo'] = $_POST['IDperiodo'];} 
if(!isset($_SESSION['IDperiodo'])) {$_SESSION['IDperiodo'] = $periodo23;} 
$IDperiodo = $_SESSION['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_elperiodo = "SELECT * FROM sed_clima_periodos WHERE IDperiodo = $IDperiodo";
$elperiodo = mysql_query($query_elperiodo, $vacantes) or die(mysql_error());
$row_elperiodo = mysql_fetch_assoc($elperiodo);
$totalRows_elperiodo = mysql_num_rows($elperiodo); 


$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

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

mysql_select_db($database_vacantes, $vacantes);
$query_clima = "SELECT DISTINCT sed_clima.IDempleado, sed_clima.manual, sed_clima.emp_paterno, sed_clima.emp_materno, sed_clima.emp_nombre, sed_clima.IDarea, sed_clima.anio,  sed_clima.IDperiodo, sed_clima.denominacion, sed_clima.IDmatriz, sed_clima.IDjefe AS jefe_IDempleado, sed_clima.j_emp_paterno AS jefe_emp_paterno, sed_clima.J_emp_materno AS jefe_emp_materno, sed_clima.J_emp_nombre AS jefe_emp_nombre, sed_clima.j_denominacion AS jefe_denominacion, vac_areas.area FROM sed_clima LEFT JOIN vac_areas ON vac_areas.IDarea = sed_clima.IDarea WHERE sed_clima.IDperiodo = '$IDperiodo' AND sed_clima.IDmatriz = '$IDmatriz' GROUP BY sed_clima.IDempleado"; 
mysql_query("SET NAMES 'utf8'");
$clima = mysql_query($query_clima, $vacantes) or die(mysql_error());
$row_clima = mysql_fetch_assoc($clima);
$totalRows_clima = mysql_num_rows($clima);

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


//plantilla
mysql_select_db($database_vacantes, $vacantes);
$query_plantilla = "SELECT DISTINCT Count(prod_activos.rfc) as plantilla FROM prod_activos WHERE prod_activos.IDmatriz ='$la_matriz'";
$plantilla = mysql_query($query_plantilla, $vacantes) or die(mysql_error());
$row_plantilla = mysql_fetch_assoc($plantilla);
$totalRows_plantilla = mysql_num_rows($plantilla);

//plantilla
mysql_select_db($database_vacantes, $vacantes);
$query_encuestas = "SELECT DISTINCT sed_clima.IDempleado FROM sed_clima  WHERE sed_clima.IDmatriz ='$la_matriz' AND IDperiodo = '$IDperiodo'";
$encuestas = mysql_query($query_encuestas, $vacantes) or die(mysql_error());
$row_encuestas = mysql_fetch_assoc($encuestas);
$totalRows_encuestas = mysql_num_rows($encuestas);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la encuesta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la encuesta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Clima Laboral</h6>
								</div>

								<div class="panel-body">
								<p>A continuación se muestran las encuesta de Clima Laboral de la Sucursal <strong><?php echo $row_matriz['matriz']; ?></strong>.</p>
                                <p>Para la captura manual, descarga <a href="files/formato_encuesta.pdf"><strong>AQUI</strong></a> el formato autorizado.</p>
								<p>Periodo: <b><?php echo $row_elperiodo['periodo']; ?></b></p>
                                <p>Plantilla activa: <strong><?php echo $row_plantilla['plantilla']; ?></strong></p>
                                <p>Encuestas: <strong><?php echo $totalRows_encuestas; ?></strong></p>
                                <p>% de participación: <strong><?php echo round(($totalRows_encuestas / $row_plantilla['plantilla'])*100,0); ?>%</strong> (se requiere del 80% como mínimo para mostrar resultados)</p>
								
								

					<form method="POST" action="clima_captura.php">
					<table class="table">
							<td>
                            Periodo: <select name="IDperiodo"  class="form-control" >
							<?php do { ?>
								<option value="<?php echo $row_periodos['IDperiodo']?>"<?php if (!(strcmp($row_periodos['IDperiodo'], $IDperiodo))) {echo "selected=\"selected\"";} ?>><?php echo $row_periodos['periodo']?></option>
								<?php
								} while ($row_periodos = mysql_fetch_assoc($periodos));
								$rows = mysql_num_rows($periodos);
								if($rows > 0) {
									mysql_data_seek($periodos, 0);
									$row_periodos = mysql_fetch_assoc($periodos);
								} ?> </select>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button> 
							</td>
					      </tr>
				    </table>
				</form>


                                
                  <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-primary" href="clima_captura_edit.php">Agregar Encuesta<i class="icon-arrow-right14 position-right"></i></a>
                    </div>
					</div>
					<!-- /colored button -->
                                

								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                    <th>Jefe</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_clima > 0 ) { ?>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_clima['IDempleado']; ?></td>
                                      <td><?php if ($row_clima['manual'] != 1)
									  { echo $row_clima['emp_paterno'] . " " . $row_clima['emp_materno'] . " " . $row_clima['emp_nombre'];
									  } else { echo "CAPTURA MANUAL"; } ?></td>
                                      <td><?php echo $row_clima['area']; ?></td>
                                      <td><?php if ($row_clima['manual'] != 1) { echo $row_clima['denominacion']; } else { echo "CAPTURA MANUAL"; } ?></td>
                                      <td><?php echo $row_clima['jefe_emp_paterno']. " " .$row_clima['jefe_emp_materno']. " " .$row_clima['jefe_emp_nombre']; ?></td>
                                      <td>
                                      <button type="button" data-target="#modal_theme_danger<?php echo $row_clima['IDempleado']; ?>" 
                                       data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                                      
                                       </td>
                                    </tr>

                   <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_clima['IDempleado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la captura?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="clima_captura_edit.php?IDempleado=<?php echo $row_clima['IDempleado']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                                    
                                    <?php } while ($row_clima = mysql_fetch_assoc($clima)); ?>
 							  <?php } else { ?>
<tr>
                                      <td>No se tienen captruas de Clima para este periodo.</td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                    </tr>
                              <?php } ?>
                                    
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