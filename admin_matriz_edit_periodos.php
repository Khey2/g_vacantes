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
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatrizes = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];
$la_matriz = $_GET['IDmatriz'];

$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT sed_clima_periodos.*, vac_matriz.matriz FROM sed_clima_periodos LEFT JOIN vac_matriz ON sed_clima_periodos.IDmatriz = vac_matriz.IDmatriz WHERE sed_clima_periodos.IDmatriz = $la_matriz";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDperiodod = $_POST["IDperiodo"]; 
	$estatusd = $_POST["estatus"]; 
	$query1 = "UPDATE sed_clima_periodos SET estatus = '$estatusd' WHERE IDperiodo = '$IDperiodod'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: admin_matriz_edit_periodos.php?IDmatriz=$la_matriz&info=2"); 	
}

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $la_matriz AND estatus = 1";
$periodos  = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos  = mysql_fetch_assoc($periodos);
$totalRows_periodos  = mysql_num_rows($periodos);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	
	$periodoN = utf8_decode($_POST['periodo']);
	$IDmatrizA = $_POST['IDmatriz'];
	$estatusA = $_POST['estatus'];
	
	$insertSQL = sprintf("INSERT INTO sed_clima_periodos (periodo, IDmatriz, estatus) VALUES (%s, %s, %s)",
						   GetSQLValueString($periodoN, "text"),
						   GetSQLValueString($IDmatrizA, "text"),
						   GetSQLValueString($estatusA , "text"));
	
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
	$last_id =  mysql_insert_id();
	
	header("Location:  admin_matriz_edit_periodos.php?IDmatriz=$la_matriz&info=4");
	 }
	

	 // borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
	$borrado = $_GET['IDperiodo'];
	$deleteSQL = "DELETE FROM sed_clima_periodos WHERE IDperiodo ='$borrado'";
  
	mysql_select_db($database_vacantes, $vacantes);
	$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
	header("Location: admin_matriz_edit_periodos.php?IDmatriz=$la_matriz&info=3");
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

	<script src="assets/js/app.js"></script>
   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
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

						<!-- Basic alert -->
                        <?php if($totalRows_periodos > 1) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							No debe existir más de un periodo abierto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona la Matriz que requiera editar.<br/>
							<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-xs btn-success">Agregar</button>
							</p>
					</div>
                    
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>IDPeriodo</th>
                          <th>Matriz</th>
                          <th>Periodo</th>
                          <th>Estatus</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { 
					  
				  
$la_matriz = $row_puestos['IDmatriz'];

					  ?>
                        <tr>
						 <td><?php echo $row_puestos['IDperiodo']; ?></td>
                          <td><?php echo $row_puestos['matriz']; ?></td>
                          <td><?php echo $row_puestos['periodo']; ?></td>
                          <td><?php if ($row_puestos['estatus'] == 1) {echo "Abierto";} else { echo "Cerrado";} ?></td>
                         <td>
						 <button type="button" data-target="#modal_theme_danger<?php echo $row_puestos['IDperiodo']; ?>"  data-toggle="modal" class="btn btn-xs btn-primary">Editar</button>
						 <button type="button" data-target="#modal_theme_danger2<?php echo $row_puestos['IDperiodo']; ?>"  data-toggle="modal" class="btn btn-xs btn-danger">Borrar</button>

					                <!-- danger modal -->
									<div id="modal_theme_danger<?php echo $row_puestos['IDperiodo']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-info">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cambiar estatus</h6>
												</div>
												<div class="modal-body">
																			
														<form action="admin_matriz_edit_periodos.php?IDmatriz=<?php echo $la_matriz; ?>" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Estatus:</label>
															  <div class="col-lg-9">
															<select name="estatus" id="estatus" class="form-control" >
																<option value="1"<?php if ($row_puestos['estatus'] == 1) {echo "selected=\"selected\"";} ?>>Activo</option>
																<option value="2"<?php if ($row_puestos['estatus'] == 2) {echo "selected=\"selected\"";} ?>>Cerrado</option>
															</select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-info">Actualizar</button> 
													<input type="hidden" name="MM_insert" value="form1" />
													<input type="hidden" name="IDperiodo" value="<?php echo $row_puestos['IDperiodo']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->

									<!-- danger modal -->
									<div id="modal_theme_danger2<?php echo $row_puestos['IDperiodo']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Borrado</h6>
												</div>
												<div class="modal-body">

												<p>Estas seguro de borrar el Periodo</p>
													
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                				    <a class="btn btn-danger" href="admin_matriz_edit_periodos.php?IDmatriz=<?php echo $la_matriz; ?>&IDperiodo=<?php echo $row_puestos['IDperiodo']; ?>&borrar=1">Si borrar</a>
												</div>

											</div>
										</div>
									</div>
									<!-- danger modal -->


						</td>
                        </tr>                       
                        <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
                   	</tbody>							  
                 </table>
					<!-- /Contenido -->


<!-- danger modal -->
<div id="modal_theme_danger" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-success">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h6 class="modal-title">Agregar Periodo</h6>
			</div>
			<div class="modal-body">
										
					<form action="admin_matriz_edit_periodos.php?IDmatriz=<?php echo $la_matriz; ?>" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
					 <fieldset>
					 

					 <div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $IDmatriz))) 
												  {echo "SELECTED";} ?>><?php echo $row_lmatriz['matriz']?></option>
												  <?php
												 } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
												   $rows = mysql_num_rows($lmatriz);
												   if($rows > 0) {
												   mysql_data_seek($lmatriz, 0);
												   $row_lmatriz = mysql_fetch_assoc($lmatriz);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre del Periodo:</label>
										<div class="col-lg-9">
										<input type="text" name="periodo" id="periodo" class="form-control" placeholder="Indique el nombre del Periodo" required="required">
										</div>
									</div>
									<!-- /basic text input -->

					 <!-- Basic text input -->
					  <div class="form-group">
						  <label class="control-label col-lg-3">Estatus:</label>
						  <div class="col-lg-9">
						<select name="estatus" id="estatus" class="form-control" >
							<option value="1"<?php if ($row_puestos['estatus'] == 1) {echo "selected=\"selected\"";} ?>>Activo</option>
							<option value="2"<?php if ($row_puestos['estatus'] == 2) {echo "selected=\"selected\"";} ?>>Cerrado</option>
						</select>
						 </div>
					  </div>
					  <!-- /basic text input -->

					 </fieldset>

					<div>
					</div>
										
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" id="submit" name="import" class="btn btn-info">Actualizar</button> 
				<input type="hidden" name="MM_insert" value="form2" />
			</div>
					 </form>
		</div>
	</div>
</div>
<!-- danger modal -->



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