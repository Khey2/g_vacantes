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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$matriz_actual = $row_usuario['IDmatriz'];


//las variables de sesion para el filtrado
//if(isset($_POST['la_matriz']) && ($_POST['la_matriz'] > 0)) {
//$_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = "";}


if (isset($_POST['el_area'])) {	foreach ($_POST['el_area'] as $areas)
	{	$_SESSION['el_area'] = implode(", ", $_POST['el_area']);}	}  else { $_SESSION['el_area'] = "";}

if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } else { $_SESSION['el_mes'] = "";}

if(isset($_POST['el_estatus'])) {
$_SESSION['el_estatus'] = 1; }  else {
$_SESSION['el_estatus'] = 0; }

$el_mes = $_SESSION['el_mes'];
//$la_matriz = $_SESSION['la_matriz'];
$el_estatus = $_SESSION['el_estatus'];
$el_area = $_SESSION['el_area'];

$a1 = "";
//$b1	= "";
$c1 = "";

if($el_mes > 0) {
$a1 = " AND month(fecha_requi) = '$el_mes'"; }
//if($la_matriz > 0) {
//$b1 = " AND vac_vacante.IDmatriz = '$la_matriz'"; }
if($el_area > 0) {
$c1 = " AND vac_areas.IDarea IN ($el_area)"; }

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
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$matriz_actual'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 

mysql_select_db($database_vacantes, $vacantes);
$query_vacantes = "SELECT vac_vacante.IDvacante, vac_matriz.matriz, vac_puestos.denominacion, vac_areas.area, vac_puestos.dias, vac_vacante.ajuste_dias, vac_vacante.IDrequi, vac_vacante.IDmatriz, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_estatus.estatus, vac_tipo_vacante.tipo_vacante, vac_sucursal.sucursal, vac_vacante.IDusuario5, vac_vacante.fecha_usr4 FROM vac_vacante LEFT JOIN vac_estatus ON vac_vacante.IDestatus = vac_estatus.IDestatus LEFT JOIN vac_matriz ON vac_vacante.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_tipo_vacante ON vac_vacante.IDtipo_vacante = vac_tipo_vacante.IDtipo_vacante LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz AND vac_vacante.IDsucursal = vac_sucursal.IDsucursal WHERE ((vac_vacante.IDusuario = '$el_usuario' OR vac_vacante.IDusuario2 = '$el_usuario' OR vac_vacante.IDusuario3 = '$el_usuario' OR vac_vacante.IDusuario4 = '$el_usuario' OR vac_vacante.IDusuario5 = '$el_usuario') OR vac_vacante.IDmatriz = '$matriz_actual')" . $a1 . $c1; 
$vacantes = mysql_query($query_vacantes, $vacantes) or die(mysql_error());
$row_vacantes = mysql_fetch_assoc($vacantes);
$totalRows_vacantes = mysql_num_rows($vacantes);

//fechas
require_once('assets/dias.php');

// el mes
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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


	                <!-- Content area -->
				<div class="content">
                
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Vacantes Totales</h5>
						</div>

					<div class="panel-body">
							<p>A continuación se muestran las vacantes totales que tienes asignadas. Selecciona la vacante que requieras editar.</br>
							Utilizar el buscador para filtrar las vacantes por cualquier criterio existente en la tabla.</br>
                            Las plazas asignadas a seguimiento corporativo, tienen un indicador especial: <i class='icon-pushpin'></i></br>
					        Puedes exportar el resultado a Excel, así como seleccionar las columnas a exporatar.</p>
			     </div>
                    
                       <form method="POST" action="vacantes_totales.php">

					<table class="table">
						<tbody>							  
							<tr>
							<td><div class="col-lg-9">
                                             <select class="multiselect" multiple="multiple" name="el_area[]">
											<?php do { ?>
                                               <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_area['area']?></option>
                                               <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) {
												  mysql_data_seek($area, 0);
												  $row_area = mysql_fetch_assoc($area);
											  } ?> </select>
						    </div></td>
							<td>
                            <div class="col-lg-9">
                                             <select name="el_mes" class="form-control">
                                               <option value="" <?php if (!(strcmp("", $el_mes))) {echo "selected=\"selected\"";} ?>>Mes: Todos</option>
                                               <?php do {  ?>
                                               <option value="<?php echo $row_mes['IDmes']?>"<?php if (!(strcmp($row_mes['IDmes'], $el_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_mes['mes']?></option>
                                               <?php
											  } while ($row_mes = mysql_fetch_assoc($mes));
											  $rows = mysql_num_rows($mes);
											  if($rows > 0) {
												  mysql_data_seek($mes, 0);
												  $row_mes = mysql_fetch_assoc($mes);
											  } ?></select>
						    </div>
                            </td>
                            <td>
                             <?php if (!isset($_POST['el_estatus'])) { ?>
							<input name="el_estatus" type="checkbox" class="switch" value="1" data-on-text="Vencida" data-off-text="A&nbsp;tiempo">
                             <?php } else { ?>
                            <input name="el_estatus" type="checkbox" class="switch" value="1" checked data-on-text="Vencida" data-off-text="A&nbsp;tiempo">
                            <?php } ?></td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                             </td>
					      </tr>
					    </tbody>
				    </table>

					</form>

					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Folio</th>
							    <th>Matriz</th>
							    <th>Denominación</th>
							    <th>Área</th>
							    <th>Fecha Requi</th>
							    <th>Días Transcurridos</th>
							    <th>Con Requi</th>
							    <th>Estatus</th>
							    <th class="text-center">Acciones</th>
						    </tr>
					    </thead>
						<tbody>							  

						<?php do { $dias4 = $row_vacantes['fecha_usr4']; ?>
							<?php  $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));
							           if (($row_vacantes['fecha_ocupacion'] != 0) && ($row_vacantes['IDestatus'] != 1)) { $end_date =  date('Y/m/d', strtotime($row_vacantes['fecha_ocupacion']));
									} else { $end_date = date('Y/m/d'); }
                                       $resultado = getWorkingDays($startdate, $end_date, $holidays);
                               
							            // aplicamos ajuste de dias;
									   $ajuste_dias = $row_vacantes['ajuste_dias'];
                                           if ($ajuste_dias != 0) { $resultado = $resultado - $ajuste_dias; }  
			       						  if ($resultado > ($row_vacantes['dias']) || $el_estatus == 0) {?>

							<tr>
							<td><?php echo $row_vacantes['IDvacante']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['matriz'] . " - " . $row_vacantes['sucursal']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['denominacion']; ?>&nbsp; </td>
							<td><?php echo $row_vacantes['area']; ?>&nbsp; </td>
							<td><?php if ($row_vacantes['fecha_requi'] != 0) { echo date( 'd/m/Y', strtotime($row_vacantes['fecha_requi'])); }?></td>
                            <td><?php  $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));
							           if (($row_vacantes['fecha_ocupacion'] != 0) && ($row_vacantes['IDestatus'] != 1)) { $end_date2 =  date('Y/m/d', strtotime($row_vacantes['fecha_ocupacion'])); 
									   $resultado = getWorkingDays($startdate, $end_date2, $holidays);} else {
                                       $resultado = getWorkingDays($startdate, $end_date, $holidays);}
                              ?><?php 
							            // aplicamos ajuste de dias;
									   $ajuste_dias = $row_vacantes['ajuste_dias'];
                                           if ($ajuste_dias != 0) { $resultado = $resultado - $ajuste_dias; } 
                                           if ($resultado <= 0) {  
						            echo "<div class='label label-primary'>0 DÍAS</div>";
									} else if ($resultado < 4) {  
									echo "<div class='label label-success'>". round($resultado) . " DÍAS</div>"; 
									} else if ($resultado < ($row_vacantes['dias'])) {  
									echo "<div class='label label-success'>". round($resultado) . " DÍAS</div>"; 
									} else if ($resultado < ($row_vacantes['dias'] + 4)) {  
									echo "<div class='label label-warning'>". round($resultado) . " DÍAS</div>";
									} else if ($dias4 > 0) {
									echo "<div class='label label-danger'>". round($resultado) . " DÍAS <i class='icon-pushpin'></i></div>"; 									
									} else if ($resultado > ($row_vacantes['dias'] + 1)) {
									echo "<div class='label label-danger'>". round($resultado) . " DÍAS</div>"; }?></td>
							<td><?php if ($row_vacantes['IDrequi'] == 1) {echo "No";} else {echo "Si";} ?>&nbsp; </td>
							<td><?php switch ($row_vacantes['IDestatus']) {
                             case 1: echo "EN PROCESO"; break;
                             case 2: echo "CUBIERTA"; break;
                             case 3: echo "SUSPENDIDA"; break;
                           } ?>&nbsp; </td>
							<td>
                         <button type="button" class="btn btn-primary btn-icon" onClick="window.location.href='vacante_edit_.php?IDvacante=<?php echo $row_vacantes['IDvacante']; ?>'">Editar</button>
                         
                            </td>
						    </tr>
							
												                  <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_vacantes['IDvacante']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="vacante_edit_.php?IDvacante_borrar=<?php echo $row_vacantes['IDvacante']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


                        <?php } ?>
					    <?php } while ($row_vacantes = mysql_fetch_assoc($vacantes)); ?>
					    </tbody>
				    </table>
                    </form>
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
<?php
mysql_free_result($vacantes);
?>