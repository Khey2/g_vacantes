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

$IDempleado = $_GET['IDempleado'];	

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT *  FROM prod_activos WHERE prod_activos.IDempleado = '$IDempleado'";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_turnos = "SELECT *  FROM vac_turnos_t";
$turnos = mysql_query($query_turnos, $vacantes) or die(mysql_error());
$row_turnos = mysql_fetch_assoc($turnos);
$totalRows_turnos = mysql_num_rows($turnos);


?>
                            <?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?>                            


            					<form method="post" class="form-horizontal form-validate-jquery" name="form2" action="productividad_captura_puesto_t.php?IDpuesto=<?php echo $row_puestos['IDpuesto']; ?>" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_update" value="form2">                                   
                                	<input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">                                   


									<div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="TURNO ASIGNADOI">TURNO ASIGNADO:</label>
											<div class="col-sm-9">
											<div class="checkbox">
											<select name="IDturno" id="IDturno" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													<?php do {  ?>
													<option value="<?php echo $row_turnos['IDturno']?>"<?php if (!(strcmp($row_puestos['IDturno'], $row_turnos['IDturno']))) {echo "SELECTED";} ?>><?php echo $row_turnos['turno']?></option>
													<?php
													} while ($row_turnos = mysql_fetch_assoc($turnos));
													$rows = mysql_num_rows($turnos);
													if($rows > 0) {
													mysql_data_seek($turnos, 0);
													$row_turnos = mysql_fetch_assoc($turnos);
													} ?>
                                            </select>
										    </div>
                                          </div>
	                                    </div>
 									</div>
                                        
								
                                    </div>


									<p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        	<input type="submit" class="btn btn-primary" value="Guardar">


                                    </div>
								</form>
                                
