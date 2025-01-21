<?php require_once('Connections/vacantes.php'); ?>
<?php 

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
$desfase = $row_variables['dias_desfase'];

$IDempleado = $_GET['IDempleado'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT inc_vacaciones.IDvacaciones, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.descripcion_nomina, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDpuesto, prod_activos.IDarea, vac_areas.area, inc_vacaciones.IDdias_pendientes, inc_vacaciones.IDdias_asignados, inc_vacaciones.fecha_inicio, inc_vacaciones.fecha_fin FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN inc_vacaciones ON prod_activos.IDempleado = inc_vacaciones.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'"; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);


?>

                                  No. Emp. : <?php echo $row_detalle['IDempleado']; ?><br />
								  Nombre: <?php echo $row_detalle['emp_paterno']; ?> <?php echo $row_detalle['emp_materno']; ?> <?php echo $row_detalle['emp_nombre']; ?><br />
								  Area:  <?php echo $row_detalle['area']; ?><br />
								  Puesto:  <?php echo $row_detalle['denominacion']; ?><br />

							<p>&nbsp;</p>

							<table class="table  table-condensed">
                    			<thead>
                                	<tr> 
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>D&iacute;as</th>
                                    <th>Borrar</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo date('d/m/Y', strtotime($row_detalle['fecha_inicio'])); ?></td>
                                      <td><?php echo date('d/m/Y', strtotime($row_detalle['fecha_fin'])); ?></td>
                                      <td><?php echo $row_detalle['IDdias_asignados']; ?></td>
                                      <td><a href="vacaciones.php?IDvacaciones=<?php echo $row_detalle['IDvacaciones'] ?>&borrar=1"><i class="btn icon-trash btn-danger"></i></a></td>
                                    <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                                  </tbody>
                            </table>
							
							<p>&nbsp;</p>

							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							</div>

