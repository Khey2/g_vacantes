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

$el_usuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDmatrizes'];
$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$matriz_actual = $row_usuario['IDmatriz'];


if(isset($_POST['el_area']) && ($_POST['el_area']  > 0)) {
$_SESSION['el_area'] = $_POST['el_area']; }  else { $_SESSION['el_area'] = "";}

if(isset($_POST['el_puesto']) && ($_POST['el_puesto']  > 0)) {
$_SESSION['el_puesto'] = $_POST['el_puesto']; }  else { $_SESSION['el_puesto'] = "";}


if(isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; }  else { $_SESSION['la_matriz'] = "";}


$la_matriz = $_SESSION['la_matriz'];
$el_area = $_SESSION['el_area'];
$el_puesto = $_SESSION['el_puesto'];
//$la_matriz = $_SESSION['la_matriz'];
//$el_area = $_SESSION['el_area'];

if($el_area == "") { $b = ""; } else { $b = " AND prod_activos.IDarea = " . $el_area; } 
if($el_puesto == "") { $c = ""; } else { $c = " AND prod_activos.IDpuesto = " . $el_puesto; } 
if($el_area == "") { $a = ""; } else { $a = " WHERE vac_puestos.IDarea = " . $el_area; } 
if($la_matriz == "") { $d = ""; } else { $d = " AND prod_activos.IDmatriz = " . $la_matriz; } 

if($row_usuario['password'] == md5($row_usuario['IDusuario'])) { header("Location: cambio_pass.php?info=4"); }

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT cov_casos.IDcovid,  cov_casos.IDempleado, cov_casos.IDpuesto, cov_casos.emp_paterno,  cov_casos.emp_materno,  cov_casos.emp_nombre, cov_casos.IDmatriz, cov_casos.IDsucursal, cov_casos.enf_respiratoria, cov_casos.tratam_inicio, cov_casos.tratam_fin, cov_casos.IDestatus,  cov_casos.observaciones, cov_casos.IDmotivo,  cov_casos.IDreemplazo, cov_casos.fecha_final, cov_casos.fecha_inicio, cov_casos.enfermedad_general, cov_casos.IDdoc_oficial, vac_puestos.denominacion, vac_areas.area, vac_areas.IDarea, vac_matriz.matriz FROM cov_casos LEFT JOIN cov_estatus ON cov_estatus.IDestatus = cov_casos.IDestatus LEFT JOIN cov_motivos ON cov_motivos.IDmotivo = cov_casos.IDmotivo LEFT JOIN prod_activos ON prod_activos.IDempleado = cov_casos.IDempleado LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = cov_casos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE cov_casos.IDcovid > 0 " . $d . $b . $c;
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
$value = $row_detalle['IDmotivo'];

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$matriz_actual'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos" . $a;
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

// actualizar 1
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$eIDmotivo = $_POST['IDmotivo'];
foreach($eIDmotivo as $el_motivo){ $eIDmotivo_aux[] = $el_motivo; } $valores = implode(',', $eIDmotivo_aux); 

$fecha_i = date("Y-m-d", strtotime($_POST['fecha_inicio']));
$fecha_f = date("Y-m-d", strtotime($_POST['fecha_final']));
$fecha_i2 = date("Y-d-m", strtotime($_POST['tratam_inicio']));
$fecha_f2 = date("Y-d-m", strtotime($_POST['tratam_fin']));

$captura = $_POST['IDcovid'];
$updateSQL = sprintf("UPDATE cov_casos SET IDempleado=%s, IDmotivo=%s, IDdoc_oficial=%s, IDestatus=%s, enfermedad_general=%s, fecha_inicio=%s, fecha_final=%s, IDreemplazo=%s, observaciones=%s, capturador=%s, fecha_captura=%s, enf_respiratoria=%s, tratam_inicio=%s, tratam_fin=%s WHERE IDcovid=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($valores, "text"),
                       GetSQLValueString($_POST['IDdoc_oficial'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['enfermedad_general'], "text"),
                       GetSQLValueString($fecha_i, "date"),
                       GetSQLValueString($fecha_f, "date"),
                       GetSQLValueString($_POST['IDreemplazo'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['enf_respiratoria'], "date"),
                       GetSQLValueString($fecha_i2, "date"),
                       GetSQLValueString($fecha_f2, "date"),
                       GetSQLValueString($_POST['IDcovid'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_covid_al.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
   header(sprintf("Location: %s", $updateGoTo));
}

//insertar 1
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$eIDmotivo = $_POST['IDmotivo'];
foreach($eIDmotivo as $el_motivo){ $eIDmotivo_aux[] = $el_motivo; } $valores = implode(',', $eIDmotivo_aux); 


$fecha_i = date("Y-m-d", strtotime($_POST['fecha_inicio']));
$fecha_f = date("Y-m-d", strtotime($_POST['fecha_final']));

$insertSQL = sprintf("INSERT INTO cov_casos (IDempleado, IDmotivo, IDdoc_oficial, IDestatus, enfermedad_general, fecha_inicio, fecha_final, IDreemplazo, observaciones, capturador, enf_respiratoria, tratam_inicio, tratam_fin, fecha_captura, IDmatriz, IDsucursal, IDarea, IDpuesto, emp_paterno, emp_materno, emp_nombre) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($valores, "text"),
                       GetSQLValueString($_POST['IDdoc_oficial'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['enfermedad_general'], "text"),
                       GetSQLValueString($fecha_i, "date"),
                       GetSQLValueString($fecha_f, "date"),
                       GetSQLValueString($_POST['IDreemplazo'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['enf_respiratoria'], "date"),
                       GetSQLValueString($_POST['tratam_inicio'], "date"),
                       GetSQLValueString($_POST['tratam_fin'], "date"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "admin_covid_al.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
 header(sprintf("Location: %s", $insertGoTo));
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>

    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>        
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
                
                        
               			<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 3)) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han restablecido el password correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                        

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido. Da clic en capturar para revizar o actualizar el caso identificado.</p>
							<p>Utiliza el filtro para mostrar a los empleados por puesto, sucursal o área. Utiliza el filtro rápido para buscar por cualquier campo.</p>
							<a href="admin_covid_reporte.php">Descarga aqui el reporte en Excel</a>
                   
                   
                       <form method="POST" action="admin_covid_al.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <div class="col-lg-9">
                                 <select name="la_matriz" class="form-control">
                                   <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz: Todas</option>
                                   <?php do {  ?>
                                   <option value="<?php echo $row_matriz['IDmatriz']?>"<?php if (!(strcmp($row_matriz['IDmatriz'], $la_matriz))) 
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_matriz['matriz']?></option>
                                   <?php
                                  } while ($row_matriz = mysql_fetch_assoc($matriz));
                                  $rows = mysql_num_rows($matriz);
                                  if($rows > 0) {
                                      mysql_data_seek($matriz, 0);
                                      $row_matriz = mysql_fetch_assoc($matriz);
                                  } ?></select>
						    </div>
                            </td>
							<td>
                            <div class="col-lg-9">
                                 <select name="el_puesto" class="form-control">
                                   <option value="" <?php if (!(strcmp("", $el_puesto))) {echo "selected=\"selected\"";} ?>>Puesto: Todos</option>
                                   <?php do {  ?>
                                   <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $el_puesto))) 
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_puesto['denominacion']?></option>
                                   <?php
                                  } while ($row_puesto = mysql_fetch_assoc($puesto));
                                  $rows = mysql_num_rows($puesto);
                                  if($rows > 0) {
                                      mysql_data_seek($puesto, 0);
                                      $row_puesto = mysql_fetch_assoc($puesto);
                                  } ?></select>
						    </div>
                            </td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                             </td>
					      </tr>
					    </tbody>
				    </table>
					</form>




				<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Acciones</th>
                      <th>NoEmp.</th>
                      <th>Nombre</th>
                      <th>Puesto</th>
                      <th>Matriz</th>
                      <th>Motivo Vulnerable</th>
                      <th>Estatus COVID</th>
                      <th>Enf. General</th>
                      <th>Reemp.</th>
               		 </tr>
                    </thead>
  					  <tbody>
                        <?php do { 	?>
                          <tr>
                            </td>
                            <td><button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>" 
                            data-toggle="modal" class="btn btn-info btn-icon">Actualizar</button></td>
                            <td><?php echo $row_detalle['IDempleado']; ?>&nbsp; </td>
                            <td><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?></td>
                            <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
                            <td><?php echo $row_detalle['matriz']; ?>&nbsp; </td>
                            <td><?php
									  if (strlen(strstr($row_detalle['IDmotivo'],'1')) > 0) {echo "* DIABETICO </br>";}
									  if (strlen(strstr($row_detalle['IDmotivo'],'2')) > 0) {echo "* HIPERTENSO </br>";}
									  if (strlen(strstr($row_detalle['IDmotivo'],'3')) > 0) {echo "* MAYOR A 60 </br>";}
									  if (strlen(strstr($row_detalle['IDmotivo'],'4')) > 0) {echo "* EMBARAZO </br>";}
									  if (strlen(strstr($row_detalle['IDmotivo'],'5')) > 0) {echo "* LACTANCIA ";}
									  if (strlen(strstr($row_detalle['IDmotivo'],'6')) > 0) {echo "* ENFERMEDAD ESPECIAL ";}
									  if (strlen(strstr($row_detalle['IDmotivo'],'7')) > 0) {echo "* OTRO ";}
									  ?></td>
                            <td><?php
								  switch ($row_detalle['IDestatus']) {
									case 0:  $el_estatus = "";      break;     
									case 1:  $el_estatus = "POSITIVO EN RECUPERACION";      break;     
									case 5:  $el_estatus = "POSITIVO RECUPERADO";      break;    
									case 13:  $el_estatus = "POSITIVO HOSPITALIZADO";      break;    
									case 2:  $el_estatus = "SOSPECHOSO";    break;    
									case 6:  $el_estatus = "SOSPECHOSO EN AISLAMIENTO";      break;    
									case 10:  $el_estatus = "SOSPECHOSO REINGRESADO";      break;    
									case 11:  $el_estatus = "SOSPECHOSO DECESO";      break;    
									case 12:  $el_estatus = "SOSPECHOSO HOSPITALIZADO";      break;    
									case 3:  $el_estatus = "POR CONTACTO";      break;    
									case 7:  $el_estatus = "POR CONTACTO EN AISLAMIENTO";      break;    
									case 4:  $el_estatus = "NO APLICA";      break;    
									case 8:  $el_estatus = "POR CONTACTO REINGRESADO";      break;    
									case 9:  $el_estatus = "POSITIVO DECESO";      break;    
									  }
								echo $el_estatus; ?></td>                                           
                            <td><?php if($row_detalle['enf_respiratoria'] == 1) { echo "SI";} ?></td>                                           
                            <td><?php if($row_detalle['IDreemplazo'] == 1) { echo "SI";} ?></td>                                           
    					</tr>
                         <?php  $moda1 = "assets/modals/inc_cov_al.php"; 	require($moda1); ?>
                          <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 
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