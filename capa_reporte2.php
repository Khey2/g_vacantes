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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];


require_once 'assets/PHPExcel.php';
set_time_limit(0);

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT capa_becarios.emp_paterno, capa_becarios.emp_materno, capa_becarios.emp_nombre, capa_becarios.rfc13, vac_areas.area, vac_subareas.subarea, vac_matriz.matriz, CONCAT( prod_activos.emp_paterno, ' ', prod_activos.emp_materno, ' ', prod_activos.emp_nombre ) AS nombre_tutor, capa_becarios.IDempleadoJcorreo, capa_becarios_tipo.tipo, capa_becarios.fecha_alta, capa_becarios.fecha_baja,  capa_becarios.hora_entrada,  capa_becarios.hora_salida, capa_becarios.IDmodalidad, capa_becarios.IDrol, capa_becarios.correo, capa_becarios.telefono, capa_becarios.activo, capa_becarios_motivo_baja.motivo, capa_becarios.observaciones  FROM capa_becarios LEFT JOIN vac_areas ON capa_becarios.IDarea = vac_areas.IDarea LEFT JOIN vac_subareas ON capa_becarios.IDsubarea = vac_subareas.IDsubarea LEFT JOIN vac_matriz ON capa_becarios.IDmatriz = vac_matriz.IDmatriz LEFT JOIN prod_activos ON capa_becarios.IDempleadoJ = prod_activos.IDempleado LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo LEFT JOIN capa_becarios_motivo_baja ON capa_becarios.IDmotivo_baja = capa_becarios_motivo_baja.IDmotivo WHERE capa_becarios.activo = 0";
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("becariosfiles/reporte1.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	
	if ($row_reporte[11] == '0000-00-00') {$fecha_baja = '';} else {$fecha_baja == $row_reporte[11];} 
	if ($row_reporte[14] == 1) {$modalidad = 'PRESENCIAL';} elseif ($row_reporte[14] == 2) {$modalidad = 'DISTANCIA';} else {$modalidad = 'MIXTO';} 
	if ($row_reporte[18] == 1) {$estatus = 'ACTIVO';} else {$estatus = 'INACTIVO';} 

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte[5]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte[6]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte[7]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[8]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte[9]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte[10]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $fecha_baja); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte[12]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_reporte[13]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $modalidad); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_reporte[15]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $row_reporte[16]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $row_reporte[17]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $estatus); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $row_reporte[19]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $row_reporte[20]); 
		
// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="ReporteBecarios.xls"');
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