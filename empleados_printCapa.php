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
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fecha = date("Ymd");


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//$IDempleado = $_POST['IDempleado'];
//$IDempleado = "1413, 7854";

$N = count($_POST['IDempleado']); 
$Cadena = "";
for($i=0; $i < $N; $i++){
  $Cadena = $Cadena.$_POST['IDempleado'][$i].",";
}
$Cadena = $Cadena."0";

$el_curso =  $_POST['el_curso'];
$el_mes =  $_POST['el_mes'];
$el_anio =  $_POST['el_anio'];
$firma_1 =  $_POST['firma_1'];
$firma_2 =  $_POST['firma_2'];

mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT
capa_avance.denominacion,
capa_avance.emp_paterno,
capa_avance.emp_materno,
capa_avance.emp_nombre,
capa_avance.curp,
capa_avance.anio,
capa_avance.mes,
capa_avance.fecha_evento,
capa_avance.IDC_tipo_curso,
capa_avance.IDmatriz,
capa_avance.IDsucursal,
incapacidades_companias.IDllave_compania,
incapacidades_companias.razon_social,
incapacidades_companias.rfc_compania,
capa_avance.duracion,
capa_tipos_tematica_stps.IDno,
capa_tipos_tematica_stps.tipo_evento,
capa_cursos.IDC_capa_cursos,
capa_tipos_tematica_stps.IDtematicastps,
capa_cursos.nombre_curso 
FROM
capa_avance
LEFT JOIN incapacidades_companias ON capa_avance.IDcompania = incapacidades_companias.IDcompania
INNER JOIN capa_cursos ON capa_avance.IDC_capa_cursos = capa_cursos.IDC_capa_cursos
INNER JOIN capa_tipos_tematica_stps ON capa_cursos.IDtematicastps = capa_tipos_tematica_stps.IDtematicastps 
WHERE capa_avance.IDempleado IN ($Cadena) AND capa_avance.IDC_capa_cursos = $el_curso AND capa_avance.anio = $el_anio AND capa_avance.mes IN ($el_mes)";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

$query_firma_1 = "SELECT * FROM capa_firmas WHERE IDfirma = $firma_1";
$firma_1 = mysql_query($query_firma_1, $vacantes) or die(mysql_error());
$row_firma_1 = mysql_fetch_assoc($firma_1);

$query_firma_2 = "SELECT * FROM capa_firmas WHERE IDfirma = $firma_2";
$firma_2 = mysql_query($query_firma_2, $vacantes) or die(mysql_error());
$row_firma_2 = mysql_fetch_assoc($firma_2);

$firma_1_ = 'capa/'.$row_firma_1['file'];
$firma_2_ = 'capa/'.$row_firma_2['file'];
$firmador_1_ = $row_firma_1['firma'];
$firmador_2_ = $row_firma_2['firma'];


use setasign\Fpdi\Fpdi;
require_once('global_assets/fpdf/fpdf.php');
require_once('global_assets/fpdi2/src/autoload.php');

// initiate FPDI
$pdf = new Fpdi('P','mm','letter');
      
do {
$el_empleado = $row_contratos['IDempleado'];
$el_nombre = $row_contratos['emp_paterno']." ".$row_contratos['emp_materno']." ".$row_contratos['emp_nombre'];
$el_curp =  $row_contratos['curp'];
$el_puesto =  $row_contratos['denominacion'];
$la_compania =  'IMPULSORA SAHUAYO S.A DE C.V';
$IDcompania =  'I    S    A    -    9    5    0    8    1    0    -    2    2    9';
//$IDcompania =  $row_contratos['rfc_compania'];
$nombre_curso = $row_contratos['nombre_curso'];
$la_duracion = $row_contratos['duracion']." horas.";
$tematica = $row_contratos['IDno'];
$tematica2 = $row_contratos['tipo_evento'];
$tematica3 = $tematica2.' ('.$tematica.')';


$fechaSegundos = strtotime($row_contratos['fecha_evento']);
$dia = date("j", $fechaSegundos);
$mes = date("n", $fechaSegundos);
$año =  date("Y", $fechaSegundos);

// add a page
$pdf->AddPage();
// set the source file
$pdf->setSourceFile('capa/DS3a.pdf');
// import page 1
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 220);

// now write some text above the imported page
$pdf->SetFont('Arial', '', '9'); 
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(10,  50);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $el_nombre));

$pdf->SetXY(10,  60);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $el_curp));

$pdf->SetXY(10,  69);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $el_puesto));

$pdf->SetXY(10,  91);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $la_compania));

$pdf->SetXY(12,  101);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $IDcompania));

$pdf->SetXY(12,  120);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $nombre_curso));

$pdf->SetXY(12,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $la_duracion));

$pdf->SetXY(98,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $año));

$pdf->SetXY(120,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $mes));

$pdf->SetXY(134,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $dia));

$pdf->SetXY(163,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $año));

$pdf->SetXY(185,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $mes));

$pdf->SetXY(200,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $dia));

$pdf->SetXY(12,  137);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $tematica3));

$pdf->SetXY(12,  146);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $firmador_1_));

$pdf->SetXY(20,  180);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $firmador_1_));

$pdf->SetXY(145,  180);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $firmador_2_));


$pdf-> Image($firma_1_,30,150,30,30);

$pdf-> Image($firma_2_,155,150,30,30);


// add a page
$pdf->AddPage();
// set the source file
$pdf->setSourceFile('capa/DS3b.pdf');
// import page 1
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 220);

  
} while ($row_contratos = mysql_fetch_assoc($contratos));

$nombreDelDocumento = $fecha."_DS3.pdf";
$pdf->Output($nombreDelDocumento, 'D');

?>