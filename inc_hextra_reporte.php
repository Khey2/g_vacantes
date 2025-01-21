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
$mes_actual = date("m");
$fecha = date("Y-m-d"); // la fecha actual

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 


if (isset($_POST['la_matriz'])) {
	foreach ($_POST['la_matriz'] as $matriz)
	{
	$cadena_matriz = implode(", ", $_POST['la_matriz']);
	}
	//echo " </br>Matrices: " .$cadena_matriz;
	}

if (isset($_POST['el_anio'])) {
	$el_anio = $_POST['el_anio'];
	//echo " </br>El año: " . $el_anio;
	}

if (isset($_POST['la_semana'])) {
	foreach ($_POST['la_semana'] as $semana)
	{
	$cadena_semana = implode(", ", $_POST['la_semana']);
	}
	//echo " </br>Semanas: " .$cadena_semana;
	}

										
mysql_select_db($database_vacantes, $vacantes);
$query_consulta = "SELECT inc_captura.IDempleado, inc_captura.emp_paterno, inc_captura.emp_materno, inc_captura.emp_nombre, inc_captura.semana, vac_matriz.matriz, inc_captura.horas1, inc_captura.dias1, inc_captura.inc1, inc_motivos.motivo, inc_captura.obs1 FROM inc_captura LEFT JOIN vac_matriz ON inc_captura.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_motivos ON inc_captura.IDmotivo1 = inc_motivos.IDmotivo WHERE inc_captura.inc1  > 0 AND inc_captura.IDmatriz IN ($cadena_matriz) AND inc_captura.semana IN ($cadena_semana) AND  inc_captura.anio = $el_anio ";
mysql_query("SET NAMES 'utf8'");
$consulta = mysql_query($query_consulta, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CI/RHReporte.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_consulta = mysql_fetch_array($consulta)){ 
	

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_consulta[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_consulta[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_consulta[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_consulta[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_consulta[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_consulta[5]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_consulta[6]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_consulta[7]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_consulta[8]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_consulta[9]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_consulta[10]); 
		$objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getNumberFormat()->setFormatCode('$0.00');

// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Horas Extra '.date('d-m-Y'). '.xls"');
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