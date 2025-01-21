<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

$restrict->addLevel("3");
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->Execute();
//End Restrict Access To Page

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

// Start trigger
$formValidation = new tNG_FormValidation();
$tNGs->prepareValidation($formValidation);
// End trigger

//start Trigger_FileUpload trigger
//remove this line if you want to edit the code by hand 
function Trigger_FileUpload(&$tNG) {
  $uploadObj = new tNG_FileUpload($tNG);
  $uploadObj->setFormFieldName("nombre");
  $uploadObj->setDbFieldName("nombre");
  $uploadObj->setFolder("expedientes/");
  $uploadObj->setMaxSize(1500);
  $uploadObj->setAllowedExtensions("pdf, doc, xlsx");
  $uploadObj->setRename("auto");
  return $uploadObj->Execute();
}
//end Trigger_FileUpload trigger

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

//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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

if(isset($_POST['semana'])) {$semana = $_POST['semana']; } 

if(isset($_POST['el_anio']) && $_POST['el_anio'] == '2020') { 

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.IDpuesto, vac_matriz.IDmatriz, inc_captura_2020.IDcaptura, inc_captura_2020.perc, inc_captura_2020.prima, inc_captura_2020.dias1, inc_captura_2020.dias2, inc_captura_2020.horas1, inc_captura_2020.horas2, inc_captura_2020.pprueba, inc_captura_2020.obs1, inc_captura_2020.obs2, inc_captura_2020.obs3, inc_captura_2020.obs4, inc_captura_2020.obs5, inc_captura_2020.IDmotivo1,  inc_captura_2020.IDmotivo2,  inc_captura_2020.IDmotivo3, inc_captura_2020.inc1 AS INC1, inc_captura_2020.inc2 AS INC2, inc_captura_2020.inc3 AS INC3,  inc_captura_2020.inc3, inc_captura_2020.inc4 AS INC4, inc_captura_2020.inc5 AS INC5, inc_captura_2020.inc6 AS INC6, prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, inc_captura_2020.lul, inc_captura_2020.mal, inc_captura_2020.mil, inc_captura_2020.jul, inc_captura_2020.vil, inc_captura_2020.sal, inc_captura_2020.dol, inc_captura_2020.luf, inc_captura_2020.maf, inc_captura_2020.mif, inc_captura_2020.juf, inc_captura_2020.vif, inc_captura_2020.saf, inc_captura_2020.dof, inc_files.incentivo, inc_files.nombre FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura_2020 ON inc_captura_2020.IDempleado = prod_activos.IDempleado AND inc_captura_2020.semana = '$semana' LEFT JOIN inc_files ON inc_files.IDempleado = inc_captura_2020.IDempleado WHERE prod_activos.IDmatriz = '$IDmatriz' AND inc_files.nombre > 0";
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_costos = "SELECT Sum(inc_captura_2020.inc1) AS INC1, Sum(inc_captura_2020.inc2) AS INC2, Sum(inc_captura_2020.inc3) AS INC3, Sum(inc_captura_2020.inc4) AS INC4, Sum(inc_captura_2020.inc5) AS INC5,  Sum(inc_captura_2020.inc6) AS INC6 FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura_2020 ON inc_captura_2020.IDempleado = prod_activos.IDempleado AND inc_captura_2020.semana = '$semana' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
$row_costos = mysql_fetch_assoc($costos);
$totalRows_costos = mysql_num_rows($costos);

} else {

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.IDpuesto, vac_matriz.IDmatriz, inc_captura.IDcaptura, inc_captura.perc, inc_captura.prima, inc_captura.dias1, inc_captura.dias2, inc_captura.horas1, inc_captura.horas2, inc_captura.pprueba, inc_captura.obs1, inc_captura.obs2, inc_captura.obs3, inc_captura.obs4, inc_captura.obs5, inc_captura.IDmotivo1,  inc_captura.IDmotivo2,  inc_captura.IDmotivo3, inc_captura.inc1 AS INC1, inc_captura.inc2 AS INC2, inc_captura.inc3 AS INC3,  inc_captura.inc3, inc_captura.inc4 AS INC4, inc_captura.inc5 AS INC5, inc_captura.inc6 AS INC6, prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, inc_captura.lul, inc_captura.mal, inc_captura.mil, inc_captura.jul, inc_captura.vil, inc_captura.sal, inc_captura.dol, inc_captura.luf, inc_captura.maf, inc_captura.mif, inc_captura.juf, inc_captura.vif, inc_captura.saf, inc_captura.dof, inc_files.incentivo, inc_files.nombre FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' LEFT JOIN inc_files ON inc_files.IDempleado = inc_captura.IDempleado WHERE prod_activos.IDmatriz = '$IDmatriz' AND inc_files.nombre > 0";
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_costos = "SELECT Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5,  Sum(inc_captura.inc6) AS INC6 FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
$row_costos = mysql_fetch_assoc($costos);
$totalRows_costos = mysql_num_rows($costos);


}

// Make an insert transaction instance
$ins_inc_files = new tNG_insert($conn_vacantes);
$tNGs->addTransaction($ins_inc_files);
// Register triggers
$ins_inc_files->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_inc_files->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_inc_files->registerTrigger("END", "Trigger_Default_Redirect", 99, "inc_cap_puesto_files.php?info=1");
$ins_inc_files->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$ins_inc_files->setTable("inc_files");
$ins_inc_files->addColumn("IDempleado", "NUMERIC_TYPE", "POST", "IDempleado");
$ins_inc_files->addColumn("semana", "NUMERIC_TYPE", "POST", "semana");
$ins_inc_files->addColumn("nombre", "FILE_TYPE", "FILES", "nombre");
$ins_inc_files->addColumn("incentivo", "NUMERIC_TYPE", "POST", "incentivo");
$ins_inc_files->setPrimaryKey("IDfile", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsinc_files = $tNGs->getRecordset("inc_files");
$row_rsinc_files = mysql_fetch_assoc($rsinc_files);
$totalRows_rsinc_files = mysql_num_rows($rsinc_files);

mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT * FROM prod_semanas";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);
$totalRows_semanas = mysql_num_rows($semanas);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

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
<?php echo $tNGs->displayValidationRules();?>
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
<?php
	echo $tNGs->getErrorMsg();
?>
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

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el archivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<div class="panel-body">
							<p>Bienvenido. En esta sección, podrás cargar los archivos de justificación y/o autorización de las incidencias semanales reportadas.</p>
                            <p>Para cualquier duda con la información capturada, contacta con Guadalupe Mendiola, a la Ext. 1219 o al correo <a href="mailto:GEMendiola@sahuayo.mx">
                            GEMendiola@sahuayo.mx</a></p>
							<p>Utiliza el siguiente filtro para mostrar a los empleados por semana o captura el nombre del empleado en el filtro rápido.</p>
                            <p><strong>Semana: </strong><?php echo $semana;	?> </p>



                                            <p>&nbsp;</p>


                  <form method="POST" action="admin_incidencias_files.php">
					<table class="table">
						<tbody>							  
							<tr>
                            <td>
                             <select name="el_anio" class="form-control">
                               <option value="2021"<?php if (!(strcmp($anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
                               <option value="2022"<?php if (!(strcmp($anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
                               <option value="2023"<?php if (!(strcmp($anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
                               <option value="2024"<?php if (!(strcmp($anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
                               <option value="2025"<?php if (!(strcmp($anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
                             </select>
                            </td>
							<td>
                             <div class="col-lg-9">
                                 <select name="semana" class="form-control">
                                   <option value="" <?php if (!(strcmp("", $semana))) {echo "selected=\"selected\"";} ?>>Semana: Actual</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_semanas['semana']?>"<?php if (!(strcmp($row_semanas['semana'], $semana)))
                                   {echo "selected=\"selected\"";} ?>>Semana <?php echo $row_semanas['semana']?></option>
                                   <?php
                                  } while ($row_semanas = mysql_fetch_assoc($semanas));
                                  $rows = mysql_num_rows($semanas);
                                  if($rows > 0) {
                                      mysql_data_seek($semanas, 0);
                                      $row_semanas = mysql_fetch_assoc($semanas);
                                  } ?> </select>
						     </div>
                            </td>
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
                      <th>Cargados / Motivo</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php do { 	?>
									    <tr>
									        <td><?php echo $row_detalle['IDempleado']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
                                             <td>
											<?php if($row_detalle['nombre'] != 0 ) { 
											switch ($row_detalle['incentivo']) {
												case 0:  $el_estatus = "";      break;     
												case 2:  $el_estatus = "SUPLENCIA";      break;     
												case 4:  $el_estatus = "DOMINGOS";      break;    
												case 1:  $el_estatus = "HORAS EXTRA";      break;    
												case 3:  $el_estatus = "INCENTIVOS";      break;    
												case 5:  $el_estatus = "PxV";      break;    
												  } echo "<a href='expedientes/" . $row_detalle['nombre'] . "'>" . $el_estatus . "</a>";} ?>
                                      
											</td>
										</tr>
                                        
                                        
                                        
                      <!-- Inline form modal -->
					<div id="modal_form_inline<?php echo $row_detalle['IDempleado']; ?>File" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Carga de Archivo</h5>
								</div>

            					<form method="post" class="form-horizontal form-validate-jquery" enctype="multipart/form-data" name="file" id="file"
                                action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" >
									<div class="modal-body">
                                    
                 		         <p>&nbsp;</p>
                                  (<?php echo $row_detalle['IDempleado']; ?>)
								  <?php echo $row_detalle['emp_paterno']; ?> <?php echo $row_detalle['emp_materno']; ?> <?php echo $row_detalle['emp_nombre']; ?>                                 <p>&nbsp;</p>
                                  
                                    <input type="hidden" name="IDempleado" id="IDempleado" value="<?php echo $row_detalle['IDempleado']; ?>" >
                                    <input type="hidden" name="semana" id="semana"  value="<?php echo $semana; ?>" >
                                    
									
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Monto">Archivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="file" class="form-control" name="nombre" id="nombre"/>
												</div>
											</div>
	                                    </div>
										<p>&nbsp;</p>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Incentivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select  name="incentivo" id="incentivo" class="form-control" required="required">
                                            <option value="2">Suplencia</option>
                                            <option value="4">Domingos</option>
                                            <option value="1">Horas Extra</option>
                                            <option value="3">Incentivos</option>
                                            <option value="5">PxV</option>
                                                  </select>
												</div>
											</div>
	                                    </div>
										<p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        <input type="submit" class="btn btn-primary" name="KT_Insert1" id="KT_Insert1" value="Cargar">
									</div>
								</form>
                                
							</div>
						</div>
					</div>
					<!-- /inline form modal -->
                                        
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