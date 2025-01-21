<?php require_once('Connections/vacantes.php'); ?>
<?php 

$IDempleado = $_GET['IDempleado']; 
$Tipo = $_GET['Tipo']; 

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT * FROM prod_activos_anual WHERE prod_activos_anual.IDempleado = $IDempleado"; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

?>                                                                       
             			<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="aumento_anual.php" >
								<div class="modal-body">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" >
                                	<input type="hidden" name="MM_insert" value="form1">
                                	<input type="hidden" name="sueldo_mensual" value="<?php echo $row_detalle['sueldo_mensual']; ?>">

<?php if ($Tipo == 1) { ?>

									Indica el <b>Monto</b> de aumento anual.<br/>
									<?php if ($row_detalle['exepcione'] != ''){ ?><span class="text text-danger">Criterio de excepción o descarte: <?php echo $row_detalle['exepcione']; ?></span><?php } ?>
									<p>&nbsp;</p>

                                	<input type="hidden" name="tipo" value="1">
											<div class="form-group">
													<div class="col-sm-3">
													<label>Empleado:</span></label>
													</div>
													<div class="col-sm-9">
														<?php echo $row_detalle['emp_paterno']." ".$row_detalle['emp_materno']." ".$row_detalle['emp_nombre']." (".$row_detalle['IDempleado'].")"; ?>
													</div>
												</div>

												<div class="form-group">
													<div class="col-sm-3">
													<label>Aumento($):<span class="text-danger">*</span></label>
													</div>
													
													<div class="col-sm-9">
													<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="number" min="0" max="15000" step="0.01" name="aumento" class="form-control" id="aumento" value="<?php echo $row_detalle['aumento_monto'];?>" required="required">
													</div>
													</div>

													<p>&nbsp;</p>
													
													<div class="col-sm-3">
													<label>Justificacion:<span class="text-danger">*</span></label>
													</div>
													<div class="col-sm-9">
														<textarea name="comentarios" rows="3" required="required" class="form-control" id="comentarios" placeholder="Justificacion."><?php echo $row_detalle['comentarios']; ?></textarea>
													</div>
												</div>
<?php } else { ?>
									Indica el <b>Porcentaje</b> de aumento anual.<br/>
									<?php if ($row_detalle['exepcione'] != ''){ ?><span class="text text-danger">Criterio de excepción o descarte: <?php echo $row_detalle['exepcione']; ?></span><?php } ?>
									<p>&nbsp;</p>

									<input type="hidden" name="tipo" value="2">

												<div class="form-group">
													<div class="col-sm-3">
													<label>Empleado:</span></label>
													</div>
													<div class="col-sm-6">
														<?php echo $row_detalle['emp_paterno']." ".$row_detalle['emp_materno']." ".$row_detalle['emp_nombre']." (".$row_detalle['IDempleado'].")"; ?>
													</div>
												</div>

											<div class="form-group">
													<div class="col-sm-3">
													<label>Aumento(%):<span class="text-danger">*</span></label>
													</div>

													<div class="col-sm-9">
													<div class="input-group">
												<input type="number" min="0" max="30" step="0.01" name="aumento" class="form-control" id="aumento" value="<?php echo $row_detalle['aumento_porcentaje']; ?>" required="required">
												<span class="input-group-addon">%</span>
													</div>
													</div>

													<p>&nbsp;</p>
													<div class="col-sm-3">
													<label>Justificacion:<span class="text-danger">*</span></label>
													</div>
													<div class="col-sm-9">
														<textarea name="comentarios" rows="3" class="form-control" id="comentarios" required="required" placeholder="Justificacion."><?php echo $row_detalle['comentarios']; ?></textarea>
													</div>
												</div>
                        
<?php } ?>
									<div class="modal-footer">
												<?php if ($row_detalle['ajustado'] == 1){ ?>
                                                <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn bg-danger-400">Restablecer</button>
                                                <?php } ?>
												<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Guardar">
						            </div>

						</form>

					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-sm modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de restablecimiento</h6>
								</div>

								<div class="modal-body">
									<p><br />¿Estas seguro de que quieres restablecer la captura?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="aumento_anual.php?IDempleado=<?php echo $IDempleado?>&restablecer=1">Si restablecer</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

						
