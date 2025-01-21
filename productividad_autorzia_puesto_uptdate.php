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
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $_GET['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$semana= date("W") + 1;
$la_captura = $_GET['IDcaptura'];

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");

mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT prod_activos.IDnivel_antiguedad, prod_activos.IDnivel_caja, prod_activos.IDempleado, prod_captura.IDcaptura, prod_captura.IDempleado, prod_captura.IDpuesto, prod_captura.fecha_captura, prod_captura.semana, prod_captura.IDmatriz, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.garantizado, prod_captura.adicional, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.pago, prod_captura.pago_total, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.fecha_antiguedad, prod_activos.fecha_nacimiento, prod_activos.sueldo_mensual, prod_activos.sueldo_diario,  prod_activos.sobre_sueldo, prod_activos.`sueldo total`, prod_activos.descripcion_nomina, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea FROM prod_captura LEFT JOIN prod_activos ON prod_captura.IDempleado = prod_activos.IDempleado WHERE prod_captura.IDcaptura = '$la_captura'"; 
mysql_query("SET NAMES 'utf8'");
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);
$IDnivel_antiguedad = $row_resultado['IDnivel_antiguedad'];
$IDnivel_caja = $row_resultado['IDnivel_caja'];

//puestos aplicables
$el_puesto = $row_resultado['IDpuesto'];
$IDcaptura = $row_resultado['IDcaptura'];

//garantia
mysql_select_db($database_vacantes, $vacantes);
$query_garantia = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $su_matriz AND IDnivel = '$IDnivel_antiguedad'";
$garantia = mysql_query($query_garantia, $vacantes) or die(mysql_error());
$row_garantia = mysql_fetch_assoc($garantia);
$totalRows_garantia = mysql_num_rows($garantia);
$aplica_asistencia = $row_garantia['asistencia']; 
$monto_asistencias = $row_garantia['monto_asistencia']; 


//monto garantia
$garantizado = $row_garantia['garantia'];

//Asistencia
$asistencia = $row_resultado['a1'];

// KPIs
$a2 = $row_resultado['a2'];
$a3 = $row_resultado['a3'];
$a4 = $row_resultado['a4'];
$a5 = $row_resultado['a5'];
$a6 = $row_resultado['a6'];

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
$sueldo_diario = $row_resultado['sueldo_diario'];
$sueldo_semanal = $sueldo_diario * 7;
$dias_pagar = 0;


    if ($dias_laborados == 7) {$dias_pagar = 7; } 
elseif ($dias_laborados == 6) {$dias_pagar = 7; } 
elseif ($dias_laborados  < 6) {$dias_pagar = $dias_laborados + 1; } 
else 						  {$dias_pagar = 0; } 


// productividad real
$suma = $a2 +$a3 +$a4 +$a5 + $a6; 
$porcentaje = $suma * 0.001;
$porcentaje_carga = $suma * 0.1;
$monto_previo = $dias_pagar * $sueldo_diario;
$monto =  round($monto_previo * $porcentaje, 2);

//garantia
$gatantizado = $row_resultado['garantizado'];
if ($gatantizado == 1) {    
$garantia_monto = round(($sueldo_semanal * $garantizado) * 0.01, 2);
} else {
$garantia_monto =  0;
}

//adicional
$adicional = $row_resultado['adicional'] * 0.01;
if ($adicional != 0) {    
$monto_adicional =  round($monto_previo * $adicional, 2);
} else {
$monto_adicional =  0;
}


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
    if ($gatantizado == 1) { 
		
    $query1 = "UPDATE prod_captura SET pago = '$garantizado', pago_total = '$garantia_monto', adicional2 = '$monto_adicional' WHERE IDcaptura = '$IDcaptura'"; 
    $result1 = mysql_query($query1) or die(mysql_error());  
	
	//redirecto
	header("Location: productividad_valida_puesto.php?IDpuesto=$el_puesto&IDmatriz=$la_matriz"); 	
				
	//cubre con asistencia y puntualidad
	}  else if ($gatantizado == 0 && $asistencia == 1) {
		
    $query1 = "UPDATE prod_captura SET pago = '$porcentaje_carga', pago_total = '$monto', adicional2 = '$monto_adicional' WHERE IDcaptura = '$IDcaptura'"; 
    $result1 = mysql_query($query1) or die(mysql_error());  
	
	//redirecto
	header("Location: productividad_valida_puesto.php?IDpuesto=$el_puesto&IDmatriz=$la_matriz"); 	
				
	}  else {
		
    $query2 = "UPDATE prod_captura SET pago = '$porcentaje_carga', pago_total = '0' WHERE IDcaptura = '$IDcaptura'"; 
    $result2 = mysql_query($query2) or die(mysql_error());  
	
	//redirecto
	header("Location: productividad_valida_puesto.php?IDpuesto=$el_puesto&IDmatriz=$la_matriz"); 	
		
		}
?>