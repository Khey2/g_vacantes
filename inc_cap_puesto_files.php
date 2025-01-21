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
$restrict->addLevel("1");
$restrict->addLevel("2");
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
  $uploadObj->setFolder("files/");
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

if ((isset($_GET['IDfile'])) && ($_GET['borrar'] == 1)) {
  $deleteSQL = sprintf("DELETE FROM inc_files WHERE IDfile=%s",
                       GetSQLValueString($_GET['IDfile'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

	header('Location: inc_cap_puesto_files.php?info=2');

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

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.IDpuesto, vac_matriz.IDmatriz, inc_captura.IDcaptura, inc_captura.perc, inc_captura.prima, inc_captura.dias1, inc_captura.dias2, inc_captura.horas1, inc_captura.horas2, inc_captura.pprueba, inc_captura.obs1, inc_captura.obs2, inc_captura.obs3, inc_captura.obs4, inc_captura.obs5, inc_captura.IDmotivo1,  inc_captura.IDmotivo2,  inc_captura.IDmotivo3, inc_captura.inc1 AS INC1, inc_captura.inc2 AS INC2, inc_captura.inc3 AS INC3,  inc_captura.inc3, inc_captura.inc4 AS INC4, inc_captura.inc5 AS INC5,  inc_captura.inc6 AS INC6, prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, inc_captura.lul, inc_captura.mal, inc_captura.mil, inc_captura.jul, inc_captura.vil, inc_captura.sal, inc_captura.dol, inc_captura.luf, inc_captura.maf, inc_captura.mif, inc_captura.juf, inc_captura.vif, inc_captura.saf, inc_captura.dof, inc_files.incentivo, inc_files.nombre,  inc_files.IDfile FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio' LEFT JOIN inc_files ON inc_files.IDempleado = inc_captura.IDempleado WHERE prod_activos.IDmatriz = '$IDmatriz' AND inc_captura.IDcaptura > 0";
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

mysql_select_db($database_vacantes, $vacantes);
$query_costos = "SELECT Sum(inc_captura.inc1) AS INC1, Sum(inc_captura.inc2) AS INC2, Sum(inc_captura.inc3) AS INC3, Sum(inc_captura.inc4) AS INC4, Sum(inc_captura.inc5) AS INC5, Sum(inc_captura.inc6) AS INC6  FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio'WHERE prod_activos.IDmatriz = '$IDmatriz'";
$costos = mysql_query($query_costos, $vacantes) or die(mysql_error());
$row_costos = mysql_fetch_assoc($costos);
$totalRows_costos = mysql_num_rows($costos);


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
$ins_inc_files->addColumn("anio", "NUMERIC_TYPE", "POST", "anio");
$ins_inc_files->addColumn("nombre", "FILE_TYPE", "FILES", "nombre");
$ins_inc_files->addColumn("incentivo", "NUMERIC_TYPE", "POST", "incentivo");
$ins_inc_files->setPrimaryKey("IDfile", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsinc_files = $tNGs->getRecordset("inc_files");
$row_rsinc_files = mysql_fetch_assoc($rsinc_files);
$totalRows_rsinc_files = mysql_num_rows($rsinc_files);

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
<?php echo $tNGs->displayValidationRules();?>
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

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el archivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el archivo.
					    </div>
                        <?php } ?>



					<div class="panel-body">
							<p>Bienvenido. En esta sección, podrás cargar los archivos de justificación y/o autorización de las incidencias semanales reportadas.</p>
                            <p>Para cualquier duda con la información capturada, contacta con Guadalupe Mendiola, a la Ext. 1219 o al correo <a href="mailto:GEMendiola@sahuayo.mx">
                            GEMendiola@sahuayo.mx</a></p>
							<p>Utiliza el siguiente filtro para mostrar a los empleados por puesto o captura el nombre del empleado en el filtro rápido.</p>
                            <p><strong>Semana: </strong><?php echo $semana;	?> </p>



                                            <p>&nbsp;</p>




				<div class="table-responsive">
                    <table class="table datatable-show-all">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>Acciones</th>
                      <th>Cargados / Motivo</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php do { 	?>
									    <tr>
									        <td><?php echo $row_detalle['IDempleado']; ?>&nbsp; </td>
									        <td><a href="inc_detalle_empleado.php?IDempleado=<?php echo $row_detalle['IDempleado']?>"><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?></a>&nbsp; </td>
									        <td><?php echo $row_detalle['denominacion']; ?>&nbsp; </td>
									         <td>
											<button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDempleado']; ?>File" 
                                             data-toggle="modal" class="btn btn-info"> Cargar</button>
                                            <?php if($row_detalle['IDfile'] != 0 ) { ?>
                                 		   <a class="btn btn-danger" href="inc_cap_puesto_files.php?IDfile=<?php echo $row_detalle['IDfile']; ?>&borrar=1">Borrar</a>
											 <?php } ?>
											 </td>  
                                             <td>
											<?php if($row_detalle['nombre'] != 0 ) { 
											switch ($row_detalle['incentivo']) {
												case 0:  $el_estatus = "";      break;     
												case 2:  $el_estatus = "SUPLENCIA";      break;     
												case 4:  $el_estatus = "DOMINGOS";      break;    
												case 1:  $el_estatus = "HORAS EXTRA";      break;    
												case 3:  $el_estatus = "INCENTIVOS/DOMINGOS";      break;    
												case 5:  $el_estatus = "PxV";      break;    
												  } echo "<a href='files/" . $row_detalle['nombre'] . "'>" . $el_estatus . "</a>";} ?>
                                      
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
											<input type="file" class="form-control" name="nombre" id="nombre" required="required"/>
												</div>
											</div>
	                                    </div>
										<p>&nbsp;</p>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Incentivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select  name="incentivo" id="incentivo" class="form-control" required="required">
                                            <option value="">Seleccione...</option>
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
										<input type="hidden" name="anio" id="anio" value="<?php echo $anio ?>"/>
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