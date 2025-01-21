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
$semana = date("W", strtotime($la_fecha));

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];


require_once 'assets/PHPExcel.php';
set_time_limit(0);

if($_GET['areas'] == 1) {$a1 = ' AND prod_captura.IDarea in (1,2,3,4,11) ';}
else if($_GET['areas'] == 2) {$a1 = ' AND prod_captura.IDarea in (1,2,11) ';}
else if($_GET['areas'] == 3) {$a1 = ' AND prod_captura.IDarea in (3,4) ';}
else {$a1 = ' AND prod_captura.IDarea in (1,2,3,4,11) ';}

if($_GET['tipo'] == 1) {$b1 = ' AND prod_captura.autorizador IS NOT NULL ';} 
else {$b1 = '';}

if (isset($_GET['IDmatriz'])) {$c1 = " AND prod_captura.IDmatriz IN (".$_GET['IDmatriz'].") ";}
else {$c1 = " AND prod_captura.IDmatriz IN ".$IDmatriz;} 

$lasemana = $semana;
if(isset($_GET['semana'])) { $lasemana = $_GET['semana'];} elseif (isset($_GET['mi_semana'])) { $lasemana = $_GET['mi_semana'];} 
else {$lasemana = $semana;}

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_captura.IDempleado, prod_captura.emp_paterno, prod_captura.emp_materno, prod_captura.emp_nombre, prod_captura.denominacion, prod_captura.sueldo_total, prod_captura.sueldo_total, prod_captura.garantizado, prod_captura.adicional, prod_captura.adicional2, prod_captura.reci, prod_captura.carg, prod_captura.dist, prod_captura.esti, prod_captura.pago, prod_captura.pago_total, prod_captura.capturador, prod_captura.validador, prod_captura.autorizador, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, vac_matriz.matriz, prod_captura.IDpuesto, prod_captura.autorizador, prod_captura.bono_asistencia FROM prod_captura LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_captura.IDmatriz WHERE prod_captura.semana = '$lasemana'  AND prod_captura.anio = '$anio' ".$a1.$b1.$c1." ORDER BY vac_matriz.IDmatriz ASC";
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PRD/cedulaT.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 3; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	$AuxiliaresAlmacen = array(2, 18, 281, 282, 313, 371);
	$sueldo = (($row_reporte[5] / 30) * 7);
	$el_puesto = $row_reporte[27];
	
	if($row_reporte[7] > 0) { $garantia = "SI"; } else { $garantia = "NO"; }
	if($row_reporte[29] > 0) { $bono_asistencia = $row_reporte[29]; } else { $bono_asistencia = 0; }
	if($row_reporte[16] == "") { $capturado = "NO"; } else { $capturado = "SI"; }
	if($row_reporte[17] == "") { $validado = "NO"; } else { $validado = "SI"; }
	if($row_reporte[18] == "") { $autorizado = "NO"; } else { $autorizado = "SI"; }
	if($row_reporte[14] > 0) { $calculado = $row_reporte[14] / 100; } else { $calculado = 0; }
	if(($row_reporte[19] + $row_reporte[20] + $row_reporte[21] + $row_reporte[22] + $row_reporte[23] + $row_reporte[24] + $row_reporte[25]) < 6 ) 
	{ $asistencia = "NO"; } else { $asistencia = "SI"; }

	if (in_array($el_puesto, $AuxiliaresAlmacen)) { $pago_total_f = $row_reporte[8] + $row_reporte[15] + $row_reporte[29]; } else { $pago_total_f = $row_reporte[9] + $row_reporte[15] + $row_reporte[29];}
	if (in_array($el_puesto, $AuxiliaresAlmacen)) { $adicional = 0; } else {if($row_reporte[8] > 0) { $adicional = $row_reporte[8] / 100; } else { $adicional = 0; }	}

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $sueldo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $garantia); 

		if (in_array($el_puesto, $AuxiliaresAlmacen)) {
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $adicional); 
		
			if ($row_reporte[8] == 0) {
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, '0'); 
			} else {
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[8]); 
		}

		} else {
			
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $adicional); 
		
			if ($row_reporte[8] == 0) {
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, '0'); 
			} else {
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[9]); 
		} 
		
		}

  	if($row_reporte[10] > 0) { $rec1 = $row_reporte[10]; } else { $rec1 = 0; }  
	if($row_reporte[11] > 0) { $rec2 = $row_reporte[11]; } else { $rec2 = 0; }  
	if($row_reporte[12] > 0) { $rec3 = $row_reporte[12]; } else { $rec3 = 0; }  
	if($row_reporte[13] > 0) { $rec4 = $row_reporte[13]; } else { $rec4 = 0; }  


       $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $bono_asistencia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $rec1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $rec2); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $rec4); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $rec3); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $calculado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_reporte[15]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $pago_total_f); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $capturado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $validado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $autorizado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $asistencia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $semana); 
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $row_reporte[26]); 
		
// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Productividad '.date('dmY').' Semana '.$lasemana.'.xls"');
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