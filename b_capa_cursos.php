<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/b_tNG.inc.php');

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
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_capacitacion = "SELECT * FROM capa_becarios_seguridad WHERE IDempleado = $el_usuario";
$capacitacion = mysql_query($query_capacitacion, $vacantes) or die(mysql_error());
$row_capacitacion = mysql_fetch_assoc($capacitacion);
$totalRows_capacitacion = mysql_num_rows($capacitacion);

if ( $row_capacitacion['modulo1'] != '') {$modulo1 = 1;} else {$modulo1 = 0;}
if ( $row_capacitacion['modulo2'] != '') {$modulo2 = 1;} else {$modulo2 = 0;}
if ( $row_capacitacion['modulo3'] != '') {$modulo3 = 1;} else {$modulo3 = 0;}
if ( $row_capacitacion['modulo4'] != '') {$modulo4 = 1;} else {$modulo4 = 0;}
if ( $row_capacitacion['modulo5'] != '') {$modulo5 = 1;} else {$modulo5 = 0;}
if ( $row_capacitacion['evaluacion'] != '') {$evaluacion = 1;} else {$evaluacion = 0;} 
if ($modulo1 + $modulo2 + $modulo3 + $modulo4 + $modulo5 == 5) {$acceso_evaluacion = 1;} else {$acceso_evaluacion = 0;}

$query_evaluacioncurso = "SELECT AVG(preg21) AS Total FROM capa_becarios_seguridad_respuestas";
$evaluacioncurso = mysql_query($query_evaluacioncurso, $vacantes) or die(mysql_error());
$row_evaluacioncurso = mysql_fetch_assoc($evaluacioncurso);
$totalRows_evaluacioncurso = mysql_num_rows($evaluacioncurso);

$query_evaluacioncurso2 = "SELECT preg21 FROM capa_becarios_seguridad_respuestas";
$evaluacioncurso2 = mysql_query($query_evaluacioncurso2, $vacantes) or die(mysql_error());
$row_evaluacioncurso2 = mysql_fetch_assoc($evaluacioncurso2);
$totalRows_evaluacioncurso2 = mysql_num_rows($evaluacioncurso2);

$evaluacion_curso = round($row_evaluacioncurso['Total'],1);


// borrar alternativo
if (isset($_GET['IDmodulo'])) {
  
	$IDmodulo = $_GET['IDmodulo'];
	$modulo = "modulo";
	$fecha= "_fecha";

	if ($totalRows_capacitacion  > 0){

	$deleteSQL = "UPDATE capa_becarios_seguridad SET $modulo$IDmodulo = 1, $modulo$IDmodulo$fecha =  NOW() WHERE IDempleado = $el_usuario";
	mysql_select_db($database_vacantes, $vacantes);
	$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
	echo $deleteSQL;

	} else {

	$deleteSQL = "INSERT INTO capa_becarios_seguridad (IDempleado, $modulo$IDmodulo, $modulo$IDmodulo$fecha) VALUES ($el_usuario, 1, NOW())";
	mysql_select_db($database_vacantes, $vacantes);
	$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
	
	}

	header("Location: b_capa_cursos.php?info=1");
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
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<!-- /theme JS files -->

	<?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
	<script>
	 setTimeout(function(){
    $('.alert').fadeTo("slow", 0.1, function(){
        $('.alert').alert('close')
    });     
    }, 3000)    
    </script>
	<?php } ?>

</head>
<body class="has-detached-right <?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>">

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
                
						<!-- Basic alert -->
						<?php if ($row_capacitacion['calificacion'] != '') { ?>	
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Gracias por tu participación, has completado el curso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
				
					<!-- Detached content -->
					<div>
						<div>

							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Curso de Ciberseguridad Sahuayo</h6>

									<div class="heading-elements">
										<ul class="list-inline list-inline-separate heading-text">
											<li><b>Evaluación:</b> <span class="text-semibold"><?php echo $evaluacion_curso; ?></span></li>
											<li>
												<?php if ( $evaluacion_curso >= 1) { ?>
												<i class="icon-star-full2 text-size-base text-warning-300"></i>
												<?php } else {  ?>
												<i class=" icon-star-empty3 text-size-base text-warning-300"></i>
												<?php }	?>
												<?php if ( $evaluacion_curso >= 2) { ?>
												<i class="icon-star-full2 text-size-base text-warning-300"></i>
												<?php } else {  ?>
												<i class=" icon-star-empty3 text-size-base text-warning-300"></i>
												<?php }	?>
												<?php if ( $evaluacion_curso >= 3) { ?>
												<i class="icon-star-full2 text-size-base text-warning-300"></i>
												<?php } else {  ?>
												<i class=" icon-star-empty3 text-size-base text-warning-300"></i>
												<?php }	?>
												<?php if ( $evaluacion_curso >= 4) { ?>
												<i class="icon-star-full2 text-size-base text-warning-300"></i>
												<?php } else {  ?>
												<i class=" icon-star-empty3 text-size-base text-warning-300"></i>
												<?php }	?>
												<?php if ( $evaluacion_curso = 5) { ?>
												<i class="icon-star-full2 text-size-base text-warning-300"></i>
												<?php } else {  ?>
												<i class=" icon-star-empty3 text-size-base text-warning-300"></i>
												<?php }	?>
												<span class="text-muted position-right"><?php echo "(".$totalRows_evaluacioncurso2.")"; ?></span>
											</li>
										</ul>
				                	</div>
								</div>

								<ul class="nav nav-lg nav-tabs nav-tabs-bottom nav-tabs-toolbar no-margin">
									<li class="active"><a href="#course-overview" data-toggle="tab"><i class="icon-menu7 position-left"></i>Lecciones</a></li>
									<li><a href="#course-schedule" data-toggle="tab"><i class="icon-calendar3 position-left"></i>Evaluación</a></li>
								</ul>

								<div class="tab-content">
									<div class="tab-pane fade in active" id="course-overview">
										<div class="panel-body">
											<div class="content-group-lg">
												<p><span class="text text-semibold">Bienvenido</span><br/>
												<p></p>La ciberseguridad es fundamental en la era digital actual, donde la mayoría de nuestras actividades, tanto personales como empresariales, dependen de sistemas y redes informáticas interconectadas.<br/>
												En este curso conocerás la importancia de la Ciberseguridad, así como; la práctica de proteger equipos, redes, aplicaciones software, sistemas críticos y datos de posibles amenazas dentro de Sahuayo, manteniendo la confianza y cumpliendo con la normatividad.</p>
												<p>&nbsp;</p>
												<span class="text text-semibold">Objetivo General:</span><br/>
												<p>El curso de Ciberseguridad, impartido 100% en línea, te aportará los conocimientos que necesitas en la protección activa de sistemas de información distribuidos, utilizando estrategias de protección y sistemas de detección y de protección de intrusos. Además, trabajarás los conceptos esenciales para hacer frente, reducir y detectar cualquier tipo de amenaza o ataque en los sistemas de información de Sahuayo.</p>
												<p>&nbsp;</p>
												<span class="text text-semibold">Objetivo Principal:</span><br/>
												<p>El objetivo principal de la ciberseguridad es garantizar la confidencialidad, integridad y disponibilidad de la información digital y los recursos tecnológicos. </p>
												<p>&nbsp;</p>
												<span class="text text-semibold">Instrucciones:</span><br/>
												<p>El curso se divide en cinco módulos que deberás ir completando de forma consecutiva.<br/>
												Para cualquier duda respecto del Curso, contacta a <span class="text text-primary">Esperanza Flores</span>  al correo  <a href="mailto:EGFlores@sahuayo.mx">EGFlores@sahuayo.mx</a>.</p>


										</div>

										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th style="width: 15%">Tipo</th>
														<th style="width: 30%">Nombre</th>
														<th style="width: 15%">Duración<nav></nav></th>
														<th style="width: 15%">Estatus</th>
														<th style="width: 25%">Acciones</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Lección 1</td>
														<td><span class="text text-danger">Seguridad de la Información</span></td>
														<td>4.1 minutos</td>
														<td>
															<span <?php if ($modulo1 == 0) { echo "class='label label-default'";} else {echo "class='label label-success'";} ?>>
															<?php if ($modulo1 == 0) { echo "Pendiente";} else {echo "Completado";} ?></span>
														</td>
														<td>
															<a class="btn btn-xs btn-primary" href="b_capa_cursos_detalle.php?IDmodulo=1">Acceder</a>
														</td>
													</tr>
													<tr>
														<td>Lección 2</td>
														<td><span class="text text-danger">Malware</span></td>
														<td>4.1 minutos</td>
														<td>
															<span <?php if ($modulo2 == 0) { echo "class='label label-default'";} else {echo "class='label label-success'";} ?>>
															<?php if ($modulo2 == 0) { echo "Pendiente";} else {echo "Completado";} ?></span>
														</td>
														<td>
														<?php if ($modulo1 == 1) { ?>											
															<a class="btn btn-xs btn-primary" href="b_capa_cursos_detalle.php?IDmodulo=2">Acceder</a>
														<?php } else { ?>
															<a class="btn btn-xs btn-default" disabled="disabled">Acceder</a>
														<?php } ?>	
														</td>
													</tr>
													<tr>
														<td>Lección 3</td>
														<td><span class="text text-danger">Ingenieria Social</span></td>
														<td>1.3 minutos</td>
														<td>
															<span <?php if ($modulo3 == 0) { echo "class='label label-default'";} else {echo "class='label label-success'";} ?>>
															<?php if ($modulo3 == 0) { echo "Pendiente";} else {echo "Completado";} ?></span>
														</td>
														<td>
														<?php if ($modulo2 == 1) { ?>											
														<a class="btn btn-xs btn-primary" href="b_capa_cursos_detalle.php?IDmodulo=3">Acceder</a>
														<?php } else { ?>
															<a class="btn btn-xs btn-default" disabled="disabled">Acceder</a>
														<?php } ?>		
														</td>
													</tr>
													<tr>
														<td>Lección 4</td>
														<td><span class="text text-danger">Cómo detectar un correo malicioso</span></td>
														<td>2.5 minutos</td>
														<td>
															<span <?php if ($modulo4 == 0) { echo "class='label label-default'";} else {echo "class='label label-success'";} ?>>
															<?php if ($modulo4 == 0) { echo "Pendiente";} else {echo "Completado";} ?></span>
														</td>
														<td>
														<?php if ($modulo3 == 1) { ?>											
															<a class="btn btn-xs btn-primary" href="b_capa_cursos_detalle.php?IDmodulo=4">Acceder</a>
														<?php } else { ?>
															<a class="btn btn-xs btn-default" disabled="disabled">Acceder</a>
														<?php } ?>		
														</td>
													</tr>
													<tr>
														<td>Lección 5</td>
														<td><span class="text text-danger">Uso de Dispositivos Móviles</span></td>
														<td>3.9 minutos</td>
														<td>
															<span <?php if ($modulo5 == 0) { echo "class='label label-default'";} else {echo "class='label label-success'";} ?>>
															<?php if ($modulo5 == 0) { echo "Pendiente";} else {echo "Completado";} ?></span>
														</td>
														<td>
														<?php if ($modulo4 == 1) { ?>											
															<a class="btn btn-xs btn-primary" href="b_capa_cursos_detalle.php?IDmodulo=5">Acceder</a>
														<?php } else { ?>
															<a class="btn btn-xs btn-default" disabled="disabled">Acceder</a>
														<?php } ?>		
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>


									<div class="tab-pane fade" id="course-schedule">
										<div class="panel-body">
											<div class="schedule"></div>
										</div>
									</div>
								</div>
							</div>
							<!-- /course overview -->


							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
                                    <h6 class="text-semibold">Evaluación Final</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">


									<span class="text text-semibold">Instrucciones:</span><br/>
									<p>Al terminar de ver  los módulos, deberás  realizar una evaluación, de la que se requiere una calificación <b>minima aprobatoria de 8.0</b> para cumplir con tu capacitación.<br/>
									La evaluación solo estará disponible al terminar de ver los cinco módulos.</p>


									<div class="table-responsive">
											<table class="table">
											<thead>
													<tr>
														<th style="width: 15%">Tipo</th>
														<th style="width: 30%">Nombre</th>
														<th style="width: 15%">Calificación<nav></nav></th>
														<th style="width: 15%">Estatus</th>
														<th style="width: 25%">Acciones</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Evaluación</td>
														<td><span class="text text-danger">Evaluación Final</span></td>
														<td><?php if ($row_capacitacion['modulo1'] != '') {echo $row_capacitacion['calificacion']."/10";} else { echo "-";} ?></td>
														<td>
															<span <?php if ($evaluacion == 0) { echo "class='label label-default'";} else {echo "class='label label-success'";} ?>>
															<?php if ($evaluacion == 0) { echo "Pendiente";} else {echo "Completado";} ?></span></td>
														<td>
														<?php if ($acceso_evaluacion == 1 AND $evaluacion == 0) { ?>											
														<a class="btn btn-xs btn-primary" href="b_capa_cursos_evaluacion.php">Acceder</a>
														<?php } else  if ( $evaluacion> 0) { ?>
														<a class="btn btn-xs btn-success" href="b_capa_cursos_evaluacion_respuestas.php">Ver Respuestas</a>
														<?php } else { ?>
														<a class="btn btn-xs btn-default">Pendiente</a>
														<?php } ?>		
														</td>
													</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- /about author -->

 
						</div>
					</div>
					<!-- /detached content -->


					<!-- /panel heading options -->

					<!-- Footer -->
					<div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <span class="text text-primary"><?php echo $row_variables['nombre_sistema']; ?></> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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