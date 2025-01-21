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
$el_mes = $_GET['mes'];
$el_anio = $_GET['anio'];
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.emp_paterno, prod_activosfaltas.emp_materno, prod_activosfaltas.emp_nombre, prod_activosfaltas.fecha_alta, prod_activosfaltas.descripcion_nomina, prod_activosfaltas.denominacion, prod_activosfaltas.IDmatriz, prod_activosfaltas.IDpuesto, prod_activosfaltas.IDarea, vac_areas.area, ind_asistencia.IDasistencia, ind_asistencia.IDestatus, ind_asistencia.IDruta, ind_asistencia.IDtipo, ind_asistencia.IDtipov, ind_asistencia.comentarios  FROM prod_activosfaltas LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activosfaltas.IDarea LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado AND ind_asistencia.IDfecha = '$IDfecha' AND ind_asistencia.mes = '$el_mes' AND ind_asistencia.anio = '$el_anio' WHERE prod_activosfaltas.IDempleado = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos2 = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

$query_tipos = "SELECT * FROM ind_asistencia_tipos Order by IDtipo ASC";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos); 

$eltipo = $row_activos2['IDtipo'];
$eltipov = $row_activos2['IDtipov'];
$query_tiposr = "SELECT * FROM ind_asistencia_tipos WHERE IDtipo = '$eltipo'";
$tiposr = mysql_query($query_tiposr, $vacantes) or die(mysql_error());
$row_tiposr = mysql_fetch_assoc($tiposr);
$totalRows_tiposr = mysql_num_rows($tiposr);

$reportado = $row_tiposr['tipo'];
$IDpuesto = $row_activos2['IDpuesto'];
$IDarea = $row_activos2['IDarea'];
$IDvalidador = $el_usuario;
$emp_paterno = $row_activos2['emp_paterno'];
$emp_materno = $row_activos2['emp_materno'];
$emp_nombre = $row_activos2['emp_nombre'];
$denominacion = $row_activos2['denominacion'];

?>
<div class="modal-body">
                                                        
<?php if ($row_activos2['IDtipo'] == '' AND $row_activos2['IDtipov'] == '') { ?>


	<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="asistencias_editB.php?anio=<?php echo $el_anio?>&mes=<?php echo $el_mes ?>&IDfecha=<?php echo $IDfecha?>" >
									<div class="modal-body">
                                	<input type="hidden" name="IDruta" value="9">
                                	<input type="hidden" name="IDestatus" value="9">
                                	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $el_anio; ?>" >
                                    <input type="hidden" name="mes" value="<?php echo $el_mes; ?>" >
                                    <input type="hidden" name="IDfecha" value="<?php echo $IDfecha; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $IDmatriz; ?>" >
                                    <input type="hidden" name="emp_paterno" value="<?php echo $emp_paterno; ?>" >
                                    <input type="hidden" name="emp_materno" value="<?php echo $emp_materno; ?>" >
                                    <input type="hidden" name="emp_nombre" value="<?php echo $emp_nombre; ?>" >
                                    <input type="hidden" name="denominacion" value="<?php echo $denominacion; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $IDpuesto; ?>" >
                                    <input type="hidden" name="IDarea" value="<?php echo $IDarea; ?>" >
                                    <input type="hidden" name="IDvalidador" value="<?php echo $IDvalidador; ?>" >
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Nombre: insert</label>
											<div class="col-sm-9">
											<b><?php echo $row_activos2['emp_paterno']; ?> <?php echo $row_activos2['emp_materno']; ?> <?php echo $row_activos2['emp_nombre']; ?></b>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

								
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Estatus Capturado:</label>
												<div class="col-sm-9">
													<?php if ($reportado != '') {echo $reportado;} else { echo "Sin captura";} ?>										
												</div>
												</div>
											</div>
								
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Estatus Validado:</label>
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
											<input type="hidden" name="IDruta" value="9">
											<input type="hidden" name="IDestatus" value="9">
                                        	<input type="submit" class="btn btn-info" value="Guardar">
                                    </div>
								</form>
                                
<?php } else { ?>


	<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="asistencias_editB.php?anio=<?php echo $el_anio?>&mes=<?php echo $el_mes ?>&IDfecha=<?php echo $IDfecha?>" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $el_anio; ?>" >
                                    <input type="hidden" name="mes" value="<?php echo $el_mes; ?>" >
                                    <input type="hidden" name="IDfecha" value="<?php echo $IDfecha; ?>" >
                                    <input type="hidden" name="IDvalidador" value="<?php echo $IDvalidador; ?>" >
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Nombre: upd:</label>
											<div class="col-sm-9">
											<b><?php echo $row_activos2['emp_paterno']; ?> <?php echo $row_activos2['emp_materno']; ?> <?php echo $row_activos2['emp_nombre']; ?></b>
                                             </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->
									
								
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Estatus Capturado:</label>
												<div class="col-sm-9">
													<?php if ($reportado != '') {echo $reportado;} else { echo "Sin captura";} ?>										
												</div>
												</div>
											</div>
								


								<?php if ($eltipo == '') {?>			
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Estatus Validado:</label>
												<div class="col-sm-9">
												<select name="IDtipo" class="form-control" required="required">
												  <?php  do { ?>
												  <option value="<?php echo $row_tipos['IDtipo']?>" <?php if ($row_tipos['IDtipo'] == $eltipov) {echo "SELECTED";} ?> ><?php echo $row_tipos['tipo']?></option>
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

								<?php } else {?>			

									<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Estatus Validado:</label>
												<div class="col-sm-9">
												<select name="IDtipo" class="form-control" required="required">
												  <?php  do { ?>
												  <option value="<?php echo $row_tipos['IDtipo']?>" <?php if ($row_tipos['IDtipo'] == $eltipo) {echo "SELECTED";} ?> ><?php echo $row_tipos['tipo']?></option>
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
											
								<?php } ?>			
		

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Comentarios" >Comentarios:</label>
												<div class="col-sm-9">
                                                  <textarea name="comentarios" class="form-control" placeholder="Si aplica, ingresa comentarios respecto de la ausencia."><?php echo htmlentities($row_activos2['comentarios'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

                                    </div>

                                    <div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        	<input type="submit" class="btn btn-primary" value="Guardar">
                                    </div>
								</form>


<?php } ?>
