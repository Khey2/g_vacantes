<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');
require_once 'assets/PHPExcel.php';
set_time_limit(0);
// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
$colname_usuario = $_SESSION['kt_login_id'];}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];


//varibles
if (isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) { $_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = $IDmatriz; }
if (isset($_POST['la_semana']) && ($_POST['la_semana']  > 0)) { $_SESSION['la_semana'] = $_POST['la_semana']; } else { $_SESSION['la_semana'] = $semana; }
if (isset($_POST['anioo']) && ($_POST['anioo']  > 0)) { $_SESSION['anioo'] = $_POST['anioo']; } else { $_SESSION['anioo'] = $anio; }

$la_matriz = $_SESSION['la_matriz'];
$la_semana = $_SESSION['la_semana'];
$anioo = $_SESSION['anioo'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz_texto = $row_matriz['matriz'];

//Auxiliar de Almacen
mysql_select_db($database_vacantes, $vacantes);
$query_reporte_aux = "SELECT prod_captura.IDempleado, prod_captura.emp_paterno, prod_captura.emp_materno, prod_captura.emp_nombre, prod_captura.denominacion, prod_captura.garantizado, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue,  prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.adicional, prod_captura.pago, prod_captura.reci, prod_captura.carg,  prod_captura.esti, prod_captura.dist FROM prod_captura WHERE prod_captura.IDmatriz = '$la_matriz' AND prod_captura.semana = '$la_semana' AND prod_captura.IDpuesto = 2  AND prod_captura.anio = '$anioo' ORDER BY prod_captura.IDempleado ASC";
mysql_query("SET NAMES 'utf8'");
$reporte_aux = mysql_query($query_reporte_aux, $vacantes) or die(mysql_error());


// Los ID de los demás puestos
mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT DISTINCT prod_captura.IDempleado, prod_captura.emp_paterno, prod_captura.emp_materno, prod_captura.emp_nombre, prod_captura.denominacion, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue,  prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.pago, prod_captura.garantizado, prod_captura.adicional, prod_captura.IDpuesto FROM prod_captura WHERE  prod_captura.IDmatriz = '$la_matriz' AND prod_captura.semana = '$la_semana' AND prod_captura.anio = '$anioo' AND prod_captura.IDpuesto <> 2"; 
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());


// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("PRD/imprime_prod.xlsx");

    // Agregamos Auxiliares de Almacén
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte_aux = mysql_fetch_array($reporte_aux)){ 

		if($row_reporte_aux['garantizado'] > 0) { $garantia = "SI"; } else { $garantia = "NO"; }
		$faltasp = $row_reporte_aux['lun'] + $row_reporte_aux['mar'] + $row_reporte_aux['mie'] + $row_reporte_aux['jue'] + $row_reporte_aux['vie'] + $row_reporte_aux['sab'];
		$faltas = 6 - $faltasp;
		if($row_reporte_aux['adicional'] > 0) { $adicional = "SI"; } else { $adicional = "NO"; }

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte_aux['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte_aux['emp_paterno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte_aux['emp_materno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte_aux['emp_nombre']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte_aux['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $la_semana); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $faltas); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte_aux['reci']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte_aux['carg']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporte_aux['esti']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte_aux['dist']);
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte_aux['pago']);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $garantia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $adicional); 
		$objPHPExcel->getActiveSheet()->getRowDimension($rowCount)->setRowHeight(30);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));

	
// Increment the Excel row counter
        $rowCount++; 
    }


    // Agregamos el resto de los puestos
    $objPHPExcel->setActiveSheetIndex(1);

    $rowCount2 = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 

		$IDpuesto = $row_reporte['IDpuesto'];
		$IDmatriz = $la_matriz;
		$Rkpi1 = $row_reporte['a1'];
		$Rkpi2 = $row_reporte['a2'];
		$Rkpi3 = $row_reporte['a3'];
		$Rkpi4 = $row_reporte['a4'];
				
		$query_Indicador1 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.p = $Rkpi1 AND prod_kpis.a = 1 AND prod_kpis.b = 3";
		$Indicador1 = mysql_query($query_Indicador1, $vacantes) or die(mysql_error());
		$row_Indicador1 = mysql_fetch_assoc($Indicador1);
		$totalRows_Indicador1 = mysql_num_rows($Indicador1);
		$Resulta1 = $row_Indicador1['c'];

		$query_Indicador2 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.p = $Rkpi2 AND prod_kpis.a = 2 AND prod_kpis.b = 3";
		$Indicador2 = mysql_query($query_Indicador2, $vacantes) or die(mysql_error());
		$row_Indicador2 = mysql_fetch_assoc($Indicador2);
		$totalRows_Indicador2 = mysql_num_rows($Indicador2);
		$Resulta2 = $row_Indicador2['c'];

		$query_Indicador3 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.p = $Rkpi3 AND prod_kpis.a = 3 AND prod_kpis.b = 3";
		$Indicador3 = mysql_query($query_Indicador3, $vacantes) or die(mysql_error());
		$row_Indicador3 = mysql_fetch_assoc($Indicador3);
		$totalRows_Indicador3 = mysql_num_rows($Indicador3);
		$Resulta3 = $row_Indicador3['c'];

		$query_Indicador4 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.p = $Rkpi4 AND prod_kpis.a = 4 AND prod_kpis.b = 3";
		$Indicador4 = mysql_query($query_Indicador4, $vacantes) or die(mysql_error());
		$row_Indicador4 = mysql_fetch_assoc($Indicador4);
		$totalRows_Indicador4 = mysql_num_rows($Indicador4);
		$Resulta4 = $row_Indicador4['c'];

		$query__Indicador1 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.a = 1 AND prod_kpis.b = 1";
		$_Indicador1 = mysql_query($query__Indicador1, $vacantes) or die(mysql_error());
		$row__Indicador1 = mysql_fetch_assoc($_Indicador1);
		$totalRows__Indicador1 = mysql_num_rows($_Indicador1);
		$Resulta1_ = $row__Indicador1['c'];

		$query__Indicador2 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.a = 2 AND prod_kpis.b = 1";
		$_Indicador2 = mysql_query($query__Indicador2, $vacantes) or die(mysql_error());
		$row__Indicador2 = mysql_fetch_assoc($_Indicador2);
		$totalRows__Indicador2 = mysql_num_rows($_Indicador2);
		$Resulta2_ = $row__Indicador2['c'];

		$query__Indicador3 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.a = 3 AND prod_kpis.b = 1";
		$_Indicador3 = mysql_query($query__Indicador3, $vacantes) or die(mysql_error());
		$row__Indicador3 = mysql_fetch_assoc($_Indicador3);
		$totalRows__Indicador3 = mysql_num_rows($_Indicador3);
		$Resulta3_ = $row__Indicador3['c'];

		$query__Indicador4 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDpuesto = $IDpuesto AND prod_kpis.IDmatriz = $IDmatriz AND prod_kpis.a = 4 AND prod_kpis.b = 1";
		$_Indicador4 = mysql_query($query__Indicador4, $vacantes) or die(mysql_error());
		$row__Indicador4 = mysql_fetch_assoc($_Indicador4);
		$totalRows__Indicador4 = mysql_num_rows($_Indicador4);
		$Resulta4_ = $row__Indicador4['c'];

		if($row_reporte['garantizado'] > 0) { $garantia = "SI"; } else { $garantia = "NO"; }
		$faltasp = $row_reporte['lun'] + $row_reporte['mar'] + $row_reporte['mie'] + $row_reporte['jue'] + $row_reporte['vie'] + $row_reporte['sab'];
		$faltas = 6 - $faltasp;
		if($row_reporte['adicional'] > 0) { $adicional = $row_reporte['adicional']; } else { $adicional = ""; }
		
		
		if($totalRows_Indicador1 > 0) { $Resultado1 = $Resulta1_ . " :\r\n ". $Resulta1;} else { $Resultado1 = '';}
		if($totalRows_Indicador2 > 0) { $Resultado2 = $Resulta2_ . " :\r\n ". $Resulta2;} else { $Resultado2 = '';}
		if($totalRows_Indicador3 > 0) { $Resultado3 = $Resulta3_ . " :\r\n ". $Resulta3;} else { $Resultado3 = '';}
		if($totalRows_Indicador4 > 0) { $Resultado4 = $Resulta4_ . " :\r\n ". $Resulta4;} else { $Resultado4 = '';}


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount2, $row_reporte['IDempleado']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount2, $row_reporte['emp_paterno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount2, $row_reporte['emp_materno']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount2, $row_reporte['emp_nombre']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount2, $row_reporte['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount2, $la_semana); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount2, $faltas); 

		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount2, $Resultado1); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount2, $Resultado2); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount2, $Resultado3); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount2, $Resultado4);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount2, $row_reporte['pago']);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount2, $garantia); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount2, $adicional); 
		$objPHPExcel->getActiveSheet()->getRowDimension($rowCount2)->setRowHeight(30);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('K'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('L'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ))));
		$objPHPExcel->getActiveSheet()->getStyle('M'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
		$objPHPExcel->getActiveSheet()->getStyle('N'.$rowCount2)->applyFromArray( array('borders' => array( 'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));

	
// Increment the Excel row counter
        $rowCount2++; 
    }


    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Productividad Sucursal '.$la_matriz_texto.' Semana '.$la_semana.'.xls"');
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