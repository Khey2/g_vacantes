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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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
$fecha = date("dmY"); // la fecha actual

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_becarios  = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_evaluacion.IDevaluacion, capa_becarios_evaluacion.fecha_evaluacion, capa_becarios_evaluacion.IDmes, capa_becarios_evaluacion.anio, capa_becarios_evaluacion.IDcalificacion, capa_becarios_tipo.tipo, vac_meses.mes  FROM capa_becarios_evaluacion INNER JOIN capa_becarios ON capa_becarios_evaluacion.IDempleado = capa_becarios.IDempleado LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo LEFT JOIN vac_meses ON capa_becarios_evaluacion.IDmes = vac_meses.IDmes";
mysql_query("SET NAMES 'utf8'");
$becarios = mysql_query($query_becarios , $vacantes) or die(mysql_error());
$row_becarios = mysql_fetch_assoc($becarios);
$totalRows_becarios  = mysql_num_rows($becarios );

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDevaluacion'];
  $deleteSQL = "DELETE FROM capa_becarios_evaluacion WHERE IDevaluacion = $borrado";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_becarios_evaluaciones.php?info=3");
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<!-- /Theme JS files -->
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
			  
  						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

	<div class="panel panel-flat">

	<div class="media panel-body no-margin">
		<div class="media-body">
		
		<p>Selecciona la evaluación para Consultar.</p>

					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Becario</th>
							    <th>Programa</th>
							    <th>Modalidad</th>
							    <th>Año</th>
							    <th>Mes</th>
							    <th>Estatus</th>
							    <th>Resultado</th>
							    <th>Fecha Evaluacion</th>
							    <th>Documentos</th>
							    <th class="text-center">Acciones</th>
						    </tr>
					    </thead>
						<tbody>							  
						<?php if ($totalRows_becarios > 0){ ?>
						<?php do { 
						$mes_file = $row_becarios['IDmes'];
						$el_becario = $row_becarios['IDempleado'];
						$query_doctos  = "SELECT *, capa_becarios_files.*, capa_becarios_tipo_file.tipo_file FROM capa_becarios_files LEFT JOIN capa_becarios_tipo_file ON capa_becarios_files.IDtipo_file = capa_becarios_tipo_file.IDtipo_file WHERE IDempleado = '$el_becario' AND IDmes = $mes_file AND anio = $anio";
						mysql_query("SET NAMES 'utf8'");
						$doctos = mysql_query($query_doctos , $vacantes) or die(mysql_error());
						$row_doctos = mysql_fetch_assoc($doctos);
						$totalRows_doctos  = mysql_num_rows($doctos );
						?>
							<tr>
							<td><?php echo $row_becarios['emp_paterno']." ".$row_becarios['emp_materno']." ".$row_becarios['emp_nombre']; ?></td>
							<td><?php echo $row_becarios['tipo']; ?></td>
							<td><?php 
										 if ($row_becarios['IDmodalidad'] == 1) {echo "Presencial";}
									else if ($row_becarios['IDmodalidad'] == 2) {echo "Distancia";} 
									else if ($row_becarios['IDmodalidad'] == 3) {echo "Mixto";}
									else {echo "NA";} ?></td>
							<td><?php echo $row_becarios['anio']; ?></td>
							<td><?php echo $row_becarios['mes']; ?></td>
							<td><?php if ($row_becarios['IDcalificacion'] != '') { echo 'Evaluado'; } else { echo 'Pendiete'; }?></td>
							<td><?php if ($row_becarios['IDcalificacion'] > 1) { for ($x = 0; $x < $row_becarios['IDcalificacion']; $x++) { echo "<i class='icon-star-full2 text-success'></i>"; }} else { echo "<i class='icon-star-full2 text-success'></i>"; }?></td>
							<td><?php echo date('d/m/Y', strtotime($row_becarios['fecha_evaluacion']));  ?></td>
							<td><?php if ($totalRows_doctos > 0) { do { ?><a href="becariosfiles/<?php echo $row_doctos['IDempleado']; ?>/<?php echo $row_doctos['file']; ?>"><i class="icon-file-download2"></i></a><?php } while ($row_doctos = mysql_fetch_assoc($doctos)); } ?></td>
							<td>
								<button type="button" class="btn btn-success btn-icon" onClick="window.location.href='capa_becarios_evaluar.php?IDempleado=<?php echo $row_becarios['IDempleado']; ?>&IDmes=<?php echo $row_becarios['IDmes']; ?>&anio=<?php echo $row_becarios['anio']; ?>'">Ver evaluación</button>
								<a class="btn btn-warning btn-icon" target="_blank" href='capa_becarios_print.php?IDempleado=<?php echo $row_becarios['IDempleado']; ?>&IDmes=<?php echo $row_becarios['IDmes']; ?>&anio=<?php echo $row_becarios['anio']; ?>' >Imprimir</a>
							<button type="button" data-target="#modal_theme_danger<?php echo $row_becarios['IDevaluacion']; ?>"  data-toggle="modal" class="btn bg-danger">Borrar</button></td>
                            </td>
						    </tr>
							
							
							
                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_becarios['IDevaluacion']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="capa_becarios_evaluaciones.php?IDevaluacion=<?php echo $row_becarios['IDevaluacion']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


							
					    <?php } while ($row_becarios = mysql_fetch_assoc($becarios)); ?>
						<?php } else { ?>
                        
							<tr>
							<td colspan="5">No se tienen evaluaciones registradas en el Sistema.</td>
						    </tr>     
                                               
						<?php }  ?>
					    </tbody>
				    </table>
				</div>
									
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
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>