<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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

$IDmatriz = $row_usuario['IDmatriz'];

$IDusuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


if(isset($_POST['IDtipo']) && ($_POST['IDtipo']  > 0)) {$_SESSION['IDtipo'] = $_POST['IDtipo']; } 
if(!isset( $_SESSION['IDtipo'])) {$_SESSION['IDtipo'] = 1; } 

$IDtipo = $_SESSION['IDtipo'];


mysql_select_db($database_vacantes, $vacantes);
$query_catalogo = "SELECT capa_becarios_preguntas.IDpregunta, capa_becarios_preguntas.IDtipo_preg, capa_becarios_preguntas.IDtipo, capa_becarios_preguntas.IDtipo_opciones, capa_becarios_preguntas.pregunta,  capa_becarios_tipo_preg.tipo_preg, capa_becarios_tipo.tipo FROM capa_becarios_preguntas LEFT JOIN capa_becarios_tipo_preg ON capa_becarios_preguntas.IDtipo_preg = capa_becarios_tipo_preg.IDtipo_preg LEFT JOIN capa_becarios_tipo ON capa_becarios_preguntas.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios_preguntas.IDtipo = $IDtipo";
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);
$totalRows_catalogo = mysql_num_rows($catalogo);

mysql_select_db($database_vacantes, $vacantes);
$query_programa = "SELECT * FROM capa_becarios_tipo";
$programa = mysql_query($query_programa, $vacantes) or die(mysql_error());
$row_programa = mysql_fetch_assoc($programa);
$totalRows_programa = mysql_num_rows($programa);

mysql_select_db($database_vacantes, $vacantes);
$query_tipo_pregunta = "SELECT * FROM capa_becarios_tipo_preg";
mysql_query("SET NAMES 'utf8'");
$tipo_pregunta = mysql_query($query_tipo_pregunta, $vacantes) or die(mysql_error());
$row_tipo_pregunta = mysql_fetch_assoc($tipo_pregunta);
$totalRows_tipo_pregunta = mysql_num_rows($tipo_pregunta);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE capa_becarios_preguntas SET pregunta=%s, IDusuario=%s WHERE IDpregunta=%s",
                       GetSQLValueString($_POST['pregunta'], "text"),
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($_POST['IDpregunta'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: capa_becarios_catalogos_d.php?info=2"); 	
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO capa_becarios_preguntas (IDusuario, IDtipo_preg, IDpreguntaNum, IDtipo_opciones, IDtipo, pregunta) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($_POST['IDtipo_preg'], "int"),
                       GetSQLValueString($_POST['IDpreguntaNum'], "int"),
                       GetSQLValueString($_POST['IDtipo_opciones'], "int"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['pregunta'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  $captura = mysql_insert_id();
  header("Location: capa_becarios_catalogos_d.php?info=1"); 	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDpregunta'];
  $deleteSQL = "DELETE FROM capa_becarios_preguntas WHERE IDpregunta = $borrado";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_becarios_catalogos_d.php?info=3");
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
	<script src="global_assets/js/plugins/tables/datatables/bec_datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html_bec.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
	<!-- /theme JS files -->
</head>
<body> 
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
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona el registro para editarlo.</p>
							


										<form action="capa_becarios_catalogos_d.php" method="POST">

											<div class="form-group">
													<select class="form-control" name="IDtipo">
													   <option value="">Programa (Todos)</option>
													   <?php do { ?>
													   <option value="<?php echo $row_programa['IDtipo']?>"<?php if (!(strcmp($row_programa['IDtipo'], $IDtipo))) {echo "selected=\"selected\"";} ?>><?php echo $row_programa['tipo']?></option>
													   <?php } while ($row_programa = mysql_fetch_assoc($programa)); $rows = mysql_num_rows($programa);  if($rows > 0) { mysql_data_seek($programa, 0); $row_programa = mysql_fetch_assoc($programa); } ?> 
													</select>
											</div>
											
											<button type="submit" class="btn bg-blue"><i class="icon-search4 text-size-base position-left"></i>Filtrar</button>
											<button type="button" data-target="#modal_theme_agregar"  data-toggle="modal" class="btn bg-success">Agregar registro</button>
										</form>
						
						<p>&nbsp;</p>

			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>ID</th>
                          <th>Programa</th>
                          <th>Tipo de Pregunta</th>
                          <th>Tipo de Respuesta</th>
                          <th>Pregunta</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php if ($totalRows_catalogo > 0) {  ?>
                      <?php do {  ?>
                        <tr>
                          <td><?php echo $row_catalogo['IDpregunta']; ?></td>
                          <td><?php echo $row_catalogo['tipo']; ?></td>
                          <td><?php echo $row_catalogo['tipo_preg']; ?></td>
                          <td><?php if ($row_catalogo['IDtipo_opciones'] == 1) {echo "3";} elseif ($row_catalogo['IDtipo_opciones'] == 3) {echo "Obs";} else {echo "5";} ?></td>
                          <td><?php echo $row_catalogo['pregunta']; ?></td>
                         <td>
						 <button type="button" data-target="#modal_theme_actualizar<?php echo $row_catalogo['IDpregunta']; ?>"  data-toggle="modal" class="btn bg-primary">Actualizar</button>
						 <button type="button" data-target="#modal_theme_danger<?php echo $row_catalogo['IDpregunta']; ?>"  data-toggle="modal" class="btn bg-danger">Borrar</button></td>
                        </tr> 




                     <!-- danger modal -->
					<div id="modal_theme_actualizar<?php echo $row_catalogo['IDpregunta']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
									<div class="modal-header bg-primary">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h6 class="modal-title">Actualizar registro</h6>
									</div>

									<form action="capa_becarios_catalogos_d.php" method="post" name="importar" id="importar">
									<div class="modal-body">
									<fieldset>
										 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Programa:</label>
											  <div class="col-lg-8">
												<?php echo $row_catalogo['tipo']; ?>
											 </div>
										  </div>
										  <!-- /basic text input -->
														<p>&nbsp;</p>

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Grupo de Pregunta:</label>
											  <div class="col-lg-8">
												<?php echo $row_catalogo['tipo_preg']; ?>
											 </div>
										  </div>
										  <!-- /basic text input -->
														<p>&nbsp;</p>

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Tipo de Pregunta:</label>
											  <div class="col-lg-8">
												<?php    if ($row_catalogo['IDtipo_opciones'] == 1) {echo "3 Opciones";} 
													else if ($row_catalogo['IDtipo_opciones'] == 2) {echo "5 Opciones (JCF)";}
													else if ($row_catalogo['IDtipo_opciones'] == 3) {echo "Abierta";}
													else 											{echo "-";} ?>
											 </div>
										  </div>
										  <!-- /basic text input -->
														<p>&nbsp;</p>

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Pregunta:</label>
											  <div class="col-lg-8">
												<input type="text" name="pregunta" id="pregunta" class="form-control" value="<?php echo $row_catalogo['pregunta']; ?>" placeholder="Pregunta" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

									</fieldset>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
										<button type="submit" id="submit" name="import" class="btn btn-primary">Actualizar</button> 
										<input type="hidden" name="MM_update" value="form1" />
										<input type="hidden" name="IDpregunta" value="<?php echo $row_catalogo['IDpregunta']; ?>" />
									</div>
									</form>

							</div>
						</div>
					</div>
					<!-- /danger modal -->


                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_catalogo['IDpregunta']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="capa_becarios_catalogos_d.php?IDpregunta=<?php echo $row_catalogo['IDpregunta']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

						
                        <?php } while ($row_catalogo = mysql_fetch_assoc($catalogo)); ?>
						
						<?php } else {  ?>
                          <tr><td colspan="6">No existen preguntas con el filtro seleccionado</td></tr>
						<?php }  ?>
						</tbody>							  
                 </table>

					</div>

					<!-- /Contenido -->


                     <!-- danger modal -->
					<div id="modal_theme_agregar" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Agregar registro</h6>
								</div>

									<form action="capa_becarios_catalogos_d.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
									<div class="modal-body">
									<fieldset>
														 
										 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Programa:</label>
											  <div class="col-lg-8">
													<select class="form-control" name="IDtipo">
													   <?php do { ?>
													   <option value="<?php echo $row_programa['IDtipo']?>"<?php if (!(strcmp($row_programa['IDtipo'], $IDtipo))) {echo "selected=\"selected\"";} ?>><?php echo $row_programa['tipo']?></option>
													   <?php } while ($row_programa = mysql_fetch_assoc($programa)); $rows = mysql_num_rows($programa);  if($rows > 0) { mysql_data_seek($programa, 0); $row_programa = mysql_fetch_assoc($programa); } ?> 
													</select>
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Grupo de Pregunta:</label>
											  <div class="col-lg-8">
													<select class="form-control" name="IDtipo_preg">
													   <?php do { ?>
													   <option value="<?php echo $row_tipo_pregunta['IDtipo_preg']?>"><?php echo $row_tipo_pregunta['tipo_preg']?></option>
													   <?php } while ($row_tipo_pregunta = mysql_fetch_assoc($tipo_pregunta)); $rows = mysql_num_rows($tipo_pregunta);  if($rows > 0) { mysql_data_seek($tipo_pregunta, 0); $row_tipo_pregunta = mysql_fetch_assoc($tipo_pregunta); } ?> 
													</select>
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Tipo de Pregunta:</label>
											  <div class="col-lg-8">
													<select class="form-control" name="IDtipo_opciones">
													   <option value="1">3 Opciones</option>
													   <option value="2">5 Opciones (JCF)</option>
													   <option value="3">Abierta</option>
													</select>
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Pregunta:</label>
											  <div class="col-lg-8">
												<input type="text" name="pregunta" id="pregunta" class="form-control" value="<?php echo $row_catalogo['pregunta']; ?>" placeholder="Pregunta" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->


									</fieldset>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<button type="submit" id="submit" name="import" class="btn btn-success">Agregar</button> 
											<input type="hidden" name="MM_insert" value="form1" />
											<input type="hidden" name="IDpreguntaNum" value="1" />
										</div>
									</form>

							</div>
						</div>
					</div>
					<!-- /danger modal -->





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