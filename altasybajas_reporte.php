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
$elanio = '2020';

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 


if (isset($_POST['el_tipo'])) {
	$el_tipo = $_POST['el_tipo'];
//	echo " </br>El tipo: " . $el_tipo;
	}

if (isset($_POST['nivel'])) {
	foreach ($_POST['nivel'] as $nivel)
	{
	$cadena_niveles = implode(", ", $_POST['nivel']);
	}
//	echo " </br>Niveles: " .$cadena_niveles;
	}

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

if (isset($_POST['el_anio'])) {
	$el_anio = $_POST['el_anio'];
//	echo " </br>El año: " . $el_anio;
	}

if (isset($_POST['el_mes'])) {
	$el_mes = $_POST['el_mes'];
//	echo " </br>El mes: " . $el_mes;
	}

$tipo = "";
$la_fecha = $el_anio . "-" . $el_mes . "-01";

     if ($el_tipo == 2){	$tipo = " AND (baja_mes = $el_mes AND baja_anio = $el_anio)";	} 
else if ($el_tipo == 3){	$tipo = " AND (alta_mes = $el_mes AND alta_anio = $el_anio)";	} 
else if ($el_tipo == 1){	$tipo = " AND (MONTH(fecha_alta) <= $la_fecha AND fecha_baja IS NULL)";	} 	
else if ($el_tipo == 4){	$tipo = " AND (MONTH(fecha_alta) <= '$la_fecha' AND (fecha_baja IS NULL OR MONTH(fecha_baja) <= '$la_fecha')) ";	} 	
										
mysql_select_db($database_vacantes, $vacantes);
$query_consulta = "SELECT ind_bajas.* FROM ind_bajas WHERE IDmatriz IN ($cadena_matriz) AND IDarea IN ($cadena_areas) AND IDpuesto IN ($cadena_puestos) AND nivel_puesto IN ($cadena_niveles) " . $tipo;
mysql_query("SET NAMES 'utf8'");
$consulta = mysql_query($query_consulta, $vacantes) or die(mysql_error()); 

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');
ini_set('memory_limit', '-1');
set_time_limit(0);


// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CI/Reporte.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_consulta = mysql_fetch_array($consulta)){ 
	
	
  switch ($row_consulta['nivel_puesto']) {
    case 1:  $level = "DG Y VPS";      break;     
    case 2:  $level = "DIRECTOR";    break;    
    case 3:  $level = "GERENTE";      break;    
    case 4:  $level = "JEFE DE AREA";      break;    
    case 5:  $level = "COORD./SUP./ENC.";       break;    
    case 6:  $level = "ANALISTA";      break;    
    case 7:  $level = "AUXILIAR";      break;    
    case 8:  $level = "OPERATIVO";     break;    
      }
	  
  switch ($row_consulta['estatus']) {
    case 1:  $estatus = "ACTIVO";      	break;     
    case 2:  $estatus = "BAJA";    		break;    
    case 3:  $estatus = "REINGRESO";    break;    
    case 4:  $estatus = "SUSPENDIDO";   break;    
      }
	  
	 $fecha_alt = date( 'd/m/Y', strtotime($row_consulta['fecha_alta'])); 
	 $fecha_ant = date( 'd/m/Y', strtotime($row_consulta['fecha_antiguedad'])); 
	 $fecha_nac = date( 'd/m/Y', strtotime($row_consulta['fecha_nacimiento'])); 
	 if($row_consulta['fecha_baja'] == ''){$fecha_bja = 'NO APLICA';} else {$fecha_bja = date( 'd/m/Y' , strtotime($row_consulta['fecha_baja'])) ;} 
	
	  if(substr($row_consulta['curp'], 10, 1) == 'H'){$sexo = 'HOMBRE';} 
	 else if(substr($row_consulta['curp'], 10, 1) == 'M'){$sexo = 'MUJER';} 	
	 else {$sexo = '-';} 

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_consulta['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_consulta['emp_paterno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_consulta['emp_materno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_consulta['emp_nombre']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_consulta['RFC']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_consulta['curp']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $fecha_alt); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $fecha_ant); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $fecha_nac); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $fecha_bja); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_consulta['puesto']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_consulta['descripcion_puesto']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_consulta['descripcion_nivel']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $level); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_consulta['area']);
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_consulta['MATRIZ']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $row_consulta['SUCURSAL']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $sexo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $estatus); 

// Increment the Excel row counter
        $rowCount++; 
    }

	header('Content-Type: application/vnd.ms-excel'); //mime type
  header('Content-Disposition: attachment;filename="Reporte '.date('dmY'). '.xlsx"');
	header('Cache-Control: max-age=0'); //no cache
	

  
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
  exit;
?>