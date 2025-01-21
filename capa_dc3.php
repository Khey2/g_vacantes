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
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fecha_mes = date("m")-1;

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if (isset($_POST['la_matriz']) AND $_POST['la_matriz'] > 0) { foreach ($_POST['la_matriz'] as $la_matrizx)
	{ $_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);}}  else {$_SESSION['la_matriz'] = $IDmatriz;}

if (isset($_POST['el_area'])  AND $_POST['el_area'] > 0) { foreach ($_POST['el_area'] as $el_areax)
	{ $_SESSION['el_area'] = implode(", ", $_POST['el_area']);}}  else {$_SESSION['el_area'] = '1,2,3,4,5,6,7,8,9,10,11';}

if (isset($_POST['el_anio'])  AND $_POST['el_anio'] > 0) { $_SESSION['el_anio'] = $_POST['el_anio'];}  else {$_SESSION['el_anio'] = $anio;}
	
if (isset($_POST['el_mes'])  AND $_POST['el_mes'] > 0) { foreach ($_POST['el_mes'] as $el_mesx)
	{ $_SESSION['el_mes'] = implode(", ", $_POST['el_mes']);}}  else {$_SESSION['el_mes'] = '1,2,3,4,5,6,7,8,9,10,11,12';}

if (isset($_POST['el_curso'])  AND $_POST['el_curso'] > 0) { $_SESSION['el_curso'] = $_POST['el_curso'];}  else {$_SESSION['el_curso'] = 0;}


$la_matriz = $_SESSION['la_matriz'];
$el_area = $_SESSION['el_area']; 
$el_anio = $_SESSION['el_anio'];
$el_mes = $_SESSION['el_mes'];
$el_curso = $_SESSION['el_curso'];

$query_consulta = "SELECT capa_avance.*, capa_cursos.*, vac_areas.area, vac_matriz.matriz FROM capa_avance LEFT JOIN capa_cursos ON capa_avance.IDC_capa_cursos = capa_cursos.IDC_capa_cursos LEFT JOIN vac_matriz ON capa_avance.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON capa_avance.IDarea = vac_areas.IDarea WHERE capa_avance.IDC_capa_cursos = $el_curso AND capa_avance.IDmatriz IN ($la_matriz) AND capa_avance.IDarea IN ($el_area) AND capa_avance.anio = $el_anio AND capa_avance.mes IN ($el_mes)";
$consulta = mysql_query($query_consulta, $vacantes) or die(mysql_error());
$row_consulta = mysql_fetch_assoc($consulta);
$totalRows_consulta = mysql_num_rows($consulta);

$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) AND region_op > 0";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea IN (1,2,3,4,5,6,7,8,9,10,11) ORDER BY IDarea ASC";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_meses = "SELECT * FROM vac_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);

$query_cursos = "SELECT * FROM capa_cursos ORDER BY nombre_curso ASC";
$cursos = mysql_query($query_cursos, $vacantes) or die(mysql_error());
$row_cursos = mysql_fetch_assoc($cursos);

$query_firmas = "SELECT * FROM capa_firmas ORDER BY firma ASC";
$firmas = mysql_query($query_firmas, $vacantes) or die(mysql_error());
$row_firmas = mysql_fetch_assoc($firmas);

set_time_limit(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>	
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/bec_datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5_cd3.js"></script>
	<!-- /theme JS files -->

<script>
function toggle(source) {
var checkboxes = document.querySelectorAll('input[type="checkbox"]');
for (var i = 0; i < checkboxes.length; i++) {
if (checkboxes[i] != source)
checkboxes[i].checked = source.checked;
}
}
</script>
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


	                <!-- Content area -->
				<div class="content">
                
					<!-- Contenido -->
                  <div class="panel panel-flat">

					<div class="panel-body">
						<p class="content-group"><b>Instrucciones:</b></p>


					<form action="capa_dc3.php" class="form-inline" method="POST">
					<table class="table">
							<tr> 
							<td style="width:50%"> 
								<label>Curso: </label>
								<select name="el_curso" id="el_curso" class="bootstrap-select" data-live-search="true" data-width="100%">
									<?php  do { ?>
									<option value="<?php echo $row_cursos['IDC_capa_cursos']?>"<?php if (!(strcmp($row_cursos['IDC_capa_cursos'], $el_curso))) {echo "SELECTED";} ?>><?php echo $row_cursos['nombre_curso']?></option>
									<?php
									} while ($row_cursos = mysql_fetch_assoc($cursos));
									$rows = mysql_num_rows($cursos);
									if($rows > 0) {
									mysql_data_seek($cursos, 0);
									$row_cursos = mysql_fetch_assoc($cursos);
									} ?>
								</select>
							</td>
							<td>
								<label>Sucursal: </label>
								<select name="la_matriz[]" id="la_matriz[]" class="multiselect-select-all" multiple="multiple">
									<?php $array = explode(",", $la_matriz); ?>
									<?php  do { ?>
									<option value="<?php echo $row_amatriz['IDmatriz']?>"<?php foreach ($array as $la_matrizS) { if (!(strcmp($row_amatriz['IDmatriz'], $la_matrizS))) {echo "SELECTED";} } ?>><?php echo $row_amatriz['matriz']?></option>
									<?php
									} while ($row_amatriz = mysql_fetch_assoc($amatriz));
									$rows = mysql_num_rows($amatriz);
									if($rows > 0) {
									mysql_data_seek($amatriz, 0);
									$row_amatriz = mysql_fetch_assoc($amatriz);
									} ?>
								</select>
							</td>
							<td>
							<label>Area: </label>
								<select name="el_area[]" id="el_area[]" class="multiselect-select-all" multiple="multiple">
									<?php $array = explode(",", $el_area); ?>
									<?php do { ?>
									<option value="<?php echo $row_areas['IDarea']?>"<?php foreach ($array as $el_areaS) { if (!(strcmp($row_areas['IDarea'], $el_areaS))) {echo "SELECTED";} } ?>><?php echo $row_areas['area']?></option>
									<?php
									} while ($row_areas = mysql_fetch_assoc($areas));
									$rows = mysql_num_rows($areas);
									if($rows > 0) {
									mysql_data_seek($areas, 0);
									$row_areas = mysql_fetch_assoc($areas); } ?> 
								</select>
                            </td>
							<td>
								<label>Año: </label>
									<select name="el_anio" id="el_anio" class="bootstrap-select" data-live-search="true" data-width="100%">
									<option value="2025"<?php if (!(strcmp(2025, $el_anio))) {echo "SELECTED";} ?>>2025</option>
									<option value="2024"<?php if (!(strcmp(2024, $el_anio))) {echo "SELECTED";} ?>>2024</option>
										<option value="2023"<?php if (!(strcmp(2023, $el_anio))) {echo "SELECTED";} ?>>2023</option>
									</select>
							</td>
							<td>
								<label>Mes: </label>
								<select name="el_mes[]" id="el_mes[]" class="multiselect-select-all" multiple="multiple">
									<?php $array = explode(",", $el_mes); ?>
									<?php do { ?>
									<option value="<?php echo $row_meses['IDmes']?>"<?php foreach ($array as $el_mesS) { if (!(strcmp($row_meses['IDmes'], $el_mesS))) {echo "SELECTED";} } ?>><?php echo $row_meses['mes']?></option>
									<?php
									} while ($row_meses = mysql_fetch_assoc($meses));
									$rows = mysql_num_rows($meses);
									if($rows > 0) {
									mysql_data_seek($meses, 0);
									$row_meses = mysql_fetch_assoc($meses); } ?> 
								</select>
							</td>
							<td>
								<div class="form-group has-feedback">
								<button type="submit" class="btn btn-primary">Filtrar </button>
								</div>
							</td>
							<td>
							</tr>
				    </table>
					</form>

                    <form method="POST" name="form1" action="empleados_printCapa.php">
					<input type="hidden" name="el_curso" value="<?php echo $el_curso; ?>">
					<input type="hidden" name="el_anio" value="<?php echo $el_anio; ?>">
					<input type="hidden" name="el_mes" value="<?php echo $el_mes; ?>">
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
						<tr class="bg-primary text-center">
							<th><input type="checkbox" id="selectall" name="selectall" class="form-check-input-styled" onclick="toggle(this)" autocomplete="off" checked="checked"></th>
							<th>No. Emp</th>
							<th>Empleado</th>
							<th>Matriz</th>
							<th>Area</th>
							<th>Puesto</th>
							<th>Fecha</th>
						</tr>
                    </thead>
                    <tbody>
					<?php if ($totalRows_consulta > 0) { do {  $el_empleado = $row_consulta['IDempleado'];?>
					  	<tr>
						  	<td><input type="checkbox" name="IDempleado[]" value="<?php echo $el_empleado; ?>" checked="checked"></td>
						  	<td><?php echo $row_consulta['IDempleado']; ?></td>
						 	<td><?php echo $row_consulta['emp_paterno']." ".$row_consulta['emp_materno']." ".$row_consulta['emp_nombre']; ?></td>
							<td><?php echo $row_consulta['matriz']; ?></td>
							<td><?php echo $row_consulta['area']; ?></td>
							<td><?php echo $row_consulta['denominacion']; ?></td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_consulta['fecha_evento'])); ?></td>
					  	</tr>
					<?php } while ($row_consulta = mysql_fetch_assoc($consulta)); ?>					  
					<?php } else { ?>
						<tr>
						<td colspan="7">Utiliza el filtro para ver participantes.</td>
					  	</tr>
					<?php }?>
                    </tbody>
					</table>

                     <!-- danger modal -->
					 <div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Selección de Firmas</h6>
								</div>

								<div class="modal-body">

								<label>Firma Instructor: </label>
								<select name="firma_1" id="firma_1" class="form-control">
									<?php do { ?>
									<option value="<?php echo $row_firmas['IDfirma']?>"><?php echo $row_firmas['firma']?></option>
									<?php
									} while ($row_firmas = mysql_fetch_assoc($firmas));
									$rows = mysql_num_rows($firmas);
									if($rows > 0) {
									mysql_data_seek($firmas, 0);
									$row_firmas = mysql_fetch_assoc($firmas); } ?> 
								</select>

								<p>&nbsp;</p>

								<label>Firma Representante Trabajadores: </label>
								<select name="firma_2" id="firma_2" class="form-control">
									<?php do { ?>
									<option value="<?php echo $row_firmas['IDfirma']?>"><?php echo $row_firmas['firma']?></option>
									<?php
									} while ($row_firmas = mysql_fetch_assoc($firmas));
									$rows = mysql_num_rows($firmas);
									if($rows > 0) {
									mysql_data_seek($firmas, 0);
									$row_firmas = mysql_fetch_assoc($firmas); } ?> 
								</select>

								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Generar CD3</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


					</form> 


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

