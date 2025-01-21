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
   $query1 = "UPDATE prod_captura SET semana = '0' WHERE IDempleado = $repe_usuario AND semana = '$semana' AND anio = '$anio' AND IDcaptura <> '$la_captura'"; 
   $resultado = mysql_query($query1) or die(mysql_error());  
}


mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT prod_activos.IDempleado, prod_captura.IDcaptura, prod_captura.IDempleado, prod_captura.IDpuesto, prod_captura.fecha_captura, prod_captura.semana, prod_captura.IDmatriz, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.garantizado, prod_captura.adicional, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.pago, prod_captura.pago_total, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.fecha_antiguedad, prod_activos.fecha_nacimiento, prod_activos.sueldo_mensual, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.sueldo_diario, prod_activos.sobre_sueldo, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.descripcion_nomina, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM prod_captura LEFT JOIN prod_activos ON prod_captura.IDempleado = prod_activos.IDempleado WHERE prod_captura.IDcaptura = '$la_captura'"; 
mysql_query("SET NAMES 'utf8'");
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);
$su_matriz = $row_resultado['IDmatriz'];
$la_sucursal = $row_resultado['IDsucursal'];

//puestos aplicables
$el_puesto = $row_resultado['IDpuesto'];
$IDcaptura = $row_resultado['IDcaptura'];

//garantia
mysql_select_db($database_vacantes, $vacantes);
$query_garantia = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $su_matriz AND IDsucursal = $la_sucursal";
$garantia = mysql_query($query_garantia, $vacantes) or die(mysql_error());
$row_garantia = mysql_fetch_assoc($garantia);
$totalRows_garantia = mysql_num_rows($garantia);
$aplica_asistencia = $row_garantia['asistencia'];
$monto_asistencias = $row_garantia['monto_asistencia'];

	$camion_tulti = array(3476, 60953, 60792, 60780, 34484, 23087, 3047, 62418, 15542, 61551);
	$el_empleado = $row_resultado['IDempleado'];

	if (in_array($el_empleado, $camion_tulti)) {
	$query_camionT = "SELECT * FROM inc_camion_tulti WHERE IDempleado = $el_empleado";
	$camionT = mysql_query($query_camionT, $vacantes) or die(mysql_error());
	$row_camionT = mysql_fetch_assoc($camionT);
	$monto_asistencias = $monto_asistencias + $row_camionT['monto']; 
	}

//valores
mysql_select_db($database_vacantes, $vacantes);
$query_kpis = "SELECT * FROM prod_kpis WHERE IDpuesto = $el_puesto";
$kpis = mysql_query($query_kpis, $vacantes) or die(mysql_error());
$row_kpis = mysql_fetch_assoc($kpis);
$totalRows_kpis = mysql_num_rows($kpis);

//tabulador 
mysql_select_db($database_vacantes, $vacantes);
$query_tabulador = "SELECT * FROM vac_tabulador WHERE IDpuesto = $el_puesto AND IDmatriz = $su_matriz AND IDnivel = '$IDnivel_antiguedad'";
$tabulador = mysql_query($query_tabulador, $vacantes) or die(mysql_error());
$row_tabulador = mysql_fetch_assoc($tabulador);
$totalRows_tabulador = mysql_num_rows($tabulador);
$monto_tabulador = ($row_tabulador['variable_mensual']/30)*7;



//monto garantia
$garantizado = $row_garantia['garantia'];

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

// dias laborados
$dias_laborados = $lun_a + $mar_a + $mie_a + $jue_a + $vie_a + $sab_a + $dom_a;
	if ($dias_laborados >= 7) {$asistencia = 1; }  else  {$asistencia = 0; } 

echo "Laborados: " .$dias_laborados . "</br></br>";	


// si no cubre con asistencia de 6 dias
echo "Aplica castigo asistencia: " .$aplica_asistencia . "</br>";	
if ($asistencia == 0 and $aplica_asistencia == 1) { $asistencia = 0;} else {$asistencia = $dias_laborados;}
echo "Asistencia: " .$asistencia . "</br></br>";	

// KPIs
$a1 = $row_resultado['a1'];
$a2 = $row_resultado['a2'];
$a3 = $row_resultado['a3'];
$a4 = $row_resultado['a4'];
$a5 = $row_resultado['a5'];
$a6 = $row_resultado['a6'];
$a7 = $row_resultado['a7'];

//dias
$lun = $row_resultado['lun'];
$mar = $row_resultado['mar'];
$mie = $row_resultado['mie'];
$jue = $row_resultado['jue'];
$vie = $row_resultado['vie'];
$sab = $row_resultado['sab'];
$dom = $row_resultado['dom'];


// sueldo
$dias_laborados = $lun + $mar + $mie + $jue + $vie + $sab + $dom;
$sueldo_diario = $row_resultado['sueldo_total_productividad'] / 30;
$sueldo_semanal = $sueldo_diario * 7;
$dias_pagar = 0;


    if ($dias_laborados == 7) {$dias_pagar = 7; $parap = 1;} 
elseif ($dias_laborados == 6) {$dias_pagar = 6; $parap = 0.8571;} 
elseif ($dias_laborados == 5) {$dias_pagar = 5; $parap = 0.7143;} 
elseif ($dias_laborados == 4) {$dias_pagar = 4; $parap = 0.5714;} 
elseif ($dias_laborados == 3) {$dias_pagar = 3; $parap = 0.4286;} 
elseif ($dias_laborados == 2) {$dias_pagar = 2; $parap = 0.2857;} 
elseif ($dias_laborados == 1) {$dias_pagar = 1; $parap = 0.1429;} 
elseif ($dias_laborados == 0) {$dias_pagar = 0; $parap = 0;} 


// productividad real
$suma = $a1 + $a2 + $a3 + $a4 + $a5 + $a6; 
$monto_previo = ($monto_tabulador * $suma ) / 100; 
$porcentaje_carga = ($monto_previo * $parap); 
$monto =  round($porcentaje_carga, 2); 
if ( $monto <= 0) { $monto = 0; } 


if ($dias_laborados == 7) {$bono_asistencia = $monto_asistencias; }  else  {$bono_asistencia = 0; } 
echo "bono".$bono_asistencia."<br/>";
echo "dias ".$dias_laborados."<br/>";

//garantia
$gatantizado = $row_resultado['garantizado'];
if ($gatantizado > 0) {    
$garantia_monto = round($monto_tabulador, 2);
} else {
$garantia_monto =  0;
}

//adicional
$monto_adicional = $row_resultado['adicional'];

// horas extra
$horas_extra = $row_resultado['horas_extra'];
$hora_extra = ($sueldo_diario / 8 ) * 2;
$horas_extras = $hora_extra * $horas_extra;


// adicional solo si cubre asistencia
//if ($asistencia != "x" AND $el_puesto != "57" AND $asistencia == 0) { $monto_adicional = 0; }

echo 'La a7 = '  . $a7. "</br>";
echo "IDPuesto: " . $el_puesto. "</br>";
echo "Asistencia: " . $asistencia . "</br>";
echo "Sueldo_diario: " . $sueldo_diario . "</br>";
echo "Sueldo_semanal: " . $sueldo_semanal . "</br>";
echo "Porcentaje: " . $porcentaje . "</br>";
echo "Porcentaje Carga: " . $porcentaje_carga . "</br>";
echo "Dias a pagar: " . $dias_pagar . "</br>";
echo "Monto previo: " . $monto_previo . "</br>";
echo "Monto: " . $monto . "</br>";
echo "Monto Garantia: " . $garantia_monto . "</br>";
echo "% Garantia: " . $garantizado . "</br>";
echo "Adicional: " . $monto_adicional . "</br>";

	//cubre con asistencia y puntualidad
    if ($gatantizado >= 1) { 
		
    $query1 = "UPDATE prod_captura SET bono_asistencia = $bono_asistencia, pago = '$garantizado', pago_total = '$garantia_monto', adicional2 = '$monto_adicional' WHERE IDcaptura = '$IDcaptura'"; 
    $result1 = mysql_query($query1) or die(mysql_error());  
	}  else {
    $query2 = "UPDATE prod_captura SET bono_asistencia = $bono_asistencia, pago = '$porcentaje_carga', pago_total = '$monto', adicional2 = '$monto_adicional' WHERE IDcaptura = '$IDcaptura'"; 
    $result2 = mysql_query($query2) or die(mysql_error());  
		}
	//redirecto
	header("Location: productividad_captura_puesto_all.php?IDpuesto=$el_puesto"); 	

?>