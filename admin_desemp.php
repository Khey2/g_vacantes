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

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

if (isset($_POST['la_matriz'])) {foreach ($_POST['la_matriz'] as $matris)  {	$_SESSION['la_matriz'] = implode(",", $_POST['la_matriz']);}}  else { $_SESSION['la_matriz'] = 7;} 
$la_matriz = $_SESSION['la_matriz'];


$IDperiodo = $_SESSION['IDperiodo'];
$query_periodo_ac = "SELECT * FROM sed_periodos_sed WHERE IDperiodo = $IDperiodo"; 
$periodos_ac = mysql_query($query_periodo_ac, $vacantes) or die(mysql_error());
$row_periodos_ac = mysql_fetch_assoc($periodos_ac);
$totalRows_periodos_ac = mysql_num_rows($periodos_ac);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, sed_individuales_resultados.estatus, sed_individuales_resultados.IDresultado, sed_individuales_resultados.especial, sed_individuales_resultados.resultado, sed_individuales_resultados.IDperiodo, vac_matriz.matriz, vac_areas.area FROM prod_activos LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON prod_activos.IDarea = vac_areas.IDarea LEFT JOIN sed_individuales_resultados ON prod_activos.IDempleado = sed_individuales_resultados.IDempleado AND sed_individuales_resultados.IDperiodo =  $IDperiodo WHERE prod_activos.IDmatriz IN ($la_matriz)  ORDER BY prod_activos.IDempleado ASC";  
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($la_matriz)";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if ((isset($_GET["borrar"])) && ($_GET["borrar"] == "1")) {
$IDresultado = $_GET["IDresultado"];
$updateSQL = "DELETE FROM sed_individuales_resultados WHERE IDresultado = $IDresultado";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header('Location: admin_desemp.php?info=9');
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
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html99.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el resultado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<ul>
							<li>Selecciona el resultado que requiera editar.</li>
							<li>Especial puede evaluar aún cuando esté cerrado el Periodo.</li>
							<li>Periodo Actual: <strong><?php echo $row_periodos_ac['periodo'];  ?> </strong></li>
							<li>Matrices Actuales: <strong><?php
							$myString = '';
							do { $myString = $myString." ".$row_matriz['matriz'].","; } while ($row_matriz = mysql_fetch_assoc($matriz));
							$myString = substr($myString, 0, -1);
							echo $myString.". ";  // 'number 1, number 2, number 3'
							?>

							</strong></li>
							</ul>
					</div>
                    
                    
                    
                    <form method="POST" action="admin_desemp.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <strong>Periodo:</strong>
                                             <select name="IDperiodo" class="form-control">
                                               <?php do {  ?>
                                               <option value="<?php echo $row_periodos['IDperiodo']?>"<?php if (!(strcmp($row_periodos['IDperiodo'], $IDperiodo))) {echo "selected=\"selected\"";} ?>><?php echo $row_periodos['periodo']?></option>
                                               <?php
											  } while ($row_periodos = mysql_fetch_assoc($periodos));
											  $rows = mysql_num_rows($periodos);
											  if($rows > 0) {
												  mysql_data_seek($periodos, 0);
												  $row_periodos = mysql_fetch_assoc($periodos);
											  } ?></select>
                            </td>
							<td>
                            <strong>Matrices: </strong>
                             <select class="multiselect" multiple="multiple" name="la_matriz[]">
											<?php $cadena = $la_matriz; $array = explode(",", $cadena);
											do { ?>
											   <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php foreach ($array as $lematriz) { if (!(strcmp($row_lmatriz['IDmatriz'], $lematriz))) {echo "selected=\"selected\"";} } ?>><?php echo $row_lmatriz['matriz']?></option>
											   <?php
											  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
											  $rows = mysql_num_rows($lmatriz);
											  if($rows > 0) { mysql_data_seek($lmatriz, 0);
											  $row_lmatriz = mysql_fetch_assoc($lmatriz); 
											  } ?> 
                                         </select>	
                            </td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>

                    
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>Nombre</th>
                          <th>IDempleado</th>
                          <th>Matriz</th>
                          <th>Area</th>
                          <th>Puesto</th>
                          <th>Estatus</th>
                          <th>Calificacion</th>
                          <th>Especial</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_resultados['emp_paterno'] . " " . $row_resultados['emp_materno'] . " " . $row_resultados['emp_nombre']; ?></td>
                          <td><?php echo $row_resultados['IDempleado']; ?></td>
                          <td><?php echo $row_resultados['matriz']; ?></td>
                          <td><?php echo $row_resultados['area']; ?></td>
                          <td><?php echo $row_resultados['denominacion']; ?></td>
                          <td><?php      if ($row_resultados['estatus'] == 0) {echo "Sin Captura";} 
									else if ($row_resultados['estatus'] == 1) {echo "Capturado";} 
									else if ($row_resultados['estatus'] == 2) {echo "Propuesto";} 
									else if ($row_resultados['estatus'] == 3) {echo "Evaluado";} 
									else {echo "Sin Evaluacion";}
							?></td>
                          <td><?php echo $row_resultados['resultado']; ?></td>
                          <td><?php if ($row_resultados['especial'] == 1)  {echo "No";} else if ($row_resultados['especial'] == 2) {echo "Si";} else { echo "No";}?></td>
                          <td>
                         <button type="button" class="btn btn-info" onClick="window.location.href='admin_indiv_edit.php?IDperiodo=<?php echo $IDperiodo; ?>&id=1&IDempleado=<?php echo $row_resultados['IDempleado']; ?>'">Evalua</button> 
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_indiv_capt.php?IDperiodo=<?php echo $IDperiodo; ?>&id=1&IDempleado=<?php echo $row_resultados['IDempleado']; ?>'">Captura</button> 
						 <button type="button" class="btn btn-success" onClick="window.location.href='admin_desemp_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>'">Calif.</button>
						<button type="button" data-target="#modal_theme_danger<?php echo $row_resultados['IDresultado']; ?>"  data-toggle="modal" class="btn btn-danger">Borrar</button>						 
						 </td>
						</tr>     


                     <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_resultados['IDresultado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Cierre</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el resultado?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_desemp.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->



						
                        <?php } while ($row_resultados = mysql_fetch_assoc($resultados)); ?>
                   	</tbody>							  
                 </table>

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