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

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$fecha = date("Y-m-d");
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if(isset($_POST['el_area']) && ($_POST['el_area']  > 0)) {
$_SESSION['el_area'] = $_POST['el_area']; }  else { $_SESSION['el_area'] = "";}

if(isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; }  else { $_SESSION['la_matriz'] = $la_matriz;}

$el_area = $_SESSION['el_area'];
$la_matriz = $_SESSION['la_matriz'];

if($el_area > 0) {
$a1 = " AND vac_areas.IDarea = '$el_area'"; } else {$a1 = "";}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_bmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$bmatriz = mysql_query($query_bmatriz, $vacantes) or die(mysql_error());
$row_bmatriz = mysql_fetch_assoc($bmatriz);
$totalRows_bmatriz = mysql_num_rows($bmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_tabulador = "SELECT vac_tabulador.*, vac_matriz.matriz, vac_areas.area, vac_puestos.denominacion FROM vac_tabulador LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_tabulador.IDpuesto LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = vac_tabulador.IDmatriz LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE vac_tabulador.IDmatriz = '$la_matriz' " . $a1 ;
$tabulador = mysql_query($query_tabulador, $vacantes) or die(mysql_error());
$row_tabulador = mysql_fetch_assoc($tabulador);
$totalRows_tabulador = mysql_num_rows($tabulador);
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->


</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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
							Se ha agregado correctamente el tabulador.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-info-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el tabulador.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                                						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el tabulador.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Tabulador de Sueldos</h5>
						</div>

					<div class="panel-body">
            <p>Selecciona el Tabulador para editarlo. Utiliza el filto para cambiar de Sucursal.</p>
            <p>Sucursal: <strong><strong><?php echo $row_bmatriz['matriz']; ?></strong></strong></p>

                    <form method="POST" action="admin_tabulador.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                                             <select name="el_area" class="form-control">
                                               <option value="" <?php if (!(strcmp("", $el_area))) {echo "selected=\"selected\"";} ?>>Área: Todas</option>
											<?php do { ?>
                                               <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $el_area))) {echo "selected=\"selected\"";} ?>>
											   <?php echo $row_area['area']?></option>
                                               <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) {
												  mysql_data_seek($area, 0);
												  $row_area = mysql_fetch_assoc($area);
											  } ?> </select>
						   </td>
							<td>
                                             <select name="la_matriz" class="form-control">
                                               <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz: Actual</option>
											<?php do { ?>
                                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>>
											   <?php echo $row_lmatriz['matriz']?></option>
                                               <?php
											  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
											  $rows = mysql_num_rows($lmatriz);
											  if($rows > 0) {
												  mysql_data_seek($lmatriz, 0);
												  $row_lmatriz = mysql_fetch_assoc($lmatriz);
											  } ?> </select>
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
							    <th>IDPuesto</th>
							    <th>Área</th>
							    <th>Denominación</th>
							    <th>Nivel</th>
							    <th>Sueldo Mensual</th>
							    <th>Sueldo Diario</th>
							    <th>Sueldo Dirio Integrado</th>
							    <th>Acciones</th>
						    </tr>
					    </thead>
						<tbody>		
                        	<?php do { ?>				  
							<tr>
							<td><?php echo $row_tabulador['IDpuesto']; ?>&nbsp; </td>								
							<td><?php echo $row_tabulador['area']; ?>&nbsp; </td>								
							<td><?php echo $row_tabulador['denominacion']; ?>&nbsp; </td>								
							<td><?php echo $row_tabulador['IDnivel']; ?>&nbsp; </td>								
							<td><?php echo "$" .number_format($row_tabulador['sueldo_mensual'],2); ?>&nbsp; </td>								
							<td><?php echo "$" .number_format($row_tabulador['sueldo_diario'],2); ?>&nbsp; </td>								
							<td><?php echo "$" .number_format($row_tabulador['sueldo_integrado'],2); ?>&nbsp; </td>								
							<td><a href="admin_tabulador_edit.php?IDtabulador=<?php echo $row_tabulador['IDtabulador']; ?>" class="btn btn-success">Editar</a></td>								
							</tr>
                    <?php } while ($row_tabulador = mysql_fetch_assoc($tabulador)); ?>
                        </tbody>
                   </table> 
                   
					<!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-primary" href="admin_tabulador_edit.php">Agregar Tabulador<i class="icon-arrow-right14 position-right"></i></a>
                    </div>
					</div>
					<!-- /colored button -->                   
                   
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


</body>
</html>