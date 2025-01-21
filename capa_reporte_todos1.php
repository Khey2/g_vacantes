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


if (isset($_POST['anio'])) {$_SESSION['anio']  = $_POST['anio'];}
if (!isset($_SESSION['anio'])) {$_SESSION['anio'] = $anio;}
$anio = $_SESSION['anio'];


mysql_select_db($database_vacantes, $vacantes);
$query_cursos = "SELECT capa_cursos.IDC_capa_cursos, capa_cursos.nombre_curso, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 1 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Ene`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 2 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Feb`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 3 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Mar`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 4 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Abr`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 5 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `May`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 6 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Jun`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 7 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Jul`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 8 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Ags`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 9 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Sep`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 10 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Oct`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 11 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Nov`, COALESCE ( COUNT(( CASE WHEN (capa_avance.mes = 12 ) THEN capa_avance.IDC_capa_cursos END )), 0 ) AS `Dic` FROM capa_avance LEFT JOIN capa_cursos ON capa_avance.IDC_capa_cursos = capa_cursos.IDC_capa_cursos WHERE capa_avance.anio = $anio AND capa_cursos.nombre_curso != '' GROUP BY capa_avance.IDC_capa_cursos";
mysql_query("SET NAMES 'utf8'");
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
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html59.js"></script>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Cursos Impartidos.</p>



			<form method="POST" action="capa_reporte_todos1.php">
					<table class="table">
							<tr>
                           <td>
							 <select name="anio" class="form-control">
							 <option value="2025" <?php if ( 2025 == $anio) {echo "selected=\"selected\"";} ?>>2025</option>
							 <option value="2024" <?php if ( 2024 == $anio) {echo "selected=\"selected\"";} ?>>2024</option>
							 <option value="2023" <?php if ( 2023 == $anio) {echo "selected=\"selected\"";} ?>>2023</option>
							 <option value="2022" <?php if ( 2022 == $anio) {echo "selected=\"selected\"";} ?>>2022</option>
							 <option value="2021" <?php if ( 2021 == $anio) {echo "selected=\"selected\"";} ?>>2021</option>
									</select>
						   </td>
                           <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button> 
							</td>
					      </tr>
				    </table>
			</form>


						
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>ID</th>
                          <th>Curso</th>
                          <th>Ene</th>
                          <th>Feb</th>
                          <th>Mar</th>
                          <th>Abr</th>
                          <th>May</th>
                          <th>Jun</th>
                          <th>Jul</th>
                          <th>Ags</th>
                          <th>Sep</th>
                          <th>Oct</th>
                          <th>Nov</th>
                          <th>Dic</th>
						 </tr>
					    </thead>
						<tbody>							  
						<?php do { ?>
                        <tr>
                          <td><?php echo $row_cursos['IDC_capa_cursos']; ?></td>
                          <td><?php echo $row_cursos['nombre_curso']; ?></td>
                          <td><?php echo $row_cursos['Ene']; ?></td>
                          <td><?php echo $row_cursos['Feb']; ?></td>
                          <td><?php echo $row_cursos['Mar']; ?></td>
                          <td><?php echo $row_cursos['Abr']; ?></td>
                          <td><?php echo $row_cursos['May']; ?></td>
                          <td><?php echo $row_cursos['Jun']; ?></td>
                          <td><?php echo $row_cursos['Jul']; ?></td>
                          <td><?php echo $row_cursos['Ags']; ?></td>
                          <td><?php echo $row_cursos['Sep']; ?></td>
                          <td><?php echo $row_cursos['Oct']; ?></td>
                          <td><?php echo $row_cursos['Nov']; ?></td>
                          <td><?php echo $row_cursos['Dic']; ?></td>
                        </tr> 					
                        <?php } while ($row_cursos = mysql_fetch_assoc($cursos)); ?>
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