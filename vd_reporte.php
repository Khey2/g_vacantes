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


$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana =  date("W"); //la semana empieza ayer 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];


require_once 'assets/PHPExcel.php';
set_time_limit(0);

$mes = $_GET['mes'];
$anio = $_GET['anio'];

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT com_vd.IDmes, com_vd.anio, com_vd.fecha_antiguedad, com_vd.calculado, com_vd.Comisiones_pieza, com_vd.Comisiones_caja, com_vd.Premio_1, com_vd.Premio_2, com_vd.bt_01_fecha, com_vd.BonoVentaNeta, com_vd.BonoClientesVenta, com_vd.BonoDevPorc, com_vd.bt_02_fecha, com_vd.bt_03_fecha, com_vd.bt_04_fecha, com_vd.bt_05_fecha, com_vd.IDvd, com_vd.IDmatriz, com_vd.IDempleadoS, com_vd.IDempleado, com_vd.Clave, com_vd.VentaNeta, com_vd.VentaNetaCajas, com_vd.VentaNetaPieza, com_vd.ClientesVenta, com_vd.NoPedidos, com_vd.Visitas, com_vd.DevImporte, com_vd.DevPorc, com_vd.Presupuesto, com_vd.Cubrimiento, com_vd.MargenBruto, com_vd.IDsemana, com_vd.bt_01, com_vd.bt_02, com_vd.bt_03, com_vd.bt_04, com_vd.bt_05, com_vd.bt_garantizado, com_vd.bt_adicional, com_vd.bt_observaciones, com_vd.bt_capturador, com_vd.bt_fecha_captura, com_vd.BonoProductividad, com_vd.Premios, com_vd.Comisiones, vac_matriz.matriz, Empleados.IDempleado, Empleados.emp_paterno AS emp_paterno, Empleados.emp_materno AS emp_materno, Empleados.emp_nombre AS emp_nombre, Empleados.denominacion AS emp_denominacion, Empleados.IDpuesto AS emp_IDpuesto, vac_meses.mes, com_vd.bt_01_ad, com_vd.bt_02_ad, com_vd.bt_03_ad, com_vd.bt_04_ad, com_vd.bt_05_ad, com_vd.BonoVentaNeta_ad, com_vd.BonoClientesVenta_ad, com_vd.BonoDevPorc_ad, com_vd.Premio_1_ad, com_vd.Premio_2_ad, com_vd.Comisiones_pieza_ad, com_vd.Comisiones_caja_ad FROM com_vd LEFT JOIN prod_activos AS Empleados ON com_vd.IDempleado = Empleados.IDempleado LEFT JOIN vac_matriz ON com_vd.IDmatriz  = vac_matriz.IDmatriz LEFT JOIN vac_meses ON com_vd.IDmes = vac_meses.IDmes WHERE com_vd.IDmes = '$mes' AND com_vd.anio = '$anio' AND Empleados.IDpuesto in (212,235,0)";
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CVD/cedula.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	$nombre = $row_reporte['emp_paterno']." ".$row_reporte['emp_materno']." ".$row_reporte['emp_nombre'];
	$puesto = $row_reporte['emp_denominacion'];
	$el_empleado = $row_reporte['IDempleado'];

	if ($row_reporte['emp_denominacion'] == '' ) {$nombre = "VACANTE"; $puesto = "VACANTE"; $el_empleado = "0";} 

	$fecha_ant =  date('d/m/Y', strtotime($row_reporte['fecha_antiguedad']));
	$el_mes = $row_reporte['mes'];
	$monto_transporte = $row_reporte['bt_01'] + $row_reporte['bt_02'] + $row_reporte['bt_03'] + $row_reporte['bt_04'] + $row_reporte['bt_05'] + $row_reporte['bt_01_ad'] + $row_reporte['bt_02_ad'] + $row_reporte['bt_03_ad'] + $row_reporte['bt_04_ad'] + $row_reporte['bt_05_ad'];

	$monto_BonoProductividad = $row_reporte['BonoProductividad'] + $row_reporte['BonoClientesVenta_ad'] +$row_reporte['BonoVentaNeta_ad'] + $row_reporte['BonoDevPorc_ad'];
	$monto_BonoVentaNeta = $row_reporte['BonoVentaNeta_ad'] + $row_reporte['BonoVentaNeta'];
	$monto_BonoClientesVenta = $row_reporte['BonoClientesVenta_ad'] + $row_reporte['BonoClientesVenta'];
	$monto_BonoDevPorc = $row_reporte['BonoDevPorc_ad'] + $row_reporte['BonoDevPorc'];
	
	$monto_BonoPremios = $row_reporte['Premios'] + $row_reporte['Premio_1_ad'] + $row_reporte['Premio_2_ad'];
	$monto_Premio_1 = $row_reporte['Premio_1_ad'] + $row_reporte['Premio_1'];
	$monto_Premio_2 = $row_reporte['Premio_2_ad'] + $row_reporte['Premio_2'];

	$monto_Comisiones = $row_reporte['Comisiones'] + $row_reporte['Comisiones_pieza_ad'] + $row_reporte['Comisiones_caja_ad'];
	$monto_Comisiones_pieza = $row_reporte['Comisiones_pieza_ad'] + $row_reporte['Comisiones_pieza'];
	$monto_Comisiones_caja = $row_reporte['Comisiones_caja_ad'] + $row_reporte['Comisiones_caja'];
	
	$bt_01 = $row_reporte['bt_01'] + $row_reporte['bt_01_ad'];
	$bt_02 = $row_reporte['bt_02'] + $row_reporte['bt_02_ad'];
	$bt_03 = $row_reporte['bt_03'] + $row_reporte['bt_03_ad'];
	$bt_04 = $row_reporte['bt_04'] + $row_reporte['bt_04_ad'];
	$bt_05 = $row_reporte['bt_05'] + $row_reporte['bt_05_ad'];
	
	
         $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $el_empleado); 
         $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre); 
         $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $fecha_ant); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte['Clave']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $puesto); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $el_mes); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte['matriz']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte['VentaNeta']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte['VentaNetaCajas']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte['VentaNetaPieza']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte['ClientesVenta']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte['NoPedidos']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte['Visitas']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_reporte['DevImporte']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_reporte['DevPorc']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_reporte['Presupuesto']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $row_reporte['Cubrimiento']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $row_reporte['MargenBruto']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $monto_transporte); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $bt_01); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $row_reporte['bt_01_ad']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $row_reporte['bt_01_fecha']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $bt_02); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $row_reporte['bt_02_ad']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $row_reporte['bt_02_fecha']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $bt_03); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $row_reporte['bt_03_ad']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $row_reporte['bt_03_fecha']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $bt_04); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, $row_reporte['bt_04_ad']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, $row_reporte['bt_04_fecha']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowCount, $bt_05); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, $row_reporte['bt_05_ad']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, $row_reporte['bt_05_fecha']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, $row_reporte['bt_garantizado']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, $monto_BonoProductividad); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, $monto_BonoVentaNeta); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, $monto_BonoClientesVenta); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AM'.$rowCount, $monto_BonoDevPorc); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AN'.$rowCount, $monto_BonoPremios); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AO'.$rowCount, $monto_Premio_1); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AP'.$rowCount, $monto_Premio_2); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AQ'.$rowCount, $monto_Comisiones); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AR'.$rowCount, $monto_Comisiones_pieza); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('AS'.$rowCount, $monto_Comisiones_caja); 
		
// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Comisiones VD '.date('dmY').' Mes '.$el_mes.'.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>