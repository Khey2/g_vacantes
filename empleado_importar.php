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
$restrict->addLevel("2");
$restrict->Execute();
//End Restrict Access To Page

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
$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$type = 0;
set_time_limit(0);

	//abre 1
    if (isset($_POST["import"])){

	$type = 0;
	$conn = mysqli_connect($hostname_vacantes,$username_vacantes ,$password_vacantes,$database_vacantes);
	require_once('importar/vendor/php-excel-reader/excel_reader2.php');
	require_once('importar/vendor/SpreadsheetReader.php');
    
	// borrado
	mysqli_query($conn, "TRUNCATE TABLE con_empleados_temp");

	$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
	  
		  //abre 2
		if(in_array($_FILES["file"]["type"],$allowedFileType)){
		
		$targetPath = 'importar/uploads/'.$_FILES['file']['name'];
		move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
		$Reader = new SpreadsheetReader($targetPath);
		$Reader->ChangeSheet(0);
	
	
			//abre 3
			foreach ($Reader as $Row)
			{
					// determinamos el numero de empleado
				$IDmatriz = "";
				if(isset($Row[0])) {$IDmatriz = utf8_decode(mysqli_real_escape_string($conn,$Row[0]));}
				
				$estatus = "";
				if(isset($Row[1])) {$estatus  = utf8_decode(mysqli_real_escape_string($conn,$Row[1]));}
				if($estatus = "ACTIVO"){$estatus_ = 1;} else {$estatus_ = 0;}
				
				$IDempleado = "";
				if(isset($Row[2])) {$IDempleado = mysqli_real_escape_string($conn,$Row[2]);}
				
				$a_paterno = "";
				if(isset($Row[3])) {$a_paterno =  utf8_decode(mysqli_real_escape_string($conn,$Row[3]));}
				
				$a_materno = "";
				if(isset($Row[4])) {$a_materno =  utf8_decode(mysqli_real_escape_string($conn,$Row[4]));}
				
				$a_nombre = "";
				if(isset($Row[5])) {$a_nombre =  utf8_decode(mysqli_real_escape_string($conn,$Row[5]));}

				$a_correo = "";
				if(isset($Row[6])) {$a_correo =  utf8_decode(mysqli_real_escape_string($conn,$Row[6]));}

				$a_rfc = "";
				if(isset($Row[7])) {$a_rfc = str_replace("-", "", mysqli_real_escape_string($conn,$Row[7]));}
				
				$a_curp = "";
				if(isset($Row[8])) {$a_curp = utf8_decode(mysqli_real_escape_string($conn,$Row[8]));}
				
				$a_sexo = "";
				if(isset($Row[9])) {$a_sexo = utf8_decode(mysqli_real_escape_string($conn,$Row[9]));}
				if($a_sexo = "HOMBRE"){$a_sexo_ = 1;} else {$a_sexo_ = 2;}

				$a_imss = "";
				if(isset($Row[10])) {$a_imss = utf8_decode(mysqli_real_escape_string($conn,$Row[10]));}
				
				$IDnacionalidad = "";
				if(isset($Row[11])) {$IDnacionalidad = utf8_decode(mysqli_real_escape_string($conn,$Row[11]));}
				if($IDnacionalidad = "MEXICANA"){$IDnacionalidad_ = 1;} else {$IDnacionalidad_ = 2;}
				
				$a_estado_civil = "";
				if(isset($Row[12])) {$a_estado_civil = utf8_decode(mysqli_real_escape_string($conn,$Row[12]));}
				if($a_estado_civil = "SOLTERO"){$a_estado_civil_ = 1;} else {$a_estado_civil_ = 2;}
				
				$a_banco = "";
				if(isset($Row[13])) {$a_banco = utf8_decode(mysqli_real_escape_string($conn,$Row[13]));}
				
				$a_cuenta_bancaria_clabe = "";
				if(isset($Row[14])) {$a_cuenta_bancaria_clabe =  utf8_decode(mysqli_real_escape_string($conn,$Row[14]));}
				
				$a_cuenta_bancaria = "";
				if(isset($Row[15])) {$a_cuenta_bancaria =  utf8_decode(mysqli_real_escape_string($conn,$Row[15]));}
				
				$fecha_alta = "";
				if(isset($Row[16])) {$fecha_alta = mysqli_real_escape_string($conn, date('Y/m/d', strtotime($Row[16])));}
								
				$fecha_nacimiento  = "";
				if(isset($Row[17])) {$fecha_nacimiento = mysqli_real_escape_string($conn,date('Y/m/d', strtotime($Row[17])));}
				
				$d_calle = "";
				if(isset($Row[18])) {$d_calle =  utf8_decode(mysqli_real_escape_string($conn,$Row[18]));}
				
				$d_numero_calle = "";
				if(isset($Row[19])) {$d_numero_calle = utf8_decode(mysqli_real_escape_string($conn,$Row[19]));}
				
				$d_colonia = "";
				if(isset($Row[20])) {$d_colonia = utf8_decode(mysqli_real_escape_string($conn,$Row[20]));}
				
				$d_delegacion_municipio = "";
				if(isset($Row[21])) {$d_delegacion_municipio = utf8_decode(mysqli_real_escape_string($conn,$Row[21]));}
				
				$IDestado = "";
				if(isset($Row[22])) {$IDestado  = utf8_decode(mysqli_real_escape_string($conn,$Row[22]));}
				
				$d_codigo_postal = "";
				if(isset($Row[23])) {$d_codigo_postal = utf8_decode(mysqli_real_escape_string($conn,$Row[23]));}
				
				$query_empresa = "SELECT * FROM vac_matriz WHERE empresa = '$IDmatriz'";
				$empresa = mysql_query($query_empresa, $vacantes) or die(mysql_error());
				$row_empresa = mysql_fetch_assoc($empresa);
				$IDmatriz_ = $row_empresa['IDmatriz'];

				$query_banco = "SELECT * FROM con_bancos WHERE banco = '$a_banco'";
				$banco = mysql_query($query_banco, $vacantes) or die(mysql_error());
				$row_banco = mysql_fetch_assoc($banco);
				$a_banco_ = $row_banco['IDbanco'];

				$query_estado = "SELECT * FROM con_estados WHERE estado = '$IDestado'";
				$estado = mysql_query($query_estado, $vacantes) or die(mysql_error());
				$row_estado = mysql_fetch_assoc($estado);
				$a_estado_ = $row_estado['IDestado'];
				
					//abre 4
					if (!empty($IDempleado)) {
										
					//carga		
					$query = "insert into con_empleados_temp (IDempleado, a_paterno, a_materno, a_nombre, a_correo, a_rfc, a_curp, a_sexo, a_imss, IDnacionalidad, a_estado_civil, a_banco, a_cuenta_bancaria_clabe, a_cuenta_bancaria, fecha_alta, c_fecha_nacimiento,  d_calle, d_numero_calle, d_colonia, d_delegacion_municipio, IDestado, d_codigo_postal, estatus, IDmatriz) values ('".$IDempleado."', '".  $a_paterno."', '". $a_materno."','".  $a_nombre."','".  $a_correo."', '".  $a_rfc."', '".  $a_curp."', '".  $a_sexo_."', '".  $a_imss."', '".  $IDnacionalidad_."', '".  $a_estado_civil_."', '".  $a_banco_."','".  $a_cuenta_bancaria_clabe."', '".  $a_cuenta_bancaria. "', '".	$fecha_alta."', '".	$c_fecha_nacimiento."', '".   $d_calle."','". $d_numero_calle."', '".  $d_colonia."', '".  $d_delegacion_municipio."', '".  $a_estado_."', '". $d_codigo_postal."', '".  $estatus_."', '".  $IDmatriz_ ."')";
					$result = mysqli_query($conn, $query) or die(mysql_error());
					if (!empty($result)) { $type = 1; } else { $type = 2;}    

					} //cierra 4
			} //cierra 3
		} else { $type = 3;} // cierra 2
	} //cierra 1
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

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

						<?php if($type == 1) {  ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han importado correctamente los empleados.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($type == 2) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Problema al importar Empleados.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($type == 3) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Archivo no permitido.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Productividad</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">Instrucciones para importar empelados activos:</p>
									<p class="content-group">En el archivo de CATEM:</br>
                                    1.- Utiliza la pestaña ACTIVOS Y SUSPENDIDOS.</br>
                                    2.- Cambia el nombre del puesto a MAYUSCULAS, y sin acentos ni espacios al inicio o final.</br>
                                    3.- Cambia el formato de fechas a YYYY/MM/DD.</br>


                             <!-- Basic text input -->
                             <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Archivo (.xlsx, .xls):</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xls,.xlsx" class="form-control">
								  </div>
							  </div>
							  <!-- /basic text input -->
                              
                            <p>&nbsp;</p>
                              
                            <div>
                         <button type="submit" id="submit" name="import" class="btn btn-primary">Importar Empleados</button>
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
						&copy; 2020. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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