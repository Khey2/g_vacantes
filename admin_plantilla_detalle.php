<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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

$currentPage = $_SERVER["PHP_SELF"];

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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar 1
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {

$el_tipo = $_POST['IDtipo_plaza'];
$el_estatus = 1;
$la_matriz = $_GET['IDmatriz'];
$el_puesto = $_POST['IDpuesto'];
$IDplantilla = $_POST['IDplantilla'];

$fecha_filtro = $_POST['fecha_inicio'];
$y1 = substr( $fecha_filtro, 6, 4 );
$m1 = substr( $fecha_filtro, 3, 2 );
$d1 = substr( $fecha_filtro, 0, 2 );
$fecha_inicio =  $y1."-".$m1."-".$d1;

if($_POST['fecha_fin'] != '') {
$fecha_filtro2 = $_POST['fecha_fin'];
$y2 = substr( $fecha_filtro2, 6, 4 );
$m2 = substr( $fecha_filtro2, 3, 2 );
$d2 = substr( $fecha_filtro2, 0, 2 );
$fecha_fin =  $y2."-".$m2."-".$d2;
} else {$fecha_fin = '';}

if($_POST['fecha_congelada'] != '') {
$fecha_filtro3 = $_POST['fecha_congelada'];
$y3 = substr( $fecha_filtro3, 6, 4 );
$m3 = substr( $fecha_filtro3, 3, 2 );
$d3 = substr( $fecha_filtro3, 0, 2 );
$fecha_congelada =  $y3."-".$m3."-".$d3;
} else {$fecha_congelada = '';}

$updateSQL = sprintf("UPDATE prod_plantilla SET IDsucursal=%s, IDpuesto=%s, IDestatus=%s, IDtipo_plaza=%s, fecha_inicio=%s, fecha_fin=%s, fecha_congelada=%s, IDmotivo=%s, observaciones=%s 
					 WHERE IDplantilla='$IDplantilla'",
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['IDtipo_plaza'], "int"),
                       GetSQLValueString($fecha_inicio, "date"),
                       GetSQLValueString($fecha_fin, "date"),
                       GetSQLValueString($fecha_congelada, "date"),
                       GetSQLValueString($_POST['IDmotivo'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($IDplantilla, "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
   header('Location: admin_plantilla_detalle.php?info=2&IDpuesto='.$el_puesto.'&IDmatriz='.$la_matriz.'&IDtipo_plaza='.$el_tipo);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$el_tipo = $_POST['IDtipo_plaza'];
$el_estatus = 1;
$la_matriz = $_GET['IDmatriz'];
$el_puesto = $_POST['IDpuesto'];

$fecha_filtro = $_POST['fecha_inicio'];
$y1 = substr( $fecha_filtro, 6, 4 );
$m1 = substr( $fecha_filtro, 3, 2 );
$d1 = substr( $fecha_filtro, 0, 2 );
$fecha_inicio =  $y1."-".$m1."-".$d1;

if($_POST['fecha_fin'] != '') {
$fecha_filtro2 = $_POST['fecha_fin'];
$y2 = substr( $fecha_filtro2, 6, 4 );
$m2 = substr( $fecha_filtro2, 3, 2 );
$d2 = substr( $fecha_filtro2, 0, 2 );
$fecha_fin =  $y2."-".$m2."-".$d2;
} else {$fecha_fin = '';}

if($_POST['fecha_congelada'] != '') {
$fecha_filtro3 = $_POST['fecha_congelada'];
$y3 = substr( $fecha_filtro3, 6, 4 );
$m3 = substr( $fecha_filtro3, 3, 2 );
$d3 = substr( $fecha_filtro3, 0, 2 );
$fecha_congelada =  $y3."-".$m3."-".$d3;
} else {$fecha_congelada = '';}


$insertSQL = sprintf("INSERT INTO prod_plantilla (IDmatriz, IDsucursal, IDpuesto, IDestatus, IDtipo_plaza, fecha_inicio, fecha_fin, fecha_congelada, IDmotivo, observaciones) 
												   VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_GET['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['IDtipo_plaza'], "int"),
                       GetSQLValueString($fecha_inicio, "date"),
                       GetSQLValueString($fecha_fin, "date"),
                       GetSQLValueString($fecha_congelada, "date"),
                       GetSQLValueString($_POST['IDmotivo'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();
   header('Location: admin_plantilla_detalle.php?info=2&IDpuesto='.$el_puesto.'&IDmatriz='.$la_matriz.'&IDtipo_plaza='.$el_tipo);
}


if(isset($_GET['IDtipo_plaza'])) {$el_tipo = $_GET['IDtipo_plaza'];} else {$el_tipo = 1;}
if(isset($_GET['IDmatriz'])) {$la_matriz = $_GET['IDmatriz'];} else {$la_matriz = $IDmatriz;}
if(isset($_GET['IDpuesto'])) {$el_puesto = $_GET['IDpuesto'];} else {$el_puesto = 1;}
if(isset($_GET['fecha_filtro'])) {$fecha_filtro = $_GET['fecha_filtro'];} else {$fecha_filtro = date("Y-m-d");}

// borrar alternativo
if ((isset($_GET['borrado'])) && ($_GET['borrado'] == 1)) {

  $borrado = $_GET['IDplantilla'];
  $deleteSQL = "UPDATE prod_plantilla SET IDestatus = 0 WHERE IDplantilla = $borrado";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
   header('Location: admin_plantilla_detalle.php?info=3&IDpuesto='.$el_puesto.'&IDmatriz='.$la_matriz.'&IDtipo_plaza='.$el_tipo);
}

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = '$la_matriz'";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz  WHERE IDmatriz = '$la_matriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz2 = "SELECT * FROM vac_matriz ";
$lmatriz2 = mysql_query($query_lmatriz2, $vacantes) or die(mysql_error());
$row_lmatriz2 = mysql_fetch_assoc($lmatriz2);
$totalRows_lmatriz2 = mysql_num_rows($lmatriz2);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos ORDER BY denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);


//$y1 = substr( $fecha_filtro, 6, 4 );
//$m1 = substr( $fecha_filtro, 3, 2 );
//$d1 = substr( $fecha_filtro, 0, 2 );
//$fecha_inicio =  $y1."-".$m1."-".$d1;

//$fini_mes1 = new DateTime($fecha_inicio);
//$fini_mes1->modify('first day of this month');
//$fini_mes1k = $fini_mes1->format('Y/m/d'); 

//$fter_mes1 = new DateTime($fecha_inicio);
//$fter_mes1->modify('last day of this month');
//$fter_mes1k = $fter_mes1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT prod_plantilla.IDplantilla, prod_plantilla.IDpuesto,prod_plantilla.IDpuesto, prod_plantilla.IDmatriz,  prod_plantilla.IDmotivo,  prod_plantilla.observaciones, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.clave_puesto, vac_puestos.denominacion, vac_puestos.dias, vac_areas.IDarea, vac_areas.area, vac_matriz.matriz FROM  prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz WHERE prod_plantilla.IDmatriz IN ($la_matriz) AND prod_plantilla.IDtipo_plaza IN ($el_tipo) AND prod_plantilla.IDpuesto = $el_puesto AND prod_plantilla.IDestatus = 1 AND (DATE(fecha_inicio) <= '$fecha_filtro') AND ( DATE(fecha_fin) > '$fecha_filtro' OR DATE(fecha_fin) = '0000-00-00' OR DATE(fecha_fin) IS NULL) AND ( DATE(fecha_congelada) > '$fecha_filtro' OR DATE(fecha_congelada) = '0000-00-00' OR DATE(fecha_congelada) IS NULL) ORDER BY vac_puestos.denominacion ASC";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }

$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);



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

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/natural_sort.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    
    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<!-- /theme JS files -->
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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
							Se ha agregado correctamente la plaza.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la plaza.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la plaza.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Plantilla Autorizada</h6>
								</div>

							<div class="panel-body">
								<p>A continuación se muestra la plantilla autorizada de la Sucursal.</br>
								<p>&nbsp;</br>
                                      <button type="button" data-target="#modal_theme_danger" data-toggle="modal" class="btn btn-success btn-icon">Agregar Plaza</button>
  									<a class="btn btn-default" href="admin_plantilla.php">Regresar</a>
  
  
  					<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                  <tr class="bg-primary"> 
                                    <th>IDPuesto</th>
                                    <th>Clave</th>
                                    <th>Matriz</th>
                                    <th>Área</th>
                                    <th>Denominación</th>
                                    <th>Estatus</th>
                                    <th>Tipo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Cierre</th>
                                    <th>Fecha Congelada</th>
                                    <th>Detalles</th>
                                  <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_autorizados > 0){ ?>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_autorizados['IDplantilla']; ?>&nbsp;</td>
                                      <td><?php echo $row_autorizados['clave_puesto']; ?>&nbsp;</td>
                                      <td><?php echo $row_autorizados['matriz']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['area']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['denominacion']; ?>&nbsp; </td>
                                      <td><?php if($row_autorizados['IDestatus'] == 1 ) { echo "Activa"; }
										   else if($row_autorizados['IDestatus'] == 2 ) { echo "Cerrada"; } 
										   else if($row_autorizados['IDestatus'] == 3 ) { echo "Congelada"; }?></td>
                                      <td><?php if($row_autorizados['IDtipo_plaza'] == 1 ) { echo "Planta"; }
									  	   else if($row_autorizados['IDtipo_plaza'] == 2 ) { echo "Temporal"; } ?></td>
                                      <td><?php echo date( 'd/m/Y', strtotime($row_autorizados['fecha_inicio']));  ?>&nbsp; </td>
                                      <td><?php if($row_autorizados['fecha_fin'] == '') { echo "Sin fecha";} else { echo date( 'd/m/Y', strtotime($row_autorizados['fecha_fin'])); } ?>&nbsp; </td>
                                      <td><?php if($row_autorizados['fecha_congelada'] == '') { echo "Sin fecha";} else { echo date( 'd/m/Y', strtotime($row_autorizados['fecha_congelada'])); } ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['IDmotivo']; ?> <?php echo $row_autorizados['observaciones']; ?>&nbsp;</td>
                                      <td>
                                      <button type="button" data-target="#modal_theme_danger<?php echo $row_autorizados['IDplantilla']; ?>"  data-toggle="modal" class="btn btn-primary btn-icon">Editar</button>
                                      <button type="button" data-target="#modal_theme_danger<?php echo $row_autorizados['IDplantilla']; ?>2"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                                      </tr>
                                      
                    <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_autorizados['IDplantilla']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Editar Plaza</h6>
								</div>

								<div class="modal-body">
            					<form method="post" class="form-horizontal form-validate-jquery" name="form2"  action="<?php echo $editFormAction; ?>" >

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Matriz:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="IDmatriz" id="IDmatriz"  value="<?php echo $row_lmatriz['matriz']; ?>" readonly>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Sucursal:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDsucursal" id="IDsucursal" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_sucursal['IDsucursal']?>"<?php if (!(strcmp($row_sucursal['IDsucursal'], $row_autorizados['IDsucursal']))) {echo "SELECTED";} ?>><?php echo $row_sucursal['sucursal']?></option>
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
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Puesto:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDpuesto" id="IDpuesto" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $row_autorizados['IDpuesto']))) {echo "SELECTED";} ?>><?php echo $row_puesto['denominacion']?></option>
													  <?php
													 } while ($row_puesto = mysql_fetch_assoc($puesto));
													 $rows = mysql_num_rows($puesto);
													 if($rows > 0) {
													 mysql_data_seek($puesto, 0);
													 $row_puesto = mysql_fetch_assoc($puesto);
													 } ?>
                                          </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Tipo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDtipo_plaza" class="form-control">
                                            	<option value="1" <?php if (!(strcmp(1, htmlentities($row_autorizados['IDtipo_plaza'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Planta</option>
                                            	<option value="2" <?php if (!(strcmp(2, htmlentities($row_autorizados['IDtipo_plaza'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Temporal</option>
									      </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Fecha inicio:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control pickadate" name="fecha_inicio" id="fecha_inicio" value="<?php if ($row_autorizados['fecha_inicio'] == "") {echo "";} else { echo  date('d-m-Y', strtotime($row_autorizados['fecha_inicio']));  }?>" placeholder="Selecciona" required="required">
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Fecha cierre:</label>
												<div class="col-sm-9">
											<input type="text" class="form-control pickadate" name="fecha_fin" id="fecha_fin"  value="<?php if ($row_autorizados['fecha_fin'] == "") {echo "";} else { echo  date('d-m-Y', strtotime($row_autorizados['fecha_fin']));  }?>" placeholder="Selecciona">
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Fecha congelada:</label>
												<div class="col-sm-9">
											<input type="text" class="form-control pickadate" name="fecha_congelada" id="fecha_congelada"  value="<?php if ($row_autorizados['fecha_congelada'] == "") {echo "";} else { echo  date('d-m-Y', strtotime($row_autorizados['fecha_congelada']));  }?>" placeholder="Selecciona" >
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Motivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="IDmotivo" id="IDmotivo"  value="<?php  echo $row_autorizados['IDmotivo']; ?>" required="required" placeholder="Indica el motivo.">
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Observaciones:</label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="observaciones" id="observaciones"  value="<?php echo $row_autorizados['observaciones']; ?>" placeholder="Indica No. de Plaza y Comentarios." >
												</div>
											</div>
	                                    </div>



								<div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			                                	<input type="hidden" name="IDestatus" value="1">
			                                	<input type="hidden" name="MM_update" value="form2">
			                                	<input type="hidden" name="IDplantilla" value="<?php echo $row_autorizados['IDplantilla']; ?>">
                                                <input type="submit" class="btn btn-primary" value="Actualizar">
								</div>
                                
                                </form>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                    
                    <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_autorizados['IDplantilla']; ?>2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
								¿Estas seguro que quieres borrar la plaza? En su caso aplica el estatus de Cerrada.
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_plantilla_detalle.php?IDplantilla=<?php echo $row_autorizados['IDplantilla']; ?>&borrado=1&IDpuesto=<?php echo $row_autorizados['IDpuesto']; ?>&IDmatriz=<?php echo $row_autorizados['IDmatriz']; ?>&IDtipo_plaza=<?php echo $row_autorizados['IDtipo_plaza']; ?>&IDestatus=<?php echo $row_autorizados['IDestatus']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                                      
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados));   ?>
									<?php } else { ?>
                                    <tr>
                                      <td colspan="10">No hay plazas con el criterio indicado.</td>
                                    </tr>
									<?php } ?>
                                  </tbody>
                                </table>
                                
                                
                                                    
                    <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_autorizados['IDplantilla']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Agregar Plaza</h6>
								</div>

								<div class="modal-body">
            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="<?php echo $editFormAction; ?>" >

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Sucursal:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDsucursal" id="IDsucursal" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_lmatriz2['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz2['IDmatriz'], $la_matriz))) {echo "SELECTED";} ?>><?php echo $row_lmatriz2['matriz']?></option>
													  <?php
													 } while ($row_lmatriz2 = mysql_fetch_assoc($lmatriz2));
													 $rows = mysql_num_rows($lmatriz2);
													 if($rows > 0) {
													 mysql_data_seek($lmatriz2, 0);
													 $row_lmatriz2 = mysql_fetch_assoc($lmatriz2);
													 } ?>
                                          </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Sucursal:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDsucursal" id="IDsucursal" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_sucursal['IDsucursal']?>"><?php echo $row_sucursal['sucursal']?></option>
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
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Puesto:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDpuesto" id="IDpuesto" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $el_puesto))) {echo "SELECTED";} ?>><?php echo $row_puesto['denominacion']?></option>
													  <?php
													 } while ($row_puesto = mysql_fetch_assoc($puesto));
													 $rows = mysql_num_rows($puesto);
													 if($rows > 0) {
													 mysql_data_seek($puesto, 0);
													 $row_puesto = mysql_fetch_assoc($puesto);
													 } ?>
                                          </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Tipo de plaza">Tipo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDtipo_plaza" class="form-control">
                                            	<option value="1">Planta</option>
                                            	<option value="2">Temporal</option>
									      </select>
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Fecha inicio:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control pickadate" name="fecha_inicio" id="fecha_inicio" placeholder="Selecciona" required="required">
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Fecha cierre:</label>
												<div class="col-sm-9">
											<input type="text" class="form-control pickadate" name="fecha_fin" id="fecha_fin" placeholder="Selecciona">
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Fecha congelada:</label>
												<div class="col-sm-9">
											<input type="text" class="form-control pickadate" name="fecha_congelada" id="fecha_congelada"  placeholder="Selecciona" >
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Motivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="IDmotivo" id="IDmotivo"  value="" placeholder="Indica el motivo." required="required">
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Observaciones:</label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="observaciones" id="observaciones"  value="<?php echo $row_autorizados['observaciones']; ?>" placeholder="Indica No. de Plaza y Comentarios.">
												</div>
											</div>
	                                    </div>



								<div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			                                	<input type="hidden" name="IDestatus" value="1">
			                                	<input type="hidden" name="MM_insert" value="form1">
                                                <input type="submit" class="btn btn-primary" value="Agregar Plaza">
								</div>
                                
                                </form>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


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