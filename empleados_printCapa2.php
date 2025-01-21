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


mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT * FROM prod_activos WHERE IDempleado IN ($IDempleado)";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$el_empleado = $row_contratos['IDempleado'];

use setasign\Fpdi\Fpdi;
require_once('global_assets/fpdf/fpdf.php');
require_once('global_assets/fpdi2/src/autoload.php');

class automate_pdf extends FPDF{    
  function generate($name){
      if(isset($name)){
          $nombrePDF = 'PDF'. $name.'.pdf';
          $pdf = new FPDF();
          $pdf = new Fpdi('P','mm','letter');
          $pdf->AddPage();
          $pdf->setSourceFile('CONTS/FONDO.pdf');
          $tplIdx = $pdf->importPage(1);
          $pdf->useTemplate($tplIdx, 0, 0, 200);

          $pdf->SetFont('Arial', '', '9'); 
          $pdf->SetTextColor(0,0,0);

          $pdf->SetXY(100,  82);
          $pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', "texto plano"));
            

          $pdf->Cell(40,10,'Hola, Mundo ' . $name);
          $pdf->Output( $nombrePDF, "F");
          //ob_end_clean();
          //$pdf -> Output();
      }
  }

}

do {
  $i = $row_contratos['IDempleado'];
  //echo $i .'<BR>';
    $arcpdf = new automate_pdf();
    //$i . "<BR>";
    //echo $nombre = $i; 
    $arcpdf->generate($i);
} while ($row_contratos = mysql_fetch_assoc($contratos));
?>