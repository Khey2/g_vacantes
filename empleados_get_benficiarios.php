<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

if(isset($_GET['p'])) {$p = $_GET['p'];} else {$p = 0;}

header("Content-Type: text/html;charset=utf-8");


if ( $p == 1 ) { ?>
	<script type="text/javascript">
    $("#fecha_nacimiento").datepicker();
	</script>


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono de contacto para beneficiarios y contactos de emergencia" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección (70 caracteres máximo):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" maxlength="70" name="direccion" id="direccion" class="form-control" placeholder="Direccion para beneficiarios y contactos de emergencia" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
									
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de Nacimiento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="input-group">
											<span class="input-group-addon"><i class="icon-calendar3"></i></span>
											<input type="text" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" data-mask="99/99/9999"	>
										</div>
										</div>
									</div>
									<!-- /basic text input -->
									


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Asignación de % Seguros:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="number" maxlength="3" max="100" min="0" name="observaciones" id="observaciones" class="form-control" placeholder="Si es beneficiario, indique el % del seguro asignado." value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->

     
<?php } if ( $p == 2) { ?>

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:</label>
										<div class="col-lg-9">
											<input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono de contacto para beneficiarios y contactos de emergencia" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección (70 caracteres máximo):</label>
										<div class="col-lg-9">
											<input type="text" maxlength="70" name="direccion" id="direccion" class="form-control" placeholder="Direccion para beneficiarios y contactos de emergencia" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
									

     
<?php } if ( $p == 3) { ?>

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono" id="telefono" class="form-control" placeholder="Teléfono de contacto para beneficiarios y contactos de emergencia" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección (70 caracteres máximo):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" maxlength="70"  name="direccion" id="direccion" class="form-control" placeholder="Direccion para beneficiarios y contactos de emergencia" value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
									
									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de Nacimiento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<div class="input-group">
											<span class="input-group-addon"><i class="icon-calendar3"></i></span>
											<input type="text" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" data-mask="99/99/9999"	>
										</div>
										</div>
									</div>
									<!-- /basic text input -->
								

									
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Asignación de % Seguros:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="number" maxlength="3" max="100" min="0" name="observaciones" id="observaciones" class="form-control" placeholder="Si es beneficiario, indique el % del seguro asignado." value="" required="required">
										</div>
									</div>
									<!-- /basic text input -->
     
<?php } ?>
