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

//borramos lo que hay
mysql_select_db($database_vacantes, $vacantes);
$query_resultado1 = "DELETE FROM prod_activos WHERE prod_activos.manual = 0";
$resultado1 = mysql_query($query_resultado1, $vacantes) or die(mysql_error());


$conn = mysqli_connect($hostname_vacantes, $username_vacantes ,$password_vacantes, $database_vacantes);

//$data=file_get_contents("https://sahuayo.eslabon.cloud/ews.smanager/api/EmpleadosConsumer/GetEmpleadosActivos?p1=NNp6GmWfpmXdaLP9YFV/uQ==&p2=rQNFw8MBt4djtfWDSXTxkw==&p3=X9eC1BXXSVUJYWjfXXnG7A==&p4=vhWJhhFxHMS4HdJ26tDYUg==&queryfilter=[]");

$arrContextOptions=array(
  "ssl"=>array(
       "verify_peer"=>false,
       "verify_peer_name"=>false,
  ),
);  
$url = "https://sahuayo.eslabon.cloud/ews.smanager/api/EmpleadosConsumer/GetEmpleadosActivos?p1=NNp6GmWfpmXdaLP9YFV/uQ==&p2=rQNFw8MBt4djtfWDSXTxkw==&p3=X9eC1BXXSVUJYWjfXXnG7A==&p4=vhWJhhFxHMS4HdJ26tDYUg==&queryfilter=[]";
$data = file_get_contents($url, false, stream_context_create($arrContextOptions));

$array = json_decode($data, true); //Convert JSON String into PHP Array

foreach($array as $row) //Extract the Array Values by using Foreach Loop 
{

$IDempleado = 	  	  $row["empleado"];
$IDdps =	 		        $row["id"];
$emp_paterno =		    utf8_decode($row["paterno"]);
$emp_materno =		    utf8_decode($row["materno"]);
$emp_nombre = 		    utf8_decode($row["nombre"]);
$rfc_ =		 	    	    $row["curp"];
$rfc =                str_replace("-", "", $rfc_);
$d_calle =	 		      utf8_decode($row["calle"]);
$d_num =			        utf8_decode($row["numeroCalle"]);
$d_col =			        utf8_decode($row["colonia"]);
$d_del =		  	      utf8_decode($row["delegacionMunicipio"]);
$d_est =		  	      utf8_decode($row["estado"]);
$d_cp =			  	      utf8_decode($row["codigoPostal"]);

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

$fecha_nacimiento_ =  utf8_decode($row["fechaNacimiento"]);
$y3 = substr( $fecha_nacimiento_, 6, 4 );
$m3 = substr( $fecha_nacimiento_, 3, 2 );
$d3 = substr( $fecha_nacimiento_, 0, 2 );
$fecha_nacimiento =  $y3."-".$m3."-".$d3;

$fecha_baja_ =	      utf8_decode($row["fechaBaja"]);
$y4 = substr( $fecha_baja_, 6, 4 );
$m4 = substr( $fecha_baja_, 3, 2 );
$d4 = substr( $fecha_baja_, 0, 2 );
$fecha_baja =  $y4."-".$m4."-".$d4;

$sueldo_mensual =	    $row["sueldoMensual"];
$sueldo_diario =	    $row["sueldoDiario"];
$sobre_sueldo =		    $row["sobreSueldo"];
if($sobre_sueldo == 0) {$sueldo_total = $sueldo_mensual; } else {$sueldo_total = $sobre_sueldo;}
$descripcion_nomina = $row["descripcionNomina"];
$descripcion_nivel =  $row["descripcionNivel"];
$denominacion1 =	    $row["descripcionPuesto"];
$denominacion2 =      utf8_decode(trim(strtoupper($denominacion1)));

$originales = 'ÁÉÍÓÚáéíóúÑñ';
$originales = utf8_decode($originales); 
$modificadas ='AEIOUAEIOUNN'; 
$denominacion = strtr($denominacion2, $originales, $modificadas);
$password = md5($IDempleado);
      
$imss =             $row["cedulaImss"];
$curp =             $row["cuentaIndividual"];
$estatus =          $row["estatus"];

//ver si ya esta
$query_yasta = "SELECT * FROM prod_activos WHERE IDempleado = '$IDempleado'";
$yasta = mysql_query($query_yasta, $vacantes) or die(mysql_error());
$row_yasta = mysql_fetch_assoc($yasta);

// si no está
if($IDempleado > 0 and $row_yasta['IDempleado'] == ''){

$query = "INSERT INTO prod_activos (IDempleado, IDdps, emp_paterno, emp_materno, emp_nombre, rfc, fecha_alta, fecha_antiguedad, fecha_baja, fecha_nacimiento ,sueldo_mensual, sueldo_diario, sobre_sueldo, sueldo_total, descripcion_nomina, descripcion_nivel, denominacion,	d_calle, d_num, d_col, d_del, d_est, d_cp, imss, curp, rfc13, manual, estatus) values ('".$IDempleado."', '". $IDdps."', '". $emp_paterno."', '". $emp_materno."','". $emp_nombre."','". $rfc."','". $fecha_alta."','". $fecha_antiguedad."','".$fecha_baja."','". $fecha_nacimiento."', '".$sueldo_mensual."',	'". $sueldo_diario."','". $sobre_sueldo."','". $sueldo_total."','". $descripcion_nomina ."','".$descripcion_nivel."','". $denominacion."', '".$d_calle."','".$d_num."','".$d_col."','".$d_del."', '".$d_est."','".$d_cp."','".$imss."','".$curp."','".$rfc."', 0, '".$estatus."')";
$result = mysqli_query($conn, $query) or die(mysql_error());

    }
}
?> 