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
$el_puesto = $_GET['IDpuesto'];	
$IDmatriz  = $_GET['IDmatriz'];	
$IDsucursal  = $_GET['IDsucursal'];	

//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = $_GET['semana']; //la semana empieza ayer 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


//echo "Empleado:". $IDempleado;	
//echo ", Puesto: ". $el_puesto;	
//echo ", Matriz: ". $IDmatriz;	
//echo ", Semana: ". $semana;	

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_activos.fecha_antiguedad, prod_captura.horas_extra, prod_activos.IDempleado, prod_activos.IDturno, prod_activos.emp_paterno, prod_activos.emp_materno,  prod_activos.denominacion, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.adicional, prod_captura.adicional2, prod_captura.semana, prod_captura.capturador, prod_captura.observaciones, prod_captura.fecha_captura, prod_captura.reci, prod_captura.carg, prod_captura.esti, prod_captura.dist, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.a8, prod_captura.a9, prod_captura.a10, prod_captura.a11, prod_captura.a12, prod_captura.a13, prod_captura.a14, prod_captura.a15, prod_captura.a16, prod_captura.a17, prod_captura.a18, prod_captura.a21,  prod_captura.a19, prod_captura.a20, prod_captura.a22, prod_captura.a23, prod_captura.a24, prod_captura.a25, prod_captura.a26, prod_captura.a27, prod_captura.a28, prod_captura.validador, prod_captura.validadorrh, prod_captura.autorizador, prod_captura.lun_g, prod_captura.mar_g, prod_captura.mie_g, prod_captura.jue_g, prod_captura.vie_g, prod_captura.sab_g FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio' WHERE prod_activos.IDempleado = '$IDempleado'";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$date1 = new DateTime($row_puestos['fecha_antiguedad']);
$date2 = new DateTime($fecha);
$diff = $date1->diff($date2);
$antiguedad =  (($diff->format('%y') * 12) + $diff->format('%m'));
mysql_select_db($database_vacantes, $vacantes);
$query_montos_cajas = "SELECT * FROM prod_valor_cajas WHERE IDmatriz = $IDmatriz AND $antiguedad >= meses_inicio AND $antiguedad <= meses_final";
$montos_cajas = mysql_query($query_montos_cajas, $vacantes) or die(mysql_error());
$row_montos_cajas = mysql_fetch_assoc($montos_cajas);
$totalRows_montos_cajas = mysql_num_rows($montos_cajas);

$valor_estiba = ($row_montos_cajas['estiba']/ 10)."c" ;
$valor_carga = ($row_montos_cajas['carga']/ 10)."c" ;
$valor_recibo = ($row_montos_cajas['recibo']/ 10)."c" ;
$valor_distribucion = ($row_montos_cajas['distribucion']/ 10)."c" ;


?>
                            <?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre'];?>													

                            <?php if ($row_puestos['capturador'] != '') { ?>


            					<form method="post" name="form1<?php echo $row_puestos['IDempleado']; ?>" action="productividad_captura_puesto_a_t.php?IDpuesto=<?php echo $row_puestos['IDpuesto']; ?>" 
                                class="form-horizontal form-validate-jquery">
									<div class="modal-body">
                                    
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDcaptura" value="<?php echo $row_puestos['IDcaptura']; ?>">                
                                    <input type="hidden" name="capturador" value="<?php echo $row_usuario['IDusuario']; ?>" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_puestos['IDempleado']; ?>" >
                                    <input type="hidden" name="IDturno" value="<?php echo $row_puestos['IDturno']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_puestos['IDpuesto']; ?>" >
                                    <input type="hidden" name="emp_paterno" value="<?php echo $row_puestos['emp_paterno']; ?>" >
                                    <input type="hidden" name="emp_materno" value="<?php echo $row_puestos['emp_materno']; ?>" >
                                    <input type="hidden" name="emp_nombre" value="<?php echo $row_puestos['emp_nombre']; ?>" >
                                    <input type="hidden" name="denominacion" value="<?php echo $row_puestos['denominacion']; ?>" >
                                    <input type="hidden" name="sueldo_total" value="<?php echo $row_puestos['sueldo_total_productividad']; ?>" >
                                    <input type="hidden" name="fecha_captura" value="<?php echo date('Y/m/d'); ?>" >
                                    <input type="hidden" name="semana" value="<?php echo $semana; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $anio; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $row_puestos['IDmatriz']; ?>" >
                                    <input type="hidden" name="IDsucursal" value="<?php echo $row_puestos['IDsucursal']; ?>" >
                                    <input type="hidden" name="IDarea" value="<?php echo $row_puestos['IDarea']; ?>" >                                   
                                    <input type="hidden" name="adicional" value="0" >                                   

                                    <!-- Basic single checkbox -->
									<div class="form-group">
									 <div class="row">
										<label class="control-label col-sm-2" data-popup="tooltip-custom" title="REPORTE DIARIO DE ASISTENCIA">ASISTENCIA </br>(pierde bono asistencia)</label>
											<div class="col-sm-1">
											<div class="checkbox">
												<label>
													<input type="checkbox" name="lun" class="control-primary" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['lun'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />
													Lun
												</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="mar" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['mar'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />
													Mar
												</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
                                            	<label>
													<input type="checkbox" name="mie" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['mie'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />
													Mie
												</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="jue" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['jue'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />
													Jue
												</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="vie" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['vie'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />
													Vie
												</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="sab" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['sab'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />
													Sab
												</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
													<input type="hidden" name="dom" value="1"/>
                                           </div>
                                         </div>
									 </div>
                                     <!-- /basic singlecheckbox -->
                                            

									 <div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA LUNES">LUNES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a1" value="<?php echo htmlentities($row_puestos['a1'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a2" value="<?php echo htmlentities($row_puestos['a2'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a3" value="<?php echo htmlentities($row_puestos['a3'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a4" value="<?php echo htmlentities($row_puestos['a4'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											<div class="col-sm-1">
													Garantizado<input type="checkbox" name="lun_g" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['lun_g'], ENT_COMPAT, 'utf-8'),"1"))) {
														echo "checked=\"checked\"";} ?> />
                                          </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA MARTES">MARTES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a5" value="<?php echo htmlentities($row_puestos['a5'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a6" value="<?php echo htmlentities($row_puestos['a6'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a7" value="<?php echo htmlentities($row_puestos['a7'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a8" value="<?php echo htmlentities($row_puestos['a8'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											<div class="col-sm-1">
													Garantizado<input type="checkbox" name="mar_g" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['mar_g'], ENT_COMPAT, 'utf-8'),"1"))) {
														echo "checked=\"checked\"";} ?> />
                                          </div>
											</div>
										</div>
											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA MIERCOLES">MIERCOLES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a9" value="<?php echo htmlentities($row_puestos['a9'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a10" value="<?php echo htmlentities($row_puestos['a10'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a11" value="<?php echo htmlentities($row_puestos['a11'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a12" value="<?php echo htmlentities($row_puestos['a12'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											<div class="col-sm-1">
													Garantizado<input type="checkbox" name="mie_g" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['mie_g'], ENT_COMPAT, 'utf-8'),"1"))) {
														echo "checked=\"checked\"";} ?> />
                                          </div>
											</div>
										</div>
											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA JUEVES">JUEVES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a13" value="<?php echo htmlentities($row_puestos['a13'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a14" value="<?php echo htmlentities($row_puestos['a14'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a15" value="<?php echo htmlentities($row_puestos['a15'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a16" value="<?php echo htmlentities($row_puestos['a16'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											<div class="col-sm-1">
													Garantizado<input type="checkbox" name="jue_g" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['jue_g'], ENT_COMPAT, 'utf-8'),"1"))) {
														echo "checked=\"checked\"";} ?> />
                                          </div>
											</div>
										</div>
											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA VIERNES">VIERNES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a17" value="<?php echo htmlentities($row_puestos['a17'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a18" value="<?php echo htmlentities($row_puestos['a18'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a19" value="<?php echo htmlentities($row_puestos['a19'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a20" value="<?php echo htmlentities($row_puestos['a20'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											<div class="col-sm-1">
													Garantizado<input type="checkbox" name="vie_g" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['vie_g'], ENT_COMPAT, 'utf-8'),"1"))) {
														echo "checked=\"checked\"";} ?> />
                                          </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA SABADO">SABADO</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a21" value="<?php echo htmlentities($row_puestos['a21'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a22" value="<?php echo htmlentities($row_puestos['a22'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a23" value="<?php echo htmlentities($row_puestos['a23'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a24" value="<?php echo htmlentities($row_puestos['a24'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											<div class="col-sm-1">
													Garantizado<input type="checkbox" name="sab_g" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['sab_g'], ENT_COMPAT, 'utf-8'),"1"))) {
														echo "checked=\"checked\"";} ?> />
                                          </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA DOMINGO">DOMINGO</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a25" value="<?php echo htmlentities($row_puestos['a25'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a26" value="<?php echo htmlentities($row_puestos['a26'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a27" value="<?php echo htmlentities($row_puestos['a27'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a28" value="<?php echo htmlentities($row_puestos['a28'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											</div>
										</div>

										<div class="form-group">
		 					 <div class="row">
											<label class="control-label col-sm-2" data-popup="tooltip-custom" title="PAGO SEMANAL GARANTIZADO">GARANTIZADO</label>
									<div class="col-sm-4">
											<select name="garantizado" class="form-control">
                                                <option value="" <?php if (!(strcmp($row_puestos['garantizado'],  ''))) {echo "SELECTED";} ?>>No aplica</option>
                                                <option value="1" <?php if (!(strcmp($row_puestos['garantizado'], '1'))) {echo "SELECTED";} ?>>Nuevo Ingreso</option>
                                                <option value="11" <?php if (!(strcmp($row_puestos['garantizado'], '11'))) {echo "SELECTED";} ?>>Tultitlan TVespertino</option>
                                                <option value="2" <?php if (!(strcmp($row_puestos['garantizado'], '2'))) {echo "SELECTED";} ?>>Funciones Administrativas</option>
                                                <option value="3" <?php if (!(strcmp($row_puestos['garantizado'], '3'))) {echo "SELECTED";} ?>>En proceso de promoci&oacute;n</option>
                                                <option value="4" <?php if (!(strcmp($row_puestos['garantizado'], '4'))) {echo "SELECTED";} ?>>Vacaciones</option>
                                                <option value="5" <?php if (!(strcmp($row_puestos['garantizado'], '5'))) {echo "SELECTED";} ?>>Incapacidad COVID</option>
                                                <option value="6" <?php if (!(strcmp($row_puestos['garantizado'], '6'))) {echo "SELECTED";} ?>>Permiso por Paternidad</option>
                                                <option value="7" <?php if (!(strcmp($row_puestos['garantizado'], '7'))) {echo "SELECTED";} ?>>Permiso por Defunci&oacute;n</option>
												<?php if($row_puestos['IDmatriz'] == 30 or $row_puestos['IDmatriz'] == 4 or $row_puestos['IDmatriz'] == 10) { ?>
                                                <option value="8" <?php if (!(strcmp($row_puestos['garantizado'], '8'))) {echo "SELECTED";} ?>>Tamemes-CEDA MX (40%)</option>
                                                <option value="10" <?php if (!(strcmp($row_puestos['garantizado'], '10'))) {echo "SELECTED";} ?>>Tamemes-CEDA MX (50%)</option>
												<?php } ?>
    									    </select>
									</div>

											<label class="control-label col-sm-2" data-popup="tooltip-custom" title="HORAS EXTRAS">HORAS EXTRAS</label>
									<div class="col-sm-3">
											<input type="number" name="horas_extra" max="4" value="<?php echo $row_puestos['horas_extra']; ?>" class="form-control" />
												<span class="help-block">Topado a 4, máximo 3 dias consecutivos.</span>
                                    </div>
							</div>
						</div>


                                    <div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="MONTO $ DE PAGO ADICIONAL">ADICIONAL ($)</label>
												<div class="col-sm-9">
                                                   	<input type="number" name="adicional" <?php if( $row_matriz['adicionales'] == 0) { echo "disabled='disabled'";} ?> maxlength="6" value="<?php echo htmlentities($row_puestos['adicional'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
														<span class="help-block">Recuerda que no se puede rebasar el presupuesto semanal.</span>
											</div>
										</div>



										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="OBSERVACIONES">OBSERVACIONES</label>
												<div class="col-sm-9">
                                                  <textarea name="observaciones" class="form-control"><?php echo htmlentities($row_puestos['observaciones'], ENT_COMPAT, 'utf-8'); ?></textarea>
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
                                
            					<form method="post" name="form1<?php echo $row_puestos['IDempleado']; ?>" action="productividad_captura_puesto_a_t.php?IDpuesto=<?php echo $row_puestos['IDpuesto']; ?>" 
                                class="form-horizontal form-validate-jquery">
									<div class="modal-body">
                                	<input type="hidden" name="MM_insert" value="form1" >
                                    <input type="hidden" name="capturador" value="<?php echo $row_usuario['IDusuario']; ?>" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_puestos['IDempleado']; ?>" >
                                    <input type="hidden" name="denominacion" value="<?php echo $row_puestos['denominacion']; ?>" >
                                    <input type="hidden" name="emp_paterno" value="<?php echo $row_puestos['emp_paterno']; ?>" >
                                    <input type="hidden" name="sueldo_total" value="<?php echo $row_puestos['sueldo_total_productividad']; ?>" >
                                    <input type="hidden" name="emp_materno" value="<?php echo $row_puestos['emp_materno']; ?>" >
                                    <input type="hidden" name="emp_nombre" value="<?php echo $row_puestos['emp_nombre']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_puestos['IDpuesto']; ?>">
                                    <input type="hidden" name="fecha_captura" value="<?php echo date('Y/m/d'); ?>" >
                                    <input type="hidden" name="semana" value="<?php echo $semana; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $anio; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $row_puestos['IDmatriz']; ?>" >
                                    <input type="hidden" name="IDsucursal" value="<?php echo $row_puestos['IDsucursal']; ?>" >
                                    <input type="hidden" name="IDarea" value="<?php echo $row_puestos['IDarea']; ?>" >                                   
                                    <input type="hidden" name="adicional" value="0" >                                   
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-2" data-popup="tooltip-custom" title="REPORTE DIARIO DE ASISTENCIA">ASISTENCIA </br>(pierde bono del dia que falta)</label>
											<div class="col-sm-1">
											<div class="checkbox">
												<label>
													<input type="checkbox" name="lun" value="1" checked="checked" />
													Lun
												</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="mar" value="1" checked="checked" />
													Mar
												</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
                                            	<label>
													<input type="checkbox" name="mie" value="1" checked="checked" />
													Mie
												</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="jue" value="1" checked="checked" />
													Jue
												</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="vie" value="1" checked="checked" />
													Vie
												</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label>
													<input type="checkbox" name="sab" value="1" checked="checked" />
													Sab
												</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
													<input type="hidden" name="dom" value="1"/>
                                           </div>
                                         </div>
									 </div><!-- /basic singlecheckbox -->
                                            
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA LUNES">LUNES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a1" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a2" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a3" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a4" value="" class="form-control" />
												</div>
                                               <div class="col-sm-1">
												<label>
													Garantizado <input type="checkbox" name="lun_g" value="1" />
												</label>
                                             </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA MARTES">MARTES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a5" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a6" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a7" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a8" value="" class="form-control" />
												</div>
                                               <div class="col-sm-1">
												<label>
													Garantizado <input type="checkbox" name="mar_g" value="1"/>
												</label>
                                             </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA MIERCOLES">MIERCOLES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a9" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a10" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a11" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a12" value="" class="form-control" />
												</div>
                                               <div class="col-sm-1">
												<label>
													Garantizado <input type="checkbox" name="mie_g" value="1"/>
												</label>
                                             </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA JUEVES">JUEVES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a13" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a14" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a15" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a16" value="" class="form-control" />
												</div>
                                               <div class="col-sm-1">
												<label>
													Garantizado <input type="checkbox" name="jue_g" value="1" />
												</label>
                                             </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA VIERNES">VIERNES</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a17" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a18" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a19" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a20" value="" class="form-control" />
												</div>
                                               <div class="col-sm-1">
												<label>
													Garantizado <input type="checkbox" name="vie_g" value="1"  />
												</label>
                                             </div>
											</div>
										</div>

											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA SABADO">SABADO</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a21" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a22" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a23" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a24" value="" class="form-control" />
												</div>
                                               <div class="col-sm-1">
												<label>
													Garantizado <input type="checkbox" name="sab_g" value="1" />
												</label>
                                             </div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="CAJAS Cargadas (<?php echo $valor_carga; ?>) DIA DOMINGO">DOMINGO</label>
												<div class="col-sm-2">
													Recibidas  (<?php echo $valor_recibo; ?>)<input type="number"  maxlength="4" name="a25" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas (<?php echo $valor_carga; ?>)<input type="number"  maxlength="4" name="a26" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas (<?php echo $valor_estiba; ?>)<input type="number"  maxlength="4" name="a27" value="" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas (<?php echo $valor_distribucion; ?>)<input type="number"  maxlength="4" name="a28" value="" class="form-control" />
												</div>
											    <div class="col-sm-1">
                                             </div>
										   </div>
										</div>


                                    <div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="MONTO $ DE PAGO ADICIONAL">ADICIONAL ($)</label>
												<div class="col-sm-9">
												   <div class="input-group">
                                                   	<input type="number" name="adicional" maxlength="6" <?php if( $row_matriz['adicionales'] == 0) { echo "disabled='disabled'";} ?> value="" class="form-control" />
														<span class="help-block">Los motivos de suplencia, horas extras y apoyos adicionales se reportan y pagan en incidencias semanales, previa autorizaci&oacute;n del Gerente Regional de Operaciones.</span>
                                                 </div>
											</div>
										</div>
                                        
                                        
										<div class="form-group">
		 						<div class="row">
													<label class="control-label col-sm-2" data-popup="tooltip-custom" title="PAGO SEMANAL GARANTIZADO">GARANTIZADO</label>
											<div class="col-sm-4">
													<select name="garantizado" class="form-control">
														<option value="">No aplica</option>
														<option value="1">Nuevo Ingreso</option>
														<option value="11">Tultitlan TVespertino</option>
														<option value="2">Funciones Administrativas</option>
														<option value="3">En proceso de promoci&oacute;n</option>
														<option value="4">Vacaciones</option>
														<option value="5">Incapacidad COVID</option>
														<option value="6">Permiso por Paternidad</option>
														<option value="7">Permiso por Defunci&oacute;n</option>
														<option value="11">Acapulco apoyo</option>
														<?php if($row_puestos['IDmatriz'] == 30 or $row_puestos['IDmatriz'] == 4 or $row_puestos['IDmatriz'] == 10) { ?>
														<option value="8">Tamemes-CEDA MX (40%)</option>
														<?php } ?>
													</select>
											</div>

									
													<label class="control-label col-sm-2" data-popup="tooltip-custom" title="HORAS EXTRAS">HORAS EXTRAS</label>
												<div class="col-sm-3">
													<input type="number" name="horas_extra" max="4" value="" class="form-control" />
												<span class="help-block">Topado a 4	, máximo 3 dias consecutivos.</span>
										</div>
								</div>
							</div>

									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="OBSERVACIONES">OBSERVACIONES</label>
												<div class="col-sm-9">
                                                  <textarea name="observaciones" class="form-control"></textarea>
												</div>
											</div>
										</div>


                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                       		<input type="submit" class="btn btn-primary" value="Capturar">
                                    </div>
								</form>
                                


                            <?php } ?>   
