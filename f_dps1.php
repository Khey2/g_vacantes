<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1"); 
$restrict->addLevel("2");
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
$fecha = date("Y-m-d"); 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$user = $_SESSION['kt_login_user'];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto_1 = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE vac_puestos.IDpuesto = '$IDpuesto'";
mysql_query("SET NAMES 'utf8'");
$puesto_1 = mysql_query($query_puesto_1, $vacantes) or die(mysql_error());
$row_puesto_1 = mysql_fetch_assoc($puesto_1);
$totalRows_puesto_1 = mysql_num_rows($puesto_1);
$llave = $row_puesto_1['IDllave'];

mysql_select_db($database_vacantes, $vacantes);
$query_puesto_2 = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE prod_llave.IDllaveJ = '$llave'"; 	
$puesto_2 = mysql_query($query_puesto_2, $vacantes) or die(mysql_error());
$row_puesto_2 = mysql_fetch_assoc($puesto_2);
$totalRows_puesto_2 = mysql_num_rows($puesto_2);
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
	<script src="global_assets/js/core/libraries/jquery_ui/core.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/effects.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/extensions/cookie.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_all.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_childcounter.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/extra_trees.js"></script>
	<!-- /theme JS files -->
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">


                      	<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Actualizado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Borrado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

			<!-- Main content -->
			<div class="content-wrapper">

			<!-- Content area -->
			  <div class="content">
              
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Descriptivo de Puesto</h5>
						</div>

					<div class="panel-body">

							<h6 class="panel-title">Mi Descriptivo</h6>
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th class="col-xs-1">IDpuesto</th>
                          <th class="col-xs-4">Denominacion</th>
                          <th class="col-xs-3">Area</th>
                          <th class="col-xs-1">Puesto Tipo</th>
                          <th class="col-xs-1">Estatus</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  

                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_puesto_1['IDpuesto']; ?></td>
                          <td><?php echo $row_puesto_1['denominacion']; ?></td>
                          <td><?php echo $row_puesto_1['area']; ?></td>
                          <td><?php if ($row_puesto_1['tipo'] == 1) {echo "Si";} else {echo "No";} ?></td>
                          <td><?php if ($row_puesto_1['descrito'] == 1) {echo "Descrito";} else {echo "Pendiente";} ?></td>
                         <td>
                <button type="button" class="btn btn-primary" onClick="window.location.href='#?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Describir</button>
                <button type="button" class="btn btn-info" onClick="window.location.href='#?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Descargar</button>
                		</td>
                        </tr>                       
                        <?php } while ($row_puesto_1 = mysql_fetch_assoc($puesto_1)); ?>
                   	</tbody>							  
                 </table>
<p>&nbsp; </p>

							<h6 class="panel-title">Mis Colaboradores</h6>
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th class="col-xs-1">IDpuesto</th>
                          <th class="col-xs-4">Denominacion</th>
                          <th class="col-xs-3">Area</th>
                          <th class="col-xs-1">Puesto Tipo</th>
                          <th class="col-xs-1">Estatus</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php if($totalRows_puesto_2 > 0) { ?>
                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_puesto_2['IDpuesto']; ?></td>
                          <td><?php echo $row_puesto_2['denominacion']; ?></td>
                          <td><?php echo $row_puesto_2['area']; ?></td>
                          <td><?php if ($row_puesto_2['tipo'] == 1) {echo "Si";} else {echo "No";} ?></td>
                          <td><?php if ($row_puesto_2['descrito'] == 1) {echo "Descrito";} else {echo "Pendiente";} ?></td>
                         <td>
                <button type="button" class="btn btn-info" onClick="window.location.href='#?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Descargar</button>
                		</td>
                        </tr>                       
                        <?php } while ($row_puesto_2 = mysql_fetch_assoc($puesto_2)); ?>
                      <?php } else { ?>
                        <tr>
                          <td></td>
                          <td>No tiene colaboradores asignados.</td>
                          <td></td>
                          <td></td>
                          <td></td>
                         <td>
                		</td>
                        </tr>                       
                      <?php } ?>
                   	</tbody>							  
                 </table>
                    
                    
                    </div>

					<!-- /Contenido -->
                </div>
				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['n35/direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
			    </div>
				    <!-- /footer -->
                </div>
				<!-- /content area -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>