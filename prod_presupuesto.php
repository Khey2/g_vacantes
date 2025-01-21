	<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php'); 

setlocale(LC_MONETARY, 'es_MX');
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
$desfase = $row_variables['dias_desfase'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDusuario = $row_usuario['IDusuario'];
$las_matrizes = $row_usuario['IDmatrizes'];
$la_fecha = date("Y-m-d"); // la fecha actual

$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$semana_previa = $semana - 1;
if ($semana == 1) {$semana_previa = 52;}

$anio = $row_variables['anio'];
$el_anio = $row_variables['anio'];
$anio_previo = $anio;
if ($semana == 1) {$anio_previo = $anio - 1;}

$nivel = $_SESSION['kt_login_level'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz  WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

// Filtros
mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM prod_meses_presupesto WHERE visible = 1";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

if (isset($_POST['el_mes'])) {$el_mes =  $_POST['el_mes'];} else {$el_mes =  date("m"); }
if (isset($_POST['el_anio'])) {$el_anio =  $_POST['el_anio'];} else {$el_anio =  $anio; }
if (isset($_POST['IDmatriz'])) {$IDmatriz =  $_POST['IDmatriz'];} 

mysql_select_db($database_vacantes, $vacantes);
$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as resultadoP FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$IDmatriz' AND IDanio = '$anio' AND IDsemana = $semana AND IDestatus = 1";
$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
$row_adicional = mysql_fetch_assoc($adicional);
$totalRows_adicional = mysql_num_rows($adicional);
$aplica_adicional = $row_adicional['resultadoP'];

// $fecha_filtro = $anio."/".$el_mes."/01";
//lunes
$fecha_filtro = date('Y/m/d', strtotime('monday -1 week'));

//consiera pull en presupuesto
if($row_matriz['incluye_pull'] == 1) {$pulls = '1,2';} else { $pulls = '1'; }
$chofers = array(42, 43, 44, 45, 57, 372);

?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
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
	<script src="global_assets/js/plugins/tables/datatables/xdatatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
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

					<!-- Colored tabs -->
					<div class="row">
						
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Presupuesto semanal de productividad</h5>
								</div>
								

								<div class="panel-body">
									<div class="tabbable">

										<div class="tab-content">
											<div class="tab-pane active" id="colored-justified-tab1">
                                            <p>Presupuesto asignado:<br/>
											<ul>
											<li>El presupuesto se calcula de acuerdo con la Plantilla Autorizada, antiguedad de cada empleado y monto topado de Productividad por puesto.</li>
											<li>Matriz: <?php echo $row_matriz['matriz'];?>.</li>
											<li>Semana: <?php echo $semana;?>.</li>
											</ul>
											</p>
																		
											
										<table class="table table-condensed datatable-button-html5-columns">
      							              <thead> 
                                                <tr class="bg-teal-400">
                                                  <th>Puesto</th>
                                                  <th>Área</th>
                                                  <th>Autorizados</th>
                                                  <th>Activos</th>
                                                  <th>Sueldo</th>
                                                  <th>Productividad</th>
                                                  <th>Asistencia</th>
                                                  <th>Presupuesto</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php




$MontoZ = 0;
$AutorizadosZ = 0;

// RECORRER TODOS LOS PUESTOS 
$query_puestos_aplicablesB = "SELECT vac_puestos.denominacion, vac_puestos.IDaplica_PROD, prod_plantilla.IDplantilla, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDpuesto, prod_plantilla.IDestatus, prod_plantilla.IDtipo_plaza  FROM prod_plantilla LEFT JOIN vac_puestos ON prod_plantilla.IDpuesto = vac_puestos.IDpuesto  WHERE prod_plantilla.IDmatriz = $IDmatriz AND vac_puestos.IDaplica_PROD = 1  AND prod_plantilla.IDestatus = 1  AND ( DATE ( fecha_inicio ) <= '$fecha_filtro' )  AND ( DATE ( fecha_fin ) > '$fecha_filtro' OR DATE ( fecha_fin ) = '0000-00-00' OR DATE ( fecha_fin ) IS NULL )  AND ( DATE ( fecha_congelada ) > '$fecha_filtro' OR DATE ( fecha_congelada ) = '0000-00-00' OR DATE ( fecha_congelada ) IS NULL ) GROUP BY prod_plantilla.IDpuesto";
$puestos_aplicablesB = mysql_query($query_puestos_aplicablesB, $vacantes) or die(mysql_error());
$row_puestos_aplicablesB = mysql_fetch_assoc($puestos_aplicablesB);
$totalRows_puestos_aplicablesB = mysql_num_rows($puestos_aplicablesB);

do {

$IDpuestoB = $row_puestos_aplicablesB['IDpuesto']; 

// detalles del puesto para pintar el área
$query_el_puestoB = "SELECT vac_puestos.*, vac_areas.area FROM vac_puestos LEFT JOIN vac_areas ON  vac_puestos.IDarea = vac_areas.IDarea WHERE IDpuesto = $IDpuestoB";
$el_puestoB = mysql_query($query_el_puestoB, $vacantes) or die(mysql_error());
$row_el_puestoB = mysql_fetch_assoc($el_puestoB);
$totalRows_el_puestoB = mysql_num_rows($el_puestoB);
$el_areaB = $row_el_puestoB['area'];

// activos para mostrar activos
$query_activosB = "SELECT prod_activos.*  FROM prod_activos WHERE IDmatriz = $IDmatriz AND IDpuesto IN ($IDpuestoB)";
$activosB = mysql_query($query_activosB, $vacantes) or die(mysql_error());
$row_activosB = mysql_fetch_assoc($activosB);
$totalRows_activosB = mysql_num_rows($activosB);

// plantilla autorizada
$query_plantillaB = "SELECT Count(prod_plantilla.IDplantilla) AS Plantilla FROM prod_plantilla WHERE IDmatriz = $IDmatriz AND IDpuesto IN ($IDpuestoB) AND prod_plantilla.IDestatus = 1 AND ( DATE ( fecha_inicio ) <= '$fecha_filtro' ) AND ( DATE ( fecha_fin ) > '$fecha_filtro' OR DATE ( fecha_fin ) = '0000-00-00' OR DATE ( fecha_fin ) IS NULL ) AND ( DATE ( fecha_congelada ) > '$fecha_filtro' OR DATE ( fecha_congelada ) = '0000-00-00' OR DATE ( fecha_congelada ) IS NULL )";
$plantillaB = mysql_query($query_plantillaB, $vacantes) or die(mysql_error());
$row_plantillaB = mysql_fetch_assoc($plantillaB);
$totalRows_plantillaB = mysql_num_rows($plantillaB);

// monto segun activos y garantia
$query_presupuesto_cajasB = "SELECT SUM(vac_tabulador.variable_mensual / 30 ) * 7 As MontoA, SUM(vac_tabulador.asistencia_mensual / 30 ) * 7 As MontoB FROM prod_activos LEFT JOIN vac_tabulador ON prod_activos.IDpuesto = vac_tabulador.IDpuesto AND prod_activos.IDmatriz = vac_tabulador.IDmatriz AND prod_activos.IDnivel_antiguedad = vac_tabulador.IDnivel WHERE prod_activos.IDmatriz = $IDmatriz AND prod_activos.IDpuesto = $IDpuestoB";
$presupuesto_cajasB = mysql_query($query_presupuesto_cajasB, $vacantes) or die(mysql_error()); 
$row_presupuesto_cajasB = mysql_fetch_assoc($presupuesto_cajasB);
$totalRows_presupuesto_cajasB = mysql_num_rows($presupuesto_cajasB);
$Monto_sueldosB = $row_presupuesto_cajasB['MontoA'];
$Monto_asistenciaB = $row_presupuesto_cajasB['MontoB'];
	
// nivel minimo para la sucursal
$query_minimo_tabuladorB = "SELECT * FROM prod_valor_antiguedad WHERE IDmatriz = $IDmatriz AND IDpuesto = $IDpuestoB AND meses_inicio = 0";
$minimo_tabuladorB = mysql_query($query_minimo_tabuladorB, $vacantes) or die(mysql_error());
$row_minimo_tabuladorB = mysql_fetch_assoc($minimo_tabuladorB);
$totalRows_minimo_tabuladorB = mysql_num_rows($minimo_tabuladorB);
$Nivel_minimoB = $row_minimo_tabuladorB['IDnivel'];

// Monto de la garantia en porcentaje para recien ingresos
$query_garantia_tabuladorB = "SELECT * FROM prod_garantias WHERE IDmatriz = $IDmatriz AND IDpuesto = $IDpuestoB AND IDnivel = '$Nivel_minimoB'";
$garantia_tabuladorB = mysql_query($query_garantia_tabuladorB, $vacantes) or die(mysql_error());
$row_garantia_tabuladorB = mysql_fetch_assoc($garantia_tabuladorB);
$totalRows_garantia_tabuladorB = mysql_num_rows($garantia_tabuladorB);
//$Monto_garantiaB = $row_garantia_tabuladorB['garantia']; 

// Tabulador autorizado
$query_tabuladorB = "SELECT * FROM vac_tabulador WHERE IDmatriz = $IDmatriz AND IDpuesto = $IDpuestoB";
$tabuladorB = mysql_query($query_tabuladorB, $vacantes) or die(mysql_error()); 
$row_tabuladorB = mysql_fetch_assoc($tabuladorB);
$totalRows_tabuladorB = mysql_num_rows($tabuladorB);
$Monto_garantiaB = ($row_tabuladorB['variable_mensual']/30)*7;
$Monto_sueldo_tabuladorB = $row_tabuladorB['sueldo_diario'] * 7;
$Monto_asistencia_tabuladorB =  ($row_tabuladorB['asistencia_mensual']/30)*7;

// diferencia de plazas
if ($row_plantillaB['Plantilla'] > $totalRows_activosB ) {
$diferencia_plazasB = $row_plantillaB['Plantilla'] - $totalRows_activosB; 
$Monto_2B = $diferencia_plazasB * $Monto_garantiaB; 
$Monto_3B = $diferencia_plazasB * $Monto_asistencia_tabuladorB;
//ECHO "Extras: ".$diferencia_plazasB." IDPuesto: ".$IDpuestoB." Monto garantia: ".$Monto_2B."Monto asistencia: ".$Monto_3B."Monto tabulador: ".$Monto_garantiaB."<br/>";
} else {
$Monto_2B = 0;
$Monto_3B = 0;
}



$Monto_4B = $Monto_sueldosB + $Monto_asistenciaB + $Monto_2B + $Monto_3B;
$MontoZ = $MontoZ + $Monto_4B;
$AutorizadosZ = $AutorizadosZ + $row_plantillaB['Plantilla'];
echo "<tr><td>".$row_puestos_aplicablesB['denominacion']."</td>";
echo "<td>".$el_areaB."</td>";
echo "<td>".$row_plantillaB['Plantilla']."</td>";
echo "<td>".$totalRows_activosB."</td>";
echo "<td>$" .number_format($Monto_sueldo_tabuladorB,2)."</td>";
echo "<td>$" .number_format($Monto_garantiaB,2)."</td>";
echo "<td>$" .number_format($Monto_asistencia_tabuladorB,2)."</td>";
echo "<td>$" .number_format($Monto_4B,2)."</td></tr>";

} while ($row_puestos_aplicablesB = mysql_fetch_assoc($puestos_aplicablesB)); 

mysql_select_db($database_vacantes, $vacantes);
$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as Adicional FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$IDmatriz' AND IDanio = '$anio' AND IDsemana = $semana AND IDestatus = 1";
$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
$row_adicional = mysql_fetch_assoc($adicional);
$totalRows_adicional = mysql_num_rows($adicional);
$aplica_adicional = $row_adicional['Adicional'];

mysql_select_db($database_vacantes, $vacantes);
$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as Adicional FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$IDmatriz' AND IDanio = '$anio' AND IDsemana = $semana AND IDestatus = 1";
$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
$row_adicional = mysql_fetch_assoc($adicional);
$totalRows_adicional = mysql_num_rows($adicional);
$aplica_adicional = $row_adicional['Adicional'];
												
												?>
         							              <tfoot> 
                                                <tr>
                                                  <th colspan="2">Total Semanal</th>
                                                  <th><?php echo $AutorizadosZ; ?></th>	
                                                  <th colspan="4"></th>
                                                  <th><?php  if ($aplica_adicional != 0) { $MontoZ = $MontoZ + $aplica_adicional;  echo "$".number_format(round($MontoZ,2)); }  else {  echo "$".number_format(round($MontoZ,2)); }?></th>
                                                </tr>
                                                </tfoot>
                                             </tbody>
                                              </table>
                                              <br>
                                              
                                              </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /colored tabs -->




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