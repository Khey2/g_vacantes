<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
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


mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
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
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$semana_inicio = $semana - 8;
if ($semana_inicio < 1){$semana_inicio = 1;}
$semana_fin = $semana;
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
	<!-- /theme JS files -->

</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
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


	                <!-- Content area -->
				<div class="content">
                
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 1) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente la justificacion.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Plan de Carrera</h5>
						</div>

					<div class="panel-body">
							<p>El Plan de Carrera se ha dearrollado con el fin de que los colaboradores realicen carrera en Sahuayo y eleven su productividad. Además, permite que los colaboradores 
                            alcancen un nivel jerárquico mayor o de especialización, lo  cual elevará el sentido de pertenencia y promoverá el desarrollo y la realización de  los colaboradores, así
                             como incentivará al colaborador en la búsqueda de la mejora continua a través de la superación personal.</p>
                            </ul>
							<p>&nbsp;</p>
                            <h5>Requisitos de la Política:</h5>
                            <p>Al momento de que  un colaborador se acerque al área de Recursos Humanos para externar que desea  participar en el proceso, el Jefe de Recursos Humanos será el responsable
                               de  identificar y plasmar en el <strong>Pasaporte de Desarrollo</strong> del colaborador  los requisitos de la Política.</p>
                            </ul>
                            <ul>
                              <li><strong>Disciplina Progresiva.</strong> Los colaboradores que hayan sido sujetos a un proceso de Disciplina Progresiva en el último año, serán considerados como 
                              candidatos no viables para participar  en el proceso del Plan de Carrera.</li>
                            </ul>
                            <ul>
                              <li><strong>Puntualidad y asistencia.</strong> El colaborador no deberá presentar más de tres  faltas en un periodo de un año, previo a la fecha en la cual solicita 
                               participar en el proceso del Plan de Carrea.</li>
                            </ul>
                            <ul>
                              <li><strong>Desempeño.</strong> El colaborador deberá obtener al menos dos meses continuos de buen desempeño según indicadores de pago de productividad.</li>
                            </ul>
                            <ul>
                            <li><strong>Antigüedad.</strong> El colaborador deberá contar con al menos seis meses de antigüedad en  la empresa y tres meses en la posición previa a la posición a promocionarse. </li>
                            </ul>
							<p>&nbsp;</p>
                            <h5>Requisitos del Perfil</h5>
                            <p>El colaborador que se postule a una  vacante, deberá cumplir con los requisitos establecidos en el perfil del puesto  vacante (licencia de manejo, escolaridad, conocimientos 
                            técnicos, años y áreas de experiencia), los  cuales podrán ser exceptuados en los siguientes casos:</p>
                            </ul>
                            <ul>
                              <li>Cuando el candidato interno haya demostrado contar con las competencias y experiencia requeridos para el puesto en su cargo  actual.</li>
                            </ul>
                            <ul>
                              <li>Cuando haya estado a cargo de las funciones  del puesto de manera temporal.</li>
                            </ul>
							<p>&nbsp;</p>
                            <h5>Requisitos de Capacitación</h5>
                            <p>El colaborador que se postule para ser promovido mediane el Plan de Carrera, deberá cumplir con la capacitación, tanto teórica como práctica que se determina para el puesto.
                             La capacitación mínima necesaria que debe cubrir es:</p>
                            </ul>
                            <ul><li>Prueba de manejo.</li></ul>
                            <ul><li>Operador experto.</li></ul>
                            <ul><li>Práctica visual.</li></ul>
                            <ul><li>Inducción al puesto.</li></ul>
                            <ul><li>Entrenamiento.</li></ul>
                            <ul><li>Práctica de Manejo.</li></ul>

							<p>Para cualquier duda relacionada con el Plan de Carrera, contacta a Marco Antonio Hernández al correo <a href="mailto:mahernandez@sahuayo.mx">mahernandez@sahuayo.mx</a></p>





                   <!-- Inline form modal -->
			  <div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Semáforo</h5>
								</div>
							<div class="modal-body">
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->


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
<script>
function loadDynamicContentModal2(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('plan_carrera_mdl2.php?IDempleado=' + modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>