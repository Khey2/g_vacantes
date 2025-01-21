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
$IDperiodo = $row_variables['IDperiodoN35'];
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
// la matriz y el usuario
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDempleado'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
// matriz en nombre
$mi_matriz = $row_matriz['matriz'];
$nom35_g1 = $row_matriz['nom35_g1'];
$nom35_g2 = $row_matriz['nom35_g2'];


if (!isset($_GET['IDexamen'])) { header('Location:  f_encuestas_aplicar.php?info=5'); } else
if (!isset($_GET['IDpregunta'])) { header('Location:  f_encuestas_aplicar.php?info=5'); } else
if (isset($_GET['IDrespuesta'])) {$la_respuesta = $_GET['IDrespuesta']; }
$la_pregunta = $_GET['IDpregunta'];
$el_examen =  $_GET['IDexamen'];
$la_pregunta_sig = $la_pregunta + 1;
$la_pregunta_ant = $la_pregunta - 1;

mysql_select_db($database_vacantes, $vacantes);
$query_intentos = "SELECT * FROM nom35_resultados WHERE IDempleado = '$el_usuario' AND IDexamen = '$el_examen' AND IDperiodo = $IDperiodo";
$intentos = mysql_query($query_intentos, $vacantes) or die(mysql_error());
$row_intentos = mysql_fetch_assoc($intentos);
$totalRows_intentos = mysql_num_rows($intentos);

if ($totalRows_intentos > 0) { header('Location:  f_panel.php?info=6'); }

mysql_select_db($database_vacantes, $vacantes);
$query_examen = "SELECT * FROM nom35_examenes WHERE IDexamen = '$el_examen'";
$examen = mysql_query($query_examen, $vacantes) or die(mysql_error());
$row_examen = mysql_fetch_assoc($examen);
$totalRows_examen = mysql_num_rows($examen);
// total de preguntas del examen
$preguntas_totales = $row_examen['examen_total_preguntas'];

//respuestas
mysql_select_db($database_vacantes, $vacantes);
$query_respuesta = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta' AND IDempleado = '$el_usuario'  AND IDperiodo = $IDperiodo";
$respuesta = mysql_query($query_respuesta, $vacantes) or die(mysql_error());
$row_respuesta = mysql_fetch_assoc($respuesta);
$totalRows_respuesta = mysql_num_rows($respuesta);
$la_respuesta = $row_respuesta['respuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_previa = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta_ant' AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta_previa = mysql_query($query_respuesta_previa, $vacantes) or die(mysql_error());
$row_respuesta_previa = mysql_fetch_assoc($respuesta_previa);
$totalRows_respuesta_previa = mysql_num_rows($respuesta_previa);
// respuesta previa
$la_respuesta_previa = $row_respuesta_previa['IDrespuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_siguiente = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta_sig' AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta_siguiente = mysql_query($query_respuesta_siguiente, $vacantes) or die(mysql_error());
$row_respuesta_siguiente = mysql_fetch_assoc($respuesta_siguiente);
$totalRows_respuesta_siguiente = mysql_num_rows($respuesta_siguiente);
// respuesta siguiente
$la_respuesta_siguiente = $row_respuesta_siguiente['IDrespuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_primera = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = 1 AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta_primera = mysql_query($query_respuesta_primera, $vacantes) or die(mysql_error());
$row_respuesta_primera = mysql_fetch_assoc($respuesta_primera);
$totalRows_respuesta_primera = mysql_num_rows($respuesta_primera);
// respuesta primera
$la_respuesta_primera = $row_respuesta_primera['IDrespuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_ultima = "SELECT DISTINCT Max(nom35_respuestas.IDpregunta) AS IDpregunta, nom35_respuestas.IDrespuesta FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta_ultima = mysql_query($query_respuesta_ultima, $vacantes) or die(mysql_error());
$row_respuesta_ultima = mysql_fetch_assoc($respuesta_ultima);
$totalRows_respuesta_ultima = mysql_num_rows($respuesta_ultima);
// respuesta ultima
$la_respuesta_ultima = $row_respuesta_ultima['IDrespuesta'];
$la_pregunta_ultima = $row_respuesta_ultima['IDpregunta']; 

mysql_select_db($database_vacantes, $vacantes);
$query_pregunta = "SELECT * FROM nom35_preguntas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta'";
mysql_query("SET NAMES 'utf8'");
$pregunta = mysql_query($query_pregunta, $vacantes) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$totalRows_pregunta = mysql_num_rows($pregunta);
// texto de la pregunta
$IDcategoria = $row_pregunta['IDcategoria'];
$IDdominio = $row_pregunta['IDdominio'];
$IDdimension = $row_pregunta['IDdimension'];
$texto_pregunta = $row_pregunta['pregunta_texto'];
$pregunta_llave = $row_pregunta['pregunta_llave'];
$pregunta_tipo = $row_pregunta['pregunta_tipo'];

$reenvio = 'f_encuestas.php';
if ( $totalRows_respuesta_siguiente == NULL ){$reenvio = 'f_encuestas_calc.php?IDexamen='.$el_examen;} 
else {$reenvio = 'f_encuestas_calc.php?IDexamen='.$el_examen.'&IDpregunta='.$la_pregunta_sig;}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$IDrespuesta = $_GET['IDrespuesta'];
	
  $updateSQL = sprintf("UPDATE nom35_respuestas SET respuesta =%s WHERE IDrespuesta = '$IDrespuesta'",
                       GetSQLValueString($_POST['respuesta'], "int"),
                       GetSQLValueString($_GET['IDrespuesta'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "$reenvio";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO nom35_respuestas (IDempleado, IDexamen, IDpregunta, respuesta, IDcategoria, IDdominio, IDdimension, pregunta_tipo, IDperiodo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDexamen'], "int"),
                       GetSQLValueString($_POST['IDpregunta'], "int"),
                       GetSQLValueString($_POST['respuesta'], "int"),
                       GetSQLValueString($_POST['IDcategoria'], "int"),
                       GetSQLValueString($_POST['IDdominio'], "int"),
                       GetSQLValueString($_POST['IDdimension'], "int"),
                       GetSQLValueString($_POST['pregunta_tipo'], "int"),
                       GetSQLValueString($IDperiodo, "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "$reenvio";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


//echo "Examen: " . $el_examen . "<br>";
//echo "Pregunta: " . $la_pregunta . "<br>";
//echo "Usuario: " . $el_usuario . "<br>";
//echo "Respuesta: " . $la_respuesta . "<br>";
//echo "Respuesta Prev: " . $la_respuesta_previa . "<br>";
//echo "Respuesta: " . $la_respuesta_siguiente . "<br>";



mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_previa1 = "SELECT Max(nom35_respuestas.IDpregunta) AS IDpregunta FROM nom35_respuestas WHERE IDexamen = 1 AND nom35_respuestas.IDempleado = $el_usuario  AND IDperiodo = $IDperiodo";
$respuesta_previa1 = mysql_query($query_respuesta_previa1, $vacantes) or die(mysql_error());
$row_respuesta_previa1 = mysql_fetch_assoc($respuesta_previa1);
$totalRows_respuesta_previa1 = mysql_num_rows($respuesta_previa1);
$ultima1 = $row_respuesta_previa1['IDpregunta'];


mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_previa2 = "SELECT Max(nom35_respuestas.IDpregunta) AS IDpregunta FROM nom35_respuestas WHERE IDexamen = 2 AND nom35_respuestas.IDempleado = $el_usuario  AND IDperiodo = $IDperiodo";
$respuesta_previa2 = mysql_query($query_respuesta_previa2, $vacantes) or die(mysql_error());
$row_respuesta_previa2 = mysql_fetch_assoc($respuesta_previa2);
$totalRows_respuesta_previa2 = mysql_num_rows($respuesta_previa2);
$ultima2 = $row_respuesta_previa2['IDpregunta'];


mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_previa3 = "SELECT Max(nom35_respuestas.IDpregunta) AS IDpregunta FROM nom35_respuestas WHERE IDexamen = 3 AND nom35_respuestas.IDempleado = $el_usuario  AND IDperiodo = $IDperiodo";
$respuesta_previa3 = mysql_query($query_respuesta_previa3, $vacantes) or die(mysql_error());
$row_respuesta_previa3 = mysql_fetch_assoc($respuesta_previa3);
$totalRows_respuesta_previa3 = mysql_num_rows($respuesta_previa3);
$ultima3 = $row_respuesta_previa3['IDpregunta'];



mysql_select_db($database_vacantes, $vacantes);
$query_intentos1 = "SELECT * FROM nom35_resultados WHERE IDempleado = '$el_usuario' AND IDexamen = 1 AND IDperiodo = $IDperiodo";
$intentos1 = mysql_query($query_intentos1, $vacantes) or die(mysql_error());
$row_intentos1 = mysql_fetch_assoc($intentos1);
$totalRows_intentos1 = mysql_num_rows($intentos1);

mysql_select_db($database_vacantes, $vacantes);
$query_intentos2 = "SELECT * FROM nom35_resultados WHERE IDempleado = '$el_usuario' AND IDexamen = 2 AND IDperiodo = $IDperiodo";
$intentos2 = mysql_query($query_intentos2, $vacantes) or die(mysql_error());
$row_intentos2 = mysql_fetch_assoc($intentos2);
$totalRows_intentos2 = mysql_num_rows($intentos2);

mysql_select_db($database_vacantes, $vacantes);
$query_intentos3 = "SELECT * FROM nom35_resultados WHERE IDempleado = '$el_usuario' AND IDexamen = 3 AND IDperiodo = $IDperiodo";
$intentos3 = mysql_query($query_intentos3, $vacantes) or die(mysql_error());
$row_intentos3 = mysql_fetch_assoc($intentos3);
$totalRows_intentos3 = mysql_num_rows($intentos3);


if ($totalRows_intentos1 > 0) { $terminado1 = 1; } else { $terminado1 = 0; }
if ($totalRows_intentos2 > 0) { $terminado2 = 1; } else { $terminado2 = 0; }
if ($totalRows_intentos3 > 0) { $terminado3 = 1; } else { $terminado3 = 0; }


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
<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>

<script src="assets/js/app.js"></script>
<script src="global_assets/js/demo_pages/form_input_groups.js"></script>

    <script>
	$(document).ready(function(){ $("#ModalPreguntas").modal('show'); });
	</script>

</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>


	<!-- Page container -->
	<div class="page-container">

				<div class="content">
                

                    <div id="ModalPreguntas" class="modal fade" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                        
                          <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
							<h6 class="modal-title">Identificación y Análisis de Factores de Riesgo Psicosocial</h6>
                          </div>
                          
                          <div class="modal-body">
						 <form method="post" id="form1" action="<?php echo $editFormAction; ?>">
									
								<?php  if ($pregunta_tipo == 3) { // llave cierra ?>    
                                
                                    <div class="form-group pt-15">
                                        
                                       <h5><span class="text-muted">Pregunta <?php echo $la_pregunta; ?> de <?php echo $preguntas_totales; ?>:</span> <label class="text-semibold"><?php echo $texto_pregunta; ?></label></h5>
                                        
                                        <div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),6))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_1" value="6" class="styled"  required="required" />
												Si
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),5))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_2" value="5" class="styled"/>
												No
											</label>
										</div>
									</div>
                                        
								<?php } elseif ($pregunta_tipo == 1) {  ?> 
                                
                                    <div class="form-group pt-15">
								<h5><span class="text-muted">Pregunta <?php echo $la_pregunta; ?> de <?php echo $preguntas_totales; ?>:</span> <label class="text-semibold"><?php echo $texto_pregunta; ?>.</label></h5>
										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_1" value="4" class="styled"  required="required" />
												Siempre.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_2" value="3" class="styled"/>
												Casi Siempre.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_3" value="2" class="styled"/>
												Algunas Veces.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_4" value="1" class="styled"/>
												Casi Nunca.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),0))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_5" value="0" class="styled"/>
												Nunca.
											</label>
										</div>
									</div>


								<?php } else {  ?>    
                                    
                                        <div class="form-group pt-15">
										<h5><span class="text-muted">Pregunta <?php echo $la_pregunta; ?> de <?php echo $preguntas_totales; ?>:</span> <label class="text-semibold"><?php echo $texto_pregunta; ?>.</label></h5>
										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),0))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_1" value="0" class="styled"  required="required" />
												Siempre.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_2" value="1" class="styled"/>
												Casi Siempre.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_3" value="2" class="styled"/>
												Algunas Veces.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_4" value="3" class="styled"/>
												Casi Nunca.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['respuesta'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> type="radio" name="respuesta" id="respuesta_5" value="4" class="styled"/>
												Nunca.
											</label>
										</div>
									</div>

								<?php } ?>    

					        <input type="hidden" name="IDexamen" value="<?php echo $el_examen; ?>" />
					        <input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>" />
					        <input type="hidden" name="IDcategoria" value="<?php echo $IDcategoria; ?>" />
					        <input type="hidden" name="IDdominio" value="<?php echo $IDdominio; ?>" />
					        <input type="hidden" name="pregunta_tipo" value="<?php echo $pregunta_tipo; ?>" />
					        <input type="hidden" name="IDdimension" value="<?php echo $IDdimension; ?>" />
					        <input type="hidden" name="IDpregunta" value="<?php echo $la_pregunta; ?>" />

      							<div class="modal-footer">
                                
                                
								<?php  if ($la_pregunta > 1) { ?> 
                                <a class="btn bg-primary-300" href="f_encuestas.php?IDexamen=<?php echo $el_examen; 
								?>&IDpregunta=1&IDrespuesta=<?php echo $la_respuesta_primera; ?>"><<- Primera</a>
                                <?php } ?>


								<?php  if ($la_pregunta > 1) { ?> 
                                <a class="btn bg-primary-700" href="f_encuestas.php?IDexamen=<?php echo $el_examen; 
								?>&IDpregunta=<?php echo $la_pregunta_ant; ?>&IDrespuesta=<?php echo $la_respuesta_previa; ?>"><- Anterior</a>
                                <?php } ?>
                                
                                <?php if ($la_pregunta == $preguntas_totales) { ?>

                                <?php if ($la_respuesta == "") { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_insert" value="Terminar" />
							    <input type="hidden" name="MM_insert" value="form1" />
					            
								<?php } else { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_update" value="Terminar" />
							    <input type="hidden" name="MM_update" value="form1" />
					            <?php }  ?>

								<?php } else { ?>

                                <?php if ($la_respuesta == "") { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_insert" value="Siguiente ->" />
							    <input type="hidden" name="MM_insert" value="form1" />
					            
								<?php } else { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_update" value="Siguiente ->" />
							    <input type="hidden" name="MM_update" value="form1" />
					            <?php }  ?>

					            <?php }  ?>
                                
                                
                                <?php if ($la_pregunta < $preguntas_totales && $la_pregunta > 1) { ?>
                                <a class="btn bg-primary-300" href="f_encuestas.php?IDexamen=<?php echo $el_examen; 

								?>&IDpregunta=<?php echo $la_pregunta_ultima; ?>&IDrespuesta=<?php echo $la_respuesta_ultima; ?>">Última ->></a>
					            <?php }  ?>

                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                              </div>
                              
                            </form>
                          </div>
                        </div>
                      </div>
                 </div>
            <!-- Contenido -->
            

			<?php if($nom35_g1 == 1) {?>			
<?php if($terminado1 == 0) {?>

			<!-- Contenido Guia 1 -->
			<div class="panel panel-flat">

			<div class="row">
				<div class="col-sm-6 col-md-2">
					<div class="panel-body">

<?php if($ultima1 == 0) {?>
				<a class="btn btn-info btn-float btn-float-lg" href="f_encuestas.php?IDexamen=1&IDpregunta=1"><i class="icon-play3"></i> <span>Iniciar la Encuesta</span></a>
<?php } else { ?>
				<a class="btn btn-warning btn-float btn-float-lg" href="f_encuestas.php?IDexamen=1&IDpregunta=<?php echo $ultima1;?>"><i class="icon-forward2"></i> <span>Continuar con la Encuesta</span></a>
<?php } ?>


					</div>
				</div>
				<div class="col-sm-6 col-md-10">
					<div class="panel-body">

					<p>A continuación se muestran una serie de preguntas relacionadas con la <b> Guia 1 de la NOM35: Cuestionario para identificar a los trabajadores que fueron sujetos a acontecimientos traumáticos severos.</b><br/>
					 Da clic en el suguiente botón para respondar la encuesta.</p>
						
					</div>
				</div>
			</div>
		</div>

<?php } else {?>

			<div class="panel panel-flat">

			<div class="row">
				<div class="col-sm-6 col-md-2">
					<div class="panel-body">

				<a class="btn btn-success btn-float btn-float-lg"><i class=" icon-checkmark3"></i> <span>Encuesta Terminada</span></a>


					</div>
				</div>
				<div class="col-sm-6 col-md-10">
					<div class="panel-body">			
						Gracias por tu participación, tus respuestas se han guardado correctamente.
					</div>
				</div>
			</div>
		</div>

<?php } ?>
<?php } ?>



<?php if($nom35_g2 == 1) {?>			
<?php if($terminado2 == 0) {?>

					<!-- Contenido Guia 2 -->
					<div class="panel panel-flat">

<div class="row">
	<div class="col-sm-6 col-md-2">
		<div class="panel-body">

		<?php if($ultima2 == 0) {?>
  <a class="btn btn-info btn-float btn-float-lg" href="f_encuestas.php?IDexamen=2&IDpregunta=1"><i class="icon-play3"></i> <span>Iniciar la Encuesta</span></a>
		<?php } else { ?>
  <a class="btn btn-warning btn-float btn-float-lg" href="f_encuestas.php?IDexamen=2&IDpregunta=<?php echo $ultima2;?>"><i class="icon-forward2"></i> <span>Continuar con la Encuesta</span></a>
		<?php } ?>


		</div>
	</div>
	<div class="col-sm-6 col-md-10">
		<div class="panel-body">

		<p>A continuación se muestran una serie de preguntas relacionadas con la <b> Guia 2 de la NOM35 :Identificación y análisis de factores de riesgo psicosocial.</b><br/>
		Da clic en el suguiente botón para respondar la encuesta.</p>
			
		</div>
	</div>
</div>
</div>

<?php } else {?>

	<div class="panel panel-flat">

<div class="row">
	<div class="col-sm-6 col-md-2">
		<div class="panel-body">

	<a class="btn btn-success btn-float btn-float-lg"><i class=" icon-checkmark3"></i> <span>Encuesta Terminada</span></a>


		</div>
	</div>
	<div class="col-sm-6 col-md-10">
		<div class="panel-body">			
			Gracias por tu participación, tus respuestas se han guardado correctamente.
		</div>
	</div>
</div>
</div>

<?php } ?>
<?php } ?>

<?php if($nom35_g2 == 2) {?>			
<?php if($terminado3 == 0) {?>
<!-- Contenido Guia 3 -->
<div class="panel panel-flat">

<div class="row">
	<div class="col-sm-6 col-md-2">
		<div class="panel-body">

		<?php if($ultima3 == 0) {?>
  <a class="btn btn-info btn-float btn-float-lg" href="f_encuestas.php?IDexamen=3&IDpregunta=1"><i class="icon-play3"></i> <span>Iniciar Encuesta</span></a>
		<?php } else { ?>
  <a class="btn btn-warning btn-float btn-float-lg" href="f_encuestas.php?IDexamen=3&IDpregunta=<?php echo $ultima3;?>"><i class="icon-forward2"></i> <span>Continuar Encuesta</span></a>
		<?php } ?>


		</div>
	</div>
	<div class="col-sm-6 col-md-10">
		<div class="panel-body">

		<p>A continuación se muestran una serie de preguntas relacionadas con la <b> Guia 3 de la NOM35: Identificación y análisis de los factores de riesgo psicosocial y evaluación del entorno organizacional en los centros de trabajo.</b><br/>
		Da clic en el botón para respondar la encuesta.</p>
			
		</div>
	</div>
</div>
</div>

<?php } else {?>

	<div class="panel panel-flat">

<div class="row">
	<div class="col-sm-6 col-md-2">
		<div class="panel-body">

	<a class="btn btn-success btn-float btn-float-lg"><i class=" icon-checkmark3"></i> <span>Encuesta Terminada</span></a>


		</div>
	</div>
	<div class="col-sm-6 col-md-10">
		<div class="panel-body">			
			Gracias por tu participación, tus respuestas se han guardado correctamente.
		</div>
	</div>
</div>
</div>

<?php } ?>
<?php } ?>



  <div class="panel panel-flat">
		<div class="panel-body">
      <div class="col-sm-6">
          <span>
					<img src="assets/img/N35.jpg" class="img-responsive" alt="">
					</span>
      </div>
      <div class="col-sm-6">
        <h3>Política de Prevención de Riesgos Psicosociales</h3>
                            <p><b>Impulsora Sahuayo S.A. de C.V. </b>promueve la prevención de los factores de riesgo psicosocial; la prevención de la violencia laboral, y la promoción de un entorno organizacional favorable, la diversidad e inclusión, facilitando a los colaboradores acciones de sensibilización, programas de comunicación, capacitación y espacios de participación y consulta, quedando estrictamente prohibidos los actos de violencia laboral, represalias, abusos, discriminación por creencias, raza, sexo, religión, etnia o edad, preferencia sexual o cualquier otra condición que derive en riesgo psicosocial o acciones en contra del favorable entorno organizacional.</p>

							<p>Tus respuestas serán  tratadas de forma <strong>CONFIDENCIAL</strong> Y <strong>ANÓNIMA</strong>.<br/> No serán utilizadas para ningún propósito distinto al de ayudarnos a mejorar y formar parte de la nueva cultura Laboral.</p>

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


</body>
</html>