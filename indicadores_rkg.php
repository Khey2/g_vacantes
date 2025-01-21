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
$fecha = date("Y-m-d"); // la fecha actual
$mes_actual = date("m"); // la fecha actual
if ($mes_actual = 01) {$mes_actual = 12;} else {$mes_actual = $mes_actual - 1;}
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
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } else { $_SESSION['el_mes'] = $mes_actual;}

if(isset($_POST['el_anio']) && ($_POST['el_anio']  > 0)) {
$_SESSION['el_anio'] = $_POST['el_anio']; } else { $_SESSION['el_anio'] = $anio_actual;}


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

	<script src="global_assets/js/sucursal.js"></script>
	<script src="global_assets/js/sucursal2.js"></script>
	<script src="global_assets/js/area.js"></script>
	
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
    
	<script src="assets/rot_antig.js"></script>
	<script src="assets/rot_area.js"></script>
	<script src="assets/rot_motivo.js"></script>
	


	
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Indicadores de Recursos Humanos.</h5>
						</div>

                            <p>&nbsp;</p>
                             <form method="POST" action="indicadores_rkg.php">

					<table class="table">
							<tr>
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
                             </td>
					      </tr>
				    </table>
                    </form>
					
										<!-- Simple statistics -->


						<div class="panel-body">
					<p>El índice de rotación de personal es indicador que permite medir cuál es el flujo de salidas y entradas de empleados en nuestra empresa y que 
					sirve para determinar estrategias de retención del Capital Humano.</br>
					En la presente sección podrás conocer el Ranking de cubrimiento para todas las sucursales.</p>
                   <p>Los días programados por tipo de vacante son:<br>
                    <ul>
                      <li>Almacén = 8 días.</li>
                      <li>Distribución = 11 días.</li>
                      <li>Ventas = 16 días.</li>
                      <li>Administrativos = 21 días.</li>
                    </ul>
                    Los días de cálculo son laborales y no se consideran días festivos.</p>
                   <p> La calificación se calcula de acuerdo a la siguiente regla, de acuerdo a los días programados de cobertura de cada vacante:<br>
                    <ul>
                      <li>Vacantes cubiertas antes de tiempo: x 1.5 su valor.</li>
                      <li>Vacantes cubiertas en tiempo: x su valor.</li>
                      <li>Vacantes cubiertas fuera de tiempo: x su valor en negativo.</li>
                      <li>Vacantes cubiertas muy fuera de tiempo: x -1.5 su valor.</li>
                    </ul>
                    </p>
                   <p>La calificaciómn considera los siguientes rangos:<br>
                    <ul>
                      <li>Mayor al 100% = Sobresaliente.</li>
                      <li>Entre 85 y 100%= Satisfactorio.</li>
                      <li>Entre 75 y 84% = Suficiente.</li>
                      <li>Menor a 75% = Deficiente.</li>
                    </ul>
                    </p>
                    </div>
               	  </div>


					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Ranking Mensual</h5>
						</div>

						<div class="panel-body">
								<div class="content-group">
                                
				<div class="table-responsive">
                    <table class="table datatable-show-all">
                    <thead> 
                    <tr class="bg-success"> 
                      <th class="col-lg-5">SUCURSAL</th>
                      <th>CUBIERTAS</th>
                      <th>MUY FUERA </br> DE TIEMPO</th>
                      <th>FUERA </br> DE TIEMPO</th>
                      <th>EN TIEMPO</th>
                      <th>ANTES </br> DE TIEMPO</th>
                      <th>EFECTIVIDAD</th>
                      <th>CALIFICACIÓN</th>
               		 </tr>
                    </thead>
                    <tbody>					
					<?php
					
					do {
					$IDmatriz = $row_lmatriz['IDmatriz'];
					$cada_matriz = $row_lmatriz['matriz'];
					
					//mes diferente para grafica
					mysql_select_db($database_vacantes, $vacantes);
					$query_otromes = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDmatriz, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, 
					vac_vacante.ajuste_dias, vac_puestos.dias FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto 
					WHERE vac_vacante.IDmatriz = '$IDmatriz'
					AND vac_vacante.anio = '$anio' AND vac_vacante.IDestatus = 2";
					$otromes = mysql_query($query_otromes, $vacantes) or die(mysql_error());
					$row_otromes = mysql_fetch_assoc($otromes);
					$totalRows_otromes = mysql_num_rows($otromes);
					
					
					
					//variables en 0
					$antes_tiempoy = 0;
					$a_tiempoy = 0;
					$fuera_tiempoy = 0;
					$muy_fuera_tiempoy = 0;
					
					// recorremos cada vacante
					do { 
					
					 $startdatey = date('Y/m/d', strtotime($row_otromes['fecha_requi']));
					 $end_datey =  date('Y/m/d', strtotime($row_otromes['fecha_ocupacion']));
					
					 $previoy = getWorkingDays($startdatey, $end_datey, $holidays);
												 
					  // aplicamos ajuste de dias;
					  $ajuste_diasy = $row_otromes['ajuste_dias'];
					  if ($ajuste_diasy != 0) { $previoy = $previoy - $ajuste_diasy; } 
					  
					  // resultado grafica
						 if (($previoy < 4) && ($totalRows_otromes != 0)) {  
						 $antes_tiempoy = $antes_tiempoy + 1; 
						} else if (($previoy <  $row_otromes['dias']) && ($totalRows_otromes != 0)) {   
						 $a_tiempoy = $a_tiempoy + 1;
						} else if (($previoy < $row_otromes['dias'] + 4) && ($totalRows_otromes != 0)) {  
						 $fuera_tiempoy = $fuera_tiempoy + 1;
						} else if (($previoy >= $row_otromes['dias']) && ($totalRows_otromes != 0)) {
						 $muy_fuera_tiempoy = $muy_fuera_tiempoy + 1; 
						}
					} while ($row_otromes = mysql_fetch_assoc($otromes)); 
					
					if ($totalRows_otromes != 0) {


					//porcentaje
					$ax = $antes_tiempoy;
					$bx = $a_tiempoy;
					$cx = $fuera_tiempoy;
					$dx = $muy_fuera_tiempoy;

					//porcentaje
					$a = ($antes_tiempoy / $totalRows_otromes)*100;
					$b = ($a_tiempoy / $totalRows_otromes)*100;
					$c = ($fuera_tiempoy / $totalRows_otromes)*100;
					$d = ($muy_fuera_tiempoy / $totalRows_otromes)*100;
								
								
					//puntos
					$a2 = $a * 1.5;
					$b2 = $b * 1;
					$c2 = $c * -1;
					$d2 = $d * -1.5;
					
					//calificacion
					$e3 = $a2 + $b2 + $c2 + $d2;

					$f4 = round($e3, 0);
				
					if ( $f4 > 100) {$calificacion = "1.Sobresaliente";} elseif ( $f4 > 85) {$calificacion = "2. Satisfactorio";} elseif ( $f4 > 70)
					{$calificacion = "3. Suficiente";} else {$calificacion = "4. Deficiente";}
					if ( $f4 > 100) {$color = "success";} elseif ( $f4 > 85) {$color = "info";} elseif ( $f4 > 70) {$color = "warning";} else {$color  = "danger";}
					if ( $f4 > 100) {$icono = "icon-checkmark3";} elseif ($f4 > 85) {$icono = "icon-checkmark3";} elseif ($f4 > 70) {$icono = "icon-checkmark3";} else {$icono  = "icon-cross2";}
					
					} else { $calificacion = "5. Sin vacantes"; $f4 = '0'; $color  = "grey"; $icono  = "icon-minus3";}
					?>
					<tr>
                    <td><?php echo $cada_matriz; ?></td>
                    <td><?php echo $totalRows_otromes; ?></td>
                    <td><?php echo $muy_fuera_tiempoy; ?></td>
                    <td><?php echo $fuera_tiempoy; ?></td>
                    <td><?php echo $a_tiempoy; ?></td>
                    <td><?php echo $antes_tiempoy; ?></td>
                    <td><?php echo $f4; ?>%</td>
                    <td><i class="<?php echo $icono?> text-<?php echo $color ?>-400"></i> <?php echo $calificacion; ?></td>
					<?php	} while ($row_lmatriz = mysql_fetch_assoc($lmatriz)); ?>
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