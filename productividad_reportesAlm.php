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




mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $_GET['IDmatriz'];


require_once 'assets/PHPExcel.php';

if(isset($_GET['el_anio']) && $_GET['el_anio'] == '2020') { 

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, vac_puestos.denominacion, prod_activos.sueldo_total, prod_activos.sueldo_diario, prod_captura_2020.garantizado, prod_captura_2020.adicional, prod_captura_2020.adicional2, prod_captura_2020.reci, prod_captura_2020.carg, prod_captura_2020.dist, prod_captura_2020.esti, prod_captura_2020.pago, prod_captura_2020.pago_total, prod_captura_2020.capturador, prod_captura_2020.validador, prod_captura_2020.autorizador, prod_captura_2020.lun, prod_captura_2020.mar, prod_captura_2020.mie, prod_captura_2020.jue, prod_captura_2020.vie, prod_captura_2020.sab, prod_captura_2020.dom, vac_matriz.matriz, prod_activos.IDpuesto FROM prod_activos LEFT JOIN prod_captura_2020 ON prod_captura_2020.IDempleado = prod_activos.IDempleado AND prod_captura_2020.semana = '$semana' LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE prod_activos.IDmatriz = '$matriz' AND prod_activos.IDarea IN (1,2) AND prod_activos.IDaplica_SED = 0 ORDER BY vac_matriz.IDmatriz ASC ";
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

} else {

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, vac_puestos.denominacion, prod_activos.sueldo_total, prod_activos.sueldo_diario, prod_captura.garantizado, prod_captura.adicional, prod_captura.adicional2, prod_captura.reci, prod_captura.carg, prod_captura.dist, prod_captura.esti, prod_captura.pago, prod_captura.pago_total, prod_captura.capturador, prod_captura.validador, prod_captura.autorizador, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, vac_matriz.matriz, prod_activos.IDpuesto FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana'  AND prod_captura.anio = '$anio' LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE prod_activos.IDmatriz = '$matriz' AND prod_activos.IDarea IN (1,2) AND prod_activos.IDaplica_SED = 0 ORDER BY vac_matriz.IDmatriz ASC ";
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

}

set_time_limit(0);


// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PRD/cedula.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 3; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	$sueldo = (($row_reporte[5] / 30) * 7);
	
	if($row_reporte[7] > 0) { $garantia = "SI"; } else { $garantia = ""; }
	if($row_reporte[16] == "") { $capturado = ""; } else { $capturado = "SI"; }
	if($row_reporte[17] == "") { $validado = ""; } else { $validado = "SI"; }
	if($row_reporte[18] == "") { $autorizado = ""; } else { $autorizado = "SI"; }
	if($row_reporte[14] > 0) { $calculado = $row_reporte[14] / 100; } else { $calculado = ""; }
	if(($row_reporte[19] + $row_reporte[20] + $row_reporte[21] + $row_reporte[22] + $row_reporte[23] + $row_reporte[24] + $row_reporte[25]) < 6 ) 
	{ $asistencia = "NO"; } else { $asistencia = "SI"; }
	$pago_total_f = $row_reporte[9] + $row_reporte[15];


if ($row_reporte[27] == 2) { $adicional = ""; } else {
	if($row_reporte[8] > 0) { $adicional = $row_reporte[8] / 100; } else { $adicional = ""; }
}

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $sueldo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $garantia); 

if ($row_reporte[27] == 2) {
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $adicional); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[8]); 
} else {
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $adicional); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[9]); 
}

        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte[10]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte[11]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte[13]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte[12]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $calculado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_reporte[15]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $pago_total_f); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $capturado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $validado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $autorizado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $asistencia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $semana); 
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $row_reporte[26]); 

// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Productividad '.date('dmY') . " " . $matriz . '.xls"');
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