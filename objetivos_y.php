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
$mmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$mmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$la_matriz = $IDmatriz;
$el_anio = 2024;

$query_tareas = "SELECT ztar_tareas.IDtarea, ztar_tareas.IDarea_rh, ztar_tareas.descripcion, ztar_areas_rh.area_rh FROM ztar_tareas LEFT JOIN ztar_areas_rh ON ztar_tareas.IDarea_rh = ztar_areas_rh.IDarea_rh WHERE anio = $el_anio ORDER BY ztar_tareas.IDarea_rh";
mysql_query("SET NAMES 'utf8'");
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);

if (isset($_POST['la_tarea'])) {$la_tarea = $_POST['la_tarea'];} else {$la_tarea = '0';}

$query_tarea = "SELECT ztar_tareas.IDtarea, ztar_tareas.IDsob, ztar_tareas.IDsat, ztar_tareas.IDdef, ztar_tareas.IDarea_rh, ztar_tareas.descripcion, ztar_areas_rh.area_rh FROM ztar_tareas LEFT JOIN ztar_areas_rh ON ztar_tareas.IDarea_rh = ztar_areas_rh.IDarea_rh WHERE IDtarea = $la_tarea";
mysql_query("SET NAMES 'utf8'");
$tarea = mysql_query($query_tarea, $vacantes) or die(mysql_error());
$row_tarea = mysql_fetch_assoc($tarea);
$totalRows_tarea = mysql_num_rows($tarea);

$IDsob = $row_tarea['IDsob'];
$IDsat = $row_tarea['IDsat'];
$IDdef = $row_tarea['IDdef'];


mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (5,7,10,27)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

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
                

					<!-- Detailed task -->
					<div class="row">
						<div class="col-lg-12">

							<!-- Task overview -->
							<div class="panel panel-flat">
								<div class="panel-heading mt-5">
									<h4 class="panel-title">Desempeño JRH - Calificación anual por Objetivo.</h4>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">
									<p>A continuación se muestan los resultados de la Evaluación a Recursos Humanos del <?php echo  $el_anio ?> del Area <strong><?php echo $row_tarea['area_rh'] ?></strong> y Objetivo <strong><?php echo $row_tarea['descripcion']; ?></strong>.</p>
                                 
					<form method="POST" action="objetivos_y.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
								<select name="la_tarea" class="form-control">
									<?php do { ?>
									   <option value="<?php echo $row_tareas['IDtarea']?>"<?php if (!(strcmp($row_tareas['IDtarea'], $la_tarea)))
									   {echo "selected=\"selected\"";} ?>><?php echo $row_tareas['area_rh'].": ".$row_tareas['descripcion']?></option>
									   <?php
									  } while ($row_tareas = mysql_fetch_assoc($tareas));
									  $rows = mysql_num_rows($tareas);
									  if($rows > 0) {
										  mysql_data_seek($tareas, 0);
										  $row_tareas = mysql_fetch_assoc($tareas);
									  } ?> 
								</select>
                            <td><button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>
							</td>
					      </tr>
					    </tbody>
				    </table>
					</form>
					
					
<?php if($la_tarea != '0') { ?>					
                                  
<table class="table table-condensed datatable-button-html5-columns">
											<thead>
												<tr class="bg-blue">
													<th>Matriz</th>
													<th>Área</th>
													<th>Objetivo</th>
													<th>Resultado</th>
													<th>Rango</th>
												</tr>
											</thead>
											<tbody>
                                           <tr>
										<?php do { 
										
                                        $mimatriz = $row_matrizes['IDmatriz'];	

                                        $query_resultado = "SELECT ztar_avances.IDavance, ztar_avances.IDtarea, ztar_avances.IDmatriz, ztar_avances.IDestatus, ztar_avances.IDresultado, ztar_avances.anio FROM ztar_avances INNER JOIN ztar_tareas ON ztar_avances.IDtarea = ztar_tareas.IDtarea WHERE ztar_avances.IDmatriz = $mimatriz AND ztar_avances.IDtarea = $la_tarea AND ztar_avances.anio = $el_anio AND ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0";
                                        $resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
                                        $row_resultado = mysql_fetch_assoc($resultado);
										$totalRows_resultado = mysql_num_rows($resultado);
										
										$Total = 0;
										
										 do { 
										 
										$Total = $Total + $row_resultado['IDresultado'];	
										

										 } while ($row_resultado = mysql_fetch_assoc($resultado)); 
										if($totalRows_resultado > 0) {$final = $Total / $totalRows_resultado;} else {$final = 0;}

										?>
										<td><div class="text text-semibold"><?php echo $row_matrizes['matriz']; ?></div></td>
										<td><?php echo $row_tarea['area_rh']; ?></td>
										<td><?php echo $row_tarea['descripcion']; ?></td>
										<td><?php echo round($final, 0); ?>%</td>
										<td><?php if ($final > 100) { echo "<div class='text text-primary text-bold'>Sobresaliente</div>";} 
										  else if ($final >= 70) { echo "<div class='text text-success text-bold'>Satisfactorio</div>";} 
										  else if ($final > 1) { echo "<div class='text text-danger text-bold'>Deficiente</div>";}
										  else if ($final == 0) { echo "<div class='text text-bold'>-</div>";} ?></td>
										</tr>
                                    <?php } while ($row_matrizes = mysql_fetch_assoc($matrizes)); ?>
                                   </tbody>
                                   </table>

<?php } ?>					
                        
								</div>

							</div>
							<!-- /task overview -->

						</div>

					</div>
					<!-- /detailed task -->

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