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
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$area_rh = $row_usuario['area_rh'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT * FROM ztar_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if (isset($_POST['el_anio'])) {$el_anio = $_POST['el_anio'];} 
else if (isset($_SESSION['el_anio'])) {$el_anio = $_SESSION['el_anio'];} 
else {$el_anio = $anio;}
$_SESSION['el_anio'] = $el_anio;

if(isset($_POST['el_area'])) {$el_area = $_POST['el_area'];} 
else if (isset($_SESSION['el_area'])) {$el_area = $_SESSION['el_area'];} 
else {$el_area = $area_rh;}
$_SESSION['el_area'] = $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_tareas = "SELECT ztar_tareas.IDtarea, ztar_tareas.anio, ztar_tareas.IDarea_rh, ztar_tareas.descripcion,   ztar_tareas.ponderacion, ztar_periodicidad.periodicidad, ztar_areas_rh.area_rh, Count(ztar_avances.IDavance), ztar_avances.fecha_esperada FROM ztar_tareas LEFT JOIN ztar_periodicidad ON ztar_periodicidad.IDperiodicidad = ztar_tareas.IDperiodicidad LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea WHERE ztar_areas_rh.IDarea_rh = '$el_area' AND ztar_tareas.anio = '$el_anio' GROUP BY ztar_tareas.IDtarea, ztar_tareas.IDarea_rh,  ztar_tareas.descripcion, ztar_tareas.ponderacion, ztar_periodicidad.periodicidad, ztar_areas_rh.area_rh";
mysql_query("SET NAMES 'utf8'");
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);

mysql_select_db($database_vacantes, $vacantes);
$query_arrh = "SELECT DISTINCT ztar_areas_rh.area_rh, ztar_areas_rh.IDarea_rh, ztar_tareas.IDtarea, ztar_valor_areas.valor_area FROM ztar_areas_rh RIGHT JOIN ztar_tareas ON ztar_tareas.IDarea_rh = ztar_areas_rh.IDarea_rh INNER JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_areas_rh.IDarea_rh GROUP BY  ztar_tareas.IDarea_rh";
$arrh = mysql_query($query_arrh, $vacantes) or die(mysql_error());
$row_arrh = mysql_fetch_assoc($arrh);
$totalRows_arrh = mysql_num_rows($arrh);

mysql_select_db($database_vacantes, $vacantes);
$query_arrhl = "SELECT DISTINCT ztar_areas_rh.area_rh, ztar_areas_rh.IDarea_rh, ztar_tareas.IDtarea, ztar_valor_areas.valor_area FROM ztar_areas_rh RIGHT JOIN ztar_tareas ON ztar_tareas.IDarea_rh = ztar_areas_rh.IDarea_rh INNER JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_areas_rh.IDarea_rh WHERE ztar_areas_rh.IDarea_rh = '$el_area' AND ztar_valor_areas.anio = $el_anio";
$arrhl = mysql_query($query_arrhl, $vacantes) or die(mysql_error());
$row_arrhl = mysql_fetch_assoc($arrhl);
$totalRows_arrhl = mysql_num_rows($arrhl);


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
	<script src="global_assets/js/plugins/tables/datatables/extensions/natural_sort.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/tasks_list7.js"></script>
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
                

					<!-- Task manager table -->
					<div class="panel panel-white">
						<div class="panel-heading">
							<h6 class="panel-title">Desempeño Jefes RH</h6>
						</div>
							
                            <div class="panel-body">
                            
                            
                         <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el objetivo y las tareas.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                            
							<p class="text-semibold">Instrucciones:</p>
							<p>A continuación se muestran los objetivos para los Jefes de Rh en Sucursales de:</br>
							 <strong>Area: </strong> <?php echo $row_arrhl['area_rh']; ?>.</br>
							 <strong>Año: </strong> <?php echo $el_anio; ?>.</br>
							 <strong>Valor del Área: </strong> <?php echo  $row_arrhl['valor_area'];?>%.</p>


							<p>&nbsp;</p>                            
					<form method="POST" action="objetivos_d.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>Area:
								<select name="el_area" class="form-control">
									<?php do { ?>
									   <option value="<?php echo $row_arrh['IDarea_rh']?>"<?php if (!(strcmp($row_arrh['IDarea_rh'], $el_area)))
									   {echo "selected=\"selected\"";} ?>><?php echo $row_arrh['area_rh']?></option>
									   <?php
									  } while ($row_arrh = mysql_fetch_assoc($arrh));
									  $rows = mysql_num_rows($arrh);
									  if($rows > 0) {
										  mysql_data_seek($arrh, 0);
										  $row_arrh = mysql_fetch_assoc($arrh);
									  } ?> 
								</select>
							</td>
							<td>Año:
								<select name="el_anio" class="form-control">
								<option value="2025"<?php if (!(strcmp($el_anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
								<option value="2024"<?php if (!(strcmp($el_anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
								   <option value="2023"<?php if (!(strcmp($el_anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
								   <option value="2022"<?php if (!(strcmp($el_anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
								</select>
						    </td>
                            <td><button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>
							<?php if($_SESSION['el_anio'] == 2024) { ?>
                                <a href="objetivos_d_edita.php" class="btn btn-success">Agregar Objetivo</a>
							<?php } ?>
							</td>
					      </tr>
					    </tbody>
				    </table>
					</form>
                             
							<p>&nbsp;</p>                            
                            <table class="table">
							<thead>
								<tr>
									<th>ID</th>
					                <th>Área</th>
					                <th>Objetivo</th>
					                <th>Ponderación</th>
									<th>Acciones</th>
					            </tr>
							</thead>
							<tbody>
						<?php if ($totalRows_tareas > 0) {?>
						<?php do { $IDtarea = $row_tareas['IDtarea'];?>
								<tr>
									<td><?php echo $row_tareas['IDtarea']; ?></td>
					                <td><div class="text-semibold"><?php echo $row_tareas['area_rh']; ?></div></td>
					                <td><div class="text-semibold"><?php echo $row_tareas['descripcion']; ?></div></td>
									<td><div class="text-muted"><?php echo $row_tareas['ponderacion']; ?>%</div></td>
									<td><?php if($el_anio == 2024) { ?>
                                        <a href="objetivos_d_edita.php?IDtarea=<?php echo $row_tareas['IDtarea']; ?>" class="btn btn-info">Editar</a>
                                        <button type="button" data-target="#modal_theme_danger<?php echo $row_tareas['IDtarea']; ?>"  data-toggle="modal" class="btn btn-danger">Borrar</button>
										
										<?php } ?>
										<a href="objetivos_d_detalle.php?IDtarea=<?php echo $row_tareas['IDtarea']; ?>" class="btn btn-success">Ver Avances</a>
									</td>
					            </tr>
								
								
								    <!-- danger modal -->
									<div id="modal_theme_danger<?php echo $row_tareas['IDtarea']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Borrado</h6>
												</div>

												<div class="modal-body">
													<p>¿Estas seguro que quieres borrar el Objetivo de Desempeño?<br/>
													<strong>Se borrarán las tareas asignadas a cada Sucusal.</strong></p>
												</div>

												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a href="objetivos_d_edita.php?IDtarea=<?php echo $row_tareas['IDtarea']; ?>&borrar=1" class="btn btn-danger">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->


								
					    <?php } while ($row_tareas = mysql_fetch_assoc($tareas)); ?>
						<?php } else {?>
                        <td colspan="9">No tienes Objetivos asignados para ese año.</td>
						<?php } ?>
							</tbody>
						</table>
					</div>
                    </div>
					<!-- /task manager table -->

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

mysql_free_result($tareas);
?>
