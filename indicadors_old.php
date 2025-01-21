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
if(isset($_POST['el_anio'])) { $anio = $_POST['el_anio'];} else {$anio = $row_variables['anio'];}
$desfase = $row_variables['dias_desfase'];
$fecha = date("Y-m-d"); 
if(isset($_POST['el_anio'])) { $anio_actual = $_POST['el_anio'];} else {$anio_actual = $row_variables['anio'];}

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
//$IDmatriz = $row_usuario['IDmatriz'];
$IDmatriz = 16;
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];
$mes_actual = date("m"); 

if ($mes_actual == 01) {$mes_actual = 12; } else {$mes_actual = $mes_actual - 1;}
if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } else { $_SESSION['el_mes'] = $mes_actual;}

//$el_mes = $_SESSION['el_mes'];
$el_mes = 1;
$el_anio = $anio; 
$anio_anterior = $el_anio - 1; 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_anior = "SELECT * FROM vac_anios ORDER BY vac_anios.IDanio DESC";
$anior = mysql_query($query_anior, $vacantes) or die(mysql_error());
$row_anior = mysql_fetch_assoc($anior);
$totalRows_anior = mysql_num_rows($anior);

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

	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/sucursal.js"></script>
	<script src="global_assets/js/sucursal2.js"></script>
	<script src="global_assets/js/area.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    
	<script src="assets/rot_antig.js"></script>
	<script src="assets/rot_area.js"></script>
	<script src="assets/rot_motivo.js"></script>
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
							<h5 class="panel-title">Rotación Mensual</h5>
						</div>

					<div class="panel-body">
					<p>El índice de rotación de personal es indicador que permite medir cuál es el flujo de salidas y entradas de empleados en nuestra empresa y que 
					sirve para determinar estrategias de retención del Capital Humano.</br>
					En la presente sección podrás conocer cuál es el comportamiento de dicho indicador para cada sucursal de la Organización.</br>
					Cuando el índice de rotación es alto, es responsabilidad de Recursos Humanos entender las causas y buscar estrategias 
					de solución que involucren a todos los líderes y responsables para solucionarlo de forma permanente.
					</p>
                    </div>
               	  </div>


					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Resultado Mensual</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">En la siguiente tabla, se muestra el resultado mensual y la tendencia anual. El semáforo muestra el nivel de cumplimiento de forma anualizada.</p>
							<h5>Resultado <?php echo $anio_anterior; ?>:<strong> <?php echo $row_resultados_anterior['resultado']; ?>%</strong> </h5>
							<h5>Objetivo <?php echo $anio_actual; ?>:<strong> <?php echo $row_resultados['objetivo']; ?>%</strong></h5>
							<h5>Objetivo mensual:<strong> <?php echo round($row_resultados['objetivo'] / 12, 1); ?>%</strong> </h5>
								<div class="content-group">



                                <div class="table-responsive">
                                <table class="table">
                                <thead> 
                                <tr class="bg-success"> 
                                <th>

<?php 

for ($x = 1; $x <= 12; $x++) {

    // Resultado x mes año actual
$fini_ms1 = new DateTime($anio_actual.'-'.$el_mes.'-01');
$fini_ms1->modify('first day of this month');
$fini_ms1k = $fini_ms1->format('Y/m/d'); 

$fter_ms1 = new DateTime($anio_actual.'-'.$el_mes.'-01');
$fter_ms1->modify('last day of this month');
$fter_ms1k = $fter_ms1->format('Y/m/d'); 

//Inicio mes
$query_res_ms1a = "SELECT Count( ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDmatriz = $IDmatriz AND fecha_antiguedad <= '$fini_ms1k' AND (fecha_baja >= '$fini_ms1k' OR fecha_baja IS NULL)";
$res_ms1a = mysql_query($query_res_ms1a, $vacantes) or die(mysql_error());
$row_res_ms1a = mysql_fetch_assoc($res_ms1a);

//Fin mes
$query_res_ms1b = "SELECT Count( ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDmatriz = $IDmatriz AND fecha_antiguedad <= '$fter_ms1k' AND (fecha_baja >= '$fter_ms1k' OR fecha_baja IS NULL)";
$res_ms1b = mysql_query($query_res_ms1b, $vacantes) or die(mysql_error());
$row_res_ms1b = mysql_fetch_assoc($res_ms1b);

$row_res_ms1 = $row_res_ms1a['TOTAL'] + $row_res_ms1b['TOTAL']/2;

// bajas mes 
$query_bajas = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = $IDmatriz AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$bajas = mysql_query($query_bajas, $vacantes) or die(mysql_error());
$row_bajas = mysql_fetch_assoc($bajas);

$total_activos_ini = $row_activos_ini['TOTAL'];
$total_activos_fin = $row_activos_fin['TOTAL'];
$total_bajas = $row_bajas['TOTAL'];
$promedio_activos = round(($total_activos_ini  + $total_activos_fin)/2, 0);
$total_rotacion = round(($total_bajas / $promedio_activos)*100,1);

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
    case 12: $elmes = "Diciembre";  break;  }

$el_mes++;

?>
<?php echo $elmes; ?> </th>
<th class="bg-danger">ACUMULADO</th>
<th class="bg-danger">OBJETIVO</th>
<th class="bg-danger">SEMAFORO</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<?php ?>
<?php } ?>
</td>
<td><?php echo round($Acumulado * 100, 1); ?>%</td>
<td><?php echo round((($row_resultados['objetivo'] / 12) * $el_mes), 2); ?>%</td>
<td><?php 
$a_ = round((($row_resultados['objetivo'] / 12) * $el_mes), 2);
$b_ = round($Acumulado * 100, 1);
if ($a_ < $b_) {echo "<i class='icon-checkmark-circle text-danger position-left'></i>ROJO";} else {echo "<i class='icon-checkmark-circle text-success position-left'></i>VERDE";}
?></td>
</tr>
</tbody>
</table> 
</div>












</div>
</div>
</div>


<div class="panel panel-flat">
<div class="panel-heading">
<h5 class="panel-title">Resultado mensual por Área</h5>
</div>

<div class="panel-body">
<p class="content-group">A continuación, se muestra el resultado de rotación por área, correspondiente al <?php echo $elmes; ?>. 
Para cambiar el mes, selecciona el mes en el siguiente filtro y da clic en "filtrar". El filtro aplica para todas las gráficas.</p>
<div class="content-group">

<form method="POST" action="indicadors.php">
<table class="table">
<tbody>							  
<tr>
<td>
<select name="el_mes" class="form-control">
<option value="" <?php if (!(strcmp("", $el_mes))) {echo "selected=\"selected\"";} ?>>Mes: Actual</option>
<?php do {  ?>
<option value="<?php echo $row_mes['IDmes']?>"<?php if (!(strcmp($row_mes['IDmes'], $el_mes)))
{echo "selected=\"selected\"";} ?>><?php echo $row_mes['mes']?></option>
<?php
} while ($row_mes = mysql_fetch_assoc($mes));
$rows = mysql_num_rows($mes);
if($rows > 0) {
mysql_data_seek($mes, 0);
$row_mes = mysql_fetch_assoc($mes);
} ?></select>
</td>
<td>
<select name="el_anio" class="form-control">
<option value="2020"<?php if (!(strcmp($anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
<option value="2021"<?php if (!(strcmp($anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
<option value="2022"<?php if (!(strcmp($anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
<option value="2023"<?php if (!(strcmp($anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
<option value="2024"<?php if (!(strcmp($anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
<option value="2025"<?php if (!(strcmp($anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
</select>
</td>
<td>
<button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
<td>
</tr>
</tbody>
</table>
</form>



				<div class="table-responsive">
                  <table class="table table-bordered">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>ÁREA </th>
                      <th class="col-lg-1 bg-danger">Bajas <?php echo $anio_anterior; ?></th>
                      <th class="col-lg-1 bg-danger">Activos <?php echo $anio_anterior; ?></th>
                      <th class="col-lg-2 bg-danger">Rotación <?php echo $anio_anterior; ?></th>
                      <th class="col-lg-1 bg-blue">Bajas <?php echo $anio_actual; ?></th>
                      <th class="col-lg-1 bg-blue">Activos <?php echo $anio_actual; ?></th>
                      <th class="col-lg-2 bg-blue">Rotación <?php echo $anio_actual; ?></th>
               		 </tr>
                    </thead>
                    <tfoot> 
                    <tr> 
                      <th>Total </th>
                      <th><?php $TTBan2 = $ar1an2r + $ar2an2r + $ar3an2r + $ar4an2r + $ar5an2r + $ar6an2r + $ar7an2r;  echo $TTBan2; ?></th>
                      <th><?php $TTAan2 = $Toar1an2r + $Toar2an2r + $Toar3an2r + $Toar4an2r + $Toar5an2r + $Toar6an2r + $Toar7an2r;  echo $TTAan2; ?></th>
                      <th><?php $Prev2 = ($TTBan2 / $TTAan2) * 100;  echo round($Prev2 , 1); ?>%</th>
                      <th><?php $TTBan1 = $ar1an1r + $ar2an1r + $ar3an1r + $ar4an1r + $ar5an1r + $ar6an1r + $ar7an1r;  echo $TTBan1; ?></th>
                      <th><?php $TTAan1 = $Toar1an1r + $Toar2an1r + $Toar3an1r + $Toar4an1r + $Toar5an1r + $Toar6an1r + $Toar7an1r;  echo $TTAan1; ?></th>
                      <th><?php $Prev1 = ($TTBan1 / $TTAan1) * 100;  echo round($Prev1 , 1); ?>%</th>
               		 </tr>
                    </tfoot>
                    <tbody>
                 	<tr>
                    <td>Almacén</td>
                    <td><?php echo $ar1an2r; ?></td>
                    <td><?php echo $Toar1an2r; ?></td>
                    <td><?php if ($ar1an2r > 0) {echo (round($ar1an2r / $Toar1an2r, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar1an1r; ?></td>
                    <td><?php echo $Toar1an1r; ?></td>
                    <td><?php if ($ar1an1r > 0) {echo (round($ar1an1r / $Toar1an1r, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Almacén Detalle</td>
                    <td><?php echo $ar2an2r; ?></td>
                    <td><?php echo $Toar2an2r; ?></td>
                    <td><?php if ($ar2an2r > 0) {echo (round($ar2an2r / $Toar2an2r, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar2an1r; ?></td>
                    <td><?php echo $Toar2an1r; ?></td>
                    <td><?php if ($ar2an1r > 0) {echo (round($ar2an1r / $Toar2an1r, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Distribución</td>
                    <td><?php echo $ar3an2r; ?></td>
                    <td><?php echo $Toar3an2r; ?></td>
                    <td><?php if ($ar3an2r > 0) {echo (round($ar3an2r / $Toar3an2r, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar3an1r; ?></td>
                    <td><?php echo $Toar3an1r; ?></td>
                    <td><?php if ($ar3an1r > 0) {echo (round($ar3an1r / $Toar3an1r, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Distribución Detalle</td>
                    <td><?php echo $ar4an2r; ?></td>
                    <td><?php echo $Toar4an2r; ?></td>
                    <td><?php if ($ar4an2r > 0) {echo (round($ar4an2r / $Toar4an2r, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar4an1r; ?></td>
                    <td><?php echo $Toar4an1r; ?></td>
                    <td><?php if ($ar4an1r > 0) {echo (round($ar4an1r / $Toar4an1r, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Ventas</td>
                    <td><?php echo $ar5an2r; ?></td>
                    <td><?php echo $Toar5an2r; ?></td>
                    <td><?php if ($ar5an2r > 0) {echo (round($ar5an2r / $Toar5an2r, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar5an1r; ?></td>
                    <td><?php echo $Toar5an1r; ?></td>
                    <td><?php if ($ar5an1r > 0) {echo (round($ar5an1r / $Toar5an1r, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Ventas Detalle</td>
                    <td><?php echo $ar6an2r; ?></td>
                    <td><?php echo $Toar6an2r; ?></td>
                    <td><?php if ($ar6an2r > 0) {echo (round($ar6an2r / $Toar6an2r, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar6an1r; ?></td>
                    <td><?php echo $Toar6an1r; ?></td>
                    <td><?php if ($ar6an1r > 0) {echo (round($ar6an1r / $Toar6an1r, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Administración</td>
                    <td><?php echo $ar7an2r; ?></td>
                    <td><?php echo $Toar7an2r; ?></td>
                    <td><?php if ($ar7an2r > 0) {echo (round($ar7an2r / $Toar7an2r, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar7an1r; ?></td>
                    <td><?php echo $Toar7an1r; ?></td>
                    <td><?php if ($ar7an1r > 0) {echo (round($ar7an1r / $Toar7an1r, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                    </tbody>
                   </table>
                   <p>&nbsp;</p>
                    <div class="alert bg-info alert-styled-left">
                    <button type="button" data-target="#modal_form_inlineact" data-toggle="modal" class="btn btn-info">
                     Da clic aqui para ver el detalle de bajas del año actual</button>
                    </div>

                   <p>
                   </p>
                </div>                                
                   </div>
				</div>
			</div>


				<div class="row">
					<div class="col-md-6">
					<!-- Exploded pie charts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación por Antiguedad</h5>
						</div>

						<div class="panel-body">

							<div class="row">
								<div class="col-md">
									<div class="chart-container text-center content-group">
										<div class="chart" id="rot_antig"></div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- /exploded pie charts -->
					</div>
                    
                    
					<div class="col-md-6">
					<!-- Exploded pie charts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación por Área</h5>
						</div>

						<div class="panel-body">

							<div class="row">
								<div class="col-md">
									<div class="chart-container text-center content-group">
										<div class="chart" id="rot_areas"></div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- /exploded pie charts -->
					</div>
				</div>


					<!-- Exploded pie charts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación por Motivo</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">Los motivos señalados en color azul, corresponden a motivos no deseados, mientras que los rojos, corresponden a motivos deseados. </p>

									<div class="chart-container text-center content-group">
										<div class="chart" id="rot_motivo"></div>
							</div>
						</div>
					</div>
					<!-- /exploded pie charts -->
					</div>

                      <!-- Inline form modal -->
					<div id="modal_form_inlineact" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-info">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Detalle de bajas <?php echo "del mes de " . $elmes. ", del año " . $anio_actual ?></h5>
								</div>

            					<form>
									<div class="modal-body">
                                
                                <?php
                                
                                //Mes Actual Motivos
                                mysql_select_db($database_vacantes, $vacantes);
                                $query_det_baj_act = "SELECT ind_bajas.Idempleado, ind_bajas.emp_paterno, ind_bajas.emp_materno, ind_bajas.emp_nombre, ind_bajas.fecha_baja, 
								vac_motivo_baja.motivo, ind_bajas.descripcion_puesto FROM ind_bajas INNER JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE 
								ind_bajas.IDmatriz = '$la_matriz' AND (ind_bajas.baja_mes = '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') AND ind_bajas.IDmotivo < 15 ORDER BY 
								ind_bajas.fecha_baja desc";
                                mysql_query("SET NAMES 'utf8'");
                                $det_baj_act = mysql_query($query_det_baj_act, $vacantes) or die(mysql_error());
                                $row_det_baj_act = mysql_fetch_assoc($det_baj_act);
                                ?>
                                                    
                                <div class="table-responsive-sm">
                                    <table class="table table-bordered table-condensed">
                                    <thead> 
                                    <tr> 
                                      <th>No.</th>
                                      <th>Emp.</th>
                                      <th>Nombre</th>
                                      <th>Puesto</th>
                                      <th>Motivo</th>
                                      <th>Fecha</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 0; do { ?>
                                    <tr> 
                                      <td><?php $i = $i + 1; echo $i;?></td>
                                      <td><?php echo $row_det_baj_act['Idempleado'];?></td>
                                      <td><?php echo $row_det_baj_act['emp_paterno'] . " " . $row_det_baj_act['emp_materno'] . " " . $row_det_baj_act['emp_nombre'];?></td>
                                      <td><?php echo $row_det_baj_act['descripcion_puesto'];?></td>
                                      <td><?php echo $row_det_baj_act['motivo'];?></td>
                                      <td><?php echo $row_det_baj_act['fecha_baja'];?></td>
                                    </tr> 
                                    <?php } while ($row_det_baj_act = mysql_fetch_assoc($det_baj_act));  ?>
                                    
                                   </table> 
									</div>                                
                                    
									</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									</div>
								</form>
                                
							</div>
						</div>
					</div>
					<!-- /inline form modal -->

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