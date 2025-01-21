<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$IDperiodovar = $row_variables['IDperiodo'];


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];

$area_rh = $row_usuario['area_rh'];

$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$query_periodos = "SELECT * FROM sed_periodos_sed"; 
mysql_query("SET NAMES 'utf8'");
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];

if (isset($_POST['IDperiodo'])) {$_SESSION['IDperiodo'] = $_POST['IDperiodo'];} 
elseif (!isset($_SESSION['IDperiodo'])){$_SESSION['IDperiodo'] = $IDperiodovar;}

$IDperiodo = $_SESSION['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT sed_servicio_preguntas.pregunta_texto, sed_servicio_preguntas.pregunta_area, sed_servicio_preguntas.pregunta_tema, sed_servicio_preguntas.pregunta_responsable, sed_servicio.IDservicio, sed_servicio.IDpregunta, Avg(sed_servicio.IDrespuesta) AS Resultado, sed_servicio.observaciones FROM sed_servicio LEFT JOIN sed_servicio_preguntas ON sed_servicio_preguntas.IDpregunta = sed_servicio.IDpregunta WHERE sed_servicio.IDpregunta NOT LIKE 29 GROUP BY sed_servicio.IDpregunta";
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_universo = "SELECT Count(sed_servicio.IDempleado), sed_servicio.IDservicio FROM sed_servicio GROUP BY sed_servicio.IDempleado";
$universo = mysql_query($query_universo, $vacantes) or die(mysql_error());
$row_universo = mysql_fetch_assoc($universo);
$totalRows_universo = mysql_num_rows($universo);

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
	<!-- /core JS files -->

   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/tasks_list6.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
    
    
    
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
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido a la Sección de Resultados de la Encuesta de Servicio de RH.</p>
							<p>Coloca el cursos del mouse sobre el icono <i class="icon-pointer"></i> para ver la pregunta.</p>
							<p>Las preguntas con el icono <i class="icon-comment-discussion position-left"></i> tienen comentarios, da clic en el icono para verlos.</p>
							<p><strong>Universo de encuestados: </strong><?php echo $totalRows_universo;?> colaboradores.</p>
					</div>
                    
                    
					<div class="table-responsive content-group">
                   <table class="table tasks-list table-condensed">
						<thead>
						 <tr>
                          <th>Area</th>
                          <th>Tema</th>
                          <th>Responsable</th>
                          <th>Resultado</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { $resultado_final = ($row_resultados['Resultado']-2);  ?>
                        <tr>
                          <td><?php echo $row_resultados['pregunta_area']; ?></td>
                          <td><button type="button" class="btn btn-default btn-xs" data-popup="popover" title="Pregunta:" data-trigger="hover" data-content=" <?php echo $row_resultados['pregunta_texto']; ?>"> <i class="icon-pointer"></i></button> <?php echo $row_resultados['pregunta_tema']; ?></td>
                          <td><?php echo $row_resultados['pregunta_responsable']; ?></td>
                          <td><?php if($resultado_final > 0) {echo round($resultado_final,0)."% ";} else { echo "";}  ?>
						    <?php if($resultado_final >= 95) { echo "<span class='label label-flat label-icon text-success-600'>
							 <i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-full2'></i></span>"; } 
							 else if($resultado_final >= 90) { echo "<span class='label label-flat label-icon text-success-600'>
							 <i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-empty3'></i></span>"; } 
							 else if($resultado_final >= 85) { echo "<span class='label label-flat label-icon text-success-600'>
							 <i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i></span>"; } 
							 else if($resultado_final >= 80) { echo "<span class='label label-flat label-icon text-success-600'>
							 <i class='icon-star-full2'></i><i class='icon-star-full2'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i></span>"; } 
							 else if($resultado_final >= 75) { echo "<span class='label label-flat label-icon text-success-600'>
							 <i class='icon-star-full2'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i></span>"; }
							 else if($resultado_final > 20) { echo "<span class='label label-flat label-icon text-success-600'>
							 <i class='icon-star-empty3'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i><i class='icon-star-empty3'></i></span>"; }
							 else { echo "";} ?>
                             
                             
                              <?php
                             $IDpregunta = $row_resultados['IDpregunta']; 
							 mysql_select_db($database_vacantes, $vacantes);
							 $query_comentarios = "SELECT * FROM sed_servicio WHERE IDpregunta = '$IDpregunta' AND observaciones IS NOT NULL  AND observaciones != ''";
							 $comentarios = mysql_query($query_comentarios, $vacantes) or die(mysql_error());
							 $row_comentarios = mysql_fetch_assoc($comentarios); 
							 $totalRows_comentarios = mysql_num_rows($comentarios); 
							 if ( $totalRows_comentarios > 0 ) { ?>
                             
						  <button type="button" data-target="#comentario<?php echo $IDpregunta ?>" data-toggle="modal" class="label label-flat label-icon text-primary-600">
                          <i class="icon-comment-discussion position-left"></i></button>
                          <?php } ?>
                          </td>
                        </tr>
                        
                        
                      <!-- Modal Importar -->
					<div id="comentario<?php echo $IDpregunta ?>" class="modal fade" tabindex="-2">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Comentarios:</h6>
								</div>

								<div class="modal-body">
									
									 <?php do { ?>
                                     <p>
    	                                 <ul class="text-muted">
											 <li><span class="text-muted"><?php echo $row_comentarios['observaciones']; ?></span></li>
 										</ul>
                                     </p>
                                    <?php } while ($row_comentarios = mysql_fetch_assoc($comentarios)); ?>
                                </div>

								<div class="modal-footer">
                               <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- //Importar  -->

                                               
                        <?php } while ($row_resultados = mysql_fetch_assoc($resultados)); ?>
                   	</tbody>							  
                   </table>
					</div>
					<!-- /Contenido -->

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