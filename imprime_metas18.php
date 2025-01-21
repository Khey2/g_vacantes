<?php require_once('../Connections/sahuayo.php'); ?>
<?php
header('Content-Type:text/html; charset=UTF-8');
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


// PHPExcel
require_once('../includes/Classes/PHPExcel.php');

// PHPExcel_IOFactory
include('../includes/Classes/PHPExcel/IOFactory.php');


//Ampliamos la session
if (!isset($_SESSION)) {
ini_set("session.cookie_lifetime", 10800);
ini_set("session.gc_maxlifetime", 10800); 
  session_start();
}

mysql_select_db($database_sahuayo, $sahuayo);
$query_variables = "SELECT * FROM sed_variables";
$variables = mysql_query($query_variables, $sahuayo) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$periodo = $row_variables['anio'];
$annio = $_GET['anio'];
$con_resultado = $_GET['resultado'];


$usuario1 = $_GET['usuario'];

$colname_usuario = "-1";
if (isset($_GET['usuario'])) {
  $colname_usuario = $_GET['usuario'];
}
mysql_select_db($database_sahuayo, $sahuayo);
$query_usuario = sprintf("SELECT * FROM sed_usuarios WHERE usuario = %s", GetSQLValueString($colname_usuario, "text"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $sahuayo) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$plaza = $row_usuario['IDPuesto'];

$query_jeff = "SELECT * FROM sed_puestos WHERE IDPuesto = '$plaza'"; 
mysql_query("SET NAMES 'utf8'");
$jeff = mysql_query($query_jeff, $sahuayo) or die(mysql_error());
$row_jeff = mysql_fetch_assoc($jeff);
$totalRows_jeff = mysql_num_rows($jeff);
$jeff = $row_jeff['IDPlaza_jefe'];

$query_boss = "SELECT * FROM sed_puestos WHERE IDPuesto = '$jeff'"; 
mysql_query("SET NAMES 'utf8'");
$boss = mysql_query($query_boss, $sahuayo) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
$totalRows_boss = mysql_num_rows($boss);
$jefe2 = $row_boss['IDPuesto'];

$query_boss2 = "SELECT * FROM sed_usuarios WHERE IDPuesto = '$jefe2'"; 
$boss2 = mysql_query($query_boss2, $sahuayo) or die(mysql_error());
$row_boss2 = mysql_fetch_assoc($boss2);
$totalRows_boss2 = mysql_num_rows($boss2);
$jefe = $row_boss2['usuario_nombre'];

$colname_individuales = "-1";
if (isset($_GET['usuario'])) {
  $colname_individuales = $_GET['usuario'];
}
mysql_select_db($database_sahuayo, $sahuayo);
$query_individuales = sprintf("SELECT * FROM sed_individuales WHERE usuario = %s and anio = '$annio'", GetSQLValueString($colname_individuales, "text"));
mysql_query("SET NAMES 'utf8'");
$individuales = mysql_query($query_individuales, $sahuayo) or die(mysql_error());
$row_individuales = mysql_fetch_assoc($individuales);
$totalRows_individuales = mysql_num_rows($individuales);

$puesto = $row_usuario['IDPuesto'];

$query_puesto = "SELECT * FROM sed_puestos WHERE IDPuesto = '$puesto'";
mysql_query("SET NAMES 'utf8'");
$puesto = mysql_query($query_puesto, $sahuayo) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

$usuario1 = $_GET['usuario'];
mysql_select_db($database_sahuayo, $sahuayo);
$query_fechas = "SELECT usuario, DATE_FORMAT(fecha_ingreso,'%d-%m-%y') as fecha FROM sed_usuarios WHERE  usuario = '$usuario1'";
mysql_query("SET NAMES 'utf8'");
$fechas = mysql_query($query_fechas, $sahuayo) or die(mysql_error());
$row_fechas = mysql_fetch_assoc($fechas);
$totalRows_fechas = mysql_num_rows($fechas);
$fecha1 = $row_fechas['fecha'];
$fecha2 = $row_usuario['fecha_ingreso'];

//variables usuario
$nombre = $row_usuario['usuario_nombre'];
$edad = $row_usuario['edad'];

$fecha_de_nacimiento = $fecha2; 
$fecha_actual = date ("Y-m-d"); 
//$fecha_actual = date ("2006-03-05"); //para pruebas 

// separamos en partes las fechas 
$array_nacimiento = explode ( "-", $fecha_de_nacimiento ); 
$array_actual = explode ( "-", $fecha_actual ); 

$anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años 
$meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
$dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días 

//ajuste de posible negativo en $días 
if ($dias < 0) 
{ 
    --$meses; 

    //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual 
    switch ($array_actual[1]) { 
           case 1:     $dias_mes_anterior=31; break; 
           case 2:     $dias_mes_anterior=31; break; 
           case 3:  
                if (bisiesto($array_actual[0])) 
                { 
                    $dias_mes_anterior=29; break; 
                } else { 
                    $dias_mes_anterior=28; break; 
                } 
           case 4:     $dias_mes_anterior=31; break; 
           case 5:     $dias_mes_anterior=30; break; 
           case 6:     $dias_mes_anterior=31; break; 
           case 7:     $dias_mes_anterior=30; break; 
           case 8:     $dias_mes_anterior=31; break; 
           case 9:     $dias_mes_anterior=31; break; 
           case 10:     $dias_mes_anterior=30; break; 
           case 11:     $dias_mes_anterior=31; break; 
           case 12:     $dias_mes_anterior=30; break; 
    } 

    $dias=$dias + $dias_mes_anterior; 
} 

//ajuste de posible negativo en $meses 
if ($meses < 0) 
{ 
    --$anos; 
    $meses=$meses + 12; 
} 


function bisiesto($periodo_actual){ 
    $bisiesto=false; 
    //probamos si el mes de febrero del año actual tiene 29 días 
      if (checkdate(2,29,$periodo_actual)) 
      { 
        $bisiesto=true; 
    } 
    return $bisiesto; 
} 


$aa = ' años, ';
$bb = ' meses';
$cc = $anos;
$dd = $meses;
$antiguedad = $anos . utf8_encode($aa) . $meses . $bb;
$fecha = $row_fechas['fecha'];
$empleado = $row_usuario['IDUsuario'];

//variables puesto
$puesto = $row_puesto['denominacion'];
$sucursal = $row_puesto['sucursal'];
$area = $row_puesto['area'];

//fechas
$fecha_ingreso = $row_fechas['fecha'];
$observaciones = $row_individuales['mi_acciones_correctivas'];

//metas
$mi_m1 = $row_individuales['mi_m1'];
$mi_m1_um = $row_individuales['mi_m1_um'];
$mi_m1_ponderacion = $row_individuales['mi_m1_ponderacion'];
$mi_m1_satis = $row_individuales['mi_m1_satis'];
$mi_m1_suficiente = $row_individuales['mi_m1_suficiente'];
$mi_m1_deficiente = $row_individuales['mi_m1_deficiente'];
$mi_m2 = $row_individuales['mi_m2'];
$mi_m2_um = $row_individuales['mi_m2_um'];
$mi_m2_ponderacion = $row_individuales['mi_m2_ponderacion'];
$mi_m2_satis = $row_individuales['mi_m2_satis'];
$mi_m2_suficiente = $row_individuales['mi_m2_suficiente'];
$mi_m2_deficiente = $row_individuales['mi_m2_deficiente'];
$mi_m3 = $row_individuales['mi_m3'];
$mi_m3_um = $row_individuales['mi_m3_um'];
$mi_m3_ponderacion = $row_individuales['mi_m3_ponderacion'];
$mi_m3_satis = $row_individuales['mi_m3_satis'];
$mi_m3_suficiente = $row_individuales['mi_m3_suficiente'];
$mi_m3_deficiente = $row_individuales['mi_m3_deficiente'];
$mi_m4 = $row_individuales['mi_m4'];
$mi_m4_um = $row_individuales['mi_m4_um'];
$mi_m4_ponderacion = $row_individuales['mi_m4_ponderacion'];
$mi_m4_satis = $row_individuales['mi_m4_satis'];
$mi_m4_suficiente = $row_individuales['mi_m4_suficiente'];
$mi_m4_deficiente = $row_individuales['mi_m4_deficiente'];
$mi_m5 = $row_individuales['mi_m5'];
$mi_m5_um = $row_individuales['mi_m5_um'];
$mi_m5_ponderacion = $row_individuales['mi_m5_ponderacion'];
$mi_m5_satis = $row_individuales['mi_m5_satis'];
$mi_m5_suficiente = $row_individuales['mi_m5_suficiente'];
$mi_m5_deficiente = $row_individuales['mi_m5_deficiente'];
$mi_m6 = $row_individuales['mi_m6'];
$mi_m6_um = $row_individuales['mi_m6_um'];
$mi_m6_ponderacion = $row_individuales['mi_m6_ponderacion'];
$mi_m6_satis = $row_individuales['mi_m6_satis'];
$mi_m6_suficiente = $row_individuales['mi_m6_suficiente'];
$mi_m6_deficiente = $row_individuales['mi_m6_deficiente'];
$mi_m7 = $row_individuales['mi_m7'];
$mi_m7_um = $row_individuales['mi_m7_um'];
$mi_m7_ponderacion = $row_individuales['mi_m7_ponderacion'];
$mi_m7_satis = $row_individuales['mi_m7_satis'];
$mi_m7_suficiente = $row_individuales['mi_m7_suficiente'];
$mi_m7_deficiente = $row_individuales['mi_m7_deficiente'];

//resultados
$mi_m1_resultado_prev = $row_individuales['mi_m1_resultado_prev'];
$mi_m2_resultado_prev = $row_individuales['mi_m2_resultado_prev'];
$mi_m3_resultado_prev = $row_individuales['mi_m3_resultado_prev'];
$mi_m4_resultado_prev = $row_individuales['mi_m4_resultado_prev'];
$mi_m5_resultado_prev = $row_individuales['mi_m5_resultado_prev'];
$mi_m6_resultado_prev = $row_individuales['mi_m6_resultado_prev'];
$mi_m7_resultado_prev = $row_individuales['mi_m7_resultado_prev'];


//obs
$mi_m1_obs = $row_individuales['mi_m1_obs'];
$mi_m2_obs = $row_individuales['mi_m2_obs'];
$mi_m3_obs = $row_individuales['mi_m3_obs'];
$mi_m4_obs = $row_individuales['mi_m4_obs'];
$mi_m5_obs = $row_individuales['mi_m5_obs'];
$mi_m6_obs = $row_individuales['mi_m6_obs'];
$mi_m7_obs = $row_individuales['mi_m7_obs'];
$obs_texto_1 = 'Objetivo 1: ';
$obs_texto_2 = 'Objetivo 2: ';
$obs_texto_3 = 'Objetivo 3: ';
$obs_texto_4 = 'Objetivo 4: ';
$obs_texto_5 = 'Objetivo 5: ';
$obs_texto_6 = 'Objetivo 6: ';
$obs_texto_7 = 'Objetivo 7: ';
$salto = '
';
$coments = $obs_texto_1  . $mi_m1_obs . $salto . $obs_texto_2  . $mi_m2_obs . $salto . $obs_texto_3  . $mi_m3_obs . $salto . $obs_texto_4  . $mi_m4_obs . $salto . $obs_texto_5  . $mi_m5_obs . $salto . $obs_texto_6  . $mi_m6_obs . $salto . $obs_texto_7  . $mi_m7_obs;

//indicadores
$mi_m1_IDTipo_indicador = $row_individuales['mi_m1_IDTipo_indicador'];
$mi_m1_IDIndicador = $row_individuales['mi_m1_IDIndicador'];
$mi_m2_IDTipo_indicador = $row_individuales['mi_m2_IDTipo_indicador'];
$mi_m2_IDIndicador = $row_individuales['mi_m2_IDIndicador'];
$mi_m3_IDTipo_indicador = $row_individuales['mi_m3_IDTipo_indicador'];
$mi_m3_IDIndicador = $row_individuales['mi_m3_IDIndicador'];
$mi_m4_IDTipo_indicador = $row_individuales['mi_m4_IDTipo_indicador'];
$mi_m4_IDIndicador = $row_individuales['mi_m4_IDIndicador'];
$mi_m5_IDTipo_indicador = $row_individuales['mi_m5_IDTipo_indicador'];
$mi_m5_IDIndicador = $row_individuales['mi_m5_IDIndicador'];
$mi_m6_IDTipo_indicador = $row_individuales['mi_m6_IDTipo_indicador'];
$mi_m6_IDIndicador = $row_individuales['mi_m6_IDIndicador'];
$mi_m7_IDTipo_indicador = $row_individuales['mi_m7_IDTipo_indicador'];
$mi_m7_IDIndicador = $row_individuales['mi_m7_IDIndicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_resultado_1 = "SELECT * FROM sed_ponderaciones WHERE calificacion = '$mi_m1_resultado_prev'";
$resultado_1 = mysql_query($query_resultado_1, $sahuayo) or die(mysql_error());
$row_resultado_1 = mysql_fetch_assoc($resultado_1);
$totalRows_resultado_1 = mysql_num_rows($resultado_1);
$mi_m1_resultado_ = $row_resultado_1['valor'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_resultado_2 = "SELECT * FROM sed_ponderaciones WHERE calificacion = '$mi_m2_resultado_prev'";
$resultado_2 = mysql_query($query_resultado_2, $sahuayo) or die(mysql_error());
$row_resultado_2 = mysql_fetch_assoc($resultado_2);
$totalRows_resultado_2 = mysql_num_rows($resultado_2);
$mi_m2_resultado_ = $row_resultado_2['valor'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_resultado_3 = "SELECT * FROM sed_ponderaciones WHERE calificacion = '$mi_m3_resultado_prev'";
$resultado_3 = mysql_query($query_resultado_3, $sahuayo) or die(mysql_error());
$row_resultado_3 = mysql_fetch_assoc($resultado_3);
$totalRows_resultado_3 = mysql_num_rows($resultado_3);
$mi_m3_resultado_ = $row_resultado_3['valor'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_resultado_4 = "SELECT * FROM sed_ponderaciones WHERE calificacion = '$mi_m4_resultado_prev'";
$resultado_4 = mysql_query($query_resultado_4, $sahuayo) or die(mysql_error());
$row_resultado_4 = mysql_fetch_assoc($resultado_4);
$totalRows_resultado_4 = mysql_num_rows($resultado_4);
$mi_m4_resultado_ = $row_resultado_4['valor'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_resultado_5 = "SELECT * FROM sed_ponderaciones WHERE calificacion = '$mi_m5_resultado_prev'";
$resultado_5 = mysql_query($query_resultado_5, $sahuayo) or die(mysql_error());
$row_resultado_5 = mysql_fetch_assoc($resultado_5);
$totalRows_resultado_5 = mysql_num_rows($resultado_5);
$mi_m5_resultado_ = $row_resultado_5['valor'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_resultado_6 = "SELECT * FROM sed_ponderaciones WHERE calificacion = '$mi_m6_resultado_prev'";
$resultado_6 = mysql_query($query_resultado_6, $sahuayo) or die(mysql_error());
$row_resultado_6 = mysql_fetch_assoc($resultado_6);
$totalRows_resultado_6 = mysql_num_rows($resultado_6);
$mi_m6_resultado_ = $row_resultado_6['valor'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_resultado_7 = "SELECT * FROM sed_ponderaciones WHERE calificacion = '$mi_m7_resultado_prev'";
$resultado_7 = mysql_query($query_resultado_7, $sahuayo) or die(mysql_error());
$row_resultado_7 = mysql_fetch_assoc($resultado_7);
$totalRows_resultado_7 = mysql_num_rows($resultado_7);
$mi_m7_resultado_ = $row_resultado_7['valor'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_unidad_1 = "SELECT * FROM sed_unidad_medida WHERE id = '$mi_m1_um'";
$unidad_1 = mysql_query($query_unidad_1, $sahuayo) or die(mysql_error());
$row_unidad_1 = mysql_fetch_assoc($unidad_1);
$totalRows_unidad_1 = mysql_num_rows($unidad_1);
$mi_m1_ume = $row_unidad_1['unidad_med'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_unidad_2 = "SELECT * FROM sed_unidad_medida WHERE id = '$mi_m2_um'";
$unidad_2 = mysql_query($query_unidad_2, $sahuayo) or die(mysql_error());
$row_unidad_2 = mysql_fetch_assoc($unidad_2);
$totalRows_unidad_2 = mysql_num_rows($unidad_2);
$mi_m2_ume = $row_unidad_2['unidad_med'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_unidad_3 = "SELECT * FROM sed_unidad_medida WHERE id = '$mi_m3_um'";
$unidad_3 = mysql_query($query_unidad_3, $sahuayo) or die(mysql_error());
$row_unidad_3 = mysql_fetch_assoc($unidad_3);
$totalRows_unidad_3 = mysql_num_rows($unidad_3);
$mi_m3_ume = $row_unidad_3['unidad_med'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_unidad_4 = "SELECT * FROM sed_unidad_medida WHERE id = '$mi_m4_um'";
$unidad_4 = mysql_query($query_unidad_4, $sahuayo) or die(mysql_error());
$row_unidad_4 = mysql_fetch_assoc($unidad_4);
$totalRows_unidad_4 = mysql_num_rows($unidad_4);
$mi_m4_ume = $row_unidad_4['unidad_med'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_unidad_5 = "SELECT * FROM sed_unidad_medida WHERE id = '$mi_m5_um'";
$unidad_5 = mysql_query($query_unidad_5, $sahuayo) or die(mysql_error());
$row_unidad_5 = mysql_fetch_assoc($unidad_5);
$totalRows_unidad_5 = mysql_num_rows($unidad_5);
$mi_m5_ume = $row_unidad_5['unidad_med'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_unidad_6 = "SELECT * FROM sed_unidad_medida WHERE id = '$mi_m6_um'";
$unidad_6 = mysql_query($query_unidad_6, $sahuayo) or die(mysql_error());
$row_unidad_6 = mysql_fetch_assoc($unidad_6);
$totalRows_unidad_6 = mysql_num_rows($unidad_6);
$mi_m6_ume = $row_unidad_6['unidad_med'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_unidad_7 = "SELECT * FROM sed_unidad_medida WHERE id = '$mi_m7_um'";
$unidad_7 = mysql_query($query_unidad_7, $sahuayo) or die(mysql_error());
$row_unidad_7 = mysql_fetch_assoc($unidad_7);
$totalRows_unidad_7 = mysql_num_rows($unidad_7);
$mi_m7_ume = $row_unidad_7['unidad_med'];

//indicadores
mysql_select_db($database_sahuayo, $sahuayo);
$query_tipo_indicador_1= "SELECT * FROM sed_indicadores_tipos WHERE IDTipo_indicador = '$mi_m1_IDTipo_indicador'";
$tipo_indicador_1= mysql_query($query_tipo_indicador_1, $sahuayo) or die(mysql_error());
$row_tipo_indicador_1= mysql_fetch_assoc($tipo_indicador_1);
$totalRows_tipo_indicador_1= mysql_num_rows($tipo_indicador_1);
$mi_m1_IDTipo_indicador_ = $row_tipo_indicador_1['tipo_indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_tipo_indicador_2= "SELECT * FROM sed_indicadores_tipos WHERE IDTipo_indicador = '$mi_m2_IDTipo_indicador'";
$tipo_indicador_2= mysql_query($query_tipo_indicador_2, $sahuayo) or die(mysql_error());
$row_tipo_indicador_2= mysql_fetch_assoc($tipo_indicador_2);
$totalRows_tipo_indicador_2= mysql_num_rows($tipo_indicador_2);
$mi_m2_IDTipo_indicador_ = $row_tipo_indicador_2['tipo_indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_tipo_indicador_3= "SELECT * FROM sed_indicadores_tipos WHERE IDTipo_indicador = '$mi_m3_IDTipo_indicador'";
$tipo_indicador_3= mysql_query($query_tipo_indicador_3, $sahuayo) or die(mysql_error());
$row_tipo_indicador_3= mysql_fetch_assoc($tipo_indicador_3);
$totalRows_tipo_indicador_3= mysql_num_rows($tipo_indicador_3);
$mi_m3_IDTipo_indicador_ = $row_tipo_indicador_3['tipo_indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_tipo_indicador_4= "SELECT * FROM sed_indicadores_tipos WHERE IDTipo_indicador = '$mi_m4_IDTipo_indicador'";
$tipo_indicador_4= mysql_query($query_tipo_indicador_4, $sahuayo) or die(mysql_error());
$row_tipo_indicador_4= mysql_fetch_assoc($tipo_indicador_4);
$totalRows_tipo_indicador_4= mysql_num_rows($tipo_indicador_4);
$mi_m4_IDTipo_indicador_ = $row_tipo_indicador_4['tipo_indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_tipo_indicador_5= "SELECT * FROM sed_indicadores_tipos WHERE IDTipo_indicador = '$mi_m5_IDTipo_indicador'";
$tipo_indicador_5= mysql_query($query_tipo_indicador_5, $sahuayo) or die(mysql_error());
$row_tipo_indicador_5= mysql_fetch_assoc($tipo_indicador_5);
$totalRows_tipo_indicador_5= mysql_num_rows($tipo_indicador_5);
$mi_m5_IDTipo_indicador_ = $row_tipo_indicador_5['tipo_indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_tipo_indicador_6= "SELECT * FROM sed_indicadores_tipos WHERE IDTipo_indicador = '$mi_m6_IDTipo_indicador'";
$tipo_indicador_6= mysql_query($query_tipo_indicador_6, $sahuayo) or die(mysql_error());
$row_tipo_indicador_6= mysql_fetch_assoc($tipo_indicador_6);
$totalRows_tipo_indicador_6= mysql_num_rows($tipo_indicador_6);
$mi_m6_IDTipo_indicador_ = $row_tipo_indicador_6['tipo_indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_tipo_indicador_7= "SELECT * FROM sed_indicadores_tipos WHERE IDTipo_indicador = '$mi_m7_IDTipo_indicador'";
$tipo_indicador_7= mysql_query($query_tipo_indicador_7, $sahuayo) or die(mysql_error());
$row_tipo_indicador_7= mysql_fetch_assoc($tipo_indicador_7);
$totalRows_tipo_indicador_7= mysql_num_rows($tipo_indicador_7);
$mi_m7_IDTipo_indicador_ = $row_tipo_indicador_7['tipo_indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_indicador_1= "SELECT * FROM sed_indicadores_sus WHERE IDindicador = '$mi_m1_IDIndicador'";
$indicador_1= mysql_query($query_indicador_1, $sahuayo) or die(mysql_error());
$row_indicador_1= mysql_fetch_assoc($indicador_1);
$totalRows_indicador_1= mysql_num_rows($indicador_1);
$mi_m1_IDIndicador_ = $row_indicador_1['indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_indicador_2= "SELECT * FROM sed_indicadores_sus WHERE IDindicador = '$mi_m2_IDIndicador'";
$indicador_2= mysql_query($query_indicador_2, $sahuayo) or die(mysql_error());
$row_indicador_2= mysql_fetch_assoc($indicador_2);
$totalRows_indicador_2= mysql_num_rows($indicador_2);
$mi_m2_IDIndicador_ = $row_indicador_2['indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_indicador_3= "SELECT * FROM sed_indicadores_sus WHERE IDindicador = '$mi_m3_IDIndicador'";
$indicador_3= mysql_query($query_indicador_3, $sahuayo) or die(mysql_error());
$row_indicador_3= mysql_fetch_assoc($indicador_3);
$totalRows_indicador_3= mysql_num_rows($indicador_3);
$mi_m3_IDIndicador_ = $row_indicador_3['indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_indicador_4= "SELECT * FROM sed_indicadores_sus WHERE IDindicador = '$mi_m4_IDIndicador'";
$indicador_4= mysql_query($query_indicador_4, $sahuayo) or die(mysql_error());
$row_indicador_4= mysql_fetch_assoc($indicador_4);
$totalRows_indicador_4= mysql_num_rows($indicador_4);
$mi_m4_IDIndicador_ = $row_indicador_4['indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_indicador_5= "SELECT * FROM sed_indicadores_sus WHERE IDindicador = '$mi_m5_IDIndicador'";
$indicador_5= mysql_query($query_indicador_5, $sahuayo) or die(mysql_error());
$row_indicador_5= mysql_fetch_assoc($indicador_5);
$totalRows_indicador_5= mysql_num_rows($indicador_5);
$mi_m5_IDIndicador_ = $row_indicador_5['indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_indicador_6= "SELECT * FROM sed_indicadores_sus WHERE IDindicador = '$mi_m6_IDIndicador'";
$indicador_6= mysql_query($query_indicador_6, $sahuayo) or die(mysql_error());
$row_indicador_6= mysql_fetch_assoc($indicador_6);
$totalRows_indicador_6= mysql_num_rows($indicador_6);
$mi_m6_IDIndicador_ = $row_indicador_6['indicador'];

mysql_select_db($database_sahuayo, $sahuayo);
$query_indicador_7= "SELECT * FROM sed_indicadores_sus WHERE IDindicador = '$mi_m7_IDIndicador'";
$indicador_7= mysql_query($query_indicador_7, $sahuayo) or die(mysql_error());
$row_indicador_7= mysql_fetch_assoc($indicador_7);
$totalRows_indicador_7= mysql_num_rows($indicador_7);
$mi_m7_IDIndicador_ = $row_indicador_7['indicador'];

// PHPExcel SE CONSTRUYE EL ARCHIVO
// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos la plantlla
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("formato20202.xlsx");

// Indicamos que se pare en la hoja uno del libro
$objPHPExcel->setActiveSheetIndex(0);


//GENERALES
$objPHPExcel->getActiveSheet()->setCellValue('F1', $nombre);
$objPHPExcel->getActiveSheet()->mergeCells('F1:H1');
$objPHPExcel->getActiveSheet()->setCellValue('F2', $puesto);
$objPHPExcel->getActiveSheet()->mergeCells('F2:H2');
$objPHPExcel->getActiveSheet()->setCellValue('B2', $sucursal);
$objPHPExcel->getActiveSheet()->mergeCells('B2:C2');
$objPHPExcel->getActiveSheet()->setCellValue('B1', $area);
$objPHPExcel->getActiveSheet()->mergeCells('B1:C1');
$objPHPExcel->getActiveSheet()->setCellValue('J1', $empleado);
$objPHPExcel->getActiveSheet()->setCellValue('J2', $fecha_ingreso);
$objPHPExcel->getActiveSheet()->setCellValue('B3', $antiguedad);
$objPHPExcel->getActiveSheet()->mergeCells('B3:C3');

//METAS
$objPHPExcel->getActiveSheet()->setCellValue('A7', $mi_m1); 
$objPHPExcel->getActiveSheet()->mergeCells('A7:D7');
$objPHPExcel->getActiveSheet()->getStyle('A7:D7')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A13', $mi_m2);
$objPHPExcel->getActiveSheet()->mergeCells('A13:D13');
$objPHPExcel->getActiveSheet()->getStyle('A13:D13')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A19', $mi_m3);
$objPHPExcel->getActiveSheet()->mergeCells('A19:D19');
$objPHPExcel->getActiveSheet()->getStyle('A19:D19')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A25', $mi_m4);
$objPHPExcel->getActiveSheet()->mergeCells('A25:D25');
$objPHPExcel->getActiveSheet()->getStyle('A25:D25')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A31', $mi_m5);
$objPHPExcel->getActiveSheet()->mergeCells('A31:D31');
$objPHPExcel->getActiveSheet()->getStyle('A31:D31')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A37', $mi_m6);
$objPHPExcel->getActiveSheet()->mergeCells('A37:D37');
$objPHPExcel->getActiveSheet()->getStyle('A37:D37')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A43', $mi_m7);
$objPHPExcel->getActiveSheet()->mergeCells('A43:D43');
$objPHPExcel->getActiveSheet()->getStyle('A43:D43')->getAlignment()->setWrapText(true);

//Ponderaciones
$objPHPExcel->getActiveSheet()->setCellValue('B8', $mi_m1_ponderacion); 
$objPHPExcel->getActiveSheet()->setCellValue('E8', $mi_m1_ume); 
$objPHPExcel->getActiveSheet()->mergeCells('E8:F8');
$objPHPExcel->getActiveSheet()->setCellValue('B14', $mi_m2_ponderacion); 
$objPHPExcel->getActiveSheet()->setCellValue('E14', $mi_m2_ume); 
$objPHPExcel->getActiveSheet()->mergeCells('E14:F14');
$objPHPExcel->getActiveSheet()->setCellValue('B20', $mi_m3_ponderacion); 
$objPHPExcel->getActiveSheet()->setCellValue('E20', $mi_m3_ume); 
$objPHPExcel->getActiveSheet()->mergeCells('E20:F20');
$objPHPExcel->getActiveSheet()->setCellValue('B26', $mi_m4_ponderacion); 
$objPHPExcel->getActiveSheet()->setCellValue('E26', $mi_m4_ume); 
$objPHPExcel->getActiveSheet()->mergeCells('E26:F26');
$objPHPExcel->getActiveSheet()->setCellValue('B32', $mi_m5_ponderacion); 
$objPHPExcel->getActiveSheet()->setCellValue('E32', $mi_m5_ume); 
$objPHPExcel->getActiveSheet()->mergeCells('E32:F32');
$objPHPExcel->getActiveSheet()->setCellValue('B38', $mi_m6_ponderacion); 
$objPHPExcel->getActiveSheet()->setCellValue('E38', $mi_m6_ume); 
$objPHPExcel->getActiveSheet()->mergeCells('E38:F38');
$objPHPExcel->getActiveSheet()->setCellValue('B44', $mi_m7_ponderacion); 
$objPHPExcel->getActiveSheet()->setCellValue('E44', $mi_m7_ume); 
$objPHPExcel->getActiveSheet()->mergeCells('E44:F44');

//objetivos
$objPHPExcel->getActiveSheet()->setCellValue('E7', $mi_m1_satis); 
$objPHPExcel->getActiveSheet()->mergeCells('E7:F7');
$objPHPExcel->getActiveSheet()->setCellValue('G7', $mi_m1_suficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('G7:H7');
$objPHPExcel->getActiveSheet()->setCellValue('I7', $mi_m1_deficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('I7:J7');
$objPHPExcel->getActiveSheet()->setCellValue('E13', $mi_m2_satis); 
$objPHPExcel->getActiveSheet()->mergeCells('E13:F13');
$objPHPExcel->getActiveSheet()->setCellValue('G13', $mi_m2_suficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('G13:H13');
$objPHPExcel->getActiveSheet()->setCellValue('I13', $mi_m2_deficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('I13:J13');
$objPHPExcel->getActiveSheet()->setCellValue('E19', $mi_m3_satis); 
$objPHPExcel->getActiveSheet()->mergeCells('E19:F19');
$objPHPExcel->getActiveSheet()->setCellValue('G19', $mi_m3_suficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('G19:H19');
$objPHPExcel->getActiveSheet()->setCellValue('I19', $mi_m3_deficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('I19:J19');
$objPHPExcel->getActiveSheet()->setCellValue('E25', $mi_m4_satis); 
$objPHPExcel->getActiveSheet()->mergeCells('E25:F25');
$objPHPExcel->getActiveSheet()->setCellValue('G25', $mi_m4_suficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('G25:H25');
$objPHPExcel->getActiveSheet()->setCellValue('I25', $mi_m4_deficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('I25:J25');
$objPHPExcel->getActiveSheet()->setCellValue('E31', $mi_m5_satis); 
$objPHPExcel->getActiveSheet()->mergeCells('E31:F31');
$objPHPExcel->getActiveSheet()->setCellValue('G31', $mi_m5_suficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('G31:H31');
$objPHPExcel->getActiveSheet()->setCellValue('I31', $mi_m5_deficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('I31:J31');
$objPHPExcel->getActiveSheet()->setCellValue('E37', $mi_m6_satis); 
$objPHPExcel->getActiveSheet()->mergeCells('E37:F37');
$objPHPExcel->getActiveSheet()->setCellValue('G37', $mi_m6_suficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('G37:H37');
$objPHPExcel->getActiveSheet()->setCellValue('I37', $mi_m6_deficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('I37:J37');
$objPHPExcel->getActiveSheet()->setCellValue('E43', $mi_m7_satis); 
$objPHPExcel->getActiveSheet()->mergeCells('E43:F43');
$objPHPExcel->getActiveSheet()->setCellValue('G43', $mi_m7_suficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('G43:H43');
$objPHPExcel->getActiveSheet()->setCellValue('I43', $mi_m7_deficiente); 
$objPHPExcel->getActiveSheet()->mergeCells('I43:J43');

//Indicadores
$objPHPExcel->getActiveSheet()->setCellValue('C9', $mi_m1_IDTipo_indicador_); 
$objPHPExcel->getActiveSheet()->mergeCells('C9:D9');
$objPHPExcel->getActiveSheet()->setCellValue('C15', $mi_m2_IDTipo_indicador_); 
$objPHPExcel->getActiveSheet()->mergeCells('C15:D15');
$objPHPExcel->getActiveSheet()->setCellValue('C21', $mi_m3_IDTipo_indicador_); 
$objPHPExcel->getActiveSheet()->mergeCells('C21:D21');
$objPHPExcel->getActiveSheet()->setCellValue('C27', $mi_m4_IDTipo_indicador_); 
$objPHPExcel->getActiveSheet()->mergeCells('C27:D27');
$objPHPExcel->getActiveSheet()->setCellValue('C33', $mi_m5_IDTipo_indicador_); 
$objPHPExcel->getActiveSheet()->mergeCells('C33:D33');
$objPHPExcel->getActiveSheet()->setCellValue('C39', $mi_m6_IDTipo_indicador_); 
$objPHPExcel->getActiveSheet()->mergeCells('C39:D39');
$objPHPExcel->getActiveSheet()->setCellValue('C45', $mi_m7_IDTipo_indicador_); 
$objPHPExcel->getActiveSheet()->mergeCells('C45:D45');


//firmas
$objPHPExcel->getActiveSheet()->setCellValue('A53', $nombre); 
$objPHPExcel->getActiveSheet()->mergeCells('A53:C53');
$objPHPExcel->getActiveSheet()->setCellValue('E53', $jefe); 
$objPHPExcel->getActiveSheet()->mergeCells('E53:G53');
$objPHPExcel->getActiveSheet()->setCellValue('B47', $observaciones); 
$objPHPExcel->getActiveSheet()->mergeCells('B47:J47');


//resultados
$objPHPExcel->getActiveSheet()->setCellValue('I8', $mi_m1_resultado_); 
$objPHPExcel->getActiveSheet()->mergeCells('I8:J8');
$objPHPExcel->getActiveSheet()->setCellValue('I14', $mi_m2_resultado_); 
$objPHPExcel->getActiveSheet()->mergeCells('I14:J14');
$objPHPExcel->getActiveSheet()->setCellValue('I20', $mi_m3_resultado_); 
$objPHPExcel->getActiveSheet()->mergeCells('I20:J20');
$objPHPExcel->getActiveSheet()->setCellValue('I26', $mi_m4_resultado_); 
$objPHPExcel->getActiveSheet()->mergeCells('I26:J26');
$objPHPExcel->getActiveSheet()->setCellValue('I32', $mi_m5_resultado_); 
$objPHPExcel->getActiveSheet()->mergeCells('I32:J32');
$objPHPExcel->getActiveSheet()->setCellValue('I38', $mi_m6_resultado_); 
$objPHPExcel->getActiveSheet()->mergeCells('I38:J38');
$objPHPExcel->getActiveSheet()->setCellValue('I44', $mi_m7_resultado_); 
$objPHPExcel->getActiveSheet()->mergeCells('I44:J44');


$objPHPExcel->getActiveSheet()->setCellValue('B47', $coments); 
$objPHPExcel->getActiveSheet()->mergeCells('B47:J47');

//calucar celdas
PHPExcel_Calculation::getInstance($objPHPExcel)->clearCalculationCache();
PHPExcel_Calculation::getInstance()->clearCalculationCache();

//Guardamos el archivo en formato Excel 2007
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setPreCalculateFormulas(FALSE);
$file = $nombre . ' METAS 2020 2DO SEMESTRE.xlsx';
$objWriter->save($file);

//FORZAMOS LA DESCARGA
header('Content-Description: File Transfer'); 
header('Content-Type: application/force-download'); 
header('Content-Length: '.filesize($file)); 
header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
readfile($file); 
exit;
unlink($file);

echo 'Resultado:' . $mi_m1_resultado_prev;?>