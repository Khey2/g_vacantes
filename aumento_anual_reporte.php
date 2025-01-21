<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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

$IDsubareax = $_SESSION['IDsubareax'];
$IDareax = $_SESSION['IDareax'];
$IDmatrix = $_SESSION['IDmatrix'];
$IDajustes = $_SESSION['IDajustes'];
$IDpuestox = $_SESSION['IDpuestox'];
$filtro1 = $_SESSION['filtro1'];
$filtro2 = $_SESSION['filtro2'];
$filtro3 = $_SESSION['filtro3'];
$filtro4 = $_SESSION['filtro4'];
$filtro5 = $_SESSION['filtro5'];

require_once 'assets/PHPExcel.php';
set_time_limit(0);

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT vac_matriz.matriz, vac_sucursal.sucursal, vac_areas.area, vac_subareas.subarea, prod_activos_anual.IDempleado, CONCAT( prod_activos_anual.emp_paterno, ' ', prod_activos_anual.emp_materno, ' ', prod_activos_anual.emp_nombre ) AS Nombre, prod_activos_anual.denominacion, prod_activos_anual.fecha_antiguedad, prod_activos_anual.sueldo_mensual, prod_activos_anual.criterio1, prod_activos_anual.criterio2, prod_activos_anual.criterio3, prod_activos_anual.aumento_porcentaje, prod_activos_anual.aumento_monto FROM prod_activos_anual LEFT JOIN vac_matriz ON prod_activos_anual.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON prod_activos_anual.IDarea = vac_areas.IDarea LEFT JOIN vac_subareas ON prod_activos_anual.IDsubarea = vac_subareas.IDsubarea LEFT JOIN vac_sucursal ON prod_activos_anual.IDsucursal = vac_sucursal.IDsucursal  WHERE ( prod_activos_anual.IDL1 = '$el_usuario'  OR prod_activos_anual.IDL2 = '$el_usuario'  OR prod_activos_anual.IDL3 = '$el_usuario'  OR prod_activos_anual.IDL4 = '$el_usuario'  OR prod_activos_anual.IDL5 = '$el_usuario') ".$filtro1.$filtro2.$filtro3.$filtro4.$filtro5; 
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("ANUAL/reporte.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	$aumento = $row_reporte['sueldo_mensual'] + $row_reporte['aumento_monto'];
	if ($row_reporte['criterio1'] == 1) {$criterio1 = 'SI';} else  {$criterio1 = 'NO';} 
	if ($row_reporte['criterio2'] == 1) {$criterio2 = 'SI';} else  {$criterio2 = 'NO';} 
	if ($row_reporte['criterio3'] > 0) {$criterio3 = 'SI';} else  {$criterio3 = 'NO';} 
	$aumento_porcentaje = ROUND($row_reporte['aumento_porcentaje'] / 100, 2);
	
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte['matriz']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte['sucursal']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte['area']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte['subarea']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte['Nombre']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte['fecha_antiguedad']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte['sueldo_mensual']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $criterio1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $criterio2); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $criterio3); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $aumento_porcentaje); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_reporte['aumento_monto']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $aumento); 
		
		
		$objPHPExcel->getActiveSheet()->getStyle('I2:I500')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
		$objPHPExcel->getActiveSheet()->getStyle('M2:M500')->getNumberFormat()->setFormatCode('0.0%');
		$objPHPExcel->getActiveSheet()->getStyle('N2:M500')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
		$objPHPExcel->getActiveSheet()->getStyle('O2:O500')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');



// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="AJUSTE ANUAL 2024 '.date('d-m-Y').'.xls"');
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