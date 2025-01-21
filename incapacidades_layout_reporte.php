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


if (isset($_POST['IDestatus'])) {
	$IDestatus = $_POST['IDestatus'];
//	echo " </br>El estatus: " . $IDestatus;
	}

if (isset($_POST['el_anio'])) {
  $el_anio = $_POST['el_anio'];
//	echo " </br>El anio: " . $el_anio;
  }


if (isset($_POST['la_matriz'])) {
	foreach ($_POST['la_matriz'] as $matriz)
	{
	$cadena_matriz = implode(", ", $_POST['la_matriz']);
	}
//	echo " </br>Matrices: " .$cadena_matriz;
	}


if (isset($_POST['el_mes'])) {
  foreach ($_POST['el_mes'] as $emes)
  {
  $cadena_mes = implode(", ", $_POST['el_mes']);
  }
//	echo " </br>Meses: " .$cadena_mes;
if ( $cadena_mes == 0) {$extra1 = "";} else {$extra1 = "AND mes IN ($cadena_mes) ";}
if ( $cadena_mes == 0) {$extra3 = "";} else {$extra3 = "AND incapacidades_accidentes.mes IN ($cadena_mes) ";}
  }
 

  if (isset($_POST['la_semana'])) {
    foreach ($_POST['la_semana'] as $la_semana)
    {
    $cadena_semana = implode(", ", $_POST['la_semana']);
    }
//	echo " </br>Semanas: " .$cadena_semana;
if ( $cadena_semana == 0) {$extra2 = "";} else {$extra2 = "AND semana IN ($cadena_semana) ";}
if ( $cadena_semana == 0) {$extra4 = "";} else {$extra4 = "AND incapacidades_accidentes.semana IN ($cadena_semana) ";}
    }
  

  if (isset($_POST['IDtipo'])) {
    $IDtipo = $_POST['IDtipo'];
//	echo " </br>IDtipo: " . $IDtipo;
    }
  
     
if ($IDtipo == 1) {

      //REPORTE ACCIDENTES

									
mysql_select_db($database_vacantes, $vacantes);
$query_consulta = "SELECT * FROM incapacidades_accidentes WHERE IDmatriz IN ($cadena_matriz) ".$extra1.$extra2." AND anio = $el_anio AND IDestatus = $IDestatus";
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
$objPHPExcel = $objReader->load("incp/Reporte1.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 3; //new

    while($row_consulta = mysql_fetch_array($consulta)){ 

  $IDincapacidad = $row_consulta['IDincapacidad'];

  mysql_select_db($database_vacantes, $vacantes);
  $query_subconsulta = "SELECT incapacidades_certificados.*, SUM(incapacidades_certificados.dias) as Dias FROM incapacidades_certificados WHERE IDincapacidad = $IDincapacidad";
  $subconsulta  = mysql_query($query_subconsulta, $vacantes) or die(mysql_error());
  $row_subconsulta = mysql_fetch_assoc($subconsulta);
  $totalRows_subconsulta = mysql_num_rows($subconsulta);
	  
  $fecha1_a = date( 'Y', strtotime($row_consulta['fecha_inicio'])); 
  $fecha1_b = date( 'm', strtotime($row_consulta['fecha_inicio'])); 
  $fecha1_c = date( 'd', strtotime($row_consulta['fecha_inicio'])); 

  if($IDestatus == 2) {
  $fecha2_a = date( 'Y', strtotime($row_consulta['fecha_fin'])); 
  $fecha2_b = date( 'm', strtotime($row_consulta['fecha_fin'])); 
  $fecha2_c = date( 'd', strtotime($row_consulta['fecha_fin'])); 
  } else {
  $fecha2_a = ""; 
  $fecha2_b = ""; 
  $fecha2_c = ""; 
  }


  $nss_a = substr($row_consulta['nss'], 0, 10);
  $nss_b = substr($row_consulta['nss'], -1);
  $nombre = $row_consulta['emp_nombre']." ".$row_consulta['emp_paterno']." ".$row_consulta['emp_materno'];

  if ($row_consulta['defuncion'] == 1) {$defuncion = "D";} else {$defuncion = "";}
  if ($row_consulta['IDperm_parcial'] == 1){ $IDtipo = "Permanente";}
  if ($row_consulta['IDperm_parcial'] == 2){ $IDtipo = "Parcial";}
  if ($row_consulta['IDperm_parcial'] == 3){ $IDtipo = "Total";}
  if ($row_consulta['IDperm_parcial'] == ''){ $IDtipo = "Sin Definir";}
   
		    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $nss_a); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nss_b); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_consulta['curp']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $nombre); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, ""); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $fecha1_a); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $fecha1_b); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $fecha1_c); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_consulta['IDtipo_accidente']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_subconsulta['Dias']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $IDtipo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $defuncion); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $fecha2_a); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $fecha2_b); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $fecha2_c);

// Increment the Excel row counter
        $rowCount++; 
    }


	header('Content-Type: application/vnd.ms-excel'); //mime type
  header('Content-Disposition: attachment;filename="Reporte Accidentes '.date('dmY'). '.xlsx"');
	header('Cache-Control: max-age=0'); //no cache
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
  $objWriter->save('php://output');

  } else {



    //REPORTE INCAPACIDADES

mysql_select_db($database_vacantes, $vacantes);
$query_consulta = "SELECT incapacidades_accidentes.*, incapacidades_certificados.* FROM incapacidades_accidentes INNER JOIN incapacidades_certificados ON  incapacidades_accidentes.IDincapacidad = incapacidades_certificados.IDincapacidad WHERE incapacidades_accidentes.IDmatriz IN ($cadena_matriz) ".$extra3.$extra4." AND incapacidades_accidentes.anio = $el_anio AND incapacidades_accidentes.IDestatus = $IDestatus";
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
$objPHPExcel = $objReader->load("incp/Reporte2.xlsx");

// Add some data
$objPHPExcel->setActiveSheetIndex(0);

$rowCount = 1; //new

while($row_consulta = mysql_fetch_array($consulta)){ 

$fecha1_a = date( 'Y', strtotime($row_consulta['fecha_inicio'])); 
$fecha1_b = date( 'm', strtotime($row_consulta['fecha_inicio'])); 
$fecha1_c = date( 'd', strtotime($row_consulta['fecha_inicio'])); 
$fecha_1 = $fecha1_c."/".$fecha1_b."/".$fecha1_a;

if($IDestatus == 2) {
$fecha2_a = date( 'Y', strtotime($row_consulta['fecha_fin'])); 
$fecha2_b = date( 'm', strtotime($row_consulta['fecha_fin'])); 
$fecha2_c = date( 'd', strtotime($row_consulta['fecha_fin'])); 
$fecha_2 = $fecha2_c."/".$fecha2_b."/".$fecha2_a;
} else {
$fecha2_a = ""; 
$fecha2_b = ""; 
$fecha2_c = ""; 
$fecha_2 = "";
}


if ($row_consulta['IDtipo_incapacidad'] == 1){ $IDtipo = "EG";}
if ($row_consulta['IDtipo_incapacidad'] == 2){ $IDtipo = "AT";}
if ($row_consulta['IDtipo_incapacidad'] == 3){ $IDtipo = "MA";}
if ($row_consulta['IDtipo_incapacidad'] == 4){ $IDtipo = "HC";}

$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_consulta['IDempleado']); 
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_consulta['folio_certificado']); 
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $IDtipo); 
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $fecha_1); 
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_consulta['dias']); 

// Increment the Excel row counter
$rowCount++; 
}


header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="Reporte Accidentes '.date('dmY'). '.csv"');
header('Cache-Control: max-age=0'); //no cache
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV'); 
$objWriter->setDelimiter(',');  // Define delimiter
$objWriter->save('php://output');

}
?>