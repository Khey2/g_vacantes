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
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$semana = date("W"); //la semana empieza ayer 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_uniformes_periodos";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);

mysql_select_db($database_vacantes, $vacantes);
$query_variablesa = "SELECT * FROM sed_uniformes_variables";
$variablesa = mysql_query($query_variablesa, $vacantes) or die(mysql_error());
$row_variablesa = mysql_fetch_assoc($variablesa);
$totalRows_variablesa = mysql_num_rows($variablesa);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$updateSQL = sprintf("UPDATE sed_uniformes_variables SET cantidad_camisa_ventas=%s WHERE IDvariable = 1",
			GetSQLValueString($_POST['cantidad_camisa_ventas'], "int"),
			GetSQLValueString(1, "int"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes_variables.php?info=2");
	}
	
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	$updateSQL = sprintf("UPDATE sed_uniformes_variables SET cantidad_pantalon_ventas=%s WHERE IDvariable = 1",
			GetSQLValueString($_POST['cantidad_pantalon_ventas'], "int"),
			GetSQLValueString(1, "int"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes_variables.php?info=2");
	}
		
	
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
	$updateSQL = sprintf("UPDATE sed_uniformes_variables SET cantidad_playera_polo=%s WHERE IDvariable = 1",
			GetSQLValueString($_POST['cantidad_playera_polo'], "int"),
			GetSQLValueString(1, "int"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes_variables.php?info=2");
	}
			

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form4")) {
	$updateSQL = sprintf("UPDATE sed_uniformes_variables SET cantidad_playera_roja=%s WHERE IDvariable = 1",
			GetSQLValueString($_POST['cantidad_playera_roja'], "int"),
			GetSQLValueString(1, "int"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes_variables.php?info=2");
	}
				

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
	$updateSQL = sprintf("UPDATE sed_uniformes_variables SET cantidad_pantalon_mezclilla=%s WHERE IDvariable = 1",
			GetSQLValueString($_POST['cantidad_pantalon_mezclilla'], "int"),
			GetSQLValueString(1, "int"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes_variables.php?info=2");
	}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form6")) {
	$updateSQL = sprintf("UPDATE sed_uniformes_variables SET cantidad_botas=%s WHERE IDvariable = 1",
			GetSQLValueString($_POST['cantidad_botas'], "int"),
			GetSQLValueString(1, "int"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes_variables.php?info=2");
	}
						

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form7")) {
	$updateSQL = sprintf("UPDATE sed_uniformes_variables SET cantidad_faja=%s WHERE IDvariable = 1",
			GetSQLValueString($_POST['cantidad_faja'], "int"),
			GetSQLValueString(1, "int"));
	
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
		header("Location: uniformes_variables.php?info=2");
	}
							

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form8")) {
$updateSQL = sprintf("UPDATE sed_uniformes_variables SET extra=%s WHERE IDvariable = 1",
		GetSQLValueString($_POST['extra'], "int"),
		GetSQLValueString(1, "int"));

	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	header("Location: uniformes_variables.php?info=2");
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {

	$fecha_filtro1 = $_POST['fecha_inicio'];
	$y1 = substr( $fecha_filtro1, 6, 4 );
	$m1 = substr( $fecha_filtro1, 3, 2 );
	$d1 = substr( $fecha_filtro1, 0, 2 );
	$fecha_inicio =  $y1."-".$m1."-".$d1;
	
	$fecha_filtro2 = $_POST['fecha_fin'];
	$y2 = substr( $fecha_filtro2, 6, 4 );
	$m2 = substr( $fecha_filtro2, 3, 2 );
	$d2 = substr( $fecha_filtro2, 0, 2 );
	$fecha_fin =  $y2."-".$m2."-".$d2;

	$updateSQL = sprintf("UPDATE sed_uniformes_periodos SET periodo=%s, fecha_inicio=%s, fecha_fin=%s WHERE IDperiodo=%s",
		GetSQLValueString($_POST['periodo'], "text"),
		GetSQLValueString($fecha_inicio, "text"),
		GetSQLValueString($fecha_fin, "text"),
		GetSQLValueString($_POST['IDperiodo'], "int"));
	
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	header("Location: uniformes_variables.php?info=2");
	}
	
	if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form4")) {

	$fecha_filtro1 = $_POST['fecha_inicio'];
	$y1 = substr( $fecha_filtro1, 6, 4 );
	$m1 = substr( $fecha_filtro1, 3, 2 );
	$d1 = substr( $fecha_filtro1, 0, 2 );
	$fecha_inicio =  $y1."-".$m1."-".$d1;
	
	$fecha_filtro2 = $_POST['fecha_fin'];
	$y2 = substr( $fecha_filtro2, 6, 4 );
	$m2 = substr( $fecha_filtro2, 3, 2 );
	$d2 = substr( $fecha_filtro2, 0, 2 );
	$fecha_fin =  $y2."-".$m2."-".$d2;

	$updateSQL = sprintf("INSERT INTO sed_uniformes_periodos (periodo, fecha_inicio, fecha_fin) VALUES (%s, %s, %s)",
		GetSQLValueString($_POST['periodo'], "text"),
		GetSQLValueString($fecha_inicio, "text"),
		GetSQLValueString($fecha_fin, "text"));
	
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	header("Location: uniformes_variables.php?info=1");
	}

	// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
	$borrado = $_GET['IDperiodo'];
	$deleteSQL = "DELETE FROM sed_uniformes_periodos WHERE IDperiodo ='$borrado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: uniformes_variables.php?info=3");
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
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>

	<script src="assets/js/app.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Variables</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona las variables a editar.</p>
					</div>
                    
			     		<table class="table table-condensed">
						<thead>
						 <tr class="bg-blue">
                          <th>Variable</th>
                          <th>Valor</th>
                          <th>Instrucciones</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                        <tr>
                          <td>Camisa (Ventas)</td>
                          <td><?php echo $row_variablesa['cantidad_camisa_ventas']; ?></td>
                          <td>Determina la cantidad de prendas para Camisa (Ventas).</td>
                         <td><button type="button" data-target="#modal_theme_danger1"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                        <tr>
                          <td>Pantalon (Ventas)</td>
                          <td><?php echo $row_variablesa['cantidad_pantalon_ventas']; ?></td>
                          <td>Determina la cantidad de prendas para Pantalon (Ventas).</td>
                         <td><button type="button" data-target="#modal_theme_danger2"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                        <tr>
                          <td>Playera Polo (Distribución)</td>
                          <td><?php echo $row_variablesa['cantidad_playera_polo']; ?></td>
                          <td>Determina la cantidad de prendas para Playera Polo (Distribución).</td>
                         <td><button type="button" data-target="#modal_theme_danger3"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                        <tr>
                          <td>Playera Roja (Almacen)</td>
                          <td><?php echo $row_variablesa['cantidad_playera_roja']; ?></td>
                          <td>Determina la cantidad de prendas para Playera Roja (Almacen).</td>
                         <td><button type="button" data-target="#modal_theme_danger4"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                        <tr>
                          <td>Pantalon Mezclilla (Almacen y Distribución)</td>
                          <td><?php echo $row_variablesa['cantidad_pantalon_mezclilla']; ?></td>
                          <td>Determina la cantidad de prendas para Pantalon Mezclilla (Almacen y Distribución)</td>
                         <td><button type="button" data-target="#modal_theme_danger5"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                        <tr>
                          <td>Botas (Almacen y Distribución)</td>
                          <td><?php echo $row_variablesa['cantidad_botas']; ?></td>
                          <td>Determina la cantidad de prendas para Botas.</td>
                         <td><button type="button" data-target="#modal_theme_danger6"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                        <tr>
                          <td>Faja (Almacen y Distribución)</td>
                          <td><?php echo $row_variablesa['cantidad_faja']; ?></td>
                          <td>Determina la cantidad de Fajas (Almacen y Distribución).</td>
                         <td><button type="button" data-target="#modal_theme_danger7"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                        <tr>
                          <td>Stock Extra (%)</td>
                          <td><?php echo $row_variablesa['extra']; ?></td>
                          <td>Indica el % de prendas que se calculan como adicional a los activos para stock.</td>
                         <td><button type="button" data-target="#modal_theme_danger8"  data-toggle="modal" class="btn bg-primary-400">Editar</button></td>
                        </tr>                       
                   	</tbody>							  
                 </table>





        <!-- danger modal -->
		<div id="modal_theme_danger1" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Camisa (Ventas):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="cantidad_camisa_ventas" id="cantidad_camisa_ventas" value="<?php echo $row_variablesa['cantidad_camisa_ventas']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form1">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->


		        <!-- danger modal -->
				<div id="modal_theme_danger2" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form2" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Pantalon (Ventas):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="cantidad_pantalon_ventas" id="cantidad_pantalon_ventas" value="<?php echo $row_variablesa['cantidad_pantalon_ventas']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form2">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->



		        <!-- danger modal -->
				<div id="modal_theme_danger3" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form3" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Playera Polo (Distribución):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="cantidad_playera_polo" id="cantidad_playera_polo" value="<?php echo $row_variablesa['cantidad_playera_polo']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form3">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->



		        <!-- danger modal -->
				<div id="modal_theme_danger4" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form4" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Playera Roja (Almacen):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="cantidad_playera_roja" id="cantidad_playera_roja" value="<?php echo $row_variablesa['cantidad_playera_roja']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form4">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->



		        <!-- danger modal -->
				<div id="modal_theme_danger5" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form5" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Pantalon Mezclilla (Almacen y Distribución):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="cantidad_pantalon_mezclilla" id="cantidad_pantalon_mezclilla" value="<?php echo $row_variablesa['cantidad_pantalon_mezclilla']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form5">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->



		        <!-- danger modal -->
				<div id="modal_theme_danger6" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form6" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Botas (Almacen y Distribución):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="cantidad_botas" id="cantidad_botas" value="<?php echo $row_variablesa['cantidad_botas']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form6">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->



		        <!-- danger modal -->
				<div id="modal_theme_danger7" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form7" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Faja (Almacen y Distribución):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="cantidad_faja" id="cantidad_faja" value="<?php echo $row_variablesa['cantidad_faja']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form7">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->





            <!-- danger modal -->
			<div id="modal_theme_danger8" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form8" action="uniformes_variables.php">

						<div class="modal-body">

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-3">Stock Extra (%):</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="extra" id="extra" value="<?php echo $row_variablesa['extra']; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->
						</div>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form8">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->



				 <p>&nbsp;</p><p>&nbsp;</p>
				 <div class="panel-heading">
							<h5 class="panel-title">Periodos</h5>
						</div>

						<button type="button" data-target="#modal_theme_danger4"  data-toggle="modal" class="btn bg-success-400">Agregar Periodo</button>

				 <table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>IDperiodo</th>
                          <th>Periodo</th>
                          <th>Inicio</th>
                          <th>Fin</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_periodos['IDperiodo']; ?></td>
                          <td><?php echo $row_periodos['periodo']; ?></td>
                          <td><?php echo date( 'd/m/Y' , strtotime($row_periodos['fecha_inicio'])); ?></td>
                          <td><?php echo date( 'd/m/Y' , strtotime( $row_periodos['fecha_fin'])); ?></td>
                         <td>
							<button type="button" data-target="#modal_theme_danger3<?php echo $row_periodos['IDperiodo']; ?>"  data-toggle="modal" class="btn bg-primary-400">Editar</button>
						 	<button type="button" data-target="#modal_theme_danger5<?php echo $row_periodos['IDperiodo']; ?>"  data-toggle="modal" class="btn bg-danger-400">Borrar</button>
							<button type="button" data-target="#modal_theme_danger6"  data-toggle="modal" class="btn bg-warning-400">Trasladar Captura</button>
						</td>
                        </tr>              
						
						
                     <!-- danger modal -->
					 <div id="modal_theme_danger5<?php echo $row_periodos['IDperiodo']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el Periodo?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="uniformes_variables.php?IDperiodo=<?php echo $row_periodos['IDperiodo']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->




		<!-- danger modal -->
		<div id="modal_theme_danger3<?php echo $row_periodos['IDperiodo']; ?>" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form3" action="uniformes_variables.php">

						<div class="modal-body">


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Periodo:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    	<input type="text" class="form-control" name="periodo" id="periodo" value="<?php echo $row_periodos['periodo']; ?>" required="required">
									</div>
                                   </div>
                                  </div> 
							<!-- Fecha -->
							<p>&nbsp;</p>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" 
										value="<?php echo date( 'd/m/Y' , strtotime($row_periodos['fecha_inicio'])) ?>" required="required">
									</div>
                                   </div>
                                  </div> 
							<!-- Fecha -->
							<p>&nbsp;</p>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha fin:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
									<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" 
									value="<?php echo date( 'd/m/Y' , strtotime($row_periodos['fecha_fin'])) ?>" required="required">
									</div>
                                   </div>
                                  </div> 
							<!-- Fecha -->
							<p>&nbsp;</p>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form3">
									<input type="hidden" name="IDperiodo" value="<?php echo $row_periodos['IDperiodo']; ?>">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-primary">Actualizar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->



                        <?php } while ($row_periodos = mysql_fetch_assoc($periodos)); ?>
                   	</tbody>							  
                 </table>



	<!-- danger modal -->
		<div id="modal_theme_danger4" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar Variables</h6>
								</div>
						<form method="post" class="form-horizontal form-validate-jquery" name="form4" action="uniformes_variables.php">

						<div class="modal-body">


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Periodo:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    	<input type="text" class="form-control" name="periodo" id="periodo" value="" required="required">
									</div>
                                   </div>
                                  </div> 
							<!-- Fecha -->
							<p>&nbsp;</p>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" 
										value="" required="required">
									</div>
                                   </div>
                                  </div> 
							<!-- Fecha -->
							<p>&nbsp;</p>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-4">Fecha fin:<span class="text-danger">*</span></label>
			                        <div class="col-lg-8">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
									<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" 
									value="" required="required">
									</div>
                                   </div>
                                  </div> 
							<!-- Fecha -->
							<p>&nbsp;</p>


						<div class="modal-footer">
									<input type="hidden" name="MM_update" value="form4">
									<input type="hidden" name="IDperiodo" value="<?php echo $row_periodos['IDperiodo']; ?>">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-success">Agregar</button>
						</div>

						</form>
		
					</div>
				</div>
			</div>
		<!-- /danger modal -->








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