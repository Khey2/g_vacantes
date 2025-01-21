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
mysql_query("SET NAMES 'utf8'");
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
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM vac_puestos LEFT JOIN prod_activos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE prod_activos.IDmatriz = $IDmatriz ORDER BY vac_puestos.denominacion ASC";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);


// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

$la_semana = $_GET['la_semana'];
$el_puesto = $_GET['el_puesto'];

if(isset($_POST['el_anio']) && $_POST['el_anio'] == '2020') { 

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.IDpuesto, vac_matriz.IDmatriz, inc_captura_2020.IDcaptura, inc_captura_2020.perc, inc_captura_2020.prima, inc_captura_2020.dias1, inc_captura_2020.dias2, inc_captura_2020.horas1, inc_captura_2020.horas2, inc_captura_2020.pprueba, inc_captura_2020.obs1, inc_captura_2020.obs2, inc_captura_2020.obs3, inc_captura_2020.obs4, inc_captura_2020.obs5, inc_captura_2020.IDmotivo1,  inc_captura_2020.IDmotivo2,  inc_captura_2020.IDmotivo3, inc_captura_2020.inc1 AS INC1, inc_captura_2020.inc2 AS INC2, inc_captura_2020.inc3 AS INC3,  inc_captura_2020.inc3, inc_captura_2020.inc4 AS INC4, inc_captura_2020.inc5 AS INC5, inc_captura_2020.inc6 As INC6, prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, inc_captura_2020.lul, inc_captura_2020.mal, inc_captura_2020.mil, inc_captura_2020.jul, inc_captura_2020.vil, inc_captura_2020.sal, inc_captura_2020.dol, inc_captura_2020.luf, inc_captura_2020.maf, inc_captura_2020.mif, inc_captura_2020.juf, inc_captura_2020.vif, inc_captura_2020.saf, inc_captura_2020.dof FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura_2020 ON inc_captura_2020.IDempleado = prod_activos.IDempleado AND inc_captura_2020.semana = '$la_semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDpuesto = '$el_puesto'";
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_costos = "SELECT Sum(inc_captura_2020.inc1) AS INC1, Sum(inc_captura_2020.inc2) AS INC2, Sum(inc_captura_2020.inc3) AS INC3, Sum(inc_captura_2020.inc4) AS INC4, Sum(inc_captura_2020.inc5) AS INC5, Sum(inc_captura_2020.inc6) AS INC6 FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura_2020 ON inc_captura_2020.IDempleado = prod_activos.IDempleado AND inc_captura_2020.semana = '$la_semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
$row_costos = mysql_fetch_assoc($costos);
$totalRows_costos = mysql_num_rows($costos);

} else {
	
	mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.IDpuesto, vac_matriz.IDmatriz, inc_captura.IDcaptura, inc_captura.perc, inc_captura.prima, inc_captura.dias1, inc_captura.dias2, inc_captura.horas1, inc_captura.horas2, inc_captura.pprueba, inc_captura.obs1, inc_captura.obs2, inc_captura.obs3, inc_captura.obs4, inc_captura.obs5, inc_captura.IDmotivo1,  inc_captura.IDmotivo2,  inc_captura.IDmotivo3, inc_captura.inc1 AS INC1, inc_captura.inc2 AS INC2, inc_captura.inc3 AS INC3,  inc_captura.inc3, inc_captura.inc4 AS INC4, inc_captura.inc5 AS INC5, inc_captura.inc6 As INC6, prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, inc_captura.lul, inc_captura.mal, inc_captura.mil, inc_captura.jul, inc_captura.vil, inc_captura.sal, inc_captura.dol, inc_captura.luf, inc_captura.maf, inc_captura.mif, inc_captura.juf, inc_captura.vif, inc_captura.saf, inc_captura.dof FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$la_semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDpuesto = '$el_puesto'";
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_costos = "SELECT Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5, Sum(inc_captura.inc6) AS INC6 FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$la_semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
$row_costos = mysql_fetch_assoc($costos);
$totalRows_costos = mysql_num_rows($costos);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
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


	                <!-- Content area -->
				<div class="content">
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte semanal de incidencias</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido. En esta sección, podrás reportar las incidencias semanales de la Sucursal. Para cualquier duda con la información capturada, contacta con Guadalupe Mendiola, a la Ext. 1219 o al correo <a href="mailto:GEMendiola@sahuayo.mx">GEMendiola@sahuayo.mx</a></p>
							<p><strong>Horas Extra:</strong> Captura los días y horas extras trabajadas. No puedes exceder de 9 horas y 3 días consecutivos. No aplica a todos los puestos.</p>
							<p><strong>Compensación por Suplencia:</strong> Captura los días y horas extras trabajadas. No puedes exceder de 9 horas y 3 días consecutivos. No aplica a todos los puestos.</p>
							<p><strong>Premios por Viaje.</strong> Captura premios por día, tanto locales como foráneos. En el apartado de captura, se muesta el monto y tope autorizado para cada puesto. Solo aplica a puestos de Distribución.</p>
							<p><strong>Incentivos:</strong> Captura el monto del incentivo y asegurate de capturar la justificación. Sujeto a revisión.</p>
							<p><strong>Domingos Laborados:</strong> El concepto de Prima Dominical aplica en todos los casos, pero no así la percepción. Sujeto a revisión y autorización.</p>
							<p>Utiliza el siguiente filtro para mostrar a los empleados por puesto o captura el nombre del empleado en el filtro rápido. Los empleados que se muestran, son los empleados activos en Nómina; al igual que la captura de productividad, la base se actualiza los jueves. Da clic en el nombre del empleado, para ver el histórico de pago.</p>


				<!-- Statistics with progress bar -->
					<div class="row">

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Horas Extra</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC1']; ?>
                                        <a href="inc_reporte_semana1.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">x Suplencia</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC2']; ?>
                                        <a href="inc_reporte_semana2.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">PxV</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC5']; ?>
                                        <a href="inc_reporte_semana5.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Incentivos</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:  </strong><?php echo $row_costos['INC3']; ?>
                                        <a href="inc_reporte_semana3.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>

						<div class="col-sm-2 col-md-2">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Domingos</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Monto:</strong> <?php echo $row_costos['INC4']; ?>
                                        <a href="inc_reporte_semana4.php?IDmatriz=<?php echo $IDmatriz; ?>&semana=<?php echo $la_semana; ?>"> <i class="icon-file-download"></i></a></span>
							</div>
						</div>


						<div class="col-sm-2 col-md-2">
						  <div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Semana</h6>
									</div>

									<div class="media-right media-middle">
									</div>
								</div>

							<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
						    </div>
									  <span class="text-muted"><?php echo $la_semana; ?></span>
						  </div>
						</div>

					</div>

					<!-- /statistics with progress bar -->

                                            <p>&nbsp;</p>

                  <form method="POST" action="inc_cap_puesto_dt.php?el_puesto=<?php echo $el_puesto; ?>&la_semana=<?php echo $la_semana; ?>">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                             <div class="col-lg-9">
                                 <select name="el_puesto" class="form-control">
                                   <option value="" <?php if (!(strcmp("", $el_puesto))) {echo "selected=\"selected\"";} ?>>Puesto: Todos</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if (!(strcmp($row_puestos['IDpuesto'], $el_puesto)))
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_puestos['denominacion']?> (<?php echo $row_puestos['area']?>)</option>
                                   <?php
                                  } while ($row_puestos = mysql_fetch_assoc($puestos));
                                  $rows = mysql_num_rows($puestos);
                                  if($rows > 0) {
                                      mysql_data_seek($puestos, 0);
                                      $row_puestos = mysql_fetch_assoc($puestos);
                                  } ?> </select>
						     </div>
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
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                            </td>
					      </tr>
					    </tbody>
				    </table>
				</form>



				<div class="table-responsive">
                    <table class="table datatable-show-all">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>H.Extra</th>
                      <th>x suplencia</th>
                      <th>PxV</th>
                      <th>Inc/Domingos</th>
                      <th>Domingos</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php do { 	?>
									    <tr>
									        <td><?php echo $row_detalle['IDempleado']; ?>&nbsp; </td>
									        <td><a href="inc_detalle_empleado.php?IDempleado=<?php echo $row_detalle['IDempleado']?>"><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?></a>&nbsp; </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
									        <td>
											<?php  echo $row_detalle['INC1']; ?>
											</td>
									        <td>
											<?php echo $row_detalle['INC2']; ?>
											</td>                                           
									        <td>
											<?php  echo $row_detalle['INC5']; ?>
											</td>  
									        <td>
											<?php  echo $row_detalle['INC3'] + $row_detalle['INC6']; ?>
											</td>                                           
									        <td>
											<?php  echo $row_detalle['INC4']; ?>
											 </td>                                           
										</tr>
									      <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 
					  </div>

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