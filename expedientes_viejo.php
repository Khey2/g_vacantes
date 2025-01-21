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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_empleado = "SELECT exp_tipos.tipo, exp_files.nombre, exp_files.IDempleado, exp_files.denominacion, exp_files.emp_paterno, exp_files.emp_materno, exp_files.emp_nombre, exp_files.rfc FROM exp_files LEFT JOIN exp_tipos ON exp_tipos.IDTipo = exp_files.IDtipo WHERE exp_files.IDempleado = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT * FROM sed_files_tipos";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

mysql_select_db($database_vacantes, $vacantes);
$query_files = "select nombre, coalesce(sum(case when IDtipo = 1 then 1 end), 0) as Doc1, coalesce(sum(case when IDtipo = 2 then 1 end), 0) as Doc2, coalesce(sum(case when IDtipo = 3 then 1 end), 0) as Doc3, coalesce(sum(case when IDtipo = 4 then 1 end), 0) as Doc4, coalesce(sum(case when IDtipo = 5 then 1 end), 0) as Doc5, coalesce(sum(case when IDtipo = 6 then 1 end), 0) as Doc6, coalesce(sum(case when IDtipo = 7 then 1 end), 0) as Doc7, coalesce(sum(case when IDtipo = 8 then 1 end), 0) as Doc8, coalesce(sum(case when IDtipo = 9 then 1 end), 0) as Doc9, coalesce(sum(case when IDtipo = 10 then 1 end), 0) as Doc10, coalesce(sum(case when IDtipo = 11 then 1 end), 0) as Doc11, coalesce(sum(case when IDtipo = 12 then 1 end), 0) as Doc12, coalesce(sum(case when IDtipo = 13 then 1 end), 0) as Doc13, coalesce(sum(case when IDtipo = 14 then 1 end), 0) as Doc14 from exp_files WHERE IDempleado = '$IDempleado'";
$files = mysql_query($query_files, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_files = mysql_fetch_assoc($files);
$totalRows_files = mysql_num_rows($files);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT DISTINCT exp_tipos.IDtipo, exp_tipos.tipo, exp_files.nombre, exp_files.IDempleado FROM exp_tipos RIGHT JOIN exp_files ON exp_files.IDtipo = exp_tipos.IDtipo WHERE
exp_files.IDempleado = '$IDempleado' GROUP BY exp_tipos.IDtipo ORDER BY exp_tipos.tipo ASC";
mysql_query("SET NAMES 'utf8'");
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

//si no está declarada la sesion 
if (!isset($_SESSION['el_tipo'])) {$_SESSION['el_tipo'] = 0;}
// si se cambia
if (isset($_POST['el_tipo'])) {	foreach ($_POST['el_tipo'] as $los_tipos)
	{	$_SESSION['el_tipo'] = implode(", ", $_POST['el_tipo']);}} 

$el_tipo = $_SESSION['el_tipo'];
$a1 = "";
if($el_tipo > 0) { $a1 = " AND exp_files.IDtipo IN ($el_tipo)"; }

mysql_select_db($database_vacantes, $vacantes);
$query_file = "SELECT exp_files.id, exp_files.nombre, exp_files.IDempleado, exp_files.IDtipo, exp_files.borrado, exp_files.observaciones, exp_files.fecha, exp_tipos.IDtipo, exp_tipos.tipo, exp_tipos.detalle, exp_tipos.estatus, exp_tipos.IDmatriz FROM exp_files LEFT JOIN exp_tipos ON exp_tipos.IDtipo = exp_files.IDtipo WHERE exp_files.IDempleado = '$IDempleado' AND exp_files.borrado = 0 " . $a1 . "ORDER BY exp_tipos.tipo ASC";
mysql_query("SET NAMES 'utf8'");
$file = mysql_query($query_file, $vacantes) or die(mysql_error());
$row_file = mysql_fetch_assoc($file);
$totalRows_file = mysql_num_rows($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect3.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Expendientes Digitales</h5>
						</div>

					<div class="panel-body">
					<p>A continuación se muestran los documentos cargados para el empleado indicado.</br>
                    Da clic en el botón <strong>Agregar Documento</strong>, para agregar un documento nuevo.</br>
                    Puedes descargar los documentos cargados, dando clic en el botón <strong>Descargar</strong>.</p>
					<h6><strong>Datos del Empleado</strong></h6>
                    <p><strong>No. de Empleado: </strong><?php echo $row_empleado['IDempleado']; ?></p>
                    <p><strong>Empleado: </strong><?php echo $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre']; ?></p>
                    <p><strong>Puesto: </strong><?php echo $row_empleado['denominacion']; ?></p>
                    <p><strong>Sucursal: </strong><?php echo $row_matriz['matriz']; ?></p>
                    <p>&nbsp;</p>
                    <div>
                    
					<h6><strong>Documentos cargados</strong></h6>

					<table class="table table-condensed datatable-button-html5-columns">
		          <thead>
                    <tr class="bg-success"> 
		              <th>Documento</th>
		              <th>Tipo</th>
		              <th>Fecha carga</th>
		              <th>Observaciones</th>
		              <th>Acciones</th>
		            </tr>
		            </thead>
		          <tbody>

                <?php if ($totalRows_file == 0) { ?>
                   <tr>
                     <td colpsan="5">Sin documentos cargados.</td>
                   </tr>
                <?php } else { ?> 
                 <?php  do { 
				 
 					$valores = explode(".", $row_file['nombre']);
					$resultado_file = $valores[count($valores)-1];
					$tipo = $row_file['IDtipo']; 
					$query_file_n = "SELECT * FROM exp_tipos WHERE IDtipo = '$tipo'";
					$file_n = mysql_query($query_file_n, $vacantes) or die(mysql_error());
					$row_file_n = mysql_fetch_assoc($file_n);

				 ?>
		          <tr>
		            <td><?php echo $row_file_n['tipo']?></td>
                    <td>   <?php if ($resultado_file == 'jpg')  { ?><i class="icon-file-picture"></i>.jpg
		            <?php } else if ($resultado_file == 'jpeg') { ?><i class="icon-file-picture"></i>.jpg
		            <?php } else if ($resultado_file == 'png')  { ?><i class="icon-file-picture"></i>.png
		            <?php } else if ($resultado_file == 'pdf')  { ?><i class="icon-file-pdf"></i>.pdf
				    <?php } else if ($resultado_file == 'ppt' or $resultado_file == 'pptx')  { ?><i class="icon-file-presentation"></i>.ppt
				    <?php } else if ($resultado_file == 'xls' or $resultado_file == 'xlsx')  { ?><i class="icon-file-excel"></i>.xls
				    <?php } else if ($resultado_file == 'doc' or $resultado_file == 'docx')  { ?><i class="icon-file-word"></i>.doc
				    <?php } else if ($resultado_file == 'zip' or $resultado_file == 'rar')  { ?><i class="icon-file-zip"></i>.zip
				    <?php }?></td>
		            <td><?php echo date("d/m/Y", strtotime($row_file['fecha'])); ?></td>
                    <td><?php if($row_file['observaciones'] != '') {echo $row_file['observaciones']; } else { echo "Sin observaciones";}?></td>
                    <td><a class="btn btn-info" href="files/<?php echo $IDempleado."/".$row_file['nombre']; ?>" download>Descargar</a></td>
		          </tr>
                 <?php } while ($row_file = mysql_fetch_assoc($file)); ?>
                <?php }?>



                  </tbody>
		          </table>                    

                    </div>
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
			<!-- /main content -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>
<?php
mysql_free_result($variables);
?>
