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
$query_resultados = "SELECT sed_servicio_preguntas.pregunta_texto, sed_servicio_preguntas.pregunta_area, sed_servicio_preguntas.pregunta_tema, sed_servicio_preguntas.pregunta_responsable, sed_servicio.IDservicio, sed_servicio.IDpregunta, Avg(sed_servicio.IDrespuesta) AS Resultado, sed_servicio.observaciones, sed_servicio_preguntas.IDarea FROM sed_servicio LEFT JOIN sed_servicio_preguntas ON sed_servicio_preguntas.IDpregunta = sed_servicio.IDpregunta WHERE sed_servicio.anio = $anio AND sed_servicio.IDpregunta NOT LIKE 29 AND sed_servicio_preguntas.IDarea in ($area_rh) GROUP BY sed_servicio.IDpregunta";
mysql_query("SET NAMES 'utf8'");
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_universo = "SELECT Count(sed_servicio.IDempleado), sed_servicio.IDservicio FROM sed_servicio WHERE sed_servicio.anio = $anio GROUP BY sed_servicio.IDempleado";
$universo = mysql_query($query_universo, $vacantes) or die(mysql_error());
$row_universo = mysql_fetch_assoc($universo);
$totalRows_universo = mysql_num_rows($universo);

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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>
	</head>
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
					</div>
                    
                    
					<div class="table-responsive content-group">
			<table class="table table-condensed datatable-button-html5-columns">				
						<thead>
						 <tr>
                          <th>Area</th>
                          <th>Pregunta</th>
                          <th>Tema</th>
                          <th>Responsable</th>
                          <th>Resultado</th>
                          <th>Año Anterior</th>
                          <th>Comentarios</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { $resultado_final = ($row_resultados['Resultado']); 

						$IDpregunta = $row_resultados['IDpregunta']; 
						$anio_ant = $anio  - 1;
						mysql_select_db($database_vacantes, $vacantes);
						$query_resultados_pre = "SELECT sed_servicio_preguntas.pregunta_texto, sed_servicio_preguntas.pregunta_area, sed_servicio_preguntas.pregunta_tema, sed_servicio_preguntas.pregunta_responsable, sed_servicio.IDservicio, sed_servicio.IDpregunta, Avg(sed_servicio.IDrespuesta) AS Resultado, sed_servicio.observaciones FROM sed_servicio LEFT JOIN sed_servicio_preguntas ON sed_servicio_preguntas.IDpregunta = sed_servicio.IDpregunta WHERE sed_servicio.anio = $anio_ant AND sed_servicio.IDpregunta NOT LIKE 29 AND sed_servicio.IDpregunta = '$IDpregunta' GROUP BY sed_servicio.IDpregunta";
						$resultados_pre = mysql_query($query_resultados_pre, $vacantes) or die(mysql_error());
						$row_resultados_pre = mysql_fetch_assoc($resultados_pre);
						$totalRows_resultados_pre = mysql_num_rows($resultados_pre);

					  $resultado_final_pre = ($row_resultados_pre['Resultado']); ?>
                        <tr>
                          <td><?php echo $row_resultados['pregunta_area']; ?></td>
                          <td><?php echo $row_resultados['pregunta_texto']; ?></td>
                          <td><?php echo $row_resultados['pregunta_tema']; ?></td>
                          <td><?php echo $row_resultados['pregunta_responsable']; ?></td>
                          <td><?php if($resultado_final > 0) {echo round($resultado_final,0)."% ";} else { echo "";}  ?>
                              <?php
                             $IDpregunta = $row_resultados['IDpregunta']; 
							 mysql_select_db($database_vacantes, $vacantes);
							 $query_comentarios = "SELECT * FROM sed_servicio WHERE ANIO = '$anio' AND IDpregunta = '$IDpregunta' AND observaciones IS NOT NULL  AND observaciones != ''";
							 $comentarios = mysql_query($query_comentarios, $vacantes) or die(mysql_error());
							 $row_comentarios = mysql_fetch_assoc($comentarios); 
							 $totalRows_comentarios = mysql_num_rows($comentarios); 
							 if ( $totalRows_comentarios > 0 ) { ?>
                          <?php } ?>
                          </td>
						  <td><?php if($resultado_final_pre > 0) {echo round($resultado_final_pre,0)."% ";} else { echo "";}  ?></td>
						  <td><?php do { echo $row_comentarios['observaciones']."<br />"; } while ($row_comentarios = mysql_fetch_assoc($comentarios)); ?><br/></td>
                        </tr>
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