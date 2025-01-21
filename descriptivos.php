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
$IDmatrizes = $row_usuario['IDmatrizes'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

$fecha = date("Y-m-d");


$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_dps = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.tipo, vac_areas.IDarea, vac_areas.area, vac_puestos.descrito, sed_dps.fecha_actualizacion, prod_activos.IDmatriz, 	vac_puestos.IDaplica_PROD, vac_puestos.IDaplica_SED, vac_puestos.IDaplica_INC, vac_puestos.IDaplica_SIND  FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea  LEFT JOIN sed_dps ON sed_dps.IDpuesto = vac_puestos.IDpuesto LEFT JOIN prod_activos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = $IDmatriz AND vac_areas.IDarea IN ($mis_areas) AND vac_puestos.estatus = 1 GROUP BY  vac_puestos.denominacion ORDER BY vac_puestos.denominacion ASC"; 
mysql_query("SET NAMES 'utf8'");
$dps = mysql_query($query_dps, $vacantes) or die(mysql_error());
$row_dps = mysql_fetch_assoc($dps);
$totalRows_dps = mysql_num_rows($dps);


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
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Descriptivos</h5>
						</div>

					<div class="panel-body">
							Consulta las descripciones de los puestos registrados en el sistema.
					</div>

					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>IDPuesto</th>
							    <th>Denominación</th>
							    <th>Área</th>
							    <th>Fecha Act.</th>
							    <th>Productividad</th>
							    <th>Desempeño</th>
							    <th>Incentivos</th>
							    <th>Sindicato</th>
							    <th>Activos</th>
							    <th>Descarga</th>
						    </tr>
					    </thead>
						<tbody>							  
							<?php if ($totalRows_dps > 0){ ?>
							<?php do { ?>
							<tr>
							<td><?php echo $row_dps['IDpuesto']; ?></td>								
							<td><?php echo $row_dps['denominacion']; ?></td>								
							<td><?php echo $row_dps['area']; ?></td>								
	                        <td><?php if ($row_dps['fecha_actualizacion'] > 0) {echo date( 'd/m/Y' , strtotime($row_dps['fecha_actualizacion'])); } else {echo "Sin fecha";} ?></td>
							<td><?php if ($row_dps['IDaplica_PROD'] == 1) { echo 'Si'; } else { echo 'No';} ?></td>	
							<td><?php if ($row_dps['IDaplica_SED'] == 1) { echo 'Si'; } else { echo 'No';} ?></td>	
							<td><?php if ($row_dps['IDaplica_INC'] == 1) { echo 'Si'; } else { echo 'No';} ?></td>	
							<td><?php if ($row_dps['IDaplica_SIND'] == 1) { echo 'Si'; } else { echo 'No';} ?></td>	
							<td><?php
						  					 
							$IDpuesto = $row_dps['IDpuesto'];
							$query_ocupantes = "SELECT * FROM prod_activos WHERE IDpuesto = $IDpuesto AND IDmatriz = $IDmatriz";
							$ocupantes = mysql_query($query_ocupantes, $vacantes) or die(mysql_error());
							$row_ocupantes = mysql_fetch_assoc($ocupantes);
							$totalRows_ocupantes = mysql_num_rows($ocupantes);
													
							echo $totalRows_ocupantes; 
											  
							 ?></td>
							<td>
								<?php if ($row_dps['descrito'] == 3) {?>
								<a href="imprimir.php?IDpuesto=<?php echo $row_dps['IDpuesto'];?>" target="_blank" class="btn btn-danger btn-icon"><i class="icon-file-pdf"></i></a> 
								<a href="dps/imprimir_demo.php?IDpuesto=<?php echo $row_dps['IDpuesto'];?>" target="_blank" class="btn btn-success btn-icon"><i class="icon-file-excel"></i></a>
								<?php } else { echo 'Pendiente';} ?>
							</td>	
							</tr>
							<?php } while ($row_dps = mysql_fetch_assoc($dps)); ?>
							<?php } else { ?>
							<tr>
							<td colspan="5">No hay documentos cargados.</td>	
							</tr>
							<?php } ?>
                        </tbody>
                   </table> 
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