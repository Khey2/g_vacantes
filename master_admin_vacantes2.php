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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

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
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($mis_matrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


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
$query_apoyo = "SELECT * FROM vac_apoyo";
$apoyo = mysql_query($query_apoyo, $vacantes) or die(mysql_error());
$row_apoyo = mysql_fetch_assoc($apoyo);
$totalRows_apoyo = mysql_num_rows($apoyo);

mysql_select_db($database_vacantes, $vacantes);
$query_estato = "SELECT * FROM vac_estatus";
$estato = mysql_query($query_estato, $vacantes) or die(mysql_error());
$row_estato = mysql_fetch_assoc($estato);
$totalRows_estato = mysql_num_rows($estato);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$lmatriz = $row_lmatriz['matriz'];

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 

mysql_select_db($database_vacantes, $vacantes);
$query_vacantes = "SELECT vac_vacante.IDvacante, vac_vacante.fecha_usr4, vac_matriz.matriz, vac_puestos.denominacion, vac_areas.area, vac_vacante.IDmotivo_v,  vac_vacante.IDrequi, vac_puestos.dias, vac_vacante.ajuste_dias, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_estatus.estatus, vac_tipo_vacante.tipo_vacante, vac_sucursal.sucursal, vac_apoyo.apoyo FROM vac_vacante LEFT JOIN vac_estatus ON vac_vacante.IDestatus = vac_estatus.IDestatus LEFT JOIN vac_matriz ON vac_vacante.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_tipo_vacante ON vac_vacante.IDtipo_vacante = vac_tipo_vacante.IDtipo_vacante LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz AND vac_vacante.IDsucursal = vac_sucursal.IDsucursal LEFT JOIN vac_apoyo ON vac_vacante.IDapoyo = vac_apoyo.IDapoyo";
mysql_query("SET NAMES 'utf8'");
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
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
</head>

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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Vacantes</h5>
						</div>

					<div class="panel-body">
			     </div>

					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr>
							    <th>Matriz</th>
							    <th>Sucursal</th>
							    <th>Denominación</th>
							    <th>Área</th>
							    <th>Temporal</th>
							    <th>Fecha</th>
							    <th>Días</th>
							    <th>Estatus</th>
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
							<td><?php echo $row_vacantes['matriz']; ?></td>
							<td><?php echo $row_vacantes['sucursal']; ?></td>
							<td><?php echo $row_vacantes['denominacion']; ?></td>
							<td><?php echo $row_vacantes['area']; ?></td>
							<td><?php if ($row_vacantes['IDmotivo_v'] == 3) {echo "Si";} else {echo "No";} ?></td>
							<td><?php if ($row_vacantes['fecha_requi'] != 0) { echo date( 'd/m/Y', strtotime($row_vacantes['fecha_requi'])); }?></td>
                            <td><?php if ($row_vacantes['IDrequi'] == 1) {echo "<div class='label label-default'>N/A</div>";} else {

									   $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));
							           if (($row_vacantes['fecha_ocupacion'] != 0) && ($row_vacantes['IDestatus'] != 1)) { $end_date2 =  date('Y/m/d', strtotime($row_vacantes['fecha_ocupacion'])); 
									   $resultado = getWorkingDays($startdate, $end_date2, $holidays);} else {
                                       $resultado = getWorkingDays($startdate, $end_date, $holidays);}
									   $ajuste_dias = $row_vacantes['ajuste_dias'];
                                           if ($ajuste_dias != 0) { $resultado = $resultado - $ajuste_dias; } 
                                           if ($resultado <= 0) { echo 0; } else { echo round($resultado); } }?>
                                    </td>
							<td><?php switch ($row_vacantes['IDestatus']) {
                             case 1: echo "EN PROCESO"; break;
                             case 2: echo "CUBIERTA"; break;
                             case 3: echo "SUSPENDIDA"; break;
                           } ?></td>
						    </tr>
                        <?php } ?>
					    <?php } while ($row_vacantes = mysql_fetch_assoc($vacantes)); ?>
					    </tbody>
				    </table>
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