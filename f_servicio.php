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
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDempleado'];
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDpuesto = $row_usuario['IDpuesto'];
$IDarea = $row_usuario['IDarea'];
$IDsucursal = $row_usuario['IDsucursal'];
$_SESSION['IDmatriz'] = $IDmatriz;

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

//Si tiene resultados
mysql_select_db($database_vacantes, $vacantes);
$query_clima = "SELECT * FROM sed_servicio WHERE IDempleado = '$el_usuario' AND anio = '$anio' AND IDpregunta = 1";
$clima = mysql_query($query_clima, $vacantes) or die(mysql_error());
$row_clima = mysql_fetch_assoc($clima);
$totalRows_clima = mysql_num_rows($clima);
$el_user_matriz = $row_clima['IDmatriz'];

// saber si se muestran resultados
$resultados = $row_matriz['clima'];


//Ultima Respuesta
mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_ultima = "SELECT DISTINCT Max(sed_servicio.IDpregunta) AS IDpregunta FROM sed_servicio WHERE IDempleado = '$el_usuario' AND anio = '$anio'";
$respuesta_ultima = mysql_query($query_respuesta_ultima, $vacantes) or die(mysql_error());
$row_respuesta_ultima = mysql_fetch_assoc($respuesta_ultima);
$totalRows_respuesta_ultima = mysql_num_rows($respuesta_ultima);

// Para Ubicarse
$la_pregunta_ultima = $row_respuesta_ultima['IDpregunta'];
if(isset($_GET['IDpregunta'])) {$la_pregunta_actual = $_GET['IDpregunta'];} else {$la_pregunta_actual = 1;}
$la_pregunta_siguiente = $la_pregunta_actual + 1;
$la_pregunta_anterior = $la_pregunta_actual - 1;

//Preguntas
$query_pregunta = "SELECT * FROM sed_servicio_preguntas WHERE IDpregunta = '$la_pregunta_actual'";
mysql_query("SET NAMES 'utf8'");
$pregunta = mysql_query($query_pregunta, $vacantes) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$pregunta_texto = $row_pregunta['pregunta_texto'];
$pregunta_area = $row_pregunta['pregunta_area'];
$pregunta_tema = $row_pregunta['pregunta_tema'];
$pregunta_responsable = $row_pregunta['pregunta_responsable'];

$query_maxima = "SELECT MAX(IDpregunta) AS max_preg FROM sed_servicio_preguntas";
$maxima = mysql_query($query_maxima, $vacantes) or die(mysql_error());
$row_maxima = mysql_fetch_assoc($maxima);
$max_preg = $row_maxima['max_preg'];

//Respuestas
mysql_select_db($database_vacantes, $vacantes);
$query_respuesta = "SELECT * FROM sed_servicio WHERE IDpregunta = '$la_pregunta_actual' AND IDempleado = '$el_usuario' AND anio = '$anio'";
$respuesta = mysql_query($query_respuesta, $vacantes) or die(mysql_error());
$row_respuesta = mysql_fetch_assoc($respuesta);
$totalRows_respuesta = mysql_num_rows($respuesta);
$la_respuesta = $row_respuesta['IDrespuesta'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$actualiza_respuesta = $_POST['IDrespuesta'];	
$actualiza_observaciones = $_POST['observaciones'];	

  $updateSQL = "UPDATE sed_servicio SET IDrespuesta = '$actualiza_respuesta', observaciones = '$actualiza_observaciones' WHERE IDpregunta = '$la_pregunta_actual' AND IDempleado = '$el_usuario' AND anio = '$anio'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: f_servicio.php?IDpregunta=$la_pregunta_siguiente&activar=1");
}

//insertar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$emp_paterno = $row_usuario['emp_paterno'];
$emp_materno = $row_usuario['emp_materno'];
$emp_nombre = $row_usuario['emp_nombre'];
$denominacion = $row_usuario['denominacion'];
$observaciones = $_POST['observaciones'];
$IDpuesto = $row_usuario['IDpuesto'];

$insertSQL = sprintf("INSERT INTO sed_servicio (IDempleado, anio, fecha, IDpregunta, IDmatriz, IDarea, IDrespuesta, emp_paterno, emp_materno, emp_nombre, denominacion, observaciones, IDpuesto) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,  %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['anio'], "text"),
                       GetSQLValueString($_POST['fecha'], "text"),
                       GetSQLValueString($_POST['IDpregunta'], "int"),
                       GetSQLValueString($IDmatriz, "int"),
                       GetSQLValueString($IDarea, "int"),
                       GetSQLValueString($_POST['IDrespuesta'], "text"),
                       GetSQLValueString($emp_paterno, "text"),
                       GetSQLValueString($emp_materno, "text"),
                       GetSQLValueString($emp_nombre, "text"),
                       GetSQLValueString($denominacion, "text"),
                       GetSQLValueString($observaciones, "text"),
                       GetSQLValueString($IDpuesto, "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  $captura = mysql_insert_id();
  header("Location: f_servicio.php?IDpregunta=$la_pregunta_siguiente&activar=1");
}

//cierre de la encuesta
	if ($la_pregunta_ultima == $max_preg) {
	$max_preg_extra = $max_preg + 1;
	$updateSQL = "INSERT INTO sed_servicio (IDempleado, anio, fecha, IDpregunta, IDrespuesta, IDmatriz, IDarea) VALUES 
	('$el_usuario', '$anio', '$fecha', '$max_preg_extra', 9, '$IDmatriz', '$IDarea')"; 
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  header("Location: f_servicio.php");
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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/gallery.js"></script>

	<script>
	<?php if ($_GET['activar'] == 1) { ?> 
	 $(document).ready(function(){ $("#ModalPreguntas").modal('show'); }); 
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

				
				<div class="content">
				
				
				    <div class="panel panel-flat">

					<div class="row">
						<div class="col-sm-6 col-md-2">
							<div class="panel-body">

								<?php if($la_pregunta_ultima > $max_preg) { // Si termino la encuesta ?>
                 
									<a class="btn btn-success btn-float btn-float-lg"><i class="icon-checkmark4"></i> <span>Encuesta Terminada</span></a>
                 
								<?php } elseif($totalRows_clima > 0) { // Si no termino la encuesta ?>

									<a class="btn btn-warning btn-float btn-float-lg"  href="f_servicio.php?IDpregunta=<?php echo $la_pregunta_ultima + 1;?>&activar=1"><i class="icon-forward2"></i> <span>Continuar con la Encuesta</span> </a>
							
								<?php } else {  // Si no ha contestado nada ?>

									<a class="btn btn-info btn-float btn-float-lg" href="f_servicio.php?IDpregunta=<?php echo $la_pregunta_actual;?>&activar=1"><i class="icon-play3"></i> <span>Iniciar la Encuesta</span></a>
									
								<?php } ?>

							</div>
						</div>
						<div class="col-sm-6 col-md-10">
							<div class="panel-body">

                 <p>A continuación te presentamos una serie de preguntas relacionadas con el Servicio que has recibido por parte de las <strong>áreas Corporativas de Recursos Humanos</strong>.</p>
                 <p>Para cada pregunta, califica de acuerdo a la <strong>escala presentada</strong>. </p>
                 <p>En caso de que lo consideres necesario, para cada pregunta puedes enviar tus <strong>comentarios y recomendaciones de mejora</strong>. </p>
                 <p>Al final podrás también enviar tus recomendaciones y sugerencias a la <strong>Dirección de Recursos Humanos</strong>. </p>
                 <p>Tus respuestas serán tratadas de forma <strong>CONFIDENCIAL</strong> Y <strong>ANÓNIMA</strong> y no serán utilizadas para ningún propósito distinto al de ayudarnos a mejorar y formar parte de la nueva cultura para Evaluar el Desempeño.</p>
                 <p>Recuerda &nbsp;el clima organizacional lo  hacemos todos. <spam class="text-success text-bold">¡Sahuayo eres tú!</spam></p>
								
							</div>
						</div>
					</div>
				</div>
				
                <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Evaluación de Servicio</h5>
						</div>

					<div class="row">
						<div class="col-sm-6 col-md-6">
							<div class="panel-body">

                 <p><strong>Bienvenido</strong>, tu opinión es muy importante para  nosotros.!!</p>
			     <p>La evaluación de Servicio de RH corporativo es muy importante porque:</p>
				 <ul>
				 <li>Establece un canal de comunicación entre RH Sucursales y Corporativo.</li>
				 <li>Permite identificar áreas de oportunidad en el servicio otorgado a los Jefes de RH Sucursal.</li>
				 <li>Asegura que exista una correcta alineación de estrategias a nivel nacional.</li>
				 <li>Facilita la retroalimentación efectiva para el Personal Corporativo.</li>
				 <li>Permite identificar recomendaciones y solicitudes de mejora de los Jefes de RH Sucursal.</li>
				 </ul>
				 </p>

							</div>
						</div>
						<div class="col-sm-6 col-md-6">
								<div class="thumb">
									<img src="assets/img/orga.png" alt="">
									<div class="caption-overflow">
										<span>
											<a href="assets/img/orga.png" data-popup="lightbox" class="btn border-white text-white btn-flat btn-icon btn-rounded">
                                            <i class="icon-plus3"></i></a>
										</span>
								</div>
								
							</div>
						</div>
					</div>
				</div>


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

                    <div id="ModalPreguntas" class="modal fade" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header bg-primary-700">
										
										<div class="col-md-12">
								<h6>Encuesta de Servicio RH Sahuayo <?php echo $anio; ?> &nbsp;&nbsp;| &nbsp;&nbsp; Pregunta <?php echo $row_pregunta['IDpregunta']. " de ".$max_preg; ?></h6>
                                    	</div>
                          </div>
						  
                          <div class="modal-body">
						 <form method="post" id="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">


								<fieldset class="content-group">
                                
                                	<div class="form-group">
										<div class="col-lg-12">
                                        <h5><span class="display-block text-semibold"><?php echo $pregunta_texto; ?></span></h5>
                                        </div>

										<p>&nbsp;</p>                                        

                             <?php if ($la_pregunta_actual != $max_preg){ ?>

										<div class="col-lg-4">
                                        <p class="text-muted"><strong>Área: </strong><?php echo $pregunta_area; ?></p>
                                        </div>
										<div class="col-lg-4">
										<p class="text-muted"><strong>Tema: </strong><?php echo $pregunta_tema; ?></p>
                                        </div>
										<div class="col-lg-4">
										<p class="text-muted"><strong>Responsable: </strong><?php echo $pregunta_responsable; ?></p>
                                        </div>
									</div>
                                    
                                        <hr>
										<p>&nbsp;</p>

								  <div class="form-group">
                                        <div class="col-lg-2"><p class="text-semibold text-danger text-right">Totalmente <br/> en desacuerdo</p></div>
                                        <div class="col-lg-8">
                                            <div class="rangeslider"> 
                                            
                                            <?php if ($la_respuesta != ""){ ?>
                                              <input type="range" min="50" max="100" value="<?php echo $row_respuesta['IDrespuesta']; ?>" step="10" class="form-control" id="IDrespuesta" name="IDrespuesta"> 
											<?php } else { ?>
											 <input type="range" min="50" max="100" value="90" step="10" class="form-control" id="IDrespuesta" name="IDrespuesta"> 
											<?php } ?>
                                            <p>Calificación: <span id="demo"></span>%</p> 
                                            </div>
                                        </div>
                                        <div class="col-lg-2"><p class="text-semibold text-success text-left">Totalmente <br/> de acuerdo</p></div>
									</div>
                                    
                             <?php } ?>
                                    

								  <div class="form-group">
										<div class="col-lg-12">
                                            <?php if ($la_respuesta != ""){ ?>
                                          <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Comenta aqui tus observcaciones y sugerencias de mejora."><?php echo $row_respuesta['observaciones']; ?></textarea>
                                          	<?php } else { ?>
                                          <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Comenta aqui tus observcaciones y sugerencias de mejora."></textarea>
											<?php } ?>

										</div>
									</div>

                                        <hr>

					        <input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>" />
					        <input type="hidden" name="IDpregunta" value="<?php echo $la_pregunta_actual; ?>" />
					        <input type="hidden" name="anio" value="<?php echo $anio; ?>" />
					        <input type="hidden" name="fecha" value="<?php echo $fecha; ?>" />

      							<div class="modal-footer">
                                
								<?php  if ($la_pregunta_actual > 1) { ?> 
                                <a class="btn bg-primary-300" href="f_servicio.php?IDpregunta=1&activar=1"><<- Primera</a>
                                <?php } ?>


								<?php  if ($la_pregunta_actual > 1) { ?> 
                                <a class="btn bg-primary-700" href="f_servicio.php?IDpregunta=<?php echo $la_pregunta_anterior; ?>&activar=1"><- Anterior</a>
                                <?php } ?>

                                <?php if ($la_respuesta == "") { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_insert" value="<?php if ($la_pregunta_actual != $max_preg) { echo "Siguiente ->"; } else {echo "Terminar"; } ?>" />
							    <input type="hidden" name="MM_insert" value="form1" />
					            
								<?php } else { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_update" value="<?php if ($la_pregunta_actual != $max_preg) { echo "Siguiente ->"; } else {echo "Terminar"; } ?>" />
							    <input type="hidden" name="MM_update" value="form1" />
					            <?php }  ?>

								<?php  if ($la_pregunta_ultima > 0 && $la_pregunta_actual != $max_preg) { ?> 
                                <a class="btn bg-primary-300" href="f_servicio.php?IDpregunta=<?php echo $la_pregunta_ultima + 1; ?>&activar=1">Última ->></a>
                                <?php } ?>

                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                              </div>
								</fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
<script> 
var rangeslider = document.getElementById("IDrespuesta"); 
var output = document.getElementById("demo"); 
output.innerHTML = rangeslider.value; 
  
rangeslider.oninput = function() { 
  output.innerHTML = this.value; 
} 
</script>
 </body>
</html>