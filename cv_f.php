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
$la_matriz = $row_usuario['IDmatriz'];
$las_matrizes = $row_usuario['IDmatrizes'];

$IDusuario = $_GET['IDusuario'];
mysql_select_db($database_vacantes, $vacantes);
$query_candidatos = "SELECT * FROM cv_activos WHERE IDusuario = '$IDusuario'";
$candidatos = mysql_query($query_candidatos, $vacantes) or die(mysql_error());
$row_candidatos = mysql_fetch_assoc($candidatos);
$totalRows_candidatos = mysql_num_rows($candidatos);


$query_tipos = "SELECT * FROM sed_files_tipos";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

mysql_select_db($database_vacantes, $vacantes);
$query_file1 = "SELECT * FROM cv_dependientes WHERE IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'e'";
$file1 = mysql_query($query_file1, $vacantes) or die(mysql_error());
$row_file1 = mysql_fetch_assoc($file1);
$totalRows_file1 = mysql_num_rows($file1);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$y1 = substr( $_POST['fecha_inicio'], 6, 4 );
$m1 = substr( $_POST['fecha_inicio'], 3, 2 );
$d1 = substr( $_POST['fecha_inicio'], 0, 2 );
$fecha1 = $y1."-".$m1."-".$d1;

$y2 = substr( $_POST['fecha_termino'], 6, 4 );
$m2 = substr( $_POST['fecha_termino'], 3, 2 );
$d2 = substr( $_POST['fecha_termino'], 0, 2 );
$fecha2 = $y2."-".$m2."-".$d2;


$insertSQL = sprintf("INSERT INTO cv_dependientes (IDusuario, nombre, direccion, puestos, ocupacion, telefono, sueldo, causa, fecha_inicio, fecha_termino, IDref, actual, observaciones) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString(htmlentities($_POST['IDusuario'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['nombre'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['direccion'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['puestos'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['ocupacion'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['telefono'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['sueldo'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['causa'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($fecha1, ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($fecha2, ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['IDref'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['actual'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['observaciones'], ENT_COMPAT, ''), "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

	header("Location: cv_f.php?IDusuario=$IDusuario&info=1"); 	
}

//borrado
if(isset($_GET['id'])) {
     $id = $_GET['id'];
    $query2 = "UPDATE cv_dependientes SET borrado = 1 WHERE id = '$id'"; 
    $result2 = mysql_query($query2) or die(mysql_error());  
	header("Location: cv_f.php?IDusuario=$IDusuario&info=3"); 	
	}

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);

$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
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
							Se ha agregado correctamente el Empleo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el Empleo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Empleos</h5>
						</div>


					<div class="panel-body">
					<p><strong>Instrucciones</strong>: ingrese la información solicitada. 
                    Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>

					<legend class="text-semibold">Candidato: <?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno']. " " . $row_candidatos['a_nombre']; ?></legend>
					<h6><strong>Agregar</strong></h6>
                  
                  
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Empresa:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="nombre" id="nombre" class="form-control" placeholder="Empresa" value="" required>
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="direccion" id="direccion" class="form-control" placeholder="Dirección" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto - Funciones:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="puestos" id="puestos" class="form-control" placeholder="Puestos y funciones" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Final:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="sueldo" id="sueldo" class="form-control" placeholder="Sueldo final" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="" required="required">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Termino:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_termino" id="fecha_termino" value="" required="required">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Causa de Separación:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="causa" id="causa" class="form-control" placeholder="Causa de Separación" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato (Nombre):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="observaciones" id="observaciones" class="form-control" placeholder="Nombre Jefe Inmediato" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato (Puesto):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="ocupacion" id="ocupacion" class="form-control" placeholder="Puesto Jefe Inmediato" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato (Teléfono y/o correo de contacto):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono" id="telefono" class="form-control" placeholder="Contacto Jefe Inmediato" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Empleo actual?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="actual" id="actual" class="form-control"  required="required">
                            <option value="2">No</option>
                            <option value="1">Si</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                    

                          <div class="text-right">
                            <div>
                                <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Guardar" />
                    			<input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
                    			<input type="hidden" name="IDref" value="e">
                    			<input type="hidden" name="estatus" value="1">
                       		    <input type="hidden" name="MM_insert" value="form1">
                            </div>
                          </div>

                       </fieldset>
                      </form>
	                    
                  <p>&nbsp;</p>
					<h6><strong>Agregados</strong></h6>
					<table class="table">
		          <thead>
                    <tr class="bg-success"> 
		              <th>Empresa</th>
		              <th>Puesto</th>
                      <th>Periodo</th>
                      <th>Empleo actual</th>
		              <th>Acciones</th>
		            </tr>
		            </thead>
		          <tbody>
		        <tr>
                 <?php if($totalRows_file1 > 0 ) {  ?>
                 <?php  do {  ?>
		          <tr>
                     <td><?php echo $row_file1['nombre'];?></td>
                    <td><?php echo $row_file1['puestos'];?></td>
                    <td><?php echo "Del " .  date( 'd/m/Y' , strtotime($row_file1['fecha_inicio']))   . " al " .  date( 'd/m/Y' , strtotime($row_file1['fecha_termino'])) ; ?></td>
                    <td><?php if($row_file1['actual'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file1['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file1['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="cv_f.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file1['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                    
		          </tr>
                 <?php } while ($row_file1 = mysql_fetch_assoc($file1)); ?>
                 <?php } else { ?>
                 <td>Sin empleos registrados</td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <?php } ?>

                  
		          </table>                    
                    </div>
                    </div>
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
<?php
mysql_free_result($variables);
?>
