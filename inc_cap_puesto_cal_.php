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
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 
$el_usuario = $row_usuario['IDusuario'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$tipo = $_GET['tipo'];
$IDcaptura =  $_GET['IDcaptura'];

mysql_select_db($database_vacantes, $vacantes);
$query_captura = "SELECT * FROM inc_captura WHERE IDcaptura = $IDcaptura";
$captura = mysql_query($query_captura, $vacantes) or die(mysql_error());
$row_captura = mysql_fetch_assoc($captura);
$totalRows_captura = mysql_num_rows($captura);

$el_empleado = $row_captura['IDempleado'];
$el_puesto = $row_captura['IDpuesto'];
if($row_captura['pprueba'] == "" OR $row_captura['pprueba'] == 0) {$el_puesto = $row_captura['IDpuesto'];} else {$el_puesto = $row_captura['pprueba'];}
echo "IDpuesto: ".$el_puesto;

$la_matriz = $row_captura['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_activo = "SELECT * FROM prod_activos WHERE IDempleado = $el_empleado";
$activo = mysql_query($query_activo, $vacantes) or die(mysql_error());
$row_activo = mysql_fetch_assoc($activo);
$totalRows_activo = mysql_num_rows($activo);
$sueldo_diario = $row_activo['sueldo_diario'];
$fecha_antiguedad = strtotime($row_activo['fecha_antiguedad']);
$fecha_tope = strtotime('2022-06-30');

echo "<br/>Fecha Alta: ".$row_activo['fecha_antiguedad'];

$date_a = new DateTime($row_activo['fecha_antiguedad']);
$date_a->modify('-1 day');
$date_b = new DateTime("last sunday");
$diff_c = $date_a->diff($date_b);
$periodo_d =  $diff_c->days;
$DateA = $date_a->format('d/m/Y'); // the result will 01/12/2015
$DateB = $date_b->format('d/m/Y'); // the result will 01/12/2015

print "<br/>Fecha A: ".$DateA;
print "<br/>Fecha B: ".$DateB;
echo "<br/>Dias Dif: ".$periodo_d." Dias";

mysql_select_db($database_vacantes, $vacantes);
$query_pxv_loc = "SELECT * FROM inc_pxv WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz AND tipo = 1";
$pxv_loc = mysql_query($query_pxv_loc, $vacantes) or die(mysql_error());
$row_pxv_loc = mysql_fetch_assoc($pxv_loc);
$totalRows_pxv_loc = mysql_num_rows($pxv_loc);

mysql_select_db($database_vacantes, $vacantes);
$query_pxv_for = "SELECT * FROM inc_pxv WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz AND tipo = 2";
$pxv_for = mysql_query($query_pxv_for, $vacantes) or die(mysql_error());
$row_pxv_for = mysql_fetch_assoc($pxv_for);
$totalRows_pxv_for = mysql_num_rows($pxv_for);

// nuevo pago de Premio Transporte
mysql_select_db($database_vacantes, $vacantes);
$query_transporte = "SELECT * FROM inc_transporte WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz";
$transporte = mysql_query($query_transporte, $vacantes) or die(mysql_error());
$row_transporte = mysql_fetch_assoc($transporte);
$totalRows_transporte = mysql_num_rows($transporte);
$monto_transporte = $row_transporte['monto'];
$valor_transporte = $row_captura['transporte'];


//Ampliamos la session
if (!isset($_SESSION)) {
ini_set("session.cookie_lifetime", 10800);
ini_set("session.gc_maxlifetime", 10800); 
  session_start();
}

if ($tipo == 1) {
echo "horas extras";	
	
	$horas = $row_captura['horas1'];
	$dias = $row_captura['dias1'];
	$sueldo_hora = 	$sueldo_diario / 8;
	$inc1 = ($sueldo_hora * $horas ) * 2;

    $query1 = "UPDATE inc_captura SET inc1 = '$inc1' WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	//header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");
	
} 

elseif ($tipo == 2) {
echo "Suplencia";	

	$horas = $row_captura['horas2'];
	$dias = $row_captura['dias2'];
	$sueldo_hora = 	$sueldo_diario / 8;
	$inc2 = $sueldo_hora * $horas;
	
	echo "Horas: ".$horas;
	echo "<br/>Dias: ".$dias;
	echo "<br/>Sueldo x hora: ".$sueldo_hora;
	echo "<br/>Pago: ".$inc2;

    $query1 = "UPDATE inc_captura SET inc2 = '$inc2' WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	//header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

} 

elseif ($tipo == 3) {
	
	$monto = $row_captura['inc3'];
	$inc3p = 0;
	$sueldo_semana_30 = ($sueldo_diario * 7) * 0.3;
	$sueldo_super_30 = ($sueldo_diario * 7) * 0.15;
	//puestos de supervisor
	$supers = array(58, 56, 270, 17);
	
	echo "</br>Monto capturado: ".$monto."</br>";
	echo "Sueldo diario: ".$sueldo_diario."</br>";
	echo "Tope por Sueldo al 30: ".$sueldo_semana_30."</br>";
	echo "Tope por Supervisor: ".$sueldo_super_30."</br>";
	
	
	if ($monto > $sueldo_semana_30 ) {$inc3p = $sueldo_semana_30; echo "A";} else {$inc3p = $monto; echo "B";}
	if ( in_array($row_captura['IDpuesto'], $supers) and $monto > $sueldo_super_30 ) { $inc3 = $sueldo_super_30; echo "C";} else {$inc3 = $inc3p; echo "D";}
	
    echo "<br/>Monto Trans: ".$monto_transporte;	
	echo "<br/>INC3: ".$inc3;
	echo "<br/>Valor: ".$valor_transporte;
	echo "<br/>fecha:".$fecha_antiguedad;
	echo "<br/>Tope:".$fecha_tope;
	
	if ($valor_transporte == 1) {$pago_transporte = $monto_transporte; } else {$pago_transporte = 0; }
	echo "<br/>A Monto: ".$pago_transporte;
	
	// pago parcial de bono de transporte
	if ($periodo_d <= 7 AND $pago_transporte != 0) {$pago_transporte = ($monto_transporte / 7 ) * $periodo_d; }
	
	echo "<br/>Dias diferencia: ".$periodo_d;
	echo "<br/>B Monto: ".$pago_transporte;

    $query1 = "UPDATE inc_captura SET inc3 = '$inc3', transporte_monto = '$pago_transporte' WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	//header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");
} 

elseif ($tipo == 6) {
echo "Festivos";	
	$dias = $row_captura['diasf'];
	$inc6 = ($sueldo_diario * $dias) * 2;

    $query1 = "UPDATE inc_captura SET inc6 = '$inc6' WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");
} 

elseif ($tipo == 4) {
echo "Domingos";	

	$perc = $row_captura['perc'];
	$prima = $row_captura['prima'];

	$primam = $sueldo_diario * 0.25;
	$percm = $sueldo_diario * 2;
	
	if ($perc == 2) {$inc4 = $primam + $percm; } else { $inc4 = $primam; }

    $query1 = "UPDATE inc_captura SET inc4 = '$inc4' WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	//header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

} 

elseif ($tipo == 5) {

echo "PXV </br>";	
echo $row_captura['lul'].$row_captura['mal'].$row_captura['mil'].$row_captura['jul'].$row_captura['vil']."</br>";



	$locs = $row_captura['lul'] +  $row_captura['mal'] +  $row_captura['mil'] +  $row_captura['jul'] +  $row_captura['vil'] +  $row_captura['sal'] +  $row_captura['dol']; 
	$fors = $row_captura['luf'] +  $row_captura['maf'] +  $row_captura['mif'] +  $row_captura['juf'] +  $row_captura['vif'] +  $row_captura['saf'] +  $row_captura['dof']; 
	$loc_max = $row_pxv_loc['maximo'];
	$for_max = $row_pxv_for['maximo'];
	$loc_monto = $row_pxv_loc['monto'];
	$for_monto = $row_pxv_for['monto'];	
	
	if ($locs > $loc_max) { $locs = $loc_max; }
	if ($fors > $for_max) { $fors = $for_max; }
	
	$prev_loc = $locs * $loc_monto;
	$prev_for = $fors * $for_monto;
	
	$inc5 = $prev_loc + $prev_for;
	
	echo 'IDempleado: ' . $el_empleado . '</br>';
	echo 'Puesto: ' . $el_puesto . '</br>';
	echo 'Locales: ' . $locs . '</br>';
	echo 'Foraneos: ' . $fors . '</br>';
	echo 'Loc Max: ' . $loc_max . '</br>';
	echo 'For Max: ' . $for_max . '</br>';
	echo 'Loc Monto: ' . $loc_monto . '</br>';
	echo 'For Monto: ' . $for_monto . '</br>';
	echo 'Prev Loc: ' . $prev_loc . '</br>';
	echo 'Prev For: ' . $prev_for . '</br>';
	echo 'inc5: ' . $inc5 . '</br>';
	echo 'TRansporte: ' . $pago_transporte . '</br>';

    $query1 = "UPDATE inc_captura SET inc5 = '$inc5' WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
 //header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");



} else { 

//header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

}
?>