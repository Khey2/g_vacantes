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

if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;

$query_periodo_sed = "SELECT * FROM sed_periodos_sed WHERE IDperiodo = '$IDperiodo'";
$periodo_sed = mysql_query($query_periodo_sed, $vacantes) or die(mysql_error());
$row_periodo_sed = mysql_fetch_assoc($periodo_sed);
$_periodo = $row_periodo_sed['periodo'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }


$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$usuario_activo = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$el_usuario = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_evaluado = "SELECT prod_activos.emp_paterno, prod_activos.IDempleado,  prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, prod_activos.IDllave, vac_areas.area, vac_matriz.matriz FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz  WHERE prod_activos.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$evaluado = mysql_query($query_evaluado, $vacantes) or die(mysql_error());
$row_evaluado = mysql_fetch_assoc($evaluado);
$totalRows_evaluado = mysql_num_rows($evaluado);

$IDempleado = $row_evaluado['IDempleado'];
$_nombre = $row_evaluado['emp_nombre'] . " " . $row_evaluado['emp_paterno'] . " " . $row_evaluado['emp_materno'];
$_puesto = $row_evaluado['denominacion'];
$_sucursal = $row_evaluado['matriz'];
$_area = $row_evaluado['area'];
$_fecha_ingreso = $row_evaluado['fecha_alta'];

$query_mis_metas = "SELECT * FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'"; 
mysql_query("SET NAMES 'utf8'");
$mis_metas = mysql_query($query_mis_metas, $vacantes) or die(mysql_error());
$row_mis_metas = mysql_fetch_assoc($mis_metas);
$totalRows_mis_metas = mysql_num_rows($mis_metas);

//capturadas
$query_mis_capturadas = "SELECT Count(sed_individuales.IDmeta) AS Total FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'"; 
$mis_capturadas = mysql_query($query_mis_capturadas, $vacantes) or die(mysql_error());
$row_mis_capturadas = mysql_fetch_assoc($mis_capturadas);
$totalRows_mis_capturadas = mysql_num_rows($mis_capturadas);
$metas_capturadas = $row_mis_capturadas['Total'];

//evaluadas
$query_mis_propuestas = "SELECT Count(sed_individuales.IDmeta) AS Total FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo' AND sed_individuales.estatus = 2"; 
$mis_propuestas = mysql_query($query_mis_propuestas, $vacantes) or die(mysql_error());
$row_mis_propuestas = mysql_fetch_assoc($mis_propuestas);
$totalRows_mis_propuestas = mysql_num_rows($mis_propuestas);
$metas_propuestas = $row_mis_propuestas['Total'];

//evaluadas
$query_mis_evaluadas = "SELECT Count(sed_individuales.IDmeta) AS Total FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo' AND sed_individuales.estatus = 3"; 
$mis_evaluadas = mysql_query($query_mis_evaluadas, $vacantes) or die(mysql_error());
$row_mis_evaluadas = mysql_fetch_assoc($mis_evaluadas);
$totalRows_mis_evaluadas = mysql_num_rows($mis_evaluadas);
$metas_evaluadas = $row_mis_evaluadas['Total'];

$query_indicadores = "SELECT * FROM sed_indicadores_tipos"; 
$indicadores = mysql_query($query_indicadores, $vacantes) or die(mysql_error());
$row_indicadores = mysql_fetch_assoc($indicadores);

$query_unidades = "SELECT * FROM sed_unidad_medida"; 
$unidades = mysql_query($query_unidades, $vacantes) or die(mysql_error());
$row_unidades = mysql_fetch_assoc($unidades);

$query_ponderacion = "SELECT Sum(sed_individuales.mi_ponderacion) AS total_p, Count(sed_individuales.mi_mi) AS total_m FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'";  
$ponderacion = mysql_query($query_ponderacion, $vacantes) or die(mysql_error());
$row_ponderacion = mysql_fetch_assoc($ponderacion);
$ponderacion_total = $row_ponderacion['total_p'];
$metas_total = $row_ponderacion['total_m'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']); }

$query_resultados = "SELECT * FROM sed_individuales_resultados WHERE sed_individuales_resultados.IDempleado = '$el_usuario' AND sed_individuales_resultados.IDperiodo = '$IDperiodo'"; 
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);
$estatus_actual = $row_resultados['estatus'];

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $IDmeta = $_POST['IDmeta'];	
  $mi_resultado = $_POST['mi_resultado'];	
  $mi_obs = $_POST['mi_obs'];	
  $id= $_POST['id'];	
  if ($el_usuario == $usuario_activo) {$estatus= 2;} else {$estatus= 3;}
  
  $updateSQL = "UPDATE sed_individuales SET mi_resultado = '$mi_resultado', mi_obs = '$mi_obs', estatus = '$estatus' WHERE IDmeta = '$IDmeta'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: f_desemp_evaluacion.php?info=1&IDperiodo=$IDperiodo&IDempleado=$el_usuario&id=$id");
  
  
  // actualizar resultados, poner botón de terminar!!
}

// cerrar captura 
if ((isset($_GET['terminacaptura'])) && ($_GET['terminacaptura'] != "")) {
  $id = $_GET['id'];
  $IDempleado = $_GET['IDempleado'];
  $terminacaptura = $_GET['terminacaptura'];
  if ($terminacaptura == 1) { $nuevo_estado = 2; } 
  elseif ($terminacaptura == 0) { $nuevo_estado = 3; } 
  
// calcula resultados

$query_resulta = "SELECT sum(case when estatus > 0 then 1 else 0 end) as Metas, sum(case when mi_resultado > 0 AND mi_resultado != 4 then mi_ponderacion else 0 end) as Ponderacion, sum(case when mi_resultado = 1 then mi_ponderacion * 10 else 0 end) as Sobresaliente, sum(case when mi_resultado = 2 then mi_ponderacion * 9 else 0 end) as Satisfactorio, sum(case when mi_resultado = 3 then mi_ponderacion * 6 else 0 end) as Deficiente, sum(case when mi_resultado = 4 then 1 else 0 end) as Enproceso, sed_individuales.IDempleado, sed_individuales.estatus, sed_individuales.IDperiodo FROM sed_individuales WHERE sed_individuales.IDempleado = '$IDempleado' AND sed_individuales.IDperiodo = '$IDperiodo'"; 
$resulta = mysql_query($query_resulta, $vacantes) or die(mysql_error());
$row_resulta = mysql_fetch_assoc($resulta);
$totalRows_resulta = mysql_num_rows($resulta);

$total_de_metas = $row_resulta['Metas'];
$total_de_ponderacion = $row_resulta['Ponderacion'];
$total_de_sobresalientes = $row_resulta['Sobresaliente'];
$total_de_satisfactorios = $row_resulta['Satisfactorio'];
$total_de_deficientes = $row_resulta['Deficiente'];
$total_de_enproceso = $row_resulta['Enproceso'];
$total_resultados = $total_de_sobresalientes + $total_de_satisfactorios + $total_de_deficientes;
$metas_a_calificar = $total_de_metas - $total_de_enproceso;
$total_resultados_100 = ($total_resultados / $total_de_ponderacion) * 10;

// ya hay resultados cargados
	if ($totalRows_resultados > 0) {
  $MYSQL = "UPDATE sed_individuales_resultados SET estatus = '$nuevo_estado', resultado = '$total_resultados_100' WHERE IDempleado = '$IDempleado' AND IDperiodo = '$IDperiodo'";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($MYSQL, $vacantes) or die(mysql_error());

 //$MYSQL2 = "UPDATE sed_individuales SET estatus = '$nuevo_estado' WHERE IDempleado = '$IDempleado' AND IDperiodo = '$IDperiodo'";
 //mysql_select_db($database_vacantes, $vacantes);
 //$result = mysql_query($MYSQL2, $vacantes) or die(mysql_error());

} else {

  $MYSQL = "INSERT INTO sed_individuales_resultados (IDempleado, IDperiodo, resultado, especial, IDllave, estatus) VALUES ('$IDempleado', '$IDperiodo', '$total_resultados_100', 1, '$IDllave', '$terminacaptura')";	
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($MYSQL, $vacantes) or die(mysql_error());

 //$MYSQL2 = "UPDATE sed_individuales SET estatus = '$nuevo_estado' WHERE IDempleado = '$IDempleado' AND IDperiodo = '$IDperiodo'";
 //mysql_select_db($database_vacantes, $vacantes);
 //$result = mysql_query($MYSQL2, $vacantes) or die(mysql_error());

}
  
  header("Location:  f_desemp_evaluacion.php?info=$terminacaptura&IDperiodo=$IDperiodo&IDempleado=$IDempleado&id=$id");
}

$metas_pendientes = $metas_capturadas - $metas_evaluadas;

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
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
    
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>	
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>

	<?php if ($metas_evaluadas != $metas_capturadas) { ?> 
	<script>
	    var  metas_pendientes = <?php echo $metas_pendientes; ?>;
      function load() {
       new Noty({
            text: 'Tiene '+metas_pendientes+' objetivos sin evaluar.',
            type: 'warning'
        }).show();
    }
	 window.onload = load;
     </script>
	<?php } ?>
    

	<?php if (($metas_evaluadas == $metas_capturadas) && $estatus_actual == 2) { ?> 
	<script>
      function load() {
       new Noty({
            text: 'Todas las metas han sido evaluadas, ya puedes dar clic en terminar evaluación.',
            type: 'info'
        }).show();
    }
	 window.onload = load;
     </script>
	<?php } ?>

	<?php if ($estatus_actual == 3) { ?> 
	<script>
      function load() {
       new Noty({
            text: 'La evaluación ha terminado, gracias por tu participación.',
            type: 'success'
        }).show();
    }
	 window.onload = load;
     </script>
	<?php } ?>
    

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
              
					<h1 class="text-center content-group text-danger">
						Evaluación del Desempeño
						<small class="display-block"><?php echo $row_periodo_sed['periodo']; ?></small>
					</h1>

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha evaluado correctamente el Objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Instrucciones</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
                                      <ol>
        <li>Asegurate de evaluar todos los objetivos.</li>
        <li>Da clic en el botón Evaluar y/o Actualizar, según corresponda.</li>
        <li><strong>Una vez que hayas terminado de evaluar, da clic en el botón Terminar Evaluación.</strong></li>
                                      </ol>
                                    </div>
								</div>
							</div>
							<!-- /about author -->

				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

							<!-- /inicia ciclo metas -->
<?php $count = 1;  do { $IDmeta = $row_mis_metas['IDmeta']; ?>
							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Objetivo <?php echo $count; ?> 
									<?php if ($row_mis_metas['estatus'] == 3) {echo "<span class='label label-warning'>Evaluado</span>";} ?>
                                    </h6>

									<div class="heading-elements">
										<ul class="list-inline list-inline-separate">
                                        
                                        <li>

											<?php  if ($row_mis_metas['estatus'] == 3  && ($estatus_actual == 2 or $estatus_actual == 1)) { ?>
                           <button type="button" data-target="#actualizar<?php echo $row_mis_metas['IDmeta']; ?>" data-toggle="modal" class="btn btn-warning btn-xs">Actualizar</button>
											<?php }  elseif ($row_mis_metas['estatus'] == 2 or $row_mis_metas['estatus'] == 1) {?>
                           <button type="button" data-target="#actualizar<?php echo $row_mis_metas['IDmeta']; ?>" data-toggle="modal" class="btn btn-warning	 btn-xs">Evaluar</button>
											<?php } else {?>
                           <button type="button" data-target="#actualizar<?php echo $row_mis_metas['IDmeta']; ?>" data-toggle="modal" class="btn btn-warning	 btn-xs">Ver</button>
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
					  <?php if ($row_mis_metas['mi_obs'] != '') { ?>
						<tr>
                        <td colspan="2">Observaciones:<br/>
						<?php echo $row_mis_metas['mi_obs']; ?>
						</td>
                      </tr>
					  <?php } ?>
                     </tbody>
                   </table>    
						</div>
                        
						</div>

						<div class="col-md-3">
							<div class="panel-body">
								<p class="content-group-sm"><strong>Ponderación: </strong><?php echo $row_mis_metas['mi_ponderacion']; ?>%</p>
								<p class="content-group-sm"><strong>Unidad de Medida: </strong>
								<?php   switch ($row_mis_metas['mi_IDunidad']) {
									case "": $unidad = 'Sin definir';  break;    
									case 1: $unidad = 'Cantidad.';  break;    
									case 2: $unidad = 'Calidad.';  break;    
									case 3: $unidad = 'Cantidad-Costo.';  break;    
									case 4: $unidad = 'Cantidad-Calidad.';  break;    
									case 5: $unidad = 'Cantidad-Tiempo.';  break;    
									case 6: $unidad = 'Costo-Calidad.';  break;    
									case 7: $unidad = 'Tiempo.';  break;    
									case 8: $unidad = 'Tiempo-Calidad.';  break;    
									case 9: $unidad = 'Tiempo-Costo.';  break;    
									default: $unidad = 'Sin definir';  }
									echo $unidad;
 								?></p>
								<p class="content-group-sm"><strong>Tipo Indicador: </strong>
								<?php   switch ($row_mis_metas['mi_IDindicador']) {
									case "": $indicador = 'Sin definir';  break;    
									case 1: $indicador = 'Estratégico Sahuayo.';  break;    
									case 2: $indicador = 'Estratégico del Área.';  break;    
									case 3: $indicador = 'Funcional.';  break;    
									default: $indicador = 'Sin definir';  
								  } echo $indicador;
 								?></p>
                                <?php if ($row_mis_metas['estatus'] == 3 or $row_mis_metas['estatus'] == 2) {?>
                                <p class="content-group-sm"><strong>Resultado: </strong>
								<?php 
							      if($row_mis_metas['mi_resultado'] == 1) { echo "<span class='label label-primary'>Sobresaliente</span>"; } 
							 else if($row_mis_metas['mi_resultado'] == 2) { echo "<span class='label label-success'>Satisfactorio</span>"; } 
							 else if($row_mis_metas['mi_resultado'] == 3) { echo "<span class='label label-warning'>Deficiente</span>"; } 
							 else if($row_mis_metas['mi_resultado'] == 4) { echo "<span class='label label-default'>En proceso-No aplica</span>"; } 
							 else { echo "<span class='label label-default'>Sin Evaluación</span>";} ?></p>
                              <?php } ?>
                              </p>
                            </div>
						</div>
                        
                        
 						</div>
					</div>
							<!-- /course overview -->
                            

                    <!-- Modal de Actualizacion -->
					<div id="actualizar<?php echo $row_mis_metas['IDmeta']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-warning">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Evaluar Objetivo de Desempeño</h5>
								</div>
                                
                                <div class="text-primary"><strong>Instrucciones: </strong>Selecciona el resultado logrado. Asimismo, describe el resultado obtenido.</div>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $_GET['id']; ?>" > 
                                <fieldset class="content-group">
                                <div class="modal-body">

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-1"><div class="text-bold content-group">Objetivo:</div></label>
										<div class="col-lg-11">
											<div class="text-semibold content-group text-left"><?php echo $row_mis_metas['mi_mi']; ?></div>
										</div>
									</div>
									<!-- /basic text input -->
                                    
									<div class="form-group">
											<div class="col-md-12 text-left">
												<div class="radio">
													<label>
														<input type="radio"id="mi_resultado" name="mi_resultado" class="control-primary" value="1" <?php if (!(strcmp(htmlentities($row_mis_metas['mi_resultado'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?>required="required">
														<span class="text text-primary text-bold">Sobresaliente: </span><?php echo htmlentities($row_mis_metas['mi_3'], ENT_COMPAT, ''); ?>
													</label>
												</div>

												<div class="radio">
													<label>
														<input type="radio" id="mi_resultado" name="mi_resultado" class="control-success" value="2" <?php if (!(strcmp(htmlentities($row_mis_metas['mi_resultado'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?>>
														<span class="text text-success text-bold">Satisfactorio: </span><?php echo htmlentities($row_mis_metas['mi_2'], ENT_COMPAT, ''); ?>
													</label>
												</div>

												<div class="radio">
													<label>
														<input type="radio" id="mi_resultado" name="mi_resultado" class="control-warning" value="3" <?php if (!(strcmp(htmlentities($row_mis_metas['mi_resultado'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?>>
														<span class="text text-danger text-bold">Deficiente: </span><?php echo htmlentities($row_mis_metas['mi_1'], ENT_COMPAT, ''); ?>
													</label>
												</div>

												<div class="radio">
													<label>
														<input type="radio" id="mi_resultado" name="mi_resultado" class="control-info" value="4" <?php if (!(strcmp(htmlentities($row_mis_metas['mi_resultado'], ENT_COMPAT, 'utf-8'),0))) {echo "checked=\"checked\"";} ?>>
														<span class="text text-danger text-info">En proceso-No aplica:</span> No cuenta para la evaluación. 
													</label>
												</div>

											</div>
									</div>
                                    
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-1"><div class="text-bold content-group">Pond. %:</div></label>
										<div class="col-lg-2">
											<div class="text content-group text-left"><?php echo htmlentities($row_mis_metas['mi_ponderacion'], ENT_COMPAT, ''); ?></div>
										</div>
									<!-- /basic text input -->
                          
                                    <!-- Basic select -->
										<label class="control-label col-lg-1"><div class="text-bold content-group">Unidad Medida:</div></label>
										<div class="col-lg-2">
											<div class="text content-group text-left"><?php echo htmlentities($unidad, ENT_COMPAT, ''); ?></div>
										</div>
									<!-- /basic select -->

                                    
									<!-- Basic select -->
										<label class="control-label col-lg-1"><div class="text-bold content-group">Tipo Ind.:</div></label>
										<div class="col-lg-2">
											<div class="text content-group text-left"><?php echo htmlentities($indicador, ENT_COMPAT, ''); ?></div>
										</div>
									<!-- /basic select -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<div class="col-lg-12">
											<textarea rows="3" class="wysihtml5 wysihtml5-min form-control" id="mi_obs" name="mi_obs" placeholder="Describa el resultado obtenido."><?php echo htmlentities($row_mis_metas['mi_obs'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                    <hr>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          	                      		<input type="hidden" name="MM_update" value="form1">
          	                      		<input type="hidden" name="estatus" value="3">
          	                      		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
          	                      		<input type="hidden" name="fecha_captura" value="<?php echo $fecha; ?>">
          	                      		<input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>">
          	                      		<input type="hidden" name="IDperiodo" value="<?php echo $IDperiodo; ?>">
          	                      		<input type="hidden" name="IDmeta" value="<?php echo $row_mis_metas['IDmeta']; ?>">
                                        
											<?php if ($row_mis_metas['estatus'] == 3  && ($estatus_actual == 2  OR $estatus_actual == 1)) {  ?>
                           <input type="submit" class="btn btn-warning" value="Evaluar Objetivo">
											<?php }  elseif ($row_mis_metas['estatus'] == 2  or $row_mis_metas['estatus'] == 1) {?>
                           <input type="submit" class="btn btn-warning" value="Evaluar Objetivo">
											<?php } else {?>
                           
											<?php } ?>
                                        
									</div>
								
                                </div>
                                </fieldset>
                                </form>
                                
                           </div>
                        </div>
                     </div>
                    <!-- //Modal de Actualizacion -->


 <?php $count++; } while ($row_mis_metas = mysql_fetch_assoc($mis_metas)); ?>                           
							<!-- /termina ciclo metas -->
                            
                            
						</div>
					</div>
					<!-- /detached content -->



                    <!-- Modal de Cerrar-->
					<div id="cierre" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Cierre de Evaluación</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres cerrar la Evaluación?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                                    <a class="btn btn-warning" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=<?php echo $_GET['id']; ?>&terminacaptura=0">Si Cerrar</a>
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
									<h6 class="modal-title">Habilitar Evaluación</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres abrir la Evaluación?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Regresar</button>
                                    <a class="btn btn-warning" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=<?php echo $_GET['id']; ?>&terminacaptura=1">Si Habilitar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- //Reabrir  -->



					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">


								<!-- Categories -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Acciones</span>
									</div>

									<div class="category-content no-padding">
									<div class="category-content">
                                        
                                        <?php if (($metas_capturadas == $metas_evaluadas) && ($estatus_actual == 2 or $estatus_actual == 1)) { ?>
	                                 <p><button type="button" data-target="#cierre" data-toggle="modal" class="btn btn-xs btn-success btn-block content-group">Terminar Evaluación</button></p>
                                        <?php } ?> 
                                        
                                         <?php if ($estatus_actual == 3) { // abrir de nuevo ?>
										<button type="button" data-target="#abrir" data-toggle="modal" class="btn btn-xs btn-primary btn-block content-group">Abrir Evaluación</button>
										<?php } ?> 

                                         <?php if ($estatus_actual > 0) { ?>
                                    	<p><a class="btn btn-primary  btn-xs btn-block content-group" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario; ?>&print=1"><i class="icon-printer4"></i> Imprimir Resultados</a></p>
                                        <?php } ?> 
                                    
                                    <p><strong>Ponderación:</strong>
                                     <?php if ($ponderacion_total != 100) { 
									echo '<span class="control-label no-margin text-danger">' . $ponderacion_total . '% (Revisar)</span>';
									} else {
									echo '<span class="control-label no-margin text-success">' . $ponderacion_total . '% (Correcto)</span>'; } ?>
                                    </p>
                                     <p><strong>Objetivos Evaluados:</strong>
                                      <?php if ($metas_evaluadas == $metas_capturadas) { 
									echo '<span class="control-label no-margin text-success">' . $metas_evaluadas . ' de '. $metas_capturadas .'.</span>';
									} else {
									echo '<span class="control-label no-margin text-warning">' . $metas_evaluadas  . ' de '. $metas_capturadas .'.</span>';
									} ?>
                                    </p>
                                    <p><strong>Resultado:</strong>
                                      <?php if ($estatus_actual == 3) { 
							      if($row_resultados['resultado'] > 95) { echo $row_resultados['resultado']. "% <span class='label label-primary'>Sobresaliente</span>"; } 
							 else if($row_resultados['resultado'] > 75) { echo $row_resultados['resultado']. "% <span class='label label-success'>Satisfactorio</span>"; } 
							 else if($row_resultados['resultado'] > 1 ) { echo $row_resultados['resultado']. "% <span class='label label-warning'>Deficiente</span>"; } 
							 else { echo "<span class='label label-default'>Sin Evaluación</span>";}
									  } ?>
                                    </p>
                                     </div>   
									</div>
								</div>
								<!-- /categories -->



								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Datos del Evaluado</span>
									</div>

									<div class="category-content">

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Nombre:</label>
											<div><?php echo $_nombre; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<div><?php echo $_puesto; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Sucursal:</label>
											<div><?php echo $_sucursal; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $_area; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Periodo de Evaluación:</label>
											<div><?php echo $_periodo; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Fecha Ingreso:</label>
											<div><?php echo $_fecha_ingreso; ?></div>
										</div>

									</div>
								</div>
								<!-- /course details -->


								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Fechas Compromiso</span>
									</div>

									<div class="category-content">
										<ul class="media-list">

												  <?php
	 											    $query_mensajes = "SELECT * FROM sed_mensajes WHERE IDperiodo = '$IDperiodo'"; 
													$mensajes = mysql_query($query_mensajes, $vacantes) or die(mysql_error());
													$row_mensajes = mysql_fetch_assoc($mensajes);
													
													do { ?>

											<li class="media">
												<div class="media-left">
													<h5 class="no-margin text-center text-success"> <?php echo $row_mensajes['mes']; ?>
														<small class="display-block text-size-small no-margin"><?php echo $row_mensajes['anio']; ?></small>
													</h5>
												</div>

												<div class="media-body">
													<span class="text-semibold"><?php echo $row_mensajes['mensaje']; ?></span>
													<ul class="list-inline list-inline-separate no-margin-bottom mt-5">
														<li><span class="text-muted"><?php echo $row_periodo_sed['periodo']; ?></span></li>
													</ul>
												</div>
											</li>

												  <?php } while ($row_mensajes = mysql_fetch_assoc($mensajes)); ?>

										</ul>
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


<script>
		document.addEventListener("DOMContentLoaded", function(){
			// Invocamos cada 5 segundos ;)
			const milisegundos = 60 *1000;
			setInterval(function(){
				// No esperamos la respuesta de la petición porque no nos importa
				fetch("./refresco.php");
			},milisegundos);
		});
</script>

</body>
</html>