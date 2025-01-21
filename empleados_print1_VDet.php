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
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT con_empleados.*, con_bancos.banco, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion,  vac_matriz.firma_contratos, vac_puestos.denominacion FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto LEFT JOIN con_bancos ON con_empleados.a_banco = con_bancos.IDbanco WHERE con_empleados.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$elbanco =  $row_contratos['banco'];

mysql_select_db($database_vacantes, $vacantes);
$query_beneficiarios = "SELECT *, con_parentesco.parentesco FROM con_dependientes LEFT JOIN con_parentesco ON con_dependientes.IDtipo = con_parentesco.IDparentesco WHERE con_dependientes.IDempleado = $IDempleado AND con_dependientes.emergencias in(1,3)";
$beneficiarios = mysql_query($query_beneficiarios, $vacantes) or die(mysql_error());
$row_beneficiarios = mysql_fetch_assoc($beneficiarios);
$totalRows_beneficiarios = mysql_num_rows($beneficiarios);

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


// convertir sueldo en letras
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

$sueldo_diario = "$".number_format($row_contratos['b_sueldo_diario'],2);
$sueldo_diario_enletras = num2letras($row_contratos['b_sueldo_diario']);

$tipo_nomina = $row_contratos['tipo_nomina'];
if ($tipo_nomina == 1){$tipo_nomina = "los <strong>sábados</strong>, de cada semana laboral vencida";} else {$tipo_nomina = "de forma quincenal, a quincena vencida";}

$el_empleado = $row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre'];
$estado =  $row_contratos['matriz'];
$ubicacionfirma =  $row_contratos['matriz_cv'];
$direccion_empresa =  $row_contratos['direccion'];
$fecha_alta = date('d/m/Y', strtotime($row_contratos['fecha_alta']));
$timestp = date("dmYHm"); // la fecha actual
$enletras = str_replace(',','',$row_contratos['b_sueldo_mensual']); 
$en_letras = num2letras($enletras);
if ($row_contratos['IDempresa'] == 1) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';}
elseif ($row_contratos['IDempresa'] == 2) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';} 
elseif ($row_contratos['IDempresa'] == 3) {$empresa = 'PERINTO S.A. DE C.V.';}
else{ $empresa = 'SIN DETERMINAR';}
 
if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$rep_legal = $row_contratos['firma_contratos'];} else {$rep_legal = 'Alejandro Barrios Uribe';} 
//$rep_legal = $row_variables['rep_legal'];
if($row_contratos['IDnacionalidad'] == 1) {$nacionalidad = "Mexicana";} else {$nacionalidad = "Extranjera";}
if($row_contratos['a_sexo'] == 1) {$sexo = "Masculino";} else {$sexo = "Femenino";}
if($row_contratos['a_estado_civil'] == 1) {$edo_civil = "Soltero";} else {$edo_civil = "Casado";}
$IDestado = $row_contratos['IDestado'];
if($row_contratos['a_banco'] == 2) {$cuenta_bancaria = "________________________";} else {$cuenta_bancaria = $row_contratos['a_cuenta_bancaria'];}

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
table {
    border-collapse: collapse;
}
</style>
<body>
<br clear="ALL"/>
<p align="center"><strong>CONTRATO INDIVIDUAL DE TRABAJO</strong></p>
<p class="justificar"> En '.$ubicacionfirma.', a <strong>'.$ini_cont_d.' de '.$ini_cont_m.' de '.$ini_cont_y.'</strong>, los que suscribimos el presente contrato a saber <strong>'.$empresa.'</strong> Representada por <strong>'.$rep_legal.'</strong>, por la otra <strong>'.$el_empleado.'</strong> por sus propios derechos con domicilios respectivos en <strong>'.$direccion_empresa.'</strong> y en <strong>Calle '.$row_contratos['d_calle'].', '.$row_contratos['d_numero_calle'].', Colonia '.$row_contratos['d_colonia'].', C.P. '.$row_contratos['d_codigo_postal'].', '.$row_contratos['d_delegacion_municipio'].', '.$row_contratos['d_estado'].',</strong> hacemos constar que hemos convenido celebrar un contrato individual de  trabajo al tenor de las siguientes:</p>

<p align="center"><strong>CLAUSULAS</strong></p>

<p class="justificar"><strong>Primera.- </strong> Los contratantes se reconocen expresamente la personalidad jurídica conque se ostentan para todos los efectos legales a que haya lugar y convienen que en el cuerpo del presente contrato en lo sucesivo se  denominaran respectivamente Empresa y Trabajador y cuando las partes se refieran a la Ley Federal del Trabajo, solo se utilizara la palabra LEY.</p>

<p class="justificar"><strong>Segunda.-</strong> El Trabajador manifiesta bajo protesta de decir verdad que tiene la capacidad física, mental y legal así como los conocimientos necesarios para desempe&#241;ar el trabajo estipulado y para tal efecto declara ser de nacionalidad <strong>'.$nacionalidad.'</strong>, fecha de nacimiento <strong>'.$nac_cont_d.' de '.$nac_cont_m.' de '.$nac_cont_y.'</strong>, RFC <strong>'.$row_contratos['a_rfc'].',</strong> CURP <strong>'.$row_contratos['a_curp'].',</strong> NSS <strong>'.$row_contratos['a_imss'].',</strong> teléfono <strong>'.$row_contratos['telefono_1'].', </strong>estado civil <strong> '.$edo_civil.'</strong>, edad <strong>'.$periodo_d.' a&#241;os</strong> y sexo <strong>'.$sexo.'</strong>.
</p>

<p class="justificar"><strong>Tercera.-</strong> La empresa por su parte manifiesta estar legalmente constituida conforme a las leyes mexicanas y tener facultades legales y la capacidad necesaria para la celebración del presente contrato de trabajo.</p>

<p class="justificar"><strong>Cuarta.-</strong> Ambas partes convienen que el presente contrato se celebra por <strong>tiempo determinado de 45 días</strong> a partir de la fecha de firma del presente contrato, tiempo en el cual el trabajador debe demostrar las facultades, habilidades, conocimientos y experiencia requeridas para el puesto al que se le está contratando; termino durante el cual, la empresa podrá rescindir el contrato sin responsabilidad para esta, siempre en los casos y condiciones especificadas en la LEY en vigor, así como en las mencionadas en el cuerpo del presente contrato, de conformidad con el artículo 53 Fracción III de la LEY.</p>

<p class="justificar"><strong>Quinta.-</strong> El Trabajador se obliga a prestar sus servicios personales y subordinados a la empresa bajo su dirección, dependencia y subordinación, los cuales consistirán en forma enunciativa pero no limitativa desempeñando el puesto de <strong>'.$row_contratos['denominacion'].'</strong>.</p>

<p class="justificar">Este trabajo deberá ejecutarlo con esmero y eficiencia, pues queda expresamente convenido que el trabajador acatará en el desempeño de sus funciones, todas las disposiciones, órdenes, circulares que dicte la empresa y todos los ordenamientos como el Reglamento Interior de Trabajo que ya conoce y está de acuerdo con su contenido, y demás disposiciones legales que le sean aplicables por LEY.</p>

<p class="justificar"><strong>Sexta.-</strong> El Trabajador y la Empresa convienen en que ésta podrá cambiar al Trabajador de plaza, lugar o actividad, aun y cuando este tenga que desempeñarse en otro turno u horario, siempre y cuando se le respete para todos los efectos legales, su categoría y salario; así mismo, el Trabajador se compromete a ejecutar sus labores en las oficina o local de la empresa, en cualquier lugar donde éstas se encuentran o donde la empresa desempeñe su objeto social. </p>

<p class="justificar"><strong>Séptima.-</strong> El Trabajador percibirá por la prestación de los servicios a que se refiere este contrato, un salario diario de <strong>'.$sueldo_diario.'</strong> ('.$sueldo_diario_enletras.'). El salario se le cubrirá '.$tipo_nomina.', en moneda de curso legal o a través de los sistemas electrónicos de pago y en las oficinas de la empresa, estando obligado el trabajador a firmar las constancias de pago respectivas, teniendo en cuenta lo dispuesto en los artículos 108 y 109 de dicha LEY. </p>

<p class="justificar"><strong>Octava.-</strong> El trabajador manifiesta que por motivos de seguridad solicita a la empresa que su salario y/o cualquier prestación en efectivo derivadas de la relación laboral le sean depositadas en la cuenta bancaria número <strong>'.$cuenta_bancaria.'</strong> del Banco <strong>'.$elbanco.'</strong> que se encuentra a su nombre del cual dispondrá libremente de acuerdo al contrato de depósito que tiene celebrado con dicha institución y no contraviniendo con este hecho a lo dispuesto por el artículo 101 de la Ley. </p>





<p class="justificar"><strong>Novena.- </strong>Las comisiones devengadas por el vendedor no podrán detenerse, ni descontarse, si posteriormente se deja sin efecto la operación que les sirvió de base, con arreglo a lo dispuesto en el artículo 288 de la Ley Federal del Trabajo, ni podrán ser deducidas por el trabajador de la cobranza en su caso.</p>

<p class="justificar"><strong>Décima.- </strong>Queda convenido que la Comisión a que se refiere el presente contrato se determinará en base al porcentaje de cubrimiento y/o cumplimiento del objetivo de venta convenido en el anexo UNO del presente.</p>

<p class="justificar"><strong>Décima Primera.- </strong>El trabajador se obliga a entregar al Patrón toda la documentación necesaria para realizar la venta debidamente requisitada y firmada, en los plazos establecidos por al empresa, de lo contrario la empresa no estará obligada a cubrir el importe de la comisión respectiva por la(s) venta(s) realizada(s). Por venta realizada se entenderá todo proceso inicial, intermedio y final para lograr la cobranza completa del producto vendido por el trabajador y distribuido por la empresa.</p>

<p class="justificar"><strong>Décima Segunda.- </strong>Para que el trabajador haga descuentos o bonificaciones mayores que los indicados para las ventas, necesitará autorización especial de la empresa y se sujetará a las instrucciones dadas  previas y por escrito por el mismo.</p>

<p class="justificar">La empresa no reconocerá ningún arreglo extraordinario que el vendedor haga con el cliente, que no esté autorizado por escrito por la empresa y que no esté anotado en el pedido (como descuentos, garantías, etc.), y, en caso contrario, los gastos y diferencias resultantes serán a cargo del propio trabajador las que desde ahora manifiesta su consentimiento para que sean deducidas de las comisiones devengadas por ventas pasadas, presentes o futuras al momento de la deducción respectiva, respetando los porcentajes establecidos por el artículo 110 de la Ley Federal del Trabajo.</p>

<p class="justificar"><strong>Décima Tercera.- </strong>Queda expresamente convenido que el Trabajador no podrá dedicarse (ni aun a título gratuito y mucho menos onerosa a la venta de ningún otro producto de ninguna otra empresa durante su jornada de trabajo, y que, fuera de su jornada de trabajo, le estará estrictamente prohibido dedicarse a cualquier labor que signifique en cualquier forma una competencia para las actividades que el patrón realiza o tiene por objeto, de lo contrario será considerado como una causal especial de rescisión.</p>








<p class="justificar"><strong>Décima Cuarta.-</strong> Las partes convienen expresamente en que el salario estipulado en la clausula octava se encuentra incluido en pago correspondiente al <strong>séptimo día.</strong></p>

<p class="justificar"><strong>Décima Quinta.- </strong> La duración de la jornada será fijada y modificada conforme a las necesidades y requerimientos de la Empresa, por lo que desde este momento el Trabajador otorga su consentimiento a efecto de que la Empresa determine libremente el horario, el turno, el puesto y el lugar en los cuales el Trabajador ha de desempeñar sus funciones, manifestando desde este momento su conformidad con los mismos.</p>


<p class="justificar"> La jornada diurna será de ocho horas, de siete la nocturna y de siete y media la mixta, en la inteligencia que podrá ser discontinua o continua y si fuere éste el caso, disfrutará el Trabajador de media hora de descanso al intermedio.</p>

<p class="justificar"> Se le concederá al Trabajador tiempo necesario para que tome sus alimentos, en la inteligencia de que dicho tiempo, nunca será menor de treinta minutos    y éste no será considerado como tiempo efectivo dentro de la jornada laboral, por lo que quedará a elección de el Trabajador permanecer o no dentro de las instalaciones de la Empresa durante el período de descanso.</p>

<p class="justificar"><strong>Décima Sexta.-</strong> Cuando por circunstancias extraordinarias el trabajador tenga que prestar sus servicios a la empresa aumentándose su jornada de trabajo se&#241;alada en la clausula octava del presente contrato, se le pagara de conformidad a lo establecido por el articulo 67 y 68 de la LEY, quedando prohibido expresamente que el trabajador labore TIEMPO EXTRAORDINARIO, salvo que tenga permiso previo y por escrito de la empresa a través de sus representantes legarles y/o patronal o del Jefe Inmediato del trabajador. </p>

<p class="justificar"> <strong>Décima Séptima.- </strong> El trabajador tendrá un día de descanso por cada seis días de trabajo, conviniéndose en que dicho día será el domingo de cada semana, en la inteligencia de que si el trabajador llegase a laborar el domingo tendrá derecho a que se le pague una prima de un 25% sobre su salario base.</p>

<p class="justificar"><strong>Décima Octava.-</strong> El trabajador está obligado en su caso dado la naturaleza del puesto y/o actividad a checar su tarjeta o firmar las listas de asistencia, a la entrada y salida de sus labores, por lo que el incumplimiento de ese requisito indicará la falta injustificada a sus labores para todos los efectos legales.</p>

<p class="justificar"><strong>Décima Novena.-</strong> Son días de descanso obligatorio los se&#241;alados por el artículo 74 de la Ley Federal del Trabajo, en el entendido que si llegara a ser necesario que el trabajador labore en cualquiera de los días mencionados por dicho precepto legal, éste estará obligado en términos de lo dispuesto  por el artículo 75 de la ley y será cubierto en los términos del mismo, previa autorización por escrito que la empresa otorgue al Trabajador por escrito.</p>

<p class="justificar"> <strong>Vigésima.-</strong> Cuando el trabajador cumpla un a&#241;o de servicio prestados para la empresa, disfrutara de un periodo anual de vacaciones pagadas a salario
base, de conformidad con lo establecido por el artículo 76 de la LEY. El trabajador recibirá por concepto de prima vacacional el 25% conforme a lo establecido en la LEY. Las vacaciones no podrán compensarse con una remuneración.</p>

<p class="justificar">Si la relación de trabajo termina antes de que se cumpla el a&#241;o de servicio, el trabajador tendrá derecho a una remuneración proporcional al tiempo de servicio prestado por las vacaciones en mención.</p>

<p class="justificar"><strong>Vigésima Primera.-</strong> El trabajador percibirá un aguinaldo anual, que deberá pagársele antes del día 20 de diciembre de cada a&#241;o equivalente a 15 días de salario.</p>

<p class="justificar">Cuando no haya cumplido el a&#241;o de servicios, tendrá derecho a que se le pague en proporción al tiempo trabajado. </p>

<p class="justificar"><strong>Vigésima Segunda.-</strong> El trabajador conviene en someterse a los reconocimientos médicos que periódicamente ordene el patrón en los términos de la fracción X del artículo 134 de la Ley Federal del Trabajo; en la inteligencia de que el médico que los practique será designado y retribuido por el patrón.</p>

<p class="justificar"><strong>Vigésima Tercera.-</strong> El trabajador se compromete a sujetarse a los cursos de capacitación y adiestramiento a que se refieren los Artículos 153-a al 153-x de la Ley Federal de Trabajo.</p>

<p class="justificar"> <strong>Vigésima Cuarta.-</strong> Ambas partes declaran que conocen las obligaciones que les impone respectivamente a la empresa el Artículo 132 y al trabajador el Artículo 134 de la Ley Federal de Trabajo, así mismo que conocen la importancia de los artículos 47 y 51 respecto de las causales de rescisión de la relación de trabajo, sin responsabilidad para la empresa y el trabajador.</p>

<p class="justificar"><strong>Vigésima Quinta.- </strong> Cuando el trabajador por circunstancias extraordinarias no imputables al mismo no pudiera llagar puntualmente al inicio de sus labores señaladas en este contrato, la empresa está conforme en tolerar como máximo dos retardos a la semana de un periodo máximo de 10 minutos, de lo contrario y para los efectos legales a que haya lugar, se computara como falta injustificada, sin goce de sueldo, para lo cual el trabajador manifiesta su conocimiento y consentimiento.</p>

<p class="justificar"><strong>Vigésima Sexta.-</strong> El trabajador faculta expresamente a la empresa para que le descuente oportunamente de su salario las cuotas para el pago de las aportaciones al Instituto Mexicano del Seguro Social y en caso de adquirir un crédito ante el INFONAVIT y/o INFONACOT los pagos correspondientes a dicho crédito. </p>

<p class="justificar">Así mismo el trabajador faculta a la empresa a descontar el pago correspondiente al crédito otorgado por el INFONAVIT y/o INFONACOT en el finiquito cuando este se retire de la empresa. </p> 


<p class="justificar"><strong>Vigésima Séptima.- DE LA DESIGNACIÓN DE BENEFICIARIOS.-</strong> El trabajador de manera libre y espontánea, dando cumplimiento con lo establecido en el artículo 25 fracción  X de la Ley Federal del Trabajo, designa para el pago de salarios y prestaciones devengadas en caso de fallecimiento, al siguiente (s) beneficiarios y en los respectivos porcentajes: </p>

<table border="1" width="100%">
<tr>
<td width="25%"><p align="center">PARENTESCO</p></td>
<td width="45%"><p align="center">NOMBRE</p></td>
<td width="10%"><p align="center">PORCENTAJE</p></td>
</tr>';
$body .= '<tbody>';
do {
$body .= '<tr>';
$body .= '<td><p align="center">';
$body .=  $row_beneficiarios['parentesco'];
$body .= '</p></td>';
$body .= '<td><p align="center">';
$body .=  $row_beneficiarios['nombre'];
$body .= '</p></td>';
$body .= '<td><p align="center">';
$body .=  $row_beneficiarios['observaciones'];
$body .= '%</p></td>';
} while ($row_beneficiarios = mysql_fetch_assoc($beneficiarios));
$body .= '</tr>
</tbody>
</table>

<p class="justificar"><strong>Vigésima Octava.-</strong> El trabajador está obligado a informar y entregar a la empresa cualquier documento emitido y proporcionado por el Instituto Mexicano del Seguro Social con todo lo relacionado a Riesgos Trabajo e Incapacidades. </p>

<p class="justificar"><strong>Vigésima Novenda.-</strong> Las partes convienen en que todo lo que no previsto en el presente contrato se regirá por lo dispuesto en la Ley Federal del Trabajo y que para la interpretación y cumplimiento se someten expresamente a la jurisdicción y competencia de la Autoridad Laboral Competente en turno y de la Entidad Federativa que corresponda al domicilio de su centro de trabajo. </p> 

<p class="justificar">Leído que fue el presente contrato por las partes ante los testigos que también firman y sabedores de su contenido, lo firman por duplicado quedando un tanto en poder de cada una de las partes.

<table border="0" cellspacing="0" cellpadding="0" width="60%" align="center">
    <tr>
      <td width="45%" valign="top"><p align="center">
        <strong>&quot;EL TRABAJADOR&quot;</strong> <br>
        &nbsp; <br>
        &nbsp; <br>
        &nbsp; <br>
        &nbsp; <br>
        <strong>___________________________</strong><br>
        <strong>C. ' .$row_contratos['a_nombre'].' '.$row_contratos['a_paterno'].' '.$row_contratos['a_materno'].'</strong><br>
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
</table>
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
$nombreDelDocumento = "CONTRATO DETERMINADO ".date('dmY')." ".$IDempleado.".pdf";
//$nombreDelDocumento = $timestp." CONTRATO.pdf";
$bytes = file_put_contents("CONTS/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
?>