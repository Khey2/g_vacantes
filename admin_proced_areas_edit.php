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
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$IDdireccion = $_GET['IDdireccion'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_direcciones = "SELECT * FROM proced_direcciones";
mysql_query("SET NAMES 'utf8'");
$direcciones = mysql_query($query_direcciones, $vacantes) or die(mysql_error());
$row_direcciones = mysql_fetch_assoc($direcciones);
$totalRows_direcciones = mysql_num_rows($direcciones);

if(isset($_GET['IDDarea'])) {
	
$IDDarea = $_GET['IDDarea'];
mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT proced_areas.area, proced_areas.IDdireccion, proced_areas.IDDarea, proced_direcciones.direccion FROM proced_areas left JOIN proced_direcciones ON proced_areas.IDdireccion = proced_direcciones.IDdireccion WHERE proced_areas.IDDarea = '$IDDarea'";
mysql_query("SET NAMES 'utf8'"); 
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$updateSQL = sprintf("UPDATE proced_areas SET area=%s WHERE IDDarea=%s",
                       GetSQLValueString($_POST['area'], "text"),
                       GetSQLValueString($_POST['IDDarea'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_proced_areas.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));  
} 

else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO proced_areas (IDdireccion, area) VALUES (%s, %s)",
                       GetSQLValueString($IDdireccion, "int"),
                       GetSQLValueString($_POST['area'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "admin_proced_areas.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] != "")) {
  
  $borrado = $_GET['IDDarea'];
  $IDdireccion = $_GET['IDdireccion'];
  $deleteSQL = "DELETE FROM proced_areas WHERE IDDarea ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_proced_areas.php?IDdireccion=".$IDdireccion."&info=3");
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>

	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/login_validation.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<!-- /theme JS files -->

</head>

<body class="has-detached-right">	<?php require_once('assets/mainnav.php'); ?>
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
							<h5 class="panel-title">Editar / Actualizar Área</h5>
						</div>

					<div class="panel-body">
									<?php if (isset($_GET['IDDarea'])) {?>


							<p>Actualiza la información.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">


                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDdireccion" id="IDdireccion" class="form-control" required="required" disabled="disabled">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_direcciones['IDdireccion']?>"<?php if (!(strcmp($row_direcciones['IDdireccion'], $IDdireccion))) {echo "SELECTED";} ?>><?php echo $row_direcciones['direccion']?></option>
													  <?php
													 } while ($row_direcciones = mysql_fetch_assoc($direcciones));
													 $rows = mysql_num_rows($direcciones);
													 if($rows > 0) {
													 mysql_data_seek($direcciones, 0);
													 $row_direcciones = mysql_fetch_assoc($direcciones);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área:</label>
										<div class="col-lg-9">
						<input type="text" name="area" id="area" class="form-control" value="<?php echo htmlentities($row_resultado['area'], ENT_COMPAT, ''); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                         <input class="btn bg-success btn-icon" type="submit" value="Actualizar" />
                         <button type="button" onClick="window.location.href='admin_proced_areas.php?IDdireccion=<?php echo $_GET['IDdireccion']?>'" class="btn btn-default btn-icon">Regresar</button>
                         <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDDarea" value="<?php echo $row_resultado['IDDarea']; ?>">

									<?php } else { ?>
                           <p>Agregar la información solicitada.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDdireccion" id="IDdireccion" class="form-control" required="required" disabled="disabled">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_direcciones['IDdireccion']?>"<?php if (!(strcmp($row_direcciones['IDdireccion'], $IDdireccion))) {echo "SELECTED";} ?>><?php echo $row_direcciones['direccion']?></option>
													  <?php
													 } while ($row_direcciones = mysql_fetch_assoc($direcciones));
													 $rows = mysql_num_rows($direcciones);
													 if($rows > 0) {
													 mysql_data_seek($direcciones, 0);
													 $row_direcciones = mysql_fetch_assoc($direcciones);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área:</label>
										<div class="col-lg-9">
                        <input type="text" name="area" id="area" class="form-control" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                            

                         <input class="btn bg-success btn-icon" type="submit" value="Agregar" />
                         <button type="button" onClick="window.location.href='admin_proced_areas.php?IDdireccion=<?php echo $_GET['IDdireccion']?>'" class="btn btn-default btn-icon">Regresar</button>
                         <input type="hidden" name="MM_insert" value="form1">
									<?php } ?>
                                    
                            </form>
                            <p>&nbsp;</p>
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