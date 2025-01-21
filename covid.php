<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);


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

mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = 10000";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];

$el_usuario = $row_usuario['IDusuario'];


//por Diagnostico1
mysql_select_db($database_vacantes, $vacantes);
$query_diagn1 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1, sum(case when IDarea = 2 then 1 else 0 end) as Area2, sum(case when IDarea = 3 then 1 else 0 end) as Area3, sum(case when IDarea = 4 then 1 else 0 end) as Area4, sum(case when IDarea = 5 then 1 else 0 end) as Area5, sum(case when IDarea = 6 then 1 else 0 end) as Area6, sum(case when IDarea = 7 then 1 else 0 end) as Area7,  sum(case when IDarea = 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 then 1 else 0 end) as Area9, sum(case when IDarea = 10 then 1 else 0 end) as Area10, sum(case when IDarea = 11 then 1 else 0 end) as Area11 FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDmotivo = 1";
$diagn1 = mysql_query($query_diagn1, $vacantes) or die(mysql_error());
$row_diagn1 = mysql_fetch_assoc($diagn1);
$totalRows_diagn1 = mysql_num_rows($diagn1);

$logistica1 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area11'];
$ventas1  = $row_diagn1['Area5'] + $row_diagn1['Area6'];
$compras1  = $row_diagn1['Area7'];
$finanzas1  = $row_diagn1['Area8'];					
$sistemas1  = $row_diagn1['Area9'];					
$rh1  = $row_diagn1['Area10'];
$total1 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area5'] + $row_diagn1['Area6'] + $row_diagn1['Area7'] + $row_diagn1['Area8'] + $row_diagn1['Area9'] + $row_diagn1['Area10'] + $row_diagn1['Area11'];

//por Diagnostico2
mysql_select_db($database_vacantes, $vacantes);
$query_diagn1 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1, sum(case when IDarea = 2 then 1 else 0 end) as Area2, sum(case when IDarea = 3 then 1 else 0 end) as Area3, sum(case when IDarea = 4 then 1 else 0 end) as Area4, sum(case when IDarea = 5 then 1 else 0 end) as Area5, sum(case when IDarea = 6 then 1 else 0 end) as Area6, sum(case when IDarea = 7 then 1 else 0 end) as Area7,  sum(case when IDarea = 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 then 1 else 0 end) as Area9, sum(case when IDarea = 10 then 1 else 0 end) as Area10, sum(case when IDarea = 11 then 1 else 0 end) as Area11 FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  wHERE cov_casos.IDmotivo = 2";
$diagn2 = mysql_query($query_diagn1, $vacantes) or die(mysql_error());
$row_diagn1 = mysql_fetch_assoc($diagn2);
$totalRows_diagn1 = mysql_num_rows($diagn2);

$logistica2 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area11'];
$ventas2  = $row_diagn1['Area5'] + $row_diagn1['Area6'];
$compras2  = $row_diagn1['Area7'];
$finanzas2  = $row_diagn1['Area8'];					
$sistemas2  = $row_diagn1['Area9'];					
$rh2  = $row_diagn1['Area10'];
$total2 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area5'] + $row_diagn1['Area6'] + $row_diagn1['Area7'] + $row_diagn1['Area8'] + $row_diagn1['Area9'] + $row_diagn1['Area10'] + $row_diagn1['Area11'];

//por Diagnostico3
mysql_select_db($database_vacantes, $vacantes);
$query_diagn1 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1, sum(case when IDarea = 2 then 1 else 0 end) as Area2, sum(case when IDarea = 3 then 1 else 0 end) as Area3, sum(case when IDarea = 4 then 1 else 0 end) as Area4, sum(case when IDarea = 5 then 1 else 0 end) as Area5, sum(case when IDarea = 6 then 1 else 0 end) as Area6, sum(case when IDarea = 7 then 1 else 0 end) as Area7,  sum(case when IDarea = 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 then 1 else 0 end) as Area9, sum(case when IDarea = 10 then 1 else 0 end) as Area10, sum(case when IDarea = 11 then 1 else 0 end) as Area11 FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  wHERE cov_casos.IDmotivo = 3";
$diagn3 = mysql_query($query_diagn1, $vacantes) or die(mysql_error());
$row_diagn1 = mysql_fetch_assoc($diagn3);
$totalRows_diagn1 = mysql_num_rows($diagn3);

$logistica3 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area11'];
$ventas3  = $row_diagn1['Area5'] + $row_diagn1['Area6'];
$compras3  = $row_diagn1['Area7'];
$finanzas3  = $row_diagn1['Area8'];					
$sistemas3  = $row_diagn1['Area9'];					
$rh3  = $row_diagn1['Area10'];
$total3 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area5'] + $row_diagn1['Area6'] + $row_diagn1['Area7'] + $row_diagn1['Area8'] + $row_diagn1['Area9'] + $row_diagn1['Area10'] + $row_diagn1['Area11'];

//por Diagnostico4
mysql_select_db($database_vacantes, $vacantes);
$query_diagn1 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1, sum(case when IDarea = 2 then 1 else 0 end) as Area2, sum(case when IDarea = 3 then 1 else 0 end) as Area3, sum(case when IDarea = 4 then 1 else 0 end) as Area4, sum(case when IDarea = 5 then 1 else 0 end) as Area5, sum(case when IDarea = 6 then 1 else 0 end) as Area6, sum(case when IDarea = 7 then 1 else 0 end) as Area7,  sum(case when IDarea = 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 then 1 else 0 end) as Area9, sum(case when IDarea = 10 then 1 else 0 end) as Area10, sum(case when IDarea = 11 then 1 else 0 end) as Area11 FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  wHERE cov_casos.IDmotivo = 4";
$diagn4 = mysql_query($query_diagn1, $vacantes) or die(mysql_error());
$row_diagn1 = mysql_fetch_assoc($diagn4);
$totalRows_diagn1 = mysql_num_rows($diagn4);

$logistica4 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area11'];
$ventas4  = $row_diagn1['Area5'] + $row_diagn1['Area6'];
$compras4  = $row_diagn1['Area7'];
$finanzas4  = $row_diagn1['Area8'];					
$sistemas4  = $row_diagn1['Area9'];					
$rh4  = $row_diagn1['Area10'];
$total4 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area5'] + $row_diagn1['Area6'] + $row_diagn1['Area7'] + $row_diagn1['Area8'] + $row_diagn1['Area9'] + $row_diagn1['Area10'] + $row_diagn1['Area11'];

//por Diagnostico5
mysql_select_db($database_vacantes, $vacantes);
$query_diagn1 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1, sum(case when IDarea = 2 then 1 else 0 end) as Area2, sum(case when IDarea = 3 then 1 else 0 end) as Area3, sum(case when IDarea = 4 then 1 else 0 end) as Area4, sum(case when IDarea = 5 then 1 else 0 end) as Area5, sum(case when IDarea = 6 then 1 else 0 end) as Area6, sum(case when IDarea = 7 then 1 else 0 end) as Area7,  sum(case when IDarea = 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 then 1 else 0 end) as Area9, sum(case when IDarea = 10 then 1 else 0 end) as Area10, sum(case when IDarea = 11 then 1 else 0 end) as Area11 FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  wHERE cov_casos.IDmotivo = 5";
$diagn5 = mysql_query($query_diagn1, $vacantes) or die(mysql_error());
$row_diagn1 = mysql_fetch_assoc($diagn5);
$totalRows_diagn1 = mysql_num_rows($diagn5);

$logistica5 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area11'];
$ventas5  = $row_diagn1['Area5'] + $row_diagn1['Area6'];
$compras5  = $row_diagn1['Area7'];
$finanzas5  = $row_diagn1['Area8'];					
$sistemas5  = $row_diagn1['Area9'];					
$rh5  = $row_diagn1['Area10'];
$total5 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area5'] + $row_diagn1['Area6'] + $row_diagn1['Area7'] + $row_diagn1['Area8'] + $row_diagn1['Area9'] + $row_diagn1['Area10'] + $row_diagn1['Area11'];

//por Diagnostico6
mysql_select_db($database_vacantes, $vacantes);
$query_diagn1 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1, sum(case when IDarea = 2 then 1 else 0 end) as Area2, sum(case when IDarea = 3 then 1 else 0 end) as Area3, sum(case when IDarea = 4 then 1 else 0 end) as Area4, sum(case when IDarea = 5 then 1 else 0 end) as Area5, sum(case when IDarea = 6 then 1 else 0 end) as Area6, sum(case when IDarea = 7 then 1 else 0 end) as Area7,  sum(case when IDarea = 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 then 1 else 0 end) as Area9, sum(case when IDarea = 10 then 1 else 0 end) as Area10, sum(case when IDarea = 11 then 1 else 0 end) as Area11 FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  wHERE cov_casos.IDmotivo = 6";
$diagn6 = mysql_query($query_diagn1, $vacantes) or die(mysql_error());
$row_diagn1 = mysql_fetch_assoc($diagn6);
$totalRows_diagn1 = mysql_num_rows($diagn6);

$logistica6 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area11'];
$ventas6  = $row_diagn1['Area5'] + $row_diagn1['Area6'];
$compras6  = $row_diagn1['Area7'];
$finanzas6  = $row_diagn1['Area8'];					
$sistemas6  = $row_diagn1['Area9'];					
$rh6  = $row_diagn1['Area10'];
$total6 = $row_diagn1['Area1'] + $row_diagn1['Area2'] + $row_diagn1['Area3'] + $row_diagn1['Area4'] + $row_diagn1['Area5'] + $row_diagn1['Area6'] + $row_diagn1['Area7'] + $row_diagn1['Area8'] + $row_diagn1['Area9'] + $row_diagn1['Area10'] + $row_diagn1['Area11'];

//por Diagnostico6
mysql_select_db($database_vacantes, $vacantes);
$query_diagn7 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1, sum(case when IDarea = 2 then 1 else 0 end) as Area2, sum(case when IDarea = 3 then 1 else 0 end) as Area3, sum(case when IDarea = 4 then 1 else 0 end) as Area4, sum(case when IDarea = 5 then 1 else 0 end) as Area5, sum(case when IDarea = 6 then 1 else 0 end) as Area6, sum(case when IDarea = 7 then 1 else 0 end) as Area7,  sum(case when IDarea = 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 then 1 else 0 end) as Area9, sum(case when IDarea = 10 then 1 else 0 end) as Area10, sum(case when IDarea = 11 then 1 else 0 end) as Area11 FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDmotivo = 7";
$diagn6 = mysql_query($query_diagn7, $vacantes) or die(mysql_error());
$row_diagn7 = mysql_fetch_assoc($diagn6);
$totalRows_diagn7 = mysql_num_rows($diagn6);

$logistica7 = $row_diagn7['Area1'] + $row_diagn7['Area2'] + $row_diagn7['Area3'] + $row_diagn7['Area4'] + $row_diagn7['Area11'];
$ventas7  = $row_diagn7['Area5'] + $row_diagn7['Area6'];
$compras7  = $row_diagn7['Area7'];
$finanzas7  = $row_diagn7['Area8'];					
$sistemas7  = $row_diagn7['Area9'];					
$rh7  = $row_diagn7['Area10'];
$total7 = $row_diagn7['Area1'] + $row_diagn7['Area2'] + $row_diagn7['Area3'] + $row_diagn7['Area4'] + $row_diagn7['Area5'] + $row_diagn7['Area6'] + $row_diagn7['Area7'] + $row_diagn7['Area8'] + $row_diagn7['Area9'] + $row_diagn7['Area10'] + $row_diagn7['Area11'];


$logistica8 = $logistica1 + $logistica2 + $logistica3 + $logistica4 + $logistica5 + $logistica6 + $logistica7;
$ventas8 = $ventas1 + $ventas2 + $ventas3 + $ventas4 + $ventas5 + $ventas6 + $ventas7;
$compras8 = $compras1 + $compras2 + $compras3 + $compras4 + $compras5 + $compras6 + $compras7;
$finanzas8 = $finanzas1 + $finanzas2 + $finanzas3 + $finanzas4 + $finanzas5 + $finanzas6 + $finanzas7;
$sistemas8 = $sistemas1 + $sistemas2 + $sistemas3 + $sistemas4 + $sistemas5 + $sistemas6 + $sistemas7;
$rh8 = $rh1 + $rh2 + $rh3 + $rh4 + $rh5 + $rh6 + $rh7;
$total8 = $total1 + $total2 + $total3 + $total4 + $total5 + $total6 + $total7;


//por reemplazo
mysql_select_db($database_vacantes, $vacantes);
$query_reemp1 = "SELECT  sum(case when IDarea = 1 AND IDmotivo < 8 then 1 else 0 end) as Area1,  sum(case when IDarea = 2 AND IDmotivo < 8 then 1 else 0 end) as Area2,  sum(case when IDarea = 3 AND IDmotivo < 8 then 1 else 0 end) as Area3,  sum(case when IDarea = 4 AND IDmotivo < 8 then 1 else 0 end) as Area4,  sum(case when IDarea = 5 AND IDmotivo < 8 then 1 else 0 end) as Area5, sum(case when IDarea = 6 AND IDmotivo < 8 then 1 else 0 end) as Area6,sum(case when IDarea = 7 AND IDmotivo < 8 then 1 else 0 end) as Area7,sum(case when IDarea = 8 AND IDmotivo < 8 then 1 else 0 end) as Area8,sum(case when IDarea = 9 AND IDmotivo < 8 then 1 else 0 end) as Area9, sum(case when IDarea = 10 AND IDmotivo < 8 then 1 else 0 end) as Area10, sum(case when IDarea = 11 AND IDmotivo < 8 then 1 else 0 end) as Area11 FROM cov_casos WHERE cov_casos.IDreemplazo = 1";
$reemp1 = mysql_query($query_reemp1, $vacantes) or die(mysql_error());
$row_reemp1 = mysql_fetch_assoc($reemp1);
$totalRows_reemp1 = mysql_num_rows($reemp1);

$logisticar1 = $row_reemp1['Area1'] + $row_reemp1['Area2'] + $row_reemp1['Area3'] + $row_reemp1['Area4'] + $row_reemp1['Area11'];
$ventasr1  = $row_reemp1['Area5'] + $row_reemp1['Area6'];
$comprasr1  = $row_reemp1['Area7'];
$finanzasr1  = $row_reemp1['Area8'];					
$sistemasr1  = $row_reemp1['Area9'];					
$rhr1  = $row_reemp1['Area10'];
$totalr1 = $row_reemp1['Area1'] + $row_reemp1['Area2'] + $row_reemp1['Area3'] + $row_reemp1['Area4'] + $row_reemp1['Area5'] + $row_reemp1['Area6'] + $row_reemp1['Area7'] + $row_reemp1['Area8'] + $row_reemp1['Area9'] + $row_reemp1['Area10'] + $row_reemp1['Area11'];

//por reemplazo
mysql_select_db($database_vacantes, $vacantes);
$query_reemp2 = "SELECT  sum(case when IDarea = 1 AND IDmotivo < 8 then 1 else 0 end) as Area1, sum(case when IDarea = 2 AND IDmotivo < 8 then 1 else 0 end) as Area2,  sum(case when IDarea = 3 AND IDmotivo < 8 then 1 else 0 end) as Area3,  sum(case when IDarea = 4 AND IDmotivo < 8 then 1 else 0 end) as Area4,  sum(case when IDarea = 5 AND IDmotivo < 8 then 1 else 0 end) as Area5, sum(case when IDarea = 6 AND IDmotivo < 8 then 1 else 0 end) as Area6,sum(case when IDarea = 7 AND IDmotivo < 8 then 1 else 0 end) as Area7,sum(case when IDarea = 8 AND IDmotivo < 8 then 1 else 0 end) as Area8, sum(case when IDarea = 9 AND IDmotivo < 8 then 1 else 0 end) as Area9, sum(case when IDarea = 10 AND IDmotivo < 8 then 1 else 0 end) as Area10, sum(case when IDarea = 11 AND IDmotivo < 8 then 1 else 0 end) as Area11 FROM cov_casos WHERE cov_casos.IDreemplazo = 0";
$reemp2 = mysql_query($query_reemp2, $vacantes) or die(mysql_error());
$row_reemp2 = mysql_fetch_assoc($reemp2);
$totalRows_reemp2 = mysql_num_rows($reemp2);

$logisticar2 = $row_reemp2['Area1'] + $row_reemp2['Area2'] + $row_reemp2['Area3'] + $row_reemp2['Area4'] + $row_reemp2['Area11'];
$ventasr2  = $row_reemp2['Area5'] + $row_reemp2['Area6'];
$comprasr2  = $row_reemp2['Area7'];
$finanzasr2  = $row_reemp2['Area8'];					
$sistemasr2  = $row_reemp2['Area9'];					
$rhr2  = $row_reemp2['Area10'];
$totalr2 = $row_reemp2['Area1'] + $row_reemp2['Area2'] + $row_reemp2['Area3'] + $row_reemp2['Area4'] + $row_reemp2['Area5'] + $row_reemp2['Area6'] + $row_reemp2['Area7'] + $row_reemp2['Area8'] + $row_reemp2['Area9'] + $row_reemp2['Area10'] + $row_reemp2['Area11'];

$logisticar3 = $logisticar1 + $logisticar2;
$ventasr3 = $ventasr1 + $ventasr2;
$comprasr3 = $comprasr1 + $comprasr2;
$finanzasr3 = $finanzasr1 + $finanzasr2;
$sistemasr3 = $sistemasr1 + $sistemasr2;
$rhr3 = $rhr1 + $rhr2;
$totalr3 = $totalr1 + $totalr2;


//POR COSTO
mysql_select_db($database_vacantes, $vacantes);
$query_coost1 = "SELECT  sum(case when IDarea = 1 then cov_casos.sueldo_total else 0 end) as Area1,  sum(case when IDarea = 2 then cov_casos.sueldo_total else 0 end) as Area2,  sum(case when IDarea = 3 then cov_casos.sueldo_total else 0 end) as Area3,  sum(case when IDarea = 4 then cov_casos.sueldo_total else 0 end) as Area4,  sum(case when IDarea = 5 then cov_casos.sueldo_total else 0 end) as Area5,  sum(case when IDarea = 6 then cov_casos.sueldo_total else 0 end) as Area6,  sum(case when IDarea = 7 then cov_casos.sueldo_total else 0 end) as Area7,   sum(case when IDarea = 8 then cov_casos.sueldo_total else 0 end) as Area8,  sum(case when IDarea = 9 then cov_casos.sueldo_total else 0 end) as Area9,  sum(case when IDarea = 10 then cov_casos.sueldo_total else 0 end) as Area10,  sum(case when IDarea = 11 then cov_casos.sueldo_total else 0 end) as Area11  FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDreemplazo = 1";
$coost1 = mysql_query($query_coost1, $vacantes) or die(mysql_error());
$row_coost1 = mysql_fetch_assoc($coost1);
$totalRows_coost1 = mysql_num_rows($coost1);

$logisticaco1 = ($row_coost1['Area1'] + $row_coost1['Area2'] + $row_coost1['Area3'] + $row_coost1['Area4'] + $row_coost1['Area11']) * 1;
$ventasco1  = ($row_coost1['Area5'] + $row_coost1['Area6']) * 1;
$comprasco1  = $row_coost1['Area7'] * 1;
$finanzasco1 = $row_coost1['Area8'] * 1;					
$sistemasco1 = $row_coost1['Area9'] * 1;					
$rhco1  = $row_coost1['Area10'] * 1;
$totalco1 = $logisticaco1 + $ventasco1 + $comprasco1 + $finanzasco1 + $sistemasco1 + $rhco1;

//por COSTO2
mysql_select_db($database_vacantes, $vacantes);
$query_coost2 = "SELECT  sum(case when IDarea = 1 then cov_casos.sueldo_total else 0 end) as Area1,  sum(case when IDarea = 2 then cov_casos.sueldo_total else 0 end) as Area2,  sum(case when IDarea = 3 then cov_casos.sueldo_total else 0 end) as Area3,  sum(case when IDarea = 4 then cov_casos.sueldo_total else 0 end) as Area4,  sum(case when IDarea = 5 then cov_casos.sueldo_total else 0 end) as Area5,  sum(case when IDarea = 6 then cov_casos.sueldo_total else 0 end) as Area6,  sum(case when IDarea = 7 then cov_casos.sueldo_total else 0 end) as Area7,   sum(case when IDarea = 8 then cov_casos.sueldo_total else 0 end) as Area8,  sum(case when IDarea = 9 then cov_casos.sueldo_total else 0 end) as Area9,  sum(case when IDarea = 10 then cov_casos.sueldo_total else 0 end) as Area10,  sum(case when IDarea = 11 then cov_casos.sueldo_total else 0 end) as Area11  FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDreemplazo = 0";
$coost2 = mysql_query($query_coost2, $vacantes) or die(mysql_error());
$row_coost2 = mysql_fetch_assoc($coost2);
$totalRows_coost2 = mysql_num_rows($coost2);

$logisticaco2 = ($row_coost2['Area1'] + $row_coost2['Area2'] + $row_coost2['Area3'] + $row_coost2['Area4'] + $row_coost2['Area11']) * 1;
$ventasco2  = ($row_coost2['Area5'] + $row_coost2['Area6']) * 1;
$comprasco2  = $row_coost2['Area7'] * 1;
$finanzasco2  = $row_coost2['Area8'] * 1;					
$sistemasco2  = $row_coost2['Area9'] * 1;					
$rhco2  = $row_coost2['Area10'] * 1;
$totalco2 = $logisticaco2 + $ventasco2 + $comprasco2 + $finanzasco2 + $sistemasco2 + $rhco2;

$logisticaco3 = $logisticaco1 + $logisticaco2;
$ventasco3 = $ventasco1 + $ventasco2;
$comprasco3 = $comprasco1 + $comprasco2;
$finanzasco3 = $finanzasco1 + $finanzasco2;
$sistemasco3 = $sistemasco1 + $sistemasco2;
$rhco3 = $rhco1 + $rhco2;
$totalco3 = $totalco1 + $totalco2;

//por COSTO PULLS
mysql_select_db($database_vacantes, $vacantes);
$query_copull = "SELECT  sum(case when IDarea = 1 then cov_casos.sueldo_total else 0 end) as Area1,  sum(case when IDarea = 2 then cov_casos.sueldo_total else 0 end) as Area2,  sum(case when IDarea = 3 then cov_casos.sueldo_total else 0 end) as Area3,  sum(case when IDarea = 4 then cov_casos.sueldo_total else 0 end) as Area4,  sum(case when IDarea = 5 then cov_casos.sueldo_total else 0 end) as Area5,  sum(case when IDarea = 6 then cov_casos.sueldo_total else 0 end) as Area6,  sum(case when IDarea = 7 then cov_casos.sueldo_total else 0 end) as Area7,   sum(case when IDarea = 8 then cov_casos.sueldo_total else 0 end) as Area8,  sum(case when IDarea = 9 then cov_casos.sueldo_total else 0 end) as Area9,  sum(case when IDarea = 10 then cov_casos.sueldo_total else 0 end) as Area10,  sum(case when IDarea = 11 then cov_casos.sueldo_total else 0 end) as Area11  FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDreemplazo = 1";
$copull = mysql_query($query_copull, $vacantes) or die(mysql_error());
$row_copull = mysql_fetch_assoc($copull);
$totalRows_copull = mysql_num_rows($copull);

$logisticapull = ($row_copull['Area1'] + $row_copull['Area2'] + $row_copull['Area3'] + $row_copull['Area4'] + $row_copull['Area11']) * 1.3;
$ventaspull  = ($row_copull['Area5'] + $row_copull['Area6']) * 1.3;
$compraspull  = $row_copull['Area7'] * 1.3;
$finanzaspull  = $row_copull['Area8'] * 1.3;					
$sistemaspull  = $row_copull['Area9'] * 1.3;					
$rhpull  = $row_copull['Area10'] * 1.3;
$totalpull = $logisticapull + $ventaspull + $compraspull + $finanzaspull + $sistemaspull + $rhpull;

//por TOTALES PULLS
mysql_select_db($database_vacantes, $vacantes);
$query_copull2 = "SELECT  sum(case when IDarea = 1  then 1 else 0 end) as Area1,  sum(case when IDarea = 2  then 1 else 0 end) as Area2,  sum(case when IDarea = 3  then 1 else 0 end) as Area3,  sum(case when IDarea = 4  then 1 else 0 end) as Area4,  sum(case when IDarea = 5  then 1 else 0 end) as Area5, sum(case when IDarea = 6  then 1 else 0 end) as Area6, sum(case when IDarea = 7  then 1 else 0 end) as Area7, sum(case when IDarea = 8  then 1 else 0 end) as Area8, sum(case when IDarea = 9  then 1 else 0 end) as Area9, sum(case when IDarea = 10  then 1 else 0 end) as Area10, sum(case when IDarea = 11  then 1 else 0 end) as Area11 FROM cov_casos WHERE cov_casos.IDreemplazo = 1";
$copull2 = mysql_query($query_copull2, $vacantes) or die(mysql_error());
$row_copull2 = mysql_fetch_assoc($copull2);
$totalRows_copull2 = mysql_num_rows($copull2);

$logisticapull2 = $row_copull2['Area1'] + $row_copull2['Area2'] + $row_copull2['Area3'] + $row_copull2['Area4'] + $row_copull2['Area11'];
$ventaspull2  = $row_copull2['Area5'] + $row_copull2['Area6'];
$compraspull2  = $row_copull2['Area7'];
$finanzaspull2  = $row_copull2['Area8'];					
$sistemaspull2  = $row_copull2['Area9'];					
$rhpull2  = $row_copull2['Area10'];
$totalpull2 = $logisticapull2 + $ventaspull2 + $compraspull2 + $finanzaspull2 + $sistemaspull2 + $rhpull2;

// totales
mysql_select_db($database_vacantes, $vacantes);
$query_totales1 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus IN (1,5,9)";
$totales1 = mysql_query($query_totales1, $vacantes) or die(mysql_error());
$row_totales1 = mysql_fetch_assoc($totales1);
$totalRows_totales1 = mysql_num_rows($totales1);

// totales
mysql_select_db($database_vacantes, $vacantes);
$query_totales2 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus IN (2,6,10,11,12)";
$totales2 = mysql_query($query_totales2, $vacantes) or die(mysql_error());
$row_totales2 = mysql_fetch_assoc($totales2);
$totalRows_totales2 = mysql_num_rows($totales2);

// totales
mysql_select_db($database_vacantes, $vacantes);
$query_totales3 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus IN (3,7,8)";
$totales3 = mysql_query($query_totales3, $vacantes) or die(mysql_error());
$row_totales3 = mysql_fetch_assoc($totales3);
$totalRows_totales3 = mysql_num_rows($totales3);

// matrices con casos
mysql_select_db($database_vacantes, $vacantes);
$query_matriz_casos = "SELECT DISTINCT cov_casos.IDempleado, cov_casos.IDmatriz FROM cov_casos INNER JOIN prod_activos ON cov_casos.IDempleado = cov_casos.IDempleado  GROUP BY cov_casos.IDmatriz";
$matriz_casos = mysql_query($query_matriz_casos, $vacantes) or die(mysql_error());
$row_matriz_casos = mysql_fetch_assoc($matriz_casos);
$totalRows_matriz_casos = mysql_num_rows($matriz_casos);

// matrices con casos
mysql_select_db($database_vacantes, $vacantes);
$query_matriz_casos2 = "SELECT DISTINCT cov_casos.IDempleado, cov_casos.IDmatriz FROM cov_casos INNER JOIN prod_activos ON cov_casos.IDempleado = cov_casos.IDempleado  GROUP BY cov_casos.IDmatriz";
$matriz_casos2 = mysql_query($query_matriz_casos2, $vacantes) or die(mysql_error());
$row_matriz_casos2 = mysql_fetch_assoc($matriz_casos2);
$totalRows_matriz_casos2 = mysql_num_rows($matriz_casos2);

// matrices con casos
mysql_select_db($database_vacantes, $vacantes);
$query_matriz_estados = "SELECT DISTINCT vac_matriz.estado AS Estado, sum(case when IDestatus IN (1,2,3,5,6,7,8,9,10,11) then 1 else 0 end) as Totales FROM cov_casos RIGHT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz GROUP BY vac_matriz.estado";
$matriz_estados = mysql_query($query_matriz_estados, $vacantes) or die(mysql_error());
$row_matriz_estados = mysql_fetch_assoc($matriz_estados);
$totalRows_matriz_estados = mysql_num_rows($matriz_estados);


//CASOS POR caso1
mysql_select_db($database_vacantes, $vacantes);
$query_casos1 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1,  sum(case when IDarea = 2 then 1 else 0 end) as Area2,  sum(case when IDarea = 3 then 1 else 0 end) as Area3,  sum(case when IDarea = 4 then 1 else 0 end) as Area4,  sum(case when IDarea = 5 then 1 else 0 end) as Area5,  sum(case when IDarea = 6 then 1 else 0 end) as Area6,  sum(case when IDarea = 7 then 1 else 0 end) as Area7,   sum(case when IDarea = 8 then 1 else 0 end) as Area8,  sum(case when IDarea = 9 then 1 else 0 end) as Area9,  sum(case when IDarea = 10 then 1 else 0 end) as Area10,  sum(case when IDarea = 11 then 1 else 0 end) as Area11  FROM  cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDestatus IN (1,5,9)";
$casos1 = mysql_query($query_casos1, $vacantes) or die(mysql_error());
$row_casos1 = mysql_fetch_assoc($casos1);
$totalRows_casos1 = mysql_num_rows($casos1);

$logisticaca1 = $row_casos1['Area1'] + $row_casos1['Area2'] + $row_casos1['Area3'] + $row_casos1['Area4'] + $row_casos1['Area11'];
$ventasca1  = $row_casos1['Area5'] + $row_casos1['Area6'];
$comprasca1  = $row_casos1['Area7'];
$finanzasca1  = $row_casos1['Area8'];					
$sistemasca1  = $row_casos1['Area9'];					
$rhca1  = $row_casos1['Area10'];
$totalca1 = $row_casos1['Area1'] + $row_casos1['Area2'] + $row_casos1['Area3'] + $row_casos1['Area4'] + $row_casos1['Area5'] + $row_casos1['Area6'] + $row_casos1['Area7'] + $row_casos1['Area8'] + $row_casos1['Area9'] + $row_casos1['Area10'] + $row_casos1['Area11'];


//CASOS POR caso2
mysql_select_db($database_vacantes, $vacantes);
$query_casos2 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1,  sum(case when IDarea = 2 then 1 else 0 end) as Area2,  sum(case when IDarea = 3 then 1 else 0 end) as Area3,  sum(case when IDarea = 4 then 1 else 0 end) as Area4,  sum(case when IDarea = 5 then 1 else 0 end) as Area5,  sum(case when IDarea = 6 then 1 else 0 end) as Area6,  sum(case when IDarea = 7 then 1 else 0 end) as Area7,   sum(case when IDarea = 8 then 1 else 0 end) as Area8,  sum(case when IDarea = 9 then 1 else 0 end) as Area9,  sum(case when IDarea = 10 then 1 else 0 end) as Area10,  sum(case when IDarea = 11 then 1 else 0 end) as Area11  FROM  cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDestatus IN (2,6,10,11,12)";
$casos2 = mysql_query($query_casos2, $vacantes) or die(mysql_error());
$row_casos2 = mysql_fetch_assoc($casos2);
$totalRows_casos2 = mysql_num_rows($casos2);

$logisticaca2 = $row_casos2['Area1'] + $row_casos2['Area2'] + $row_casos2['Area3'] + $row_casos2['Area4'] + $row_casos2['Area11'];
$ventasca2  = $row_casos2['Area5'] + $row_casos2['Area6'];
$comprasca2  = $row_casos2['Area7'];
$finanzasca2  = $row_casos2['Area8'];					
$sistemasca2  = $row_casos2['Area9'];					
$rhca2  = $row_casos2['Area10'];
$totalca2 = $row_casos2['Area1'] + $row_casos2['Area2'] + $row_casos2['Area3'] + $row_casos2['Area4'] + $row_casos2['Area5'] + $row_casos2['Area6'] + $row_casos2['Area7'] + $row_casos2['Area8'] + $row_casos2['Area9'] + $row_casos2['Area10'] + $row_casos2['Area11'];

//CASOS POR caso3
mysql_select_db($database_vacantes, $vacantes);
$query_casos3 = "SELECT sum(case when IDarea = 1 then 1 else 0 end) as Area1,  sum(case when IDarea = 2 then 1 else 0 end) as Area2,  sum(case when IDarea = 3 then 1 else 0 end) as Area3,  sum(case when IDarea = 4 then 1 else 0 end) as Area4,  sum(case when IDarea = 5 then 1 else 0 end) as Area5,  sum(case when IDarea = 6 then 1 else 0 end) as Area6,  sum(case when IDarea = 7 then 1 else 0 end) as Area7,   sum(case when IDarea = 8 then 1 else 0 end) as Area8,  sum(case when IDarea = 9 then 1 else 0 end) as Area9,  sum(case when IDarea = 10 then 1 else 0 end) as Area10,  sum(case when IDarea = 11 then 1 else 0 end) as Area11  FROM  cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE cov_casos.IDestatus IN (3,7,8)";
$casos3 = mysql_query($query_casos3, $vacantes) or die(mysql_error());
$row_casos3 = mysql_fetch_assoc($casos3);
$totalRows_casos3 = mysql_num_rows($casos3);

$logisticaca3 = $row_casos3['Area1'] + $row_casos3['Area2'] + $row_casos3['Area3'] + $row_casos3['Area4'] + $row_casos3['Area11'];
$ventasca3  = $row_casos3['Area5'] + $row_casos3['Area6'];
$comprasca3  = $row_casos3['Area7'];
$finanzasca3  = $row_casos3['Area8'];					
$sistemasca3  = $row_casos3['Area9'];					
$rhca3  = $row_casos3['Area10'];
$totalca3 = $row_casos3['Area1'] + $row_casos3['Area2'] + $row_casos3['Area3'] + $row_casos3['Area4'] + $row_casos3['Area5'] + $row_casos3['Area6'] + $row_casos3['Area7'] + $row_casos3['Area8'] + $row_casos3['Area9'] + $row_casos3['Area10'] + $row_casos3['Area11'];

$logisticaca4 = $logisticaca1 + $logisticaca2 + $logisticaca3;
$ventasca4 = $ventasca1 + $ventasca2 + $ventasca3;
$comprasca4 = $comprasca1 + $comprasca2 + $comprasca3;
$finanzasca4 = $finanzasca1 + $finanzasca2 + $finanzasca3;
$sistemasca4 = $sistemasca1 + $sistemasca2 + $sistemasca3;
$rhca4 = $rhca1 + $rhca2 + $rhca3;
$totalca4 = $totalca1 + $totalca2 + $totalca3;


mysql_select_db($database_vacantes, $vacantes);
$query_comentarios = "SELECT  cov_casos.observaciones, cov_casos.IDempleado, vac_matriz.matriz, vac_puestos.denominacion  FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz INNER JOIN vac_puestos ON cov_casos.IDpuesto = vac_puestos.IDpuesto WHERE cov_casos.IDestatus IN (1,5,9)";
$comentarios = mysql_query($query_comentarios, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_comentarios = mysql_fetch_assoc($comentarios);
$totalRows_comentarios = mysql_num_rows($comentarios);

mysql_select_db($database_vacantes, $vacantes);
$query_comentarios2 = "SELECT cov_casos.observaciones, cov_casos.enf_respiratoria, cov_casos.tratam_inicio, cov_casos.enfermedad_general, cov_casos.tratam_fin, cov_casos.IDempleado, vac_matriz.matriz, cov_casos.emp_paterno, cov_casos.emp_materno, cov_casos.emp_nombre, vac_puestos.denominacion FROM cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = cov_casos.IDpuesto WHERE cov_casos.enf_respiratoria = 1 AND vac_matriz.IDmatriz IN ($mis_matrizes)"; 
$comentarios2 = mysql_query($query_comentarios2, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_comentarios2 = mysql_fetch_assoc($comentarios2);
$totalRows_comentarios2 = mysql_num_rows($comentarios2);


// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales13 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 1 GROUP BY cov_casos.IDestatus";
$totales13 = mysql_query($query_totales13, $vacantes) or die(mysql_error());
$row_totales13 = mysql_fetch_assoc($totales13);
$totalRows_totales13 = mysql_num_rows($totales13);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales14 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 5 GROUP BY cov_casos.IDestatus";
$totales14 = mysql_query($query_totales14, $vacantes) or die(mysql_error());
$row_totales14 = mysql_fetch_assoc($totales14);
$totalRows_totales14 = mysql_num_rows($totales14);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales6 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 6 GROUP BY cov_casos.IDestatus";
$totales6 = mysql_query($query_totales6, $vacantes) or die(mysql_error());
$row_totales6 = mysql_fetch_assoc($totales6);
$totalRows_totales6 = mysql_num_rows($totales6);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales7 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 7 GROUP BY cov_casos.IDestatus";
$totales7 = mysql_query($query_totales7, $vacantes) or die(mysql_error());
$row_totales7 = mysql_fetch_assoc($totales7);
$totalRows_totales7 = mysql_num_rows($totales7);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales8 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 8 GROUP BY cov_casos.IDestatus";
$totales8 = mysql_query($query_totales8, $vacantes) or die(mysql_error());
$row_totales8 = mysql_fetch_assoc($totales8);
$totalRows_totales8 = mysql_num_rows($totales8);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales5 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 9 GROUP BY cov_casos.IDestatus";
$totales5 = mysql_query($query_totales5, $vacantes) or die(mysql_error());
$row_totales5 = mysql_fetch_assoc($totales5);
$totalRows_totales5 = mysql_num_rows($totales5);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales50 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 13 GROUP BY cov_casos.IDestatus";
$totales50 = mysql_query($query_totales50, $vacantes) or die(mysql_error());
$row_totales50 = mysql_fetch_assoc($totales50);
$totalRows_totales50 = mysql_num_rows($totales50);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales9 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 10 GROUP BY cov_casos.IDestatus";
$totales9 = mysql_query($query_totales9, $vacantes) or die(mysql_error());
$row_totales9 = mysql_fetch_assoc($totales9);
$totalRows_totales9 = mysql_num_rows($totales9);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales11 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 11 GROUP BY cov_casos.IDestatus";
$totales11 = mysql_query($query_totales11, $vacantes) or die(mysql_error());
$row_totales11 = mysql_fetch_assoc($totales11);
$totalRows_totales11 = mysql_num_rows($totales11);

// subtotales
mysql_select_db($database_vacantes, $vacantes);
$query_totales12 = "SELECT Count(cov_casos.IDcovid) AS Total FROM cov_casos WHERE cov_casos.IDestatus = 12 GROUP BY cov_casos.IDestatus";
$totales12 = mysql_query($query_totales12, $vacantes) or die(mysql_error());
$row_totales12 = mysql_fetch_assoc($totales12);
$totalRows_totales12 = mysql_num_rows($totales12);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/sucursal.js"></script>
	<script src="global_assets/js/sucursal2.js"></script>
	<script src="global_assets/js/area.js"></script>

	<script src="assets/js/app.js"></script>
    <script src="global_assets/js/demo_pages/components_thumbnails.js"></script>

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="assets/mapa.js"></script>
	<script type="text/javascript">
<?php
do { echo 'var ' . $row_matriz_estados['Estado'] . ' = ' . $row_matriz_estados['Totales'] . '; ';}
while ($row_matriz_estados = mysql_fetch_assoc($matriz_estados)); 
?>
</script>
</head>
<body class="sidebar-xs">
<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">
 			<!-- Main content -->
			<div class="content-wrapper">
          		<?php require_once('assets/pheader.php'); ?>
			<!-- Content area -->
				<div class="content">
                
               			<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 3)) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han restablecido el password correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte COVID</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido(a) <?php echo $row_usuario['usuario_nombre']; ?> al Reporte del Estatus de Contingencia COVID en Sahuayo.</p>
							<p>La información reportada a continuación se actualiza de diariamente.</p>
							<p>Para cualquier duda o aclaración respecto del presente contenido, favor de contactar con Recursos Humanos de tu Sucursal.</p>
                   </div>
					  </div>

                  <!-- Statistics with progress bar -->
					<div class="row">
						<div class="col-sm-6 col-md-6">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Total de Casos <strong>Positivos</strong></h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-pulse2 icon-2x text-danger-400 opacity-100"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-danger-400" style="width: 100%">
									</div>
								</div>
				               <h4><?php echo $row_totales1['Total']; ?> Casos </h4> 
							   <?php if ($row_totales13['Total'] > 0) 
							   { echo $row_totales13['Total'] . " en recuperación ";} else { echo "0 en recuperación ";}?>
							   <?php if ($row_totales14['Total'] > 0) 
							   { echo " | " . $row_totales14['Total'] . " recuperados ";} else { echo "| 0 recuperados ";}?>
							   <?php if ($row_totales50['Total'] > 0) 
							   { echo " | " . $row_totales50['Total'] . " hospitalizados";} else { echo "| 0 hospitalizados ";}?>
							   <?php $TTT = $row_totales5['Total'] + $row_totales11['Total']; if ($row_totales5['Total'] > 0) 
							   { echo " | " . $TTT . " decesos ";} else { echo "| 0 decesos ";}?>
							</div>
						</div>

						<div class="col-sm-6 col-md-6">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Total de Casos <strong>Sospechosos</strong></h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-pulse2 icon-2x text-warning-400 opacity-100"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-warning-400" style="width: 100%">
									</div>
								</div>
				               <h4><?php echo $row_totales2['Total'];  ?> Casos </h4>
							   <?php if ($row_totales6['Total'] > 0) 
							   { echo $row_totales6['Total'] . " en aislamiento";}  else { echo "0 en aislamiento";}?>
                               <?php if ($row_totales9['Total'] > 0)
							   { echo " | " . $row_totales9['Total'] . " reingresados";}  else { echo " | 0 reingresados";}?>
                               <?php if ($row_totales12['Total'] > 0)
							   { echo " | " . $row_totales12['Total'] . " hospitalizados";}  else { echo " | 0 hospitalizados";}?>
							</div>
						</div>

					</div>

					<!-- /statistics with progress bar -->

					<!-- Grid -->
					<div class="row">
					
					

						<div class="col-md-6">
            				<!-- Horizontal a -->
						  <div class="panel panel-flat">
							<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder-heart position-left"></i>
										Casos por Sucursal
									</h6>
		                	  </div>
							<div class="table-responsive">
							<table class="table table-xxs">
								  <thead>
                                  <tr class="bg-danger text-center">
								    <td>SUCURSAL</td>
								    <td>POSITIVO</td>
								    <td>SOSPECHOSO</td>
								    <td>POR CONTACTO</td>
								    <td>TOTAL</td>
								    </thead>
								<tbody>
								<?php
                                do {
                                $la_matr = $row_matriz_casos['IDmatriz'];
                                mysql_select_db($database_vacantes, $vacantes);
                                $query_porsuc = "SELECT 
								sum(case when IDestatus IN (1,5,9,13) then 1 else 0 end) as Motivo1,
								sum(case when IDestatus IN (2,6,10,11,12) then 1 else 0 end) as Motivo2,  
								sum(case when IDestatus IN (3,7,8) then 1 else 0 end) as Motivo3, 
								vac_matriz.matriz 
								FROM 
								cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE vac_matriz.IDmatriz = '$la_matr'";
                                $porsuc = mysql_query($query_porsuc, $vacantes) or die(mysql_error());
                                $row_porsuc = mysql_fetch_assoc($porsuc);
                                $totalRows_porsuc = mysql_num_rows($porsuc);
                                $motivo1t = $row_porsuc['Motivo1'] +  $row_porsuc['Motivo2'] +  $row_porsuc['Motivo3'];
                                if ($motivo1t > 0) {?>
                                <tr class="text-center">
								    <td><?php echo $row_porsuc['matriz']; ?></td>
								    <td><?php echo $row_porsuc['Motivo1']; ?></td>
								    <td><?php echo $row_porsuc['Motivo2']; ?></td>
								    <td><?php echo $row_porsuc['Motivo3']; ?></td>
								    <td><strong><?php echo $motivo1t; ?></strong></td>
                                 </tr>
								<?php } } while ($row_matriz_casos = mysql_fetch_assoc($matriz_casos)); ?>

                                <?php    mysql_select_db($database_vacantes, $vacantes);
                                $query_porsucT = "SELECT 
								sum(case when IDestatus IN (1,5,9,13) then 1 else 0 end) as Motivo1,
								sum(case when IDestatus IN (2,6,10,11,12) then 1 else 0 end) as Motivo2,  
								sum(case when IDestatus IN (3,7,8) then 1 else 0 end) as Motivo3, 
								vac_matriz.matriz 
								FROM 
								cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz";
                                $porsucT = mysql_query($query_porsucT, $vacantes) or die(mysql_error());
                                $row_porsucT = mysql_fetch_assoc($porsucT);
                                $totalRows_porsucT = mysql_num_rows($porsucT);
                                $motivo1tT = $row_porsucT['Motivo1'] +  $row_porsucT['Motivo2'] +  $row_porsucT['Motivo3'];
                                ?>   
                                <tr class="text-center">
								    <td><strong>TOTAL</strong></td>
								    <td><strong><?php echo $row_porsucT['Motivo1']; ?></strong></td>
								    <td><strong><?php echo $row_porsucT['Motivo2']; ?></strong></td>
								    <td><strong><?php echo $row_porsucT['Motivo3']; ?></strong></td>
								    <td><strong><?php echo $motivo1tT; ?></strong></td>
                                 </tr>
						    </tbody>
                            </table>
							 </div>
                             </div>
						</div>
					<!-- /grid -->

						<div class="col-md-6">
							<!-- Horizontal a -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-location3 position-left"></i>
										Casos Positivos
									</h6>
								</div>
						<div class="table-responsive">
							<table class="table table-xxs">
								  <thead>
                                  <tr class="bg-danger text-center">
								    <td>SUCURSAL</td>
								    <td>POSITIVO ACTIVO</td>
								    <td>POSTIVO RECUPERADO</td>
								    <td>TOTAL</td>
								    </thead>
								<tbody>
								<?php
                                do {
                                $la_matr2 = $row_matriz_casos2['IDmatriz'];
                                mysql_select_db($database_vacantes, $vacantes);
									$query_porsuc2 = "SELECT 
									sum(case when IDestatus IN (1) then 1 else 0 end) as Motivo1,
									sum(case when IDestatus IN (5) then 1 else 0 end) as Motivo2,  
									vac_matriz.matriz 
									FROM 
									cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz  WHERE vac_matriz.IDmatriz = '$la_matr2'";
                                $porsuc2 = mysql_query($query_porsuc2, $vacantes) or die(mysql_error());
                                $row_porsuc2 = mysql_fetch_assoc($porsuc2);
                                $totalRows_porsuc2 = mysql_num_rows($porsuc2);
                                $motivo1t2 = $row_porsuc2['Motivo1'] +  $row_porsuc2['Motivo2'];
                                if ($motivo1t2 > 0) {?>
                                <tr class="text-center">
								    <td><?php echo $row_porsuc2['matriz']; ?></td>
								    <td><?php echo $row_porsuc2['Motivo1']; ?></td>
								    <td><?php echo $row_porsuc2['Motivo2']; ?></td>
								    <td><strong><?php echo $motivo1t2; ?></strong></td>
                                 </tr>
								<?php } } while ($row_matriz_casos2 = mysql_fetch_assoc($matriz_casos2)); ?>

                                <?php    mysql_select_db($database_vacantes, $vacantes);
                                $query_porsucT2 = "SELECT 
								sum(case when IDestatus IN (1) then 1 else 0 end) as Motivo1,
								sum(case when IDestatus IN (5) then 1 else 0 end) as Motivo2,  
								vac_matriz.matriz 
								FROM 
								cov_casos LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cov_casos.IDmatriz";
                                $porsucT2 = mysql_query($query_porsucT2, $vacantes) or die(mysql_error());
                                $row_porsucT2 = mysql_fetch_assoc($porsucT2);
                                $totalRows_porsucT2 = mysql_num_rows($porsucT2);
                                $motivo1tT2 = $row_porsucT2['Motivo1'] +  $row_porsucT2['Motivo2'];
                                ?>   
                                <tr class="text-center">
								    <td><strong>TOTAL</strong></td>
								    <td><strong><?php echo $row_porsucT2['Motivo1']; ?></strong></td>
								    <td><strong><?php echo $row_porsucT2['Motivo2']; ?></strong></td>
								    <td><strong><?php echo $motivo1tT2; ?></strong></td>
                                 </tr>
						    </tbody>
                            </table>
							 </div>
                                    
			                	</div>
							</div>
							<!-- /horizotal a -->

						</div>

						
						<div class="col-md-12">

							<!-- Horizontal a -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-location3 position-left"></i>
										Casos por Estado
									</h6>
                                    <div id="geochart-colors"></div>
                                    
			                	</div>
							</div>
							<!-- /horizotal a -->

						</div>


						<div class="col-md-12">
							<div class="panel panel-flat">
							<div class="panel-heading">
                            <h6 class="text-semibold panel-title">
                                <i class="icon-pencil4 position-left"></i>
                                Información de Casos Positivos:
                            </h6>
		                	</div>

                             <div class="table-wrapper-scroll-y my-custom-scrollbar">
							<table class="table table-xxs">
								<tbody>
                                	<?php do { ?>
                                 <tr> 
								    <td><?php echo $row_comentarios['matriz']; ?></td>
								    <td><?php echo $row_comentarios['denominacion']; ?></td>
								    <td><?php echo str_replace("\n", "<br>", $row_comentarios['observaciones']); ?></td>
                                 </tr>
                                 <?php } while ($row_comentarios = mysql_fetch_assoc($comentarios)); ?>
						    </tbody>
                            </table>
							 </div>
							 </div>
						</div>


						<div class="col-md-12">
							<!-- Horizontal a -->
						  <div class="panel panel-flat">
							<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder-heart position-left"></i>
										Casos por Área
									</h6>
		                	  </div>
							<div class="table-responsive">
							<table class="table datatable-show-all">
								  <thead>
                                  <tr class="bg-danger text-center">
								    <td>ESTATUS</td>
								    <td>LOGÍSTICA</td>
								    <td>VENTAS</td>
								    <td>COMPRAS</td>
								    <td>FINANZAS</td>
								    <td>SISTEMAS</td>
								    <td>R.H.</td>
								    <td>TOTAL</td>
							      </tr>
								 </thead>
								<tbody>
                                 <tr class="text-center">
								    <td>POSITIVO</td>
								    <td><?php echo $logisticaca1; ?></td>
								    <td><?php echo $ventasca1; ?></td>
								    <td><?php echo $comprasca1; ?></td>
								    <td><?php echo $finanzasca1; ?></td>
								    <td><?php echo $sistemasca1; ?></td>
								    <td><?php echo $rhca1; ?></td>
								    <td><?php echo $totalca1; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>SOSPECHOSO</td>
								    <td><?php echo $logisticaca2; ?></td>
								    <td><?php echo $ventasca2; ?></td>
								    <td><?php echo $comprasca2; ?></td>
								    <td><?php echo $finanzasca2; ?></td>
								    <td><?php echo $sistemasca2; ?></td>
								    <td><?php echo $rhca2; ?></td>
								    <td><?php echo $totalca2; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>POR CONTACTO</td>
								    <td><?php echo $logisticaca3; ?></td>
								    <td><?php echo $ventasca3; ?></td>
								    <td><?php echo $comprasca3; ?></td>
								    <td><?php echo $finanzasca3; ?></td>
								    <td><?php echo $sistemasca3; ?></td>
								    <td><?php echo $rhca3; ?></td>
								    <td><?php echo $totalca3; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td><strong>TOTAL</strong></td>
								    <td><strong><?php echo $logisticaca4; ?></strong></td>
								    <td><strong><?php echo $ventasca4; ?></strong></td>
								    <td><strong><?php echo $comprasca4; ?></strong></td>
								    <td><strong><?php echo $finanzasca4; ?></strong></td>
								    <td><strong><?php echo $sistemasca4; ?></strong></td>
								    <td><strong><?php echo $rhca4; ?></strong></td>
								    <td><strong><?php echo $totalca4; ?></strong></td>
							      </tr>
						    </tbody>
                            </table>
							 </div>
                             </div>
							<!-- /horizotal a -->

<p>&nbsp;</p>
<h1 class="text-semibold panel-title">Casos de Enfermedad General</h1>
<p>&nbsp;</p>

						<div class="panel panel-flat">
							<div class="panel-heading">
                            <h6 class="text-semibold panel-title">
                                <i class="icon-pencil4 position-left"></i>
                                Información de casos con enfermedad general:
                            </h6>
		                	</div>


                             <div class="table-wrapper-scroll-y my-custom-scrollbar">
							<table class="table table-xs">
								  <thead>
                                  <tr class="bg-grey text-center">
								    <th scope="col">Sucursal</td>
								    <th scope="col">No.Emp.</td>
								    <th scope="col">Nombre</td>
								    <th scope="col">Puesto</td>
								    <th scope="col">Inicio de tratamiento</td>
								    <th scope="col">Término de tratamiento</td>
								    <th scope="col">Estatus</td>
							      </tr>
								 </thead>
								<tbody>
                                	<?php do { ?>
                                <tr>
								    <td><?php echo $row_comentarios2['matriz']; ?></td>
								    <td><?php echo $row_comentarios2['IDempleado']; ?></td>
								    <td><?php echo $row_comentarios2['emp_paterno'] . " " . $row_comentarios2['emp_materno'] . " " . $row_comentarios2['emp_nombre']; ?></td>
								    <td><?php echo $row_comentarios2['denominacion']; ?></td>
								    <td><?php setlocale(LC_TIME, "spanish"); if ($row_comentarios2['tratam_inicio']!= '')
									{echo strftime("%d de %B", strtotime($row_comentarios2['tratam_inicio']));} ?></td>
								    <td><?php setlocale(LC_TIME, "spanish"); if ($row_comentarios2['tratam_fin']!= '') 
									{echo strftime("%d de %B", strtotime($row_comentarios2['tratam_fin']));} ?></td>
								    <td><?php if ($row_comentarios2['enfermedad_general'] != '') { echo $row_comentarios2['enfermedad_general'] . ": </br>"; } 
									echo str_replace("\n", "<br>", $row_comentarios2['observaciones']); ?></td>
                                 </tr>
                                 <?php } while ($row_comentarios2 = mysql_fetch_assoc($comentarios2)); ?>
						    </tbody>
                            </table>
							 </div>
							 </div>


<p>&nbsp;</p>
<h1 class="text-semibold panel-title">Personal Vulnerable</h1>
<p>&nbsp;</p>


					<!-- Grid -->
					<div class="row">
						<div class="col-md-12">
                            							<!-- Horizontal a -->
						  <div class="panel panel-flat">
							<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder-heart position-left"></i>
										Personal Vulnerable por Diagnóstico
									</h6>
		                	  </div>
							<div class="table-responsive">
							<table class="table table-xs">
								  <thead>
                                  <tr class="bg-warning text-center">
								    <td>DIAGNOSTICO</td>
								    <td>LOGÍSTICA</td>
								    <td>VENTAS</td>
								    <td>COMPRAS</td>
								    <td>FINANZAS</td>
								    <td>SISTEMAS</td>
								    <td>R.H.</td>
								    <td>TOTAL</td>
							      </tr>
								 </thead>
								<tbody>
                                 <tr class="text-center">
								    <td>DIABÉTICO</td>
								    <td><?php echo $logistica1; ?></td>
								    <td><?php echo $ventas1; ?></td>
								    <td><?php echo $compras1; ?></td>
								    <td><?php echo $finanzas1; ?></td>
								    <td><?php echo $sistemas1; ?></td>
								    <td><?php echo $rh1; ?></td>
								    <td><?php echo $total1; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>EMBARAZO</td>
								    <td><?php echo $logistica2; ?></td>
								    <td><?php echo $ventas2; ?></td>
								    <td><?php echo $compras2; ?></td>
								    <td><?php echo $finanzas2; ?></td>
								    <td><?php echo $sistemas2; ?></td>
								    <td><?php echo $rh2; ?></td>
								    <td><?php echo $total2; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>ENFERMEDAD ESPECIAL</td>
								    <td><?php echo $logistica3; ?></td>
								    <td><?php echo $ventas3; ?></td>
								    <td><?php echo $compras3; ?></td>
								    <td><?php echo $finanzas3; ?></td>
								    <td><?php echo $sistemas3; ?></td>
								    <td><?php echo $rh3; ?></td>
								    <td><?php echo $total3; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>HIPERTENSO</td>
								    <td><?php echo $logistica4; ?></td>
								    <td><?php echo $ventas4; ?></td>
								    <td><?php echo $compras4; ?></td>
								    <td><?php echo $finanzas4; ?></td>
								    <td><?php echo $sistemas4; ?></td>
								    <td><?php echo $rh4; ?></td>
								    <td><?php echo $total4; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>LACTANCIA</td>
								    <td><?php echo $logistica5; ?></td>
								    <td><?php echo $ventas5; ?></td>
								    <td><?php echo $compras5; ?></td>
								    <td><?php echo $finanzas5; ?></td>
								    <td><?php echo $sistemas5; ?></td>
								    <td><?php echo $rh5; ?></td>
								    <td><?php echo $total5; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>MAYORES DE 60 AÑOS</td>
								    <td><?php echo $logistica6; ?></td>
								    <td><?php echo $ventas6; ?></td>
								    <td><?php echo $compras6; ?></td>
								    <td><?php echo $finanzas6; ?></td>
								    <td><?php echo $sistemas6; ?></td>
								    <td><?php echo $rh6; ?></td>
								    <td><?php echo $total6; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>OTROS</td>
								    <td><?php echo $logistica7; ?></td>
								    <td><?php echo $ventas7; ?></td>
								    <td><?php echo $compras7; ?></td>
								    <td><?php echo $finanzas7; ?></td>
								    <td><?php echo $sistemas7; ?></td>
								    <td><?php echo $rh7; ?></td>
								    <td><?php echo $total7; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td><strong>TOTAL</strong></td>
								    <td><strong><?php echo $logistica8; ?></strong></td>
								    <td><strong><?php echo $ventas8; ?></strong></td>
								    <td><strong><?php echo $compras8; ?></strong></td>
								    <td><strong><?php echo $finanzas8; ?></strong></td>
								    <td><strong><?php echo $sistemas8; ?></strong></td>
								    <td><strong><?php echo $rh8; ?></strong></td>
								    <td><strong><?php echo $total8; ?></strong></td>
							      </tr>
						    </tbody>
                            </table>
							 </div>
                             </div>
							<!-- /horizotal a -->
					<!-- /grid -->



					<!-- Grid -->
					<div class="row">
						<div class="col-md-6">
							<!-- Horizontal a -->
						  <div class="panel panel-flat">
							<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder-heart position-left"></i>
										Personal Vulnerable por Estatus Laboral
									</h6>
		                	  </div>
							<div class="table-responsive">
							<table class="table table-xs">
								  <thead>
                                  <tr class="bg-warning text-center">
								    <td>ESTATUS</td>
								    <td>LOGÍS.</td>
								    <td>VENTAS</td>
								    <td>COMPRAS</td>
								    <td>FINANZAS</td>
								    <td>SISTEMAS</td>
								    <td>R.H.</td>
								    <td>T.</td>
							      </tr>
								 </thead>
								<tbody>
                                 <tr class="text-center">
								    <td>EN FUNCIONES</td>
								    <td><?php echo $logisticar2; ?></td>
								    <td><?php echo $ventasr2; ?></td>
								    <td><?php echo $comprasr2; ?></td>
								    <td><?php echo $finanzasr2; ?></td>
								    <td><?php echo $sistemasr2; ?></td>
								    <td><?php echo $rhr2; ?></td>
								    <td><?php echo $totalr2; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>EN CASA</td>
								    <td><?php echo $logisticar1; ?></td>
								    <td><?php echo $ventasr1; ?></td>
								    <td><?php echo $comprasr1; ?></td>
								    <td><?php echo $finanzasr1; ?></td>
								    <td><?php echo $sistemasr1; ?></td>
								    <td><?php echo $rhr1; ?></td>
								    <td><?php echo $totalr1; ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td><strong>TOTAL</strong></td>
								    <td><strong><?php echo $logisticar3; ?></strong></td>
								    <td><strong><?php echo $ventasr3; ?></strong></td>
								    <td><strong><?php echo $comprasr3; ?></strong></td>
								    <td><strong><?php echo $finanzasr3; ?></strong></td>
								    <td><strong><?php echo $sistemasr3; ?></strong></td>
								    <td><strong><?php echo $rhr3; ?></strong></td>
								    <td><strong><?php echo $totalr3; ?></strong></td>
							      </tr>
                                 <tr class="text-center">
								    <td>TOTAL PULL</td>
								    <td><?php echo $logisticapull2; ?></td>
								    <td><?php echo $ventaspull2; ?></td>
								    <td><?php echo $compraspull2; ?></td>
								    <td><?php echo $finanzaspull2; ?></td>
								    <td><?php echo $sistemaspull2; ?></td>
								    <td><?php echo $rhpull2; ?></td>
								    <td><?php echo $totalpull2; ?></td>
							      </tr>
						    </tbody>
                            </table>
							 </div>
                             </div>
							<!-- /horizotal a -->

						</div>
						<div class="col-md-6">
		     			<!-- Horizontal a -->
						  <div class="panel panel-flat">
							<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder-heart position-left"></i>
										Personal Vulnerable por Costo
									</h6>
                                    <div class="heading-elements">
									</div>
		                	  </div>
							<div class="table-responsive">
							<table class="table table-xs">
								  <thead>
                                  <tr class="bg-warning text-center">
								    <td>ESTATUS</td>
								    <td>LOGÍS.</td>
								    <td>VENTAS</td>
								    <td>COMPRAS</td>
								    <td>FINANZAS</td>
								    <td>SISTEMAS</td>
								    <td>R.H.</td>
								    <td>TOTAL</td>
							      </tr>
								 </thead>
								<tbody>
                                 <tr class="text-center">
								    <td>EN FUNCIONES</td>
								    <td><?php echo "$" . number_format($logisticaco2); ?></td>
								    <td><?php echo "$" . number_format( $ventasco2); ?></td>
								    <td><?php echo "$" . number_format( $comprasco2); ?></td>
								    <td><?php echo "$" . number_format( $finanzasco2); ?></td>
								    <td><?php echo "$" . number_format( $sistemasco2); ?></td>
								    <td><?php echo "$" . number_format( $rhco2); ?></td>
								    <td><?php echo "$" . number_format( $totalco2); ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td>EN CASA</td>
								    <td><?php echo "$" . number_format( $logisticaco1); ?></td>
								    <td><?php echo "$" . number_format( $ventasco1); ?></td>
								    <td><?php echo "$" . number_format( $comprasco1); ?></td>
								    <td><?php echo "$" . number_format( $finanzasco1); ?></td>
								    <td><?php echo "$" . number_format( $sistemasco1); ?></td>
								    <td><?php echo "$" . number_format( $rhco1); ?></td>
								    <td><?php echo "$" . number_format( $totalco1); ?></td>
							      </tr>
                                 <tr class="text-center">
								    <td><strong>TOTAL</strong></td>
								    <td><strong><?php echo "$" . number_format( $logisticaco3); ?></strong></td>
								    <td><strong><?php echo "$" . number_format( $ventasco3); ?></strong></td>
								    <td><strong><?php echo "$" . number_format( $comprasco3); ?></strong></td>
								    <td><strong><?php echo "$" . number_format( $finanzasco3); ?></strong></td>
								    <td><strong><?php echo "$" . number_format( $sistemasco3); ?></strong></td>
								    <td><strong><?php echo "$" . number_format( $rhco3); ?></strong></td>
								    <td><strong><?php echo "$" . number_format( $totalco3); ?></strong></td>
							      </tr>
                                 <tr class="text-center">
								    <td>COSTO PULL</td>
								    <td><?php echo "$" . number_format( $logisticapull); ?></td>
								    <td><?php echo "$" . number_format( $ventaspull); ?></td>
								    <td><?php echo "$" . number_format( $compraspull); ?></td>
								    <td><?php echo "$" . number_format( $finanzaspull); ?></td>
								    <td><?php echo "$" . number_format( $sistemaspull); ?></td>
								    <td><?php echo "$" . number_format( $rhpull); ?></td>
								    <td><?php echo "$" . number_format( $totalpull); ?></td>
							      </tr>
						    </tbody>
                            </table>
							 </div>
                             </div>
							<!-- /horizotal a -->
						</div>
						</div>
					<!-- /grid -->

<p>&nbsp;</p>
<h1 class="text-semibold panel-title">Contenido Informativo</h1>
<p>&nbsp;</p>

                    <!-- Grid -->
					<div class="row">
						<div class="col-md-6">

						<!-- Horizontal a -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder6 position-left"></i>
										Normatividad
									</h6>
                                    <div class="heading-elements">
										<span class="heading-text text-muted">(5)</span>
									</div>
			                	</div>
								<div class="list-group no-border">
									<a href="assets/files/Protocolo-Sahuayo-COVID-19.pdf" class="list-group-item">
										<i class="icon-file-pdf"></i> Protocolo COVID <span class="label bg-success-400">New</span>
									</a>
									<p>&nbsp;</p>
									<a href="assets/files/Protocolo-Contingencia-COVID-19.pdf" class="list-group-item">
										<i class="icon-file-pdf"></i> Protocolo para continuidad operativa y trabajo remoto <span class="label bg-success-400">New</span>
									</a>
									<p>&nbsp;</p>
									<a href="assets/files/Manual-de-Políticas-y-Procedimientos-medidas-de-prevención-COVID-19" class="list-group-item">
										<i class="icon-file-pdf"></i> COVID-19 Manual de Políticas y Procedimientos. Medidas de prevención <span class="label bg-success-400">New</span>
									</a>
									<p>&nbsp;</p>
                                    <a href="assets/files/Colaboradores-con-enfermedades-crónico-degenerativas.pdf" class="list-group-item">
										<i class="icon-file-pdf"></i> Manual para Colaboradores con enfermedades crónico degenerativas <span class="label bg-success-400">New</span>
									</a>
									<p>&nbsp;</p>
                                    <a href="assets/files/1.pdf" class="list-group-item">
										<i class="icon-file-pdf"></i> Diptico Medidas de Prevención <span class="label bg-success-400">New</span>
									</a>
									<p>&nbsp;</p>
                                    <a href="assets/files/2.pdf" class="list-group-item">
										<i class="icon-file-pdf"></i> Diptico Medidas de Prevención para personal de Ventas<span class="label bg-success-400">New</span>
									</a>


								</div>
							</div>
							<!-- /horizotal a -->

						</div>

						<div class="col-md-6">
					<!-- Basic thumbnails -->
                <h6 class="text-semibold panel-title">
                    <i class="icon-comments position-left"></i>
                    Material Informativo
                </h6>
                <p>&nbsp;</p>
					<div class="row">
						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/1.jpg" data-popup="lightbox">
												<img src="assets/files/1.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/2.jpg" data-popup="lightbox">
												<img src="assets/files/2.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/3.jpg" data-popup="lightbox">
												<img src="assets/files/3.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/4.jpg" data-popup="lightbox">
												<img src="assets/files/4.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/5.jpg" data-popup="lightbox">
												<img src="assets/files/5.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/1.jpg" data-popup="lightbox">
												<img src="assets/files/1.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/7.jpg" data-popup="lightbox">
												<img src="assets/files/7.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/8.jpg" data-popup="lightbox">
												<img src="assets/files/8.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/9.jpg" data-popup="lightbox">
												<img src="assets/files/9.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/10.jpg" data-popup="lightbox">
												<img src="assets/files/10.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/11.jpg" data-popup="lightbox">
												<img src="assets/files/11.jpg" alt="">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-2 col-sm-2">
							<div class="panel panel-flat">

								<div class="panel-body">
									<div class="thumbnail">
										<div class="thumb">
											<a href="assets/files/video.mp4" data-popup="lightbox">
												<img src="assets/files/video.jpg" alt=""><span class="text-muted">Video D.General</span>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
					<!-- /basic thumbnails -->						
                    </div>
					</div>
					<!-- /grid -->


							<!-- Latest posts -->
							<div class="panel panel-flat">
								<div class="panel-heading">
                <h6 class="text-semibold panel-title">
                    <i class="icon-users4 position-left"></i>
                  Integrantes del COMITE COVID SAHUAYO
                </h6>
			                	</div>

								<div class="panel-body">
									<div class="row">
										<div class="col-lg-3">
											<ul class="media-list content-group">
												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

				                					<div class="media-body">
														<h6 class="media-heading"><a href="mailto:rtejeda@sahuayo.mx">Raymundo Tejeda</a></h6>
														Director de Recursos Humanos
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> rtejeda@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(55) 4010 8825</li>
							                    		</ul>													
							                    	</div>
												</li>

												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

				                					<div class="media-body">
														<h6 class="media-heading"><a href="mailto:gcastellanos@sahuayo.mx">Gabriel Castellanos</a></h6>
														Gerente Corporativo Jurídico
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> gcastellanos@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(55) 5407 3566</li>
							                    		</ul>													
							                    	</div>
												</li>
											</ul>
										</div>

										<div class="col-lg-3">
											<ul class="media-list content-group">
												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

				                					<div class="media-body">
														<h6 class="media-heading"><a href="mailto:mmaldonado@sahuayo.mx">Montserrat Maldonado</a></h6>
														Jefe de Seguridad e Higiene
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> mmaldonado@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(55) 3506 6306</li>
							                    		</ul>													
							                    	</div>
												</li>

												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

				                					<div class="media-body">
														<h6 class="media-heading"><a href="mailto:jasanchezi@sahuayo.mx">Jorge Sanchez</a></h6>
														Gerente Nacional de Distribución
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> jasanchezi@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(55) 8422 9126</li>
							                    		</ul>													
							                    	</div>
												</li>
											</ul>
										</div>

										<div class="col-lg-3">
											<ul class="media-list content-group">
												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

				                					<div class="media-body">
														<h6 class="media-heading"><a href="mailto:fcreyna@sahuayo.mx">Gustavo Rojas</a></h6>
														Gerente Sr. Nacional de Almacén
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i>  grojas@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(55) 2254 8892</li>
							                    		</ul>													
							                    	</div>
												</li>

												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

				                					<div class="media-body">
														Gerentes Regionales de Ventas
														<h6 class="media-heading"><a href="mailto:jcastaneda@sahuayo.mx">Julio Castañeda</a></h6>
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> jcastaneda@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(99) 9159 9913</li>
							                    		</ul>
														<h6 class="media-heading"><a href="mailto:jpruiz@sahuayo.mx">Juan Pablo Ruíz</a></h6>
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> jpruiz@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(66) 4169 0431</li>
							                    		</ul>
														<h6 class="media-heading"><a href="mailto:dmartinez@sahuayo.mx">Damián Martínez</a></h6>
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> dmartinez@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(87) 1178 9312</li>
							                    		</ul>
													</div>
												</li>
											</ul>
										</div>

										<div class="col-lg-3">
											<ul class="media-list content-group">
												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

													<div class="media-body">
														<h6 class="media-heading"><a href="#">Jose Luís Gómez</a></h6>
														Gerente de Auditoría Interna
														<ul class="list-inline list-inline-separate text-muted mb-5">
							                    			<li><i class="icon-envelop position-left"></i> jlgomez@sahuayo.mx</li>
							                    			<li><i class="icon-phone2 position-left"></i>(55) 3399 2655</li>
							                    		</ul>
							                    	</div>												</li>

												<li class="media stack-media-on-mobile">
				                					<div class="media-left">
														<div class="thumb">
															<a href="#">
																<img src="global_assets/images/placeholders/placeholder.jpg" class="img-responsive img-rounded media-preview" alt="">
																<span class="zoom-image"><i class="icon-play3"></i></span>
															</a>
														</div>
													</div>

				                					<div class="media-body">
				                						<p>&nbsp;</p>
														Gerentes Administrativos
													</div>
												</li>
											</ul>
										</div>


									</div>
								</div>
								</div>
								</div>
								</div>
								</div>
							<!-- /latest posts -->                    
                    
					<div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
				  </div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>