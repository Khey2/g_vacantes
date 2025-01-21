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
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (7,27,5,10)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$mmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT * FROM ztar_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

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


$la_matriz = $_GET['IDmatriz'];
$el_anio = $_GET['anio'];
$el_area = $_GET['IDarea'];


mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT ztar_avances.IDavance, ztar_avances.IDtarea, ztar_avances.IDresultado, ztar_tareas.descripcion, vac_matriz.IDmatriz, vac_matriz.matriz, ztar_avances.fecha_esperada, ztar_areas_rh.area_rh  FROM ztar_avances INNER JOIN ztar_tareas ON ztar_tareas.IDtarea = ztar_avances.IDtarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = ztar_avances.IDmatriz INNER JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh WHERE ztar_avances.IDmatriz = '$la_matriz' AND ztar_tareas.IDarea_rh = '$el_area' AND ztar_tareas.anio = '$el_anio'";
mysql_query("SET NAMES 'utf8'");
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);

mysql_select_db($database_vacantes, $vacantes);
$query_arrh2 = "SELECT * FROM ztar_areas_rh WHERE ztar_areas_rh.IDarea_rh IN (1,2,3,4,6,7,8,9)";
$arrh2 = mysql_query($query_arrh2, $vacantes) or die(mysql_error());
$row_arrh2 = mysql_fetch_assoc($arrh2);
$totalRows_arrh2 = mysql_num_rows($arrh2);

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

   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect3.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/tasks_list10.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
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
                
							<!-- Task overview -->
							<div class="panel panel-flat">
								<div class="panel-heading mt-5">
									<h6 class="panel-title">Desempeño Jefes RH</h6>
								</div>

								<div class="panel-body">
										<p><b>Area: </b><?php echo $row_avances['area_rh'];?></p>

								<a href="objetivos_z_rkg.php"  class="btn btn-primary">Regresar<i class="icon-arrow-left13 position-right"></i></a>
					<div class="table-responsive content-group">
                   <table class="table tasks-list table-condensed">
											<thead>
												<tr>
													<th>Descripción</th>
													<th>Matriz</th>
													<th>Fecha</th>
													<th>Resultado</th>
												</tr>
											</thead>
											<tbody>
                                            
                                     <?php  do { ?>
                                           <tr>
													<td><?php echo $row_avances['descripcion'];?></td>
													<td><?php echo $row_avances['matriz'];?></td>
													<td><?php $fecha = date('d/m/Y', strtotime($row_avances['fecha_esperada'])); echo $fecha; ?></td>
													<td><?php 
													
									$la_tarea = $row_avances['IDtarea'];
									$query_ponds = "SELECT * FROM ztar_tareas WHERE IDtarea = $la_tarea";
									$ponds = mysql_query($query_ponds, $vacantes) or die(mysql_error());
									$row_ponds = mysql_fetch_assoc($ponds);


									if ($row_avances['IDresultado'] == 0) { echo "<span class='label label-default'>En proceso / Incompleto</span>"; }
										 else if ($row_avances['IDresultado'] == $row_ponds['IDsob']) { echo "<span class='label label-primary'>Sobresaliente</span>"; } 
										 else if ($row_avances['IDresultado'] == $row_ponds['IDsat']) { echo "<span class='label label-success'>Satisfactorio</span>"; } 
										 else if ($row_avances['IDresultado'] == $row_ponds['IDdef']) { echo "<span class='label label-warning'>Deficiente</span>"; }
										 else if ($row_avances['IDresultado'] == '') { echo "<span class='label label-warning'>No aplica</span>"; }
														 ?>
													</td>
												</tr>
					    			<?php } while ($row_avances = mysql_fetch_assoc($avances));  ?>
                                                    </tbody>
										</table>
									</div>
								</div>

							</div>
							<!-- /task overview -->


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