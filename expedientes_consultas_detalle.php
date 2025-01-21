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
$query_contratos = "SELECT exp_files.id, exp_files.nombre, exp_files.IDempleado, exp_files.emp_materno, exp_files.emp_paterno, exp_files.emp_nombre, exp_files.rfc, exp_files.denominacion, exp_files.IDmatriz, exp_files.IDpuesto, exp_files.IDarea, exp_files.IDsucursal, vac_matriz.matriz, vac_puestos.denominacion, vac_areas.area, exp_consultas.IDempleado, exp_consultas.IDusuario, exp_consultas.fecha_inicio, exp_consultas.fecha_fin, exp_consultas.IDexpc FROM exp_files INNER JOIN vac_matriz ON exp_files.IDmatriz = vac_matriz.IDmatriz INNER JOIN vac_puestos ON exp_files.IDpuesto = vac_puestos.IDpuesto INNER JOIN vac_areas ON exp_files.IDarea = vac_areas.IDarea INNER JOIN exp_consultas ON exp_files.IDempleado = exp_consultas.IDempleado WHERE exp_consultas.IDusuario = $IDempleado GROUP BY exp_files.IDempleado, exp_files.emp_paterno, exp_files.emp_materno, exp_files.emp_nombre, exp_files.rfc";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT * FROM sed_files_tipos";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

mysql_select_db($database_vacantes, $vacantes);
$query_file = "SELECT exp_files.id, exp_files.nombre, exp_files.IDempleado, exp_files.IDtipo, exp_files.borrado, exp_files.observaciones, exp_files.fecha, exp_tipos.IDtipo, exp_tipos.tipo, exp_tipos.detalle, exp_tipos.estatus, exp_tipos.IDmatriz FROM exp_files LEFT JOIN exp_tipos ON exp_tipos.IDtipo = exp_files.IDtipo WHERE exp_files.IDempleado = '$IDempleado' AND exp_files.borrado = 0 ORDER BY exp_tipos.tipo ASC";
mysql_query("SET NAMES 'utf8'");
$file = mysql_query($query_file, $vacantes) or die(mysql_error());
$row_file = mysql_fetch_assoc($file);
$totalRows_file = mysql_num_rows($file);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos_d = "SELECT * FROM exp_tipos";
mysql_query("SET NAMES 'utf8'");
$tipos_d = mysql_query($query_tipos_d, $vacantes) or die(mysql_error());
$row_tipos_d = mysql_fetch_assoc($tipos_d);
$totalRows_tipos_d = mysql_num_rows($tipos_d);
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Expendientes Digitales</h5>
						</div>

					<div class="panel-body">
					<p>A continuación se muestran los documentos cargados para el empleado indicado.
					
					
					<p>
					<ul class="media-list content-group">
					<li>No. Emp.:<strong><?php echo $row_contratos['IDempleado']; ?></strong></li>
					<li>Matriz: <strong><?php echo $row_contratos['matriz']; ?></strong></li>
					<li>Area: <strong><?php echo $row_contratos['area']; ?></strong></li>
					<li>Nombre: <strong><?php echo $row_contratos['emp_paterno']." ".$row_contratos['emp_materno']." ".$row_contratos['emp_nombre']; ?></strong></li>
					<li>Puesto: <strong><?php echo $row_contratos['denominacion']; ?></strong></li>
					</ul>
					</p>

					<table class="table">
		          <thead>
                    <tr class="bg-primary"> 
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
                    <td>
					<?php if ($resultado_file == 'pdf')  { ?>
					<div onClick="loadDynamicContentModal('<?php echo $row_file['id']; ?>')" class="btn bg-primary">Visualizar</a></div>
					<?php } else { ?>
                    <a class="btn btn-primary" href="expedientes_consultas_descarga.php?IDdocumento=<?php echo $row_file['id']; ?>">Descargar</a>  
					<?php }  ?>
                     </td>
		          </tr>
                 <?php } while ($row_file = mysql_fetch_assoc($file)); ?>
                <?php }?>

                  </tbody>
		          </table>                    

                    </div>
                    </div>
                    </div>

                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Documentos</h5>
						</div>

					<div class="panel-body">
                    
                   <p>A continuación, se muestran los documentos que deben integrar el Expediente:</p>

					<table class="table table-condensed table-striped table-framed" >
		          <thead>
                    <tr> 
		              <th>ID</th>
		              <th>Documento</th>
		              <th>Detalle</th>
		              <th>Obligatorio</th>
		            </tr>
		            </thead>
		          <tbody>
                 <?php  do {  ?>
		          <tr>
		            <td><?php echo $row_tipos_d['IDTipo']?></td>
		            <td><?php echo $row_tipos_d['tipo']?></td>
		            <td><?php echo $row_tipos_d['detalle']?></td>
		            <td><?php if ($row_tipos_d['requerido'] == 1) {echo "Si";}?></td>
		          </tr>
                 <?php } while ($row_tipos_d = mysql_fetch_assoc($tipos_d)); ?>
                  </tbody>
		          </table>                    

                    </div>
				  </div>


                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-full">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Lectura de Documento</h5>
								</div>
							<div class="modal-body">
							<div id="conte-modal"></div>
								<div class="modal-footer">
								<p>&nbsp;</p>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->



<script>
var IDempleado = <?php echo $IDempleado; ?>;

function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('expedientes_mdl.php?IDempleado=' + IDempleado + '&id=' + modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 

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