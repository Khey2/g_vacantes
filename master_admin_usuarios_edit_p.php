<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$updateSQL = sprintf("UPDATE vac_usuarios SET user_aumento_anual=%s, user_becarios=%s, user_casos_sindicato=%s, user_credenciales=%s, user_prod_adicionales=%s, user_prod_reporte=%s, user_disciplina=%s, user_proced=%s, user_operaciones_regional=%s, sueldos=%s, calendario=%s, com_vd=%s, rel_lab=%s, contratos=%s,  user_prod=%s, servicio=%s, user_vacs=%s, user_plan=%s, user_admi=%s, user_dps=%s, user_ind=%s, user_inc=%s, covid=%s, n35=%s, semaforo=%s, tabulador=%s, plan_carrera=%s, candidatos=%s, desemp_rh=%s, area_rh=%s, altasybajas=%s, kpis=%s,  clima=%s, user_expediente=%s, user_prueba=%s WHERE IDusuario=%s",
                       GetSQLValueString($_POST['user_aumento_anual'], "int"),
                       GetSQLValueString($_POST['user_becarios'], "int"),
                       GetSQLValueString($_POST['user_casos_sindicato'], "int"),
                       GetSQLValueString($_POST['user_credenciales'], "int"),
                       GetSQLValueString($_POST['user_prod_adicionales'], "int"),
                       GetSQLValueString($_POST['user_prod_reporte'], "int"),
                       GetSQLValueString($_POST['user_disciplina'], "int"),
                       GetSQLValueString($_POST['user_proced'], "int"),
                       GetSQLValueString($_POST['user_operaciones_regional'], "int"),
                       GetSQLValueString($_POST['sueldos'], "int"),
                       GetSQLValueString($_POST['calendario'], "int"),
                       GetSQLValueString($_POST['com_vd'], "int"),
                       GetSQLValueString($_POST['rel_lab'], "int"),
                       GetSQLValueString($_POST['contratos'], "int"),
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

  $updateGoTo = "master_admin_usuarios_edit.php?IDusuario=$IDusuario&info=2";
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
							<h5 class="panel-title">Permisos de Usuario</h5>
						</div>

					<div class="panel-body">

							<p>Actualiza la información del usuario.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario:</label>
										<div class="col-lg-9">
						<?php echo htmlentities($row_usuario_['IDusuario'], ENT_COMPAT, ''); ?>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:</label>
										<div class="col-lg-9">
						<?php echo htmlentities($row_usuario_['usuario_parterno']." ".$row_usuario_['usuario_materno']." ".$row_usuario_['usuario_nombre'], ENT_COMPAT, ''); ?>
										</div>
									</div>
									<!-- /basic text input -->
                                   
                            
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
										<label class="control-label col-lg-3">Aumento Anual:</label>
										<div class="col-lg-9">
						                 <select name="user_aumento_anual" id="user_aumento_anual" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_aumento_anual'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_aumento_anual'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Becarios:</label>
										<div class="col-lg-9">
						                 <select name="user_becarios" id="user_becarios" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_becarios'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_becarios'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Casos Sindicato:</label>
										<div class="col-lg-9">
						                 <select name="user_casos_sindicato" id="user_casos_sindicato" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_casos_sindicato'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_casos_sindicato'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Credenciales:</label>
										<div class="col-lg-9">
						                 <select name="user_credenciales" id="user_credenciales" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_credenciales'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_credenciales'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Adicionales Productividad:</label>
										<div class="col-lg-9">
						                 <select name="user_prod_adicionales" id="user_prod_adicionales" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_prod_adicionales'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_prod_adicionales'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Reporte Productividad:</label>
										<div class="col-lg-9">
						                 <select name="user_prod_reporte" id="user_prod_reporte" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_prod_reporte'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_prod_reporte'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Disciplina Progresiva:</label>
										<div class="col-lg-9">
						                 <select name="user_disciplina" id="user_disciplina" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_disciplina'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_disciplina'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Procedimientos:</label>
										<div class="col-lg-9">
						                 <select name="user_proced" id="user_proced" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_proced'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_proced'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">user_operaciones_regionales:</label>
										<div class="col-lg-9">
						                 <select name="user_operaciones_regionales" id="user_operaciones_regionales" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['user_operaciones_regionales'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['user_operaciones_regionales'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldos:</label>
										<div class="col-lg-9">
						                 <select name="sueldos" id="sueldos" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['sueldos'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['sueldos'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Calendario:</label>
										<div class="col-lg-9">
						                 <select name="calendario" id="calendario" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['calendario'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['calendario'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Comisiones VD:</label>
										<div class="col-lg-9">
						                 <select name="com_vd" id="com_vd" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['com_vd'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['com_vd'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Relaciones Laborales:</label>
										<div class="col-lg-9">
						                 <select name="rel_lab" id="rel_lab" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['rel_lab'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['rel_lab'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Contratos:</label>
										<div class="col-lg-9">
						                 <select name="contratos" id="contratos" class="form-control" >
						                   <option value="0" <?php if (!(strcmp(0, htmlentities($row_usuario_['contratos'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>No</option>
						                   <option value="1" <?php if (!(strcmp(1, htmlentities($row_usuario_['contratos'], ENT_COMPAT, 'utf-8')))) {echo "selected=\"selected\"";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->



                         <input class="btn bg-success btn-icon" type="submit" value="Asignar Permisos" />
                         <button type="button" onClick="window.location.href='master_admin_usuarios_edit.php?IDusuario=<?php echo $elusuario; ?>'" class="btn btn-default btn-icon">Regresar</button>
                         <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDusuario" value="<?php echo $row_usuario_['IDusuario']; ?>">

                            </form>
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