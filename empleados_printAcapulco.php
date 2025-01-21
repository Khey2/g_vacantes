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
  set_time_limit(0);

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

//variables recuperadas
$IDempleado = $_POST['IDempleado'];
$IDempleadoF = $_POST['firma'];
$fecha_baja = $_POST['fecha1'];
$monto_ = $_POST['monto'];

mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT prod_activos.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.IDarea FROM prod_activos LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$elarea = $row_contratos ['IDarea'];
$sueldo_mensual = "$".number_format($row_contratos['sueldo_mensual'],2);
$monto = "$".number_format($monto_,2);


if ($elarea == 1 OR $elarea == 2 OR $elarea == 3 OR $elarea == 4) { $horario1 = 'de 7:00 a 16:00 horas'; $horario2 = 'de 11:00 a 12:00 horas'; } else {$horario1 = 'de 8:00 a 18:00 horas'; $horario2 = 'de 14:00 a 16:00 horas';}

$el_empleado = $row_contratos['emp_paterno']." ".$row_contratos['emp_materno']." ".$row_contratos['emp_nombre'];
$ubicacionfirma =  $row_contratos['matriz_cv'];
$fecha_antiguedad = date('d-m-Y', strtotime($row_contratos['fecha_antiguedad']));
// convertir fecha en letras
$ini_cont_d = date('d', strtotime($row_contratos['fecha_antiguedad']));
$ini_cont_m_ = date('m', strtotime($row_contratos['fecha_antiguedad']));
$ini_cont_y = date('Y', strtotime($row_contratos['fecha_antiguedad']));


function num2letras($num, $fem = false, $dec = true) { 
    $matuni[2]  = "dos"; 
    $matuni[3]  = "tres"; 
    $matuni[4]  = "cuatro"; 
    $matuni[5]  = "cinco"; 
    $matuni[6]  = "seis"; 
    $matuni[7]  = "siete"; 
    $matuni[8]  = "ocho"; 
    $matuni[9]  = "nueve"; 
    $matuni[10] = "diez"; 
    $matuni[11] = "once"; 
    $matuni[12] = "doce"; 
    $matuni[13] = "trece"; 
    $matuni[14] = "catorce"; 
    $matuni[15] = "quince"; 
    $matuni[16] = "dieciseis"; 
    $matuni[17] = "diecisiete"; 
    $matuni[18] = "dieciocho"; 
    $matuni[19] = "diecinueve"; 
    $matuni[20] = "veinte"; 
    $matunisub[2] = "dos"; 
    $matunisub[3] = "tres"; 
    $matunisub[4] = "cuatro"; 
    $matunisub[5] = "quin"; 
    $matunisub[6] = "seis"; 
    $matunisub[7] = "sete"; 
    $matunisub[8] = "ocho"; 
    $matunisub[9] = "nove"; 
 
    $matdec[2] = "veint"; 
    $matdec[3] = "treinta"; 
    $matdec[4] = "cuarenta"; 
    $matdec[5] = "cincuenta"; 
    $matdec[6] = "sesenta"; 
    $matdec[7] = "setenta"; 
    $matdec[8] = "ochenta"; 
    $matdec[9] = "noventa"; 
    $matsub[3]  = 'mill'; 
    $matsub[5]  = 'bill'; 
    $matsub[7]  = 'mill'; 
    $matsub[9]  = 'trill'; 
    $matsub[11] = 'mill'; 
    $matsub[13] = 'bill'; 
    $matsub[15] = 'mill'; 
    $matmil[4]  = 'millones'; 
    $matmil[6]  = 'billones'; 
    $matmil[7]  = 'de billones'; 
    $matmil[8]  = 'millones de billones'; 
    $matmil[10] = 'trillones'; 
    $matmil[11] = 'de trillones'; 
    $matmil[12] = 'millones de trillones'; 
    $matmil[13] = 'de trillones'; 
    $matmil[14] = 'billones de trillones'; 
    $matmil[15] = 'de billones de trillones'; 
    $matmil[16] = 'millones de billones de trillones'; 
    
    //Zi hack
    $float=explode('.',$num);
    $num=$float[0];
 
    $num = trim((string)@$num); 
    if ($num[0] == '-') { 
       $neg = 'menos '; 
       $num = substr($num, 1); 
    }else 
       $neg = ''; 
    while ($num[0] == '0') $num = substr($num, 1); 
    if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
    $zeros = true; 
    $punt = false; 
    $ent = ''; 
    $fra = ''; 
    for ($c = 0; $c < strlen($num); $c++) { 
       $n = $num[$c]; 
       if (! (strpos(".,'''", $n) === false)) { 
          if ($punt) break; 
          else{ 
             $punt = true; 
             continue; 
          } 
 
       }elseif (! (strpos('0123456789', $n) === false)) { 
          if ($punt) { 
             if ($n != '0') $zeros = false; 
             $fra .= $n; 
          }else 
 
             $ent .= $n; 
       }else 
 
          break; 
 
    } 
    $ent = '     ' . $ent; 
    if ($dec and $fra and ! $zeros) { 
       $fin = ' coma'; 
       for ($n = 0; $n < strlen($fra); $n++) { 
          if (($s = $fra[$n]) == '0') 
             $fin .= ' cero'; 
          elseif ($s == '1') 
             $fin .= $fem ? ' una' : ' un'; 
          else 
             $fin .= ' ' . $matuni[$s]; 
       } 
    }else 
       $fin = ''; 
    if ((int)$ent === 0) return 'Cero ' . $fin; 
    $tex = ''; 
    $sub = 0; 
    $mils = 0; 
    $neutro = false; 
    while ( ($num = substr($ent, -3)) != '   ') { 
       $ent = substr($ent, 0, -3); 
       if (++$sub < 3 and $fem) { 
          $matuni[1] = 'una'; 
          $subcent = 'as'; 
       }else{ 
          $matuni[1] = $neutro ? 'un' : 'uno'; 
          $subcent = 'os'; 
       } 
       $t = ''; 
       $n2 = substr($num, 1); 
       if ($n2 == '00') { 
       }elseif ($n2 < 21) 
          $t = ' ' . $matuni[(int)$n2]; 
       elseif ($n2 < 30) { 
          $n3 = $num[2]; 
          if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
          $n2 = $num[1]; 
          $t = ' ' . $matdec[$n2] . $t; 
       }else{ 
          $n3 = $num[2]; 
          if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
          $n2 = $num[1]; 
          $t = ' ' . $matdec[$n2] . $t; 
       } 
       $n = $num[0]; 
       if ($n == 1) { 
          $t = ' ciento' . $t; 
       }elseif ($n == 5){ 
          $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
       }elseif ($n != 0){ 
          $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
       } 
       if ($sub == 1) { 
       }elseif (! isset($matsub[$sub])) { 
          if ($num == 1) { 
             $t = ' mil'; 
          }elseif ($num > 1){ 
             $t .= ' mil'; 
          } 
       }elseif ($num == 1) { 
          $t .= ' ' . $matsub[$sub] . '?n'; 
       }elseif ($num > 1){ 
          $t .= ' ' . $matsub[$sub] . 'ones'; 
       }   
       if ($num == '000') $mils ++; 
       elseif ($mils != 0) { 
          if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
          $mils = 0; 
       } 
       $neutro = true; 
       $tex = $t . $tex; 
    } 
    $tex = $neg . substr($tex, 1) . $fin; 
    //Zi hack --> return ucfirst($tex);
    $end_num=ucfirst($tex).' pesos '.$float[1].'/100 M.N.';
    return $end_num; 
 } 
 

$sueldo_diario_enletras = num2letras($monto_);


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

$fecha = date("Y-m-d");
$date_a = new DateTime($row_contratos['fecha_antiguedad']);
$date_b = new DateTime($fecha);
$diff_c = $date_a->diff($date_b);
$antiguedad =  $diff_c->y;
//$antiguedad =  1;

switch ($antiguedad) {
    case 1:  $dias_vac = "12 dias"; break;     
    case 2:  $dias_vac = "14 dias"; break;    
    case 3:  $dias_vac = "16 dias"; break;    
    case 4:  $dias_vac = "18 dias"; break;    
    case 5:  $dias_vac = "20 dias"; break;    
    case 6:  $dias_vac = "22 dias"; break;    
    case 7:  $dias_vac = "22 dias"; break;    
    case 8:  $dias_vac = "22 dias"; break;    
    case 9:  $dias_vac = "22 dias"; break;    
    case 10: $dias_vac = "22 dias"; break;    
    case 11: $dias_vac = "24 dias"; break;    
    case 12: $dias_vac = "24 dias"; break;   
    case 13: $dias_vac = "24 dias"; break;   
    case 14: $dias_vac = "24 dias"; break;   
    case 15: $dias_vac = "24 dias"; break;   
    case 16: $dias_vac = "26 dias"; break;   
    case 17: $dias_vac = "26 dias"; break;   
    case 18: $dias_vac = "26 dias"; break;   
    case 19: $dias_vac = "26 dias"; break;   
    case 20: $dias_vac = "26 dias"; break;   
    case 21: $dias_vac = "28 dias"; break;   
    case 22: $dias_vac = "28 dias"; break;   
    case 23: $dias_vac = "28 dias"; break;   
    case 24: $dias_vac = "28 dias"; break;   
    case 25: $dias_vac = "28 dias"; break;   
    case 26: $dias_vac = "30 dias"; break;   
    case 27: $dias_vac = "30 dias"; break;   
    case 28: $dias_vac = "30 dias"; break;   
    case 29: $dias_vac = "30 dias"; break;   
    case 30: $dias_vac = "30 dias"; break;   
    case 31: $dias_vac = "32 dias"; break;   
    case 32: $dias_vac = "32 dias"; break;   
    case 33: $dias_vac = "32 dias"; break;   
    case 34: $dias_vac = "32 dias"; break;   
    case 35: $dias_vac = "32 dias"; break;   
    }
    


// convertir fecha en letras
$eldiafecha = date('d', strtotime($fecha_baja));
$elmesfecha = date('m', strtotime($fecha_baja));
$elaniofecha = date('Y', strtotime($fecha_baja));

// convertir fecha en letras
$eldiafecha2 = date('d', strtotime($fecha_antiguedad));
$elmesfecha2 = date('m', strtotime($fecha_antiguedad));
$elaniofecha2 = date('Y', strtotime($fecha_antiguedad));

switch ($elmesfecha) {
case '01':  $elmes_fecha = "enero";      break;     
case '02':  $elmes_fecha = "febrero";    break;    
case '03':  $elmes_fecha = "marzo";      break;    
case '04':  $elmes_fecha = "abril";      break;    
case '05':  $elmes_fecha = "mayo";       break;    
case '06':  $elmes_fecha = "junio";      break;    
case '07':  $elmes_fecha = "julio";      break;    
case '08':  $elmes_fecha = "agosto";     break;    
case '09':  $elmes_fecha = "septiembre"; break;    
case '10': $elmes_fecha = "octubre";    break;    
case '11': $elmes_fecha = "noviembre";  break;    
case '12': $elmes_fecha = "diciembre";  break;   
}
switch ($elmesfecha2) {
    case '01':  $elmes_fecha2 = "enero";      break;     
    case '02':  $elmes_fecha2 = "febrero";    break;    
    case '03':  $elmes_fecha2 = "marzo";      break;    
    case '04':  $elmes_fecha2 = "abril";      break;    
    case '05':  $elmes_fecha2 = "mayo";       break;    
    case '06':  $elmes_fecha2 = "junio";      break;    
    case '07':  $elmes_fecha2 = "julio";      break;    
    case '08':  $elmes_fecha2 = "agosto";     break;    
    case '09':  $elmes_fecha2 = "septiembre"; break;    
    case '10': $elmes_fecha2 = "octubre";    break;    
    case '11': $elmes_fecha2 = "noviembre";  break;    
    case '12': $elmes_fecha2 = "diciembre";  break;   
    }
    

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
            margin: 80px 80px 80px 80px !important;
            padding: 5px 5px 5px 5px !important;
}
</style>
<body>
<br clear="ALL"/>
<p align="center"><strong>CONVENIO</strong></p>

<p  class="justificar">Con fundamento en los artículos 123, apartado A, fracción XXVII, inciso h) párrafo segundo, de la constitución política de los Estados Unidos Mexicanos; articulo 33y 684-E de la Ley Federal del Trabajo, se celebra  el presente convenio por una parte <strong>'.$el_empleado.'</strong> quien en lo subsecuente se denominara <strong>“TRABAJADOR”</strong>, y por otro lado <b>IMPULSORA SAHUAYO, S.A DE C.V</b>, quien en lo sucesivo se le denominara <strong>EMPLEADORA</strong>, representada por <strong>'.strtoupper($IDempleadoF).'</strong> a quienes en lo sucesivo de forma conjunta se les denominara <strong>“PARTES”</strong>, quienes se someten y obligan en los términos de las siguientes declaraciones y clausulas:</p>
&nbsp; <br>
<p align="center"><strong>D E C L A R A C I O N E S</strong>
<p  class="justificar"><strong>PRIMERA</strong>. - La parte <strong>TRABAJADORA</strong> se identifica con credencial de elector expedida a su favor por el Instituto Nacional Electoral, por ser persona mayor de edad por lo que tiene plenas capacidades de goce y ejercicio para convenir o transigir.</p>
<p  class="justificar"><strong>SEGUNDA</strong>. -  Declara '.$IDempleadoF.'  quien se identifica con credencial de elector expedida a su favor por el Instituto Nacional Electoral, y con fundamento en el art. 11 de la Ley Federal del Trabajo, en representación de <strong>IMPULSORA SAHUAYO S.A DE C.V</strong>, cuenta con facultades suficientes para convenir a nombre de su representada.</p>
<p  class="justificar"><strong>TERCERA</strong>. -  Declara la parte <strong>TRABAJADORA</strong>: <br>
A) Que fue contratada por la parte <strong>EMPLEADORA</strong> desde el <strong>'.$eldiafecha2.' del '.$elmes_fecha2.' de '.$elaniofecha2.'</strong> para prestar sus servicios como  <strong>'.$row_contratos['denominacion'].'</strong>, puesto que desempeño hasta el día <strong>'.$eldiafecha.' del '.$elmes_fecha.' de '.$elaniofecha.'</strong>.<br>
B) Que por el desempeño de sus labores contaba con las siguientes prestaciones:<br>
- Salario mensual: <strong>'.$sueldo_mensual.'</strong>.<br>
- Días de descanso: <strong>1</strong>.<br>
- Vacaciones: <strong>'.$dias_vac.'</strong>.<br>
- Aguinaldo: <strong>15 días al año</strong>.<br>
c) Que desempeñaba sus actividades laborales en las siguientes condiciones</strong>.<br>
- Horario: <strong>'.$horario1.'</strong>.<br>
- Horario de comida: <strong> '.$horario2.', fuera de las instalaciones</strong>.<br>
- Domicilio donde prestaba sus servicios: <strong>Calle Juan N. Alvarez No. 108 y 110, Renacimiento, Acapulco de Juarez, Guerrero</strong>.<br>
</p>
<p  class="justificar"><strong>CUARTA</strong>. - Declara la parte <strong>EMPLEADORA</strong>:<br>
A) Que la parte <strong>TRABAJADORA</strong> fue contratada en los términos señalados en la declaración inmediata anterior.</p>
<p  class="justificar"><strong>QUINTA</strong>. - Declaran las <strong>PARTES</strong>:<br>
A) Que el presente convenio se celebra con la finalidad de dar por terminada la relación laboral.</p>
&nbsp; <br>
<p align="center"><strong>C L A U S U L A S</strong>
<p  class="justificar"><strong>PRIMERA</strong>. - Las partes han determinado que por así convenir a sus intereses dan por concluida la relación laboral por mutuo acuerdo, conforme a lo estipulado por el articulo 53, fracciones I y V en relación con la fracción I del artículo 434, ambos de la Ley Federal del Trabajo. Lo anterior como consecuencia de las causas de fuerza mayor derivadas del huracán OTIS, toda vez que no existen las condiciones propicias para continuar con las labores y actividades del centro de trabajo. </p>
<p  class="justificar"><strong>SEGUNDA</strong>. - La parte <strong>TRABAJADORA</strong>, manifiesta bajo protesta de decir verdad, que el vínculo laboral lo mantuvo exclusivamente con la parte EMPLEADORA, por lo anterior expresa que no existió relación laboral alguna con otras personas, incluyendo el personal que fungía como superior jerárquico en el centro de trabajo donde la parte TRABAJADORA desempeñaba sus funciones.</p>
<p  class="justificar"><strong>TERCERA</strong>. -  La empleadora otorga a favor de la <strong>TRABAJADORA</strong>, el pago acordado conforme las disposiciones de la Ley Federal del Trabajo y respetando los derechos consagrados en el mismo ordenamiento legal. Asimismo, la TRABAJADORA manifiesta su entera conformidad y la aceptación de este.</p>
&nbsp; <br>
<p  class="justificar"><strong>CUARTA</strong>. - La parte trabajadora manifiesta que durante el tiempo que laboro para la parte <strong>EMPLEADORA</strong>, se cubrió en tiempo y forma el pago de su salario; cada una de las prestaciones ordinarias y extraordinarias en especie que conforme a derecho le corresponden, así mismo como cualquier riesgo o accidente de trabajo que haya sufrido. Por lo anterior, la parte empleadora no adeuda pago de concepto alguno.</p>
<p  class="justificar"><strong>QUINTA</strong>. La <strong>TRABAJADORA</strong> recibirá por parte de la <strong>EMPLEADORA</strong> la cantidad neta de '.$monto.' ('.$sueldo_diario_enletras.'), conforme a los conceptos legales establecidos y detallados en el documento que se anexa al presente convenio, el cual contiene el detalle de las percepciones que en este acto recibe por la conclusión de los servicios prestados, por lo que, con la firma del presente convenio extiende el finiquito más amplio que en derecho proceda, no reservándose acción o derecho alguno que ejercitar en el futuro en contra de la <strong>EMPLEADORA</strong> o cualesquiera de sus empresas filiales y subsidiarias, o de cualquiera de sus funcionarios, respecto de la relación laborar que ahora se da por terminada.</p>
<p  class="justificar"><strong>SEXTA</strong>. - En caso de que la <strong>EMPLEADORA</strong> no cubra el pago de la cantidad, deberá pagar a la <strong>TRABAJADORA</strong> el equivalente a un día de salario, el cual se fijara en razón del salario que perciba dicha parte antes de terminar la relación de trabajo.</p>
<p  class="justificar"><strong>SEPTIMA</strong>. - Las partes manifiestan que es su voluntad ratificar el presente convenio en todas y cada una de sus partes y la aprobación de su contenido, por lo que no se reservan acción legal o derecho alguno para ejercitar con posterioridad la firma del presente convenio.</p>
<p  class="justificar"><strong>OCTAVA</strong>. - Las <strong>PARTES</strong> manifiestan que en la celebración del presente convenio no existió violencia, mala fe, dolo, lesión o cualquier otro tipo de vicio del consentimiento que pudiera nulificarlo.</p>
<p  class="justificar">Enteradas las partes del alcance del presente convenio se firma en Acapulco, Guerrero a los '.$eldiafecha.'dias del '.$elmes_fecha.' de '.$elaniofecha.'.</p>

&nbsp; <br>
&nbsp; <br>
&nbsp; <br>

<table border="0" cellspacing="0" cellpadding="0" width="80%" align="center">
    <tr>
      <td width="25%" valign="top"><p align="center">
 	    <strong>___________________________</strong><br>
 	    <strong>'.strtoupper($IDempleadoF).'<br>
        LA PARTE EMPLEADORA</strong><br>
		</td>
      <td width="13%" valign="top"><p align="center">
      <p align="center" class="blanco"> _______</td>
      <td width="25%" valign="top"><p align="center">
        <strong>___________________________</strong><br>
        <strong>C. ' .$el_empleado.'<br>
        LA PARTE TRABAJADORA</strong><br>
      </td>
	  </td>
</table>
<br clear="ALL"/>

<div class="saltopagina"></div>

<br clear="ALL"/>

<p align="right">Acapulco, Guerrero a los '.$eldiafecha.' dias del '.$elmes_fecha.' de '.$elaniofecha.'.</p>
&nbsp; <br>
<p><strong>IMPULSORA SAHUAYO S.A DE C.V</strong></p>
<p><strong>P R E S E N T E.</strong></p>
&nbsp; <br>

<p class="justificar"><strong>'.$el_empleado.'</strong>, por propio derecho, deseo manifestar que por así convenir a mis intereses y de manera voluntaria, con esta fecha doy por terminada la relación de trabajo que me unía con <strong>IMPULSORA SAHUAYO S.A DE C.V</strong>, empresa en la cual me desempeñe con el puesto de <strong>'.$row_contratos['denominacion'].'</strong>, relación que inicio desde el día '.$eldiafecha2.' del '.$elmes_fecha2.' de '.$elaniofecha2.' y que de manera formal hoy doy por terminada.</p>
&nbsp; <br>

<p class="justificar">Aprovecho la ocasión para agradecer muy cumplidamente las atenciones que me dispensaron y manifiesto que durante el tiempo que presté mis servicios a la empresa siempre me fueron cubiertas oportunamente todas y cada una de las prestaciones a que tuve derecho conforme a mi contrato individual de trabajo y a la ley, tales como vacaciones, prima vacacional, aguinaldo, utilidades, etc. Expreso de igual manera que durante dicho tiempo no sufrí enfermedad o accidente de trabajo alguno.</p>
&nbsp; <br>

<p class="justificar">Asimismo, dejo constancia para todos los efectos legales a que haya lugar, que nunca laboré tiempo extraordinario alguno, ni séptimos días, ni días festivos de descanso obligatorio, por lo que extiendo el más amplio finiquito que en derecho proceda a favor de mi único patrón <strong>IMPULSORA SAHUAYO S.A DE C.V</strong>, no me reservo acción ni derecho que ejercitar en su contra.</p>
&nbsp; <br>

<p align="center"><strong>A T E N T A M E N T E.</strong></p>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
<p align="center">________________________________ </p>
<p align="center"><strong>'.$el_empleado.'</strong></p>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
<p>RATIFICO DE CONFORMIDAD</p>

<div class="saltopagina"></div>
<br clear="ALL"/>

&nbsp; <br>
&nbsp; <br>
<p><strong><strong>IMPULSORA SAHUAYO S.A DE C.V</strong></strong></p>
<p><strong>P R E S E N T E.</strong></p>
&nbsp; <br>

<p class="justificar"><strong>'.$el_empleado.'</strong>, por propio derecho, deseo manifestar que por así convenir a mis intereses y de manera voluntaria, con esta fecha doy por terminada la relación de trabajo que me unía con <strong>IMPULSORA SAHUAYO S.A DE C.V</strong>, empresa en la cual me desempeñe con el puesto de <strong>'.$row_contratos['denominacion'].'</strong>, relación que inicio desde el día '.$eldiafecha2.' del '.$elmes_fecha2.' de '.$elaniofecha2.' y que de manera formal hoy doy por terminada.</p>
&nbsp; <br>

<p class="justificar">Aprovecho la ocasión para agradecer muy cumplidamente las atenciones que me dispensaron y manifiesto que durante el tiempo que presté mis servicios a la empresa siempre me fueron cubiertas oportunamente todas y cada una de las prestaciones a que tuve derecho conforme a mi contrato individual de trabajo y a la ley, tales como vacaciones, prima vacacional, aguinaldo, utilidades, etc. Expreso de igual manera que durante dicho tiempo no sufrí enfermedad o accidente de trabajo alguno.</p>
&nbsp; <br>

<p class="justificar">Asimismo, dejo constancia para todos los efectos legales a que haya lugar, que nunca laboré tiempo extraordinario alguno, ni séptimos días, ni días festivos de descanso obligatorio, por lo que extiendo el más amplio finiquito que en derecho proceda a favor de mi único patrón <strong>IMPULSORA SAHUAYO S.A DE C.V</strong>, no me reservo acción ni derecho que ejercitar en su contra.</p>
&nbsp; <br>

<p align="center"><strong>A T E N T A M E N T E.</strong></p>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
<p align="center">________________________________ </p>
<p align="center"><strong>'.$el_empleado.'</strong></p>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
<p>RATIFICO DE CONFORMIDAD</p></body></html>';
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
$nombreDelDocumento = "FORMATOS ".date('dmY')." ".$IDempleado.".pdf";
$bytes = file_put_contents("CONTS/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
?>