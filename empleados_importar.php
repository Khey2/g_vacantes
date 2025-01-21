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

if (!isset($_SESSION['la_empresa'])) {  $_SESSION['la_empresa'] =  $IDmatriz; } 
$la_empresa = $_SESSION['la_empresa'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if (isset($_GET['IDborrado'])) {
$IDempleado_borrar = $_GET['IDborrado'];
$sql = "DELETE FROM con_empleados_temp WHERE IDempleado_temp = '$IDempleado_borrar'";
$sql_ = mysql_query($sql) or die(mysql_error()); 
header("Location: empleados_importar.php?info=4"); 	
}

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

				$IDmatriz = "";
				if(isset($Row[0])) {$IDmatriz = utf8_decode(mysqli_real_escape_string($conn,$Row[0]));}

				$estatus = "";
				if(isset($Row[1])) {$estatus  = utf8_decode(mysqli_real_escape_string($conn,$Row[1]));}
				if($estatus == "ACTIVO"){$estatus_ = 1;} else {$estatus_ = 0;}

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
				if($a_sexo == "HOMBRE"){$a_sexo_ = 1;} else {$a_sexo_ = 2;}

				$a_imss = "";
				if(isset($Row[10])) {$a_imss = utf8_decode(mysqli_real_escape_string($conn,$Row[10]));}

				$IDnacionalidad = "";
				if(isset($Row[11])) {$IDnacionalidad = utf8_decode(mysqli_real_escape_string($conn,$Row[11]));}
				if($IDnacionalidad == "MEXICANA"){$IDnacionalidad_ = 1;} else {$IDnacionalidad_ = 2;}

				$a_estado_civil = "";
				if(isset($Row[12])) {$a_estado_civil = utf8_decode(mysqli_real_escape_string($conn,$Row[12]));}
				if($a_estado_civil == "CASADO" ){$a_estado_civil_ = 2;} elseif ($a_estado_civil == "SOLTERO") {$a_estado_civil_ = 1;} else{$a_estado_civil_ = 0;}
				
				$a_banco = "";
				if(isset($Row[13])) {$a_banco = mysqli_real_escape_string($conn,$Row[13]);}
				
				$a_cuenta_bancaria_clabe = "";
				if(isset($Row[14])) {$a_cuenta_bancaria_clabe =  utf8_decode(mysqli_real_escape_string($conn,$Row[14]));}

				$a_cuenta_bancaria = "";
				if(isset($Row[15])) {$a_cuenta_bancaria =  utf8_decode(mysqli_real_escape_string($conn,$Row[15]));}

				$c_fecha_nacimiento  = "";
				if(isset($Row[16])) {
					$y1 = substr( $Row[16], 6, 4 );
					$m1 = substr( $Row[16], 3, 2 );
					$d1 = substr( $Row[16], 0, 2 );
					$c_fecha_nacimiento = $y1."-".$m1."-".$d1;}

				$fecha_alta  = "";
				if(isset($Row[37])) {
					$y2 = substr( $Row[37], 6, 4 );
					$m2 = substr( $Row[37], 3, 2 );
					$d2 = substr( $Row[37], 0, 2 );
					$fecha_alta = $y2."-".$m2."-".$d2;}

				$d_calle = "";
				if(isset($Row[17])) {$d_calle =  utf8_decode(mysqli_real_escape_string($conn,$Row[17]));}

				$d_numero_calle = "";
				if(isset($Row[18])) {$d_numero_calle = utf8_decode(mysqli_real_escape_string($conn,$Row[18]));}

				$d_colonia = "";
				if(isset($Row[19])) {$d_colonia = utf8_decode(mysqli_real_escape_string($conn,$Row[19]));}

				$d_delegacion_municipio = "";
				if(isset($Row[20])) {$d_delegacion_municipio = utf8_decode(mysqli_real_escape_string($conn,$Row[20]));}

				$IDestado = "";
				if(isset($Row[21])) {$IDestado  = utf8_decode(mysqli_real_escape_string($conn,$Row[21]));}

				$d_codigo_postal = "";
				if(isset($Row[22])) {$d_codigo_postal = utf8_decode(mysqli_real_escape_string($conn,$Row[22]));}

				$IDmatriz2 = "";
				if(isset($Row[23])) {$IDmatriz2 = utf8_decode(mysqli_real_escape_string($conn,$Row[23]));}

				$IDcuenta = "";
				if(isset($Row[24])) {$IDcuenta = utf8_decode(mysqli_real_escape_string($conn,$Row[24]));}

				$IDsubcuenta = "";
				if(isset($Row[25])) {$IDsubcuenta = utf8_decode(mysqli_real_escape_string($conn,$Row[25]));}

				$local_foraneo = "";
				if(isset($Row[26])) {$local_foraneo = utf8_decode(mysqli_real_escape_string($conn,$Row[26]));}
				if($local_foraneo == "LOCAL") { $local_foraneo_ = 1; } elseif($local_foraneo == "FORANEO") { $local_foraneo_ = 2; } else { $local_foraneo_ = 0; }

				$b_sueldo_diario = "";
				if(isset($Row[27])) {$b_sueldo_diario = utf8_decode(mysqli_real_escape_string($conn,$Row[27]));
				if (strpos($b_sueldo_diario, '.') === false) { $b_sueldo_diario = $b_sueldo_diario.".00"; }}

				$b_sueldo_diario_int = "";
				if(isset($Row[28])) {$b_sueldo_diario_int = utf8_decode(mysqli_real_escape_string($conn,$Row[28]));}

				$b_sueldo_mensual = "";
				if(isset($Row[29])) {$b_sueldo_mensual = utf8_decode(mysqli_real_escape_string($conn,$Row[29]));}

				$tipo_de_contrato = "";
				if(isset($Row[30])) {$tipo_de_contrato = utf8_decode(mysqli_real_escape_string($conn,$Row[30]));}
				if($tipo_de_contrato == "DETERMINADO") { $tipo_de_contrato_ = 1; } elseif($tipo_de_contrato == "INDETERMINADO") { $tipo_de_contrato_ = 2; } else { $tipo_de_contrato_ = 0; }

				$IDpuesto = "";
				if(isset($Row[32])) {$IDpuesto = utf8_decode(mysqli_real_escape_string($conn,$Row[32]));}

				$beneficiario_nombre = "";
				if(isset($Row[33])) {$beneficiario_nombre = utf8_decode(mysqli_real_escape_string($conn,$Row[33]));}

				$beneficiario_direccion = "";
				if(isset($Row[34])) {$beneficiario_direccion = utf8_decode(mysqli_real_escape_string($conn,$Row[34]));}

				$beneficiario_telefono = "";
				if(isset($Row[35])) {$beneficiario_telefono = utf8_decode(mysqli_real_escape_string($conn,$Row[35]));}

				$beneficiario_parentesco = "";
				if(isset($Row[36])) {$beneficiario_parentesco = utf8_decode(mysqli_real_escape_string($conn,$Row[36]));}

				$query_empresa = "SELECT * FROM vac_matriz WHERE matriz = '$IDmatriz'";
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

				$query_cuenta = "SELECT * FROM con_cuentas WHERE cuenta = '$IDcuenta'";
				$cuenta = mysql_query($query_cuenta, $vacantes) or die(mysql_error());
				$row_cuenta = mysql_fetch_assoc($cuenta);
				$a_cuenta_ = $row_cuenta['IDcuenta']; 
				
				$query_subcuenta = "SELECT * FROM con_subcuentas WHERE subcuenta = '$IDsubcuenta'";
				$subcuenta = mysql_query($query_subcuenta, $vacantes) or die(mysql_error());
				$row_subcuenta = mysql_fetch_assoc($subcuenta);
				$a_subcuenta_ = $row_subcuenta['IDsubcuenta']; 

				switch ($beneficiario_parentesco) {
				case "Esposo(a), Concubino(a)": $beneficiario_parentesco_ = 1; break;    
				case "Padre": $beneficiario_parentesco_ = 2; break;    
				case "Madre": $beneficiario_parentesco_ = 3; break;    
				case "Hijo(a)": $beneficiario_parentesco_ = 4; break;    
				case "Abuelo(a)": $beneficiario_parentesco_ = 5; break;    
				case "Nieto(a)": $beneficiario_parentesco_ = 6; break;    
				case "Hermano(a)": $beneficiario_parentesco_ = 7; break;    
				case "Tio(a)": $beneficiario_parentesco_ = 8; break;    
				case "Sobirno(a)": $beneficiario_parentesco_ = 9; break;    
				case "Suegro(a)": $beneficiario_parentesco_ = 10; break;    
				case "Otro (sin parentezco familiar)": $beneficiario_parentesco_ = 11; break;    
				default: $beneficiario_parentesco_ = "NO DEFINIDO";  }
 
 				//abre 4
					if (!empty($IDempleado and $IDempleado != "" and $IDempleado != "IDempleado")) {
										
					//carga		
					$query = "insert into con_empleados_temp  (IDempleado, a_paterno, a_materno, a_nombre, a_correo, password, a_rfc, a_curp, a_sexo, a_imss, IDnacionalidad, a_estado_civil, a_banco, a_cuenta_bancaria_clabe, a_cuenta_bancaria, c_fecha_nacimiento,  d_calle, d_numero_calle, d_colonia, d_delegacion_municipio, IDestado, d_codigo_postal, estatus, IDmatriz, IDcuenta, IDsubcuenta, local_foraneo, b_sueldo_diario, b_sueldo_diario_int, b_sueldo_mensual, tipo_de_contrato, IDpuesto, beneficiario_nombre, beneficiario_direccion, beneficiario_telefono, beneficiario_parentesco, fecha_alta) values ('$IDempleado', '$a_paterno', '$a_materno','$a_nombre','$a_correo','$IDempleado', '$a_rfc', '$a_curp', '$a_sexo_', '$a_imss', '$IDnacionalidad_', '$a_estado_civil_', '$a_banco_','$a_cuenta_bancaria_clabe', '$a_cuenta_bancaria ', '$c_fecha_nacimiento' , '$d_calle', '$d_numero_calle', '$d_colonia', '$d_delegacion_municipio', '$a_estado_', '$d_codigo_postal', '$estatus_', '$IDmatriz_ ', 
'$a_cuenta_', '$a_subcuenta_', '$local_foraneo_', '$b_sueldo_diario', '$b_sueldo_diario_int', '$b_sueldo_mensual', '$tipo_de_contrato_', '$IDpuesto', '$beneficiario_nombre', '$beneficiario_direccion', '$beneficiario_telefono', '$beneficiario_parentesco_', '$fecha_alta')";
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
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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


						<?php if($type == 4) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado el registro correctamente.
					    </div>
					    <!-- /basic alert -->
						<?php } ?>

						<?php if($type == 5) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado el registro correctamente.
					    </div>
					    <!-- /basic alert -->
						<?php } ?>


					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Importar Empleados</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">Instrucciones para importar empelados activos:</p>
									<p><a href="EMP/cedula.xlsx">Descargue y complete el Layout</a> para la importación de Empleados.</p>
									<p>Asegurese de capturar todos los campos solicitados en el archivo.</p>
									<p>Solo se puede importar cuando no haya errores de validación.</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                             <!-- Basic text input -->
                             <form action="" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Archivo (.xlsx, .xls):</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xls,.xlsx"  class="file-styled" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->
                              
                            <p>&nbsp;</p>
                              
                            <div>
                         <button type="submit" id="submit" name="import" class="btn btn-primary">Cargar Archivo</button>
                            </div>
                             </form>
                            <p>&nbsp;</p>
                            
                            
                   <?php
$query_contratos = "SELECT
con_empleados_temp.IDempleado_temp,
con_empleados_temp.IDmatriz,
con_empleados_temp.estatus,
con_empleados_temp.IDempleado,
con_empleados_temp.a_paterno, 
con_empleados_temp.a_materno,
con_empleados_temp.a_nombre, 
con_empleados_temp.a_correo,
con_empleados_temp.a_rfc,
con_empleados_temp.a_curp,
con_empleados_temp.a_sexo,
con_empleados_temp.a_imss,
con_empleados_temp.IDnacionalidad,
con_empleados_temp.a_estado_civil,
con_empleados_temp.IDestado, 
con_empleados_temp.a_banco, 
con_empleados_temp.a_cuenta_bancaria_clabe, 
con_empleados_temp.a_cuenta_bancaria,
con_empleados_temp.fecha_alta, 
con_empleados_temp.c_fecha_nacimiento, 
con_empleados_temp.d_calle, 
con_empleados_temp.d_numero_calle, 
con_empleados_temp.d_colonia, 
con_empleados_temp.d_delegacion_municipio,
con_empleados_temp.d_estado,
con_empleados_temp.d_codigo_postal,
con_empleados_temp.local_foraneo,
con_empleados_temp.b_sueldo_diario,
con_empleados_temp.b_sueldo_diario_int,
con_empleados_temp.b_sueldo_mensual,
con_empleados_temp.tipo_de_contrato,
con_empleados_temp.IDpuesto,
con_empleados_temp.IDcuenta,
con_empleados_temp.IDsubcuenta,
con_empleados_temp.beneficiario_nombre,
con_empleados_temp.beneficiario_telefono,
con_empleados_temp.beneficiario_direccion,
con_empleados_temp.beneficiario_parentesco,
vac_matriz.matriz,
con_bancos.banco, 
con_cuentas.cuenta,
con_subcuentas.subcuenta
FROM con_empleados_temp
LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = con_empleados_temp.IDmatriz 
LEFT JOIN con_bancos ON con_bancos.IDbanco = con_empleados_temp.a_banco 
LEFT JOIN con_estados ON con_estados.IDestado = con_empleados_temp.IDestado 
LEFT JOIN con_cuentas ON con_cuentas.IDcuenta = con_empleados_temp.IDcuenta
LEFT JOIN con_subcuentas ON con_subcuentas.IDsubcuenta = con_empleados_temp.IDsubcuenta";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
				   
				   if ($totalRows_contratos > 0) {?>

					<h6 class="panel-title">Datos importados</h6>
					<p>Valide los datos importados.</p>

					<form action="empleados_temp.php" method="post">
                <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-right">
                    <button type="submit" id="submit" name="import" class="btn btn-danger">Borrar Seleccionados</button>
                    </div>
					</div>
				<!-- /colored button -->

					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>Select&nbsp; </th>
                      <th>IDempleado</th>
                      <th>Registro Patronal</th>
                      <th>Paterno</th>
                      <th>Materno</th>
                      <th>Nombre</th>
                      <th>RFC</th>
                      <th>Errores</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php $error = 0; ?> 
                        <?php do {

$cumple = 0;
// ver si ya existe							
$IDempleado = $row_contratos['IDempleado'];
$a_correo = $row_contratos['a_correo'];
$query_existentes = "SELECT * FROM con_empleados WHERE con_empleados.IDempleado = '$IDempleado' OR con_empleados.a_correo = '$a_correo'";
$existentes = mysql_query($query_existentes, $vacantes) or die(mysql_error());
$row_existentes = mysql_fetch_assoc($existentes);
$totalRows_existentes = mysql_num_rows($existentes);

// ver si ya existe							
$query_existentes2 = "SELECT * FROM con_empleados_temp WHERE con_empleados_temp.IDempleado = '$IDempleado' OR con_empleados_temp.a_correo = '$a_correo'";
$existentes2 = mysql_query($query_existentes2, $vacantes) or die(mysql_error());
$row_existentes2 = mysql_fetch_assoc($existentes2);
$totalRows_existentes2 = mysql_num_rows($existentes2);
?>
                          <tr>
                		    <th><input name="IDempleado_select[]" type="checkbox" value="<?php echo $IDempleado; ?>" class="styled"></th>
                            <td><?php echo $row_contratos['IDempleado']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['matriz']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['a_paterno']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['a_materno']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['a_nombre']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['a_rfc']; ?>&nbsp; </td>
                            <td>
							<?php if($totalRows_existentes > 0){echo "El empleado o usuario ya existe </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                            
                            <?php if($totalRows_existentes2 > 1){echo "Registro duplicado </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                            
							<?php if($row_contratos['IDmatriz'] == ""){echo "Sin empresa asignada </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>

							<?php if($row_contratos['IDpuesto'] == ""){echo "Sin puesto </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                            
							<?php if($row_contratos['a_paterno'] == "" 
									OR $row_contratos['a_materno'] == "" 
									OR $row_contratos['a_nombre'] == ""){echo "Nombre incompleto </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
							<?php if($row_contratos['a_rfc'] == "" 
									OR (strlen($row_contratos['a_rfc']) != 13) 
									OR $row_contratos['a_curp'] == "" 
									OR (strlen($row_contratos['a_curp']) != 18) 
									OR $row_contratos['a_imss'] == ""
									OR (strlen($row_contratos['a_imss']) < 10) 
									OR $row_contratos['a_estado_civil'] == ""
									OR $row_contratos['IDnacionalidad'] == ""){echo "Datos personales incompletos </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
							<?php if($row_contratos['b_sueldo_diario'] == "" 
									OR $row_contratos['b_sueldo_diario_int'] == "" 
									OR $row_contratos['b_sueldo_mensual'] == ""
									OR $row_contratos['tipo_de_contrato'] == ""
									OR $row_contratos['local_foraneo'] == ""){echo "Datos de pago incompletos </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
							<?php if($row_contratos['a_banco'] == "" 
									OR $row_contratos['a_cuenta_bancaria'] == "" 
									OR $row_contratos['a_cuenta_bancaria_clabe'] == ""){echo "Datos bancarios incompletos </br>";$error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
							<?php if($row_contratos['c_fecha_nacimiento'] == ""
									OR $row_contratos['fecha_alta'] == ""){echo "Fechas incompletas </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
							<?php if($row_contratos['a_rfc'] == "" 
									OR $row_contratos['d_calle'] == "" 
									OR $row_contratos['d_numero_calle'] == ""
									OR $row_contratos['d_colonia'] == ""
									OR $row_contratos['IDestado'] == "" 
									OR $row_contratos['d_codigo_postal'] == ""
									OR $row_contratos['d_delegacion_municipio'] == ""){echo "Direccion incompleta </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
                                    <?php if($row_contratos['beneficiario_nombre'] == "" 
									OR $row_contratos['beneficiario_telefono'] == "" 
									OR $row_contratos['beneficiario_parentesco'] == "" 
									OR $row_contratos['beneficiario_direccion'] == "" 
									){echo "Datos del Beneficiario incompletos </br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
                                    <?php if($row_contratos['IDcuenta'] == "" 
									OR $row_contratos['IDsubcuenta'] == ""){echo "Datos de Cuenta incompletos". "</br>"; $error = $error + 1; } else {$cumple = $cumple + 1;}?>
                                    
                            		<?php if($cumple == 12) { ?>Sin errores<?php } ?>
                            </td>
                            <td><a class="btn btn-warning" href="empleados_importar.php?IDborrado=<?php echo $row_contratos['IDempleado_temp']; ?>">Borrar</a>
                            <a class="btn btn-success" href="empleados_nuevo_importar.php?IDempleado_temp=<?php echo $row_contratos['IDempleado_temp']; ?>">Editar</a>
                           </td>
                          </tr>
                          <?php } while ($row_contratos = mysql_fetch_assoc($contratos)); ?>
                     </tbody>
					</table>
					</form>

				<?php $error = 0; if($error == 0){ ?> 

                <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-primary" href="empleados_importar_correcto.php">Importar Empleados<i class="icon-arrow-right14 position-right"></i></a>
                    </div>
					</div>
				<!-- /colored button -->

                   <?php } ?>
                   <?php } ?>

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