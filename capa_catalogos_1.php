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
$query_catalogo = "SELECT capa_tipo_modalidad.modalidad, capa_tipos_facilitadores.tipo_facilitador, capa_cursos.IDC_capa_cursos, capa_cursos.nombre_curso, capa_cursos.IDC_tipo_facilitador, capa_cursos.duracion, capa_cursos.IDC_tipo_curso, capa_cursos.IDC_modalidad, capa_cursos.IDtematicastps, capa_cursos.IDC_estatus, capa_tipos_cursos.tipo_evento FROM capa_cursos LEFT JOIN capa_tipos_facilitadores ON capa_cursos.IDC_tipo_facilitador = capa_tipos_facilitadores.IDC_tipo_facilitador LEFT JOIN capa_tipos_cursos ON capa_cursos.IDC_tipo_curso = capa_tipos_cursos.ID_tipo_evento LEFT JOIN capa_tipo_modalidad ON capa_cursos.IDC_modalidad = capa_tipo_modalidad.IDC_modalidad";
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);
$totalRows_catalogo = mysql_num_rows($catalogo);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE capa_cursos SET nombre_curso=%s, IDC_tipo_facilitador=%s, duracion=%s, IDC_tipo_curso=%s, IDC_modalidad=%s, IDC_estatus=%s, IDusuario=%s, IDtematicastps=%s WHERE IDC_capa_cursos=%s",
                       GetSQLValueString($_POST['nombre_curso'], "text"),
                       GetSQLValueString($_POST['IDC_tipo_facilitador'], "int"),
                       GetSQLValueString($_POST['duracion'], "int"),
                       GetSQLValueString($_POST['IDC_tipo_curso'], "int"),
                       GetSQLValueString($_POST['IDC_modalidad'], "int"),
                       GetSQLValueString($_POST['IDC_estatus'], "int"),
                       GetSQLValueString($_POST['IDusuario'], "int"),
                       GetSQLValueString($_POST['IDtematicastps'], "int"),
                       GetSQLValueString($_POST['IDC_capa_cursos'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: capa_catalogos_1.php?info=2"); 	
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO capa_cursos (nombre_curso, IDC_tipo_facilitador, duracion, IDC_tipo_curso, IDC_modalidad, IDC_estatus, IDtematicastps, IDusuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['nombre_curso'], "text"),
                       GetSQLValueString($_POST['IDC_tipo_facilitador'], "int"),
                       GetSQLValueString($_POST['duracion'], "int"),
                       GetSQLValueString($_POST['IDC_tipo_curso'], "int"),
                       GetSQLValueString($_POST['IDC_modalidad'], "int"),
                       GetSQLValueString($_POST['IDC_estatus'], "int"),
                       GetSQLValueString($_POST['IDtematicastps'], "int"),
                       GetSQLValueString($_POST['IDusuario'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  $captura = mysql_insert_id();
  header("Location: capa_catalogos_1.php?info=1"); 	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDC_capa_cursos'];
  $deleteSQL = "DELETE FROM capa_cursos WHERE IDC_capa_cursos = $borrado";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_catalogos_1.php?info=3");
}

$query_facilitadores = "SELECT * FROM capa_tipos_facilitadores";
$facilitadores = mysql_query($query_facilitadores, $vacantes) or die(mysql_error());
$row_facilitadores = mysql_fetch_assoc($facilitadores);
$totalRows_facilitadores = mysql_num_rows($facilitadores);

$query_modalidades = "SELECT * FROM capa_tipo_modalidad";
$modalidades = mysql_query($query_modalidades, $vacantes) or die(mysql_error());
$row_modalidades = mysql_fetch_assoc($modalidades);
$totalRows_modalidades = mysql_num_rows($modalidades);

$query_tiposeventos = "SELECT * FROM capa_tipos_cursos";
$tiposeventos = mysql_query($query_tiposeventos, $vacantes) or die(mysql_error());
$row_tiposeventos = mysql_fetch_assoc($tiposeventos);
$totalRows_tiposeventos = mysql_num_rows($tiposeventos);

$query_tematicas = "SELECT * FROM capa_tipos_tematica_stps ORDER BY IDno ASC";
$tematicas = mysql_query($query_tematicas, $vacantes) or die(mysql_error());
$row_tematicas = mysql_fetch_assoc($tematicas);
$totalRows_tematicas = mysql_num_rows($tematicas);


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
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_inputs.js"></script>
	<!-- /theme JS files -->

<?php if (isset($_GET['IDC_capa'])) { ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#modal_theme_agregar').modal('show');
});
</script>
<?php } ?>

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
							<p><b>Eventos: </b>Selecciona el registro para editarlo.</p>
							
							
						<button type="button" data-target="#modal_theme_agregar"  data-toggle="modal" class="btn bg-success">Agregar registro</button>
						<p>&nbsp;</p>

						<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>ID</th>
                          <th>Curso</th>
                          <th>Tipo Facilitador</th>
                          <th>Duración</th>
                          <th>Tipo de Evento</th>
                          <th>Modalidad</th>
                          <th>Estatus</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do {  ?>
                        <tr>
                          <td><?php echo $row_catalogo['IDC_capa_cursos']; ?></td>
                          <td><?php echo $row_catalogo['nombre_curso']; ?></td>
                          <td><?php if ($row_catalogo['IDC_tipo_facilitador'] == 4) {echo "Interno";} else {echo "Externo";} ?></td>
                          <td><?php echo $row_catalogo['duracion']; ?> horas</td>
                          <td><?php echo $row_catalogo['tipo_evento']; ?></td>
                          <td><?php echo $row_catalogo['modalidad']; ?></td>
                          <td><?php if ($row_catalogo['IDC_estatus'] == 1) {echo "Activo";} else {echo "Cerrado";} ?></td>
                         <td>
						 <button type="button" data-target="#modal_theme_actualizar<?php echo $row_catalogo['IDC_capa_cursos']; ?>"  data-toggle="modal" class="btn bg-primary">Actualizar</button>
						 <button type="button" data-target="#modal_theme_danger<?php echo $row_catalogo['IDC_capa_cursos']; ?>"  data-toggle="modal" class="btn bg-danger">Borrar</button></td>
                        </tr> 




                     <!-- danger modal -->
					<div id="modal_theme_actualizar<?php echo $row_catalogo['IDC_capa_cursos']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar registro</h6>
								</div>

								<div class="modal-body">

									<form action="capa_catalogos_1.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 
									<fieldset class="content-group">	 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-3">Nombre del Curso:<span class="text-danger">*</span></label>
											  <div class="col-lg-9">
												<input type="text" name="nombre_curso" id="nombre_curso" class="form-control" value="<?php echo $row_catalogo['nombre_curso']; ?>" placeholder="Nombre del Curso" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Tipo de Facilitador:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_tipo_facilitador" id="IDC_tipo_facilitador" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_facilitadores['IDC_tipo_facilitador']?>"<?php if (!(strcmp($row_facilitadores['IDC_tipo_facilitador'], $row_catalogo['IDC_tipo_facilitador']))) {echo "SELECTED";} ?>><?php echo $row_facilitadores['tipo_facilitador']?></option>
														  <?php
														 } while ($row_facilitadores = mysql_fetch_assoc($facilitadores));
														 $rows = mysql_num_rows($facilitadores);
														 if($rows > 0) {
														 mysql_data_seek($facilitadores, 0);
														 $row_facilitadores = mysql_fetch_assoc($facilitadores);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->

										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Tipo de Evento:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_tipo_curso" id="IDC_tipo_curso" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_tiposeventos['ID_tipo_evento']?>"<?php if (!(strcmp($row_tiposeventos['ID_tipo_evento'], $row_catalogo['IDC_tipo_curso']))) {echo "SELECTED";} ?>><?php echo $row_tiposeventos['tipo_evento']?></option>
														  <?php
														 } while ($row_tiposeventos = mysql_fetch_assoc($tiposeventos));
														 $rows = mysql_num_rows($tiposeventos);
														 if($rows > 0) {
														 mysql_data_seek($tiposeventos, 0);
														 $row_tiposeventos = mysql_fetch_assoc($tiposeventos);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->


										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Modalidad:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_modalidad" id="IDC_modalidad" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_modalidades['IDC_modalidad']?>"<?php if (!(strcmp($row_modalidades['IDC_modalidad'], $row_catalogo['IDC_modalidad']))) {echo "SELECTED";} ?>><?php echo $row_modalidades['modalidad']?></option>
														  <?php
														 } while ($row_modalidades = mysql_fetch_assoc($modalidades));
														 $rows = mysql_num_rows($modalidades);
														 if($rows > 0) {
														 mysql_data_seek($modalidades, 0);
														 $row_modalidades = mysql_fetch_assoc($modalidades);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->

										<p>&nbsp;</p>
										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Temática STPS:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDtematicastps" id="IDtematicastps" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_tematicas['IDtematicastps']?>"<?php if (!(strcmp($row_tematicas['IDtematicastps'], $row_catalogo['IDtematicastps']))) {echo "SELECTED";} ?>><?php echo $row_tematicas['tipo_evento']?> (<?php echo $row_tematicas['IDno']?>)</option>
														  <?php
														 } while ($row_tematicas = mysql_fetch_assoc($tematicas));
														 $rows = mysql_num_rows($tematicas);
														 if($rows > 0) {
														 mysql_data_seek($tematicas, 0);
														 $row_tematicas = mysql_fetch_assoc($tematicas);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->

										<p>&nbsp;</p>

										<div class="form-group">
											  <label class="control-label col-lg-3">Duración (horas):<span class="text-danger">*</span></label>
											  <div class="col-lg-9">
												<input type="number" name="duracion" id="duracion" class="form-control" value="<?php echo $row_catalogo['duracion']; ?>" placeholder="Duración en horas" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_estatus" id="IDC_estatus" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <option value="1"<?php if ($row_catalogo['IDC_modalidad'] == 1) {echo "SELECTED";} ?>>Activo</option>
														  <option value="2"<?php if ($row_catalogo['IDC_modalidad'] == 2) {echo "SELECTED";} ?>>Cerrado</option>
											  </select>
											</div>
										</div>
										<!-- /basic select -->
									</fieldset>
														
																			
												</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<button type="submit" id="submit" name="import" class="btn btn-primary">Actualizar</button> 
											<input type="hidden" name="MM_update" value="form1" />
											<input type="hidden" name="IDusuario" value="<?php echo $row_usuario['IDusuario']; ?>" />
											<input type="hidden" name="IDC_capa_cursos" value="<?php echo $row_catalogo['IDC_capa_cursos']; ?>" />
										</div>
										
									</form>

							</div>
						</div>
					</div>
					<!-- /danger modal -->






                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_catalogo['IDC_capa_cursos']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="capa_catalogos_1.php?IDC_capa_cursos=<?php echo $row_catalogo['IDC_capa_cursos']; ?>&borrar=1">Si borrar</a>
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

									<form action="capa_catalogos_1.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
									<fieldset>
														 
										 
									<fieldset class="content-group">	 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-3">Nombre del Curso:<span class="text-danger">*</span></label>
											  <div class="col-lg-9">
												<input type="text" name="nombre_curso" id="nombre_curso" class="form-control" placeholder="Nombre del Curso" 
												value="<?php if(isset($_GET['IDC_capa'])) {
												$IDavance = $_GET['IDC_capa'];
												$query_tiposeventos_pre = "SELECT * FROM capa_avance_temp WHERE IDC_capa = '$IDavance'";
												$tiposeventos_pre = mysql_query($query_tiposeventos_pre, $vacantes) or die(mysql_error());
												$row_tiposeventos_pre = mysql_fetch_assoc($tiposeventos_pre);
												echo $row_tiposeventos_pre['nombre_cargado'];} ?>" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Tipo de Facilitador:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_tipo_facilitador" id="IDC_tipo_facilitador" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_facilitadores['IDC_tipo_facilitador']?>"><?php echo $row_facilitadores['tipo_facilitador']?></option>
														  <?php
														 } while ($row_facilitadores = mysql_fetch_assoc($facilitadores));
														 $rows = mysql_num_rows($facilitadores);
														 if($rows > 0) {
														 mysql_data_seek($facilitadores, 0);
														 $row_facilitadores = mysql_fetch_assoc($facilitadores);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->

										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Tipo de Evento:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_tipo_curso" id="IDC_tipo_curso" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_tiposeventos['ID_tipo_evento']?>"><?php echo $row_tiposeventos['tipo_evento']?></option>
														  <?php
														 } while ($row_tiposeventos = mysql_fetch_assoc($tiposeventos));
														 $rows = mysql_num_rows($tiposeventos);
														 if($rows > 0) {
														 mysql_data_seek($tiposeventos, 0);
														 $row_tiposeventos = mysql_fetch_assoc($tiposeventos);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->


										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Modalidad:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_modalidad" id="IDC_modalidad" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_modalidades['IDC_modalidad']?>"><?php echo $row_modalidades['modalidad']?></option>
														  <?php
														 } while ($row_modalidades = mysql_fetch_assoc($modalidades));
														 $rows = mysql_num_rows($modalidades);
														 if($rows > 0) {
														 mysql_data_seek($modalidades, 0);
														 $row_modalidades = mysql_fetch_assoc($modalidades);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->

										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Temática STPS:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDtematicastps" id="IDtematicastps" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <?php do {  ?>
														  <option value="<?php echo $row_tematicas['IDtematicastps']?>"><?php echo $row_tematicas['tipo_evento']?> (<?php echo $row_tematicas['IDno']?>)</option>
														  <?php
														 } while ($row_tematicas = mysql_fetch_assoc($tematicas));
														 $rows = mysql_num_rows($tematicas);
														 if($rows > 0) {
														 mysql_data_seek($tematicas, 0);
														 $row_tematicas = mysql_fetch_assoc($tematicas);
														 } ?>
											  </select>
											</div>
										</div>
										<!-- /basic select -->


										<p>&nbsp;</p>

										
										<!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-3">Duración (horas):<span class="text-danger">*</span></label>
											  <div class="col-lg-9">
												<input type="number" name="duracion" id="duracion" class="form-control" placeholder="Duración en horas" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										<p>&nbsp;</p>

										<!-- Basic select -->
										<div class="form-group">
											<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
											<div class="col-lg-9">
												<select name="IDC_estatus" id="IDC_estatus" class="form-control" required="required">
													<option value="">Seleccione una opción</option> 
														  <option value="1">Activo</option>
														  <option value="2">Cerrado</option>
											  </select>
											</div>
										</div>
										<!-- /basic select -->



									</fieldset>


									</fieldset>
														
																			
												</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<button type="submit" id="submit" name="import" class="btn btn-success">Agregar</button> 
											<input type="hidden" name="IDusuario" value="<?php echo $row_usuario['IDusuario']; ?>" />
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