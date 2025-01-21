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

if(isset($_GET['q'])) {$q = $_GET['q'];} else {$q = 0;} 

mysql_select_db($database_vacantes, $vacantes);
$query_repetido = "SELECT * FROM capa_becarios WHERE correo = '$q'"; 
$repetido = mysql_query($query_repetido, $vacantes) or die(mysql_error());
$row_repetido = mysql_fetch_assoc($repetido);
$totalRows_repetido = mysql_num_rows($repetido);


if ($totalRows_repetido > 0) {echo "El correo ya existe.";}
?>