<?php require_once('Connections/vacantes.php'); ?> 
<?php

require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$actualusuario = $_SESSION['kt_login_id'];
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$actualusuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$el_usuario = $row_usuario['IDusuario'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDempleado = $_GET['IDempleado'];
$IDpaso = $_GET['IDpaso']; 

$query_activos = "SELECT * FROM prod_activosfaltas WHERE IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);
$el_rfc = $row_activos['RFC'];

$query_telefono = "SELECT * FROM con_empleados WHERE a_rfc = '$el_rfc'";
$telefono = mysql_query($query_telefono, $vacantes) or die(mysql_error());
$row_telefono = mysql_fetch_assoc($telefono);
$totalRows_telefono = mysql_num_rows($telefono);

$query_reactivos1 = "SELECT * FROM reclu_exp_sahuayo_preguntas WHERE IDpaso = $IDpaso AND IDpregunta = 1"; 
$reactivos1 = mysql_query($query_reactivos1, $vacantes) or die(mysql_error());
$row_reactivos1 = mysql_fetch_assoc($reactivos1);
$totalRows_reactivos1 = mysql_num_rows($reactivos1);

$query_reactivos2 = "SELECT * FROM reclu_exp_sahuayo_preguntas WHERE IDpaso = $IDpaso AND IDpregunta = 2"; 
$reactivos2 = mysql_query($query_reactivos2, $vacantes) or die(mysql_error());
$row_reactivos2 = mysql_fetch_assoc($reactivos2);
$totalRows_reactivos2 = mysql_num_rows($reactivos2);

$query_reactivos3 = "SELECT * FROM reclu_exp_sahuayo_preguntas WHERE IDpaso = $IDpaso AND IDpregunta = 3"; 
$reactivos3 = mysql_query($query_reactivos3, $vacantes) or die(mysql_error());
$row_reactivos3 = mysql_fetch_assoc($reactivos3);
$totalRows_reactivos3 = mysql_num_rows($reactivos3);

$query_reactivos4 = "SELECT * FROM reclu_exp_sahuayo_preguntas WHERE IDpaso = $IDpaso AND IDpregunta = 4"; 
$reactivos4 = mysql_query($query_reactivos4, $vacantes) or die(mysql_error());
$row_reactivos4 = mysql_fetch_assoc($reactivos4);
$totalRows_reactivos4 = mysql_num_rows($reactivos4);

$query_encuesta = "SELECT * FROM reclu_exp_sahuayo WHERE IDempleado = $IDempleado AND IDpaso = $IDpaso";
$encuesta = mysql_query($query_encuesta, $vacantes) or die(mysql_error());
$row_encuesta = mysql_fetch_assoc($encuesta);
$totalRows_encuesta = mysql_num_rows($encuesta);

?>
							<div class="modal-body">
    

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="plantilla_nuevos.php">
								<div class="modal-body">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" >
                                    
									<div class="form-group">
										<label class="control-label col-lg-4">Nombre:</label>
											<div class="col-lg-8">
											<b><?php echo $row_activos['emp_paterno']; ?> <?php echo $row_activos['emp_materno']; ?> <?php echo $row_activos['emp_nombre']; ?></b>
											</div>
									</div>
									<div class="form-group">
										 <label class="control-label col-lg-4">Teléfono (Whatsapp):</label>
											<div class="col-lg-8">
											<b><?php echo $row_telefono['telefono_1']; ?></b>
											</div>
								 	</div>
									 <div class="form-group">
										 <label class="control-label col-lg-4">Puesto:</label>
											<div class="col-lg-8">
											<b><?php echo $row_activos['denominacion']; ?></b>
											</div>
								 	</div>



					<?php if ($totalRows_encuesta > 0) {?>


						<?php if ($IDpaso == 2 OR $IDpaso == 4 OR $IDpaso == 5) {?>
									
										<div class="form-group">
												<label class="control-label col-lg-7" ><?php echo  $row_reactivos1['pregunta']; ?>:<span class="text-danger">*</span></label>
												<div class="col-lg-5">
												<select name="preg1" id="preg1" class="form-control">
													<option value="0"<?php if ( $row_encuesta['preg1'] == 0) {echo "SELECTED";} ?>>Seleccione una opción</option> 
													<option value="3"<?php if ( $row_encuesta['preg1'] == 3) {echo "SELECTED";} ?>>Buena</option>
													<option value="2"<?php if ( $row_encuesta['preg1'] == 2) {echo "SELECTED";} ?>>Regular</option>
													<option value="1"<?php if ( $row_encuesta['preg1'] == 1) {echo "SELECTED";} ?>>Mala</option>
                                          		</select>
												</div>
	                                    </div>

						<?php } else if ($IDpaso == 3) {?>

							<div class="form-group">
												<label class="control-label col-lg-7" ><?php echo  $row_reactivos1['pregunta']; ?>:<span class="text-danger">*</span></label>
												<div class="col-lg-5">
												<select name="preg1" id="preg1" class="form-control">
													<option value="0"<?php if ( $row_encuesta['preg1'] == 0) {echo "SELECTED";} ?>>Seleccione una opción</option> 
													<option value="3"<?php if ( $row_encuesta['preg1'] == 3) {echo "SELECTED";} ?>>Bueno</option>
													<option value="2"<?php if ( $row_encuesta['preg1'] == 2) {echo "SELECTED";} ?>>Regular</option>
													<option value="1"<?php if ( $row_encuesta['preg1'] == 1) {echo "SELECTED";} ?>>Malo</option>
                                          		</select>
												</div>
	                                    </div>

						<?php } else { ?>

							<div class="form-group">
												<label class="control-label col-lg-7" ><?php echo  $row_reactivos1['pregunta']; ?>:<span class="text-danger">*</span></label>
												<div class="col-lg-5">
												<select name="preg1" id="preg1" class="form-control">
													<option value="0"<?php if ( $row_encuesta['preg1'] == 0) {echo "SELECTED";} ?>>Seleccione una opción</option> 
													<option value="2"<?php if ( $row_encuesta['preg1'] == 2) {echo "SELECTED";} ?>>Si</option>
													<option value="1"<?php if ( $row_encuesta['preg1'] == 1) {echo "SELECTED";} ?>>No</option>
                                          		</select>
												</div>
	                                    </div>

						<?php }  ?>


										<div class="form-group">
												<label class="control-label col-lg-7" ><?php echo  $row_reactivos2['pregunta']; ?>:<span class="text-danger">*</span></label>
												<div class="col-lg-5">
												<select name="preg2" id="preg2" class="form-control">
													<option value="0"<?php if ( $row_encuesta['preg2'] == 0) {echo "SELECTED";} ?>>Seleccione una opción</option> 
													<option value="2"<?php if ( $row_encuesta['preg2'] == 2) {echo "SELECTED";} ?>>Si</option>
													<option value="1"<?php if ( $row_encuesta['preg2'] == 1) {echo "SELECTED";} ?>>No</option>
                                          		</select>
												</div>
	                                    </div>


										<div class="form-group">
												<label class="control-label col-lg-7" ><?php echo  $row_reactivos3['pregunta']; ?>:<span class="text-danger">*</span></label>
												<div class="col-lg-5">
												<select name="preg3" id="preg3" class="form-control">
													<option value="0"<?php if ( $row_encuesta['preg3'] == 0) {echo "SELECTED";} ?>>Seleccione una opción</option> 
													<option value="2"<?php if ( $row_encuesta['preg3'] == 2) {echo "SELECTED";} ?>>Si</option>
													<option value="1"<?php if ( $row_encuesta['preg3'] == 1) {echo "SELECTED";} ?>>No</option>
                                          		</select>
												</div>
	                                    </div>


					<?php if ($IDpaso == 4) {?>
									
									<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos4['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg4" id="preg4" class="form-control">
												<option value="0"<?php if ( $row_encuesta['preg4'] == 0) {echo "SELECTED";} ?>>0 faltas</option>
												<option value="1"<?php if ( $row_encuesta['preg4'] == 1) {echo "SELECTED";} ?>>1 falta</option>
												<option value="2"<?php if ( $row_encuesta['preg4'] == 2) {echo "SELECTED";} ?>>2 faltas</option>
												<option value="3"<?php if ( $row_encuesta['preg4'] == 3) {echo "SELECTED";} ?>>3 faltas</option>
												<option value="4"<?php if ( $row_encuesta['preg4'] == 4) {echo "SELECTED";} ?>>4 faltas</option>
											  </select>
											</div>
									</div>

					<?php } else if ($IDpaso == 2) {?>

						<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos4['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg4" id="preg4" class="form-control">
												<option value="0"<?php if ( $row_encuesta['preg4'] == 0) {echo "SELECTED";} ?>>Seleccione una opción</option> 
												<option value="3"<?php if ( $row_encuesta['preg4'] == 3) {echo "SELECTED";} ?>>Buena</option>
												<option value="2"<?php if ( $row_encuesta['preg4'] == 2) {echo "SELECTED";} ?>>Regular</option>
												<option value="1"<?php if ( $row_encuesta['preg4'] == 1) {echo "SELECTED";} ?>>Mala</option>
											  </select>
											</div>
									</div>

					<?php } else { ?>

						<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos4['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg4" id="preg4" class="form-control">
												<option value="0"<?php if ( $row_encuesta['preg4'] == 0) {echo "SELECTED";} ?>>Seleccione una opción</option> 
												<option value="2"<?php if ( $row_encuesta['preg4'] == 2) {echo "SELECTED";} ?>>Si</option>
												<option value="1"<?php if ( $row_encuesta['preg4'] == 1) {echo "SELECTED";} ?>>No</option>
											  </select>
											</div>
									</div>

					<?php }  ?>


										<div class="form-group">
												<label class="control-label col-lg-7" >Observaciones y motivo de baja (si aplica):</label>
												<div class="col-lg-5">
												<textarea rows="2" cols="2" name="observaciones" id="observaciones" class="form-control" placeholder="Observaciones adicionales." ><?php echo $row_encuesta['observaciones']; ?></textarea>
												</div>
	                                    </div>


					<?php } else {?>

						<?php if ($IDpaso == 2 OR $IDpaso == 4 OR $IDpaso == 5) {?>
									
									<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos1['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg1" id="preg1" class="form-control">
												<option value="0" >Seleccione una opción</option> 
												<option value="3">Buena</option>
												<option value="2">Regular</option>
												<option value="1">Mala</option>
											  </select>
											</div>
									</div>

					<?php } else if ($IDpaso == 3) {?>

						<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos1['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg1" id="preg1" class="form-control">
												<option value="0" >Seleccione una opción</option> 
												<option value="3">Bueno</option>
												<option value="2">Regular</option>
												<option value="1">Malo</option>
											  </select>
											</div>
									</div>

					<?php } else { ?>

						<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos1['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg1" id="preg1" class="form-control">
												<option value="0" >Seleccione una opción</option> 
												<option value="2">Si</option>
												<option value="1">No</option>
											  </select>
											</div>
									</div>

					<?php }  ?>


									<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos2['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg2" id="preg2" class="form-control">
												<option value="0" >Seleccione una opción</option> 
												<option value="2">Si</option>
												<option value="1">No</option>
											  </select>
											</div>
									</div>


									<div class="form-group">
											<label class="control-label col-lg-7" ><?php echo  $row_reactivos3['pregunta']; ?>:<span class="text-danger">*</span></label>
											<div class="col-lg-5">
											<select name="preg3" id="preg3" class="form-control">
												<option value="0" >Seleccione una opción</option> 
												<option value="2">Si</option>
												<option value="1">No</option>
											  </select>
											</div>
									</div>


				<?php if ($IDpaso == 4) {?>
								
								<div class="form-group">
										<label class="control-label col-lg-7" ><?php echo  $row_reactivos4['pregunta']; ?>:<span class="text-danger">*</span></label>
										<div class="col-lg-5">
										<select name="preg4" id="preg4" class="form-control">
											<option value="0">0 faltas</option>
											<option value="1">1 falta</option>
											<option value="2">2 faltas</option>
											<option value="3">3 faltas</option>
											<option value="4">4 faltas</option>
										  </select>
										</div>
								</div>

				<?php } else if ($IDpaso == 2) {?>

					<div class="form-group">
										<label class="control-label col-lg-7" ><?php echo  $row_reactivos4['pregunta']; ?>:<span class="text-danger">*</span></label>
										<div class="col-lg-5">
										<select name="preg4" id="preg4" class="form-control">
											<option value="0" >Seleccione una opción</option> 
											<option value="3">Buena</option>
											<option value="2">Regular</option>
											<option value="1">Mala</option>
										  </select>
										</div>
								</div>

				<?php } else { ?>

					<div class="form-group">
										<label class="control-label col-lg-7" ><?php echo  $row_reactivos4['pregunta']; ?>:<span class="text-danger">*</span></label>
										<div class="col-lg-5">
										<select name="preg4" id="preg4" class="form-control">
											<option value="0" >Seleccione una opción</option> 
											<option value="2">Si</option>
											<option value="1">No</option>
										  </select>
										</div>
								</div>

				<?php }  ?>

										<div class="form-group">
												<label class="control-label col-lg-7" >Observaciones y motivo de baja (si aplica):</label>
												<div class="col-lg-5">
												<textarea rows="2" cols="2" name="observaciones" id="observaciones" class="form-control" placeholder="Observaciones adicionales."></textarea>
												</div>
	                                    </div>



					<?php } ?>


										<div class="modal-footer">
								<?php if ($totalRows_encuesta > 0) {?>
											<button type="submit" class="btn btn-info" id="send">Actualizar</button> 
											<input type="hidden" name="MM_update" value="form1" />
								<?php } else {?>
											<button type="submit" class="btn btn-success" id="send">Guardar</button> 
											<input type="hidden" name="MM_insert" value="form1" />
								<?php }?>
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<input type="hidden" name="IDempleado" id="IDempleado" value="<?php echo $IDempleado; ?>" >
											<input type="hidden" name="IDpaso" id="IDpaso" value="<?php echo $IDpaso; ?>" >
                                    </div>
                                    </div>
								</form>

                               