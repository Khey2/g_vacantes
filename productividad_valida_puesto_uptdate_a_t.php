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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$la_captura = $_GET['IDcaptura'];

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");

//borrar duplicados
mysql_select_db($database_vacantes, $vacantes);
$query_duplicado = "SELECT * FROM prod_captura WHERE IDcaptura = $la_captura";
$duplicado = mysql_query($query_duplicado, $vacantes) or die(mysql_error());
$row_duplicado = mysql_fetch_assoc($duplicado);
$repe_usuario = $row_duplicado['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_duplicado2 = "SELECT Count(prod_captura.IDempleado) as REPETIDO FROM prod_captura WHERE IDempleado = '$repe_usuario' AND semana= '$semana' AND anio = '$anio'";
$duplicado2 = mysql_query($query_duplicado2, $vacantes) or die(mysql_error());
$row_duplicado2 = mysql_fetch_assoc($duplicado2);

if ($row_duplicado2['REPETIDO'] > 1) {
   // borrado
   $query1 = "UPDATE prod_captura SET semana = '0' WHERE IDempleado = $repe_usuario AND semana = '$semana' AND IDcaptura <> '$la_captura' AND anio = '$anio'"; 
   $resultado = mysql_query($query1) or die(mysql_error());  
}


mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT prod_activos.IDnivel_antiguedad, prod_activos.IDnivel_caja, prod_captura.horas_extra, prod_captura.horas_extra_monto, prod_captura.IDturno, prod_captura.a29, prod_activos.IDempleado, prod_captura.IDcaptura, prod_captura.IDempleado, prod_captura.IDpuesto, prod_captura.fecha_captura, prod_captura.semana, prod_captura.adicional, prod_captura.bono_asistencia, prod_captura.IDmatriz, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.a8, prod_captura.a9, prod_captura.a10, prod_captura.a11, prod_captura.a12, prod_captura.a13, prod_captura.a14, prod_captura.a15, prod_captura.a16, prod_captura.a17, prod_captura.a18, prod_captura.a19, prod_captura.a20, prod_captura.a21, prod_captura.a22, prod_captura.a23, prod_captura.a24, prod_captura.a25, prod_captura.a26, prod_captura.a27, prod_captura.a28, prod_captura.garantizado, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.lun_g, prod_captura.mar_g, prod_captura.mie_g, prod_captura.jue_g, prod_captura.vie_g, prod_captura.sab_g, prod_captura.dom, prod_captura.pago, prod_captura.pago_total, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.fecha_antiguedad, prod_activos.fecha_nacimiento, prod_activos.sueldo_mensual, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.sueldo_diario, prod_activos.sobre_sueldo, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.descripcion_nomina, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM prod_captura LEFT JOIN prod_activos ON prod_captura.IDempleado = prod_activos.IDempleado WHERE prod_captura.IDcaptura = '$la_captura'";
mysql_query("SET NAMES 'utf8'");
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);
$su_matriz = $row_resultado['IDmatriz'];
$IDturno = $row_resultado['IDturno'];

$IDnivel_antiguedad = $row_resultado['IDnivel_antiguedad'];
$IDnivel_caja = $row_resultado['IDnivel_caja'];
$query_montos_cajas = "SELECT * FROM prod_valor_cajas WHERE IDmatriz = $su_matriz AND IDnivel = '$IDnivel_caja'";
$montos_cajas = mysql_query($query_montos_cajas, $vacantes) or die(mysql_error());
$row_montos_cajas = mysql_fetch_assoc($montos_cajas);
$totalRows_montos_cajas = mysql_num_rows($montos_cajas);

//puestos aplicables
$el_puesto = $row_resultado['IDpuesto'];
$IDcaptura = $row_resultado['IDcaptura'];
$la_sucursal = $row_resultado['IDsucursal'];

//garantia
mysql_select_db($database_vacantes, $vacantes);
$query_garantia = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $su_matriz AND IDnivel = '$IDnivel_caja'";
$garantia = mysql_query($query_garantia, $vacantes) or die(mysql_error());
$row_garantia = mysql_fetch_assoc($garantia);
$totalRows_garantia = mysql_num_rows($garantia);

$aplica_asistencia = $row_garantia['asistencia'];
$monto_asistencias = $row_garantia['monto_asistencia'];

if ($row_resultado['garantizado'] == 10) { $monto_garantia = 50;} else {$monto_garantia = $row_garantia['garantia'];}

$monto_garantia_diario = $row_garantia['garantia'];

$_res1 = $row_resultado['a1'];
$_res2 = $row_resultado['a2'];
$_res3 = $row_resultado['a3'];
$_res4 = $row_resultado['a4'];
$_res5 = $row_resultado['a5'];
$_res6 = $row_resultado['a6'];
$_res7 = $row_resultado['a7'];
$_res8 = $row_resultado['a8'];
$_res9 = $row_resultado['a9'];
$_res10 = $row_resultado['a10'];
$_res11 = $row_resultado['a11'];
$_res12 = $row_resultado['a12'];
$_res13 = $row_resultado['a13'];
$_res14 = $row_resultado['a14'];
$_res15 = $row_resultado['a15'];
$_res16 = $row_resultado['a16'];
$_res17 = $row_resultado['a17'];
$_res18 = $row_resultado['a18'];
$_res19 = $row_resultado['a19'];
$_res20 = $row_resultado['a20'];
$_res21 = $row_resultado['a21'];
$_res22 = $row_resultado['a22'];
$_res23 = $row_resultado['a23'];
$_res24 = $row_resultado['a24'];
$_res25 = $row_resultado['a25'];
$_res26 = $row_resultado['a26'];
$_res27 = $row_resultado['a27'];
$_res28 = $row_resultado['a28'];

// cajas minimas
$cuota_lun = 0;
$cuota_mar = 0;
$cuota_mie = 0;
$cuota_jue = 0;
$cuota_vie = 0;
$cuota_sab = 0;
$cuota_dom = 0;
 
// cajas minimas
mysql_select_db($database_vacantes, $vacantes);
$query_minimo = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$minimo = mysql_query($query_minimo, $vacantes) or die(mysql_error());
$row_minimo = mysql_fetch_assoc($minimo);
$minimos = $row_minimo['minimas'];

echo "Minimo Diario: " . $minimos . "</br></br>";

// productividad real
//$estib = 0.025;
//$otros = 0.10;
$monto_recibo = $row_montos_cajas['recibo'];
$monto_carga = $row_montos_cajas['carga'];
$monto_estiba = $row_montos_cajas['estiba'];
$monto_reparto = $row_montos_cajas['distribucion'];

//cajas cargadas estibadas y repartidas
$ellun =  $_res1   + $_res2   + $_res4;
$elmar =  $_res5   + $_res6   + $_res8;
$elmie =  $_res9   + $_res10  + $_res12;
$eljue =  $_res13  + $_res14  + $_res16;
$elvie =  $_res17  + $_res18  + $_res20;
$elsab =  $_res21  + $_res22  + $_res24;
$eldom =  $_res25  + $_res26  + $_res28;

echo "Cajas lun: " .$ellun . "</br>";	
echo "Cajas mar: " .$elmar . "</br>";	
echo "Cajas mie: " .$elmie . "</br>";	
echo "Cajas jue: " .$eljue . "</br>";	
echo "Cajas vie: " .$elvie . "</br>";	
echo "Cajas sab: " .$elsab . "</br>";	
echo "Cajas dom: " .$eldom . "</br></br>";	

// dias que cubre
if ($_res1  + $_res3  + $_res2  + $_res4  < $minimos) { $cuota_lun = 0;} else { $cuota_lun = 1;}
if ($_res5  + $_res7  + $_res6  + $_res8  < $minimos) { $cuota_mar = 0;} else { $cuota_mar = 1;}
if ($_res9  + $_res11 + $_res10 + $_res12 < $minimos) { $cuota_mie = 0;} else { $cuota_mie = 1;}
if ($_res13 + $_res15 + $_res14 + $_res16 < $minimos) { $cuota_jue = 0;} else { $cuota_jue = 1;}
if ($_res17 + $_res19 + $_res18 + $_res20 < $minimos) { $cuota_vie = 0;} else { $cuota_vie = 1;}
if ($_res21 + $_res23 + $_res22 + $_res24 < $minimos) { $cuota_sab = 0;} else { $cuota_sab = 1;}
if ($_res25 + $_res27 + $_res26 + $_res28 < $minimos) { $cuota_dom = 0;} else { $cuota_dom = 1;}

echo "Cuota lun: " . $cuota_lun . "</br>";
echo "Cuota mar: " . $cuota_mar . "</br>";
echo "Cuota mie: " . $cuota_mie . "</br>";
echo "Cuota jue: " . $cuota_jue . "</br>";
echo "Cuota vie: " . $cuota_vie . "</br>";
echo "Cuota sab: " . $cuota_sab . "</br>";
echo "Cuota dom: " . $cuota_dom . "</br></br>";

//dias sin cubrir
$cuota_t = $cuota_lun + $cuota_mar + $cuota_mie + $cuota_jue + $cuota_vie + $cuota_sab + $cuota_dom;
echo "Dias que si cubre: " . $cuota_t . "</br>";

//cajas estibadas
$eslun =  $_res3;
$esmar =  $_res7;
$esmie =  $_res11;
$esjue =  $_res15;
$esvie =  $_res19;
$essab =  $_res23;
$esdom =  $_res27;

//cajas cargadas estibadas y repartidas
$ellun =  $_res1   + $_res2   + $_res4;
$elmar =  $_res5   + $_res6   + $_res8;
$elmie =  $_res9   + $_res10  + $_res12;
$eljue =  $_res13  + $_res14  + $_res16;
$elvie =  $_res17  + $_res18  + $_res20;
$elsab =  $_res21  + $_res22  + $_res24;
$eldom =  $_res25  + $_res26  + $_res28;


if ($cuota_lun == 1) {$montolun = (($_res1  * $monto_recibo)/1000) +  (($_res2  * $monto_carga)/1000) +  (($_res3  * $monto_estiba)/1000) + (($_res4  * $monto_reparto)/1000); } else { $montolun = 0;}
if ($cuota_mar == 1) {$montomar = (($_res5  * $monto_recibo)/1000) +  (($_res6  * $monto_carga)/1000) +  (($_res7  * $monto_estiba)/1000) + (($_res8  * $monto_reparto)/1000); } else { $montomar = 0;}
if ($cuota_mie == 1) {$montomie = (($_res9  * $monto_recibo)/1000) +  (($_res10 * $monto_carga)/1000) +  (($_res11 * $monto_estiba)/1000) + (($_res12 * $monto_reparto)/1000); } else { $montomie = 0;}
if ($cuota_jue == 1) {$montojue = (($_res13 * $monto_recibo)/1000) +  (($_res14 * $monto_carga)/1000) +  (($_res15 * $monto_estiba)/1000) + (($_res16 * $monto_reparto)/1000); } else { $montojue = 0;}
if ($cuota_vie == 1) {$montovie = (($_res17 * $monto_recibo)/1000) +  (($_res18 * $monto_carga)/1000) +  (($_res19 * $monto_estiba)/1000) + (($_res20 * $monto_reparto)/1000); } else { $montovie = 0;}
if ($cuota_sab == 1) {$montosab = (($_res21 * $monto_recibo)/1000) +  (($_res22 * $monto_carga)/1000) +  (($_res23 * $monto_estiba)/1000) + (($_res24 * $monto_reparto)/1000); } else { $montosab = 0;}
if ($cuota_dom == 1) {$montodom = (($_res25 * $monto_recibo)/1000) +  (($_res26 * $monto_carga)/1000) +  (($_res27 * $monto_estiba)/1000) + (($_res28 * $monto_reparto)/1000); } else { $montodom = 0;}


echo "$ monto lun: " .$montolun . "</br>";	
echo "$ monto mar: " .$montomar . "</br>";	
echo "$ monto mie: " .$montomie . "</br>";	
echo "$ monto jue: " .$montojue . "</br>";	
echo "$ monto vie: " .$montovie . "</br>";	
echo "$ monto sab: " .$montosab . "</br>";	
echo "$ monto dom: " .$montodom . "</br></br>";	

//dias garantias
$lun_g = $row_resultado['lun_g'];
$mar_g = $row_resultado['mar_g'];
$mie_g = $row_resultado['mie_g'];
$jue_g = $row_resultado['jue_g'];
$vie_g = $row_resultado['vie_g'];
$sab_g = $row_resultado['sab_g'];

echo "garat lun: " .$lun_g . "</br>";	
echo "garat mar: " .$mar_g . "</br>";	
echo "garat mie: " .$mie_g . "</br>";	
echo "garat jue: " .$jue_g . "</br>";	
echo "garat vie: " .$vie_g . "</br>";	
echo "garat sab: " .$sab_g . "</br></br>";	

$sueldo_diario = $row_resultado['sueldo_total_productividad'] / 30;
$sueldo_semanal = $sueldo_diario * 7;
$productividad_diaria = ($sueldo_diario * $monto_garantia_diario) / 100;

// no cubre cajas pero es garantizado
if ($lun_g == 1) { $montolun = $productividad_diaria;}
if ($mar_g == 1) { $montomar = $productividad_diaria;}
if ($mie_g == 1) { $montomie = $productividad_diaria;}
if ($jue_g == 1) { $montojue = $productividad_diaria;}
if ($vie_g == 1) { $montovie = $productividad_diaria;}
if ($sab_g == 1) { $montosab = $productividad_diaria;}

echo "monto garant lun: " .$montolun . "</br>";	
echo "monto garant mar: " .$montomar . "</br>";	
echo "monto garant mie: " .$montomie . "</br>";	
echo "monto garant jue: " .$montojue . "</br>";	
echo "monto garant vie: " .$montovie . "</br>";	
echo "monto garant sab: " .$montosab . "</br>";	
echo "monto garant dom: " .$montodom . "</br></br>";	

//dias asistencia
$lun_a = $row_resultado['lun'];
$mar_a = $row_resultado['mar'];
$mie_a = $row_resultado['mie'];
$jue_a = $row_resultado['jue'];
$vie_a = $row_resultado['vie'];
$sab_a = $row_resultado['sab'];
$dom_a = $row_resultado['dom'];


echo "asist lun: " .$lun_a . "</br>";	
echo "asist mar: " .$mar_a . "</br>";	
echo "asist mie: " .$mie_a . "</br>";	
echo "asist jue: " .$jue_a . "</br>";	
echo "asist vie: " .$vie_a . "</br>";	
echo "asist sab: " .$sab_a . "</br>";	
echo "asist dom: " .$dom_a . "</br></br>";	

// no cubre cajas pero es garantizado
if ($lun_a == 0 && $lun_g == 0) { $montolun = 0;}
if ($mar_a == 0 && $mar_g == 0) { $montomar = 0;}
if ($mie_a == 0 && $mie_g == 0) { $montomie = 0;}
if ($jue_a == 0 && $jue_g == 0) { $montojue = 0;}
if ($vie_a == 0 && $vie_g == 0) { $montovie = 0;}
if ($sab_a == 0 && $sab_g == 0) { $montosab = 0;}

// suma pago
$suma = $montolun + $montomar + $montomie + $montojue + $montovie + $montosab + $montodom;
echo "Pago $: " .$suma . "</br>";	

// dias laborados
$dias_laborados = $lun_a + $mar_a + $mie_a + $jue_a + $vie_a + $sab_a + $dom_a;
	if ($dias_laborados == 7) {$asistencia = 1; }  else  {$asistencia = 0; } 

// si no cubre con asistencia de 6 dias
echo "Aplica castigo asistencia: " .$aplica_asistencia . "</br>";	
if ($asistencia == 0 and $aplica_asistencia == 1) { $suma = 0;}
echo "Pago Luego de asistencia:</br>";	
echo "monto lun: " .$montolun . "</br>";	
echo "monto mar: " .$montomar . "</br>";	
echo "monto mie: " .$montomie . "</br>";	
echo "monto jue: " .$montojue . "</br>";	
echo "monto vie: " .$montovie . "</br>";	
echo "monto sab: " .$montosab . "</br>";	
echo "monto dom: " .$montodom . "</br></br>";	

echo "Asistencia: " .$asistencia . "</br></br>";	

echo "Cajas lun: " .$ellun . "</br>";	
echo "Cajas mar: " .$elmar . "</br>";	
echo "Cajas mie: " .$elmie . "</br>";	
echo "Cajas jue: " .$eljue . "</br>";	
echo "Cajas vie: " .$elvie . "</br>";	
echo "Cajas sab: " .$elsab . "</br>";	
echo "Cajas dom: " .$eldom . "</br></br>";	


$monto =  $suma;
echo "Monto A: " . $monto . "</br>";
if ( $monto < 0) { $monto = 0; }
$porc_pago = round(($monto / $sueldo_semanal), 2) * 100;

//adicional
$adicional = $row_resultado['adicional'];
if ($adicional > 0) {    
$monto_adicional =  $adicional;
} else {
$monto_adicional =  0;
}

//garantia
$garantizado = $row_resultado['garantizado'];
echo "Garantizado semana: " . $garantizado . "</br>";
echo "Monto garantia: " . $monto_garantia . "</br>";
if ($garantizado > 0) {    
$monto = 0;
$garantia_monto = round(($sueldo_semanal * $monto_garantia) * 0.01, 2);
} else {
$garantia_monto =  0;
}
echo "Monto garantizado: " . $garantia_monto . "</br>";

$bono_asistencia = 0;
if($asistencia == 1){ $bono_asistencia = $monto_asistencias;} else { $bono_asistencia = 0;}

 echo "Bono Asistencia: ".$bono_asistencia."</br>";

$monto_T = $monto + $garantia_monto;
if ( $monto_T < 0) { $monto_T = 0; }
$porc_pago = round(($monto_T / $sueldo_semanal), 2) * 100;

// KPIs
$reci = $_res1 + $_res5 + $_res9  + $_res13 + $_res17 + $_res21 + $_res25;
$carg = $_res2 + $_res6 + $_res10 + $_res14 + $_res18 + $_res22 + $_res26;
$esti = $_res3 + $_res7 + $_res11 + $_res15 + $_res19 + $_res23 + $_res27;
$dist = $_res4 + $_res8 + $_res12 + $_res16 + $_res20 + $_res24 + $_res28;


//tope
if ( $porc_pago > 99) { 
// maximo topes
$Tope = $sueldo_semanal * 0.99; 
$Tope2 = 99;

// diferencias
$diferencia1 = $monto_T - $Tope;
$diferencia2 = $porc_pago - $Tope2;

// a capturar
$monto_T = $sueldo_semanal * 0.99; 
$porc_pago = 99;

} else { 

// a capturar
$monto_T =  $monto_T; 
$porc_pago = $porc_pago; 

// diferencias
$diferencia1 = 0;
$diferencia2 = 0;
}

// horas extra
$horas_extra = $row_resultado['horas_extra'];
$hora_extra = ($sueldo_diario / 8) * 2;
$horas_extras = $hora_extra * $horas_extra;


echo "Sueldo_diario: " . $sueldo_diario . "</br>";
echo "Sueldo_semanal: " . $sueldo_semanal . "</br>";
echo "Dias laborados: " . $dias_laborados . "</br>";
echo "Garantizado: " . $garantizado . "</br>";
echo "% Garantia: " . $garantia_monto . "</br>";
echo "% Adicional: " . $adicional . "</br>";
echo "Adicional: " . $monto_adicional . "</br>";
echo "Monto: " . $monto_T . "</br>";
echo "Monto en %: " . $porc_pago . "</br>";
echo "Recibo: " . $reci . "</br>";
echo "Carga: " . $carg . "</br>";
echo "Estiba: " . $esti . "</br>";
echo "Distrib: " . $dist . "</br>";

echo "</br>";

echo "diferencia1: " . $diferencia1 . "</br>";
echo "diferencia2: " . $diferencia2 . "</br>";
echo "porc_pago: " . $porc_pago . "</br>";

echo "</br>";
echo "Hextra #: " . $horas_extra . "</br>";
echo "Hextra $: " . $hora_extra . "</br>";
echo "Hextra: " . $horas_extras . "</br>";

echo "</br>";
echo "Sobre su 7 Funciones generales: ".$_res29_valor;



$query1 = "UPDATE prod_captura SET adicional3 = $diferencia1,  horas_extra_monto = $horas_extras, pago_resto = $diferencia2, bono_asistencia = $bono_asistencia, pago = '$porc_pago', pago_total = '$monto_T', reci = '$reci', carg = '$carg', esti = '$esti', dist = '$dist' WHERE IDcaptura = '$IDcaptura'"; 
$result1 = mysql_query($query1) or die(mysql_error());  

//redirecto
header("Location: productividad_valida_puesto_a_t.php?IDpuesto=$el_puesto"); 	
				
?>