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
$IDempleado = $_GET['IDempleado'];	

$mi_fecha =  date('Y/m/d');
$el_mes = 11;
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.emp_paterno, prod_activosfaltas.emp_materno, prod_activosfaltas.emp_nombre, prod_activosfaltas.rfc, prod_activosfaltas.fecha_alta, prod_activosfaltas.descripcion_nomina, prod_activosfaltas.denominacion, prod_activosfaltas.IDmatriz, prod_activosfaltas.IDpuesto, prod_activosfaltas.IDarea, vac_areas.area, ind_asistencia.IDasistencia, ind_asistencia.IDestatus, ind_asistencia.IDruta, ind_asistencia.IDtipo, ind_asistencia.comentarios  FROM prod_activosfaltas LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activosfaltas.IDarea LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado AND ind_asistencia.IDfecha = $IDfecha WHERE prod_activosfaltas.IDempleado = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

$query_tipos = "SELECT * FROM ind_asistencia_tipos Order by IDtipo";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);


$IDpuesto = $row_activos['IDpuesto'];
$IDarea = $row_activos['IDarea'];
$IDcapturador = $el_usuario;

?>
							<div class="modal-body">
                                                        

<?php if ($row_activos['IDestatus'] != '') { ?>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="asistencias_editA.php?IDfecha=<?php echo $IDfecha; ?>&mes=11&IDmatriz=<?php echo $IDmatriz; ?>" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $anio; ?>" >
                                    <input type="hidden" name="mes" value="<?php echo $el_mes; ?>" >
                                    <input type="hidden" name="IDfecha" value="<?php echo $IDfecha; ?>" >
                                    <input type="hidden" name="IDcapturador" value="<?php echo $IDcapturador; ?>" >
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Nombre:</label>
											<div class="col-sm-9">
											<b><?php echo $row_activos['emp_paterno']; ?> <?php echo $row_activos['emp_materno']; ?> <?php echo $row_activos['emp_nombre']; ?></b>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->
									
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Asistencia:<span class="text-danger">*</span></label>
											<div class="col-sm-9">
											<select name="IDestatus" class="form-control" required="required">
												<option value="1" <?php if ($row_activos['IDestatus'] == 1) {echo "SELECTED";} ?>>Si se presentó</option> 
                                                <option value="0" <?php if ($row_activos['IDestatus'] == 0) {echo "SELECTED";} ?>>No se presentó</option>
                                              </select>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Salió a Ruta:<span class="text-danger">*</span></label>
											<div class="col-sm-9">
											<select name="IDruta" class="form-control" required="required">
                                                <option value="0" <?php if ($row_activos['IDruta'] == 0) {echo "SELECTED";} ?>>No salió</option>
												<option value="1" <?php if ($row_activos['IDruta'] == 1) {echo "SELECTED";} ?>>Si salió</option> 
                                              </select>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo de Ausencia (si aplica):</label>
												<div class="col-sm-9">
											<select name="IDtipo" class="form-control" required="required">
												  <?php  do { ?>
												  <option value="<?php echo $row_tipos['IDtipo']?>" <?php if ($row_tipos['IDtipo'] == $row_activos['IDtipo']) {echo "SELECTED";} ?> ><?php echo $row_tipos['tipo']?></option>
												  <?php
												 } while ($row_tipos = mysql_fetch_assoc($tipos));
												   $rows = mysql_num_rows($tipos);
												   if($rows > 0) {
												   mysql_data_seek($tipos, 0);
												   $row_tipos = mysql_fetch_assoc($tipos);
												 } ?>
                                              </select>
												</div>
											</div>
	                                    </div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Comentarios" >Comentarios:</label>
												<div class="col-sm-9">
                                                  <textarea name="comentarios" class="form-control" placeholder="Si aplica, ingresa comentarios respecto de la ausencia."><?php echo htmlentities($row_activos['comentarios'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

                                    </div>

                                    <div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
								</form>
                                
<?php } else { ?>


            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="asistencias_editA.php?IDfecha=<?php echo $IDfecha; ?>&mes=11&IDmatriz=<?php echo $IDmatriz; ?>" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $anio; ?>" >
                                    <input type="hidden" name="mes" value="<?php echo $el_mes; ?>" >
                                    <input type="hidden" name="IDfecha" value="<?php echo $IDfecha; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $IDmatriz; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $IDpuesto; ?>" >
                                    <input type="hidden" name="IDarea" value="<?php echo $IDarea; ?>" >
                                    <input type="hidden" name="IDcapturador" value="<?php echo $IDcapturador; ?>" >
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Nombre:</label>
											<div class="col-sm-9">
											<b><?php echo $row_activos['emp_paterno']; ?> <?php echo $row_activos['emp_materno']; ?> <?php echo $row_activos['emp_nombre']; ?></b>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

									
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Asistencia:<span class="text-danger">*</span></label>
											<div class="col-sm-9">
											<select name="IDestatus" class="form-control" required="required">
												<option value="1">Si se presentó</option> 
                                                <option value="0">No se presentó</option>
                                              </select>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Salió a Ruta:<span class="text-danger">*</span></label>
											<div class="col-sm-9">
											<select name="IDruta" class="form-control" required="required">
                                                <option value="0">No salió</option>
												<option value="1">Si salió</option> 
                                              </select>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo de Ausencia (si aplica):</label>
												<div class="col-sm-9">
											<select name="IDtipo" class="form-control" required="required">
												  <?php  do { ?>
												  <option value="<?php echo $row_tipos['IDtipo']?>"><?php echo $row_tipos['tipo']?></option>
												  <?php
												 } while ($row_tipos = mysql_fetch_assoc($tipos));
												   $rows = mysql_num_rows($tipos);
												   if($rows > 0) {
												   mysql_data_seek($tipos, 0);
												   $row_tipos = mysql_fetch_assoc($tipos);
												 } ?>
                                              </select>
												</div>
											</div>
	                                    </div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Comentarios" >Comentarios:</label>
												<div class="col-sm-9">
                                                  <textarea name="comentarios" class="form-control" placeholder="Si aplica, ingresa comentarios respecto de la ausencia."></textarea>
												</div>
											</div>
										</div>

                                    </div>

                                    <div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
								</form>


<?php } ?>
