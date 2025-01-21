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
$direccion_empresa =  $row_contratos['direccion'];
$fecha_alta = date('d/m/Y', strtotime($row_contratos['fecha_alta']));
$timestp = date("dmYHm"); // la fecha actual
$enletras = str_replace(',','',$row_contratos['b_sueldo_mensual']); 

if ($row_contratos['IDempresa'] == 1) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';}
elseif ($row_contratos['IDempresa'] == 2) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';} 
elseif ($row_contratos['IDempresa'] == 3) {$empresa = 'PERINTO S.A. DE C.V.';}
else{ $empresa = 'SIN DETERMINAR';}
 
if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$rep_legal = $row_variables['rep_legal'];} else {$rep_legal = 'Alejandro Barrios Uribe';} 

if($row_contratos['IDnacionalidad'] == 1) {$nacionalidad = "Mexicana";} else {$nacionalidad = "Extranjera";}
if($row_contratos['a_sexo'] == 1) {$sexo = "Masculino";} else {$sexo = "Femenino";}
if($row_contratos['a_estado_civil'] == 1) {$edo_civil = "Soltero";} else {$edo_civil = "Casado";}$IDestado = $row_contratos['IDestado'];
if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$logotipo = 'CONTS/logo.jpg';} else {$logotipo = 'https://www.gestionvacantes.com/CONTS/logo2.jpg';} 

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

<p align="center"><strong>POL&#205;TICA SOBRE CONFLICTO DE INTERESES</strong></p>

<p class="justificar">Para cumplir con la pol&#237;tica aprobada por la VP de Finanzas y la Direcci&#243;n de Recursos Humanos obtendr&#225; y conservar&#225; de cada uno de los empleados de las &#225;reas de Ventas, Log&#237;stica, Finanzas, Tecnol&#243;gicas de la Informaci&#243;n (Sistemas), Recursos Humanos, Compras y Jur&#237;dico, su correspondiente declaraci&#243;n respecto a conflicto de intereses, firmada por cada empleado, que por la naturaleza de sus funciones haga necesario el cumplimiento de la pol&#237;tica como a continuaci&#243;n se establece:</p>

<p class="justificar">Todos los empleados que presten sus servicios para <strong>'.$empresa.'</strong>, evitar&#225;n incurrir en conflicto de intereses, entendi&#233;ndose por tal, toda situaci&#243;n o evento en que los intereses personales, directos o indirectos, de los empleados o funcionarios se encuentren en oposici&#243;n con los de la sociedad, y en virtud de ello interfieran con los deberes que le competen a La empresa o bien que, a dicho empleado o funcionario act&#250;e motivado por un inter&#233;s personal, con la finalidad de obtener un beneficio de tipo econ&#243;mico o no, y que en virtud de ello interfiera con el cumplimiento de sus responsabilidades o bien, con los objetivos de la Empresa.</p>

<p class="justificar">La Empresa establecer&#225; procedimientos para asegurar que, cualquier hecho donde haya la apariencia o un conflicto de intereses, real o potencial, sea reportado y debidamente investigado.</p> 

<p class="justificar">El principio usado para determinar si el conflicto de intereses deber&#237;a ser reportado, es determinar donde podr&#237;a haber la apariencia o la potencialidad para un conflicto de intereses. No es &#250;nicamente si el empleado podr&#237;a usar informaci&#243;n confidencial para su beneficio personal, o si el empleado tomar&#225; decisiones, las cuales podr&#237;an afectar los intereses de la empresa desfavorablemente. Existen varias &#225;reas de conflicto potencial como son:</p>

<p class="justificar">1. Toda transacci&#243;n de un empleado de y con	<strong>'.$empresa.'</strong>, o cualquiera de sus afiliadas ser&#225; de manera formal y a un precio justo de mercado. Toda excepci&#243;n deber&#225; estar informada y aprobada por el Director General y/o Consejo de Administraci&#243;n y/o Vicepresidente de Finanzas y Administraci&#243;n previamente a su realizaci&#243;n.</p>

<p class="justificar">2. Regalos, obsequios, viajes o favores recibidos u ofrecidos por terceros en alguna forma relacionada con las funciones desempe&#241;adas para	<strong>'.$empresa.'</strong>., deber&#225; ser informado a la Direcci&#243;n de Recursos Humanos y Vicepresidente de Finanzas y Administraci&#243;n.</p>

<p class="justificar">Los empleados con responsabilidades en las &#225;reas de Ventas, Log&#237;stica, Finanzas, Tecnol&#243;gicas de la Informaci&#243;n (Sistemas), Recursos Humanos, Compras y Jur&#237;dico o de cualquier tipo de negociaciones de la empresa, necesitan ejercer particular cuidado para evitar un posible conflicto de intereses.</p> 

<p class="justificar">3. Los empleados no participar&#225;n pasiva ni activamente en ninguna forma con empresas competidoras, proveedores, prestadores de servicios o negocios del cliente, as&#237; mismo, no podr&#225;n usar informaci&#243;n confidencial de la empresa, para obtener una ganancia personal. Cualquier participaci&#243;n del empleado debe ser informado por escrito.</p> 

<p class="justificar">4. Los empleados deben informar de cualquier transacci&#243;n o negociaci&#243;n mayor a los $10,000.00 (DIEZ MIL PESOS 00/1000 M.N.), que celebren en nombre de <strong>'.$empresa.'</strong>, (o cualquiera de sus empresas afiliadas o filiales) cuando en la contra parte est&#233; involucrado cualquier familiar, amigo o existan intereses personales en la misma.</p>

<p class="justificar">5. Todos los ejecutivos con personal a su cargo deber&#225;n ser objetivos y justos en la administraci&#243;n de los recursos financieros y humanos, evitando conflictos de intereses al favorecer o limitar injustamente a los empleados en su carrera dentro de la organizaci&#243;n.</p> 

<p class="justificar">Un conflicto de intereses puede existir si cualquiera de las transacciones arriba mencionadas involucra a un familiar directo, en l&#237;nea ascendente o descendente, o colateral hasta el tercer grado, c&#243;nyuge, concubino, amigo o entidad propia afiliados. Las transacciones con tales personas deben estar descritas en esta forma. Un "familiar" es la esposa/o, hijo/a, pap&#225;/mam&#225;, hermano/a, t&#237;o/a, primo/a, cu&#241;ado/a, suegro/a, amigo/a y sobrino/a. Una "entidad afiliada" es una empresa, socio, propietario en com&#250;n, u otra entidad donde usted o uno de sus familiares sea socio, consejero, ejecutivo o due&#241;o desde el 10% del valor de la entidad.</p>

<p class="justificar">Cuando menos una vez al a&#241;o, los empleados deben de informar a la empresa de todo tipo de conflicto potencial de intereses que pudieran surgir despu&#233;s de firmar este documento, el empleado debe inmediatamente firmar una nueva forma y hacerla llegar al Director de Recursos Humanos. De no hacerlo por parte de cualquier empleado, de no firmar a tiempo y enviar una aclaraci&#243;n sobre el conflicto de intereses, o cumplir con esta pol&#237;tica, puede dar lugar a la terminaci&#243;n de la relaci&#243;n de trabajo sin responsabilidad para <strong>'.$empresa.'</strong></p>

<p class="justificar">Todos los empleados se comprometen a mantener el car&#225;cter de confidencialidad de la informaci&#243;n que reciban o generen, incluyendo estudios, bases de datos, documentos, archivos, pol&#237;ticas, formatos y cualquier otro documento o informaci&#243;n perteneciente a la empresa, ya sea que hayan sido preparados por el Empleado,	<strong>'.$empresa.'</strong>, o a trav&#233;s de un tercero, toda esta documentaci&#243;n se denominara "Informaci&#243;n Confidencial". Tambi&#233;n se considerada "Informaci&#243;n Confidencial" aquella informaci&#243;n que pertenezca a las empresas subsidiarias, filiales y empresas relacionadas de <strong>'.$empresa.'</strong></p> 

<p class="justificar">Los empleados a partir de la fecha de suscripci&#243;n del presente documento, se obligan a no enajenar, arrendar, prestar, grabar, negociar, revelar, publicar, ense&#241;ar, dar a conocer, transmitir o de alguna otra forma divulgarla o proporcionarla toda la informaci&#243;n y documentaci&#243;n que se considere	"Informaci&#243;n Confidencial", ya sea a cualquier persona f&#237;sica o moral, nacional o extranjera, p&#250;blica o privada, por cualquier medio, a&#250;n cuando se trate de incluirla o entregarla en otros documentos como estudios, reportes, propuestas u ofertas, ni en todo ni en parte, por ning&#250;n motivo a terceras personas f&#237;sicas o morales, nacionales o extranjeras, p&#250;blicas o privadas, presentes o futuras, que no hayan 
 sido autorizadas previamente y por escrito por <strong>'.$empresa.'</strong></p>

<p class="justificar">Asimismo, ning&#250;n empleado podr&#225; aprovechar o utilizar, en ning&#250;n caso, la Informaci&#243;n Confidencial para beneficio propio, de alg&#250;n familiar, amistad o de alg&#250;n tercero. En caso de acreditarse el uso indebido, aprovechamiento o divulgaci&#243;n de la "Informaci&#243;n Confidencial", desde luego traer&#225; como consecuencia la rescisi&#243;n de la relaci&#243;n laboral, sin perjuicio de que <strong>'.$empresa.'</strong>, ejercite las acciones civiles y/o penales que en derecho correspondan.</p>

<p class="justificar">Los empleados reconocen los documentos o informaci&#243;n relacionada con la situaci&#243;n financiera de cualquiera de las empresas del grupo, en forma enunciativa m&#225;s no limitativa: estados de cuenta bancarios, detalles de operaciones bancarias, informaci&#243;n corporativa, p&#243;lizas, formatos, etc, ser&#225; considerada como Informaci&#243;n Confidencial, y propiedad exclusiva de la empresa <strong>'.$empresa.'</strong>.</p>

<p class="justificar">En caso de que cualquier empleado sea sorprendido intercambiando Informaci&#243;n Confidencial, <strong>'.$empresa.'</strong>, rescindir&#225; la relaci&#243;n laboral, sin perjuicio de ejercitar las acciones civiles y/o penales que en derecho correspondan.</p>

<p class="justificar">El o los trabajadores que sean sorprendidos intercambiando Informaci&#243;n Confidencial, se comprometen a indemnizar a <strong>'.$empresa.'</strong>, debi&#233;ndole pagar los da&#241;os y perjuicios, que pudieran surgir de una revelaci&#243;n no autorizada de la Informaci&#243;n Confidencial.</p> 

<p class="justificar">La obligaci&#243;n de no revelar la	Informaci&#243;n Confidencial ser&#225; por todo el tiempo que el Empleado se encuentre laborando para	<strong>'.$empresa.'</strong>, En caso de que el Empleado deje de laborar para	<strong>'.$empresa.'</strong>, la
obligaci&#243;n de la Confidencialidad subsistir&#225; de forma indefinida.</p>

<p class="justificar">El Director de Recursos Humanos tiene la autoridad de aprobar cualquier interpretaci&#243;n o desviaci&#243;n en esta pol&#237;tica.</p>

<p class="justificar">Manifiesto de acuerdo a todo lo anterior que he cumplido con las normas y lineamientos del C&#243;digo de Conducta, as&#237; como con las pol&#237;ticas y procedimientos vigentes.</p>

<p class="justificar">Le&#237;do este aviso y habiendo entendido su contenido y alcance la firmo en '.$ubicacionfirma.' el d&#237;a '.$ini_cont_d.' de '.$ini_cont_m.' de '.$ini_cont_y.'.; de conformidad:</p>

<p class="justificar">&nbsp;</p>
<p class="justificar"><strong>Nombre: </strong><strong>'.$el_empleado.' </strong></p>
<p class="justificar">&nbsp;</p>
<p class="justificar">&nbsp;</p>
<p class="justificar"><strong>Firma: ___________________</strong></p>

</body>';
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
$nombreDelDocumento = "POLITICA SOBRE CONFLICTO DE INTERESES ".date('dmY')." ".$IDempleado.".pdf";
$bytes = file_put_contents("CONTS/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
unlink("CONTS/".$nombreDelDocumento);
?>