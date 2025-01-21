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
$el_usuario = $row_usuario['IDusuario'];

$la_matriz = $row_usuario['IDmatriz'];
$matriz_importa = $_GET['IDmatriz'];
$captura = $_SESSION['kt_login_id'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$IDsmatriz = $_GET['IDmatriz']; 						

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
							Se ha agregado correctamente la información.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Importar Incentivos</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group"><strong>Instrucciones:</strong></br>
                                    1.- Utiliza la <a href="PXV/inc_reporte_semana_incentivos.php?IDmatriz=<?php echo $IDmatriz; ?>">CEDULA</a> de Personal Activo, ya que de otra forma, no se importarán los datos.</br>
                                    2.- Valida que se hayan cargado correctamente los montos, descargando el acumulado y verificando que las cifras coincidan.</br>
                                              

	<?php
	$type = 0;
	$conn = mysqli_connect($hostname_vacantes,$username_vacantes ,$password_vacantes,$database_vacantes);
	require_once('importar/vendor/php-excel-reader/excel_reader2.php');
	require_once('importar/vendor/SpreadsheetReader.php');
	//abre1
	if (isset($_POST["import"])){

	  $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		//abre2
	  if(in_array($_FILES["file"]["type"],$allowedFileType)){ 

			$targetPath = 'importar/uploads/'.$_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
			
			$Reader = new SpreadsheetReader($targetPath);
			
				$Reader->ChangeSheet(0);

				echo "<span class='text text-muted'>Empleados importados: "; 

				//abre 3
				foreach ($Reader as $Row)
				{
						// determinamos el numero de empleado
						$ELempleado = "";
						if(isset($Row[0])) {
							$ELempleado = mysqli_real_escape_string($conn,$Row[0]);  
						}

						$errorIDmatriz = 0;
						mysql_select_db($database_vacantes, $vacantes);
						$query_errores = "SELECT prod_activos.IDmatriz FROM prod_activos WHERE prod_activos.IDempleado = '$ELempleado'"; 
						$errores = mysql_query($query_errores, $vacantes) or die(mysql_error());
						$row_errores = mysql_fetch_assoc($errores);
						
						//echo $row_errores['IDmatriz']." ".$IDsmatriz;
						
						if ($row_errores['IDmatriz'] != $IDsmatriz) {$errorIDmatriz = 1;} 
						
						echo $ELempleado." ";

						$IDmotivo3 = "";
						if(isset($Row[8])) {
							$IDmotivo3 = mysqli_real_escape_string($conn,$Row[8]);
						}
						
						$inc3 = "";
						if(isset($Row[6])) {
							$inc3 = mysqli_real_escape_string($conn,$Row[6]);
						}
						
						echo  ": ".$inc3."<br/>";


						$obs3 = "";
						if(isset($Row[7])) {
							$obs3 = mysqli_real_escape_string($conn,$Row[7]);
						}

						$emp_paterno = "";
						if(isset($Row[1])) {
							$emp_paterno = mysqli_real_escape_string($conn,$Row[1]);
						}

						$emp_materno = "";
						if(isset($Row[2])) {
							$emp_materno = mysqli_real_escape_string($conn,$Row[2]);
						}

						$emp_nombre = "";
						if(isset($Row[3])) {
							$emp_nombre = mysqli_real_escape_string($conn,$Row[3]);
						}

					// abre 4
					if (!empty($ELempleado) && is_numeric($ELempleado) && $errorIDmatriz == 0) {
						
						
							// seleccionamos lo que falta
							mysql_select_db($database_vacantes, $vacantes);
							$query_datos= "SELECT prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.IDpuesto, vac_puestos.denominacion, inc_captura.IDcaptura, inc_captura.IDmatriz, inc_captura.semana FROM prod_activos LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN inc_captura ON prod_activos.IDempleado = inc_captura.IDempleado AND inc_captura.semana =  $semana AND inc_captura.anio = $anio WHERE prod_activos.IDempleado =  $ELempleado"; 
							$datos = mysql_query($query_datos, $vacantes) or die(mysql_error());
							$row_datos = mysql_fetch_assoc($datos);
							$totalRows_datos= mysql_num_rows($datos);
							$la_llave = $row_datos['IDcaptura'];
							
						// abre 5
						if ($la_llave == "") { 

								$fecha_captura = $fecha;
								$la_semana = $semana;
								$IDsmatriz = $_GET['IDmatriz']; 
								$capturador = $captura;
 								$IDPuesto = $row_datos['IDpuesto'];
								$sueldo_diario = $row_datos['sueldo_diario'];
								
								//carga		
								$query = "INSERT INTO inc_captura (IDempleado, emp_paterno, emp_materno, emp_nombre, semana, anio, IDmatriz, IDpuesto, fecha_captura, inc3, IDmotivo3, obs3, capturador)	values ('".$ELempleado."','". $emp_paterno."','". $emp_materno."','". $emp_nombre."','". $la_semana."','". $anio."','". $IDsmatriz ."','". $IDPuesto ."','". $fecha_captura ."','". $inc3 ."','". $IDmotivo3 ."','". $obs3 ."','". $el_usuario ."')";
								$result = mysqli_query($conn, $query) or die(mysql_error());

								if (!empty($result)) { $type = 1; } else { $type = 2;}    
							

							// si ya existe en la base 		
							} else { 								

								$IDpuesto = $row_datos['IDpuesto']; 
								$puestob = $row_datos['denominacion'];
								$fecha_captura = $fecha;
								$la_semana = $semana;
								$IDsmatriz = $_GET['IDmatriz']; 
								$capturador = $captura;
								$sueldo_diario = $row_datos['sueldo_diario'];

								// actualiza	
								$query = "UPDATE inc_captura SET IDmotivo3 = '$IDmotivo3', inc3 = '$inc3', obs3 = '$obs3', capturador = '$capturador' WHERE inc_captura.IDempleado = '$ELempleado' AND inc_captura.semana = '$semana' AND inc_captura.anio = $anio";
								$result = mysqli_query($conn, $query) or die(mysql_error());

								if (!empty($result)) { $type = 1; } else { $type = 2;}   

						}//cierra5
					} //cierra4
			} // cierra3
		 } else { $type = 3;}// cierra2
	} //cierra1
?>
<?php if($type == 1) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han importado correctamente los incentivos.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

<?php if($type == 2) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Problema al importar incentivos.
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

                             <!-- Basic text input -->
                             <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Cédula:</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xls,.xlsx" class="form-control">
								  </div>
							  </div>
							  <!-- /basic text input -->
                              
                            <p>&nbsp;</p>
                              
                            <div>
                         <button type="submit" id="submit" name="import" class="btn btn-primary">Importar Incentivos</button>
                            </div>
                             </form>

                            <p>&nbsp;</p>

    </div>
    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
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