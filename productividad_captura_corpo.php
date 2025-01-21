<?php require_once('Connections/vacantes.php'); ?>
<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
if(isset($_POST['el_anio'])) { $anio = $_POST['el_anio'];} else {$anio = $row_variables['anio'];}
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

$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT DISTINCT Count( prod_activos.IDempleado ) AS Ocupados, prod_activos.IDarea, vac_puestos.denominacion, vac_areas.area, prod_activos.IDpuesto, vac_puestos.prod_captura_tipo FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea WHERE  vac_puestos.prod_captura_tipo = 2 GROUP BY vac_puestos.denominacion"; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_cierre = "SELECT * FROM prod_semana_cierre WHERE IDmatriz = '$la_matriz' AND semana = '$semana' AND anio = '$anio'";
$cierre = mysql_query($query_cierre, $vacantes) or die(mysql_error());
$row_cierre = mysql_fetch_assoc($cierre);
$totalRows_cierre = mysql_num_rows($cierre);

$query_cap_ups = "SELECT * FROM prod_captura WHERE prod_captura.IDpuesto IN (1,38,39,47,54,55,17,56) AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio'";
$cap_ups = mysql_query($query_cap_ups, $vacantes) or die(mysql_error());
$row_cap_ups = mysql_fetch_assoc($cap_ups);
$totalRows_cap_ups = mysql_num_rows($cap_ups);


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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha guardado correctamente la productividad del puesto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la productividad del puesto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Captura semanal de productividad - Corporativo</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
				       <p class="content-group">Selecciona el puesto para mostrar la lista de empleados.</br>
                       La lista de empleados se actualiza los días jueves.</br>   
                       Si existen datos previamente importador, se borrarán con la nueva importación.</p>                                                                     

    	                <p><strong>Semana actual:</strong> <?php echo $semana; ?></p>
						<p><button type="button" class="btn btn-success" onClick="window.location.href='prod_corp/cedula_prod_corpo.php'">Descargar Reporte de Activos</button>                    
						<button type="button" class="btn btn-warning" onClick="window.location.href='productividad_impotar_corpo.php'">Importar Productividad</button></p>
    	                <p>&nbsp;</p> 

					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Acciones</th>
                      <th>Denominacion</th>
                      <th>Area</th>
                      <th>Empleados</th>
                      <th>Estatus</th>
               		 </tr>
                    </thead>
                    <tbody>
					<?php do {
					$el_puesto = $row_detalle['IDpuesto'];
					$query_cap_up = "SELECT * FROM prod_captura WHERE prod_captura.IDpuesto = '$el_puesto' AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio'";
					$cap_up = mysql_query($query_cap_up, $vacantes) or die(mysql_error());
					$row_cap_up = mysql_fetch_assoc($cap_up);
					$totalRows_cap_up = mysql_num_rows($cap_up);
					?>
				  <tr>
					<td>
					<?php if ( $row_cierre['estatus'] != 3) { ?>
					<button type="button" class="btn btn-primary btn-xs" 
					onClick="window.location.href='productividad_captura_c.php?el_puesto=<?php echo $row_detalle['IDpuesto']; ?>'">
					<i class="icon-checkmark-circle2"></i> Capturar</button>
					<?php } else { echo "Semana Cerrada"; } ?>											
					</td>
					<td><?php echo $row_detalle['denominacion']; ?> </td>
					<td><?php echo $row_detalle['area']; ?>&nbsp; </td>
					<td><?php echo $row_detalle['Ocupados']; ?></td>
					<td><?php if ($totalRows_cap_up > 0){echo $totalRows_cap_up. " CAPTURADOS";} else {echo "SIN CAPTURA";} ?> </td>
				</tr>
				  <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
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