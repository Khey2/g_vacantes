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
$semana = $_GET['semana'];
$la_matriz = $_GET['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

require_once 'assets/PHPExcel.php';
	
mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, vac_puestos.denominacion, prod_activos.sueldo_diario, inc_captura.lul, inc_captura.luf, inc_captura.mal, inc_captura.maf, inc_captura.mil, inc_captura.mif, inc_captura.jul, inc_captura.juf, inc_captura.vil, inc_captura.vif, inc_captura.sal, inc_captura.saf, inc_captura.dol, inc_captura.dof, inc_captura.inc5, inc_captura.validador, inc_captura.obs5, prod_activos.descripcion_nomina, vac_matriz.matriz FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado LEFT JOIN inc_motivos ON inc_captura.IDmotivo3 = inc_motivos.IDmotivo WHERE inc_captura.inc5 > 0  AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio'"; 
mysql_query("SET NAMES 'utf8'"); 
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
	
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'No.Emp');
    $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Paterno');
    $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Materno');
    $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Nombre');
    $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Puesto');
    $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Sueldo Diario');
    $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Locales');
    $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Foraneos');
    $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Monto');
    $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Validado');
    $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Observaciones');
    $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Empresa');
    $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Sucursal');

$rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 

	if($row_reporte[21] == "") { $validado = ""; } else { $validado = "SI"; }
	$locales = $row_reporte[6] + $row_reporte[8] + $row_reporte[10] + $row_reporte[12] + $row_reporte[14] + $row_reporte[16] + $row_reporte[18];
	$foraneo = $row_reporte[7] + $row_reporte[9] + $row_reporte[11] + $row_reporte[13] + $row_reporte[15] + $row_reporte[17] + $row_reporte[19];


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte[5]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $locales); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $foraneo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[20]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $validado); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte[22]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte[23]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte[24]); 
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I2:I500')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
		$objPHPExcel->getActiveSheet()->getStyle('I2:I500')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		foreach(range('A','M') as $columnID) {$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);}


// Increment the Excel row counter
        $rowCount++; 
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Excel');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Premios por Viaje - Semana ' . $semana . " del " . $anio .  '  global.xls"');
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