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

$currentPage = $_SERVER["PHP_SELF"];

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

$IDmatrizes = $row_usuario['IDmatrizes'];
$mmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$mmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$la_matriz = $IDmatriz;
$_SESSION['el_anio'] = $anio;

if (isset($_POST['el_anio'])) {$el_anio = $_POST['el_anio'];} 
else {$el_anio = 2022;}

if (isset($_POST['mi_mes'])) {	foreach ($_POST['mi_mes'] as $mis_meses)
	{	$_SESSION['mi_mes'] = implode(", ", $_POST['mi_mes']);}	}  else { $_SESSION['mi_mes'] = "1,2,3,4,5,6,7,8,9,10,11,12";}
$mi_mes = $_SESSION['mi_mes'];

mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT ztar_avances.IDavance, ztar_avances.IDresultado, ztar_avances.IDestatus,  ztar_tareas.descripcion, vac_matriz.IDmatriz, vac_matriz.matriz, ztar_avances.fecha_esperada, ztar_areas_rh.area_rh, ztar_valor_areas.valor_area, ztar_valor_areas.anio FROM ztar_avances INNER JOIN ztar_tareas ON ztar_tareas.IDtarea = ztar_avances.IDtarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = ztar_avances.IDmatriz INNER JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh INNER JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_areas_rh.IDarea_rh WHERE ztar_avances.IDmatriz = '$la_matriz' AND MONTH(ztar_avances.fecha_esperada) IN ($mi_mes) AND ztar_valor_areas.anio = '$el_anio' AND ztar_avances.IDestatus IS NOT NULL"; 
mysql_query("SET NAMES 'utf8'");
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);

mysql_select_db($database_vacantes, $vacantes);
$query_arrh2 = "SELECT * FROM ztar_areas_rh WHERE ztar_areas_rh.IDarea_rh IN (1,2,3,4,6,7,8,9)";
$arrh2 = mysql_query($query_arrh2, $vacantes) or die(mysql_error());
$row_arrh2 = mysql_fetch_assoc($arrh2);
$totalRows_arrh2 = mysql_num_rows($arrh2);

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

   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect3.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/tasks_list8.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
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
                

					<!-- Detailed task -->
					<div class="row">
						<div class="col-lg-12">

							<!-- Task overview -->
							<div class="panel panel-flat">
								<div class="panel-heading mt-5">
									<h4 class="panel-title">Desempeño JRH - Calificación anual por Área.</h4>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">
									<p>A continuación se muestan los resultados de la Evaluación a Recursos Humanos del 2022 para <?php echo $row_matriz['matriz']; ?>.</p>
                                 
                                  
                         <div class="row">
						<div class="col-sm-12">
                                        <table class="table">
											<thead>
												<tr class="bg-blue">
													<th>Área</th>
													<th>Resultado Área</th>
													<th>Ponderación</th>
													<th>Resultado</th>
												</tr>
											</thead>
											<tbody>
                                           <tr>
										   <?php  $final = 0; ?>
                                           
										<?php do { 
										
                                        $area_r = $row_arrh2['IDarea_rh'];		
                                        $query_tareas2 = "
										SELECT
											ztar_tareas.ponderacion AS Ponderacion,
											ztar_tareas.IDsob, 
											ztar_tareas.IDsat, 
											ztar_tareas.IDdef,
											ztar_valor_areas.valor_area AS Valorarea,
											Avg(ztar_avances.IDresultado) AS Total,
											ztar_valor_areas.valor_area,
											ztar_valor_areas.anio,
											ztar_areas_rh.IDarea_rh,
											ztar_avances.IDtarea,
											ztar_tareas.descripcion
											FROM
											ztar_tareas
											LEFT JOIN ztar_areas_rh ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh
											LEFT JOIN ztar_avances ON ztar_avances.IDtarea = ztar_tareas.IDtarea
											LEFT JOIN ztar_valor_areas ON ztar_valor_areas.IDarea_rh = ztar_tareas.IDarea_rh
											WHERE
											ztar_tareas.anio = '2022' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDestatus != 0 AND ztar_avances.IDresultado != 0 AND 
											ztar_avances.IDmatriz = '$la_matriz'
											GROUP BY
											ztar_tareas.IDarea_rh,
											ztar_avances.IDtarea
										";
                                        $tareas2 = mysql_query($query_tareas2, $vacantes) or die(mysql_error());
                                        $row_tareas2 = mysql_fetch_assoc($tareas2);
										
										$Valorarea = $row_tareas2['Valorarea'];
										
										$prev0 = 0;
										
										do {

										$Total = round($row_tareas2['Total'],1);
										
										$Ponderacion = $row_tareas2['Ponderacion'];
										
										$prev1 = $Total * $Ponderacion;

										$prev0 = $prev0 + $prev1;
										
										$prevx = $prev0/10;
										
										$prev2 = round($prevx * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev41 = round($prev3 / 100, 0);

										?>
										<td><div class="text text-semibold"><?php echo $row_arrh2['area_rh']; ?></div></td>
										<td><?php if($prev2 !=0 ) { echo $prev2; } else { echo "-"; }?></td>
										<td><?php if($prev2 !=0 ) { echo $Valorarea."%"; } else { echo "-"; }?></td>
										<td><?php if($prev2 !=0 ) { echo $prev41; } else { echo "-"; }?></td>
										</tr>
                                    <?php 
									
										$final =  $final + $prev41;

									  } while ($row_arrh2 = mysql_fetch_assoc($arrh2));
									  
									  ?>
                                   </tbody>
                                   	<tfoot>
                                    <tr>
                                        <th><div>Total</div></th>
                                        <th></th>
                                        <th><div>100%</div></th>
                                        <th><div class="text text-success">
										
										<?php if ($final > 100) { echo "<div class='text text-primary text-bold'>".$final."% Sobresaliente</div>";} 
										  else if ($final > 70) { echo "<div class='text text-success text-bold'>".$final."% Satisfactorio</div>";} 
										  else if ($final < 70) { echo "<div class='text text-danger text-bold'>".$final."% Deficiente</div>";} ?>
										
										</div></th>
                                    </tr>
                                	</tfoot>
                                   </table>
                            </div>
                            </div>
                        
								</div>

							</div>
							<!-- /task overview -->

						</div>

					</div>
					<!-- /detailed task -->

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