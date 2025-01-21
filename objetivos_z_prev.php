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

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT * FROM ztar_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

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


$la_matriz = $IDmatriz;
$_SESSION['el_anio'] = $la_matriz;

if (isset($_POST['el_anio'])) {$el_anio = $_POST['el_anio'];} 
else {$el_anio = '2022';}

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
									<h1 class="panel-title">Resultados - Desempeño RH Sucursal</h1>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">
                                
									<h6 class="text-semibold">Instrucciones:</h6>
									<p>A continuación se muestan los resultados de la Evaluación a Recursos Humanos Sucursal.</p>
									<p>Selecciona los meses y año para filtrar resultados.</p>

                              <p>&nbsp;</p>
							  <h6>CALIFICACIÓN ANUAL POR ÁREA:</h6>
                                  
                                  
                         <div class="row">
						<div class="col-sm-6">
                                        <table class="table">
											<thead>
												<tr>
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
											ztar_tareas.anio = '2020' AND
											ztar_tareas.IDarea_rh = '$area_r' AND
											ztar_avances.IDresultado IS NOT NULL AND
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

											if ($Total >= 3)  { $prev1 = 10 * $Ponderacion; }
										elseif ($Total >= 2)  { $prev1 = 9  * $Ponderacion; }
										elseif ($Total >= 1)  { $prev1 = 7  * $Ponderacion; }
										elseif ($Total >= 0)  { $prev1 = 8  * $Ponderacion; }
										elseif ($Total == '') { $prev1 = 0  * $Ponderacion; }

										$prev0 = $prev0 + $prev1;
										
										$prev2 = round($prev0 * 0.1,0);

										} while ($row_tareas2 = mysql_fetch_assoc($tareas2)); 
										
										$prev3 = $prev2 * $Valorarea;

										$prev4 = round($prev3 / 100, 0);

										?>
										<td><div class="text text-danger"><?php echo $row_arrh2['area_rh']; ?></div></td>
										<td><?php echo $prev2; ?></td>
										<td><?php echo $Valorarea; ?>%</td>
										<td><?php echo $prev4; ?></td>
										</tr>
                                    <?php 
									
										$final =  $final + $prev4;

									  } while ($row_arrh2 = mysql_fetch_assoc($arrh2));
									  
									  ?>
                                   </tbody>
                                   	<tfoot>
                                    <tr>
                                        <th><div class="text text-success">Total</div></th>
                                        <th></th>
                                        <th><div class="text text-success">100%</div></th>
                                        <th><div class="text text-success"><?php echo $final; ?>%</div></th>
                                    </tr>
                                	</tfoot>
                                   </table>
                            </div>
                            </div>
                        
                              <p>&nbsp;</p>
                              <p>&nbsp;</p>
                              <p>&nbsp;</p>
                              <p>&nbsp;</p>
							  <h6>CALIFICACIÓN POR TAREA:</h6>
                     					  <form method="POST" action="objetivos_z.php">
                                        <table class="table">
                                            <tbody>							  
                                                <tr>
                                                 <td> <div class="col-lg-2">Mes:</div></td>
								    <td>
                                     <div class="col-lg-12">
                                             <select class="multiselect" multiple="multiple" name="mi_mes[]">
											<?php do { ?>
                                               <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $mi_mes))) 
											   {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
                                               <?php
											  } while ($row_meses = mysql_fetch_assoc($meses));
											  $rows = mysql_num_rows($meses);
											  if($rows > 0) {
												  mysql_data_seek($meses, 0);
												  $row_meses = mysql_fetch_assoc($meses);
											  } ?> 
                                              </select>
                                      </div>
                                     </td>
											    <td> <div class="col-lg-2">Año:</div></td>
								    <td>
                                     <div class="col-lg-12">
                                             <select name="el_anio" class="form-control">
                                               <option value="2020"<?php if (!(strcmp($el_anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
                                               <option value="2022"<?php if (!(strcmp($el_anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
											 </select>
                                      </div>
                                     </td>
												<td>
												<button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
												 </td>
											  </tr>
											</tbody>
										</table>
						</form>
				        
                        
					<div class="table-responsive content-group">
                   <table class="table tasks-list table-condensed">
											<thead>
												<tr>
													<th>IDresultado</th>
													<th>Área RH</th>
													<th>Matriz</th>
													<th>Descripción</th>
													<th>Fecha Esperada</th>
													<th>Resultado</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>
                                            
                                     <?php do { ?>
                                           <tr>
													<td><?php echo $row_avances['IDavance'];?></td>
													<td><?php echo $row_avances['area_rh'];?></td>
													<td><?php echo $row_avances['matriz'];?></td>
													<td><?php echo $row_avances['descripcion'];?></td>
													<td><?php $fecha = date('d/m/Y', strtotime($row_avances['fecha_esperada'])); echo $fecha; ?></td>
													<td><?php if ($row_avances['IDresultado'] == 0) { echo "<span class='label label-default'>En proceso / Incompleto</span>"; }
														 else if ($row_avances['IDresultado'] == 3) { echo "<span class='label label-primary'>Sobresaliente</span>"; } 
														 else if ($row_avances['IDresultado'] == 2) { echo "<span class='label label-success'>Satisfactorio</span>"; } 
														 else if ($row_avances['IDresultado'] == 1) { echo "<span class='label label-warning'>Deficiente</span>"; }
														 else if ($row_avances['IDresultado'] == '') { echo "<span class='label label-warning'>No aplica</span>"; }
														 ?>
													</td>
													<td><a class="btn btn-primary" href="objetivos_b_detalles.php?IDavance=<?php echo $row_avances['IDavance'];?>">Detalles</a></td>
												</tr>
					    			<?php } while ($row_avances = mysql_fetch_assoc($avances));  ?>
                                                    </tbody>
										</table>
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