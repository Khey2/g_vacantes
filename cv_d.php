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
$query_file1 = "SELECT * FROM cv_dependientes WHERE IDtipo = 1 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file1 = mysql_query($query_file1, $vacantes) or die(mysql_error());
$row_file1 = mysql_fetch_assoc($file1);
$totalRows_file1 = mysql_num_rows($file1);

mysql_select_db($database_vacantes, $vacantes);
$query_file2 = "SELECT * FROM cv_dependientes WHERE IDtipo = 2 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file2 = mysql_query($query_file2, $vacantes) or die(mysql_error());
$row_file2 = mysql_fetch_assoc($file2);
$totalRows_file2 = mysql_num_rows($file2);

mysql_select_db($database_vacantes, $vacantes);
$query_file3 = "SELECT * FROM cv_dependientes WHERE IDtipo = 3 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file3 = mysql_query($query_file3, $vacantes) or die(mysql_error());
$row_file3 = mysql_fetch_assoc($file3);
$totalRows_file3 = mysql_num_rows($file3);

mysql_select_db($database_vacantes, $vacantes);
$query_file4 = "SELECT * FROM cv_dependientes WHERE IDtipo = 4 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file4 = mysql_query($query_file4, $vacantes) or die(mysql_error());
$row_file4 = mysql_fetch_assoc($file4);
$totalRows_file4 = mysql_num_rows($file4);

mysql_select_db($database_vacantes, $vacantes);
$query_file5 = "SELECT * FROM cv_dependientes WHERE IDtipo = 5 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file5 = mysql_query($query_file5, $vacantes) or die(mysql_error());
$row_file5 = mysql_fetch_assoc($file5);
$totalRows_file5 = mysql_num_rows($file5);

mysql_select_db($database_vacantes, $vacantes);
$query_file6 = "SELECT * FROM cv_dependientes WHERE IDtipo = 6 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file6 = mysql_query($query_file6, $vacantes) or die(mysql_error());
$row_file6 = mysql_fetch_assoc($file6);
$totalRows_file6 = mysql_num_rows($file6);

mysql_select_db($database_vacantes, $vacantes);
$query_file7 = "SELECT * FROM cv_dependientes WHERE IDtipo = 7 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file7 = mysql_query($query_file7, $vacantes) or die(mysql_error());
$row_file7 = mysql_fetch_assoc($file7);
$totalRows_file7 = mysql_num_rows($file7);

mysql_select_db($database_vacantes, $vacantes);
$query_file8 = "SELECT * FROM cv_dependientes WHERE IDtipo = 8 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file8 = mysql_query($query_file8, $vacantes) or die(mysql_error());
$row_file8 = mysql_fetch_assoc($file8);
$totalRows_file8 = mysql_num_rows($file8);

mysql_select_db($database_vacantes, $vacantes);
$query_file9 = "SELECT * FROM cv_dependientes WHERE IDtipo = 9 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file9 = mysql_query($query_file9, $vacantes) or die(mysql_error());
$row_file9 = mysql_fetch_assoc($file9);
$totalRows_file9 = mysql_num_rows($file9);

mysql_select_db($database_vacantes, $vacantes);
$query_file10 = "SELECT * FROM cv_dependientes WHERE IDtipo = 10 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file10 = mysql_query($query_file10, $vacantes) or die(mysql_error());
$row_file10 = mysql_fetch_assoc($file10);
$totalRows_file10 = mysql_num_rows($file10);
$mira =  $row_file10['nombre'] ;

mysql_select_db($database_vacantes, $vacantes);
$query_file11 = "SELECT * FROM cv_dependientes WHERE IDtipo = 11 AND IDusuario = '$IDusuario' AND borrado = 0 AND IDref = 'f'";
$file11 = mysql_query($query_file11, $vacantes) or die(mysql_error());
$row_file11 = mysql_fetch_assoc($file11);
$totalRows_file11 = mysql_num_rows($file11);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$IDusuario = $_POST['IDusuario'];
$insertSQL = sprintf("INSERT INTO cv_dependientes (edad, estatus, domicilio, telefono, ocupacion, IDusuario, nombre, paterno, materno, IDtipo, IDref, observaciones) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString(htmlentities($_POST['edad'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['estatus'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['domicilio'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['telefono'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['ocupacion'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['IDusuario'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['nombre'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['paterno'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['materno'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['IDtipo'], ENT_COMPAT, ''), "int"),
						GetSQLValueString(htmlentities($_POST['IDref'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['observaciones'], ENT_COMPAT, ''), "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

	header("Location: cv_d.php?IDusuario=$IDusuario&info=1"); 	
}

//borrado
if(isset($_GET['id'])) {
     $id = $_GET['id'];

    $query2 = "UPDATE cv_dependientes SET borrado = 1 WHERE id = '$id'"; 
    $result2 = mysql_query($query2) or die(mysql_error());  

	header("Location: cv_d.php?IDusuario=$IDusuario&info=1"); 	

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
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>

	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
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
							Se ha agregado correctamente el Familiar.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el Familiar.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Datos Familiares</h5>
						</div>


					<div class="panel-body">
					<p><strong>Instrucciones</strong>: ingrese la información solicitada. </br>
                    Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>

					<legend class="text-semibold">Candidato: <?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno']. " " . $row_candidatos['a_nombre']; ?></legend>
					<h6><strong>Agregar</strong></h6>
                  
                  
                      <form method="post" name="form1" action="cv_d.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre(s):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombres" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="paterno" id="paterno" class="form-control" placeholder="Paterno" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Materno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="materno" id="materno" class="form-control" placeholder="Materno" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">Edad (años):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="number" name="edad" id="edad" class="form-control" placeholder="Edad en años" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Parentesco:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo" id="IDtipo" class="form-control" required="required">
												<option value = "" >Seleccione una opción</option> 
												<option value = "1" >Esposo(a), Concubino(a)</option> 
												<option value = "2" >Padre</option> 
												<option value = "3" >Madre</option> 
												<option value = "4" >Hijo(a)</option> 
												<option value = "7" >Hermano(a)</option> 
												<option value = "5" >Otro (indique en observaciones)</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Vive?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="estatus" id="estatus" class="form-control" required="required">
												<option value = "" >Seleccione una opción</option> 
												<option value = "1" >Si</option> 
												<option value = "2" >No</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic text input -->
									 <div class="form-group">
										<label class="control-label col-lg-3">Dirección:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="domicilio" id="domicilio" class="form-control" placeholder="Dirección completa" value="" required>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Telefono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono de contacto" value="" >
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Ocupacion:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="ocupacion" id="ocupacion" class="form-control" placeholder="Ocupación actual" value="" required>
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Observaciones:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="observaciones" id="observaciones" class="form-control" placeholder="Observaciones" value="">
										</div>
									</div>
									<!-- /basic text input -->

                          <div class="text-right">
                            <div>
                                <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Guardar" />
                    			<input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
                    			<input type="hidden" name="IDref" value="f">
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
		              <th>Parentesco</th>
		              <th>Nombre Completo</th>
                      <th>¿Vive?</th>
                      <th>Edad</th>
                      <th>Domicilio</th>
                      <th>Teléfono</th>
                      <th>Ocupación</th>
                      <th>Observaciones</th>
		              <th>Acciones</th>
		            </tr>
		            </thead>
		          <tbody>
		        <tr>
                
                <?php if ($totalRows_file1 == 0) { ?>
                   <tr>
                     <td>Esposo(a), Concubino(a)</td>
                     <td>No reportado</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
                     <td>-</td>
</tr>
                <?php } else { ?> 
                 <?php  do {  ?>
		          <tr>
                     <td>Esposo(a), Concubino(a)</td>
                    <td><?php echo $row_file1['nombre'] . " " . $row_file1['paterno'] . " " . $row_file1['materno'];?></td>
                    <td><?php if ($row_file1['estatus'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><?php echo $row_file1['edad']; ?></td>
                    <td><?php echo $row_file1['domicilio']; ?></td>
                    <td><?php echo $row_file1['telefono']; ?></td>
                    <td><?php echo $row_file1['ocupacion']; ?></td>
                    <td><?php echo $row_file1['observaciones']; ?></td>
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file1['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file1 = mysql_fetch_assoc($file1)); ?>
                <?php }?>



                <?php if ($totalRows_file2 == 0) { ?>
                   <tr>
                     <td>Padre</td>
                     <td>No reportado</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
                     <td>-</td>
				  </tr>
                <?php } else { ?> 
                 <?php  do {  ?>
		          <tr>
                     <td>Padre</td>
                    <td><?php echo $row_file2['nombre'] . " " . $row_file2['paterno'] . " " . $row_file2['materno'];?></td>
                    <td><?php if ($row_file2['estatus'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><?php echo $row_file2['edad']; ?></td>
                    <td><?php echo $row_file2['domicilio']; ?></td>
                    <td><?php echo $row_file2['telefono']; ?></td>
                    <td><?php echo $row_file2['ocupacion']; ?></td>
                    <td><?php echo $row_file2['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file2['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file2['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file2['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file2 = mysql_fetch_assoc($file2)); ?>
                <?php }?>


                <?php if ($totalRows_file3 == 0) { ?>
                   <tr>
                     <td>Madre</td>
                     <td>No reportado</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
                     <td>-</td>
</tr>
                <?php } else { ?> 
                 <?php  do {  ?>
		          <tr>
                     <td>Madre</td>
                    <td><?php echo $row_file3['nombre'] . " " . $row_file3['paterno'] . " " . $row_file3['materno'];?></td>
                    <td><?php if ($row_file3['estatus'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><?php echo $row_file3['edad']; ?></td>
                    <td><?php echo $row_file3['domicilio']; ?></td>
                    <td><?php echo $row_file3['telefono']; ?></td>
                    <td><?php echo $row_file3['ocupacion']; ?></td>
                    <td><?php echo $row_file3['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file3['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file3['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file3['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file3 = mysql_fetch_assoc($file3)); ?>
                <?php }?>


                <?php if ($totalRows_file4 == 0) { ?>
                   <tr>
                     <td>Hijo(a)</td>
                     <td>No reportado</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
					 <td>-</td>
                     <td>-</td>
				</tr>
                <?php } else { ?> 
                 <?php  do {  ?>
		          <tr>
                     <td>Hijo(a)</td>
                    <td><?php echo $row_file4['nombre'] . " " . $row_file4['paterno'] . " " . $row_file4['materno'];?></td>
                    <td><?php if ($row_file4['estatus'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><?php echo $row_file4['edad']; ?></td>
                    <td><?php echo $row_file4['domicilio']; ?></td>
                    <td><?php echo $row_file4['telefono']; ?></td>
                    <td><?php echo $row_file4['ocupacion']; ?></td>
                    <td><?php echo $row_file4['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file4['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file4['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file4['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file4 = mysql_fetch_assoc($file4)); ?>
                <?php }?>


                <?php if ($totalRows_file5 != 0) { ?> 
                 <?php  do { ?>
		          <tr>
                     <td>Otro</td>
                    <td><?php echo $row_file5['nombre'] . " " . $row_file5['paterno'] . " " . $row_file5['materno'];?></td>
                    <td><?php if ($row_file5['estatus'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><?php echo $row_file5['edad']; ?></td>
                    <td><?php echo $row_file5['domicilio']; ?></td>
                    <td><?php echo $row_file5['telefono']; ?></td>
                    <td><?php echo $row_file5['ocupacion']; ?></td>
                    <td><?php echo $row_file5['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file5['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file5['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file5['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file5 = mysql_fetch_assoc($file5)); ?>
                <?php }?>


                <?php if ($totalRows_file6 != 0) { ?> 
                 <?php  do { ?>
		          <tr>
                     <td>Nieto(a)</td>
                    <td><?php echo $row_file6['nombre'] . " " . $row_file6['paterno'] . " " . $row_file6['materno'];?></td>
                    <td><?php if ($row_file6['estatus'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><?php echo $row_file6['edad']; ?></td>
                    <td><?php echo $row_file6['domicilio']; ?></td>
                    <td><?php echo $row_file6['telefono']; ?></td>
                    <td><?php echo $row_file6['ocupacion']; ?></td>
                    <td><?php echo $row_file6['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file6['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file6['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file6['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file6 = mysql_fetch_assoc($file6)); ?>
                <?php }?>


                <?php if ($totalRows_file7 != 0) { ?> 
                 <?php  do { ?>
		          <tr>
                     <td>Hermano(a)</td>
                    <td><?php echo $row_file7['nombre'] . " " . $row_file7['paterno'] . " " . $row_file7['materno'];?></td>
                    <td><?php if ($row_file7['estatus'] == 1) {echo "Si";} else {echo "No";} ?></td>
                    <td><?php echo $row_file7['edad']; ?></td>
                    <td><?php echo $row_file7['domicilio']; ?></td>
                    <td><?php echo $row_file7['telefono']; ?></td>
                    <td><?php echo $row_file7['ocupacion']; ?></td>
                    <td><?php echo $row_file7['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file7['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file7['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file7['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
		          </tr>
                 <?php } while ($row_file7 = mysql_fetch_assoc($file7)); ?>
                <?php }?>


                <?php if ($totalRows_file8 != 0) { ?> 
                 <?php  do {  ?>
		          <tr>
                     <td>Tio(a)</td>
                    <td><?php echo $row_file8['nombre'] . " " . $row_file9['paterno'] . " " . $row_file9['materno'];?></td>
                    <td><?php echo $row_file8['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file8['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file8['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file8['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file8 = mysql_fetch_assoc($file8)); ?>
                <?php }?>


                <?php if ($totalRows_file9 != 0) { ?> 
                 <?php  do { ?>
		          <tr>
                     <td>Sobirno(a)</td>
                    <td><?php echo $row_file9['nombre'] . " " . $row_file9['paterno'] . " " . $row_file9['materno'];?></td>
                    <td><?php echo $row_file9['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file9['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file9['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file9['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file9 = mysql_fetch_assoc($file9)); ?>
                <?php }?>


                <?php if ($totalRows_file10 != 0)  { ?> 
                 <?php  do { ?>
		          <tr>
                     <td>Suegro(a)</td>
                    <td><?php echo $row_file10['nombre'] . " " . $row_file10['paterno'] . " " . $row_file10['materno'];?></td>
                    <td><?php echo $row_file10['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file10['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file10['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file10['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file10 = mysql_fetch_assoc($file10)); ?>
                <?php }?>


                <?php if ($totalRows_file11 != 0) {?> 
                 <?php  do {  ?>
		          <tr>
                     <td>Otro (sin parentezco familiar)</td>
                    <td><?php echo $row_file11['nombre'] . " " . $row_file11['paterno'] . " " . $row_file11['materno'];?></td>
                    <td><?php echo $row_file11['observaciones']; ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file11['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file11['id']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="cv_d.php?IDusuario=<?php echo $IDusuario; ?>&id=<?php echo $row_file11['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file11 = mysql_fetch_assoc($file11)); ?>
                <?php }?>

                
                  </tbody>
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
