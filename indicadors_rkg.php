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
$fecha = date("Y-m-d"); // la fecha actual
$mes_actual = date("m")-1; // la fecha actual
$anio_actual = date("Y"); // la fecha actual

//echo $fecha_inicio_mes_ok; 

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
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

 $_SESSION['el_mes'] = $mes_actual;
 $_SESSION['el_anio'] = $anio_actual;


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


$el_mes = $_SESSION['el_mes'];
$el_anio = $_SESSION['el_anio'];
$anio_anterior = $el_anio - 1; // la fecha actual

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_anio = "SELECT * FROM vac_anios ORDER BY vac_anios.IDanio DESC";
$anio = mysql_query($query_anio, $vacantes) or die(mysql_error());
$row_anio = mysql_fetch_assoc($anio);
$totalRows_anio = mysql_num_rows($anio);

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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Indicadores de Recursos Humanos.</h5>
						</div>

					<div class="panel-body">
					<p>El índice de rotación de personal es indicador que permite medir cuál es el flujo de salidas y entradas de empleados en nuestra empresa y que 
					sirve para determinar estrategias de retención del Capital Humano.</br>
					En la presente sección podrás conocer el Ranking de cubrimiento para todas las sucursales.</p>
                    </div>
               	  </div>


					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Ranking Mensual</h5>
						</div>

						<div class="panel-body">
								<div class="content-group">
                                
				<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>SUCURSAL</th>
                      <th>RESULTADO MENSUAL</th>
                      <th>OBJETIVO MENSUAL</th>
                      <th class="bg-danger">ACUMULADO ANUAL</th>
                      <th class="bg-danger">OBJETIVO ANUAL</th>
                      <th class="bg-danger">SEMAFORO</th>
               		 </tr>
                    </thead>
                    <tbody>					
					<?php
					
					do {
					$IDmatriz = $row_lmatriz['IDmatriz'];
					$cada_matriz = $row_lmatriz['matriz'];
					
					//objetivo y total año anterior
					mysql_select_db($database_vacantes, $vacantes);
					$query_resultados = "SELECT * FROM ind_objetivo WHERE IDmatriz = $IDmatriz AND anio = $el_anio";
					$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
					$row_resultados = mysql_fetch_assoc($resultados);
					$totalRows_resultados = mysql_num_rows($resultados);

					mysql_select_db($database_vacantes, $vacantes);
					$query_resultados_anterior = "SELECT * FROM ind_objetivo WHERE IDmatriz = $IDmatriz AND anio = $anio_anterior";
					$resultados_anterior = mysql_query($query_resultados_anterior, $vacantes) or die(mysql_error());
					$row_resultados_anterior = mysql_fetch_assoc($resultados_anterior);
					$totalRows_resultados_anterior = mysql_num_rows($resultados_anterior);

					// Resultado MES ACUTAL
					$fini_ms1 = new DateTime($anio_actual . "-" . $mes_actual . '-01');
					$fini_ms1->modify('first day of this month');
					$fini_ms1k = $fini_ms1->format('Y/m/d'); 

					$fter_ms1 = new DateTime($anio_actual . "-" .  $mes_actual . '-01');
					$fter_ms1->modify('last day of this month');
					$fter_ms1k = $fter_ms1->format('Y/m/d'); 

					mysql_select_db($database_vacantes, $vacantes);
					$query_res_ms1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad <= '$fter_ms1k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms1k')";
					$res_ms1 = mysql_query($query_res_ms1, $vacantes) or die(mysql_error());
					$row_res_ms1 = mysql_fetch_assoc($res_ms1);
					$totalRows_res_ms1 = mysql_num_rows($res_ms1);

					mysql_select_db($database_vacantes, $vacantes);
					$query_bja_ms1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = '$mes_actual' AND YEAR(fecha_baja) = $anio_actual)";
					$bja_ms1 = mysql_query($query_bja_ms1, $vacantes) or die(mysql_error());
					$row_bja_ms1 = mysql_fetch_assoc($bja_ms1);
					$totalRows_bja_ms1 = mysql_num_rows($bja_ms1);
					
					if($row_bja_ms1['TOTAL'] == 0) {$RotTotalM1 = 0;} else {$RotTotalM1 = $row_bja_ms1['TOTAL'] / $row_res_ms1['TOTAL']; }

					// Resultado TODO EL AÑO
					$fini_ms2 = new DateTime($anio_actual . '-01-01');
					$fini_ms2->modify('first day of this month');
					$fini_ms2k = $fini_ms2->format('Y/m/d'); 

					$fter_ms2 = new DateTime($anio_actual . "-" .  $mes_actual . '-01');
					$fter_ms2->modify('last day of this month');
					$fter_ms2k = $fter_ms2->format('Y/m/d'); 

					mysql_select_db($database_vacantes, $vacantes);
					$query_res_ms2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND fecha_antiguedad < '$fter_ms2k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms2k')";
					$res_ms2 = mysql_query($query_res_ms2, $vacantes) or die(mysql_error());
					$row_res_ms2 = mysql_fetch_assoc($res_ms2);
					$totalRows_res_ms2 = mysql_num_rows($res_ms2);

					mysql_select_db($database_vacantes, $vacantes);
					$query_bja_ms2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (YEAR(fecha_baja) = $anio_actual)";
					$bja_ms2 = mysql_query($query_bja_ms2, $vacantes) or die(mysql_error());
					$row_bja_ms2 = mysql_fetch_assoc($bja_ms2);
					$totalRows_bja_ms2 = mysql_num_rows($bja_ms2);


					if($row_bja_ms2['TOTAL'] == 0) {$RotTotalM2 = 0;} else {$RotTotalM2 = $row_bja_ms2['TOTAL'] / $row_res_ms2['TOTAL']; }
					// acumualdo total año
					$Acumulado = $RotTotalM2;
?>
						  
					
					<tr>
                    <td>
					<?php 
					echo $cada_matriz;
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM1 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM1 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM1 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM1 * 100, 1) . "%";}
					?>
                    </td>
                    <td><?php echo round(($row_resultados['objetivo']/12),2); ?>%</td>
                    <td><?php echo round($Acumulado * 100, 2); ?>%</td>
                    <td><?php echo round($row_resultados['objetivo'], 2); ?>%</td>
                    <td><?php 
					$a_ = ($Acumulado / $mes_actual) * 12;
					$b_ = $a_ * 100;
					$c_ = round($b_, 1);
					$r_ = $row_resultados['objetivo'];
					if ($c_ >= $r_) {echo "<i class='icon-checkmark-circle text-danger position-left'></i>ROJO";} else {echo "<i class='icon-checkmark-circle text-success position-left'></i>VERDE";}
					?>
					
					<?php	} while ($row_lmatriz = mysql_fetch_assoc($lmatriz)); ?>

					</td>
                    </tr>
                    </tbody>
                   </table> 
                       </div>
                       </div>
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