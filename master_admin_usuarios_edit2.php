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
$query_usuario_ = "SELECT * FROM vac_usuarios WHERE IDusuario = '$elusuario'";
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

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}
$el_mes = $_SESSION['el_mes'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$updateSQL = sprintf("UPDATE vac_usuarios SET usuario=%s, usuario_nombre=%s, usuario_parterno=%s, usuario_materno=%s, usuario_telefono=%s, usuario_correo=%s, nivel_acceso=%s, activo=%s,  corpo=%s, IDusuario_puesto=%s, IDmatriz=%s, user_prod=%s, servicio=%s, user_vacs=%s, user_plan=%s, user_admi=%s, user_dps=%s, user_ind=%s, user_inc=%s, covid=%s, n35=%s, semaforo=%s, tabulador=%s, plan_carrera=%s, candidatos=%s, desemp_rh=%s, area_rh=%s, altasybajas=%s, kpis=%s,  clima=%s, user_expediente=%s, user_prueba=%s WHERE IDusuario=%s",
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString($_POST['usuario_nombre'], "text"),
                       GetSQLValueString($_POST['usuario_parterno'], "text"),
                       GetSQLValueString($_POST['usuario_materno'], "text"),
                       GetSQLValueString($_POST['usuario_telefono'], "text"),
                       GetSQLValueString($_POST['usuario_correo'], "text"),
                       GetSQLValueString($_POST['nivel_acceso'], "int"),
                       GetSQLValueString($_POST['activo'], "int"),
                       GetSQLValueString($_POST['corpo'], "int"),
                       GetSQLValueString($_POST['IDusuario_puesto'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['user_prod'], "int"),
                       GetSQLValueString($_POST['servicio'], "int"),
                       GetSQLValueString($_POST['user_vacs'], "int"),
                       GetSQLValueString($_POST['user_plan'], "int"),
                       GetSQLValueString($_POST['user_admi'], "int"),
                       GetSQLValueString($_POST['user_dps'], "int"),
                       GetSQLValueString($_POST['user_ind'], "int"),
                       GetSQLValueString($_POST['user_inc'], "int"),
                       GetSQLValueString($_POST['covid'], "int"),
                       GetSQLValueString($_POST['n35'], "int"),
                       GetSQLValueString($_POST['semaforo'], "int"),
                       GetSQLValueString($_POST['tabulador'], "int"),
                       GetSQLValueString($_POST['plan_carrera'], "int"),
                       GetSQLValueString($_POST['candidatos'], "int"),
                       GetSQLValueString($_POST['desemp_rh'], "int"),
                       GetSQLValueString($_POST['area_rh'], "int"),
                       GetSQLValueString($_POST['altasybajas'], "int"),
                       GetSQLValueString($_POST['kpis'], "int"),
                       GetSQLValueString($_POST['clima'], "int"),
                       GetSQLValueString($_POST['user_expediente'], "int"),
                       GetSQLValueString($_POST['user_prueba'], "int"),
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
	
  $insertSQL = sprintf("INSERT INTO vac_usuarios (IDusuario, usuario, password, usuario_correo, usuario_nombre, usuario_parterno, usuario_materno, usuario_telefono, nivel_acceso, activo, IDusuario_puesto, IDmatriz, IDmatrizes, IDareas, corpo, user_prod, servicio, user_vacs, user_plan, user_admi, user_dps, user_ind, user_inc, covid, n35, semaforo, tabulador, plan_carrera, candidatos, desemp_rh, area_rh, altasybajas, kpis, user_expediente, user_prueba, clima) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDusuario'], "int"),
                       GetSQLValueString($_POST['usuario'], "text"),
                       GetSQLValueString(md5($_POST['IDusuario']), "text"),
                       GetSQLValueString($_POST['usuario_correo'], "text"),
                       GetSQLValueString($_POST['usuario_nombre'], "text"),
                       GetSQLValueString($_POST['usuario_parterno'], "text"),
                       GetSQLValueString($_POST['usuario_materno'], "text"),
                       GetSQLValueString($_POST['usuario_telefono'], "text"),
                       GetSQLValueString($_POST['nivel_acceso'], "int"),
                       GetSQLValueString($_POST['activo'], "int"),
                       GetSQLValueString($_POST['IDusuario_puesto'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "text"),
                       GetSQLValueString($_POST['IDmatriz'], "text"),
                       GetSQLValueString($_POST['IDareas'], "text"),
                       GetSQLValueString($_POST['corpo'], "int"),
                       GetSQLValueString($_POST['user_prod'], "int"),
                       GetSQLValueString($_POST['servicio'], "int"),
                       GetSQLValueString($_POST['user_vacs'], "int"),
                       GetSQLValueString($_POST['user_plan'], "int"),
                       GetSQLValueString($_POST['user_admi'], "int"),
                       GetSQLValueString($_POST['user_dps'], "int"),
                       GetSQLValueString($_POST['user_ind'], "int"),
                       GetSQLValueString($_POST['user_inc'], "int"),
                       GetSQLValueString($_POST['covid'], "int"),
                       GetSQLValueString($_POST['n35'], "int"),
                       GetSQLValueString($_POST['semaforo'], "int"),
                       GetSQLValueString($_POST['tabulador'], "int"),
                       GetSQLValueString($_POST['plan_carrera'], "int"),
                       GetSQLValueString($_POST['candidatos'], "int"),
                       GetSQLValueString($_POST['desemp_rh'], "int"),
                       GetSQLValueString($_POST['area_rh'], "int"),
                       GetSQLValueString($_POST['altasybajas'], "int"),
                       GetSQLValueString($_POST['kpis'], "int"),
                       GetSQLValueString($_POST['user_expediente'], "int"),
                       GetSQLValueString($_POST['user_prueba'], "int"),
                       GetSQLValueString($_POST['clima'], "int"));

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
										<label class="control-label col-lg-3">Usuario (correo completo):</label>
										<div class="col-lg-9">
						<input type="email" name="usuario" id="usuario" class="form-control" value="<?php echo htmlentities($row_usuario_['usuario'], ENT_COMPAT, ''); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Activo:</label>
										<div class="col-lg-9">
						                 <select name="activo" id="activo" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['activo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No activo</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['activo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Activo</option>
											</select>
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
										<label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="email" name="usuario_correo" id="usuario_correo" class="form-control" value="<?php echo htmlentities($row_usuario_['usuario_correo'], ENT_COMPAT, ''); ?>" required="required">
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

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Activo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<select name="activo" id="activo" class="form-control" required="required">
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['activo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
						                   <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['activo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
									</select>
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


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Usuario:</label>
										<div class="col-lg-9">
						                 <select name="nivel_acceso" id="nivel_acceso" class="form-control" required="required">
						                   <option value="" <?php if (!(strcmp("", htmlentities($row_usuario_['nivel_acceso'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Seleccione una opción</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['nivel_acceso'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Usuario</option>
						                   <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['nivel_acceso'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Administrador</option>
						                   <option value="3" <?php if (!(strcmp(3, htmlentities($row_usuario_['nivel_acceso'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Usuario Vista</option>
					                       <option value="4" <?php if (!(strcmp(4, htmlentities($row_usuario_['nivel_acceso'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Mastar Admin</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    <h5>Permisos</h5>
							<p>Asigna permisos a usuario.</p>
                                   <p>&nbsp;</p>

                            
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Productividad:</label>
										<div class="col-lg-9">
						                 <select name="user_prod" id="user_prod" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_prod'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_prod'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Captura</option>
						                   <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['user_prod'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Valida</option>
						                   <option value="3" <?php if (!(strcmp(3, htmlentities($row_usuario_['user_prod'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Autoriza</option>
					                       <option value="5" <?php if (!(strcmp(5, htmlentities($row_usuario_['user_prod'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>
                                            <option value="4" <?php if (!(strcmp(4, htmlentities($row_usuario_['user_prod'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Admin</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Vacantes:</label>
										<div class="col-lg-9">
						                 <select name="user_vacs" id="user_vacs" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_vacs'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_vacs'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Captura</option>
						                   <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['user_vacs'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>
						                   <option value="3" <?php if (!(strcmp(3, htmlentities($row_usuario_['user_vacs'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Solicita</option>

											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Plantilla:</label>
										<div class="col-lg-9">
						                 <select name="user_plan" id="user_plan" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_plan'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_plan'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nivel Admin:</label>
										<div class="col-lg-9">
						                 <select name="user_admi" id="user_admi" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_admi'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_admi'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Admin</option>
                                           <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['user_admi'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Master</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Descriptivos:</label>
										<div class="col-lg-9">
						                 <select name="user_dps" id="user_dps" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_dps'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_dps'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>           
											</select>
										</div>
									</div>

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Incidencias Semanales:</label>
										<div class="col-lg-9">
						                 <select name="user_inc" id="user_inc" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_inc'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_inc'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Captura</option>
                                           <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['user_inc'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Covid 19:</label>
										<div class="col-lg-9">
						                 <select name="covid" id="covid" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['covid'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['covid'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>
                                           <option value="3" <?php if (!(strcmp(3, htmlentities($row_usuario_['covid'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Admin</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Indicadores:</label>
										<div class="col-lg-9">
						                 <select name="user_ind" id="user_ind" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_ind'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_ind'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Todos</option>
                                           <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['user_ind'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Solo Rotación</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Resultados NOM-35:</label>
										<div class="col-lg-9">
						                 <select name="n35" id="n35" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['n35'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['n35'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Admin</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Semáforo:</label>
										<div class="col-lg-9">
						                 <select name="semaforo" id="semaforo" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['semaforo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['semaforo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tabulador:</label>
										<div class="col-lg-9">
						                 <select name="tabulador" id="tabulador" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['tabulador'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['tabulador'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Servicio RH:</label>
										<div class="col-lg-9">
						                 <select name="servicio" id="servicio" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['servicio'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['servicio'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
						                   <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['servicio'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Resultados</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Desempeño RH:</label>
										<div class="col-lg-9">
						                 <select name="desemp_rh" id="desemp_rh" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['desemp_rh'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['desemp_rh'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Evaluado</option>
						                   <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['desemp_rh'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Evaluador</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area RH (Desempeño RH)</label>
										<div class="col-lg-9">
											<select name="area_rh" id="area_rh" class="form-control">
                                            	<option value="">No aplica</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_area_rh['IDarea_rh']?>"<?php if (!(strcmp($row_area_rh['IDarea_rh'], $row_usuario_['area_rh']))) {echo "SELECTED";} ?>><?php echo $row_area_rh['area_rh']?></option>
													  <?php
													 } while ($row_area_rh = mysql_fetch_assoc($area_rh));
													 $rows = mysql_num_rows($lmarea_rhatriz);
													 if($rows > 0) {
													 mysql_data_seek($area_rh, 0);
													 $row_area_rh = mysql_fetch_assoc($area_rh);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Consultas:</label>
										<div class="col-lg-9">
						                 <select name="altasybajas" id="altasybajas" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['altasybajas'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['altasybajas'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Altas y Bajas</option>
						                   <option value="2" <?php if (!(strcmp(2, htmlentities($row_usuario_['altasybajas'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Activos</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">KPIs:</label>
										<div class="col-lg-9">
						                 <select name="kpis" id="kpis" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['kpis'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['kpis'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Clima:</label>
										<div class="col-lg-9">
						                 <select name="clima" id="clima" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['clima'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['clima'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Plan Carrera:</label>
										<div class="col-lg-9">
						                 <select name="plan_carrera" id="plan_carrera" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['plan_carrera'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['plan_carrera'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Candidatos:</label>
										<div class="col-lg-9">
						                 <select name="candidatos" id="candidatos" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['candidatos'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['candidatos'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Log de Usuarios:</label>
										<div class="col-lg-9">
						                 <select name="corpo" id="corpo" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['corpo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['corpo'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Expedientes:</label>
										<div class="col-lg-9">
						                 <select name="user_expediente" id="user_expediente" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_expediente'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No aplica</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_expediente'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

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
                        <button type="button" onClick="window.location.href='master_admin_usuarios_asigdar.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>'" class="btn bg-indigo btn-icon">Asignar Matrices</button>
                         <button type="button" onClick="window.location.href='master_admin_usuarios_asignar.php?IDusuario=<?php echo $row_usuario_['IDusuario']; ?>'" class="btn bg-indigo btn-icon">Asignar Áreas</button>
                         <button type="button" onClick="window.location.href='master_admin_usuarios.php'" class="btn btn-info btn-icon">Regresar</button>
                         <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-warning btn-icon">Restaurar Password *</button>
                          <span class="help-block">* El password será el mismo que el usuario.</span>
                         <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDusuario" value="<?php echo $row_usuario_['IDusuario']; ?>">

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
										<label class="control-label col-lg-3">Usuario (correo completo):</label>
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
										<label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="email" name="usuario_correo" id="usuario_correo" class="form-control" value="" required="required">
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

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Activo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<select name="activo" id="activo" class="form-control" required="required">
						                   <option value="1">Si</option>
						                   <option value="2">No</option>
									</select>
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


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Usuario:</label>
										<div class="col-lg-9">
						                 <select name="nivel_acceso" id="nivel_acceso" class="form-control" required="required">
						                   <option value="">Seleccione una opción</option>
						                   <option value="1">Usuario</option>
						                   <option value="2">Administrador</option>
						                   <option value="3">Usuario Vista</option>
					                       <option value="4">Mastar Admin</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    <h5>Permisos</h5>
							<p>Asigna permisos a usuario.</p>
                                   <p>&nbsp;</p>

                            
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Productividad:</label>
										<div class="col-lg-9">
						                 <select name="user_prod" id="user_prod" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Captura</option>
						                   <option value="2">Valida</option>
						                   <option value="3">Autoriza</option>
					                       <option value="5">Consulta</option>
                                            <option value="4">Admin</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Vacantes:</label>
										<div class="col-lg-9">
						                 <select name="user_vacs" id="user_vacs" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Captura</option>
						                   <option value="2">Consulta</option>
						                   <option value="3">Solicita</option>

											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Plantilla:</label>
										<div class="col-lg-9">
						                 <select name="user_plan" id="user_plan" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Consulta</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nivel Admin:</label>
										<div class="col-lg-9">
						                 <select name="user_admi" id="user_admi" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Admin</option>
                                           <option value="2">Master</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Descriptivos:</label>
										<div class="col-lg-9">
						                 <select name="user_dps" id="user_dps" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Consulta</option>           
											</select>
										</div>
									</div>

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Incidencias Semanales:</label>
										<div class="col-lg-9">
						                 <select name="user_inc" id="user_inc" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Captura</option>
                                           <option value="2">Consulta</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Covid 19:</label>
										<div class="col-lg-9">
						                 <select name="covid" id="covid" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Consulta</option>
                                           <option value="3">Admin</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Indicadores:</label>
										<div class="col-lg-9">
						                 <select name="user_ind" id="user_ind" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Todos</option>
                                           <option value="2">Solo Rotación</option>           
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Resultados NOM-35:</label>
										<div class="col-lg-9">
						                 <select name="n35" id="n35" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Admin</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Semáforo:</label>
										<div class="col-lg-9">
						                 <select name="semaforo" id="semaforo" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tabulador:</label>
										<div class="col-lg-9">
						                 <select name="tabulador" id="tabulador" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Desempeño RH:</label>
										<div class="col-lg-9">
						                 <select name="desemp_rh" id="desemp_rh" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Evaluado</option>
						                   <option value="2">Evaluador</option>
											</select>
										</div>
									</div>

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Servicio RH:</label>
										<div class="col-lg-9">
						                 <select name="servicio" id="servicio" class="form-control" >
						                   <option value="0">No</option>
						                   <option value="1">Si</option>
						                   <option value="2">Resultados</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
									<!-- /basic text input -->

                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area RH (Desempeño RH)</label>
										<div class="col-lg-9">
											<select name="area_rh" id="area_rh" class="form-control">
                                            	<option value="">No aplica</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_area_rh['IDarea_rh']?>"><?php echo $row_area_rh['area_rh']?></option>
													  <?php
													 } while ($row_area_rh = mysql_fetch_assoc($area_rh));
													 $rows = mysql_num_rows($lmarea_rhatriz);
													 if($rows > 0) {
													 mysql_data_seek($area_rh, 0);
													 $row_area_rh = mysql_fetch_assoc($area_rh);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Consultas:</label>
										<div class="col-lg-9">
						                 <select name="altasybajas" id="altasybajas" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Altas y Bajas</option>
						                   <option value="2">Activos</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">KPIs:</label>
										<div class="col-lg-9">
						                 <select name="kpis" id="kpis" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Clima:</label>
										<div class="col-lg-9">
						                 <select name="clima" id="kpis" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Plan Carrera:</label>
										<div class="col-lg-9">
						                 <select name="plan_carrera" id="plan_carrera" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Candidatos:</label>
										<div class="col-lg-9">
						                 <select name="candidatos" id="candidatos" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Log de Usuarios:</label>
										<div class="col-lg-9">
						                 <select name="corpo" id="corpo" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Consulta</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Expendietes:</label>
										<div class="col-lg-9">
						                 <select name="user_expediente" id="user_expediente" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Periodos de Prueba:</label>
										<div class="col-lg-9">
						                 <select name="user_prueba" id="user_prueba" class="form-control" >
						                   <option value="0">No aplica</option>
						                   <option value="1">Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

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