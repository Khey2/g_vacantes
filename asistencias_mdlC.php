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
$IDfecha = $_GET['IDfecha'];	

mysql_select_db($database_vacantes, $vacantes);
$query_trucks = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = '$IDmatriz' AND IDfecha = '$IDfecha'";
mysql_query("SET NAMES 'utf8'");
$trucks = mysql_query($query_trucks, $vacantes) or die(mysql_error());
$row_trucks = mysql_fetch_assoc($trucks);
$totalRows_trucks = mysql_num_rows($trucks);

$IDcapturador = $el_usuario;

?>
							<div class="modal-body">
                                                        
            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="asistencias_trucks.php" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDmatriz" value="<?php echo $IDmatriz; ?>" >
                                    <input type="hidden" name="IDfecha" value="<?php echo $IDfecha; ?>" >
                                    <input type="hidden" name="IDcapturador" value="<?php echo $IDcapturador; ?>" >
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-6">Unidades Disponibles:</label>
										<div class="col-lg-6">
											<input type="number" name="disponibles" id="disponibles" class="form-control" value="<?php echo $row_trucks['disponibles']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-6">Unidades en Ruta:</label>
										<div class="col-lg-6">
											<input type="number" name="en_ruta" id="en_ruta" class="form-control" value="<?php echo $row_trucks['en_ruta']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-6">Unidades Cargadas sin Salir:</label>
										<div class="col-lg-6">
											<input type="number" name="cargados_sin_salir" id="cargados_sin_salir" class="form-control" value="<?php echo $row_trucks['cargados_sin_salir']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-6">Unidades No Disponibles (Taller, Descompuesta, Trámite, etc).:</label>
										<div class="col-lg-6">
											<input type="number" name="en_taller" id="en_taller" class="form-control" value="<?php echo $row_trucks['en_taller']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

										<div class="form-group">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Comentarios" >Comentarios:</label>
												<div class="col-sm-9">
                                                  <textarea name="comentarios" class="form-control" placeholder="Si aplica, ingresa comentarios."><?php echo htmlentities($row_trucks['comentarios'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
										</div>


                                    </div>

                                    <div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        	<input type="submit" class="btn btn-primary" value="Capturar">
                                    </div>
								</form>
                                
