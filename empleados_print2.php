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
$fecha = date("Y-m-d"); // la fecha actual

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


$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT con_empleados.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.denominacion, vac_puestos.clave_puesto FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$IDpuesto = $row_contratos['IDpuesto'];
$clave_puesto = $row_contratos['clave_puesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT vac_areas.area, vac_puestos.IDpuesto, vac_puestos.denominacion FROM vac_puestos INNER JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE vac_puestos.IDpuesto = $IDpuesto";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);


//variables
$elarea = $row_puesto['area'];
$matriz = $row_contratos['matriz'];
$denominacion = $row_contratos['denominacion'];
$IDempleadoJ = html_entity_decode($row_contratos['IDempleadoJ'], ENT_QUOTES, "UTF-8");
$IDempleadoJP = html_entity_decode($row_contratos['IDempleadoJP'], ENT_QUOTES, "UTF-8");
$sueldo_mensual = $row_contratos['b_sueldo_mensual'];
$a_paterno = html_entity_decode($row_contratos['a_paterno'], ENT_QUOTES, "UTF-8");
$a_materno = html_entity_decode($row_contratos['a_materno'], ENT_QUOTES, "UTF-8");
$a_nombre = html_entity_decode($row_contratos['a_nombre'], ENT_QUOTES, "UTF-8");
$a_rfc = $row_contratos['a_rfc'];
$a_curp = $row_contratos['a_curp'];
$a_imss = "'".$row_contratos['a_imss'];
$a_sexo_ = $row_contratos['a_sexo'];

if ($a_sexo_ == 1) {$a_sexo = "M";} else {$a_sexo = "F";}

$lugar_nacimiento_ = substr($row_contratos['a_curp'],11,2);
$query_estados = "SELECT * FROM con_estados WHERE estado_2 = '$lugar_nacimiento_'";
$estados = mysql_query($query_estados, $vacantes) or die(mysql_error());
$row_estados = mysql_fetch_assoc($estados);
$lugar_nacimiento = $row_estados['estado'];

$c_fecha_nacimiento = date('d/m/Y', strtotime($row_contratos['c_fecha_nacimiento']));

$unidad_medica = $row_contratos['unidad_medica'];

$query_padre = "SELECT * FROM con_dependientes WHERE IDempleado = $IDempleado AND IDtipo = 2";
$padre = mysql_query($query_padre, $vacantes) or die(mysql_error());
$row_padre = mysql_fetch_assoc($padre);
$a_padre = $row_padre['nombre'];

$query_madre = "SELECT * FROM con_dependientes WHERE IDempleado = $IDempleado AND IDtipo = 3";
$madre = mysql_query($query_madre, $vacantes) or die(mysql_error());
$row_madre = mysql_fetch_assoc($madre);
$a_madre = $row_madre['nombre'];


if($row_contratos['a_banco'] == 2) {$a_cuenta_bancaria = 0;} 		else {$a_cuenta_bancaria = "'".$row_contratos['a_cuenta_bancaria'];}
if($row_contratos['a_banco'] == 2) {$a_cuenta_bancaria_clabe = 0;}	else {$a_cuenta_bancaria_clabe = "'".$row_contratos['a_cuenta_bancaria_clabe'];}

$date_a = new DateTime($row_contratos['fecha_alta']);
$date_b = new DateTime($row_contratos['c_fecha_nacimiento']);
$diff_c = $date_a->diff($date_b);

$a_edad =  $diff_c->y;

$a_estado_civil_ = $row_contratos['a_estado_civil'];
if ($a_estado_civil_ == 2){$a_estado_civil = "Casado";} else {$a_estado_civil = "Soltero";}

$a_correo = $row_contratos['a_correo'];

$d_calle = html_entity_decode($row_contratos['d_calle'], ENT_QUOTES, "UTF-8");
$d_numero_calle = $row_contratos['d_numero_calle'];
$d_numero_calle_int = $row_contratos['d_numero_calle_int'];
$d_colonia = html_entity_decode($row_contratos['d_colonia'], ENT_QUOTES, "UTF-8");
$d_delegacion_municipio = html_entity_decode($row_contratos['d_delegacion_municipio'], ENT_QUOTES, "UTF-8");
$d_estado = html_entity_decode($row_contratos['d_estado'], ENT_QUOTES, "UTF-8");
$d_codigo_postal = $row_contratos['d_codigo_postal'];
$telefono_1 = $row_contratos['telefono_1'];

$c_fecha_alta = date('d/m/Y', strtotime($row_contratos['fecha_alta']));

$b_sueldo_diario_int = $row_contratos['b_sueldo_diario_int'];
$b_sueldo_diario = $row_contratos['b_sueldo_diario'];
$b_sueldo_mensual = $row_contratos['b_sueldo_mensual'];


// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CONTS/requi.xlsx");

// Add some data
//$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->SetCellValue('W3', $fecha); 
$objPHPExcel->getActiveSheet()->SetCellValue('R12', $matriz); 
$objPHPExcel->getActiveSheet()->SetCellValue('R13', $elarea); 
$objPHPExcel->getActiveSheet()->SetCellValue('F17', $denominacion); 
$objPHPExcel->getActiveSheet()->SetCellValue('U17', $clave_puesto); 
$objPHPExcel->getActiveSheet()->SetCellValue('E19', $IDempleadoJP); 
$objPHPExcel->getActiveSheet()->SetCellValue('R19', $IDempleadoJ); 
$objPHPExcel->getActiveSheet()->SetCellValue('C88', $IDempleadoJ); 
$objPHPExcel->getActiveSheet()->SetCellValue('N53', $a_paterno);  
$objPHPExcel->getActiveSheet()->SetCellValue('T53', $a_materno); 
$objPHPExcel->getActiveSheet()->SetCellValue('H53', $a_nombre); 
$objPHPExcel->getActiveSheet()->SetCellValue('U55', $a_rfc); 
$objPHPExcel->getActiveSheet()->SetCellValue('M55', $a_curp); 
$objPHPExcel->getActiveSheet()->SetCellValue('E55', $a_imss); 
$objPHPExcel->getActiveSheet()->SetCellValue('D57', $a_sexo); 
$objPHPExcel->getActiveSheet()->SetCellValue('L57', $lugar_nacimiento); 
$objPHPExcel->getActiveSheet()->SetCellValue('H59', $c_fecha_nacimiento); 
$objPHPExcel->getActiveSheet()->SetCellValue('U59', $unidad_medica); 
$objPHPExcel->getActiveSheet()->SetCellValue('G61', $a_padre); 
$objPHPExcel->getActiveSheet()->SetCellValue('G63', $a_madre); 
$objPHPExcel->getActiveSheet()->SetCellValue('K65', $a_cuenta_bancaria); 
$objPHPExcel->getActiveSheet()->SetCellValue('R65', $a_cuenta_bancaria_clabe); 
$objPHPExcel->getActiveSheet()->SetCellValue('D67', $a_edad); 
$objPHPExcel->getActiveSheet()->SetCellValue('I67', $a_estado_civil); 
$objPHPExcel->getActiveSheet()->SetCellValue('S67', $a_correo); 
$objPHPExcel->getActiveSheet()->SetCellValue('G69', $d_calle); 
$objPHPExcel->getActiveSheet()->SetCellValue('H71', $d_numero_calle); 
$objPHPExcel->getActiveSheet()->SetCellValue('M71', $d_numero_calle_int); 
$objPHPExcel->getActiveSheet()->SetCellValue('J73', $d_colonia); 
$objPHPExcel->getActiveSheet()->SetCellValue('E75', $d_delegacion_municipio); 
$objPHPExcel->getActiveSheet()->SetCellValue('S75', $d_estado); 
$objPHPExcel->getActiveSheet()->SetCellValue('T71', $d_codigo_postal); 
$objPHPExcel->getActiveSheet()->SetCellValue('T73', $telefono_1); 
$objPHPExcel->getActiveSheet()->SetCellValue('G78', $c_fecha_alta); 
$objPHPExcel->getActiveSheet()->SetCellValue('U78', $b_sueldo_diario_int); 
$objPHPExcel->getActiveSheet()->SetCellValue('Q78', $b_sueldo_diario); 
$objPHPExcel->getActiveSheet()->SetCellValue('K78', $b_sueldo_mensual); 

$objPHPExcel->getActiveSheet()->mergeCells('W3:AA3');
$objPHPExcel->getActiveSheet()->mergeCells('R12:Z12');
$objPHPExcel->getActiveSheet()->mergeCells('R13:Z13');
$objPHPExcel->getActiveSheet()->mergeCells('F17:O17');
$objPHPExcel->getActiveSheet()->mergeCells('U17:Z17');
$objPHPExcel->getActiveSheet()->mergeCells('E19:O19');
$objPHPExcel->getActiveSheet()->mergeCells('R19:Z19');
$objPHPExcel->getActiveSheet()->mergeCells('H53:L53');
$objPHPExcel->getActiveSheet()->mergeCells('N53:R53');
$objPHPExcel->getActiveSheet()->mergeCells('T53:Z53');
$objPHPExcel->getActiveSheet()->mergeCells('E55:I55');
$objPHPExcel->getActiveSheet()->mergeCells('M55:R55');
$objPHPExcel->getActiveSheet()->mergeCells('U55:Z55');
$objPHPExcel->getActiveSheet()->mergeCells('L57:Z57');
$objPHPExcel->getActiveSheet()->mergeCells('H59:L59');
$objPHPExcel->getActiveSheet()->mergeCells('U59:Z59');
$objPHPExcel->getActiveSheet()->mergeCells('G61:Z61');
$objPHPExcel->getActiveSheet()->mergeCells('G63:Z63');
$objPHPExcel->getActiveSheet()->mergeCells('K65:N65');
$objPHPExcel->getActiveSheet()->mergeCells('R65:V65');
$objPHPExcel->getActiveSheet()->mergeCells('I67:L67');
$objPHPExcel->getActiveSheet()->mergeCells('S67:Z67');
$objPHPExcel->getActiveSheet()->mergeCells('G69:Z69');
$objPHPExcel->getActiveSheet()->mergeCells('H71:I71');
$objPHPExcel->getActiveSheet()->mergeCells('M71:N71');
$objPHPExcel->getActiveSheet()->mergeCells('T71:Z71');
$objPHPExcel->getActiveSheet()->mergeCells('J73:Q73');
$objPHPExcel->getActiveSheet()->mergeCells('T73:Z73');
$objPHPExcel->getActiveSheet()->mergeCells('E75:O75');
$objPHPExcel->getActiveSheet()->mergeCells('S75:Z75');
$objPHPExcel->getActiveSheet()->mergeCells('G78:I78');
$objPHPExcel->getActiveSheet()->mergeCells('K78:M78');
$objPHPExcel->getActiveSheet()->mergeCells('Q78:S78');
$objPHPExcel->getActiveSheet()->mergeCells('U78:Z78');

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="REQUISICION '.date('dmY')." ".$IDempleado.'.xls"');
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