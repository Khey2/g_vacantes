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


$mes_actual = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$elanio = '2022';

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 


if (isset($_POST['el_area'])) {
	foreach ($_POST['el_area'] as $areas)
	{
	$cadena_areas = implode(", ", $_POST['el_area']);
	}
//	echo " </br>Areas: " .$cadena_areas;
	}

if (isset($_POST['puesto'])) {
	foreach ($_POST['puesto'] as $puestos)
	{
	$cadena_puestos = implode(", ", $_POST['puesto']);
	}
//	echo " </br>Areas: " .$cadena_areas;
	}

if (isset($_POST['la_matriz'])) {
	foreach ($_POST['la_matriz'] as $matriz)
	{
	$cadena_matriz = implode(", ", $_POST['la_matriz']);
	}
//	echo " </br>Matrices: " .$cadena_matriz;
	}

										
mysql_select_db($database_vacantes, $vacantes);
$query_consulta = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.sueldo_diario, prod_activos.denominacion, vac_areas.area, vac_matriz.matriz, prod_activos.rfc, prod_activos.curp, prod_activos.imss, prod_activos.d_calle, prod_activos.d_num, prod_activos.d_col, prod_activos.d_del, prod_activos.d_est, prod_activos.d_cp, prod_activos.IDmatriz, prod_activos.descripcion_nomina FROM prod_activos left JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto left JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea left JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz  WHERE prod_activos.IDmatriz IN ($cadena_matriz) AND prod_activos.IDarea IN ($cadena_areas) AND prod_activos.IDpuesto IN ($cadena_puestos)";
mysql_query("SET NAMES 'utf8'");
$consulta = mysql_query($query_consulta, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CI/Reporte2.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_consulta = mysql_fetch_array($consulta)){ 
	
	if($row_consulta[18] == 7 or $row_consulta[19] == 'Nomina Quincenal CORVI') {$sueldo = "0";} else {$sueldo = $row_consulta[5];}
	
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_consulta[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_consulta[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_consulta[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_consulta[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_consulta[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $sueldo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_consulta[6]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_consulta[7]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_consulta[8]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_consulta[9]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_consulta[10]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_consulta[11]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_consulta[12]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_consulta[13]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_consulta[14]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_consulta[15]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $row_consulta[16]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $row_consulta[17]); 

// Increment the Excel row counter
        $rowCount++; 
    }
	
	    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Excel');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Activos.xls"');
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