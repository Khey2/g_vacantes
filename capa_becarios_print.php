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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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
$fecha = date("dmY"); // la fecha actual

$mes_actual = date("m")-1;

if (isset($_GET['IDmes'])) {$el_mes = $_GET['IDmes'];} else {$el_mes = $mes_actual;}
if (isset($_GET['anio'])) {$anio = $_GET['anio'];}

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
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];

$IDempleado = $_GET["IDempleado"];
mysql_select_db($database_vacantes, $vacantes);
$query_becario = "SELECT capa_becarios.*,  capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, capa_becarios_evaluacion.IDevaluacion, capa_becarios_evaluacion.IDcalificacion, capa_becarios_evaluacion.anio, capa_becarios_evaluacion.IDmes, vac_meses.mes FROM capa_becarios LEFT JOIN capa_becarios_evaluacion ON capa_becarios.IDempleado = capa_becarios_evaluacion.IDempleado LEFT JOIN vac_meses ON capa_becarios_evaluacion.IDmes = vac_meses.IDmes LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$becario = mysql_query($query_becario, $vacantes) or die(mysql_error());
$row_becario = mysql_fetch_assoc($becario);
$totalRows_becario = mysql_num_rows($becario);
$IDsubarea = $row_becario['IDsubarea'];
$IDarea = $row_becario['IDarea'];
$IDmatriz_b = $row_becario['IDmatriz'];
$IDsucursal = $row_becario['IDsucursal'];
$IDtipo = $row_becario['IDtipo'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT * FROM capa_becarios_evaluacion WHERE IDempleado = $IDempleado AND IDmes = $el_mes AND anio = $anio";
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
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
	<link href="assets/prints.css" rel="stylesheet" type="text/css">	
	
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
    
    <script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>
	<!-- /Theme JS files -->
 </head>
	<script type="text/javascript">
		  window.onload = function() { window.print(); }
	</script>
<body>


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">	

			<!-- Content area -->
			  <div class="content">

	<div class="panel panel-flat">
	<div class="media panel-body no-margin">
		<div class="media-body">
		

								<ul class="media-list">
									<li class="media panel-body stack-media-on-mobile">
										<div>
											<a href="#">
												<?php if ($row_becario['Fotografia'] != '') { ?>
												<img src="<?php echo 'becariosfiles/'.$row_becario['ELempleado'].'/'.$row_becario['Fotografia']; ?>" alt="Fotografia" width="80" height="100"><br/>
												<?php } else { ?>
												<img src="files/foto.jpg" alt="Fotografia" width="80" height="100"><br/>
												<?php } ?>
											</a>
										</div>

										<div class="media-body">
											<h6 class="media-heading text-semibold">
												<a href="#"><?php echo $row_becario['emp_paterno']." ". $row_becario['emp_materno']." ". $row_becario['emp_nombre']; ?></a>
											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becario['tipo']; ?></li>
												<li><strong>Modalidad:</strong> <?php if ($row_becario['IDmodalidad'] == 1) {echo "Presencial";} else if ($row_becario['IDmodalidad'] == 2) {echo "Remoto ";} else {echo "Mixto";} ?><li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becario['fecha_alta'])); ?></li>
											</li>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Mes de Evaluación:</strong> <?php echo $elmes; ?></li>
												<li><strong>Año de Evaluación:</strong> <?php echo $anio; ?></li>
											</li>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Resultado:</strong> <?php if ($row_becario['IDcalificacion'] > 1) { for ($x = 0; $x < $row_becario['IDcalificacion']; $x++) { echo "<i class='icon-star-full2 text-success'></i>"; }} else { echo "<i class='icon-star-full2 text-success'></i>"; }?></li>
											</ul>												
										</div>
											
									</li>
								</ul>							
							
								<?php 
								$conteo = 1;
								mysql_select_db($database_vacantes, $vacantes);
								$query_preguntas = "SELECT capa_becarios_preguntas.IDpregunta, capa_becarios_preguntas.IDtipo_opciones, capa_becarios_preguntas.IDpreguntaNum, capa_becarios_tipo_preg.tipo_preg, capa_becarios_preguntas.pregunta, capa_becarios_preguntas.IDtipo FROM capa_becarios_preguntas INNER JOIN capa_becarios_tipo_preg ON  capa_becarios_preguntas.IDtipo_preg = capa_becarios_tipo_preg.IDtipo_preg WHERE capa_becarios_preguntas.IDtipo = $IDtipo ORDER BY capa_becarios_preguntas.IDtipo_opciones ASC";
								$preguntas = mysql_query($query_preguntas, $vacantes) or die(mysql_error());
								$row_preguntas = mysql_fetch_assoc($preguntas);
								$totalRows_preguntas = mysql_num_rows($preguntas);
								
								do { 
								$el_tipo_pregunta = $row_preguntas['IDtipo_opciones']; 
								$IDpregunta = $row_preguntas['IDpreguntaNum'];								
								$query_respuesta = "SELECT * FROM capa_becarios_respuestas WHERE IDpregunta = $IDpregunta AND IDempleado = $IDempleado AND IDmes = $el_mes AND anio = $anio";
								$respuesta = mysql_query($query_respuesta, $vacantes) or die(mysql_error());
								$row_respuesta = mysql_fetch_assoc($respuesta);
								$totalRows_respuesta = mysql_num_rows($respuesta);						
								?>


								<div class="form-group pt-15">
								        <span class="text-semibold"><?php echo $conteo." de ".$totalRows_preguntas; ?>. <?php echo $row_preguntas['tipo_preg']; ?>:
										<span class="text-semibold no-margin-top text-primary"><?php echo $row_preguntas['pregunta']; ?></span>

								<?php if ($el_tipo_pregunta == 1 ) { ?>											
								  
										<div>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),5))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_3" value="5" class="control-success"/>
												Siempre.
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_2" value="3" class="control-warning"/>
												Algunas veces.
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_1" value="2" class="control-danger"/>
												Nunca.
										</div>


								<?php } else if ($el_tipo_pregunta == 2 ) {?>							
								
										<div>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),5))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_5" value="5" class="control-success"/>
												Totalmente de acuerdo.
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_4" value="4" class="control-success" />
												De acuerdo.
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_3" value="3" class="control-success" />
												Medianamente de acuerdo.
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_2" value="2" class="control-danger"/>
												En desacuerdo.
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> type="radio" name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" id="respuesta_1" value="1" class="control-danger"/>
												Totalmente en desacuerdo.
										</div>
								
								
								
								<?php } else if ($el_tipo_pregunta == 3 ) {?>		

							
										<div class="form-group pt-15">
												  <textarea name="<?php echo $row_preguntas['IDpreguntaNum']; ?>" rows="3" class="form-control" id="<?php echo $row_preguntas['IDpreguntaNum']; ?>" placeholder="Observaciones."><?php echo ($row_respuesta['IDrespuesta']); ?></textarea>
										</div>
								<?php } ?>

									</div>


							<?php
							$conteo++;

							} while ($row_preguntas = mysql_fetch_assoc($preguntas)); ?>
									
			</div>
		</div>
	</div>
                        


					<!-- /Contenido -->
                </div>
				<!-- /content area -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>