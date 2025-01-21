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

$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) AND region_op > 0";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

$query_puestosaplican = "SELECT * FROM capa_plan_carrera_puestos";
$puestosaplican = mysql_query($query_puestosaplican, $vacantes) or die(mysql_error());
$row_puestosaplican = mysql_fetch_assoc($puestosaplican);
$totalRows_puestosaplican = mysql_num_rows($puestosaplican);

$APuestosAplicables = '';
do { $APuestosAplicables = $row_puestosaplican['IDpuesto'].", ".$APuestosAplicables;} while ($row_puestosaplican = mysql_fetch_assoc($puestosaplican));
$APuestosAplicables = $APuestosAplicables."0";

//if (isset($_POST['la_matriz'])) { foreach ($_POST['la_matriz'] as $matrizz)
//	{ $_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);}}  else {$_SESSION['la_matriz'] = $IDmatriz;}

if (isset($_POST['el_puesto'])) { $_SESSION['el_puesto'] = $_POST['el_puesto'];}  else {$_SESSION['el_puesto'] = 2;}

//if (isset($_POST['el_area'])) { foreach ($_POST['el_area'] as $areas)
//	{ $_SESSION['el_area'] = implode(", ", $_POST['el_area']);}}  else {$_SESSION['el_area'] = 2;}

if (isset($_POST['el_mes'])) { $_SESSION['el_mes'] = $_POST['el_mes'];}  else {$_SESSION['el_mes'] = $fecha_mes;}

if (isset($_POST['el_anio'])) { $_SESSION['el_anio'] = $_POST['el_anio'];}  else {$_SESSION['el_anio'] = 2024;}


$la_matriz = $IDmatriz;
$el_puesto = $_SESSION['el_puesto'];
$el_anio = $_SESSION['el_anio'];
//$el_area = $_SESSION['el_area'];
$el_mes = $_SESSION['el_mes'];

$query_plancarrera = "SELECT * FROM ind_bajas WHERE ind_bajas.alta_anio = $el_anio AND ind_bajas.alta_mes = $el_mes AND ind_bajas.IDpuesto =$el_puesto"; 
$plancarrera = mysql_query($query_plancarrera, $vacantes) or die(mysql_error());
$row_plancarrera = mysql_fetch_assoc($plancarrera);
$totalRows_plancarrera = mysql_num_rows($plancarrera);

$query_puestos = "SELECT * FROM vac_puestos WHERE IDpuesto IN ($APuestosAplicables)";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);

$query_reportes = "SELECT * FROM capa_eventos_grupo ORDER BY evento ASC";
$reportes = mysql_query($query_reportes, $vacantes) or die(mysql_error());
$row_reportes = mysql_fetch_assoc($reportes);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea IN (1,2,3,4,5,6) ORDER BY IDarea ASC";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_meses = "SELECT * FROM vac_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);


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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

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
 	<script src="global_assets/js/demo_pages/datatables_responsive.js"></script>

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
						<p>Selecciona los datos para el filtro.</p>


                <form method="POST" action="capa_reporte_4.php">
					<table class="table">
							<tr>
                           <td>Puesto
							<select name="el_puesto" id="el_puesto" class="form-control">
									  <?php do {  ?>
									  <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if ($row_puestos['IDpuesto'] == $el_puesto) {echo "SELECTED";} ?>><?php echo $row_puestos['denominacion']?></option>
									  <?php
									  } while ($row_puestos = mysql_fetch_assoc($puestos));
									 $rows = mysql_num_rows($puestos);
									 if($rows > 0) {
									 mysql_data_seek($puestos, 0);
									 $row_puestos = mysql_fetch_assoc($puestos);
									 } ?>
							</select>
                            <td>
                           <td>Año
							<select name="el_anio" id="el_anio" class="form-control">
								<option value="">Seleccione una opción</option> 
								<option value="2025"<?php if ($el_anio == 2025) {echo "SELECTED";} ?>>2025</option>
								<option value="2024"<?php if ($el_anio == 2024) {echo "SELECTED";} ?>>2024</option>
								<option value="2023"<?php if ($el_anio == 2023) {echo "SELECTED";} ?>>2023</option>
									  <option value="2022"<?php if ($el_anio == 2022) {echo "SELECTED";} ?>>2022</option>
							</select>
                            <td>
                           <td>Mes
							<select name="el_mes" id="el_mes" class="form-control">
								<option value="">Seleccione una opción</option> 
									  <?php do {  ?>
									  <option value="<?php echo $row_meses['IDmes']?>"<?php if ($row_meses['IDmes'] == $el_mes) {echo "SELECTED";} ?>><?php echo $row_meses['mes']?></option>
									  <?php
									  } while ($row_meses = mysql_fetch_assoc($meses));
									 $rows = mysql_num_rows($meses);
									 if($rows > 0) {
									 mysql_data_seek($area, 0);
									 $row_meses = mysql_fetch_assoc($meses);
									 } ?>
							</select>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button> 
							<input type="hidden" name="filtro" id="filtro" value="1">
							</td>
					      </tr>
				    </table>
				</form>


					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
					  <tr>
						<td class="bg-primary text-center" width="20%">Empleado</td>
						<td class="bg-primary text-center" width="10%">Matriz</td>
						<td class="bg-primary text-center" width="5%">No. Emp.</td>
						<td class="bg-primary text-center" width="5%">Estatus</td>
						
						<?php $query_cursosaplicables = "SELECT capa_plan_carrera_cursos.IDC_capa_cursos, capa_cursos.nombre_curso FROM capa_plan_carrera_cursos LEFT JOIN 	capa_cursos ON capa_plan_carrera_cursos.IDC_capa_cursos = capa_cursos.IDC_capa_cursos WHERE IDpuesto = $el_puesto ORDER BY capa_cursos.nombre_curso";
						$cursosaplicables = mysql_query($query_cursosaplicables, $vacantes) or die(mysql_error());
						$row_cursosaplicables = mysql_fetch_assoc($cursosaplicables);
						$totalRows_cursosaplicables = mysql_num_rows($cursosaplicables); 
						$widthe = 60 / $totalRows_cursosaplicables;
						do { echo '<td class="bg-primary text-center" width="'.$widthe.'%">'.$row_cursosaplicables['nombre_curso'].'</td>'; } while ($row_cursosaplicables = mysql_fetch_assoc($cursosaplicables)); ?>
						</tr>
                    </thead>
                    <tbody>
						<?php  do { $IDempleado = $row_plancarrera['IDempleado']; ?>
					  <tr>
						<td><?php echo $row_plancarrera['emp_paterno']." ".$row_plancarrera['emp_materno']." ".$row_plancarrera['emp_nombre']; ?></td>
						<td><?php echo $row_plancarrera['MATRIZ']; ?></td>
						<td><?php echo $row_plancarrera['IDempleado']; ?></td>
						<td class="text-center"><?php if ($row_plancarrera['fecha_baja'] == "") {echo "ACTIVO";} else { echo "BAJA";} ?></td>
						
						<?php $query_cursosaplicables2 = "SELECT capa_plan_carrera_cursos.IDC_capa_cursos, capa_cursos.nombre_curso FROM capa_plan_carrera_cursos LEFT JOIN 	capa_cursos ON capa_plan_carrera_cursos.IDC_capa_cursos = capa_cursos.IDC_capa_cursos WHERE IDpuesto = $el_puesto ORDER BY capa_cursos.nombre_curso";
						$cursosaplicables2 = mysql_query($query_cursosaplicables2, $vacantes) or die(mysql_error());
						$row_cursosaplicables2 = mysql_fetch_assoc($cursosaplicables2);
						$totalRows_cursosaplicables2 = mysql_num_rows($cursosaplicables2);

						do { $IDC_capa_cursos = $row_cursosaplicables2['IDC_capa_cursos'];
											
						$query_cursosestatus = "SELECT * FROM capa_avance WHERE IDempleado = $IDempleado AND IDC_capa_cursos = $IDC_capa_cursos";
						$cursosestatus = mysql_query($query_cursosestatus, $vacantes) or die(mysql_error());
						$row_cursosestatus = mysql_fetch_assoc($cursosestatus);
						$totalRows_cursosestatus = mysql_num_rows($cursosestatus);
								  
						if ($totalRows_cursosestatus > 0) { echo '<td>'.$row_cursosestatus['calificacion'].'</td>';} else { echo '<td>-</td>';}
						
						} while ($row_cursosaplicables2 = mysql_fetch_assoc($cursosaplicables2)); ?>

					  </tr>
					 <?php } while ($row_plancarrera = mysql_fetch_assoc($plancarrera)); ?>					  
                    </tbody>
					</table>
				


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

