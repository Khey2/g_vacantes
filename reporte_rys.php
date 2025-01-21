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
$filtr = $_SESSION['filtr'];
$IDusuario = $row_usuario['IDusuario'];
$estatusf = $_SESSION['estatusf']; 


     if ($filtr == 1) {$filtro = ' AND prod_activosfaltas.fecha_alta BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() '; } 
else if ($filtr == 2) {$filtro = ' AND prod_activosfaltas.fecha_alta BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() '; } 
else if ($filtr == 0) {$filtro = ' AND prod_activosfaltas.fecha_alta < DATE_SUB(NOW(), INTERVAL 60 DAY) AND reclu_exp_sahuayo.IDpaso = 1 '; } 
                 else {$filtro = ' AND prod_activosfaltas.fecha_alta BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() '; } 

     if ($estatusf == 1) {$filtro3 = ' AND prod_activosfaltas.estatus IN (1,3,4) '; } 
else if ($estatusf == 2) {$filtro3 = ' AND prod_activosfaltas.estatus = 2 '; } 
else if ($estatusf == 3) {$filtro3 = ' AND prod_activosfaltas.estatus IN (1,2,3,4) '; } 
                    else {$filtro3 = ' AND prod_activosfaltas.estatus IN (1,2,3,4) '; } 


require_once 'assets/PHPExcel.php';
set_time_limit(0);

if ($row_usuario['contratos'] == 2)  { $filtro2 = "";} else { $filtro2 = " AND prod_activosj.IDusuario_segimiento = '$IDusuario' ";}


mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT DISTINCT prod_activosfaltas.*, vac_areas.area, vac_matriz.matriz, con_empleados.telefono_1, prod_activosj.IDusuario_segimiento FROM prod_activosfaltas LEFT JOIN vac_matriz ON prod_activosfaltas.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activosfaltas.IDarea LEFT JOIN con_empleados ON prod_activosfaltas.RFC = con_empleados.a_rfc LEFT JOIN prod_activosj ON prod_activosfaltas.IDempleado = prod_activosj.IDempleado WHERE prod_activosfaltas.IDmatriz = $IDmatriz ".$filtro.$filtro2.$filtro3." ORDER BY prod_activosfaltas.fecha_alta DESC"; 
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("files/reporterys.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 3; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 

      $IDempleado = $row_reporte['IDempleado'];

      $query_capturas1 = "SELECT * FROM reclu_exp_sahuayo WHERE IDempleado = '$IDempleado' AND IDpaso = 1";
      mysql_query("SET NAMES 'utf8'");
      $capturas1 = mysql_query($query_capturas1, $vacantes) or die(mysql_error());
      $row_capturas1 = mysql_fetch_assoc($capturas1);
      $totalRows_capturas1 = mysql_num_rows($capturas1);

      $query_capturas2 = "SELECT * FROM reclu_exp_sahuayo WHERE IDempleado = '$IDempleado' AND IDpaso = 2";
      mysql_query("SET NAMES 'utf8'");
      $capturas2 = mysql_query($query_capturas2, $vacantes) or die(mysql_error());
      $row_capturas2 = mysql_fetch_assoc($capturas2);
      $totalRows_capturas2 = mysql_num_rows($capturas2);

      $query_capturas3 = "SELECT * FROM reclu_exp_sahuayo WHERE IDempleado = '$IDempleado' AND IDpaso = 3";
      mysql_query("SET NAMES 'utf8'");
      $capturas3 = mysql_query($query_capturas3, $vacantes) or die(mysql_error());
      $row_capturas3 = mysql_fetch_assoc($capturas3);
      $totalRows_capturas3 = mysql_num_rows($capturas3);

      $query_capturas4 = "SELECT * FROM reclu_exp_sahuayo WHERE IDempleado = '$IDempleado' AND IDpaso = 4";
      mysql_query("SET NAMES 'utf8'");
      $capturas4 = mysql_query($query_capturas4, $vacantes) or die(mysql_error());
      $row_capturas4 = mysql_fetch_assoc($capturas4);
      $totalRows_capturas4 = mysql_num_rows($capturas4);

      $query_capturas5 = "SELECT * FROM reclu_exp_sahuayo WHERE IDempleado = '$IDempleado' AND IDpaso = 5";
      mysql_query("SET NAMES 'utf8'");
      $capturas5 = mysql_query($query_capturas5, $vacantes) or die(mysql_error());
      $row_capturas5 = mysql_fetch_assoc($capturas5);
      $totalRows_capturas5 = mysql_num_rows($capturas5);

      $query_capturas6 = "SELECT DISTINCT prod_activosj.IDusuario_segimiento, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno FROM prod_activosj LEFT JOIN vac_usuarios ON prod_activosj.IDusuario_segimiento = vac_usuarios.IDusuario WHERE prod_activosj.IDempleado = '$IDempleado'";
      mysql_query("SET NAMES 'utf8'");
      $capturas6 = mysql_query($query_capturas6, $vacantes) or die(mysql_error());
      $row_capturas6 = mysql_fetch_assoc($capturas6);
      $totalRows_capturas6 = mysql_num_rows($capturas6);

      if ($row_reporte['estatus'] == 1) { $estatus = 'Activo';} else if ($row_reporte['estatus'] == 3) { $estatus = 'Reingreso';} else if ($row_reporte['estatus'] == 4) { $estatus = 'Suspendido';} 
      $nombre = $row_reporte['emp_paterno']." ".$row_reporte['emp_materno']." ".$row_reporte['emp_nombre'];
      $nombre_usuario = $row_capturas6['usuario_parterno']." ".$row_capturas6['usuario_materno']." ".$row_capturas6['usuario_nombre'];

      if ($row_capturas1['preg1'] == 2) { $preg11 = 'Si';} else if ($row_capturas1['preg1'] == 1) { $preg11 = 'No';} else { $preg11 = '-';} 
      if ($row_capturas1['preg2'] == 2) { $preg12 = 'Si';} else if ($row_capturas1['preg2'] == 1) { $preg12 = 'No';} else { $preg12 = '-';} 
      if ($row_capturas1['preg3'] == 2) { $preg13 = 'Si';} else if ($row_capturas1['preg3'] == 1) { $preg13 = 'No';} else { $preg13 = '-';} 
      if ($row_capturas1['preg4'] == 2) { $preg14 = 'Si';} else if ($row_capturas1['preg4'] == 1) { $preg14 = 'No';} else { $preg14 = '-';} 

      if ($row_capturas2['preg1'] == 3) { $preg21 = 'Buena';} else if ($row_capturas2['preg1'] == 2) { $preg21 = 'Regular';} else if ($row_capturas2['preg1'] == 1) { $preg21 = 'Mala';} else { $preg21 = '-';} 
      if ($row_capturas2['preg2'] == 2) { $preg22 = 'Si';} else if ($row_capturas2['preg2'] == 1) { $preg22 = 'No';} else { $preg22 = '-';} 
      if ($row_capturas2['preg3'] == 2) { $preg23 = 'Si';} else if ($row_capturas2['preg3'] == 1) { $preg23 = 'No';} else { $preg23 = '-';} 
      if ($row_capturas2['preg4'] == 3) { $preg24 = 'Buena';} else if ($row_capturas2['preg4'] == 2) { $preg24 = 'Regular';} else if ($row_capturas2['preg4'] == 1) { $preg24 = 'Mala';} else { $preg24 = '-';} 

      if ($row_capturas3['preg1'] == 3) { $preg31 = 'Buena';} else if ($row_capturas3['preg3'] == 2) { $preg31 = 'Regular';} else if ($row_capturas3['preg3'] == 1) { $preg31 = 'Mala';} else { $preg31 = '-';} 
      if ($row_capturas3['preg2'] == 2) { $preg32 = 'Si';} else if ($row_capturas3['preg2'] == 1) { $preg32 = 'No';} else { $preg32 = '-';} 
      if ($row_capturas3['preg3'] == 2) { $preg33 = 'Si';} else if ($row_capturas3['preg3'] == 1) { $preg33 = 'No';} else { $preg33 = '-';} 
      if ($row_capturas3['preg4'] == 2) { $preg34 = 'Si';} else if ($row_capturas3['preg4'] == 1) { $preg34 = 'No';} else { $preg34 = '-';} 

      if ($row_capturas4['preg1'] == 3) { $preg41 = 'Buena';} else if ($row_capturas4['preg1'] == 2) { $preg41 = 'Regular';} else if ($row_capturas4['preg1'] == 1) { $preg41 = 'Mala';} else { $preg41 = '-';} 
      if ($row_capturas4['preg2'] == 2) { $preg42 = 'Si';} else if ($row_capturas4['preg2'] == 1) { $preg42 = 'No';} else { $preg42 = '-';} 
      if ($row_capturas4['preg3'] == 2) { $preg43 = 'Si';} else if ($row_capturas4['preg3'] == 1) { $preg43 = 'No';} else { $preg43 = '-';} 
      if ($row_capturas4['preg4'] == 4) { $preg44 = '4 faltas';} else if ($row_capturas4['preg4'] == 3) { $preg44 = '3 faltas';} else if ($row_capturas4['preg4'] == 2) { $preg44 = '2 faltas';}  else if ($row_capturas4['preg4'] == 1) { $preg44 = '1 falta';}  else if ($row_capturas4['preg4'] == 0) { $preg44 = '-';} 

      if ($row_capturas5['preg1'] == 3) { $preg51 = 'Buena';} else if ($row_capturas5['preg1'] == 2) { $preg51 = 'Regular';} else if ($row_capturas5['preg1'] == 1) { $preg51 = 'Mala';} else { $preg51 = '-';} 
      if ($row_capturas5['preg2'] == 2) { $preg52 = 'Si';} else if ($row_capturas5['preg2'] == 1) { $preg52 = 'No';} else { $preg52 = '-';} 
      if ($row_capturas5['preg3'] == 2) { $preg53 = 'Si';} else if ($row_capturas5['preg3'] == 1) { $preg53 = 'No';} else { $preg53 = '-';} 
      if ($row_capturas5['preg4'] == 2) { $preg54 = 'Si';} else if ($row_capturas5['preg4'] == 1) { $preg54 = 'No';} else { $preg54 = '-';} 

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte['telefono_1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte['fecha_antiguedad']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte['area']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $estatus); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $nombre_usuario); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $preg11); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $preg12); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $preg13); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $preg14); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_capturas1['observaciones']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $preg21); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $preg22); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $preg23); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $preg24); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $row_capturas2['observaciones']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $preg31); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $preg32); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $preg33); 
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $preg34); 
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $row_capturas3['observaciones']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $preg41); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $preg42); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $preg43); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $preg44); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $row_capturas4['observaciones']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $preg51); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, $preg52); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, $preg53); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowCount, $preg54); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, $row_capturas5['observaciones']); 
		
// Increment the Excel row counter
        $rowCount++; 
    }
	
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Reporte RyS'.date('dmY').'.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
    exit;
?>