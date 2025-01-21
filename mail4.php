<?php require_once('Connections/vacantes.php'); ?>
<?php

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
date_default_timezone_set("America/Mexico_City");
$desfase = $row_variables['dias_desfase'];
// mes y semana
$el_mes = date("m");
$el_mes_anterior = $el_mes - 1;
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el a�o anterior 
$semana = date("W", strtotime($la_fecha));
$semana = $semana - 1;
$semana_prev = $semana - 2;
$anio = $row_variables['anio'];
$anio_actual = $anio;
$anio_anterior = $anio - 1; // la fecha actual

//select cada matriz
//$query_matriz = "SELECT * FROM vac_matriz";
if (!isset($_GET['IDmatriz'])){
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (7,27,29,10,8,31)";
} else {
$IDmatriz = $_GET['IDmatriz'];
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
}
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);

//se envia correo
require 'assets/PHPMailer/PHPMailerAutoload.php';

// loop para cada matriz
do { 
$LaMatriz = $row_matriz['IDmatriz'];

$query_vacabtes = "SELECT
vac_vacante.IDvacante,
vac_areas.area,
vac_puestos.denominacion,
vac_vacante.IDmotivo_baja,
vac_vacante.IDmotivo_v,
vac_vacante.IDrequi,
vac_vacante.ajuste_dias,
vac_vacante.fecha_requi,
vac_puestos.dias AS Dias_esperados,
(DATEDIFF( now(),vac_vacante.fecha_requi)) AS Dias_transcurridos,
vac_matriz.matriz,
vac_apoyo.apoyo
FROM
vac_vacante
LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea
LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto
INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz
INNER JOIN vac_apoyo ON vac_apoyo.IDapoyo = vac_vacante.IDapoyo
WHERE
vac_vacante.IDmatriz = '$LaMatriz' AND
vac_vacante.IDestatus = 1
GROUP BY
vac_vacante.IDvacante,
vac_vacante.ajuste_dias,
vac_vacante.observaciones,
vac_puestos.dias,
vac_areas.area,
vac_vacante.IDestatus,
vac_vacante.fecha_requi,
vac_puestos.denominacion
"; 
mysql_query("SET NAMES 'utf8'");
$vacabtes = mysql_query($query_vacabtes, $vacantes) or die(mysql_error());
$row_vacabtes = mysql_fetch_assoc($vacabtes);
$totalRows_vacabtes = mysql_num_rows($vacabtes);

$query_cubiertas = "SELECT
vac_vacante.IDvacante,
vac_areas.area,
vac_puestos.denominacion,
vac_vacante.IDmotivo_baja,
vac_vacante.IDmotivo_v,
vac_vacante.IDrequi,
vac_vacante.ajuste_dias,
vac_vacante.fecha_requi,
vac_puestos.dias AS Dias_esperados,
vac_matriz.matriz,
vac_apoyo.apoyo,
(DATEDIFF( now(),vac_vacante.fecha_requi)) AS Dias_transcurridos,
vac_vacante.fecha_ocupacion
FROM
vac_vacante
LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea
LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto
INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz
INNER JOIN vac_apoyo ON vac_apoyo.IDapoyo = vac_vacante.IDapoyo
WHERE
vac_vacante.IDmatriz = '$LaMatriz'  AND
vac_vacante.IDestatus = 2 AND
vac_vacante.fecha_ocupacion >= date_add(NOW(), INTERVAL -12 DAY)
GROUP BY
vac_vacante.IDvacante,
vac_vacante.ajuste_dias,
vac_vacante.observaciones,
vac_puestos.dias,
vac_areas.area,
vac_vacante.IDestatus,
vac_vacante.fecha_requi,
vac_puestos.denominacion
"; 
mysql_query("SET NAMES 'utf8'");
$cubiertas = mysql_query($query_cubiertas, $vacantes) or die(mysql_error());
$row_cubiertas = mysql_fetch_assoc($cubiertas);
$totalRows_cubiertas = mysql_num_rows($cubiertas);

//incentivos
mysql_select_db($database_vacantes, $vacantes);
$query_incidencias = "SELECT
inc_captura.IDmatriz,
Sum(inc_captura.inc1) AS INC1,
Sum(inc_captura.inc2) AS INC2,
Sum(inc_captura.inc3) AS INC3,
Sum(inc_captura.inc4) AS INC4,
Sum(inc_captura.inc5) AS INC5,
Sum(inc_captura.inc6) AS INC6,
inc_captura.semana
FROM
inc_captura
WHERE
inc_captura.IDmatriz = '$LaMatriz'
AND inc_captura.semana = '$semana'
GROUP BY
inc_captura.IDmatriz"; 
$incidencias = mysql_query($query_incidencias, $vacantes) or die(mysql_error());
$row_incidencias = mysql_fetch_assoc($incidencias);

//incentivos
mysql_select_db($database_vacantes, $vacantes);
$query_incidencias_ant = "SELECT
inc_captura.IDmatriz,
Sum(inc_captura.inc1) AS INC1,
Sum(inc_captura.inc2) AS INC2,
Sum(inc_captura.inc3) AS INC3,
Sum(inc_captura.inc4) AS INC4,
Sum(inc_captura.inc5) AS INC5,
Sum(inc_captura.inc6) AS INC6,
inc_captura.semana
FROM
inc_captura
WHERE
inc_captura.IDmatriz = '$LaMatriz'
AND inc_captura.semana = '$semana_prev'
GROUP BY
inc_captura.IDmatriz"; 
$incidencias_ant = mysql_query($query_incidencias_ant, $vacantes) or die(mysql_error());
$row_incidencias_ant = mysql_fetch_assoc($incidencias_ant);

//productividad
mysql_select_db($database_vacantes, $vacantes);
$query_productividad = "SELECT
prod_captura.IDmatriz,
Sum(prod_captura.adicional2) AS PROD2,
Sum(prod_captura.pago_total) AS PROD1
FROM
prod_captura
WHERE
prod_captura.IDmatriz = '$LaMatriz' AND
prod_captura.semana = '$semana'
"; 
$productividad = mysql_query($query_productividad, $vacantes) or die(mysql_error());
$row_productividad = mysql_fetch_assoc($productividad);

//productividad
mysql_select_db($database_vacantes, $vacantes);
$query_productividad_prev = "SELECT
prod_captura.IDmatriz,
Sum(prod_captura.adicional2) AS PROD2,
Sum(prod_captura.pago_total) AS PROD1
FROM
prod_captura
WHERE
prod_captura.IDmatriz = '$LaMatriz' AND
prod_captura.semana = '$semana_prev'
"; 
$productividad_prev = mysql_query($query_productividad_prev, $vacantes) or die(mysql_error());
$row_productividad_prev = mysql_fetch_assoc($productividad_prev);

$total=	$row_incidencias['INC1'] + $row_incidencias['INC2'] + $row_incidencias['INC3'] + $row_incidencias['INC4'] + $row_incidencias['INC5'] + $row_incidencias['INC6'] + $row_productividad['PROD1'] + $row_productividad['PROD2'];
$total_prev = $row_incidencias_ant['INC1'] + $row_incidencias_ant['INC2'] + $row_incidencias_ant['INC3'] + $row_incidencias_ant['INC4'] + $row_incidencias_ant['INC5'] + $row_incidencias_ant['INC6']+ $row_productividad_prev['PROD1'] + $row_productividad_prev['PROD2'];
if ($total > $total_prev) { $siendo = " mayor ";} else { $siendo = " menor ";}	
$prod_all = $row_productividad['PROD1'] + $row_productividad['PROD2'];

$query_faltas = "SELECT
prod_captura.lun,
prod_captura.mar,
prod_captura.jue,
prod_captura.mie,
prod_captura.vie,
prod_captura.sab,
prod_captura.dom,
prod_activos.IDempleado,
prod_activos.emp_materno,
prod_activos.emp_paterno,
prod_activos.emp_nombre,
inc_faltas.dias_menos
FROM
prod_captura
LEFT JOIN prod_activos ON prod_captura.IDempleado = prod_activos.IDempleado
LEFT JOIN inc_faltas ON inc_faltas.IDempleado = prod_activos.IDempleado
WHERE
prod_captura.IDmatriz = '$LaMatriz' AND
prod_captura.semana = '$semana'
";   
$faltas = mysql_query($query_faltas, $vacantes) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$totalRows_faltas = mysql_num_rows($faltas);

//objetivo y total a�o anterior
mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT * FROM ind_objetivo WHERE IDmatriz = '$LaMatriz' AND anio = '$anio_actual'";
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados_anterior = "SELECT * FROM ind_objetivo WHERE IDmatriz = '$LaMatriz' AND anio = '$anio_anterior'";
$resultados_anterior = mysql_query($query_resultados_anterior, $vacantes) or die(mysql_error());
$row_resultados_anterior = mysql_fetch_assoc($resultados_anterior);
$totalRows_resultados_anterior = mysql_num_rows($resultados_anterior);

// Resultado Mes 1 anio actual
//$fini_ms1 = new DateTime('NOW');
$fini_ms1 = new DateTime($anio_actual.'-'.$el_mes_anterior.'-01');
$fini_ms1->modify('first day of this month');
$fini_ms1k = $fini_ms1->format('Y/m/d'); 

//$fini_ms1 = new DateTime('NOW');
$fter_ms1 = new DateTime($anio_actual.'-'.$el_mes_anterior.'-01');
$fter_ms1->modify('last day of this month');
$fter_ms1k = $fter_ms1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$fter_ms1k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$fini_ms1k')  AND ind_bajas.IDmotivo < 15";
$res_ms1 = mysql_query($query_res_ms1, $vacantes) or die(mysql_error());
$row_res_ms1 = mysql_fetch_assoc($res_ms1);
$totalRows_res_ms1 = mysql_num_rows($res_ms1);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms1 = mysql_query($query_bja_ms1, $vacantes) or die(mysql_error());
$row_bja_ms1 = mysql_fetch_assoc($bja_ms1);
$totalRows_bja_ms1 = mysql_num_rows($bja_ms1);

$RotTotalM1 =  $row_bja_ms1['TOTAL'] / $row_res_ms1['TOTAL'];

//resultados por area
mysql_select_db($database_vacantes, $vacantes);
$query_ar1an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 1 AND ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$ar1an1 = mysql_query($query_ar1an1, $vacantes) or die(mysql_error());
$row_ar1an1 = mysql_fetch_assoc($ar1an1);
$totalRows_ar1an1 = mysql_num_rows($ar1an1);
$ar1an1r = $row_ar1an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar2an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 2 AND ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$ar2an1 = mysql_query($query_ar2an1, $vacantes) or die(mysql_error());
$row_ar2an1 = mysql_fetch_assoc($ar2an1);
$totalRows_ar2an1 = mysql_num_rows($ar2an1);
$ar2an1r = $row_ar2an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar3an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 3 AND ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$ar3an1 = mysql_query($query_ar3an1, $vacantes) or die(mysql_error());
$row_ar3an1 = mysql_fetch_assoc($ar3an1);
$totalRows_ar3an1 = mysql_num_rows($ar3an1);
$ar3an1r = $row_ar3an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar4an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 4 AND ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$ar4an1 = mysql_query($query_ar4an1, $vacantes) or die(mysql_error());
$row_ar4an1 = mysql_fetch_assoc($ar4an1);
$totalRows_ar4an1 = mysql_num_rows($ar4an1);
$ar4an1r = $row_ar4an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar5an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 5 AND ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$ar5an1 = mysql_query($query_ar5an1, $vacantes) or die(mysql_error());
$row_ar5an1 = mysql_fetch_assoc($ar5an1);
$totalRows_ar5an1 = mysql_num_rows($ar5an1);
$ar5an1r = $row_ar5an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar6an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 6 AND ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$ar6an1 = mysql_query($query_ar6an1, $vacantes) or die(mysql_error());
$row_ar6an1 = mysql_fetch_assoc($ar6an1);
$totalRows_ar6an1 = mysql_num_rows($ar6an1);
$ar6an1r = $row_ar6an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar7an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea IN (7,8,9,10,11,12) AND ind_bajas.IDmatriz = '$LaMatriz' AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = $el_mes_anterior AND YEAR(fecha_baja) = $anio_actual)";
$ar7an1 = mysql_query($query_ar7an1, $vacantes) or die(mysql_error());
$row_ar7an1 = mysql_fetch_assoc($ar7an1);
$totalRows_ar7an1 = mysql_num_rows($ar7an1);
$ar7an1r = $row_ar7an1['TOTAL'];

// Resultado actual total  a�o actual
$Tfini_ms1 = new DateTime($anio_actual . '-' .$el_mes_anterior . '-01');
$Tfini_ms1->modify('first day of this month');
$Tfini_msk1 = $Tfini_ms1->format('Y/m/d'); 

$Tfter_ms1 = new DateTime($anio_actual . '-' .$el_mes_anterior . '-01');
$Tfter_ms1->modify('last day of this month');
$Tfter_msk1 = $Tfter_ms1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_Toar1an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 1 AND ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$Tfter_msk1' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk1')";
$Toar1an1 = mysql_query($query_Toar1an1, $vacantes) or die(mysql_error());
$row_Toar1an1 = mysql_fetch_assoc($Toar1an1);
$totalRows_Toar1an1 = mysql_num_rows($Toar1an1);
$Toar1an1r = $row_Toar1an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar2an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 2 AND ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$Tfter_msk1' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk1')";
$Toar2an1 = mysql_query($query_Toar2an1, $vacantes) or die(mysql_error());
$row_Toar2an1 = mysql_fetch_assoc($Toar2an1);
$totalRows_Toar2an1 = mysql_num_rows($Toar2an1);
$Toar2an1r = $row_Toar2an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar3an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 3 AND ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$Tfter_msk1' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk1')";
$Toar3an1 = mysql_query($query_Toar3an1, $vacantes) or die(mysql_error());
$row_Toar3an1 = mysql_fetch_assoc($Toar3an1);
$totalRows_Toar3an1 = mysql_num_rows($Toar3an1);
$Toar3an1r = $row_Toar3an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar4an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 4 AND ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$Tfter_msk1' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk1')";
$Toar4an1 = mysql_query($query_Toar4an1, $vacantes) or die(mysql_error());
$row_Toar4an1 = mysql_fetch_assoc($Toar4an1);
$totalRows_Toar4an1 = mysql_num_rows($Toar4an1);
$Toar4an1r = $row_Toar4an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar5an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 5 AND ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$Tfter_msk1' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk1')";
$Toar5an1 = mysql_query($query_Toar5an1, $vacantes) or die(mysql_error());
$row_Toar5an1 = mysql_fetch_assoc($Toar5an1);
$totalRows_Toar5an1 = mysql_num_rows($Toar5an1);
$Toar5an1r = $row_Toar5an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar6an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea = 6 AND ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$Tfter_msk1' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk1')";
$Toar6an1 = mysql_query($query_Toar6an1, $vacantes) or die(mysql_error());
$row_Toar6an1 = mysql_fetch_assoc($Toar6an1);
$totalRows_Toar6an1 = mysql_num_rows($Toar6an1);
$Toar6an1r = $row_Toar6an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar7an1 = "SELECT Count(ind_bajas.IDempleado) AS TOTAL FROM ind_bajas WHERE IDarea IN (7,8,9,10,11,12) AND ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$LaMatriz' AND fecha_alta <= '$Tfter_msk1' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk1')";
$Toar7an1 = mysql_query($query_Toar7an1, $vacantes) or die(mysql_error());
$row_Toar7an1 = mysql_fetch_assoc($Toar7an1);
$totalRows_Toar7an1 = mysql_num_rows($Toar7an1);
$Toar7an1r = $row_Toar7an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$LaMatriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);

//correo rh
mysql_select_db($database_vacantes, $vacantes);
$query_correo_1 = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($LaMatriz, IDmatrizes) AND correo_semanal  = 5";
$correo_1 = mysql_query($query_correo_1, $vacantes) or die(mysql_error());
$row_correo_1 = mysql_fetch_assoc($correo_1);
$totalRows_correo_1 = mysql_num_rows($correo_1);
if($totalRows_correo_1 > 0) {$El_correo_1 = $row_correo_1['usuario_correo'];} else {$El_correo_1 = 'jacardenas@sahuayo.mx';}

//correo JOR
mysql_select_db($database_vacantes, $vacantes);
$query_correo_2 = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($LaMatriz, IDmatrizes) AND correo_semanal  = 2";
$correo_2 = mysql_query($query_correo_2, $vacantes) or die(mysql_error());
$row_correo_2 = mysql_fetch_assoc($correo_2);
$totalRows_correo_2 = mysql_num_rows($correo_2);
if($totalRows_correo_2 > 0) {$El_correo_2 = $row_correo_2['usuario_correo'];} else {$El_correo_2 = 'jacardenas@sahuayo.mx';}

//correo CRAT
mysql_select_db($database_vacantes, $vacantes);
$query_correo_3 = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($LaMatriz, IDmatrizes) AND correo_semanal  = 3";
$correo_3 = mysql_query($query_correo_3, $vacantes) or die(mysql_error());
$row_correo_3 = mysql_fetch_assoc($correo_3);
$totalRows_correo_3 = mysql_num_rows($correo_3);
if($totalRows_correo_3 > 0) {$El_correo_3 = $row_correo_3['usuario_correo'];} else {$El_correo_3 = 'jacardenas@sahuayo.mx';}

//correo Gerente
mysql_select_db($database_vacantes, $vacantes);
$query_correo_4 = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($LaMatriz, IDmatrizes) AND correo_semanal  = 1";
$correo_4 = mysql_query($query_correo_4, $vacantes) or die(mysql_error());
$row_correo_4 = mysql_fetch_assoc($correo_4);
$totalRows_correo_4 = mysql_num_rows($correo_4);
if($totalRows_correo_4 > 0) {$El_correo_4 = $row_correo_4['usuario_correo'];} else {$El_correo_4 = 'jacardenas@sahuayo.mx';}

//correo Rregional
mysql_select_db($database_vacantes, $vacantes);
$query_correo_5 = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($LaMatriz, IDmatrizes) AND correo_semanal  = 6";
$correo_5 = mysql_query($query_correo_5, $vacantes) or die(mysql_error());
$row_correo_5 = mysql_fetch_assoc($correo_5);
$totalRows_correo_5 = mysql_num_rows($correo_5);
if($totalRows_correo_5 > 0) {$El_correo_5 = $row_correo_5['usuario_correo'];} else {$El_correo_5 = 'jacardenas@sahuayo.mx';}


$mail = new PHPMailer;
//$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = "smtp.office365.com";
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure = '';
$mail->Username = "reporte_diario@gestionvacantes.com";
$mail->Password = "parazoom2020!";
$mail->setFrom('reporte_diario@gestionvacantes.com', 'Sistema de Gestion de Recursos Humanos');
$mail->addReplyTo('reporte_diario@gestionvacantes.com', 'Recursos Humanos');
$mail->AddAddress($El_correo_1);
$mail->AddAddress($El_correo_2);
$mail->AddAddress($El_correo_4);
$mail->AddAddress('grojas@sahuayo.mx');
$mail->AddAddress('caarzate@sahuayo.mx');
$mail->AddAddress('acortes@sahuayo.mx');
$mail->AddAddress('rnolasco@sahuayo.mx');
$mail->AddAddress('jmcamacho@sahuayo.mx');
$mail->AddAddress('rerivas@sahuayo.mx');
$mail->AddAddress('rtejeda@sahuayo.mx');
$mail->AddAddress('dmmartinez@sahuayo.mx');
$mail->AddAddress('cgaona@sahuayo.mx');
$mail->AddAddress('jacardenas@sahuayo.mx');
$mail->AddAddress('gemendiola@sahuayo.mx');
$mail->AddAddress('mahernandez@sahuayo.mx');
$mail->Subject = 'Reporte Semanal de Indicadores de Recursos Humanos.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Reporte Semanal de Indicadores de Recursos Humanos.';
$body = '

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <!--[if !mso]--><!-- -->
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet">
    <!--<![endif]-->
	<title>Sistema de Gestion de Recursos Humanos</title>

    <style type="text/css">
        body {
            width: 100%;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            mso-margin-top-alt: 0px;
            mso-margin-bottom-alt: 0px;
            mso-padding-alt: 0px 0px 0px 0px;
        }

        p,
        h1,
        h2,
        h3,
        h4 {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        span.preheader {
            display: none;
            font-size: 1px;
        }

        html {
            width: 100%;
        }

        table {
            font-size: 14px;
            border: 0;
        }
        /* ----------- responsivity ----------- */

        @media only screen and (max-width: 640px) {
            /*------ top header ------ */
            .main-header {
                font-size: 20px !important;
            }
            .main-section-header {
                font-size: 28px !important;
            }
            .show {
                display: block !important;
            }
            .hide {
                display: none !important;
            }
            .align-center {
                text-align: center !important;
            }
            .no-bg {
                background: none !important;
            }
            /*----- main image -------*/
            .main-image img {
                width: 440px !important;
                height: auto !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 440px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 440px !important;
            }
            .container580 {
                width: 400px !important;
            }
            .main-button {
                width: 220px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 320px !important;
                height: auto !important;
            }
            .team-img img {
                width: 100% !important;
                height: auto !important;
            }
        }

        @media only screen and (max-width: 479px) {
            /*------ top header ------ */
            .main-header {
                font-size: 18px !important;
            }
            .main-section-header {
                font-size: 26px !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 280px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 280px !important;
            }
            .container590 {
                width: 280px !important;
            }
            .container580 {
                width: 260px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 280px !important;
                height: auto !important;
            }
        }
		#customers {
		  border-collapse: collapse;
		  width: 100%;
		}
		
		#customers td, #customers th {
		  border: 1px solid #ddd;
		  padding: 4px;
		}
		
		#customers tr:nth-child(even){background-color: #f2f2f2;}
		
		#customers th {
		  padding-top: 12px;
		  padding-bottom: 12px;
		  text-align: left;
		  background-color: #C30F2D;
		  color: white;
		}
    </style>
    <!--[if gte mso 9]><style type=�text/css�>
        body {
        font-family: arial, sans-serif!important;
        }
        </style>
    <![endif]-->
</head>


<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <!-- header -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">

        <tr>
            <td align="center">
                <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

                    <tr>
                        <td style="font-size: 25px; line-height: 25px;">&nbsp;</td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <!-- end header -->

    <!-- big image section -->

    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">

        <tr>
            <td align="center">
                <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

                    <tr>
                        <td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;"
                            class="main-header">
                            <!-- section text ======-->

                            <div style="line-height: 35px">

                                Reporte Semanal de Indicadores de <span style="color: #C30F2D;">Recursos Humanos</span>

                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td align="center">
                            <table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee">
                                <tr>
                                    <td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td align="left">
                            <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590">
                                <tr>
                                    <td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">
                                        <p style="line-height: 24px; margin-bottom:15px;">

                                            Estimado Usuario,

                                        </p>
                                        <p style="line-height: 24px;margin-bottom:15px;">
                                           A continuaci&oacute;n se muestran los indicadores de gesti&oacute;n de Recursos Humanos de la Sucursal <strong>'.$row_lmatriz['matriz'].'</strong>,
										   correspondientes a la semana <strong>'.$semana.'</strong>.
                                        </p>
                                        <p style="line-height: 24px; margin-bottom:20px;">
                                           Puedes consultar los detalles accediendo al acceso del t&iacute;tulo de cada apartado o en el <strong>Sistema de Gesti&oacute;n de Recursos Humanos Sahuayo </strong> dando clic en el bot&oacute;n al final del presente correo.
                                        </p>

										<p style="line-height: 24px;margin-bottom:15px;">
                                          <h3><a href="https://gestionvacantes.com/inc_gasto.php">Reporte de Gasto</a></h3>
                                        </p>
<p>Gasto Total semana actual: <strong>$'.number_format($total).'</strong></p>
<p>Gasto Total semana anterior: <strong>$'.number_format($total_prev).'</strong></p>
<p>El gasto de la semana actual es <strong>'.$siendo.'</strong>que la semana anterior.</p>
<p><em>Nota: Si el gasto es mayor que la semana anterior, se  debe validar y revisar el detalle.</em></p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center" style="text-align:center">Horas Extra</th>
<th style="text-align:center" style="text-align:center">Suplencia</th>
<th style="text-align:center" style="text-align:center">Incentivos</th>
<th style="text-align:center" style="text-align:center">Domingos </br> Trabajados</th>
<th style="text-align:center" style="text-align:center">Premios</br>por Viaje</th>
<th style="text-align:center" style="text-align:center">Festivos</th>
<th style="text-align:center" style="text-align:center">Productividad</th>
</tr>
</thead>';
$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  "$" . number_format($row_incidencias['INC1']);
$body .= '</td>';
$body .= '<td align="center">';
$body .=  "$" . number_format($row_incidencias['INC2']);
$body .= '</td>';
$body .= '<td align="center">';
$body .=  "$" . number_format($row_incidencias['INC3']);
$body .= '</td>';
$body .= '<td align="center">';
$body .=  "$" . number_format($row_incidencias['INC4']);
$body .= '</td>';
$body .= '<td align="center">';
$body .=  "$" . number_format($row_incidencias['INC5']);
$body .= '</td>';
$body .= '<td align="center">';
$body .=  "$" . number_format($row_incidencias['INC6']);
$body .= '</td>';
$body .= '<td align="center">';
$body .=  "$" . number_format($prod_all);
$body .= '</td>';
$body .= '</tbody></table>

                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
										<p style="line-height: 24px;margin-bottom:15px;">
                                          <h3><a href="https://gestionvacantes.com/inc_faltas.php">Ausentismo</a></h3>
                                        </p>
                                        <p>A continuaci&oacute;n se muestra al personal con m&aacute;s de una falta en la semana.</p>
<p><em>Nota: Los empleados con m&aacute;s de 2 faltas en la semana, deben ser acreedores a asesor&iacute;a para mejorar.</em></p>
<p><em>Nota: Despu&eacute;s de la tercer falta, se inicia con la b&uacute;squeda de la vacante, a&uacute;n cuando el empleado no haya causado baja.</em></p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center">No. Emp.</th>
<th style="text-align:center">Nombre</th>
<th style="text-align:center">Faltas</th>
</tr>
</thead>';
$body .= '<tbody>';

$count_f = 0;
	do {
	$Faltas = $row_faltas['lun'] +  $row_faltas['mar'] + $row_faltas['mie'] + $row_faltas['jue'] + $row_faltas['vie'] + $row_faltas['sab'] - $row_faltas['dias_menos'];
	
		if($Faltas != 6 && $Faltas > 0 && $row_faltas['IDempleado'] != ''){ $count_f = $count_f + 1;
		$body .= '<tr>';
		$body .= '<td>';
		$body .=  $row_faltas['IDempleado'];
		$body .= '</td>';
		$body .= '<td>';
		$body .=  $row_faltas['emp_paterno'].' '.$row_faltas['emp_materno'].' '.$row_faltas['emp_nombre'];
		$body .= '</td>';
		$body .= '<td align="center">';
		$body .=  6 - $Faltas;
		$body .= '</td>';
		$body .= '</tr>';
		}
	} while($row_faltas = mysql_fetch_array($faltas));

if($count_f == 0) {
$body .= '<tr><td colspan="3">No hay faltas reportadas.</td></tr>';
}

$body .= '</tbody></table>
										<p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
                                        <p style="line-height: 24px;margin-bottom:15px;">
                                          <h3><a href="https://gestionvacantes.com/vacantes_activas.php">Vacantes Activas</a></h3>
                                        </p>
                                        <p>A continuaci&oacute;n se muestran las vacantes activas actuales.</p>
                                        <p>Vacantes activas: <strong>'.$totalRows_vacabtes.' vacantes.</strong></p>
<p><em>Nota: Las vacantes sin requisici&oacute;n (marcadas con *) no cuentan para la evaluaci&oacute;n del reclutamiento.</em></p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center">Nombre</th>
<th style="text-align:center">D&iacute;as</th>
<th style="text-align:center">Requi</th>
<th style="text-align:center">Pull Vac</th>
<th style="text-align:center">Estatus</th>
</tr>
</thead>';
$body .= '<tbody>';
if($totalRows_vacabtes > 0) {
do {
if($row_vacabtes['IDrequi'] == 1) {$reqi = "No";} else {$reqi = "Si";}
if($row_vacabtes['IDrequi'] == 1) {$tiempo = $row_vacabtes['Dias_transcurridos']."*";} else {$tiempo =  $row_vacabtes['Dias_transcurridos'];}
if($row_vacabtes['IDmotivo_v'] == 5) {$pullvacaciones = "Si";} else {$pullvacaciones = "No";}
	
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $tiempo;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $reqi;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $pullvacaciones;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['apoyo'];
$body .= '</td>';
$body .= '</tr>';
} while($row_vacabtes=mysql_fetch_array($vacabtes));
} else {
$body .= '<tr><td  colspan="5">No hay vacantes activas.</td></tr>';
}
$body .= '</tbody></table>
                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
										<p style="line-height: 24px;margin-bottom:15px;">
                                          <h3><a href="https://gestionvacantes.com/vacantes_cerradas.php">Vacantes Cubiertas</a></h3>
                                        </p>
                                        <p>A continuaci&oacute;n se muestran las vacantes cubiertas en los &uacute;ltimos 10 d&iacute;as.</p>
                                        <p>Vacantes cubiertas: <strong>'.$totalRows_cubiertas.' vacantes.</strong></p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center">Folio Vacante</th>
<th style="text-align:center">Nombre de la Vacante</th>
<th style="text-align:center">Dias Transcurridos</th>
<th style="text-align:center">Pull Vac.</th>
</tr>
</thead>';
$body .= '<tbody>';
if($totalRows_cubiertas > 0) {
do{ 
if($row_cubiertas['IDmotivo_v'] == 5) {$pullvacacionesc = "Si";} else {$pullvacacionesc = "No";}
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_cubiertas['IDvacante'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_cubiertas['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=   $row_cubiertas['Dias_transcurridos'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $pullvacacionesc;
$body .= '</td>';
$body .= '</tr>';
} while($row_cubiertas=mysql_fetch_array($cubiertas));
} else {	
$body .= '<tr>';
$body .= '<td  colspan="4">No hay vacantes cubiertas.</td>';
$body .= '</tr>';
}
$body .= '</tbody></table>
                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
										<p style="line-height: 24px;margin-bottom:15px;">
                                          <h3><a href="https://gestionvacantes.com/indicadors.php">Rotaci&oacute;n</a></h3>
                                        </p>
                                        <p>A continuaci&oacute;n se muestran los resultados de rotaci&oacute;n del &uacute;ltimo mes.</p>

							<p>Resultado anual '.$anio_anterior.': <strong>'.$row_resultados_anterior['objetivo'].'%</strong> </p>
							<p>Objetivo mensual '.$anio_actual.': <strong>'.round(($row_resultados_anterior['objetivo'] - $row_resultados['reduccion'] ) / 12, 1).'%</strong> </p>
							<p>Resultado &uacute;ltimo mes: <strong>'.round($RotTotalM1 * 100, 1).'%</strong> </p>
<p><em>Nota: En el SGRH puedes consultar, los motivos y la rotaci&oacute;n por &aacute;rea.</em></p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center">AREA</th>
<th style="text-align:center">Bajas</th>
<th style="text-align:center">Activos</th>
<th style="text-align:center">Rotaci&oacute;n</th>
</tr>';
$body .= '</thead>';
$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td>Almac&eacute;n</td>';
$body .= '<td align="center">';
$body .=  $ar1an1r;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Toar1an1r;
$body .= '</td>';
$body .= '<td align="center">';
if ($ar1an1r != 0) {$ttt1 = (round($ar1an1r / $Toar1an1r, 3) * 100);} else {$ttt1 = 0;}
$body .= $ttt1.'%';
$body .= '</tr>';

$body .= '<tr>';
$body .= '<td>Almac&eacute;n Detalle</td>';
$body .= '<td align="center">';
$body .=  $ar2an1r;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Toar2an1r;
$body .= '</td>';
$body .= '<td align="center">';
if ($ar2an1r != 0) {$ttt2 = (round($ar2an1r / $Toar2an1r, 3) * 100);} else {$ttt2 = 0;}
$body .= $ttt2.'%';
$body .= '</tr>';

$body .= '<tr>';
$body .= '<td>Distribuci&oacute;n</td>';
$body .= '<td align="center">';
$body .=  $ar3an1r;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Toar3an1r;
$body .= '</td>';
$body .= '<td align="center">';
if ($ar3an1r != 0) {$ttt3 = (round($ar3an1r / $Toar3an1r, 3) * 100);} else {$ttt3 = 0;}
$body .= $ttt3.'%';
$body .= '</tr>';

$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td>Distribuci&oacute;n Detalle</td>';
$body .= '<td align="center">';
$body .=  $ar4an1r;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Toar4an1r;
$body .= '</td>';
$body .= '<td align="center">';
if ($ar4an1r != 0) {$ttt4 = (round($ar4an1r / $Toar4an1r, 3) * 100);} else {$ttt4 = 0;}
$body .= $ttt4.'%';
$body .= '</tr>';

$body .= '<tr>';
$body .= '<td>Ventas</td>';
$body .= '<td align="center">';
$body .=  $ar5an1r;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Toar5an1r;
$body .= '</td>';
$body .= '<td align="center">';
if ($ar5an1r != 0) {$ttt5 = (round($ar5an1r / $Toar5an1r, 3) * 100);} else {$ttt5 = 0;}
$body .= $ttt5.'%';
$body .= '</tr>';

$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td>Ventas Detalle</td>';
$body .= '<td align="center">';
$body .=  $ar6an1r;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Toar6an1r;
$body .= '</td>';
$body .= '<td align="center">';
if ($ar6an1r != 0) {$ttt6 = (round($ar6an1r / $Toar6an1r, 3) * 100);} else {$ttt6 = 0;}
$body .= $ttt6.'%';
$body .= '</tr>';

$body .= '<tr>';
$body .= '<td>Administraci&oacute;n</td>';
$body .= '<td align="center">';
$body .=  $ar7an1r;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Toar7an1r;
$body .= '</td>';
$body .= '<td align="center">';
if ($ar7an1r != 0) {$ttt7 = (round($ar7an1r / $Toar7an1r, 3) * 100);} else {$ttt7 = 0;}
$body .= $ttt7.'%';
$body .= '</tr>';

$body .= '<tfood>';
$body .= '<tr>';
$body .= '<td><strong>Total</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $ar1an1r + $ar2an1r + $ar3an1r + $ar4an1r + $ar5an1r + $ar6an1r;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Toar1an1r + $Toar2an1r + $Toar3an1r + $Toar4an1r + $Toar5an1r + $Toar6an1r + $Toar7an1r;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .= round($RotTotalM1 * 100, 1).'%';
$body .= '</strong></tr>';
$body .= '</tfood>';

$body .= '</tbody></table>
								
                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
                                        <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="C30F2D" style="margin-bottom:20px;">

                                            <tr>
                                                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                                            </tr>

                                            <tr>
                                                <td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">
                                                    <!-- main section button -->

                                                    <div style="line-height: 22px;">
                                                        <a href="https://gestionvacantes.com/f_index.php" style="color: #ffffff; text-decoration: none;">Accede al SGRH</a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                                            </tr>

                                        </table>
                                        <p style="line-height: 24px">
                                            Saludos Cordiales,</br>
                                            <strong>Sistema de Gesti&oacute;n de Recursos Humanos </strong></br>
											Sahuayo 2022</br>
                                        </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
        <tr>
            <td><p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>Si recibiste por error este correo o no quieres recibirlo en adelante, solicita tu baja a <a href="mailto:jacardenas@sahuayo.mx">jacardenas@sahuayo.mx</a></td>
        </tr>
    </table>
</body>
</html>
';
$mail->Body = $body;
echo $body;
if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo; }
// cierre loop para cada matriz
} while ($row_matriz = mysql_fetch_assoc($matriz));
?>


