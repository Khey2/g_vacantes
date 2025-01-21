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
$IDmatriz = $_GET['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

//$el_anio = $_GET['anio'];
//$el_mes = $_GET['mes'];
//$IDfecha = $_GET['IDfecha'];


$query_reporte = "SELECT inc_vacaciones.IDempleado, prod_activosfaltas.emp_paterno, prod_activosfaltas.emp_materno, prod_activosfaltas.emp_nombre, inc_vacaciones.denominacion, vac_areas.area, vac_matriz.matriz, inc_vacaciones.fecha_inicio, inc_vacaciones.fecha_fin, inc_vacaciones.IDdias_asignados  FROM inc_vacaciones LEFT JOIN vac_areas ON inc_vacaciones.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON inc_vacaciones.IDmatriz = vac_matriz.IDmatriz LEFT JOIN prod_activosfaltas ON inc_vacaciones.IDempleado = prod_activosfaltas.IDempleado WHERE inc_vacaciones.IDmatriz = $IDmatriz";
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
$row_reporte = mysql_fetch_assoc($reporte);


require_once 'assets/PHPExcel.php';
set_time_limit(0);

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("files/vacacionesB.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte['emp_paterno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte['emp_materno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte['emp_nombre']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte['area']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte['matriz']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte['fecha_inicio']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte['fecha_fin']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte['IDdias_asignados']); 

// Increment the Excel row counter
        $rowCount++; 

    }

$la_matriz = $row_reporte['matriz'];

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="VACACIONES 2024 '.date('d-m-Y')." ".$la_matriz.'.xlsx"');
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