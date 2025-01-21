<?php require_once('Connections/vacantes.php');

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
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$anio_previo = $row_variables['anio']-4;
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
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

$IDempleado = $_GET['IDempleado'];

$query_pcarrera = "SELECT pc_semaforo.*, prod_activos.*, vac_puestos.denominacion AS denominacionPC FROM pc_semaforo left JOIN prod_activos ON pc_semaforo.IDempleado = prod_activos.IDempleado left JOIN vac_puestos ON pc_semaforo.IDpuestoPC = vac_puestos.IDpuesto WHERE prod_activos.IDempleado = $IDempleado"; 
mysql_query("SET NAMES 'utf8'");
$pcarrera = mysql_query($query_pcarrera, $vacantes) or die(mysql_error());
$row_pcarrera = mysql_fetch_assoc($pcarrera);
$totalRows_pcarrera = mysql_num_rows($pcarrera);

$fecha_antiguedad = $row_pcarrera['fecha_antiguedad']; 
$fecha_alta = $row_pcarrera['fecha_alta']; 

$date_a1 = new DateTime(date("Y-m-d")); 
$date_b1 = new DateTime($fecha_antiguedad); 
$diff_c1 = $date_b1->diff($date_a1);
$periodo_d1 =  $diff_c1->m;

$date_a2 = new DateTime(date("Y-m-d"));
$date_b2 = new DateTime($fecha_alta); 
$diff_c2 = $date_a2->diff($date_b2);
$periodo_d2 =  $diff_c2->m; 

//modulo 1 Capacitación
$query_M1a = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 185 AND year(fecha_evento) >= $anio_previo"; 
$M1a = mysql_query($query_M1a, $vacantes) or die(mysql_error()); 
$row_M1a = mysql_fetch_assoc($M1a);
$totalRows_M1a = mysql_num_rows($M1a);

$query_M2a = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 249 AND year(fecha_evento) >= $anio_previo"; 
$M2a = mysql_query($query_M2a, $vacantes) or die(mysql_error());
$row_M2a = mysql_fetch_assoc($M2a);
$totalRows_M2a = mysql_num_rows($M2a);

$query_M3a = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 3 AND year(fecha_evento) >= $anio_previo"; 
$M3a = mysql_query($query_M3a, $vacantes) or die(mysql_error());
$row_M3a = mysql_fetch_assoc($M3a);
$totalRows_M3a = mysql_num_rows($M3a);

$query_M4a = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 4 AND year(fecha_evento) >= $anio_previo"; 
$M4a = mysql_query($query_M4a, $vacantes) or die(mysql_error());
$row_M4a = mysql_fetch_assoc($M4a);
$totalRows_M4a = mysql_num_rows($M4a);

$query_M5a = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 182 AND year(fecha_evento) >= $anio_previo"; 
$M5a = mysql_query($query_M5a, $vacantes) or die(mysql_error());
$row_M5a = mysql_fetch_assoc($M5a);
$totalRows_M5a = mysql_num_rows($M5a);

$query_M6a = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 267 AND year(fecha_evento) >= $anio_previo"; 
$M6a = mysql_query($query_M6a, $vacantes) or die(mysql_error());
$row_M6a = mysql_fetch_assoc($M6a);
$totalRows_M6a = mysql_num_rows($M6a);

$query_M7a = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 62 AND year(fecha_evento) >= $anio_previo"; 
$M7a = mysql_query($query_M7a, $vacantes) or die(mysql_error());
$row_M7a = mysql_fetch_assoc($M7a);
$totalRows_M7a = mysql_num_rows($M7a);


//modulo 2 Capacitación
$query_M1b = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 258 AND year(fecha_evento) >= $anio_previo"; 
$M1b = mysql_query($query_M1b, $vacantes) or die(mysql_error());
$row_M1b = mysql_fetch_assoc($M1b);
$totalRows_M1b = mysql_num_rows($M1b);

$query_M2b = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 257 AND year(fecha_evento) >= $anio_previo"; 
$M2b = mysql_query($query_M2b, $vacantes) or die(mysql_error());
$row_M2b = mysql_fetch_assoc($M2b);
$totalRows_M2b = mysql_num_rows($M2b);

$query_M3b = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 59 AND year(fecha_evento) >= $anio_previo"; 
$M3b = mysql_query($query_M3b, $vacantes) or die(mysql_error());
$row_M3b = mysql_fetch_assoc($M3b);
$totalRows_M3b = mysql_num_rows($M3b);

$query_M4b = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 203 AND year(fecha_evento) >= $anio_previo"; 
$M4b = mysql_query($query_M4b, $vacantes) or die(mysql_error());
$row_M4b = mysql_fetch_assoc($M4b);
$totalRows_M4b = mysql_num_rows($M4b);

$query_M5b = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 58 AND year(fecha_evento) >= $anio_previo"; 
$M5b = mysql_query($query_M5b, $vacantes) or die(mysql_error());
$row_M5b = mysql_fetch_assoc($M5b);
$totalRows_M5b = mysql_num_rows($M5b);

$query_M6b = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 148 AND year(fecha_evento) >= $anio_previo"; 
$M6b = mysql_query($query_M6b, $vacantes) or die(mysql_error());
$row_M6b = mysql_fetch_assoc($M6b);
$totalRows_M6b = mysql_num_rows($M6b);


//modulo 3 Capacitación
$query_M1c = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 250 AND year(fecha_evento) >= $anio_previo"; 
$M1c = mysql_query($query_M1c, $vacantes) or die(mysql_error());
$row_M1c = mysql_fetch_assoc($M1c);
$totalRows_M1c = mysql_num_rows($M1c);

//modulo 4 Capacitación
$query_M1d = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 251 AND year(fecha_evento) >= $anio_previo"; 
$M1d = mysql_query($query_M1d, $vacantes) or die(mysql_error());
$row_M1d = mysql_fetch_assoc($M1d);
$totalRows_M1d = mysql_num_rows($M1d);

//modulo 5 Capacitación
$query_M1e = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 252 AND year(fecha_evento) >= $anio_previo"; 
$M1e = mysql_query($query_M1e, $vacantes) or die(mysql_error());
$row_M1e = mysql_fetch_assoc($M1e);
$totalRows_M1e = mysql_num_rows($M1e);

//modulo 6 Capacitación
$query_M1f = "SELECT * FROM capa_avance  WHERE IDempleado = $IDempleado AND IDC_capa_cursos = 253 AND year(fecha_evento) >= $anio_previo"; 
$M1f = mysql_query($query_M1f, $vacantes) or die(mysql_error());
$row_M1f = mysql_fetch_assoc($M1f);
$totalRows_M1f = mysql_num_rows($M1f);


$total_modulo1 = 0;
if ($totalRows_M1a + $totalRows_M2a + $totalRows_M3a + $totalRows_M4a + $totalRows_M5a + $totalRows_M6a + $totalRows_M7a >= 7) {$total_modulo1 = 1;}

$total_modulo2 = 0;
if ($totalRows_M1b + $totalRows_M2b + $totalRows_M3b + $totalRows_M4b + $totalRows_M5b + $totalRows_M6b >= 6) {$total_modulo2 = 1;}

$total_modulo3 = 0;
if ($totalRows_M1c > 0) {$total_modulo3 = 1;}

$total_modulo4 = 0;
if ($totalRows_M1d > 0) {$total_modulo4 = 1;}

$total_modulo5 = 0;
if ($totalRows_M1e > 0) {$total_modulo5 = 1;}

$total_modulo6 = 0;
if ($totalRows_M1f > 0) {$total_modulo6 = 1;}

//asesorias mejora
$query_L1a = "SELECT * FROM rel_lab_asesorias  WHERE IDempleado = $IDempleado AND fecha_captura < DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 6 MONTH)"; 
$L1a = mysql_query($query_L1a, $vacantes) or die(mysql_error());
$row_L1a = mysql_fetch_assoc($L1a);
$totalRows_L1a = mysql_num_rows($L1a);

//asistencia
$query_L1b = "SELECT ind_asistencia_tipos.tipo, ind_asistencia.* FROM ind_asistencia LEFT JOIN ind_asistencia_tipos ON ind_asistencia.IDtipo = ind_asistencia_tipos.IDtipo  WHERE IDempleado = $IDempleado AND ind_asistencia_tipos.tipo = 101 AND fecha_captura < DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 3 MONTH)"; 
$L1b = mysql_query($query_L1b, $vacantes) or die(mysql_error());
$row_L1b = mysql_fetch_assoc($L1b);
$totalRows_L1b = mysql_num_rows($L1b);


//modulo 7 JO
$query_M1g = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado AND IDevaluador = 1 AND IDciclo = 1"; 
$M1g = mysql_query($query_M1g, $vacantes) or die(mysql_error());
$row_M1g = mysql_fetch_assoc($M1g);
$totalRows_M1g = mysql_num_rows($M1g);

$query_M2g = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado AND IDevaluador = 1 AND IDciclo = 2"; 
$M2g = mysql_query($query_M2g, $vacantes) or die(mysql_error());
$row_M2g = mysql_fetch_assoc($M2g);
$totalRows_M2g = mysql_num_rows($M2g);

$query_M3g = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado AND IDevaluador = 1 AND IDciclo = 3"; 
$M3g = mysql_query($query_M3g, $vacantes) or die(mysql_error());
$row_M3g = mysql_fetch_assoc($M3g);
$totalRows_M3g = mysql_num_rows($M3g);



//modulo 7 SUP
$query_M1h = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado AND IDevaluador = 2 AND IDciclo = 1"; 
$M1h = mysql_query($query_M1h, $vacantes) or die(mysql_error());
$row_M1h = mysql_fetch_assoc($M1h);
$totalRows_M1h = mysql_num_rows($M1h);

//modulo 7 SUP
$query_M2h = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado AND IDevaluador = 2 AND IDciclo = 2"; 
$M2h = mysql_query($query_M2h, $vacantes) or die(mysql_error());
$row_M2h = mysql_fetch_assoc($M2h);
$totalRows_M2h = mysql_num_rows($M2h);

//modulo 7 SUP
$query_M3h = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado AND IDevaluador = 2 AND IDciclo = 3"; 
$M3h = mysql_query($query_M3h, $vacantes) or die(mysql_error());
$row_M3h = mysql_fetch_assoc($M3h);
$totalRows_M3h = mysql_num_rows($M3h);



//modulo 7 OP
$query_M1i = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado  AND IDevaluador = 3 AND IDciclo = 1"; 
$M1i = mysql_query($query_M1i, $vacantes) or die(mysql_error());
$row_M1i = mysql_fetch_assoc($M1i);
$totalRows_M1i = mysql_num_rows($M1i);

$query_M2i = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado  AND IDevaluador = 3 AND IDciclo = 2"; 
$M2i = mysql_query($query_M2i, $vacantes) or die(mysql_error());
$row_M2i = mysql_fetch_assoc($M2i);
$totalRows_M2i = mysql_num_rows($M2i);

$query_M3i = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado  AND IDevaluador = 3 AND IDciclo = 3"; 
$M3i = mysql_query($query_M3i, $vacantes) or die(mysql_error());
$row_M3i = mysql_fetch_assoc($M3i);
$totalRows_M3i = mysql_num_rows($M3i);


//modulo 7 OP
$query_M1ix = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado  AND IDevaluador = 1 AND viable = 1"; 
$M1ix = mysql_query($query_M1ix, $vacantes) or die(mysql_error());
$row_M1ix = mysql_fetch_assoc($M1ix);
$totalRows_M1ix = mysql_num_rows($M1ix);

$query_M2ix = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado  AND IDevaluador = 2 AND  viable = 1"; 
$M2ix = mysql_query($query_M2ix, $vacantes) or die(mysql_error());
$row_M2ix = mysql_fetch_assoc($M2ix);
$totalRows_M2ix = mysql_num_rows($M2ix);

$query_M3ix = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado  AND IDevaluador = 3 AND  viable = 1"; 
$M3ix = mysql_query($query_M3ix, $vacantes) or die(mysql_error());
$row_M3ix = mysql_fetch_assoc($M3ix);
$totalRows_M3ix = mysql_num_rows($M3ix);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	$IDempleado = $_POST["IDempleado"]; 
	$estatus_pc = $_POST["estatus_pc"]; 
	$query1 = "UPDATE pc_semaforo SET estatus_pc = '$estatus_pc' WHERE IDempleado = $IDempleado"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: plan_carrera_tablero_edit.php?IDempleado=$IDempleado&info=3"); 	
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$IDusuario_carpeta = 'filesPC/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: plan_carrera_tablero_edit.php?info=2&IDempleado='.$IDempleado);
exit;
} 

$IDevaluador = $_POST['IDevaluador'];	
$IDciclo  = $_POST['IDciclo'];	
$name_new = $IDempleado."_".$IDevaluador."_".$IDciclo.".".$extension;
$targetPath = 'filesPC/'.$IDempleado."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$insertSQL = sprintf("INSERT INTO pc_modulovii (IDempleado, IDevaluador, IDciclo, fecha_carga, viable, observaciones, file) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDevaluador'], "int"),
                       GetSQLValueString($_POST['IDciclo'], "int"),
                       GetSQLValueString($fecha, "text"),
                       GetSQLValueString($_POST['viable'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($name_new, "text"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header('Location: plan_carrera_tablero_edit.php?info=1&IDempleado='.$IDempleado);
}
 
 
 if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$IDusuario_carpeta = 'filesPC/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
	header('Location: plan_carrera_tablero_edit.php?info=2&IDempleado='.$IDempleado);
	exit;
} 

$IDevaluador = $_POST['IDevaluador'];	
$IDciclo  = $_POST['IDciclo'];	
$name_new = $IDempleado."_".$IDevaluador."_".$IDciclo.".".$extension;
$targetPath = 'filesPC/'.$IDempleado."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$updateSQL = sprintf("UPDATE pc_modulovii SET file=%s, fecha_carga=%s, viable=%s, observaciones=%s WHERE IDempleado = $IDempleado AND IDevaluador= $IDevaluador AND IDciclo = $IDciclo",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($fecha, "text"),
                       GetSQLValueString($_POST['viable'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header('Location: plan_carrera_tablero_edit.php?info=1&IDempleado='.$IDempleado);
 }

 // borrar alternativo
 if ((isset($_GET['disciplina'])) && ($_GET['disciplina'] == 1)) {
  
	$borrado = $_GET['IDfile'];
	$IDempleado = $_GET['IDempleado'];
	$deleteSQL = "UPDATE pc_semaforo SET a_discprog = 1 WHERE IDempleado ='$IDempleado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header('Location: plan_carrera_tablero_edit.php?info=4&IDempleado='.$IDempleado);
}
  
 // borrar alternativo
 if ((isset($_GET['antiguedad'])) && ($_GET['antiguedad'] == 1)) {
  
	$borrado = $_GET['IDfile'];
	$IDempleado = $_GET['IDempleado'];
	$deleteSQL = "UPDATE pc_semaforo SET a_antig = 1 WHERE IDempleado ='$IDempleado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header('Location: plan_carrera_tablero_edit.php?info=4&IDempleado='.$IDempleado);
}
  
 // borrar alternativo 2
 if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
	$IDavance = $_GET['IDavance'];
	$IDempleado = $_GET['IDempleado'];
	$deleteSQL = "DELETE FROM pc_modulovii WHERE IDavance ='$IDavance'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header('Location: plan_carrera_tablero_edit.php?info=5&IDempleado='.$IDempleado);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<!-- /theme JS files -->

</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/pheader.php'); ?>
			<!-- Content area -->
				<div class="content">

	                <!-- Content area -->
				<div class="content">
                
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 1) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente el archivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 3) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se cambió correctamente el estatus.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 4) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se cambió correctamente el requisito de antiguedad.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 2) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Archivo no permitido.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 5) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el archivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
					<!-- Timeline -->
					<div class="timeline timeline-left">
						<div class="timeline-container">

							<!-- Sales stats -->
							<div class="timeline-row">
								<div class="timeline-icon">
									<div class="bg-primary-400">
										<i class="icon-user"></i>
									</div>
								</div>

								<div class="panel panel-flat timeline-content">

									<div class="panel-body">
										<div>

										<div class="col-sm-4">
										<div class="panel-heading"><h6 class="panel-title">Datos del Empleado</h6></div>
										<b>No. Emp:</b> <?php echo $row_pcarrera['IDempleado']; ?><br/>
										<b>Nombre:</b> <?php echo $row_pcarrera['emp_paterno']." ".$row_pcarrera['emp_materno']." ".$row_pcarrera['emp_nombre']; ?><br/>
										<b>Fecha antiguedad:</b> <?php echo date( 'd/m/Y' , strtotime( $row_pcarrera['fecha_antiguedad'])); ?><br/>
										<b>Puesto actual:</b> <?php echo $row_pcarrera['denominacion']; ?><br/>
										<b>Puesto a promover:</b> <?php echo $row_pcarrera['denominacionPC']; ?><br/>
										</div>

										<div class="col-sm-4">
										<div class="panel-heading"><h6 class="panel-title">Licencia</h6></div>
										<b>Tipo:</b> <?php
										 if ($row_pcarrera['reqd'] == 1) {echo "Particular A";} 
										 if ($row_pcarrera['reqd'] == 2) {echo "Local B";} 
										 if ($row_pcarrera['reqd'] == 3) {echo "Local c";} 
										 if ($row_pcarrera['reqd'] == 5) {echo "Federal B";} 
										 if ($row_pcarrera['reqd'] == 6) {echo "Federal C";} 
										?><br/>
										<b>Fecha expedición:</b> <?php echo $row_pcarrera['fecha_licencia']; ?><br/>
										<a class="btn btn-primary btn-xs" href="plan_carrera_inv.php?licencia=1&IDempleado=<?php echo $IDempleado; ?>">Editar</a>

										</div>
										<div class="col-sm-4">
										<div class="panel-heading"><h6 class="panel-title">Estatus</h6></div>

										<b>Estatus Actual:</b> <?php if ($row_pcarrera['estatus_pc'] == 0) { echo "En proceso";} else { echo "Promovido";} ?><br/><br/>

										<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-xs btn-success">Cambiar Estatus</button>
										<button type="button" data-target="#modal_theme_danger2"  data-toggle="modal" class="btn btn-xs btn-danger">Omitir Antiguedad</button>
										<button type="button" data-target="#modal_theme_danger3"  data-toggle="modal" class="btn btn-xs btn-danger">Omitir D.Prog.</button>
										&nbsp;&nbsp;&nbsp;<a class="btn btn-default btn-xs" href="plan_carrera_tablero.php">Regresar</a>

										</div>
												
					<!-- danger modal -->
					<div id="modal_theme_danger2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Antiguedad</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres omitir el requisito de antiguedad?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="plan_carrera_tablero_edit.php?IDempleado=<?php echo $IDempleado; ?>&antiguedad=1">Si omitir</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

					<!-- danger modal -->
					<div id="modal_theme_danger3" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Disciplina Progresiva</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres omitir el requisito de Disciplina Progresiva?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="plan_carrera_tablero_edit.php?IDempleado=<?php echo $IDempleado; ?>&disciplina=1">Si omitir</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->



										<!-- danger modal -->
									<div id="modal_theme_danger" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cambiar estatus</h6>
												</div>
												<div class="modal-body">																		
														<form action="plan_carrera_tablero_edit.php?IDempleado=<?php echo $IDempleado; ?>" method="post" name="estatus" id="estatus" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														  <div class="form-group">
															  <label class="control-label col-lg-3">Estatus:</label>
															  <div class="col-lg-9">
															<select name="estatus_pc" id="estatus_pc" class="form-control" >
																<option value="0"<?php if (!(strcmp($row_pcarrera['estatus_pc'], 0))) {echo "selected=\"selected\"";} ?>>En proceso</option>
																<option value="1"<?php if (!(strcmp($row_pcarrera['estatus_pc'], 1))) {echo "selected=\"selected\"";} ?>>Promovido</option>
															</select>
															 </div>
														  </div>
														 </fieldset>
														<div>
														</div>		
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-success">Cambiar Estatus</button> 
													<input type="hidden" name="MM_insert" value="form2" />
													<input type="hidden" name="IDempleado" value="<?php echo $row_pcarrera['IDempleado']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->
										
										</div>
									</div>
								</div>
							</div>
							<!-- /sales stats -->


							<!-- Sales stats -->
							<div class="timeline-row">
								<div class="timeline-icon">
									<div class="bg-primary-400">
										<i class="icon-file-text3"></i>
									</div>
								</div>

								<div class="panel panel-flat timeline-content">
									<div class="panel-heading">
										<h6 class="panel-title">Requisitos de Política</h6>
									</div>

									<div class="panel-body">
									<b>Requisitos de Política.</b> No contar con disciplina progresiva en los últimos 6 meses; no presentar más de tres faltas en un periodo de 3 meses; contar con al menos dos meses continuos de buen desempeño según indicadores de productividad y contar con al menos seis meses de antigüedad en la empresa y tres meses en la posición actual. Se genera con base en la información capturada en las secciones de Productividad, Asistencia, Disciplina Progresiva y Plantilla por parte de Operaciones y JRH.<br/><br/>
									
									<div class="panel-group panel-group-control content-group-lg" id="accordion-control">
									<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion-control" href="#accordion-control-group1">Disciplina Progresiva</a>
										<?php if ($totalRows_L1a == 0  OR $row_pcarrera['a_discprog'] == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control-group1" class="panel-collapse collapse in">
										<div class="panel-body">
										No contar con ningún proceso de disciplina progresiva en el ultimo semestre.


											<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>ID</th>
														<th>Fecha</th>
														<th>Estatus</th>
														<th>Acciones</th>
													</tr>
												</thead>
												<tbody>
												<?php if($totalRows_L1a > 0) { do { ?>
													<tr>
														<td><?php echo $row_L1a['IDasesoria']?></td>
														<td><?php echo date( 'd/m/Y' , strtotime($row_L1a['fecha_captura'])); ?></td>
														<td><?php if ($row_L1a['IDestatus'] == 1) {echo "Activa";} else { echo "Cerrada";} ?></td>
														<td><a href="rel_lab_detalle.php?IDempleado=<?php echo $IDempleado ?>" class="btn btn-xs btn-info">Ver</a></td>
													</tr>
												<?php } while ($row_L1a = mysql_fetch_assoc($L1a));  } else { ?>
														<tr>
														<td colspan="4">No tiene asesorias registradas.</td>
														</tr>
												<?php } ?>
												</tbody>
											</table>
											</div>


										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control" href="#accordion-control-group2">Puntualidad y asistencia</a>
											<?php if ($totalRows_L1b == 0) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control-group2" class="panel-collapse collapse">
										<div class="panel-body">
										No presentar más de tres faltas en un periodo de un trimestre.


										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Fecha</th>
														<th>Motivo</th>
														<th>Validado</th>
													</tr>
												</thead>
												<tbody>
												<?php if($totalRows_L1b > 0) { do { ?>
													<tr>
														<td><?php echo date( 'd/m/Y' , strtotime($row_L1b['fecha_captura'])); ?></td>
														<td><?php echo $row_L1b['tipo'] ?></td>
														<td><?php if ($row_L1b['IDtipov'] != '') {echo "Si";} else { echo "No";} ?></td>
													</tr>
												<?php } while ($row_L1b = mysql_fetch_assoc($L1b));  } else { ?>
														<tr>
														<td colspan="4">No tiene faltas registradas.</td>
														</tr>
												<?php } ?>
												</tbody>
											</table>
											</div>





										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control" href="#accordion-control-group3">Desempeño</a>
											<span class="label label-success pull-right">Si cumple</span>
										</h6>
									</div>
									<div id="accordion-control-group3" class="panel-collapse collapse">
										<div class="panel-body">
										Contar con al menos dos meses continuos de buen desempeño según indicadores de productividad.
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a  class="collapsed" data-toggle="collapse" data-parent="#accordion-control" href="#accordion-control-group4">Antigüedad</a>
											<?php if (($periodo_d1 >= 6 AND $periodo_d2 >= 3) OR $row_pcarrera['a_antig'] == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control-group4" class="panel-collapse collapse">
										<div class="panel-body">
										Contar con al menos seis meses de antigüedad en la empresa y tres meses en la posición actual.<br/>

										<b>Antiguedad en la empresa:</b>  <?php echo $periodo_d1." meses"; ?>.<br/>
										<b>Antiguedad en el puesto:</b>  <?php echo $periodo_d2." meses"; ?>. <br/>
										<?php if ($row_pcarrera['a_antig'] == 1) { ?><span class="text text-semibold text-success">Requisito Omitido</span><br/><?php }  ?>

										</div>
									</div>
								</div>
							</div>

									</div>
								</div>
							</div>
							<!-- /sales stats -->


							<!-- Sales stats -->
							<div class="timeline-row">
								<div class="timeline-icon">
								<div class="bg-primary-400">
										<i class="icon-bubbles6"></i>
									</div>
								</div>

								<div class="panel panel-flat timeline-content">
									<div class="panel-heading">
										<h6 class="panel-title">Capacitación I a VI</h6>
									</div>

									<div class="panel-body">
									<b>Capacitación Módulos del I al VI.</b> Describe el avance en el cumplimiento de los cursos de capacitación y de manejo práctico, tanto presenciales, como virtuales necesarios en el Plan de Carrera. Los cursos se reportan por JRH y el área de capacitación a través del reporte de avance mensual. La vigencia del curso es desde enero del año anterior.<br/><br/>

									<div class="panel-group panel-group-control content-group-lg" id="accordion-control2">
									<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion-control2" href="#accordion-control2-group11">Módulo I</a>
										<?php if ($total_modulo1 == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control2-group11" class="panel-collapse collapse in">
										<div class="panel-body">
										Los cursos se reportan por el Jefe de Recursos Humanos, a través del reporte de avance mensual.

											<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Curso</th>
														<th>Tema</th>
														<th>Estatus</th>
														<th>Fecha</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Curso de Inducción</td>
														<td>Induccion institucional y a la empresa</td>
														<td><?php if ($totalRows_M1a > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1a > 0) {echo date( 'd/m/Y' , strtotime($row_M1a['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Curso de Inducción</td>
														<td>Diversidad e inclusión</td>
														<td><?php if ($totalRows_M2a > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M2a > 0) {echo date( 'd/m/Y' , strtotime($row_M2a['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Curso de Inducción</td>
														<td>Inducción al puesto</td>
														<td><?php if ($totalRows_M3a > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M3a > 0) {echo date( 'd/m/Y' , strtotime($row_M3a['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Curso de Inducción</td>
														<td>Entrenamiento al puesto</td>
														<td><?php if ($totalRows_M4a > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M4a > 0) {echo date( 'd/m/Y' , strtotime($row_M4a['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Curso de Inducción</td>
														<td>Retroalimentación SAB</td>
														<td><?php if ($totalRows_M5a > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M5a > 0) {echo date( 'd/m/Y' , strtotime($row_M5a['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Curso de Inducción</td>
														<td>Normatividad Interna</td>
														<td><?php if ($totalRows_M6a > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M6a > 0) {echo date( 'd/m/Y' , strtotime($row_M6a['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Curso de Inducción</td>
														<td>Integridad Sahuayo</td>
														<td><?php if ($totalRows_M7a > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M7a > 0) {echo date( 'd/m/Y' , strtotime($row_M7a['fecha_evento']));} else { echo "-";}?></td>
													</tr>
												</tbody>
											</table>
											</div>


										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control2" href="#accordion-control2-group12">Módulo II</a>
											<?php if ($total_modulo2 == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control2-group12" class="panel-collapse collapse">
										<div class="panel-body">


										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Curso</th>
														<th>Tema</th>
														<th>Estatus</th>
														<th>Fecha</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Crecimiento al puesto </td>
														<td>Entrenamiento al Puesto Promoción</td>
														<td><?php if ($totalRows_M1b > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1b > 0) {echo date( 'd/m/Y' , strtotime($row_M1b['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Crecimiento al puesto </td>
														<td>Induccion al puesto/Plan de Carrera</td>
														<td><?php if ($totalRows_M2b > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M2b > 0) {echo date( 'd/m/Y' , strtotime($row_M2b['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Crecimiento al puesto </td>
														<td>Buenas prácticas de reparto</td>
														<td><?php if ($totalRows_M3b > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M3b > 0) {echo date( 'd/m/Y' , strtotime($row_M3b['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Crecimiento al puesto </td>
														<td>Autoridades Federales Operativo</td>
														<td><?php if ($totalRows_M4b > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M4b > 0) {echo date( 'd/m/Y' , strtotime($row_M4b['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Crecimiento al puesto </td>
														<td>Operador experto</td>
														<td><?php if ($totalRows_M5b > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M5b > 0) {echo date( 'd/m/Y' , strtotime($row_M5b['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Crecimiento al puesto </td>
														<td>Visita dentro de rango app</td>
														<td><?php if ($totalRows_M6b > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M6b > 0) {echo date( 'd/m/Y' , strtotime($row_M6b['fecha_evento']));} else { echo "-";}?></td>
													</tr>
												</tbody>
											</table>
											</div>



										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control2" href="#accordion-control2-group13">Módulo III</a>
											<?php if ($total_modulo3 == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control2-group13" class="panel-collapse collapse">
										<div class="panel-body">


										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Curso</th>
														<th>Tema</th>
														<th>Estatus</th>
														<th>Fecha</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Normatividad en carretera </td>
														<td>Alcoholímetro</td>
														<td><?php if ($totalRows_M1c > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1c > 0) {echo date( 'd/m/Y' , strtotime($row_M1c['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Normatividad en carretera </td>
														<td>Licencias obtención y renovación</td>
														<td><?php if ($totalRows_M1c > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1c > 0) {echo date( 'd/m/Y' , strtotime($row_M1c['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Normatividad en carretera </td>
														<td>Control Combustible</td>
														<td><?php if ($totalRows_M1c > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1c > 0) {echo date( 'd/m/Y' , strtotime($row_M1c['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Normatividad en carretera </td>
														<td>Reclamo de siniestros unidades de reparto</td>
														<td><?php if ($totalRows_M1c > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1c > 0) {echo date( 'd/m/Y' , strtotime($row_M1c['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Normatividad en carretera </td>
														<td>Comprobación de gastos</td>
														<td><?php if ($totalRows_M1c > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1c > 0) {echo date( 'd/m/Y' , strtotime($row_M1c['fecha_evento']));} else { echo "-";}?></td>
													</tr>
												</tbody>
											</table>
											</div>


									</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control2" href="#accordion-control2-group14">Módulo IV</a>
											<?php if ($total_modulo4 == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control2-group14" class="panel-collapse collapse">
										<div class="panel-body">
										Contar con al menos seis meses de antigüedad en la empresa y tres meses en la posición actual.

										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Curso</th>
														<th>Tema</th>
														<th>Estatus</th>
														<th>Fecha</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Reglamento de tránsito </td>
														<td>Reglamento de tránsito </td>
														<td><?php if ($totalRows_M1d > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1d > 0) {echo date( 'd/m/Y' , strtotime($row_M1d['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Reglamento de tránsito </td>
														<td>Regulaciones básicas al conducir</td>
														<td><?php if ($totalRows_M1d > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1d > 0) {echo date( 'd/m/Y' , strtotime($row_M1d['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Reglamento de tránsito </td>
														<td>Documentos oficiales durante la ruta</td>
														<td><?php if ($totalRows_M1d > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1d > 0) {echo date( 'd/m/Y' , strtotime($row_M1d['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Reglamento de tránsito </td>
														<td>NOM 012 SCT2 2017 Peso y dimensiones</td>
														<td><?php if ($totalRows_M1d > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1d > 0) {echo date( 'd/m/Y' , strtotime($row_M1d['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Reglamento de tránsito </td>
														<td>NOM 087 SCT 2 2017 Bitácora de horas de manejo y descanso</td>
														<td><?php if ($totalRows_M1d > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1d > 0) {echo date( 'd/m/Y' , strtotime($row_M1d['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Reglamento de tránsito </td>
														<td>Sanciones</td>
														<td><?php if ($totalRows_M1d > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1d > 0) {echo date( 'd/m/Y' , strtotime($row_M1d['fecha_evento']));} else { echo "-";}?></td>
													</tr>
												</tbody>
											</table>
											</div>



										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control2" href="#accordion-control2-group15">Módulo V</a>
											<?php if ($total_modulo5 == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control2-group15" class="panel-collapse collapse">
										<div class="panel-body">


										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Curso</th>
														<th>Tema</th>
														<th>Estatus</th>
														<th>Fecha</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 1. Tecnología vehícular/partes automotrices</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 1. Mantenimiento</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 1. Check list y documentación</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 1. Neúmaticos</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 2. Conocimiento y funcinamiento de la unidad</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 2. Conducción técnico - económico</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 2. Principios y reglas al conducir</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Aprendiendo a manejar </td>
														<td>Parte 2. Aspectos prácticos de la conducción eficiente</td>
														<td><?php if ($totalRows_M1e > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1e > 0) {echo date( 'd/m/Y' , strtotime($row_M1e['fecha_evento']));} else { echo "-";}?></td>
													</tr>
												</tbody>
											</table>
											</div>


										</div>
									</div>
								</div>


								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control2" href="#accordion-control2-group16">Módulo VI</a>
											<?php if ($total_modulo6 == 1) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control2-group16" class="panel-collapse collapse">
										<div class="panel-body">



										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Curso</th>
														<th>Tema</th>
														<th>Estatus</th>
														<th>Fecha</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Clases de manejo práctico </td>
														<td>Clase 1. Partes automotrices externas</td>
														<td><?php if ($totalRows_M1f > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1f > 0) {echo date( 'd/m/Y' , strtotime($row_M1f['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Clases de manejo práctico </td>
														<td>Clase 2. Interior de cabina</td>
														<td><?php if ($totalRows_M1f > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1f > 0) {echo date( 'd/m/Y' , strtotime($row_M1f['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Clases de manejo práctico </td>
														<td>Clase 3. Prueba de manejo práctico</td>
														<td><?php if ($totalRows_M1f > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1f > 0) {echo date( 'd/m/Y' , strtotime($row_M1f['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Clases de manejo práctico </td>
														<td>Clase 4. Ruta en campo/alrededores</td>
														<td><?php if ($totalRows_M1f > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1f > 0) {echo date( 'd/m/Y' , strtotime($row_M1f['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Clases de manejo práctico </td>
														<td>Clase 5. Ruta en campo/autopista</td>
														<td><?php if ($totalRows_M1f > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1f > 0) {echo date( 'd/m/Y' , strtotime($row_M1f['fecha_evento']));} else { echo "-";}?></td>
													</tr>
													<tr>
														<td>Clases de manejo práctico </td>
														<td>Clase 6. Ruta en campo/carretera</td>
														<td><?php if ($totalRows_M1f > 0) {echo "<span class='text text-success'>Cursado</span>";} else { echo "<span class='text text-danger'>No Cursado</span>";} ?></td>
														<td><?php if ($totalRows_M1f > 0) {echo date( 'd/m/Y' , strtotime($row_M1f['fecha_evento']));} else { echo "-";}?></td>
													</tr>
												</tbody>
											</table>
											</div>

										</div>
									</div>
								</div>


							</div>

									</div>
								</div>
							</div>
							<!-- /sales stats -->


							<!-- Sales stats -->
							<div class="timeline-row">
								<div class="timeline-icon">
									<div class="bg-primary-400">
										<i class="icon-bubble7"></i>
									</div>
								</div>

								<div class="panel panel-flat timeline-content">
									<div class="panel-heading">
										<h6 class="panel-title">Capacitación VII</h6>
									</div>

									<div class="panel-body">
									<b>Capacitación Módulo VII.</b> Indican el cumplimiento de la última fase de capacitación y empoderamiento por parte del área de Operaciones necesarios para asegurar que el empleado domine las funciones del puesto, previo a la promoción definitiva. Se reporta directamente en el sistema por parte del JRH, con base en las evaluaciones del Jefe de Operaciones, Supervisor de Tráfico y Operador a cargo. Las evaluaciones deberán ser quincenales.<br/><br/>
									<div class="panel-group panel-group-control content-group-lg" id="accordion-control3">
									<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
										<a  data-toggle="collapse" data-parent="#accordion-control3" href="#accordion-control3-group21">Evaluación Jefe de Operaciones</a>
										<?php if ($totalRows_M1ix > 0) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control3-group21" class="panel-collapse collapse in">
										<div class="panel-body">

										<p>Da clic aqui para <a href="filesPC/FormatoJO.pdf" target="_blank" class="text text-semibold text-danger">descargar el formato.</a></p>

										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Tipo</th>
														<th>Avance</th>
														<th>Fecha</th>
														<th>Estatus</th>
														<th>Archivo</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Jefe de Operaciones</td>
														<td>Evaluación 1</td>
														<td><?php if ($totalRows_M1g > 0) {echo date( 'd/m/Y', strtotime( $row_M1g['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M1g > 0 AND $row_M1g['viable'] == 1) {echo "Apto";} else if ($totalRows_M1g > 0 AND $row_M1g['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M1g > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M1g['file']; ?>" class="btn btn-xs btn-info">Descargar</a><div onClick="loadDynamicContentModal2(<?php echo $row_M1g['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(1,1,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php if ($totalRows_M1g > 0 AND $row_M1g['viable'] != 1) { ?>
													<tr>
														<td>Jefe de Operaciones</td>
														<td>Evaluación 2</td>
														<td><?php if ($totalRows_M2g > 0) {echo date( 'd/m/Y', strtotime( $row_M2g['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M2g > 0 AND $row_M2g['viable'] == 1) {echo "Apto";} else if ($totalRows_M2g > 0 AND $row_M2g['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M2g > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M2g['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M2g['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(1,2,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php } if ($totalRows_M2g > 0 AND $row_M2g['viable'] != 1) { ?>
													<tr>
														<td>Jefe de Operaciones</td>
														<td>Evaluación 3</td>
														<td><?php if ($totalRows_M3g > 0) {echo date( 'd/m/Y', strtotime( $row_M3g['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M3g > 0 AND $row_M3g['viable'] == 1) {echo "Apto";} else if ($totalRows_M3g > 0 AND $row_M3g['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M3g > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M3g['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M3g['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(1,3,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php }  if ($totalRows_M3g > 0 AND $row_M3g['viable'] != 1) { ?>
													<tr>
														<td>Jefe de Operaciones</td>
														<td>Evaluación 4</td>
														<td><?php if ($totalRows_M4g > 0) {echo date( 'd/m/Y', strtotime( $row_M4g['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M4g > 0 AND $row_M3g['viable'] == 1) {echo "Apto";} else if ($totalRows_M4g > 0 AND $row_M4g['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M4g > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M4g['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M4g['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(1,4,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php }  ?>
												</tbody>
											</table>
											</div>



									</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading" >
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control3" href="#accordion-control3-group22">Evaluación Supervisor</a>
											<?php if ($totalRows_M2ix > 0) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control3-group22" class="panel-collapse collapse">
										<div class="panel-body">

										<p>Da clic aqui para <a href="filesPC/FormatoST.pdf"  target="_blank" class="text text-semibold text-danger">descargar el formato.</a></p>

										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Tipo</th>
														<th>Avance</th>
														<th>Fecha</th>
														<th>Estatus</th>
														<th>Archivo</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Supervisor de Tráfico</td>
														<td>Evaluación 1</td>
														<td><?php if ($totalRows_M1h > 0) {echo date( 'd/m/Y', strtotime( $row_M1h['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M1h > 0 AND $row_M1h['viable'] == 1) {echo "Apto";} else if ($totalRows_M1h > 0 AND $row_M1h['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M1h > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M1h['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M1h['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(2,1,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php if ($totalRows_M1h > 0 AND $row_M1h['viable'] != 1) { ?>
													<tr>
														<td>Supervisor de Tráfico</td>
														<td>Evaluación 2</td>
														<td><?php if ($totalRows_M2h > 0) {echo date( 'd/m/Y', strtotime( $row_M2h['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M2h > 0 AND $row_M2h['viable'] == 1) {echo "Apto";} else if ($totalRows_M2h > 0 AND $row_M2h['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M2h > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M2h['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M2h['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(2,2,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php } if ($totalRows_M2h > 0 AND $row_M2h['viable'] != 1) { ?>
													<tr>
														<td>Supervisor de Tráfico</td>
														<td>Evaluación 3</td>
														<td><?php if ($totalRows_M3h > 0) {echo date( 'd/m/Y', strtotime( $row_M3h['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M3h > 0 AND $row_M3h['viable'] == 1) {echo "Apto";} else if ($totalRows_M3h > 0 AND $row_M3h['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M3h > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M3h['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M3h['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(2,3,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php } if ($totalRows_M3h > 0 AND $row_M3h['viable'] != 1) { ?>
													<tr>
														<td>Supervisor de Tráfico</td>
														<td>Evaluación 4</td>
														<td><?php if ($totalRows_M4h > 0) {echo date( 'd/m/Y', strtotime( $row_M4h['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M4h > 0 AND $row_M3h['viable'] == 1) {echo "Apto";} else if ($totalRows_M4h > 0 AND $row_M4h['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M4h > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M4h['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M4h['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(2,4,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php }  ?>
												</tbody>
											</table>
											</div>


										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" data-parent="#accordion-control3" href="#accordion-control3-group23">Evaluación Operador</a>
											<?php if ($totalRows_M3ix > 0) { ?><span class="label label-success pull-right">Si cumple</span><?php } else { ?><span class="label label-danger pull-right">No cumple</span><?php } ?>
										</h6>
									</div>
									<div id="accordion-control3-group23" class="panel-collapse collapse">
										<div class="panel-body">

										<p>Da clic aqui para <a href="filesPC/FormatoOP.pdf" target="_blank" class="text text-semibold text-danger">descargar el formato.</a></p>

										<div class="table-responsive">
											<table class="table table-striped">
												<thead>
													<tr>
														<th>Tipo</th>
														<th>Avance</th>
														<th>Fecha</th>
														<th>Estatus</th>
														<th>Archivo</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Operador</td>
														<td>Evaluación 1</td>
														<td><?php if ($totalRows_M1i > 0) {echo date( 'd/m/Y', strtotime( $row_M1i['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M1i > 0 AND $row_M1i['viable'] == 1) {echo "Apto";} else if ($totalRows_M1i > 0 AND $row_M1i['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M1i > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M1i['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M1i['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?><div onClick="loadDynamicContentModal(3,1,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php if ($totalRows_M1i > 0 AND $row_M1i['viable'] != 1) { ?>
													<tr>
														<td>Operador</td>
														<td>Evaluación 2</td>
														<td><?php if ($totalRows_M2i > 0) {echo date( 'd/m/Y', strtotime( $row_M2i['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M2i > 0 AND $row_M2i['viable'] == 1) {echo "Apto";} else if ($totalRows_M2i > 0 AND $row_M2i['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M2i > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M2i['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M2i['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(3,2,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php } if ($totalRows_M2i > 0 AND $row_M2i['viable'] != 1) { ?>
													<tr>
														<td>Operador</td>
														<td>Evaluación 3</td>
														<td><?php if ($totalRows_M3i > 0) {echo date( 'd/m/Y', strtotime( $row_M3i['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M3i > 0 AND $row_M3i['viable'] == 1) {echo "Apto";} else if ($totalRows_M3i > 0 AND $row_M3i['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M3i > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M3i['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M3i['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(3,3,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php } if ($totalRows_M3i > 0 AND $row_M3i['viable'] != 1) { ?>
													<tr>
														<td>Operador</td>
														<td>Evaluación 4</td>
														<td><?php if ($totalRows_M4i > 0) {echo date( 'd/m/Y', strtotime( $row_M4i['fecha_carga']));} else {echo "-";}?></td>
														<td><?php if ($totalRows_M4i > 0 AND $row_M3i['viable'] == 1) {echo "Apto";} else if ($totalRows_M4i > 0 AND $row_M4i['viable'] != 1) {echo "No Apto";} else {echo "-";}?></td>
														<td><?php if ($totalRows_M4i > 0) { ?><a target="_blank" href="filesPC/<?php echo $IDempleado; ?>/<?php echo $row_M4i['file']; ?>" class="btn btn-xs btn-info">Descargar</a> <div onClick="loadDynamicContentModal2(<?php echo $row_M4i['IDavance'] ?>, <?php echo $IDempleado; ?>)" class="btn btn-danger btn-xs">Borrar</div><?php } else { ?>
															<div onClick="loadDynamicContentModal(3,4,<?php echo $IDempleado; ?>)" class="btn btn-info btn-xs">Cargar</div>
															<?php } ?></td>
													</tr>
													<?php }  ?>
												</tbody>
											</table>
											</div>




										</div>
									</div>
								</div>

							</div>

									</div>
								</div>
							</div>
							<!-- /sales stats -->


                   <!-- Inline form modal -->
				   <div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Capacitación VII</h5>
								</div>
							<div class="modal-body">
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->
					</div>
					</div>

                   <!-- Inline form modal -->
				   <div id="bootstrap-modal2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content text-center">
								<div class="modal-header bg-danger">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Confirmación de Borrado</h5>
								</div>
								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

									<div id="conte-modal2">

									</div>

								</div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->
					</div>
					</div>
					</div>



						</div>
						</div>
				    <!-- /timeline -->

					<!-- Footer -->
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



<script>
function loadDynamicContentModal(IDevaluador, IDciclo, IDempleado){
	var options = {
			modal: true
		};
	$('#conte-modal').load('plan_carrera_tablero_edit_mdl.php?IDevaluador=' + IDevaluador + '&IDciclo=' + IDciclo + '&IDempleado='+ IDempleado, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
<script>
function loadDynamicContentModal2(IDavance, IDempleado){
	var options = {
			modal: true
		};
	$('#conte-modal2').load('plan_carrera_tablero_borrar_mdl.php?IDavance=' + IDavance + '&IDempleado='+ IDempleado, function() {
		$('#bootstrap-modal2').modal({show:true});
    });    
}
</script> 

</body>
</html>