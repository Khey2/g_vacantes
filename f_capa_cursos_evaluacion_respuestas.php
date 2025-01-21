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
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDempleado'];

if ($row_usuario['password'] == md5($row_usuario['IDempleado'])) { header("Location: f_cambio_pass.php?info=6"); } 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_capacitacion = "SELECT * FROM capa_curso_seguridad WHERE IDempleado = $el_usuario";
$capacitacion = mysql_query($query_capacitacion, $vacantes) or die(mysql_error());
$row_capacitacion = mysql_fetch_assoc($capacitacion);
$totalRows_capacitacion = mysql_num_rows($capacitacion);

mysql_select_db($database_vacantes, $vacantes);
$query_capacitacionev = "SELECT * FROM capa_curso_seguridad_preguntas WHERE IDpregunta <= 20";
mysql_query("SET NAMES 'utf8'");
$capacitacionev = mysql_query($query_capacitacionev, $vacantes) or die(mysql_error());
$row_capacitacionev = mysql_fetch_assoc($capacitacionev);
$totalRows_capacitacionev = mysql_num_rows($capacitacionev);

$query_larespuesta = "SELECT DISTINCT * FROM capa_curso_seguridad_respuestas WHERE IDempleado = $el_usuario"; 
$larespuesta = mysql_query($query_larespuesta, $vacantes) or die(mysql_error());
$row_larespuesta = mysql_fetch_assoc($larespuesta);

$respuesta1 = $row_larespuesta['preg1']; 
$respuesta2 = $row_larespuesta['preg2']; 
$respuesta3 = $row_larespuesta['preg3']; 
$respuesta4 = $row_larespuesta['preg4']; 
$respuesta5 = $row_larespuesta['preg5']; 
$respuesta6 = $row_larespuesta['preg6']; 
$respuesta7 = $row_larespuesta['preg7']; 
$respuesta8 = $row_larespuesta['preg8']; 
$respuesta9 = $row_larespuesta['preg9']; 
$respuesta10 = $row_larespuesta['preg10']; 
$respuesta11 = $row_larespuesta['preg11']; 
$respuesta12 = $row_larespuesta['preg12']; 
$respuesta13 = $row_larespuesta['preg13']; 
$respuesta14 = $row_larespuesta['preg14']; 
$respuesta15 = $row_larespuesta['preg15']; 
$respuesta16 = $row_larespuesta['preg16']; 
$respuesta17 = $row_larespuesta['preg17']; 
$respuesta18 = $row_larespuesta['preg18']; 
$respuesta19 = $row_larespuesta['preg19']; 
$respuesta20 = $row_larespuesta['preg20']; 


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
                                                <p><b>Resultado: </b><?php echo $row_capacitacion['calificacion']; ?> / 10. </p>
                                                <p>La respuesta correcta está marcada en Verde.</p>

                                                <p>&nbsp;</p>
												
                                                
                            <div class="text-right">
                            <div>
                            <button type="button" onClick="window.location.href='f_capa_cursos.php'" class="btn btn-default btn-icon">Regresar</button>
                            </div>
                            </div>
                      
           
                <?php do { $IDpregunta =  "preg".$row_capacitacionev['IDpregunta']; 
                         ?> 

                        <fieldset class="content-group">

                          <div class="form-group">
                              <label class="control-label col-lg-4"><?php echo $row_capacitacionev['IDpregunta']." de 20. " ?><?php echo $row_capacitacionev['pregunta'] ?><span class="text-danger">*</span></label>
                              <div class="col-lg-8">
                                <div class="radio">
                                    <label>
                                        <input type="radio" <?php if ($row_larespuesta[$IDpregunta] == 1) { echo "checked='checked'"; } ?> class="styled" value="1" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php if ($row_capacitacionev['respuesta'] == 1) { echo "<span class='text text-success'> ".$row_capacitacionev['opcion1']."</span>"; } else {  echo $row_capacitacionev['opcion1']; } ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" <?php if ($row_larespuesta[$IDpregunta] == 2) { echo "checked='checked'"; } ?> class="styled" value="2" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php if ($row_capacitacionev['respuesta'] == 2) { echo "<span class='text text-success'> ".$row_capacitacionev['opcion2']."</span>"; } else {  echo $row_capacitacionev['opcion2']; } ?>
                                    </label>
                                </div>
                                <?php if ($row_capacitacionev['tipo'] > 2) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" <?php if ($row_larespuesta[$IDpregunta] == 3) { echo "checked='checked'"; } ?> class="styled" value="3" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php if ($row_capacitacionev['respuesta'] == 3) { echo "<span class='text text-success'> ".$row_capacitacionev['opcion3']."</span>"; } else {  echo $row_capacitacionev['opcion3']; } ?>
                                    </label>
                                </div>
                                <?php } ?>
                                <?php if ($row_capacitacionev['tipo'] > 3) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" <?php if ($row_larespuesta[$IDpregunta] == 4) { echo "checked='checked'"; } ?> class="styled" value="4" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php if ($row_capacitacionev['respuesta'] == 4) { echo "<span class='text text-success'> ".$row_capacitacionev['opcion4']."</span>"; } else {  echo $row_capacitacionev['opcion4']; } ?>
                                    </label>
                                </div>
                                <?php } ?>
                                <?php if ($row_capacitacionev['tipo'] > 4) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" class="styled" value="5" id="preg<?php echo $row_capacitacionev['IDpregunta'] ?>" name="preg<?php echo $row_capacitacionev['IDpregunta'] ?>">
                                        <?php if ($row_capacitacionev['respuesta'] == 5) { echo "<span class='text text-success'> ".$row_capacitacionev['opcion5']."</span>"; } else {  echo $row_capacitacionev['opcion5']; } ?>
                                    </label>
                                </div>
                                <?php } ?>
                              </div>
                          </div>
                          <!-- /basic select -->


                        </fieldset>

                    <?php } while ($row_capacitacionev = mysql_fetch_assoc($capacitacionev)); ?>

                        <div class="text-right">
                        <div>
                            <button type="button" onClick="window.location.href='f_capa_cursos.php'" class="btn btn-default btn-icon">Regresar</button>
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