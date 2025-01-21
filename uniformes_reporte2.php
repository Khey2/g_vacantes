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
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

require_once 'assets/PHPExcel.php';
set_time_limit(0);

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT sed_uniformes.IDempleado, sed_uniformes.T_pantalon_ventas, sed_uniformes.T_pantalon_operaciones, sed_uniformes.T_camisa_ventas, sed_uniformes.T_playera_polo_distribucion, sed_uniformes.T_playera_roja_almacen, sed_uniformes.T_faja, sed_uniformes.T_botas, sed_uniformes.Sexo, prod_activosfaltas.denominacion, vac_matriz.matriz, vac_areas.area, prod_activosfaltas.emp_paterno, prod_activosfaltas.emp_materno, prod_activosfaltas.emp_nombre FROM sed_uniformes LEFT JOIN prod_activosfaltas ON sed_uniformes.IDempleado = prod_activosfaltas.IDempleado LEFT JOIN vac_matriz ON prod_activosfaltas.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON prod_activosfaltas.IDarea = vac_areas.IDarea WHERE prod_activosfaltas.estatus <> 2 AND sed_uniformes.IDmatriz IN ($IDmatrizes)";
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CI/Uniformes2.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(7);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	
	if($row_reporte['T_camisa_ventas'] == '50') { $T_camisa_ventas = '4X'; } 
  if($row_reporte['T_camisa_ventas'] == '52') { $T_camisa_ventas = '5X'; }
  if($row_reporte['T_camisa_ventas'] == '54') { $T_camisa_ventas = '7X'; }
  if($row_reporte['T_camisa_ventas'] == '56') { $T_camisa_ventas = '9X'; }
  else { $T_camisa_ventas = $row_reporte['T_camisa_ventas']; }

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte['T_pantalon_ventas']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte['T_pantalon_operaciones']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $T_camisa_ventas); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte['T_playera_polo_distribucion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte['T_playera_roja_almacen']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte['T_faja']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte['T_botas']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte['Sexo']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte['matriz']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte['area']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte['emp_paterno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_reporte['emp_materno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_reporte['emp_nombre']); 
		
// Increment the Excel row counter
        $rowCount++; 
    }
	
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Reporte Uniformes '.date('dmY').'.xlsx"');
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