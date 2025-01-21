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
$mi_fecha =  date('Y/m/d');

if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;

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
$el_usuario = $row_usuario['IDempleado'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

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
    $_SESSION['IDarea'] = $IDarea;
    $_SESSION['IDmatriz'] = $IDmatriz;
    $_SESSION['IDsindicato'] = $IDsindicato;
    
    $query_casos_responsable = "SELECT vac_usuarios.IDusuario, casos_responsables.IDtipo, vac_usuarios.usuario_correo,  vac_usuarios.usuario_nombre,  vac_usuarios.usuario_parterno,   vac_usuarios.usuario_materno,  vac_usuarios.IDusuario_puesto, vac_puestos.denominacion FROM vac_usuarios INNER JOIN casos_responsables ON  vac_usuarios.IDusuario = casos_responsables.IDusuario INNER JOIN vac_puestos ON  vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE IDsindicato = $IDsindicato ORDER BY casos_responsables.IDtipo ASC";
    $casos_responsable = mysql_query($query_casos_responsable, $vacantes) or die(mysql_error());
    $row_casos_responsable = mysql_fetch_assoc($casos_responsable);
    $totalRows_casos_responsable = mysql_num_rows($casos_responsable);
    
    } else {
        
    $IDmatriz = $row_usuario['IDmatriz'];
    $_SESSION['IDmatriz'] = $IDmatriz;
    $_SESSION['IDsindicato'] = 0;
    $_SESSION['IDsucursal'] = $IDsucursal;
    $_SESSION['IDarea'] = $IDarea;
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
    header('Location: f_casos_sindicato_edit.php?info=9&IDsindicato='.$IDsindicato.'');
    exit;
    } 
    if (isset($_POST['foto'])) {$name_new = $IDsindicato."_".$fechapp.".".$extension;} else {$name_new = '';} ;
    $targetPath = 'SINDICATO/'.$IDsindicato."/".$name_new;
    move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);
    
    $fecha2a = $_POST['fecha_esperada']; 
    $fecha2b = explode("-",$fecha2a);
    $fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];
    
    $descripcion = utf8_decode($_POST['descripcion']);
    $asunto = utf8_decode($_POST['asunto']);
    
    $IDmatrizA = $row_usuario['IDmatriz'];
    $IDareaA = $row_usuario['IDarea'];
    $EsSindicable = $_POST['IDsindicable'];
    
    $insertSQL = sprintf("INSERT INTO casos_sindicato (file, fecha_inicio, fecha_esperada, IDusuario, IDmatriz, IDarea, IDsucursal, IDestatus, asunto, IDtema, IDsindicable, IDempleado, descripcion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                           GetSQLValueString($name_new, "text"),
                           GetSQLValueString($mi_fecha, "text"),
                           GetSQLValueString($fecha2, "text"),
                           GetSQLValueString($_POST['IDusuario'], "int"),
                           GetSQLValueString($IDmatriz, "int"),
                           GetSQLValueString($IDarea, "text"),
                           GetSQLValueString($IDsucursal, "int"),
                           GetSQLValueString($_POST['IDestatus'], "int"),
                           GetSQLValueString($asunto, "text"),
                           GetSQLValueString($_POST['IDtema'], "int"),
                           GetSQLValueString($_POST['IDsindicable'], "int"),
                           GetSQLValueString($el_usuario, "int"),
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
    $totalRows_respos = mysql_num_rows($respos);

	if ($totalRows_respos > 0){
    
    do {
    
    $IDusuarioA = $row_respos['IDusuario'];
    
    $insertSQLA = "INSERT INTO casos_responsables (IDsindicato, IDusuario, IDtipo) VALUES ($last_id , $IDusuarioA, 2)";
    mysql_select_db($database_vacantes, $vacantes);
    $ResultA = mysql_query($insertSQLA, $vacantes) or die(mysql_error());
    
    } while ($row_respos = mysql_fetch_assoc($respos));

	} else {
	
	$insertSQLA = "INSERT INTO casos_responsables (IDsindicato, IDusuario, IDtipo) VALUES ($last_id , '1413', 2)";
    mysql_select_db($database_vacantes, $vacantes);
    $ResultA = mysql_query($insertSQLA, $vacantes) or die(mysql_error());
		
	}
    
    $i++; } 
    
    
    if ($EsSindicable == 1){
        
    $query_Sindicos = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($IDmatrizA,IDmatrizes) AND IDusuario_puesto = 511";
    $Sindicos = mysql_query($query_Sindicos, $vacantes) or die(mysql_error());
    $row_Sindicos = mysql_fetch_assoc($Sindicos);
        
    do {	
    
    $IDusuariox = $row_Sindicos['IDusuario'];
    
    $insertSQLx = "INSERT INTO casos_responsables (IDsindicato, IDusuario, IDtipo) VALUES ($last_id , $IDusuariox, 1)";
    mysql_select_db($database_vacantes, $vacantes);
    $Resultx = mysql_query($insertSQLx, $vacantes) or die(mysql_error());
    
    } while ($row_Sindicos = mysql_fetch_assoc($Sindicos));
        
    }
    
    header("Location: f_casos_sindicato.php?info=3");
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
    header('Location: f_casos_sindicato_edit.php?info=9&IDsindicato='.$IDsindicato.'');
    exit;
    } 
    if (isset($_POST['foto'])) {$name_new = $IDsindicato."_".$fechapp.".".$extension;} else {$name_new = '';} ;
    $targetPath = 'SINDICATO/'.$IDsindicato."/".$name_new;
    move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);
    
    $fecha2a = $_POST['fecha_esperada']; 
    $fecha2b = explode("-",$fecha2a);
    $fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];
    
    $descripcion = ($_POST['descripcion']);
    $asunto = ($_POST['asunto']);
    
    
    $updateSQL = sprintf("UPDATE casos_sindicato SET file=%s, fecha_esperada=%s, IDusuario=%s, IDmatriz=%s, IDarea=%s, IDsucursal=%s, asunto=%s, IDtema=%s, IDestatus=%s,  IDsindicable=%s, descripcion=%s WHERE IDsindicato=%s",
                           GetSQLValueString($name_new, "text"),
                           GetSQLValueString($fecha2, "text"),
                           GetSQLValueString($_POST['IDusuario'], "int"),
                           GetSQLValueString($IDmatriz, "int"),
                           GetSQLValueString($IDarea, "text"),
                           GetSQLValueString($IDsucursal, "int"),
                           GetSQLValueString($asunto, "text"),
                           GetSQLValueString($_POST['IDtema'], "int"),
                           GetSQLValueString($_POST['IDestatus'], "int"),
                           GetSQLValueString($_POST['IDsindicable'], "int"),
                           GetSQLValueString($descripcion, "text"),
                           GetSQLValueString($IDsindicato, "int"));
    
     mysql_select_db($database_vacantes, $vacantes);
     $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
    
     $last_id =  mysql_insert_id();
     header("Location: f_casos_sindicato.php?info=2");
     }
    
    // borrar alternativo
    if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
      
      $borrado = $_GET['IDsindicato'];
      $deleteSQL = "UPDATE casos_sindicato SET IDestatus = 0 WHERE IDsindicato ='$borrado'";
    
      mysql_select_db($database_vacantes, $vacantes);
      $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
      header("Location: f_casos_sindicato.php?info=4");
    }
    
    
    mysql_select_db($database_vacantes, $vacantes);
    $query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
    $matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
    $row_matriz = mysql_fetch_assoc($matriz);
    $totalRows_matriz = mysql_num_rows($matriz);
    $la_matriz = $row_matriz['matriz']; 
    
    mysql_select_db($database_vacantes, $vacantes);
    $query_lsucursal = "SELECT * FROM vac_sucursal ORDER BY sucursal";
    $lsucursal = mysql_query($query_lsucursal, $vacantes) or die(mysql_error());
    $row_lsucursal = mysql_fetch_assoc($lsucursal);
    $totalRows_lsucursal = mysql_num_rows($lsucursal);

    
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
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5Sin2.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>

    <script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<!-- /Theme JS files -->
 </head>
<body class= "has-detached-right<?php if (isset($_COOKIE["lmenu"])) { echo ' sidebar-xs';}?>">

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
              
					<h1 class="text-center content-group text-danger">
						Atención de Inquietudes
                    </h1>

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el Objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el Objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Atención de Inquietudes</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
										
                                    
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
												<option value="1"<?php if ($row_sindicato['IDestatus'] == 1) {echo "SELECTED";} ?>>En Proceso</option> 
												<option value="2"<?php if ($row_sindicato['IDestatus'] == 2) {echo "SELECTED";} ?>>Atendido</option> 
												<?php if ($row_usuario['user_casos_sindicato'] == 2) { ?>
												<option value="3"<?php if ($row_sindicato['IDestatus'] == 3) {echo "SELECTED";} ?>>Cerrado</option> 
												<?php } ?>
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

                                <?php } else { ?>
										<button type="submit"  class="btn btn-primary">Agregar</button>
                                        <input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
                                        <input type="hidden" name="MM_insert" value="form1">
                                        <input type="hidden" name="IDestatus" value="1">
								<?php } ?>
										<button type="button" onClick="window.location.href='f_casos_sindicato.php'" class="btn btn-default btn-icon">Regresar</button>

						 
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
                                    <a class="btn btn-danger" href="f_casos_sindicato_edit.php?IDsindicato=<?php echo $_GET['IDsindicato']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->
						 
						</fieldset>
                        </form>
                                    



                                    
                                    </div>
								</div>
							</div>
							<!-- /about author -->

                        




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
										<span>Instrucciones</span>
									</div>

									<div class="category-content">

										<div class="form-group">


										<p class="content-group">
										Ingresa la información solicitada; algunos campos son obligatorios.</br>
										</p>


										</div>

									</div>
								</div>
								<!-- /course details -->

								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Contacto</span>
									</div>

									<div class="category-content">

										<div class="form-group">

										<p class="content-group">
										Para cualquier duda respecto del uso de SGRH, por favor contactanos al teléfono 55 772 394 9396 o al correo jacardenas@sahuayo.mx.
										</p>


										</div>

									</div>
								</div>
								<!-- /upcoming courses -->

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