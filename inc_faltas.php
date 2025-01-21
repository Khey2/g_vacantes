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
mysql_query("SET NAMES 'utf8'");
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
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
if($semana < 9) { $semana_inicio = 01; } else {$semana_inicio = $semana - 8;}
$semana_fin = $semana;


if(isset($_POST['el_area'])) {$_SESSION['el_area'] = $_POST['el_area']; } 
else if(!isset($_SESSION['el_area'])) {$_SESSION['el_area'] = 0; } else {$_SESSION['el_area'] = 0; }
$el_area = $_SESSION['el_area'];

$c1 = "";
if($el_area > 0) {
$c1 = " AND prod_activos.IDarea = $el_area"; }

//las variables de sesion para el filtrado

if (isset($_POST['la_matriz'])) {	foreach ($_POST['la_matriz'] as $empres)
	{	$_SESSION['la_matriz'] = implode(",", $_POST['la_matriz']);}	}  else { $_SESSION['la_matriz'] = $IDmatriz;}
$la_matriz = $_SESSION['la_matriz'];

set_time_limit(0);

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.IDmatriz,  prod_activos.IDarea, prod_activos.IDaplica_INC, vac_matriz.matriz, inc_faltas.justificacion, inc_faltas.dias_menos FROM prod_activos INNER JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz left JOIN inc_faltas ON prod_activos.IDempleado = inc_faltas.IDempleado WHERE prod_activos.IDmatriz in ( $la_matriz ) AND prod_activos.IDaplica_INC = 1 ".$c1; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea IN (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
if ($_POST['justificacion'] == ''){$justificacion = '';} else {$justificacion = $_POST['justificacion'];}
if ($_POST['dias_menos'] == ''){$dias_menos = '';} else {$dias_menos = $_POST['dias_menos'];}
$el_empleado = $_GET['IDempleado'];

$updateSQL = "UPDATE inc_faltas SET justificacion = '$justificacion', dias_menos = '$dias_menos' WHERE IDempleado= '$el_empleado'";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "inc_faltas.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
if ($_POST['justificacion'] == ''){$justificacion = '';} else {$justificacion = $_POST['justificacion'];}
if ($_POST['dias_menos'] == ''){$dias_menos = '';} else {$dias_menos = $_POST['dias_menos'];}
$el_empleado = $_GET['IDempleado'];

$insertSQL = "INSERT INTO inc_faltas (IDempleado, justificacion, dias_menos) VALUES ('$el_empleado', '$justificacion', '$dias_menos')";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "inc_faltas.php?info=1";
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
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
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


	                <!-- Content area -->
				<div class="content">
                
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 1) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente la justificacion.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte de Faltas por Empleado</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido. En esta sección puedes consultar las faltas de los empleados en las últimas 8 semanas (de la semana <?php echo $semana_inicio; ?> a la <?php echo $semana_fin; ?>).</p>
							<p>Da clic en justificar para indicar los días que no aplican como faltas.</p>
							<p>Los empelados marcados con <i class='icon-checkmark-circle text-danger position-left'></i>deben ser sujetos de disciplina progresiva.</p>
							<p><a href="inc_faltas_detalle.php" class="text-semibold">Da clic aqui para ver el detalle por semana.</a></p>

                       <form method="POST" action="inc_faltas.php">

					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <div class="col-lg-9">
                                 <select class="form-control" name="el_area">
                                   <option value="0"<?php if (!(strcmp($el_area, 0))) {echo "selected=\"selected\"";} ?>>TODAS</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_area['area']?></option>
                                   <?php
                                  } while ($row_area = mysql_fetch_assoc($area));
                                  $rows = mysql_num_rows($area);
                                  if($rows > 0) {
                                      mysql_data_seek($area, 0);
                                      $row_area = mysql_fetch_assoc($area);
                                  } ?> </select>
						    </div>
                            </td>
							<td><div class="col-lg-9">
                                             <select class="multiselect" multiple="multiple" name="la_matriz[]">
											<?php do { ?>
                                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                                               <?php
											  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
											  $rows = mysql_num_rows($lmatriz);
											  if($rows > 0) {
												  mysql_data_seek($lmatriz, 0);
												  $row_lmatriz = mysql_fetch_assoc($lmatriz);
											  } ?> </select>
						    </div></td>
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
                      <th>Matriz</th>
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>Fecha de Alta</th>
                      <th>Faltas</th>
                      <th>Días Justif.</th>
                      <th>Justificacion</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php do { $IDempleado = $row_detalle['IDempleado'];
                        $query_faltas = "SELECT
                            sum( CASE WHEN prod_captura.lun = 0 THEN 1 ELSE 0 END ) AS Falta_lun,
                            sum( CASE WHEN prod_captura.mar = 0 THEN 1 ELSE 0 END ) AS Falta_mar,
                            sum( CASE WHEN prod_captura.mie = 0 THEN 1 ELSE 0 END ) AS Falta_mie,
                            sum( CASE WHEN prod_captura.jue = 0 THEN 1 ELSE 0 END ) AS Falta_jue,
                            sum( CASE WHEN prod_captura.vie = 0 THEN 1 ELSE 0 END ) AS Falta_vie,
                            sum( CASE WHEN prod_captura.sab = 0 THEN 1 ELSE 0 END ) AS Falta_sab
                        FROM prod_captura WHERE prod_captura.anio = '$anio' AND prod_captura.IDempleado = '$IDempleado' AND 
                            (prod_captura.semana >= $semana_inicio AND prod_captura.semana <= $semana_fin)";   

                        $faltas = mysql_query($query_faltas, $vacantes) or die(mysql_error());
                        $row_faltas = mysql_fetch_assoc($faltas);
						
                        $Faltas = $row_faltas['Falta_lun'] +  $row_faltas['Falta_mar'] + $row_faltas['Falta_mie']
                        + $row_faltas['Falta_jue'] + $row_faltas['Falta_vie'] + $row_faltas['Falta_sab'] - $row_detalle['dias_menos'];
						 ?>
						<?php if ($Faltas > 0) { ?>
                        <tr>
                            <td><?php echo $row_detalle['matriz']; ?>&nbsp; </td>
                            <td><?php echo $row_detalle['IDempleado']; ?>&nbsp; </td>
                            <td><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?></td>
                            <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
                            <td><?php echo date('d/m/Y', strtotime($row_detalle['fecha_antiguedad'])); ?></td>
                            <td><?php echo $Faltas; if($Faltas >= 4 ){echo " <i class='icon-checkmark-circle text-danger position-left'></i>";}?></td>
                            <td><?php echo $row_detalle['dias_menos']; ?>&nbsp; </td>
                            <td><?php echo $row_detalle['justificacion']; ?>&nbsp; </td>
                            <td><?php if ($row_detalle['justificacion'] != '') { ?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>')" class="btn btn-success btn-icon">Justificado</div>
							<?php } else { ?>
                            <div onClick="loadDynamicContentModal('<?php echo $row_detalle['IDempleado']; ?>')" class="btn btn-info btn-icon">Justificar</div>
                            <?php } ?>
							</td>
                        </tr>
                          <?php }  ?>
                          <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 
					  </div>


                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Captura de justificación de Faltas</h5>
								</div>
							<div class="modal-body">
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->


					</div>
					</div>
					<!-- /panel heading options -->


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
<script>
function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('inc_faltas_mdl.php?IDempleado=' + modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>