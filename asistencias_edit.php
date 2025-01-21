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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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
$IDusuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

$IDfecha = $_GET['IDfecha'];
$el_mes = $_GET['mes'];

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.emp_paterno, prod_activosfaltas.fecha_baja, prod_activosfaltas.emp_materno, prod_activosfaltas.emp_nombre, prod_activosfaltas.rfc, prod_activosfaltas.fecha_alta, prod_activosfaltas.descripcion_nomina, prod_activosfaltas.denominacion, prod_activosfaltas.IDmatriz, prod_activosfaltas.IDpuesto, prod_activosfaltas.IDarea, vac_areas.area, ind_asistencia.IDasistencia, ind_asistencia.IDestatus  FROM prod_activosfaltas LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activosfaltas.IDarea LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado AND ind_asistencia.IDfecha = $IDfecha WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' ) ORDER BY prod_activosfaltas.IDpuesto ASC";
mysql_query("SET NAMES 'utf8'");
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$IDempleado = $_POST['IDempleado']; 
$IDestatus = $_POST['IDestatus'];
$IDtipo = $_POST['IDtipo'];
$comentarios = $_POST['comentarios']; 
$IDfecha = $_POST['IDfecha'];
$anio = $_POST['anio'];
$mes = $_POST['mes'];

$updateSQL = sprintf("UPDATE ind_asistencia SET IDestatus=%s, IDtipo=%s, comentarios=%s WHERE IDempleado = '$IDempleado' AND IDfecha = $IDfecha",
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['comentarios'], "text"),
                       GetSQLValueString($_POST['IDempleado'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  
  // CIERRE
		$query_activostotales = "SELECT * FROM prod_activosfaltas WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' )";
		$activostotales = mysql_query($query_activostotales, $vacantes) or die(mysql_error());
		$row_activostotales = mysql_fetch_assoc($activostotales);
		$totalRows_activostotales = mysql_num_rows($activostotales);


		$query_activoscapturados = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.emp_paterno, prod_activosfaltas.fecha_baja, prod_activosfaltas.emp_materno, prod_activosfaltas.emp_nombre, prod_activosfaltas.rfc, prod_activosfaltas.fecha_alta, prod_activosfaltas.descripcion_nomina, prod_activosfaltas.denominacion, prod_activosfaltas.IDmatriz, prod_activosfaltas.IDpuesto, prod_activosfaltas.IDarea, vac_areas.area, ind_asistencia.IDasistencia, ind_asistencia.IDestatus  FROM prod_activosfaltas LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activosfaltas.IDarea LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado AND ind_asistencia.IDfecha = $IDfecha WHERE ind_asistencia.IDestatus != '' AND prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' ) ORDER BY prod_activosfaltas.IDpuesto ASC";
		$activoscapturados = mysql_query($query_activoscapturados, $vacantes) or die(mysql_error());
		$row_activoscapturados = mysql_fetch_assoc($activoscapturados);
		$totalRows_activoscapturados = mysql_num_rows($activoscapturados);


		if($totalRows_activos == $totalRows_activoscapturados) {
		  $deleteSQL = "UPDATE ind_asistencia_dias SET d".$IDfecha." = 1 WHERE IDmatriz ='$IDmatriz'";
		  mysql_select_db($database_vacantes, $vacantes);
		  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
		}

		  header("Location: asistencias_edit.php?IDfecha=$IDfecha&IDmatriz=$IDmatriz&info=2");
}

//insertar 1
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO ind_asistencia (IDempleado, IDmatriz, IDpuesto, IDarea, IDcapturador, IDestatus, anio, mes, IDfecha, IDtipo, comentarios) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDcapturador'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['mes'], "int"),
                       GetSQLValueString($_POST['IDfecha'], "int"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['comentarios'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();
  
    // CIERRE
		$query_activostotales = "SELECT * FROM prod_activosfaltas WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' )";
		$activostotales = mysql_query($query_activostotales, $vacantes) or die(mysql_error());
		$row_activostotales = mysql_fetch_assoc($activostotales);
		$totalRows_activostotales = mysql_num_rows($activostotales);


		$query_activoscapturados = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.emp_paterno, prod_activosfaltas.fecha_baja, prod_activosfaltas.emp_materno, prod_activosfaltas.emp_nombre, prod_activosfaltas.rfc, prod_activosfaltas.fecha_alta, prod_activosfaltas.descripcion_nomina, prod_activosfaltas.denominacion, prod_activosfaltas.IDmatriz, prod_activosfaltas.IDpuesto, prod_activosfaltas.IDarea, vac_areas.area, ind_asistencia.IDasistencia, ind_asistencia.IDestatus  FROM prod_activosfaltas LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activosfaltas.IDarea LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado AND ind_asistencia.IDfecha = $IDfecha WHERE ind_asistencia.IDestatus != '' AND prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' ) ORDER BY prod_activosfaltas.IDpuesto ASC";
		$activoscapturados = mysql_query($query_activoscapturados, $vacantes) or die(mysql_error());
		$row_activoscapturados = mysql_fetch_assoc($activoscapturados);
		$totalRows_activoscapturados = mysql_num_rows($activoscapturados);


		if($totalRows_activos == $totalRows_activoscapturados) {
		  $deleteSQL = "UPDATE ind_asistencia_dias SET d".$IDfecha." = 1 WHERE IDmatriz ='$IDmatriz'";
		  mysql_select_db($database_vacantes, $vacantes);
		  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
		}

		header("Location: asistencias_edit.php?IDfecha=$IDfecha&IDmatriz=$IDmatriz&info=1");
}

 switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

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


					<!-- Sorting data -->
					<div class="panel panel-flat">

						<div class="panel-body">
							<p class="content-group"><b>Instrucciones:</b><br/>
							Da clic en el botón de <b>Capturar</b> o <b>Actualizar</b> para reportar la asistencia de cada colaborador.<br/>
							Asegurate de capturar todos los casos, incluyuendo las bajas si es que aplica por la fecha.<br/>
							En cada caso, indica el motivo de su ausencia y si es necesario, captura los comentarios pertinentes.</p>
							<p><b>Fecha de captura actual:</b> <?php echo $IDfecha." de ".$elmes; ?>.</p>
							
							 <form method="post">
								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>Acciones</th>
                                    <th>No. Emp.</th>
                                    <th>Nombre</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                    <th>Fecha Alta</th>
                                    <th>Fecha Baja</th>
                                    <th>Estatus</th>
                                  </thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td>
									  <?php if ($row_activos['IDestatus'] != '') { ?>
									  <div onClick="loadDynamicContentModal('<?php echo $row_activos['IDempleado']; ?>', '<?php echo $IDfecha; ?>')" class="btn btn-primary btn-icon">Actualizar</div></td>
									  <?php } else { ?>
									  <div onClick="loadDynamicContentModal('<?php echo $row_activos['IDempleado']; ?>', '<?php echo $IDfecha; ?>')" class="btn btn-success btn-icon">Capturar</div></td>
									  <?php } ?>	
                                      <td><?php echo $row_activos['IDempleado']; ?></td>
                                      <td><?php echo $row_activos['emp_paterno'] . " " . $row_activos['emp_materno'] . " " . $row_activos['emp_nombre']; ?></td>
                                      <td><?php echo $row_activos['area']; ?>&nbsp; </td>
                                      <td><?php echo $row_activos['denominacion']; ?>&nbsp; </td>
                                      <td><?php echo date('d/m/Y', strtotime($row_activos['fecha_alta'])); ?></td>
                                      <td><?php if ($row_activos['fecha_baja'] != '0000-00-00') {echo date('d/m/Y', strtotime($row_activos['fecha_baja']));} else {echo "-";} ?></td>
                                      <td><?php if ($row_activos['fecha_baja'] != '0000-00-00') { echo "Baja";} else { echo "Activo";} ?></td>
                                    </tr>
                                    <?php } while ($row_activos = mysql_fetch_assoc($activos)); ?>
                                  </tbody>
                                </table>
							</form> 							
							
						</div>
					</div>
					<!-- /sorting data -->

                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h3 class="modal-title">Asistencia</h3>
								</div>
			              <div id="conte-modal"></div>
						</div>
					</div>
					<!-- /inline form modal -->
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
<script>
function loadDynamicContentModal(modal, IDfecha){
	var options = { modal: true };
	$('#conte-modal').load('asistencias_mdl.php?IDfecha=' + IDfecha + '&IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
