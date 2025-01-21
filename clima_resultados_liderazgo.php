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
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $IDmatriz";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos); 

if(isset($_POST['IDperiodo'])) {$_SESSION['IDperiodo'] = $_POST['IDperiodo'];} 
if(!isset($_SESSION['IDperiodo'])) {$_SESSION['IDperiodo'] = 2024;} 
$IDperiodo = $_SESSION['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_elperiodo = "SELECT * FROM sed_clima_periodos WHERE IDperiodo = $IDperiodo";
$elperiodo = mysql_query($query_elperiodo, $vacantes) or die(mysql_error());
$row_elperiodo = mysql_fetch_assoc($elperiodo);
$totalRows_elperiodo = mysql_num_rows($elperiodo); 

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
$query_clima = "SELECT sed_clima.IDclima, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDarea, vac_areas.area FROM sed_clima INNER JOIN prod_activos ON prod_activos.IDempleado = sed_clima.IDjefe INNER JOIN vac_areas ON prod_activos.IDarea = vac_areas.IDarea WHERE prod_activos.IDmatriz = '$IDmatriz' GROUP BY sed_clima.IDjefe"; 
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
									<h6 class="panel-title">Liderazgo</h6>
								</div>

								<div class="panel-body">
								<p>A continuación se muestran los resultados de los Lídres de la Sucursal <strong><?php echo $row_matriz['matriz']; ?></strong> que cuentan con evaluaciones de Liderazgo.</p>
                                
								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                    <th>Evaluadores</th>
                                    <th>Resultado %</th>
                                    <th>Resultado</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_clima > 0 ) { ?>
								  <?php do { 
									
									$el_evaluado = $row_clima['IDempleado'];
									mysql_select_db($database_vacantes, $vacantes);
									$query_resultadogeneral = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) * 100 AS Calificacion FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta =  sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 1 AND sed_clima.IDjefe = '$el_evaluado' AND sed_clima.IDperiodo = '$IDperiodo'";  
									$resultadogeneral = mysql_query($query_resultadogeneral, $vacantes) or die(mysql_error());
									$row_resultadogeneral = mysql_fetch_assoc($resultadogeneral);
									$resultado_general = round($row_resultadogeneral['Calificacion']/100,2);
									
									?>
                                    <tr>
                                      <td><?php echo $row_clima['IDempleado']; ?></td>
                                      <td><?php echo $row_clima['emp_paterno'] . " " . $row_clima['emp_materno'] . " " . $row_clima['emp_nombre'];?></td>
                                      <td><?php echo $row_clima['area']; ?></td>
                                      <td><?php echo $row_clima['denominacion']; ?></td>
                                      <td><?php 
										  
									$el_evaluado = $row_clima['IDempleado'];
									mysql_select_db($database_vacantes, $vacantes);
									$query_evaluadores = "SELECT Count(sed_clima.IDempleado) AS Contador FROM sed_clima WHERE sed_clima.IDpregunta = 1 AND sed_clima.IDjefe = '$el_evaluado' AND sed_clima.IDperiodo = '$IDperiodo'";  
									$evaluadores = mysql_query($query_evaluadores, $vacantes) or die(mysql_error());
									$row_evaluadores = mysql_fetch_assoc($evaluadores);
									echo $row_evaluadores['Contador'];
										  ?></td>
                                      <td><?php echo round($resultado_general * 100,2); ?>%</td>
										
									  <td>
										<?php if ($row_resultadogeneral['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_resultadogeneral['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_resultadogeneral['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
									</td>	  
										
                                      <td><a class="btn btn-success" href="clima_resultados_l.php?IDempleado=<?php echo $row_clima['IDempleado']; ?>">Ver resultados</a>
                                      <a class="btn btn-warning" href="clima_resultados_plan.php?IDempleado=<?php echo $row_clima['IDempleado']; ?>">Plan acción</a></td>
                                    </tr>
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