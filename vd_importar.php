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
$mi_fecha =  date('Y/m/d');


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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
set_time_limit(0);

if (isset($_POST['el_mes'])) {$IDmes = $_POST['el_mes'];} else { $IDmes = date("m") - 1; }
if (isset($_POST['el_anio'])) {$anio = $_POST['el_anio'];}


extract($_POST);
$status = "";
    if (isset($_POST["import"])){
		
//borramos para cargar de nuevo
$query_borrar = "DELETE FROM com_vd_temp WHERE IDvd > 0";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
		
		
//cargamos el archivo al servidor con el mismo nombre(solo le agregue el sufijo bak_)
$archivo = $_FILES['file']['name']; //captura el nombre del archivo
$tipo = $_FILES['file']['type']; //captura el tipo de archivo (2003 o 2007)
 
$destino = "bak_".$archivo; //lugar donde se copiara el archivo


if (copy($_FILES['file']['tmp_name'],$destino)) //si dese copiar la variable excel (archivo).nombreTemporal a destino (bak_.archivo) (si se ha dejado copiar)
{
echo "Archivo Cargado Con Exito"; 
header("Location: vd_importar.php?info=1"); 	
}
else
{
echo "Error Al Cargar el Archivo"; 
header("Location: vd_importar.php?info=2"); 
}
 
////////////////////////////////////////////////////////
if (file_exists ("bak_".$archivo)) //validacion para saber si el archivo ya existe previamente
{
/*INVOCACION DE CLASES Y CONEXION A BASE DE DATOS*/
/** Invocacion de Clases necesarias */
chmod("bak_".$archivo, 0755);
require_once('assets/PHPExcel.php');
require_once('assets/PHPExcel/Reader/Excel2007.php');
$conn = mysqli_connect($hostname_vacantes, $username_vacantes ,$password_vacantes, $database_vacantes);


// Cargando la hoja de calculo
$objReader = new PHPExcel_Reader_Excel2007(); //instancio un objeto como PHPExcelReader(objeto de captura de datos de excel)
$objPHPExcel = $objReader->load("bak_".$archivo); //carga en objphpExcel por medio de objReader,el nombre del archivo
$objFecha = new PHPExcel_Shared_Date();

// Asignar hoja de excel activa
$objPHPExcel->setActiveSheetIndex(0); //objPHPExcel tomara la posicion de hoja (en esta caso 0 o 1) con el setActiveSheetIndex(numeroHoja)

// Llenamos un arreglo con los datos del archivo xlsx
$i=2; //celda inicial en la cual empezara a realizar el barrido de la grilla de excel
$param=0;
$contador=0;
while($param==0) //mientras el parametro siga en 0 (iniciado antes) que quiere decir que no ha encontrado un NULL entonces siga metiendo datos
{


$chek1 =	$objPHPExcel->getActiveSheet()->getCell('A1')->getCalculatedValue();
if ($chek1 != 'Sucursal') {header("Location: vd_importar.php?info=9"); }
$chek2 =	$objPHPExcel->getActiveSheet()->getCell('Q1')->getCalculatedValue();
if ($chek2 != 'Margen Bruto') {header("Location: vd_importar.php?info=9"); }
$chek3 =	$objPHPExcel->getActiveSheet()->getCell('A2')->getCalculatedValue();
if ($chek3 == '') {	header("Location: vd_importar.php?info=9"); }

$Sucursal = 		$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
$SucursalM =  utf8_decode(trim(strtoupper($Sucursal)));

//busca matrix
$query_zmatriz = "SELECT * FROM com_vd_matriz WHERE matriz = '$SucursalM'";
$zmatriz = mysql_query($query_zmatriz, $vacantes) or die(mysql_error());
$row_zmatriz = mysql_fetch_assoc($zmatriz);

$IDmatriza =		$row_zmatriz['IDmatriz'];
$IDempleado_s =		$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
$Clave =			$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
$IDempleado =		$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
$VentaNeta =		$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
$VentaNetaCajas =	$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
$VentaNetaPieza =	$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
$ClientesVenta =	$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
$NoPedidos =		$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
$Visitas =			$objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
$DevImporte =		$objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
$DevPorc =			$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
$Presupuesto =		$objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
$Cubrimiento =		$objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
$MargenBruto =		$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();

if($IDempleado > 0){
$query_empleado = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);

$emp_paterno = $row_empleado['emp_paterno'];
$emp_materno = $row_empleado['emp_materno'];
$emp_nombre = $row_empleado['emp_nombre'];
$denominacion  = $row_empleado['denominacion'];
$fecha_antiguedad  = $row_empleado['fecha_antiguedad'];
$IDpuesto  = $row_empleado['IDpuesto'];
} else {  
$emp_paterno = "";
$emp_materno = "";
$emp_nombre = "VACANTE";
$denominacion  = "VACANTE";
$IDpuesto  = 0;
}

//insertamos
$query = "insert into com_vd_temp (fecha_antiguedad, IDmatriz, IDempleadoS, Clave, IDempleado, emp_paterno, emp_materno, emp_nombre, denominacion, IDpuesto, VentaNeta, VentaNetaCajas, VentaNetaPieza, ClientesVenta, NoPedidos, Visitas, DevImporte, DevPorc, Presupuesto, Cubrimiento, MargenBruto, fecha_importacion, IDmes, anio) values ('".$fecha_antiguedad."', '".$IDmatriza."', '".$IDempleado_s."', '". $Clave."', '". $IDempleado."', '". $emp_paterno."','". $emp_materno."','". $emp_nombre."','". $denominacion."','". $IDpuesto."','".   $VentaNeta ."', '".$VentaNetaCajas."',	'". $VentaNetaPieza."','". $ClientesVenta."','". $NoPedidos."','". $Visitas ."','".$DevImporte ."','".  $DevPorc ."', '".$Presupuesto ."','".$Cubrimiento ."','".$MargenBruto ."','".$mi_fecha."','".$IDmes."','".$anio."')";
$result = mysqli_query($conn, $query) or die(mysql_error());
					
if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()==NULL) //pregunto que si ha encontrado un valor null en una columna inicie un parametro en 1 que indicaria el fin del ciclo while
{
$param=1; //para detener el ciclo cuando haya encontrado un valor NULL
}
$i++;
$contador=$contador+1;
}
$totalIngresados=$contador-1; //(porque se se para con un NULL y le esta registrando como que tambien un dato)
//echo "Total elementos subidos: $totalIngresados "; 
header("Location: vd_importar.php?info=3&el_mes=$IDmes"); 
}
else//si no se ha cargado el bak
{
//echo "Necesitas primero importar el archivo"; 
header("Location: vd_importar.php?info=2&el_mes=$IDmes"); 
}
unlink($destino); //desenlazar a destino el lugar donde salen los datos(archivo)
}

$query_meses = "SELECT * FROM vac_meses";
mysql_query("SET NAMES 'utf8'");
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_amatriz = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

// si ya se calculo comisiones
$query_bt_01a = "SELECT * FROM com_vd_temp WHERE bt_01 != '' AND BonoProductividad != ''";
$bt_01a = mysql_query($query_bt_01a, $vacantes) or die(mysql_error());
$row_bt_01a = mysql_fetch_assoc($bt_01a);
$totalRows_bt_01a = mysql_num_rows($bt_01a);

// si ya se calculo comisiones suypers
$query_bt_02a = "SELECT * FROM com_vd_temp WHERE bt_01 != '' AND IDpuesto = 235";
$bt_02a = mysql_query($query_bt_02a, $vacantes) or die(mysql_error());
$row_bt_02a = mysql_fetch_assoc($bt_02a);
$totalRows_bt_02a = mysql_num_rows($bt_02a);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
<?php if(isset($_GET['info'])) {$info = $_GET['info']; } else {$info = 0;} ?>

						<?php if($info == 1) {  ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han pre cargado correctamente las Comisiones.</a>
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 2) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Problema al importar Empleados.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 3) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han pre cargado correctamente las Comisiones.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 7) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 6) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han calculado correctamente las Comisiones.
					    </div>
					    <!-- /basic alert -->
						<?php } else if($info == 9) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo Cargado no es tiene el formato correcto.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 4) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Puesto agregado.
					    </div>
					    <!-- /basic alert -->


						<?php } else if($info == 9) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El mes ya existe.
					    </div>
					    <!-- /basic alert -->
						<?php } else if($info == 5) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Empleado Borrado.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Comisiones VD</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">Instrucciones:</br>
                                    1. Importar Excel (archivo .xlsx)</br>
                                    2. Calacular Comisiones.</br>
                                    3. Confirmar resultado.<p>

                             <!-- Basic text input -->
                             <form action="vd_importar.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
							 
							 <fieldset class="content-group">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Archivo (.xlsx):</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xlsx" class="form-control" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->
                              
                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Mes:</label>
								  <div class="col-lg-9">
                            <select name="el_mes" id="el_mes" class="form-control" >
							<?php do { ?>
							   <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $IDmes))) 
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
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Año:</label>
								  <div class="col-lg-9">
									<select name="el_anio" id="el_anio"  class="form-control" >
									<option value="2025"<?php if (!(strcmp(2025, $anio))) {echo "selected=\"selected\"";} ?>>2025</option>
									<option value="2024"<?php if (!(strcmp(2024, $anio))) {echo "selected=\"selected\"";} ?>>2024</option>
									<option value="2023"<?php if (!(strcmp(2023, $anio))) {echo "selected=\"selected\"";} ?>>2023</option>
									<option value="2022"<?php if (!(strcmp(2022, $anio))) {echo "selected=\"selected\"";} ?>>2022</option>
									</select>
								  </div>
							  </div>
							  <!-- /basic text input -->
							  
							  
  							 </fieldset>

                              
  						<?php
								mysql_select_db($database_vacantes, $vacantes);
								$query_puestos = "SELECT com_vd_temp.*, vac_matriz.matriz, Jefes.IDempleado AS jefe_IDempleado, Jefes.emp_paterno AS jefe_paterno, Jefes.emp_materno AS jefe_materno, Jefes.emp_nombre AS jefe_nombre, Jefes.denominacion AS jefe_denominacion, Jefes.IDpuesto  AS jefe_IDpuesto FROM com_vd_temp LEFT JOIN prod_activos AS Empleados ON com_vd_temp.IDempleado = Empleados.IDempleado LEFT JOIN prod_activos AS Jefes ON com_vd_temp.IDempleadoS = Jefes.IDempleado LEFT JOIN vac_matriz ON com_vd_temp.IDmatriz = vac_matriz.IDmatriz";
								mysql_query("SET NAMES 'utf8'");
								$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
								$row_puestos = mysql_fetch_assoc($puestos);
								$totalRows_puestos = mysql_num_rows($puestos);
								$IDms = $row_puestos['IDmes'];

						?>  

							  
                            <div>
						<input type="hidden" name="importar" id="importar" value"">
                         <button type="submit" id="submit" name="import" class="btn btn-info">Importar Datos</button> 
						 
						 <?php if ($totalRows_puestos > 0 and $totalRows_bt_01a == 0) { ?>
						 <button type="button" onClick="window.location.href='vd_importar_update.php?IDmes=<?php echo $row_puestos['IDmes'] ?>'" class="btn btn-primary">Calcular Comisiones</button>
						 <?php } ?>
						 						 
						 <?php if ($totalRows_puestos > 0 && $row_puestos['calculado'] == 1) { ?>
						 <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-success">Cargar Resultado</button>
						 <?php } ?>
						 
						 <?php if ($totalRows_puestos > 0 ) { ?>
						 <button type="button" onClick="window.location.href='vd_importar_update.php?borrar=1'" class="btn btn-danger">Borrar</button>
						 <?php } ?>
                            </div>
                             </form>

						</div>
						</div>

							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Resultado</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">

					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-indigo-600">
                          <th>No. Emp.</th>
                          <th>No. Sup.</th>
                          <th>Garant.</th>
                          <th>Empleado</th>
                          <th>Mes</th>
                          <th>Fecha Ant.</th>
                          <th>Clave</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
                          <th>Bono Transporte</th>
                          <th>Bono Productividad</th>
                          <th>Premios</th>
                          <th>Comisiones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { 
						$nombre = $row_puestos['emp_paterno']." ".$row_puestos['emp_materno']." ".$row_puestos['emp_nombre']; ?>
						
						
                        <?php if ($row_puestos['IDpuesto'] == 235) { ?>
						
						<?php
						$IDmatriz = $row_puestos['IDmatriz'];
						$IDsuper = $row_puestos['IDempleadoS'];

						mysql_select_db($database_vacantes, $vacantes);
						$query_objetivos_mes = "SELECT * FROM com_vd_objetivo_mes WHERE IDmes = $IDmes AND IDmatriz = $IDmatriz AND anio = $anio";
						$objetivos_mes = mysql_query($query_objetivos_mes, $vacantes) or die(mysql_error());
						$row_objetivos_mes = mysql_fetch_assoc($objetivos_mes);
						$totalRows_objetivos_mes = mysql_num_rows($objetivos_mes);

						// objetivos
						$ObjetivoVenta = $row_objetivos_mes['objetivo_venta'];
						$ObjetivoClientesVenta = $row_objetivos_mes['objetivo_clientes_venta'];

						//resultados
						$query_Montos = "SELECT com_vd_temp.IDempleadoS, SUM(com_vd_temp.bt_01) as Monto1a, SUM(com_vd_temp.bt_02) as Monto2a, SUM(com_vd_temp.bt_03) as Monto3a, SUM(com_vd_temp.bt_04) as Monto4a, SUM(com_vd_temp.bt_05) as Monto5a, COUNT(com_vd_temp.bt_01) as Monto1b, COUNT(com_vd_temp.bt_02) as Monto2b, COUNT(com_vd_temp.bt_03) as Monto3b, COUNT(com_vd_temp.bt_04) as Monto4b, COUNT(com_vd_temp.bt_05) as Monto5b, SUM(com_vd_temp.VentaNeta) as MontoVentaNeta, SUM(com_vd_temp.ClientesVenta) as MontoClientesVenta,  AVG(MargenBRuto) as Margen FROM com_vd_temp WHERE com_vd_temp.IDempleadoS = $IDsuper";
						$Montos = mysql_query($query_Montos, $vacantes) or die(mysql_error());
						$row_Montos = mysql_fetch_assoc($Montos);
						$Montos = mysql_num_rows($Montos);
						$MontoVentaNeta = $row_Montos['MontoVentaNeta'];
						
												
												
						$Margen = $row_Montos['Margen'];
						if ($MontoVentaNeta >= $ObjetivoVenta) { 
						
							      if ($Margen >= 0.12){$FactorComision = 0.0035;}
							 else if ($Margen >= 0.11 AND $Margen < 0.12) {$FactorComision = 0.0030;} 
							 else if ($Margen >= 0.10 AND $Margen < 0.11) {$FactorComision = 0.0025;} 
							 else {$FactorComision = 0.0020;}
						
						
						} else if ($MontoVentaNeta >= ($ObjetivoVenta *0.9) AND $MontoVentaNeta < $ObjetivoVenta) { 
						
							      if ($Margen >= 0.12){$FactorComision = 0.0025;}
							 else if ($Margen >= 0.11 AND $Margen < 0.12) {$FactorComision = 0.0020;} 
							 else if ($Margen >= 0.10 AND $Margen < 0.11) {$FactorComision = 0.0010;} 
							 else {$FactorComision = 0.0005;}
						
						} else {
						
						$FactorComision = 0;
						
						}
												

						?>
						
                          <tr>
                            <td><?php if($row_puestos['IDempleado'] != 0) {echo $row_puestos['IDempleado']; } else {echo "-";} ?></td>
                            <td><?php if($row_puestos['IDempleadoS'] != 0) {echo $row_puestos['IDempleadoS']; } else {echo "-";} ?></td>
                            <td><?php if($row_puestos['IDgarantizado'] == 1) {echo "SI"; } else {echo "NO";} ?></td>
                            <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>"><?php if($row_puestos['IDempleado'] != 0) {echo $nombre; } else {echo "VACANTE";} ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul>
								<li><strong>Venta Neta: </strong><?php echo "$" . number_format($row_Montos['MontoVentaNeta']); ?>&nbsp;</li>
								<li><strong>Objetivo Venta: </strong><?php echo "$" . number_format($ObjetivoVenta); ?>&nbsp;</li>
								<li><strong>Clientes con Venta: </strong><?php echo $row_Montos['MontoClientesVenta']; ?>&nbsp;</li>
								<li><strong>Objetivo Clientes con Venta: </strong><?php echo "$" . number_format($ObjetivoClientesVenta); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
                            <td><?php 
								$le_mes = $row_puestos['IDmes'];
								$query_mesess = "SELECT * FROM vac_meses WHERE IDmes = '$le_mes'";
								$mesess = mysql_query($query_mesess, $vacantes) or die(mysql_error());
								$row_mesess = mysql_fetch_assoc($mesess);
								$totalRows_mesess = mysql_num_rows($mesess);
								echo $row_mesess['mes']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row_puestos['fecha_antiguedad'])); ?></td>
                            <td><?php echo $row_puestos['Clave']; ?></td>
                            <td><?php if($row_puestos['IDempleado'] != 0) {echo $row_puestos['denominacion']; } else {echo "VACANTE";} ?></td>
                            <td><?php echo $row_puestos['matriz']; ?></td>
							 <td>
							<?php $monto_transporte = $row_puestos['bt_01'] + $row_puestos['bt_02'] + $row_puestos['bt_03'] + $row_puestos['bt_04'] + $row_puestos['bt_05']; ?>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>BT"><?php echo "$" . number_format($monto_transporte); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>BT" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_01_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_01']); ?>&nbsp;</li>
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_02_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_02']); ?>&nbsp;</li>
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_03_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_03']); ?>&nbsp;</li>
								<li><strong><?php if ($row_puestos['bt_04_fecha'] != '') { echo date( 'd/m/Y', strtotime($row_puestos['bt_04_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_04']); }?>&nbsp;</li>
								<li><strong><?php if ($row_puestos['bt_05_fecha'] != '') { echo date( 'd/m/Y', strtotime($row_puestos['bt_05_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_05']); } ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>BP"><?php echo "$" . number_format($row_puestos['BonoProductividad']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>BP" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Venta Neta: </strong><?php echo "$" . number_format($row_Montos['MontoVentaNeta']); ?>&nbsp;</li>
								<li><strong>Objetivo Venta: </strong><?php echo "$" . number_format($ObjetivoVenta); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>PR"><?php echo "$" . number_format($row_puestos['Premios']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>PR" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Premio Venta Neta: </strong><?php echo "$" . number_format($row_puestos['Premios']); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>CO"><?php echo "$" . number_format($row_puestos['Comisiones']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>CO" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Venta Neta: </strong><?php echo "$" . number_format($row_Montos['MontoVentaNeta']); ?>&nbsp;</li>
								<li><strong>Factor: </strong><?php echo ($FactorComision * 10000)."%"; ?>&nbsp;</li>
								</ul>
							</div>
							</td>
                           </tr>                         
						
						
						
						
						
                        <?php } else { ?>
						
						
                          <tr>
                            <td><?php if($row_puestos['IDempleado'] != 0) {echo $row_puestos['IDempleado']; } else {echo "-";} ?></td>
                            <td><?php if($row_puestos['IDempleadoS'] != 0) {echo $row_puestos['IDempleadoS']; } else {echo "-";} ?></td>
                            <td><?php if($row_puestos['IDgarantizado'] == 1) {echo "SI"; } else {echo "NO";} ?></td>
                            <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>"><?php if($row_puestos['IDempleado'] != 0) {echo $nombre; } else {echo "VACANTE";} ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul>
								<li><strong>Venta Neta: </strong><?php echo "$" . number_format($row_puestos['VentaNeta']); ?>&nbsp;</li>
								<li><strong>Venta Neta Cajas: </strong><?php echo "$" . number_format($row_puestos['VentaNetaCajas']); ?>&nbsp;</li>
								<li><strong>Venta Neta Piezas: </strong><?php echo "$" . number_format($row_puestos['VentaNetaPieza']); ?>&nbsp;</li>
								<li><strong>Clientes con Venta: </strong><?php echo $row_puestos['ClientesVenta']; ?>&nbsp;</li>
								<li><strong>Número de Pedidos: </strong><?php echo $row_puestos['NoPedidos']; ?>&nbsp;</li>
								<li><strong>Visitas: </strong><?php echo $row_puestos['Visitas']; ?>&nbsp;</li>
								<li><strong>Devoluciones $: </strong><?php echo "$" . number_format($row_puestos['DevImporte']); ?>&nbsp;</li>
								<li><strong>Devoluciones %: </strong><?php echo round($row_puestos['DevPorc'] * 100, 2) ."%"; ?>&nbsp;</li>
								<li><strong>Presupuesto: </strong><?php echo "$" . number_format($row_puestos['Presupuesto']); ?>&nbsp;</li>
								<li><strong>Cubrimiento %: </strong><?php echo round($row_puestos['Cubrimiento'] * 100, 2) ."%"; ?>&nbsp;</li>
								<li><strong>Margen Bruto: </strong><?php echo round($row_puestos['MargenBruto'] * 100, 2) ."%"; ?>&nbsp;</li>
								</ul>
							</div>
							</td>
                            <td><?php 
								$le_mes = $row_puestos['IDmes'];
								$query_mesess = "SELECT * FROM vac_meses WHERE IDmes = '$le_mes'";
								$mesess = mysql_query($query_mesess, $vacantes) or die(mysql_error());
								$row_mesess = mysql_fetch_assoc($mesess);
								$totalRows_mesess = mysql_num_rows($mesess);
								echo $row_mesess['mes']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row_puestos['fecha_antiguedad'])); ?></td>
                            <td><?php echo $row_puestos['Clave']; ?></td>
                            <td><?php if($row_puestos['IDempleado'] != 0) {echo $row_puestos['denominacion']; } else {echo "VACANTE";} ?></td>
                            <td><?php echo $row_puestos['matriz']; ?></td>
							 <td>
							<?php $monto_transporte = $row_puestos['bt_01'] + $row_puestos['bt_02'] + $row_puestos['bt_03'] + $row_puestos['bt_04'] + $row_puestos['bt_05']; ?>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>BT"><?php echo "$" . number_format($monto_transporte); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>BT" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_01_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_01']); ?>&nbsp;</li>
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_02_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_02']); ?>&nbsp;</li>
								<li><strong><?php echo date( 'd/m/Y', strtotime($row_puestos['bt_03_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_03']); ?>&nbsp;</li>
								<li><strong><?php if ($row_puestos['bt_04_fecha'] != '') { echo date( 'd/m/Y', strtotime($row_puestos['bt_04_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_04']); }?>&nbsp;</li>
								<li><strong><?php if ($row_puestos['bt_05_fecha'] != '') { echo date( 'd/m/Y', strtotime($row_puestos['bt_05_fecha'])); ?> : </strong><?php echo "$" . number_format($row_puestos['bt_05']); } ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>BP"><?php echo "$" . number_format($row_puestos['BonoProductividad']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>BP" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Venta Neta : </strong><?php echo "$" . number_format($row_puestos['BonoVentaNeta']); ?>&nbsp;</li>
								<li><strong>Clientes con Venta : </strong><?php echo "$" . number_format($row_puestos['BonoClientesVenta']); ?>&nbsp;</li>
								<li><strong>Devoluciones : </strong><?php echo "$" . number_format($row_puestos['BonoDevPorc']); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>PR"><?php echo "$" . number_format($row_puestos['Premios']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>PR" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Premio Venta Neta : </strong><?php echo "$" . number_format($row_puestos['Premios']); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
							 <td>
							<a class="collapsed text-indigo-600" data-toggle="collapse" href="#collapse-group<?php echo $row_puestos['IDvd']; ?>CO"><?php echo "$" . number_format($row_puestos['Comisiones']); ?><span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_puestos['IDvd']; ?>CO" class="panel-collapse collapse">
							<p>&nbsp;</p>
								<ul class="media-list">
								<li><strong>Pieza: </strong><?php echo "$" . number_format($row_puestos['Comisiones_pieza']); ?>&nbsp;</li>
								<li><strong>Caja : </strong><?php echo "$" . number_format($row_puestos['Comisiones_caja']); ?>&nbsp;</li>
								</ul>
							</div>
							</td>
                           </tr>                         
						 <?php } ?>
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>

                         <?php } else { ?>
                         <td colspan="6">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
				    </table>
				</div>                   
	</div>
	</div>
    </div>
					<!-- /Contenido -->
					
					
					                <!-- danger modal -->
									<div id="modal_theme_danger" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Importación</h6>
												</div>
												<div class="modal-body">
												<?php //comprobar si el mes a cargar ya existe
												mysql_select_db($database_vacantes, $vacantes);
												$query_resultado2 = "SELECT * FROM com_vd WHERE IDmes = '$IDms' AND anio = '$anio' GROUP BY IDmes";
												$resultado2 = mysql_query($query_resultado2, $vacantes) or die(mysql_error());
												$row_resultado2 = mysql_fetch_assoc($resultado2);
												$totalRows_resultado2 = mysql_num_rows($resultado2);
												if ( $totalRows_resultado2 > 0 ) { echo "<p>El mes cargado ya existe. Por favor validar</p>";} else {echo "<p>¿Estas seguro que quieres cargar la información?</p>"; }	?>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												<?php if ( $totalRows_resultado2 == 0 ) { ?>
													<a class="btn btn-success" href="vd_importar_update_carga.php?">Si cargar</a>
												<?php } ?>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->


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