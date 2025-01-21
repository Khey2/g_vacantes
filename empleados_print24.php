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
$IDsindicato = $_GET['IDsindicato'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT con_empleados.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.denominacion FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = $IDempleado";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$la_empresa = $row_contratos["IDmatriz"];


mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT con_empleados.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM con_empleados LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE con_empleados.IDempleado = $IDempleado";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);
$d_area = $row_area['area'];

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


$date_a = new DateTime($row_contratos['fecha_alta']);
$date_b = new DateTime($row_contratos['c_fecha_nacimiento']);
$diff_c = $date_a->diff($date_b);
$periodo_d =  $diff_c->y;

$date_a2 = new DateTime(date("Y-m-d"));
$date_b2 = new DateTime($row_contratos['c_fecha_nacimiento']); 
$diff_c2 = $date_a2->diff($date_b2);
$periodo_d2 =  $diff_c2->y;

$el_empleado = html_entity_decode($row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre'], ENT_QUOTES, "UTF-8");
$a_paterno = html_entity_decode($row_contratos['a_paterno'], ENT_QUOTES, "UTF-8");
$a_materno = html_entity_decode($row_contratos['a_materno'], ENT_QUOTES, "UTF-8");
$a_nombre  = html_entity_decode($row_contratos['a_nombre'],  ENT_QUOTES, "UTF-8");
$a_rfc  = html_entity_decode($row_contratos['a_rfc'],  ENT_QUOTES, "UTF-8");
$a_curp  = html_entity_decode($row_contratos['a_curp'],  ENT_QUOTES, "UTF-8");

$estado_2 = substr($a_curp, 11, 2);

$telefono_1  = html_entity_decode($row_contratos['telefono_1'],  ENT_QUOTES, "UTF-8");
$IDempleado_real = $row_contratos['IDempleado_real'];

mysql_select_db($database_vacantes, $vacantes);
$query_entidadnac = "SELECT * FROM con_estados WHERE estado_2 = '$estado_2'";
$entidadnac = mysql_query($query_entidadnac, $vacantes) or die(mysql_error());
$row_entidadnac = mysql_fetch_assoc($entidadnac);
$totalRows_entidadnac = mysql_num_rows($entidadnac);
$d_estado_nac = $row_entidadnac['estado'];


$d_calle =  html_entity_decode($row_contratos['d_calle'],  ENT_QUOTES, "UTF-8");
$d_numero_calle =  html_entity_decode($row_contratos['d_numero_calle'],  ENT_QUOTES, "UTF-8");
$d_colonia =  html_entity_decode($row_contratos['d_colonia'],  ENT_QUOTES, "UTF-8");
$d_delegacion_municipio =   html_entity_decode($row_contratos['d_delegacion_municipio'],  ENT_QUOTES, "UTF-8");
$d_estado =  $row_contratos['d_estado'];
$d_codigo_postal =  $row_contratos['d_codigo_postal'];
$IDescolaridad =  $row_contratos['escolaridad'];


mysql_select_db($database_vacantes, $vacantes);
$query_escolaridad = "SELECT * FROM con_escolaridad WHERE IDescolaridad = $IDescolaridad";
$escolaridad = mysql_query($query_escolaridad, $vacantes) or die(mysql_error());
$row_escolaridad = mysql_fetch_assoc($escolaridad);
$totalRows_escolaridad = mysql_num_rows($escolaridad);


$d_escolaridad =  $row_escolaridad['escolaridad'];


$estado =  $row_contratos['matriz'];
$denominacion =  $row_contratos['denominacion'];
$ubicacionfirma =  html_entity_decode($row_contratos['matriz_cv'],  ENT_QUOTES, "UTF-8"); 
$direccion_empresa =  $row_contratos['direccion'];
$fecha_alta = date('d/m/Y', strtotime($row_contratos['fecha_alta']));
$c_fecha_nacimiento = date('d/m/Y', strtotime($row_contratos['c_fecha_nacimiento']));
$timestp = date("dmYHm"); // la fecha actual
$sueldo = "$".number_format($row_contratos['b_sueldo_mensual'], 2);
$a_correo =  $row_contratos['a_correo'];



if ($row_contratos['IDempresa'] == 1) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';}
elseif ($row_contratos['IDempresa'] == 2) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';} 
elseif ($row_contratos['IDempresa'] == 3) {$empresa = 'PERINTO S.A. DE C.V.';}
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
// import page 1

     if ($_GET['IDsindicato'] == 1) { $pdf->setSourceFile('CONTS/sindicato1.pdf'); } 
else if ($_GET['IDsindicato'] == 2) { $pdf->setSourceFile('CONTS/sindicato2.pdf'); } 


$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 5, 215);


if ($_GET['IDsindicato'] == 1) {


// now write some text above the imported page
$pdf->SetFont('Arial', '', '10'); 
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(35,  44);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $el_empleado));

$pdf->SetXY(35,  98);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $el_empleado));

$pdf->SetXY(155,  115);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $telefono_1));

$pdf->SetXY(115,  115);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $c_fecha_nacimiento));


if($row_contratos['a_sexo'] == 1) {
$pdf->SetXY(81,  115);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', 'Hombre'));
}
if($row_contratos['a_sexo'] == 2) {
$pdf->SetXY(81,  115);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', 'Mujer'));
} 

$d_calles = "Calle: ".$d_calle." No. ".$d_numero_calle."; Col. ".$d_colonia."; ".$d_delegacion_municipio;
$pdf->SetXY(20,  132);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $d_calles));


$pdf->SetXY(165,  132);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $d_codigo_postal));

$pdf->SetXY(85,  150);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $d_escolaridad));

$pdf->SetXY(19,  185);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $empresa));

$pdf->SetXY(95,  185);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $fecha_alta));

$pdf->SetXY(135,  185);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $a_correo));

$pdf->SetXY(50,  203);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $IDempleado_real));

$pdf->SetXY(110,  203);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $denominacion));

$pdf->SetXY(35,  220);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $estado));

$pdf->SetXY(35,  240);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', '55 5628 5100'));

$pdf->SetXY(85,  240);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $ubicacionfirma));

$pdf->SetXY(135,  240);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $d_area));

$pdf->SetXY(35,  115);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $d_estado_nac ));

} else if ($_GET['IDsindicato'] == 2) {

// now write some text above the imported page
$pdf->SetFont('Arial', '', '10'); 
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(50,  83);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $el_empleado));

$d_calles = "Calle: ".$d_calle." No. ".$d_numero_calle."; Col. ".$d_colonia."; ".$d_delegacion_municipio;
$pdf->SetXY(53,  92);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $d_calles));

$pdf->SetXY(45,  101);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $periodo_d2." años"));

if($row_contratos['a_estado_civil'] == 2) {
    $pdf->SetXY(95,  101);
    $pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', 'Casado'));
    }
    if($row_contratos['a_estado_civil'] == 1) {
    $pdf->SetXY(95,  101);
    $pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', 'Soltero'));
    }

    
$pdf->SetXY(75,  109);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', "Si"));

$pdf->SetXY(56,  118);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $d_estado_nac));

$pdf->SetXY(60,  127);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $empresa));

$pdf->SetXY(70,  135);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $denominacion));

$pdf->SetXY(55,  144);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $sueldo." mensual"));

$pdf->SetXY(75,  219);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $ini_cont_d));

$pdf->SetXY(120,  219);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $ini_cont_m));

$pdf->SetXY(150,  219);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $ini_cont_y));

$pdf->SetXY(48,  245);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $el_empleado));






}

$nombreDelDocumento = "AFIL SINDICATO ".date('dmY')." ".$IDempleado.".pdf";

$pdf->Output($nombreDelDocumento, 'D');


?>