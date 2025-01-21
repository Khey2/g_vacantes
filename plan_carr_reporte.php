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
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
if (isset($_GET['semana'])){ $semana = $_GET['semana'];}



require_once 'assets/PHPExcel.php';

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea, prod_activos.IDaplica_SED, pc_semaforo.IDplan, pc_semaforo.reqa, pc_semaforo.reqb, pc_semaforo.reqc, pc_semaforo.reqd, pc_semaforo.reqe, pc_semaforo.estatus, vac_puestos.plan_carrera, vac_matriz.matriz, pc_semaforo.reqf FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE prod_activos.IDmatriz = '$IDmatriz'  AND vac_puestos.plan_carrera = 1 AND pc_semaforo.IDplan is not null"; 
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());


set_time_limit(0);


// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PRD/cedulaPC.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	
	if($row_reporte[10] == 1) { $experiencia = "SI"; } else { $experiencia = "NO"; }
	
	if($row_reporte[11] == 1) { $le_interesa1 = "SI"; } else { $le_interesa1 = "NO"; }
	
	    if($row_reporte[12] == 1) { $tipo_unidad = "Camioneta 3 1/2"; }
	elseif($row_reporte[12] == 2) { $tipo_unidad = "Torton"; }
	elseif($row_reporte[12] == 3) { $tipo_unidad = "Rabón"; }
	elseif($row_reporte[12] == 5) { $tipo_unidad = "Automóvil particular estándar"; }
	elseif($row_reporte[12] == 6) { $tipo_unidad = "Automóvil particular automático"; }
	elseif($row_reporte[12] == 7) { $tipo_unidad = "Ninguno"; }
	else { $tipo_unidad = "N/A"; }
	
	    if($row_reporte[13] == 0) { $tiene_licencia = "No tiene Licencia"; }
	elseif($row_reporte[13] == 1) { $tiene_licencia = "Particular A"; }
	elseif($row_reporte[13] == 2) { $tiene_licencia = "Local B"; }
	elseif($row_reporte[13] == 3) { $tiene_licencia = "Local C"; }
	elseif($row_reporte[13] == 4) { $tiene_licencia = "Local D"; }
	elseif($row_reporte[13] == 5) { $tiene_licencia = "Federal B"; }
	elseif($row_reporte[13] == 6) { $tiene_licencia = "Federal C"; }
	elseif($row_reporte[13] == 7) { $tiene_licencia = "Federal D"; }
	else { $tiene_licencia = "N/A"; }

	if($row_reporte[14] == 1) { $le_interesa2 = "SI"; } else { $le_interesa2 = "NO"; }
	if($row_reporte[18] == 1) { $le_interesa3 = "SI"; } else { $le_interesa3 = "NO"; }
	
	$lamatri = $row_reporte[17];

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte[5]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte[17]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $experiencia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $le_interesa1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $tipo_unidad); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $tiene_licencia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $le_interesa2); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $le_interesa3); 

// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Plan Carrera '.date('dmY') . " " . $lamatri . '.xls"');
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