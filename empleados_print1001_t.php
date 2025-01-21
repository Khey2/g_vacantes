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

//las variables de sesion para el filtrado
if (isset($_GET['IDempleado'])) { $credenciales = $_GET['IDempleado'];}

mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT prod_activos.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.denominacion, vac_puestos.IDarea, prod_activos_fotos.IDfoto, prod_activos_fotos.file FROM prod_activos LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN prod_activos_fotos ON prod_activos.IDempleado = prod_activos_fotos.IDempleado WHERE prod_activos.IDempleado = $credenciales";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

$fondo = $_GET['IDfondo']."_T.pdf";

use setasign\Fpdi\Fpdi;
require_once('global_assets/fpdf/fpdf.php');
require_once('global_assets/fpdi2/src/autoload.php');

$pdf = new Fpdi('P','mm',array(56.7,89.1));
$pdf->AddPage();
$pdf->setSourceFile('files/'.$fondo);
$tplIdx = $pdf->importPage(2);
$pdf->useTemplate($tplIdx, 0, 1, 56.7);

$pdf->SetFont('Arial', '', 5.5, '', true);
$pdf->SetTextColor(0,0,0);

$IDempleado = $row_contratos['IDempleado'];
$IDpuesto = $row_contratos['IDpuesto'];
$elarea = $row_contratos ['IDarea'];
$a_rfc =  $row_contratos['rfc13'];
$a_curp =  $row_contratos['curp'];
$a_imss =  $row_contratos['imss'];
$IDempleado_real =  $row_contratos['IDempleado'];
$fotografia =  $row_contratos['file'];
$la_fecha = date("Y", strtotime($row_contratos['fecha_antiguedad']." + 1 year")); 
$a_rfc =  $row_contratos['rfc13'];
if($la_fecha <= $anio) {$la_fecha = $anio + 1;}

//cortamos nobres largos
$el_empleado = html_entity_decode($row_contratos['emp_paterno']." ".$row_contratos['emp_materno']." ".$row_contratos['emp_nombre'], ENT_QUOTES, "UTF-8");
$largo = strlen($el_empleado); 
if($largo > 15){
$completo = strpos($el_empleado, ' ', 10);
$parte2 = substr($el_empleado, ($completo + 1), 42);  
$resto = $largo - $completo;
$parte3 = substr($el_empleado, 0, "-".$resto);  
}else{
$parte3 = $el_empleado;  
$parte2 = ''; 
}



//cortamos puestos largos
$denominacion =  $row_contratos['denominacion'];
$largob = strlen($denominacion); 
if($largob > 15){
$completob = strpos($denominacion, ' ', 10);
$parte2b = substr($denominacion, ($completob), 42);  
$restob = $largob - $completob;
$parte3b = substr($denominacion, 0, "-".$restob);  
}else{
$parte3b = $denominacion;  
$parte2b = ''; 
}

if ($fotografia != "") {
$extension = pathinfo($fotografia, PATHINFO_EXTENSION);
$liga = 'files/'.$IDempleado.'/'.$fotografia;
} else {
$fotografia = 'foto.jpg';
$extension = 'jpg';
$liga = 'files/foto.jpg';
}

$pdf->SetXY(42, 25);
$pdf->Write(0, "No. Emp.: ".$IDempleado_real);
$pdf->SetXY(42, 29);
$pdf->Write(0, "NSS: ".$a_imss);
$pdf->SetXY(42, 33);
$pdf->Write(0, "RFC: ".$a_rfc);
$pdf->SetXY(42, 37);
$pdf->Write(0, "CURP: ".$a_curp);
$pdf->SetXY(42, 41);
$pdf->Write(0, "PUESTO: ".$parte3b);
$pdf->SetXY(42, 45);
$pdf->Write(0, $parte2b);
$pdf->SetXY(42, 59);
$pdf->Write(0, 'Vigencia: '.$la_fecha);


$pdf->AddPage();
$pdf->setSourceFile('files/'.$fondo);
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 1, 56.7);


if (strlen($parte2) > 15) {
    $pdf->SetFont('Arial', 'B', 6, '', true);    
} else {
   $pdf->SetFont('Arial', 'B', 6.5, '', true); 
}


$midPtX = $pdf->GetPageWidth() / 2;
$attendeeNameWidth = $pdf->GetStringWidth($parte3);
$shiftLeft = $attendeeNameWidth / 2;
$x1 = ($midPtX - $shiftLeft);
$pdf->setXY($x1, 64); 
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $parte3));

$midPtX = $pdf->GetPageWidth() / 2;
$attendeeNameWidth = $pdf->GetStringWidth($parte2);
$shiftLeft = $attendeeNameWidth / 2;
$x1 = ($midPtX - $shiftLeft);
$pdf->setXY($x1, 68); 
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $parte2));

try{
    $pdf->Image($liga, 17, 32, 25, 30, $extension, '');    
} catch(Exception $e) {
    var_dump($e);
    exit();
}



$nombreDelDocumento = "CREDENCIAL ".date('dmY')." ".$IDempleado.".pdf";

$pdf->Output($nombreDelDocumento, 'D');
?>