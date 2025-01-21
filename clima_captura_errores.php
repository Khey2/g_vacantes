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
$la_matriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_clima = "SELECT sed_clima.IDempleado, sed_clima.manual, sed_clima.emp_paterno,  sed_clima.IDpregunta, sed_clima.IDrespuesta, sed_clima.emp_materno, sed_clima.emp_nombre, sed_clima.IDarea, sed_clima.anio, sed_clima.IDperiodo, sed_clima.denominacion, sed_clima.IDmatriz, sed_clima.IDjefe AS jefe_IDempleado, sed_clima.j_emp_paterno AS jefe_emp_paterno, sed_clima.J_emp_materno AS jefe_emp_materno, sed_clima.J_emp_nombre AS jefe_emp_nombre, sed_clima.j_denominacion AS jefe_denominacion, vac_areas.area FROM sed_clima LEFT JOIN vac_areas ON vac_areas.IDarea = sed_clima.IDarea WHERE (sed_clima.IDjefe IS NULL OR sed_clima.IDjefe = 0 OR sed_clima.emp_paterno IS NULL) AND sed_clima.manual is null AND sed_clima.IDpregunta = 2";   
mysql_query("SET NAMES 'utf8'");
$clima = mysql_query($query_clima, $vacantes) or die(mysql_error());
$row_clima = mysql_fetch_assoc($clima);
$totalRows_clima = mysql_num_rows($clima);
$el_usuario = $row_clima['IDempleado'];
$IDperiodo = $row_clima['IDperiodo'];


if (isset($_GET['corregir'])){

$IDempleado = $_POST['IDempleado'];
$IDempleadoJ = $_POST['IDempleadoJ'];

echo "que merda:".$_POST['IDempleadoJ'];
echo "</br>que merda:".$_POST['IDempleado'];

$query_usser = "SELECT * FROM prod_activos WHERE IDempleado = '$IDempleado'";
$usser = mysql_query($query_usser, $vacantes) or die(mysql_error());
$row_usser = mysql_fetch_assoc($usser);

$query_boss = "SELECT * FROM prod_activos WHERE IDempleado = '$IDempleadoJ'";
$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
	
$j_emp_paterno = $row_boss['emp_paterno'];
$j_emp_materno = $row_boss['emp_materno'];
$j_emp_nombre = $row_boss['emp_nombre'];
$j_denominacion = $row_boss['denominacion'];
$j_IDpuesto = $row_boss['IDpuesto'];
$j_IDarea = $row_boss['IDarea'];

$emp_paterno = $row_usser['emp_paterno'];
$emp_materno = $row_usser['emp_materno'];
$emp_nombre = $row_usser['emp_nombre'];
$denominacion = $row_usser['denominacion'];
$IDpuesto = $row_usser['IDpuesto'];
$IDarea = $row_usser['IDarea'];


$updateSQL2 = "UPDATE sed_clima SET IDjefe = '$IDempleadoJ', j_emp_paterno = '$j_emp_paterno', j_emp_materno = '$j_emp_materno', j_emp_nombre = '$j_emp_nombre', j_denominacion = '$j_denominacion', j_IDpuesto = '$j_IDpuesto', j_IDarea = '$j_IDarea' WHERE IDempleado = '$IDempleado' AND IDperiodo = '$IDperiodo'"; 
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL2, $vacantes) or die(mysql_error());


$updateSQL = "UPDATE sed_clima SET  emp_paterno = '$emp_paterno', emp_materno = '$emp_materno', emp_nombre = '$emp_nombre', denominacion = '$denominacion', IDpuesto = '$IDpuesto', IDarea = '$IDarea' WHERE IDempleado = '$IDempleado' AND IDperiodo = '$IDperiodo'"; 
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  header("Location: clima_captura_errores.php?info=1");

}



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

//plantilla
mysql_select_db($database_vacantes, $vacantes);
$query_plantilla = "SELECT Count(prod_activos.rfc) as plantilla FROM prod_activos WHERE prod_activos.IDmatriz ='$la_matriz'";
$plantilla = mysql_query($query_plantilla, $vacantes) or die(mysql_error());
$row_plantilla = mysql_fetch_assoc($plantilla);
$totalRows_plantilla = mysql_num_rows($plantilla);

//plantilla
mysql_select_db($database_vacantes, $vacantes);
$query_encuestas = "SELECT DISTINCT sed_clima.IDempleado as encuestas FROM sed_clima  WHERE sed_clima.IDmatriz ='$la_matriz' AND IDperiodo = '$IDperiodo'";
$encuestas = mysql_query($query_encuestas, $vacantes) or die(mysql_error());
$row_encuestas = mysql_fetch_assoc($encuestas);
$totalRows_encuestas = mysql_num_rows($encuestas);

$los_puestos = "87, 145, 146, 147, 148, 149, 150, 120, 250, 252, 95, 96, 176, 253, 254, 121, 154, 177, 97, 98, 203, 221, 211, 202, 209, 255, 220, 207, 227, 232, 218, 219, 222, 204, 225, 214, 217, 233, 256, 215, 234, 272, 241, 257, 205, 224, 262, 223, 261, 258, 208, 231, 216, 99, 100, 101, 102, 122, 10, 123, 36, 103, 124, 37, 125, 180, 181, 126, 11, 12, 13, 182, 201, 127, 128, 129, 51, 130, 131, 183, 184, 265, 264, 266, 267, 191, 213, 192, 17, 270, 56, 58, 193, 198, 235, 237, 238, 239, 240";


// select para Jefe
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE IDpuesto IN ($los_puestos) OR manual IS NOT NULL ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);


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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
							Se ha corregido la captura.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la encuesta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Clima Laboral</h6>
								</div>

								<div class="panel-body">
								<p>Capturas sin Nombre de Jefe.</p>
								
								
                                
                  <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-primary" href="clima_captura_edit.php">Agregar Encuesta<i class="icon-arrow-right14 position-right"></i></a>
                    </div>
					</div>
					<!-- /colored button -->
                                

								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                    <th>Jefe</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_clima > 0 ) { ?>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_clima['IDempleado']; ?></td>
                                      <td><?php if ($row_clima['manual'] != 1)
									  { echo $row_clima['emp_paterno'] . " " . $row_clima['emp_materno'] . " " . $row_clima['emp_nombre'];
									  } else { echo "CAPTURA MANUAL"; } ?></td>
                                      <td><?php echo $row_clima['area']; ?></td>
                                      <td><?php if ($row_clima['manual'] != 1) { echo $row_clima['denominacion']; } else { echo "CAPTURA MANUAL"; } ?></td>
                                      <td><?php echo $row_clima['jefe_emp_paterno']. " " .$row_clima['jefe_emp_materno']. " " .$row_clima['jefe_emp_nombre']; ?></td>
                                      <td>
                                      <button type="button" data-target="#modal_theme_danger<?php echo $row_clima['IDempleado']; ?>" 
                                       data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                                      <button type="button" data-target="#capturar<?php echo $row_clima['IDempleado']; ?>" data-toggle="modal" class="btn btn-primary">Corregir</button>


					<!-- Modal de Captura -->
					<div id="capturar<?php echo $row_clima['IDempleado']; ?>" class="modal fade" tabindex="-3">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-success">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Corrección</h5>
								</div>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="clima_captura_errores.php?corregir=1" > 
                                <fieldset class="content-group">
                                    
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<div class="form-group">
										<label class="control-label col-lg-3"><div class="text-bold content-group">Jefe Inmediato:<span class="text-danger">*</span></div></label>
										<div class="col-lg-9">
										<select name="IDempleadoJ" id="IDempleadoJ" class="bootstrap-select" data-live-search="true" data-width="100%"  required="required">
										<option value="">Selecciona el Jefe Inmediato</option>
                                        <?php  do { ?>
													  <option value="<?php echo $row_jefes['IDempleado']?>"><?php echo $row_jefes['emp_nombre'] . " " . $row_jefes['emp_paterno'] . " " . $row_jefes['emp_materno'] .  " (". $row_jefes['denominacion'] . ")"; ?></option>
													  <?php
													 } while ($row_jefes = mysql_fetch_assoc($jefes));
													   $rows = mysql_num_rows($jefes);
													   if($rows > 0) {
													   mysql_data_seek($jefes, 0);
													   $row_jefes = mysql_fetch_assoc($jefes);
													 } ?>
										</select>										
                                        </div>
									</div>
									<!-- /basic select -->
                                    
									<p>&nbsp;</p>

                                    <div class="modal-footer">
          	                      		<input type="hidden" name="MM_update" value="form1">
          	                      		<input type="hidden" name="IDempleado" value="<?php  echo $row_clima['IDempleado']; ?>">
                                        <input type="submit" class="btn btn-primary" value="Corregir">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									</div>
									<p>&nbsp;</p>
								
                                </fieldset>
                                </form>
                                
                           </div>
                        </div>
                     </div>
                    <!-- //Modal de Captura -->


									  
                                       </td>
                                    </tr>

                   <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_clima['IDempleado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la captura?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="clima_captura_edit.php?IDempleado=<?php echo $row_clima['IDempleado']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
					
					

                                    
                                    <?php } while ($row_clima = mysql_fetch_assoc($clima)); ?>
 							  <?php } else { ?>
<tr>
                                      <td>No se tienen errores de captura.</td>
                                      <td></td>
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