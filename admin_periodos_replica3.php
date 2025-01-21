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
$IDperiodovar = $row_variables['IDperiodo'];


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];


$query_periodos = "SELECT * FROM sed_periodos_sed"; 
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);

$query_periodos2 = "SELECT * FROM sed_periodos_sed"; 
$periodos2 = mysql_query($query_periodos2, $vacantes) or die(mysql_error());
$row_periodos2 = mysql_fetch_assoc($periodos2);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT * FROM vac_puestos WHERE vac_puestos.IDaplica_SED = 1 ORDER BY vac_puestos.denominacion ASC";  
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatriz)";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if (isset($_POST["IDempleado"]) AND isset($_POST["IDperiodo1"]) AND isset($_POST["IDaccion"])) {
foreach ($_POST['IDempleado'] as $puests)	{ $Empleados = implode(", ", $_POST['IDempleado']); } 
$_SESSION['Empleados'] = $Empleados;
$_SESSION['IDperiodo'] = $_POST['IDperiodo1'];
}

$IDperiodo = $_SESSION['IDperiodo'];
$fecha = date("Y-m-d"); 

$query_ponderacion = "SELECT Sum(sed_individuales.mi_ponderacion) AS total_p, Count(sed_individuales.mi_mi) AS total_m FROM sed_individuales WHERE sed_individuales.IDempleado = 10000 AND sed_individuales.IDperiodo = $IDperiodo";
mysql_query("SET NAMES 'utf8'");
$ponderacion = mysql_query($query_ponderacion, $vacantes) or die(mysql_error());
$row_ponderacion = mysql_fetch_assoc($ponderacion);
$ponderacion_total = $row_ponderacion['total_p'];
$metas_total = $row_ponderacion['total_m'];

$query_resultados = "SELECT * FROM sed_individuales_resultados WHERE sed_individuales_resultados.IDempleado = 10000 AND sed_individuales_resultados.IDperiodo = $IDperiodo"; 
mysql_query("SET NAMES 'utf8'");
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);
$estatus_actual = $row_resultados['estatus'];

$query_indicadores = "SELECT * FROM sed_indicadores_tipos"; 
$indicadores = mysql_query($query_indicadores, $vacantes) or die(mysql_error());
$row_indicadores = mysql_fetch_assoc($indicadores);

$query_unidades = "SELECT * FROM sed_unidad_medida"; 
$unidades = mysql_query($query_unidades, $vacantes) or die(mysql_error());
$row_unidades = mysql_fetch_assoc($unidades);

$query_mis_metas = "SELECT * FROM sed_individuales WHERE sed_individuales.IDempleado = 10000 AND sed_individuales.IDperiodo = $IDperiodo"; 
mysql_query("SET NAMES 'utf8'");
$mis_metas = mysql_query($query_mis_metas, $vacantes) or die(mysql_error());
$row_mis_metas = mysql_fetch_assoc($mis_metas);
$totalRows_mis_metas = mysql_num_rows($mis_metas);

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $IDmeta = $_POST['IDmeta'];	
  $mi_mi = $_POST['mi_mi'];	
  $mi_IDunidad = $_POST['mi_IDunidad'];	
  $mi_ponderacion = $_POST['mi_ponderacion'];	
  $mi_IDindicador = $_POST['mi_IDindicador'];	
  $mi_3 = $_POST['mi_3'];	
  $mi_2 = $_POST['mi_2'];	
  $mi_1 = $_POST['mi_1'];	
  $fecha_captura = $_POST['fecha_captura'];	
  $fecha_termino = $fecha; 
  
  $updateSQL = "UPDATE sed_individuales SET mi_mi = '$mi_mi', mi_mi = '$mi_mi', mi_IDunidad = '$mi_IDunidad', mi_ponderacion = '$mi_ponderacion', mi_IDindicador = '$mi_IDindicador', mi_3 = '$mi_3', mi_2 = '$mi_2', mi_1 = '$mi_1', fecha_captura = '$fecha_captura',  fecha_termino = '$fecha_termino' WHERE IDmeta = '$IDmeta'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: admin_periodos_replica3.php?info=2");
}

//insertar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
//correccion fechas

$fecha1 = $fecha; 
$insertSQL = sprintf("INSERT INTO sed_individuales (IDempleado, mi_mi, mi_IDunidad, mi_ponderacion, mi_IDindicador, mi_3, mi_2, mi_1, estatus, fecha_captura, fecha_termino, IDperiodo)
											   VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['mi_mi'], "text"),
                       GetSQLValueString($_POST['mi_IDunidad'], "int"),
                       GetSQLValueString($_POST['mi_ponderacion'], "text"),
                       GetSQLValueString($_POST['mi_IDindicador'], "int"),
                       GetSQLValueString($_POST['mi_3'], "text"),
                       GetSQLValueString($_POST['mi_2'], "text"),
                       GetSQLValueString($_POST['mi_1'], "text"),
                       GetSQLValueString($_POST['estatus'], "text"),
                       GetSQLValueString($_POST['fecha_captura'], "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($_POST['IDperiodo'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
 header("Location: admin_periodos_replica3.php?info=1");
}

// borrar 
if ((isset($_GET['IDmeta'])) && ($_GET['IDmeta'] != "")) {
  
  $borrado = $_GET['IDmeta'];
  $deleteSQL = "DELETE FROM sed_individuales WHERE IDmeta = '$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  
  header("Location:  admin_periodos_replica3.php?info=3");
}

// cerrar captura 
if ((isset($_GET['terminacaptura'])) && ($_GET['terminacaptura'] == 1)) {
$str_arr = explode (",", $_SESSION['Empleados']); 
foreach ($str_arr as $puests) { 

mysql_select_db($database_vacantes, $vacantes);
$query_activo = "SELECT * FROM prod_activos WHERE IDempleado=$puests";  
$activo = mysql_query($query_activo, $vacantes) or die(mysql_error());
$row_activo = mysql_fetch_assoc($activo);
$totalRows_activo = mysql_num_rows($activo);
$IDllave = $row_activo['IDllave'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultadosA = "SELECT * FROM sed_individuales WHERE IDempleado=10000 AND IDperiodo = $IDperiodo";  
$resultadosA = mysql_query($query_resultadosA, $vacantes) or die(mysql_error());
$row_resultadosA = mysql_fetch_assoc($resultadosA);
$totalRows_resultadosA = mysql_num_rows($resultadosA);
	
$updateSQLb = "INSERT INTO sed_individuales_resultados (IDempleado, IDperiodo, resultado, estatus, especial, IDllave) VALUES ($puests, $IDperiodo, 0, 1, 1, $IDllave)";
mysql_select_db($database_vacantes, $vacantes);
$Result1b = mysql_query($updateSQLb, $vacantes) or die(mysql_error());

do {
	
	$mi_mi = $row_resultadosA['mi_mi'];
	$mi_IDunidad = $row_resultadosA['mi_IDunidad'];
	$mi_ponderacion = $row_resultadosA['mi_ponderacion'];
	$mi_IDindicador = $row_resultadosA['mi_IDindicador'];
	$mi_3 = $row_resultadosA['mi_3'];
	$mi_2 = $row_resultadosA['mi_2'];
	$mi_1 = $row_resultadosA['mi_1'];
	
$updateSQLb = "INSERT INTO sed_individuales (IDempleado, mi_mi, mi_IDunidad, mi_ponderacion, mi_IDindicador, mi_3, mi_2, mi_1, mi_resultado, estatus, IDperiodo, fecha_captura, fecha_termino) VALUES($puests, '$mi_mi', '$mi_IDunidad', '$mi_ponderacion', '$mi_IDindicador', '$mi_3', '$mi_2', '$mi_1', 0, 1, '$IDperiodo', '$fecha', '$fecha')";
mysql_select_db($database_vacantes, $vacantes);
$Result1b = mysql_query($updateSQLb, $vacantes) or die(mysql_error());
	
} while ($row_resultadosA = mysql_fetch_assoc($resultadosA));

}
	
$deleteSQL = "DELETE FROM sed_individuales WHERE IDempleado=10000 AND IDperiodo = $IDperiodo";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

header("Location:  admin_periodos_replica.php?info=1");

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
	<script src="global_assets/js/plugins/forms/inputs/duallistbox.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_dual_listboxes.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    <?php if ($totalRows_mis_metas == 0) { ?> 
	<script>
      function load() {
       new Noty({
            text: 'Da clic en "Aregar Objetivo" para iniciar la captura.',
            type: 'info'
        }).show();
    }
	 window.onload = load;
     </script>
	<?php } ?>

    	<?php if ($ponderacion_total == 0 && $estatus_actual == "") { ?> 
	<script>
      function load() {
       new Noty({
            text: 'Debes capturar al menos 3 objetivos.',
            type: 'warning'
        }).show();
    }
	 window.onload = load;
     </script>
	<?php } ?>
    
    	<?php if ($ponderacion_total == 100 && ($estatus_actual == 0 or $estatus_actual == "")) { ?> 
	<script>
      function load() {
       new Noty({
            text: 'Su ponderación es igual a 100%, ya puedes dar clic en terminar captura.',
            type: 'success'
        }).show();
    }
	 window.onload = load;
     </script>
	<?php } ?>


</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
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
                        
    						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cerrado correctamente la captura de Objetivos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

    						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 0))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha habilitado la captura de Objetivos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

    						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 8 or $_GET['info'] == 9))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han importado correctamente los Objetivos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
					
					<p>Captura las metas. No se pueden editar de forma masiva.</p>                    
										 <?php if ($ponderacion_total == 100  && ($estatus_actual == 0 OR $estatus_actual == "") && $metas_total >= 3) { ?>
                                       <button type="button" data-target="#cierre" data-toggle="modal" class="btn btn-success">Terminar Captura y Replicar</button>
                                        <?php } ?> 

										<?php if ($ponderacion_total >= 100 ) { ?>
                                        <button type="button" data-target="#capturar2" data-toggle="modal" class="btn btn-primary">Agregar Objetivo</button>
  										<?php } else if ($metas_total == 10) { ?>
                                        <button type="button" data-target="#capturar3" data-toggle="modal" class="btn btn-primary">Agregar Objetivo</button>
										<?php } else { ?>
                                        <button type="button" data-target="#capturar" data-toggle="modal" class="btn btn-primary">Agregar Objetivo</button>
										<?php } ?>
					<p>&nbsp;</p>					


<?php if ($totalRows_mis_metas > 0) { ?>                       
                        

							<!-- /inicia ciclo metas -->
<?php $count = 1;  do { $IDmeta = $row_mis_metas['IDmeta']; ?>
							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Objetivo <?php echo $count; ?></h6>

									<div class="heading-elements">
										<ul class="list-inline list-inline-separate">
                                        <?php if ($estatus_actual == 0 or $estatus_actual == '') { ?>                       

											<li>
                                    <button type="button" data-target="#actualizar<?php echo $row_mis_metas['IDmeta']; ?>" data-toggle="modal" class="btn btn-primary btn-xs">Editar</button>
                                    <button type="button" data-target="#borrar<?php echo $row_mis_metas['IDmeta']; ?>" data-toggle="modal" class="btn btn-danger btn-xs">Borrar</button>
                                        <?php } ?>                       

											</li>
										</ul>
				                	</div>
								</div>

                      <div class="row">
						<div class="col-md-9">
                        
                        <div class="table-responsive">
							<table class="table table-xxs">
								<thead>
									<tr class="border-bottom-primary">
                       	<th colspan="2"><div class="text-bold content-group"> <?php echo $row_mis_metas['mi_mi']; ?></div></th>
                   	  </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td width="15%"><div class="text-bold content-group text-info">Sobresaliente:</div></td>
                        	<td width="85%"> <?php echo $row_mis_metas['mi_3']; ?></td>
                      </tr>
						<tr>
                        	<td><div class="text-bold content-group text-success">Satisfactorio:</div></td>
                        	<td> <?php echo $row_mis_metas['mi_2']; ?></td>
                      	</tr>
                      	<tr>
                        	<td><div class="text-bold content-group text-danger">Deficiente:</div></td>
                        	<td> <?php echo $row_mis_metas['mi_1']; ?></td>
                      </tr>
                     </tbody>
                   </table>    
						</div>
                        
						</div>

						<div class="col-md-3">
							<div class="panel-body">
								<p class="content-group-sm"><strong>Ponderación: </strong><?php echo $row_mis_metas['mi_ponderacion']; ?>%</p>
								<p class="content-group-sm"><strong>Unidad de Medida: </strong>
								<?php   switch ($row_mis_metas['mi_IDunidad']) {
									case 1: $unidad = 'Cantidad.';  break;    
									case 2: $unidad = 'Calidad.';  break;    
									case 3: $unidad = 'Cantidad-Costo.';  break;    
									case 4: $unidad = 'Cantidad-Calidad.';  break;    
									case 5: $unidad = 'Cantidad-Tiempo.';  break;    
									case 6: $unidad = 'Costo-Calidad.';  break;    
									case 7: $unidad = 'Tiempo.';  break;    
									case 8: $unidad = 'Tiempo-Calidad.';  break;    
									case 9: $unidad = 'Tiempo-Costo.';  break;    
								  } echo $unidad;
 								?></p>
								<p class="content-group-sm"><strong>Tipo Indicador: </strong>
								<?php   switch ($row_mis_metas['mi_IDindicador']) {
									case 1: $indicador = 'Estratégico Empresa.';  break;    
									case 2: $indicador = 'Estratégico del Área.';  break;    
									case 3: $indicador = 'Funcional.';  break;    
								  } echo $indicador;
 								?></p>
							</div>
						</div>
                        
                        
 						</div>
					</div>
							<!-- /course overview -->
                            

                    <!-- Modal de Actualizacion -->
					<div id="actualizar<?php echo $row_mis_metas['IDmeta']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Editar Objetivo de Desempeño</h5>
								</div>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="admin_periodos_replica3.php" > 
                                <fieldset class="content-group">
                                <div class="modal-body">

                                       <!-- Basic text input -->
								  <div class="form-group">
										<div class="col-lg-12">
											<textarea rows="3" class="wysihtml5 wysihtml5-min form-control" id="mi_mi" name="mi_mi" placeholder="Captura el objetivo de Desempeño." required="required"><?php echo $row_mis_metas['mi_mi']; ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                        
                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group text-info">Sobresaliente:*</div></label>
										<div class="col-lg-10">
											<textarea rows="2" class="form-control" id="mi_3" name="mi_3" required="required"><?php echo htmlentities($row_mis_metas['mi_3'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group text-success">Satisfactorio:*</div></label>
										<div class="col-lg-10">
											<textarea rows="2" class="form-control" id="mi_2" name="mi_2" required="required"><?php echo htmlentities($row_mis_metas['mi_2'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group text-danger">Deficiente:*</div></label>
										<div class="col-lg-10">
											<textarea rows="2" class="form-control" id="mi_1" name="mi_1" required="required"><?php echo htmlentities($row_mis_metas['mi_1'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group">Ponderación %:<span class="text-danger">*</span></div></label>
										<div class="col-lg-2">
						<input type="number" name="mi_ponderacion" id="mi_ponderacion" min="1" max="<?php echo (100 - $ponderacion_total + $row_mis_metas['mi_ponderacion']); ?>" class="form-control" value="<?php echo htmlentities($row_mis_metas['mi_ponderacion'], ENT_COMPAT, ''); ?>" required="required">
						 <span class="help-block">Máximo: <?php echo (100 - $ponderacion_total + $row_mis_metas['mi_ponderacion']) . "%"; ?></span>
										</div>
									<!-- /basic text input -->
                                                              
                                    <!-- Basic select -->
										<label class="control-label col-lg-2"><div class="text-bold content-group">Unidad de Medida:<span class="text-danger">*</span></div></label>
										<div class="col-lg-4">
											<select name="mi_IDunidad" id="mi_IDunidad" class="form-control" required="required">
												<option value="">Seleccione...</option> <?php  do { ?>
											  <option value="<?php echo $row_unidades['IDunidad']?>"<?php if (!(strcmp($row_unidades['IDunidad'], $row_mis_metas['mi_IDunidad']))) 
											  {echo "SELECTED";} ?>><?php echo $row_unidades['unidad']?></option>
											  <?php
											 } while ($row_unidades = mysql_fetch_assoc($unidades));
											   $rows = mysql_num_rows($unidades);
											   if($rows > 0) {
											   mysql_data_seek($unidades, 0);
											   $row_unidades = mysql_fetch_assoc($unidades);
											 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group">Tipo de Indicador:<span class="text-danger">*</span></div></label>
										<div class="col-lg-4">
											<select name="mi_IDindicador" id="mi_IDindicador" class="form-control" required="required">
												<option value="">Seleccione...</option> <?php  do { ?>
											  <option value="<?php echo $row_indicadores['IDtipo_indicador']?>"<?php if (!(strcmp($row_indicadores['IDtipo_indicador'], $row_mis_metas['mi_IDindicador']))) 
											  {echo "SELECTED";} ?>><?php echo $row_indicadores['tipo_indicador']?></option>
											  <?php
											 } while ($row_indicadores = mysql_fetch_assoc($indicadores));
											   $rows = mysql_num_rows($indicadores);
											   if($rows > 0) {
											   mysql_data_seek($indicadores, 0);
											   $row_indicadores = mysql_fetch_assoc($indicadores);
											 } ?>
									   </select>
									</div>
									<!-- /basic select -->

									</div>
								
                                    <hr>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          	                      		<input type="hidden" name="MM_update" value="form1">
          	                      		<input type="hidden" name="estatus" value="1">
          	                      		<input type="hidden" name="fecha_captura" value="<?php echo $fecha; ?>">
          	                      		<input type="hidden" name="fecha_termino" value="<?php echo $fecha; ?>">
          	                      		<input type="hidden" name="IDempleado" value="10000">
          	                      		<input type="hidden" name="IDperiodo" value="<?php echo $IDperiodo; ?>">
          	                      		<input type="hidden" name="IDmeta" value="<?php echo $row_mis_metas['IDmeta']; ?>">
                                        <input type="submit" class="btn btn-primary" value="Editar Objetivo">
									</div>
								
                                </fieldset>
                                </form>
                                
                           </div>
                        </div>
                     </div>
                    <!-- //Modal de Actualizacion -->

                    <!-- Modal de Borrado -->
					<div id="borrar<?php echo $row_mis_metas['IDmeta']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Borrar Objetivo de Desempeño</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el Objetivo?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-warning" href="admin_periodos_replica3.php?IDmeta=<?php echo $row_mis_metas['IDmeta']; ?>&borrar=1&IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=10000">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- //Modal de Borrado -->

                    <!-- Modal de Ponderacion Alcanzada -->
					<div id="capturar2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Ponderación</h6>
								</div>

								<div class="modal-body">
									<p>Estimado Usuario, tu ponderación actual suma 100%, por favor edita la ponderación de los objetivos actuales.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- //Ponderacion Alcanzada  -->

                    <!-- Modal de Ponderacion Alcanzada -->
					<div id="capturar3" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Objetivos</h6>
								</div>

								<div class="modal-body">
									<p>Estimado Usuario, has alcanzado el límite de 10 Objetivos para el periodo.</p>
                                     <?php if ($ponderacion_total != 100) { 
									echo '<span class="control-label no-margin text-danger">Por favor revisa tu ponderación y ajustala a 100%.</span>';
									} ?>
                                    </p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- //Ponderacion Alcanzada  -->


                    <!-- Modal de Cerrar-->
					<div id="cierre" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Cierre de Captura</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres cerrar la Captura?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                                    <a class="btn btn-warning" href="admin_periodos_replica3.php?terminacaptura=1">Si Cerrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- //Cerrar  -->

                    <!-- Modal Reabrir-->
					<div id="abrir" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Habilitar Captura</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres editar los Objetivos?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                                    <a class="btn btn-warning" href="admin_periodos_replica3.php?terminacaptura=0">Si Habilitar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- //Reabrir  -->


 <?php $count++; } while ($row_mis_metas = mysql_fetch_assoc($mis_metas)); ?>                           
							<!-- /termina ciclo metas -->
                            
                            
<?php } else { ?>   

							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Captura de Objetivos</h6>
								</div>

                      <div class="row">
						<div class="col-md-9">
                        
                        <div class="table-responsive">
							<table class="table">
								<tbody>
									<tr>
                       				<th><div class="content-group">Aún no cuentas con Objetivos capurados en este periodo de evaluación.</div>
                   	  				</tr>
                                </tbody>
		                   </table>    
						</div>
						</div>
 						</div>
					</div>
							<!-- /course overview -->
                    
<?php } ?>                       


                    <!-- Modal de Captura -->
					<div id="capturar" class="modal fade" tabindex="-3">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-success">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Agregar Objetivo de Desempeño</h5>
								</div>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="admin_periodos_replica3.php" > 
                                <fieldset class="content-group">
                                <div class="modal-body">

                                       <!-- Basic text input -->
								  <div class="form-group">
										<div class="col-lg-12">
											<textarea rows="3" class="wysihtml5 wysihtml5-min form-control" id="mi_mi" name="mi_mi" placeholder="Captura el objetivo de Desempeño." required="required"><?php echo htmlentities($row_mis_metas['mi_mi'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                        
                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group text-info">Sobresaliente:*</div></label>
										<div class="col-lg-10">
											<textarea rows="2" class="form-control" id="mi_3" name="mi_3" placeholder="Captura el resultado Sobresaliente" required="required"></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group text-success">Satisfactorio:*</div></label>
										<div class="col-lg-10">
											<textarea rows="2" class="form-control" id="mi_2" name="mi_2" placeholder="Captura el resultado Satisfactorio, es decir, el deber ser." required="required"></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group text-danger">Deficiente:*</div></label>
										<div class="col-lg-10">
											<textarea rows="2" class="form-control" id="mi_1" name="mi_1" placeholder="Captura el resultado Deficiente." required="required"></textarea>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group">Ponderación %:<span class="text-danger">*</span></div></label>
										<div class="col-lg-2">
						<input type="number" name="mi_ponderacion"   min="1" max="<?php echo (100 - $ponderacion_total); ?>" id="mi_ponderacion" class="form-control" value="" required="required">
						 <span class="help-block">Máximo: <?php echo (100 - $ponderacion_total) . "%"; ?></span>
                         				</div>
									<!-- /basic text input -->
                                    
                                    
                                    <!-- Basic select -->
										<label class="control-label col-lg-2"><div class="text-bold content-group">Unidad de Medida:<span class="text-danger">*</span></div></label>
										<div class="col-lg-4">
											<select name="mi_IDunidad" id="mi_IDunidad" class="form-control" required="required">
												<option value="">Seleccione...</option> 
											  <?php  do { ?>
											  <option value="<?php echo $row_unidades['IDunidad']?>"><?php echo $row_unidades['unidad']?></option>
											  <?php
											 } while ($row_unidades = mysql_fetch_assoc($unidades));
											   $rows = mysql_num_rows($unidades);
											   if($rows > 0) {
											   mysql_data_seek($unidades, 0);
											   $row_unidades = mysql_fetch_assoc($unidades);
											 } ?>
										  </select>
										</div>
									</div>
									<!-- /basic select -->

                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-2"><div class="text-bold content-group">Tipo de Indicador:<span class="text-danger">*</span></div></label>
										<div class="col-lg-4">
											<select name="mi_IDindicador" id="mi_IDindicador" class="form-control" required="required">
												<option value="">Seleccione...</option> <?php  do { ?>
											  <option value="<?php echo $row_indicadores['IDtipo_indicador']?>"><?php echo $row_indicadores['tipo_indicador']?></option>
											  <?php
											 } while ($row_indicadores = mysql_fetch_assoc($indicadores));
											   $rows = mysql_num_rows($indicadores);
											   if($rows > 0) {
											   mysql_data_seek($indicadores, 0);
											   $row_indicadores = mysql_fetch_assoc($indicadores);
											 } ?>
																			</select>
										</div>
                                        </div>
									<!-- /basic select -->
                                    
                                    <hr>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          	                      		<input type="hidden" name="MM_insert" value="form1">
          	                      		<input type="hidden" name="estatus" value="1">
          	                      		<input type="hidden" name="fecha_captura" value="<?php echo $fecha; ?>">
          	                      		<input type="hidden" name="fecha_termino" value="<?php echo $fecha; ?>">
          	                      		<input type="hidden" name="IDperiodo" value="<?php echo $IDperiodo; ?>">
          	                      		<input type="hidden" name="IDempleado" value="10000">
                                        <input type="submit" class="btn btn-primary" value="Agregar Objetivo">
									</div>
								
                                </div>
                                </fieldset>
                                </form>
                                
                           </div>
                        </div>
                     </div>
                    <!-- //Modal de Captura -->

				</div>

					<!-- /Contenido -->

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