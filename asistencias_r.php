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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
 $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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
$desfase = $row_variables['dias_desfase'];
$anio = $row_variables['anio'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
 $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$IDusuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = $IDmatriz";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

$query_matriz_R = "SELECT * FROM vac_matriz WHERE IDmatriz IN($IDmatrizes) AND IDmatriz NOT IN(7, 27,31,5) ORDER BY matriz DESC";
$matriz_R = mysql_query($query_matriz_R, $vacantes) or die(mysql_error());
$row_matriz_R = mysql_fetch_assoc($matriz_R);
$totalRows_matriz_R = mysql_num_rows($matriz_R);


if (isset($_POST['fecha_filtro'])) {
	$el_anio = substr( $_POST['fecha_filtro'], 6, 4 );
	$el_mes = substr( $_POST['fecha_filtro'], 3, 2 );
	$IDfecha = substr( $_POST['fecha_filtro'], 0, 2 );
	$_SESSION['fecha_filtro'] = $IDfecha."/".$el_mes."/".$el_anio;
} else {
	$el_anio = date("Y");
	$el_mes = date("m");
	$IDfecha = date("d");
	$_SESSION['fecha_filtro'] = $IDfecha."/".$el_mes."/".$el_anio;
}
	
 switch ($el_mes) {
  case 1: $elmes = "Enero";   break;   
  case 2: $elmes = "Febrero";  break;  
  case 3: $elmes = "Marzo";   break;  
  case 4: $elmes = "Abril";   break;  
  case 5: $elmes = "Mayo";    break;  
  case 6: $elmes = "Junio";   break;  
  case 7: $elmes = "Julio";   break;  
  case 8: $elmes = "Agosto";   break;  
  case 9: $elmes = "Septiembre"; break;  
  case 10: $elmes = "Octubre";  break;  
  case 11: $elmes = "Noviembre"; break;  
  case 12: $elmes = "Diciembre"; break;  
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
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
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
<body>
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


					<!-- Sorting data -->
					<div class="panel panel-flat">

						<div class="panel-body">
							<p class="content-group"><b>Instrucciones:</b><br/>



                <form method="POST" action="asistencias_r.php">
					<table class="table">
							<tr>
                            <td>
                            <div class="input-group">
								<span class="input-group-addon"><i class="icon-calendar5"></i></span>
								<input type="text" class="form-control  daterange-single" name="fecha_filtro" id="fecha_filtro" value="<?php if (isset($_SESSION['fecha_filtro'])) {echo $_SESSION['fecha_filtro'];} else { echo "";} ?>">
							</div>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>
							<a href="asistencias_reporte_r_dias.php?IDmatriz=<?php echo $IDmatriz?>&anio=<?php echo $el_anio?>&mes=<?php echo $el_mes?>&IDfecha=<?php echo $IDfecha?>" class="btn bg-success">Descargar Día</a> 
							<a href="asistencias_reporte_r_mes.php?IDmatriz=<?php echo $IDmatriz?>&anio=<?php echo $el_anio?>&mes=<?php echo $el_mes?>&IDfecha=<?php echo $IDfecha?>" class="btn bg-warning">Descargar Mes</a> </td>
							</td>
					      </tr>
				    </table>
				</form>


					<table class="table table-condensed datatable-button-html5-columns">
          		<thead>
                  <tr class="bg-primary"> 
                  <th>Acciones</th>
                  <th>Matriz</th>
                  <th>Autorizada</th>
                  <th>Activos</th>
				  <th>Ausencias</th>
				  <th>Validadas</th>
                  <th>FTE Autorizada</th>
                  <th>FTE Activa</th>
                </thead>
                <tbody>
					<?php  do {
						//CAPTURADOS
						$IDmatriz = $row_matriz_R['IDmatriz'];
						$fecha_filtro = $el_anio."-".$el_mes."-".$IDfecha; 
						$query_reporte = "SELECT ind_asistencia.IDmatriz, COUNT( ind_asistencia.IDtipo ) AS Estatus, ind_asistencia.anio, ind_asistencia.mes, ind_asistencia.IDfecha, ind_asistencia.IDtipo, vac_matriz.matriz FROM ind_asistencia left JOIN vac_matriz ON ind_asistencia.IDmatriz = vac_matriz.IDmatriz left JOIN vac_puestos ON ind_asistencia.IDpuesto = vac_puestos.IDpuesto left JOIN vac_areas ON ind_asistencia.IDarea = vac_areas.IDarea WHERE ind_asistencia.IDmatriz = $IDmatriz AND ind_asistencia.mes = $el_mes AND ind_asistencia.anio = $el_anio AND ind_asistencia.IDfecha = $IDfecha GROUP BY ind_asistencia.IDmatriz";
						$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
						$row_reporte = mysql_fetch_assoc($reporte);
						$totalRows_reporte = mysql_num_rows($reporte);

						//VALIDADOS
						$query_reporte2 = "SELECT ind_asistencia.IDmatriz, COUNT( ind_asistencia.IDtipov ) AS Estatus, ind_asistencia.anio, ind_asistencia.mes, ind_asistencia.IDfecha, ind_asistencia.IDtipo, vac_matriz.matriz FROM ind_asistencia left JOIN vac_matriz ON ind_asistencia.IDmatriz = vac_matriz.IDmatriz left JOIN vac_puestos ON ind_asistencia.IDpuesto = vac_puestos.IDpuesto left JOIN vac_areas ON ind_asistencia.IDarea = vac_areas.IDarea WHERE ind_asistencia.IDmatriz = $IDmatriz AND ind_asistencia.mes = $el_mes AND ind_asistencia.anio = $el_anio AND ind_asistencia.IDfecha = $IDfecha GROUP BY ind_asistencia.IDmatriz";
						$reporte2 = mysql_query($query_reporte2, $vacantes) or die(mysql_error());
						$row_reporte2 = mysql_fetch_assoc($reporte2);
						$totalRows_reporte2 = mysql_num_rows($reporte2);

						//AUTORIZADOS
						mysql_select_db($database_vacantes, $vacantes);
						$query_autorizados = "SELECT Count(prod_plantilla.IDplantilla) AS Autorizada, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_puestos.clave_puesto, vac_areas.IDarea, vac_areas.area, vac_matriz.matriz FROM prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_plantilla.IDmatriz = vac_matriz.IDmatriz WHERE prod_plantilla.IDmatriz IN ($IDmatriz) AND vac_puestos.IDarea IN (1,2,3,4) AND prod_plantilla.IDestatus = 1 AND (DATE(fecha_inicio) <= '$fecha_filtro') AND ( DATE(fecha_fin) > '$fecha_filtro' OR DATE(fecha_fin) = '0000-00-00' OR DATE(fecha_fin) IS NULL) AND ( DATE(fecha_congelada) > '$fecha_filtro' OR DATE(fecha_congelada) = '0000-00-00' OR DATE(fecha_congelada) IS NULL) GROUP BY prod_plantilla.IDmatriz";
						$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
						$row_autorizados = mysql_fetch_assoc($autorizados);
						$totalRows_autorizados = mysql_num_rows($autorizados);
						
						//ACTIVOS
						mysql_select_db($database_vacantes, $vacantes);
						$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDarea IN (1,2,3,4)";
						$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
						$row_activos = mysql_fetch_assoc($activos);
						$totalRows_activos = mysql_num_rows($activos);

						?>
                  <tr>
                   <td>
				   <?php if ($totalRows_reporte > 0) { ?>
				   <a href="asistencias_edit_r.php?IDmatriz=<?php echo $IDmatriz?>&anio=<?php echo $el_anio?>&mes=<?php echo $el_mes?>&IDfecha=<?php echo $IDfecha?>" class="btn bg-info"><i class="icon icon-eye"></i></a>
				   <a href="asistencias_reporte_r_dia.php?IDmatriz=<?php echo $IDmatriz?>&anio=<?php echo $el_anio?>&mes=<?php echo $el_mes?>&IDfecha=<?php echo $IDfecha?>" class="btn bg-success"><i class="icon icon-file-download2"></i></a>
				   	<?php } ?>
					</td>
                   <td><?php echo $row_matriz_R['matriz']; ?></td>
                   <td><?php echo $row_autorizados['Autorizada']; ?></td>
                   <td><?php echo $row_activos['TActivos']; ?></td>
                   <td><?php if($row_reporte['Estatus'] > 0) { echo $row_reporte['Estatus'];} else { echo 0;}; ?></td>
                   <td><?php if($row_reporte2['Estatus'] > 0) { echo $row_reporte2['Estatus'];} else { echo 0;}; ?></td>
                   <td><?php echo round(((($row_activos['TActivos'] - $row_reporte2['Estatus']) / $row_autorizados['Autorizada'])*100),0)  ?>%</td>
                   <td><?php echo round(((($row_activos['TActivos'] - $row_reporte2['Estatus']) / $row_activos['TActivos'])*100),0)  ?>%</td>
				   <?php }  while ($row_matriz_R = mysql_fetch_assoc($matriz_R)); ?>
                  </tr>
                  
                </tbody>
            </table>		
		<input type="hidden" name="MM_insert" value="form3">
							
						</div>
					</div>
					<!-- /sorting data -->



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
