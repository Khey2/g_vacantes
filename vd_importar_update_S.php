<?php require_once('Connections/vacantes.php'); ?>
<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
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

if(isset($_GET['borrar']) AND $_GET['borrar'] == 1) {
//borramos para cargar de nuevo
$query_borrar = "DELETE FROM com_vd_temp WHERE IDvd > 0";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
//redirecto
header("Location: vd_importar.php?info=6"); 	
}


//globales
$mi_fecha =  date('Y/m/d');
$el_mes = $_GET['IDmes'];
if (strlen($el_mes) == 1) {$el_mes_compara = "0".$el_mes;} else {$el_mes_compara = $el_mes;}
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$anio = $row_variables['anio'];
$el_mes_bono = date("m");

// seleccionados los datos para importar
mysql_select_db($database_vacantes, $vacantes);
$query_resultado1 = "SELECT * FROM com_vd_temp";
$resultado1 = mysql_query($query_resultado1, $vacantes) or die(mysql_error());
$row_resultado1 = mysql_fetch_assoc($resultado1);
$totalRows_resultado1 = mysql_num_rows($resultado1);

$cantidadDias = cal_days_in_month(CAL_GREGORIAN, $el_mes, $anio);
$fechaInicio = strtotime($anio."-".$el_mes_bono."-01");
$fechaFin = strtotime($anio."-".$el_mes_bono."-".$cantidadDias);

do {

$IDmatriz = $row_resultado1['IDmatriz'];
$IDsuper = $row_resultado1['IDempleadoS'];

mysql_select_db($database_vacantes, $vacantes);
$query_objetivos_mes = "SELECT * FROM com_vd_objetivo_mes WHERE IDmes = $el_mes AND $IDmatriz = IDmatriz AND anio = $anio";
$objetivos_mes = mysql_query($query_objetivos_mes, $vacantes) or die(mysql_error());
$row_objetivos_mes = mysql_fetch_assoc($objetivos_mes);
$totalRows_objetivos_mes = mysql_num_rows($objetivos_mes);

// objetivos
$ObjetivoVenta = $row_objetivos_mes['objetivo_venta'];
$ObjetivoClientesVenta = $row_objetivos_mes['objetivo_clientes_venta'];

//resultados
$query_Montos = "SELECT com_vd_temp.IDempleadoS, SUM(com_vd_temp.bt_01) as Monto1a, SUM(com_vd_temp.bt_02) as Monto2a, SUM(com_vd_temp.bt_03) as Monto3a, SUM(com_vd_temp.bt_04) as Monto4a, SUM(com_vd_temp.bt_05) as Monto5a, COUNT(com_vd_temp.bt_01) as Monto1b, COUNT(com_vd_temp.bt_02) as Monto2b, COUNT(com_vd_temp.bt_03) as Monto3b, COUNT(com_vd_temp.bt_04) as Monto4b, COUNT(com_vd_temp.bt_05) as Monto5b, SUM(com_vd_temp.VentaNeta) as MontoVentaNeta, SUM(com_vd_temp.ClientesVenta) as MontoClientesVenta, AVG(MargenBRuto) as Margen FROM com_vd_temp WHERE com_vd_temp.IDempleadoS = $IDsuper";
$Montos = mysql_query($query_Montos, $vacantes) or die(mysql_error());
$row_Montos = mysql_fetch_assoc($Montos);
$Montos = mysql_num_rows($Montos);

//Bono transporte:
//A Bono Transporte

	$semana = 0;
	if ($row_Montos['Monto1a'] == 0 OR $row_Montos['Monto1b'] == 0) {$Monto1 = 0; } else { $Monto1 = $row_Montos['Monto1a'] / $row_Montos['Monto1b']; 
		if ($Monto1 >= 700) { $Monto1 = 1166;} else if ($Monto1 > 350 AND $Monto1 < 700){  $Monto1 = 500;} else { $Monto1 = 0;}	}
	if ($row_Montos['Monto2a'] == 0 OR $row_Montos['Monto2b'] == 0) {$Monto2 = 0; } else { $Monto2 = $row_Montos['Monto2a'] / $row_Montos['Monto2b']; 
		if ($Monto2 >= 700) { $Monto2 = 1166;} else if ($Monto2 > 350 AND $Monto2 < 700){  $Monto2 = 500;} else { $Monto2 = 0;}	}
	if ($row_Montos['Monto3a'] == 0 OR $row_Montos['Monto3b'] == 0) {$Monto3 = 0; } else { $Monto3 = $row_Montos['Monto3a'] / $row_Montos['Monto3b']; 
		if ($Monto3 >= 700) { $Monto3 = 1166;} else if ($Monto3 > 350 AND $Monto3 < 700){  $Monto3 = 500;} else { $Monto3 = 0;}	}
	if ($row_Montos['Monto4a'] == 0 OR $row_Montos['Monto4b'] == 0) {$Monto4 = 0; } else { $Monto4 = $row_Montos['Monto4a'] / $row_Montos['Monto4b']; 
		if ($Monto4 >= 700) { $Monto4 = 1166;} else if ($Monto4 > 350 AND $Monto4 < 700){  $Monto4 = 500;} else { $Monto4 = 0;}	}
	if ($row_Montos['Monto5a'] == 0 OR $row_Montos['Monto5b'] == 0) {$Monto5 = 0; } else { $Monto5 = $row_Montos['Monto5a'] / $row_Montos['Monto5b']; 
		if ($Monto5 >= 700) { $Monto5 = 1166;} else if ($Monto5 > 350 AND $Monto5 < 700){  $Monto5 = 500;} else { $Monto5 = 0;}	}
		$array1 = array(0, $Monto1, $Monto2, $Monto3, $Monto4, $Monto5);
				
		for ($i =  $fechaInicio; $i <= $fechaFin; $i += 86400 * 7) {
			
			$semana = $semana + 1;
			//echo date("Y-m-d", $i)." ";
			$fecha = date("Y-m-d", strtotime('next Friday', $i));
			$query_btransporte = "UPDATE com_vd_temp SET bt_0".$semana." = ".$array1[$semana].", bt_0".$semana."_fecha = '".$fecha."' WHERE com_vd_temp.IDpuesto = 235 AND com_vd_temp.IDempleado = '".$IDsuper."' AND com_vd_temp.anio = '".$anio."' AND com_vd_temp.IDmes = '".$el_mes."'";
			$btransporte = mysql_query($query_btransporte, $vacantes) or die(mysql_error());
		
			$result = substr($fecha, 5, 2);
			if ($result != $el_mes_compara){
			$query_borramos = "UPDATE com_vd_temp SET bt_05 = '', bt_05_fecha = '' WHERE com_vd_temp.IDpuesto = 235 AND com_vd_temp.IDempleado = '".$IDsuper."' AND com_vd_temp.anio = '".$anio."' AND com_vd_temp.IDmes = '".$el_mes."'";
			$borramos = mysql_query($query_borramos, $vacantes) or die(mysql_error()); 
			}
		}
			

// B. Bono productividad:
$MontoVentaNeta = $row_Montos['MontoVentaNeta'];

     if ($MontoVentaNeta >= $ObjetivoVenta ) { $BonoProductividad = 5000; }
else if ($MontoVentaNeta >= ($ObjetivoVenta * 0.9) AND $MontoVentaNeta < $ObjetivoVenta) { $BonoProductividad = 2500; }
else if ($MontoVentaNeta >= ($ObjetivoVenta * 0.8) AND ($MontoVentaNeta < $ObjetivoVenta * 0.9)) { $BonoProductividad = 1500; }
else { $BonoProductividad = 0; }

$query_bVentaNeta = "UPDATE com_vd_temp SET BonoProductividad = ".$BonoProductividad." WHERE com_vd_temp.IDpuesto = 235 AND com_vd_temp.IDempleado = '".$IDsuper."' AND com_vd_temp.anio = '".$anio."' AND com_vd_temp.IDmes = '".$el_mes."'";
$bVentaNeta = mysql_query($query_bVentaNeta, $vacantes) or die(mysql_error());

//echo  $bono_productividad."<br/>";

// C. Premios:
$MontoClientesVenta = $row_Montos['MontoClientesVenta'];

$Premios = 0;
if ($MontoClientesVenta >= $ObjetivoClientesVenta ) { $Premios = 5000; }
else if ($MontoClientesVenta > ($ObjetivoClientesVenta * 0.8) ) { $Premios = 2000; }


if ($MontoVentaNeta > ($ObjetivoVenta * 1.1)  )  { $Premios = $Premios + 1000; }

$query_bClientesVenta = "UPDATE com_vd_temp SET Premios = ".$Premios." WHERE com_vd_temp.IDpuesto = 235 AND com_vd_temp.IDempleado = '".$IDsuper."' AND com_vd_temp.anio = '".$anio."' AND com_vd_temp.IDmes = '".$el_mes."'";
$bClientesVenta = mysql_query($query_bClientesVenta, $vacantes) or die(mysql_error());


//D. Comisiones

$Margen = $row_Montos['Margen'];
if ($MontoVentaNeta >= $ObjetivoVenta) { 

	      if ($Margen >= 0.12){$FactorComision = 0.0035;}
	 else if ($Margen >= 0.11 AND $Margen < 0.12) {$FactorComision = 0.0030;} 
	 else if ($Margen >= 0.10 AND $Margen < 0.11) {$FactorComision = 0.0025;} 
	 else {$FactorComision = 0.0020;}


} else if ($MontoVentaNeta >= ($ObjetivoVenta *0.9) AND $MontoVentaNeta < $ObjetivoVenta) { 

	      if ($Margen >= 0.12){$FactorComision = 0.0025;}
	 else if ($Margen >= 0.11 AND $Margen < 0.12) {$FactorComision = 0.0020;} 
	 else if ($Margen >= 0.10 AND $Margen < 0.11) {$FactorComision = 0.0010;} 
	 else {$FactorComision = 0.0005;}

} else {

$FactorComision = 0;

}

$ComisionesMonto = $MontoVentaNeta * $FactorComision;

$query_bComisiones = "UPDATE com_vd_temp SET Comisiones = ".$ComisionesMonto." WHERE com_vd_temp.IDpuesto = 235 AND com_vd_temp.IDempleado = '".$IDsuper."' AND com_vd_temp.anio = '".$anio."' AND com_vd_temp.IDmes = '".$el_mes."'";
$bComisiones = mysql_query($query_bComisiones, $vacantes) or die(mysql_error());

 } while ($row_resultado1 = mysql_fetch_assoc($resultado1));
 
 header("Location: vd_importar.php?info=6"); 	

?>

