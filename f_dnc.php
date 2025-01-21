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
$query_usuario = sprintf("SELECT prod_activos.nivel_acceso, vac_matriz.IDmatriz, vac_matriz.matriz, vac_matriz.IDmatriz, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.IDmatriz, prod_activos.emp_materno, prod_activos.IDarea, prod_activos.IDpuesto, prod_activos.fecha_antiguedad, prod_activos.IDmatriz, prod_activos.emp_nombre, prod_activos.rfc13, prod_activos.curp, prod_activos.fecha_alta, prod_activos.denominacion, capa_dnc.IDdnc, vac_puestos.denominacion, vac_areas.area  FROM prod_activos LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN capa_dnc ON prod_activos.IDempleado = capa_dnc.IDempleado LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON  prod_activos.IDarea = vac_areas.IDarea WHERE prod_activos.IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];
$IDempleado = $row_usuario['IDempleado'];
$el_usuario = $row_usuario['IDempleado'];
$IDarea = $row_usuario['IDarea'];
$antiguedad = $row_usuario['fecha_antiguedad'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_dnc = "SELECT * FROM capa_dnc WHERE IDempleado = $IDempleado";
$dnc = mysql_query($query_dnc, $vacantes) or die(mysql_error());
$row_dnc = mysql_fetch_assoc($dnc);
$totalRows_dnc = mysql_num_rows($dnc);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$insertSQL = sprintf("INSERT INTO capa_dnc (IDempleado, IDpuesto, antiguedad_actual, personal_a_cargo ,personal_a_cargo_cuantos, subdireccion, anio, actualizado, funcion_1, funcion_2, funcion_3, funcion_4, funcion_5, funcion_6, funcion_7, funcion_nivel_1, funcion_nivel_2, funcion_nivel_3, funcion_nivel_4, funcion_nivel_5, funcion_nivel_6, funcion_nivel_7, escolar, dipomas, modalidad,
observaciones, 3_1_1, 3_1_2, 3_1_3, 3_1_4,  3_1_5, 3_1_6, 3_1_7, 3_1_8, 3_1_9, 3_1_10, 3_1_11, 3_1_12, 3_1_13, 3_1_14, 3_1_15, 3_1_16, 3_1_17, 3_1_18, 3_1_19, 3_1_20, 3_1_21, 3_1_22, 3_2_1, 3_2_2, 3_2_3, 3_2_4, 3_2_5, 3_2_6, 3_3_1, 3_3_2, 3_3_3, 3_3_4, 3_3_5, 3_3_6, 3_3_7, 3_3_8, 3_3_9, 3_4_1, 3_4_2, 3_4_3, 3_4_4, 3_4_5, 3_4_6, 3_4_7, 3_4_8, 3_4_9, 3_4_10, 3_4_11, 3_4_12, 3_4_13, 3_4_14, 3_5_1, 4_1_a, 4_1_b, 4_2_a, 4_2_b, 4_3_a, 4_3_b, 4_4_a, 4_4_b, 4_5_a, 4_5_b, 4_6_a, 4_6_b, 4_7_a, 4_7_b, 4_8_a, 4_8_b, 4_9_a, 4_9_b ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "text"),
                       GetSQLValueString($nombre, "text"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['emergencias'], "int"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['fecha_nacimiento'], "text"),
                       GetSQLValueString($direccion, "text"),
                       GetSQLValueString($_POST['observaciones'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

	//header("Location: empleados_beneficiarios.php?IDempleado=$IDempleado&info=1"); 	
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/1picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>


	</head>
</head>

<body class="has-detached-right">
	
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
			
					<!-- Contenido -->
                  <div class="panel panel-flat">

					<div class="panel-body">
							<p><b>Instrucciones</b><br/>
							</p>						
							<p>A continuación se muestran una serie de preguntas relacionadas con sus necesidades de capacitación. Responda según sus necesidades actuales.</p>


                        <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-success alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
                            Ajuste guardado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


              <form method="post" id="form1" action="f_dnc.php">
                <table cellpadding="2" cellspacing="0" class="table table-condensed">
                  <tr>
                    <td colspan="3">
					<strong>Datos del Ocupante</strong><br />A continuación se muestran sus datos personales y del puesto que actualmente ocupa. Por favor complete la información restante.
                  </td>
                  </tr>
                  <tr>
                    <td><strong>Número de Empleado</strong>: <?php echo $row_usuario['IDempleado']; ?></td>
                    <td colspan="2"><strong>Antiguedad actual</strong>:
					  <select name="antiguedad_actual" id="antiguedad_actual" class="form-control">
						<option value="60" <?php if (!(strcmp(60, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>menos de 6 meses</option>
						<option value="120" <?php if (!(strcmp(120, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>de 6 meses a 1 año</option>
						<option value="1" <?php if (!(strcmp(1, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>1 año</option>
						<option value="2" <?php if (!(strcmp(2, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>2 años</option>
						<option value="3" <?php if (!(strcmp(3, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>3 años</option>
						<option value="4" <?php if (!(strcmp(4, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>4 años</option>
						<option value="5" <?php if (!(strcmp(5, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>5 años</option>
						<option value="6" <?php if (!(strcmp(6, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>6 años</option>
						<option value="7" <?php if (!(strcmp(7, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>7 años</option>
						<option value="8" <?php if (!(strcmp(8, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>8 años</option>
						<option value="9" <?php if (!(strcmp(9, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>9 años</option>
						<option value="10" <?php if (!(strcmp(10, KT_escapeAttribute($row_dnc['antiguedad_actual'])))) {echo "SELECTED";} ?>>10 años o mas</option>
						</select>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Nombre:</strong> <?php echo $row_usuario['emp_nombre'] .' '.  $row_usuario['emp_paterno'] .' '.  $row_usuario['emp_materno']; ?></td>
                    <td colspan="2"><strong>Cuenta con personal a cargo:</strong>
                      <input <?php if ($row_dnc['personal_a_cargo'] == 1) {echo "//checked//";} ?> class="control-form" type="radio" name="personal_a_cargo" id="personal_a_cargo_1" value="1" />
						Si &nbsp;
                      <input <?php if ($row_dnc['personal_a_cargo'] == 0) {echo "//checked//";} ?> class="control-form" type="radio" name="personal_a_cargo" id="personal_a_cargo_1" value="0" />
						No </td>
                  </tr>
                  <tr>
                    <td><strong>Puesto:</strong> <?php echo $row_usuario['denominacion']; ?></td>
                    <td><strong>¿Cuantos?</strong></td>
                    <td><input type="text" class="form-control" name="personal_a_cargo_cuantos" id="personal_a_cargo_cuantos" value="<?php echo KT_escapeAttribute($row_dnc['personal_a_cargo_cuantos']); ?>" size="5" />
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Antiguedad en la empresa:</strong> <?php echo $antiguedad; ?></td>
                    <td colspan="2"><strong>Área:</strong> <?php echo $row_usuario['area']; ?></td>
                  </tr>
                  <tr>
                    <td colspan="3"><p>&nbsp;</p>
                      <p><strong>Capacitación Orientada a Funciones</strong><br />
                        Describa sus principales funciones o actividades que realiza, considerando las establecidas en su descripción de puesto o en sus términos de contrato Si no cuenta con descripción o términos de contrato, describa sus funciones de acuerdo a lo que actualmente desempeña. Posteriormente seleccione el nivel de domino o logro en cada función. Capture al menos tres funciones.<br />
                    </p>
                    
                    <p><strong>Ejemplo: </strong><em>Elaborar reportes y escritos de auditoria para proporcionar información veraz y actualizada de la situación contractual de cada proyecto y el porcentaje de avance en el que se encuentra.</em></p></td>
                  </tr>
                  <tr>
                    <td><strong>Función 1
                      </strong>
                      <input type="text" class="form-control" name="funcion_1" id="funcion_1" value="<?php echo KT_escapeAttribute($row_dnc['funcion_1']); ?>" size="80" />
                    </td>
                    <td colspan="2">Nivel de Dominio:<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_1']),"4"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_1" id="funcion_nivel_1_1" value="4" />
                      Excelente &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_1']),"3"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_1" id="funcion_nivel_1_2" value="3" />
                      Bueno &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_1']),"2"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_1" id="funcion_nivel_1_3" value="2" />
                      Regular &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_1']),"1"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_1" id="funcion_nivel_1_4" value="1" />
                      Deficiente
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Función 2
                      </strong>
                      <input type="text" class="form-control" name="funcion_2" id="funcion_2" value="<?php echo KT_escapeAttribute($row_dnc['funcion_2']); ?>" size="80" />
                    </td>
                    <td colspan="2">Nivel de Dominio:<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_2']),"4"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_2" id="funcion_nivel_2_1" value="4" />
                      Excelente &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_2']),"3"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_2" id="funcion_nivel_2_2" value="3" />
                      Bueno &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_2']),"2"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_2" id="funcion_nivel_2_3" value="2" />
                      Regular &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_2']),"1"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_2" id="funcion_nivel_2_4" value="1" />
                      Deficiente
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Función 3
                      </strong>
                      <input type="text" class="form-control" name="funcion_3" id="funcion_3" value="<?php echo KT_escapeAttribute($row_dnc['funcion_3']); ?>" size="80" />
                    </td>
                    <td colspan="2">Nivel de Dominio:<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_3']),"4"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_3" id="funcion_nivel_3_1" value="4" />
                      Excelente &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_3']),"3"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_3" id="funcion_nivel_3_2" value="3" />
                      Bueno &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_3']),"2"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_3" id="funcion_nivel_3_3" value="2" />
                      Regular &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_3']),"1"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_3" id="funcion_nivel_3_4" value="1" />
                      Deficiente
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Función 4
                      </strong>
                      <input type="text" class="form-control" name="funcion_4" id="funcion_4" value="<?php echo KT_escapeAttribute($row_dnc['funcion_4']); ?>" size="80" />
                    </td>
                    <td colspan="2">Nivel de Dominio:<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_4']),"4"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_4" id="funcion_nivel_4_1" value="4" />
                      Excelente &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_4']),"3"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_4" id="funcion_nivel_4_2" value="3" />
                      Bueno &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_4']),"2"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_4" id="funcion_nivel_4_3" value="2" />
                      Regular &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_4']),"1"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_4" id="funcion_nivel_4_4" value="1" />
                      Deficiente
                   </td>
                  </tr>
                  <tr>
                    <td><strong>Función 5
                      </strong>
                      <input type="text" class="form-control" name="funcion_5" id="funcion_5" value="<?php echo KT_escapeAttribute($row_dnc['funcion_5']); ?>" size="80" />
                   </td>
                    <td colspan="2">Nivel de Dominio:<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_5']),"4"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_5" id="funcion_nivel_5_1" value="4" />
                      Excelente &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_5']),"3"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_5" id="funcion_nivel_5_2" value="3" />
                      Bueno &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_5']),"2"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_5" id="funcion_nivel_5_3" value="2" />
                      Regular &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_5']),"1"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_5" id="funcion_nivel_5_4" value="1" />
                      Deficiente
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Función 6
                      </strong>
                      <input type="text" class="form-control" name="funcion_6" id="funcion_6" value="<?php echo KT_escapeAttribute($row_dnc['funcion_6']); ?>" size="80" />
                    </td>
                    <td colspan="2">Nivel de Dominio:<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_6']),"4"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_6" id="funcion_nivel_6_1" value="4" />
                      Excelente &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_6']),"3"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_6" id="funcion_nivel_6_2" value="3" />
                      Bueno &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_6']),"2"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_6" id="funcion_nivel_6_3" value="2" />
                      Regular &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_6']),"1"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_6" id="funcion_nivel_6_4" value="1" />
                      Deficiente
                   </td>
                  </tr>
                  <tr>
                    <td><strong>Función 7</strong>
                      <input type="text" class="form-control" name="funcion_7" id="funcion_7" value="<?php echo KT_escapeAttribute($row_dnc['funcion_7']); ?>" size="80" />
                    </td>
                    <td colspan="2">Nivel de Dominio:<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_7']),"4"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_7" id="funcion_nivel_7_1" value="4" />
                      Excelente &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_7']),"3"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_7" id="funcion_nivel_7_2" value="3" />
                      Bueno &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_7']),"2"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_7" id="funcion_nivel_7_3" value="2" />
                      Regular &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['funcion_nivel_7']),"1"))) {echo "//checked//";} ?> type="radio" name="funcion_nivel_7" id="funcion_nivel_7_4" value="1" />
                      Deficiente
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3"><p>&nbsp;</p>
                      <p><strong>Capacitación Orientada a Funciones</strong><br />
                      Seleccione las áreas ó temas de capacitación que podrían mejorar el logro de sus funciones.</p></td>
                  </tr>
                  <tr>
                    <td colspan="3"><em><strong>Técnicas / Habilidades y Conocimientos necesarios para el desempeño básico en el puesto.</strong></em></td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_1']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_1" id="3_1_1" value="1" />
                     Cobranza efectiva</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_12']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_12" id="3_1_12" value="1" />
                       Devoluciones</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_2']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_2" id="3_1_2" value="1" />
                     Uso y Manejo de IBS</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_13']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_13" id="3_1_13" value="1" />
                       Cancelaciones		</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_3']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_3" id="3_1_3" value="1" />
                     Técnicas de venta </td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_14']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_14" id="3_1_14" value="1" />
                       Facturación	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_4']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_4" id="3_1_4" value="1" />
                     Manejo de objeciones	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_15']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_15" id="3_1_15" value="1" />
                       Merma	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_5']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_5" id="3_1_5" value="1" />
                     Manejo de Excel o alguna otro programa (especifique en observaciones)</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_16']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_16" id="3_1_16" value="1" />
                       Energías renovables</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_6']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_6" id="3_1_6" value="1" />
                     Promoción de ofertas		</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_17']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_17" id="3_1_17" value="1" />
                       Redacción	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_7']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_7" id="3_1_7" value="1" />
                     Pláticas de seguridad para prevención de siniestros	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_18']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_18" id="3_1_18" value="1" />
                      Oratoria	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_8']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_8" id="3_1_8" value="1" />
                    Manejo a la defensiva </td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_19']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_19" id="3_1_19" value="1" />
                       Presentaciones eficaces			</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_9']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_9" id="3_1_9" value="1" />
                     Análisis Financiero</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_20']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_20" id="3_1_20" value="1" />
                      Finanzas aplicadas a ventas	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_10']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_10" id="3_1_10" value="1" />
                    Sistema de Gestión de la Calidad </td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_21']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_21" id="3_1_21" value="1" />
                      Combustibles</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_11']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_11" id="3_1_11" value="1" />
                     Construcción de indicadores y objetivos	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_1_22']),"1"))) {echo "checked";} ?> type="checkbox" name="3_1_22" id="3_1_22" value="1" />
                       Actualización  (especifique en observaciones)	</td>
                  </tr>
                  <tr>
                    <td colspan="3"><em><strong>Administrativas </strong></em></td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_2_1']),"1"))) {echo "checked";} ?> type="checkbox" name="3_2_1" id="3_2_1" value="1" />
                     Administración del tiempo </td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_2_4']),"1"))) {echo "checked";} ?> type="checkbox" name="3_2_4" id="3_2_4" value="1" />
                       Proceso administrativo</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_2_2']),"1"))) {echo "checked";} ?> type="checkbox" name="3_2_2" id="3_2_2" value="1" />
                     Organización	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_2_5']),"1"))) {echo "checked";} ?> type="checkbox" name="3_2_5" id="3_2_5" value="1" />
                     Análisis de la información</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_2_3']),"1"))) {echo "checked";} ?> type="checkbox" name="3_2_3" id="3_2_3" value="1" />
                     Planeación	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_2_6']),"1"))) {echo "checked";} ?> type="checkbox" name="3_2_6" id="3_2_6" value="1" />
                      Administración de Proyectos	</td>
                  </tr>
                  <tr>
                    <td colspan="3"><em><strong>Protección Civil y Vigilancia</strong></em></td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_1']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_1" id="3_3_1" value="1" />
                     Primeros Auxilios	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_6']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_6" id="3_3_6" value="1" />
                       Manejo y Almacenamiento de materiales	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_2']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_2" id="3_3_2" value="1" />
                     Prevención y Combate de incendios</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_7']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_7" id="3_3_7" value="1" />
                      Trabajos en altura	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_3']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_3" id="3_3_3" value="1" />
                     Evacuación, búsqueda y rescate</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_8']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_8" id="3_3_8" value="1" />
                       Equipos de Protección Personal (EPP)	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_4']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_4" id="3_3_4" value="1" />
                    Seguridad y Comunicaciones</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_9']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_9" id="3_3_9" value="1" />
                      Colores y señales de seguridad				</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_3_5']),"1"))) {echo "checked";} ?> type="checkbox" name="3_3_5" id="3_3_5" value="1" />
                    Manejo, Transporte y Almacenamiento de sustancias peligrosas	</td>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3"><em><strong>Social</strong></em></td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_1']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_1" id="3_4_1" value="1" />
                     Resolución de conflictos</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_8']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_8" id="3_4_8" value="1" />
                      Negociación	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_2']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_2" id="3_4_2" value="1" />
                     Equipos de trabajo </td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_9']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_9" id="3_4_9" value="1" />
                      Poder de influencia	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_3']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_3" id="3_4_3" value="1" />
                    Comunicación 	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_10']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_10" id="3_4_10" value="1" />
                      Liderazgo	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_4']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_4" id="3_4_4" value="1" />
                     Relaciones Interpersonales</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_11']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_11" id="3_4_11" value="1" />
                      Análisis de Problemas y Toma de decisiones	</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_5']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_5" id="3_4_5" value="1" />
                     Inteligencia emocional	</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_12']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_12" id="3_4_12" value="1" />
                     Autoestima		</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_6']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_6" id="3_4_6" value="1" />
                     Asertividad		</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_13']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_13" id="3_4_13" value="1" />
                     Imagen Profesional		</td>
                  </tr>
                  <tr>
                    <td><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_7']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_7" id="3_4_7" value="1" />
                     Atención y Servicio</td>
                    <td colspan="2"><input  <?php if (!(strcmp(KT_escapeAttribute($row_dnc['3_4_14']),"1"))) {echo "checked";} ?> type="checkbox" name="3_4_14" id="3_4_14" value="1" />
                     Motivación y Superación	</td>
                  </tr>
                  <tr>
                    <td colspan="3"><em><strong>Otros (especifique).
                      </strong>
                      <input type="text" name="3_5_1" id="3_5_1" class="form-control" value="<?php echo KT_escapeAttribute($row_dnc['3_5_1']); ?>" size="120" />
                    </em></td>
                  </tr>
                  <tr>
                    <td colspan="3"><p>&nbsp;</p>
                      <p><strong>Capacitación Orientada al Desarrollo de Habilidades.</strong><br />
                        
                    Defina el curso con base al puesto que ocupa.</p></td>
                  </tr>
                  <tr>
                    <td>Fortalecer el conocimiento y dominio de las actividades de mi puesto.<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_1_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_1_a" id="4_1_a_1" value="1" />
                      Si &nbsp;
  <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_1_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_1_a" id="4_1_a_2" value="0" />
                    No </td>
                    <td colspan="2">Quiero fortalecerme en:
                      <br />
                      <input type="text" class="form-control" name="4_1_b" id="4_1_b" value="<?php echo KT_escapeAttribute($row_dnc['4_1_b']); ?>" size="32" />
                    </td>
                  </tr>
                  <tr>
                    <td>Organizar mejor mi trabajo para el logro de objetivos.<br />
  <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_2_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_2_a" id="4_2_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_2_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_2_a" id="4_2_a_2" value="0" />
                    No </td>
                    <td colspan="2">Actividades que quiero organizar:<br />
  <input type="text" name="4_2_b" id="4_2_b" class="form-control" value="<?php echo KT_escapeAttribute($row_dnc['4_2_b']); ?>" size="32" />
                    </td>
                  </tr>
                  <tr>
                    <td>Mejorar mis relaciones con mis compañeros(as) y jefes inmediatos.<br />
  <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_3_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_3_a" id="4_3_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_3_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_3_a" id="4_3_a_2" value="0" />
                    No </td>
                    <td colspan="2">Relaciones que quiero mejorar:<br />
  <input type="text" name="4_3_b" class="form-control" id="4_3_b" value="<?php echo KT_escapeAttribute($row_dnc['4_3_b']); ?>" size="32" />
                   </td>
                  </tr>
                  <tr>
                    <td>Conocer lineamientos, funciones designadas, políticas y procedimientos.<br />
  <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_4_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_4_a" id="4_4_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_4_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_4_a" id="4_4_a_2" value="0" />
                    No </td>
                    <td colspan="2">Deseo capacitación para:
                      <br />
                      <input type="text" class="form-control" name="4_4_b" id="4_4_b" value="<?php echo KT_escapeAttribute($row_dnc['4_4_b']); ?>" size="32" />
                   </td>
                  </tr>
                  <tr>
                    <td>Mejorar la comunicación con mis compañeros(as) y jefes inmediatos.<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_5_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_5_a" id="4_5_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_5_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_5_a" id="4_5_a_2" value="0" />
                    No </td>
                    <td colspan="2">Iniciativas que quiero desarrollar:<br />
                      <input type="text" class="form-control" name="4_5_b" id="4_5_b" value="<?php echo KT_escapeAttribute($row_dnc['4_5_b']); ?>" size="32" />
                    </td>
                  </tr>
                  <tr>
                    <td>Mejorar el grado de precisión y calidad en mi trabajo.<br />
  <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_6_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_6_a" id="4_6_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_6_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_6_a" id="4_6_a_2" value="0" />
                    No </td>
                    <td colspan="2">Quiero mejorar en:<br />
  <input type="text" name="4_6_b" id="4_6_b" class="form-control" value="<?php echo KT_escapeAttribute($row_dnc['4_6_b']); ?>" size="32" />                    
                    </td>
                  </tr>
                  <tr>
                    <td>Desarrollar actitudes positivas hacia mis compañeros/as y mejorar la atención a usuarios.<br />
  <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_7_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_7_a" id="4_7_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_7_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_7_a" id="4_7_a_2" value="0" />
                    No </td>
                    <td colspan="2">Requiero capacitación en:<br />
  <input type="text" name="4_7_b" id="4_7_b" class="form-control" value="<?php echo KT_escapeAttribute($row_dnc['4_7_b']); ?>" size="32" />
                    </td>
                  </tr>
                  <tr>
                    <td>Capacitación para mejorar mi autoestima e incrementar conductas positivas.<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_8_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_8_a" id="4_8_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_8_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_8_a" id="4_8_a_2" value="0" />
                    No </td>
                    <td colspan="2">Requiero capacitación en:<br />
  <input type="text" name="4_8_b" id="4_8_b" class="form-control" value="<?php echo KT_escapeAttribute($row_dnc['4_8_b']); ?>" size="32" />
                    </td>
                  </tr>
                  <tr>
                    <td>Integrar y desarrollar al personal que forma parte de mi trabajo en equipo.<br />
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_9_a']),"1"))) {echo "//checked//";} ?> type="radio" name="4_9_a" id="4_9_a_1" value="1" />
                      Si &nbsp;
                      <input <?php if (!(strcmp(KT_escapeAttribute($row_dnc['4_9_a']),"0"))) {echo "//checked//";} ?> type="radio" name="4_9_a" id="4_9_a_2" value="0" />
                    No </td>
                    <td colspan="2">Requiero capacitación en:<br />
  <input type="text" name="4_9_b" id="4_9_b" class="form-control" value="<?php echo KT_escapeAttribute($row_dnc['4_9_b']); ?>" size="32" />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3"><p>&nbsp;</p>
                      <p><strong>Formación académica.</strong><br />
                        
                   Si usted desea continuar con su formación académica ¿Qué tema o área de conocimiento desea fortalecer para el desempeño de sus funciones?.</p></td>
                  </tr>
                  <tr>
                    <td><p>
                      Tema:
                      <input type="text" name="escolar" class="form-control" id="escolar" value="<?php echo KT_escapeAttribute($row_dnc['escolar']); ?>" size="80" />
                    </em></p></td>
                    <td><p>¿A que nivel?
                      <br />
                      <select name="dipomas" id="dipomas" class="form-control">
                        <option value="" >Selecciona una opción...</option>
                        <option value="1" <?php if (!(strcmp(1, KT_escapeAttribute($row_dnc['dipomas'])))) {echo "SELECTED";} ?>>Diplomado</option>
                        <option value="2" <?php if (!(strcmp(2, KT_escapeAttribute($row_dnc['dipomas'])))) {echo "SELECTED";} ?>>Maestría</option>
                        <option value="3" <?php if (!(strcmp(3, KT_escapeAttribute($row_dnc['dipomas'])))) {echo "SELECTED";} ?>>Doctorado</option>
                       
                    </p></td>
                    <td>Modalidad <br />
                      <select name="modalidad" id="modalidad" class="form-control">
                        <option value="" >Selecciona una opción...</option>
                        <option value="1" <?php if (!(strcmp(1, KT_escapeAttribute($row_dnc['modalidad'])))) {echo "SELECTED";} ?>>Presencial</option>
                        <option value="2" <?php if (!(strcmp(2, KT_escapeAttribute($row_dnc['modalidad'])))) {echo "SELECTED";} ?>>En Línea</option>
                     	<option value="3" <?php if (!(strcmp(3, KT_escapeAttribute($row_dnc['modalidad'])))) {echo "SELECTED";} ?>>Mixto</option>
                    </select></td>
                  </tr>
                  <tr>
                    <td colspan="3"><strong>Observaciones:</strong>
                      <textarea name="observaciones" class="form-control" id="observaciones" cols="100" rows="3"><?php echo KT_escapeAttribute($row_dnc['observaciones']); ?></textarea>
                    </td>
                  </tr>
                  <tr class="KT_buttons">
                    <td colspan="3"><input type="submit" name="KT_Insert1" id="KT_Insert1" value="Guardar" class="btn btn-success"/></td>
                  </tr>
                </table>
                <input type="hidden" name="IDempleado" id="IDempleado" value="<?php echo KT_escapeAttribute($row_dnc['IDempleado']); ?>" />
                <input type="hidden" name="anio" id="anio" value="<?php echo $anio; ?>" />
                <input type="hidden" name="actualizado" id="actualizado" value="1" />
				<input type="hidden" name="MM_update" value="form1">

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

