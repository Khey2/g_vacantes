<?php require_once('Connections/vacantes.php'); 
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

$IDempleado = $_GET['IDempleado'];	
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea,  prod_activos.IDpuesto, prod_activos.IDaplica_SED, pc_semaforo.IDplan, pc_semaforo.IDpuestoPC, pc_semaforo.fecha_licencia, pc_semaforo.observaciones, pc_semaforo.reqa, pc_semaforo.reqb, pc_semaforo.reqc, pc_semaforo.reqd, pc_semaforo.reqe, pc_semaforo.reqf, pc_semaforo.estatus FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'"; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
$el_puest = $row_detalle['IDpuesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_kpis_n = "SELECT * FROM pc_kpis WHERE pc_kpis.IDpuesto = '$el_puest' AND pc_kpis.tipo = 1 GROUP BY pc_kpis.IDreq"; 
mysql_query("SET NAMES 'utf8'");
$kpis_n = mysql_query($query_kpis_n, $vacantes) or die(mysql_error());
$row_kpis_n = mysql_fetch_assoc($kpis_n);
$totalRows_kpis_n = mysql_num_rows($kpis_n);
?>

                            <p><strong>No. Emp.:</strong> <?php echo $row_detalle['IDempleado']; ?></p>												
                            <p><strong>Nombre:</strong> <?php echo $row_detalle['emp_paterno']; ?> <?php echo $row_detalle['emp_materno']; ?> <?php echo $row_detalle['emp_nombre'];?></p>												
                            <p><strong>Puesto:</strong> <?php echo $row_detalle['denominacion']; ?></p>												

                            <?php if ($row_detalle['IDplan'] != '') { ?>

            					<form method="post" name="form1" action="plan_carrera_inv.php?IDempleado=<?php echo $row_detalle['IDempleado']; ?>" 
                                class="form-horizontal form-validate-jquery">
                                
									<div class="modal-body">
                                    
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_detalle['IDempleado']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_detalle['IDpuesto']; ?>" >
                                    
                                   <?php do {  $IDreq = $row_kpis_n['IDreq'];
									$query_kpis = "SELECT * FROM pc_kpis WHERE pc_kpis.IDpuesto = '$el_puest' AND pc_kpis.tipo = 2 AND pc_kpis.Idreq = '$IDreq'"; 
									$kpis = mysql_query($query_kpis, $vacantes) or die(mysql_error());
									$row_kpis = mysql_fetch_assoc($kpis);
									?>
                                    
                                    <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" ><?php echo $row_kpis_n['descripcion']; ?><span class="text-danger">*</span></label>
											<div class="col-sm-7">
											<select name="<?php echo $row_kpis['IDreq']?>" class="form-control" required="required">
                                               <option value="" <?php if (!(strcmp($row_detalle[$IDreq],  ''))) {echo "SELECTED";} ?>>No establecido</option>
					                            <?php do { ?>
				                               <option value="<?php echo $row_kpis['valor']?>"<?php if (!(strcmp($row_kpis['valor'], $row_detalle[$IDreq]))) {echo "selected=\"selected\"";} ?>>
                                                <?php echo $row_kpis['descripcion']?></option>
												   <?php
                                                  } while ($row_kpis = mysql_fetch_assoc($kpis));
                                                  $rows = mysql_num_rows($kpis);
                                                  if($rows > 0) {
                                                      mysql_data_seek($kpis, 0);
                                                      $row_kpis = mysql_fetch_assoc($kpis);
                                                  } ?>
    									    </select>
                                          </div>
	                                    </div>
 									</div>
                                    
                                  <?php } while ($row_kpis_n = mysql_fetch_assoc($kpis_n)); ?>
                                    

                                  <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" >Fecha de Expedición Licencia</label>
											<div class="col-sm-7">
                                            <input type="date" name="fecha_licencia" class="form-control" id="fecha_licencia" value="<?php echo $row_detalle['fecha_licencia'];?>">
                                          </div>
	                                    </div>
 									</div>
                                    


                                  <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" >¿Le interesa ser promovido?<span class="text-danger">*</span></label>
											<div class="col-sm-7">
											<select name="reqe" class="form-control" required="required">
                                                <option value="" <?php if (!(strcmp($row_detalle['reqe'],  ''))) {echo "SELECTED";} ?>>No establecido</option>
                                                <option value="0" <?php if (!(strcmp($row_detalle['reqe'], '0'))) {echo "SELECTED";} ?>>No le interesa</option>
                                                <option value="1" <?php if (!(strcmp($row_detalle['reqe'], '1'))) {echo "SELECTED";} ?>>Si le interesa</option>
    									    </select>
                                          </div>
	                                    </div>
 									</div>
                                    
                                     <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" >¿Que tipo de unidad le interesa?<span class="text-danger">*</span></label>
											<div class="col-sm-7">
											<select name="IDpuestoPC" class="form-control" required="required">
                                                <option value="" <?php if (!(strcmp($row_detalle['IDpuestoPC'], ''))) {echo "SELECTED";} ?>>No aplica</option>
                                                <option value="42" <?php if (!(strcmp($row_detalle['IDpuestoPC'], '42'))) {echo "SELECTED";} ?>>Chofer Camioneta</option>
                                                <option value="43" <?php if (!(strcmp($row_detalle['IDpuestoPC'], '43'))) {echo "SELECTED";} ?>>Chofer Rabón</option>
                                                <option value="44" <?php if (!(strcmp($row_detalle['IDpuestoPC'], '44'))) {echo "SELECTED";} ?>>Chofer Torton</option>
                                                <option value="45" <?php if (!(strcmp($row_detalle['IDpuestoPC'], '44'))) {echo "SELECTED";} ?>>Chofer Trailer</option>
                                                <option value="99" <?php if (!(strcmp($row_detalle['IDpuestoPC'], '99'))) {echo "SELECTED";} ?>>Otro (espeficifar en comentarios)</option>
    									    </select>
                                          </div>
	                                    </div>
 									</div>
                                    
                                     <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" >¿Le interesaría cambiar de Sucusal?</label>
											<div class="col-sm-7">
											<select name="reqf" class="form-control">
                                                <option value="" <?php if (!(strcmp($row_detalle['reqf'],  ''))) {echo "SELECTED";} ?>>No establecido</option>
                                                <option value="0" <?php if (!(strcmp($row_detalle['reqf'], '0'))) {echo "SELECTED";} ?>>No le interesa</option>
                                                <option value="1" <?php if (!(strcmp($row_detalle['reqf'], '1'))) {echo "SELECTED";} ?>>Si le interesa</option>
    									    </select>
                                          </div>
	                                    </div>
 									</div>


										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-5">Observaciones:</label>
												<div class="col-sm-7">
                                                  <textarea name="observaciones" class="form-control" placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['observaciones'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

                                       <p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        	<input type="submit" class="btn btn-primary" value="Capturar">
                                    </div>
                                    
                                    </div>
								</form>

                            <?php } else { ?>   
                                
            					<form method="post" name="form1" action="plan_carrera_inv.php?IDempleado=<?php echo $row_detalle['IDempleado']; ?>" 
                                class="form-horizontal form-validate-jquery">
                                
									<div class="modal-body">
                                	<input type="hidden" name="MM_insert" value="form1" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_detalle['IDempleado']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_detalle['IDpuesto']; ?>" >
                                    <input type="hidden" name="estatus" value="1" >
                                    
                                    
                                   <?php do {  $IDreq = $row_kpis_n['IDreq'];
									$query_kpis = "SELECT * FROM pc_kpis WHERE pc_kpis.IDpuesto = '$el_puest' AND pc_kpis.tipo = 2 AND pc_kpis.Idreq = '$IDreq'"; 
									$kpis = mysql_query($query_kpis, $vacantes) or die(mysql_error());
									$row_kpis = mysql_fetch_assoc($kpis);
									?>

                                    <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" ><?php echo $row_kpis_n['descripcion']; ?><span class="text-danger">*</span></label>
											<div class="col-sm-7">
											<select name="<?php echo $row_kpis['IDreq']?>" class="form-control" required="required">
                                               <option value="">No establecido</option>
					                            <?php do { ?>
				                               <option value="<?php echo $row_kpis['valor']?>"><?php echo $row_kpis['descripcion']?></option>
												   <?php
                                                  } while ($row_kpis = mysql_fetch_assoc($kpis));
                                                  $rows = mysql_num_rows($kpis);
                                                  if($rows > 0) {
                                                      mysql_data_seek($kpis, 0);
                                                      $row_kpis = mysql_fetch_assoc($kpis);
                                                  } ?>
    									    </select>
                                          </div>
	                                    </div>
 									</div>
                                    
                                  <?php } while ($row_kpis_n = mysql_fetch_assoc($kpis_n)); ?>
                                    
								  <div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-5">Fecha de Expedición Licencia</label>
												<div class="col-sm-7">
                                                <input type="date" name="fecha_licencia" class="form-control" id="fecha_licencia">
												</div>
											</div>
										</div>

                                    <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" >¿Le interesa ser promovido?<span class="text-danger">*</span></label>
											<div class="col-sm-7">
											<select name="reqe" class="form-control" required="required">
                                                <option value="">No establecido</option>
                                                <option value="0">No le interesa</option>
                                                <option value="1">Si le interesa</option>
    									    </select>
                                          </div>
	                                    </div>
 									</div>
                                    
                                     <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" >¿Que tipo de unidad le interesa?<span class="text-danger">*</span></label>
											<div class="col-sm-7">
											<select name="IDpuestoPC" class="form-control" required="required">
                                                <option value="42">Chofer Camioneta</option>
                                                <option value="43">Chofer Rabón</option>
                                                <option value="44">Chofer Torton</option>
                                                <option value="45">Chofer Trailer</option>
                                                <option value="99">Otro (especificar en comentarios)</option>
    									    </select>
                                          </div>
	                                    </div>
 									</div>
                                    

                                    <div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-5" >¿Le interesa cambiar de Sucursal?</label>
											<div class="col-sm-7">
											<select name="reqf" class="form-control">
                                                <option value="">No establecido</option>
                                                <option value="0">No le interesa</option>
                                                <option value="1">Si le interesa</option>
    									    </select>
                                          </div>
	                                    </div>
 									</div>
                                    

                                        <div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-5">Observaciones</label>
												<div class="col-sm-7">
                                                  <textarea name="observaciones" class="form-control" placeholder="Indicar informacion relevante." ></textarea>
												</div>
											</div>
										</div>


                                        <p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                       		<input type="submit" class="btn btn-primary" value="Capturar">
                                    </div>
                                    </div>

								</form>
                            <?php } ?>   