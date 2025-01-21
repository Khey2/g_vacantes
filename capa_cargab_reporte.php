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
$mi_fecha =  date('Y/m/d');


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
$la_matriz = $row_usuario['IDmatriz'];
$fecha_mes = date("m");

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
set_time_limit(0);

$query_meses = "SELECT * FROM vac_meses";
mysql_query("SET NAMES 'utf8'");
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_amatriz = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

$fecha = $_GET['fecha'];
$fecha_archivo = date( 'd/m/Y' , strtotime($fecha));

$query_capa = "SELECT capa_avance.IDC_capa, capa_avance.IDempleado, capa_avance.emp_nombre, capa_avance.emp_paterno, capa_avance.emp_materno, capa_avance.fecha_antiguedad, capa_avance.fecha_baja, capa_avance.fecha_evento, capa_avance.anio, capa_avance.mes, capa_avance.calificacion, capa_avance.IDC_tipo_curso, capa_avance.IDC_programado, capa_avance.IDmatriz, capa_avance.IDarea, capa_avance.IDpuesto, capa_avance.denominacion, capa_avance.estatus, capa_avance.fecha, capa_cursos.nombre_curso, capa_tipos_cursos.tipo_evento, vac_areas.area, vac_matriz.matriz, vac_sucursal.sucursal FROM capa_avance LEFT JOIN capa_cursos ON  capa_avance.IDC_capa_cursos = capa_cursos.IDC_capa_cursos LEFT JOIN capa_tipos_cursos ON  capa_cursos.IDC_tipo_curso = capa_tipos_cursos.ID_tipo_evento LEFT JOIN vac_areas ON capa_avance.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON  capa_avance.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON  capa_avance.IDsucursal = vac_sucursal.IDsucursal WHERE capa_avance.fecha = '$fecha'"; 
mysql_query("SET NAMES 'utf8'");
$capa = mysql_query($query_capa, $vacantes) or die(mysql_error());
$row_capa = mysql_fetch_assoc($capa);
$totalRows_capa = mysql_num_rows($capa);

set_time_limit(0);


// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("capa/reporte.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_capa = mysql_fetch_array($capa)){ 
	
	$fecha_antiguedad = date( 'd/m/Y' , strtotime($row_capa['fecha_antiguedad']));
	$fecha_evento = date( 'd/m/Y' , strtotime($row_capa['fecha_evento']));
	if($row_capa['IDC_programado'] == 0) { $programado = "NO"; } else { $programado = "SI"; }
	$nombre = $row_capa['emp_paterno']." ".$row_capa['emp_materno']." ".$row_capa['emp_nombre'];
	
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_capa['IDC_capa']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_capa['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $nombre); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_capa['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_capa['matriz']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_capa['area']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $fecha_antiguedad); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_capa['nombre_curso']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $fecha_evento); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_capa['calificacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_capa['tipo_evento']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $programado); 

// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte de Carga '.$fecha_archivo.'.xls"');
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