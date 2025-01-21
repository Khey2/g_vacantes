<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/b_tNG.inc.php');

// Make unified connection variable
$conn_nom35 = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 
$el_mes = date("m"); 


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM capa_becarios WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];


$IDempleado = $row_usuario['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_evaluado = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, vac_matriz.matriz, vac_areas.area, vac_subareas.subarea FROM capa_becarios LEFT JOIN vac_subareas ON capa_becarios.IDsubarea = vac_subareas.IDsubarea LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo LEFT JOIN vac_matriz ON capa_becarios.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON capa_becarios.IDarea = vac_areas.IDarea WHERE capa_becarios.IDempleado = $IDempleado";
$evaluado = mysql_query($query_evaluado, $vacantes) or die(mysql_error());
$row_evaluado = mysql_fetch_assoc($evaluado);
$totalRows_evaluado = mysql_num_rows($evaluado);
$IDsubarea = $row_evaluado['IDsubarea'];
$IDarea = $row_evaluado['IDarea'];
$IDmatriz_b = $row_evaluado['IDmatriz'];
$IDsucursal = $row_evaluado['IDsucursal'];
$IDtipo = $row_evaluado['IDtipo'];

if (isset($_POST["estatus"])) { $_SESSION["estatus"] = $_POST["estatus"];}
if (!isset($_SESSION["estatus"])) { $_SESSION["estatus"] = 1;}
$estatus = $_SESSION["estatus"]; 

$query_mis_metas = "SELECT * FROM capa_becarios_sed WHERE capa_becarios_sed.IDempleado = $IDempleado AND estatus = $estatus"; 
mysql_query("SET NAMES 'utf8'");
$mis_metas = mysql_query($query_mis_metas, $vacantes) or die(mysql_error());
$row_mis_metas = mysql_fetch_assoc($mis_metas);
$totalRows_mis_metas = mysql_num_rows($mis_metas);

$query_unidades = "SELECT * FROM sed_unidad_medida"; 
$unidades = mysql_query($query_unidades, $vacantes) or die(mysql_error());
$row_unidades = mysql_fetch_assoc($unidades);


$query_mis_metasA = "SELECT * FROM capa_becarios_sed WHERE capa_becarios_sed.IDempleado = $IDempleado AND estatus = 0"; 
$mis_metasA = mysql_query($query_mis_metasA, $vacantes) or die(mysql_error());
$row_mis_metasA = mysql_fetch_assoc($mis_metasA);
$totalRows_mis_metasA = mysql_num_rows($mis_metasA);
$query_mis_metasB = "SELECT * FROM capa_becarios_sed WHERE capa_becarios_sed.IDempleado = $IDempleado AND estatus = 1"; 
$mis_metasB = mysql_query($query_mis_metasB, $vacantes) or die(mysql_error());
$row_mis_metasB = mysql_fetch_assoc($mis_metasB);
$totalRows_mis_metasB = mysql_num_rows($mis_metasB);
$query_mis_metasC = "SELECT * FROM capa_becarios_sed WHERE capa_becarios_sed.IDempleado = $IDempleado AND estatus = 2"; 
$mis_metasC = mysql_query($query_mis_metasC, $vacantes) or die(mysql_error());
$row_mis_metasC = mysql_fetch_assoc($mis_metasC);
$totalRows_mis_metasC = mysql_num_rows($mis_metasC);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>


	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>


	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<!-- /Theme JS files -->

 </head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/b_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/b_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/b_pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">
              
					<h1 class="text-center content-group text-danger">
						Evaluación del Desempeño
						<small class="display-block">Programa Semillero de Talento</small>
					</h1>


							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Instrucciones</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
											Sigue estos sencillos pasos:
                                      <ol>
										<li>Consulta el material de  apoyo <a href="files/SMART.pdf" download>metas SMART</a> y redacta cada una de los objetivos a comprometer. </li>
										<li>Los objetivos se deberán acordar entre el evaluado y el jefe inmediato.</li>
                                      </ol>
                                    </div>
								</div>
							</div>
							<!-- /about author -->
                            
				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
                        
<?php if ($totalRows_mis_metas > 0) { ?>                       
                        

							<!-- /inicia ciclo metas -->
<?php $count = 1;  do { $IDmeta = $row_mis_metas['IDmeta']; ?>
							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Objetivo <?php echo $count; ?></h6>
								</div>

                    <div class="row">
						<div class="col-md-9">
                        
                        <div class="table-responsive">
							<table class="table table-xxs">
								<thead>
									<tr class="border-bottom-primary">
										<th colspan="2"><div class="text-bold content-group"> <?php echo $row_mis_metas['mi_mi']; ?></div></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="15%"><div class="text-bold content-group text-info">Sobresaliente:</div></td>
										<td width="85%"> <?php echo $row_mis_metas['mi_3']; ?></td>
									</tr>
									<tr>
										<td><div class="text-bold content-group text-success">Satisfactorio:</div></td>
										<td> <?php echo $row_mis_metas['mi_2']; ?></td>
									</tr>
									<tr>
										<td><div class="text-bold content-group text-danger">Deficiente:</div></td>
										<td> <?php echo $row_mis_metas['mi_1']; ?></td>
									</tr>
										<?php if ($row_mis_metas['mi_obs'] != '') {?>
									<tr>
										<td><div class="text-bold content-group">Observaciones:</div></td>
										<td> <?php echo $row_mis_metas['mi_obs']; ?></td>
									</tr>
										<?php } ?>
								</tbody>
							</table>    
						</div>
                        
						</div>

						<div class="col-md-3">
							<div class="panel-body">
								<p class="content-group-sm"><strong>Unidad de Medida: </strong>
								<?php   switch ($row_mis_metas['mi_IDunidad']) {
									case 1: $unidad = 'Cantidad.';  break;    
									case 2: $unidad = 'Calidad.';  break;    
									case 3: $unidad = 'Cantidad-Costo.';  break;    
									case 4: $unidad = 'Cantidad-Calidad.';  break;    
									case 5: $unidad = 'Cantidad-Tiempo.';  break;    
									case 6: $unidad = 'Costo-Calidad.';  break;    
									case 7: $unidad = 'Tiempo.';  break;    
									case 8: $unidad = 'Tiempo-Calidad.';  break;    
									case 9: $unidad = 'Tiempo-Costo.';  break;    
								  } echo $unidad;
 								?></p>
								<p class="content-group-sm"><strong>Fecha Captura: </strong>
								<?php if ($row_mis_metas['fecha_captura']  != '') {echo date( 'd/m/Y' , strtotime($row_mis_metas['fecha_captura'])); } else { echo "-";}	?></p>

								<p class="content-group-sm"><strong>Fecha Compromiso: </strong>
								<?php if ($row_mis_metas['fecha_termino']  != '') {echo date( 'd/m/Y' , strtotime($row_mis_metas['fecha_termino'])); } else { echo "-";}	?></p>
								
								<p class="content-group-sm"><strong>Fecha Evaluación: </strong>
								<?php if ($row_mis_metas['fecha_cierre']  != '') {echo date( 'd/m/Y' , strtotime($row_mis_metas['fecha_cierre'])); } else { echo "-";}	?></p>

								<?php if ($row_mis_metas['estatus'] == 1) {?>
                                
								<p class="content-group-sm"><strong>Resultado: </strong>
								<?php 
							      if($row_mis_metas['mi_resultado'] == 1) { echo "<span class='label label-primary'>Sobresaliente</span>"; } 
							 else if($row_mis_metas['mi_resultado'] == 2) { echo "<span class='label label-success'>Satisfactorio</span>"; } 
							 else if($row_mis_metas['mi_resultado'] == 3) { echo "<span class='label label-warning'>Deficiente</span>"; } 
							 else if($row_mis_metas['mi_resultado'] == 4) { echo "<span class='label label-default'>En proceso-No aplica</span>"; } 
							 else { echo "<span class='label label-default'>Sin Evaluación</span>";} ?></p>
                              <?php } ?>
							  
							</div>
						</div>
                        
                        
					</div>
					<!-- /course overview -->


 <?php $count++; } while ($row_mis_metas = mysql_fetch_assoc($mis_metas)); ?>                           
							<!-- /termina ciclo metas -->
                            
                            
<?php } else { ?>   

							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Objetivo 1</h6>
								</div>

								<div class="row">
									<div class="col-md-9">
									
										<div class="table-responsive">
											<table class="table">
												<tbody>
													<tr>
													<th><div class="content-group">No se encontraron Objetivos con el crtierio seleccionado.</div></th>
													</tr>
												</tbody>
										   </table>    
										</div>
									</div>
								</div>
							</div>
							<!-- /course overview -->
                    
<?php } ?>                       
                            
						</div>
					</div>
					<!-- /detached content -->


					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">
							
								<!-- Categories -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Acciones</span>
									</div>

									<div class="category-content no-padding">
									<div class="category-content">
                                    											 
										<form method="post" name="form1" action="b_sed.php" > 
										<div class="col-lg-8">
											<select name="estatus" id="estatus" class="form-control">
												<option value="1"<?php if ($estatus == 1) {echo "SELECTED";} ?>>En proceso</option> 
												<option value="2"<?php if ($estatus == 2) {echo "SELECTED";} ?>>Evaluados</option> 
												<option value="0"<?php if ($estatus == 0) {echo "SELECTED";} ?>>Cerrados</option> 
											</select>
										</div>
										<div class="col-lg-4">
										<input type="submit" class="btn btn-primary" value="Filtrar">										 
										</div>
										</form> 
										<p>&nbsp;</p>
                                     </div>   
									</div>
								</div>
								<!-- /categories -->

							<!-- Application status -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Resultados</h6>
								</div>

								<div class="panel-body">
									<ul class="progress-list">
									<?php
									$lasuma =  $totalRows_mis_metasA + $totalRows_mis_metasB + $totalRows_mis_metasC;
									if ($lasuma > 0) {
									$avanceA = ($totalRows_mis_metasA / $lasuma) * 100; 
									$avanceB = ($totalRows_mis_metasB / $lasuma) * 100; 
									$avanceC = ($totalRows_mis_metasC / $lasuma) * 100; 
									} else {
									$avanceA = 0; 
									$avanceB = 0; 
									$avanceC = 0; 
										}
									?>
							            <li>
							                <label>En proceso<span><?php echo $totalRows_mis_metasB;?></span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-info" style="width: <?php echo $avanceB;?>%">
													<span class="sr-only"><?php echo $totalRows_mis_metasB;?></span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Evaluados<span><?php echo $totalRows_mis_metasC;?></span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-success" style="width: <?php echo $avanceC;?>%">
													<span class="sr-only"><?php echo $totalRows_mis_metasC;?></span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Cerrados<span><?php echo $totalRows_mis_metasA;?></span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-warning" style="width: <?php echo $avanceA;?>%">
													<span class="sr-only"><?php echo $totalRows_mis_metasA;?></span>
												</div>
											</div>
							            </li>

							        </ul>
								</div>
							</div>
							<!-- /application status -->


								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Datos del Evaluado</span>
									</div>

									<div class="category-content">

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Nombre:</label>
											<div><?php echo $row_evaluado['emp_paterno']." ". $row_evaluado['emp_materno']." ". $row_evaluado['emp_nombre']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Programa:</label>
											<div><?php echo $row_evaluado['tipo']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Fecha Alta:</label>
											<div><?php echo date('d/m/Y', strtotime($row_evaluado['fecha_alta'])); ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Fecha Baja:</label>
											<div><?php if ($row_evaluado['fecha_baja'] != '') {echo date('d/m/Y', strtotime($row_evaluado['fecha_baja']));} else { echo "No aplica";} ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $row_evaluado['area']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Subárea:</label>
											<div><?php echo $row_evaluado['subarea']; ?></div>
										</div>

									</div>
								</div>
								<!-- /course details -->

							</div>
						</div>
					</div>
		            <!-- /detached sidebar -->


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