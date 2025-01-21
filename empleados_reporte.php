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

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];


$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

require_once 'assets/PHPExcel.php';

$la_empresa = $_GET['la_empresa'];
$el_estatus = $_GET['estatus'];

$a1 = "";
$b1	= "";

if($la_empresa > 0) {
$a1 = " AND con_empleados.IDmatriz IN ($la_empresa)"; }

if($el_estatus == 0) {
$b1 = " AND (con_empleados.estatus = 0 OR con_empleados.estatus = '') "; }

$query_reporte = "SELECT
vac_matriz.matriz,
con_empleados.estatus,
con_empleados.IDempleado,
con_empleados.a_paterno, 
con_empleados.a_materno,
con_empleados.a_nombre, 
con_empleados.a_correo,
con_empleados.a_rfc,
con_empleados.a_curp,
con_empleados.a_sexo,
con_empleados.a_imss,
con_empleados.IDnacionalidad,
con_empleados.a_estado_civil,
con_bancos.banco, 
con_empleados.a_cuenta_bancaria_clabe, 
con_empleados.a_cuenta_bancaria,
con_empleados.fecha_alta,
con_empleados.c_fecha_nacimiento, 
con_empleados.d_calle, 
con_empleados.d_numero_calle, 
con_empleados.d_colonia, 
con_empleados.d_delegacion_municipio,
con_empleados.d_estado,
con_empleados.d_codigo_postal,
con_cuentas.cuenta,
con_subcuentas.subcuenta,
con_empleados.local_foraneo,
con_empleados.b_sueldo_diario,
con_empleados.b_sueldo_diario_int,
con_empleados.b_sueldo_mensual,
con_empleados.tipo_de_contrato,
con_empleados.IDpuesto
FROM con_empleados 
LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = con_empleados.IDmatriz 
LEFT JOIN con_bancos ON con_bancos.IDbanco = con_empleados.a_banco 
LEFT JOIN con_estados ON con_estados.IDestado = con_empleados.IDestado 
LEFT JOIN con_cuentas ON con_cuentas.IDcuenta = con_empleados.IDcuenta
LEFT JOIN con_subcuentas ON con_subcuentas.IDsubcuenta = con_empleados.IDsubcuenta
WHERE IDempleado IS NOT NULL" . $a1 . $b1;
mysql_query("SET NAMES 'utf8'");
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("EMP/cedulaB.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 2; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
		$IDempleado = $row_reporte['IDempleado'];
	
		if($row_reporte[1] == 0) { $estatus = "INACTIVO"; } else { $estatus = "ACTIVO"; }
		if($row_reporte[9] == 1) { $sexo = "HOMBRE"; } else { $sexo = "MUJER"; }
		if($row_reporte[11] == 1) { $nacionalidad = "MEXICANA"; } else { $nacionalidad = "EXTRANJERA"; }
		if($row_reporte[12] == 1) { $civil = "SOLTERO"; } else { $civil = "CASADO"; }
		if($row_reporte[26] == 1) { $local_foraneo = "LOCAL"; } else { $local_foraneo = "FORANEO"; }
		if($row_reporte[26] == 1) { $tipo_de_contrato = "DETERMINADO"; } else { $tipo_de_contrato = "INDETERMINADO"; }
		
		//edad
		$fecha_hoy = new DateTime('now');
		$fecha_nacimiento = new DateTime($row_reporte['17']);
		$diferencia = $fecha_hoy->diff($fecha_nacimiento);
		$edad =  $diferencia->y;

		//beneficiario
		mysql_select_db($database_vacantes, $vacantes);
		$query_benficiario = "SELECT * FROM con_dependientes WHERE IDempleado = '$IDempleado' AND beneficiario = 1";
		$benficiario = mysql_query($query_benficiario, $vacantes) or die(mysql_error());
		$row_benficiario = mysql_fetch_assoc($benficiario);
		$totalRows_benficiario = mysql_num_rows($benficiario);
		
		if($totalRows_usuario > 0) { 
		$beneficiario_nombre = $row_benficiario['nombre'];
		$beneficiario_direccion = $row_benficiario['direccion'];
		$beneficiario_telefono = $row_benficiario['telefono'];
		$beneficiario_parentesco_prev = $row_benficiario['IDtipo'];
		
 		switch ($beneficiario_parentesco_prev) {
  		case 1: $beneficiario_parentesco = "Esposo(a), Concubino(a)"; break;    
  		case 2: $beneficiario_parentesco = "Padre"; break;    
  		case 3: $beneficiario_parentesco = "Madre"; break;    
  		case 4: $beneficiario_parentesco = "Hijo(a)"; break;    
  		case 5: $beneficiario_parentesco = "Abuelo(a)"; break;    
  		case 6: $beneficiario_parentesco = "Nieto(a)"; break;    
  		case 7: $beneficiario_parentesco = "Hermano(a)"; break;    
  		case 8: $beneficiario_parentesco = "Tio(a)"; break;    
  		case 9: $beneficiario_parentesco = "Sobirno(a)"; break;    
  		case 10: $beneficiario_parentesco = "Suegro(a)"; break;    
  		case 11: $beneficiario_parentesco = "Otro (sin parentezco familiar)"; break;    
		default: $beneficiario_parentesco = "NO DEFINIDO";  }
		
		} else { 
		$beneficiario_nombre = 'NO DEFINIDO';
		$beneficiario_direccion = 'NO DEFINIDO';
		$beneficiario_telefono = 'NO DEFINIDO';
		$beneficiario_parentesco = 'NO DEFINIDO';
		}
		

		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte[0]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $estatus); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte[2]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row_reporte[3]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporte[4]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporte[5]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporte[6]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporte[7]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporte[8]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $sexo); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporte[10]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $nacionalidad); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $civil); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_reporte[13]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_reporte[14]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_reporte[15]); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, date('d/m/Y', strtotime($row_reporte['17']))); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, html_entity_decode($row_reporte[18], ENT_QUOTES, "UTF-8")); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, html_entity_decode($row_reporte[19], ENT_QUOTES, "UTF-8")); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, html_entity_decode($row_reporte[20], ENT_QUOTES, "UTF-8")); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, html_entity_decode($row_reporte[21], ENT_QUOTES, "UTF-8")); 
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, html_entity_decode($row_reporte[22], ENT_QUOTES, "UTF-8")); 
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $row_reporte[23]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $row_reporte[0]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $row_reporte[24]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $row_reporte[25]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $local_foraneo); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $row_reporte[27]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $row_reporte[28]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, $row_reporte[29]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, $tipo_de_contrato); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowCount, $edad); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, $row_reporte[31]); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, $beneficiario_nombre); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, $beneficiario_direccion); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, $beneficiario_telefono); 
		$objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, $beneficiario_parentesco); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, date('d/m/Y', strtotime($row_reporte['16']))); 

// Increment the Excel row counter
        $rowCount++; 
    }

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Empleados '.date('dmY') . '.xls"');
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