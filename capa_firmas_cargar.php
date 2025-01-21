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

$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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
$IDmatriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if (isset($_GET['IDfirma'])) {
$IDfirma = $_GET['IDfirma'];
$query_autorizados = "SELECT * FROM capa_firmas WHERE IDfirma = $IDfirma";
mysql_query("SET NAMES 'utf8'");
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {

	$Elempleado = utf8_decode($_POST["firma"]); 
	$formatos_permitidos =  array('jpeg', 'png', 'jpg');
	$name=$_FILES['file']['name'];
	$size=$_FILES['file']['size'];
	$type=$_FILES['file']['type'];
	$temp=$_FILES['file']['tmp_name'];
	$extension = pathinfo($name, PATHINFO_EXTENSION);
	$targetPath = 'capa/'.$name;
	
	if(in_array($extension, $formatos_permitidos) ) { move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
	
	$updateSQL = "INSERT INTO capa_firmas (firma, file) VALUES ('$Elempleado', '$name')";
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	header("Location: capa_firmas.php?info=1");
	} else {
	header("Location: capa_firmas.php?info=9");
	}
}
	
// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $IDfirma = $_GET['IDfirma'];
  $deleteSQL = "DELETE FROM capa_firmas WHERE IDfirma ='$IDfirma'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
 header("Location: capa_firmas.php?info=3");
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/col_reorder.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_reorder.js"></script>	
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
							Se guardó correctamente la Foto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Archivo borrado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo es incorrecto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				
					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Carga de Fotografía para Frima en DC3</h6>
								</div>

								<div class="panel-body">
								<p>Selecciona el archivo para cargar.</p>
								
								
								
								<?php if (isset($_GET['IDfirma'])) { ?>
								<form method="post"  enctype="multipart/form-data" class="form-horizontal form-validate-jquery" name="form2" action="capa_firmas_cargar.php?IDfirma=<?php echo $row_autorizados['IDfirma'];?>" >
								<?php } else { ?>
								<form method="post"  enctype="multipart/form-data" class="form-horizontal form-validate-jquery" name="form2" action="capa_firmas_cargar.php" >
								<?php }  ?>
									<div class="modal-body">
                                                                       

									<?php if (isset($_GET['IDfirma'])) { ?>
										<div class="form-group">
											 <div class="row">
												<div class="col-sm-3">Nombre del Empleado:</div>
												<div class="col-sm-9"><?php echo $row_autorizados['firma']; ?>
												</div>
											</div>
										</div>
										<?php } else { ?>
										<div class="form-group">
											 <div class="row">
												<div class="col-sm-3">Nombre del Empleado:</div>
												<div class="col-sm-9"><input type="text" name="firma" id="firma" class="form-control" required="required">
												</div>
											</div>
										</div>
										<?php } ?>

										<?php if (isset($_GET['IDfirma'])) { ?>
										<div class="form-group">
											 <div class="row">
												<div class="col-sm-3">Firma:</div>
												<div class="col-sm-9">
												<img src="<?php echo 'capa/'.$row_autorizados['file']; ?>" alt="Firma" width="100" height="120"><br/>
												</div>
											</div>
										</div>
										<?php } else { ?>
											<div class="form-group">
											 <div class="row">
												<div class="col-sm-3">Firma:</div>
												<div class="col-sm-9">
												<img src="files/foto.jpg" alt="Firma" width="100" height="120"><br/>
												</div>
											</div>
										</div>
										<?php }  ?>



										<div class="form-group">
											 <div class="row">
												<div class="col-sm-3">Cargar:	</div>
												<div class="col-sm-9">
												<p>&nbsp;</p>
												<input type="file" name="file" id="file" class="form-control" value="" required="required">
												<span class="help-block">Solo se permiten fotos en formato <code>.jpeg</code>, <code>.png</code> y <code>.jpg</code>.
												</div>
											</div>
										</div>
                                           
                                                
										<?php if (isset($_GET['IDfirma'])) { ?>
                                            <div class="modal-footer">
                                                <a href="capa_firmas.php" class="btn btn-default" role="button">Regresar</a>
                                                <input type="submit" class="btn btn-primary" value="Guardar">
                                                <?php if ($row_autorizados['file'] != '') { ?>
												<button type="button" data-target="#modal_theme_danger" data-toggle="modal" class="btn bg-danger-400">Borrar</button>
												<a href="<?php echo 'capa/'.$row_autorizados['file']; ?>" class="btn btn-info" role="button" target="_blank">Descargar</a>
												<input type="hidden" name="IDempleado" value="<?php echo $row_autorizados['IDempleado']; ?>">                
												<?php } ?>
												<input type="hidden" name="MM_update" value="form2">

												<?php } else { ?>

												<div class="modal-footer">
                                                <a href="capa_firmas.php" class="btn btn-default" role="button">Regresar</a>
                                                <input type="submit" class="btn btn-primary" value="Guardar">
												<input type="hidden" name="MM_insert" value="form2">
												<?php } ?>
									
									
									
									
											</form>

								
								</div>
								</div>
								</div>
								</div>
								</div>
								
								
								
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la fotografía?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="capa_firmas_cargar.php?IDfirma=<?php echo $row_autorizados['IDfirma']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


                                    
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
