<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

var_dump("panel.php"); exit;

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

//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  
  
 if (!isset($_SESSION['kt_IDsistema'])) { header("Location: f_panel.php?info=4"); }

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

if($row_usuario['password'] == md5($row_usuario['IDusuario'])) { header("Location: cambio_pass.php?info=4"); }

$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$semana = date("W"); //la semana empieza ayer 
if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['mi_mes'] = $el_mes;
$_SESSION['el_anio'] = $anio;

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

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
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>

	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/sucursal.js"></script>
	<script src="global_assets/js/sucursal2.js"></script>
	<script src="global_assets/js/area.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
	
<?php if($row_usuario['usuario_area'] > 0) { require_once('assets/mensajes.php'); } ?>

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
                

					<!-- Contenido -->
                  <div class="panel panel-flat">
                    <h4 class="text-center content-group-lg">
								Bienvenido(a) al <?php echo $row_variables['nombre_sistema']; ?>
								<small class="display-block">Sahuayo <?php echo $row_variables['anio']; ?></small>
							</h4>
                   </div>


                  <div class="panel panel-flat">
					<div class="panel-body">
							<p>Bienvenido(a) <?php echo $row_usuario['usuario_nombre']; ?>.</p>
							<p>El objetivo del <strong>Sistema de Gestión de Recursos Humanos</strong> es asegurar un adecuado control y seguimiento del diversos procesos relacionados con la gestión del capital humano, asegurando que se cubran los objetivos estratégicos de Recursos Humanos.</p>
                            <p><strong>Indicadores Cuantitativos</strong>
							<ul class="list-group no-border">
							<li><strong>Rotación:</strong> Puesto, Área, Mes, Motivos de baja.</li>
							<li><strong>Vacantes:</strong> Tiempo de cobertura.</li>
							<li><strong>KPIs:</strong> Estratégicos, Informativos.</li>
							<li><strong>Productividad:</strong> Presupuesto, Gasto semanal.</li>
							<li><strong>Incidencias:</strong> Horas Extras, Incentivos, Domingos Trabajados, Comisiones.</li>
							<li><strong>Estructura:</strong> Plantilla autorizada, Tabulador sueldos.</li>
							</ul>
							</p>
							
                            <p><strong>Indicadores Cualitativos</strong>
							<ul class="list-group no-border">
							<li><strong>Clima Laboral:</strong> Puesto, Área, Mes, Motivos de baja.</li>
							<li><strong>Evaluación del Desempeño:</strong> Objetivos por área, 360°.</li>
							<li><strong>Plan Carrera:</strong> Prioridad Choferes, Cantidad de promovidos.</li>
							</ul>
							</p>
							
                   </div>
                  </div>


				  <div class="row">
                    <div class="col-md-6">
                                            
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder6 position-left"></i>
										Archivos disponibles para descarga
									</h6>
								</div>
								
								<div class="list-group no-border">
									<a href="files/sed_captura_objetivos.pdf" target="_blank" class="list-group-item">
										<i class="icon-file-pdf"></i> Guia de Captura de Objetivos de Desempeño<span class="label bg-success-400">Nuevo</span>
									</a>
									<a href="files/sed_revision_final.pdf" target="_blank" class="list-group-item">
										<i class="icon-file-pdf"></i> Guia de Revisión Final de Desempeño <span class="label bg-success-400">Nuevo</span>
									</a>
									<a href="rys/15.%20Procedimientos/MANUAL.pdf" target="_blank" class="list-group-item">
										<i class="icon-file-pdf"></i> Manual de Reclutamiento y Selección 
									</a>

									<a href="rys/4.%20Entrevista%20General/corporativo/Reporte%20de%20Entrevista.pptx" target="_blank"  class="list-group-item">
										<i class=" icon-file-presentation"></i> Guia de Entrevista por Competencias
									</a>

									<a href="rys/6.%20Entrevista%20Competencias/Entrevista%20por%20Competencias.xlsx" target="_blank"  class="list-group-item">
										<i class="icon-file-excel"></i> Formato de Entrevista por Competencias
									</a>

									<a href="rys/6.%20Entrevista%20Competencias/Catalogo%20de%20Competencias.pdf" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Catálogo de Competencias Laborales
									</a>

									<a href="rys/6.%20Entrevista%20Competencias/Diccionario%20de%20Competencias%20y%20Comportamientos%20Sahuayo.pdf"
                                     target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Diccionario de Competencias Laborales
									</a>

									<a href="rys/16.%20Requi/formato.xls" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Formato de Requisición de Personal
									</a>

									<a href="rys/16.%20Requi/Reporte.xls"  target="_blank" class="list-group-item">
										<i class="icon-file-excel"></i> Versión previa del Reporte de Vacantes
									</a>
								</div>
							</div>
                     
                     </div>

						<div class="col-md-6">
						
							<div class="panel panel-flat">
                            <!-- Simple inline block with icon and button -->
								<div class="panel-body text-center">
									<div class="media-left media-middle">
										<i class="icon-lifebuoy icon-3x text-muted no-edge-top"></i>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold">¿Tienes dudas o sugerencias?</h6>
										<span class="text-muted">Contactanos por correo electrónico a <a href="mailto:jacardenas@sahuayo.mx">jacardenas@sahuayo.mx</a></br>
                                                                 O por teléfono a la extensión 1221.</br>
                                                                 Nos pondremos en contacto contigo a la brevedad.</span>
									</div>
								</div>
							<!-- /simple inline block with icon and button -->
                          </div>

							<div class="panel panel-flat">
                            <!-- Widget with centered text and colored icon -->
								<div class="panel-body text-center">
									<div class="content-group mt-5">
									</div>
									<a href="general_faq.php"><div class="icon-object border-success text-success"><i class="icon-book"></i></div></a>
									<h6 class="text-semibold"><a href="general_faq.php" class="text-default">Preguntas Frecuentes</a></h6>
									<p class="mb-15">Todas las dudas acerca del uso del sistema, las puedes consultar en las <a href="general_faq.php">FAQs &rarr;</a></p>
								</div>
							<!-- /widget with centered text and colored icon -->							
                            </div>

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