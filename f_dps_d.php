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
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$query_estudios = "SELECT * FROM sed_estudios";
$estudios = mysql_query($query_estudios, $vacantes) or die(mysql_error());
$row_estudios = mysql_fetch_assoc($estudios);

$IDpuesto = $_GET['IDpuesto'];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE vac_puestos.IDpuesto = '$IDpuesto'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);

$query_puesto_general = "SELECT sed_dps.IDpuesto, sed_dps.IDcriterio, sed_dps.b_mision, sed_dps.e_jefe_de_jefe, sed_dps.e_jefe, sed_dps.e_pares, sed_dps.e_colaboradores, sed_dps.f_escolaridad, sed_dps.f_avance, sed_dps.f_carreras, sed_dps.f_idioma, sed_dps.f_idioma_nivel, sed_dps.f_otros_estudios, sed_dps.f_conocimientos1, sed_dps.f_conocimientos2, sed_dps.f_conocimientos3, sed_dps.f_conocimientos4, sed_dps.f_conocimientos5, sed_dps.f_conocimientos6, sed_dps.f_exp_areas, sed_dps.f_exp_anios, sed_dps.f_viajar, sed_dps.f_frecuencia, sed_dps.f_edad, sed_dps.f_turnos, sed_dps.IDplaza, vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.descrito FROM sed_dps LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = sed_dps.IDpuesto WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_general = mysql_query($query_puesto_general, $vacantes) or die(mysql_error());
$row_puesto_general = mysql_fetch_assoc($puesto_general);
$totalRows_puesto_general = mysql_num_rows($puesto_general);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO sed_dps (f_escolaridad, f_avance, f_carreras, f_idioma, f_idioma_nivel, f_otros_estudios, f_conocimientos1, f_conocimientos2, f_conocimientos3, f_conocimientos4, f_conocimientos5, f_conocimientos6, f_exp_areas, f_exp_anios, f_viajar, f_frecuencia, f_edad, IDpuesto,captura_b, f_turnos) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['f_escolaridad'], "int"),
                       GetSQLValueString($_POST['f_avance'], "int"),
                       GetSQLValueString($_POST['f_carreras'], "text"),
                       GetSQLValueString($_POST['f_idioma'], "text"),
                       GetSQLValueString($_POST['f_idioma_nivel'], "text"),
                       GetSQLValueString($_POST['f_otros_estudios'], "text"),
                       GetSQLValueString($_POST['f_conocimientos1'], "text"),
                       GetSQLValueString($_POST['f_conocimientos2'], "text"),
                       GetSQLValueString($_POST['f_conocimientos3'], "text"),
                       GetSQLValueString($_POST['f_conocimientos4'], "text"),
                       GetSQLValueString($_POST['f_conocimientos5'], "text"),
                       GetSQLValueString($_POST['f_conocimientos6'], "text"),
                       GetSQLValueString($_POST['f_exp_areas'], "text"),
                       GetSQLValueString($_POST['f_exp_anios'], "int"),
                       GetSQLValueString($_POST['f_viajar'], "int"),
                       GetSQLValueString($_POST['f_frecuencia'], "int"),
                       GetSQLValueString($_POST['f_edad'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['captura_b'], "int"),
                       GetSQLValueString($_POST['f_turnos'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  header("Location: f_dps_d.php?info=1&IDpuesto=$IDpuesto");
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE sed_dps SET f_escolaridad=%s, f_avance=%s, f_carreras=%s, f_idioma=%s, f_idioma_nivel=%s, f_otros_estudios=%s, f_conocimientos1=%s, f_conocimientos2=%s, f_conocimientos3=%s, f_conocimientos4=%s, f_conocimientos5=%s,  f_conocimientos6=%s, f_exp_areas=%s, f_exp_anios=%s, f_viajar=%s, f_frecuencia=%s, f_edad=%s, captura_b=%s, f_turnos=%s WHERE IDpuesto=%s",
                       GetSQLValueString($_POST['f_escolaridad'], "int"),
                       GetSQLValueString($_POST['f_avance'], "int"),
                       GetSQLValueString($_POST['f_carreras'], "text"),
                       GetSQLValueString($_POST['f_idioma'], "text"),
                       GetSQLValueString($_POST['f_idioma_nivel'], "text"),
                       GetSQLValueString($_POST['f_otros_estudios'], "text"),
                       GetSQLValueString($_POST['f_conocimientos1'], "text"),
                       GetSQLValueString($_POST['f_conocimientos2'], "text"),
                       GetSQLValueString($_POST['f_conocimientos3'], "text"),
                       GetSQLValueString($_POST['f_conocimientos4'], "text"),
                       GetSQLValueString($_POST['f_conocimientos5'], "text"),
                       GetSQLValueString($_POST['f_conocimientos6'], "text"),
                       GetSQLValueString($_POST['f_exp_areas'], "text"),
                       GetSQLValueString($_POST['f_exp_anios'], "int"),
                       GetSQLValueString($_POST['f_viajar'], "int"),
                       GetSQLValueString($_POST['f_frecuencia'], "int"),
                       GetSQLValueString($_POST['captura_b'], "int"),
                       GetSQLValueString($_POST['f_edad'], "text"),
                       GetSQLValueString($_POST['f_turnos'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  header("Location: f_dps_d.php?info=2&IDpuesto=$IDpuesto");
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

 <body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">
              
              			<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro se ha agregado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                      	<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro se ha actualizado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro se ha borrado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                                                    
				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
                        
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Descriptivo de Puesto - Entorno y Perfil.</h5>
						</div>

					<div class="panel-body">

			<p><strong>Instrucciones</strong></p>
			<p>Ingresa la información solicitada. Los campos marcados con * son obligatorios.</p>
			<p>&nbsp;</p>

				<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="form-horizontal form-validate-jquery">
                    <fieldset class="content-group">
                    
                    
									<legend class="text-bold">Conocimientos</legend>
                                                                        
                                   <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Escolaridad:*</label>
										<div class="col-lg-4">
                                        <select name="f_escolaridad" id="f_escolaridad" class="form-control" required="required">
                                        <option value="">Selecciona uno...</option>
                              <?php do {  ?>
                              <option value="<?php echo $row_estudios['IDestudios']?>"<?php if (!(strcmp($row_estudios['IDestudios'], $row_puesto_general['f_escolaridad']))) {echo "SELECTED";} ?>><?php echo $row_estudios['estudios']?></option>
		     						  <?php
                                    } while ($row_estudios = mysql_fetch_assoc($estudios));
                                      $rows = mysql_num_rows($estudios);
                                      if($rows > 0) {
                                          mysql_data_seek($estudios, 0);
                                          $row_estudios = mysql_fetch_assoc($estudios);
                                      }
                                    ?>
		                            </select>           
									<span class="help-block">Indique el nivel mínimo requerido que se deberá cubrir para ocupar el puesto.</span>
										</div>
                                    <!-- /basic text input -->


                                   <!-- Basic text input -->
										<label class="control-label col-lg-1">Avance:*</label>
										<div class="col-lg-5">
                                        <select name="f_avance" id="f_avance" class="form-control" required="required">
	                                        <option value="">Selecciona uno...</option>
											<option value="1"<?php if (!(strcmp($row_puesto_general['f_avance'], 1))) {echo "SELECTED";} ?>>Titulado</option>
											<option value="2"<?php if (!(strcmp($row_puesto_general['f_avance'], 2))) {echo "SELECTED";} ?>>Pasante o Terminado</option>
                                            </select>            
									<span class="help-block">Seleccine el estatus mínimo de estudios requerido para la ocupación del puesto.</span>
										</div>
									</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Carreras:*</label>
										<div class="col-lg-10">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                       <textarea name="f_carreras" rows="3" class="form-control" id="f_carreras" placeholder="Indiqe al menos 3 carreras o áreas de estudio relacionadas con las funciones del puesto." 
                                       required="required"><?php echo $row_puesto_general['f_carreras']; ?></textarea>                                 
                                        <?php } else { ?>
                                        <textarea name="f_carreras" rows="3" class="form-control" id="f_carreras" placeholder="Indiqe al menos 3 carreras o áreas de estudio relacionadas con las funciones del puesto."
                                         required="required"></textarea>                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Idiomas:</label>
										<div class="col-lg-4">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_idioma" id="f_idioma" class="form-control" placeholder="Si se requiere para el desarrollo de las funciones del puesto." 
                                        value="<?php echo $row_puesto_general['f_idioma']; ?>">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_idioma" id="f_idioma" class="form-control" placeholder="Si se requiere para el desarrollo de las funciones del puesto." 
                                        value="" required="required">                                 
                                        <?php } ?>
										</div>
										<!-- /basic text input -->

                                       <!-- Basic text input -->
										<label class="control-label col-lg-1">Nivel:</label>
										<div class="col-lg-5">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_idioma_nivel" id="f_idioma_nivel" class="form-control" placeholder="Nivel de dominio del idioma determinado (básico, intermedio, avanzado, experto, técnico)." value="<?php echo $row_puesto_general['f_idioma_nivel']; ?>">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_idioma_nivel" id="e_jf_idioma_nivelefe_de_jefe" class="form-control" placeholder="Nivel de dominio del idioma determinado (básico, intermedio, avanzado, experto, técnico)" value="" required="required">                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Otros estudios:</label>
										<div class="col-lg-10">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <textarea name="f_otros_estudios" rows="3" class="form-control" id="f_otros_estudios" placeholder="Estudios adicionales (técnicos, certificaciones, cursos, etc.) necesarios o deseables relacionados con las áreas de competencia del puesto."><?php echo $row_puesto_general['f_otros_estudios']; ?></textarea>                                 
                                        <?php } else { ?>
                                        <textarea name="f_otros_estudios" rows="3" class="form-control" id="f_otros_estudios" placeholder="Estudios adicionales (técnicos, certificaciones, cursos, etc.) necesarios o deseables relacionados con las áreas de competencia del puesto."></textarea>                                 
                                        <?php } ?>
									</div>
									</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Conocimientos:*</label>
										<div class="col-lg-4">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_conocimientos1" id="f_conocimientos1" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="<?php echo $row_puesto_general['f_conocimientos1']; ?>" required="required">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_conocimientos1" id="f_conocimientos1" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="" required="required">                                 
                                        <?php } ?>
										</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
										<label class="control-label col-lg-1">Conocimientos:</label>
										<div class="col-lg-5">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_conocimientos2" id="f_conocimientos2" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="<?php echo $row_puesto_general['f_conocimientos2']; ?>">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_conocimientos2" id="f_conocimientos2" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="" >                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Conocimientos:</label>
										<div class="col-lg-4">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_conocimientos3" id="f_conocimientos3" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto." value="<?php echo $row_puesto_general['f_conocimientos3']; ?>">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_conocimientos3" id="f_conocimientos3" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="" >                                 
                                        <?php } ?>
										</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
										<label class="control-label col-lg-1">Conocimientos:</label>
										<div class="col-lg-5">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_conocimientos4" id="f_conocimientos4" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="<?php echo $row_puesto_general['f_conocimientos4']; ?>">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_conocimientos4" id="f_conocimientos4" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="" >                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Conocimientos:</label>
										<div class="col-lg-4">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_conocimientos5" id="f_conocimientos5" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto." value="<?php echo $row_puesto_general['f_conocimientos5']; ?>">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_conocimientos5" id="f_conocimientos5" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto."  value="">                                 
                                        <?php } ?>
										</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
										<label class="control-label col-lg-1">Conocimientos:</label>
										<div class="col-lg-5">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_conocimientos6" id="f_conocimientos6" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto." value="<?php echo $row_puesto_general['f_conocimientos6']; ?>">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_conocimientos6" id="f_conocimientos6" class="form-control" placeholder="Conocimientos técnicos para el desarrollo de las funciones del puesto." value="">                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->

									<legend class="text-bold">Experiencia</legend>

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Experiencia Laboral:*</label>
										<div class="col-lg-10">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <textarea name="f_exp_areas" rows="3" class="form-control" id="f_exp_areas" placeholder="Indique al menos 3 áreas de experiencia que estén relacionadas con las funciones del puesto." required="required"><?php echo $row_puesto_general['f_exp_areas']; ?></textarea>                                 
                                        <?php } else { ?>
                                        <textarea name="f_exp_areas" rows="3" class="form-control" id="f_exp_areas" placeholder="Indique al menos 3 áreas de experiencia que estén relacionadas con las funciones del puesto." required="required"></textarea>                                 
                                        <?php } ?>
									</div>
									</div>
									<!-- /basic text input -->
                                    
                                    
                                   <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Años de Experiencia:*</label>
										<div class="col-lg-4">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_exp_anios" id="f_exp_anios" class="form-control" placeholder="Tiempo de experiencia laboral minimo requerido." 
                                        value="<?php echo $row_puesto_general['f_exp_anios']; ?>" required="required">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_exp_anios" id="f_exp_anios" class="form-control" placeholder="Tiempo de experiencia laboral minimo requerido." 
                                        value="" required="required">                                 
                                        <?php } ?>
										</div>
									<!-- /basic text input -->
                                    
                                   <!-- Basic text input -->
										<label class="control-label col-lg-1">Rango de edad:*</label>
										<div class="col-lg-5">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="f_edad" id="f_edad" class="form-control" placeholder="Indique el rango mínimo y máximo de años." 
                                        value="<?php echo $row_puesto_general['f_edad']; ?>" required="required">                                 
                                        <?php } else { ?>
                                        <input type="text" name="f_edad" id="f_edad" class="form-control" placeholder="Indique el rango mínimo y máximo de años." 
                                        value="" required="required">                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->      

									<legend class="text-bold">Requerimietos específicos</legend>

                                   <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Requiere Viajar:*</label>
										<div class="col-lg-2">
                                        <select name="f_viajar" id="f_viajar" class="form-control" required="required">
	                                        <option value="">Selecciona uno...</option>
											<option value="1"<?php if (!(strcmp($row_puesto_general['f_viajar'], 1))) {echo "SELECTED";} ?>>Si</option>
											<option value="0"<?php if (!(strcmp($row_puesto_general['f_viajar'], 0))) {echo "SELECTED";} ?>>No</option>
                                            </select>            
										</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
										<label class="control-label col-lg-1">Frecuencia:*</label>
										<div class="col-lg-3">
                                        <select name="f_frecuencia" id="f_frecuencia" class="form-control" required="required">
	                                        <option value="">Selecciona uno...</option>
											<option value="1"<?php if (!(strcmp($row_puesto_general['f_frecuencia'], 1))) {echo "SELECTED";} ?>>En ocasiones</option>
											<option value="2"<?php if (!(strcmp($row_puesto_general['f_frecuencia'], 2))) {echo "SELECTED";} ?>>Frecuentemente</option>
											<option value="0"<?php if (!(strcmp($row_puesto_general['f_frecuencia'], 0))) {echo "SELECTED";} ?>>No aplica</option>
                                            </select>            
										</div>
									<!-- /basic text input -->

      
                                   <!-- Basic text input -->
										<label class="control-label col-lg-1">Rolar turnos:*</label>
										<div class="col-lg-3">
                                        <select name="f_turnos" id="f_turnos" class="form-control" required="required">
	                                        <option value="">Selecciona uno...</option>
											<option value="1"<?php if (!(strcmp($row_puesto_general['f_turnos'], 1))) {echo "SELECTED";} ?>>Si</option>
											<option value="0"<?php if (!(strcmp($row_puesto_general['f_turnos'], 0))) {echo "SELECTED";} ?>>No</option>
                                            </select>            
										</div>
									</div>
									<!-- /basic text input -->


								<div class="modal-footer">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                 	<input type="hidden" name="MM_update" value="form1">
                                    <input type="submit" class="btn btn-primary" value="Actualizar">
										<?php } else { ?>
                                 	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="submit" class="btn btn-primary" value="Agregar">
                                        <?php } ?>
                                 	<input type="hidden" name="captura_b" value="1">
                                 	<input type="hidden" name="IDpuesto" value="<?php echo $IDpuesto; ?>">
								</div>
                           
                    </fieldset>
				</form>



                    <!-- vista video modal -->
					<div id="modal_theme_ver" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar DPs</h6>
								</div>


				<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="form-horizontal form-validate-jquery">
                    <fieldset class="content-group">

								<div class="modal-body">

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Tipo:*</label>
										<div class="col-lg-9">
                                        <select name="criterio_a" id="criterio_a" class="form-control" required="required">
											<option value="1">Internas</option>
											<option value="2">Externas</option>
                                            </select>            
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">¿Con quien?:*</label>
										<div class="col-lg-9">
											<textarea rows="4" class="form-control" id="criterio_b" name="criterio_b" placeholder="Proveedores"
                                             required="required"></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">¿Para que?:*</label>
										<div class="col-lg-9">
											<textarea rows="4" class="form-control" id="criterio_c" name="criterio_c" placeholder="Para la negociación de precios."
                                             required="required"></textarea>
										</div>
									</div>
									<!-- /basic text input -->

								</div>

<hr>
								<div class="modal-footer">
                                 	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="submit" class="btn btn-success" value="Agregar">
                                 	<input type="hidden" name="criterio" value="d">
                                 	<input type="hidden" name="IDpuesto" value="<?php echo $IDpuesto; ?>">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
                           
                    </fieldset>
				</form>
							</div>
						</div>
					</div>
					<!-- /vista video modal -->








                    
                    </div>
                    </div>

					<!-- /Contenido -->



                            
						</div>
					</div>
					<!-- /detached content -->


					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">


								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Acciones</span>
									</div>

									<div class="category-content">

										<div class="form-group">
									<a class="btn btn-xs btn-primary btn-block content-group" href="f_dps_desc.php?IDpuesto=<?php echo $IDpuesto; ?>">Regresar al resumen</a>
										</div>

									<?php if ($row_puesto['descrito'] == 3) { ?>
										<div class="form-group">
                                        <a class="btn btn-xs btn-success btn-block content-group" href="dps/f_imprimir.php?IDpuesto=<?php echo $IDpuesto; ?>">Imprimir</a>
                                        </div>
									<?php } ?>

									</div>
								</div>
								<!-- /course details -->


								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Información del Puesto</span>
									</div>

									<div class="category-content">

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Denominacióm:</label>
											<div><?php echo $row_puesto['denominacion']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $row_puesto['area']; ?></div>
										</div></div>
								</div>
								<!-- /course details -->

							</div>
						</div>
					</div>
		            <!-- /detached sidebar -->


					<!-- /Contenido -->

				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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