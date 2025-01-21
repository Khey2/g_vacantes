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
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$mi_mes = $el_mes;
if (isset($_POST['mi_mes'])) {	foreach ($_POST['mi_mes'] as $mis_meses)
	{	$mi_mes =  implode(", ", $_POST['mi_mes']);}	}  
elseif (isset($_SESSION['mi_mes'])) {$mi_mes = $_SESSION['mi_mes'];} 

$_SESSION['mi_mes'] = $mi_mes;


if (isset($_POST['el_anio'])) {$_SESSION['el_anio'] = $_POST['el_anio'];} 
elseif (!isset($_SESSION['el_anio'])) {$_SESSION['el_anio'] = $anio;} 
//echo $_SESSION['el_anio'];
$el_anio = $_SESSION['el_anio'];

if (isset($_POST['el_area'])) {	foreach ($_POST['el_area'] as $mis_areas)
	{ $el_area = implode(", ", $_POST['el_area']);}	} 
else {  $el_area = "1,2,3,4,6,7,8,9";}
$_SESSION['el_area'] = $el_area;


//echo $el_anio;
//echo $mi_mes;
//echo  $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_tareas = "SELECT ztar_tareas.IDtarea, ztar_tareas.IDarea_rh,  ztar_tareas.IDperiodicidad,  ztar_tareas.por_evento, ztar_avances.IDestatus, ztar_avances.dias_recorrer,  ztar_avances.descripcion, ztar_avances.IDresultado, ztar_tareas.descripcion AS Descrito, ztar_tareas.ponderacion, ztar_periodicidad.periodicidad, ztar_areas_rh.area_rh, ztar_avances.IDavance, ztar_avances.fecha_esperada FROM ztar_tareas LEFT JOIN ztar_periodicidad ON ztar_periodicidad.IDperiodicidad = ztar_tareas.IDperiodicidad LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea WHERE ztar_avances.IDmatriz = '$IDmatriz' AND MONTH(ztar_avances.fecha_esperada) in ($mi_mes) AND ztar_tareas.IDarea_rh in ($el_area) AND ztar_avances.anio = $el_anio";
mysql_query("SET NAMES 'utf8'");
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);

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

$query_meses = "SELECT * FROM ztar_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_areas = "SELECT * FROM ztar_areas_rh";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);
$totalRows_areas = mysql_num_rows($areas);
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
	<script src="global_assets/js/demo_pages/tasks_list8.js"></script>
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
                                
									<h6 class="text-semibold">Instrucciones:</h6>
                                    <p>Para reportar el avance esperado, da clic en "Reportar avance".</p>
									<p>Selecciona los meses para filtrar resultados.</p>
                                    <p>&nbsp;</p>  
                                                      
								<form method="POST" action="objetivos_c.php">
                                <table class="table">
								  <tr>
								    <td> <div class="col-lg-2">Mes:</div></td>
								    <td>
                                     <div class="col-lg-12">
                                             <select class="multiselect" multiple="multiple" name="mi_mes[]">
											<?php do { ?>
                                               <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $mi_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
                                               <?php
											  } while ($row_meses = mysql_fetch_assoc($meses));
											  $rows = mysql_num_rows($meses);
											  if($rows > 0) {
												  mysql_data_seek($meses, 0);
												  $row_meses = mysql_fetch_assoc($meses);
											  } ?> </select>
                                      </div>
                                     </td>
								    <td> <div class="col-lg-2">Área:</div></td>
								    <td>
                                     <div class="col-lg-12">
                                             <select class="multiselect" multiple="multiple" name="el_area[]">
											<?php do { ?>
                                               <option value="<?php echo $row_areas['IDarea_rh']?>"<?php if (!(strcmp($row_areas['IDarea_rh'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_areas['area_rh']?></option>
                                               <?php
											  } while ($row_areas = mysql_fetch_assoc($areas));
											  $rows = mysql_num_rows($areas);
											  if($rows > 0) {
												  mysql_data_seek($areas, 0);
												  $row_areas = mysql_fetch_assoc($areas);
											  } ?> </select>
                                      </div>
                                     </td>
								    <td> <div class="col-lg-2">Año:</div></td>
							<td>
								<select name="el_anio" class="form-control">
								   <option value="2021"<?php if (!(strcmp($el_anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
								   <option value="2022"<?php if (!(strcmp($el_anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
								   <option value="2023"<?php if (!(strcmp($el_anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
								   <option value="2024"<?php if (!(strcmp($el_anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
								   <option value="2025"<?php if (!(strcmp($el_anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
								</select>
						    </td>
								    <td>
                                    <div class="col-lg-2">
                                    <button type="submit" class="btn btn-primary">Cambiar <i class="icon-arrow-right14 position-right"></i></button>
                                    </div>
                                    </td>
                                  </tr>
							  </table>
                             </form>

					<div class="table-responsive content-group">
                            <table class="table tasks-list table-condensed">
							<thead>
								<tr>
									<th>ID</th>
									<th>Área</th>
									<th>Objetivo</th>
					                <th>Tarea</th>
					                <th>Fecha límite</th>
					                <th>Resultado</th>
									<th>Acciones</th>
					            </tr>
							</thead>
							<tbody>
						<?php do { $IDtarea = $row_tareas['IDtarea'];?>

								<tr>
									<td><?php echo $row_tareas['IDtarea']; ?></td>
									<td><?php echo $row_tareas['area_rh']; ?></td>
									<td><div class="text-semibold"><?php echo $row_tareas['Descrito']; ?></div></td>
					                <td><?php echo $row_tareas['descripcion']; ?></td>
									<td><div class="text-muted"><?php $fecha = date('d/m/Y', strtotime($row_tareas['fecha_esperada']. '+'.$row_tareas['dias_recorrer'].'day')); 
																  if($row_tareas['por_evento'] == 1) { echo "Por evento";} 
															else  if($row_tareas['fecha_esperada'] > 0) { echo $fecha;} 
															else {echo "-";}?>
									</td>
                                    <td><?php
									$la_tarea = $row_tareas['IDtarea'];
									$query_ponds = "SELECT * FROM ztar_tareas WHERE IDtarea = $la_tarea";
									$ponds = mysql_query($query_ponds, $vacantes) or die(mysql_error());
									$row_ponds = mysql_fetch_assoc($ponds);
									
									  switch ($row_tareas['IDresultado']) {
										case '':  $el_resultado = "Pendiente";  $el_resultado_i = "label-info";   break;     
										case $row_ponds['IDsob']:  $el_resultado = "Sobresaliente";  $el_resultado_i = "label-success";     break;     
										case $row_ponds['IDsat']:  $el_resultado = "Satisfactorio";  $el_resultado_i = "label-primary";   break;    
										case $row_ponds['IDdef']:  $el_resultado = "Deficiente"; $el_resultado_i = "label-danger";   break;    
										case 0:  $el_resultado = "No aplica - Pendiente"; $el_resultado_i = "label-default";   break;    
										  }
											?><a class="label <?php echo $el_resultado_i;  ?>"><?php echo $el_resultado;  ?></a></td>
									<td class="text-center">
                                    <?php  if ($row_tareas['IDestatus'] != 0) {?>
										<a href="objetivos_b_detalle.php?IDavance=<?php echo $row_tareas['IDavance']; ?>" class="btn btn-info">Ver avance</a>
								<?php } else if ($row_tareas['IDestatus'] == 0) { ?>
										<a href="objetivos_b_detalle.php?IDavance=<?php echo $row_tareas['IDavance']; ?>" class="btn btn-success">Reportar avance</a>
                                <?php } ?>
									</td>
					            </tr>
					    <?php } while ($row_tareas = mysql_fetch_assoc($tareas)); ?>
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
