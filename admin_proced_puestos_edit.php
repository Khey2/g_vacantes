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
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDusuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDpuesto = $_GET["IDpuesto"];

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDpuesto = $IDpuesto";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_catalogo = "SELECT proced_puestos.IDpuesto, proced_documentos.documento, proced_documentos.IDdocumento, proced_direcciones.direccion, proced_documentos.IDDarea, proced_documentos.IDdireccion, proced_documentos.IDsubarea, proced_subareas.subarea, proced_areas.area FROM proced_documentos LEFT JOIN proced_puestos ON proced_documentos.IDdocumento = proced_puestos.IDdocumento 
AND proced_puestos.IDpuesto = $IDpuesto LEFT JOIN proced_direcciones ON proced_documentos.IDdireccion = proced_direcciones.IDdireccion LEFT JOIN proced_areas ON proced_documentos.IDDarea = proced_areas.IDDarea
LEFT JOIN proced_subareas ON proced_documentos.IDsubarea = proced_subareas.IDsubarea";  
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);
$totalRows_catalogo = mysql_num_rows($catalogo);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO proced_puestos (IDusuario, IDdocumento, IDpuesto) VALUES (%s, %s, %s)",
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($IDdocumento, "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  $captura = mysql_insert_id();
  header("Location: admin_proced_puestos.php?info=1&IDdocumento=$IDdocumento"); 	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDdocumento_tipos'];
  $deleteSQL = "DELETE FROM proced_puestos WHERE IDdocumento_tipos = $borrado";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_proced_puestos.php?info=3&IDdocumento=$IDdocumento");
}

// agregar todos
if ((isset($_GET['todos'])) && ($_GET['todos'] == 1)) {
$query_puestosall = "SELECT IDpuesto FROM vac_puestos WHERE IDarea < 13";
$puestosall = mysql_query($query_puestosall, $vacantes) or die(mysql_error());
$row_puestosall = mysql_fetch_assoc($puestosall);
$totalRows_puestosall = mysql_num_rows($puestosall);

do {
$elpuesto = $row_puestosall['IDpuesto'];
$agregartodos = "INSERT INTO proced_puestos (IDdocumento, IDpuesto, IDusuario) value ('$IDdocumento', '$elpuesto', '$IDusuario')";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($agregartodos, $vacantes) or die(mysql_error());
} while ($row_puestosall = mysql_fetch_assoc($puestosall));
header("Location: admin_proced_puestos.php?info=1&IDdocumento=$IDdocumento");
}

// bororar todos
if ((isset($_GET['todos'])) && ($_GET['todos'] == 2)) {
$borrado = $_GET['IDdocumento'];
$deleteSQL = "DELETE FROM proced_puestos WHERE IDdocumento = $borrado";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
header("Location: admin_proced_puestos.php?info=3&IDdocumento=$IDdocumento");
}

// bororar algunos
if ((isset($_GET['todos'])) && ($_GET['todos'] == 3)) {
$borrado = $_GET['IDdocumento'];
foreach ($_POST['IDpuesto'] as $credens) { $credenciales = implode(",", $_POST['IDpuesto']); } echo $credenciales;
$deleteSQL = "DELETE FROM proced_puestos WHERE IDdocumento = $borrado AND IDpuesto IN ($credenciales)";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
header("Location: admin_proced_puestos.php?info=3&IDdocumento=$IDdocumento");
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
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5pd.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
	<!-- /theme JS files -->
 <script>
    function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i] != source)
    checkboxes[i].checked = source.checked;
    }
    }
    </script>
</head>
<body> 
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p></p><b>Puesto:</b> <?php echo $row_puesto['denominacion']; ?></p>
						<a href="admin_proced_directorio.php" class="btn btn-default">Regresar</a><p>&nbsp;</p>

                        <table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>IDdoc</th>
                          <th>Documento</th>
                          <th>Direccion</th>
                          <th>Area</th>
                          <th>Subarea</th>
 					      <th class="text-center">Estatus</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { 
                        $IDdocumento = $row_catalogo['IDdocumento'];
                        mysql_select_db($database_vacantes, $vacantes);
                        $query_estado = "SELECT * FROM proced_puestos WHERE IDdocumento = $IDdocumento AND IDpuesto = $IDpuesto";
                        $estado = mysql_query($query_estado, $vacantes) or die(mysql_error());
                        $row_estado = mysql_fetch_assoc($estado);
                        $totalRows_estado = mysql_num_rows($estado);
                        ?>
                        <tr>
                          <td><?php echo $row_catalogo['IDdocumento']; ?></td>
                          <td><?php echo $row_catalogo['documento']; ?></td>
                          <td><?php if ($row_catalogo['IDdireccion'] != '') {echo $row_catalogo['direccion'];} else { echo "-";} ?></td>
                          <td><?php if ($row_catalogo['IDDarea'] != '') {echo $row_catalogo['area'];} else { echo "-";} ?></td>
                          <td><?php if ($row_catalogo['IDsubarea'] != '') {echo $row_catalogo['subarea'];} else { echo "-";} ?></td>
                          <td><?php if ( $totalRows_estado > 0){echo "Asignado";} else {echo "No asignado";}?></td>
                        </tr> 
                        <?php } while ($row_catalogo = mysql_fetch_assoc($catalogo)); ?>
                   	</tbody>							  
                 </table>
				 


					</div>

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