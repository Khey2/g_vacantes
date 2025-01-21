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
$factor_integracion = $row_variables['factor_integracion'];


$currentPage = $_SERVER["PHP_SELF"];
mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el a�o anterior 
$semana = date("W", strtotime($la_fecha));

$colname_usuario = "-1";

if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

//recogemos datos del empleado	
$IDpprueba = $_GET['IDpprueba'];
mysql_select_db($database_vacantes, $vacantes);
$query_empleado = "SELECT pp_prueba.sueldo_actual, pp_prueba.sueldo_nuevo, pp_prueba.file, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDarea, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, pp_prueba.IDmatriz_destino, pp_prueba.IDarea_destino, pp_prueba.fecha_fin, pp_prueba.fecha_inicio, pp_prueba.val1, pp_prueba.val2, pp_prueba.val3, pp_prueba.val4, pp_prueba.IDestatus, pp_prueba.observaciones, prod_activos.descripcion_nomina, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.fecha_antiguedad, puesto_origen.clave_puesto AS clave_puesto_origen, puesto_origen.denominacion AS denominacion_origen, area_oringen.area AS area_origen, matriz_origen.matriz AS matriz_origen, matriz_destino.matriz as matriz_destino, area_destino.area AS area_destino, puesto_destino.denominacion AS denominacion_destino, puesto_destino.clave_puesto FROM pp_prueba LEFT JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos AS puesto_origen ON pp_prueba.IDpuesto = puesto_origen.IDpuesto LEFT JOIN vac_areas AS area_oringen ON puesto_origen.IDarea = area_oringen.IDarea LEFT JOIN vac_matriz AS matriz_origen ON pp_prueba.IDmatriz = matriz_origen.IDmatriz LEFT JOIN vac_matriz AS matriz_destino ON pp_prueba.IDmatriz_destino = matriz_destino.IDmatriz LEFT JOIN vac_puestos AS puesto_destino ON pp_prueba.IDpuesto_destino = puesto_destino.IDpuesto LEFT JOIN vac_areas AS area_destino ON puesto_destino.IDarea = area_destino.IDarea WHERE pp_prueba.IDpprueba = $IDpprueba";
mysql_query("SET NAMES 'utf8'");
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);

$deleteSQL = "UPDATE pp_prueba SET IDestatus = 6 WHERE IDpprueba = $IDpprueba";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

$nombre = $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre'];
$destino = $row_empleado['denominacion_destino'];
$fecha_archivos = date("dmY"); 
$fecha_antiguedad = date( 'd/m/Y', strtotime($row_empleado['fecha_antiguedad']));
$fecha_alta = date( 'd/m/Y', strtotime($row_empleado['fecha_alta']));
$fecha_inicio = date( 'd/m/Y', strtotime($row_empleado['fecha_inicio']));
$fecha_fin = date( 'd/m/Y', strtotime($row_empleado['fecha_fin']."+ 1 days"));
$fecha_fin_mprox_mes = "01/" . date( 'm/Y', strtotime($row_empleado['fecha_fin']."+ 1 months"));
$fecha_elaboracion = date("d-m-Y"); 
$sueldo_diario = $row_empleado['sueldo_nuevo'] / 30;
$sueldo_diario_i = $sueldo_diario * $factor_integracion;
$porcentaje_diferencia = ($row_empleado['sueldo_nuevo'] / $row_empleado['sueldo_actual']) - 1;

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PPP/formato2.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

	//datos fijos
	$objPHPExcel->getActiveSheet()->setCellValue('Z3', $fecha_elaboracion); 
	$objPHPExcel->getActiveSheet()->setCellValue('E8', $nombre); 
	$objPHPExcel->getActiveSheet()->setCellValue('U8', $row_empleado['IDempleado']); 
	$objPHPExcel->getActiveSheet()->setCellValue('Z8', $fecha_alta); 
	$objPHPExcel->getActiveSheet()->setCellValue('Z10', $fecha_antiguedad); 
	$objPHPExcel->getActiveSheet()->setCellValue('G10', $row_empleado['denominacion_origen']); 
	$objPHPExcel->getActiveSheet()->setCellValue('E12', $row_empleado['descripcion_nomina']); 
	$objPHPExcel->getActiveSheet()->setCellValue('U12', $row_empleado['matriz_origen']); 
	$objPHPExcel->getActiveSheet()->setCellValue('I14', $row_empleado['area_origen']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G16', $row_empleado['sueldo_actual']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G21', $row_empleado['denominacion_destino']); 
	$objPHPExcel->getActiveSheet()->setCellValue('T21', $row_empleado['clave_puesto']);
	$objPHPExcel->getActiveSheet()->setCellValue('U10', $row_empleado['clave_puesto_origen']); 
	$objPHPExcel->getActiveSheet()->setCellValue('F23', $row_empleado['descripcion_nomina']); 
	$objPHPExcel->getActiveSheet()->setCellValue('V23', $row_empleado['matriz_destino']); 
	$objPHPExcel->getActiveSheet()->setCellValue('V25', $row_empleado['area_destino']); 
	$objPHPExcel->getActiveSheet()->setCellValue('H31', $row_empleado['sueldo_nuevo']); 
	$objPHPExcel->getActiveSheet()->setCellValue('Y31', $fecha_fin); 
	$objPHPExcel->getActiveSheet()->setCellValue('Y34', $fecha_fin_mprox_mes); 
	$objPHPExcel->getActiveSheet()->setCellValue('G59', $row_empleado['observaciones']); 
	$objPHPExcel->getActiveSheet()->setCellValue('X37', $sueldo_diario); 
	$objPHPExcel->getActiveSheet()->setCellValue('X39', $sueldo_diario_i); 
	$objPHPExcel->getActiveSheet()->setCellValue('Q31', $porcentaje_diferencia); 

	$objPHPExcel->getActiveSheet()->mergeCells('u10:v10');
	$objPHPExcel->getActiveSheet()->mergeCells('Z3:AE3');
	$objPHPExcel->getActiveSheet()->mergeCells('E8:L8');
	$objPHPExcel->getActiveSheet()->mergeCells('U8:X8');
	$objPHPExcel->getActiveSheet()->mergeCells('Z8:AE8');
	$objPHPExcel->getActiveSheet()->mergeCells('G10:Q10');
	$objPHPExcel->getActiveSheet()->mergeCells('Z10:AE10');
	$objPHPExcel->getActiveSheet()->mergeCells('E12:Q12');
	$objPHPExcel->getActiveSheet()->mergeCells('U12:AE12');
	$objPHPExcel->getActiveSheet()->mergeCells('I14:P14');
	$objPHPExcel->getActiveSheet()->mergeCells('G16:K16');
	$objPHPExcel->getActiveSheet()->mergeCells('G21:Q21');
	$objPHPExcel->getActiveSheet()->mergeCells('T21:U21');
	$objPHPExcel->getActiveSheet()->mergeCells('F23:P23');
	$objPHPExcel->getActiveSheet()->mergeCells('V23:AE23');
	$objPHPExcel->getActiveSheet()->mergeCells('V25:AE25');
	$objPHPExcel->getActiveSheet()->mergeCells('H31:L31');
	$objPHPExcel->getActiveSheet()->mergeCells('Y31:AD31');
	$objPHPExcel->getActiveSheet()->mergeCells('Y34:AD34');
	$objPHPExcel->getActiveSheet()->mergeCells('G39:V39');
	$objPHPExcel->getActiveSheet()->mergeCells('X37:AB37');
	$objPHPExcel->getActiveSheet()->mergeCells('X39:AB39');



    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('AFECTACION');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
	
	
		// Redirect output to a client�s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=AFECTACION '.$fecha_archivos.' '.$nombre.'.xlsx');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
		exit;

?>