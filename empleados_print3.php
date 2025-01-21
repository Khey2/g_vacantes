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
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT con_empleados.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_matriz.firma_contratos, vac_puestos.denominacion FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
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

$date_a = new DateTime($row_contratos['fecha_alta']);
$date_b = new DateTime($row_contratos['c_fecha_nacimiento']);
$diff_c = $date_a->diff($date_b);
$periodo_d =  $diff_c->y;

$el_empleado = $row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre'];
$estado =  $row_contratos['matriz'];
$ubicacionfirma =  $row_contratos['matriz_cv'];
$direccion_empresa =  $row_contratos['direccion'];
$fecha_alta = date('d/m/Y', strtotime($row_contratos['fecha_alta']));
$timestp = date("dmYHm"); // la fecha actual
$enletras = str_replace(',','',$row_contratos['b_sueldo_mensual']); 

if ($row_contratos['IDempresa'] == 1) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';}
elseif ($row_contratos['IDempresa'] == 2) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';} 
elseif ($row_contratos['IDempresa'] == 3) {$empresa = 'PERINTO S.A. DE C.V.';}
else{ $empresa = 'SIN DETERMINAR';}
 
if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$rep_legal = $row_contratos['firma_contratos'];} else {$rep_legal = 'Alejandro Barrios Uribe';} 

if($row_contratos['IDnacionalidad'] == 1) {$nacionalidad = "Mexicana";} else {$nacionalidad = "Extranjera";}
if($row_contratos['a_sexo'] == 1) {$sexo = "Masculino";} else {$sexo = "Femenino";}
if($row_contratos['a_estado_civil'] == 1) {$edo_civil = "Soltero";} else {$edo_civil = "Casado";}$IDestado = $row_contratos['IDestado'];
if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$logotipo = 'CONTS/logo.jpg';} else {$logotipo = 'CONTS/logo2.jpg';} 

$body = '<!DOCTYPE html>
<meta charset="UTF-8"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
body {
font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
font-size: 12px;  
}
.justificar {
	text-align: justify;
}
.centrado {
	text-align: center;
}
.blanco {
	color: #FFF;
}
.saltopagina {
	page-break-after:always;
}
 @page {
            margin: 60px 60px 80px 60px !important;
            padding: 0px 0px 0px 0px !important;
}
</style>
<body>
<br clear="ALL"/>



<p class="justificar"><img width="191" height="65" src="'.$logotipo.'" /></p>
<p align="center"><strong>'.$empresa.'</strong></p>

<p align="center"><strong>Aviso de Privacidad</strong></p>

<p class="justificar">Por medio del presente documento, y en cumplimiento de la Ley de Protecci&#243;n de Datos Personales en Posesi&#243;n de Particulares, le comunicamos la siguiente informaci&#243;n, relativa a sus derechos inherentes a la informaci&#243;n personal que de manera voluntaria nos ha proporcionado.</p>

<p class="justificar">Los datos personales proporcionados por usted, son necesarios para la relaci&#243;n jur&#237;dica y contractual que podremos iniciar. </p>

<p class="justificar">
DATOS PERSONALES</p> 

<p class="justificar">Usted es el titular o propietario de sus datos e informaci&#243;n de car&#225;cter personal, lo que nosotros reconocemos y respetamos. La informaci&#243;n y datos personales que usted voluntariamente nos ha proporcionado, incluyendo en su caso datos sensibles, son necesarios y los utilizaremos para gestionar y dar tr&#225;mite a la solicitud de empleo que usted tambi&#233;n ha presentado, y a su inter&#233;s expreso de laborar para esta empresa.</p>

<p class="justificar">El tratamiento de sus datos personales incluye la obtenci&#243;n, uso, divulgaci&#243;n y almacenamiento de estos, por cualquier medio. Su uso abarca cualquier acci&#243;n de acceso, manejo, aprovechamiento, transferencia o disposici&#243;n de tales datos personales, incluyendo el otorgar referencias laborales del titular a terceras personas, respecto de su comportamiento, honestidad, metas, logros, jefes, trayectoria laboral y cualquier otra informaci&#243;n relacionada con el titular.</p>

<p class="justificar">Estos datos personales se los entrega usted a <strong>'.$empresa.'</strong>, identificada en este documento como "LA EMPRESA", con domicilio en <strong>'.$direccion_empresa.'</strong> , y n&#250;mero de tel&#233;fono 55 5628 5100, quien ser&#225; la entidad legal que asume la responsabilidad de la guarda y custodia de tal informaci&#243;n.</p>

<p class="justificar">La empresa no es responsable de la veracidad ni la precisi&#243;n de los datos que usted les ha proporcionado, ni tampoco los ha verificado, sino que &#250;nicamente los recibe, registra y conserva. </p> 

<p class="justificar">Asimismo, usted declara y confirma que cuenta con el consentimiento expreso de aquellas personas de las cuales tambi&#233;n proporciona informaci&#243;n personal, como los son, ejemplificativamente, c&#243;nyuge, concubina(o), dependientes econ&#243;micos, parientes, etc. </p>

<p class="justificar">SUS DERECHOS</p>

<p class="justificar">Le informamos por medio de este documento que usted podr&#225; acceder, rectificar o cancelar sus datos personales, as&#237; como oponerse o limitar su trasferencia, tratamiento o divulgaci&#243;n de estos, incluyendo la revocaci&#243;n de cualquier consentimiento anterior.</p>

<p class="justificar">Para ello, deber&#225; contactar al Responsable de la guarda de sus datos personales, quien es favalos@sahuayo.mx, satisfaciendo el siguiente procedimiento y requisitos: </p>

<p class="justificar">a) Proporcionar por escrito su nombre completo y domicilio actual. </p>
<p class="justificar">b) Anexar copia de una identificaci&#243;n oficial, como credencial para votar, licencia para conducir o pasaporte.</p>
<p class="justificar">c) El se&#241;alamiento claro y preciso de los datos personales a los que desea acceder, rectificar, cancelar o tratamiento al que desea oponerse.</p>
<p class="justificar">d) Cualquier otro requisito que la ley, su reglamento y dem&#225;s disposiciones aplicables establezcan. </p>

<p class="justificar">ALMACENAMIENTO DE SUS DATOS PERSONALES</p>

<p class="justificar">La empresa podr&#225; conservar sus datos personales en bases de datos ubicadas en su domicilio actual o en cualquier otro futuro, tanto en el pa&#237;s o en el extranjero.</p>

<p class="justificar">TRASFERENCIA DE DATOS PERSONALES</p>

<p class="justificar">Los datos personales que usted entrega a esta empresa podr&#225;n ser compartidos con las empresas filiales, subsidiarias, accionistas, asociadas o que de cualquier manera forman parte integrante del grupo empresarial y comercial al que pertenece esta empresa, en M&#233;xico o en el extranjero, circunstancia y tratamiento al que usted otorga su conformidad y autorizaci&#243;n expresa con la firma correspondiente en este documento.</p>

<p class="justificar">CAMBIOS AL AVISO DE PRIVACIDAD</p>

<p class="justificar">Si hubiera alg&#250;n cambio, modificaci&#243;n o actualizaci&#243;n a este Aviso de Privacidad, se le comunicar&#225; de manera oportuna y fehaciente, conforme a los datos proporcionados por usted o dicha actualizaci&#243;n se podr&#225; consultar en la p&#225;gina de internet http://www.sahuayo.com.mx.</p>

<p class="justificar">ACTUALIZACI&#211;N</p>

<p class="justificar">La empresa es responsable de la guarda y conservaci&#243;n de los datos personales proporcionados por usted. No lo es de su actualizaci&#243;n o modificaci&#243;n, por lo que si hubiese alg&#250;n cambio, usted deber&#225; comunicarlo oportunamente y por escrito al responsable de la guarda de los mismos.</p>

<p class="justificar">CONFORMIDAD</p>

<p class="justificar">Usted, como titular de los datos personales a que se refiere este Aviso de Privacidad, estampa su firma como constancia de recibo del mismo, y como expresi&#243;n voluntaria de su conformidad con lo contenido en este documento.</p>

<p class="justificar">'.$ubicacionfirma.' a '.$ini_cont_d.' de '.$ini_cont_m.' de '.$ini_cont_y.'.</p>

<table border="0" cellspacing="0" cellpadding="0" width="60%" align="center">
    <tr>
      <td width="45%" valign="top"><p align="center">
        <strong>&quot;EL TRABAJADOR&quot;</strong> <br>
        &nbsp; <br>
        &nbsp; <br>
        &nbsp; <br>
        &nbsp; <br>
        <strong>___________________________</strong><br>
        <strong>C. '.$row_contratos['a_nombre'].' '.$row_contratos['a_paterno'].' '.$row_contratos['a_materno'].'</strong><br>
	    <strong>&nbsp;</strong>
      </td>
      <td width="10%" valign="top"><p align="center"><p align="center" class="blanco">_______</p></p></td>
      <td width="45%" valign="top"><p align="center">
	  <strong>&quot;LA EMPRESA&quot;</strong> <br>
		&nbsp; <br>
		&nbsp; <br>
		&nbsp; <br>
		&nbsp; <br>
 	    <strong>___________________________</strong><br>
 	    <strong>'.$rep_legal.'</strong>
		</td>
	  </td>
</table></body>';
include_once "global_assets/dompdf/autoload.inc.php";
use Dompdf\Dompdf;
$dompdf = new Dompdf();
header('Content-Type: text/html; charset=UTF-8');
$dompdf->loadHtml($body);
$dompdf->set_option('enable_html5_parser', TRUE);
$dompdf->set_option('isRemoteEnabled', TRUE);
$dompdf->setPaper('letter');
$dompdf->render();
$contenido = $dompdf->output();
$nombreDelDocumento = "AVISO DE PRIVACIDAD ".date('dmY')." ".$IDempleado.".pdf";
$bytes = file_put_contents("CONTS/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);

unlink("CONTS/".$nombreDelDocumento);

?>