<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_nom35 = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
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

$IDpuesto = $_GET['IDpuesto'];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.tipo_funciones, vac_puestos.familia, vac_puestos.grupo, vac_puestos.grado, vac_puestos.nivel, vac_puestos.sueldo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE vac_puestos.IDpuesto = '$IDpuesto'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);

$query_puesto_general = "SELECT sed_dps.IDpuesto, sed_dps.IDcriterio, sed_dps.b_mision, sed_dps.e_jefe_de_jefe, sed_dps.e_jefe, sed_dps.e_pares, sed_dps.e_colaboradores, sed_dps.f_escolaridad, sed_dps.f_avance, sed_dps.f_carreras, sed_dps.f_idioma, sed_dps.f_idioma_nivel, sed_dps.f_otros_estudios, sed_dps.f_conocimientos1, sed_dps.f_conocimientos2, sed_dps.f_conocimientos3, sed_dps.f_conocimientos4, sed_dps.f_conocimientos5, sed_dps.f_exp_areas, sed_dps.f_exp_anios, sed_dps.f_viajar, sed_dps.f_frecuencia, sed_dps.f_edad, sed_dps.f_turnos, sed_dps.IDplaza, vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.descrito FROM sed_dps LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = sed_dps.IDpuesto WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_general = mysql_query($query_puesto_general, $vacantes) or die(mysql_error());
$row_puesto_general = mysql_fetch_assoc($puesto_general);

$query_puesto_catalogos = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.descrito, sed_dps_catalogos.IDcriterio, sed_dps_catalogos.IDpuesto, sed_dps_catalogos.criterio, sed_dps_catalogos.criterio_a, sed_dps_catalogos.criterio_b, sed_dps_catalogos.criterio_c, sed_dps_catalogos.criterio_d, sed_dps_catalogos.IDplaza FROM  vac_puestos INNER JOIN sed_dps_catalogos ON vac_puestos.IDpuesto = sed_dps_catalogos.IDpuesto WHERE  sed_dps_catalogos.IDpuesto = '$IDpuesto' AND  sed_dps_catalogos.criterio = 'z'";
$puesto_catalogos = mysql_query($query_puesto_catalogos, $vacantes) or die(mysql_error());
$row_puesto_catalogos = mysql_fetch_assoc($puesto_catalogos);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$IDpuesto_array   = $_POST['IDpuesto'];
    $criterio_array   = $_POST['criterio'];
    $criterio_a_array = $_POST['criterio_a'];
    $criterio_b_array = $_POST['criterio_b'];
	
    for ($i = 0; $i < count($IDpuesto_array); $i++) {

        $IDpuesto =   mysql_real_escape_string($IDpuesto_array[$i]);
        $criterio =   mysql_real_escape_string($criterio_array[$i]);
        $criterio_a = mysql_real_escape_string($criterio_a_array[$i]);
        $criterio_b = mysql_real_escape_string($criterio_b_array[$i]);

        $updateSQL = "INSERT INTO sed_dps_catalogos (IDpuesto, criterio, criterio_a, criterio_b) VALUES ('$IDpuesto', '$criterio', '$criterio_a', '$criterio_b')";
		mysql_select_db($database_vacantes, $vacantes);
	    $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

    } 

//  $insertGoTo = "f_dps_desc.php?IDpuesto=$IDpuesto&info=1";
//  if (isset($_SERVER['QUERY_STRING'])) {
//    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
//    $insertGoTo .= $_SERVER['QUERY_STRING'];
//  }
//  header(sprintf("Location: %s", $insertGoTo));
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE sed_dps SET IDcriterio=%s, b_mision=%s, e_jefe_de_jefe=%s, e_jefe=%s, e_pares=%s, e_colaboradores=%s, f_escolaridad=%s, f_avance=%s, f_carreras=%s, f_idioma=%s, f_idioma_nivel=%s, f_otros_estudios=%s, f_conocimientos1=%s, f_conocimientos2=%s, f_conocimientos3=%s, f_conocimientos4=%s, f_conocimientos5=%s, f_exp_areas=%s, f_exp_anios=%s, f_viajar=%s, f_frecuencia=%s, f_edad=%s, f_turnos=%s, IDplaza=%s WHERE IDpuesto=%s",
                       GetSQLValueString($_POST['f_viajar'], "int"),
                       GetSQLValueString($_POST['f_frecuencia'], "int"),
                       GetSQLValueString($_POST['f_edad'], "text"),
                       GetSQLValueString($_POST['f_turnos'], "int"),
                       GetSQLValueString($_POST['IDplaza'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "ddd.php";
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

			<!-- Main content -->
			<div class="content-wrapper">

			<!-- Content area -->
			  <div class="content">
              
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Descriptivo de Puesto - Requerimientos de Equipo del Puesto</h5>
						</div>

					<div class="panel-body">
            <p><strong>Denominación:</strong> <?php echo $row_puesto['denominacion']; ?></p>
            <p><strong>Área: </strong><?php echo $row_puesto['area']; ?></p>
            <p><strong>Ocupante:</strong> <?php echo $row_usuario['emp_nombre']. " ". $row_usuario['emp_paterno']." ". $row_usuario['emp_materno']; ?></p>
			<p>	&nbsp; </p>     	
			<p>Capture la información requerida: </p>


				<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="form-horizontal form-validate-jquery">
                                <fieldset class="content-group">

									<?php do { ?>	

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group">
                                         <?php  if ($row_puesto_catalogos['criterio_a'] == 1) { echo "Equipo de Cómputo";} 
										 	elseif ($row_puesto_catalogos['criterio_a'] == 2) { echo "Software";} 
											elseif ($row_puesto_catalogos['criterio_a'] == 3) { echo "Automovil";} 
										 	elseif ($row_puesto_catalogos['criterio_a'] == 4) { echo "Teléfono";} 
										 	elseif ($row_puesto_catalogos['criterio_a'] == 5) { echo "Otros";}
											  else { echo "n/a";} ?>: *</div></label>
										<div class="col-lg-10">
											<textarea rows="2" class="form-control" id="criterio_b[]" name="criterio_b[]" placeholder="Captura la información solicitada." 
                                            required="required"></textarea>
										</div>
									</div>
                                 		<input type="hidden" name="criterio[]" id="criterio[]" value="z">
                                 		<input type="hidden" name="IDpuesto[]" id="IDpuesto[]"value="<?php echo $IDpuesto; ?>">
                                 		<input type="hidden" id="criterio_a[]" name="criterio_a[]" value="<?php $row_puesto_catalogos['criterio_a'] ?>">
									<!-- /basic text input -->

									<?php  } while ($row_puesto_catalogos = mysql_fetch_assoc($puesto_catalogos)); ?>	
                                    
                                    
                                 		<input type="hidden" name="MM_insert" value="form1">
                                        <input type="submit" class="btn btn-primary" value="Guardar">
                         				<button type="button" onClick="window.location.href='f_dps_desc.php?IDpuesto=<?php echo $row_puesto['IDpuesto']; ?>'" class="btn btn-default btn-icon">Cancelar</button>

</fieldset>
</form>
                    
                    </div>

					<!-- /Contenido -->
                </div>
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