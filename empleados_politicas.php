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

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$nombredoc = htmlentities($_POST['documento'], ENT_COMPAT, '');
	
$updateSQL = sprintf("UPDATE con_documentos SET documento=%s WHERE IDdocumento=%s",
                       GetSQLValueString($nombredoc, "text"),
                       GetSQLValueString($_POST['IDdocumento'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "empleados_politicas.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$nombredoc = htmlentities($_POST['documento'], ENT_COMPAT, '');
echo $_POST['IDarea'];
    if ($_POST['IDarea'] == '1,2') {$area = 'Almacén'; $elarea = '1,2';} 
elseif ($_POST['IDarea'] == '3,4') {$area = 'Distribución'; $elarea = '3,4';} 
elseif ($_POST['IDarea'] == '5,6') {$area = 'Ventas'; $elarea = '5,6';} 
elseif ($_POST['IDarea'] == '7,8,9,10,11,12') {$area = 'Administrativos'; $elarea = '7,8,9,10,11,12';} 

    if ($_POST['IDtipo'] == '1') {$area = 'Generales';} 

	
$insertSQL = sprintf("INSERT INTO con_documentos (documento, IDtipo, IDarea, area) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($nombredoc, "text"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($elarea, "text"),
                       GetSQLValueString($area, "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "empleados_politicas.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz1 = "SELECT * FROM vac_matriz";
$matriz1 = mysql_query($query_matriz1, $vacantes) or die(mysql_error());
$row_matriz1 = mysql_fetch_assoc($matriz1);
$totalRows_matriz1 = mysql_num_rows($matriz1);


if (isset($_POST['el_area'])) {$el_area = $_POST['el_area'];} else {$el_area = '1,2';} 

mysql_select_db($database_vacantes, $vacantes);
$query_politicas = "SELECT * FROM con_documentos WHERE IDarea = '$el_area'";
mysql_query("SET NAMES 'utf8'"); 
$politicas = mysql_query($query_politicas, $vacantes) or die(mysql_error());
$row_politicas = mysql_fetch_assoc($politicas);
$totalRows_politicas = mysql_num_rows($politicas);

if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
	$IDdocumento_b = $_GET["IDdocumento"]; 
	$query1 = "DELETE FROM con_documentos WHERE IDdocumento = '$IDdocumento_b'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: empleados_politicas.php?info=3"); 	
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
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han agregado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han actualizado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han borrado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Documentos acuse Políticas y Procedimientos</h5>
						</div>

					<div class="panel-body">
					<p>Selecciona el área.</br>				
					
				<form method="POST" action="empleados_politicas.php">
					<table class="table"><tbody><tr>
							<td><select class="form-control"  name="el_area">
                                   <option value="1,2"<?php if (!(strcmp('1,2', $el_area))) {echo "selected=\"selected\"";} ?>>Almacén</option>
                                   <option value="3,4"<?php if (!(strcmp('3,4', $el_area))) {echo "selected=\"selected\"";} ?>>Distribución</option>
                                   <option value="5,6"<?php if (!(strcmp('5,6', $el_area))) {echo "selected=\"selected\"";} ?>>Ventas</option>
                                   <option value="7,8,9,10,11,12"<?php if (!(strcmp('7,8,9,10,11,12', $el_area))) {echo "selected=\"selected\"";} ?>>Administrativos</option>
							</select></td>
                            <td>
							<button type="submit" class="btn btn-primary">Seleccionar Área</button> 
							<button type="button" data-target="#modal_agregar"  data-toggle="modal" class="btn btn-success">Agregar Documento</button>
							</td>
					</tr></tbody></table>
				</form>
                  <p>&nbsp;</p>

                    
					<table class="table table-condensed">
                    <thead> 
                    <tr class="bg-primary"> 
                      <th>ID</th>
                      <th>Documento</th>
                      <th>Tipo</th>
                      <th>Área</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php do { ?>
                        <tr>
                            <td><?php echo $row_politicas['IDdocumento']; ?>&nbsp;  </td>
                            <td><?php echo $row_politicas['documento']; ?>&nbsp;  </td>
                            <td><?php echo $row_politicas['area']; ?>&nbsp;  </td>
                            <td><?php 
								if ($row_politicas['IDarea'] == '1,2') {echo 'Almacén';} 
							elseif ($row_politicas['IDarea'] == '3,4') {echo 'Distribución';} 
							elseif ($row_politicas['IDarea'] == '5,6') {echo 'Ventas';} 
							elseif ($row_politicas['IDarea'] == '7,8,9,10,11,12') {echo 'Administrativos';} 
							else {echo '-';} 
							?>&nbsp;  </td>
                            <td>
							<button type="button" data-target="#modal_actualizar<?php echo $row_politicas['IDdocumento']; ?>"  data-toggle="modal" class="btn btn-primary  btn-sm">Actualizar</button>
							<button type="button" data-target="#modal_borrar<?php echo $row_politicas['IDdocumento']; ?>"  data-toggle="modal" class="btn btn-danger btn-sm">Borrar</button>
							</td>
                        </tr>
						
						
									<!-- danger modal -->
									<div id="modal_borrar<?php echo $row_politicas['IDdocumento']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de borrado</h6>
												</div>
												<div class="modal-body">
												<p>¿Estas seguro que quieres borrar el registro?</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="empleados_politicas.php?IDdocumento=<?php echo $row_politicas['IDdocumento']; ?>&borrar=1">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->



									 <!-- danger modal -->
									<div id="modal_actualizar<?php echo $row_politicas['IDdocumento']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Actualizar Documento</h6>
												</div>
												<div class="modal-body">
																			
														<form action="empleados_politicas.php" method="post" name="form1" id="form1" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Nombre del Documento:</label>
															  <div class="col-lg-9">
																	<input name="documento" id="documento" type="text" class="form-control" value="<?php echo $row_politicas['documento']; ?>" required="required">
															 </div>
														  </div>
														  <!-- /basic text input -->


														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-primary">Actualizar</button> 
													<input type="hidden" name="MM_update" value="form1" />
													<input type="hidden" name="IDdocumento" value="<?php echo $row_politicas['IDdocumento']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->
						
					 <?php } while ($row_politicas = mysql_fetch_assoc($politicas)); ?>
                    </tbody>
                   </table> 



									 <!-- danger modal -->
									<div id="modal_agregar" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Agregar Documento</h6>
												</div>
												<div class="modal-body">
																			
														<form action="empleados_politicas.php" method="post" name="form1" id="form1" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Nombre del Documento:</label>
															  <div class="col-lg-9">
																	<input name="documento" id="documento" type="text" class="form-control" value="" required="required">
															 </div>
														  </div>
														  <!-- /basic text input -->

														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Área:</label>
															  <div class="col-lg-9">
															<select name="IDarea" id="IDarea" class="form-control" required="required">
																<option value="1,2">Almacén</option>
																<option value="3,4">Distribución</option>
																<option value="5,6">Ventas</option>
																<option value="7,8,9,10,11,12">Administrativos</option>
															</select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Tipo:</label>
															  <div class="col-lg-9">
															<select name="IDtipo" id="IDtipo" class="form-control" required="required">
																<option value="1">General</option>
																<option value="2">Específico del Área</option>
															</select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-success">Agregar</button> 
													<input type="hidden" name="MM_insert" value="form1" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->                                     

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