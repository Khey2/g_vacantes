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
mysql_query("SET NAMES 'utf8'"); 
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
mysql_query("SET NAMES 'utf8'"); 
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

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
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
    
	<!-- /theme JS files -->

</head>

<body class="<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>">

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

					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

                            <!-- Search field -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Administración - Manuales y Procedimientos</h5>
						</div>

						<div class="panel-body">
							Bienvenido, utiliza el buscador para encontrar el documento que necesitas.</br>
						
							
	<!-- Categories -->
	<div>
		<div class="category-title">
		</div>
		<?php
		// menu A
		$query_direcciones = "SELECT * FROM proced_direcciones";
		$direcciones = mysql_query($query_direcciones, $vacantes) or die(mysql_error());
		$row_direcciones = mysql_fetch_assoc($direcciones);
		$totalRows_direcciones = mysql_num_rows($direcciones);
		 ?>
			<ul>
				<?php do {  
		$la_direccion = $row_direcciones['IDdireccion'];
		$query_direcciones_count = "SELECT * FROM proced_documentos WHERE IDdireccion = $la_direccion AND IDDarea = '' AND IDsubarea = ''";
		$direcciones_count = mysql_query($query_direcciones_count, $vacantes) or die(mysql_error());
		$row_direcciones_count = mysql_fetch_assoc($direcciones_count);
		$totalRows_direcciones_count = mysql_num_rows($direcciones_count);
				?>

						<li>
							<strong><a style="color:#352C2C;" href="admin_proced.php?IDdireccion=<?php echo $row_direcciones['IDdireccion'] ?>"><i class="icon-forward3"></i> <?php echo $row_direcciones['direccion'] ?></a> (<?php echo $totalRows_direcciones_count; ?>)</strong>
							
			<?php
			// menu B
			$la_direccion = $row_direcciones['IDdireccion'];
			$query_areas = "SELECT * FROM proced_areas WHERE IDdireccion = $la_direccion";
			$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
			$row_areas = mysql_fetch_assoc($areas);
			$totalRows_areas = mysql_num_rows($areas);
			 ?>
							<ul>
					<?php if ($totalRows_areas > 0) { do { 
		$la_area = $row_areas['IDDarea'];
		$query_areas_count = "SELECT * FROM proced_documentos WHERE IDdireccion = $la_direccion AND IDDarea = $la_area AND IDsubarea = ''";
		$areas_count = mysql_query($query_areas_count, $vacantes) or die(mysql_error());
		$row_areas_count = mysql_fetch_assoc($areas_count);
		$totalRows_areas_count = mysql_num_rows($areas_count);
					?>
								<li>
									<a href="admin_proced.php?IDdireccion=<?php echo $row_direcciones['IDdireccion'] ?>&IDDarea=<?php echo $row_areas['IDDarea'] ?>"><i class="icon-arrow-right5"></i> <?php echo $row_areas['area'] ?> (<?php echo $totalRows_areas_count; ?>)</a> 
									
				<?php
				// menu C
				$el_area = $row_areas['IDDarea'];
				$query_subarea = "SELECT * FROM proced_subareas WHERE IDDarea = $el_area";
				$subarea = mysql_query($query_subarea, $vacantes) or die(mysql_error());
				$row_subarea = mysql_fetch_assoc($subarea);
				$totalRows_subarea = mysql_num_rows($subarea);
				 ?>
						<ul><?php if ($totalRows_subarea > 0) { do {  
		$la_subarea = $row_subarea['IDsubarea'];
		$query_subareas_count = "SELECT * FROM proced_documentos WHERE IDdireccion = $la_direccion AND IDDarea = $la_area AND IDsubarea = $la_subarea";
		$subareas_count = mysql_query($query_subareas_count, $vacantes) or die(mysql_error());
		$row_subareas_count = mysql_fetch_assoc($subareas_count);
		$totalRows_subareas_count = mysql_num_rows($subareas_count);
						?>
									
									<li><i class="icon-arrow-right22"></i> <a href="admin_proced.php?IDdireccion=<?php echo $row_direcciones['IDdireccion'] ?>&IDDarea=<?php echo $row_areas['IDDarea'] ?>&IDsubarea=<?php echo $row_subarea['IDsubarea'] ?>"><?php echo $row_subarea['subarea'] ?> (<?php echo $totalRows_subareas_count; ?>)</a></li>
									
						<?php }  while ($row_subarea = mysql_fetch_assoc($subarea));  }?></ul>
									
								</li>
					<?php } while ($row_areas = mysql_fetch_assoc($areas)); ?>
							</ul>
							
						</li>
						
					<?php }  } while ($row_direcciones = mysql_fetch_assoc($direcciones)); ?>
			</ul>
		
		</div>
	<!-- /categories -->
							
							
							
							
							
							
							
							
							
						</div>
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