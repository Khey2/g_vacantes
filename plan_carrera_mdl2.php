<?php require_once('Connections/vacantes.php'); 
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

$IDempleado = $_GET['IDempleado'];	
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea,  prod_activos.IDpuesto, prod_activos.IDaplica_SED, pc_semaforo.IDplan,  c_capa1, c_capa2, c_capa3, a_discprog_c, a_puntyasist_c, a_desemp_c, a_antig_c, b_puesto_c, c_capa1_c, c_capa2_c, c_capa3_c, a_discprog, a_puntyasist, a_desemp, a_antig, b_puesto, estatus_pc FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'"; 
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
                            <p><strong>Puesto para Pormoción: </strong>CHOFER</p>												


            					<form method="post" name="form1" action="plan_carrera_carr.php?IDempleado=<?php echo $row_detalle['IDempleado']; ?>" 
                                class="form-horizontal form-validate-jquery">
                                
									<div class="modal-body">
                                    
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_detalle['IDempleado']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_detalle['IDpuesto']; ?>" >
                                    <input type="hidden" name="estatus_pc" value="1" >

                            <p><strong>Requisitos de Política:</strong></p>												

                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Disciplina Progresiva.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											No contar con ningún proceso de disciplina progresiva en el ultimo año.
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="a_discprog" id="a_discprog" value=""<?php if ($row_detalle['a_discprog'] == 1) {echo "checked='checked'";} ?>> </label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="a_discprog_c" class="form-control" placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['a_discprog_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                          </div>
	                               </div>
 							</div>

                                    
                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Puntualidad y asistencia.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											No presentar más de tres faltas en un periodo de un año.
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="a_puntyasist" id="a_puntyasist" value=""<?php if ($row_detalle['a_puntyasist'] == 1) {echo "checked='checked'";} ?>> </label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="a_puntyasist_c" class="form-control" placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['a_puntyasist_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                          </div>
	                               </div>
 							</div>

                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Desempeño.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											Contar con al menos dos meses continuos de buen desempeño según indicadores de productividad.
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="a_desemp" id="a_desemp" value=""<?php if ($row_detalle['a_desemp'] == 1) {echo "checked='checked'";} ?>> </label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="a_desemp_c" class="form-control"  placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['a_desemp_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                          </div>
	                               </div>
 							</div>

                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Antigüedad.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											Contar con al menos seis meses de antigüedad en la empresa y tres meses en la posición actual.
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="a_antig" id="a_antig" value=""<?php if ($row_detalle['a_antig'] == 1) {echo "checked='checked'";} ?>> </label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="a_antig_c" class="form-control"  placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['a_antig_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                          </div>
	                               </div>
 							</div>
                            
                            
                         <p><strong>Requisitos del Puesto:</strong></p>												

                            
                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Licencia de Manejo.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											Cuenta con la Licencia de Manejo necesaria para la promoción.<?php echo $row_detalle['b_puesto']; ?>
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="b_puesto" id="b_puesto" value=""<?php if ($row_detalle['b_puesto'] == 1) {echo "checked='checked'";} ?>> </label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="b_puesto_c" class="form-control"  placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['b_puesto_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                          </div>
	                               </div>
 							</div>
                            
                            
                    	<p><strong>Requisitos de Capacitación:</strong></p>												

                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Capacitación - Evaluación.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											Cursos: Prueba de Manejo.
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="c_capa3" id="c_capa3" value=""<?php if ($row_detalle['c_capa3'] == 1) {echo "checked='checked'";} ?>></label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="c_capa3_c" class="form-control"  placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['c_capa3_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                          </div>
	                               </div>
 							</div>
                            
                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Capacitación Teórica.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											Cursos: Operador Experto e Inducción al Puesto.
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="c_capa1" id="c_capa1" value=""<?php if ($row_detalle['c_capa1'] == 1) {echo "checked='checked'";} ?>> </label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="c_capa1_c" class="form-control"  placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['c_capa1_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                          </div>
	                               </div>
 							</div>

                                <div class="form-group">
                                     <div class="row">
											<label class="control-label col-sm-2" ><strong>Capacitación Práctica.</strong></label>
										<div class="col-sm-4">
											<div class="checkbox" align="left">
											Cursos: Práctiva Visual y Entrenamiento.
											</div>
                                       </div>
										<div class="col-sm-1">
											<label><input type="checkbox" class="styled" name="c_capa2" id="c_capa2" value=""<?php if ($row_detalle['c_capa2'] == 1) {echo "checked='checked'";} ?>> </label>
                                       </div>
											<div class="col-sm-4">
											<textarea name="c_capa2_c" class="form-control"  placeholder="Indicar informacion relevante." ><?php echo htmlentities($row_detalle['c_capa2_c'], ENT_COMPAT, 'utf-8'); ?></textarea>
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