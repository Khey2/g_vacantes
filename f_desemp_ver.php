<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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

if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;

$query_periodo_sed = "SELECT * FROM sed_periodos_sed WHERE IDperiodo = '$IDperiodo'";
$periodo_sed = mysql_query($query_periodo_sed, $vacantes) or die(mysql_error());
$row_periodo_sed = mysql_fetch_assoc($periodo_sed);
$_periodo = $row_periodo_sed['periodo'];
$estatus_periodo = $row_periodo_sed['estatus'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }


$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$el_usuario = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_evaluado = "SELECT prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.IDempleado, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, prod_activos.IDllave, vac_areas.area, vac_matriz.matriz FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz  WHERE prod_activos.IDempleado = '$el_usuario'";
$evaluado = mysql_query($query_evaluado, $vacantes) or die(mysql_error());
$row_evaluado = mysql_fetch_assoc($evaluado);
$totalRows_evaluado = mysql_num_rows($evaluado);

$_nombre = $row_evaluado['emp_nombre'] . " " . $row_evaluado['emp_paterno'] . " " . $row_evaluado['emp_materno'];
$_puesto = $row_evaluado['denominacion'];
$_sucursal = $row_evaluado['matriz'];
$_area = $row_evaluado['area'];
$_fecha_ingreso = $row_evaluado['fecha_alta'];
$IDempleado = $row_evaluado['IDempleado'];
$IDllave = $row_evaluado['IDllave'];

$query_mis_metas = "SELECT * FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'"; 
mysql_query("SET NAMES 'utf8'");
$mis_metas = mysql_query($query_mis_metas, $vacantes) or die(mysql_error());
$row_mis_metas = mysql_fetch_assoc($mis_metas);
$totalRows_mis_metas = mysql_num_rows($mis_metas);

$query_resultados = "SELECT * FROM sed_individuales_resultados WHERE sed_individuales_resultados.IDempleado = '$el_usuario' AND sed_individuales_resultados.IDperiodo = '$IDperiodo'"; 
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);
$estatus_actual = $row_resultados['estatus'];

$query_indicadores = "SELECT * FROM sed_indicadores_tipos"; 
$indicadores = mysql_query($query_indicadores, $vacantes) or die(mysql_error());
$row_indicadores = mysql_fetch_assoc($indicadores);

$query_unidades = "SELECT * FROM sed_unidad_medida"; 
$unidades = mysql_query($query_unidades, $vacantes) or die(mysql_error());
$row_unidades = mysql_fetch_assoc($unidades);

$query_ponderacion = "SELECT Sum(sed_individuales.mi_ponderacion) AS total_p, Count(sed_individuales.mi_mi) AS total_m FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'";  
$ponderacion = mysql_query($query_ponderacion, $vacantes) or die(mysql_error());
$row_ponderacion = mysql_fetch_assoc($ponderacion);
$ponderacion_total = $row_ponderacion['total_p'];
$metas_total = $row_ponderacion['total_m'];

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
    
   	<script>
	<?php if ($totalRows_mis_metas == 0) { ?> 
	 $(document).ready(function(){ $("#capturar").modal('show'); }); 
	<?php } ?>
	</script>

 </head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">
              
					<h1 class="text-center content-group text-danger">
						Evaluación del Desempeño
						<small class="display-block"><?php echo $row_periodo_sed['periodo']; ?></small>
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
        <li>Consulta el material de  apoyo <a href="files/SMART.pdf" download>metas SMART</a> y redacta cada una de las metas a comprometer. </li>
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

									<div class="heading-elements">
				                	</div>
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
                      	<tr>
                        	<td><div class="text-bold content-group">Observaciones:</div></td>
                        	<td> <?php echo $row_mis_metas['mi_obs']; ?></td>
                      </tr>
                     </tbody>
                   </table>    
						</div>
                        
						</div>

						<div class="col-md-3">
							<div class="panel-body">
								<p class="content-group-sm"><strong>Ponderación: </strong><?php echo $row_mis_metas['mi_ponderacion']; ?>%</p>
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
								<p class="content-group-sm"><strong>Tipo Indicador: </strong>
								<?php   switch ($row_mis_metas['mi_IDindicador']) {
									case 1: $indicador = 'Estratégico Sahuayo.';  break;    
									case 2: $indicador = 'Estratégico del Área.';  break;    
									case 3: $indicador = 'Funcional.';  break;    
								  } echo $indicador;
 								?></p>
								<?php if ($row_mis_metas['estatus'] == 3 or $row_mis_metas['estatus'] == 2) {?>
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
                       				<th><div class="content-group">Aún no cuentas con Objetivos capurados en este periodo de evaluación.</div>
                                   		<div class="content-group">
                                        <button type="button" data-target="#capturar" data-toggle="modal" class="btn btn-success btn-xs">Agregar Objetivo</button></div>
                                    </th>
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
                                    	<p><a class="btn btn-primary  btn-xs btn-block content-group" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario; ?>&print=1"><i class="icon-printer4"></i> Imprimir Objetivos</a></p>
                                    
                                    <p><strong>Ponderación:</strong>
                                     <?php if ($ponderacion_total != 100) { 
									echo '<span class="control-label no-margin text-danger">' . $ponderacion_total . '% (Revisar)</span>';
									} else {
									echo '<span class="control-label no-margin text-success">' . $ponderacion_total . '% (Correcto)</span>'; } ?>
                                    </p>
                                     <p><strong># Objetivos:</strong>
                                      <?php if ($metas_total > 7) { 
									echo '<span class="control-label no-margin text-warning">' . $metas_total . ' (Muchos)</span>';
									} else if ($metas_total > 2) { 
									echo '<span class="control-label no-margin text-success">' . $metas_total . ' (Correcto)</span>';
									} else {
									echo '<span class="control-label no-margin text-warning">' . $metas_total . ' (Pocos)</span>';
									} ?>
                                    </p>
                                    <p><strong>Resultado:</strong>
                                      <?php if ($estatus_actual == 3) { 
							      if($row_resultados['resultado'] > 95) { echo $row_resultados['resultado']. "% <span class='label label-primary'>Sobresaliente</span>"; } 
							 else if($row_resultados['resultado'] > 75) { echo $row_resultados['resultado']. "% <span class='label label-success'>Satisfactorio</span>"; } 
							 else if($row_resultados['resultado'] > 1 ) { echo $row_resultados['resultado']. "% <span class='label label-warning'>Deficiente</span>"; } 
							 else { echo "<span class='label label-default'>Sin Evaluación</span>";}
									  } ?>
                                    </p>
                                     </div>   
									</div>
								</div>
								<!-- /categories -->



								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Datos del Evaluado</span>
									</div>

									<div class="category-content">

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Nombre:</label>
											<div><?php echo $_nombre; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<div><?php echo $_puesto; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Sucursal:</label>
											<div><?php echo $_sucursal; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $_area; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Periodo de Evaluación:</label>
											<div><?php echo $_periodo; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Fecha Ingreso:</label>
											<div><?php echo $_fecha_ingreso; ?></div>
										</div>

									</div>
								</div>
								<!-- /course details -->


								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Fechas Compromiso</span>
									</div>

									<div class="category-content">
										<ul class="media-list">

												  <?php
	 											    $query_mensajes = "SELECT * FROM sed_mensajes WHERE IDperiodo = '$IDperiodo'"; 
													$mensajes = mysql_query($query_mensajes, $vacantes) or die(mysql_error());
													$row_mensajes = mysql_fetch_assoc($mensajes);
													
													do { ?>

											<li class="media">
												<div class="media-left">
													<h5 class="no-margin text-center text-success"> <?php echo $row_mensajes['mes']; ?>
														<small class="display-block text-size-small no-margin"><?php echo $row_mensajes['anio']; ?></small>
													</h5>
												</div>

												<div class="media-body">
													<span class="text-semibold"><?php echo $row_mensajes['mensaje']; ?></span>
													<ul class="list-inline list-inline-separate no-margin-bottom mt-5">
														<li><span class="text-muted"><?php echo $row_periodo_sed['periodo']; ?></span></li>
													</ul>
												</div>
											</li>

												  <?php } while ($row_mensajes = mysql_fetch_assoc($mensajes)); ?>

										</ul>
									</div>
								</div>
								<!-- /upcoming courses -->

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