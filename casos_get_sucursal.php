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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past


//las variables
$IDmatriz = $_SESSION['IDmatriz'];
$IDsucursal = $_SESSION['IDsucursal'];
$IDsindicato = $_SESSION['IDsindicato'];

if(isset($_GET['p']) AND $_GET['p'] > 0) {
	
$p = $_GET['p'];

$query_lsucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = $p ORDER BY sucursal";
$lsucursal = mysql_query($query_lsucursal, $vacantes) or die(mysql_error());
$row_lsucursal = mysql_fetch_assoc($lsucursal);
$totalRows_lsucursal = mysql_num_rows($lsucursal);

} else {
	
$query_lsucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = $IDmatriz ORDER BY sucursal";
$lsucursal = mysql_query($query_lsucursal, $vacantes) or die(mysql_error());
$row_lsucursal = mysql_fetch_assoc($lsucursal);
$totalRows_lsucursal = mysql_num_rows($lsucursal);

}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php if ($IDsindicato != 0) { ?>

									<div class="form-group">
										<label class="control-label col-lg-3">Sucursal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsucursal" id="IDsucursal" class="form-control" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_lsucursal['IDsucursal']?>"<?php if (!(strcmp($row_lsucursal['IDsucursal'], $IDsucursal))) 
												  {echo "SELECTED";} ?>><?php echo $row_lsucursal['sucursal']?></option>
												  <?php
												 } while ($row_lsucursal = mysql_fetch_assoc($lsucursal));
												   $rows = mysql_num_rows($lsucursal);
												   if($rows > 0) {
												   mysql_data_seek($lsucursal, 0);
												   $row_lsucursal = mysql_fetch_assoc($lsucursal);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

<?php } else { ?>

									<div class="form-group">
										<label class="control-label col-lg-3">Sucursal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsucursal" id="IDsucursal" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_lsucursal['IDsucursal']?>"><?php echo $row_lsucursal['sucursal']?></option>
												  <?php
												 } while ($row_lsucursal = mysql_fetch_assoc($lsucursal));
												   $rows = mysql_num_rows($lsucursal);
												   if($rows > 0) {
												   mysql_data_seek($lsucursal, 0);
												   $row_lsucursal = mysql_fetch_assoc($lsucursal);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

<?php }  ?>


</body>
</html>