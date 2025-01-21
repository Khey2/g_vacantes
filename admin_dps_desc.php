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


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$IDpuesto = $_GET['IDpuesto'];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto_1 = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE vac_puestos.IDpuesto = '$IDpuesto'";
mysql_query("SET NAMES 'utf8'");
$puesto_1 = mysql_query($query_puesto_1, $vacantes) or die(mysql_error());
$row_puesto_1 = mysql_fetch_assoc($puesto_1);
$totalRows_puesto_1 = mysql_num_rows($puesto_1);
$llave = $row_puesto_1['IDllave'];

mysql_select_db($database_vacantes, $vacantes);
$query_puesto_2 = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE prod_llave.IDllaveJ = '$llave'"; 	
$puesto_2 = mysql_query($query_puesto_2, $vacantes) or die(mysql_error());
$row_puesto_2 = mysql_fetch_assoc($puesto_2);
$totalRows_puesto_2 = mysql_num_rows($puesto_2);

// activos
$query_puesto_catalogos1 = "SELECT * FROM sed_dps WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_catalogos1 = mysql_query($query_puesto_catalogos1, $vacantes) or die(mysql_error());
$row_puesto_catalogos1 = mysql_fetch_assoc($puesto_catalogos1);
$totalRows_puesto_catalogos1 = mysql_num_rows($puesto_catalogos1);
$captura_parte_1 = $row_puesto_catalogos1['captura_a'];

// activos
$query_puesto_catalogos2 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'c' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos2 = mysql_query($query_puesto_catalogos2, $vacantes) or die(mysql_error());
$row_puesto_catalogos2 = mysql_fetch_assoc($puesto_catalogos2);
$totalRows_puesto_catalogos2 = mysql_num_rows($puesto_catalogos2);

// activos
$query_puesto_catalogos3 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'd' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos3 = mysql_query($query_puesto_catalogos3, $vacantes) or die(mysql_error());
$row_puesto_catalogos3 = mysql_fetch_assoc($puesto_catalogos3);
$totalRows_puesto_catalogos3 = mysql_num_rows($puesto_catalogos3);

// activos
$query_puesto_catalogos4 = "SELECT * FROM sed_dps WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_catalogos4 = mysql_query($query_puesto_catalogos4, $vacantes) or die(mysql_error());
$row_puesto_catalogos4 = mysql_fetch_assoc($puesto_catalogos4);
$totalRows_puesto_catalogos4 = mysql_num_rows($puesto_catalogos4);
$captura_parte_2 = $row_puesto_catalogos4['captura_b'];

// activos
$query_puesto_catalogos5 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'z' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos5 = mysql_query($query_puesto_catalogos5, $vacantes) or die(mysql_error());
$row_puesto_catalogos5 = mysql_fetch_assoc($puesto_catalogos5);
$totalRows_puesto_catalogos5 = mysql_num_rows($puesto_catalogos5);

// activos
$query_puesto_catalogos6 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'm' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos6 = mysql_query($query_puesto_catalogos6, $vacantes) or die(mysql_error());
$row_puesto_catalogos6 = mysql_fetch_assoc($puesto_catalogos6);
$totalRows_puesto_catalogos6 = mysql_num_rows($puesto_catalogos6);

// activos
$query_puesto_catalogos7 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'g' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos7 = mysql_query($query_puesto_catalogos7, $vacantes) or die(mysql_error());
$row_puesto_catalogos7 = mysql_fetch_assoc($puesto_catalogos7);
$totalRows_puesto_catalogos7 = mysql_num_rows($puesto_catalogos7);

// activos
$query_puesto_catalogos8 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'h' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos8 = mysql_query($query_puesto_catalogos8, $vacantes) or die(mysql_error());
$row_puesto_catalogos8 = mysql_fetch_assoc($puesto_catalogos8);
$totalRows_puesto_catalogos8 = mysql_num_rows($puesto_catalogos8);

$parte1 = 0;
$parte2 = 0;
$parte3 = 0;
$parte4 = 0;
$parte5 = 0;
$parte6 = 0;
$parte7 = 0;
$parte8 = 0;

if ($captura_parte_1 > 0) 			  {$parte1 = 1;}
if ($totalRows_puesto_catalogos2 > 0) {$parte2 = 1;}
if ($totalRows_puesto_catalogos3 > 0) {$parte3 = 1;}
if ($captura_parte_2 > 0) 			  {$parte4 = 1;}
if ($totalRows_puesto_catalogos5 > 0) {$parte5 = 1;}
if ($totalRows_puesto_catalogos6 > 0) {$parte6 = 1;}
if ($totalRows_puesto_catalogos7 > 0) {$parte7 = 1;}
if ($totalRows_puesto_catalogos8 > 0) {$parte8 = 1;}
$avance = $parte1 + $parte2 + $parte3 + $parte4 + $parte5 + $parte6 + $parte7 + $parte8;

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
							<h5 class="panel-title">Descriptivo de Puesto - Resumen</h5>
						</div>

					<div class="panel-body">

			<p><strong>Instrucciones</strong></p>
			<p>A continuación podrá observar las secciones del Descriptivo de Puesto.<br/>
             En cada sección, deberá capturar la información requerida.<br/>
             Para poder imprimir el puesto, deben estar capturadas todas las secciones.</p>
	         <p class="text-semibold">Cuando hayas completado de capturar todas las secciones, da clic en "Terminar Captura", para concluir.</p>
                    
                    <table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr>
                          <th>Sección</th>
                          <th>Descripción</th>
					      <th>Estatus</th>
					      <th>Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                        <tr>
                          <td>Datos Generales y Misión</td>
                          <td>Información general del puesto, incluyendo denominación, sucursal, línea de mando, entre otros.</td>
                          <td><?php if ($row_puesto_catalogos1['captura_a'] > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($row_puesto_catalogos1['captura_a'] > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_a.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_a.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>
                        <tr>
                          <td>Funciones</td>
                          <td>La razón de ser del puesto, así como sus principales funciones (entregables, procesos, resultados) esperados.</td>
                          <td><?php if ($totalRows_puesto_catalogos2 > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($totalRows_puesto_catalogos2 > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_b.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_b.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>
                        <tr>
                          <td>Relaciones del Puesto</td>
                          <td>Relaciones internas y externas del puesto necesarias para lograr los resultados esperados.</td>
                          <td><?php if ($totalRows_puesto_catalogos3 > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($totalRows_puesto_catalogos3 > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_c.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_c.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>                       
                        <tr>
                          <td>Entorno y Perfil del Puesto</td>
                          <td>Estructura organizacional, escolaridad, experiencia, conocimientos, condiciones especiales del puesto.</td>
                          <td><?php if ($row_puesto_catalogos4['captura_b'] > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($row_puesto_catalogos4['captura_b'] > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_d.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_d.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>                       
                        <tr>
                          <td>Activos</td>
                          <td>Asignación de Activos Fijos requeridos para el desarrollo del puesto (Software, Equipo, Auto, otros).</td>
                          <td><?php if ($totalRows_puesto_catalogos5 > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($totalRows_puesto_catalogos5 > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_e.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_e.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>                       
                        <tr>
                          <td>Indicadores de Gestión</td>
                          <td>Describe los indicadores de gestión del puesto, así como su unidad de medida y fórmula de cálculo.</td>
                          <td><?php if ($totalRows_puesto_catalogos6 > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($totalRows_puesto_catalogos6 > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_f.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_f.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>                       
                        <tr>
                          <td>Competencias</td>
                          <td>Se indican las competencias asignadas al Puesto, pueden ser de ingreso o permanencia.</td>
                          <td><?php if ($totalRows_puesto_catalogos7 > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($totalRows_puesto_catalogos7 > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_g.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_g.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>                       
                        <tr>
                          <td>Cursos de Capacitación</td>
                          <td>Se indican los cursos mínimos necesarios asignados al puesto, según el Programa de Capacitación Autorziado.</td>
                          <td><?php if ($totalRows_puesto_catalogos8 > 0) { echo "<span class='text-success-600'>Capturado <i class='icon-checkmark'></i></span>"; } else { echo "<span class='text-orange-600'>Pendiente <i class='icon-minus3'></i></span>";}?></td>
                         <td><?php if ($totalRows_puesto_catalogos8 > 0) { ?>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='admin_dps_h.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Actualizar</button>
                         <?php } else { ?>
                         <button type="button" class="btn bg-orange-600" onClick="window.location.href='admin_dps_h.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Capturar</button>
                         <?php } ?>
                         </td>
                        </tr>                       
                   	</tbody>							  
                 </table>







                    
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
                                        <a class="btn btn-xs btn-success btn-block content-group" href="admin_puestos.php?IDpuesto=<?php echo $IDpuesto; ?>&descrito=3">Terminar Revisión</a>
                                        </div>

										<div class="form-group">
                                        <a class="btn btn-xs btn-success btn-block content-group" href="imprimir.php?IDpuesto=<?php echo $IDpuesto; ?>"><i class="icon-file-pdf"></i> Descargar PDF</a>
                                        <a href="dps/imprimir_demo.php?IDpuesto=<?php echo $IDpuesto; ?>" target="_blank" class="btn btn-xs btn-warning btn-block content-group"><i class="icon-file-excel"></i> Descargar Excel</a>
                    </div>

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
											<div><?php echo $row_puesto_1['denominacion']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $row_puesto_1['area']; ?></div>
										</div>
                                        
                                     	<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto Tipo:</label>
											<div><?php if ($row_puesto_1['tipo'] == 1) {echo "SI";} else {echo "NO";} ?></div>
										</div>

									</div>
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