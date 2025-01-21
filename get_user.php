<?php require_once('Connections/vacantes.php'); ?>
<?php
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

if(isset($_GET['q'])) {$q = intval($_GET['q']);} else {$q = 0;}


mysql_select_db($database_vacantes, $vacantes);
$query_codpos = "SELECT d_codigo_postal, d_estado, d_delegacion_municipio, d_colonia FROM con_cp WHERE d_codigo_postal = '$q' LIMIT 1";
mysql_query("SET NAMES 'utf8'");
$codpos = mysql_query($query_codpos, $vacantes) or die(mysql_error());
$row_codpos = mysql_fetch_assoc($codpos);

$query_cps = "SELECT d_colonia FROM con_cp WHERE d_codigo_postal = '$q'";
mysql_query("SET NAMES 'utf8'");
$cps = mysql_query($query_cps, $vacantes) or die(mysql_error());
$row_cps = mysql_fetch_assoc($cps);

$query_estado = "SELECT con_estados.estado, con_cp.d_codigo_postal, con_cp.IDcp FROM con_estados left JOIN con_cp ON con_cp.IDestado = con_estados.IDestado WHERE d_codigo_postal = '$q'"; 
mysql_query("SET NAMES 'utf8'");
$estado = mysql_query($query_estado, $vacantes) or die(mysql_error());
$row_estado = mysql_fetch_assoc($estado);
$totalRows_estado = mysql_num_rows($estado);

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php
$el_empleado = $_SESSION['IDusuario_a'];
$query_contratosX = "SELECT * FROM cv_activos WHERE IDusuario = '$el_empleado'";
mysql_query("SET NAMES 'utf8'");
$contratosX = mysql_query($query_contratosX, $vacantes) or die(mysql_error());
$row_contratosX = mysql_fetch_assoc($contratosX);
		do { ?>

          <!-- Basic text input -->
        <div class="form-group">
            <label class="control-label col-lg-3">Colónia:<span class="text-danger">*</span></label>
            <div class="col-lg-9">
            <select name='d_colonia' id='d_colonia' class="form-control">
            <?php do { ?>
					<?php if($q != 0) { ?>
            <option value="<?php echo $row_cps['d_colonia']; ?>"><?php echo $row_cps['d_colonia']; ?></option>
					<?php } else { ?>
            <option value="<?php echo $row_contratosX['d_colonia']; ?>"><?php echo $row_contratosX['d_colonia']; ?></option>
					<?php } ?>
            <?php } while ($row_cps = mysql_fetch_assoc($cps))?>
            </select>
            </div>
        </div>
        <!-- /basic text input -->

          <!-- Basic text input -->
        <div class="form-group">
            <label class="control-label col-lg-3">Alcaldía o Municipio:<span class="text-danger">*</span></label>
            <div class="col-lg-9">
              <input type="text" name="d_delegacion_municipio"  id="d_delegacion_municipio" class="form-control" placeholder="Alcaldia o Municipio" value="<?php if($q != 0) { echo $row_codpos['d_delegacion_municipio']; } else { echo $row_contratosX['d_delegacion_municipio']; }?>" required>
            </div>
        </div>
        <!-- /basic text input -->

        <!-- Basic select -->
        <div class="form-group">
            <label class="control-label col-lg-3">Estado:<span class="text-danger">*</span></label>
            <div class="col-lg-9">
              <input type="text" name="d_estado" id="d_estado" class="form-control" placeholder="Estado" value="<?php if($q != 0) { echo $row_codpos['d_estado']; } else { echo $row_contratosX['d_estado']; }?>" required>
            </div>
		</div>

	<?php } while ($row_codpos = mysql_fetch_assoc($codpos)) ?>
</body>
</html>