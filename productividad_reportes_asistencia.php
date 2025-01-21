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
set_time_limit(0);


mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
if(isset($_GET['anio'])) { $anio = $_GET['anio'];} else {$anio = $row_variables['anio'];}
$desfase = $row_variables['dias_desfase'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id']; }
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];


// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

$la_semana = $_GET['semana'];
$la_matriz = $_GET['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

if(isset($_GET['anio']) && $_GET['anio'] == '2020') { 

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT 	prod_captura_2020.IDempleado, 	prod_captura_2020.emp_paterno, 	prod_captura_2020.emp_materno, 	prod_captura_2020.emp_nombre, 	prod_captura_2020.denominacion, 	prod_captura_2020.lun, 	prod_captura_2020.mar, 	prod_captura_2020.mie, 	prod_captura_2020.jue, 	prod_captura_2020.vie, 	prod_captura_2020.sab, 	prod_captura_2020.semana, 	vac_matriz.matriz  FROM 	prod_captura_2020 	LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_captura_2020.IDmatriz  WHERE 	prod_captura_2020.IDmatriz in ($la_matriz)  	AND prod_captura_2020.semana = '$la_semana' ORDER BY prod_captura_2020.IDmatriz ASC"; 
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
$row_reporte = mysql_fetch_assoc($reporte);
$totalRows_reporte = mysql_num_rows($reporte);
$nmmatriz = $row_reporte['matriz'];

} else {

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_captura.IDempleado, prod_captura.emp_paterno, prod_captura.emp_materno, prod_captura.emp_nombre, prod_captura.denominacion, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, 	prod_captura.sab, prod_captura.semana, vac_matriz.matriz FROM prod_captura LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_captura.IDmatriz WHERE prod_captura.IDmatriz in ($la_matriz) AND prod_captura.semana = '$la_semana' AND prod_captura.anio = '$anio'  ORDER BY prod_captura.IDmatriz ASC";     
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
$row_reporte = mysql_fetch_assoc($reporte);
$totalRows_reporte = mysql_num_rows($reporte);
$nmmatriz = $row_reporte['matriz'];

}

set_time_limit(0);


// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PRD/cedulaF.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	if ($row_reporte['5'] == 0)  {$Flun = "F"; } else {$Flun = "A";}
	if ($row_reporte['6'] == 0)  {$Fmar = "F"; } else {$Fmar = "A";}
	if ($row_reporte['7'] == 0)  {$Fmie = "F"; } else {$Fmie = "A";}
	if ($row_reporte['8'] == 0)  {$Fjue = "F"; } else {$Fjue = "A";}
	if ($row_reporte['9'] == 0) {$Fvie = "F"; } else {$Fvie = "A";}
	if ($row_reporte['10'] == 0) {$Fsab = "F"; } else {$Fsab = "A";}

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Flun); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Fmar); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Fmie); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $Fjue); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $Fvie); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Fsab); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte[11]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte[12]); 

// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Asistencia '.date('dmY') . " " . $nmmatriz . '.xls"');
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