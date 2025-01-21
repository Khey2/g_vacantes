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
$mis_areas = $row_usuario['IDmatrizes'];$la_matriz = $row_usuario['IDmatriz'];
$las_matrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];

// el mes
  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }
	  
mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

$IDusuario = $_GET['IDusuario'];
mysql_select_db($database_vacantes, $vacantes);
$query_candidato = "SELECT cv_activos.IDusuario, cv_activos.a_paterno, cv_activos.a_materno, cv_activos.a_nombre, cv_activos.a_correo, cv_activos.usuario, cv_activos.puesto, cv_activos.fecha_captura, cv_activos.a_rfc, cv_activos.a_curp, cv_activos.a_sexo, cv_activos.a_imss, cv_activos.a_afore, cv_activos.a_licencia, cv_activos.a_pasaporte, cv_activos.a_cedula_profesional,  cv_activos.a_cartilla, cv_activos.IDnacionalidad, cv_activos.a_estado_civil, cv_activos.a_banco, cv_activos.a_cuenta_bancaria_clabe, cv_activos.a_cuenta_bancaria, cv_activos.c_fecha_nacimiento, cv_activos.d_calle, cv_activos.d_numero_calle, cv_activos.d_colonia, cv_activos.d_delegacion_municipio, cv_activos.d_estado, cv_activos.d_codigo_postal, cv_activos.telefono_1, cv_activos.telefono_2, cv_activos.IDescolaridad, cv_activos.fecha_termino, cv_activos.escuela, cv_activos.estudios_actuales, cv_activos.idioma,  cv_activos.bienes, cv_activos.bienes_valor, cv_activos.renta, cv_activos.renta_monto,  cv_activos.auto, cv_activos.auto_modelo, cv_activos.auto_valor, cv_activos.afianzado, cv_activos.afianzado_empleo, cv_activos.afianzado_motivo, cv_activos.afianzado_compania, cv_activos.afianzado_rechazo, cv_activos.deudas, cv_activos.deutas_tipo, cv_activos.deudas_monto, cv_activos.gastos, cv_activos.ingresos_adicionales, cv_activos.negocio, cv_activos.negocio_nombre, cv_activos.medio_vacante, cv_activos.parentesco, cv_activos.parentesco_nombres, cv_activos.viajar, cv_activos.turnos, cv_activos.contacto_jefe_actual, cv_activos.trabaja_actual,  cv_activos.sindicato, cv_activos.sindicato_cual FROM cv_activos WHERE IDusuario = '$IDusuario'";
$candidato = mysql_query($query_candidato, $vacantes) or die(mysql_error());

require_once 'assets/PHPExcel.php';

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("ingreso/Candidato.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    	while($row_candidato = mysql_fetch_array($candidato)){ 
	
		$rfc = $row_candidato[8];	

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_candidato[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_candidato[1]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_candidato[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_candidato[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_candidato[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_candidato[5]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_candidato[6]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_candidato[7]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_candidato[8]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_candidato[9]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_candidato[10]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row_candidato[11]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_candidato[12]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_candidato[13]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_candidato[14]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_candidato[15]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $row_candidato[16]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $row_candidato[17]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $row_candidato[18]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $row_candidato[19]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $row_candidato[20]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $row_candidato[21]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $row_candidato[22]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $row_candidato[23]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $row_candidato[24]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $row_candidato[25]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $row_candidato[26]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $row_candidato[27]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $row_candidato[28]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, $row_candidato[29]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, $row_candidato[30]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowCount, $row_candidato[31]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, $row_candidato[32]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, $row_candidato[33]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, $row_candidato[34]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, $row_candidato[35]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, $row_candidato[36]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, $row_candidato[37]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AM'.$rowCount, $row_candidato[38]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AN'.$rowCount, $row_candidato[39]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AO'.$rowCount, $row_candidato[40]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AP'.$rowCount, $row_candidato[41]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AQ'.$rowCount, $row_candidato[42]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AR'.$rowCount, $row_candidato[43]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AS'.$rowCount, $row_candidato[44]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AT'.$rowCount, $row_candidato[45]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AU'.$rowCount, $row_candidato[46]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AV'.$rowCount, $row_candidato[47]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AW'.$rowCount, $row_candidato[48]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AX'.$rowCount, $row_candidato[49]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AY'.$rowCount, $row_candidato[50]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AZ'.$rowCount, $row_candidato[51]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BA'.$rowCount, $row_candidato[52]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BB'.$rowCount, $row_candidato[53]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BC'.$rowCount, $row_candidato[54]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BD'.$rowCount, $row_candidato[55]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BE'.$rowCount, $row_candidato[56]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BF'.$rowCount, $row_candidato[57]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BG'.$rowCount, $row_candidato[58]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BH'.$rowCount, $row_candidato[59]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BI'.$rowCount, $row_candidato[60]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BJ'.$rowCount, $row_candidato[61]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BK'.$rowCount, $row_candidato[62]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BL'.$rowCount, $row_candidato[63]); 

// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Candidato.xls"');
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