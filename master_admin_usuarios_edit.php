<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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
$la_matriz = $row_usuario['IDmatriz'];

if(isset($_GET['IDusuario'])) {
$elusuario = $_GET['IDusuario'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario_ = "SELECT * FROM vac_usuarios WHERE IDusuario = $elusuario";
mysql_query("SET NAMES 'utf8'"); 
$usuario_ = mysql_query($query_usuario_, $vacantes) or die(mysql_error());
$row_usuario_ = mysql_fetch_assoc($usuario_);
$totalRows_usuario_ = mysql_num_rows($usuario_);
$IDmatrizes = $row_usuario_['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
mysql_query("SET NAMES 'utf8'"); 
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM vac_puestos ORDER BY vac_puestos.denominacion ASC";
mysql_query("SET NAMES 'utf8'"); 
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_area_rh = "SELECT * FROM ztar_areas_rh";
$area_rh = mysql_query($query_area_rh, $vacantes) or die(mysql_error());
$row_area_rh = mysql_fetch_assoc($area_rh);
$totalRows_area_rh = mysql_num_rows($area_rh);

mysql_select_db($database_vacantes, $vacantes);
$query_larea = "SELECT * FROM vac_areas";
$larea = mysql_query($query_larea, $vacantes) or die(mysql_error());
$row_larea = mysql_fetch_assoc($larea);
$totalRows_larea = mysql_num_rows($larea);


if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}
$el_mes = $_SESSION['el_mes'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$nivel_acceso = 1;
$updateSQL = sprintf("UPDATE vac_usuarios SET usuario=%s, usuario_nombre=%s, usuario_parterno=%s, usuario_materno=%s, usuario_telefono=%s, usuario_correo=%s, nivel_acceso=%s, IDusuario_puesto=%s, IDmatriz=%s, IDarea=%s WHERE IDusuario=%s",
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString($_POST['usuario_nombre'], "text"),
                       GetSQLValueString($_POST['usuario_parterno'], "text"),
                       GetSQLValueString($_POST['usuario_materno'], "text"),
                       GetSQLValueString($_POST['usuario_telefono'], "text"),
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString($nivel_acceso, "int"),
                       GetSQLValueString($_POST['IDusuario_puesto'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDusuario'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "master_admin_usuarios.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));  
} 

else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	
$usuario_valida = $_POST['IDusuario'];
mysql_select_db($database_vacantes, $vacantes);
$query_valida = "SELECT * FROM vac_usuarios WHERE IDusuario = '$usuario_valida'";
$valida = mysql_query($query_valida, $vacantes) or die(mysql_error());
$row_valida = mysql_fetch_assoc($valida);
$totalRows_valida = mysql_num_rows($valida);

if ($totalRows_valida > 0){ header("Location: master_admin_usuarios.php?info=4");}

$nivel_acceso = 1;
$insertSQL = sprintf("INSERT INTO vac_usuarios (IDusuario, usuario, password, usuario_correo, usuario_nombre, usuario_parterno, usuario_materno, usuario_telefono, activo, IDusuario_puesto, IDmatriz, IDarea, nivel_acceso, IDmatrizes) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDusuario'], "int"),
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString(md5($_POST['IDusuario']), "text"),
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString($_POST['usuario_nombre'], "text"),
                       GetSQLValueString($_POST['usuario_parterno'], "text"),
                       GetSQLValueString($_POST['usuario_materno'], "text"),
                       GetSQLValueString($_POST['usuario_telefono'], "text"),
                       GetSQLValueString($_POST['activo'], "int"),
                       GetSQLValueString($_POST['IDusuario_puesto'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($nivel_acceso, "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "master_admin_usuarios.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] != "")) {
  
  $borrado = $_GET['IDusuario'];
  $deleteSQL = "DELETE FROM vac_usuarios WHERE IDusuario ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: master_admin_usuarios.php?info=3");
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

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Editar / Actualizar Usuario</h5>
						</div>

					<div class="panel-body">
									<?php if (isset($_GET['IDusuario'])) {?>


							<p>Actualiza la información del usuario.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No.Emp:</label>
										<div class="col-lg-9">
                        <?php echo $row_usuario_['IDusuario']; ?>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario - Correo Sahuayo:</label>
										<div class="col-lg-9">
						<input type="email" name="usuario" id="usuario" class="form-control" value="<?php echo htmlentities($row_usuario_['usuario'], ENT_COMPAT, ''); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_nombre" id="usuario_nombre" class="form-control" value="<?php echo htmlentities($row_usuario_['usuario_nombre'], ENT_COMPAT, ''); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_parterno" id="usuario_parterno" class="form-control" value="<?php echo htmlentities($row_usuario_['usuario_parterno'], ENT_COMPAT, ''); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Materno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_materno" id="usuario_materno" class="form-control" value="<?php echo htmlentities($row_usuario_['usuario_materno'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_telefono" id="usuario_telefono" class="form-control format-phone-number" value="<?php echo htmlentities($row_usuario_['usuario_telefono'], ENT_COMPAT, ''); ?>">
									<span class="help-block">(99) 99 99 99 99</span>
                                    	</div>
									</div>
									<!-- /basic text input -->


                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDusuario_puesto" id="IDusuario_puesto" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if (!(strcmp($row_puestos['IDpuesto'], $row_usuario_['IDusuario_puesto']))) 
												  {echo "SELECTED";} ?>><?php echo $row_puestos['denominacion']?></option>
												  <?php
												 } while ($row_puestos = mysql_fetch_assoc($puestos));
												   $rows = mysql_num_rows($puestos);
												   if($rows > 0) {
												   mysql_data_seek($puestos, 0);
												   $row_puestos = mysql_fetch_assoc($puestos);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz Origen:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $row_usuario_['IDmatriz']))) {echo "SELECTED";} ?>><?php echo $row_lmatriz['matriz']?></option>
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

                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área Origen:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_larea['IDarea']?>"<?php if (!(strcmp($row_larea['IDarea'], $row_usuario_['IDarea']))) {echo "SELECTED";} ?>><?php echo $row_larea['area']?></option>
													  <?php
													 } while ($row_larea = mysql_fetch_assoc($larea));
													 $rows = mysql_num_rows($larea);
													 if($rows > 0) {
													 mysql_data_seek($larea, 0);
													 $row_larea = mysql_fetch_assoc($larea);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matrices Asignadas:</label>
										<div class="col-lg-9">
						                    <?php do { ?>
						                    <?php echo $row_matrizes['matriz'] . ". "; ?>
						                    <?php } while ($row_matrizes = mysql_fetch_assoc($matrizes)); ?>
                                       </div>
									</div>
									<!-- /basic text input -->
                                    
                                    
                         <input class="btn bg-success btn-icon" type="submit" value="Actualizar Usuario" /> 
                         <button type="button" onClick="window.location.href='master_admin_usuarios.php'" class="btn btn-default btn-icon">Regresar</button> <br/><br/>
                        <input type="hidden" name="MM_update" value="form1">
                        <input type="hidden" name="IDusuario" value="<?php echo $row_usuario_['IDusuario']; ?>">						 
						 
					 
	<button type="button" onClick="window.location.href='master_admin_usuarios_edit_p.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>'" class="btn bg-info btn-icon">Permisos</button>

					 <br/><br/>

	<button type="button" onClick="window.location.href='master_admin_usuarios_asigdar.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>'" class="btn bg-indigo btn-icon">Asignar Matrices</button>
	<button type="button" onClick="window.location.href='master_admin_usuarios_asignar.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>'" class="btn bg-indigo btn-icon">Asignar Áreas</button>
	<button type="button" class="btn bg-success btn-icon" onClick="window.location.href='master_admin_usuarios_asigdar_kpis.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>'">Asignar Matrices KPIs</button>
	<button type="button" class="btn bg-success btn-icon" onClick="window.location.href='master_admin_usuarios_asignar_kpis.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>'">Asignar Areas KPIs</button>                        
						  <br/><br/>
						 
						 <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-warning btn-icon">Restaurar Password</button>

									<?php } else { ?>
                           <p>Agregar usuario.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                                    
                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No.Emp:</label>
										<div class="col-lg-9">
                        <input type="number" name="IDusuario" id="IDusuario" class="form-control" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                            
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario - Correo Sahuayo</label>
										<div class="col-lg-9">
						<input type="email" name="usuario" id="usuario" class="form-control" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_nombre" id="usuario_nombre" class="form-control" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_parterno" id="usuario_parterno" class="form-control" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Materno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_materno" id="usuario_materno" class="form-control" value="">
										</div>
									</div>
									<!-- /basic text input -->


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="usuario_telefono" id="usuario_telefono" class="form-control format-phone-number" value="">
									<span class="help-block">(99) 99 99 99 99</span>
                                    	</div>
									</div>
									<!-- /basic text input -->


                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDusuario_puesto" id="IDusuario_puesto" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_puestos['IDpuesto']?>"><?php echo $row_puestos['denominacion']?></option>
												  <?php
												 } while ($row_puestos = mysql_fetch_assoc($puestos));
												   $rows = mysql_num_rows($puestos);
												   if($rows > 0) {
												   mysql_data_seek($puestos, 0);
												   $row_puestos = mysql_fetch_assoc($puestos);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz Origen:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_lmatriz['IDmatriz']?>"><?php echo $row_lmatriz['matriz']?></option>
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

                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área Origen:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_larea['IDarea']?>"<?php if (!(strcmp($row_larea['IDarea'], $row_usuario_['IDarea']))) {echo "SELECTED";} ?>><?php echo $row_larea['area']?></option>
													  <?php
													 } while ($row_larea = mysql_fetch_assoc($larea));
													 $rows = mysql_num_rows($larea);
													 if($rows > 0) {
													 mysql_data_seek($larea, 0);
													 $row_larea = mysql_fetch_assoc($larea);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->



                         <input class="btn bg-success btn-icon" type="submit" value="Agregar Usuario" />
                         <button type="button" onClick="window.location.href='master_admin_usuarios.php'" class="btn btn-info btn-icon">Regresar</button>
                         <input type="hidden" name="MM_insert" value="form1">
                         <input type="hidden" name="IDareas" value="0">
                         <input type="hidden" name="activo" value="1">
									<?php } ?>
                                    
                            </form>
                            <p>&nbsp;</p>
                    </div>

</div>

                  <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Restauración</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres restaurar el password?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="master_admin_usuarios_reset.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>">Si restaurar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
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