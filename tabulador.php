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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in ($mis_areas)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$fecha = date("Y-m-d");
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if (isset($_POST['el_area'])) {foreach ($_POST['el_area'] as $earea)
	{	$_SESSION['el_area'] = implode(",", $_POST['el_area']);}}  else { $_SESSION['el_area'] = '1,2,3,4,5,6,7,8,9,10,11,12,13';} 


if (isset($_POST['la_matriz'])) {foreach ($_POST['la_matriz'] as $matris)
		{	$_SESSION['la_matriz'] = implode(",", $_POST['la_matriz']);}}  else { $_SESSION['la_matriz'] = $IDmatriz;} 
  

if (isset($_POST['IDaplica_SIND']) && ($_POST['IDaplica_SIND'] == 1)) {$_SESSION['IDaplica_SIND'] = 1; }
else if (isset($_POST['IDaplica_SIND']) && ($_POST['IDaplica_SIND'] == 0)) {$_SESSION['IDaplica_SIND'] = 0; } 
else if (isset($_POST['IDaplica_SIND']) && ($_POST['IDaplica_SIND'] == 3)) {$_SESSION['IDaplica_SIND'] = '0,1'; } 
else  { $_SESSION['IDaplica_SIND'] = '0,1';} 
		
		  
$el_area = $_SESSION['el_area'];
$la_matriz = $_SESSION['la_matriz'];
$IDaplica_SIND = $_SESSION['IDaplica_SIND'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);


mysql_select_db($database_vacantes, $vacantes);
$query_tabulador = "SELECT vac_tabulador.IDpuesto, vac_tabulador.IDnivel, vac_matriz.matriz, vac_puestos.IDaplica_SIND, vac_tabulador.sueldo_mensual, vac_tabulador.variable_mensual, vac_tabulador.sueldo_mensual, vac_puestos.IDver_sueldo, vac_tabulador.sueldo_diario, vac_tabulador.sueldo_integrado, vac_areas.area, vac_puestos.denominacion, vac_puestos.sgmm FROM vac_tabulador LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_tabulador.IDpuesto LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = vac_tabulador.IDmatriz LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE vac_tabulador.IDmatriz IN ($la_matriz) AND vac_puestos.IDaplica_SIND IN ($IDaplica_SIND) AND vac_puestos.IDarea IN ($el_area) AND vac_puestos.tab = 1";
$tabulador = mysql_query($query_tabulador, $vacantes) or die(mysql_error());
$row_tabulador = mysql_fetch_assoc($tabulador);
$totalRows_tabulador = mysql_num_rows($tabulador);

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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<!-- /theme JS files -->
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Tabulador de Sueldos</h5>
						</div>

					<div class="panel-body">
            <p>A continuación podrás consultar el Tabulador de Sueldos autoriado. Por favor considera: </p>
            <ul>
              <li>El sueldo autorizado es el asignado para nuevas contrataciones y promociones internas.
              <li>Los nuevos ingresos siempre deberán ser contratados en People of the Sun, Rigver o Integración Corporativa, según corresponda.</li>
              <li>Para promociones internas de empleados en Villosa, contacta con <a href="mailto:GEMemdiola@sahuayo.mx">GEMendiola@sahuayo.mx</a></li>
              <li>Si no se encuentra un puesto en el listado, solicita el sueldo al Jefe de Compensaciones Corporativo.</li>
              <li><strong>Los puestos marcados con un * se deben validar con el Jefe de Compensaciones Corporativo.</strong></li>
              <li><strong>Los puestos marcados con <i class="icon-flag3 text-info"></i> cuentan con la prestación de SGMM.</strong></li>
            </ul>
            <p>Selecciona el área para filtrar.</p>

                    <form method="POST" action="tabulador.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            Área: <select name="el_area[]" class="multiselect" multiple="multiple" >
							<?php $cadena2 = $el_area; $array = explode(",", $cadena2);
											do { ?>
											   <option value="<?php echo $row_area['IDarea']?>"<?php foreach ($array as $learea) { if (!(strcmp($row_area['IDarea'], $learea))) {echo 
												"selected=\"selected\"";} } ?>><?php echo $row_area['area']?></option>
											   <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) { mysql_data_seek($area, 0);
											  $row_area = mysql_fetch_assoc($area); 
											  } ?> 
									</select>
                            </td>
                           <td>
							Matriz: <select name="la_matriz[]" class="multiselect" multiple="multiple">
						   <?php $cadena = $la_matriz; $array = explode(",", $cadena);
											do { ?>
											   <option value="<?php echo $row_amatriz['IDmatriz']?>"<?php foreach ($array as $lematriz) { if (!(strcmp($row_amatriz['IDmatriz'], $lematriz))) {echo "selected=\"selected\"";} } ?>><?php echo $row_amatriz['matriz']?></option>
											   <?php
											  } while ($row_amatriz = mysql_fetch_assoc($amatriz));
											  $rows = mysql_num_rows($amatriz);
											  if($rows > 0) { mysql_data_seek($amatriz, 0);
											  $row_amatriz = mysql_fetch_assoc($amatriz); 
											  } ?> 
                                             </select>
                            </td>
							<td>
								Sindicato:<select class="form-control" name="IDaplica_SIND">
                                               <option value="3"<?php if (!(strcmp($IDaplica_SIND, 3))) {echo "selected=\"selected\"";} ?>>Todas</option>
                                               <option value="1"<?php if (!(strcmp($IDaplica_SIND, 1))) {echo "selected=\"selected\"";} ?>>Si</option>
                                               <option value="0"<?php if (!(strcmp($IDaplica_SIND, 0))) {echo "selected=\"selected\"";} ?>>No</option>
                                            </select>
                             </td>
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
							    <th>IDPuesto</th>
							    <th>Matriz</th>
							    <th>Área</th>
							    <th>Denominación</th>
							    <th>Sindicato</th>
							    <th>Nivel Tabulador</th>
							    <th>Sueldo Mensual</th>
							    <th>Variable Mensual</th>
							    <th>Bono Semanal Asistencia</th>
							    <th>Sueldo Diario</th>
							    <th>SDI</th>
						    </tr>
					    </thead>
						<tbody>		
                        	<?php do { 							
								$el_puesto = $row_tabulador['IDpuesto'];
								$el_nivel = $row_tabulador['IDnivel'];
								$query_garantia = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz";
								$garantia = mysql_query($query_garantia, $vacantes) or die(mysql_error());
								$row_garantia = mysql_fetch_assoc($garantia);

								$query_garantia_n = "SELECT * FROM prod_valor_antiguedad WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz AND IDnivel = '$el_nivel'";
								$garantia_n = mysql_query($query_garantia_n, $vacantes) or die(mysql_error());
								$row_garantia_n = mysql_fetch_assoc($garantia_n);
								$totalRows_garantia_n = mysql_num_rows($garantia_n);

								?>		  
							<tr>
							<td><?php echo $row_tabulador['IDpuesto']; ?></td>								
							<td><?php echo $row_tabulador['matriz']; ?></td>								
							<td><?php echo $row_tabulador['area']; ?></td>								
							<td><?php echo $row_tabulador['denominacion']; if ($row_tabulador['IDver_sueldo'] == 0) {echo "*";} ?> <?php if ($row_tabulador['sgmm'] == 1) {?><i class="icon-flag3 text-info"></i><?php } ?></td>
							<td>
										  <?php if($row_tabulador['IDaplica_SIND'] == 1 ) { echo "SI"; }
										   else if($row_tabulador['IDaplica_SIND'] == 0 ) { echo "NO"; } ?>
							</td>								
							<td>
							<?php 
								echo $row_tabulador['IDnivel'];
								?>
							</td>								
							<td><?php echo "$" .number_format($row_tabulador['sueldo_mensual'],2); ?></td>								
							<td><?php echo "$" .number_format($row_tabulador['variable_mensual'],2); ?></td>								
							<td><?php echo "$" .number_format($row_garantia['monto_asistencia'],2); ?></td>								
							<td><?php echo "$" .number_format($row_tabulador['sueldo_diario'],2); ?></td>								
							<td><?php echo "$" .number_format($row_tabulador['sueldo_integrado'],2); ?></td>								
							</tr>
                    <?php } while ($row_tabulador = mysql_fetch_assoc($tabulador)); ?>
                        </tbody>
                   </table> 
				  </div>


					<!-- /panel heading options -->

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