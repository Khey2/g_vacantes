<?php require_once('Connections/vacantes.php'); 

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

ini_set('max_execution_time', 0);
set_time_limit(1800);
ini_set('memory_limit', '-1');

$conn = mysqli_connect($hostname_vacantes, $username_vacantes ,$password_vacantes, $database_vacantes);


$data=file_get_contents("https://sahuayo.eslabon.cloud/ews.smanager/api/EmpleadosConsumer/GetEmpleadosActivos?p1=NNp6GmWfpmXdaLP9YFV/uQ==&p2=rQNFw8MBt4djtfWDSXTxkw==&p3=X9eC1BXXSVUJYWjfXXnG7A==&p4=vhWJhhFxHMS4HdJ26tDYUg==&queryfilter=[]");
$array = json_decode($data, true); //Convert JSON String into PHP Array
foreach($array as $row) //Extract the Array Values by using Foreach Loop 
{

$IDempleado = 	  	  $row["empleado"];
$IDdps =	 		        $row["id"];
$emp_paterno =		    utf8_decode($row["paterno"]);
$emp_materno =		    utf8_decode($row["materno"]);
$emp_nombre = 		    utf8_decode($row["nombre"]);

$fecha_alta_ =		    utf8_decode($row["fechaAlta"]);
$y1 = substr( $fecha_alta_, 6, 4 );
$m1 = substr( $fecha_alta_, 3, 2 );
$d1 = substr( $fecha_alta_, 0, 2 );
$fecha_alta =  $y1."-".$m1."-".$d1; 

$fecha_antiguedad_ =  utf8_decode($row["fechaAntiguedad"]);
$y2 = substr( $fecha_antiguedad_, 6, 4 );
$m2 = substr( $fecha_antiguedad_, 3, 2 );
$d2 = substr( $fecha_antiguedad_, 0, 2 );
$fecha_antiguedad =  $y2."-".$m2."-".$d2;

$fecha_baja_ = utf8_decode($row["fechaBaja"]);
$y4 = substr( $fecha_baja_, 6, 4 );
$m4 = substr( $fecha_baja_, 3, 2 );
$d4 = substr( $fecha_baja_, 0, 2 );
$fecha_baja =  $y4."-".$m4."-".$d4;

$descripcion_nomina = $row["descripcionNomina"];
$descripcion_nivel =  $row["descripcionNivel"];
$denominacion1 =	    $row["descripcionPuesto"];
$denominacion2 =      utf8_decode(trim(strtoupper($denominacion1)));

$originales = 'ÁÉÍÓÚáéíóúÑñ';
$originales = utf8_decode($originales); 
$modificadas ='AEIOUAEIOUNN'; 
$denominacion = strtr($denominacion2, $originales, $modificadas);
      
$query = "INSERT INTO prod_activosfaltas (IDempleado, emp_paterno, emp_materno, emp_nombre, fecha_alta, fecha_antiguedad, fecha_baja, descripcion_nomina, descripcion_nivel, denominacion) values ('".$IDempleado."', '". $emp_paterno."', '". $emp_materno."','". $emp_nombre."','". $fecha_alta."','". $fecha_antiguedad."','".$fecha_baja."','". $descripcion_nomina ."','".$descripcion_nivel."','". $denominacion."')";
$result = mysqli_query($conn, $query) or die(mysql_error());

}
?> 