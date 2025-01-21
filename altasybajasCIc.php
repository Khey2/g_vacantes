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

if (isset($_POST['la_matriz'])) {	foreach ($_POST['la_matriz'] as $matris)
	{	$_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);}	} 
else if (!isset($_SESSION['la_matriz'])) { $_SESSION['la_matriz'] = "7";}
$la_matriz = $_SESSION['la_matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.rfc, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.descripcion_nomina, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDarea, vac_areas.area, vac_matriz.matriz FROM prod_activos INNER JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE prod_activos.IDmatriz IN ($la_matriz) ORDER BY prod_activos.IDpuesto ASC";
mysql_query("SET NAMES 'utf8'");
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

if(isset($_POST['el_tipo']) && ($_POST['el_tipo']  > 0)) {
$_SESSION['el_tipo'] = $_POST['el_tipo']; } else { $_SESSION['el_tipo'] = 1;}

if(isset($_POST['el_estatus']) && ($_POST['el_estatus']  > 0)) {
$_SESSION['el_estatus'] = $_POST['el_estatus']; } else { $_SESSION['el_estatus'] = 1;}

$el_tipo = $_SESSION['el_tipo'];
$el_estatus = 1;


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


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$IDempleado = $_POST['IDempleado'];
$el_correo = $_POST['correo'];

mysql_select_db($database_vacantes, $vacantes);
$query_consultar = "SELECT * FROM prod_activosj WHERE IDempleado = '$IDempleado'";
$consultar = mysql_query($query_consultar, $vacantes) or die(mysql_error());
$row_consultar = mysql_fetch_assoc($consultar);
$totalRows_consultar = mysql_num_rows($consultar);

if ($totalRows_consultar == 0){

	$updateSQL = "INSERT INTO prod_activosj (IDempleado, correo) VALUES ($IDempleado, '$el_correo')";
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

	$updateGoTo = "altasybajasCIc.php?info=1";
	if (isset($_SERVER['QUERY_STRING'])) {
	$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
	$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));


} else {


	$updateSQL = sprintf("UPDATE prod_activosj SET correo=%s WHERE IDempleado=%s",
						GetSQLValueString($_POST['correo'], "text"),
						GetSQLValueString($_POST['IDempleado'], "int"));

	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

	$updateGoTo = "altasybajasCIc.php?info=1";
	if (isset($_SERVER['QUERY_STRING'])) {
	$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
	$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));
} }


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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect4.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
	<!-- /theme JS files -->
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
					    <div class="alert bg-success alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el correo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		 <!-- Basic alert -->
						 <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el correo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Plantilla Activa</h6>
								</div>


								<div class="panel-body">
								<p>A continuación se muestra la plantilla activa.</br>
                                Seleccione las sucursales que requiera consultar en el filtro.</p>


 				<form method="POST" action="altasybajasCIc.php">
                	<table class="table">
						<tbody>							  
							<tr>
							<td>
                     <div class="col-lg-12">
                             <select class="multiselect" multiple="multiple" name="la_matriz[]">
                            <?php do { ?>
                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                               <?php
                              } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                              $rows = mysql_num_rows($lmatriz);
                              if($rows > 0) {
                                  mysql_data_seek($lmatriz, 0);
                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
                              } ?> 
                              </select>
                      </div>
                            </td>
							<td>
                          <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
							<td>

                             </tr>
					    </tbody>
				    </table>
				</form>




								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Matriz</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                    <th>Correo</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_autorizados['IDempleado']; ?></td>
                                      <td><?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?></td>
                                      <td><?php echo $row_autorizados['matriz']; ?></td>
                                      <td><?php echo $row_autorizados['area']; ?></td>


									    <?php
										mysql_select_db($database_vacantes, $vacantes);
										$query_correo = "SELECT correo FROM prod_activosj WHERE IDempleado = ".$row_autorizados['IDempleado'];
										$correo = mysql_query($query_correo, $vacantes) or die(mysql_error());
										$row_correo = mysql_fetch_assoc($correo);
										$totalRows_correo = mysql_num_rows($correo);
										?>


                                      <td><?php echo $row_autorizados['denominacion']; ?></td>
                                      <td><?php if ($row_correo['correo'] != '') { echo $row_correo['correo'];} else { echo "-";} ?></td>
									  <td>
										<?php if ($row_correo['correo'] == '') { ?>
										<button type="button" data-target="#modal_theme_danger<?php echo $row_autorizados['IDempleado']; ?>"  data-toggle="modal" class="btn btn-primary btn-xsm">Agregar </button>
										<?php } else {  ?>
										<button type="button" data-target="#modal_theme_danger<?php echo $row_autorizados['IDempleado']; ?>"  data-toggle="modal" class="btn btn-info btn-xsm">Actualizar</button>
										<?php }  ?>
										<button type="button" data-target="#modal_theme_danger2<?php echo $row_autorizados['IDempleado']; ?>"  data-toggle="modal" class="btn btn-danger btn-xsm">Borrar</button>
									</td>
                                    </tr>

                  <!-- danger modal -->
				  <div id="modal_theme_danger<?php echo $row_autorizados['IDempleado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualización de Correo</h6>
								</div>
								<form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" class="form-horizontal form-validate-jquery">
								<div class="modal-body">

								<p><b>Empleado: </b><?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?> (<?php echo $row_autorizados['IDempleado']; ?>)</p>
								<p><b>Puesto: </b><?php echo $row_autorizados['denominacion']; ?></p>
								<p><b>Matriz: </b><?php echo $row_autorizados['matriz']; ?></p>
								<p><b>Area: </b><?php echo $row_autorizados['area']; ?></p>

								<fieldset class="content-group"><b>Correo:</b>				
								<input type="email" class="form-control" required="required" name="correo" id="correo" value="<?php echo $row_correo['correo']; ?>">
								</fieldset>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<input type="submit" class="btn btn-success" value="Guardar">
									<input type="hidden" name="IDempleado" value="<?php echo $row_autorizados['IDempleado']; ?>" />
									<input type="hidden" name="MM_update" value="form1">
								</div>
								</form>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


					<!-- danger modal -->
					<div id="modal_theme_danger2<?php echo $row_autorizados['IDempleado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Borrar Correo</h6>
								</div>
								<div class="modal-body">

								<p>Estas seguro de que quieres borrar el correo de </b><?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?> </p>



							</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<a href="altasybajasCIcbaja.php?IDempleado=<?php echo $row_autorizados['IDempleado']; ?>&borrar=1" class="btn btn-danger" >Si Borrar</a>
								</div>
								</form>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

					
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados)); ?>
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