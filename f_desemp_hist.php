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
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }


$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$el_usuario = $row_usuario['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_evaluado = "SELECT
prod_activos.emp_paterno,
prod_activos.emp_materno,
prod_activos.emp_nombre,
prod_activos.denominacion,
prod_activos.fecha_alta,
prod_activos.IDllave,
vac_areas.area,
vac_matriz.matriz,
sed_individuales_resultados.IDresultado,
sed_individuales_resultados.IDempleado,
sed_individuales_resultados.IDperiodo,
sed_individuales_resultados.resultado,
sed_individuales_resultados.estatus,
sed_individuales_resultados.especial,
sed_individuales_resultados.fecha_cierre,
sed_individuales_resultados.IDllave,
sed_individuales_resultados.IDllaveJ,
sed_individuales_resultados.metas_capturadas,
sed_periodos_sed.IDperiodo,
sed_periodos_sed.periodo,
sed_periodos_sed.visible,
sed_periodos_sed.estatus
FROM
prod_activos
LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea
LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz
LEFT JOIN sed_individuales_resultados ON sed_individuales_resultados.IDempleado = prod_activos.IDempleado
RIGHT JOIN sed_periodos_sed ON sed_periodos_sed.IDperiodo = sed_individuales_resultados.IDperiodo
WHERE
prod_activos.IDempleado = '$el_usuario'";
$evaluado = mysql_query($query_evaluado, $vacantes) or die(mysql_error());
$row_evaluado = mysql_fetch_assoc($evaluado);
$totalRows_evaluado = mysql_num_rows($evaluado);

//datos del evaluado
$_nombre = $row_evaluado['emp_nombre'] . " " . $row_evaluado['emp_paterno'] . " " . $row_evaluado['emp_materno'];
$_puesto = $row_evaluado['denominacion'];
$_sucursal = $row_evaluado['matriz'];
$_area = $row_evaluado['area'];
$_fecha_ingreso = $row_evaluado['fecha_alta'];

$query_periodos = "SELECT * FROM sed_periodos_sed ORDER BY sed_periodos_sed.IDperiodo DESC"; 
mysql_query("SET NAMES 'utf8'");
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);

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
    
    <script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
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
						Evaluación del Desempeño
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
									<h6 class="panel-title text-semibold">Mis Evaluaciones</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
									<p>Acontinuación se muestran sus evaluaciones registradas en el Sistema.</p>
									<p>Seleccione alguna acción.</p>
                                    
                                    
                                    
                    <table class="table table datatable-basic table-bordered">
                    <thead> 
                    <tr class="bg-primary-600"> 
                      <th>Periodo</th>
                      <th>Estatus del Periodo</th>
                      <th>Estatus de mi Evaluación</th>
                      <th>Resultado del Periodo</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
                        <?php if($totalRows_evaluado > 0){ ?>
                        <?php do { 
						
						?>
                          <tr>
                            <td><?php echo $row_evaluado['periodo']; ?></td>
                            <td><?php
							      if($row_evaluado['estatus'] == 1) { echo "Periodo de Captura"; } 
							 else if($row_evaluado['estatus'] == 2) { echo "Periodo de Evaluación"; }
							 else if($row_evaluado['estatus'] == 3) { echo "Cerrado"; }
							 else { echo "-";} ?></td>
                            <td><?php 
							      if($row_evaluado['estatus'] == "") { echo "Sin Objetivos capturados"; } 
							 else if($row_evaluado['estatus'] == 1) { echo "Objetivos Capturados"; }
							 else if($row_evaluado['estatus'] == 2) { echo "Con resultados propuestos"; }
							 else if($row_evaluado['estatus'] == 3) { echo "Evaluado"; }
							 else { echo "-";} ?></td>
                            <td><?php 
							      if($row_evaluado['resultado'] > 95) { echo $row_evaluado['resultado']. "% <span class='label label-primary'>Sobresaliente</span>"; } 
							 else if($row_evaluado['resultado'] > 75) { echo $row_evaluado['resultado']. "% <span class='label label-success'>Satisfactorio</span>"; } 
							 else if($row_evaluado['resultado'] > 1 ) { echo $row_evaluado['resultado']. "% <span class='label label-warning'>Deficiente</span>"; } 
							 else { echo "<span class='label label-default'>Sin Evaluación</span>";} ?></td>
                            <td>
						<a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $row_evaluado['IDperiodo']; ?>&IDempleado=<?php echo $row_evaluado['IDempleado']; ?>">Ver Objetivos</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $row_evaluado['IDperiodo']; ?>&IDempleado=<?php echo $row_evaluado['IDempleado']; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
                            </td>
                          </tr>
                          <?php } while ($row_evaluado = mysql_fetch_assoc($evaluado)); ?>
                        <?php } else { ?>
                           <tr>
                            <td>No existen resultados de desempeño.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                        <?php } ?>
                     </tbody>
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
											<label class="control-label no-margin text-semibold">Fecha Ingreso:</label>
											<div><?php echo $_fecha_ingreso; ?></div>
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