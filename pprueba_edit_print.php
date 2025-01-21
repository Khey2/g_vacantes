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
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
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
$query_empleado = "SELECT pp_prueba.sueldo_actual, pp_prueba.sueldo_nuevo, pp_prueba.file, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDarea, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, pp_prueba.IDmatriz_destino, pp_prueba.IDarea_destino, pp_prueba.fecha_fin, pp_prueba.fecha_inicio, pp_prueba.val1, pp_prueba.val2, pp_prueba.val3, pp_prueba.val4, pp_prueba.IDestatus, pp_prueba.observaciones, prod_activos.descripcion_nomina, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.fecha_antiguedad, puesto_origen.denominacion AS denominacion_origen, area_oringen.area AS area_origen, matriz_origen.matriz AS matriz_origen, matriz_destino.matriz as matriz_destino, area_destino.area AS area_destino, puesto_destino.denominacion AS denominacion_destino FROM pp_prueba LEFT JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos AS puesto_origen ON pp_prueba.IDpuesto = puesto_origen.IDpuesto LEFT JOIN vac_areas AS area_oringen ON puesto_origen.IDarea = area_oringen.IDarea LEFT JOIN vac_matriz AS matriz_origen ON pp_prueba.IDmatriz = matriz_origen.IDmatriz LEFT JOIN vac_matriz AS matriz_destino ON pp_prueba.IDmatriz_destino = matriz_destino.IDmatriz LEFT JOIN vac_puestos AS puesto_destino ON pp_prueba.IDpuesto_destino = puesto_destino.IDpuesto LEFT JOIN vac_areas AS area_destino ON puesto_destino.IDarea = area_destino.IDarea WHERE pp_prueba.IDpprueba = $IDpprueba";
mysql_query("SET NAMES 'utf8'");
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);
if($row_empleado['descripcion_nomina'] == 'Nomina Semanal Sahuayo') {$tipo_nomina = 1;} else {$tipo_nomina = 2;}


$nombre = $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre'];
$destino = $row_empleado['denominacion_destino'];
$fecha_archivos = date("dmY"); 
if ($row_empleado['val1'] == 1) {$val1 = "Si";} else {$val1 = "No";}
if ($row_empleado['val2'] == 1) {$val2 = "Si";} else {$val2 = "No";}
if ($row_empleado['val3'] == 1) {$val3 = "Si";} else {$val3 = "No";}
if ($row_empleado['val4'] == 1) {$val4 = "Si";} else {$val4 = "No";}
$fecha_antiguedad = date( 'd/m/Y', strtotime($row_empleado['fecha_antiguedad']));
$fecha_alta = date( 'd/m/Y', strtotime($row_empleado['fecha_alta']));
$fecha_inicio = date( 'd/m/Y', strtotime($row_empleado['fecha_inicio']));
$fecha_fin = date( 'd/m/Y', strtotime($row_empleado['fecha_fin']));
$fecha_elaboracion = date("d-m-Y"); 

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PPP/formato1.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

	//datos fijos
	$objPHPExcel->getActiveSheet()->setCellValue('W3', $fecha_elaboracion); 
	$objPHPExcel->getActiveSheet()->setCellValue('G11', $row_empleado['descripcion_nomina']); 
	$objPHPExcel->getActiveSheet()->setCellValue('S11', $row_empleado['matriz_origen']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G13', $row_empleado['IDempleado']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G14', $nombre); 
	$objPHPExcel->getActiveSheet()->setCellValue('G15', $fecha_antiguedad); 
	$objPHPExcel->getActiveSheet()->setCellValue('S15', $fecha_alta); 

	$objPHPExcel->getActiveSheet()->setCellValue('G17', $row_empleado['denominacion_origen']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G18', $row_empleado['sueldo_actual']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G20', $row_empleado['denominacion_destino']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G21', $row_empleado['sueldo_nuevo']); 
	$objPHPExcel->getActiveSheet()->setCellValue('G22', $fecha_inicio); 
	$objPHPExcel->getActiveSheet()->setCellValue('S22', $fecha_fin); 

	$objPHPExcel->getActiveSheet()->setCellValue('G26', $val1); 
	$objPHPExcel->getActiveSheet()->setCellValue('S26', $val2); 
	$objPHPExcel->getActiveSheet()->setCellValue('G28', $val3); 
	$objPHPExcel->getActiveSheet()->setCellValue('S28', $val4); 

	$objPHPExcel->getActiveSheet()->setCellValue('C47', $row_empleado['observaciones']); 

	$query_pagos = "SELECT pp_prueba_semanas.IDsemana, pp_prueba_pagos.IDempleado, pp_prueba_pagos.IDpprueba, pp_prueba_pagos.fecha_pago, pp_prueba_pagos.monto_pago, pp_prueba_pagos.IDestatus, pp_prueba_pagos.semana, pp_prueba_pagos.IDpprueba_pagos FROM pp_prueba_pagos INNER JOIN pp_prueba_semanas ON pp_prueba_pagos.semana = pp_prueba_semanas.semana WHERE pp_prueba_pagos.IDpprueba = $IDpprueba AND pp_prueba_semanas.IDtipo = $tipo_nomina"; 
	$pagos = mysql_query($query_pagos, $vacantes) or die(mysql_error());

	$objPHPExcel->getActiveSheet()->setSelectedCells('G23');
    $inicio = 33; 
	
	while($row_pagos = mysql_fetch_array($pagos)){ 
	$fecha_pago = date( 'd/m/Y', strtotime($row_pagos['fecha_pago']));
	$fecha_pago = $fecha_pago." .SEMANA ".$row_pagos['IDsemana'];
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$inicio, $row_pagos['monto_pago']); 
	$objPHPExcel->getActiveSheet()->SetCellValue('S'.$inicio, $fecha_pago); 
    $inicio = $inicio + 1; 
    }

	$query_pagosX = "SELECT * FROM pp_prueba_pagos WHERE IDpprueba = $IDpprueba"; 
	$pagosX = mysql_query($query_pagosX, $vacantes) or die(mysql_error());
	$row_pagosX = mysql_fetch_assoc($pagosX);
	$totalRows_pagosX = mysql_num_rows($pagosX);
	
if ($totalRows_pagosX < 7) {
$objPHPExcel->getActiveSheet()->getRowDimension(39)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(40)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(41)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(42)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(43)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(44)->setVisible(false);	
}

$objPHPExcel->getActiveSheet()->mergeCells('G11:N11');
$objPHPExcel->getActiveSheet()->mergeCells('S11:Z11');
$objPHPExcel->getActiveSheet()->mergeCells('G13:N13');
$objPHPExcel->getActiveSheet()->mergeCells('G14:Z14');
$objPHPExcel->getActiveSheet()->mergeCells('G15:N15');
$objPHPExcel->getActiveSheet()->mergeCells('S15:Z15');
$objPHPExcel->getActiveSheet()->mergeCells('G17:Z17');
$objPHPExcel->getActiveSheet()->mergeCells('G18:N18');
$objPHPExcel->getActiveSheet()->mergeCells('G20:Z20');
$objPHPExcel->getActiveSheet()->mergeCells('G21:L21');
$objPHPExcel->getActiveSheet()->mergeCells('G22:L22');
$objPHPExcel->getActiveSheet()->mergeCells('S22:Z22');
$objPHPExcel->getActiveSheet()->mergeCells('G26:L26');
$objPHPExcel->getActiveSheet()->mergeCells('S26:Z26');
$objPHPExcel->getActiveSheet()->mergeCells('G28:L28');
$objPHPExcel->getActiveSheet()->mergeCells('S28:Z28');
$objPHPExcel->getActiveSheet()->mergeCells('C47:Z52');

$objPHPExcel->getActiveSheet()->mergeCells('G33:L33');
$objPHPExcel->getActiveSheet()->mergeCells('G34:L34');
$objPHPExcel->getActiveSheet()->mergeCells('G35:L35');
$objPHPExcel->getActiveSheet()->mergeCells('G36:L36');
$objPHPExcel->getActiveSheet()->mergeCells('G37:L37');
$objPHPExcel->getActiveSheet()->mergeCells('G38:L38');
$objPHPExcel->getActiveSheet()->mergeCells('G39:L39');
$objPHPExcel->getActiveSheet()->mergeCells('G40:L40');
$objPHPExcel->getActiveSheet()->mergeCells('G41:L41');
$objPHPExcel->getActiveSheet()->mergeCells('G42:L42');
$objPHPExcel->getActiveSheet()->mergeCells('G43:L43');
$objPHPExcel->getActiveSheet()->mergeCells('G44:L44');

$objPHPExcel->getActiveSheet()->mergeCells('S33:Z33');
$objPHPExcel->getActiveSheet()->mergeCells('S34:Z34');
$objPHPExcel->getActiveSheet()->mergeCells('S35:Z35');
$objPHPExcel->getActiveSheet()->mergeCells('S36:Z36');
$objPHPExcel->getActiveSheet()->mergeCells('S37:Z37');
$objPHPExcel->getActiveSheet()->mergeCells('S38:Z38');
$objPHPExcel->getActiveSheet()->mergeCells('S39:Z39');
$objPHPExcel->getActiveSheet()->mergeCells('S40:Z40');
$objPHPExcel->getActiveSheet()->mergeCells('S41:Z41');
$objPHPExcel->getActiveSheet()->mergeCells('S42:Z42');
$objPHPExcel->getActiveSheet()->mergeCells('S43:Z43');
$objPHPExcel->getActiveSheet()->mergeCells('S44:Z44');
$objPHPExcel->getActiveSheet()->mergeCells('W3:Z3');

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('NOTIFICACION');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=NOTIFICACION '.$fecha_archivos.' '.$nombre.'.xlsx');
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