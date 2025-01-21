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

if (isset($_POST["filtro"])) {

$IDCcurso = $_POST["IDCcurso"];
$filtro = 1;
$query_elevento = "SELECT * FROM capa_eventos_grupo WHERE IDCcurso = $IDCcurso";
$elevento  = mysql_query($query_elevento, $vacantes) or die(mysql_error());
$row_elevento  = mysql_fetch_assoc($elevento);
$totalRows_elevento  = mysql_num_rows($elevento);

$query_todoslospuestos = "SELECT * FROM capa_eventos_grupo_puestos WHERE IDCcurso = $IDCcurso";
$todoslospuestos  = mysql_query($query_todoslospuestos, $vacantes) or die(mysql_error());
$row_todoslospuestos  = mysql_fetch_assoc($todoslospuestos);
$totalRows_todoslospuestos  = mysql_num_rows($todoslospuestos);

$query_puestos_aplicables = "SELECT capa_eventos_grupo_puestos.IDCcurso, GROUP_CONCAT(capa_eventos_grupo_puestos.IDpuesto SEPARATOR ',') AS IDpuesto,  GROUP_CONCAT(vac_puestos.denominacion SEPARATOR ', ') AS denominacion FROM capa_eventos_grupo_puestos LEFT JOIN vac_puestos ON  capa_eventos_grupo_puestos.IDpuesto = vac_puestos.IDpuesto WHERE IDCcurso = $IDCcurso"; 
$puestos_aplicables = mysql_query($query_puestos_aplicables, $vacantes) or die(mysql_error());
$row_puestos_aplicables = mysql_fetch_assoc($puestos_aplicables);
$totalRows_puestos_aplicables = mysql_num_rows($puestos_aplicables);

$query_cursos_aplicables = "SELECT capa_eventos_grupo_cursos.IDCcurso, GROUP_CONCAT( capa_eventos_grupo_cursos.IDC_capa_cursos SEPARATOR ',' ) AS IDC_capa_cursos, GROUP_CONCAT( capa_cursos.nombre_curso SEPARATOR ', ' ) AS curso FROM capa_eventos_grupo_cursos LEFT JOIN capa_cursos ON capa_eventos_grupo_cursos.IDC_capa_cursos = capa_cursos.IDC_capa_cursos WHERE IDCcurso = $IDCcurso";
$cursos_aplicables = mysql_query($query_cursos_aplicables, $vacantes) or die(mysql_error());
$row_cursos_aplicables = mysql_fetch_assoc($cursos_aplicables);
$totalRows_cursos_aplicables = mysql_num_rows($cursos_aplicables);

$fecha_inicio = $row_elevento["fecha_inicio"]; 
$fecha_fin = $row_elevento["fecha_fin"]; 
$el_evento = $row_elevento["evento"];  
$puestos = $row_puestos_aplicables['IDpuesto']."0 ";
$IDC_capa_cursos = $row_cursos_aplicables['IDC_capa_cursos']; 
$IDtipo = $row_elevento['IDtipo'];
$filtrador = 0;
	 if($IDtipo == 1) {$filtrador = " capa_avance.fecha_antiguedad BETWEEN '$fecha_inicio' AND '$fecha_fin'"; }
else if($IDtipo == 2) {$filtrador = " capa_avance.fecha_evento BETWEEN '$fecha_inicio' AND '$fecha_fin'"; }
else if($IDtipo == 3) {$filtrador = " capa_avance.fecha_antiguedad BETWEEN '$fecha_inicio' AND '$fecha_fin' AND capa_avance.fecha_evento BETWEEN '$fecha_inicio' AND '$fecha_fin'";}

} else  {

$IDCcurso = 0;
$filtro = 0;
$fecha_inicio = 0;
$fecha_fin = 0;
$puestos = 0;
$IDC_capa_cursos = 0;
$IDtipo = 0;
$filtrador = 0;

 } 

$query_aarea = "SELECT * FROM vac_areas";
$aarea = mysql_query($query_aarea, $vacantes) or die(mysql_error());
$row_aarea = mysql_fetch_assoc($aarea);

$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) AND region_op > 0";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

$query_reportes = "SELECT * FROM capa_eventos_grupo ORDER BY evento ASC";
$reportes = mysql_query($query_reportes, $vacantes) or die(mysql_error());
$row_reportes = mysql_fetch_assoc($reportes);
$totalRows_reportes = mysql_num_rows($reportes);


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
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/2picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5_capa.js"></script>

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
						<p>Selecciona los datos para el filtro.</p>


                <form method="POST" action="capa_reporte_3.php">
					<table class="table">
							<tr>
                           <td>
							<select name="IDCcurso" id="IDCcurso" class="form-control" required="required">
								<option value="">Seleccione una opción</option> 
									  <?php do {  ?>
									  <option value="<?php echo $row_reportes['IDCcurso']?>"<?php if ($row_reportes['IDCcurso'] == $IDCcurso) {echo "SELECTED";} ?>><?php echo $row_reportes['evento']?></option>
									  <?php
									 } while ($row_reportes = mysql_fetch_assoc($reportes));
									 $rows = mysql_num_rows($reportes);
									 if($rows > 0) {
									 mysql_data_seek($reportes, 0);
									 $row_reportes = mysql_fetch_assoc($reportes);
									 } ?>
							</select>
							</td>
							

                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar</button> 
							<?php if ($IDCcurso != 0) { ?>
                            <a href="capa_catalogos_6.php?IDCcurso=<?php echo $IDCcurso;?>" class="btn btn-info">Ver evento</a>
							<?php } ?>
							<input type="hidden" name="filtro" id="filtro" value="1">
							</td>
					      </tr>
				    </table>
				</form>
				
			<?php if ($IDCcurso != 0) { ?>  
			<b>Cursos incluidos:</b> <?php echo $row_cursos_aplicables['curso'];?><br />
			<b>Puestos incluidos:</b> <?php if (substr_count($row_puestos_aplicables['denominacion'], ",") > 10) {echo "más de 10";} else {echo $row_puestos_aplicables['denominacion'];}?><br />
			<b>Fecha inicio:</b> <?php if ($fecha_inicio == "") { echo "";} else { echo  date('d/m/Y', strtotime($fecha_inicio)); }?><br />
			<b>Fecha fin:</b> <?php if ($fecha_fin == "") { echo "";} else { echo date('d/m/Y', strtotime($fecha_fin)); }?><br />
			<?php } ?>				
			

<?php  if ($filtro == 1) {?>

				
                <form method="POST" action="capa_reporte_3b.php">
				<input type="hidden" name="IDC_capa_cursos" id="IDC_capa_cursos" value="<?php echo $IDC_capa_cursos;?>">
				<input type="hidden" name="IDCcurso" id="IDCcurso" value="<?php echo $IDCcurso;?>">
				<input type="hidden" name="fecha_inicio" id="fecha_inicio" value="<?php echo $fecha_inicio;?>">
				<input type="hidden" name="fecha_fin" id="fecha_fin" value="<?php echo $fecha_fin;?>">
				<div class="table-responsive">
					<table class="table table-condensed table-bordered datatable-button-html5-columns">
                    <thead> 
					  <tr>
						<td class="bg-danger  text-center">Regional</td>
						<td class="bg-danger  text-center">Sucursal</td>
						<td class="bg-primary text-center">Evento</td>
						<td class="bg-primary text-center">Universo Aplicable</td>
						<td class="bg-primary text-center">Capacitados</td>
						<td class="bg-primary text-center">Efectividad</td>
					  </tr>
                    </thead>
                    <tbody>
						<?php
							$total_Universo = 0;
							$total_Capacitados = 0;
							$total_efectividad = 0;
							
							do { 
							
							$IDmatriz = $row_amatriz['IDmatriz']; 

							if ($totalRows_todoslospuestos <= 200) {

							//TODAS LAS ALTAS
							$query_altas = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad BETWEEN '$fecha_inicio' AND '$fecha_fin' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.IDpuesto in ($puestos) AND ind_bajas.excluye_antiguedad = 1";
							$altas = mysql_query($query_altas, $vacantes) or die(mysql_error());
							$row_altas = mysql_fetch_assoc($altas);

							//CAPACITADOS 
							$query_detalle = "SELECT Count(capa_avance.IDC_capa) AS Capacitados FROM capa_avance WHERE ".$filtrador." AND capa_avance.IDmatriz = $IDmatriz AND capa_avance.IDC_capa_cursos IN ($IDC_capa_cursos) AND capa_avance.IDpuesto in ($puestos)"; 
							$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
							$row_detalle = mysql_fetch_assoc($detalle);

							} else {

							//TODAS LAS ALTAS
							$query_altas = "SELECT Count(ind_bajas.IDempleado) AS Altas FROM ind_bajas WHERE ind_bajas.fecha_antiguedad BETWEEN '$fecha_inicio' AND '$fecha_fin' AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.excluye_antiguedad = 1";
							$altas = mysql_query($query_altas, $vacantes) or die(mysql_error());
							$row_altas = mysql_fetch_assoc($altas);

							//CAPACITADOS 
							$query_detalle = "SELECT Count(capa_avance.IDC_capa) AS Capacitados FROM capa_avance WHERE ".$filtrador." AND capa_avance.IDmatriz = $IDmatriz AND capa_avance.IDC_capa_cursos IN ($IDC_capa_cursos)"; 
							$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
							$row_detalle = mysql_fetch_assoc($detalle);
							
							}
							
							//Efectividad							
							// si no se capacita a nadie
							if ($row_detalle['Capacitados'] == 0)
								{$efectividad = '0'; }
							// si se capacita a alguen pero no hay altas
							if ($row_detalle['Capacitados'] > 0 AND $row_altas['Altas'] == 0)
								{$efectividad = '100';}
							// si se capacita a alguen y hay altas													
							if ($row_detalle['Capacitados'] > 0 AND $row_altas['Altas'] > 0)
								{$efectividad = round(($row_detalle['Capacitados'] / $row_altas['Altas'])*100,0); }	
							// limpiamos si es mayor a 100
							if ($efectividad > 100){ $efectividad = 100;} 

							$efectividad_total = round($efectividad,0);
							$total_Universo = $total_Universo + $row_altas['Altas'];
							$total_Capacitados = $total_Capacitados + $row_detalle['Capacitados'];

						?>
					  <tr>
						<td>
						<b>
						<?php if ($row_amatriz['region_op'] == 1) {echo 'Norte';} 
							else if ($row_amatriz['region_op'] == 2) {echo 'Centro';} 
							else if ($row_amatriz['region_op'] == 3) {echo 'Sur';} 
							else {echo "-";} ?>
						</b>
						</td>
						<td><b><?php echo $row_amatriz['matriz']; ?></b></td>
						<td><?php echo $row_elevento['evento']; ?></td>
						<td class="text-center"><?php echo $row_altas['Altas']; ?></td>
						<td class="text-center"><?php echo $row_detalle['Capacitados'] ?></td>
						<td class="text-center"><?php echo $efectividad_total; ?>%</td>
					  </tr>
					 <?php } while ($row_amatriz = mysql_fetch_assoc($amatriz)); ?>					  
                    </tbody>
					<tfoot> 
					  <tr>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-center"><b><?php  echo $total_Universo; ?></b></td>
						<td class="text-center"><b><?php  echo $total_Capacitados; ?></b></td>
						<td class="text-center"><b><?php if ($total_Capacitados > 0 AND $total_Universo > 0) {echo round(($total_Capacitados / $total_Universo) * 100, 0);} else { echo "-";} ?>%</b></td>
					  </tr>
                    </tfoot>

					</table>
				</div>	
				
<?php } ?>


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

