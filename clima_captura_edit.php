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
mysql_query("SET NAMES 'utf8'");
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $IDmatriz";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos); 

if(isset($_POST['IDperiodo'])) {$_SESSION['IDperiodo'] = $_POST['IDperiodo'];} 
if(!isset($_SESSION['IDperiodo'])) {$_SESSION['IDperiodo'] = 0;} 
$IDperiodo = $_SESSION['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_elperiodo = "SELECT * FROM sed_clima_periodos WHERE IDperiodo = $IDperiodo";
$elperiodo = mysql_query($query_elperiodo, $vacantes) or die(mysql_error());
$row_elperiodo = mysql_fetch_assoc($elperiodo);
$totalRows_elperiodo = mysql_num_rows($elperiodo); 


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

$IDmatriz = $row_usuario['IDmatriz'];

$query_mayor = "SELECT Max(sed_clima.IDempleado) as valor FROM sed_clima WHERE manual = 1"; 
$mayor = mysql_query($query_mayor, $vacantes) or die(mysql_error());
$row_mayor = mysql_fetch_assoc($mayor);
$el_usuario = $row_mayor['valor'] + 1;

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//Preguntas
$query_pregunta = "SELECT * FROM sed_clima_preguntas";
mysql_query("SET NAMES 'utf8'");
$pregunta = mysql_query($query_pregunta, $vacantes) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$pregunta_texto = $row_pregunta['pregunta_texto'];

if (isset($_POST['IDempleado'])) {	
		$IDempleado = $_POST['IDempleado'];
		$IDpregunta = 1;
		$IDjefe = $_POST['IDjefe'];

$query_adicional = "SELECT * FROM prod_activos WHERE IDempleado = '$IDempleado'";
$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
$row_adicional = mysql_fetch_assoc($adicional);
$emp_paterno = $row_boss['emp_paterno'];
$emp_materno = $row_boss['emp_materno'];
$emp_nombre = $row_boss['emp_nombre'];
$denominacion = $row_boss['denominacion'];
$IDpuesto = $row_boss['IDpuesto'];

$query_boss = "SELECT * FROM prod_activos WHERE IDempleado = '$IDjefe'";
$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
$j_emp_paterno = $row_boss['emp_paterno'];
$j_emp_materno = $row_boss['emp_materno'];
$j_emp_nombre = $row_boss['emp_nombre'];
$j_denominacion = $row_boss['denominacion'];
$j_IDpuesto = $row_boss['IDpuesto'];
$j_IDarea = $row_boss['IDarea'];

foreach($_POST['IDrespuesta'] as $selected){
		
		$IDarea = $_POST['IDarea'];
		$updateSQL = "INSERT INTO sed_clima (IDempleado, anio, IDperiodo, fecha, IDpregunta, IDrespuesta, IDmatriz, IDarea, IDjefe, manual, emp_paterno, emp_materno, emp_nombre, denominacion, IDpuesto, j_emp_paterno, j_emp_materno, j_emp_nombre, j_denominacion, j_IDpuesto, j_IDarea) VALUES ('$el_usuario', '$anio', '$IDperiodo', '$fecha', '$IDpregunta', '$selected', '$IDmatriz', '$IDarea', '$IDjefe', 1, '$emp_paterno', '$emp_materno', '$emp_nombre', '$denominacion', '$IDpuesto', '$j_emp_paterno', '$j_emp_materno', '$j_emp_nombre', '$j_denominacion', '$j_IDpuesto', '$j_IDarea')"; 
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	
		$IDpregunta = $IDpregunta + 1;
	}
header("Location: clima_captura.php?info=1");
}

// borrar alternativo
if (isset($_GET['borrar'])) {
  
  $IDempleado = $_GET['IDempleado'];
  $deleteSQL = "DELETE FROM sed_clima WHERE IDempleado = '$IDempleado' AND IDperiodo = '$IDperiodo'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: clima_captura.php?info=3");
}

// select para Jefe
if(isset($_GET['noboss'])) {
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE IDpuesto IN (87, 145, 146, 147, 148, 149, 150, 120, 250, 252, 95, 96, 176, 253, 254, 121, 154, 177, 97, 98, 203, 221, 211, 202, 209, 255, 220, 207, 227, 232, 218, 219, 222, 204, 225, 214, 217, 233, 256, 215, 234, 272, 241, 257, 205, 224, 262, 223, 261, 258, 208, 231, 216, 99, 100, 101, 102, 122, 10, 123, 36, 103, 124, 37, 125, 180, 181, 126, 11, 12, 13, 182, 201, 127, 128, 129, 51, 130, 131, 183, 184, 265, 264, 266, 267, 191, 213, 192, 17, 270, 56, 58, 193, 198, 235, 237, 238, 239, 240) OR manual IS NOT NULL  ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);

} else {
	
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE (IDmatriz = '$la_matriz' AND IDpuesto IN (87, 145, 146, 147, 148, 149, 150, 120, 250, 252, 95, 96, 176, 253, 254, 121, 154, 177, 97, 98, 203, 221, 211, 202, 209, 255, 220, 207, 227, 232, 218, 219, 222, 204, 225, 214, 217, 233, 256, 215, 234, 272, 241, 257, 205, 224, 262, 223, 261, 258, 208, 231, 216, 99, 100, 101, 102, 122, 10, 123, 36, 103, 124, 37, 125, 180, 181, 126, 11, 12, 13, 182, 201, 127, 128, 129, 51, 130, 131, 183, 184, 265, 264, 266, 267, 191, 213, 192, 17, 270, 56, 58, 193, 198, 235, 237, 238, 239, 240)) OR manual IS NOT NULL AND IDmatriz = '$la_matriz' ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);
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

	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/general_widgets_stats.js"></script>
	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>
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

					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Clima Laboral: Captura</h6>
								</div>

								<div class="panel-body">
								<p>&nbsp;</p>
								<p>Periodo: <b><?php echo $row_elperiodo['periodo']; ?></b></p>
								<p>Instrucciones. Ingrese las respuestas de acuerdo a la siguiente numeración:</p>
                               <p><strong>3.- Totalmente de acuerdo.</strong></p>
                               <p><strong>2.- Parcialmente de acuerdo.</strong></p>
                               <p><strong>1.- En desacuerdo.</strong></p>
                               <p><strong>0.- Pregunta sin responder.</strong></p>
                                <p>&nbsp;</p>
                                
    						<form method="post" id="form1" action="clima_captura_edit.php?IDempleado=1058" class="form-horizontal form-validate-jquery">

								<fieldset class="content-group">


									<!-- Basic select -->
                            <div class="form-group row">
                              <div class="col-xs-6">
									<div class="form-group">
										<label class="control-label col-lg-4">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-8">
											<select name="IDarea" id="IDarea" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_area['IDarea']?>"><?php echo $row_area['area']?></option>
												  <?php
												 } while ($row_area = mysql_fetch_assoc($area));
												   $rows = mysql_num_rows($area);
												   if($rows > 0) {
												   mysql_data_seek($area, 0);
												   $row_area = mysql_fetch_assoc($area);
												 } ?>
											</select>
										</div>
									</div>
								</div>
							</div>


									<!-- Live search support -->
								<div class="form-group">
                              <div class="col-xs-6">
									<div class="form-group">
										<label class="control-label col-lg-4">Jefe Inmediato:<span class="text-danger">*</span></label>
										<div class="col-lg-8">
										<select class="bootstrap-select" data-live-search="true" data-width="100%" name="IDjefe" id="IDjefe" required="required">
										<option value="">Selecciona el Jefe Inmediato</option>
                                        			  <?php  do { ?>
													  <option value="<?php echo $row_jefes['IDempleado']?>"><?php echo $row_jefes['emp_nombre'] . " " . $row_jefes['emp_paterno'] . " " . $row_jefes['emp_materno'] .  " (". $row_jefes['denominacion'] . ")"; ?></option>
													  <?php
													 } while ($row_jefes = mysql_fetch_assoc($jefes));
													   $rows = mysql_num_rows($jefes);
													   if($rows > 0) {
													   mysql_data_seek($jefes, 0);
													   $row_jefes = mysql_fetch_assoc($jefes);
													 } ?>
										</select>
									</div>
								</div>
							</div>								
							<a href="clima_captura_edit.php?noboss=1" class="label label-warning">Haz clic aqui </a> para ampliar la lista.		 
							</div>


									
                            <?php do { ?>
                            <div class="form-group row">
                              <div class="col-xs-6">
                                <label>
							<?php 		if( $row_pregunta['pregunta_tipo'] == 3 ) { echo ""; } else { // oculta pregunta de jefe inmediato ?>
                            
                                <?php echo $row_pregunta['IDpregunta'] - 1;?>.- (<strong><?php echo $row_pregunta['pregunta_dimension'];?></strong>): 
								<?php echo $row_pregunta['pregunta_texto'];?>
                                
                            <?php } ?></label>
                              </div>
                              <div class="col-xs-2">
                              <div class="input-group input-group-sm">
                            <?php 		if( $row_pregunta['pregunta_tipo'] == 1 ) { // para las dos ultimas abiertas ?>
                            
                                <input class="form-control" id="IDrespuesta[]" name="IDrespuesta[]" type="number" maxlength="1" min="0" max="3" required>
                                
                            <?php } elseif( $row_pregunta['pregunta_tipo'] == 3 ) { // oculta pregunta de jefe inmediato ?>
                            
                                <input class="form-control" id="IDrespuesta[]" name="IDrespuesta[]" type="hidden" value="0">                            
                                
                            <?php } else { ?>
                            
                                <input class="form-control" id="IDrespuesta[]" name="IDrespuesta[]" type="text">                            
                                
                            <?php } ?>
                              </div>
                            </div>
                            </div>
 							<?php } while ($row_pregunta = mysql_fetch_assoc($pregunta)); ?>

								<p>&nbsp;</p>
                                <input type="hidden" name="manual" value="1" />
								<input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>" />
                                <input type="hidden" name="IDpregunta" value="<?php echo $row_pregunta['IDpregunta']; ?>" />
					            <input type="submit" class="btn bg-primary-700" name="MM_insert" value="Agregar Encuesta" />
                                <a class="btn bg-primary-300" href="clima_captura.php">Cancelar</a>

								</fieldset>
                            </form>                             
                                
                                
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