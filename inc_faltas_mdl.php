<?php require_once('Connections/vacantes.php'); 
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

$IDempleado = $_GET['IDempleado'];	
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.IDmatriz,  prod_activos.IDarea, prod_activos.IDaplica_INC,  vac_matriz.matriz, inc_faltas.justificacion, inc_faltas.dias_menos  FROM prod_activos  INNER JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz left JOIN inc_faltas ON prod_activos.IDempleado = inc_faltas.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'"; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
?>

                            <p><strong>No. Emp.:</strong> <?php echo $row_detalle['IDempleado']; ?></p>												
                            <p><strong>Nombre:</strong> <?php echo $row_detalle['emp_paterno']; ?> <?php echo $row_detalle['emp_materno']; ?> <?php echo $row_detalle['emp_nombre'];?></p>												
                            <p><strong>Puesto:</strong> <?php echo $row_detalle['denominacion']; ?></p>												
							<p>&nbsp;</p>
							<p>Justifica de faltas por incapacidad o permisos.</p>
                            <?php if ($row_detalle['justificacion'] != '') { ?>


            					<form method="post" name="form1" action="inc_faltas.php?IDempleado=<?php echo $row_detalle['IDempleado']; ?>" 
                                class="form-horizontal form-validate-jquery">
                                
									<div class="modal-body">
                                    
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_puestos['IDempleado']; ?>" >

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2">Justificacion</label>
												<div class="col-sm-9">
                                                  <textarea name="justificacion" class="form-control" required="required" placeholder="Indica la justificación de las faltas reportadas" ><?php echo htmlentities($row_detalle['justificacion'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-2">D&iacute;as que no aplican:</label>
												<div class="col-sm-9">
												<input type="number" name="dias_menos" placeholder="días que no aplican" value="<?php echo htmlentities($row_detalle['dias_menos'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											</div>
	                                    </div>


                                       <p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        	<input type="submit" class="btn btn-primary" value="Capturar">
                                    </div>
								</form>

                            <?php } else { ?>   
                                
            					<form method="post" name="form1" action="inc_faltas.php?IDempleado=<?php echo $row_detalle['IDempleado']; ?>" 
                                class="form-horizontal form-validate-jquery">
                                
									<div class="modal-body">
                                	<input type="hidden" name="MM_insert" value="form1" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_puestos['IDempleado']; ?>" >
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2">Justificacion</label>
												<div class="col-sm-9">
                                                  <textarea name="justificacion" class="form-control" required="required" placeholder="Indica la justificación de las faltas reportadas"></textarea>
												</div>
											</div>
										</div>


										<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-2">D&iacute;as que no aplican:</label>
												<div class="col-sm-9">
												<input type="number" name="dias_menos" value="" class="form-control" placeholder="días que no aplican"/>
												</div>
											</div>
	                                    </div>

                                        <p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                       		<input type="submit" class="btn btn-primary" value="Capturar">
                                    </div>
								</form>
                            <?php } ?>   