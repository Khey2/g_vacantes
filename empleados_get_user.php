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

if(isset($_GET['q'])) {$q = intval($_GET['q']);} else {$q = 0;}

$query_codpos = "SELECT d_codigo_postal, d_estado, d_delegacion_municipio, d_colonia FROM con_cp WHERE d_codigo_postal = '$q' LIMIT 1";
mysql_query("SET NAMES 'utf8'");
$codpos = mysql_query($query_codpos, $vacantes) or die(mysql_error());
$row_codpos = mysql_fetch_assoc($codpos);
$totalRows_codpos = mysql_num_rows($codpos);

$query_cps = "SELECT d_colonia FROM con_cp WHERE d_codigo_postal = '$q' ORDER BY d_colonia ASC";
mysql_query("SET NAMES 'utf8'");
$cps = mysql_query($query_cps, $vacantes) or die(mysql_error());
$row_cps = mysql_fetch_assoc($cps);

mysql_select_db($database_vacantes, $vacantes);
$query_estado = "SELECT con_estados.estado, con_cp.d_codigo_postal, con_cp.IDcp FROM con_estados left JOIN con_cp ON con_cp.IDestado = con_estados.IDestado WHERE d_codigo_postal = '$q'"; 
mysql_query("SET NAMES 'utf8'");
$estado = mysql_query($query_estado, $vacantes) or die(mysql_error());
$row_estado = mysql_fetch_assoc($estado);
$totalRows_estado = mysql_num_rows($estado);

header("Content-Type: text/html;charset=utf-8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
$el_empleado = $_SESSION['IDempleado'];
$query_contratosX = "SELECT * FROM con_empleados WHERE IDempleado = '$el_empleado'";
mysql_query("SET NAMES 'utf8'");
$contratosX = mysql_query($query_contratosX, $vacantes) or die(mysql_error());
$row_contratosX = mysql_fetch_assoc($contratosX);

do { ?>
        
     <?php if ( $totalRows_codpos == 0 and $q != 0) { ?>
        <div class="form-group">
            <div class="col-lg-3"></div>
            <div class="col-lg-9"><span class="text-danger">El C.P. no existe</span></div>
        </div>
        
     <?php } ?>
        
     <?php if ( $totalRows_codpos == 0) { ?>

        <!-- Basic text input -->
        <div class="form-group">
            <label class="control-label col-lg-3">Colonia:<span class="text-danger">*</span></label>
            <div class="col-lg-9">
              <input type="text" name="d_colonia" id="d_colonia" class="form-control" placeholder="Colonia" value="<?php if($q != 0) { echo $row_codpos['d_colonia']; } else { echo $row_contratosX['d_colonia']; }?>" required>
            </div>
        </div>
        <!-- /basic text input -->
     
     <?php } else { ?>
     
        <!-- Basic text input -->
        <div class="form-group">
            <label class="control-label col-lg-3">Colonia:<span class="text-danger">*</span></label>
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
			<span class="help-block">Si no aparece la Colonia en el menú, <a href="empleados_new_cp.php?cp=<?php echo $q; ?>">clic aqui para agregarla</a>.</span>
		</div>
        </div>
        <!-- /basic text input -->
        
     <?php } ?>

          <!-- Basic text input -->
        <div class="form-group">
            <label class="control-label col-lg-3">Alcaldía o Municipio:<span class="text-danger">*</span></label>
            <div class="col-lg-9">
              <input type="text" name="d_delegacion_municipio" id="d_delegacion_municipio" class="form-control" placeholder="Alcaldia o Municipio" value="<?php if($q != 0) { echo $row_codpos['d_delegacion_municipio']; } else { echo $row_contratosX['d_delegacion_municipio']; }?>" required>
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