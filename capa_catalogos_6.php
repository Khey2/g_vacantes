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
$query_matrize = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (27,31)";
$matrize = mysql_query($query_matrize, $vacantes) or die(mysql_error());
$row_matrize = mysql_fetch_assoc($matrize);
$totalRows_matrize = mysql_num_rows($matrize);

if (isset($_GET['IDCcurso'])) {
$DCcurso_filtro = $_GET['IDCcurso'];
mysql_select_db($database_vacantes, $vacantes);
$query_catalogo = "SELECT DISTINCT capa_eventos_grupo.IDCcurso, capa_eventos_grupo.evento, capa_eventos_grupo.fecha_inicio, capa_eventos_grupo.fecha_fin, capa_eventos_grupo.IDtipo, capa_eventos_grupo.IDusuario FROM capa_eventos_grupo WHERE capa_eventos_grupo.IDCcurso = '$DCcurso_filtro'"; 
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);
$totalRows_catalogo = mysql_num_rows($catalogo);

} else {
	
mysql_select_db($database_vacantes, $vacantes);
$query_catalogo = "SELECT DISTINCT capa_eventos_grupo.IDCcurso, capa_eventos_grupo.evento, capa_eventos_grupo.fecha_inicio, capa_eventos_grupo.IDtipo, capa_eventos_grupo.fecha_fin, capa_eventos_grupo.IDusuario FROM capa_eventos_grupo";  
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);
$totalRows_catalogo = mysql_num_rows($catalogo);
}

//las variables de sesion para el filtrado
if (isset($_POST['la_matriz'])) { foreach ($_POST['la_matriz'] as $matrizes)
	{	$la_matriz = implode(",", $_POST['la_matriz']);} }  else { $la_matriz ='';}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$y1 = substr( $_POST['fecha_inicio'], 8, 2 );
$m1 = substr( $_POST['fecha_inicio'], 3, 2 );
$d1 = substr( $_POST['fecha_inicio'], 0, 2 );
$fecha_inicio = "20".$y1."-".$m1."-".$d1;

$y2 = substr( $_POST['fecha_fin'], 8, 2 );
$m2 = substr( $_POST['fecha_fin'], 3, 2 );
$d2 = substr( $_POST['fecha_fin'], 0, 2 );
$fecha_fin = "20".$y2."-".$m2."-".$d2;

$updateSQL = sprintf("UPDATE capa_eventos_grupo SET evento=%s, fecha_inicio=%s, fecha_fin=%s, IDusuario=%s, IDtipo=%s WHERE IDCcurso=%s",
                       GetSQLValueString($_POST['evento'], "text"),
                       GetSQLValueString($fecha_inicio, "text"),
                       GetSQLValueString($fecha_fin, "text"),
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['IDCcurso'], "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location: capa_catalogos_6.php?info=2"); 	
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$y1 = substr( $_POST['fecha_inicio'], 8, 2 );
$m1 = substr( $_POST['fecha_inicio'], 3, 2 );
$d1 = substr( $_POST['fecha_inicio'], 0, 2 );
$fecha_inicio = "20".$y1."-".$m1."-".$d1;

$y2 = substr( $_POST['fecha_fin'], 8, 2 );
$m2 = substr( $_POST['fecha_fin'], 3, 2 );
$d2 = substr( $_POST['fecha_fin'], 0, 2 );
$fecha_fin = "20".$y2."-".$m2."-".$d2;

	
$insertSQL = sprintf("INSERT INTO capa_eventos_grupo (IDusuario, evento, fecha_inicio, fecha_fin, IDtipo) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($_POST['evento'], "text"),
                       GetSQLValueString($fecha_inicio, "text"),
                       GetSQLValueString($fecha_fin, "text"),
					   GetSQLValueString($_POST['IDtipo'], "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
$captura = mysql_insert_id();
header("Location: capa_catalogos_6.php?info=1"); 	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDCcurso'];
  $deleteSQL = "DELETE FROM capa_eventos_grupo WHERE IDCcurso = $borrado";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
header("Location: capa_catalogos_6.php?info=3");
}

mysql_select_db($database_vacantes, $vacantes);
$query_cursos = "SELECT * FROM capa_cursos ORDER BY nombre_curso ASC";
$cursos = mysql_query($query_cursos, $vacantes) or die(mysql_error());
$row_cursos = mysql_fetch_assoc($cursos);
$totalRows_cursos = mysql_num_rows($cursos);

mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (27,31)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);



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
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>

	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>

	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
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
							<p><b>Eventos: </b>Selecciona el registro para editarlo.</p>
							
							
						<button type="button" data-target="#modal_theme_agregar"  data-toggle="modal" class="btn bg-success">Agregar registro</button>
							<?php if (isset($_GET['IDCcurso'])) { ?>
                            <a href="capa_catalogos_6.php" class="btn btn-info">Ver todos</a>
							<?php } ?>
						<p>&nbsp;</p>

			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>ID</th>
                          <th>Evento</th>
                          <th>Fecha Inicio</th>
                          <th>Fecha Fin</th>
                          <th>Tipo Filtrado</th>
                          <th>Cursos Asignados</th>
                          <th>Puestos Asignados</th>
                          <th>Sucursales Asignadas</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
						<?php do { 

							$IDCcurso = $row_catalogo['IDCcurso'];
							mysql_select_db($database_vacantes, $vacantes);
							$query_puestos_asignados = "SELECT * FROM capa_eventos_grupo_puestos WHERE IDCcurso = $IDCcurso"; 
							$puestos_asignados = mysql_query($query_puestos_asignados, $vacantes) or die(mysql_error());
							$row_puestos_asignados = mysql_fetch_assoc($puestos_asignados);
							$totalRows_puestos_asignados = mysql_num_rows($puestos_asignados);

							mysql_select_db($database_vacantes, $vacantes);
							$query_cursos_asignados = "SELECT * FROM capa_eventos_grupo_cursos WHERE IDCcurso = $IDCcurso"; 
							$cursos_asignados = mysql_query($query_cursos_asignados, $vacantes) or die(mysql_error());
							$row_cursos_asignados = mysql_fetch_assoc($cursos_asignados);
							$totalRows_cursos_asignados = mysql_num_rows($cursos_asignados);

							mysql_select_db($database_vacantes, $vacantes);
							$query_matriz_asignados = "SELECT * FROM capa_eventos_grupo_matriz WHERE IDCcurso = $IDCcurso"; 
							$matriz_asignados = mysql_query($query_matriz_asignados, $vacantes) or die(mysql_error());
							$row_matriz_asignados = mysql_fetch_assoc($matriz_asignados);
							$totalRows_matriz_asignados = mysql_num_rows($matriz_asignados);
						?>
                        <tr>
                          <td><?php echo $row_catalogo['IDCcurso']; ?></td>
                          <td><?php echo $row_catalogo['evento']; ?></td>
                          <td><?php echo date('d/m/Y', strtotime($row_catalogo['fecha_inicio'])); ?></td>
                          <td><?php echo date('d/m/Y', strtotime($row_catalogo['fecha_fin'])); ?></td>
                          <td><?php if ($row_catalogo['IDtipo'] == 1){ echo "Antiguedad";} else if ($row_catalogo['IDtipo'] == 2){ echo "Curso";} else { echo "Ambos";}?></td>
                          <td><?php echo $totalRows_cursos_asignados; ?></td>
                          <td><?php echo $totalRows_puestos_asignados; ?></td>
                          <td><?php echo $totalRows_matriz_asignados; ?></td>
                         <td>
						 <button type="button" data-target="#modal_theme_actualizar<?php echo $row_catalogo['IDCcurso']; ?>"  data-toggle="modal" class="btn bg-primary">Actualizar</button>
						 <button type="button" data-target="#modal_theme_danger<?php echo $row_catalogo['IDCcurso']; ?>"  data-toggle="modal" class="btn bg-danger">Borrar</button>
						 <a href="capa_catalogos_7.php?IDCcurso=<?php echo $row_catalogo['IDCcurso']; ?>" class="btn bg-info">Puestos</a>
						 <a href="capa_catalogos_8.php?IDCcurso=<?php echo $row_catalogo['IDCcurso']; ?>" class="btn bg-warning">Cursos</a>
						 <a href="capa_catalogos_9.php?IDCcurso=<?php echo $row_catalogo['IDCcurso']; ?>" class="btn bg-success">Sucursales</a>
						 </td>
                        </tr> 




                     <!-- danger modal -->
					<div id="modal_theme_actualizar<?php echo $row_catalogo['IDCcurso']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar registro</h6>
								</div>

								<div class="modal-body">

									<form action="capa_catalogos_6.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
									<fieldset>
														 
										 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Nombre del Evento:<span class="text-danger">*</span></label>
											  <div class="col-lg-8">
												<input type="text" name="evento" id="evento" class="form-control" value="<?php echo $row_catalogo['evento']; ?>" placeholder="Nombre del Evento" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->
<p>&nbsp;</p>

										 
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha Inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="<?php if ($row_catalogo['fecha_inicio'] == "") { echo "";} else  { echo KT_formatDate($row_catalogo['fecha_inicio']); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->
<p>&nbsp;</p>

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha Fin:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" value="<?php if ($row_catalogo['fecha_fin'] == "") { echo "";} else  { echo KT_formatDate($row_catalogo['fecha_fin']); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

<p>&nbsp;</p>

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Tipo de Filtrado:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
										<select name="IDtipo" class="form-control" required="required">
                                           <option value="1"<?php if ($row_catalogo['IDtipo'] == 1) {echo "selected=\"selected\"";} ?>>Por fecha de Antiguedad</option>
                                           <option value="2"<?php if ($row_catalogo['IDtipo'] == 2) {echo "selected=\"selected\"";} ?>>Por fecha en la que se toma el curso</option>
                                           <option value="3"<?php if ($row_catalogo['IDtipo'] == 3) {echo "selected=\"selected\"";} ?>>Por ambos criterios (antiguedad y curso)</option>
                                        </select>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									</fieldset>
														
																			
												</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<button type="submit" id="submit" name="import" class="btn btn-primary">Actualizar</button> 
											<input type="hidden" name="MM_update" value="form1" />
											<input type="hidden" name="IDCcurso" value="<?php echo $row_catalogo['IDCcurso']; ?>" />
										</div>
										
									</form>

							</div>
						</div>
					</div>
					<!-- /danger modal -->






                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_catalogo['IDCcurso']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="capa_catalogos_6.php?IDCcurso=<?php echo $row_catalogo['IDCcurso']; ?>&borrar=1">Si borrar</a>
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

									<form action="capa_catalogos_6.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
									<fieldset>
														 
										 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Nombre del Evento:<span class="text-danger">*</span></label>
											  <div class="col-lg-8">
												<input type="text" name="evento" id="evento" class="form-control" placeholder="Nombre del Evento" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->
<p>&nbsp;</p>

										 
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha Inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->
<p>&nbsp;</p>

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha Fin:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" value="" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->


<p>&nbsp;</p>

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Tipo de Filtrado:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
										<select name="IDtipo" class="form-control" required="required">
                                           <option value="1">Por fecha de Antiguedad</option>
                                           <option value="2">Por fecha en la que se toma el curso</option>
                                           <option value="3">Por ambos criterios (antiguedad y curso)</option>
                                        </select>
                                   </div>
                                  </div> 
									<!-- Fecha -->
									</fieldset>
														
																			
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