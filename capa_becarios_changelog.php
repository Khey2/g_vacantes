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


if(isset($_POST['el_area']) && ($_POST['el_area']  > 0)) {
$_SESSION['el_area'] = $_POST['el_area']; } else { $_SESSION['el_area'] = "1,2,3,4,5,6,7,8,9,10,11";}

if(isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = "0";}

if(isset($_POST['el_programa']) && ($_POST['el_programa']  > 0)) {
$_SESSION['el_programa'] = $_POST['el_programa']; } else { $_SESSION['el_programa'] = "1,2,3,4,5,6";}

if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } else { $_SESSION['el_mes'] = date("m");}

if(isset($_POST['el_estatus'])) {
$_SESSION['el_estatus'] = $_POST['el_estatus']; } else { $_SESSION['el_estatus'] = 1;}

$el_programa = $_SESSION['el_programa'];
$el_mes = $_SESSION['el_mes'];
$el_estatus = $_SESSION['el_estatus'];
$el_area = $_SESSION['el_area'];
$la_matriz = $_SESSION['la_matriz'];

if ($la_matriz != 0) { $filtroMatriz = ' AND capa_becarios.IDmatriz  = $la_matriz';} else { $filtroMatriz = '';}

$Fecha = $anio.'-'.$el_mes.'-01';
$fini = new DateTime($Fecha);
$fini->modify('first day of this month');
$finik = $fini->format('Y/m/d'); 

$fter = new DateTime($Fecha);
$fter->modify('last day of this month');
$fterk = $fter->format('Y/m/d'); 

if (isset($_POST['buscado'])) {	
$arreglo = '';
$array = explode(" ", $_POST['buscado']);
$contar = substr_count($_POST['buscado'], ' ') + 1;
$i = 0;
while($contar > $i) {
$arreglo .= " AND (capa_becarios.emp_paterno LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR vac_areas.area LIKE '%" . $array[$i] . "%' "; 
$arreglo .= " OR vac_subareas.subarea LIKE '%" . $array[$i] . "%' "; 
$arreglo .= " OR capa_becarios.emp_materno LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR capa_becarios.emp_nombre LIKE '%" . $array[$i] . "%' )"; 
    $i++; } }
	
if (!isset($_POST['buscado'])) { $filtroBuscado = ''; }  else { $filtroBuscado = $arreglo; $IDvisible = 1;}

$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_becarios  = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, vac_matriz.matriz, vac_areas.area, vac_subareas.subarea FROM capa_becarios LEFT JOIN vac_subareas ON capa_becarios.IDsubarea = vac_subareas.IDsubarea LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo LEFT JOIN vac_matriz ON capa_becarios.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON capa_becarios.IDarea = vac_areas.IDarea WHERE DATE(capa_becarios.fecha_alta) <= '$fterk' AND capa_becarios.activo = '$el_estatus' AND capa_becarios.IDtipo in ($el_programa) AND capa_becarios.IDarea in ($el_area)".$filtroMatriz.$filtroBuscado;
mysql_query("SET NAMES 'utf8'");
$becarios = mysql_query($query_becarios , $vacantes) or die(mysql_error());
$row_becarios = mysql_fetch_assoc($becarios);
$totalRows_becarios  = mysql_num_rows($becarios );

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizl = "SELECT DISTINCT vac_matriz.matriz, vac_matriz.IDmatriz FROM vac_matriz RIGHT JOIN capa_becarios ON vac_matriz.IDmatriz = capa_becarios.IDmatriz ORDER BY vac_matriz.matriz ASC";
$matrizl = mysql_query($query_matrizl, $vacantes) or die(mysql_error());
$row_matrizl = mysql_fetch_assoc($matrizl);
$totalRows_matrizl = mysql_num_rows($matrizl);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT * FROM capa_becarios_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_programa = "SELECT * FROM capa_becarios_tipo";
$programa = mysql_query($query_programa, $vacantes) or die(mysql_error());
$row_programa = mysql_fetch_assoc($programa);
$totalRows_programa = mysql_num_rows($programa);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDempleado_n = $_POST["IDempleado"]; 
	$IDestatus_p = $_POST["activo"]; 
	$query1 = "UPDATE capa_becarios SET activo = '$IDestatus_p' WHERE capa_becarios.IDempleado = '$IDempleado_n'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: capa_becarios_activos.php?info=9"); 	
}
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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/bec_datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html_bec.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
	<!-- /theme JS files -->
</head>
<body class="has-detached-left" > 
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
                

						<!-- Basic alert -->

					<!-- Detached sidebar -->

						
					<table class="table">
						<thead>
							<tr>
							    <th>Fecha</th>
							    <th>Cambios</th>
						    </tr>
					    </thead>
						<tbody>							  
							<tr><td>12 agosto 22</td><td>
							V1. Inicial</td></tr>
							<tr><td>15 agosto 22</td><td>
							Se agrega a BD y vista admin campo de fecha de evaluación.<br/>
							Se agrega formulario de captura de campos adicionales para RFC, CURP y teléfono.<br/>
							Se agregan 4 preguntas específicas para programa JCF.<br/>
							Se agrega formato para Foto de evaluación (pendiente agregar guia).<br/>
							Se agrega filtrado por sucursal en vista activos.<br/>
							Rediseño de cálculo de escala de resultados, según total de preguntas.<br/>
							<tr><td>22 agosto 22</td><td>
							Se agrega validador de tipo de archivo para carga.<br/>
							Se agregan campos de teléfono de emergencias y contacto campo adicional.<br/>
							Se agrega opción de borrar evaluaciones para admin.<br/>
							se elimina liga normatividad interna.<br/>
							Se corrige validación de campos obligatorios en algunos campos.<br/>
							Se agrega la opción de capturar texto de inicio para Becarios desde el admin.<br/>
							Correcciónes: <br/>
							En algunos casos no aparecen opciones de respuestas.<br/>
							Motivo baja solo se ve en inactivos.<br/>
							Mariana no entra.<br/>
							No actualiza preguntas. <br/>
							</td></tr>
							<tr><td>24 agosto 22</td><td>
							Se agrega gestión de FAQs para vista de Becarios.<br/>
							Se agrega resultado de la evaluación en el mes filtrado en vista Tutor y Administrador.<br/>
							Correcciónes: <br/>
							Corrección de formato de reporte evaluaciones.</td></tr>
							<tr><td>31 agosto 22</td><td>
							Se agrega botón para restablecer password para Becarios a la sección Admin.
							Actualización de nombres de archivo por tipo de archivo.
							Actualización de datos para contactos.
							Se agregó dato de mes actual en Tutor.
							</td></tr>
							<tr><td></td><td>. </td></tr>
							<tr><td></td><td>. </td></tr>
														
							</td></tr>
                        </tbody>
                   </table> 



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