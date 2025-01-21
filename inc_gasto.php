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

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_matrizl = "SELECT * FROM vac_matriz";
$matrizl = mysql_query($query_matrizl, $vacantes) or die(mysql_error());
$row_matrizl = mysql_fetch_assoc($matrizl);
$totalRows_matrizl = mysql_num_rows($matrizl);

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
	
if(isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) {$_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = $IDmatriz;}
$la_matriz = $_SESSION['la_matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz2 = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz2 = mysql_query($query_matriz2, $vacantes) or die(mysql_error());
$row_matriz2 = mysql_fetch_assoc($matriz2);
$totalRows_matriz2 = mysql_num_rows($matriz2);
$Amatrzi = $row_matriz2['matriz']; 

	
//incentivos
mysql_select_db($database_vacantes, $vacantes);
$query_incidencias_ = "SELECT inc_captura.IDmatriz, Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5, Count(inc_captura.inc5) AS CINC5, Sum(inc_captura.inc6) AS INC6, inc_semanas_meses.IDmes, inc_semanas_meses.anio, vac_meses.mes FROM inc_captura LEFT JOIN inc_semanas_meses ON inc_captura.semana = inc_semanas_meses.IDsemana LEFT JOIN vac_meses ON vac_meses.IDmes = inc_semanas_meses.IDmes WHERE inc_captura.IDmatriz = '$la_matriz'  AND inc_captura.anio = '$anio' GROUP BY inc_captura.IDmatriz"; 
$incidencias_ = mysql_query($query_incidencias_, $vacantes) or die(mysql_error());
$row_incidencias_ = mysql_fetch_assoc($incidencias_);

//productividad
mysql_select_db($database_vacantes, $vacantes);
$query_productividad_ = "SELECT prod_captura.IDmatriz, Sum(prod_captura.adicional2) AS PROD2, Sum(prod_captura.pago_total) AS PROD1, inc_semanas_meses.IDmes, inc_semanas_meses.anio, vac_meses.mes FROM prod_captura INNER JOIN inc_semanas_meses ON inc_semanas_meses.IDsemana = prod_captura.semana INNER JOIN vac_meses ON vac_meses.IDmes = inc_semanas_meses.IDmes WHERE prod_captura.IDmatriz = '$la_matriz' AND prod_captura.anio = '$anio' GROUP BY prod_captura.IDmatriz"; 
$productividad_ = mysql_query($query_productividad_, $vacantes) or die(mysql_error());
$row_productividad_ = mysql_fetch_assoc($productividad_);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>

	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/login_validation.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<!-- /theme JS files -->

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


	                <!-- Content area -->
				<div class="content">
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte mensual de gasto</h5>
						</div>

                    <form method="POST" action="inc_gasto.php">
                	<table class="table">
						<tbody>							  
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
							<select class="form-control" name="la_matriz">
							<?php do { ?>
							   <option value="<?php echo $row_matrizl['IDmatriz']?>"<?php if (!(strcmp($row_matrizl['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_matrizl['matriz']?></option>
							   <?php
							  } while ($row_matrizl = mysql_fetch_assoc($matrizl));
							  $rows = mysql_num_rows($matrizl);
							  if($rows > 0) {
								  mysql_data_seek($matrizl, 0);
								  $row_matrizl = mysql_fetch_assoc($matrizl);
							  } ?> 
							 </select>				
							</td>
							<td>
                          <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
							<td>
                             </tr>
					    </tbody>
				    </table>
				</form>


					<div class="panel-body">
							<p>Bienvenido. En este apartado se muestra el gasto de incidencias y productividad mensual; así como el acumulado anual.</br>
							La productividad se reporta a partir de la tercera semana de mayo 2020.</br>
							Sucursal: <strong><?php echo $Amatrzi; ?></strong></p>
							<p>Puedes cambiar la sucursal de consulta, dando clic <a href="mi_matriz.php">aqui.</a></p>
                            <p>&nbsp;</p>
				<!-- Statistics with progress bar -->
					<div class="row">

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Horas Extra Acumulado</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_incidencias_['INC1']; ?></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">x Suplencia Acumulado</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo "$" . number_format($row_incidencias_['INC2']); ?></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">PxV  Acumulado</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo "$" . number_format($row_incidencias_['INC5']); ?></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Incentivos Acumulado</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo "$" . number_format($row_incidencias_['INC3']); ?></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Domingos Trabajados Acumulado </h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:</strong> <?php echo "$" . number_format($row_incidencias_['INC4']); ?></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
						  <div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Productividad Acumulado<h6>
									</div>

									<div class="media-right media-middle">
									</div>
								</div>

							<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
						    </div>
										<span class="text-muted"><strong>Monto:</strong> <?php echo "$" . number_format($row_productividad_['PROD1'] + $row_productividad_['PROD2']); ?></span>
						  </div>
						</div>

					</div>

					<!-- /statistics with progress bar -->


                                            <p>&nbsp;</p>



					<table class="table table-condensed datatable-button-html5-columns">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Semana</th>
                      <th>Mes</th>
                      <th>Horas Extras</th>
                      <th>Suplencia</th>
                      <th>Incentivos / Festivos</th>
                      <th>Domingos trabajados</th>
                      <th>Premios por Viaje</th>
                      <th class="bg-danger">Prod. ($)</th>
                      <th class="bg-danger">Prod. (%)</th>
               		 </tr>
                    </thead>
                    <tbody>
<?php 
									
//recorre meses
mysql_select_db($database_vacantes, $vacantes);
$query_semana = "SELECT DISTINCT inc_semanas_meses.IDmes, inc_semanas_meses.IDsemana FROM inc_captura INNER JOIN inc_semanas_meses ON inc_captura.semana = inc_semanas_meses.IDsemana WHERE inc_captura.anio = '$anio' GROUP BY inc_semanas_meses.IDsemana";
$semana = mysql_query($query_semana, $vacantes) or die(mysql_error());
$row_semana = mysql_fetch_assoc($semana);										
$totalRows_semana = mysql_num_rows($semana);
									
$prom_INC1 = ($row_incidencias_['INC1'] / $totalRows_semana) * 1.5;										
$prom_INC2 = ($row_incidencias_['INC2'] / $totalRows_semana) * 1.5;													
$prom_INC3 = ($row_incidencias_['INC3'] / $totalRows_semana) * 1.5;													
$prom_INC4 = ($row_incidencias_['INC4'] / $totalRows_semana) * 1.5;													
$prom_INC5 = ($row_incidencias_['INC5'] / $totalRows_semana) * 1.5;												

do { 
$the_semana = $row_semana['IDsemana'];
										
//incentivos
mysql_select_db($database_vacantes, $vacantes);
$query_incidencias = "SELECT inc_captura.IDmatriz, Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5, Count(inc_captura.inc5) AS CINC5, Sum(inc_captura.inc6) AS INC6, inc_semanas_meses.IDmes, inc_semanas_meses.anio, vac_meses.mes FROM inc_captura LEFT JOIN inc_semanas_meses ON inc_captura.semana = inc_semanas_meses.IDsemana LEFT JOIN vac_meses ON vac_meses.IDmes = inc_semanas_meses.IDmes WHERE inc_captura.IDmatriz = '$IDmatriz' AND inc_captura.anio = '$anio'  AND inc_semanas_meses.IDsemana = '$the_semana' GROUP BY inc_semanas_meses.IDsemana"; 
$incidencias = mysql_query($query_incidencias, $vacantes) or die(mysql_error());
$row_incidencias = mysql_fetch_assoc($incidencias);

//productividad
mysql_select_db($database_vacantes, $vacantes);
$query_productividad = "SELECT prod_captura.IDmatriz, Sum(prod_captura.sueldo_total) AS SUELDOS, Sum(prod_captura.adicional2) AS PROD2, Sum(prod_captura.pago_total) AS PROD1, inc_semanas_meses.IDmes,  inc_semanas_meses.anio,  vac_meses.mes FROM prod_captura INNER JOIN inc_semanas_meses ON inc_semanas_meses.IDsemana = prod_captura.semana INNER JOIN vac_meses ON vac_meses.IDmes = inc_semanas_meses.IDmes WHERE prod_captura.anio = '$anio' AND prod_captura.IDmatriz = '$IDmatriz'  AND inc_semanas_meses.IDsemana = '$the_semana' GROUP BY inc_semanas_meses.IDsemana"; 
$productividad = mysql_query($query_productividad, $vacantes) or die(mysql_error());
$row_productividad = mysql_fetch_assoc($productividad);

//incentivos
mysql_select_db($database_vacantes, $vacantes);
$query_elmes = "SELECT inc_semanas_meses.IDsemana, inc_semanas_meses.IDmes, inc_semanas_meses.anio, vac_meses.mes FROM inc_semanas_meses left JOIN vac_meses ON vac_meses.IDmes = inc_semanas_meses.IDmes WHERE inc_semanas_meses.IDsemana = '$the_semana'"; 
$elmes = mysql_query($query_elmes, $vacantes) or die(mysql_error());
$row_elmes = mysql_fetch_assoc($elmes);

?>
									      <tr>
									        <td><?php echo $the_semana; ?></td>
									        <td><?php echo $row_elmes['mes']; ?></td>
									        <td><?php if($row_incidencias['INC1'] > $prom_INC1)
											{echo "<strong>" . "$" . number_format($row_incidencias['INC1']) . "</strong> <i class='icon-warning text-danger'></i>";} 
											else {echo "$" . number_format($row_incidencias['INC1']); }?></td>                                           
									        <td><?php if($row_incidencias['INC2'] > $prom_INC2)
											{echo "<strong>" . "$" . number_format($row_incidencias['INC2']) . "</strong> <i class='icon-warning text-danger'></i>";} 
											else {echo "$" . number_format($row_incidencias['INC2']); }?></td>                                           
									        <td><?php if($row_incidencias['INC3'] > $prom_INC3)
											{echo "<strong>" . "$" . number_format($row_incidencias['INC3']) . "</strong> <i class='icon-warning text-danger'></i>";} 
											else {echo "$" . number_format($row_incidencias['INC3']); }?></td>                                           
									        <td><?php if($row_incidencias['INC4'] > $prom_INC4)
											{echo "<strong>" . "$" . number_format($row_incidencias['INC4']) . "</strong> <i class='icon-warning text-danger'></i>";} 
											else {echo "$" . number_format($row_incidencias['INC4']); }?></td>                                           
									        <td><?php if($row_incidencias['INC5'] > $prom_INC5)
											{echo "<strong>" . "$" . number_format($row_incidencias['INC5']) . "</strong> <i class='icon-warning text-danger'></i>";} 
											else {echo "$" . number_format($row_incidencias['INC5']); }?> 
											| <?php echo $row_incidencias['CINC5'];?></td>                                           
									        <td><?php $gasto = $row_productividad['PROD1'] + $row_productividad['PROD2'];
											echo "$" . number_format($gasto); ?></td>                                           
									        <td><?php
											$sueldos = ($row_productividad['SUELDOS'] /30 ) *7;
											if($gasto != 0) {$porc = round(($gasto / $sueldos) * 100, 0);} else {$porc = 0;}
											if($porc > 37) {echo "<strong>" . $porc . "%</strong> <i class='icon-warning text-danger'></i>"; } else {echo $porc . "%"; } ?>
                                            </td>                                           
                    </tr>
									      <?php } while ($row_semana = mysql_fetch_assoc($semana)); ?>

                                          
                    </tbody>
                   </table> 

						</div>
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