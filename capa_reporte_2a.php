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
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fecha_mes = date("m");
$fecha_anio = date("Y");

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
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];
//$IDmatrizes = "17";
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if (isset($_POST['el_anio'])) {$_SESSION['el_anio'] = $_POST['el_anio'];} 
if (!isset($_SESSION['el_anio'])) { $_SESSION['el_anio'] = 2024;}
$el_anio = $_SESSION['el_anio']; 

$query_aarea = "SELECT * FROM vac_areas";
$aarea = mysql_query($query_aarea, $vacantes) or die(mysql_error());
$row_aarea = mysql_fetch_assoc($aarea);

$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) AND IDmatriz NOT IN (7,5,31)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

$query_sucsnte = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) AND region_op = 1";
$sucsnte = mysql_query($query_sucsnte, $vacantes) or die(mysql_error());
$row_sucsnte = mysql_fetch_assoc($sucsnte);
$totalRows_sucsnte = mysql_num_rows($sucsnte);

$query_sucscen = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) AND region_op = 2";
$sucscen = mysql_query($query_sucscen, $vacantes) or die(mysql_error());
$row_sucscen = mysql_fetch_assoc($sucscen);
$totalRows_sucscen = mysql_num_rows($sucscen);

$query_sucssur = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) AND region_op = 2";
$sucssur = mysql_query($query_sucssur, $vacantes) or die(mysql_error());
$row_sucssur = mysql_fetch_assoc($sucssur);
$totalRows_sucssur = mysql_num_rows($sucssur);


set_time_limit(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>


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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">

					<div class="panel-body">
									<p class="content-group"><b>Instrucciones:</b></p>
                                    <p>Selecciona el año en el filtro.</p>


                <form method="POST" action="capa_reporte_2a.php">
					<table class="table">
							<tr>
                           <td> Año: <select name="el_anio" class="form-control">
						   <option value="2025"<?php if ($el_anio == 2025) {echo "selected=\"selected\"";} ?>>2025</option>
						   <option value="2024"<?php if ($el_anio == 2024) {echo "selected=\"selected\"";} ?>>2024</option>
						   <option value="2023"<?php if ($el_anio == 2023) {echo "selected=\"selected\"";} ?>>2023</option>
                                           <option value="2022"<?php if ($el_anio == 2022) {echo "selected=\"selected\"";} ?>>2022</option>
                                           <option value="2021"<?php if ($el_anio == 2021) {echo "selected=\"selected\"";} ?>>2021</option>
									 </select>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button> 
							</td>
					      </tr>
				    </table>
				</form>


				<div class="table-responsive">
					<table class="table table-condensed table-bordered datatable-button-html5-columns">
                    <thead> 
					  <tr>
						<td class="bg-danger text-center" width="10%">Regional</td>
						<td class="bg-danger text-center" width="10%">Sucursal</td>
						<td class="bg-primary text-center">Ene</td>
						<td class="bg-primary text-center">Feb</td>
						<td class="bg-primary text-center">Mar</td>
						<td class="bg-primary text-center">Abr</td>
						<td class="bg-primary text-center">May</td>
						<td class="bg-primary text-center">Jun</td>
						<td class="bg-primary text-center">Jul</td>
						<td class="bg-primary text-center">Ags</td>
						<td class="bg-primary text-center">Sep</td>
						<td class="bg-primary text-center">Oct</td>
						<td class="bg-primary text-center">Nov</td>
						<td class="bg-primary text-center">Dic</td>
						<td class="bg-info text-center">Total</td>
					  </tr>
                    </thead>
                    <tbody>
						<?php
							
							$norte = 0;
							$centro = 0;
							$sur = 0;
								
							do { 

							$IDmatriz = $row_amatriz['IDmatriz'];
							

							//barrido de meses
							$el_mes = 1;
							$el_mes_desfasado = 12;
							$el_anio_desfasado = $el_anio - 1;
							$fecha_desfasada1 = $el_anio_desfasado."-12-25";
							$fecha_desfasada2 = $el_anio."-01-26";
							
							//TODAS LAS ALTAS
							$query_altas1_ene = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_ene = mysql_query($query_altas1_ene, $vacantes) or die(mysql_error());
							$row_altas1_ene = mysql_fetch_assoc($altas1_ene);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_ene = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_ene = mysql_query($query_altas2_ene, $vacantes) or die(mysql_error());
							$row_altas2_ene = mysql_fetch_assoc($altas2_ene);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_ene = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_ene = mysql_query($query_altas3_ene, $vacantes) or die(mysql_error());
							$row_altas3_ene = mysql_fetch_assoc($altas3_ene);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_ene = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_ene = mysql_query($query_altas4_ene, $vacantes) or die(mysql_error());
							$row_altas4_ene = mysql_fetch_assoc($altas4_ene);

							//CAPACITADOS Institucional 
							$query_detalle1_ene = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_ene = mysql_query($query_detalle1_ene, $vacantes) or die(mysql_error());
							$row_detalle1_ene = mysql_fetch_assoc($detalle1_ene);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_ene = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_ene = mysql_query($query_detalle2_ene, $vacantes) or die(mysql_error());
							$row_detalle2_ene = mysql_fetch_assoc($detalle2_ene);
							
							//CAPACITADOS puesto
							$query_detalle3_ene = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_ene = mysql_query($query_detalle3_ene, $vacantes) or die(mysql_error());
							$row_detalle3_ene = mysql_fetch_assoc($detalle3_ene);

							//CAPACITADOS retroalimentacion
							$query_detalle4_ene = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_ene = mysql_query($query_detalle4_ene, $vacantes) or die(mysql_error());
							$row_detalle4_ene = mysql_fetch_assoc($detalle4_ene); 

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_ene['Capacitados'] == 0)
								{$efectividad1_ene = '0'; $pendientes1_ene = $row_altas1_ene['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_ene['Capacitados'] > 0 AND $row_altas1_ene['Altas'] == 0)
								{$efectividad1_ene = '100'; $pendientes1_ene = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_ene['Capacitados'] > 0 AND $row_altas1_ene['Altas'] > 0)
								{$efectividad1_ene = round(($row_detalle1_ene['Capacitados'] / $row_altas1_ene['Altas'])*100,0); 
								$pendientes1_ene = $row_altas1_ene['Altas'] - $row_detalle1_ene['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_ene > 100){ $efectividad1_ene = 100;} 
							if ($efectividad1_ene == 100){ $pendientes1_ene = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_ene['Capacitados'] == 0)
								{$efectividad2_ene = '0'; $pendientes2_ene = $row_altas2_ene['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_ene['Capacitados'] > 0 AND $row_altas2_ene['Altas'] == 0)
								{$efectividad2_ene = '100'; $pendientes2_ene = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_ene['Capacitados'] > 0 AND $row_altas2_ene['Altas'] > 0)
								{$efectividad2_ene = round(($row_detalle2_ene['Capacitados'] / $row_altas2_ene['Altas'])*100,0); 
								$pendientes2_ene = $row_altas2_ene['Altas'] - $row_detalle2_ene['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_ene > 100){ $efectividad2_ene = 100;} 
							if ($efectividad2_ene == 100){ $pendientes2_ene = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_ene['Capacitados'] == 0)
								{$efectividad3_ene = '0'; $pendientes3_ene = $row_altas3_ene['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_ene['Capacitados'] > 0 AND $row_altas3_ene['Altas'] == 0)
								{$efectividad3_ene = '100'; $pendientes3_ene = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_ene['Capacitados'] > 0 AND $row_altas3_ene['Altas'] > 0)
								{$efectividad3_ene = round(($row_detalle3_ene['Capacitados'] / $row_altas3_ene['Altas'])*100,0); 
								$pendientes3_ene = $row_altas3_ene['Altas'] - $row_detalle3_ene['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_ene > 100){ $efectividad3_ene = 100;} 
							if ($efectividad3_ene == 100){ $pendientes3_ene = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_ene['Capacitados'] == 0)
								{$efectividad4_ene = '0'; $pendientes4_ene = $row_altas4_ene['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_ene['Capacitados'] > 0 AND $row_altas4_ene['Altas'] == 0)
								{$efectividad4_ene = '100'; $pendientes4_ene = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_ene['Capacitados'] > 0 AND $row_altas4_ene['Altas'] > 0)
								{$efectividad4_ene = round(($row_detalle4_ene['Capacitados'] / $row_altas4_ene['Altas'])*100,0); 
								$pendientes4_ene = $row_altas4_ene['Altas'] - $row_detalle4_ene['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_ene > 100){ $efectividad4_ene = 100;} 
							if ($efectividad4_ene == 100){ $pendientes4_ene = 0;} 
							
if ($row_detalle1_ene['Capacitados'] == 0 AND $row_altas1_ene['Altas'] == 0){ $efectividad1_ene ='100'; $pendientes1_ene =0;}
if ($row_detalle2_ene['Capacitados'] == 0 AND $row_altas2_ene['Altas'] == 0){ $efectividad2_ene ='100'; $pendientes1_ene =0;}
if ($row_detalle3_ene['Capacitados'] == 0 AND $row_altas3_ene['Altas'] == 0){ $efectividad3_ene ='100'; $pendientes1_ene =0;}
if ($row_detalle4_ene['Capacitados'] == 0 AND $row_altas4_ene['Altas'] == 0){ $efectividad4_ene ='100'; $pendientes1_ene =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_ene ='0'; $efectividad2_ene ='0'; $efectividad3_ene ='0'; $efectividad4_ene ='0';}

							$efectividad_total_ene = round(($efectividad1_ene +$efectividad2_ene + $efectividad3_ene + $efectividad4_ene) / 4,0);

							//barrido de meses
							$el_mes = 2;
							$el_mes_desfasado = 1;
							$fecha_desfasada1 = $el_anio."-01-25";
							$fecha_desfasada2 = $el_anio."-02-26";
							
							//TODAS LAS ALTAS
							$query_altas1_feb = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_feb = mysql_query($query_altas1_feb, $vacantes) or die(mysql_error());
							$row_altas1_feb = mysql_fetch_assoc($altas1_feb);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_feb = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_feb = mysql_query($query_altas2_feb, $vacantes) or die(mysql_error());
							$row_altas2_feb = mysql_fetch_assoc($altas2_feb);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_feb = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_feb = mysql_query($query_altas3_feb, $vacantes) or die(mysql_error());
							$row_altas3_feb = mysql_fetch_assoc($altas3_feb);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_feb = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_feb = mysql_query($query_altas4_feb, $vacantes) or die(mysql_error());
							$row_altas4_feb = mysql_fetch_assoc($altas4_feb);

							//CAPACITADOS Institucional 
							$query_detalle1_feb = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_feb = mysql_query($query_detalle1_feb, $vacantes) or die(mysql_error());
							$row_detalle1_feb = mysql_fetch_assoc($detalle1_feb);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_feb = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_feb = mysql_query($query_detalle2_feb, $vacantes) or die(mysql_error());
							$row_detalle2_feb = mysql_fetch_assoc($detalle2_feb);
							
							//CAPACITADOS puesto
							$query_detalle3_feb = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_feb = mysql_query($query_detalle3_feb, $vacantes) or die(mysql_error());
							$row_detalle3_feb = mysql_fetch_assoc($detalle3_feb);

							//CAPACITADOS retroalimentacion
							$query_detalle4_feb = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_feb = mysql_query($query_detalle4_feb, $vacantes) or die(mysql_error());
							$row_detalle4_feb = mysql_fetch_assoc($detalle4_feb);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_feb['Capacitados'] == 0)
								{$efectividad1_feb = '0'; $pendientes1_feb = $row_altas1_feb['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_feb['Capacitados'] > 0 AND $row_altas1_feb['Altas'] == 0)
								{$efectividad1_feb = '100'; $pendientes1_feb = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_feb['Capacitados'] > 0 AND $row_altas1_feb['Altas'] > 0)
								{$efectividad1_feb = round(($row_detalle1_feb['Capacitados'] / $row_altas1_feb['Altas'])*100,0); 
								$pendientes1_feb = $row_altas1_feb['Altas'] - $row_detalle1_feb['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_feb > 100){ $efectividad1_feb = 100;} 
							if ($efectividad1_feb == 100){ $pendientes1_feb = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_feb['Capacitados'] == 0)
								{$efectividad2_feb = '0'; $pendientes2_feb = $row_altas2_feb['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_feb['Capacitados'] > 0 AND $row_altas2_feb['Altas'] == 0)
								{$efectividad2_feb = '100'; $pendientes2_feb = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_feb['Capacitados'] > 0 AND $row_altas2_feb['Altas'] > 0)
								{$efectividad2_feb = round(($row_detalle2_feb['Capacitados'] / $row_altas2_feb['Altas'])*100,0); 
								$pendientes2_feb = $row_altas2_feb['Altas'] - $row_detalle2_feb['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_feb > 100){ $efectividad2_feb = 100;} 
							if ($efectividad2_feb == 100){ $pendientes2_feb = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_feb['Capacitados'] == 0)
								{$efectividad3_feb = '0'; $pendientes3_feb = $row_altas3_feb['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_feb['Capacitados'] > 0 AND $row_altas3_feb['Altas'] == 0)
								{$efectividad3_feb = '100'; $pendientes3_feb = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_feb['Capacitados'] > 0 AND $row_altas3_feb['Altas'] > 0)
								{$efectividad3_feb = round(($row_detalle3_feb['Capacitados'] / $row_altas3_feb['Altas'])*100,0); 
								$pendientes3_feb = $row_altas3_feb['Altas'] - $row_detalle3_feb['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_feb > 100){ $efectividad3_feb = 100;} 
							if ($efectividad3_feb == 100){ $pendientes3_feb = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_feb['Capacitados'] == 0)
								{$efectividad4_feb = '0'; $pendientes4_feb = $row_altas4_feb['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_feb['Capacitados'] > 0 AND $row_altas4_feb['Altas'] == 0)
								{$efectividad4_feb = '100'; $pendientes4_feb = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_feb['Capacitados'] > 0 AND $row_altas4_feb['Altas'] > 0)
								{$efectividad4_feb = round(($row_detalle4_feb['Capacitados'] / $row_altas4_feb['Altas'])*100,0); 
								$pendientes4_feb = $row_altas4_feb['Altas'] - $row_detalle4_feb['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_feb > 100){ $efectividad4_feb = 100;} 
							if ($efectividad4_feb == 100){ $pendientes4_feb = 0;} 
							
if ($row_detalle1_feb['Capacitados'] == 0 AND $row_altas1_feb['Altas'] == 0){ $efectividad1_feb ='100'; $pendientes1_feb =0;}
if ($row_detalle2_feb['Capacitados'] == 0 AND $row_altas2_feb['Altas'] == 0){ $efectividad2_feb ='100'; $pendientes1_feb =0;}
if ($row_detalle3_feb['Capacitados'] == 0 AND $row_altas3_feb['Altas'] == 0){ $efectividad3_feb ='100'; $pendientes1_feb =0;}
if ($row_detalle4_feb['Capacitados'] == 0 AND $row_altas4_feb['Altas'] == 0){ $efectividad4_feb ='100'; $pendientes1_feb =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_feb ='0'; $efectividad2_feb ='0'; $efectividad3_feb ='0'; $efectividad4_feb ='0';}

							$efectividad_total_feb = round(($efectividad1_feb +$efectividad2_feb + $efectividad3_feb + $efectividad4_feb) / 4,0);

							//barrido de meses
							$el_mes = 3;
							$el_mes_desfasado = 2;
							$fecha_desfasada1 = $el_anio."-02-25";
							$fecha_desfasada2 = $el_anio."-03-26";
							
							//TODAS LAS ALTAS
							$query_altas1_mar = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_mar = mysql_query($query_altas1_mar, $vacantes) or die(mysql_error());
							$row_altas1_mar = mysql_fetch_assoc($altas1_mar);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_mar = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_mar = mysql_query($query_altas2_mar, $vacantes) or die(mysql_error());
							$row_altas2_mar = mysql_fetch_assoc($altas2_mar);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_mar = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_mar = mysql_query($query_altas3_mar, $vacantes) or die(mysql_error());
							$row_altas3_mar = mysql_fetch_assoc($altas3_mar);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_mar = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_mar = mysql_query($query_altas4_mar, $vacantes) or die(mysql_error());
							$row_altas4_mar = mysql_fetch_assoc($altas4_mar);

							//CAPACITADOS Institucional 
							$query_detalle1_mar = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_mar = mysql_query($query_detalle1_mar, $vacantes) or die(mysql_error());
							$row_detalle1_mar = mysql_fetch_assoc($detalle1_mar);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_mar = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_mar = mysql_query($query_detalle2_mar, $vacantes) or die(mysql_error());
							$row_detalle2_mar = mysql_fetch_assoc($detalle2_mar);
							
							//CAPACITADOS puesto
							$query_detalle3_mar = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_mar = mysql_query($query_detalle3_mar, $vacantes) or die(mysql_error());
							$row_detalle3_mar = mysql_fetch_assoc($detalle3_mar);

							//CAPACITADOS retroalimentacion
							$query_detalle4_mar = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_mar = mysql_query($query_detalle4_mar, $vacantes) or die(mysql_error());
							$row_detalle4_mar = mysql_fetch_assoc($detalle4_mar);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_mar['Capacitados'] == 0)
								{$efectividad1_mar = '0'; $pendientes1_mar = $row_altas1_mar['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_mar['Capacitados'] > 0 AND $row_altas1_mar['Altas'] == 0)
								{$efectividad1_mar = '100'; $pendientes1_mar = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_mar['Capacitados'] > 0 AND $row_altas1_mar['Altas'] > 0)
								{$efectividad1_mar = round(($row_detalle1_mar['Capacitados'] / $row_altas1_mar['Altas'])*100,0); 
								$pendientes1_mar = $row_altas1_mar['Altas'] - $row_detalle1_mar['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_mar > 100){ $efectividad1_mar = 100;} 
							if ($efectividad1_mar == 100){ $pendientes1_mar = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_mar['Capacitados'] == 0)
								{$efectividad2_mar = '0'; $pendientes2_mar = $row_altas2_mar['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_mar['Capacitados'] > 0 AND $row_altas2_mar['Altas'] == 0)
								{$efectividad2_mar = '100'; $pendientes2_mar = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_mar['Capacitados'] > 0 AND $row_altas2_mar['Altas'] > 0)
								{$efectividad2_mar = round(($row_detalle2_mar['Capacitados'] / $row_altas2_mar['Altas'])*100,0); 
								$pendientes2_mar = $row_altas2_mar['Altas'] - $row_detalle2_mar['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_mar > 100){ $efectividad2_mar = 100;} 
							if ($efectividad2_mar == 100){ $pendientes2_mar = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_mar['Capacitados'] == 0)
								{$efectividad3_mar = '0'; $pendientes3_mar = $row_altas3_mar['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_mar['Capacitados'] > 0 AND $row_altas3_mar['Altas'] == 0)
								{$efectividad3_mar = '100'; $pendientes3_mar = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_mar['Capacitados'] > 0 AND $row_altas3_mar['Altas'] > 0)
								{$efectividad3_mar = round(($row_detalle3_mar['Capacitados'] / $row_altas3_mar['Altas'])*100,0); 
								$pendientes3_mar = $row_altas3_mar['Altas'] - $row_detalle3_mar['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_mar > 100){ $efectividad3_mar = 100;} 
							if ($efectividad3_mar == 100){ $pendientes3_mar = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_mar['Capacitados'] == 0)
								{$efectividad4_mar = '0'; $pendientes4_mar = $row_altas4_mar['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_mar['Capacitados'] > 0 AND $row_altas4_mar['Altas'] == 0)
								{$efectividad4_mar = '100'; $pendientes4_mar = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_mar['Capacitados'] > 0 AND $row_altas4_mar['Altas'] > 0)
								{$efectividad4_mar = round(($row_detalle4_mar['Capacitados'] / $row_altas4_mar['Altas'])*100,0); 
								$pendientes4_mar = $row_altas4_mar['Altas'] - $row_detalle4_mar['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_mar > 100){ $efectividad4_mar = 100;} 
							if ($efectividad4_mar == 100){ $pendientes4_mar = 0;} 
							
if ($row_detalle1_mar['Capacitados'] == 0 AND $row_altas1_mar['Altas'] == 0){ $efectividad1_mar ='100'; $pendientes1_mar =0;}
if ($row_detalle2_mar['Capacitados'] == 0 AND $row_altas2_mar['Altas'] == 0){ $efectividad2_mar ='100'; $pendientes1_mar =0;}
if ($row_detalle3_mar['Capacitados'] == 0 AND $row_altas3_mar['Altas'] == 0){ $efectividad3_mar ='100'; $pendientes1_mar =0;}
if ($row_detalle4_mar['Capacitados'] == 0 AND $row_altas4_mar['Altas'] == 0){ $efectividad4_mar ='100'; $pendientes1_mar =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_mar ='0'; $efectividad2_mar ='0'; $efectividad3_mar ='0'; $efectividad4_mar ='0';}
							
							$efectividad_total_mar = round(($efectividad1_mar +$efectividad2_mar + $efectividad3_mar + $efectividad4_mar) / 4,0);

							//barrido de meses
							$el_mes = 4;
							$el_mes_desfasado = 3;
							$fecha_desfasada1 = $el_anio."-03-25";
							$fecha_desfasada2 = $el_anio."-04-26";
							
							//TODAS LAS ALTAS
							$query_altas1_abr = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_abr = mysql_query($query_altas1_abr, $vacantes) or die(mysql_error());
							$row_altas1_abr = mysql_fetch_assoc($altas1_abr);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_abr = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_abr = mysql_query($query_altas2_abr, $vacantes) or die(mysql_error());
							$row_altas2_abr = mysql_fetch_assoc($altas2_abr);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_abr = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_abr = mysql_query($query_altas3_abr, $vacantes) or die(mysql_error());
							$row_altas3_abr = mysql_fetch_assoc($altas3_abr);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_abr = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_abr = mysql_query($query_altas4_abr, $vacantes) or die(mysql_error());
							$row_altas4_abr = mysql_fetch_assoc($altas4_abr);

							//CAPACITADOS Institucional 
							$query_detalle1_abr = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_abr = mysql_query($query_detalle1_abr, $vacantes) or die(mysql_error());
							$row_detalle1_abr = mysql_fetch_assoc($detalle1_abr);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_abr = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_abr = mysql_query($query_detalle2_abr, $vacantes) or die(mysql_error());
							$row_detalle2_abr = mysql_fetch_assoc($detalle2_abr);
							
							//CAPACITADOS puesto
							$query_detalle3_abr = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_abr = mysql_query($query_detalle3_abr, $vacantes) or die(mysql_error());
							$row_detalle3_abr = mysql_fetch_assoc($detalle3_abr);

							//CAPACITADOS retroalimentacion
							$query_detalle4_abr = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_abr = mysql_query($query_detalle4_abr, $vacantes) or die(mysql_error());
							$row_detalle4_abr = mysql_fetch_assoc($detalle4_abr);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_abr['Capacitados'] == 0)
								{$efectividad1_abr = '0'; $pendientes1_abr = $row_altas1_abr['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_abr['Capacitados'] > 0 AND $row_altas1_abr['Altas'] == 0)
								{$efectividad1_abr = '100'; $pendientes1_abr = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_abr['Capacitados'] > 0 AND $row_altas1_abr['Altas'] > 0)
								{$efectividad1_abr = round(($row_detalle1_abr['Capacitados'] / $row_altas1_abr['Altas'])*100,0); 
								$pendientes1_abr = $row_altas1_abr['Altas'] - $row_detalle1_abr['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_abr > 100){ $efectividad1_abr = 100;} 
							if ($efectividad1_abr == 100){ $pendientes1_abr = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_abr['Capacitados'] == 0)
								{$efectividad2_abr = '0'; $pendientes2_abr = $row_altas2_abr['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_abr['Capacitados'] > 0 AND $row_altas2_abr['Altas'] == 0)
								{$efectividad2_abr = '100'; $pendientes2_abr = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_abr['Capacitados'] > 0 AND $row_altas2_abr['Altas'] > 0)
								{$efectividad2_abr = round(($row_detalle2_abr['Capacitados'] / $row_altas2_abr['Altas'])*100,0); 
								$pendientes2_abr = $row_altas2_abr['Altas'] - $row_detalle2_abr['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_abr > 100){ $efectividad2_abr = 100;} 
							if ($efectividad2_abr == 100){ $pendientes2_abr = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_abr['Capacitados'] == 0)
								{$efectividad3_abr = '0'; $pendientes3_abr = $row_altas3_abr['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_abr['Capacitados'] > 0 AND $row_altas3_abr['Altas'] == 0)
								{$efectividad3_abr = '100'; $pendientes3_abr = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_abr['Capacitados'] > 0 AND $row_altas3_abr['Altas'] > 0)
								{$efectividad3_abr = round(($row_detalle3_abr['Capacitados'] / $row_altas3_abr['Altas'])*100,0); 
								$pendientes3_abr = $row_altas3_abr['Altas'] - $row_detalle3_abr['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_abr > 100){ $efectividad3_abr = 100;} 
							if ($efectividad3_abr == 100){ $pendientes3_abr = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_abr['Capacitados'] == 0)
								{$efectividad4_abr = '0'; $pendientes4_abr = $row_altas4_abr['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_abr['Capacitados'] > 0 AND $row_altas4_abr['Altas'] == 0)
								{$efectividad4_abr = '100'; $pendientes4_abr = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_abr['Capacitados'] > 0 AND $row_altas4_abr['Altas'] > 0)
								{$efectividad4_abr = round(($row_detalle4_abr['Capacitados'] / $row_altas4_abr['Altas'])*100,0); 
								$pendientes4_abr = $row_altas4_abr['Altas'] - $row_detalle4_abr['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_abr > 100){ $efectividad4_abr = 100;} 
							if ($efectividad4_abr == 100){ $pendientes4_abr = 0;} 
												
if ($row_detalle1_abr['Capacitados'] == 0 AND $row_altas1_abr['Altas'] == 0){ $efectividad1_abr ='100'; $pendientes1_abr =0;}
if ($row_detalle2_abr['Capacitados'] == 0 AND $row_altas2_abr['Altas'] == 0){ $efectividad2_abr ='100'; $pendientes1_abr =0;}
if ($row_detalle3_abr['Capacitados'] == 0 AND $row_altas3_abr['Altas'] == 0){ $efectividad3_abr ='100'; $pendientes1_abr =0;}
if ($row_detalle4_abr['Capacitados'] == 0 AND $row_altas4_abr['Altas'] == 0){ $efectividad4_abr ='100'; $pendientes1_abr =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_abr ='0'; $efectividad2_abr ='0'; $efectividad3_abr ='0'; $efectividad4_abr ='0';}
							
							$efectividad_total_abr = round(($efectividad1_abr +$efectividad2_abr + $efectividad3_abr + $efectividad4_abr) / 4,0);

							//barrido de meses
							$el_mes = 5;
							$el_mes_desfasado = 4;
							$fecha_desfasada1 = $el_anio."-04-25";
							$fecha_desfasada2 = $el_anio."-05-26";
							
							//TODAS LAS ALTAS
							$query_altas1_may = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_may = mysql_query($query_altas1_may, $vacantes) or die(mysql_error());
							$row_altas1_may = mysql_fetch_assoc($altas1_may);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_may = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_may = mysql_query($query_altas2_may, $vacantes) or die(mysql_error());
							$row_altas2_may = mysql_fetch_assoc($altas2_may);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_may = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_may = mysql_query($query_altas3_may, $vacantes) or die(mysql_error());
							$row_altas3_may = mysql_fetch_assoc($altas3_may);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_may = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_may = mysql_query($query_altas4_may, $vacantes) or die(mysql_error());
							$row_altas4_may = mysql_fetch_assoc($altas4_may);

							//CAPACITADOS Institucional 
							$query_detalle1_may = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_may = mysql_query($query_detalle1_may, $vacantes) or die(mysql_error());
							$row_detalle1_may = mysql_fetch_assoc($detalle1_may);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_may = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_may = mysql_query($query_detalle2_may, $vacantes) or die(mysql_error());
							$row_detalle2_may = mysql_fetch_assoc($detalle2_may);
							
							//CAPACITADOS puesto
							$query_detalle3_may = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_may = mysql_query($query_detalle3_may, $vacantes) or die(mysql_error());
							$row_detalle3_may = mysql_fetch_assoc($detalle3_may);

							//CAPACITADOS retroalimentacion
							$query_detalle4_may = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_may = mysql_query($query_detalle4_may, $vacantes) or die(mysql_error());
							$row_detalle4_may = mysql_fetch_assoc($detalle4_may);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_may['Capacitados'] == 0)
								{$efectividad1_may = '0'; $pendientes1_may = $row_altas1_may['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_may['Capacitados'] > 0 AND $row_altas1_may['Altas'] == 0)
								{$efectividad1_may = '100'; $pendientes1_may = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_may['Capacitados'] > 0 AND $row_altas1_may['Altas'] > 0)
								{$efectividad1_may = round(($row_detalle1_may['Capacitados'] / $row_altas1_may['Altas'])*100,0); 
								$pendientes1_may = $row_altas1_may['Altas'] - $row_detalle1_may['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_may > 100){ $efectividad1_may = 100;} 
							if ($efectividad1_may == 100){ $pendientes1_may = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_may['Capacitados'] == 0)
								{$efectividad2_may = '0'; $pendientes2_may = $row_altas2_may['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_may['Capacitados'] > 0 AND $row_altas2_may['Altas'] == 0)
								{$efectividad2_may = '100'; $pendientes2_may = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_may['Capacitados'] > 0 AND $row_altas2_may['Altas'] > 0)
								{$efectividad2_may = round(($row_detalle2_may['Capacitados'] / $row_altas2_may['Altas'])*100,0); 
								$pendientes2_may = $row_altas2_may['Altas'] - $row_detalle2_may['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_may > 100){ $efectividad2_may = 100;} 
							if ($efectividad2_may == 100){ $pendientes2_may = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_may['Capacitados'] == 0)
								{$efectividad3_may = '0'; $pendientes3_may = $row_altas3_may['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_may['Capacitados'] > 0 AND $row_altas3_may['Altas'] == 0)
								{$efectividad3_may = '100'; $pendientes3_may = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_may['Capacitados'] > 0 AND $row_altas3_may['Altas'] > 0)
								{$efectividad3_may = round(($row_detalle3_may['Capacitados'] / $row_altas3_may['Altas'])*100,0); 
								$pendientes3_may = $row_altas3_may['Altas'] - $row_detalle3_may['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_may > 100){ $efectividad3_may = 100;} 
							if ($efectividad3_may == 100){ $pendientes3_may = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_may['Capacitados'] == 0)
								{$efectividad4_may = '0'; $pendientes4_may = $row_altas4_may['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_may['Capacitados'] > 0 AND $row_altas4_may['Altas'] == 0)
								{$efectividad4_may = '100'; $pendientes4_may = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_may['Capacitados'] > 0 AND $row_altas4_may['Altas'] > 0)
								{$efectividad4_may = round(($row_detalle4_may['Capacitados'] / $row_altas4_may['Altas'])*100,0); 
								$pendientes4_may = $row_altas4_may['Altas'] - $row_detalle4_may['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_may > 100){ $efectividad4_may = 100;} 
							if ($efectividad4_may == 100){ $pendientes4_may = 0;} 
							
if ($row_detalle1_may['Capacitados'] == 0 AND $row_altas1_may['Altas'] == 0){ $efectividad1_may ='100'; $pendientes1_may =0;}
if ($row_detalle2_may['Capacitados'] == 0 AND $row_altas2_may['Altas'] == 0){ $efectividad2_may ='100'; $pendientes1_may =0;}
if ($row_detalle3_may['Capacitados'] == 0 AND $row_altas3_may['Altas'] == 0){ $efectividad3_may ='100'; $pendientes1_may =0;}
if ($row_detalle4_may['Capacitados'] == 0 AND $row_altas4_may['Altas'] == 0){ $efectividad4_may ='100'; $pendientes1_may =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_may ='0'; $efectividad2_may ='0'; $efectividad3_may ='0'; $efectividad4_may ='0';}

							$efectividad_total_may = round(($efectividad1_may +$efectividad2_may + $efectividad3_may + $efectividad4_may) / 4,0);

							//barrido de meses
							$el_mes = 6;
							$el_mes_desfasado = 5;
							$fecha_desfasada1 = $el_anio."-05-25";
							$fecha_desfasada2 = $el_anio."-06-26";
							
							//TODAS LAS ALTAS
							$query_altas1_jun = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_jun = mysql_query($query_altas1_jun, $vacantes) or die(mysql_error());
							$row_altas1_jun = mysql_fetch_assoc($altas1_jun);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_jun = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_jun = mysql_query($query_altas2_jun, $vacantes) or die(mysql_error());
							$row_altas2_jun = mysql_fetch_assoc($altas2_jun);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_jun = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_jun = mysql_query($query_altas3_jun, $vacantes) or die(mysql_error());
							$row_altas3_jun = mysql_fetch_assoc($altas3_jun);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_jun = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_jun = mysql_query($query_altas4_jun, $vacantes) or die(mysql_error());
							$row_altas4_jun = mysql_fetch_assoc($altas4_jun);

							//CAPACITADOS Institucional 
							$query_detalle1_jun = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_jun = mysql_query($query_detalle1_jun, $vacantes) or die(mysql_error());
							$row_detalle1_jun = mysql_fetch_assoc($detalle1_jun);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_jun = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_jun = mysql_query($query_detalle2_jun, $vacantes) or die(mysql_error());
							$row_detalle2_jun = mysql_fetch_assoc($detalle2_jun);
							
							//CAPACITADOS puesto
							$query_detalle3_jun = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_jun = mysql_query($query_detalle3_jun, $vacantes) or die(mysql_error());
							$row_detalle3_jun = mysql_fetch_assoc($detalle3_jun);

							//CAPACITADOS retroalimentacion
							$query_detalle4_jun = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_jun = mysql_query($query_detalle4_jun, $vacantes) or die(mysql_error());
							$row_detalle4_jun = mysql_fetch_assoc($detalle4_jun);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_jun['Capacitados'] == 0)
								{$efectividad1_jun = '0'; $pendientes1_jun = $row_altas1_jun['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_jun['Capacitados'] > 0 AND $row_altas1_jun['Altas'] == 0)
								{$efectividad1_jun = '100'; $pendientes1_jun = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_jun['Capacitados'] > 0 AND $row_altas1_jun['Altas'] > 0)
								{$efectividad1_jun = round(($row_detalle1_jun['Capacitados'] / $row_altas1_jun['Altas'])*100,0); 
								$pendientes1_jun = $row_altas1_jun['Altas'] - $row_detalle1_jun['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_jun > 100){ $efectividad1_jun = 100;} 
							if ($efectividad1_jun == 100){ $pendientes1_jun = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_jun['Capacitados'] == 0)
								{$efectividad2_jun = '0'; $pendientes2_jun = $row_altas2_jun['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_jun['Capacitados'] > 0 AND $row_altas2_jun['Altas'] == 0)
								{$efectividad2_jun = '100'; $pendientes2_jun = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_jun['Capacitados'] > 0 AND $row_altas2_jun['Altas'] > 0)
								{$efectividad2_jun = round(($row_detalle2_jun['Capacitados'] / $row_altas2_jun['Altas'])*100,0); 
								$pendientes2_jun = $row_altas2_jun['Altas'] - $row_detalle2_jun['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_jun > 100){ $efectividad2_jun = 100;} 
							if ($efectividad2_jun == 100){ $pendientes2_jun = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_jun['Capacitados'] == 0)
								{$efectividad3_jun = '0'; $pendientes3_jun = $row_altas3_jun['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_jun['Capacitados'] > 0 AND $row_altas3_jun['Altas'] == 0)
								{$efectividad3_jun = '100'; $pendientes3_jun = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_jun['Capacitados'] > 0 AND $row_altas3_jun['Altas'] > 0)
								{$efectividad3_jun = round(($row_detalle3_jun['Capacitados'] / $row_altas3_jun['Altas'])*100,0); 
								$pendientes3_jun = $row_altas3_jun['Altas'] - $row_detalle3_jun['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_jun > 100){ $efectividad3_jun = 100;} 
							if ($efectividad3_jun == 100){ $pendientes3_jun = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_jun['Capacitados'] == 0)
								{$efectividad4_jun = '0'; $pendientes4_jun = $row_altas4_jun['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_jun['Capacitados'] > 0 AND $row_altas4_jun['Altas'] == 0)
								{$efectividad4_jun = '100'; $pendientes4_jun = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_jun['Capacitados'] > 0 AND $row_altas4_jun['Altas'] > 0)
								{$efectividad4_jun = round(($row_detalle4_jun['Capacitados'] / $row_altas4_jun['Altas'])*100,0); 
								$pendientes4_jun = $row_altas4_jun['Altas'] - $row_detalle4_jun['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_jun > 100){ $efectividad4_jun = 100;} 
							if ($efectividad4_jun == 100){ $pendientes4_jun = 0;} 
							
if ($row_detalle1_jun['Capacitados'] == 0 AND $row_altas1_jun['Altas'] == 0){ $efectividad1_jun ='100'; $pendientes1_jun =0;}
if ($row_detalle2_jun['Capacitados'] == 0 AND $row_altas2_jun['Altas'] == 0){ $efectividad2_jun ='100'; $pendientes1_jun =0;}
if ($row_detalle3_jun['Capacitados'] == 0 AND $row_altas3_jun['Altas'] == 0){ $efectividad3_jun ='100'; $pendientes1_jun =0;}
if ($row_detalle4_jun['Capacitados'] == 0 AND $row_altas4_jun['Altas'] == 0){ $efectividad4_jun ='100'; $pendientes1_jun =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_jun ='0'; $efectividad2_jun ='0'; $efectividad3_jun ='0'; $efectividad4_jun ='0';}

							$efectividad_total_jun = round(($efectividad1_jun +$efectividad2_jun + $efectividad3_jun + $efectividad4_jun) / 4,0);

							//barrido de meses
							$el_mes = 7;
							$el_mes_desfasado = 6;
							$fecha_desfasada1 = $el_anio."-06-25";
							$fecha_desfasada2 = $el_anio."-07-26";
							
							//TODAS LAS ALTAS
							$query_altas1_jul = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_jul = mysql_query($query_altas1_jul, $vacantes) or die(mysql_error());
							$row_altas1_jul = mysql_fetch_assoc($altas1_jul);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_jul = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_jul = mysql_query($query_altas2_jul, $vacantes) or die(mysql_error());
							$row_altas2_jul = mysql_fetch_assoc($altas2_jul);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_jul = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_jul = mysql_query($query_altas3_jul, $vacantes) or die(mysql_error());
							$row_altas3_jul = mysql_fetch_assoc($altas3_jul);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_jul = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_jul = mysql_query($query_altas4_jul, $vacantes) or die(mysql_error());
							$row_altas4_jul = mysql_fetch_assoc($altas4_jul);

							//CAPACITADOS Institucional 
							$query_detalle1_jul = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_jul = mysql_query($query_detalle1_jul, $vacantes) or die(mysql_error());
							$row_detalle1_jul = mysql_fetch_assoc($detalle1_jul);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_jul = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_jul = mysql_query($query_detalle2_jul, $vacantes) or die(mysql_error());
							$row_detalle2_jul = mysql_fetch_assoc($detalle2_jul);
							
							//CAPACITADOS puesto
							$query_detalle3_jul = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_jul = mysql_query($query_detalle3_jul, $vacantes) or die(mysql_error());
							$row_detalle3_jul = mysql_fetch_assoc($detalle3_jul);

							//CAPACITADOS retroalimentacion
							$query_detalle4_jul = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_jul = mysql_query($query_detalle4_jul, $vacantes) or die(mysql_error());
							$row_detalle4_jul = mysql_fetch_assoc($detalle4_jul);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_jul['Capacitados'] == 0)
								{$efectividad1_jul = '0'; $pendientes1_jul = $row_altas1_jul['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_jul['Capacitados'] > 0 AND $row_altas1_jul['Altas'] == 0)
								{$efectividad1_jul = '100'; $pendientes1_jul = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_jul['Capacitados'] > 0 AND $row_altas1_jul['Altas'] > 0)
								{$efectividad1_jul = round(($row_detalle1_jul['Capacitados'] / $row_altas1_jul['Altas'])*100,0); 
								$pendientes1_jul = $row_altas1_jul['Altas'] - $row_detalle1_jul['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_jul > 100){ $efectividad1_jul = 100;} 
							if ($efectividad1_jul == 100){ $pendientes1_jul = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_jul['Capacitados'] == 0)
								{$efectividad2_jul = '0'; $pendientes2_jul = $row_altas2_jul['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_jul['Capacitados'] > 0 AND $row_altas2_jul['Altas'] == 0)
								{$efectividad2_jul = '100'; $pendientes2_jul = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_jul['Capacitados'] > 0 AND $row_altas2_jul['Altas'] > 0)
								{$efectividad2_jul = round(($row_detalle2_jul['Capacitados'] / $row_altas2_jul['Altas'])*100,0); 
								$pendientes2_jul = $row_altas2_jul['Altas'] - $row_detalle2_jul['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_jul > 100){ $efectividad2_jul = 100;} 
							if ($efectividad2_jul == 100){ $pendientes2_jul = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_jul['Capacitados'] == 0)
								{$efectividad3_jul = '0'; $pendientes3_jul = $row_altas3_jul['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_jul['Capacitados'] > 0 AND $row_altas3_jul['Altas'] == 0)
								{$efectividad3_jul = '100'; $pendientes3_jul = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_jul['Capacitados'] > 0 AND $row_altas3_jul['Altas'] > 0)
								{$efectividad3_jul = round(($row_detalle3_jul['Capacitados'] / $row_altas3_jul['Altas'])*100,0); 
								$pendientes3_jul = $row_altas3_jul['Altas'] - $row_detalle3_jul['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_jul > 100){ $efectividad3_jul = 100;} 
							if ($efectividad3_jul == 100){ $pendientes3_jul = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_jul['Capacitados'] == 0)
								{$efectividad4_jul = '0'; $pendientes4_jul = $row_altas4_jul['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_jul['Capacitados'] > 0 AND $row_altas4_jul['Altas'] == 0)
								{$efectividad4_jul = '100'; $pendientes4_jul = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_jul['Capacitados'] > 0 AND $row_altas4_jul['Altas'] > 0)
								{$efectividad4_jul = round(($row_detalle4_jul['Capacitados'] / $row_altas4_jul['Altas'])*100,0); 
								$pendientes4_jul = $row_altas4_jul['Altas'] - $row_detalle4_jul['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_jul > 100){ $efectividad4_jul = 100;} 
							if ($efectividad4_jul == 100){ $pendientes4_jul = 0;} 
							
if ($row_detalle1_jul['Capacitados'] == 0 AND $row_altas1_jul['Altas'] == 0){ $efectividad1_jul ='100'; $pendientes1_jul =0;}
if ($row_detalle2_jul['Capacitados'] == 0 AND $row_altas2_jul['Altas'] == 0){ $efectividad2_jul ='100'; $pendientes1_jul =0;}
if ($row_detalle3_jul['Capacitados'] == 0 AND $row_altas3_jul['Altas'] == 0){ $efectividad3_jul ='100'; $pendientes1_jul =0;}
if ($row_detalle4_jul['Capacitados'] == 0 AND $row_altas4_jul['Altas'] == 0){ $efectividad4_jul ='100'; $pendientes1_jul =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_jul ='0'; $efectividad2_jul ='0'; $efectividad3_jul ='0'; $efectividad4_jul ='0';}

							$efectividad_total_jul = round(($efectividad1_jul +$efectividad2_jul + $efectividad3_jul + $efectividad4_jul) / 4,0);

							//barrido de meses
							$el_mes = 8;
							$el_mes_desfasado = 7;
							$fecha_desfasada1 = $el_anio."-07-25";
							$fecha_desfasada2 = $el_anio."-08-26";
							
							//TODAS LAS ALTAS
							$query_altas1_ags = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_ags = mysql_query($query_altas1_ags, $vacantes) or die(mysql_error());
							$row_altas1_ags = mysql_fetch_assoc($altas1_ags);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_ags = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_ags = mysql_query($query_altas2_ags, $vacantes) or die(mysql_error());
							$row_altas2_ags = mysql_fetch_assoc($altas2_ags);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_ags = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_ags = mysql_query($query_altas3_ags, $vacantes) or die(mysql_error());
							$row_altas3_ags = mysql_fetch_assoc($altas3_ags);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_ags = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_ags = mysql_query($query_altas4_ags, $vacantes) or die(mysql_error());
							$row_altas4_ags = mysql_fetch_assoc($altas4_ags);

							//CAPACITADOS Institucional 
							$query_detalle1_ags = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_ags = mysql_query($query_detalle1_ags, $vacantes) or die(mysql_error());
							$row_detalle1_ags = mysql_fetch_assoc($detalle1_ags);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_ags = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_ags = mysql_query($query_detalle2_ags, $vacantes) or die(mysql_error());
							$row_detalle2_ags = mysql_fetch_assoc($detalle2_ags);
							
							//CAPACITADOS puesto
							$query_detalle3_ags = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_ags = mysql_query($query_detalle3_ags, $vacantes) or die(mysql_error());
							$row_detalle3_ags = mysql_fetch_assoc($detalle3_ags);

							//CAPACITADOS retroalimentacion
							$query_detalle4_ags = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_ags = mysql_query($query_detalle4_ags, $vacantes) or die(mysql_error());
							$row_detalle4_ags = mysql_fetch_assoc($detalle4_ags);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_ags['Capacitados'] == 0)
								{$efectividad1_ags = '0'; $pendientes1_ags = $row_altas1_ags['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_ags['Capacitados'] > 0 AND $row_altas1_ags['Altas'] == 0)
								{$efectividad1_ags = '100'; $pendientes1_ags = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_ags['Capacitados'] > 0 AND $row_altas1_ags['Altas'] > 0)
								{$efectividad1_ags = round(($row_detalle1_ags['Capacitados'] / $row_altas1_ags['Altas'])*100,0); 
								$pendientes1_ags = $row_altas1_ags['Altas'] - $row_detalle1_ags['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_ags > 100){ $efectividad1_ags = 100;} 
							if ($efectividad1_ags == 100){ $pendientes1_ags = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_ags['Capacitados'] == 0)
								{$efectividad2_ags = '0'; $pendientes2_ags = $row_altas2_ags['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_ags['Capacitados'] > 0 AND $row_altas2_ags['Altas'] == 0)
								{$efectividad2_ags = '100'; $pendientes2_ags = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_ags['Capacitados'] > 0 AND $row_altas2_ags['Altas'] > 0)
								{$efectividad2_ags = round(($row_detalle2_ags['Capacitados'] / $row_altas2_ags['Altas'])*100,0); 
								$pendientes2_ags = $row_altas2_ags['Altas'] - $row_detalle2_ags['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_ags > 100){ $efectividad2_ags = 100;} 
							if ($efectividad2_ags == 100){ $pendientes2_ags = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_ags['Capacitados'] == 0)
								{$efectividad3_ags = '0'; $pendientes3_ags = $row_altas3_ags['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_ags['Capacitados'] > 0 AND $row_altas3_ags['Altas'] == 0)
								{$efectividad3_ags = '100'; $pendientes3_ags = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_ags['Capacitados'] > 0 AND $row_altas3_ags['Altas'] > 0)
								{$efectividad3_ags = round(($row_detalle3_ags['Capacitados'] / $row_altas3_ags['Altas'])*100,0); 
								$pendientes3_ags = $row_altas3_ags['Altas'] - $row_detalle3_ags['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_ags > 100){ $efectividad3_ags = 100;} 
							if ($efectividad3_ags == 100){ $pendientes3_ags = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_ags['Capacitados'] == 0)
								{$efectividad4_ags = '0'; $pendientes4_ags = $row_altas4_ags['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_ags['Capacitados'] > 0 AND $row_altas4_ags['Altas'] == 0)
								{$efectividad4_ags = '100'; $pendientes4_ags = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_ags['Capacitados'] > 0 AND $row_altas4_ags['Altas'] > 0)
								{$efectividad4_ags = round(($row_detalle4_ags['Capacitados'] / $row_altas4_ags['Altas'])*100,0); 
								$pendientes4_ags = $row_altas4_ags['Altas'] - $row_detalle4_ags['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_ags > 100){ $efectividad4_ags = 100;} 
							if ($efectividad4_ags == 100){ $pendientes4_ags = 0;} 
							
if ($row_detalle1_ags['Capacitados'] == 0 AND $row_altas1_ags['Altas'] == 0){ $efectividad1_ags ='100'; $pendientes1_ags =0;}
if ($row_detalle2_ags['Capacitados'] == 0 AND $row_altas2_ags['Altas'] == 0){ $efectividad2_ags ='100'; $pendientes1_ags =0;}
if ($row_detalle3_ags['Capacitados'] == 0 AND $row_altas3_ags['Altas'] == 0){ $efectividad3_ags ='100'; $pendientes1_ags =0;}
if ($row_detalle4_ags['Capacitados'] == 0 AND $row_altas4_ags['Altas'] == 0){ $efectividad4_ags ='100'; $pendientes1_ags =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_ags ='0'; $efectividad2_ags ='0'; $efectividad3_ags ='0'; $efectividad4_ags ='0';}

							$efectividad_total_ags = round(($efectividad1_ags +$efectividad2_ags + $efectividad3_ags + $efectividad4_ags) / 4,0);

							//barrido de meses
							$el_mes = 9;
							$el_mes_desfasado = 8;
							$fecha_desfasada1 = $el_anio."-08-25";
							$fecha_desfasada2 = $el_anio."-09-26";
							
							//TODAS LAS ALTAS
							$query_altas1_sep = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_sep = mysql_query($query_altas1_sep, $vacantes) or die(mysql_error());
							$row_altas1_sep = mysql_fetch_assoc($altas1_sep);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_sep = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_sep = mysql_query($query_altas2_sep, $vacantes) or die(mysql_error());
							$row_altas2_sep = mysql_fetch_assoc($altas2_sep);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_sep = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_sep = mysql_query($query_altas3_sep, $vacantes) or die(mysql_error());
							$row_altas3_sep = mysql_fetch_assoc($altas3_sep);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_sep = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_sep = mysql_query($query_altas4_sep, $vacantes) or die(mysql_error());
							$row_altas4_sep = mysql_fetch_assoc($altas4_sep);

							//CAPACITADOS Institucional 
							$query_detalle1_sep = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_sep = mysql_query($query_detalle1_sep, $vacantes) or die(mysql_error());
							$row_detalle1_sep = mysql_fetch_assoc($detalle1_sep);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_sep = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_sep = mysql_query($query_detalle2_sep, $vacantes) or die(mysql_error());
							$row_detalle2_sep = mysql_fetch_assoc($detalle2_sep);
							
							//CAPACITADOS puesto
							$query_detalle3_sep = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_sep = mysql_query($query_detalle3_sep, $vacantes) or die(mysql_error());
							$row_detalle3_sep = mysql_fetch_assoc($detalle3_sep);

							//CAPACITADOS retroalimentacion
							$query_detalle4_sep = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_sep = mysql_query($query_detalle4_sep, $vacantes) or die(mysql_error());
							$row_detalle4_sep = mysql_fetch_assoc($detalle4_sep);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_sep['Capacitados'] == 0)
								{$efectividad1_sep = '0'; $pendientes1_sep = $row_altas1_sep['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_sep['Capacitados'] > 0 AND $row_altas1_sep['Altas'] == 0)
								{$efectividad1_sep = '100'; $pendientes1_sep = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_sep['Capacitados'] > 0 AND $row_altas1_sep['Altas'] > 0)
								{$efectividad1_sep = round(($row_detalle1_sep['Capacitados'] / $row_altas1_sep['Altas'])*100,0); 
								$pendientes1_sep = $row_altas1_sep['Altas'] - $row_detalle1_sep['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_sep > 100){ $efectividad1_sep = 100;} 
							if ($efectividad1_sep == 100){ $pendientes1_sep = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_sep['Capacitados'] == 0)
								{$efectividad2_sep = '0'; $pendientes2_sep = $row_altas2_sep['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_sep['Capacitados'] > 0 AND $row_altas2_sep['Altas'] == 0)
								{$efectividad2_sep = '100'; $pendientes2_sep = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_sep['Capacitados'] > 0 AND $row_altas2_sep['Altas'] > 0)
								{$efectividad2_sep = round(($row_detalle2_sep['Capacitados'] / $row_altas2_sep['Altas'])*100,0); 
								$pendientes2_sep = $row_altas2_sep['Altas'] - $row_detalle2_sep['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_sep > 100){ $efectividad2_sep = 100;} 
							if ($efectividad2_sep == 100){ $pendientes2_sep = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_sep['Capacitados'] == 0)
								{$efectividad3_sep = '0'; $pendientes3_sep = $row_altas3_sep['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_sep['Capacitados'] > 0 AND $row_altas3_sep['Altas'] == 0)
								{$efectividad3_sep = '100'; $pendientes3_sep = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_sep['Capacitados'] > 0 AND $row_altas3_sep['Altas'] > 0)
								{$efectividad3_sep = round(($row_detalle3_sep['Capacitados'] / $row_altas3_sep['Altas'])*100,0); 
								$pendientes3_sep = $row_altas3_sep['Altas'] - $row_detalle3_sep['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_sep > 100){ $efectividad3_sep = 100;} 
							if ($efectividad3_sep == 100){ $pendientes3_sep = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_sep['Capacitados'] == 0)
								{$efectividad4_sep = '0'; $pendientes4_sep = $row_altas4_sep['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_sep['Capacitados'] > 0 AND $row_altas4_sep['Altas'] == 0)
								{$efectividad4_sep = '100'; $pendientes4_sep = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_sep['Capacitados'] > 0 AND $row_altas4_sep['Altas'] > 0)
								{$efectividad4_sep = round(($row_detalle4_sep['Capacitados'] / $row_altas4_sep['Altas'])*100,0); 
								$pendientes4_sep = $row_altas4_sep['Altas'] - $row_detalle4_sep['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_sep > 100){ $efectividad4_sep = 100;} 
							if ($efectividad4_sep == 100){ $pendientes4_sep = 0;} 
							
if ($row_detalle1_sep['Capacitados'] == 0 AND $row_altas1_sep['Altas'] == 0){ $efectividad1_sep ='100'; $pendientes1_sep =0;}
if ($row_detalle2_sep['Capacitados'] == 0 AND $row_altas2_sep['Altas'] == 0){ $efectividad2_sep ='100'; $pendientes1_sep =0;}
if ($row_detalle3_sep['Capacitados'] == 0 AND $row_altas3_sep['Altas'] == 0){ $efectividad3_sep ='100'; $pendientes1_sep =0;}
if ($row_detalle4_sep['Capacitados'] == 0 AND $row_altas4_sep['Altas'] == 0){ $efectividad4_sep ='100'; $pendientes1_sep =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_sep='0'; $efectividad2_sep ='0'; $efectividad3_sep ='0'; $efectividad4_sep ='0';}

							$efectividad_total_sep = round(($efectividad1_sep +$efectividad2_sep + $efectividad3_sep + $efectividad4_sep) / 4,0);

							//barrido de meses
							$el_mes = 10;
							$el_mes_desfasado = 9;
							$fecha_desfasada1 = $el_anio."-09-25";
							$fecha_desfasada2 = $el_anio."-10-26";
							
							//TODAS LAS ALTAS
							$query_altas1_oct = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_oct = mysql_query($query_altas1_oct, $vacantes) or die(mysql_error());
							$row_altas1_oct = mysql_fetch_assoc($altas1_oct);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_oct = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_oct = mysql_query($query_altas2_oct, $vacantes) or die(mysql_error());
							$row_altas2_oct = mysql_fetch_assoc($altas2_oct);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_oct = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_oct = mysql_query($query_altas3_oct, $vacantes) or die(mysql_error());
							$row_altas3_oct = mysql_fetch_assoc($altas3_oct);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_oct = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_oct = mysql_query($query_altas4_oct, $vacantes) or die(mysql_error());
							$row_altas4_oct = mysql_fetch_assoc($altas4_oct);

							//CAPACITADOS Institucional 
							$query_detalle1_oct = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_oct = mysql_query($query_detalle1_oct, $vacantes) or die(mysql_error());
							$row_detalle1_oct = mysql_fetch_assoc($detalle1_oct);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_oct = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_oct = mysql_query($query_detalle2_oct, $vacantes) or die(mysql_error());
							$row_detalle2_oct = mysql_fetch_assoc($detalle2_oct);
							
							//CAPACITADOS puesto
							$query_detalle3_oct = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_oct = mysql_query($query_detalle3_oct, $vacantes) or die(mysql_error());
							$row_detalle3_oct = mysql_fetch_assoc($detalle3_oct);

							//CAPACITADOS retroalimentacion
							$query_detalle4_oct = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_oct = mysql_query($query_detalle4_oct, $vacantes) or die(mysql_error());
							$row_detalle4_oct = mysql_fetch_assoc($detalle4_oct);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_oct['Capacitados'] == 0)
								{$efectividad1_oct = '0'; $pendientes1_oct = $row_altas1_oct['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_oct['Capacitados'] > 0 AND $row_altas1_oct['Altas'] == 0)
								{$efectividad1_oct = '100'; $pendientes1_oct = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_oct['Capacitados'] > 0 AND $row_altas1_oct['Altas'] > 0)
								{$efectividad1_oct = round(($row_detalle1_oct['Capacitados'] / $row_altas1_oct['Altas'])*100,0); 
								$pendientes1_oct = $row_altas1_oct['Altas'] - $row_detalle1_oct['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_oct > 100){ $efectividad1_oct = 100;} 
							if ($efectividad1_oct == 100){ $pendientes1_oct = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_oct['Capacitados'] == 0)
								{$efectividad2_oct = '0'; $pendientes2_oct = $row_altas2_oct['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_oct['Capacitados'] > 0 AND $row_altas2_oct['Altas'] == 0)
								{$efectividad2_oct = '100'; $pendientes2_oct = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_oct['Capacitados'] > 0 AND $row_altas2_oct['Altas'] > 0)
								{$efectividad2_oct = round(($row_detalle2_oct['Capacitados'] / $row_altas2_oct['Altas'])*100,0); 
								$pendientes2_oct = $row_altas2_oct['Altas'] - $row_detalle2_oct['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_oct > 100){ $efectividad2_oct = 100;} 
							if ($efectividad2_oct == 100){ $pendientes2_oct = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_oct['Capacitados'] == 0)
								{$efectividad3_oct = '0'; $pendientes3_oct = $row_altas3_oct['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_oct['Capacitados'] > 0 AND $row_altas3_oct['Altas'] == 0)
								{$efectividad3_oct = '100'; $pendientes3_oct = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_oct['Capacitados'] > 0 AND $row_altas3_oct['Altas'] > 0)
								{$efectividad3_oct = round(($row_detalle3_oct['Capacitados'] / $row_altas3_oct['Altas'])*100,0); 
								$pendientes3_oct = $row_altas3_oct['Altas'] - $row_detalle3_oct['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_oct > 100){ $efectividad3_oct = 100;} 
							if ($efectividad3_oct == 100){ $pendientes3_oct = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_oct['Capacitados'] == 0)
								{$efectividad4_oct = '0'; $pendientes4_oct = $row_altas4_oct['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_oct['Capacitados'] > 0 AND $row_altas4_oct['Altas'] == 0)
								{$efectividad4_oct = '100'; $pendientes4_oct = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_oct['Capacitados'] > 0 AND $row_altas4_oct['Altas'] > 0)
								{$efectividad4_oct = round(($row_detalle4_oct['Capacitados'] / $row_altas4_oct['Altas'])*100,0); 
								$pendientes4_oct = $row_altas4_oct['Altas'] - $row_detalle4_oct['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_oct > 100){ $efectividad4_oct = 100;} 
							if ($efectividad4_oct == 100){ $pendientes4_oct = 0;} 
							
if ($row_detalle1_oct['Capacitados'] == 0 AND $row_altas1_oct['Altas'] == 0){ $efectividad1_oct ='100'; $pendientes1_oct =0;}
if ($row_detalle2_oct['Capacitados'] == 0 AND $row_altas2_oct['Altas'] == 0){ $efectividad2_oct ='100'; $pendientes1_oct =0;}
if ($row_detalle3_oct['Capacitados'] == 0 AND $row_altas3_oct['Altas'] == 0){ $efectividad3_oct ='100'; $pendientes1_oct =0;}
if ($row_detalle4_oct['Capacitados'] == 0 AND $row_altas4_oct['Altas'] == 0){ $efectividad4_oct ='100'; $pendientes1_oct =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_oct ='0'; $efectividad2_oct ='0'; $efectividad3_oct ='0'; $efectividad4_oct ='0';}

							$efectividad_total_oct = round(($efectividad1_oct +$efectividad2_oct + $efectividad3_oct + $efectividad4_oct) / 4,0);

							//barrido de meses
							$el_mes = 11;
							$el_mes_desfasado = 10;
							$fecha_desfasada1 = $el_anio."-10-25";
							$fecha_desfasada2 = $el_anio."-11-26";
							
							//TODAS LAS ALTAS
							$query_altas1_nov = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_nov = mysql_query($query_altas1_nov, $vacantes) or die(mysql_error());
							$row_altas1_nov = mysql_fetch_assoc($altas1_nov);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_nov = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_nov = mysql_query($query_altas2_nov, $vacantes) or die(mysql_error());
							$row_altas2_nov = mysql_fetch_assoc($altas2_nov);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_nov = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_nov = mysql_query($query_altas3_nov, $vacantes) or die(mysql_error());
							$row_altas3_nov = mysql_fetch_assoc($altas3_nov);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_nov = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_nov = mysql_query($query_altas4_nov, $vacantes) or die(mysql_error());
							$row_altas4_nov = mysql_fetch_assoc($altas4_nov);

							//CAPACITADOS Institucional 
							$query_detalle1_nov = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_nov = mysql_query($query_detalle1_nov, $vacantes) or die(mysql_error());
							$row_detalle1_nov = mysql_fetch_assoc($detalle1_nov);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_nov = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_nov = mysql_query($query_detalle2_nov, $vacantes) or die(mysql_error());
							$row_detalle2_nov = mysql_fetch_assoc($detalle2_nov);
							
							//CAPACITADOS puesto
							$query_detalle3_nov = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_nov = mysql_query($query_detalle3_nov, $vacantes) or die(mysql_error());
							$row_detalle3_nov = mysql_fetch_assoc($detalle3_nov);

							//CAPACITADOS retroalimentacion
							$query_detalle4_nov = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_nov = mysql_query($query_detalle4_nov, $vacantes) or die(mysql_error());
							$row_detalle4_nov = mysql_fetch_assoc($detalle4_nov);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_nov['Capacitados'] == 0)
								{$efectividad1_nov = '0'; $pendientes1_nov = $row_altas1_nov['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_nov['Capacitados'] > 0 AND $row_altas1_nov['Altas'] == 0)
								{$efectividad1_nov = '100'; $pendientes1_nov = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_nov['Capacitados'] > 0 AND $row_altas1_nov['Altas'] > 0)
								{$efectividad1_nov = round(($row_detalle1_nov['Capacitados'] / $row_altas1_nov['Altas'])*100,0); 
								$pendientes1_nov = $row_altas1_nov['Altas'] - $row_detalle1_nov['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_nov > 100){ $efectividad1_nov = 100;} 
							if ($efectividad1_nov == 100){ $pendientes1_nov = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_nov['Capacitados'] == 0)
								{$efectividad2_nov = '0'; $pendientes2_nov = $row_altas2_nov['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_nov['Capacitados'] > 0 AND $row_altas2_nov['Altas'] == 0)
								{$efectividad2_nov = '100'; $pendientes2_nov = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_nov['Capacitados'] > 0 AND $row_altas2_nov['Altas'] > 0)
								{$efectividad2_nov = round(($row_detalle2_nov['Capacitados'] / $row_altas2_nov['Altas'])*100,0); 
								$pendientes2_nov = $row_altas2_nov['Altas'] - $row_detalle2_nov['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_nov > 100){ $efectividad2_nov = 100;} 
							if ($efectividad2_nov == 100){ $pendientes2_nov = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_nov['Capacitados'] == 0)
								{$efectividad3_nov = '0'; $pendientes3_nov = $row_altas3_nov['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_nov['Capacitados'] > 0 AND $row_altas3_nov['Altas'] == 0)
								{$efectividad3_nov = '100'; $pendientes3_nov = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_nov['Capacitados'] > 0 AND $row_altas3_nov['Altas'] > 0)
								{$efectividad3_nov = round(($row_detalle3_nov['Capacitados'] / $row_altas3_nov['Altas'])*100,0); 
								$pendientes3_nov = $row_altas3_nov['Altas'] - $row_detalle3_nov['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_nov > 100){ $efectividad3_nov = 100;} 
							if ($efectividad3_nov == 100){ $pendientes3_nov = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_nov['Capacitados'] == 0)
								{$efectividad4_nov = '0'; $pendientes4_nov = $row_altas4_nov['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_nov['Capacitados'] > 0 AND $row_altas4_nov['Altas'] == 0)
								{$efectividad4_nov = '100'; $pendientes4_nov = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_nov['Capacitados'] > 0 AND $row_altas4_nov['Altas'] > 0)
								{$efectividad4_nov = round(($row_detalle4_nov['Capacitados'] / $row_altas4_nov['Altas'])*100,0); 
								$pendientes4_nov = $row_altas4_nov['Altas'] - $row_detalle4_nov['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_nov > 100){ $efectividad4_nov = 100;} 
							if ($efectividad4_nov == 100){ $pendientes4_nov = 0;} 
							
if ($row_detalle1_nov['Capacitados'] == 0 AND $row_altas1_nov['Altas'] == 0){ $efectividad1_nov ='100'; $pendientes1_nov =0;}
if ($row_detalle2_nov['Capacitados'] == 0 AND $row_altas2_nov['Altas'] == 0){ $efectividad2_nov ='100'; $pendientes1_nov =0;}
if ($row_detalle3_nov['Capacitados'] == 0 AND $row_altas3_nov['Altas'] == 0){ $efectividad3_nov ='100'; $pendientes1_nov =0;}
if ($row_detalle4_nov['Capacitados'] == 0 AND $row_altas4_nov['Altas'] == 0){ $efectividad4_nov ='100'; $pendientes1_nov =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_nov ='0'; $efectividad2_nov ='0'; $efectividad3_nov ='0'; $efectividad4_nov ='0';}
							
							$efectividad_total_nov = round(($efectividad1_nov +$efectividad2_nov + $efectividad3_nov + $efectividad4_nov) / 4,0);

							//barrido de meses
							$el_mes = 12;
							$el_mes_desfasado = 11;
							$fecha_desfasada1 = $el_anio."-11-25";
							$fecha_desfasada2 = $el_anio."-12-26";
							
							//TODAS LAS ALTAS
							$query_altas1_dic = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1_dic = mysql_query($query_altas1_dic, $vacantes) or die(mysql_error());
							$row_altas1_dic = mysql_fetch_assoc($altas1_dic);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR)
							$query_altas2_dic = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2_dic = mysql_query($query_altas2_dic, $vacantes) or die(mysql_error());
							$row_altas2_dic = mysql_fetch_assoc($altas2_dic);

							//ALMACEN, DISTRIBUCIÓN
							$query_altas3_dic = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3_dic = mysql_query($query_altas3_dic, $vacantes) or die(mysql_error());
							$row_altas3_dic = mysql_fetch_assoc($altas3_dic);

							//TODAS LAS ALTAS RETROALIMENTACION
							$query_altas4_dic = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4_dic = mysql_query($query_altas4_dic, $vacantes) or die(mysql_error());
							$row_altas4_dic = mysql_fetch_assoc($altas4_dic);

							//CAPACITADOS Institucional 
							$query_detalle1_dic = "SELECT Count( capa_avance.IDC_capa ) AS Capacitados FROM capa_avance WHERE MONTH ( capa_avance.fecha_evento ) = '$el_mes' AND YEAR ( capa_avance.fecha_evento ) = '$el_anio' AND MONTH ( capa_avance.fecha_antiguedad ) = '$el_mes' AND YEAR ( capa_avance.fecha_antiguedad ) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos IN (1, 185)"; 
							$detalle1_dic = mysql_query($query_detalle1_dic, $vacantes) or die(mysql_error());
							$row_detalle1_dic = mysql_fetch_assoc($detalle1_dic);
							
							//CAPACITADOS entrenamiento
							$query_detalle2_dic = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2_dic = mysql_query($query_detalle2_dic, $vacantes) or die(mysql_error());
							$row_detalle2_dic = mysql_fetch_assoc($detalle2_dic);
							
							//CAPACITADOS puesto
							$query_detalle3_dic = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3_dic = mysql_query($query_detalle3_dic, $vacantes) or die(mysql_error());
							$row_detalle3_dic = mysql_fetch_assoc($detalle3_dic);

							//CAPACITADOS retroalimentacion
							$query_detalle4_dic = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4_dic = mysql_query($query_detalle4_dic, $vacantes) or die(mysql_error());
							$row_detalle4_dic = mysql_fetch_assoc($detalle4_dic);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1_dic['Capacitados'] == 0)
								{$efectividad1_dic = '0'; $pendientes1_dic = $row_altas1_dic['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1_dic['Capacitados'] > 0 AND $row_altas1_dic['Altas'] == 0)
								{$efectividad1_dic = '100'; $pendientes1_dic = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1_dic['Capacitados'] > 0 AND $row_altas1_dic['Altas'] > 0)
								{$efectividad1_dic = round(($row_detalle1_dic['Capacitados'] / $row_altas1_dic['Altas'])*100,0); 
								$pendientes1_dic = $row_altas1_dic['Altas'] - $row_detalle1_dic['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1_dic > 100){ $efectividad1_dic = 100;} 
							if ($efectividad1_dic == 100){ $pendientes1_dic = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2_dic['Capacitados'] == 0)
								{$efectividad2_dic = '0'; $pendientes2_dic = $row_altas2_dic['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2_dic['Capacitados'] > 0 AND $row_altas2_dic['Altas'] == 0)
								{$efectividad2_dic = '100'; $pendientes2_dic = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2_dic['Capacitados'] > 0 AND $row_altas2_dic['Altas'] > 0)
								{$efectividad2_dic = round(($row_detalle2_dic['Capacitados'] / $row_altas2_dic['Altas'])*100,0); 
								$pendientes2_dic = $row_altas2_dic['Altas'] - $row_detalle2_dic['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2_dic > 100){ $efectividad2_dic = 100;} 
							if ($efectividad2_dic == 100){ $pendientes2_dic = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3_dic['Capacitados'] == 0)
								{$efectividad3_dic = '0'; $pendientes3_dic = $row_altas3_dic['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3_dic['Capacitados'] > 0 AND $row_altas3_dic['Altas'] == 0)
								{$efectividad3_dic = '100'; $pendientes3_dic = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3_dic['Capacitados'] > 0 AND $row_altas3_dic['Altas'] > 0)
								{$efectividad3_dic = round(($row_detalle3_dic['Capacitados'] / $row_altas3_dic['Altas'])*100,0); 
								$pendientes3_dic = $row_altas3_dic['Altas'] - $row_detalle3_dic['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3_dic > 100){ $efectividad3_dic = 100;} 
							if ($efectividad3_dic == 100){ $pendientes3_dic = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4_dic['Capacitados'] == 0)
								{$efectividad4_dic = '0'; $pendientes4_dic = $row_altas4_dic['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4_dic['Capacitados'] > 0 AND $row_altas4_dic['Altas'] == 0)
								{$efectividad4_dic = '100'; $pendientes4_dic = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4_dic['Capacitados'] > 0 AND $row_altas4_dic['Altas'] > 0)
								{$efectividad4_dic = round(($row_detalle4_dic['Capacitados'] / $row_altas4_dic['Altas'])*100,0); 
								$pendientes4_dic = $row_altas4_dic['Altas'] - $row_detalle4_dic['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4_dic > 100){ $efectividad4_dic = 100;} 
							if ($efectividad4_dic == 100){ $pendientes4_dic = 0;} 
							
if ($row_detalle1_dic['Capacitados'] == 0 AND $row_altas1_dic['Altas'] == 0){ $efectividad1_dic ='100'; $pendientes1_dic =0;}
if ($row_detalle2_dic['Capacitados'] == 0 AND $row_altas2_dic['Altas'] == 0){ $efectividad2_dic ='100'; $pendientes1_dic =0;}
if ($row_detalle3_dic['Capacitados'] == 0 AND $row_altas3_dic['Altas'] == 0){ $efectividad3_dic ='100'; $pendientes1_dic =0;}
if ($row_detalle4_dic['Capacitados'] == 0 AND $row_altas4_dic['Altas'] == 0){ $efectividad4_dic ='100'; $pendientes1_dic =0;}
if ($fecha_mes < $el_mes+1 AND $el_anio == $fecha_anio) { $efectividad1_dic ='0'; $efectividad2_dic ='0'; $efectividad3_dic ='0'; $efectividad4_dic ='0';}

							$efectividad_total_dic = round(($efectividad1_dic +$efectividad2_dic + $efectividad3_dic + $efectividad4_dic) / 4,0);

							$cuenta = 0;
							if ($efectividad_total_ene != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_feb != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_mar != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_abr != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_may != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_jun != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_jul != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_ags != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_sep != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_oct != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_nov != 0) {$cuenta = $cuenta + 1;}
							if ($efectividad_total_dic != 0) {$cuenta = $cuenta + 1;}
							
							if ($cuenta == 0) {$cuenta = 1;}
								
							$efectividad_total = round(($efectividad_total_ene + $efectividad_total_feb + $efectividad_total_mar + $efectividad_total_abr + $efectividad_total_may +$efectividad_total_jun + $efectividad_total_jul + $efectividad_total_ags + $efectividad_total_sep + $efectividad_total_oct + $efectividad_total_nov + $efectividad_total_dic)/$cuenta,0);
							
								 if ($row_amatriz['region_op'] == 1) {$norte = $norte + $efectividad_total;} 
							else if ($row_amatriz['region_op'] == 2) {$centro = $centro + $efectividad_total;} 
							else if ($row_amatriz['region_op'] == 3) {$sur = $sur + $efectividad_total;} 

						?>
					  <tr>
						<td>
						<b>
						<?php    if ($row_amatriz['region_op'] == 1) {echo 'Norte';} 
							else if ($row_amatriz['region_op'] == 2) {echo 'Centro';} 
							else if ($row_amatriz['region_op'] == 3) {echo 'Sur';} 
							else {echo "-";} ?>
						</b>
						</td>
						<td><b><?php echo $row_amatriz['matriz']; ?></b></td>
						<td class="text-center"><?php echo $efectividad_total_ene; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_feb; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_mar; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_abr; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_may; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_jun; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_jul; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_ags; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_sep; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_oct; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_nov; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total_dic; ?>%</td>
						<td class="text-center"><?php echo $efectividad_total; ?>%</td>
					  </tr>
					 <?php } while ($row_amatriz = mysql_fetch_assoc($amatriz)); ?>					  
					</tbody>
					</table>
				</div>	
					
					
					<table>
					  <tr>
						<td>Efectividad Norte: <b><?php echo round($norte / $totalRows_sucsnte,0);?>%</b></td>
					  </tr>
					  <tr>
						<td>Efectividad Centro: <b><?php echo round($centro / $totalRows_sucscen,0);?>%</b></td>
					  </tr>
					  <tr>
						<td>Efectividad Sur: <b><?php echo round($sur / $totalRows_sucssur,0);?>%</b></td>
					  </tr>
					</table>

					
				</div>	
					
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

