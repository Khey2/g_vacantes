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
$query_contratos = "SELECT con_empleados.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.denominacion, vac_puestos.IDarea FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = $IDempleado";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$IDpuesto = $row_contratos['IDpuesto'];
$elarea = $row_contratos ['IDarea'];

$el_empleado_a = html_entity_decode($row_contratos['a_paterno']." ".$row_contratos['a_materno'], ENT_QUOTES, "UTF-8");
$el_empleado_b = html_entity_decode($row_contratos['a_nombre'], ENT_QUOTES, "UTF-8");
$a_rfc =  $row_contratos['a_rfc'];
$a_curp =  $row_contratos['a_curp'];
$a_imss =  $row_contratos['a_imss'];
$IDempleado_real =  $row_contratos['IDempleado_real'];
$denominacion =  $row_contratos['denominacion'];
$fotografia =  $row_contratos['file'];
$la_fecha = date("Y", strtotime($row_contratos['fecha_alta']." + 1 year")); 
if($la_fecha <= $anio) {$la_fecha = $anio + 1;}

if ($fotografia != "") {
$extension = pathinfo($fotografia, PATHINFO_EXTENSION);
$liga = 'CRED/'.$IDempleado.'/'.$fotografia;
} else {
$fotografia = 'foto.jpg';
$extension = 'jpg';
$liga = 'CRED/foto.jpg';
}

//cortamos nobres largos
$el_empleado = html_entity_decode($row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre'], ENT_QUOTES, "UTF-8");
$largo = strlen($el_empleado); 
if($largo > 30){
$completo = strpos($el_empleado, ' ', 20);
$parte2 = substr($el_empleado, ($completo + 1), 20);  
$resto = $largo - $completo;
$parte3 = substr($el_empleado, 0, "-".$resto);  
}else{
$parte3 = $el_empleado;  
$parte2 = ''; 
}

//cortamos puestos largos
$denominacion =  $row_contratos['denominacion'];
$largob = strlen($denominacion); 
if($largob > 25){
$completob = strpos($denominacion, ' ', 20);
$parte2b = substr($denominacion, ($completob), 20);  
$restob = $largob - $completob;
$parte3b = substr($denominacion, 0, "-".$restob);  
}else{
$parte3b = $denominacion;  
$parte2b = ''; 
}

//fondo
$fondo = 0;
	if($elarea == 1 OR $elarea == 2) {$fondo = 'ALM';}
elseif($elarea == 3 OR $elarea == 4) {$fondo = 'DIS';}
elseif($elarea == 5 OR $elarea == 6) {$fondo = 'VEN';}
else {$fondo = 'ADM';}

$fondo = $fondo.".pdf";

use setasign\Fpdi\Fpdi;
require_once('global_assets/fpdf/fpdf.php');
require_once('global_assets/fpdi2/src/autoload.php');

// initiate FPDI
$pdf = new Fpdi('P','mm','letter');
// add a page
$pdf->AddPage();
// set the source file
$pdf->setSourceFile('CRED/'.$fondo);
// import page 1
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at position 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 5, 215);

// now write some text above the imported page
$pdf->SetFont('Arial', '', 6, '', true);
$pdf->SetTextColor(0,0,0);
$midPtX = $pdf->GetPageWidth() / 4;

$attendeeNameWidth = $pdf->GetStringWidth($parte3);
$shiftLeft = $attendeeNameWidth / 2;
$x1 = $midPtX - $shiftLeft;
$x1 = $x1 + 2;
$pdf->setXY($x1, 93); 
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $parte3));

$attendeeNameWidth = $pdf->GetStringWidth($parte2);
$shiftLeft = $attendeeNameWidth / 2;
$x2 = $midPtX - $shiftLeft;
$x2 = $x2 + 2;
$pdf->setXY($x2, 96); 
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $parte2));

$pdf->Image($liga, 44, 60, 25, 30, $extension, '');

$pdf->SetXY(90, 65);
$pdf->Write(0, "No. Emp.: ".$IDempleado_real);
$pdf->SetXY(90, 70);
$pdf->Write(0, "NSS: ".$a_imss);
$pdf->SetXY(90, 75);
$pdf->Write(0, "RFC: ".$a_rfc);
$pdf->SetXY(90, 80);
$pdf->Write(0, "CURP: ".$a_curp);
$pdf->SetXY(90, 85);
$pdf->Write(0, "PUESTO: ".$parte3b);
$pdf->SetXY(90, 88);
$pdf->Write(0, $parte2b);
$pdf->SetXY(115, 102);
$pdf->Write(0, 'Vigencia: ');
$pdf->SetXY(125, 102);
$pdf->Write(0, $la_fecha);

$nombreDelDocumento = "CREDENCIAL ".date('dmY')." ".$IDempleado.".pdf";
$pdf->Output($nombreDelDocumento, 'D');
?>