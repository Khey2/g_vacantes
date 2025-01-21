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

$currentPage = $_SERVER["PHP_SELF"];
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
$fechapp = date("YmdHis"); // la fecha actual

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


if(isset($_POST['IDmatriz']) && ($_POST['IDmatriz']  > 0)) {
$_SESSION['IDmatriz'] = $_POST['IDmatriz']; } else { $_SESSION['IDmatriz'] = $IDmatrizes;}

$la_matriz = $_SESSION['IDmatriz'];
$IDestatus_f = $_SESSION['IDestatus_f']; 
$el_anio = $_SESSION['el_anio'];

mysql_select_db($database_vacantes, $vacantes);
$query_pprueba = "SELECT pp_prueba.file, pp_prueba.IDestatusv, pp_prueba.file2, pp_prueba.file3, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDarea, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, pp_prueba.IDmatriz_destino, pp_prueba.IDarea_destino, pp_prueba.fecha_fin, pp_prueba.fecha_inicio, pp_prueba.fecha_cierre, pp_prueba.IDestatus, pp_prueba.observaciones, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, puesto_origen.denominacion AS denominacion_origen, area_oringen.area AS area_origen, matriz_origen.matriz AS matriz_origen, matriz_destino.matriz as matriz_destino, area_destino.area AS area_destino, puesto_destino.denominacion AS denominacion_destino FROM pp_prueba LEFT JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos AS puesto_origen ON pp_prueba.IDpuesto = puesto_origen.IDpuesto LEFT JOIN vac_areas AS area_oringen ON puesto_origen.IDarea = area_oringen.IDarea LEFT JOIN vac_matriz AS matriz_origen ON pp_prueba.IDmatriz = matriz_origen.IDmatriz LEFT JOIN vac_matriz AS matriz_destino ON pp_prueba.IDmatriz_destino = matriz_destino.IDmatriz LEFT JOIN vac_puestos AS puesto_destino ON pp_prueba.IDpuesto_destino = puesto_destino.IDpuesto LEFT JOIN vac_areas AS area_destino ON puesto_destino.IDarea = area_destino.IDarea WHERE pp_prueba.IDmatriz IN ($la_matriz) AND prod_activos.emp_paterno IS NOT NULL"; 
mysql_query("SET NAMES 'utf8'");
$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
	
// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
    $borrado = $_GET['IDpprueba'];
    $deleteSQL = "DELETE FROM pp_prueba  WHERE IDpprueba ='$borrado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_pprueba_borrar.php?info=3");
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<!-- /theme JS files -->
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
							Se ha agregado correctamente el Periodo de Prueba.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el Periodo de Prueba.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 5))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha autorizado correctamente el Periodo de Prueba.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Periodos de Prueba</h5>
								</div>

								<div class="panel-body">
								<p>Borraro de Periodos de Prueba.</p>
                                
                    <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    
                    </div>
					</div>
					<!-- /colored button -->
	

<form method="POST" action="admin_pprueba_borrar.php">
<table class="table">
<tbody>							  
	<tr>
	<td><div class="col-lg-9">Matriz
		 <select name="IDmatriz" class="form-control">
         <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>TODAS</option>
		   <?php do {  ?>
		   <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
		   <?php
		  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
		  $rows = mysql_num_rows($lmatriz);
		  if($rows > 0) {
			  mysql_data_seek($lmatriz, 0);
			  $row_lmatriz = mysql_fetch_assoc($lmatriz);
		  } ?>
		  </select>
	</div></td>
	<td>
	<button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right13 position-right"></i></button>
	</td>
  </tr>
</tbody>
</table>
</form>



								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>IDPP</th>
                                    <th>No. Emp.</th>
                                    <th>Matriz</th>
                                    <th>Nombre</th>
                                    <th>Puesto Destino</th>
                                    <th>Inicio</th>
                                    <th>Termino</th>
                                    <th>Estatus</th>
                                    <th>Autorizado</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                    <td><?php echo $row_pprueba['IDpprueba']; ?></td>
                                    <td><?php echo $row_pprueba['IDempleado'];?></td>
                                      <td><?php echo $row_pprueba['matriz_origen'];?></td>
                                      <td><?php echo $row_pprueba['emp_paterno'] . " " . $row_pprueba['emp_materno'] . " " . $row_pprueba['emp_nombre'];?></td>
                                      <td><?php echo $row_pprueba['denominacion_destino']; ?></td>
                                      <td><?php echo date("d-m-Y",strtotime($row_pprueba['fecha_inicio']));  ?></td>
                                      <td><?php echo date("d-m-Y",strtotime($row_pprueba['fecha_fin']));  ?></td>
                                      <td><?php if ($row_pprueba['IDestatus'] == 1) {echo "Captura incompleta";} 
									  else if ($row_pprueba['IDestatus'] == 2) {echo "Cargar desempeño";} 
									  else if ($row_pprueba['IDestatus'] == 3) {echo "Capturar pagos";} 
									  else if ($row_pprueba['IDestatus'] == 3 and $totalRows_pagos > 0) {echo "Imprimir notificación";} 
									  else if ($row_pprueba['IDestatus'] == 4 and $row_pprueba['file2'] != '') {echo "Cargar notificación firmada";} 
									  else if ($row_pprueba['IDestatus'] == 5 or $row_pprueba['IDestatus'] == 4) {echo "Imprimir afectación";} 
									  else if ($row_pprueba['IDestatus'] == 6) {echo "Terminado";} 
									   ?></td>
                                      <td><?php if ($row_pprueba['IDestatusv'] == 1) {echo "Autorizado";}
											elseif ($row_pprueba['IDestatusv'] == 2) {echo "En proceso";} 
											else   { echo "Pendiente";} ?></td>
									  <td><button type="button" data-target="#modal_theme_danger4<?php echo $row_pprueba['IDpprueba']; ?>"  data-toggle="modal" class="btn btn-xs btn-danger"><i class="icon-trash"></i></button>
									  </td>
                                    </tr>
									
									
					<!-- danger modal -->
					<div id="modal_theme_danger4<?php echo $row_pprueba['IDpprueba']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>
								
								<form method="post" id="form1" action="admin_pprueba.php">

								<div class="modal-body">


                                ¿Estas seguro que quieres borrar el Periodo de prueba <b><?php echo $row_pprueba['IDpprueba']; ?></b>?:<br/>
                                <b>Empleado: </b><?php echo $row_pprueba['emp_paterno']." ".$row_pprueba['emp_materno']." ".$row_pprueba['emp_nombre'];?> (No.Emp. <?php echo $row_pprueba['IDempleado'];?>)<br/>
                                <b>Puesto: </b><?php echo $row_pprueba['denominacion_destino']; ?>.

                                </div>

								<div class="modal-footer">
                                <a class="btn btn-danger" href="admin_pprueba_borrar.php?IDpprueba=<?php echo $row_pprueba['IDpprueba']; ?>&borrar=1">Si borrar</a>
						 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						 <input  type="hidden" name="IDpprueba" id="IDpprueba" value="<?php echo  $row_pprueba['IDpprueba']; ?>" />
                       </form>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->

									
                                    <?php } while ($row_pprueba = mysql_fetch_assoc($pprueba)); ?>                                    
                                  </tbody>
                                </table>
								</div>
							</div>
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