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
$fecha_mes = date("m")-1;

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
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if (isset($_POST['el_anio'])) {$_SESSION['el_anio'] = $_POST['el_anio'];} 
if (!isset($_SESSION['el_anio'])) { $_SESSION['el_anio'] = 2024;}

if (isset($_POST['el_mes'])) {$_SESSION['el_mes'] = $_POST['el_mes'];} 
if (!isset($_SESSION['el_mes'])) { $_SESSION['el_mes'] = $fecha_mes;}

$el_anio = $_SESSION['el_anio']; 

$el_mes = $_SESSION['el_mes']; 
$el_mes_desfasado = $el_mes - 1;
$el_anio2 = 0;

if ($el_mes == 1) {
$el_mes_desfasado = 12;
$el_anio2 = $el_anio - 1;
$fecha_desfasada1 = $el_anio2."-".$el_mes_desfasado."-25";
$fecha_desfasada2 = $el_anio."-".$el_mes."-26";
} else {
$fecha_desfasada1 = $el_anio."-".$el_mes_desfasado."-25";
$fecha_desfasada2 = $el_anio."-".$el_mes."-26";
}

//echo $el_mes_desfasado." ";
//echo $el_anio2." ";
//echo $fecha_desfasada1." ";
//echo $fecha_desfasada2;

$query_aarea = "SELECT * FROM vac_areas";
$aarea = mysql_query($query_aarea, $vacantes) or die(mysql_error());
$row_aarea = mysql_fetch_assoc($aarea);

$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);
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
                                    <p>Selecciona el mes y el año en el filtro.</p>


                <form method="POST" action="capa_reporte_1.php">
					<table class="table">
							<tr>
                           <td> Año: <select name="el_anio" class="form-control">
                                           <option value="2023"<?php if ($el_anio == 2023) {echo "selected=\"selected\"";} ?>>2023</option>
                                           <option value="2022"<?php if ($el_anio == 2022) {echo "selected=\"selected\"";} ?>>2022</option>
                                           <option value="2021"<?php if ($el_anio == 2021) {echo "selected=\"selected\"";} ?>>2021</option>
									 </select>
                            </td>
                           <td> Mes: <select name="el_mes" class="form-control">
                                           <option value="1"<?php if ($el_mes == 1) {echo "selected=\"selected\"";} ?>>Enero</option>
                                           <option value="2"<?php if ($el_mes == 2) {echo "selected=\"selected\"";} ?>>Febrero</option>
                                           <option value="3"<?php if ($el_mes == 3) {echo "selected=\"selected\"";} ?>>Marzo</option>
                                           <option value="4"<?php if ($el_mes == 4) {echo "selected=\"selected\"";} ?>>Abril</option>
                                           <option value="5"<?php if ($el_mes == 5) {echo "selected=\"selected\"";} ?>>Mayo</option>
                                           <option value="6"<?php if ($el_mes == 6) {echo "selected=\"selected\"";} ?>>Junio</option>
                                           <option value="7"<?php if ($el_mes == 7) {echo "selected=\"selected\"";} ?>>Julio</option>
                                           <option value="8"<?php if ($el_mes == 8) {echo "selected=\"selected\"";} ?>>Agosto</option>
                                           <option value="9"<?php if ($el_mes == 9) {echo "selected=\"selected\"";} ?>>Septiembre</option>
                                           <option value="10"<?php if ($el_mes == 10) {echo "selected=\"selected\"";} ?>>Octubre</option>
                                           <option value="11"<?php if ($el_mes == 11) {echo "selected=\"selected\"";} ?>>Noviembre</option>
                                           <option value="12"<?php if ($el_mes == 12) {echo "selected=\"selected\"";} ?>>Diciembre</option>
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
						<td class="bg-danger text-center"  width="10%">Sucursal</td>
						<td class="bg-primary text-center" width="15%">Evento</td>
						<td class="bg-primary text-center" width="15%">Ingresos Aplicables</td>
						<td class="bg-primary text-center" width="15%">Número Capacitados</td>
						<td class="bg-primary text-center" width="15%">% Efectividad</td>
						<td class="bg-primary text-center" width="15%">Por Capacitar</td>
						<td class="bg-primary text-center" width="15%">Efectividad</td>
					  </tr>
                    </thead>
                    <tbody>
						<?php  do { 
							$IDmatriz = $row_amatriz['IDmatriz'];
							
							//TODAS LAS ALTAS Institucional
							$query_altas1 = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas1 = mysql_query($query_altas1, $vacantes) or die(mysql_error());
							$row_altas1 = mysql_fetch_assoc($altas1);

							//ALMACEN, DISTRIBUCIÓN Y VENTAS (NO MOSTRADOR) puesto
							$query_altas2 = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1,2) AND ind_bajas.excluye_antiguedad = 1";  
							$altas2 = mysql_query($query_altas2, $vacantes) or die(mysql_error());
							$row_altas2 = mysql_fetch_assoc($altas2); //echo $query_altas2."<br />";

							//ALMACEN, DISTRIBUCIÓN entrenamiento
							$query_altas3 = "SELECT Count(ind_bajas.IDempleado) AS Altas, vac_puestos.IDarea_capa FROM ind_bajas LEFT JOIN vac_puestos ON  ind_bajas.IDpuesto = vac_puestos.IDpuesto WHERE MONTH(ind_bajas.fecha_antiguedad) = '$el_mes' AND YEAR(ind_bajas.fecha_antiguedad) = '$el_anio' AND ind_bajas.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa IN (1) AND ind_bajas.excluye_antiguedad = 1"; 
							$altas3 = mysql_query($query_altas3, $vacantes) or die(mysql_error());
							$row_altas3 = mysql_fetch_assoc($altas3);

							//TODAS LAS ALTAS retroalimentacion
							$query_altas4 = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad > '$fecha_desfasada1' AND  ind_bajas.fecha_antiguedad < '$fecha_desfasada2' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1"; 
							$altas4 = mysql_query($query_altas4, $vacantes) or die(mysql_error());
							$row_altas4 = mysql_fetch_assoc($altas4); 

							//CAPACITADOS Institucional 
							$query_detalle1 = "SELECT Count(capa_avance.IDC_capa) AS Capacitados FROM capa_avance WHERE MONTH(capa_avance.fecha_antiguedad) = '$el_mes' AND YEAR(capa_avance.fecha_antiguedad) = '$el_anio' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (1,185)"; 
							$detalle1 = mysql_query($query_detalle1, $vacantes) or die(mysql_error());
							$row_detalle1 = mysql_fetch_assoc($detalle1);
							
							//CAPACITADOS puesto
							$query_detalle2 = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto 	LEFT JOIN ind_bajas ON capa_avance.IDempleado = ind_bajas.IDempleado WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' AND ind_bajas.excluye_antiguedad = 1 AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1,2) AND capa_avance.IDC_capa_cursos in (3)"; 
							$detalle2 = mysql_query($query_detalle2, $vacantes) or die(mysql_error());
							$row_detalle2 = mysql_fetch_assoc($detalle2); //echo $query_detalle2;
							
							//CAPACITADOS entrenamiento
							$query_detalle3 = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto 	LEFT JOIN ind_bajas ON capa_avance.IDempleado = ind_bajas.IDempleado WHERE MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio' 	AND ind_bajas.excluye_antiguedad = 1 AND capa_avance.IDmatriz = '$IDmatriz' AND vac_puestos.IDarea_capa in (1) AND capa_avance.IDC_capa_cursos in (4)"; 
							$detalle3 = mysql_query($query_detalle3, $vacantes) or die(mysql_error());
							$row_detalle3 = mysql_fetch_assoc($detalle3);

							//CAPACITADOS retroalimentacion
							$query_detalle4 = "SELECT Count(capa_avance.IDempleado) AS Capacitados, vac_puestos.IDarea_capa FROM capa_avance LEFT JOIN vac_puestos ON  capa_avance.IDpuesto = vac_puestos.IDpuesto WHERE capa_avance.fecha_antiguedad > '$fecha_desfasada1' AND capa_avance.fecha_antiguedad < '$fecha_desfasada2' AND capa_avance.IDmatriz = '$IDmatriz' AND capa_avance.IDC_capa_cursos in (182)"; 
							$detalle4 = mysql_query($query_detalle4, $vacantes) or die(mysql_error());
							$row_detalle4 = mysql_fetch_assoc($detalle4);


							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle1['Capacitados'] == 0)
								{$efectividad1 = '0'; $pendientes1 = $row_altas1['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1['Capacitados'] > 0 AND $row_altas1['Altas'] == 0)
								{$efectividad1 = '100'; $pendientes1 = 0;}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle1['Capacitados'] == 0 AND $row_altas1['Altas'] == 0)
								{$efectividad1 = '100'; $pendientes1 = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle1['Capacitados'] > 0 AND $row_altas1['Altas'] > 0)
								{$efectividad1 = round(($row_detalle1['Capacitados'] / $row_altas1['Altas'])*100,0); 
								$pendientes1 = $row_altas1['Altas'] - $row_detalle1['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad1 > 100){ $efectividad1 = 100;} 
							if ($efectividad1 == 100){ $pendientes1 = 0;} 						

							//Efectividad , empresa y entrenamiento							
							// si no se capacita a nadie
							if ($row_detalle2['Capacitados'] == 0)
								{$efectividad2 = '0'; $pendientes2 = $row_altas2['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2['Capacitados'] > 0 AND $row_altas2['Altas'] == 0)
								{$efectividad2 = '100'; $pendientes2 = 0;}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle2['Capacitados'] == 0 AND $row_altas2['Altas'] == 0)
								{$efectividad2 = '100'; $pendientes2 = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle2['Capacitados'] > 0 AND $row_altas2['Altas'] > 0)
								{$efectividad2 = round(($row_detalle2['Capacitados'] / $row_altas2['Altas'])*100,0); 
								$pendientes2 = $row_altas2['Altas'] - $row_detalle2['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad2 > 100){ $efectividad2 = 100;} 
							if ($efectividad2 == 100){ $pendientes2 = 0;} 						

							//Efectividad Institucional							
							// si no se capacita a nadie
							if ($row_detalle3['Capacitados'] == 0)
								{$efectividad3 = '0'; $pendientes3 = $row_altas3['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3['Capacitados'] > 0 AND $row_altas3['Altas'] == 0)
								{$efectividad3 = '100'; $pendientes3 = 0;}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle3['Capacitados'] == 0 AND $row_altas3['Altas'] == 0)
								{$efectividad3 = '100'; $pendientes3 = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle3['Capacitados'] > 0 AND $row_altas3['Altas'] > 0)
								{$efectividad3 = round(($row_detalle3['Capacitados'] / $row_altas3['Altas'])*100,0); 
								$pendientes3 = $row_altas3['Altas'] - $row_detalle3['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad3 > 100){ $efectividad3 = 100;} 
							if ($efectividad3 == 100){ $pendientes3 = 0;} 						
							
							//Efectividad retroalimentacion							
							// si no se capacita a nadie
							if ($row_detalle4['Capacitados'] == 0)
								{$efectividad4 = '0'; $pendientes4 = $row_altas4['Altas'];}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4['Capacitados'] > 0 AND $row_altas4['Altas'] == 0)
								{$efectividad4 = '100'; $pendientes4 = 0;}
							// si se capacita a alguen pero no hay altas
							if ($row_detalle4['Capacitados'] == 0 AND $row_altas4['Altas'] == 0)
								{$efectividad4 = '100'; $pendientes4 = 0;}
							// si se capacita a alguen y hay altas													
							if ($row_detalle4['Capacitados'] > 0 AND $row_altas4['Altas'] > 0)
								{$efectividad4 = round(($row_detalle4['Capacitados'] / $row_altas4['Altas'])*100,0); 
								$pendientes4 = $row_altas4['Altas'] - $row_detalle4['Capacitados']; }	
							// limpiamos si es mayor a 100
							if ($efectividad4 > 100){ $efectividad4 = 100;} 
							if ($efectividad4 == 100){ $pendientes4 = 0;} 
							
							$efectividad_total = round(($efectividad1 + $efectividad2 + $efectividad3 + $efectividad4) / 4,0);
							
							
						?>
					  <tr  class="border-double border-solid">
						<td rowspan="4"><b><?php echo $row_amatriz['matriz']; ?></b></td>
						<td>Institucional</td>
						<td class="text-center"><?php echo $row_altas1['Altas']; ?></td>
						<td class="text-center"><?php echo $row_detalle1['Capacitados']; ?></td>
						<td class="text-center"><?php echo $efectividad1; ?>%</td>
						<td class="text-center"><?php echo $pendientes1; ?></td>
						<td rowspan="4" class="text-center"><?php echo $efectividad_total; ?>%</td>
					  </tr>
					  <tr>
						<td>Puesto</td>
						<td class="text-center"><?php echo $row_altas2['Altas']; ?></td>
						<td class="text-center"><?php echo $row_detalle2['Capacitados']; ?></td>
						<td class="text-center"><?php echo $efectividad2; ?>%</td>
						<td class="text-center"><?php echo $pendientes2; ?></td>
					  </tr>
					  <tr>
						<td>Entrenamiento</td>
						<td class="text-center"><?php echo $row_altas3['Altas']; ?></td>
						<td class="text-center"><?php echo $row_detalle3['Capacitados']; ?></td>
						<td class="text-center"><?php echo $efectividad3; ?>%</td>
						<td class="text-center"><?php echo $pendientes3; ?></td>
					  </tr>
					  <tr>
						<td>Retroalimentación</td>
						<td class="text-center"><?php echo $row_altas4['Altas']; ?></td>
						<td class="text-center"><?php echo $row_detalle4['Capacitados']; ?></td>
						<td class="text-center"><?php echo $efectividad4; ?>%</td>
						<td class="text-center"><?php echo $pendientes4; ?></td>
					  </tr>
					 <?php } while ($row_amatriz = mysql_fetch_assoc($amatriz)); ?>					  
                    </tbody>
					</table>
				</div>	
					
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

