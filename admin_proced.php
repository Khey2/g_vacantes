<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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
$IDperiodovar = $row_variables['IDperiodo'];


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


//filtro
$IDdireccion = $_GET['IDdireccion'];
if(isset($_GET['IDDarea'])){
	$filtro1 = ' AND proced_documentos.IDDarea = '.$_GET['IDDarea'];
	$liga1 = '&IDDarea='.$_GET['IDDarea'];
	$liga1b = '/'.$_GET['IDDarea'];
	$IDDarea = $_GET['IDDarea'];
	} else {
	$filtro1 = ' AND proced_documentos.IDDarea = 0'; 
	$liga1 = '&IDDarea=0';
	$liga1b = '';
	$IDDarea = '0';
	}
if(isset($_GET['IDsubarea'])){
	$filtro2 = ' AND proced_documentos.IDsubarea = '.$_GET['IDsubarea'];
	$liga2 = '&IDsubarea='.$_GET['IDsubarea'];
	$liga2b = '/'.$_GET['IDsubarea'];
	$IDsubarea = $_GET['IDsubarea'];
	} else {
	$filtro2 = ' AND proced_documentos.IDsubarea = 0';
	$liga2 = '&IDsubarea=0';
	$liga2b = '/0';
	$IDsubarea = '0';
	}

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT proced_documentos.IDdocumento, proced_documentos.IDDarea, proced_documentos.vistas, proced_documentos.IDdireccion, proced_documentos.IDsubarea, proced_documentos.IDtipo, proced_documentos.IDvisible, proced_documentos.documento, proced_documentos.descripcion, proced_documentos.file, proced_documentos.version, proced_documentos.anio, proced_areas.area, proced_subareas.subarea, proced_direcciones.direccion  FROM proced_documentos LEFT JOIN proced_direcciones ON proced_documentos.IDdireccion = proced_direcciones.IDdireccion LEFT JOIN proced_areas ON proced_documentos.IDDarea = proced_areas.IDDarea LEFT JOIN proced_subareas ON proced_documentos.IDsubarea = proced_subareas.IDsubarea  WHERE proced_documentos.IDdireccion = $IDdireccion".$filtro1.$filtro2;
mysql_query("SET NAMES 'utf8'"); 
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

//echo $query_resultados;

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_direcciones = "SELECT * FROM proced_direcciones";
$direcciones = mysql_query($query_direcciones, $vacantes) or die(mysql_error());
$row_direcciones = mysql_fetch_assoc($direcciones);
$totalRows_direcciones = mysql_num_rows($direcciones);

		$query_direcciona = "SELECT * FROM proced_direcciones WHERE IDdireccion = $IDdireccion";
		$direcciona = mysql_query($query_direcciona, $vacantes) or die(mysql_error());
		$row_direcciona = mysql_fetch_assoc($direcciona);
		$totalRows_direcciona = mysql_num_rows($direcciona);

		$query_areaa = "SELECT * FROM proced_areas WHERE IDDarea = $IDDarea";
		$areaa = mysql_query($query_areaa, $vacantes) or die(mysql_error());
		$row_areaa = mysql_fetch_assoc($areaa);
		$totalRows_areaa = mysql_num_rows($areaa);

		$query_subareaa = "SELECT * FROM proced_subareas WHERE IDsubarea = $IDsubarea";
		$subareaa = mysql_query($query_subareaa, $vacantes) or die(mysql_error());
		$row_subareaa = mysql_fetch_assoc($subareaa);
		$totalRows_subareaa = mysql_num_rows($subareaa);
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

	<script src="assets/js/app.js"></script>
   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
        <!-- /theme JS files -->
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona el documento para editarlo.</p>
							<?php if ($totalRows_direcciona > 0) { ?>
								<li><a href="#" class="btn btn-default"><strong>Dirección:</strong> <?php echo $row_direcciona['direccion']; ?></a></li>
							<?php } ?>
							<?php if ($totalRows_areaa > 0) { ?>
								<li><a href="#" class="btn btn-default"><strong>Área:</strong> <?php echo $row_areaa['area']; ?></a></li>
							<?php } ?>
							<?php if ($totalRows_subareaa > 0) { ?>
								<li><a href="#" class="btn btn-default"><strong>Subárea:</strong> <?php echo $row_subareaa['subarea']; ?></a></li>
							<?php } ?>

					<p>&nbsp;</p>
                    <a class="btn btn-success" href="admin_proced_edit.php?IDdireccion=<?php echo $IDdireccion; ?>&IDDarea=<?php echo $IDDarea; ?>&IDsubarea=<?php echo $IDsubarea; ?>">Agregar Documento</a>
					<a class="btn btn-default" href="admin_proced_directorio.php">Regresar</a>
					</div>
                    
                   
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>ID</th>
                          <th>Nombre</th>
                          <th>Dirección</th>
                          <th>Area</th>
                          <th>Subarea</th>
                          <th>Versión</th>
                          <th>Año</th>
                          <th>Visible</th>
                          <th>Vistas</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php if ($totalRows_resultados > 0) { ?>
                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_resultados['IDdocumento']; ?></td>
                          <td><?php echo $row_resultados['documento']; ?></td>
                          <td><?php echo $row_resultados['direccion']; ?></td>
                          <td><?php echo $row_resultados['area']; ?></td>
                          <td><?php if ( $row_resultados['subarea'] != '') { echo $row_resultados['subarea']; } else { echo "-"; }?></td>
                          <td><?php echo $row_resultados['version']; ?></td>
                          <td><?php echo date('Y', strtotime($row_resultados['anio'])); ?></td>
                          <td><?php if ( $row_resultados['IDvisible'] == 1) {echo "Si"; } else{ echo "No"; } ?></td>
                          <td><?php if ( $row_resultados['vistas'] == 1) {echo "1 vista"; } 
									elseif ( $row_resultados['vistas'] > 1) {echo $row_resultados['vistas']. " vistas"; } 
									else{ echo "Sin vistas"; } ?></td>
                         <td><a class="btn btn-primary" href="admin_proced_edit.php?IDdocumento=<?php echo $row_resultados['IDdocumento']; ?>&IDdireccion=<?php echo $IDdireccion; ?><?php echo $liga1; ?><?php echo $liga2; ?>">Editar</a>
						 <a class="btn btn-success" href="proced/<?php echo $IDdireccion; ?><?php echo $liga1b; ?><?php echo $liga2b; ?>/<?php echo $row_resultados['file']; ?>">Descargar	</a>
						 <a class="btn btn-warning" href="admin_proced_puestos.php?IDdocumento=<?php echo $row_resultados['IDdocumento']; ?>&IDdireccion=<?php echo $IDdireccion; ?>">Puestos	</a>
						 <a class="btn btn-info" href="admin_proced_anexos.php?IDdocumento=<?php echo $row_resultados['IDdocumento']; ?>">Anexos</a>
                         <button type="button" data-target="#modal_theme_danger<?php echo $row_resultados['IDdocumento']; ?>" data-toggle="modal" class="btn btn-danger">Borrar</button>
                         <button type="button" data-target="#modal_liga<?php echo $row_resultados['IDdocumento']; ?>" data-toggle="modal" class="btn btn-info">Liga</button>
                        </td>
                        </tr>      
                        
                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_resultados['IDdocumento']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body text-justify">
								<p>¿Estás seguro que quieres borrar el Documento? </br></br> <strong><?php echo $row_resultados['documento']; ?></strong></p>

								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<a class="btn btn-danger" href="admin_proced_edit.php?IDdocumento=<?php echo $row_resultados['IDdocumento']; ?>&IDdireccion=<?php echo $IDdireccion; ?>&IDDarea=<?php echo $IDDarea; ?>&IDsubarea=<?php echo $IDsubarea; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
					
                     <!-- danger modal -->
					<div id="modal_liga<?php echo $row_resultados['IDdocumento']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-info">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body text-justify">
								<p>Clic en Copiar para obtener la liga y agregala al correo:
								<br/>
								<div class="col-lg-12">
								<input type="text" class="form-control" value="https://gestionvacantes.com/f_procedimientos.php?IDdocumento=<?php echo $row_resultados['IDdocumento']; ?>" id="myInput<?php echo $row_resultados['IDdocumento']; ?>">
								</div>
								<br/>
								<br/>
								<br/>
								<script>
								function myFunction<?php echo $row_resultados['IDdocumento']; ?>() {
								  // Get the text field
								  var copyText = document.getElementById("myInput<?php echo $row_resultados['IDdocumento']; ?>");

								  // Select the text field
								  copyText.select();
								  copyText.setSelectionRange(0, 99999); // For mobile devices

								  // Copy the text inside the text field
								  navigator.clipboard.writeText(copyText.value);
								  
								}
								</script>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button class="btn btn-info" onclick="myFunction<?php echo $row_resultados['IDdocumento']; ?>()">Copiar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
					
                        <?php } while ($row_resultados = mysql_fetch_assoc($resultados)); ?>
                      <?php } else { ?>
                        <tr>
                          <td>No se encontraron documentos.</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>      
                      <?php }  ?>
                   	</tbody>							  
                 </table>

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