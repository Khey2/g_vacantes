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
$elanio = '2024';

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

       if ($_POST['con_correo'] == 0) { $con_correo = '';} 
  else if ($_POST['con_correo'] == 1) { $con_correo = 'AND prod_activosj.correo IS NULL';}    
    else { $con_correo = 'AND prod_activosj.correo IS NOT NULL';}    

  $y1 = substr( $_POST['fecha_final'], 6, 4 );
  $m1 = substr( $_POST['fecha_final'], 3, 2 );
  $d1 = substr( $_POST['fecha_final'], 0, 2 );
  $fecha_final = $y1."-".$m1."-".$d1;
  $fecha_final2 = $y1."-".$m1."-".$d1;





mysql_select_db($database_vacantes, $vacantes);
$query_consulta = "SELECT prod_activos.curp, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_antiguedad, prod_activos.fecha_nacimiento, prod_activos.denominacion, vac_areas.area, vac_matriz.matriz, prod_activosj.correo, prod_activos.estatus, vac_sucursal.sucursal FROM prod_activos LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN prod_activosj ON prod_activos.IDempleado = prod_activosj.IDempleado LEFT JOIN vac_sucursal ON prod_activos.IDsucursal = vac_sucursal.IDsucursal WHERE prod_activos.IDmatriz IN ($cadena_matriz) AND prod_activos.IDarea IN ($cadena_areas) AND prod_activos.IDpuesto IN ($cadena_puestos) ".$con_correo;
mysql_query("SET NAMES 'utf8'");
$consulta = mysql_query($query_consulta, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CI/ReporteCI.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_consulta = mysql_fetch_array($consulta)){ 
	
	$nombre_completo = $row_consulta['emp_nombre'].' '.$row_consulta['emp_paterno'].' '.$row_consulta['emp_materno'];
  
  $fechaInicio = new DateTime($row_consulta['fecha_antiguedad']);
  $fechaFin = new DateTime($fecha_final);
  $intervalo = $fechaInicio->diff($fechaFin);
  $periodo_d = $intervalo->y;

  $fechaInicio2 = new DateTime($row_consulta['fecha_nacimiento']);
  $fechaFin2 = new DateTime($fecha_final2);
  $intervalo2 = $fechaInicio2->diff($fechaFin2);
  $periodo_d2 = $intervalo2->y;


		  $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_consulta['IDempleado']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_consulta['emp_paterno']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_consulta['emp_materno']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_consulta['emp_nombre']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, date('d/m/Y', strtotime($row_consulta['fecha_antiguedad']))); 
      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "=DAY(E$rowCount)"); 
      $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "=MONTH(E$rowCount)"); 
      $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "=YEAR(E$rowCount)"); 
      $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $periodo_d); 
      $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, date('d/m/Y', strtotime($row_consulta['fecha_nacimiento']))); 
      $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, "=DAY(J$rowCount)"); 
      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, "=MONTH(J$rowCount)"); 
      $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, "=YEAR(J$rowCount)"); 
      $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $periodo_d2); 
      $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_consulta['denominacion']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_consulta['area']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $row_consulta['matriz']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $row_consulta['sucursal']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $row_consulta['correo']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $row_consulta['estatus']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $row_consulta['curp']); 
      $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $nombre_completo);         
      $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, "=PROPER(V$rowCount)");                
      $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, "=MID(U$rowCount,11,1)");                
      $objPHPExcel->getActiveSheet()->setAutoFilter('A1:X'.$rowCount);

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