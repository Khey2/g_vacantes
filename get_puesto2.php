<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

if(isset($_GET['q'])) {$q = intval($_GET['q']);} else {$q = 0;}

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDarea = '$q'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

$IDpprueba = $_SESSION['IDpprueba'];
mysql_select_db($database_vacantes, $vacantes);
$query_pprueba = "SELECT pp_prueba.val1, pp_prueba.val2, pp_prueba.val3, pp_prueba.val4, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDarea, pp_prueba.sueldo_nuevo, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, pp_prueba.IDmatriz_destino, pp_prueba.IDarea_destino, pp_prueba.fecha_fin, pp_prueba.fecha_inicio, pp_prueba.IDestatus, pp_prueba.observaciones, prod_activos.fecha_antiguedad, prod_activos.sueldo_total, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, puesto_origen.denominacion AS denominacion_origen, area_oringen.area AS area_origen, matriz_origen.matriz AS matriz_origen, matriz_destino.matriz as matriz_destino, area_destino.area AS area_destino, puesto_destino.denominacion AS denominacion_destino FROM pp_prueba LEFT JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos AS puesto_origen ON pp_prueba.IDpuesto = puesto_origen.IDpuesto LEFT JOIN vac_areas AS area_oringen ON puesto_origen.IDarea = area_oringen.IDarea LEFT JOIN vac_matriz AS matriz_origen ON pp_prueba.IDmatriz = matriz_origen.IDmatriz LEFT JOIN vac_matriz AS matriz_destino ON pp_prueba.IDmatriz_destino = matriz_destino.IDmatriz LEFT JOIN vac_puestos AS puesto_destino ON pp_prueba.IDpuesto_destino = puesto_destino.IDpuesto LEFT JOIN vac_areas AS area_destino ON puesto_destino.IDarea = area_destino.IDarea WHERE IDpprueba = '$IDpprueba'";
$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);

header("Content-Type: text/html;charset=utf-8");
?>


<select name="IDpuesto_destino" id="IDpuesto_destino" class="form-control" onchange="realizaProceso($('#IDpuesto_destino').val(), $('#IDmatriz_destino').val());return false; " required="required">
<option value="">Seleccione una opción</option> 
<?php  do { ?>
<option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $row_pprueba['IDpuesto_destino']))) 
{echo "SELECTED";} ?>><?php echo $row_puesto['denominacion']?></option>
<?php
} while ($row_puesto = mysql_fetch_assoc($puesto));
$rows = mysql_num_rows($puesto);
if($rows > 0) {
mysql_data_seek($puesto, 0);
$row_puesto = mysql_fetch_assoc($puesto);
} ?>
</select>


