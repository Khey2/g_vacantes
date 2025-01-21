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
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
if (isset($_POST['anio'])) {$anio = $_POST['anio'];} else {$anio = $row_variables['anio'];}
if (isset($_POST['la_matriz'])) {$la_matriz = $_POST['la_matriz'];} 
if (isset($_POST['IDestatus'])) {$IDestatus = $_POST['IDestatus'];} else {$IDestatus = 1;}

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_casos = "SELECT * FROM incapacidades_certificados WHERE IDincapacidad = 0 AND (IDmatriz = $IDmatriz OR IDmatriz = 0)"; 
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
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<!-- /theme JS files -->
</head>

	<style>
	.hiddenRow {
    padding: 0 !important;
	}
	</style>

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
                        <?php if(isset($_GET['info']) && $_GET['info'] == 3) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cambiado el estatus de forma correcta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 99) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cerrado correctamente la incapacidad.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 4) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la incapacidad.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la incapacidad.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la incapacidad.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 11))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 12))) { ?>
					    <div class="alert bg-info-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 13) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						
					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Accidentes de Trabajo (Incapacidades no asignadas).</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group"><strong>Instrucciones:</strong></br>
									A continuación se muestran las incapacidades cargadas pore el área de Nóminas que no tienen incapacidad asignada.</br>
									Selecciona la opción que corresponda para asignarlo a un Accidente de trabajo <span class="text-success text-bold">(AC)</span> o a un Incidente de Incapacidad <span class="text-info text-bold">(IN)</span>.<br/>
									Si existe alguna incapacidad o incidente activo para el empleado, se mostrará el botón correspondiente para asociarlo.
								</p>
								</div>
									

					<table class="table table-condensed datatable-button-html5-columns">
                    <thead>
							<tr class="bg-primary"> 
							<th>Folio</th>		
							<th>IoS</th>
							<th>No. Emp.</th>
							<th>IMSS</th>
							<th>Empleado</th>
							<th>Tipo</th>
							<th>Fecha Inicio</th>	
							<th>Fecha Fin</th>	
							<th>Dias</th>	
							<th>Acciones</th>	
						</tr>
					</thead>	
					<tbody>
					<?php if ($totalRows_casos > 0) { ?>
						<?php do { 
							$IDempleado = $row_casos['IDempleado'];
							$query_nombrem = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
							$nombrem = mysql_query($query_nombrem, $vacantes) or die(mysql_error());
							$row_nombrem = mysql_fetch_assoc($nombrem);
							$totalRows_nombrem = mysql_num_rows($nombrem);

							//validamos si existen accidentes activos para dar la opcion de agregarlo.
							$query_exitentes = "SELECT * FROM incapacidades_accidentes WHERE IDempleado = $IDempleado AND IDestatus = 1 AND IDincapacidad_accidente = 1";
							$exitentes = mysql_query($query_exitentes, $vacantes) or die(mysql_error());
							$row_exitentes = mysql_fetch_assoc($exitentes);
							$totalRows_exitentes = mysql_num_rows($exitentes);

							$query_exitentes2 = "SELECT * FROM incapacidades_accidentes WHERE IDempleado = $IDempleado AND IDestatus = 1 AND IDincapacidad_accidente = 2";
							$exitentes2 = mysql_query($query_exitentes2, $vacantes) or die(mysql_error());
							$row_exitentes2 = mysql_fetch_assoc($exitentes2);
							$totalRows_exitentes2 = mysql_num_rows($exitentes2);

							?>
                        <tr>
							<td>
								<?php if ($row_casos['IDestatus'] == 2) { ?>
								<i class="text text-success icon-checkmark5"></i>
								<?php } else {?>
								<i class="text text-danger icon-cross"></i>
								<?php } ?>
								<?php echo $row_casos['folio_certificado']; ?>
							</td>
							<td><?php if ($row_casos['IDtipo_certificado'] == 1) {echo "Inicial";} else { echo "Subsecuente";}  ?></td>
							<td><?php echo $row_casos['IDempleado'] ?></td>
							<td><?php echo $row_casos['nss'] ?></td>
							<td><?php if ($totalRows_nombrem > 0) {echo $row_nombrem['emp_paterno']." ".$row_nombrem['emp_materno']." ".$row_nombrem['emp_nombre']; } else { echo "-";}?></td>
							<td><?php if ($row_casos['IDtipo_incapacidad'] == 1) {echo "EG";} 
								 else if ($row_casos['IDtipo_incapacidad'] == 2) {echo "AT";} 
								 else if ($row_casos['IDtipo_incapacidad'] == 3) {echo "MA";} 
								 else if ($row_casos['IDtipo_incapacidad'] == 4) {echo "HC";}  ?></td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_inicio'])) ?></td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_fin'])) ?></td>
							<td><?php echo $row_casos['dias']; ?></td>
							<td>
							<a href="incapacidades_edit.php?IDempleado=<?php echo $row_casos['IDempleado']; ?>&IDcertificado=<?php echo $row_casos['IDcertificado']; ?>&agregar=1" class="btn btn-success btn-xs">AC</a>
							<a href="incapacidades_edit_z.php?IDempleado=<?php echo $row_casos['IDempleado']; ?>&IDcertificado=<?php echo $row_casos['IDcertificado']; ?>&agregar=1" class="btn btn-info btn-xs">IN</a>
							<?php if ($totalRows_exitentes > 0) { ?>
							<button type="button" data-target="#modal_theme_danger<?php echo $row_casos['IDcertificado']; ?>"  data-toggle="modal" class="btn btn-danger btn-xs"><i class="icon-file-plus"></i></button>
							<?php } ?>
							<?php if ($totalRows_exitentes2 > 0) { ?>
							<button type="button" data-target="#modal_theme_primary<?php echo $row_casos['IDcertificado']; ?>"  data-toggle="modal" class="btn btn-primary btn-xs"><i class="icon-file-plus"></i></button>
							<?php } ?>
							</td>
						</tr>



                     <!-- danger modal -->
					 <div id="modal_theme_danger<?php echo $row_casos['IDcertificado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Asignación de Incapacidad a Accidente</h6>
								</div>

								<div class="modal-body">
								Selecciona el Accidente para asignar la incapacidad.<br/><br/>

								<?php do { 

									echo $row_exitentes['emp_paterno']." ".$row_exitentes['emp_materno']." ".$row_exitentes['emp_nombre']." (".$row_exitentes['IDempleado'].") - ".date( 'd/m/Y' , strtotime($row_exitentes['fecha_inicio']))." <a href='incapacidades_asignar.php?IDcertificado=".$row_casos['IDcertificado']."&IDincapacidad=".$row_exitentes['IDincapacidad']."'><i class='icon icon-arrow-right6'></i></a><br/>";

								} while ($row_exitentes = mysql_fetch_assoc($exitentes)); ?>

								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->



					<!-- danger modal -->
					<div id="modal_theme_primary<?php echo $row_casos['IDcertificado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Asignación de Incapacidad a Incidente</h6>
								</div>

								<div class="modal-body">
								Selecciona el incidente para asignar la incapacidad.<br/><br/>

								<?php do { 

									echo $row_exitentes2['emp_paterno']." ".$row_exitentes2['emp_materno']." ".$row_exitentes2['emp_nombre']." (".$row_exitentes2['IDempleado'].") - ".date( 'd/m/Y' , strtotime($row_exitentes2['fecha_inicio']))." <a href='incapacidades_asignar.php?IDcertificado=".$row_casos['IDcertificado']."&IDincapacidad=".$row_exitentes2['IDincapacidad']."'><i class='icon icon-arrow-right6'></i></a><br/>";

									} while ($row_exitentes2 = mysql_fetch_assoc($exitentes2)); ?>

								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->



						<?php } while ($row_casos = mysql_fetch_assoc($casos)); } else { ?>
							<tr>
							<td colspan="10"> No se tienen Certificados sin asignar.</td>
						</tr>
						<?php } ?>
                    </tbody>
               	</table>


				



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