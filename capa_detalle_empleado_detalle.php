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
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$IDC_capa = $_GET['IDC_capa'];
mysql_select_db($database_vacantes, $vacantes);
$query_capavance = "SELECT * FROM capa_avance WHERE IDC_capa = '$IDC_capa'";
mysql_query("SET NAMES 'utf8'");
$capavance = mysql_query($query_capavance, $vacantes) or die(mysql_error());
$row_capavance = mysql_fetch_assoc($capavance);
$totalRows_capavance = mysql_num_rows($capavance);
$IDempleado = $row_capavance['IDempleado'];


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$ay1 = substr( $_POST['fecha_evento'], 8, 2 );
$am1 = substr( $_POST['fecha_evento'], 3, 2 );
$ad1 = substr( $_POST['fecha_evento'], 0, 2 );
$fecha_evento = "20".$ay1."-".$am1."-".$ad1;

$by1 = substr( $_POST['fecha_antiguedad'], 8, 2 );
$bm1 = substr( $_POST['fecha_antiguedad'], 3, 2 );
$bd1 = substr( $_POST['fecha_antiguedad'], 0, 2 );
$fecha_antiguedad = "20".$by1."-".$bm1."-".$bd1;

$cy1 = substr( $_POST['fecha_baja'], 8, 2 );
$cm1 = substr( $_POST['fecha_baja'], 3, 2 );
$cd1 = substr( $_POST['fecha_baja'], 0, 2 );
$fecha_baja = "20".$cy1."-".$cm1."-".$cd1;

if ( $_POST['activo'] == 9) {$fecha_baja = '0000-00-00';}

$IDpuesto = $_POST['IDpuesto'];
$query_puesto = "SELECT * FROM vac_puestos WHERE IDpuesto = '$IDpuesto'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);
$denominacion = $row_puesto['denominacion'];

$IDC_capa_cursos = $_POST['IDC_capa_cursos'];
$calificacion = $_POST['calificacion'];

$updateSQL = "UPDATE capa_avance SET fecha_evento = '$fecha_evento', fecha_antiguedad = '$fecha_antiguedad', fecha_baja = '$fecha_baja', IDpuesto = '$IDpuesto',  IDC_capa_cursos = '$IDC_capa_cursos', calificacion = '$calificacion', denominacion  = '$denominacion', anio = $ay1, mes = $am1  WHERE IDC_capa = '$IDC_capa'";
mysql_query("SET NAMES 'utf8'");
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header('Location: capa_detalle_empleado.php?info=2&IDempleado='.$IDempleado.'');
}


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_cursos= "SELECT * FROM capa_cursos ORDER BY nombre_curso ASC";
$cursos = mysql_query($query_cursos, $vacantes) or die(mysql_error());
$row_cursos = mysql_fetch_assoc($cursos);
$totalRows_cursos = mysql_num_rows($cursos);

mysql_select_db($database_vacantes, $vacantes);
$query_puestos= "SELECT * FROM vac_puestos ORDER BY denominacion ASC";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
	$IDC_capa_BORRAR = $_GET["IDC_capa"]; 
	$query1 = "DELETE FROM capa_avance WHERE IDC_capa = '$IDC_capa_BORRAR'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header('Location: capa_detalle_empleado.php?info=4&IDempleado='.$IDempleado.'');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

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
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
    <script>
	function showUser(str) {
	  if (str == 0) {
	  } else {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		  if (this.readyState == 4 && this.status == 200) {
			document.getElementById("txtHint").innerHTML = this.responseText;
		  }
		};
		xmlhttp.open("GET","empleados_get_user.php?q="+str,true);
		xmlhttp.send();
	  }
	}
	</script>

</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?> onLoad="showUser()">
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

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
					<div class="panel-body">
					<p>Algunos campos son obligatorios.</p>
                    <p>&nbsp;</p>
                    <div>
                    <div>
                    
                    
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                      

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No. Empleado.</label>
										<div class="col-lg-9">
											<?php echo $IDempleado ?>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:</label>
										<div class="col-lg-9">
                                        <?php echo $row_capavance['emp_paterno']." ".$row_capavance['emp_materno']." ".$row_capavance['emp_nombre'] ?>
										</div>
									</div>
									<!-- /basic text input -->
                                      
                                <!-- Basic select -->
                                <div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto" id="IDpuesto" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if (!(strcmp($row_puestos['IDpuesto'], $row_capavance['IDpuesto']))) {echo " selected=\"selected\"";} ?>><?php echo $row_puestos['denominacion']?></option>
												  <?php
												 } while ($row_puestos = mysql_fetch_assoc($puestos));
												   $rows = mysql_num_rows($puestos);
												   if($rows > 0) {
												   mysql_data_seek($puestos, 0);
												   $row_puestos = mysql_fetch_assoc($puestos);
												 } ?>
											</select>
										</div>
									</div>


                                    <!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Antiguedad:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_antiguedad" id="fecha_antiguedad" value="<?php echo date('d/m/Y', strtotime($row_capavance['fecha_antiguedad'])); ?>" required>
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Baja:</label>
			                        <div class="col-lg-7">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    <input type="text" class="form-control  daterange-single" name="fecha_baja" id="fecha_baja" value="<?php if ($row_capavance['fecha_baja'] = '0000-00-00') { echo "";} else { echo date('d/m/Y', strtotime($row_capavance['fecha_baja']));} ?>" required>
									</div>
                                    </div>	

                                    <div class="col-lg-2">
                                    <div class="input-group">
                                    <div class="checkbox">
											<label>
												<input type="checkbox" id="activo" name="activo" value=9 <?php if ($row_capavance['fecha_baja'] = '0000-00-00') { echo "checked='checked'";} ?>> Activo
											</label>
										</div>
                                    </div>			
                                   </div>

                                  </div> 
								<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha del Evento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    <input type="text" class="form-control  daterange-single" name="fecha_evento" id="fecha_evento" value="<?php echo date('d/m/Y', strtotime($row_capavance['fecha_evento'])); ?>" required>
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->


                                <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Evento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDC_capa_cursos" id="IDC_capa_cursos" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_cursos['IDC_capa_cursos']?>"<?php if (!(strcmp($row_cursos['IDC_capa_cursos'], $row_capavance['IDC_capa_cursos']))) {echo " selected=\"selected\"";} ?>><?php echo $row_cursos['nombre_curso']?></option>
												  <?php
												 } while ($row_cursos = mysql_fetch_assoc($cursos));
												   $rows = mysql_num_rows($cursos);
												   if($rows > 0) {
												   mysql_data_seek($cursos, 0);
												   $row_cursos = mysql_fetch_assoc($cursos);
												 } ?>
											</select>
										</div>
									</div>


                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Calificación:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input name="calificacion" id="calificacion" type="text" class="form-control" id="a_rfc"  value="<?php echo $row_capavance['calificacion'] ?>"  maxlength="18" placeholder="Calificacion con decimales" required="required">
										</div>
									</div>
									<!-- /basic text input -->
 

                          <div class="text-right">
                            <div>
                                <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Actualizar" />
								<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-default btn-danger">Borrar</button>
								<button type="button" onClick="window.location.href='capa_detalle_empleado.php?IDempleado=<?php echo $IDempleado; ?>'" class="btn btn-default btn-icon">Regresar</button>
                       		    <input type="hidden" name="MM_update" value="form1">
                            </div>
                          </div>

                       </fieldset>
                      </form>


					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="capa_detalle_empleado_detalle.php?borrar=1&IDC_capa=<?php echo $IDC_capa; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->



                      <p>&nbsp;</p>
                    </div>
                    </div>
                    </div>
				  </div>


<!-- Footer -->
					<div class="footer text-muted">
	&copy; 2020. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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
?>
