<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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


require_once 'assets/PHPExcel.php';

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT cov_casos.IDempleado, cov_casos.emp_paterno, cov_casos.emp_materno, cov_casos.emp_nombre, vac_puestos.denominacion, vac_matriz.matriz, cov_casos.IDmotivo, cov_casos.IDdoc_oficial, cov_casos.IDestatus, cov_casos.enf_respiratoria,  cov_casos.enfermedad_general, cov_casos.tratam_inicio, cov_casos.tratam_fin, cov_casos.IDreemplazo, cov_casos.pull, cov_casos.observaciones FROM cov_casos LEFT JOIN prod_activos ON prod_activos.IDempleado = cov_casos.IDempleado LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = cov_casos.IDpuesto ORDER BY vac_matriz.IDmatriz ASC";
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
    $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Matriz');
    $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Motivo Vulnerable');
    $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Doc Oficial');
    $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Estatus COVID');
    $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Enfermedad General');
    $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Enfermedad General Detalle');
    $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Inicio Tratamiento');
    $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Termino Tratamiento');
    $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Reemplazo');
    $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Pull');
    $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Observaciones');

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
	$motivo = "";
	
	if (strlen(strstr($row_reporte['6'],'1')) > 0) { $motivo = $motivo . "DIABETICO ";}
	if (strlen(strstr($row_reporte['6'],'2')) > 0) { $motivo = $motivo . "HIPERTENSO ";}
	if (strlen(strstr($row_reporte['6'],'3')) > 0) { $motivo = $motivo . "MAYOR A 60 ";}
	if (strlen(strstr($row_reporte['6'],'4')) > 0) { $motivo = $motivo . "EMBARAZO ";}
	if (strlen(strstr($row_reporte['6'],'5')) > 0) { $motivo = $motivo . "LACTANCIA ";}
	if (strlen(strstr($row_reporte['6'],'6')) > 0) { $motivo = $motivo . "ENF. ESP. ";}
	if (strlen(strstr($row_reporte['6'],'7')) > 0) { $motivo = $motivo . "OTRO";}	

	if($row_reporte[7] == 1) { $docto = "SI"; } else { $docto = "NO"; }
	if($row_reporte[9] == 1) { $enfgral = "SI"; } else { $enfgral = "NO"; }
	if($row_reporte[13] == 1) { $reemp = "SI"; } else { $reemp = "NO"; }
	if($row_reporte[14] == 1) { $pull = "SI"; } else { $pull = "NO"; }


	switch ($row_reporte['8']) {
	case 0:  $el_estatus = "NO APLICA";      break;     
	case 1:  $el_estatus = "POSITIVO EN RECUPERACION";      break;     
	case 5:  $el_estatus = "POSITIVO RECUPERADO";      break;    
	case 13:  $el_estatus = "POSITIVO HOSPITALIZADO";    break;    
	case 2:  $el_estatus = "SOSPECHOSO";    break;    
	case 6:  $el_estatus = "SOSPECHOSO EN AISLAMIENTO";      break;    
	case 10:  $el_estatus = "SOSPECHOSO REINGRESADO";      break;    
	case 11:  $el_estatus = "SOSPECHOSO DECESO";      break;    
	case 12:  $el_estatus = "SOSPECHOSO HOSPITALIZADO";      break;    
	case 3:  $el_estatus = "POR CONTACTO";      break;    
	case 7:  $el_estatus = "POR CONTACTO EN AISLAMIENTO";      break;    
	case 4:  $el_estatus = "NO APLICA";      break;    
	case 8:  $el_estatus = "POR CONTACTO REINGRESADO";      break;    
	case 9:  $el_estatus = "POSITIVO DECESO";      break;    
	  }
	


        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte[5]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $motivo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $docto); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $el_estatus); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $enfgral); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte[10]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_reporte[11]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporte[12]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $reemp); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $pull); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_reporte[15]); 
		$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);

		foreach(range('A','P') as $columnID) {$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);}


// Increment the Excel row counter
        $rowCount++; 
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Excel');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Productividad '.date('dmY') . '.xls"');
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