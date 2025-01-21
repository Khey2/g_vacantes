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

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM capa_becarios WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
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

mysql_select_db($database_vacantes, $vacantes);
$query_capacitacionev = "SELECT * FROM capa_curso_seguridad_preguntas";
mysql_query("SET NAMES 'utf8'");
$capacitacionev = mysql_query($query_capacitacionev, $vacantes) or die(mysql_error());
$row_capacitacionev = mysql_fetch_assoc($capacitacionev);
$totalRows_capacitacionev = mysql_num_rows($capacitacionev);

// actualizar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$preg1 = $_POST['preg1'];	
$preg2 = $_POST['preg2'];	
$preg3 = $_POST['preg3'];	
$preg4 = $_POST['preg4'];	
$preg5 = $_POST['preg5'];	
$preg6 = $_POST['preg6'];	
$preg7 = $_POST['preg7'];	
$preg8 = $_POST['preg8'];	
$preg9 = $_POST['preg9'];	
$preg10 = $_POST['preg10'];	
$preg11 = $_POST['preg11'];	
$preg12 = $_POST['preg12'];	
$preg13 = $_POST['preg13'];	
$preg14 = $_POST['preg14'];	
$preg15 = $_POST['preg15'];	
$preg16 = $_POST['preg16'];	
$preg17 = $_POST['preg17'];	
$preg18 = $_POST['preg18'];	
$preg19 = $_POST['preg19'];	
$preg20 = $_POST['preg20'];	
$preg21 = $_POST['preg21'];	
$respuesta1 = $_POST['respuesta1'];	
$respuesta2 = $_POST['respuesta2'];	
$respuesta3 = $_POST['respuesta3'];	
$respuesta4 = $_POST['respuesta4'];	
$respuesta5 = $_POST['respuesta5'];	
$respuesta6 = $_POST['respuesta6'];	
$respuesta7 = $_POST['respuesta7'];	
$respuesta8 = $_POST['respuesta8'];	
$respuesta9 = $_POST['respuesta9'];	
$respuesta10 = $_POST['respuesta10'];	
$respuesta11 = $_POST['respuesta11'];	
$respuesta12 = $_POST['respuesta12'];	
$respuesta13 = $_POST['respuesta13'];	
$respuesta14 = $_POST['respuesta14'];	
$respuesta15 = $_POST['respuesta15'];	
$respuesta16 = $_POST['respuesta16'];	
$respuesta17 = $_POST['respuesta17'];	
$respuesta18 = $_POST['respuesta18'];	
$respuesta19 = $_POST['respuesta19'];	
$respuesta20 = $_POST['respuesta20'];	

$updateSQL = "INSERT INTO capa_becarios_seguridad_respuestas (IDempleado, preg1, preg2, preg3, preg4, preg5, preg6, preg7, preg8, preg9, preg10, preg11, preg12, preg13, preg14, preg15, preg16, preg17, preg18, preg19, preg20, preg21, fecha) values ($el_usuario, $preg1, $preg2, $preg3, $preg4, $preg5, $preg6, $preg7, $preg8, $preg9, $preg10, $preg11, $preg12, $preg13, $preg14, $preg15, $preg16, $preg17, $preg18, $preg19, $preg20, $preg21, NOW())"; echo $updateSQL;
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());


// calculamos resultado
$calificacion = 0;
if ($preg1 == $respuesta1) { $calificacion = $calificacion + 0.5; }
if ($preg2 == $respuesta2) { $calificacion = $calificacion + 0.5; }
if ($preg3 == $respuesta3) { $calificacion = $calificacion + 0.5; }
if ($preg4 == $respuesta4) { $calificacion = $calificacion + 0.5; }
if ($preg5 == $respuesta5) { $calificacion = $calificacion + 0.5; }
if ($preg6 == $respuesta6) { $calificacion = $calificacion + 0.5; }
if ($preg7 == $respuesta7) { $calificacion = $calificacion + 0.5; }
if ($preg8 == $respuesta8) { $calificacion = $calificacion + 0.5; }
if ($preg9 == $respuesta9) { $calificacion = $calificacion + 0.5; }
if ($preg10 == $respuesta10) { $calificacion = $calificacion + 0.5; }
if ($preg11 == $respuesta11) { $calificacion = $calificacion + 0.5; }
if ($preg12 == $respuesta12) { $calificacion = $calificacion + 0.5; }
if ($preg13 == $respuesta13) { $calificacion = $calificacion + 0.5; }
if ($preg14 == $respuesta14) { $calificacion = $calificacion + 0.5; }
if ($preg15 == $respuesta15) { $calificacion = $calificacion + 0.5; }
if ($preg16 == $respuesta16) { $calificacion = $calificacion + 0.5; }
if ($preg17 == $respuesta17) { $calificacion = $calificacion + 0.5; }
if ($preg18 == $respuesta18) { $calificacion = $calificacion + 0.5; }
if ($preg19 == $respuesta19) { $calificacion = $calificacion + 0.5; }
if ($preg20 == $respuesta20) { $calificacion = $calificacion + 0.5; }

// guardamos resultado y marca
$updateSQL = "UPDATE capa_becarios_seguridad SET evaluacion = 1, calificacion = $calificacion, evaluacion_fecha =  NOW() WHERE IDempleado = $el_usuario"; 
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
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
                
				
					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Curso de Ciberseguridad Sahuayo</h6>
								</div>

								<div class="tab-content">
										<div class="panel-body">
											<div class="content-group-lg">
												<p><span class="text text-semibold">Evaluación</span><br/>
                                                <p>Instrucciones: Selecciona la respuesta correcta para cada pregunta. Algunas preguntas son de opción múltiple y otras de verdadero o falso. Lee cuidadosamente antes de responder. Todas las preguntas son obligatorias.<br/>
                                                Para cualquier duda respecto del Curso, contacta a <span class="text text-primary">Esperanza Flores</span>  al correo  <a href="mailto:EGFlores@sahuayo.mx">EGFlores@sahuayo.mx</a>.</p>

                                                <p>&nbsp;</p>
												
                                                
                    <form method="post" name="form1" action="b_capa_cursos_evaluacion.php" class="form-horizontal form-validate-jquery">
                      
           
                <?php do { ?>

                        <fieldset class="content-group">

                          <div class="form-group">
                              <label class="control-label col-lg-4"><?php echo $row_capacitacionev['IDpregunta']." de 21. " ?><?php echo $row_capacitacionev['pregunta'] ?><span class="text-danger">*</span></label>
                              <div class="col-lg-8">
                                <div class="radio">
                                    <label>
                                        <input type="radio" class="styled" value="1" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" required>
                                        <?php echo $row_capacitacionev['opcion1'] ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" class="styled" value="2" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php echo $row_capacitacionev['opcion2'] ?>
                                    </label>
                                </div>
                                <?php if ($row_capacitacionev['tipo'] > 2) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" class="styled" value="3" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                       <?php echo $row_capacitacionev['opcion3'] ?>
                                    </label>
                                </div>
                                <?php } ?>
                                <?php if ($row_capacitacionev['tipo'] > 3) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" class="styled" value="4" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php echo $row_capacitacionev['opcion4'] ?>
                                    </label>
                                </div>
                                <?php } ?>
                                <?php if ($row_capacitacionev['tipo'] > 4) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" class="styled" value="5" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php echo $row_capacitacionev['opcion5'] ?>
                                    </label>
                                </div>
                                <?php } ?>
                              </div>
                          </div>
                          <!-- /basic select -->
                          <input type="hidden" name="respuesta<?php echo $row_capacitacionev['IDpregunta'] ?>" value="<?php echo $row_capacitacionev['respuesta'] ?>">


                        </fieldset>

                    <?php } while ($row_capacitacionev = mysql_fetch_assoc($capacitacionev)); ?>

                        <div class="text-right">
                        <div>
                            <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Terminar" />
                            <button type="button" onClick="window.location.href='b_capa_cursos.php'" class="btn btn-default btn-icon">Cancelar</button>
                            <input type="hidden" name="MM_insert" value="form1">
                            <input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>">
                        </div>
                        </div>

                </form>

												
       											</div>
											</div>
										</div>
								</div>
							</div>
							<!-- /course overview -->



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

    <script>
		document.addEventListener("DOMContentLoaded", function(){
			// Invocamos cada 5 segundos ;)
			const milisegundos = 60 *1000;
			setInterval(function(){
				// No esperamos la respuesta de la petición porque no nos importa
				fetch("./refresco.php");
			},milisegundos);
		});
</script>
</body>
</html>