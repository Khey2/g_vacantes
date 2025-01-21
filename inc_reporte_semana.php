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
$la_matriz = $_GET['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];


require_once 'assets/PHPExcel.php';

if(isset($_POST['el_anio']) && $_POST['el_anio'] == '2020') { 

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.IDpuesto, vac_matriz.IDmatriz, inc_captura_2020.IDcaptura, inc_captura_2020.perc, inc_captura_2020.prima, inc_captura_2020.dias1, inc_captura_2020.dias2, inc_captura_2020.horas1, inc_captura_2020.horas2, inc_captura_2020.obs1, inc_captura_2020.obs2, inc_captura_2020.obs3, inc_captura_2020.obs4, inc_captura_2020.obs5, inc_captura_2020.IDmotivo1,  inc_captura_2020.IDmotivo2,  inc_captura_2020.IDmotivo3, inc_captura_2020.inc1 AS INC1, inc_captura_2020.inc2 AS INC2, inc_captura_2020.inc3 AS INC3, inc_captura_2020.inc6 AS INC6,  inc_captura_2020.inc3, inc_captura_2020.inc6, inc_captura_2020.inc4 AS INC4, inc_captura_2020.inc5 AS INC5, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, inc_captura_2020.lul, inc_captura_2020.mal, inc_captura_2020.mil, inc_captura_2020.jul, inc_captura_2020.vil, inc_captura_2020.sal, inc_captura_2020.dol, inc_captura_2020.luf, inc_captura_2020.maf, inc_captura_2020.mif, inc_captura_2020.juf, inc_captura_2020.vif, inc_captura_2020.saf, inc_captura_2020.dof FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura_2020 ON inc_captura_2020.IDempleado = prod_activos.IDempleado AND inc_captura_2020.semana = '$semana' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());



} else {
	
mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_activos.IDarea, vac_puestos.denominacion, vac_puestos.IDpuesto, vac_matriz.IDmatriz, inc_captura.IDcaptura, inc_captura.perc, inc_captura.prima, inc_captura.dias1, inc_captura.dias2, inc_captura.horas1, inc_captura.horas2, inc_captura.obs1, inc_captura.obs2, inc_captura.obs3, inc_captura.obs4, inc_captura.obs5, inc_captura.IDmotivo1,  inc_captura.IDmotivo2,  inc_captura.IDmotivo3, inc_captura.inc1 AS INC1, inc_captura.inc2 AS INC2, inc_captura.inc3 AS INC3, inc_captura.inc6 AS INC6,  inc_captura.inc3, inc_captura.inc6, inc_captura.inc4 AS INC4, inc_captura.inc5 AS INC5, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, inc_captura.lul, inc_captura.mal, inc_captura.mil, inc_captura.jul, inc_captura.vil, inc_captura.sal, inc_captura.dol, inc_captura.luf, inc_captura.maf, inc_captura.mif, inc_captura.juf, inc_captura.vif, inc_captura.saf, inc_captura.dof FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana'  AND inc_captura.anio = '$anio' WHERE prod_activos.IDmatriz = '$IDmatriz'";
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

}
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'No.Emp');
    $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Paterno');
    $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Materno');
    $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Nombre');
    $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Puesto');
    $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Sueldo Semanal');
    $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Garantizado');
    $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Adicional (%)');
    $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Adicional ($)');
    $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Recibo');
    $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Carga');
    $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Estiba');
    $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Distr.');
    $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Calculado (%)');
    $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Pago ($)');
    $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Capturado');
    $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Validado');
    $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Autorizado');
    $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Asistencia');
    $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Semana');

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	$sueldo = (($row_reporte[5] / 30) * 7);
	
	if($row_reporte[7] > 0) { $garantia = "SI"; } else { $garantia = ""; }
	if($row_reporte[16] == "") { $capturado = ""; } else { $capturado = "SI"; }
	if($row_reporte[17] == "") { $validado = ""; } else { $validado = "SI"; }
	if($row_reporte[18] == "") { $autorizado = ""; } else { $autorizado = "SI"; }
	if($row_reporte[8] > 0) { $adicional = $row_reporte[8] / 100; } else { $adicional = ""; }
	if($row_reporte[14] > 0) { $calculado = $row_reporte[14] / 100; } else { $calculado = ""; }
	if(($row_reporte[19] + $row_reporte[20] + $row_reporte[21] + $row_reporte[22] + $row_reporte[23] + $row_reporte[24] + $row_reporte[25]) < 6 ) 
	{ $asistencia = "NO"; } else { $asistencia = "SI"; }



        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $sueldo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $garantia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $adicional); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[9]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte[10]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte[11]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte[12]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte[13]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $calculado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_reporte[15]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $capturado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $validado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $autorizado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $asistencia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $semana); 
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:T1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F2:F500')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
		$objPHPExcel->getActiveSheet()->getStyle('I2:I500')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
		$objPHPExcel->getActiveSheet()->getStyle('O2:O500')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
		$objPHPExcel->getActiveSheet()->getStyle('H2:H500')->getNumberFormat()->setFormatCode('0%');
		$objPHPExcel->getActiveSheet()->getStyle('N2:N500')->getNumberFormat()->setFormatCode('0%');

		$objPHPExcel->getActiveSheet()->getStyle('G2:G500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('P2:P500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('Q2:Q500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('R2:R500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('S2:S500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('T2:T500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		foreach(range('A','T') as $columnID) {$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);}


// Increment the Excel row counter
        $rowCount++; 
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Excel');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
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