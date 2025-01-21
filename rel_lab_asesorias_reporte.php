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

require_once 'assets/PHPExcel.php';
set_time_limit(0);

$la_matriz = $_GET['IDmatriz'];
$el_mess = $_GET['mes']; 
$el_anio = $_GET['anio'];

if ($el_mess == 0){ $losmeses = " AND rel_lab_asesorias.mes IN (1,2,3,4,5,6,7,8,9,10,11,12) "; } else { $losmeses = " AND rel_lab_asesorias.mes = $el_mess "; }

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT rel_lab_asesorias.IDasesoria, rel_lab_asesorias.IDempleado, rel_lab_asesorias.IDestatus, rel_lab_asesorias.fecha_antiguedad, rel_lab_asesorias.anio, rel_lab_asesorias.mes, rel_lab_asesorias.rfc, rel_lab_asesorias.emp_paterno, rel_lab_asesorias.emp_materno, rel_lab_asesorias.emp_nombre, rel_lab_asesorias.denominacion, rel_lab_asesorias.IDmatriz, rel_lab_asesorias.IDpuesto, rel_lab_asesorias.IDarea, rel_lab_asesorias.IDsucursal, vac_matriz.matriz, vac_areas.area  FROM rel_lab_asesorias LEFT JOIN vac_matriz ON rel_lab_asesorias.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON rel_lab_asesorias.IDarea = vac_areas.IDarea WHERE rel_lab_asesorias.IDmatriz = $la_matriz AND rel_lab_asesorias.anio = $el_anio ".$losmeses; 
mysql_query("SET NAMES 'utf8'"); 
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
$row_reporte = mysql_fetch_assoc($reporte);
$totalRows_reporte = mysql_num_rows($reporte);


// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PRD/asesorias.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 

        if ( $row_reporte['IDestatus'] == 1) {$estatus = "ACTIVA";} else {$estatus = "CERRADA";}

        $la_Asesoria = $row_reporte['IDasesoria'];

        $query_fase1 = "SELECT * FROM rel_lab_etapas WHERE IDasesoria = $la_Asesoria AND IDetapa = 1"; 
        $fase1 = mysql_query($query_fase1, $vacantes) or die(mysql_error());
        $row_fase1 = mysql_fetch_assoc($fase1);
        $totalRows_fase1 = mysql_num_rows($fase1);
              
        $query_fase2 = "SELECT * FROM rel_lab_etapas WHERE IDasesoria = $la_Asesoria AND IDetapa = 2"; 
        $fase2 = mysql_query($query_fase2, $vacantes) or die(mysql_error());
        $row_fase2 = mysql_fetch_assoc($fase2);
        $totalRows_fase2 = mysql_num_rows($fase2);

        $query_fase3 = "SELECT * FROM rel_lab_etapas WHERE IDasesoria = $la_Asesoria AND IDetapa = 3"; 
        $fase3 = mysql_query($query_fase3, $vacantes) or die(mysql_error());
        $row_fase3 = mysql_fetch_assoc($fase3);
        $totalRows_fase3 = mysql_num_rows($fase3);

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte['IDasesoria']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte['emp_paterno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte['emp_materno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte['emp_nombre']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte['matriz']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte['area']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte['anio']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte['mes']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $estatus); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $totalRows_fase1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $totalRows_fase2); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $totalRows_fase3); 
		
// Increment the Excel row counter
        $rowCount++; 
    }
	
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Asesorias '.date('dmY').'.xlsx"');
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