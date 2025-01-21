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
$query_reporte = "SELECT com_vd.IDempleado, com_vd.emp_paterno, com_vd.emp_materno, com_vd.emp_nombre, com_vd.fecha_antiguedad, com_vd.denominacion, com_vd.anio, com_vd_extras.monto, com_vd_extras.fecha_pago, vac_meses.mes, vac_matriz.matriz, com_vd_extras_conceptos.concepto  FROM com_vd LEFT JOIN com_vd_extras ON com_vd_extras.IDempleado = com_vd.IDempleado LEFT JOIN vac_meses ON com_vd.IDmes = vac_meses.IDmes LEFT JOIN vac_matriz ON com_vd.IDmatriz = vac_matriz.IDmatriz LEFT JOIN com_vd_extras_conceptos ON com_vd_extras.IDconcepto = com_vd_extras_conceptos.IDconcepto WHERE com_vd.IDmes = '$mes' AND com_vd.anio = '$anio' AND com_vd.IDpuesto not in (212,235,0)"; 
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CVD/cedula2.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	$nombre = $row_reporte['emp_paterno']." ".$row_reporte['emp_materno']." ".$row_reporte['emp_nombre'];
	$fecha_ant =  date('d/m/Y', strtotime($row_reporte['fecha_antiguedad']));
	if ($row_reporte['fecha_pago'] == '') {$fecha_pago = '';} else {$fecha_pago =  date('d/m/Y', strtotime($row_reporte['fecha_pago']));}
	if ($row_reporte['denominacion'] == '') {$nombre = "VACANTE"; $puesto = "VACANTE";} else {$puesto = $row_reporte['denominacion'];}

	$el_mes = $row_reporte['mes'];
	
	
         $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte['IDempleado']); 
         $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre); 
         $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $fecha_ant); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $puesto); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte['mes']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte['anio']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte['matriz']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte['monto']); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $fecha_pago); 
		 $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte['concepto']); 
		
// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Comisiones VD Otros '.date('dmY').' Mes '.$el_mes.'.xls"');
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