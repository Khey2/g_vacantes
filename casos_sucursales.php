<?php require_once('Connections/vacantes.php');

require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

 ?>
 <?php
 
$IDmatriz_b = $_SESSION['IDmatriz'];
$IDsucursal_b = $_SESSION['IDsucursal'];

if(isset($_POST["IDmatriz"])) { $IDmatriz_b = $_POST["IDmatriz"]; }

$query_lsucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = '$IDmatriz_b' ORDER BY sucursal";
$lsucursal = mysql_query($query_lsucursal, $vacantes) or die(mysql_error());
$row_lsucursal = mysql_fetch_assoc($lsucursal);



do { ?>
<option value="<?php echo $row_lsucursal['IDsucursal']?>"<?php if (!(strcmp($row_lsucursal['IDsucursal'], $IDsucursal_b))) {echo "selected=\"selected\"";} ?>><?php echo $row_lsucursal['sucursal']?></option>
<?php
} while ($row_lsucursal = mysql_fetch_assoc($lsucursal));
$rows = mysql_num_rows($lsucursal);
if($rows > 0) {
  mysql_data_seek($lsucursal, 0);
  $row_lsucursal = mysql_fetch_assoc($lsucursal);
}

 ?>

