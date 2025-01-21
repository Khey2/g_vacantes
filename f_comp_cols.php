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


// pares
mysql_select_db($database_vacantes, $vacantes);
$query_tipo_3 = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM sed_competencias_resultados INNER JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado_evaluador = '$el_usuario' AND sed_competencias_resultados.IDtipo = 3 AND sed_competencias_resultados.anio = $anio AND sed_competencias_resultados.IDgrupo = $el_grupo";
$tipo_3 = mysql_query($query_tipo_3, $vacantes) or die(mysql_error());
$row_tipo_3 = mysql_fetch_assoc($tipo_3);
$totalRows_tipo_3 = mysql_num_rows($tipo_3);


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];


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
									<h6 class="panel-title text-semibold">Mis colaboradores</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
									
                                    <p>Tu participación como  líder en el desarrollo de tu equipo es muy importante, por lo que te recomiendo  que consideres los siguientes puntos:</p>
                                    <ol start="1" type="1">
                                      <li>La retroalimentación que genera el reporte está pensada para que sea confidencial e individual.</li>
                                      <li>Para cada evaluado, el sistema le mostrará algunas recomendaciones de libros, películas, blogs, entre otros, para fortalecer sus áreas de oportunidad.</li>
                                      <li>Como líder del área, debes desarrollar un plan de acción para mejorar las áreas de oportunidad detectadas a cada colaborador. Para el desarrollo del plan, cuentas con:</li>
                                      <ol start="1" type="a">
                                        <li>Los contenidos de capacitación existentes en la universidad sahuayo (contacta al área de capacitación para más detalles.)</li>
                                        <li>El resultado de la evaluación del desempeño de cada colaborador (disponible en el mismo sistema).</li>
                                        <li>Resultados de Clima Laboral y Liderazgo (disponible en el mismo sistema).</li>
                                      </ol>
                                    </ol>
                                    <p>&nbsp;</p>
                                    
                                    
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
					<?php if($totalRows_tipo_3 > 0) { ?>
                          <?php do {
							  
$el_usuario = $row_tipo_3['IDempleado'];							  
							  
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Avg(sed_competencias_resultados.comp8) AS Rcomp8,  Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 1 AND sed_competencias_resultados.anio = $anio  AND sed_competencias_resultados.IDgrupo = $el_grupo GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo1 = mysql_query($query_Total_tipo1, $vacantes) or die(mysql_error());
$row_Total_tipo1 = mysql_fetch_assoc($Total_tipo1);
$totalRows_Total_tipo1 = mysql_num_rows($Total_tipo1);

$query_Total_tipo2 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Avg(sed_competencias_resultados.comp8) AS Rcomp8,  Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 2 AND sed_competencias_resultados.anio = $anio  AND sed_competencias_resultados.IDgrupo = $el_grupo GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo2 = mysql_query($query_Total_tipo2, $vacantes) or die(mysql_error());
$row_Total_tipo2 = mysql_fetch_assoc($Total_tipo2);
$totalRows_Total_tipo2 = mysql_num_rows($Total_tipo2);

$query_Total_tipo3 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Avg(sed_competencias_resultados.comp8) AS Rcomp8,  Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 3 AND sed_competencias_resultados.anio = $anio  AND sed_competencias_resultados.IDgrupo = $el_grupo GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo3 = mysql_query($query_Total_tipo3, $vacantes) or die(mysql_error());
$row_Total_tipo3 = mysql_fetch_assoc($Total_tipo3);
$totalRows_Total_tipo3 = mysql_num_rows($Total_tipo3);

$query_Total_tipo4 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Avg(sed_competencias_resultados.comp8) AS Rcomp8, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 4 AND sed_competencias_resultados.anio = $anio  AND sed_competencias_resultados.IDgrupo = $el_grupo GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo4 = mysql_query($query_Total_tipo4, $vacantes) or die(mysql_error());
$row_Total_tipo4 = mysql_fetch_assoc($Total_tipo4);
$totalRows_Total_tipo4 = mysql_num_rows($Total_tipo4);

$query_Total_tipo5 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Avg(sed_competencias_resultados.comp8) AS Rcomp8, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 5 AND sed_competencias_resultados.anio = $anio  AND sed_competencias_resultados.IDgrupo = $el_grupo GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo5 = mysql_query($query_Total_tipo5, $vacantes) or die(mysql_error());
$row_Total_tipo5 = mysql_fetch_assoc($Total_tipo5);
$totalRows_Total_tipo5 = mysql_num_rows($Total_tipo5);

$query_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario   AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 1 AND sed_competencias_resultados.anio = $anio";
$tipo1 = mysql_query($query_tipo1, $vacantes) or die(mysql_error());
$row_tipo1 = mysql_fetch_assoc($tipo1);
$totalRows_tipo1 = mysql_num_rows($tipo1);

$query_tipo2 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario  AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 2 AND sed_competencias_resultados.anio = $anio";
$tipo2 = mysql_query($query_tipo2, $vacantes) or die(mysql_error());
$row_tipo2 = mysql_fetch_assoc($tipo2);
$totalRows_tipo2 = mysql_num_rows($tipo2);

$query_tipo3 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario  AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 3 AND sed_competencias_resultados.anio = $anio";
$tipo3 = mysql_query($query_tipo3, $vacantes) or die(mysql_error());
$row_tipo3 = mysql_fetch_assoc($tipo3);
$totalRows_tipo3 = mysql_num_rows($tipo3);

$query_tipo4 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario  AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 4 AND sed_competencias_resultados.anio = $anio";
$tipo4 = mysql_query($query_tipo4, $vacantes) or die(mysql_error());
$row_tipo4 = mysql_fetch_assoc($tipo4);
$totalRows_tipo4 = mysql_num_rows($tipo4);

$query_tipo5 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario  AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 5 AND sed_competencias_resultados.anio = $anio";
$tipo5 = mysql_query($query_tipo5, $vacantes) or die(mysql_error());
$row_tipo5 = mysql_fetch_assoc($tipo5);
$totalRows_tipo5 = mysql_num_rows($tipo5);

$query_tipo6 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario  AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 6 AND sed_competencias_resultados.anio = $anio";
$tipo6 = mysql_query($query_tipo6, $vacantes) or die(mysql_error());
$row_tipo6 = mysql_fetch_assoc($tipo6);
$totalRows_tipo6 = mysql_num_rows($tipo6);

$query_tipo7 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario  AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 7 AND sed_competencias_resultados.anio = $anio";
$tipo7 = mysql_query($query_tipo7, $vacantes) or die(mysql_error());
$row_tipo7 = mysql_fetch_assoc($tipo7);
$totalRows_tipo7 = mysql_num_rows($tipo7);

$query_tipo8 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario  AND sed_competencias_resultados.IDgrupo = $el_grupo AND sed_competencias_resultados.IDtipo = 8 AND sed_competencias_resultados.anio = $anio";
$tipo8 = mysql_query($query_tipo8, $vacantes) or die(mysql_error());
$row_tipo8 = mysql_fetch_assoc($tipo8);
$totalRows_tipo8 = mysql_num_rows($tipo8);
							  
$completo = 0;
if ($row_Total_tipo1['Resultados'] == $totalRows_tipo1) { $completo = $completo + 1; }
if ($row_Total_tipo2['Resultados'] == $totalRows_tipo2 or $row_Total_tipo2['Resultados'] > 2) { $completo = $completo + 1; }
if ($row_Total_tipo3['Resultados'] == $totalRows_tipo3 or $row_Total_tipo3['Resultados'] > 2) { $completo = $completo + 1; }
if ($row_Total_tipo4['Resultados'] == $totalRows_tipo4 or $row_Total_tipo4['Resultados'] > 2) { $completo = $completo + 1; }
if ($row_Total_tipo5['Resultados'] == $totalRows_tipo5 or $row_Total_tipo5['Resultados'] > 2) { $completo = $completo + 1; }
?>
                          <tr>
                            <td><i class="btn text-danger btn-icon icon-arrow-down5"></i> Colaborador</td>
                            <td><?php echo $row_tipo_3['emp_nombre']. " ".$row_tipo_3['emp_paterno']." ".$row_tipo_3['emp_materno']; ?></td>
                            <td><?php echo $row_tipo_3['denominacion']; ?></td>
                            <td><?php if ($completo == 5){ echo "<span class='label label-success'>Completo</span>";} else { echo "<span class='label label-danger'>Incompleto</span>";}?></td>
                            <td><?php if ($completo == 5){ ?>
							<button type="button" class="btn btn-info" onClick="window.location.href='f_comp_cols_ver.php?IDevaluacion=<?php echo $row_tipo_3['IDevaluacion']; ?>&IDempleado=<?php echo $row_tipo_3['IDempleado']; ?>'">Ver Reporte</button>
							<?php } else { ?> En proceso
							<?php }  ?>
                            </td>
                          </tr>
                          <?php  } while ($row_tipo_3 = mysql_fetch_assoc($tipo_3)); ?>
                     </tbody>
					<?php } else { ?>
						<tr>
                            <td colspan="5">Estimado Usuario, no tienes colaboradores con resultados de esta evaluación.</td>
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


								<form method="POST" action="f_comp_cols.php">
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
														
                                             <select class="form-control" name="IDgrupo">
											<?php do { ?>
                                               <option value="<?php echo $row_grupos['IDgrupo']?>"<?php if (!(strcmp($row_grupos['IDgrupo'], $el_grupo))) {echo "selected=\"selected\"";} ?>><?php echo $row_grupos['grupo']?></option>
                                               <?php
											  } while ($row_grupos = mysql_fetch_assoc($grupos));
											  $rows = mysql_num_rows($grupos);
											  if($rows > 0) {
												  mysql_data_seek($grupos, 0);
												  $row_grupos = mysql_fetch_assoc($grupos);
											  } ?>
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