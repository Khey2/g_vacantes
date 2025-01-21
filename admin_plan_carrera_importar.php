<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

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

$la_matriz = $row_usuario['IDmatriz'];
$captura = $_SESSION['kt_login_id'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

set_time_limit(0);

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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html52.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<!-- /theme JS files -->

</head>
<body>	

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

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la información.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-danger alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Existen errores en el archivo cargado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Importar Plan de Carrera</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group"><strong>Instrucciones:</strong></br>
                                    1.- Utiliza el Layout autorizado, ya que de otra forma, no se importarán los datos.</br>
                                    2.- Valida la información cargada. Los empelados con datos serán sustituidos.</br>
                                    3.- Clic en borrar o sube un nuevo Layout para iniciar de nuevo.</br>
                                              

	<?php
	$type = 0;
	$conn = mysqli_connect($hostname_vacantes,$username_vacantes ,$password_vacantes,$database_vacantes);
	require_once('importar/vendor/php-excel-reader/excel_reader2.php');
	require_once('importar/vendor/SpreadsheetReader.php');

	$error = 0;

	//abre1
	if (isset($_POST["import"])){
		
		mysqli_query($conn, "truncate TABLE pc_semaforo_temp"); 

	  $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		//abre2
	  if(in_array($_FILES["file"]["type"],$allowedFileType)){ 

			$targetPath = 'importar/uploads/'.$_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
			
			$Reader = new SpreadsheetReader($targetPath);
			
				$Reader->ChangeSheet(0);
				
				//abre 3
				foreach ($Reader as $Row)
				{
						// determinamos el numero de empleado
						$ELempleado = "";
						if(isset($Row[0])) {
							$ELempleado = mysqli_real_escape_string($conn,$Row[0]);  
						}
						
						$IDpuesto = "";
						if(isset($Row[8])) {
							$IDpuesto = mysqli_real_escape_string($conn,$Row[8]);
						}
						
						$reqa = "";
						if(isset($Row[9])) {
							$reqa = mysqli_real_escape_string($conn,$Row[9]);
						}

						$reqb = "";
						if(isset($Row[10])) {
							$reqb = mysqli_real_escape_string($conn,$Row[10]);
						}

						$reqc = "";
						if(isset($Row[11])) {
							$reqc = mysqli_real_escape_string($conn,$Row[11]);
						}

						$reqd = "";
						if(isset($Row[12])) {
							$reqd = mysqli_real_escape_string($conn,$Row[12]);
						}

						$reqe = "";
						if(isset($Row[13])) {
							$reqe = mysqli_real_escape_string($conn,$Row[13]);
						}

						$reqf = "";
						if(isset($Row[14])) {
							$reqf = mysqli_real_escape_string($conn,$Row[14]);
						}

						$observaciones = "";
						if(isset($Row[7])) {
							$observaciones = mysqli_real_escape_string($conn,$Row[7]);
						}

					// borramos si hay errores
					if (is_numeric($ELempleado) AND ($IDpuesto == 'X' OR $reqa == 'X' OR $reqb == 'X' OR $reqc == 'X' OR $reqd == 'X' OR $reqe == 'X'  OR $reqf == 'X')) { $error = $error + 1; }
					
					//echo $ELempleado." ".$IDpuesto." ".$reqa." ".$reqb." ".$reqc." ".$reqd." ".$reqe." Error =".$error."<br/>";

					// abre 4
					if (!empty($ELempleado) && is_numeric($ELempleado)) {
						

							// seleccionamos lo que falta
							mysql_select_db($database_vacantes, $vacantes);
							$query_activo = "SELECT * FROM prod_activos WHERE IDempleado =  $ELempleado"; 
							$activo = mysql_query($query_activo, $vacantes) or die(mysql_error());
							$row_activo = mysql_fetch_assoc($activo);
							$totalRows_activo= mysql_num_rows($activo);

							// seleccionamos lo que falta
							mysql_select_db($database_vacantes, $vacantes);
							$query_datos= "SELECT * FROM pc_semaforo WHERE IDempleado =  $ELempleado"; 
							$datos = mysql_query($query_datos, $vacantes) or die(mysql_error());
							$row_datos = mysql_fetch_assoc($datos);
							$totalRows_datos= mysql_num_rows($datos);
							$la_llave = $row_datos['IDplan'];

							if ($totalRows_datos == 0)  { $estatus = 1;} else { $estatus = 0;}
							if ($totalRows_activo == 0) { $activo = 0; } else { $activo = 1; }
							
								//carga		
								$query = "INSERT into pc_semaforo_temp (IDempleado, IDpuesto, reqa, reqb, reqc, reqd, reqe, reqf, observaciones, estatus, activo)	values ('".$ELempleado."','". $IDpuesto."','". $reqa."','". $reqb."','". $reqc."','". $reqd."','". $reqe."','". $observaciones."','".$estatus."','".$activo."')";
								$result = mysqli_query($conn, $query) or die(mysql_error());

								if (!empty($result)) { $type = 1; } else { $type = 2;}    
							
					} //cierra4
			} // cierra3
		 } else { $type = 3;}// cierra2
	} //cierra1
						if ($error == 1) { 
					$type = 2; 	
					mysqli_query($conn, "truncate TABLE pc_semaforo_temp");
					}

?>
<?php if($type == 1) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han importado correctamente los empleados.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

<?php if($type == 2) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Problema al importar Empleados.
					    </div>
					    <!-- /basic alert -->

<?php } ?>
<?php if($type == 3) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Archivo no permitido.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

<?php mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.emp_paterno,  prod_activos.emp_materno,  prod_activos.emp_nombre,  pc_semaforo_temp.IDempleado, pc_semaforo_temp.IDplan, pc_semaforo_temp.reqa,  pc_semaforo_temp.reqb,  pc_semaforo_temp.reqc,  pc_semaforo_temp.reqd,  pc_semaforo_temp.reqe,  pc_semaforo_temp.reqf, pc_semaforo_temp.estatus,  pc_semaforo_temp.observaciones,  pc_semaforo_temp.activo,  prod_activos.IDmatriz,  prod_activos.denominacion,  vac_matriz.matriz FROM pc_semaforo_temp LEFT JOIN prod_activos ON  pc_semaforo_temp.IDempleado = prod_activos.IDempleado LEFT JOIN vac_matriz ON  prod_activos.IDmatriz = vac_matriz.IDmatriz";   
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

$query_detalle2 = "SELECT * FROM pc_semaforo_temp WHERE activo = 0";   
$detalle2 = mysql_query($query_detalle2, $vacantes) or die(mysql_error());
$row_detalle2 = mysql_fetch_assoc($detalle2);
$totalRows_detalle2 = mysql_num_rows($detalle2);
 ?>	
						<!-- Basic alert -->
                        <?php if($totalRows_detalle2 > 0) { ?>
					    <div class="alert bg-danger alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Existen empelados inactivos o dados de baja.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                             <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
								  <div class="col-lg-3">
									<input type="file" name="file" id="file" accept=".xls,.xlsx" class="form-control" required="required">
								  </div>
								  <label class="control-label col-lg-9">
								  <button type="submit" id="submit" name="import" class="btn btn-primary">1. Cargar Empleados</button>
									 <?php if ($totalRows_detalle  > 0) { ?>
									 <?php if ($totalRows_detalle2  == 0) { ?>
									   <button type="button" data-target="#modal_theme_danger2"  data-toggle="modal" class="btn btn-success">2. Importar Empleados</button>
									 <?php } ?>
									   <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-warning">3. Borrar Carga</button>
									 <?php } ?>
								  </label>
							  </div>
                             </form>

    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>

<?php if ($totalRows_detalle  > 0) { ?>	

	
				<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Matriz</th>
                      <th>No.Emp.</th>
                      <th>Empleado</th>
                      <th>Denominacion</th>
                      <th>Activo</th>
                      <th>Estatus</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php do {  ?>
                        <tr>
                            <td><?php echo $row_detalle['matriz']; ?></td>
                            <td><?php echo $row_detalle['IDempleado'];  ?></td>
                            <td><?php echo $row_detalle['emp_paterno'] . " " . $row_detalle['emp_materno'] . " " . $row_detalle['emp_nombre']; ?></td>
                            <td><?php echo $row_detalle['denominacion']; ?></td>
							<td><?php if ($row_detalle['activo'] == 1) {echo "SI";} else { echo "El empleado no Existe";} ?></td>
							<td><?php if ($row_detalle['estatus'] == 1) {echo "Sin datos cargados";} else { echo "Empleado ya registrado";} ?></td>
                        </tr>
                          <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 
				</div>

<?php } ?>	

                                    <!-- danger modal -->
									<div id="modal_theme_danger" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Borrado</h6>
												</div>

												<div class="modal-body">
													<p>¿Estas seguro que quieres borrar la información sin importar?</p>
												</div>

												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="admin_plan_carrera_importar2.php?info=3">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->

                                    <!-- danger modal -->
									<div id="modal_theme_danger2" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Borrado</h6>
												</div>

												<div class="modal-body">
													<p>¿Estas seguro que quieres cargar la información?</p>
												</div>

												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-success" href="admin_plan_carrera_importar2.php?info=1">Si cargar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->
      

					<!-- /Contenido -->

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