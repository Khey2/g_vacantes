<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

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

$currentPage = $_SERVER["PHP_SELF"];
mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDusuario = $row_usuario['IDusuario'];
$el_area = $row_usuario['area_rh'];

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz not in (5,7,10,27)";
mysql_query("SET NAMES 'utf8'");
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

$IDtarea = $_GET['IDtarea'];
//echo "La tarea: ".$IDtarea;

do {
$la_matriz = $row_matrizes['IDmatriz'];
//echo "<br/>La matriz: ".$la_matriz;

$query_meses = "SELECT * FROM ztar_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

do {	
$el_mes = $row_meses['IDmes'];
//echo "<br/>El mes: ".$el_mes;

mysql_select_db($database_vacantes, $vacantes);
$query_tareas = "SELECT ztar_tareas.descripcion_larga, ztar_tareas.por_evento, ztar_tareas.dias_recorrer, ztar_tareas.meses, ztar_tareas.dia, ztar_tareas.matrizes, ztar_tareas.IDtarea,  ztar_tareas.IDarea_rh,  ztar_tareas.descripcion, ztar_tareas.ponderacion,  ztar_tareas.IDperiodicidad,  ztar_areas_rh.area_rh FROM ztar_areas_rh left JOIN ztar_tareas ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh WHERE IDtarea = '$IDtarea' AND FIND_IN_SET($la_matriz, matrizes) AND FIND_IN_SET($el_mes, meses)";
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);

if($totalRows_tareas > 0){ //echo " Debe Existir";

mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT * FROM ztar_avances WHERE IDtarea = '$IDtarea' AND IDmatriz = $la_matriz AND IDmes = $el_mes AND IDver = 0";
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);
if($totalRows_avances > 0){  // echo " Existe";

$IDavance = $row_avances['IDavance'];
$dias_recorrer = $row_tareas['dias_recorrer']; echo $dias_recorrer;
$descripcion = $row_tareas['descripcion_larga'];
$manual = $row_avances['manual'];
if (strlen($el_mes) == 1){ $el_mes_c = '0'.$el_mes;} else { $el_mes_c = $el_mes;}
if (strlen($row_tareas['dia']) == 1){ $el_dia_c = '0'.$row_tareas['dia'];} else { $el_dia_c = $row_tareas['dia']; }
$fecha_base = "2024-".$el_mes_c."-01";
$ultimo_dia = date("Y-m-t", strtotime($fecha_base));
if ($row_tareas['dia'] == 99) { $fecha_esperada = $ultimo_dia; } else { $fecha_esperada = "2024-".$el_mes_c."-".$el_dia_c;} 
//echo $fecha_esperada;

$instrucciones = "Da clic en la imágen para ver los detalles. <br> A continuación captura el resultado solicitado. <br>Si es necesario, carga los documentos de evidencia.";

if($manual == 1){
$updateSQL = sprintf("UPDATE ztar_avances SET dias_recorrer=%s, instrucciones=%s WHERE IDavance='$IDavance'",
                        GetSQLValueString($dias_recorrer, "int"),
                        GetSQLValueString($instrucciones, "text"),
						GetSQLValueString($IDavance, "int"));
} else {
$updateSQL = sprintf("UPDATE ztar_avances SET descripcion=%s, fecha_esperada=%s, dias_recorrer=%s, instrucciones=%s WHERE IDavance='$IDavance'",
						GetSQLValueString($descripcion, "text"),
                        GetSQLValueString($fecha_esperada, "text"),
                        GetSQLValueString($dias_recorrer, "int"),
                        GetSQLValueString($instrucciones, "text"),
						GetSQLValueString($IDavance, "int"));
}

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
} else { // echo " No existe";

$IDestatus = 0;
$descripcion = $row_tareas['descripcion_larga'];
$dias_recorrer = $row_tareas['dias_recorrer'];
if (strlen($el_mes) == 1){ $el_mes_c = '0'.$el_mes;} else { $el_mes_c = $el_mes;}
if (strlen($row_tareas['dia']) == 1) { $el_dia_c = '0'.$row_tareas['dia']; } else { $el_dia_c = $row_tareas['dia']; }
$fecha_base = "2024-".$el_mes_c."-01";
$ultimo_dia = date("Y-m-t", strtotime($fecha_base));
if ($row_tareas['dia'] = 99) { $fecha_esperada = $ultimo_dia; } else { $fecha_esperada = "2024-".$el_mes_c."-".$el_dia_c;}

$instrucciones = "Da clic en la imágen para ver los detalles. <br> A continuación captura el resultado solicitado. <br>Si es necesario, carga los documentos de evidencia.";
$anio = 2024;
$IDmes = $el_mes;
$IDver = 0;
$insertSQL = sprintf("INSERT INTO ztar_avances (IDtarea, IDmatriz, IDestatus, descripcion, dias_recorrer, fecha_esperada, instrucciones, anio, IDmes, IDver) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($IDtarea, "text"),
                       GetSQLValueString($la_matriz, "text"),
                       GetSQLValueString($IDestatus, "text"),
                       GetSQLValueString($descripcion, "text"),
                       GetSQLValueString($dias_recorrer, "text"),
                       GetSQLValueString($fecha_esperada, "text"),
                       GetSQLValueString($instrucciones, "text"),
                       GetSQLValueString($anio, "text"),
                       GetSQLValueString($IDmes, "text"),
                       GetSQLValueString($IDver, "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
}

} else { //echo " No debe existir";

mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT * FROM ztar_avances WHERE IDtarea = '$IDtarea' AND IDmatriz = $la_matriz AND IDmes = $el_mes  AND IDver = 0";
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);

if($totalRows_avances > 0){ echo " Existe";

$query_avance_reportado = "SELECT * FROM ztar_avances WHERE IDtarea = '$IDtarea' AND IDmatriz = $la_matriz AND IDmes = $el_mes AND IDver = 0";
$avance_reportado = mysql_query($query_avance_reportado, $vacantes) or die(mysql_error());
$row_avance_reportado = mysql_fetch_assoc($avance_reportado);
$totalRows_avance_reportado = mysql_num_rows($avance_reportado);
if($row_avance_reportado['IDestatus'] != 0) {  //echo "Ya tiene algo, mejor cambiar a 0 el estatus";

$IDavance = $row_avances['IDavance'];
$updateSQL = "UPDATE ztar_avances SET IDver = 1 WHERE IDavance='$IDavance'";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());


} else { // echo "vacio, borrar";

$IDavance = $row_avances['IDavance'];
$deleteSQL = "DELETE FROM ztar_avances WHERE IDavance ='$IDavance'";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

}

} else { //echo " No existe (mejor) y no se hace nada!!!";
}
}
	
} while ($row_meses = mysql_fetch_assoc($meses)); 
	
} while ($row_matrizes = mysql_fetch_assoc($matrizes)); 

 header('Location: objetivos_d_edita.php?IDtarea='.$IDtarea.'&info=1');

?>