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
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
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
$semana_inicio = $semana - 8;
if ($semana_inicio < 1){$semana_inicio = 1;}
$semana_fin = $semana;

//las variables de sesion para el filtrado
if(isset($_POST['la_matriz']) && $_POST['la_matriz'] != 0) { $_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = '1,2,3,4,5,6,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,28,29,30'; } 
$la_matriz = $_SESSION['la_matriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea, prod_activos.IDaplica_SED, pc_semaforo.a_discprog, pc_semaforo.IDplan, pc_semaforo.estatus_pc,  pc_semaforo.estatus,  pc_semaforo.avance_pc,  vac_puestos.plan_carrera FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto WHERE prod_activos.IDmatriz in ($la_matriz) AND pc_semaforo.reqe = 1 AND pc_semaforo.reqb <> 0 ";   
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

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
$IDempleado = $_POST["IDempleado"];

$updateSQL = sprintf("UPDATE pc_semaforo SET a_discprog=%s, a_puntyasist=%s, a_desemp=%s, a_antig=%s, b_puesto=%s, c_capa1=%s, c_capa2=%s,  c_capa3=%s, a_discprog_c=%s, a_puntyasist_c=%s, a_desemp_c=%s, a_antig_c=%s, b_puesto_c=%s, c_capa1_c=%s, c_capa2_c=%s, c_capa3_c=%s,  estatus_pc=%s WHERE IDempleado = '$IDempleado'",
                       GetSQLValueString(isset($_POST['a_discprog']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['a_puntyasist']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['a_desemp']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['a_antig']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['b_puesto']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['c_capa1']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['c_capa2']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['c_capa3']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['a_discprog_c'], "text"),
                       GetSQLValueString($_POST['a_puntyasist_c'], "text"),
                       GetSQLValueString($_POST['a_desemp_c'], "text"),
                       GetSQLValueString($_POST['a_antig_c'], "text"),
                       GetSQLValueString($_POST['b_puesto_c'], "text"),
                       GetSQLValueString($_POST['c_capa1_c'], "text"),
                       GetSQLValueString($_POST['c_capa2_c'], "text"),
                       GetSQLValueString($_POST['c_capa3_c'], "text"),
                       GetSQLValueString($_POST['estatus_pc'], "text"),
					   GetSQLValueString($_POST['IDempleado'], "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "plan_carrera_carr_upd.php?IDempleado='$IDempleado'";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
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
							<h5 class="panel-title">Plan de Carrera</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido. En esta sección puedes el estatus del Plan de Carrera. Los empleados marcados con <span class='text-danger'>*</span> son empelados con alto potencial.</br>
                                Recuerda que debes mantener actualizado el avance de personal.</p>
                            <p>Para cualquier duda relacionada con el Plan de Carrera, contacta a Marco Antonio Hernández al correo <a href="mailto:mahernandez@sahuayo.mx">mahernandez@sahuayo.mx</a></p>


                   <form method="POST" action="admin_plan_carrera.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <div class="col-lg-9">
                             <select name="la_matriz" class="form-control">
                               <option value="0" <?php if (!(strcmp("0", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz: Todas</option>
                               <?php do {  ?>
                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
							   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                               <?php
                              } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                              $rows = mysql_num_rows($lmatriz);
                              if($rows > 0) {
                                  mysql_data_seek($lmatriz, 0);
                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
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
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>Fecha de Alta</th>
                      <th>Avance</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php if ($totalRows_detalle > 0) { ?>
						<?php do {  ?>
                        <tr>
                            <td><?php echo $row_detalle['IDempleado']; ?>&nbsp; </td>
                            <td><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre'];  if( $row_detalle['estatus'] > 12)
							{ echo "<span class='text-danger'>*</span>";} ?></td>
                            <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
                            <td><?php echo date('d/m/Y', strtotime($row_detalle['fecha_alta'])); ?></td>
                            <td><?php if($row_detalle['avance_pc'] > 0) {echo $row_detalle['avance_pc'];} else {echo '0';} ?>%</td>
                            <?php if ($row_detalle['estatus_pc'] > 0 ) {  ?>
                            <td><div onClick="loadDynamicContentModal2('<?php echo $row_detalle['IDempleado']; ?>')" class="btn btn-info btn-icon">Actualizar</div></td>
                        	<?php } else {  ?>
                            <td><div onClick="loadDynamicContentModal2('<?php echo $row_detalle['IDempleado']; ?>')" class="btn btn-info btn-icon">Capturar</div></td>
                        	<?php } ?>
                        </tr>
                          <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
						<?php } else {  ?>
                        <td colspan="8">Sin empleados registrados, revisa la sección de inventario.</td>
						<?php }  ?>
                    </tbody>
                   </table> 
					  </div>


                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Plan de Carrera</h5>
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
function loadDynamicContentModal2(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('plan_carrera_mdl2.php?IDempleado=' + modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>