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

$el_mesA = $el_mes - 1;

mysql_select_db($database_vacantes, $vacantes);
$query_objetivos_mes = "SELECT * FROM com_vd_objetivo_mes WHERE IDmes = $el_mesA";
$objetivos_mes = mysql_query($query_objetivos_mes, $vacantes) or die(mysql_error());
$row_objetivos_mes = mysql_fetch_assoc($objetivos_mes);
$totalRows_objetivos_mes = mysql_num_rows($objetivos_mes);
$objetivo_venta = $row_objetivos_mes['objetivo_venta'];
$objetivo_clientes_venta = $row_objetivos_mes['objetivo_clientes_venta'];

$cantidadDias = cal_days_in_month(CAL_GREGORIAN, $el_mes, $anio);
$fechaInicio = strtotime($anio."-".$el_mes_bono."-01");
$fechaFin = strtotime($anio."-".$el_mes_bono."-".$cantidadDias);

do {
	
	
if ($row_resultado1['IDpuesto'] != 235 ) { 

//a1: Bono Transporte

	//if ($row_resultado1['IDmatriz'] == 23 ) { $MontoBonoTransporte = 1100; } else { $MontoBonoTransporte = 700; } mismo monto para todos

			if ($row_resultado1['Visitas'] >= 900 AND $row_resultado1['VentaNeta'] >= 400000.00 ) { 
			$MontoBonoTransporte = 1000;
		$semana = 0;
		for ($i =  $fechaInicio; $i <= $fechaFin; $i += 86400 * 7) {
			$semana = $semana + 1;
			//echo date("Y-m-d", $i)." ";
			$fecha = date("Y-m-d", strtotime('next Friday', $i));
			$clave = $row_resultado1['Clave'];
			$query_btransporte = "UPDATE com_vd_temp SET bt_0".$semana." = ".$MontoBonoTransporte.", bt_0".$semana."_fecha = '".$fecha."' WHERE com_vd_temp.Clave = '".$clave."'";
			$btransporte = mysql_query($query_btransporte, $vacantes) or die(mysql_error());
			
			$result = substr($fecha, 5, 2);
			if ($result != $el_mes_compara) {
			$query_borramos = "UPDATE com_vd_temp SET bt_05 = '', bt_05_fecha = '' WHERE com_vd_temp.Clave = '".$clave."'";
			$borramos = mysql_query($query_borramos, $vacantes) or die(mysql_error()); 
			}
			
			//echo $query_btransporte."<br/>";
               }
		
		
			} else if ($row_resultado1['Visitas'] >= 900 AND $row_resultado1['VentaNeta'] >= 250000.00 AND $row_resultado1['VentaNeta'] < 400000.00) { 
			$MontoBonoTransporte = 800;
		$semana = 0;
		for ($i =  $fechaInicio; $i <= $fechaFin; $i += 86400 * 7) {
			$semana = $semana + 1;
			//echo date("Y-m-d", $i)." ";
			$fecha = date("Y-m-d", strtotime('next Friday', $i));
			$clave = $row_resultado1['Clave'];
			$query_btransporte = "UPDATE com_vd_temp SET bt_0".$semana." = ".$MontoBonoTransporte.", bt_0".$semana."_fecha = '".$fecha."' WHERE com_vd_temp.Clave = '".$clave."'";
			$btransporte = mysql_query($query_btransporte, $vacantes) or die(mysql_error());
			
			$result = substr($fecha, 5, 2);
			if ($result != $el_mes_compara){
			$query_borramos = "UPDATE com_vd_temp SET bt_05 = '', bt_05_fecha = '' WHERE com_vd_temp.Clave = '".$clave."'";
			$borramos = mysql_query($query_borramos, $vacantes) or die(mysql_error()); 
			}
			
			//echo $query_btransporte."<br/>";
		}
		

			} else if ($row_resultado1['Visitas'] > 400 AND $row_resultado1['Visitas'] < 900 AND $row_resultado1['VentaNeta'] >= 250000.00) { 
			$MontoBonoTransporte = 400;
		$semana = 0;
		for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400 * 7) {
			$semana = $semana + 1;
		
			$fecha = date("Y-m-d", strtotime('next Friday', $i));
			$clave = $row_resultado1['Clave'];
			$query_btransporte = "UPDATE com_vd_temp SET bt_0".$semana." = ".$MontoBonoTransporte.", bt_0".$semana."_fecha = '".$fecha."' WHERE com_vd_temp.Clave = '".$clave."'";
			$btransporte = mysql_query($query_btransporte, $vacantes) or die(mysql_error());

			$result = substr($fecha, 5, 2);
			if ($result != $el_mes_compara){
			$query_borramos = "UPDATE com_vd_temp SET bt_05 = '', bt_05_fecha = '' WHERE com_vd_temp.Clave = '".$clave."'";
			$borramos = mysql_query($query_borramos, $vacantes) or die(mysql_error()); 
			}

			//echo $query_btransporte."<br/>";
		}

			} else {	 
	 
	 // BONO DE TRANSPORTE
	 		$MontoBonoTransporte = 0;
	 		$semana = 0;
		for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400 * 7) {
			$semana = $semana + 1;

			$fecha = date("Y-m-d", strtotime('next Friday', $i));
			$clave = $row_resultado1['Clave'];
			$query_btransporte = "UPDATE com_vd_temp SET bt_0".$semana." = ".$MontoBonoTransporte.", bt_0".$semana."_fecha = '".$fecha."' WHERE com_vd_temp.Clave = '".$clave."'";
			$btransporte = mysql_query($query_btransporte, $vacantes) or die(mysql_error());

			$result = substr($fecha, 5, 2);
			if ($result != $el_mes_compara){
			$query_borramos = "UPDATE com_vd_temp SET bt_05 = '', bt_05_fecha = '' WHERE com_vd_temp.Clave = '".$clave."'";
			$borramos = mysql_query($query_borramos, $vacantes) or die(mysql_error()); 
			}

			//echo $query_btransporte."<br/>";
		}
	 
 }

//a2 Bono Productividad

$BonoVentaNeta = 0;
$BonoClientesVenta = 0;
$BonoDevPorc = 0;
$clave = $row_resultado1['Clave'];

if ($row_resultado1['IDmatriz'] == 12 or $row_resultado1['IDmatriz'] == 23 or $row_resultado1['IDmatriz'] == 14) {

		if ($row_resultado1['ClientesVenta'] >= 120) { $BonoClientesVenta = $row_resultado1['ClientesVenta'] * 15; } else { $BonoClientesVenta = 0; }

} else {

		if ($row_resultado1['ClientesVenta'] >= 180) { $BonoClientesVenta = $row_resultado1['ClientesVenta'] * 15; } else { $BonoClientesVenta = 0; }

}


$bono_productividad = $BonoClientesVenta;

$query_bproductividad = "UPDATE com_vd_temp SET BonoProductividad = ".$bono_productividad.", BonoVentaNeta = ".$BonoVentaNeta.", BonoClientesVenta = ".$BonoClientesVenta.", BonoDevPorc = ".$BonoDevPorc." WHERE com_vd_temp.Clave = '".$clave."'";
$bproductividad = mysql_query($query_bproductividad, $vacantes) or die(mysql_error());

//echo  $bono_productividad."<br/>";


//a3 Premios
	$Premio_1 = 0;
	$Premio_2 = 0;

//     if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 250000 AND $row_resultado1['VentaNeta'] <= 300000 ) { $Premio_1 = 1500; }
     if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 330000 AND $row_resultado1['VentaNeta'] <= 385000 ) { $Premio_1 = 2000; }
else if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 385000 AND $row_resultado1['VentaNeta'] <= 440000 ) { $Premio_1 = 2500; }
else if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 440000 AND $row_resultado1['VentaNeta'] <= 550000 ) { $Premio_1 = 3000; }
else if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 550000 ) { $Premio_1 = 4000; }

echo $row_resultado1['IDempleado']." ";
echo $row_resultado1['VentaNeta']." ";
echo $Premio_1."<br/>";


	//	if ($row_resultado1['VentaNetaCajas'] > 0  AND $row_resultado1['VentaNetaCajas'] >= 40000) { 
//	$veces = floor($row_resultado1['VentaNetaCajas'] / 40000);
//	$Premio_2 = 150 * $veces;	
//	}
//else if ($row_resultado1['VentaNetaCajas'] > 0  AND $row_resultado1['VentaNetaCajas'] >= 40000) { 
//	$veces = $row_resultado1['VentaNetaCajas'] / 40000;
//	$Premio_2 = 75 * $veces;	
//	}

	$Premios = $Premio_1 + $Premio_2;


$query_bproductividad = "UPDATE com_vd_temp SET Premios = ".$Premios.", Premio_1 = ".$Premio_1.", Premio_2 = ".$Premio_2." WHERE com_vd_temp.Clave = '".$clave."'";
$bproductividad = mysql_query($query_bproductividad, $vacantes) or die(mysql_error());

//echo $Premios."<br/>";

//a4 Comisiones
// sujeto a venta mayor a 200,000
// margen ya no va
// de 100 a 199,000 se paga 1% pieza y 0.05% caja
// abajo de 100,000 ya nose paga comision

$Comisiones = 0;
$Comisiones_pieza = 0;
$Comisiones_caja = 0;
$VentaNeta = $row_resultado1['VentaNeta'];
$VentaNetaCajas = $row_resultado1['VentaNetaCajas'];
$VentaNetaPieza = $row_resultado1['VentaNetaPieza'];

     if ( $row_resultado1['VentaNeta'] >= 250000 ) { $Comisiones_pieza = $VentaNetaPieza * 0.025; $Comisiones_caja = $VentaNetaCajas * 0.010; } 
//else if ( $row_resultado1['VentaNeta'] > 200000 AND $row_resultado1['VentaNeta'] < 250000) { $Comisiones_pieza = $VentaNetaPieza * 0.010; $Comisiones_caja = $VentaNetaCajas * 0.005; } 

$Comisiones = $Comisiones_pieza + $Comisiones_caja;

$query_bproductividad = "UPDATE com_vd_temp SET Comisiones = ".$Comisiones.", Comisiones_pieza = ".$Comisiones_pieza.", Comisiones_caja = ".$Comisiones_caja." WHERE com_vd_temp.Clave = '".$clave."'";
$bproductividad = mysql_query($query_bproductividad, $vacantes) or die(mysql_error());


//echo $Comisiones."<br/>";


// cierre de calculo 
$query_bproductividad = "UPDATE com_vd_temp SET calculado = 1, bt_capturador = ".$el_usuario." WHERE com_vd_temp.Clave = '".$clave."'";
$bproductividad = mysql_query($query_bproductividad, $vacantes) or die(mysql_error());

}

// asigno fechas
$fecha_alta = new DateTime($row_resultado1['fecha_antiguedad']);
$fecha_garantia = new DateTime($row_resultado1['fecha_antiguedad']);
$fecha_carga = new DateTime();

// recorremos fecha garantia
$fecha_garantia->modify('+8 weeks');
$fecha_garantia->modify('next monday');

//$DateA = $fecha_alta->format('d/m/Y'); 
//$DateB = $fecha_garantia->format('d/m/Y'); 

//print "Fecha A: ".$DateA;
//print " | Fecha B: ".$DateB." <br />";

if ($fecha_carga <= $fecha_garantia) {
$query_bproductividad = "UPDATE com_vd_temp SET IDgarantizado = 1, bt_01 = 0, bt_02 = 0, bt_03 = 0, bt_04 = 0, bt_05 = 0, BonoProductividad = 0, BonoVentaNeta = 0, BonoClientesVenta = 0, BonoDevPorc = 0, Premios = 0, Premio_1 = 0, Premio_2 = 0, Comisiones = 0, Comisiones_pieza = 0, Comisiones_caja = 0 WHERE com_vd_temp.Clave = '".$clave."'";
$bproductividad = mysql_query($query_bproductividad, $vacantes) or die(mysql_error());
  }


 } while ($row_resultado1 = mysql_fetch_assoc($resultado1));
 
 
 //borramos para cargar de nuevo
$query_borrar = "DELETE FROM com_vd_temp WHERE IDmatriz = 0 AND IDempleado = 0";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());

if(isset($_GET['borrar'])) { 
  header("Location: vd_importar.php?info=6"); 
} else {
 header("Location: vd_importar_update_S.php?info=6&IDmes=$el_mes"); 
}
?>

