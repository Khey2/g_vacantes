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

$query_cap_ups = "SELECT prod_captura.IDcaptura, prod_captura.emp_paterno, prod_captura.emp_materno, prod_captura.emp_nombre, prod_captura.denominacion, prod_captura.IDempleado, prod_captura.IDpuesto, prod_captura.fecha_captura, prod_captura.semana, prod_captura.IDmatriz, prod_captura.IDsucursal, prod_captura.IDarea,prod_captura.capturador, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5,  prod_captura.pago, vac_matriz.matriz FROM prod_captura left JOIN vac_matriz ON vac_matriz.IDmatriz = prod_captura.IDmatriz WHERE prod_captura.IDpuesto IN (1, 38, 39, 47, 54, 55, 17, 56, 5000) AND prod_captura.semana = '$semana'  AND prod_captura.anio = '$anio'";
$cap_ups = mysql_query($query_cap_ups, $vacantes) or die(mysql_error());
$row_cap_ups = mysql_fetch_assoc($cap_ups);
$totalRows_cap_ups = mysql_num_rows($cap_ups);


	$type = 0;
	$conn = mysqli_connect($hostname_vacantes,$username_vacantes ,$password_vacantes,$database_vacantes);
	require_once('importar/vendor/php-excel-reader/excel_reader2.php');
	require_once('importar/vendor/SpreadsheetReader.php');
//abre1
if (isset($_POST["import"])){
	
  $deleteSQL = "DELETE FROM prod_captura WHERE prod_captura.IDpuesto IN (1, 38, 39, 47, 54, 55, 17, 56, 5000) AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio'";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
	

	  $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		//abre2
	  if(in_array($_FILES["file"]["type"],$allowedFileType)){ 

			$targetPath = 'importar/uploads/'.$_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
			
			$Reader = new SpreadsheetReader($targetPath);
			
				$Reader->ChangeSheet(0);
				//abre 3
				foreach ($Reader as $Row) {
						// determinamos el numero de empleado
						$ELempleado = "";
						if(isset($Row[0])) {
							$ELempleado = mysqli_real_escape_string($conn,$Row[0]);  
						}

						$indA1 = 0;
						if(isset($Row[6])) {
							$indA1 = mysqli_real_escape_string($conn,$Row[6]);
						}
						
						$indA2 = 0;
						if(isset($Row[7])) {
							$indA2 = mysqli_real_escape_string($conn,$Row[7]);
						}

						$indA3 = 0;
						if(isset($Row[8])) {
							$indA3 = mysqli_real_escape_string($conn,$Row[8]);
						}

						$indA4 = 0;
						if(isset($Row[9])) {
							$indA4 = mysqli_real_escape_string($conn,$Row[9]);
						}

					// abre 4
					if (!empty($ELempleado) && is_numeric($ELempleado)) {

					mysql_select_db($database_vacantes, $vacantes);
					$query_datos= "SELECT * FROM prod_activos WHERE prod_activos.IDempleado =  $ELempleado"; 
					$datos = mysql_query($query_datos, $vacantes) or die(mysql_error());
					$row_datos = mysql_fetch_assoc($datos);
					$totalRows_datos = mysql_num_rows($datos);
					
					if ($totalRows_datos > 0) { $IDpuesto = $row_datos['IDpuesto'];  } else { $IDpuesto = 5000; }
					$emp_paterno = $row_datos['emp_paterno']; 
					$emp_materno = $row_datos['emp_materno']; 
					$emp_nombre = $row_datos['emp_nombre']; 
					$denominacion = $row_datos['denominacion']; 
					$sueldo_total = $row_datos['sueldo_total']; 
					$IDempelado = $ELempleado; 
					$fecha_captura = $fecha;
					$IDmatriz = $row_datos['IDmatriz']; 
					$IDsucursal = $row_datos['IDsucursal']; 
					$IDarea = $row_datos['IDarea']; 
					$suma = ($indA1 + $indA2 + $indA3 + $indA4) / 10; 
					
					
					//carga		
					$query = "INSERT into prod_captura (emp_paterno, emp_materno, emp_nombre, denominacion, sueldo_total, IDempleado, IDpuesto, fecha_captura, semana, anio, IDmatriz, IDsucursal, IDarea, capturador, a1, a2, a3, a4, pago) values ('$emp_paterno', '$emp_materno', '$emp_nombre', '$denominacion', '$sueldo_total', '$ELempleado', '$IDpuesto', '$fecha_captura', '$semana', '$anio', '$IDmatriz', '$IDsucursal', '$IDarea', '$captura', '$indA1', '$indA2', '$indA3', '$indA4', '$suma')";
					$result = mysqli_query($conn, $query) or die(mysql_error());



						}//cierra5
						if (!empty($result)) { $type = 1; } else { $type = 2;}   
			} // cierra3
		 } else { $type = 3;}// cierra2
	} //cierra1
 if($type == 1) { header("Location: productividad_impotar_corpo.php?info=1"); }
 if($type == 2) { header("Location: productividad_impotar_corpo.php?info=2"); } 
 if($type == 3) { header("Location: productividad_impotar_corpo.php?info=3");} 
 
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

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha importado correctamente la información.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Error al importar.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo es incorrecto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Importar Productividad Corporativo</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">
                                  Selecciona el archivo a importar.</br>
                                  Asegurate de que no tenga modificaciones el archivo.</br>
                                 <strong> Si existen datos previamente capturados, se borrarán.</strong></p>

                             <!-- Basic text input -->
                             <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Selecciona el Layout de carga:</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xls,.xlsx" class="file-styled" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->
                              
                            <p>&nbsp;</p>
                              
                            <div>
                         <button type="submit" id="submit" name="import" class="btn btn-primary">Importar Productividad</button>
                         <button type="button" class="btn btn-success" onClick="window.location.href='productividad_captura_corpo.php'">Regresar</button>
                            </div>
                             </form>

                        <?php if ($totalRows_cap_ups > 0) { ?>

                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p><strong>Resultado de la importación.</strong> Validar que no haya errores antes de regresar al menú anterior.</p>
                            
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
                          <th>Indicador 1</th>
                          <th>Indicador 2</th>
                          <th>Indicador 3</th>
                          <th>Indicador 4</th>
                          <th>% Pago</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php do {  $el_puesto = $row_cap_ups['IDpuesto']; $la_matriz = $row_cap_ups['IDmatriz'];?>
                          <tr>
                            <td><?php echo $row_cap_ups['IDempleado']; ?></td>
                            <td><?php if($row_cap_ups['emp_paterno'] == '' or $row_cap_ups['emp_nombre'] == '') {echo "<span class='label label-flat border-danger text-danger-600'>Baja o Inactivo</span>";} else { echo $row_cap_ups['emp_paterno']; ?> <?php echo $row_cap_ups['emp_materno']; ?> <?php echo $row_cap_ups['emp_nombre']; } ?></td>
                            <td><?php echo $row_cap_ups['denominacion']; ?></td>
                            <td><?php echo $row_cap_ups['matriz']; ?></td>
                            <td><?php  $query_dA1 = "SELECT prod_kpis.p FROM prod_kpis WHERE IDpuesto = '$el_puesto'  AND prod_kpis.z <> 0 AND prod_kpis.IDmatriz = '$la_matriz' AND prod_kpis.a = 1";
								$dA1 = mysql_query($query_dA1, $vacantes) or die(mysql_error()); $row_dA1 = mysql_fetch_assoc($dA1); $totalRows_row_dA1 = mysql_num_rows($dA1);
								$i1 = 0; do { if ($row_dA1['p'] == $row_cap_ups['a1']) {$i1 = $i1 + 1;} } while ($row_dA1 = mysql_fetch_assoc($dA1)) ?>
                                <?php if ($i1 != 1) {echo "<span class='label label-flat border-danger text-danger-600'>Error:".$row_cap_ups['a1']."</span>";} 
								else {echo $row_cap_ups['a1'];} ?></td>
                                
                            <td><?php  $query_dA2 = "SELECT prod_kpis.p FROM prod_kpis WHERE IDpuesto = '$el_puesto'  AND prod_kpis.z <> 0 AND prod_kpis.IDmatriz = '$la_matriz' AND prod_kpis.a = 2";
								$dA2 = mysql_query($query_dA2, $vacantes) or die(mysql_error());  $row_dA2 = mysql_fetch_assoc($dA2); $totalRows_row_dA2 = mysql_num_rows($dA2);
								$i2 = 0; do { if ($row_dA2['p'] == $row_cap_ups['a2']) {$i2 = $i2 + 1;} } while ($row_dA2 = mysql_fetch_assoc($dA2)) ?>
                                <?php if ($i2 != 1) {echo "<span class='label label-flat border-danger text-danger-600'>Error:".$row_cap_ups['a2']."</span>";}
								else {echo $row_cap_ups['a2'];} ?></td>
                                
                            <td><?php  $query_dA3 = "SELECT prod_kpis.p FROM prod_kpis WHERE IDpuesto = '$el_puesto'  AND prod_kpis.z <> 0 AND prod_kpis.IDmatriz = '$la_matriz' AND prod_kpis.a = 3";
								$dA3 = mysql_query($query_dA3, $vacantes) or die(mysql_error());  $row_dA3 = mysql_fetch_assoc($dA3); $totalRows_row_dA3 = mysql_num_rows($dA3);
								$i3 = 0; do { if ($row_dA3['p'] == $row_cap_ups['a3']) {$i3 = $i3 + 1;} } while ($row_dA3 = mysql_fetch_assoc($dA3)) ?>
                                <?php if ($i3 != 1) {echo "<span class='label label-flat border-danger text-danger-600'>Error:".$row_cap_ups['a3']."</span>";} 
								else {echo $row_cap_ups['a3'];} ?></td>
                                
                            <td><?php  $query_dA4 = "SELECT prod_kpis.p FROM prod_kpis WHERE IDpuesto = '$el_puesto'  AND prod_kpis.z <> 0 AND prod_kpis.IDmatriz = '$la_matriz' AND prod_kpis.a = 4";
								$dA4 = mysql_query($query_dA4, $vacantes) or die(mysql_error()); $row_dA4 = mysql_fetch_assoc($dA4); $totalRows_row_dA4 = mysql_num_rows($dA4);
							 if ($totalRows_row_dA4 > 3) {
								$i4 = 0; do { if ($row_dA4['p'] == $row_cap_ups['a4']) {$i4 = $i4 + 1;} } while ($row_dA4 = mysql_fetch_assoc($dA4)); 
								if ($i4 != 1) {echo "<span class='label label-flat border-danger text-danger-600'>Error:".$row_cap_ups['a4']."</span>";}
								else {echo $row_cap_ups['a4'];} 
							} else {echo "-";} ?></td>
                                
                            <td><?php if($row_cap_ups['pago'] > 350) {echo "<span class='label label-flat border-danger text-danger-600'>".$row_cap_ups['pago']."%</span>";}
							else if($row_cap_ups['pago'] == 0) {echo "<span class='label label-flat border-warning text-warning-600'>".$row_cap_ups['pago']."%</span>";} 
							else {echo $row_cap_ups['pago']."%";} ?></td>
                           </tr>                         
                		 <?php } while ($row_cap_ups = mysql_fetch_assoc($cap_ups)); ?>
					    </tbody>
					    </tbody>
				    </table>
                         <?php } ?>
               
	</div>
    <div>
        
        
        

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