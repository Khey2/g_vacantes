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
$IDllave = $row_usuario['IDllave'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

$query_grupos = "SELECT DISTINCT sed_competencias_resultados.IDgrupo, sed_competencias_grupos.grupo FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado INNER JOIN sed_competencias_grupos ON  sed_competencias_resultados.IDgrupo = sed_competencias_grupos.IDgrupo WHERE sed_competencias_resultados.IDempleado_evaluador = '$el_usuario' AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDgrupo ORDER BY sed_competencias_resultados.IDgrupo DESC";
$grupos  = mysql_query($query_grupos, $vacantes) or die(mysql_error());
$row_grupos  = mysql_fetch_assoc($grupos);
$totalRows_grupos = mysql_num_rows($grupos);


//comprueba que al menos haya un grupo
if($totalRows_grupos > 0) {

// si se selecciona Grupo
if(isset($_POST['IDgrupo'])) {
$_SESSION['IDgrupo'] = $_POST['IDgrupo']; 
} else {
$_SESSION['IDgrupo'] = $row_grupos['IDgrupo'];
}	
} else {
$_SESSION['IDgrupo'] = 99;
}


$el_grupo = $_SESSION['IDgrupo'];
$query_grupo_act = "SELECT * FROM sed_competencias_grupos WHERE IDgrupo = $el_grupo";
$grupo_act  = mysql_query($query_grupo_act, $vacantes) or die(mysql_error());
$row_grupo_act  = mysql_fetch_assoc($grupo_act);
$totalRows_grupo_act = mysql_num_rows($grupo_act);

// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_evaluacion = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, vac_puestos.IDnivel_puestoC,  sed_competencias_resultados.comp1, sed_competencias_resultados.comp2, sed_competencias_resultados.comp3, sed_competencias_resultados.comp4, sed_competencias_resultados.comp5, sed_competencias_resultados.comp6, sed_competencias_resultados.comp7, sed_competencias_resultados.comp8, vac_puestos.denominacion, vac_areas.area, vac_areas.area, vac_matriz.matriz FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDgrupo = $el_grupo";
$evaluacion = mysql_query($query_evaluacion, $vacantes) or die(mysql_error());
$row_evaluacion = mysql_fetch_assoc($evaluacion);
$totalRows_evaluacion = mysql_num_rows($evaluacion);
$elnivel = $row_evaluacion['IDnivel_puestoC'];

// autoevaluacion

// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM sed_competencias_resultados INNER JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = '$el_usuario' AND sed_competencias_resultados.IDempleado_evaluador = '$el_usuario' AND sed_competencias_resultados.IDtipo = 1 AND sed_competencias_resultados.anio = $anio AND sed_competencias_resultados.IDgrupo = $el_grupo";
$tipo1 = mysql_query($query_tipo1, $vacantes) or die(mysql_error());
$row_tipo1 = mysql_fetch_assoc($tipo1);
$totalRows_tipo1 = mysql_num_rows($tipo1);

// jefe inmediato
mysql_select_db($database_vacantes, $vacantes);
$query_tipo2 = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM sed_competencias_resultados INNER JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado_evaluador = '$el_usuario'  AND sed_competencias_resultados.IDtipo = 2 AND sed_competencias_resultados.anio = $anio AND sed_competencias_resultados.IDgrupo = $el_grupo";
$tipo2 = mysql_query($query_tipo2, $vacantes) or die(mysql_error());
$row_tipo2 = mysql_fetch_assoc($tipo2);
$totalRows_tipo2 = mysql_num_rows($tipo2);

// pares
mysql_select_db($database_vacantes, $vacantes);
$query_tipo3 = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM sed_competencias_resultados INNER JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado_evaluador = '$el_usuario'  AND sed_competencias_resultados.IDtipo = 3 AND sed_competencias_resultados.anio = $anio AND sed_competencias_resultados.IDgrupo = $el_grupo";
$tipo3 = mysql_query($query_tipo3, $vacantes) or die(mysql_error());
$row_tipo3 = mysql_fetch_assoc($tipo3);
$totalRows_tipo3 = mysql_num_rows($tipo3);

// Colaboradores
mysql_select_db($database_vacantes, $vacantes);
$query_tipo4 = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM sed_competencias_resultados INNER JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado_evaluador = '$el_usuario'  AND sed_competencias_resultados.IDtipo = 4 AND sed_competencias_resultados.anio = $anio AND sed_competencias_resultados.IDgrupo = $el_grupo";
$tipo4 = mysql_query($query_tipo4, $vacantes) or die(mysql_error());
$row_tipo4 = mysql_fetch_assoc($tipo4);
$totalRows_tipo4 = mysql_num_rows($tipo4);

// Clientes
mysql_select_db($database_vacantes, $vacantes);
$query_tipo5 = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM sed_competencias_resultados INNER JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado_evaluador = '$el_usuario'  AND sed_competencias_resultados.IDtipo = 5 AND sed_competencias_resultados.anio = $anio AND sed_competencias_resultados.IDgrupo = $el_grupo";
$tipo5 = mysql_query($query_tipo5, $vacantes) or die(mysql_error());
$row_tipo5 = mysql_fetch_assoc($tipo5);
$totalRows_tipo5 = mysql_num_rows($tipo5);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$query_area = "SELECT * FROM vac_areas WHERE IDarea = '$IDarea'";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);

$evaluaciones = $totalRows_tipo1 + $totalRows_tipo2 + $totalRows_tipo3 + $totalRows_tipo4 + $totalRows_tipo5;

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    
    <script src="assets/js/app.js"></script>
	<!-- /Theme JS files -->
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
						Evaluación de 360° por Competencias
                    </h1>

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha evaliado correctamente al Empleado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Mis evaluaciones asignadas</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
                                    
                                    <div class="row show-grid">
									<div class="col-md-8"><div>
                                    <p>En  <?php echo $row_variables['empresa']; ?> es importante evaluar al equipo de trabajo, debido a que el éxito de la Compañía está basado en gran parte en el desempeño de las personas, y mientras éste sea medido y monitoreado será posible tomar decisiones y emprender acciones orientadas a obtener mejores resultados en la productividad de todos.</p>
<p>La evaluación de 360° es una herramienta de gestión de talento humano que consiste en una evaluación integral para medir las competencias de los colaboradores. Esta herramienta se basa en las relaciones que tiene el empleado, de tal manera que su resultado de integra por las evaluaciones de sus colaboradores, clientes, pares, jefe y una autoevaluación.</p>
<p>En esta sección deberás contestar tus evaluaciones asignadas:</p>
</div></div>
									<div class="col-md-4"><div> <img src="assets/img/360.png" class="img-responsive" alt="360"></div></div>
								</div>
                                    
									
                                    
                    <table class="table table datatable-basic table-bordered">
                    <thead> 
                    <tr class="bg-primary-600"> 
                      <th>Tipo de Relación</th>
                      <th>Nombre del Evaluado</th>
                      <th>Puesto</th>
                      <th>Estatus</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
					<?php if($evaluaciones >= 1) { ?>
                          <?php if ($totalRows_tipo1 > 0) { ?>
                          <tr>
                            <td><i class="btn text-danger btn-icon icon-arrow-right5"></i>Autoevaluación </td>
                            <td><?php echo $row_tipo1['emp_nombre']. " ".$row_tipo1['emp_paterno']." ".$row_tipo1['emp_materno']; ?></td>
                            <td><?php echo $row_tipo1['denominacion']; ?></td>
                            <td><?php if ($row_tipo1['IDestatus'] == 1){ echo "<span class='label label-success'>Evaluado</span>";} else { echo "<span class='label label-danger'>Sin evaluación</span>";}?></td>
                            <td><a class="btn btn-xs btn-primary" href="f_comp_evaluar.php?IDevaluacion=<?php echo $row_tipo1['IDevaluacion'] ?>">Evaluar</a></td>
                          </tr>
                          <?php  }  ?>
                          <?php if ($totalRows_tipo2 > 0) { ?>
                          <tr>
                            <td><i class="btn text-danger btn-icon icon-arrow-up5"></i> Jefe</td>
                            <td><?php echo $row_tipo2['emp_nombre']. " ".$row_tipo2['emp_paterno']." ".$row_tipo2['emp_materno']; ?></td>
                            <td><?php echo $row_tipo2['denominacion']; ?></td>
                            <td><?php if ($row_tipo2['IDestatus'] == 1){ echo "<span class='label label-success'>Evaluado</span>";} else { echo "<span class='label label-danger'>Sin evaluación</span>";}?></td>
                            <td><a class="btn btn-xs btn-primary" href="f_comp_evaluar.php?IDevaluacion=<?php echo $row_tipo2['IDevaluacion'] ?>">Evaluar</a></td>
                          </tr>
                          <?php  }  ?>
                          <?php if ($totalRows_tipo3 > 0) { do { ?>
                          <tr>
                            <td><i class="btn text-danger btn-icon icon-arrow-down5"></i> Pares</td>
                            <td><?php echo $row_tipo3['emp_nombre']. " ".$row_tipo3['emp_paterno']." ".$row_tipo3['emp_materno']; ?></td>
                            <td><?php echo $row_tipo3['denominacion']; ?></td>
                            <td><?php if ($row_tipo3['IDestatus'] == 1){ echo "<span class='label label-success'>Evaluado</span>";} else { echo "<span class='label label-danger'>Sin evaluación</span>";}?></td>
                            <td><a class="btn btn-xs btn-primary" href="f_comp_evaluar.php?IDevaluacion=<?php echo $row_tipo3['IDevaluacion'] ?>">Evaluar</a></td>
                          </tr>
                          <?php  } while ($row_tipo3 = mysql_fetch_assoc($tipo3)); } ?>
                          <?php if ($totalRows_tipo4 > 0) { do { ?>
                          <tr>
                            <td><i class="btn text-danger btn-icon icon-arrow-left5"></i> Colaboradores</td>
                            <td><?php echo $row_tipo4['emp_nombre']. " ".$row_tipo4['emp_paterno']." ".$row_tipo4['emp_materno']; ?></td>
                            <td><?php echo $row_tipo4['denominacion']; ?></td>
                            <td><?php if ($row_tipo4['IDestatus'] == 1){ echo "<span class='label label-success'>Evaluado</span>";} else { echo "<span class='label label-danger'>Sin evaluación</span>";}?></td>
                            <td><a class="btn btn-xs btn-primary" href="f_comp_evaluar.php?IDevaluacion=<?php echo $row_tipo4['IDevaluacion'] ?>">Evaluar</a></td>
                          </tr>
                          <?php  } while ($row_tipo4 = mysql_fetch_assoc($tipo4)); } ?>
                          <?php if ($totalRows_tipo5 > 0) { do { ?>
                          <tr>
                            <td><i class="btn text-danger btn-icon icon-menu-close"></i> Cliente Interno</td>
                            <td><?php echo $row_tipo5['emp_nombre']. " ".$row_tipo5['emp_paterno']." ".$row_tipo5['emp_materno']; ?></td>
                            <td><?php echo $row_tipo5['denominacion']; ?></td>
                            <td><?php if ($row_tipo5['IDestatus'] == 1){ echo "<span class='label label-success'>Evaluado</span>";} else { echo "<span class='label label-danger'>Sin evaluación</span>";}?></td>
                            <td><a class="btn btn-xs btn-primary" href="f_comp_evaluar.php?IDevaluacion=<?php echo $row_tipo5['IDevaluacion'] ?>">Evaluar</a> </td>
                          </tr>
                          <?php  } while ($row_tipo5 = mysql_fetch_assoc($tipo5)); } ?>
                     </tbody>
					<?php } else { ?>
						<tr>
                            <td colspan="5">Estimado Usuario, aún no cuentas con evaluaciones asignadas. Te mantendremos informado.</td>
                          </tr>	
                    <?php }  ?>
					</table>

                                    
                                    
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


								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Grupo de Evaluación</span>
									</div>

									<div class="category-content">


								<form method="POST" action="f_comp.php">
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
														
                                             <select class="form-control" name="IDgrupo">
											<?php if ($totalRows_grupos == 0) { echo "<option value='99' selected='selected'>No aplica</option>";} else { ?>											
											
											<?php do { ?>
                                               <option value="<?php echo $row_grupos['IDgrupo']?>"<?php if (!(strcmp($row_grupos['IDgrupo'], $el_grupo))) {echo "selected=\"selected\"";} ?>><?php echo $row_grupos['grupo']?></option>
                                               <?php
											  } while ($row_grupos = mysql_fetch_assoc($grupos));
											  $rows = mysql_num_rows($grupos);
											  if($rows > 0) {
												  mysql_data_seek($grupos, 0);
												  $row_grupos = mysql_fetch_assoc($grupos);
											  } ?>
											<?php } ?>											
											  </select>
											  
											</div>
										</div>
									</div>

											  
											<button type="submit" class="btn btn-success">Cambiar Grupo<i class="icon-arrow-right14 position-right"></i></button>									
								</form>

									</div>
								</div>
								<!-- /upcoming courses -->

								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Datos del Evaluado</span>
									</div>

									<div class="category-content">

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Nombre:</label>
											<div><?php echo $row_usuario['emp_nombre']." ".$row_usuario['emp_paterno']." ".$row_usuario['emp_materno']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<div><?php echo $row_usuario['denominacion']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Sucursal:</label>
											<div><?php echo $row_matriz['matriz']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $row_area['area']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Grupo de Evaluación:</label>
											<div><?php echo $row_grupo_act['grupo']; ?></div>
										</div>


										</div>								
                                    </div>
								<!-- /upcoming courses -->


								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Contacto</span>
									</div>

									<div class="category-content">
										<ul class="media-list">
											Marco Antonio Hernández</br>
                                            Red: <strong>1218</strong></br>
                                            Correo: <strong>mahernandez@sahuayo.mx</strong>	 
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


</body>
</html>