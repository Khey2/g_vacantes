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
$IDperiodovar = $row_variables['IDperiodo'];
$fecha = date("Y-m-d"); 

$el_usuario  = $_GET['IDempleado'];
if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;

//capturadas
$query_mis_capturadas = "SELECT Count(sed_individuales.IDmeta) AS Total FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'"; 
$mis_capturadas = mysql_query($query_mis_capturadas, $vacantes) or die(mysql_error());
$row_mis_capturadas = mysql_fetch_assoc($mis_capturadas);
$totalRows_mis_capturadas = mysql_num_rows($mis_capturadas);
$metas_capturadas = $row_mis_capturadas['Total'];

//evaluadas
$query_mis_propuestas = "SELECT Count(sed_individuales.IDmeta) AS Total FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo' AND sed_individuales.estatus = 2"; 
$mis_propuestas = mysql_query($query_mis_propuestas, $vacantes) or die(mysql_error());
$row_mis_propuestas = mysql_fetch_assoc($mis_propuestas);
$totalRows_mis_propuestas = mysql_num_rows($mis_propuestas);
$metas_propuestas = $row_mis_propuestas['Total'];

//evaluadas
$query_mis_evaluadas = "SELECT Count(sed_individuales.IDmeta) AS Total FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo' AND sed_individuales.estatus = 3"; 
$mis_evaluadas = mysql_query($query_mis_evaluadas, $vacantes) or die(mysql_error());
$row_mis_evaluadas = mysql_fetch_assoc($mis_evaluadas);
$totalRows_mis_evaluadas = mysql_num_rows($mis_evaluadas);
$metas_evaluadas = $row_mis_evaluadas['Total'];

$query_indicadores = "SELECT * FROM sed_indicadores_tipos"; 
$indicadores = mysql_query($query_indicadores, $vacantes) or die(mysql_error());
$row_indicadores = mysql_fetch_assoc($indicadores);

$query_unidades = "SELECT * FROM sed_unidad_medida"; 
$unidades = mysql_query($query_unidades, $vacantes) or die(mysql_error());
$row_unidades = mysql_fetch_assoc($unidades);

$query_resultados = "SELECT * FROM sed_individuales_resultados WHERE sed_individuales_resultados.IDempleado = '$el_usuario' AND sed_individuales_resultados.IDperiodo = '$IDperiodo'"; 
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);
$estatus_actual = $row_resultados['estatus'];


$query_mis_metas = "SELECT mi_mi, mi_3, mi_2, mi_1, mi_obs, mi_ponderacion, mi_IDunidad, mi_IDindicador, fecha_termino, mi_resultado FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'"; 
mysql_query("SET NAMES 'utf8'");
$mis_metas = mysql_query($query_mis_metas, $vacantes) or die(mysql_error());

//capturadas
$query_evaluado = "SELECT prod_activos.emp_paterno, prod_activos.IDempleado,  prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, prod_activos.IDllave, vac_areas.area, vac_matriz.matriz FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz  WHERE prod_activos.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$evaluado = mysql_query($query_evaluado, $vacantes) or die(mysql_error());
$row_evaluado = mysql_fetch_assoc($evaluado);
$totalRows_datos = mysql_num_rows($evaluado);

$query_periodo_sed = "SELECT * FROM sed_periodos_sed WHERE IDperiodo = '$IDperiodo'";
$periodo_sed = mysql_query($query_periodo_sed, $vacantes) or die(mysql_error());
$row_periodo_sed = mysql_fetch_assoc($periodo_sed);

	  if($row_resultados['resultado'] > 90) {$_calificacion = "Sobresaliente"; } 
 else if($row_resultados['resultado'] > 76) {$_calificacion = "Satisfactorio"; } 
 else if($row_resultados['resultado'] >  1) {$_calificacion = "Deficiente"; } 
 else {$_calificacion = "Sin Calificación"; } 

$_periodo = $row_periodo_sed['periodo'];
$IDempleado = $row_evaluado['IDempleado'];
$_nombre = $row_evaluado['emp_nombre'] . " " . $row_evaluado['emp_paterno'] . " " . $row_evaluado['emp_materno'];
$_puesto = $row_evaluado['denominacion'];
$_sucursal = $row_evaluado['matriz'];
$_area = $row_evaluado['area'];
$_fecha_ingreso = $row_evaluado['fecha_alta'];
$_resultado = $row_resultados['resultado'];

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("sed_files/Formato.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

	//datos fijos
	$objPHPExcel->getActiveSheet()->setCellValue('C5', $_nombre); 
	$objPHPExcel->getActiveSheet()->setCellValue('C6', $_puesto); 
	$objPHPExcel->getActiveSheet()->setCellValue('C7', $_area); 
	$objPHPExcel->getActiveSheet()->setCellValue('C8', $_sucursal); 
	$objPHPExcel->getActiveSheet()->setCellValue('G5', $_fecha_ingreso); 
	$objPHPExcel->getActiveSheet()->setCellValue('G6', $_periodo); 
	$objPHPExcel->getActiveSheet()->setCellValue('G7', $_resultado); 
	$objPHPExcel->getActiveSheet()->setCellValue('G8', $_calificacion); 

	$objPHPExcel->getActiveSheet()->setSelectedCells('B11');

    $inicio = 11; 
	
	while($row_mis_metas = mysql_fetch_array($mis_metas)){ 
	
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$inicio, rtrim(html_entity_decode(strip_tags($row_mis_metas['mi_mi']))));
    $inicio = $inicio + 1; 
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$inicio, $row_mis_metas['mi_3']); 
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$inicio, $row_mis_metas['mi_ponderacion']); 
    $inicio = $inicio + 1; 
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$inicio, $row_mis_metas['mi_2']); 

	switch ($row_mis_metas['mi_IDunidad']) {
		case "": $unidad = 'Sin definir';  break;    
		case 1:  $unidad = 'Cantidad.';  break;    
		case 2:  $unidad = 'Calidad.';  break;    
		case 3:  $unidad = 'Cantidad-Costo.';  break;    
		case 4:  $unidad = 'Cantidad-Calidad.';  break;    
		case 5:  $unidad = 'Cantidad-Tiempo.';  break;    
		case 6:  $unidad = 'Costo-Calidad.';  break;    
		case 7:  $unidad = 'Tiempo.';  break;    
		case 8:  $unidad = 'Tiempo-Calidad.';  break;    
		case 9:  $unidad = 'Tiempo-Costo.';  break;    
		default: $unidad = 'Sin definir';  }
		
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$inicio, $unidad); 
    $inicio = $inicio + 1; 
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$inicio, $row_mis_metas['mi_1']); 
	
	switch ($row_mis_metas['mi_IDindicador']) {
		case "": $indicador = 'Sin definir';  break;    
		case 1:  $indicador = 'Estrategico Sahuayo.';  break;    
		case 2:  $indicador = 'Estrategico del Area.';  break;    
		case 3:  $indicador = 'Funcional.';  break;    
		default: $indicador = 'Sin definir';  }
	
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$inicio, $indicador); 
    $inicio = $inicio + 1; 
	
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$inicio, strip_tags($row_mis_metas['mi_obs']));
	$objPHPExcel->getActiveSheet()->getStyle('C'.$inicio)->getAlignment()->setWrapText(true);

		  if($row_mis_metas['mi_resultado'] == 1) { $_resultadlmi = "Sobresaliente"; } 
	 else if($row_mis_metas['mi_resultado'] == 2) { $_resultadlmi = "Satisfactorio"; } 
	 else if($row_mis_metas['mi_resultado'] == 3) { $_resultadlmi = "Deficiente"; } 
	 else if($row_mis_metas['mi_resultado'] == 4) { $_resultadlmi = "En proceso-No aplica"; } 
	 else { $_resultadlmi = "Sin Evaluacion"; } 
							 
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$inicio, $_resultadlmi); 
    $inicio = $inicio + 3; 
    
    }
	
$objPHPExcel->getActiveSheet()->mergeCells('C5:E5');
$objPHPExcel->getActiveSheet()->mergeCells('C6:E6');
$objPHPExcel->getActiveSheet()->mergeCells('C7:E7');
$objPHPExcel->getActiveSheet()->mergeCells('C8:E8');
$objPHPExcel->getActiveSheet()->mergeCells('G5:I5');
$objPHPExcel->getActiveSheet()->mergeCells('G6:I6');
$objPHPExcel->getActiveSheet()->mergeCells('G7:I7');
$objPHPExcel->getActiveSheet()->mergeCells('G8:I8');

$objPHPExcel->getActiveSheet()->mergeCells('B11:I11');
$objPHPExcel->getActiveSheet()->mergeCells('B18:I18');
$objPHPExcel->getActiveSheet()->mergeCells('B25:I25');
$objPHPExcel->getActiveSheet()->mergeCells('B32:I32');
$objPHPExcel->getActiveSheet()->mergeCells('B39:I39');
$objPHPExcel->getActiveSheet()->mergeCells('B46:I46');
$objPHPExcel->getActiveSheet()->mergeCells('B53:I53');
$objPHPExcel->getActiveSheet()->mergeCells('B60:I60');
$objPHPExcel->getActiveSheet()->mergeCells('B67:I67');
$objPHPExcel->getActiveSheet()->mergeCells('B74:I74');
$objPHPExcel->getActiveSheet()->mergeCells('B81:I81');
$objPHPExcel->getActiveSheet()->mergeCells('B88:I88');
$objPHPExcel->getActiveSheet()->mergeCells('B95:I95');
$objPHPExcel->getActiveSheet()->mergeCells('B102:I102');
$objPHPExcel->getActiveSheet()->mergeCells('B109:I109');
$objPHPExcel->getActiveSheet()->mergeCells('C11:G11');
$objPHPExcel->getActiveSheet()->mergeCells('C18:G18');
$objPHPExcel->getActiveSheet()->mergeCells('C25:G25');
$objPHPExcel->getActiveSheet()->mergeCells('C32:G32');
$objPHPExcel->getActiveSheet()->mergeCells('C39:G39');
$objPHPExcel->getActiveSheet()->mergeCells('C46:G46');
$objPHPExcel->getActiveSheet()->mergeCells('C53:G53');
$objPHPExcel->getActiveSheet()->mergeCells('C60:G60');
$objPHPExcel->getActiveSheet()->mergeCells('C67:G67');
$objPHPExcel->getActiveSheet()->mergeCells('C74:G74');
$objPHPExcel->getActiveSheet()->mergeCells('C81:G81');
$objPHPExcel->getActiveSheet()->mergeCells('C88:G88');
$objPHPExcel->getActiveSheet()->mergeCells('C95:G95');
$objPHPExcel->getActiveSheet()->mergeCells('C102:G102');
$objPHPExcel->getActiveSheet()->mergeCells('C109:G109');
$objPHPExcel->getActiveSheet()->mergeCells('C12:G12');
$objPHPExcel->getActiveSheet()->mergeCells('C19:G19');
$objPHPExcel->getActiveSheet()->mergeCells('C26:G26');
$objPHPExcel->getActiveSheet()->mergeCells('C33:G33');
$objPHPExcel->getActiveSheet()->mergeCells('C40:G40');
$objPHPExcel->getActiveSheet()->mergeCells('C47:G47');
$objPHPExcel->getActiveSheet()->mergeCells('C54:G54');
$objPHPExcel->getActiveSheet()->mergeCells('C61:G61');
$objPHPExcel->getActiveSheet()->mergeCells('C68:G68');
$objPHPExcel->getActiveSheet()->mergeCells('C75:G75');
$objPHPExcel->getActiveSheet()->mergeCells('C82:G82');
$objPHPExcel->getActiveSheet()->mergeCells('C89:G89');
$objPHPExcel->getActiveSheet()->mergeCells('C96:G96');
$objPHPExcel->getActiveSheet()->mergeCells('C103:G103');
$objPHPExcel->getActiveSheet()->mergeCells('C110:G110');
$objPHPExcel->getActiveSheet()->mergeCells('C13:G13');
$objPHPExcel->getActiveSheet()->mergeCells('C20:G20');
$objPHPExcel->getActiveSheet()->mergeCells('C27:G27');
$objPHPExcel->getActiveSheet()->mergeCells('C34:G34');
$objPHPExcel->getActiveSheet()->mergeCells('C41:G41');
$objPHPExcel->getActiveSheet()->mergeCells('C48:G48');
$objPHPExcel->getActiveSheet()->mergeCells('C55:G55');
$objPHPExcel->getActiveSheet()->mergeCells('C62:G62');
$objPHPExcel->getActiveSheet()->mergeCells('C69:G69');
$objPHPExcel->getActiveSheet()->mergeCells('C76:G76');
$objPHPExcel->getActiveSheet()->mergeCells('C83:G83');
$objPHPExcel->getActiveSheet()->mergeCells('C90:G90');
$objPHPExcel->getActiveSheet()->mergeCells('C97:G97');
$objPHPExcel->getActiveSheet()->mergeCells('C104:G104');
$objPHPExcel->getActiveSheet()->mergeCells('C111:G111');
$objPHPExcel->getActiveSheet()->mergeCells('C14:G14');
$objPHPExcel->getActiveSheet()->mergeCells('C21:G21');
$objPHPExcel->getActiveSheet()->mergeCells('C28:G28');
$objPHPExcel->getActiveSheet()->mergeCells('C35:G35');
$objPHPExcel->getActiveSheet()->mergeCells('C42:G42');
$objPHPExcel->getActiveSheet()->mergeCells('C49:G49');
$objPHPExcel->getActiveSheet()->mergeCells('C56:G56');
$objPHPExcel->getActiveSheet()->mergeCells('C63:G63');
$objPHPExcel->getActiveSheet()->mergeCells('C70:G70');
$objPHPExcel->getActiveSheet()->mergeCells('C77:G77');
$objPHPExcel->getActiveSheet()->mergeCells('C84:G84');
$objPHPExcel->getActiveSheet()->mergeCells('C91:G91');
$objPHPExcel->getActiveSheet()->mergeCells('C98:G98');
$objPHPExcel->getActiveSheet()->mergeCells('C105:G105');
$objPHPExcel->getActiveSheet()->mergeCells('C112:G112');
$objPHPExcel->getActiveSheet()->mergeCells('C15:G15');
$objPHPExcel->getActiveSheet()->mergeCells('C22:G22');
$objPHPExcel->getActiveSheet()->mergeCells('C29:G29');
$objPHPExcel->getActiveSheet()->mergeCells('C36:G36');
$objPHPExcel->getActiveSheet()->mergeCells('C43:G43');
$objPHPExcel->getActiveSheet()->mergeCells('C50:G50');
$objPHPExcel->getActiveSheet()->mergeCells('C57:G57');
$objPHPExcel->getActiveSheet()->mergeCells('C64:G64');
$objPHPExcel->getActiveSheet()->mergeCells('C71:G71');
$objPHPExcel->getActiveSheet()->mergeCells('C78:G78');
$objPHPExcel->getActiveSheet()->mergeCells('C85:G85');
$objPHPExcel->getActiveSheet()->mergeCells('C92:G92');
$objPHPExcel->getActiveSheet()->mergeCells('C99:G99');
$objPHPExcel->getActiveSheet()->mergeCells('C106:G106');
$objPHPExcel->getActiveSheet()->mergeCells('C113:G113');



$query_metas_totales = "SELECT sed_individuales.IDmeta FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo'"; 
$metas_totales = mysql_query($query_metas_totales, $vacantes) or die(mysql_error());
$row_metas_totales = mysql_fetch_assoc($metas_totales);
$totalRows_metas_totales = mysql_num_rows($metas_totales);


if ($totalRows_metas_totales == 14) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
	
} elseif ($totalRows_metas_totales == 13) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
	
} elseif ($totalRows_metas_totales == 12) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	

} elseif ($totalRows_metas_totales == 11) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	

} elseif ($totalRows_metas_totales == 10) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	

} elseif ($totalRows_metas_totales == 9) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(73)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(74)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(75)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(76)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(77)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(78)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(79)->setVisible(false);	

} elseif ($totalRows_metas_totales == 8) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(73)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(74)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(75)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(76)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(77)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(78)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(79)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(66)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(67)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(68)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(69)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(70)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(71)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(72)->setVisible(false);	


} elseif ($totalRows_metas_totales == 7) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(73)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(74)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(75)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(76)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(77)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(78)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(79)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(66)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(67)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(68)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(69)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(70)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(71)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(72)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(59)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(60)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(61)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(62)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(63)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(64)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(65)->setVisible(false);	

} elseif ($totalRows_metas_totales == 6) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(73)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(74)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(75)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(76)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(77)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(78)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(79)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(66)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(67)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(68)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(69)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(70)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(71)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(72)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(59)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(60)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(61)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(62)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(63)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(64)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(65)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(52)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(53)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(54)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(55)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(56)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(57)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(58)->setVisible(false);	

} elseif ($totalRows_metas_totales == 5) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(73)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(74)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(75)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(76)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(77)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(78)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(79)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(66)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(67)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(68)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(69)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(70)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(71)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(72)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(59)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(60)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(61)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(62)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(63)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(64)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(65)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(52)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(53)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(54)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(55)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(56)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(57)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(58)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(45)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(46)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(47)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(48)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(49)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(50)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(51)->setVisible(false);	

} elseif ($totalRows_metas_totales == 4) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(73)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(74)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(75)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(76)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(77)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(78)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(79)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(66)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(67)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(68)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(69)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(70)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(71)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(72)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(59)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(60)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(61)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(62)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(63)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(64)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(65)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(52)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(53)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(54)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(55)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(56)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(57)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(58)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(45)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(46)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(47)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(48)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(49)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(50)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(51)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(38)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(39)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(40)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(41)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(42)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(43)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(44)->setVisible(false);	

} elseif ($totalRows_metas_totales == 3) {
	
$objPHPExcel->getActiveSheet()->getRowDimension(108)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(109)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(110)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(111)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(112)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(113)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(101)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(102)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(103)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(104)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(105)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(106)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(107)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(95)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(96)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(97)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(98)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(99)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(100)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(87)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(88)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(89)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(90)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(91)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(92)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(93)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(94)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(80)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(81)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(82)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(83)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(84)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(85)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(86)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(73)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(74)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(75)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(76)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(77)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(78)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(79)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(66)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(67)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(68)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(69)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(70)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(71)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(72)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(59)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(60)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(61)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(62)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(63)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(64)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(65)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(52)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(53)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(54)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(55)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(56)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(57)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(58)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(45)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(46)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(47)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(48)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(49)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(50)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(51)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(38)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(39)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(40)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(41)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(42)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(43)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(44)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(31)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(32)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(33)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(34)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(35)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(36)->setVisible(false);	
$objPHPExcel->getActiveSheet()->getRowDimension(37)->setVisible(false);	
}
// Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('Excel');


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$_nombre. ' ' .$_periodo.'".xls');
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


?>En construccion...