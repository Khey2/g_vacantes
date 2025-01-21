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

mysql_select_db($database_vacantes, $vacantes);
$query_catalogo = "SELECT * FROM capa_becarios_tipo_file";
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);
$totalRows_catalogo = mysql_num_rows($catalogo);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE capa_becarios_tipo_file SET tipo_file=%s, IDusuario=%s, file=%s WHERE IDtipo_file=%s",
                       GetSQLValueString($_POST['tipo_file'], "text"),
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($_POST['file'], "text"),
                       GetSQLValueString($_POST['IDtipo_file'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: capa_becarios_catalogos_e.php?info=2"); 	
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO capa_becarios_tipo_file (IDusuario, tipo_file, file) VALUES (%s, %s, %s)",
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($_POST['file'], "text"),
                       GetSQLValueString($_POST['tipo_file'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  $captura = mysql_insert_id();
  header("Location: capa_becarios_catalogos_e.php?info=1"); 	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDtipo_file'];
  $deleteSQL = "DELETE FROM capa_becarios_tipo_file WHERE IDtipo_file = $borrado";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_becarios_catalogos_e.php?info=3");
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
							
							
						<button type="button" data-target="#modal_theme_agregar"  data-toggle="modal" class="btn bg-success-400">Agregar registro</button>
						<p>&nbsp;</p>

			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>ID</th>
                          <th>Nombre</th>
                          <th>Plantilla</th>
                          <th>Programas</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do {  ?>
                        <tr>
                          <td><?php echo $row_catalogo['IDtipo_file']; ?></td>
                          <td><?php echo $row_catalogo['tipo_file']; ?></td>
                          <td><a href="<?php echo "becariosfiles/".$row_catalogo['file']; ?>" target="_blank"><?php echo $row_catalogo['file']; ?></a></td>
                          <td><ul><?php
							$Valoresa = str_replace(',', '', $row_catalogo['IDtipo']);
							$Valoresa = str_split($Valoresa); 
							foreach ($Valoresa AS &$CadaValor ) {
								$query_cat = "SELECT * FROM capa_becarios_tipo WHERE IDtipo = '$CadaValor'";
								$cat = mysql_query($query_cat, $vacantes) or die(mysql_error());
								$row_cat = mysql_fetch_assoc($cat);
								echo "<li>".$row_cat['tipo']."</li>"; 
							} ?></ul></td>
                         <td>
						 <button type="button" data-target="#modal_theme_actualizar<?php echo $row_catalogo['IDtipo_file']; ?>"  data-toggle="modal" class="btn bg-primary">Actualizar</button>
						 <button type="button" data-target="#modal_theme_danger<?php echo $row_catalogo['IDtipo_file']; ?>"  data-toggle="modal" class="btn bg-danger">Borrar</button></td>
                        </tr> 




                     <!-- danger modal -->
					<div id="modal_theme_actualizar<?php echo $row_catalogo['IDtipo_file']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar registro</h6>
								</div>

								<div class="modal-body">

									<form action="#" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
									<fieldset>
														 
										 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Tipo de Archivo:</label>
											  <div class="col-lg-8">
												<input type="text" name="tipo_file" id="tipo_file" class="form-control" value="<?php echo $row_catalogo['tipo_file']; ?>" placeholder="Nombre del Programa" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->
												
												<p>&nbsp;</p>

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Archivo:</label>
											  <div class="col-lg-8">
												<input type="text" name="file" id="file" class="form-control">
											 </div>
										  </div>
										  <!-- /basic text input -->



									</fieldset>
														
																			
												</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<input type="hidden" name="MM_update" value="form1" />
											<input type="hidden" name="IDtipo_file" value="<?php echo $row_catalogo['IDtipo_file']; ?>" />
										</div>
										
									</form>

							</div>
						</div>
					</div>
					<!-- /danger modal -->






                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_catalogo['IDtipo_file']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="capa_becarios_catalogos_e.php?IDtipo_file=<?php echo $row_catalogo['IDtipo_file']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->



						
                        <?php } while ($row_catalogo = mysql_fetch_assoc($catalogo)); ?>
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

								<div class="modal-body">

									<form action="#" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
									<fieldset>
														 
										 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Tipo de Archivo:</label>
											  <div class="col-lg-8">
												<input type="text" name="tipo_file" id="tipo_file" class="form-control" value="" placeholder="Nombre del Programa" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Archivo:</label>
											  <div class="col-lg-8">
												<input type="text" name="file" id="file" class="form-control">
											 </div>
										  </div>
										  <!-- /basic text input -->



									</fieldset>
														
																			
												</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<input type="hidden" name="MM_insert" value="form1" />
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