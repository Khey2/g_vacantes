<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_nom35 = new KT_connection($vacantes, $database_vacantes);

$la_matriz = $_SESSION['IDmatriz'];
$q = $_REQUEST["q"];

mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE IDmatriz = '$la_matriz' AND (emp_nombre LIKE '%$q%' OR emp_paterno LIKE '%$q%' OR emp_materno LIKE '%$q%')";
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);

if ($totalRows_jefes > 0)  {  

 do {  echo $row_jefes['emp_nombre'] . " " .  $row_jefes['emp_paterno'] . " " .  $row_jefes['emp_materno'] . ", "; } while ($row_jefes = mysql_fetch_assoc($jefes));

} else { echo "Sin sugerencias";}

?>