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
$IDmatrizes = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];
$IDusuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_catalogo = "SELECT casos_areas.IDarea, casos_areas.IDmatriz, casos_areas.area, vac_matriz.matriz FROM casos_areas INNER JOIN vac_matriz ON casos_areas.IDmatriz = vac_matriz.IDmatriz";
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);
$totalRows_catalogo = mysql_num_rows($catalogo);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$IDareaR = $_POST['IDarea'];
$IDusuarie = $_POST['IDusuarie'];
$insertSQL = "INSERT INTO casos_areas_responsables (IDarea, IDusuario) VALUES ($IDareaR, $IDusuarie)";

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
 header("Location: casos_sind_catc.php?info=1"); 	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDresponsablearea'];
  $deleteSQL = "DELETE FROM casos_areas_responsables WHERE IDresponsablearea = $borrado";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: casos_sind_catc.php?info=3");
}

mysql_select_db($database_vacantes, $vacantes);
$query_matrizl = "SELECT * FROM vac_matriz";
$matrizl = mysql_query($query_matrizl, $vacantes) or die(mysql_error());
$row_matrizl = mysql_fetch_assoc($matrizl);
$totalRows_matrizl = mysql_num_rows($matrizl);

mysql_select_db($database_vacantes, $vacantes);
$query_responsabls = "SELECT vac_puestos.denominacion, vac_matriz.matriz, vac_usuarios.usuario_correo, vac_usuarios.IDusuario, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno FROM vac_usuarios INNER JOIN vac_puestos ON vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto INNER JOIN vac_matriz ON vac_usuarios.IDmatriz = vac_matriz.IDmatriz WHERE vac_matriz.IDmatriz IN ($IDmatrizes) OR (vac_matriz.IDmatriz = 7 AND vac_usuarios.IDarea = 10) ORDER BY vac_matriz.matriz, vac_usuarios.usuario_parterno ASC";
$responsabls = mysql_query($query_responsabls, $vacantes) or die(mysql_error());
$row_responsabls = mysql_fetch_assoc($responsabls);
$totalRows_responsabls = mysql_num_rows($responsabls);



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>

	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
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
							
							
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>ID</th>
                          <th>Matriz</th>
                          <th>Area</th>
                          <th>Responsables</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do {  ?>
                        <tr>
                          <td><?php echo $row_catalogo['IDarea']; ?></td>
                          <td><?php echo $row_catalogo['matriz']; ?></td>
                          <td><?php echo $row_catalogo['area']; ?></td>
                          <td><?php
						  
							$IDarea = $row_catalogo['IDarea']; 
							$query_casos_responsable = "SELECT casos_areas_responsables.IDarea, casos_areas_responsables.IDresponsablearea, vac_usuarios.usuario_correo, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, vac_puestos.denominacion, casos_areas_responsables.IDusuario FROM casos_areas_responsables INNER JOIN vac_usuarios ON casos_areas_responsables.IDusuario = vac_usuarios.IDusuario INNER JOIN vac_puestos ON vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE casos_areas_responsables.IDarea = $IDarea"; 
							$casos_responsable = mysql_query($query_casos_responsable, $vacantes) or die(mysql_error());
							$row_casos_responsable = mysql_fetch_assoc($casos_responsable);
							$totalRows_casos_responsable = mysql_num_rows($casos_responsable);


							if ($totalRows_casos_responsable > 0) {

							do { ?><button type="button" data-target="#modal_theme_danger<?php echo $row_casos_responsable['IDresponsablearea']; ?>"  data-toggle="modal" class="btn btn-xs bg-danger"><i class="icon icon-trash"></i></button> &nbsp;&nbsp;
							
							<?php echo $row_casos_responsable['usuario_nombre']." ".$row_casos_responsable['usuario_parterno']." ".$row_casos_responsable['usuario_materno']." (".$row_casos_responsable['denominacion'].") <br/><br/>"; ?>
							
							
                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_casos_responsable['IDresponsablearea']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="casos_sind_catc.php?IDresponsablearea=<?php echo $row_casos_responsable['IDresponsablearea']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
							
							
							
							<?php }  while ($row_casos_responsable = mysql_fetch_assoc($casos_responsable)); } ?>
							</td>
                         <td>
						 <button type="button" data-target="#modal_theme_agregar<?php echo $row_catalogo['IDarea']; ?>"  data-toggle="modal" class="btn bg-primary">Agregar</button>
						 </td>
                        </tr> 

                     <!-- danger modal -->
					<div id="modal_theme_agregar<?php echo $row_catalogo['IDarea']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Agregar responsable</h6>
								</div>

								<div class="modal-body">

									<form action="casos_sind_catc.php" method="post" name="importar" id="importar" class="form-horizontal">
									<fieldset>
														 

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-3">Matriz</label>
											  <div class="col-lg-9">
												<?php echo $row_catalogo['matriz']; ?>
											 </div>
										  </div>
										  <!-- /basic text input -->

									<p>&nbsp;</p>

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-3">Área</label>
											  <div class="col-lg-9">
												<?php echo $row_catalogo['area']; ?>
											 </div>
										  </div>
										  <!-- /basic text input -->

									<p>&nbsp;</p>

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Responsable:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDusuarie" id="IDusuarie" class="form-control" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_responsabls['IDusuario']?>"><?php echo $row_responsabls['matriz']." - ".$row_responsabls['usuario_parterno']." ". $row_responsabls['usuario_materno']." ".$row_responsabls['usuario_nombre']." (".$row_responsabls['denominacion']." - ".$row_responsabls['matriz'].") "?></option>
												  <?php
												 } while ($row_responsabls = mysql_fetch_assoc($responsabls));
												   $rows = mysql_num_rows($responsabls);
												   if($rows > 0) {
												   mysql_data_seek($responsabls, 0);
												   $row_responsabls = mysql_fetch_assoc($responsabls);
												 } ?>
											</select>
										</div>
									</div>

									</fieldset>
														
																			
												</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<button type="submit" id="submit" name="import" class="btn btn-primary">Agregar</button> 
											<input type="hidden" name="MM_insert" value="form1" />
											<input type="hidden" name="IDarea" id="IDarea" value="<?php echo $row_catalogo['IDarea']; ?>">
										</div>
										
									</form>

							</div>
						</div>
					</div>
					<!-- /danger modal -->



						
                        <?php } while ($row_catalogo = mysql_fetch_assoc($catalogo)); ?>
                   	</tbody>							  
                 </table>

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