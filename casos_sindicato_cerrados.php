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
$mi_fecha =  date('Y/m/d');


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

$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$IDmes = $el_mes = date("m") - 1;
set_time_limit(0);


// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual

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
$IDmatrizes = $row_usuario['IDmatrizes'];

if (isset($_POST['la_matriz'])) {  $_SESSION['la_matriz'] = $_POST['la_matriz']; } 
elseif (!isset($_SESSION['la_matriz'])) {  $_SESSION['la_matriz'] =  $IDmatriz; } 
$la_matriz = $_SESSION['la_matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_casos = "SELECT * FROM casos_sindicato WHERE IDestatus = 3";
mysql_query("SET NAMES 'utf8'");
$casos = mysql_query($query_casos, $vacantes) or die(mysql_error());
$row_casos = mysql_fetch_assoc($casos);
$totalRows_casos = mysql_num_rows($casos);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 12";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

// borrar alternativo
if ((isset($_GET['activar'])) && ($_GET['activar'] == 1)) {
  
  $borrado = $_GET['IDsindicato'];
  $deleteSQL = "UPDATE casos_sindicato SET IDestatus = 1 WHERE IDsindicato ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: casos_sindicato.php?info=88");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /theme JS files -->
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
                        <?php if(isset($_GET['info']) && $_GET['info'] == 3) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cambiado el estatus de forma correcta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 4) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Atención de Temas Sindicales - Casos Cerrados</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group"><strong>Instrucciones:</strong></br>
                                    1. Utiliza el filtro para identificar los casos por Sucursal.</br>
                                    2. Dando clic en <i>"Seguimiento(s)"</i> puedes ver el listado de reportes de avance:</br>
									&nbsp;&nbsp;&nbsp;&nbsp;2.1 Da clic en cada fecha para ver el detalle del seguimiento.<br />
                                    3. Dando clic en el botón <i>"Descripción"</i> puedes ver la información detallada del caso.</br>
									</p>


                             <!-- Basic text input -->

					<form method="POST" action="casos_sindicato_cerrados.php" class="form-horizontal">
					<fieldset class="content-group">
					<div class="col-lg-3">
                                 <select class="form-control" name="la_matriz">
                                <?php do { ?>
                                   <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                                   <?php
                                  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                                  $rows = mysql_num_rows($lmatriz);
                                  if($rows > 0) {
                                      mysql_data_seek($lmatriz, 0);
                                      $row_lmatriz = mysql_fetch_assoc($lmatriz);
                                  } ?> </select>
						    </div>
                            <div class="col-lg-3">
							<button type="submit" class="btn btn-primary">Filtrar</button>										
							</div>
					</fieldset>
					</form>


					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
							  <th>No. Caso</th>
							  <th>Fecha inicio</th>
							  <th>Fecha cierre</th>
							  <th class="text text-center">Asunto General</th>
							  <th class="text text-center">Seguimientos</th>
							  <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_casos > 0) { ?>

                        <?php do { 

						$IDsindicato = $row_casos['IDsindicato'];
						mysql_select_db($database_vacantes, $vacantes);
						$query_casos_seguimientos = "SELECT * FROM casos_sindicato_seguimientos WHERE IDsindicato = '$IDsindicato'";
						$casos_seguimientos = mysql_query($query_casos_seguimientos, $vacantes) or die(mysql_error());
						$row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos);
						$totalRows_casos_seguimientos = mysql_num_rows($casos_seguimientos);
						
						?>
                          <tr>
                            <td><?php echo $row_casos['IDsindicato']; ?></td>
                            <td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_inicio']))?></td>
                            <td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_fin']))?></td>
                            <td><?php echo $row_casos['asunto']; ?></td>
							<td>
							<a class="collapsed text-orange text-semibold" data-toggle="collapse" href="#collapse-group<?php echo $row_casos['IDsindicato']; ?>E1"><?php echo $totalRows_casos_seguimientos; ?> seguimiento(s)<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_casos['IDsindicato']; ?>E1" class="panel-collapse collapse">
								<ul class="list list-icons">
							<?php if ($totalRows_casos_seguimientos > 0) { ?>
							<?php do { ?>
								<li><i class="icon-files-empty2 text-orange position-left"></i><a class="text-orange" href="casos_cerrados_sindicato_seguimientos.php?IDsindicato=<?php echo $row_casos['IDsindicato'];?>&IDsindicato_seguimientos=<?php echo $row_casos_seguimientos['IDsindicato_seguimientos']; ?>"><?php echo date( 'd/m/Y', strtotime($row_casos_seguimientos['fecha_reporte'])); ?></a></li>
							<?php } while ($row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos)); ?>
							<?php } ?>
								</ul>
							</div>
							</td>
							 <td>
							 <button type="button" data-target="#modal_theme_danger2<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-primary">Descripción</button>
							<button type="button" data-target="#modal_theme_danger3<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-success">Activar</button>
                           </tr>

									<!-- danger modal -->
									<div id="modal_theme_danger2<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Descripción del Caso</h6>
												</div>
												<div class="modal-body">
												<p><strong>Descripción del Caso:</strong><br />
												<?php echo $row_casos['descripcion']; ?><br /></p>

												<p><strong>Descripción del Cierre:</strong><br />
												<?php echo $row_casos['descripcion_cierre']; ?><br /></p>

												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->
						   
									<!-- danger modal -->
									<div id="modal_theme_danger3<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Descripción del Caso</h6>
												</div>
												<div class="modal-body">
												<p>¿Estas seguro de que quieres reactivar el Caso?</p>

												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-success" href="casos_sindicato_cerrados.php?IDsindicato=<?php echo $row_casos['IDsindicato']; ?>&activar=1">Si activar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->
						   


                          <?php } while ($row_casos = mysql_fetch_assoc($casos)); ?>
                         <?php } else { ?>
                         <tr><td colspan="9">Sin casos con el filtro seleccionado.</td></tr>
                         <?php } ?>
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
			<!-- /main content -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->
</body>
</html>