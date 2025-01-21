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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
mysql_query("SET NAMES 'utf8'");
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];

$IDpuesto = $_GET['IDpuesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_elusuario = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.IDempleadoJ, Count(prod_activos.IDpuesto) AS Totales, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, vac_areas.area, vac_sucursal.sucursal, vac_matriz.matriz FROM prod_activos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea WHERE prod_activos.IDpuesto = '$IDpuesto'";
$elusuario = mysql_query($query_elusuario, $vacantes) or die(mysql_error());
$row_elusuario = mysql_fetch_assoc($elusuario);

mysql_select_db($database_vacantes, $vacantes);
$query_elpuesto = "SELECT * FROM vac_puestos WHERE IDpuesto = '$IDpuesto'";
$elpuesto = mysql_query($query_elpuesto, $vacantes) or die(mysql_error());
$row_elpuesto = mysql_fetch_assoc($elpuesto);

$jefe = $row_elusuario['IDempleadoJ'];
mysql_select_db($database_vacantes, $vacantes);
$query_jefe = "SELECT prod_activos.IDpuesto, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion FROM
prod_activos WHERE IDempleado = '$jefe'";
$jefe = mysql_query($query_jefe, $vacantes) or die(mysql_error());
$row_jefe = mysql_fetch_assoc($jefe);
$totalRows_jefe = mysql_num_rows($jefe);
$jefe_puesto = $row_jefe['denominacion'];

// activos
$query_puesto_catalogos1 = "SELECT sed_dps.IDcriterio, sed_dps.IDpuesto, sed_dps.b_mision, sed_dps.e_jefe_de_jefe, sed_dps.e_jefe, sed_dps.e_pares, sed_dps.e_colaboradores, sed_dps.f_escolaridad, sed_dps.f_avance, sed_dps.f_carreras, sed_dps.f_idioma, sed_dps.f_idioma_nivel, sed_dps.f_otros_estudios, sed_dps.f_conocimientos1, sed_dps.f_conocimientos2, sed_dps.f_conocimientos3, sed_dps.f_conocimientos4, sed_dps.f_conocimientos5, sed_dps.f_conocimientos6, sed_dps.f_exp_areas, sed_dps.f_exp_anios, sed_dps.f_viajar, sed_dps.f_frecuencia, sed_dps.f_edad, sed_dps.f_turnos, sed_dps.IDplaza, sed_dps.captura_a, sed_dps.captura_b, sed_estudios.estudios FROM sed_dps INNER JOIN sed_estudios ON sed_estudios.IDestudios = sed_dps.f_escolaridad WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_catalogos1 = mysql_query($query_puesto_catalogos1, $vacantes) or die(mysql_error());
$row_puesto_catalogos1 = mysql_fetch_assoc($puesto_catalogos1);
$totalRows_puesto_catalogos1 = mysql_num_rows($puesto_catalogos1);


// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("dps/plantilla_acuse.xlsx");

// Add some data
$objPHPExcel->setActiveSheetIndex(0);

$combo1 = $row_elpuesto['IDpuesto'] . ' ' . $row_elpuesto['denominacion'];

if ($row_elpuesto['IDdp_tipo'] == 1) {
$matriz = "PUESTO TIPO";
} else {
$matriz = $row_elusuario['matriz'];
}

$sucursal = $row_elusuario['sucursal'];
$area = $row_elusuario['area'];
$mision = $row_puesto_catalogos1['b_mision'];

//Escribimos
$objPHPExcel->getActiveSheet()->setCellValue('B2', $combo1);
$objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
$objPHPExcel->getActiveSheet()->setCellValue('B3', $area);
$objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
$objPHPExcel->getActiveSheet()->setCellValue('B4', $matriz);
$objPHPExcel->getActiveSheet()->mergeCells('B4:I4');
$objPHPExcel->getActiveSheet()->setCellValue('A7', $mision);
$objPHPExcel->getActiveSheet()->mergeCells('A7:I7');


mysql_select_db($database_vacantes, $vacantes);
$query_funciones = "SELECT CONCAT(sed_dps_catalogos.criterio_a, ' ', sed_dps_catalogos.criterio_b, ' ', sed_dps_catalogos.criterio_c) AS funcion FROM sed_dps_catalogos INNER JOIN vac_puestos ON vac_puestos.IDpuesto = sed_dps_catalogos.IDpuesto WHERE sed_dps_catalogos.criterio = 'c' AND vac_puestos.IDpuesto = '$IDpuesto' ORDER BY sed_dps_catalogos.IDpuesto ASC";
mysql_query("SET NAMES 'utf8'");
$funciones = mysql_query($query_funciones, $vacantes) or die(mysql_error());
$row_funciones = mysql_fetch_assoc($funciones);
$totalRows_funciones = mysql_num_rows($funciones);

$objPHPExcel->getActiveSheet()->setCellValue('B10', $row_funciones['funcion']);

$row = 11;
while($row_funciones = mysql_fetch_assoc($funciones)) {
    $col = 1;
	$key = 0;
    foreach($row_funciones as $key=>$value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('B10:I10');
$objPHPExcel->getActiveSheet()->mergeCells('B11:I11');
$objPHPExcel->getActiveSheet()->mergeCells('B12:I12');
$objPHPExcel->getActiveSheet()->mergeCells('B13:I13');
$objPHPExcel->getActiveSheet()->mergeCells('B14:I14');
$objPHPExcel->getActiveSheet()->mergeCells('B15:I15');
$objPHPExcel->getActiveSheet()->mergeCells('B16:I16');
$objPHPExcel->getActiveSheet()->mergeCells('B17:I17');
$objPHPExcel->getActiveSheet()->mergeCells('B18:I18');
$objPHPExcel->getActiveSheet()->mergeCells('B19:I19');
$objPHPExcel->getActiveSheet()->mergeCells('B20:I20');
$objPHPExcel->getActiveSheet()->mergeCells('B21:I21');
$objPHPExcel->getActiveSheet()->mergeCells('B22:I22');
$objPHPExcel->getActiveSheet()->mergeCells('B23:I23');
$objPHPExcel->getActiveSheet()->mergeCells('B24:I24');
$objPHPExcel->getActiveSheet()->mergeCells('B25:I25');
$objPHPExcel->getActiveSheet()->mergeCells('B26:I26');
$objPHPExcel->getActiveSheet()->mergeCells('B27:I27');
$objPHPExcel->getActiveSheet()->mergeCells('B28:I28');
$objPHPExcel->getActiveSheet()->mergeCells('B29:I29');
$objPHPExcel->getActiveSheet()->mergeCells('B30:I30');
$objPHPExcel->getActiveSheet()->mergeCells('B31:I31');
$objPHPExcel->getActiveSheet()->mergeCells('B32:I32');
$objPHPExcel->getActiveSheet()->mergeCells('B33:I33');
$objPHPExcel->getActiveSheet()->mergeCells('B34:I34');

//FUCIONES  
$funcion_valida11 = $objPHPExcel->getActiveSheet()->getCell('B11')->getCalculatedValue(); 
if ($funcion_valida11 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(11)->setVisible(false); } 
$funcion_valida12 = $objPHPExcel->getActiveSheet()->getCell('B12')->getCalculatedValue(); 
if ($funcion_valida12 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(12)->setVisible(false); } 
$funcion_valida13 = $objPHPExcel->getActiveSheet()->getCell('B13')->getCalculatedValue(); 
if ($funcion_valida13 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(13)->setVisible(false); } 
$funcion_valida14 = $objPHPExcel->getActiveSheet()->getCell('B14')->getCalculatedValue(); 
if ($funcion_valida14 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(14)->setVisible(false); } 
$funcion_valida15 = $objPHPExcel->getActiveSheet()->getCell('B15')->getCalculatedValue(); 
if ($funcion_valida15 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(15)->setVisible(false); } 
$funcion_valida16 = $objPHPExcel->getActiveSheet()->getCell('B16')->getCalculatedValue(); 
if ($funcion_valida16 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(16)->setVisible(false); } 
$funcion_valida17 = $objPHPExcel->getActiveSheet()->getCell('B17')->getCalculatedValue(); 
if ($funcion_valida17 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(17)->setVisible(false); } 
$funcion_valida18 = $objPHPExcel->getActiveSheet()->getCell('B18')->getCalculatedValue(); 
if ($funcion_valida18 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(18)->setVisible(false); } 
$funcion_valida19 = $objPHPExcel->getActiveSheet()->getCell('B19')->getCalculatedValue(); 
if ($funcion_valida19 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(19)->setVisible(false); } 
$funcion_valida20 = $objPHPExcel->getActiveSheet()->getCell('B20')->getCalculatedValue(); 
if ($funcion_valida20 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(20)->setVisible(false); } 
$funcion_valida21 = $objPHPExcel->getActiveSheet()->getCell('B21')->getCalculatedValue(); 
if ($funcion_valida21 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(21)->setVisible(false); } 
$funcion_valida22 = $objPHPExcel->getActiveSheet()->getCell('B22')->getCalculatedValue(); 
if ($funcion_valida22 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(22)->setVisible(false); } 
$funcion_valida23 = $objPHPExcel->getActiveSheet()->getCell('B23')->getCalculatedValue(); 
if ($funcion_valida23 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(23)->setVisible(false); } 
$funcion_valida24 = $objPHPExcel->getActiveSheet()->getCell('B24')->getCalculatedValue(); 
if ($funcion_valida24 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(24)->setVisible(false); } 
$funcion_valida25 = $objPHPExcel->getActiveSheet()->getCell('B25')->getCalculatedValue(); 
if ($funcion_valida25 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(25)->setVisible(false); } 
$funcion_valida26 = $objPHPExcel->getActiveSheet()->getCell('B26')->getCalculatedValue(); 
if ($funcion_valida26 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(26)->setVisible(false); } 
$funcion_valida27 = $objPHPExcel->getActiveSheet()->getCell('B27')->getCalculatedValue(); 
if ($funcion_valida27 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(27)->setVisible(false); } 
$funcion_valida28 = $objPHPExcel->getActiveSheet()->getCell('B28')->getCalculatedValue(); 
if ($funcion_valida28 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(28)->setVisible(false); } 
$funcion_valida29 = $objPHPExcel->getActiveSheet()->getCell('B29')->getCalculatedValue(); 
if ($funcion_valida29 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(29)->setVisible(false); } 
$funcion_valida30 = $objPHPExcel->getActiveSheet()->getCell('B30')->getCalculatedValue(); 
if ($funcion_valida30 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(30)->setVisible(false); } 
$funcion_valida31 = $objPHPExcel->getActiveSheet()->getCell('B31')->getCalculatedValue(); 
if ($funcion_valida31 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(31)->setVisible(false); } 
$funcion_valida32 = $objPHPExcel->getActiveSheet()->getCell('B32')->getCalculatedValue(); 
if ($funcion_valida32 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(32)->setVisible(false); } 
$funcion_valida33 = $objPHPExcel->getActiveSheet()->getCell('B33')->getCalculatedValue(); 
if ($funcion_valida33 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(33)->setVisible(false); } 
$funcion_valida34 = $objPHPExcel->getActiveSheet()->getCell('B34')->getCalculatedValue(); 
if ($funcion_valida34 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(34)->setVisible(false); } 

mysql_select_db($database_vacantes, $vacantes);
$query_relaciones_inta = "SELECT sed_dps_catalogos.criterio_b FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'd' AND sed_dps_catalogos.criterio_a = '1' AND IDpuesto = '$IDpuesto' ORDER BY sed_dps_catalogos.IDpuesto ASC ";
mysql_query("SET NAMES 'utf8'");
$relaciones_inta = mysql_query($query_relaciones_inta, $vacantes) or die(mysql_error());
$row_relaciones_inta = mysql_fetch_assoc($relaciones_inta);
$totalRows_relaciones_inta = mysql_num_rows($relaciones_inta);

$objPHPExcel->getActiveSheet()->setCellValue('B38', $row_relaciones_inta['criterio_b']);

$row = 39;
while($row_relaciones_inta = mysql_fetch_assoc($relaciones_inta)) {
    $col = 1;
    foreach($row_relaciones_inta as $key=>$value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

mysql_select_db($database_vacantes, $vacantes);
$query_relaciones_intb = "SELECT sed_dps_catalogos.criterio_c FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'd' AND sed_dps_catalogos.criterio_a = '1' AND IDpuesto = '$IDpuesto' ORDER BY sed_dps_catalogos.IDpuesto ASC ";
mysql_query("SET NAMES 'utf8'");
$relaciones_intb = mysql_query($query_relaciones_intb, $vacantes) or die(mysql_error());
$row_relaciones_intb = mysql_fetch_assoc($relaciones_intb);
$totalRows_relaciones_intb = mysql_num_rows($relaciones_intb);

$objPHPExcel->getActiveSheet()->setCellValue('E38', $row_relaciones_intb['criterio_c']);

$row = 39;
while($row_relaciones_intb = mysql_fetch_assoc($relaciones_intb)) {
    $col = 4;
    foreach($row_relaciones_intb as $key=>$value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('B38:D38');
$objPHPExcel->getActiveSheet()->mergeCells('B39:D39');
$objPHPExcel->getActiveSheet()->mergeCells('B40:D40');
$objPHPExcel->getActiveSheet()->mergeCells('B41:D41');
$objPHPExcel->getActiveSheet()->mergeCells('B42:D42');
$objPHPExcel->getActiveSheet()->mergeCells('B43:D43');
$objPHPExcel->getActiveSheet()->mergeCells('B44:D44');
$objPHPExcel->getActiveSheet()->mergeCells('B45:D45');
$objPHPExcel->getActiveSheet()->mergeCells('B46:D46');
$objPHPExcel->getActiveSheet()->mergeCells('B47:D47');
$objPHPExcel->getActiveSheet()->mergeCells('B48:D48');
$objPHPExcel->getActiveSheet()->mergeCells('B49:D49');
$objPHPExcel->getActiveSheet()->mergeCells('B50:D50');
$objPHPExcel->getActiveSheet()->mergeCells('B51:D51');
$objPHPExcel->getActiveSheet()->mergeCells('B52:D52');
$objPHPExcel->getActiveSheet()->mergeCells('B53:D53');
$objPHPExcel->getActiveSheet()->mergeCells('B54:D54');
$objPHPExcel->getActiveSheet()->mergeCells('B55:D55');
$objPHPExcel->getActiveSheet()->mergeCells('B56:D56');
$objPHPExcel->getActiveSheet()->mergeCells('B57:D57');
$objPHPExcel->getActiveSheet()->mergeCells('B58:D58');
$objPHPExcel->getActiveSheet()->mergeCells('B59:D59');
$objPHPExcel->getActiveSheet()->mergeCells('B60:D60');
$objPHPExcel->getActiveSheet()->mergeCells('B61:D61');

$objPHPExcel->getActiveSheet()->mergeCells('E38:I38');
$objPHPExcel->getActiveSheet()->mergeCells('E39:I39');
$objPHPExcel->getActiveSheet()->mergeCells('E40:I40');
$objPHPExcel->getActiveSheet()->mergeCells('E41:I41');
$objPHPExcel->getActiveSheet()->mergeCells('E42:I42');
$objPHPExcel->getActiveSheet()->mergeCells('E43:I43');
$objPHPExcel->getActiveSheet()->mergeCells('E44:I44');
$objPHPExcel->getActiveSheet()->mergeCells('E45:I45');
$objPHPExcel->getActiveSheet()->mergeCells('E46:I46');
$objPHPExcel->getActiveSheet()->mergeCells('E47:I47');
$objPHPExcel->getActiveSheet()->mergeCells('E48:I48');
$objPHPExcel->getActiveSheet()->mergeCells('E49:I49');
$objPHPExcel->getActiveSheet()->mergeCells('E50:I50');
$objPHPExcel->getActiveSheet()->mergeCells('E51:I51');
$objPHPExcel->getActiveSheet()->mergeCells('E52:I52');
$objPHPExcel->getActiveSheet()->mergeCells('E53:I53');
$objPHPExcel->getActiveSheet()->mergeCells('E54:I54');
$objPHPExcel->getActiveSheet()->mergeCells('E55:I55');
$objPHPExcel->getActiveSheet()->mergeCells('E56:I56');
$objPHPExcel->getActiveSheet()->mergeCells('E57:I57');
$objPHPExcel->getActiveSheet()->mergeCells('E58:I58');
$objPHPExcel->getActiveSheet()->mergeCells('E59:I59');
$objPHPExcel->getActiveSheet()->mergeCells('E60:I60');
$objPHPExcel->getActiveSheet()->mergeCells('E61:I61');

$funcion_valida39 = $objPHPExcel->getActiveSheet()->getCell('B39')->getCalculatedValue(); 
if ($funcion_valida39 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(39)->setVisible(false); } 
$funcion_valida40 = $objPHPExcel->getActiveSheet()->getCell('B40')->getCalculatedValue(); 
if ($funcion_valida40 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(40)->setVisible(false); } 
$funcion_valida41 = $objPHPExcel->getActiveSheet()->getCell('B41')->getCalculatedValue(); 
if ($funcion_valida41 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(41)->setVisible(false); } 
$funcion_valida42 = $objPHPExcel->getActiveSheet()->getCell('B42')->getCalculatedValue(); 
if ($funcion_valida42 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(42)->setVisible(false); } 
$funcion_valida43 = $objPHPExcel->getActiveSheet()->getCell('B43')->getCalculatedValue(); 
if ($funcion_valida43 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(43)->setVisible(false); } 
$funcion_valida44 = $objPHPExcel->getActiveSheet()->getCell('B44')->getCalculatedValue(); 
if ($funcion_valida44 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(44)->setVisible(false); } 
$funcion_valida45 = $objPHPExcel->getActiveSheet()->getCell('B45')->getCalculatedValue(); 
if ($funcion_valida45 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(45)->setVisible(false); } 
$funcion_valida46 = $objPHPExcel->getActiveSheet()->getCell('B46')->getCalculatedValue(); 
if ($funcion_valida46 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(46)->setVisible(false); } 
$funcion_valida47 = $objPHPExcel->getActiveSheet()->getCell('B47')->getCalculatedValue(); 
if ($funcion_valida47 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(47)->setVisible(false); } 
$funcion_valida48 = $objPHPExcel->getActiveSheet()->getCell('B48')->getCalculatedValue(); 
if ($funcion_valida48 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(48)->setVisible(false); } 
$funcion_valida49 = $objPHPExcel->getActiveSheet()->getCell('B49')->getCalculatedValue(); 
if ($funcion_valida49 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(49)->setVisible(false); } 
$funcion_valida50 = $objPHPExcel->getActiveSheet()->getCell('B50')->getCalculatedValue(); 
if ($funcion_valida50 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(50)->setVisible(false); } 
$funcion_valida51 = $objPHPExcel->getActiveSheet()->getCell('B51')->getCalculatedValue(); 
if ($funcion_valida51 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(51)->setVisible(false); } 
$funcion_valida52 = $objPHPExcel->getActiveSheet()->getCell('B52')->getCalculatedValue(); 
if ($funcion_valida52 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(52)->setVisible(false); } 
$funcion_valida53 = $objPHPExcel->getActiveSheet()->getCell('B53')->getCalculatedValue(); 
if ($funcion_valida53 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(53)->setVisible(false); } 
$funcion_valida54 = $objPHPExcel->getActiveSheet()->getCell('B54')->getCalculatedValue(); 
if ($funcion_valida54 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(54)->setVisible(false); } 
$funcion_valida55 = $objPHPExcel->getActiveSheet()->getCell('B55')->getCalculatedValue(); 
if ($funcion_valida55 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(55)->setVisible(false); } 
$funcion_valida56 = $objPHPExcel->getActiveSheet()->getCell('B56')->getCalculatedValue(); 
if ($funcion_valida56 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(56)->setVisible(false); } 
$funcion_valida57 = $objPHPExcel->getActiveSheet()->getCell('B57')->getCalculatedValue(); 
if ($funcion_valida57 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(57)->setVisible(false); } 
$funcion_valida58 = $objPHPExcel->getActiveSheet()->getCell('B58')->getCalculatedValue(); 
if ($funcion_valida58 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(58)->setVisible(false); } 
$funcion_valida59 = $objPHPExcel->getActiveSheet()->getCell('B59')->getCalculatedValue(); 
if ($funcion_valida59 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(59)->setVisible(false); } 
$funcion_valida60 = $objPHPExcel->getActiveSheet()->getCell('B60')->getCalculatedValue(); 
if ($funcion_valida60 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(60)->setVisible(false); } 
$funcion_valida61 = $objPHPExcel->getActiveSheet()->getCell('B61')->getCalculatedValue(); 
if ($funcion_valida61 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(61)->setVisible(false); } 

mysql_select_db($database_vacantes, $vacantes);
$query_relaciones_exta = "SELECT sed_dps_catalogos.criterio_b FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'd' AND sed_dps_catalogos.criterio_a = '2' AND IDpuesto = '$IDpuesto' ORDER BY sed_dps_catalogos.IDpuesto ASC ";
mysql_query("SET NAMES 'utf8'");
$relaciones_exta = mysql_query($query_relaciones_exta, $vacantes) or die(mysql_error());
$row_relaciones_exta = mysql_fetch_assoc($relaciones_exta);
$totalRows_relaciones_exta = mysql_num_rows($relaciones_exta);

$objPHPExcel->getActiveSheet()->setCellValue('B63', $row_relaciones_exta['criterio_b']);

$row = 64;
while($row_relaciones_exta = mysql_fetch_assoc($relaciones_exta)) {
    $col = 1;
    foreach($row_relaciones_exta as $key=>$value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

mysql_select_db($database_vacantes, $vacantes);
$query_relaciones_extb = "SELECT sed_dps_catalogos.criterio_c FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'd' AND sed_dps_catalogos.criterio_a = '2' AND IDpuesto = '$IDpuesto' ORDER BY sed_dps_catalogos.IDpuesto ASC ";
mysql_query("SET NAMES 'utf8'");
$relaciones_extb = mysql_query($query_relaciones_extb, $vacantes) or die(mysql_error());
$row_relaciones_extb = mysql_fetch_assoc($relaciones_extb);
$totalRows_relaciones_extb = mysql_num_rows($relaciones_extb);

$objPHPExcel->getActiveSheet()->setCellValue('E63', $row_relaciones_extb['criterio_c']);

$row = 64;
while($row_relaciones_extb = mysql_fetch_assoc($relaciones_extb)) {
    foreach($row_relaciones_extb as $key=>$value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('B63:D63');
$objPHPExcel->getActiveSheet()->mergeCells('B64:D64');
$objPHPExcel->getActiveSheet()->mergeCells('B65:D65');
$objPHPExcel->getActiveSheet()->mergeCells('B66:D66');
$objPHPExcel->getActiveSheet()->mergeCells('B67:D67');
$objPHPExcel->getActiveSheet()->mergeCells('B68:D68');

$objPHPExcel->getActiveSheet()->mergeCells('E63:I63');
$objPHPExcel->getActiveSheet()->mergeCells('E64:I64');
$objPHPExcel->getActiveSheet()->mergeCells('E65:I65');
$objPHPExcel->getActiveSheet()->mergeCells('E66:I66');
$objPHPExcel->getActiveSheet()->mergeCells('E67:I67');
$objPHPExcel->getActiveSheet()->mergeCells('E68:I68');

$funcion_valida64 = $objPHPExcel->getActiveSheet()->getCell('B64')->getCalculatedValue(); 
if ($funcion_valida64 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(64)->setVisible(false); } 
$funcion_valida65 = $objPHPExcel->getActiveSheet()->getCell('B65')->getCalculatedValue(); 
if ($funcion_valida65 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(65)->setVisible(false); } 
$funcion_valida66 = $objPHPExcel->getActiveSheet()->getCell('B66')->getCalculatedValue(); 
if ($funcion_valida66 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(66)->setVisible(false); } 
$funcion_valida67 = $objPHPExcel->getActiveSheet()->getCell('B67')->getCalculatedValue(); 
if ($funcion_valida67 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(67)->setVisible(false); } 
$funcion_valida68 = $objPHPExcel->getActiveSheet()->getCell('B68')->getCalculatedValue(); 
if ($funcion_valida68 == NULL) {$objPHPExcel->getActiveSheet()->getRowDimension(68)->setVisible(false); } 


//Escribimos
$objPHPExcel->getActiveSheet()->setCellValue('D72',$row_puesto_catalogos1['e_jefe_de_jefe']);
$objPHPExcel->getActiveSheet()->mergeCells('D72:I72');
$objPHPExcel->getActiveSheet()->setCellValue('D73',$row_puesto_catalogos1['e_jefe']);
$objPHPExcel->getActiveSheet()->mergeCells('D73:I73');
$objPHPExcel->getActiveSheet()->setCellValue('D74',$row_puesto_catalogos1['e_pares']);
$objPHPExcel->getActiveSheet()->mergeCells('D74:I74');
$objPHPExcel->getActiveSheet()->setCellValue('D75',$row_puesto_catalogos1['e_colaboradores']);
$objPHPExcel->getActiveSheet()->mergeCells('D75:I75');
$objPHPExcel->getActiveSheet()->setCellValue('C79',$row_puesto_catalogos1['estudios']);
$objPHPExcel->getActiveSheet()->mergeCells('C79:E79');
 if ($row_puesto_catalogos1['f_avance'] == 1){ $avance = 'Terminado o Pasante';}  else { $avance = 'Titulado';}
$objPHPExcel->getActiveSheet()->setCellValue('H79', $avance);
$objPHPExcel->getActiveSheet()->mergeCells('H79:I79');
$objPHPExcel->getActiveSheet()->setCellValue('C80',$row_puesto_catalogos1['f_carreras']);
$objPHPExcel->getActiveSheet()->mergeCells('C80:I80');
$objPHPExcel->getActiveSheet()->setCellValue('C81',$row_puesto_catalogos1['f_idioma']);
$objPHPExcel->getActiveSheet()->mergeCells('C81:E81');
$objPHPExcel->getActiveSheet()->setCellValue('H81',$row_puesto_catalogos1['f_idioma_nivel']);
$objPHPExcel->getActiveSheet()->mergeCells('H81:I81');
$objPHPExcel->getActiveSheet()->setCellValue('C82',$row_puesto_catalogos1['f_otros_estudios']);
$objPHPExcel->getActiveSheet()->mergeCells('C82:I82');
$conoc1 =$row_puesto_catalogos1['f_conocimientos1'];
$conoc2 =$row_puesto_catalogos1['f_conocimientos2'];
$conoc3 =$row_puesto_catalogos1['f_conocimientos3'];
$conoc4 =$row_puesto_catalogos1['f_conocimientos4'];
$conoc5 =$row_puesto_catalogos1['f_conocimientos5'];
if ($conoc1 == ''){ $conoc1 = 'NO APLICA';} else { $conoc1 =$row_puesto_catalogos1['f_conocimientos1'];}
if ($conoc2 == ''){ $conoc2 = 'NO APLICA';} else { $conoc2 =$row_puesto_catalogos1['f_conocimientos2'];}
if ($conoc3 == ''){ $conoc3 = 'NO APLICA';} else { $conoc3 =$row_puesto_catalogos1['f_conocimientos3'];}
if ($conoc4 == ''){ $conoc4 = 'NO APLICA';} else { $conoc4 =$row_puesto_catalogos1['f_conocimientos4'];}
if ($conoc5 == ''){ $conoc5 = 'NO APLICA';} else { $conoc5 =$row_puesto_catalogos1['f_conocimientos5'];}
$objPHPExcel->getActiveSheet()->setCellValue('C83', $conoc1);
$objPHPExcel->getActiveSheet()->mergeCells('C83:I83');
$objPHPExcel->getActiveSheet()->setCellValue('C84', $conoc2);
$objPHPExcel->getActiveSheet()->mergeCells('C84:I84');
$objPHPExcel->getActiveSheet()->setCellValue('C85', $conoc3);
$objPHPExcel->getActiveSheet()->mergeCells('C85:I85');
$objPHPExcel->getActiveSheet()->setCellValue('D87',$row_puesto_catalogos1['f_exp_anios']);
$objPHPExcel->getActiveSheet()->mergeCells('D87:E87');
$objPHPExcel->getActiveSheet()->setCellValue('A88',$row_puesto_catalogos1['f_exp_areas']);
$objPHPExcel->getActiveSheet()->mergeCells('A88:I88');
if ($row_puesto_catalogos1['f_viajar'] == 0){ $f_viajar = 'NO';}  else { $f_viajar = 'SI';}
$objPHPExcel->getActiveSheet()->setCellValue('D90', $f_viajar);
$objPHPExcel->getActiveSheet()->mergeCells('D90:E90');
if      ($row_puesto_catalogos1['f_frecuencia'] == 2){ $f_frecuencia = 'SIEMPRE'; } 
else if ($row_puesto_catalogos1['f_frecuencia'] == 1) { $f_frecuencia = 'A VECES'; } else { $f_frecuencia = 'NUNCA'; } 
$objPHPExcel->getActiveSheet()->setCellValue('H90', $f_frecuencia);
$objPHPExcel->getActiveSheet()->mergeCells('H90:I90');
$objPHPExcel->getActiveSheet()->setCellValue('D91',$row_puesto_catalogos1['f_edad']);
$objPHPExcel->getActiveSheet()->mergeCells('D91:E91');
if ($row_puesto_catalogos1['f_turnos'] == 0){ $f_turnos = 'NO'; }  else { $f_turnos = 'SI'; } 
$objPHPExcel->getActiveSheet()->setCellValue('H91', $f_turnos);
$objPHPExcel->getActiveSheet()->mergeCells('H91:I91');

$objPHPExcel->getActiveSheet()->mergeCells('A130:D130');
$objPHPExcel->getActiveSheet()->mergeCells('A134:D134');


//Competencias Tipo
mysql_select_db($database_vacantes, $vacantes);
$query_competencias_tipo = "SELECT sed_dps_catalogos.criterio_a,  sed_dps_catalogos.criterio_b,  sed_dps_catalogos.criterio_c FROM sed_dps_catalogos WHERE IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio = 'g' LIMIT 10";
$competencias_tipo = mysql_query($query_competencias_tipo, $vacantes) or die(mysql_error());
$row_competencias_tipo = mysql_fetch_assoc($competencias_tipo);
$totalRows_competencias_tipo = mysql_num_rows($competencias_tipo);

//primer valor
$value_z = $row_competencias_tipo['criterio_a'];
if ($value_z == 1){ $valuez_ = "Reclutamiento y Seleccion";} else if ($value_z == 2){ $valuez_ = "Capacitacion y Desarrollo";} else { $valuez_ = "N/A";} 

$objPHPExcel->getActiveSheet()->setCellValue('A102', $valuez_);

$row = 103;
while($row_competencias_tipo = mysql_fetch_assoc($competencias_tipo)) {
    $col = 0;
	$key = 0;
    foreach($row_competencias_tipo as $key=>$valuez) {
	if ($valuez == 1){ $valuez__ = "Reclutamiento y Seleccion";} else if ($valuez == 2){ $valuez__ = "Capacitacion y Desarrollo";} else { $valuez__ = "N/A";} 
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valuez__);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('A102:B102');
$objPHPExcel->getActiveSheet()->mergeCells('A103:B103');
$objPHPExcel->getActiveSheet()->mergeCells('A104:B104');
$objPHPExcel->getActiveSheet()->mergeCells('A105:B105');
$objPHPExcel->getActiveSheet()->mergeCells('A106:B106');
$objPHPExcel->getActiveSheet()->mergeCells('A107:B107');
$objPHPExcel->getActiveSheet()->mergeCells('A108:B108');
$objPHPExcel->getActiveSheet()->mergeCells('A109:B109');
$objPHPExcel->getActiveSheet()->mergeCells('A110:B110');
$objPHPExcel->getActiveSheet()->mergeCells('A111:B111');



//Competencias Nombre
mysql_select_db($database_vacantes, $vacantes);
$query_competencias_nombre = "SELECT sed_competencias_nombre.competencia FROM sed_dps_catalogos LEFT JOIN sed_competencias_nombre ON sed_competencias_nombre.IDcompp = sed_dps_catalogos.criterio_b WHERE sed_dps_catalogos.IDpuesto = '$IDpuesto' AND criterio = 'g' LIMIT 10" ;
$competencias_nombre = mysql_query($query_competencias_nombre, $vacantes) or die(mysql_error());
$row_competencias_nombre = mysql_fetch_assoc($competencias_nombre);
$totalRows_competencias_nombre = mysql_num_rows($competencias_nombre);


$objPHPExcel->getActiveSheet()->setCellValue('C102', $row_competencias_nombre['competencia']);

$row = 103;
while($row_competencias_nombre = mysql_fetch_assoc($competencias_nombre)) {
    $col = 2;
	$key = 0;
    foreach($row_competencias_nombre as $key=>$value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('C102:G102');
$objPHPExcel->getActiveSheet()->mergeCells('C103:G103');
$objPHPExcel->getActiveSheet()->mergeCells('C104:G104');
$objPHPExcel->getActiveSheet()->mergeCells('C105:G105');
$objPHPExcel->getActiveSheet()->mergeCells('C106:G106');
$objPHPExcel->getActiveSheet()->mergeCells('C107:G107');
$objPHPExcel->getActiveSheet()->mergeCells('C108:G108');
$objPHPExcel->getActiveSheet()->mergeCells('C109:G109');
$objPHPExcel->getActiveSheet()->mergeCells('C110:G110');
$objPHPExcel->getActiveSheet()->mergeCells('C111:G111');

//Competencias Grado
mysql_select_db($database_vacantes, $vacantes);
$query_competencias_grado = "SELECT criterio_c FROM sed_dps_catalogos WHERE sed_dps_catalogos.IDpuesto = '$IDpuesto' AND criterio = 'g' LIMIT 10";
$competencias_grado = mysql_query($query_competencias_grado, $vacantes) or die(mysql_error());
$row_competencias_grado = mysql_fetch_assoc($competencias_grado);
$totalRows_competencias_grado = mysql_num_rows($competencias_grado);

//primer valor
$value_x = $row_competencias_grado['criterio_c'];
if ($value_x == 1){ $valuex_ = "Basico";} else if ($value_x == 2){ $valuex_ = "Intermedio";} else if ($value_x == 3){ $valuex_ = "Avanzado";} else if ($value_x == 4){ $valuex_ = "Experto";} else { $valuex_ = "N/A";} 

//proximos
$objPHPExcel->getActiveSheet()->setCellValue('H102', $valuex_);

$row = 103;
while($row_competencias_grado = mysql_fetch_assoc($competencias_grado)) {
    $col = 7;
	$key = 0;
    foreach($row_competencias_grado as $key=>$valuel) {
		if ($valuel == 1){ $value_l = "Basico";} else if ($valuel == 2){ $value_l = "Intermedio";} else if ($valuel == 3){ $value_l = "Avanzado";} else if ($valuel == 4){ $value_l = "Experto";} else { $value_l = "N/A";} 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value_l);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('H102:I102');
$objPHPExcel->getActiveSheet()->mergeCells('H103:I103');
$objPHPExcel->getActiveSheet()->mergeCells('H104:I104');
$objPHPExcel->getActiveSheet()->mergeCells('H105:I105');
$objPHPExcel->getActiveSheet()->mergeCells('H106:I106');
$objPHPExcel->getActiveSheet()->mergeCells('H107:I107');
$objPHPExcel->getActiveSheet()->mergeCells('H108:I108');
$objPHPExcel->getActiveSheet()->mergeCells('H109:I109');
$objPHPExcel->getActiveSheet()->mergeCells('H110:I110');
$objPHPExcel->getActiveSheet()->mergeCells('H111:I111');


//Cursos
mysql_select_db($database_vacantes, $vacantes);
$query_cursos = "SELECT criterio_b FROM sed_dps_catalogos WHERE sed_dps_catalogos.IDpuesto = '$IDpuesto' AND criterio = 'h' LIMIT 5";
$cursos = mysql_query($query_cursos, $vacantes) or die(mysql_error());
$row_cursos = mysql_fetch_assoc($cursos);
$totalRows_cursos = mysql_num_rows($cursos);

//primer valor
$value_c = $row_cursos['criterio_b'];
$objPHPExcel->getActiveSheet()->setCellValue('B94', $value_c);

$row = 95;
while($row_cursos = mysql_fetch_assoc($cursos)) {
    $col = 1;
	$key = 0;
    foreach($row_cursos as $key=>$valuec) {
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $valuec);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('B94:I94');
$objPHPExcel->getActiveSheet()->mergeCells('B95:I95');
$objPHPExcel->getActiveSheet()->mergeCells('B96:I96');
$objPHPExcel->getActiveSheet()->mergeCells('B97:I97');
$objPHPExcel->getActiveSheet()->mergeCells('B98:I98');


///////////////////////INDICADORES///////////////////////
mysql_select_db($database_vacantes, $vacantes);
$query_indicador_a = "SELECT criterio_a FROM sed_dps_catalogos WHERE sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio = 'm' LIMIT 5";
$competencias_indicador_a = mysql_query($query_indicador_a, $vacantes) or die(mysql_error());
$row_competencias_indicador_a = mysql_fetch_assoc($competencias_indicador_a);
$totalRows_competencias_indicador_a = mysql_num_rows($competencias_indicador_a);

$objPHPExcel->getActiveSheet()->setCellValue('A115', $row_competencias_indicador_a['criterio_a']);

$row = 116;
while($row_competencias_indicador_a = mysql_fetch_assoc($competencias_indicador_a)) {
    $col = 0;
	$key = 0;
    foreach($row_competencias_indicador_a as $key=>$value_a) {
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value_a);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('A115:D115');
$objPHPExcel->getActiveSheet()->mergeCells('A116:D116');
$objPHPExcel->getActiveSheet()->mergeCells('A117:D117');
$objPHPExcel->getActiveSheet()->mergeCells('A118:D118');
$objPHPExcel->getActiveSheet()->mergeCells('A119:D119');


mysql_select_db($database_vacantes, $vacantes);
$query_indicador_b = "SELECT criterio_b FROM sed_dps_catalogos WHERE sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio = 'm' LIMIT 5";
$competencias_indicador_b = mysql_query($query_indicador_b, $vacantes) or die(mysql_error());
$row_competencias_indicador_b = mysql_fetch_assoc($competencias_indicador_b);
$totalRows_competencias_indicador_b = mysql_num_rows($competencias_indicador_b);

$objPHPExcel->getActiveSheet()->setCellValue('E115', $row_competencias_indicador_b['criterio_b']);

$row = 116;
while($row_competencias_indicador_b = mysql_fetch_assoc($competencias_indicador_b)) {
    $col = 4;
	$key = 0;
    foreach($row_competencias_indicador_b as $key=>$value_a) {
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value_a);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('E115:G115');
$objPHPExcel->getActiveSheet()->mergeCells('E116:G116');
$objPHPExcel->getActiveSheet()->mergeCells('E117:G117');
$objPHPExcel->getActiveSheet()->mergeCells('E118:G118');
$objPHPExcel->getActiveSheet()->mergeCells('E119:G119');


mysql_select_db($database_vacantes, $vacantes);
$query_indicador_c = "SELECT criterio_c FROM sed_dps_catalogos WHERE sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio = 'm' LIMIT 5";
$competencias_indicador_c = mysql_query($query_indicador_c, $vacantes) or die(mysql_error());
$row_competencias_indicador_c = mysql_fetch_assoc($competencias_indicador_c);
$totalRows_competencias_indicador_c = mysql_num_rows($competencias_indicador_c);

$objPHPExcel->getActiveSheet()->setCellValue('H115', $row_competencias_indicador_c['criterio_c']);

$row = 116;
while($row_competencias_indicador_c = mysql_fetch_assoc($competencias_indicador_c)) {
    $col = 7;
	$key = 0;
    foreach($row_competencias_indicador_c as $key=>$value_a) {
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value_a);
        $col++;
    }
    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('H115:I115');
$objPHPExcel->getActiveSheet()->mergeCells('H116:I116');
$objPHPExcel->getActiveSheet()->mergeCells('H117:I117');
$objPHPExcel->getActiveSheet()->mergeCells('H118:I118');
$objPHPExcel->getActiveSheet()->mergeCells('H119:I119');


////ACTIVOS FIJOS/////
$query_activos_catalogos1 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'z' AND sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio_a = 1";
$activos_catalogos1 = mysql_query($query_activos_catalogos1, $vacantes) or die(mysql_error());
$row_activos_catalogos1 = mysql_fetch_assoc($activos_catalogos1);
$totalRows_activos_catalogos1 = mysql_num_rows($activos_catalogos1);

$query_activos_catalogos2 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'z' AND sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio_a = 2";
$activos_catalogos2 = mysql_query($query_activos_catalogos2, $vacantes) or die(mysql_error());
$row_activos_catalogos2 = mysql_fetch_assoc($activos_catalogos2);
$totalRows_activos_catalogos2 = mysql_num_rows($activos_catalogos2);

$query_activos_catalogos3 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'z' AND sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio_a = 3";
$activos_catalogos3 = mysql_query($query_activos_catalogos3, $vacantes) or die(mysql_error());
$row_activos_catalogos3 = mysql_fetch_assoc($activos_catalogos3);
$totalRows_activos_catalogos3 = mysql_num_rows($activos_catalogos3);

$query_activos_catalogos4 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'z' AND sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio_a = 4";
$activos_catalogos4 = mysql_query($query_activos_catalogos4, $vacantes) or die(mysql_error());
$row_activos_catalogos4 = mysql_fetch_assoc($activos_catalogos4);
$totalRows_activos_catalogos4 = mysql_num_rows($activos_catalogos4);

$query_activos_catalogos5 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'z' AND sed_dps_catalogos.IDpuesto = '$IDpuesto' AND sed_dps_catalogos.criterio_a = 5";
$activos_catalogos5 = mysql_query($query_activos_catalogos5, $vacantes) or die(mysql_error());
$row_activos_catalogos5 = mysql_fetch_assoc($activos_catalogos5);
$totalRows_activos_catalogos5 = mysql_num_rows($activos_catalogos5);

if ($row_activos_catalogos1['criterio_b'] == "" OR $row_activos_catalogos1['criterio_b'] == "No Aplica")
{$computo = 'No';  $computo_c = 'N/A';} 
else
{$computo = 'Si'; $computo_c = $row_activos_catalogos1['criterio_b'];}
if ($row_activos_catalogos2['criterio_b'] == "" OR $row_activos_catalogos2['criterio_b'] == "No Aplica")
{$software = 'No'; $software_c = 'N/A';} 
else
{$software = 'Si'; $software_c = $row_activos_catalogos2['criterio_b'];}
if ($row_activos_catalogos3['criterio_b'] == "" OR $row_activos_catalogos3['criterio_b'] == "No Aplica")
{$automovil = 'No';$automovil_c = 'N/A';}
else
{$automovil = 'Si'; $automovil_c = $row_activos_catalogos3['criterio_b'];}
if ($row_activos_catalogos4['criterio_b'] == "" OR $row_activos_catalogos4['criterio_b'] == "No Aplica")
{$telefono = 'No'; $telefono_c = 'N/A';}
else 
{$telefono = 'Si'; $telefono_c = $row_activos_catalogos4['criterio_b'];}
if ($row_activos_catalogos5['criterio_b'] == "" OR $row_activos_catalogos5['criterio_b'] == "No Aplica")
{$otro2 = 'No'; $otro2_c = 'N/A';}
else 
{$otro2 = 'Si'; $otro2_c = $row_activos_catalogos5['criterio_b'];}


$objPHPExcel->getActiveSheet()->setCellValue('C124', $computo);
$objPHPExcel->getActiveSheet()->setCellValue('C125', $software);
$objPHPExcel->getActiveSheet()->setCellValue('C126', $automovil);
$objPHPExcel->getActiveSheet()->setCellValue('C127', $telefono);
$objPHPExcel->getActiveSheet()->setCellValue('C128', $otro2);

$objPHPExcel->getActiveSheet()->setCellValue('D124', $computo_c);
$objPHPExcel->getActiveSheet()->mergeCells('D124:I124');
$objPHPExcel->getActiveSheet()->setCellValue('D125', $software_c);
$objPHPExcel->getActiveSheet()->mergeCells('D125:I125');
$objPHPExcel->getActiveSheet()->setCellValue('D126', $automovil_c);
$objPHPExcel->getActiveSheet()->mergeCells('D126:I126');
$objPHPExcel->getActiveSheet()->setCellValue('D127', $telefono_c);
$objPHPExcel->getActiveSheet()->mergeCells('D127:I127');
$objPHPExcel->getActiveSheet()->setCellValue('D128', $otro2_c);
$objPHPExcel->getActiveSheet()->mergeCells('D128:I128');


    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Excel');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
    $rendererLibrary = 'dompdf';
    $rendererLibraryPath = dirname(__FILE__).'assets/PHPExcel/Writer/PDF/dompdf/';
    PHPExcel_Settings::setPdfRenderer($rendererName,$rendererLibraryPath);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
    $filePath =  'dps/dps/'.$row_elpuesto['IDpuesto'].' '.$row_elpuesto['denominacion'].'.pdf';
    $filePath2 =  $row_elpuesto['IDpuesto'].' '.$row_elpuesto['denominacion'].'.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment;filename="'.$filePath2.'"'); //tell browser what's the file name
    header('Cache-Control: max-age=0'); //no cache

    $objWriter->save($filePath);
    readfile($filePath);
    $objWriter->save('php://output');
       
    exit;

?>