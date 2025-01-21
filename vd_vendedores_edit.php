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
$mis_areas = $row_usuario['IDareas'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$IDusuario_ad = $row_usuario['IDusuario'];
$IDvd = $_GET['IDvd'];

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT com_vd.*, vac_matriz.matriz, vac_meses.mes FROM com_vd LEFT JOIN prod_activos AS Empleados ON com_vd.IDempleado = Empleados.IDempleado LEFT JOIN vac_matriz ON com_vd.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_meses ON com_vd.IDmes = vac_meses.IDmes WHERE com_vd.IDvd = '$IDvd'";
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
$row_reporte = mysql_fetch_assoc($reporte);
$totalRows_reporte = mysql_num_rows($reporte); 
$el_mes = $row_reporte['IDmes'];
$IDpuesto = $row_reporte['IDpuesto'];
$IDempleado = $row_reporte['IDempleado'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$formatos_permitidos =  array('pdf', 'PDF', 'jpeg', 'png', 'jpg');
$IDempleado_carpeta = 'CVD/'.$IDvd;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDvd.'JVD'.'.'.$extension;
$targetPath = 'CVD/'.$IDvd.'/'.$name_new;

// si se mandó archivo
if ($name != '') {	
	
if(!in_array($extension, $formatos_permitidos) ) { echo "error archivos"; 
header("Location: vd_vendedores_edit.php?IDvd=$IDvd&info=9"); 
}
	
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}

if ($name != '') {$name_new = $name_new;} else {$name_new = ''; }

 $updateSQL = sprintf("UPDATE com_vd SET bt_01_ad=%s, bt_02_ad=%s, bt_03_ad=%s, bt_04_ad=%s,  bt_05_ad=%s, BonoVentaNeta_ad=%s, BonoClientesVenta_ad=%s, BonoDevPorc_ad=%s, Premio_1_ad=%s, Premio_2_ad=%s, Comisiones_pieza_ad=%s, Comisiones_caja_ad=%s, coments=%s, IDusuario_ad=%s, file=%s WHERE IDvd=%s",
                       GetSQLValueString($_POST['bt_01_ad'], "text"),
                       GetSQLValueString($_POST['bt_02_ad'], "text"),
                       GetSQLValueString($_POST['bt_03_ad'], "text"),
                       GetSQLValueString($_POST['bt_04_ad'], "text"),
                       GetSQLValueString($_POST['bt_05_ad'], "text"),
                       GetSQLValueString($_POST['BonoVentaNeta_ad'], "text"),
                       GetSQLValueString($_POST['BonoClientesVenta_ad'], "text"),
                       GetSQLValueString($_POST['BonoDevPorc_ad'], "text"),
                       GetSQLValueString($_POST['Premio_1_ad'], "text"),
                       GetSQLValueString($_POST['Premio_2_ad'], "text"),
                       GetSQLValueString($_POST['Comisiones_pieza_ad'], "text"),
                       GetSQLValueString($_POST['Comisiones_caja_ad'], "text"),
                       GetSQLValueString($_POST['coments'], "text"),
                       GetSQLValueString($IDusuario_ad, "text"),
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($_POST['IDvd'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "vd_vendedores.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$lamatriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_mesese = "SELECT * FROM com_vd_objetivo_mes WHERE IDmatriz = $la_matriz AND IDmes = $el_mes AND anio = $anio";
$mesese = mysql_query($query_mesese, $vacantes) or die(mysql_error());
$row_mesese = mysql_fetch_assoc($mesese);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_conceptos = "SELECT * FROM com_vd_extras_conceptos";
$conceptos = mysql_query($query_conceptos, $vacantes) or die(mysql_error());
$row_conceptos = mysql_fetch_assoc($conceptos);
$totalRows_conceptos = mysql_num_rows($conceptos);

mysql_select_db($database_vacantes, $vacantes);
$query_extras = "SELECT com_vd_extras.*, com_vd_extras_conceptos.concepto FROM com_vd_extras LEFT JOIN com_vd_extras_conceptos ON com_vd_extras.IDconcepto = com_vd_extras_conceptos.IDconcepto WHERE IDvd = $IDvd";
mysql_query("SET NAMES 'utf8'");
$extras = mysql_query($query_extras, $vacantes) or die(mysql_error());
$row_extras = mysql_fetch_assoc($extras);
$totalRows_extras = mysql_num_rows($extras);

// borrar 
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDextra'];
  $deleteSQL = "DELETE FROM com_vd_extras WHERE IDextra = '$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location:  vd_vendedores_edit.php?IDvd=$IDvd&info=3");
}

//agregar extra
if ((isset($_POST["MM_update2"])) && ($_POST["MM_update2"] == "form1")) {
	
$formatos_permitidos =  array('pdf', 'PDF', 'jpeg', 'png', 'jpg');
$IDempleado_carpeta = 'CVD/'.$IDvd;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDvd.'JVD'.'.'.$extension;
$targetPath = 'CVD/'.$IDvd.'/'.$name_new;

// si se mandó archivo
if ($name != '') {	
	
if(!in_array($extension, $formatos_permitidos) ) { echo "error archivos"; 
header("Location: vd_vendedores_edit.php?IDvd=$IDvd&info=9"); 
}
	
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}

if ($name != '') {$name_new = $name_new;} else {$name_new = ''; }

$fecha_pago = date("Y-m-d");
$monto = $_POST["monto"];
$IDconcepto = $_POST["IDconcepto"];
$updateSQL = "INSERT INTO com_vd_extras (IDempleado, fecha_pago, monto, IDconcepto, IDvd, file) VALUES ($IDempleado, '$fecha_pago', '$monto', $IDconcepto, $IDvd, '$name_new')";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location:  vd_vendedores_edit.php?IDvd=$IDvd&info=4");

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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo cargado no es del tipo de archivos permitidos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro ha sido borrado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro ha sido agregado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Editar Pago Comisiones VD</h5>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.<br/>
							Para descontar un monto, captura el valor en negativo.</p>
							
                            <ul>
							<li><strong>Empleado:</strong> <?php echo $row_reporte['emp_paterno']." ".$row_reporte['emp_materno']." ".$row_reporte['emp_nombre']; ?></li>
							<li><strong>Puesto:</strong> <?php echo $row_reporte['denominacion']; ?></li>
							<li><strong>Matriz:</strong> <?php echo $row_reporte['matriz']; ?></li>
							<li><strong>Mes:</strong> <?php echo $row_reporte['mes']; ?></li>
                            </ul>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal" enctype="multipart/form-data">


<?php if ($IDpuesto == '212' ) { ?>

	<legend class="text-bold">Bono Transporte</legend>

	
                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_01_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_01']; ?></div>
										<div class="col-lg-2">
										<input type="number" name="bt_01_ad" id="bt_01_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_01_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_02_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_02']; ?></div>
										<div class="col-lg-2">
										<input type="number" name="bt_02_ad" id="bt_02_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_02_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_03_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_03']; ?></div>
										<div class="col-lg-2">
										<input type="number" name="bt_03_ad" id="bt_03_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_03_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
									 <?php if ($row_reporte['bt_04_fecha'] != '') { ?>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_04_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_04']; ?>
										</div>
										<div class="col-lg-2">
										<input type="number" name="bt_04_ad" id="bt_04_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_04_ad'], ENT_COMPAT, ''); ?>">
										</div>
									 <?php } if ($row_reporte['bt_05_fecha'] != '') {  ?>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_05_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_05']; ?>
										</div>
										<div class="col-lg-2	">
										<input type="number" name="bt_05_ad" id="bt_05_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_05_ad'], ENT_COMPAT, ''); ?>">
										</div>
									 <?php } ?>
									 </div>
									<!-- /basic text input -->
                                    
	<legend class="text-bold">Bono Productividad</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong>Venta Neta:</strong><br/><?php echo "$" . number_format($row_reporte['BonoVentaNeta']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="BonoVentaNeta_ad" id="BonoVentaNeta_ad" class="form-control" value="<?php echo htmlentities($row_reporte['BonoVentaNeta_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Clientes con Venta:</strong><br/><?php echo "$" . number_format($row_reporte['BonoClientesVenta']); ?> </div>
										<div class="col-lg-2">
										<input type="number" name="BonoClientesVenta_ad" id="BonoClientesVenta_ad" class="form-control" value="<?php echo htmlentities($row_reporte['BonoClientesVenta_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Devoluciones:</strong><br/><?php echo "$" . number_format($row_reporte['BonoDevPorc']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="BonoDevPorc_ad" id="BonoDevPorc_ad" class="form-control" value="<?php echo htmlentities($row_reporte['BonoDevPorc_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

	<legend class="text-bold">Premios</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong>Venta Cajas:</strong><br/><?php echo "$" . number_format($row_reporte['Premio_1']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Premio_1_ad" id="Premio_1_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Premio_1_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Venta Neta:</strong><br/><?php echo "$" . number_format($row_reporte['Premio_2']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Premio_2_ad" id="Premio_2_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Premio_2_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

	<legend class="text-bold">Comisiones</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong>Pieza:</strong><br/><?php echo "$" . number_format($row_reporte['Comisiones_pieza']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Comisiones_pieza_ad" id="Comisiones_pieza_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Comisiones_pieza_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Caja :</strong><br/><?php echo "$" . number_format($row_reporte['Comisiones_caja']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Comisiones_caja_ad" id="Comisiones_caja_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Comisiones_caja_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Archivo</strong>:</label>
										<div class="col-lg-7">
											<input type="file" name="file" id="file" class="file-styled" value="">
										<span class="help-block">Solo se permiten archivos de <code>Pdf</code> e <code>Imagenes</code>.</span>
										</div>
										<div class="col-lg-3">
										<?php if ($row_reporte['file'] !='') { ?><a class="btn btn-success" target="_blank" href="<?php echo "CVD/".$IDvd."/".$row_reporte['file']; ?>" >Descargar</a> <?php } ?>					
										</div>
									</div>
									<!-- /basic text input -->

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Justificación</strong>:</label>
										<div class="col-lg-10">
											<textarea rows="5" required="required" class="form-control" id="coments" name="coments"><?php echo htmlentities($row_reporte['coments'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->


                          <?php if($row_mesese['estatus'] == 0 OR 1 == 1) { ?>          
                         <button type="submit"  name="KT_Update1" class="btn btn-primary">Capturar</button>
                          <?php } ?>          
						 <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDvd" value="<?php echo $row_reporte['IDvd']; ?>">
                    	 <button type="button" onClick="window.location.href='vd_vendedores.php'" class="btn btn-default btn-icon">Regresar</button>


<?php } else if ($IDpuesto == '235') { //supervisor ?>

	<legend class="text-bold">Bono Transporte</legend>

	
                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_01_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_01']; ?></div>
										<div class="col-lg-2">
										<input type="number" name="bt_01_ad" id="bt_01_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_01_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_02_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_02']; ?></div>
										<div class="col-lg-2">
										<input type="number" name="bt_02_ad" id="bt_02_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_02_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_03_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_03']; ?></div>
										<div class="col-lg-2">
										<input type="number" name="bt_03_ad" id="bt_03_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_03_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
									 <?php if ($row_reporte['bt_04_fecha'] != '') { ?>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_04_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_04']; ?>
										</div>
										<div class="col-lg-2">
										<input type="number" name="bt_04_ad" id="bt_04_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_04_ad'], ENT_COMPAT, ''); ?>">
										</div>
									 <?php } if ($row_reporte['bt_05_fecha'] != '') {  ?>
										<div class="col-lg-2">
										<strong><?php echo date('d/m/Y', strtotime($row_reporte['bt_05_fecha'])); ?>:</strong><br/> $<?php echo $row_reporte['bt_05']; ?>
										</div>
										<div class="col-lg-2	">
										<input type="number" name="bt_05_ad" id="bt_05_ad" class="form-control" value="<?php echo htmlentities($row_reporte['bt_05_ad'], ENT_COMPAT, ''); ?>">
										</div>
									 <?php } ?>
									 </div>
									<!-- /basic text input -->
                                    
	<legend class="text-bold">Bono Productividad</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong>Venta Neta:</strong><br/><?php echo "$" . number_format($row_reporte['BonoVentaNeta']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="BonoVentaNeta_ad" id="BonoVentaNeta_ad" class="form-control" value="<?php echo htmlentities($row_reporte['BonoVentaNeta_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Clientes con Venta:</strong><br/><?php echo "$" . number_format($row_reporte['BonoClientesVenta']); ?> </div>
										<div class="col-lg-2">
										<input type="number" name="BonoClientesVenta_ad" id="BonoClientesVenta_ad" class="form-control" value="<?php echo htmlentities($row_reporte['BonoClientesVenta_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Devoluciones:</strong><br/><?php echo "$" . number_format($row_reporte['BonoDevPorc']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="BonoDevPorc_ad" id="BonoDevPorc_ad" class="form-control" value="<?php echo htmlentities($row_reporte['BonoDevPorc_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

	<legend class="text-bold">Premios</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong>Venta Cajas:</strong><br/><?php echo "$" . number_format($row_reporte['Premio_1']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Premio_1_ad" id="Premio_1_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Premio_1_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Venta Neta:</strong><br/><?php echo "$" . number_format($row_reporte['Premio_2']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Premio_2_ad" id="Premio_2_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Premio_2_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

	<legend class="text-bold">Comisiones</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong>Pieza:</strong><br/><?php echo "$" . number_format($row_reporte['Comisiones_pieza']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Comisiones_pieza_ad" id="Comisiones_pieza_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Comisiones_pieza_ad'], ENT_COMPAT, ''); ?>">
										</div>
										<div class="col-lg-2">
										<strong>Caja :</strong><br/><?php echo "$" . number_format($row_reporte['Comisiones_caja']); ?></div>
										<div class="col-lg-2">
										<input type="number" name="Comisiones_caja_ad" id="Comisiones_caja_ad" class="form-control" value="<?php echo htmlentities($row_reporte['Comisiones_caja_ad'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Archivo</strong>:</label>
										<div class="col-lg-7">
											<input type="file" name="file" id="file" class="file-styled" value="">
										<span class="help-block">Solo se permiten archivos de <code>Pdf</code> e <code>Imagenes</code>.</span>
										</div>
										<div class="col-lg-3">
										<?php if ($row_reporte['file'] !='') { ?><a class="btn btn-success" target="_blank" href="<?php echo "CVD/".$IDvd."/".$row_reporte['file']; ?>" >Descargar</a> <?php } ?>					
										</div>
									</div>
									<!-- /basic text input -->

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Justificación</strong>:</label>
										<div class="col-lg-10">
											<textarea rows="5" required="required" class="form-control" id="coments" name="coments"><?php echo htmlentities($row_reporte['coments'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->


                          <?php if($row_mesese['estatus'] == 0 OR 1 == 1) { ?>          
                         <button type="submit"  name="KT_Update1" class="btn btn-primary">Capturar</button>
                          <?php } ?>          
						 <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDvd" value="<?php echo $row_reporte['IDvd']; ?>">
                    	 <button type="button" onClick="window.location.href='vd_vendedores.php'" class="btn btn-default btn-icon">Regresar</button>


<?php } else  { ?>

	<legend class="text-bold">Pagos</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<div class="col-lg-2">
										<strong>Monto:</strong></div>
										<div class="col-lg-2">
										<input type="number" name="monto" id="monto" class="form-control" value="" required="required">
										</div>
										<div class="col-lg-2">
										<strong>Concepto:</strong></div>
										<div class="col-lg-2">
											<select name="IDconcepto" id="IDconcepto" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_conceptos['IDconcepto']?>"><?php echo $row_conceptos['concepto']?></option>
												  <?php
												 } while ($row_conceptos = mysql_fetch_assoc($conceptos));
												   $rows = mysql_num_rows($conceptos);
												   if($rows > 0) {
												   mysql_data_seek($conceptos, 0);
												   $row_conceptos = mysql_fetch_assoc($conceptos);
												 } ?>
											</select>
											</div>
									</div>
									<!-- /basic text input -->


                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Archivo</strong>:</label>
										<div class="col-lg-7">
											<input type="file" name="file" id="file" class="file-styled" value="">
										<span class="help-block">Solo se permiten archivos de <code>Pdf</code> e <code>Imagenes</code>.</span>
										</div>
										<div class="col-lg-3">
										</div>
									</div>
									<!-- /basic text input -->

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Justificación</strong>:</label>
										<div class="col-lg-10">
											<textarea rows="5" required="required" class="form-control" id="coments" name="coments"><?php echo htmlentities($row_reporte['coments'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->


                          <?php if($row_mesese['estatus'] == 0 OR 1 == 1) { ?>          
                         <button type="submit"  name="KT_Update2" class="btn btn-primary">Capturar</button>
                          <?php } ?>          
						 <input type="hidden" name="MM_update2" value="form1">
                         <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">
                    	 <button type="button" onClick="window.location.href='vd_vendedores.php'" class="btn btn-default btn-icon">Regresar</button>

<?php } ?>

						</form>
						
<p>&nbsp;</p>						
						


<?php if ($IDpuesto != '235' AND $IDpuesto != '212' AND $totalRows_extras > 0) {  ?>
						
					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-indigo-600">
                          <th>Fecha captura</th>
                          <th>Monto</th>
                          <th>Concepto</th>
                          <th>Archivo</th>
                          <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php do { ?>
                          <tr>
							</td>
							<td><?php echo date('d/m/Y', strtotime($row_extras['fecha_pago'])); ?></td>
                            <td><?php echo $row_extras['monto']; ?></td>
                            <td><?php echo $row_extras['concepto']; ?></td>
                            <td><?php if ($row_extras['file'] !='') { ?><a target="_blank" class="btn btn-success" href="<?php echo "CVD/".$IDvd."/".$row_extras['file']; ?>" >Descargar</a> <?php } ?>					
</td>
                            <td>
                            <button type="button" data-target="#borrar<?php echo $row_extras['IDextra']; ?>" data-toggle="modal" class="btn btn-danger">Borrar</button></li>
							</td>
                           </tr>                  

                    <!-- Modal de Borrado -->
					<div id="borrar<?php echo $row_extras['IDextra']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a href="vd_vendedores_edit.php?IDextra=<?php echo $row_extras['IDextra']; ?>&IDvd=<?php echo $IDvd; ?>&borrar=1" class="btn btn-danger">Si Borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- //Modal de Borrado -->

						   
                		 <?php } while ($row_extras = mysql_fetch_assoc($extras)); ?>
					    </tbody>
				    </table>
				</div>                   

<?php } ?>
					
						

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