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

set_time_limit(0);
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
if (isset($_GET['anio'])) {$anio = $_GET['anio'];} else {$anio = $row_variables['anio'];}

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
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));


if (isset($_POST['mi_semana']) && $_POST['mi_semana'] > 0) {$_SESSION['mi_semana'] = $_POST['mi_semana']; } 
if (!isset($_SESSION['mi_semana'])) {$_SESSION['mi_semana'] = $semana;} 
$la_semana = $_SESSION['mi_semana'];

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT Count(inc_captura.IDmatriz) AS Ocupados, inc_captura.IDmatriz, vac_matriz.matriz, Count(inc_captura.inc1) AS INC1, Count(inc_captura.inc2) AS INC2, Count(inc_captura.inc3) AS INC3, Count(inc_captura.inc4) AS INC4, Count(inc_captura.inc5) AS INC5, Count(inc_captura.inc6) AS INC6, Sum(inc_captura.inc1) AS CINC1, Sum(inc_captura.inc2) AS CINC2, Sum(inc_captura.inc3) AS CINC3, Sum(inc_captura.inc4) AS CINC4, Sum(inc_captura.inc5) AS CINC5,Sum(inc_captura.inc6) AS CINC6 FROM inc_captura INNER JOIN vac_matriz ON vac_matriz.IDmatriz = inc_captura.IDmatriz WHERE inc_captura.semana = '$la_semana' and inc_captura.anio = $anio GROUP BY inc_captura.IDmatriz, vac_matriz.matriz"; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT * FROM prod_semanas";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);
$totalRows_semanas = mysql_num_rows($semanas);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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


	                <!-- Content area -->
				<div class="content">
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte semanal de incidencias</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido. En este apartado se muestran los datos globales por Semana y Sucursal. Para filtrar, selecciona una semana distinta.</p>
                          <p><strong>Año:</strong> <?php echo $anio; ?></p>
                          <p><strong>Semana: </strong><?php echo $la_semana; ?></p>
                            <p><a href="admin_incidencias_montos.php">Montos PxV.</a></p>


				<!-- Statistics with progress bar -->
					<div class="row">

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Horas Extra</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><?php //echo "$" . number_format($row_costos['INC1']); ?>
                                        <a href="inc_reporte_semana12.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>">Descargar <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">x Suplencia</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><?php //echo "$" . number_format( $row_costos['INC2']); ?>
                                        <a href="inc_reporte_semana22.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>">Descargar  <i class="icon-file-download"></i></a></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">PxV</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><?php // echo "$" . number_format( $row_costos['INC5']); ?>
                                        <a href="inc_reporte_semana52.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>">Descargar  <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Incentivos</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><?php // echo "$" . number_format( $row_costos['INC3'] + $row_costos['INC6']); ?>
                                        <a href="inc_reporte_semana32.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>">Descargar  <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Domingos</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><?php // echo "$" . number_format( $row_costos['INC4']); ?>
                                        <a href="inc_reporte_semana42.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>">Descargar <i class="icon-file-download"></i></a></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
						  <div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Global</h6>
									</div>

									<div class="media-right media-middle">
									</div>
								</div>

							<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
						    </div>
										<span class="text-muted">
                                        <a href="inc_reporte_globalX.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>">Descargar <i class="icon-file-download"></i></a></span>
						  </div>
						</div>

					</div>

					<!-- /statistics with progress bar -->


                                            <p>&nbsp;</p>

                    <form method="POST" action="admin_incidencias.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td> <div class="col-lg-3 no-prints">
										<select name="mi_semana" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_semana))) {echo "selected=\"selected\"";} ?>>Semana actual</option>
                                          <?php do {  ?>
                                           <option value="<?php echo $row_semanas['semana']?>"<?php if (!(strcmp($row_semanas['semana'], $la_semana)))
										   {echo "selected=\"selected\"";} ?>>Semana <?php echo $row_semanas['semana']?></option>
											<?php
                                            } while ($row_semanas = mysql_fetch_assoc($semanas));
                                              $rows = mysql_num_rows($semanas);
                                              if($rows > 0) {
                                                  mysql_data_seek($semanas, 0);
                                                  $row_semanas = mysql_fetch_assoc($semanas);
                                              } ?></select>
										</div>
                              </td>
							  <td>
                                <button type="submit" class="btn btn-success">Filtrar <i class="icon-filter3  position-right"></i></button>	
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	


					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Sucursal</th>
                      <th>Empleados</th>
                      <th>H.Extra $(#)</th>
                      <th>x suplencia $(#)</th>
                      <th>Inc/Domingos $(#)</th>
                      <th>Domingos $(#)</th>
                      <th>PxV $(#)</th>
               	 </tr>
                    </thead>
                    <tbody>
									    <?php do { ?>
									      <tr>
									        <td><?php echo $row_detalle['matriz']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['Ocupados']; ?></td>
									        <td><?php echo "$" . number_format($row_detalle['CINC1']); ?> (<?php echo $row_detalle['INC1']; ?>)</td>                                           
									        <td><?php echo "$" . number_format($row_detalle['CINC2']); ?> (<?php echo $row_detalle['INC2']; ?>)</td>                                           
									        <td><?php echo "$" . number_format($row_detalle['CINC3'] + $row_detalle['CINC6']); ?>
                                               (<?php echo $row_detalle['INC3'] + $row_detalle['INC6']; ?>)</td>                                           
									        <td><?php echo "$" . number_format($row_detalle['CINC4']); ?> (<?php echo $row_detalle['INC4']; ?>)</td>                                           
									        <td><?php echo "$" . number_format($row_detalle['CINC5']); ?> (<?php echo $row_detalle['INC5']; ?>)</td>                                           
                    </tr>
									      <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 


					</div>
					</div>
					<!-- /panel heading options -->


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