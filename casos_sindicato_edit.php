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

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$IDusuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

if(isset($_GET['IDsindicato'])) { 

$IDsindicato = $_GET['IDsindicato'];
mysql_select_db($database_vacantes, $vacantes);
$query_sindicato = "SELECT casos_sindicato.* FROM casos_sindicato WHERE casos_sindicato.IDsindicato = $IDsindicato";
mysql_query("SET NAMES 'utf8'");
$sindicato = mysql_query($query_sindicato, $vacantes) or die(mysql_error());
$row_sindicato = mysql_fetch_assoc($sindicato);
$totalRows_sindicato = mysql_num_rows($sindicato);
$IDsindicable = $row_sindicato['IDsindicable'];
$IDmatriz = $row_sindicato['IDmatriz'];
$IDtema = $row_sindicato['IDtema'];
$IDarea = $row_sindicato['IDarea'];
$IDsucursal = $row_sindicato['IDsucursal'];

//pasamos variables
$_SESSION['IDsucursal'] = $IDsucursal;
$_SESSION['IDmatriz'] = $IDmatriz;
$_SESSION['IDsindicato'] = $IDsindicato;

$query_casos_responsable = "SELECT vac_usuarios.IDusuario, casos_responsables.IDtipo, vac_usuarios.usuario_correo,  vac_usuarios.usuario_nombre,  vac_usuarios.usuario_parterno,   vac_usuarios.usuario_materno,  vac_usuarios.IDusuario_puesto, vac_puestos.denominacion FROM vac_usuarios INNER JOIN casos_responsables ON  vac_usuarios.IDusuario = casos_responsables.IDusuario INNER JOIN vac_puestos ON  vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE IDsindicato = $IDsindicato ORDER BY casos_responsables.IDtipo ASC";
$casos_responsable = mysql_query($query_casos_responsable, $vacantes) or die(mysql_error());
$row_casos_responsable = mysql_fetch_assoc($casos_responsable);
$totalRows_casos_responsable = mysql_num_rows($casos_responsable);

} else {
	
$IDmatriz = $row_usuario['IDmatriz'];
$_SESSION['IDmatriz'] = $la_matriz;
$_SESSION['IDsindicato'] = 0;
$_SESSION['IDsucursal'] = 0;
$IDsindicable = 0;

}


$fecha = date("Y-m-d"); // la fecha actual
$formatos_permitidos =  array('PDF', 'JPG', 'JEPG', 'PNG', 'DOC', 'pdf', 'jpg', 'jepg', 'png', 'doc');
$fechapp = date("YmdHis"); // la fecha actual

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$query_max = "SELECT * FROM casos_sindicato ORDER BY IDsindicato DESC LIMIT 1";
$max = mysql_query($query_max, $vacantes) or die(mysql_error());
$row_max = mysql_fetch_assoc($max);
$IDsindicato =  $row_max['IDsindicato'] + 1;
	
$IDusuario_carpeta = 'SINDICATO/'.$IDsindicato;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: casos_sindicato_edit.php?info=9&IDsindicato='.$IDsindicato.'');
exit;
} 
if (isset($_POST['foto'])) {$name_new = $IDsindicato."_".$fechapp.".".$extension;} else {$name_new = '';} ;
$targetPath = 'SINDICATO/'.$IDsindicato."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("-",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2a = $_POST['fecha_esperada']; 
$fecha2b = explode("-",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];


$descripcion = utf8_decode($_POST['descripcion']);
$asunto = utf8_decode($_POST['asunto']);

$IDmatrizA = $_POST['IDmatriz'];
$IDareaA = $_POST['IDarea'];
$EsSindicable = $_POST['IDsindicable'];

$insertSQL = sprintf("INSERT INTO casos_sindicato (file, fecha_inicio, fecha_esperada, IDusuario, IDmatriz, IDarea, IDsucursal, IDestatus, asunto, IDtema, IDsindicable, descripcion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($fecha2, "text"),
                       GetSQLValueString($_POST['IDusuario'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDarea'], "text"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($asunto, "text"),
                       GetSQLValueString($_POST['IDtema'], "int"),
                       GetSQLValueString($_POST['IDsindicable'], "int"),
                       GetSQLValueString($descripcion, "text"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
$last_id =  mysql_insert_id();

// las áreas involucradas
$query_areasA = "SELECT * FROM casos_areas WHERE IDarea = $IDareaA";
$areasA = mysql_query($query_areasA, $vacantes) or die(mysql_error());
$row_areasA = mysql_fetch_assoc($areasA);


// insert para cada área involucrada
$arreglo = '';
$array = explode(",", $row_areasA['IDareas']);
$contar = substr_count($row_areasA['IDareas'], ",") + 1;
$i = 0;

while($contar > $i) {

$query_respos = "SELECT * FROM vac_usuarios WHERE IDmatriz = $IDmatrizA AND user_casos_sindicato_areas = ".$array[$i];
$respos = mysql_query($query_respos, $vacantes) or die(mysql_error());
$row_respos = mysql_fetch_assoc($respos);

do {

$IDusuarioA = $row_respos['IDusuario'];

$insertSQLA = "INSERT INTO casos_responsables (IDsindicato, IDusuario, IDtipo) VALUES ($last_id , $IDusuarioA, 2)";
mysql_select_db($database_vacantes, $vacantes);
$ResultA = mysql_query($insertSQLA, $vacantes) or die(mysql_error());

} while ($row_respos = mysql_fetch_assoc($respos));

$i++; } 


if ($EsSindicable == 1){
	
$query_Sindicos = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($IDmatrizA,IDmatrizes) AND IDusuario_puesto = 511";
$Sindicos = mysql_query($query_Sindicos, $vacantes) or die(mysql_error());
$row_Sindicos = mysql_fetch_assoc($Sindicos);
	
do {	

$IDusuariox = $row_Sindicos['IDusuario'];

$insertSQLx = "INSERT INTO casos_responsables (IDsindicato, IDusuario, IDtipo) VALUES ($last_id , $IDusuariox, 2)";
mysql_select_db($database_vacantes, $vacantes);
$Resultx = mysql_query($insertSQLx, $vacantes) or die(mysql_error());

} while ($row_Sindicos = mysql_fetch_assoc($Sindicos));
	
}

header("Location: casos_sindicato_responsables.php?IDsindicato=$last_id&info=3&Primero=1");
 }
 
 
 if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$IDusuario_carpeta = 'SINDICATO/'.$IDsindicato;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: casos_sindicato_edit.php?info=9&IDsindicato='.$IDsindicato.'');
exit;
} 
if (isset($_POST['foto'])) {$name_new = $IDsindicato."_".$fechapp.".".$extension;} else {$name_new = '';} ;
$targetPath = 'SINDICATO/'.$IDsindicato."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("-",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2a = $_POST['fecha_esperada']; 
$fecha2b = explode("-",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];

$descripcion = ($_POST['descripcion']);
$asunto = ($_POST['asunto']);


$updateSQL = sprintf("UPDATE casos_sindicato SET file=%s, fecha_inicio=%s, fecha_esperada=%s, IDusuario=%s, IDmatriz=%s, IDarea=%s, IDsucursal=%s, asunto=%s, IDtema=%s, IDestatus=%s,  IDsindicable=%s, descripcion=%s WHERE IDsindicato=%s",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($fecha2, "text"),
                       GetSQLValueString($_POST['IDusuario'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDarea'], "text"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($asunto, "text"),
                       GetSQLValueString($_POST['IDtema'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['IDsindicable'], "int"),
                       GetSQLValueString($descripcion, "text"),
                       GetSQLValueString($IDsindicato, "int"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header("Location: casos_sindicato_edit.php?IDsindicato=$IDsindicato&info=2");
 }

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDsindicato'];
  $deleteSQL = "UPDATE casos_sindicato SET IDestatus = 0 WHERE IDsindicato ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: casos_sindicato.php?info=4");
}


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_lsucursal = "SELECT * FROM vac_sucursal ORDER BY sucursal";
$lsucursal = mysql_query($query_lsucursal, $vacantes) or die(mysql_error());
$row_lsucursal = mysql_fetch_assoc($lsucursal);
$totalRows_lsucursal = mysql_num_rows($lsucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_temas = "SELECT * FROM casos_temas Order By tema ASC";
mysql_query("SET NAMES 'utf8'");
$temas = mysql_query($query_temas, $vacantes) or die(mysql_error());
$row_temas = mysql_fetch_assoc($temas);
$totalRows_temas = mysql_num_rows($temas);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM casos_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>  
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
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
    
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<!-- /theme JS files -->
	</head>
<script>
function showHint2(str) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint2").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "casos_get_sucursal.php?p=" + str, true);
        xmlhttp.send();
}
</script>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?> onLoad="showHint2();">
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
                        <?php if((isset($_GET['info']) && $_GET['info'] == 1)) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Registro agregado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && $_GET['info'] == 9)) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Formato de archivo adjunto no permitido.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && $_GET['info'] == 2)) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Registro actualizado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if(isset($row_sindicato['IDestatus']) AND $row_sindicato['IDestatus'] == 3 AND $row_sindicato['fecha_fin'] == '') { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Por favor indique fecha y detalles del cierre del caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">									
							<?php if(isset($_GET['IDsindicato'])) { ?>Editar Caso<?php } else {?>Agregar Caso<?php } ?></h5>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada; algunos campos son obligatorios.</p>
                            <p>&nbsp;</p>
							
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
							<fieldset class="content-group">
								
<?php if(isset($_GET['IDsindicato'])) { ?>
								
								
							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Asunto:</label>
										<div class="col-lg-9">
										<input type="text" name="asunto" id="asunto" class="form-control" value="<?php if (isset($row_sindicato['asunto'])) { echo $row_sindicato['asunto']; } ?>" placeholder="Indique el asunto general del caso" required="required">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDestatus" id="IDestatus" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												<option value="1"<?php if ($row_sindicato['IDestatus'] == 1) {echo "SELECTED";} ?>>EN PROCESO</option> 
												<option value="2"<?php if ($row_sindicato['IDestatus'] == 2) {echo "SELECTED";} ?>>ATENDIDO</option> 
												<?php if ($row_usuario['user_casos_sindicato'] == 2) { ?>
												<option value="3"<?php if ($row_sindicato['IDestatus'] == 3) {echo "SELECTED";} ?>>CERRARDO</option> 
												<?php } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $IDarea))) 
												  {echo "SELECTED";} ?>><?php echo $row_area['area']?></option>
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
										<label class="control-label col-lg-3">Tema general:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtema" id="IDtema" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_temas['IDtema']?>"<?php if (!(strcmp($row_temas['IDtema'], $IDtema))) 
												  {echo "SELECTED";} ?>><?php echo $row_temas['tema']?></option>
												  <?php
												 } while ($row_temas = mysql_fetch_assoc($temas));
												   $rows = mysql_num_rows($temas);
												   if($rows > 0) {
												   mysql_data_seek($temas, 0);
												   $row_temas = mysql_fetch_assoc($temas);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->
									
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Es un tema Sindical?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsindicable" id="IDsindicable" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <option value="0"<?php if ($IDsindicable == 0) {echo "SELECTED";} ?>>No</option>
												  <option value="1"<?php if ($IDsindicable == 1) {echo "SELECTED";} ?>>Si</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->
									

									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" onchange="showHint2(this.value)">
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

                                    <span id="txtHint2">
									
									</span>


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Seleccione la fecha" name="fecha_inicio" id="fecha_inicio" value="<?php if ($row_sindicato['fecha_inicio'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_sindicato['fecha_inicio'])); }?>">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de solución esperada/solicitada:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Seleccione la fecha" name="fecha_esperada" id="fecha_esperada" value="<?php if ($row_sindicato['fecha_esperada'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_sindicato['fecha_esperada'])); }?>">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Descripción Detallada: </label>
										<div class="col-lg-9">
                                          <textarea class="wysihtml5 wysihtml5-min form-control" name="descripcion" rows="6" id="descripcion"><?php echo $row_sindicato['descripcion']; ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- /basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Documento:</label>
										<div class="col-lg-5">
											<input type="file" class="file-styled" name="foto" id="foto">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>, <code>xlsx</code>, <code>jpg</code>, <code>png</code>, <code>doc</code>.</br>
										</div>
										<div class="col-lg-4">
                                        <?php if (isset($row_sindicato['file']) and $row_sindicato['file'] != ''){ ?>
										<a href='<?php echo "SINDICATO/".$IDsindicato."/".$row_sindicato['file']; ?>' class="btn btn-info btn-icon" target="_blank">Descargar archivo</a><?php } ?></p>
										</div>
									</div>
									<!-- /basic text input -->

								<?php if($row_sindicato['IDestatus'] == 3) { ?>
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de cierre:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Seleccione la fecha" name="fecha_fin" id="fecha_fin" value="<?php if ($row_sindicato['fecha_fin'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_sindicato['fecha_fin'])); }?>">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Detalle del cierre del caso:</label>
										<div class="col-lg-9">
                                          <textarea name="descripcion_cierre" rows="6" class="wysihtml5 wysihtml5-min form-control" id="descripcion_cierre" placeholder="Indique los acuerdos y solución obtenidos al caso en específico."><?php echo $row_sindicato['descripcion_cierre']; ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->
								<?php } ?>
								
																
									
							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Responsable(s):</label>
										<div class="col-lg-9">


									<?php if($totalRows_casos_responsable) { ?>
									<ul>
										<?php do { ?>

											<?php echo "<li><b>".$row_casos_responsable['usuario_parterno']." ".$row_casos_responsable['usuario_materno']." ".$row_casos_responsable['usuario_nombre']."</b> (".$row_casos_responsable['denominacion'].")"; ?>
											
											
											<?php if ($row_casos_responsable['IDtipo'] == 1) { echo "Responsable <i class='icon icon-user-check text-success'></i>";}
											else  if ($row_casos_responsable['IDtipo'] == 2) { echo "Para Seguimiento";}
											else { echo "Sin definir";} ?>
									
											<?php echo "</li>"; ?>
									
											<br />

										<?php } while ($row_casos_responsable = mysql_fetch_assoc($casos_responsable)); ?>
									</ul>
									<?php } else { ?>
										No se han asignado responsables.
									<?php } ?>

										</div>
									</div>
									<!-- /basic text input -->

<?php } else { ?>


								
							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Asunto:</label>
										<div class="col-lg-9">
										<input type="text" name="asunto" id="asunto" class="form-control" value="<?php if (isset($row_sindicato['asunto'])) { echo $row_sindicato['asunto']; } ?>" placeholder="Indique el asunto general del caso" required="required">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_area['IDarea']?>"><?php echo $row_area['area']?></option>
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
										<label class="control-label col-lg-3">Tema general:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtema" id="IDtema" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_temas['IDtema']?>"><?php echo $row_temas['tema']?></option>
												  <?php
												 } while ($row_temas = mysql_fetch_assoc($temas));
												   $rows = mysql_num_rows($temas);
												   if($rows > 0) {
												   mysql_data_seek($temas, 0);
												   $row_temas = mysql_fetch_assoc($temas);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Es un tema Sindical?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsindicable" id="IDsindicable" class="form-control" required="required">
												  <option value="0">No</option>
												  <option value="1">Si</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" onchange="showHint2(this.value)">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_lmatriz['IDmatriz']?>" <?php if (!(strcmp($row_lmatriz['IDmatriz'], $IDmatriz))) 
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

                                    <span id="txtHint2">
									
									</span>

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Seleccione la fecha" name="fecha_inicio" id="fecha_inicio" value="">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de solución esperada/solicitada:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Seleccione la fecha" name="fecha_esperada" id="fecha_esperada" value="">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Descripción Detallada: </label>
										<div class="col-lg-9">
                                          <textarea class="wysihtml5 wysihtml5-min form-control" name="descripcion" rows="6" id="descripcion"></textarea>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- /basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Documento:</label>
										<div class="col-lg-5">
											<input type="file" class="file-styled" name="foto" id="foto">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>, <code>jpg</code>, <code>png</code>, <code>doc</code>.</br>
                                        <?php if (isset($row_sindicato['file']) and $row_sindicato['file'] != ''){ ?>
										<a href='<?php echo "SINDICATO/".$IDsindicato."/".$row_sindicato['file']; ?>' class="btn btn-info btn-icon" target="_blank">Descargar archivo</a><?php } ?></p>
										</div>
									</div>
									<!-- /basic text input -->


<?php }  ?>


								<?php if(isset($_GET['IDsindicato'])) { ?>
										<button type="submit"  class="btn btn-primary">Actualizar</button>
                                        <input type="hidden" name="MM_update" value="form1">
                                        <input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
										<button type="button" data-target="#modal_theme_danger" data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
										<a href="casos_sindicato_responsables.php?IDsindicato=<?php echo $row_sindicato['IDsindicato']; ?>" class="btn btn-info">Responsables</a>
                                <?php } else { ?>
										<button type="submit"  class="btn btn-primary">Agregar</button>
                                        <input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
                                        <input type="hidden" name="MM_insert" value="form1">
                                        <input type="hidden" name="IDestatus" value="1">
								<?php } ?>
										<button type="button" onClick="window.location.href='casos_sindicato.php'" class="btn btn-default btn-icon">Regresar</button>

						 
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el caso?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="casos_sindicato_edit.php?IDsindicato=<?php echo $_GET['IDsindicato']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->
						 
						</fieldset>
                        </form>
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