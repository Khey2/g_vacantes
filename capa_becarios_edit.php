<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fecha = date("dmY"); // la fecha actual

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
$IDmatrizes = $row_usuario['IDmatrizes'];


if (isset($_GET["IDempleado"])) {
$IDempleado = $_GET["IDempleado"];
mysql_select_db($database_vacantes, $vacantes);
$query_becario = "SELECT capa_becarios.*,  capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, capa_becarios_evaluacion.IDevaluacion, capa_becarios_evaluacion.anio, capa_becarios_evaluacion.IDmes, vac_meses.mes FROM capa_becarios LEFT JOIN capa_becarios_evaluacion ON capa_becarios.IDempleado = capa_becarios_evaluacion.IDempleado LEFT JOIN vac_meses ON capa_becarios_evaluacion.IDmes = vac_meses.IDmes LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$becario = mysql_query($query_becario, $vacantes) or die(mysql_error());
$row_becario = mysql_fetch_assoc($becario);
$totalRows_becario = mysql_num_rows($becario);
$foto_anterior = $row_becario['Fotografia'];
} else {
$foto_anterior = '';	
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	// agregar FOTO
$formatos_permitidos =  array('jpeg', 'png', 'jpg');
$IDempleado_carpeta = 'becariosfiles/'.$IDempleado;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDempleado.$fecha.'.'.$extension;
$targetPath = 'becariosfiles/'.$IDempleado.'/'.$name_new;


// si se mandó archivo
if ($name != '') {	
	
if(!in_array($extension, $formatos_permitidos) ) { echo "error archivos"; 
header("Location: capa_becarios_edit.php?IDempleado=$IDempleado&info=9"); 
}
	
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}

if ($name != '') {$name_new = $name_new;} else {$name_new = $foto_anterior; }

$fecha_filtro = $_POST['fecha_alta'];
$y1 = substr( $fecha_filtro, 6, 4 );
$m1 = substr( $fecha_filtro, 3, 2 );
$d1 = substr( $fecha_filtro, 0, 2 );
$fecha_a =  $y1."-".$m1."-".$d1;


$fecha_rfc = $_POST['fecha_nacimiento'];
if ($_POST['fecha_nacimiento'] == '') {
$y3 = substr( $fecha_rfc, 4, 2 ); 
$m3 = substr( $fecha_rfc, 6, 2 ); 
$d3 = substr( $fecha_rfc, 8, 2 ); 
if ($y3 > 50) {$y4 = '19';} else {$y4 = '20';}
$fecha_c =  $y4.$y3."-".$m3."-".$d3;
} else {
$fecha_c =  $_POST['fecha_nacimiento'];
}

if ($_POST['fecha_baja'] != '') { 
$fecha_filtro2 = $_POST['fecha_baja'];
$y2 = substr( $fecha_filtro2, 6, 4 );
$m2 = substr( $fecha_filtro2, 3, 2 );
$d2 = substr( $fecha_filtro2, 0, 2 );
$fecha_b =  $y2."-".$m2."-".$d2;
 } else { $fecha_b = '';}
	
  $updateSQL = sprintf("UPDATE capa_becarios SET emp_paterno=%s, emp_materno=%s, emp_nombre=%s, fecha_alta=%s, fecha_baja=%s, hora_entrada=%s, hora_salida=%s, IDmatriz=%s, fecha_nacimiento=%s, rfc=%s, curp=%s, activo=%s, IDsucursal=%s, IDarea=%s, IDempleadoJ=%s, IDempleadoJcorreo=%s, IDtipo=%s, IDsubarea=%s, correo=%s, IDmodalidad=%s, IDrol=%s, telefono=%s, emergencias_a=%s, emergencias_b=%s, IDmotivo_baja=%s, observaciones=%s, file=%s WHERE IDempleado=%s",
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($fecha_a, "text"),
                       GetSQLValueString($fecha_b, "text"),
                       GetSQLValueString($_POST['hora_entrada'], "text"),
                       GetSQLValueString($_POST['hora_salida'], "text"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($fecha_c, "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['curp'], "text"),
                       GetSQLValueString($_POST['activo'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDempleadoJ'], "int"),
                       GetSQLValueString($_POST['IDempleadoJcorreo'], "text"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['IDsubarea'], "int"),
                       GetSQLValueString($_POST['correo'], "text"),
                       GetSQLValueString($_POST['IDmodalidad'], "int"),
                       GetSQLValueString($_POST['IDrol'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['emergencias_a'], "text"),
                       GetSQLValueString($_POST['emergencias_b'], "text"),
                       GetSQLValueString($_POST['IDmotivo_baja'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($_POST['IDempleado'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "capa_becarios_edit.php?IDempleado=$IDempleado&info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
 // header(sprintf("Location: %s", $updateGoTo));
}

else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
mysql_select_db($database_vacantes, $vacantes);
$query_maximo = "SELECT * FROM capa_becarios ORDER BY IDempleado DESC LIMIT 1"; 
$maximo = mysql_query($query_maximo, $vacantes) or die(mysql_error());
$row_maximo = mysql_fetch_assoc($maximo);
$totalRows_maximo = mysql_num_rows($maximo);
$IDempleado = $row_maximo['IDempleado'] + 1;
	
	


// agregar FOTO
$formatos_permitidos =  array('jpeg', 'png', 'jpg');
$IDempleado_carpeta = 'becariosfiles/'.$IDempleado;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDempleado.$fecha.'.'.$extension;
$targetPath = 'becariosfiles/'.$IDempleado.'/'.$name_new;


// si se mandó archivo
if ($name != '') {	
	
if(!in_array($extension, $formatos_permitidos) ) { echo "error archivos"; 
header("Location: empleados_adicionales.php?IDempleado=$IDempleado&info=9"); 
}
	
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}

if ($name != '') {$name_new = $name_new;} else {$name_new = $foto_anterior; }
$fecha_filtro = $_POST['fecha_alta'];
$y1 = substr( $fecha_filtro, 6, 4 );
$m1 = substr( $fecha_filtro, 3, 2 );
$d1 = substr( $fecha_filtro, 0, 2 );
$fecha_i =  $y1."-".$m1."-".$d1;

$pass = '0aab3e28d9e60055ea28acb2338b2676';

$fecha_rfc = $_POST['rfc'];
$y3 = substr( $fecha_rfc, 4, 2 );
$m3 = substr( $fecha_rfc, 6, 2 );
$d3 = substr( $fecha_rfc, 8, 2 );
if ($y3 > 50) {$y4 = '19';} else {$y4 = '20';}
$fecha_c =  $y4.$y3."-".$m3."-".$d3;

  $insertSQL = sprintf("INSERT INTO capa_becarios (IDempleado, password, emp_paterno, emp_materno, emp_nombre, IDpuesto, fecha_alta, hora_entrada, hora_salida, IDmatriz, fecha_nacimiento, rfc, curp, activo, IDsucursal, IDarea, IDempleadoJ, IDempleadoJcorreo, IDtipo, IDsubarea, correo, IDmodalidad, IDrol, telefono, emergencias_a, emergencias_b, IDmotivo_baja, observaciones, descripcion_nomina, descripcion_nivel, denominacion, IDaplica_PROD, IDaplica_SED, IDaplica_INC, manual, file) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($IDempleado, "int"),
                       GetSQLValueString($pass, "text"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($fecha_i, "text"),
                       GetSQLValueString($_POST['hora_entrada'], "text"),
                       GetSQLValueString($_POST['hora_salida'], "text"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($fecha_c, "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['curp'], "text"),
                       GetSQLValueString($_POST['activo'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDempleadoJ'], "int"),
                       GetSQLValueString($_POST['IDempleadoJcorreo'], "text"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['IDsubarea'], "int"),
                       GetSQLValueString($_POST['correo'], "text"),
                       GetSQLValueString($_POST['IDmodalidad'], "int"),
                       GetSQLValueString($_POST['IDrol'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['emergencias_a'], "text"),
                       GetSQLValueString($_POST['emergencias_b'], "text"),
                       GetSQLValueString($_POST['IDmotivo_baja'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($_POST['descripcion_nomina'], "text"),
                       GetSQLValueString($_POST['descripcion_nivel'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['IDaplica_PROD'], "int"),
                       GetSQLValueString($_POST['IDaplica_SED'], "int"),
                       GetSQLValueString($_POST['IDaplica_INC'], "int"),
                       GetSQLValueString($_POST['manual'], "int"),
                       GetSQLValueString($name_new, "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "capa_becarios_edit.php?IDempleado=$IDempleado&info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
   $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  //header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos ORDER BY prod_activos.emp_paterno";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);

mysql_select_db($database_vacantes, $vacantes);
$query_programa = "SELECT * FROM capa_becarios_tipo";
$programa = mysql_query($query_programa, $vacantes) or die(mysql_error());
$row_programa = mysql_fetch_assoc($programa);
$totalRows_programa = mysql_num_rows($programa);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_subarea = "SELECT * FROM vac_subareas";
mysql_query("SET NAMES 'utf8'");
$subarea = mysql_query($query_subarea, $vacantes) or die(mysql_error());
$row_subarea = mysql_fetch_assoc($subarea);
$totalRows_subarea = mysql_num_rows($subarea);

mysql_select_db($database_vacantes, $vacantes);
$query_motivos = "SELECT * FROM capa_becarios_motivo_baja";
mysql_query("SET NAMES 'utf8'");
$motivos = mysql_query($query_motivos, $vacantes) or die(mysql_error());
$row_motivos = mysql_fetch_assoc($motivos);
$totalRows_motivos = mysql_num_rows($motivos);
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
 
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>

	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    <script>
	function showUser(str) {
	  if (str == 0) {
	  } else {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		  if (this.readyState == 4 && this.status == 200) {
			document.getElementById("txtHint").innerHTML = this.responseText;
		  }
		};
		xmlhttp.open("GET","empleados_get_correo.php?q="+str,true);
		xmlhttp.send();
	  }
	}
	</script>
</head>
<body class="has-detached-right">	
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


                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo es incorrecto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Becario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el Becario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el Becario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
                              <?php  if (isset($_GET['IDbecario'])) { ?>
							<h5 class="panel-title">Editar Becario</h5>
                              <?php } else { ?>
							<h5 class="panel-title">Agregar Becario</h5>
                                <?php }  ?>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
                            <p>&nbsp;</p>
                            
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
                            
                              <?php  if (isset($_GET['IDempleado'])) { ?>
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">IDbecario:</label>
										<div class="col-lg-9">
						                  <p><strong><?php echo $row_becario['IDempleado']; ?></strong></p>
										</div>
									</div>
									<!-- /basic text input -->
                                <?php }  ?>




                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Foto:</label>
										<div class="col-lg-3">
												<?php if (isset($row_becario['Fotografia']) AND $row_becario['Fotografia'] != '') { ?>
												<img src="<?php echo 'becariosfiles/'.$row_becario['ELempleado'].'/'.$row_becario['Fotografia']; ?>" alt="Fotografia" width="80" height="100"><br/>
												<?php } else { ?>
												<img src="files/foto.jpg" alt="Fotografia" width="80" height="100"><br/>
												<?php } ?>
										</div>
										<div class="col-lg-6">
											<input type="file" name="file" id="file" class="file-styled" >
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="emp_paterno" id="emp_paterno" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_becario['emp_paterno'], ENT_COMPAT, ''); ?>" required="required">
                              <?php  } else { ?>
											<input type="text" name="emp_paterno" id="emp_paterno" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="" placeholder="Apellido Paterno" required="required">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Materno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="emp_materno" id="emp_materno" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_becario['emp_materno'], ENT_COMPAT, ''); ?>" required="required">
                              <?php  } else { ?>
											<input type="text" name="emp_materno" id="emp_materno" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="" placeholder="Apellido Materno" required="required">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="emp_nombre" id="emp_nombre" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_becario['emp_nombre'], ENT_COMPAT, ''); ?>" required="required">
                              <?php  } else { ?>
											<input type="text" name="emp_nombre" id="emp_nombre" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="" placeholder="Nombres" required="required">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">RFC:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="rfc" id="rfc" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_becario['rfc'], ENT_COMPAT, ''); ?>" required="required">
                              <?php  } else { ?>
											<input type="text" name="rfc" id="rfc" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="" placeholder="RFC a 10 posiciones" required="required">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">CURP:</label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="curp" id="curp" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_becario['curp'], ENT_COMPAT, ''); ?>">
                              <?php  } else { ?>
											<input type="text" name="curp" id="curp" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="" placeholder="CURP">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="telefono" id="telefono" class="form-control" value="<?php echo htmlentities($row_becario['telefono'], ENT_COMPAT, ''); ?>" required="required">
                              <?php  } else { ?>
											<input type="text" name="telefono" id="telefono" class="form-control" value="" placeholder="Teléfono de casa o celular" required="required">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Telefono Emergencias:</label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="emergencias_a" id="emergencias_a" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_becario['emergencias_a'], ENT_COMPAT, ''); ?>">
                              <?php  } else { ?>
											<input type="text" name="emergencias_a" id="emergencias_a" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="" placeholder="Teléfono de Emergencias">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Contacto de Emergencias:</label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="emergencias_b" id="emergencias_b" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_becario['emergencias_b'], ENT_COMPAT, ''); ?>">
                              <?php  } else { ?>
											<input type="text" name="emergencias_b" id="emergencias_b" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="" placeholder="Contacto de Emergencias">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="number" id="correo" name="correo" onKeyUp="showUser(this.value)" class="form-control" placeholder="Correo electrónico" value="<?php echo $row_becario['correo']; ?>" required="required">
                              <?php  } else { ?>
											<input type="email" name="correo" id="correo" onKeyUp="showUser(this.value)" class="form-control" value="" placeholder="Correo electrónico" required="required">
                              <?php  } ?>
										  <div class="help-block text-danger text-semibold" id="txtHint"></div>

										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
 											<select name="activo" id="activo" class="form-control" >
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_becario['activo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Activo</option>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_becario['activo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Inactivo</option>
											</select>
                              <?php  } else { ?>
 											<select name="activo" id="activo" class="form-control" >
                                            <option value="1">Activo</option>
                                            <option value="" >Inactivo</option>
											</select>
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Modalidad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
 											<select name="IDmodalidad" id="IDmodalidad" class="form-control" >
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_becario['IDmodalidad'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Presencial</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_becario['IDmodalidad'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Remoto</option>
                                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_becario['IDmodalidad'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Mixto</option>
											</select>
                              <?php  } else { ?>
 											<select name="IDmodalidad" id="IDmodalidad" class="form-control" >
                                            <option value="1">Presencial</option>
                                            <option value="2">Remoto</option>
                                            <option value="3">Mixto</option>
											</select>
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic select -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Hora entrada:</label>
										<div class="col-lg-9">
										<div class="input-group">
										<span class="input-group-addon"><i class="icon-alarm"></i></span>
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="hora_entrada" id="hora_entrada" class="form-control pickatime-limits" value="<?php echo htmlentities($row_becario['hora_entrada'], ENT_COMPAT, ''); ?>">
                              <?php  } else { ?>
											<input type="text" name="hora_entrada" id="hora_entrada" class="form-control pickatime-limits" value="" placeholder="Hora de entrada">
                              <?php  } ?>
									</div>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Hora salida:</label>
										<div class="col-lg-9">
										<div class="input-group">
										<span class="input-group-addon"><i class="icon-alarm"></i></span>
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="hora_salida" id="hora_salida" class="form-control pickatime-limits" value="<?php echo htmlentities($row_becario['hora_salida'], ENT_COMPAT, ''); ?>">
                              <?php  } else { ?>
											<input type="text" name="hora_salida" id="hora_salida" class="form-control pickatime-limits" value="" placeholder="Hora de salida">
                              <?php  } ?>
									</div>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Rol de Asistencia:</label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="text" name="IDrol" id="IDrol" class="form-control" value="<?php echo htmlentities($row_becario['IDrol'], ENT_COMPAT, ''); ?>">
                              <?php  } else { ?>
											<input type="text" name="IDrol" id="IDrol" class="form-control" value="" placeholder="Rol de asistencia">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->



									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha Alta:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                              <?php  if (isset($_GET['IDempleado'])) { ?>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Capturar fecha" name="fecha_alta" id="fecha_alta" value="<?php if ($row_becario['fecha_alta'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_becario['fecha_alta'])); }?>" required="required">
                              <?php  } else { ?>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Capturar fecha" name="fecha_alta" id="fecha_alta" value="" required="required">
                              <?php  } ?>
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

                              <?php  if (isset($_GET['IDempleado'])) { ?>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de baja:</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Capturar fecha" name="fecha_baja" id="fecha_baja" value="<?php if ($row_becario['fecha_baja'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_becario['fecha_baja'])); }?>">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->
                              <?php } ?>

                              <?php  if (isset($_GET['IDempleado'])) { ?>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de nacimiento:</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Capturar fecha" name="fecha_nacimiento" id="fecha_nacimiento" value="<?php if ($row_becario['fecha_nacimiento'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_becario['fecha_nacimiento'])); }?>">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->
                              <?php } ?>

							  <?php  if (isset($_GET['IDempleado']) AND $row_becario['activo'] == 0) { ?>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Motivo de Baja:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
											<select name="IDmotivo_baja" id="IDmotivo_baja" class="form-control">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_motivos['IDmotivo']?>"<?php if (!(strcmp($row_motivos['IDmotivo'], $row_becario['IDmotivo_baja']))) {echo "SELECTED";} ?>><?php echo $row_motivos['motivo']?></option>
													  <?php
													 } while ($row_motivos = mysql_fetch_assoc($motivos));
													 $rows = mysql_num_rows($motivos);
													 if($rows > 0) {
													 mysql_data_seek($motivos, 0);
													 $row_motivos = mysql_fetch_assoc($motivos);
													 } ?>
                                          </select>
                                   </div>
                                  </div> 
									<!-- Fecha -->
                              <?php } ?>

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
												<?php  if (isset($_GET['IDempleado'])) { ?>
													  <option value="<?php echo $row_matriz['IDmatriz']?>"<?php if (!(strcmp($row_matriz['IDmatriz'], $row_becario['IDmatriz']))) {echo "SELECTED";} ?>><?php echo $row_matriz['matriz']?></option>
												<?php  } else { ?>
													  <option value="<?php echo $row_matriz['IDmatriz']?>"><?php echo $row_matriz['matriz']?></option>
												<?php  } ?>
													  <?php
													 } while ($row_matriz = mysql_fetch_assoc($matriz));
													 $rows = mysql_num_rows($matriz);
													 if($rows > 0) {
													 mysql_data_seek($matriz, 0);
													 $row_matriz = mysql_fetch_assoc($matriz);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sucursal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsucursal" id="IDsucursal" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
												<?php  if (isset($_GET['IDempleado'])) { ?>
													  <option value="<?php echo $row_sucursal['IDsucursal']?>"<?php if (!(strcmp($row_sucursal['IDsucursal'], $row_becario['IDsucursal']))) {echo "SELECTED";} ?>><?php echo $row_sucursal['sucursal']?></option>
												<?php  } else { ?>
													  <option value="<?php echo $row_sucursal['IDsucursal']?>"><?php echo $row_sucursal['sucursal']?></option>
												<?php  } ?>
													  <?php
													 } while ($row_sucursal = mysql_fetch_assoc($sucursal));
													 $rows = mysql_num_rows($sucursal);
													 if($rows > 0) {
													 mysql_data_seek($sucursal, 0);
													 $row_sucursal = mysql_fetch_assoc($sucursal);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="bootstrap-select" data-live-search="true" data-width="100%" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												<?php  if (isset($_GET['IDempleado'])) { ?>
												  <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $row_becario['IDarea']))) 
												  {echo "SELECTED";} ?>><?php echo $row_area['area']?></option>
												<?php  } else { ?>
												  <option value="<?php echo $row_area['IDarea']?>"><?php echo $row_area['area']?></option>
												<?php  } ?>
												  <?php
												 } while ($row_area = mysql_fetch_assoc($area));
												   $rows = mysql_num_rows($area);
												   if($rows > 0) {
												   mysql_data_seek($area, 0);
												   $row_area = mysql_fetch_assoc($area);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Subarea:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsubarea" id="IDsubarea" class="bootstrap-select" data-live-search="true" data-width="100%" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												<?php  if (isset($_GET['IDempleado'])) { ?>
												  <option value="<?php echo $row_subarea['IDsubarea']?>"<?php if (!(strcmp($row_subarea['IDsubarea'], $row_becario['IDsubarea']))) 
												  {echo "SELECTED";} ?>><?php echo $row_subarea['subarea']?></option>
												<?php  } else { ?>
												  <option value="<?php echo $row_subarea['IDsubarea']?>"><?php echo $row_subarea['subarea']?></option>
												<?php  } ?>
												  <?php
												 } while ($row_subarea = mysql_fetch_assoc($subarea));
												   $rows = mysql_num_rows($subarea);
												   if($rows > 0) {
												   mysql_data_seek($subarea, 0);
												   $row_subarea = mysql_fetch_assoc($subarea);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

 									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDempleadoJ" id="IDempleadoJ" class="bootstrap-select" data-live-search="true" data-width="100%" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												<?php  if (isset($_GET['IDempleado'])) { ?>
												  <option value="<?php echo $row_jefes['IDempleado']?>"<?php if (!(strcmp($row_jefes['IDempleado'], $row_becario['IDempleadoJ']))) 
												  {echo "SELECTED";} ?>><?php echo $row_jefes['emp_paterno']." ".$row_jefes['emp_materno']." ".$row_jefes['emp_nombre']; ?></option>
												<?php  } else { ?>
												  <option value="<?php echo $row_jefes['IDempleado']?>"><?php echo $row_jefes['emp_paterno']." ".$row_jefes['emp_materno']." ".$row_jefes['emp_nombre']; ?></option>
												<?php  } ?>
												  <?php
												 } while ($row_jefes = mysql_fetch_assoc($jefes));
												   $rows = mysql_num_rows($jefes);
												   if($rows > 0) {
												   mysql_data_seek($jefes, 0);
												   $row_jefes = mysql_fetch_assoc($jefes);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo del tutor:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<input type="email" name="IDempleadoJcorreo" id="IDempleadoJcorreo" class="form-control" value="<?php echo htmlentities($row_becario['IDempleadoJcorreo'], ENT_COMPAT, ''); ?>" required="required">
                              <?php  } else { ?>
											<input type="email" name="IDempleadoJcorreo" id="IDempleadoJcorreo" class="form-control" value="" placeholder="Correo electrónico del tutor" required="required">
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Programa:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo" id="IDtipo" class="form-control" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												<?php  if (isset($_GET['IDempleado'])) { ?>
												  <option value="<?php echo $row_programa['IDtipo']?>"<?php if (!(strcmp($row_programa['IDtipo'], $row_becario['IDtipo']))) 
												  {echo "SELECTED";} ?>><?php echo $row_programa['tipo']?></option>
												<?php  } else { ?>
												  <option value="<?php echo $row_programa['IDtipo']?>"><?php echo $row_programa['tipo']?></option>
												<?php  } ?>
												  <?php
												 } while ($row_programa = mysql_fetch_assoc($programa));
												   $rows = mysql_num_rows($programa);
												   if($rows > 0) {
												   mysql_data_seek($programa, 0);
												   $row_programa = mysql_fetch_assoc($programa);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Observaciones:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDempleado'])) { ?>
											<textarea rows="3" cols="3" name="observaciones" id="observaciones" class="form-control"><?php echo htmlentities($row_becario['observaciones'], ENT_COMPAT, ''); ?></textarea>
                              <?php  } else { ?>
											<textarea rows="3" cols="3" name="observaciones" id="observaciones" class="form-control" placeholder="Observaciones"></textarea>
                              <?php  } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                    
                              <?php  if (isset($_GET['IDempleado'])) { ?>
							<button type="submit"  name="KT_Update1" class="btn btn-primary">Actualizar</button>
							<input type="hidden" name="MM_update" value="form1">
							<input type="hidden" name="IDempleado" value="<?php echo $row_becario['IDempleado']; ?>">
                              <?php } else { ?>
                            <input type="submit" name="KT_Insert1" class="btn btn-primary" id="KT_Insert1" value="Agregar" />
							<input type="hidden" name="manual" value="1">
							<input type="hidden" name="descripcion_nomina" value="BECARIOS">
							<input type="hidden" name="descripcion_nivel" value="BECARIOS">
							<input type="hidden" name="denominacion" value="BECARIO">
							<input type="hidden" name="IDpuesto" value="999">
							<input type="hidden" name="activo" value="1">
							<input type="hidden" name="IDaplica_PROD" value="0">
							<input type="hidden" name="IDaplica_SED" value="0">
							<input type="hidden" name="IDaplica_INC" value="0">
							<input type="hidden" name="IDmotivo_baja" value="0">
                                <?php }  ?>
							<button type="button" onClick="window.location.href='capa_becarios_activos.php'" class="btn btn-default btn-icon">Regresar</button>
							<input type="hidden" name="MM_insert" value="form1" />
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