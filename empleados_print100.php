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
$mis_areas = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT con_empleados.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.denominacion FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = $IDempleado";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

// convertir fecha en letras
$ini_cont_d = date('d', strtotime($row_contratos['fecha_alta']));
$ini_cont_m_ = date('m', strtotime($row_contratos['fecha_alta']));
$ini_cont_y = date('Y', strtotime($row_contratos['fecha_alta']));

// convertir fecha en letras
$nac_cont_d = date('d', strtotime($row_contratos['c_fecha_nacimiento']));
$nac_cont_m_ = date('m', strtotime($row_contratos['c_fecha_nacimiento']));
$nac_cont_y = date('Y', strtotime($row_contratos['c_fecha_nacimiento']));

switch ($ini_cont_m_) {
case '01':  $ini_cont_m = "enero";      break;     
case '02':  $ini_cont_m = "febrero";    break;    
case '03':  $ini_cont_m = "marzo";      break;    
case '04':  $ini_cont_m = "abril";      break;    
case '05':  $ini_cont_m = "mayo";       break;    
case '06':  $ini_cont_m = "junio";      break;    
case '07':  $ini_cont_m = "julio";      break;    
case '08':  $ini_cont_m = "agosto";     break;    
case '09':  $ini_cont_m = "septiembre"; break;    
case '10': $ini_cont_m = "octubre";    break;    
case '11': $ini_cont_m = "noviembre";  break;    
case '12': $ini_cont_m = "diciembre";  break;   
}

switch ($nac_cont_m_) {
case '01':  $nac_cont_m = "enero";      break;     
case '02':  $nac_cont_m = "febrero";    break;    
case '03':  $nac_cont_m = "marzo";      break;    
case '04':  $nac_cont_m = "abril";      break;    
case '05':  $nac_cont_m = "mayo";       break;    
case '06':  $nac_cont_m = "junio";      break;    
case '07':  $nac_cont_m = "julio";      break;    
case '08':  $nac_cont_m = "agosto";     break;    
case '09':  $nac_cont_m = "septiembre"; break;    
case '10': $nac_cont_m = "octubre";    break;    
case '11': $nac_cont_m = "noviembre";  break;    
case '12': $nac_cont_m = "diciembre";  break;   
}



$poliza = " 1  0  1  1  1   9  3  1  0  9   4  5  2";
$grupo_empleados ="Empleados en servicio activo";

$date_a = new DateTime($row_contratos['fecha_alta']);
$date_b = new DateTime($row_contratos['c_fecha_nacimiento']);
$diff_c = $date_a->diff($date_b);
$periodo_d =  $diff_c->y;

$el_empleado = html_entity_decode($row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre'], ENT_QUOTES, "UTF-8");
$a_paterno = html_entity_decode($row_contratos['a_paterno'], ENT_QUOTES, "UTF-8");
$a_materno = html_entity_decode($row_contratos['a_materno'], ENT_QUOTES, "UTF-8");
$a_nombre  = html_entity_decode($row_contratos['a_nombre'],  ENT_QUOTES, "UTF-8");
$a_rfc  = html_entity_decode($row_contratos['a_rfc'],  ENT_QUOTES, "UTF-8");
$a_curp  = html_entity_decode($row_contratos['a_curp'],  ENT_QUOTES, "UTF-8");
$a_correo  = html_entity_decode($row_contratos['a_correo'],  ENT_QUOTES, "UTF-8");
$IDempleado_real = $row_contratos['IDempleado_real'];
$estado =  $row_contratos['matriz'];
$denominacion =  $row_contratos['denominacion'];
$ubicacionfirma =  utf8_decode($row_contratos['matriz_cv']);
$direccion_empresa =  $row_contratos['direccion'];
$timestp = date("dmYHm"); // la fecha actual
$sueldo = "$".number_format($row_contratos['b_sueldo_mensual'], 2);

if ($row_contratos['IDempresa'] == 1) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';}
elseif ($row_contratos['IDempresa'] == 2) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';} 
else{ $empresa = 'SIN DETERMINAR';}
 
if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$rep_legal = $row_variables['rep_legal'];} else {$rep_legal = 'Alejandro Barrios Uribe';} 
$sexo = 'X';
$estadocivil = 'X';
if($row_contratos['IDnacionalidad'] == 1) {$nacionalidad = "Mexicana";} else {$nacionalidad = "Extranjera";}
$IDestado = $row_contratos['IDestado'];
$la_fecha_lugar = html_entity_decode($ubicacionfirma.', a '.$ini_cont_d.' de '.$ini_cont_m.' de '.$ini_cont_y.'.', ENT_QUOTES, "UTF-8");

use setasign\Fpdi\Fpdi;

require_once('global_assets/fpdf/fpdf.php');
require_once('global_assets/fpdi2/src/autoload.php');

// initiate FPDI
$pdf = new Fpdi('P','mm','letter');
// add a page
$pdf->AddPage();
// set the source file
$pdf->setSourceFile('CONTS/Poliza2025_Parte1.pdf');
// import page 1
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);

// now write some text above the imported page
$pdf->SetFont('Arial', '', '9'); 
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(12,  71);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $empresa));

$pdf->SetXY(12,  119);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $a_nombre));

$pdf->SetXY(12,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $a_paterno));

$pdf->SetXY(100,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $a_materno));

$elsexo = $row_contratos['a_sexo'];

if($elsexo == 1) {
$pdf->SetXY(88,  138);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', "X"));
}
if($elsexo == 2) {
$pdf->SetXY(68,  138);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', "X"));
} 

$fecha_alta = substr($row_contratos['fecha_alta'],8,1)."  ".substr($row_contratos['fecha_alta'],9,1)."   /   ".
              substr($row_contratos['fecha_alta'],5,1)."  ".substr($row_contratos['fecha_alta'],6,1)."   /   ".
              substr($row_contratos['fecha_alta'],0,1)."  ".substr($row_contratos['fecha_alta'],1,1)."  ".substr($row_contratos['fecha_alta'],2,1)."  ".substr($row_contratos['fecha_alta'],3,1);
$c_fecha_nacimiento = substr($row_contratos['c_fecha_nacimiento'],8,1)."  ".substr($row_contratos['c_fecha_nacimiento'],9,1)."   /   ".
              substr($row_contratos['c_fecha_nacimiento'],5,1)."  ".substr($row_contratos['c_fecha_nacimiento'],6,1)."   /   ".
              substr($row_contratos['c_fecha_nacimiento'],0,1)."  ".substr($row_contratos['c_fecha_nacimiento'],1,1)."  ".substr($row_contratos['c_fecha_nacimiento'],2,1)."  ".substr($row_contratos['c_fecha_nacimiento'],3,1);


$pdf->SetXY(12,  138);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $c_fecha_nacimiento));

$pdf->SetXY(12,  147);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $fecha_alta));

$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte2.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);


$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte3.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);


$fecha_alta2 = substr($row_contratos['fecha_alta'],8,1)."  ".substr($row_contratos['fecha_alta'],9,1)."   /   ".
              substr($row_contratos['fecha_alta'],5,1)."  ".substr($row_contratos['fecha_alta'],6,1)."   /   ".
              substr($row_contratos['fecha_alta'],0,1)."   ".substr($row_contratos['fecha_alta'],1,1)."   ".substr($row_contratos['fecha_alta'],2,1)."   ".substr($row_contratos['fecha_alta'],3,1);


$pdf->SetXY(12, 38); 
$pdf->Write(0,  iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $ubicacionfirma));

$pdf->SetXY(152, 38); 
$pdf->Write(0,  iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $fecha_alta2));


$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte4.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);

// now write some text above the imported page
$pdf->SetFont('Arial', '', '9'); 
$pdf->SetTextColor(0,0,0);

$pdf->SetXY(12,  71);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $empresa));

$pdf->SetXY(12,  119);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $a_nombre));

$pdf->SetXY(12,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $a_paterno));

$pdf->SetXY(100,  129);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $a_materno));

$elsexo = $row_contratos['a_sexo'];

if($elsexo == 1) {
$pdf->SetXY(88,  138);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', "X"));
}
if($elsexo == 2) {
$pdf->SetXY(68,  138);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', "X"));
} 

$pdf->SetXY(12,  138);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $c_fecha_nacimiento));


$pdf->SetXY(12,  147);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $fecha_alta));


$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte5.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);

$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte6.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);


$pdf->SetXY(12, 47); 
$pdf->Write(0,  iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $ubicacionfirma));

$pdf->SetXY(152, 47); 
$pdf->Write(0,  iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $fecha_alta2));


$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte7.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);

$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte8.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);

$pdf->AddPage();
$pdf->setSourceFile('CONTS/Poliza2025_Parte9.pdf');
// import page 2
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0, 200);


$nombreDelDocumento = "SEGURO DE VIDA ".date('dmY')." ".$IDempleado.".pdf";

$pdf->Output($nombreDelDocumento, 'D');
unlink("CONTS/".$nombreDelDocumento);


?>