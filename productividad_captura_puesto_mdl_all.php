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
$semana =  $_GET['semana'];	

$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 

mysql_select_db($database_vacantes, $vacantes);
$query_kpi11 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 1 AND b = 1";
mysql_query("SET NAMES 'utf8'");
$kpi11 = mysql_query($query_kpi11, $vacantes) or die(mysql_error());
$row_kpi11 = mysql_fetch_assoc($kpi11);

$query_kpi12 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 1 AND b = 2";
$kpi12 = mysql_query($query_kpi12, $vacantes) or die(mysql_error());
$row_kpi12 = mysql_fetch_assoc($kpi12);

$query_kpi13 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 1 AND b = 3";
$kpi13 = mysql_query($query_kpi13, $vacantes) or die(mysql_error());
$row_kpi13 = mysql_fetch_assoc($kpi13);

$query_kpi21 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 2 AND b = 1";
$kpi21 = mysql_query($query_kpi21, $vacantes) or die(mysql_error());
$row_kpi21 = mysql_fetch_assoc($kpi21);

$query_kpi22 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 2 AND b = 2";
$kpi22 = mysql_query($query_kpi22, $vacantes) or die(mysql_error());
$row_kpi22 = mysql_fetch_assoc($kpi22);

$query_kpi23 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 2 AND b = 3";
$kpi23 = mysql_query($query_kpi23, $vacantes) or die(mysql_error());
$row_kpi23 = mysql_fetch_assoc($kpi23);
$totalRows_kpi23 = mysql_num_rows($kpi23);

$query_kpi31 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 3 AND b = 1";
$kpi31 = mysql_query($query_kpi31, $vacantes) or die(mysql_error());
$row_kpi31 = mysql_fetch_assoc($kpi31);

$query_kpi32 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 3 AND b = 2";
$kpi32 = mysql_query($query_kpi32, $vacantes) or die(mysql_error());
$row_kpi32 = mysql_fetch_assoc($kpi32);

$query_kpi33 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 3 AND b = 3";
$kpi33 = mysql_query($query_kpi33, $vacantes) or die(mysql_error());
$row_kpi33 = mysql_fetch_assoc($kpi33);
$totalRows_kpi33 = mysql_num_rows($kpi33);

$query_kpi41 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 4 AND b = 1";
$kpi41 = mysql_query($query_kpi41, $vacantes) or die(mysql_error());
$row_kpi41 = mysql_fetch_assoc($kpi41);

$query_kpi42 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 4 AND b = 2";
$kpi42 = mysql_query($query_kpi42, $vacantes) or die(mysql_error());
$row_kpi42 = mysql_fetch_assoc($kpi42);

$query_kpi43 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 4 AND b = 3";
$kpi43 = mysql_query($query_kpi43, $vacantes) or die(mysql_error());
$row_kpi43 = mysql_fetch_assoc($kpi43);
$totalRows_kpi43 = mysql_num_rows($kpi43);

$query_kpi51 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 5 AND b = 1";
$kpi51 = mysql_query($query_kpi51, $vacantes) or die(mysql_error());
$row_kpi51 = mysql_fetch_assoc($kpi51);

$query_kpi52 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 5 AND b = 2";
$kpi52 = mysql_query($query_kpi52, $vacantes) or die(mysql_error());
$row_kpi52 = mysql_fetch_assoc($kpi52);

$query_kpi53 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 5 AND b = 3";
$kpi53 = mysql_query($query_kpi53, $vacantes) or die(mysql_error());
$row_kpi53 = mysql_fetch_assoc($kpi53);
$totalRows_kpi53 = mysql_num_rows($kpi53);

$query_kpi61 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 6 AND b = 1";
$kpi61 = mysql_query($query_kpi61, $vacantes) or die(mysql_error());
$row_kpi61 = mysql_fetch_assoc($kpi61);
$totalRows_kpi61 = mysql_num_rows($kpi61);

$query_kpi62 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 6 AND b = 2";
$kpi62 = mysql_query($query_kpi62, $vacantes) or die(mysql_error());
$row_kpi62 = mysql_fetch_assoc($kpi62);
$totalRows_kpi62 = mysql_num_rows($kpi62);

$query_kpi63 = "SELECT * FROM prod_kpis WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND a = 6 AND b = 3";
$kpi63 = mysql_query($query_kpi63, $vacantes) or die(mysql_error());
$row_kpi63 = mysql_fetch_assoc($kpi63);
$totalRows_kpi63 = mysql_num_rows($kpi63);

$query_garantia = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $IDmatriz AND IDsucursal = $IDsucursal";
$garantia = mysql_query($query_garantia, $vacantes) or die(mysql_error());
$row_garantia = mysql_fetch_assoc($garantia);
$gatantizado = $row_garantia['garantia'];

$query_tipo_captura = "SELECT * FROM vac_puestos WHERE IDpuesto = $el_puesto";
$tipo_captura = mysql_query($query_tipo_captura, $vacantes) or die(mysql_error());
$row_tipo_captura = mysql_fetch_assoc($tipo_captura);
$prod_captura_tipo = $row_tipo_captura['prod_captura_tipo'];

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
$query_puestos = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDempleado,  prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.a25, prod_captura.a26, prod_captura.a27, prod_captura.a28, prod_captura.adicional, prod_captura.adicional2, prod_captura.semana, prod_captura.capturador, prod_captura.observaciones, prod_captura.fecha_captura, vac_puestos.denominacion, vac_puestos.modal FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio' LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDempleado = '$IDempleado'";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

?>
                            <?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?>                            

                            <?php if ($row_puestos['capturador'] == '') { ?>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="productividad_captura_puesto_all.php?IDpuesto=<?php echo $row_puestos['IDpuesto']; ?>" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="hidden" name="capturador" value="<?php echo $row_usuario['IDusuario']; ?>" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_puestos['IDempleado']; ?>" >
                                    <input type="hidden" name="emp_paterno" value="<?php echo $row_puestos['emp_paterno']; ?>" >
                                    <input type="hidden" name="emp_materno" value="<?php echo $row_puestos['emp_materno']; ?>" >
                                    <input type="hidden" name="emp_nombre" value="<?php echo $row_puestos['emp_nombre']; ?>" >
                                    <input type="hidden" name="denominacion" value="<?php echo $row_puestos['denominacion']; ?>" >
                                    <input type="hidden" name="sueldo_total" value="<?php echo $row_puestos['sueldo_total_productividad']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_puestos['IDpuesto']; ?>">
                                    <input type="hidden" name="fecha_captura" value="<?php echo date('Y/m/d'); ?>" >
                                    <input type="hidden" name="semana" value="<?php echo $semana; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $anio; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $row_puestos['IDmatriz']; ?>" >
                                    <input type="hidden" name="IDsucursal" value="<?php echo $row_puestos['IDsucursal']; ?>" >
                                    <input type="hidden" name="IDarea" value="<?php echo $row_puestos['IDarea']; ?>" >
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="QUITAR INCAPACIDADES O DESCANSOS CON PERMISO">DIAS LABORADOS</label>
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
                                            <div class="checkbox">    
													<input type="hidden" name="dom" value="1" />
                                            </div>
                                           </div>
                                         </div>
									 </div><!-- /basic singlecheckbox -->
                                            
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi12['c']?>"><?php echo $row_kpi11['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a1" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi13['p']?>" <?php if (!(strcmp($row_kpi13['p'], htmlentities($row_puestos['a1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi13['c']?></option>
        <?php } while ($row_kpi13 = mysql_fetch_assoc($kpi13)); ?>
      </select>
												</div>
											</div>
	                                    </div>

										<?php if($totalRows_kpi23 != 0) { ?>
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi22['c']?>"><?php echo $row_kpi21['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a2" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi23['p']?>" <?php if (!(strcmp($row_kpi23['p'], htmlentities($row_puestos['a2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi23['c']?></option>
        <?php } while ($row_kpi23 = mysql_fetch_assoc($kpi23)); ?>
      </select>
												</div>
											</div>
	                                    </div>
										<?php } ?>

										<?php if($totalRows_kpi33 != 0) { ?>
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi32['c']?>"><?php echo $row_kpi31['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a3" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi33['p']?>" <?php if (!(strcmp($row_kpi33['p'], htmlentities($row_puestos['a3'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi33['c']?></option>
        <?php } while ($row_kpi33 = mysql_fetch_assoc($kpi33)); ?>
      </select>
												</div>
											</div>
	                                    </div>
										<?php } ?>

										<?php if($totalRows_kpi43 != 0) { ?>
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi42['c']?>"><?php echo $row_kpi41['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a4" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi43['p']?>" <?php if (!(strcmp($row_kpi43['p'], htmlentities($row_puestos['a4'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi43['c']?></option>
        <?php } while ($row_kpi43 = mysql_fetch_assoc($kpi43)); ?>
      </select>
												</div>
											</div>
	                                    </div>
										<?php } ?>
                                        
										<?php if($totalRows_kpi53 != 0) { ?>
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi52['c']?>"><?php echo $row_kpi51['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a5" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi53['p']?>" <?php if (!(strcmp($row_kpi53['p'], htmlentities($row_puestos['a5'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi53['c']?></option>
        <?php } while ($row_kpi53 = mysql_fetch_assoc($kpi53)); ?>
      </select>
												</div>
											</div>
	                                    </div>
										<?php } ?>



										<?php if($totalRows_kpi63 != 0) { ?>
											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi62['c']?>"><?php echo $row_kpi61['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a6" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi63['p']?>" <?php if (!(strcmp($row_kpi63['p'], htmlentities($row_puestos['a6'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi63['c']?></option>
        <?php } while ($row_kpi63 = mysql_fetch_assoc($kpi63)); ?>
      </select>
												</div>
											</div>
										</div>
										<?php } ?>


									<div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="PAGO SEMANAL GARANTIZADO">GARANTIZADO SEMANAL:</label>
											<div class="col-sm-9">
											<div class="checkbox">
											<select name="garantizado" class="form-control">
                                                <option value="" <?php if (!(strcmp($row_puestos['garantizado'],  ''))) {echo "SELECTED";} ?>>No aplica</option>
                                                <option value="1" <?php if (!(strcmp($row_puestos['garantizado'], '1'))) {echo "SELECTED";} ?>>En proceso de promoci&oacute;n</option>
                                                <option value="2" <?php if (!(strcmp($row_puestos['garantizado'], '2'))) {echo "SELECTED";} ?>>Funciones Administrativas</option>
                                                <option value="3" <?php if (!(strcmp($row_puestos['garantizado'], '3'))) {echo "SELECTED";} ?>>Incapacidad COVID</option>
                                                <option value="4" <?php if (!(strcmp($row_puestos['garantizado'], '4'))) {echo "SELECTED";} ?>>Nuevo Ingreso</option>
                                                <option value="5" <?php if (!(strcmp($row_puestos['garantizado'], '5'))) {echo "SELECTED";} ?>>Permiso por Paternidad</option>
                                                <option value="6" <?php if (!(strcmp($row_puestos['garantizado'], '6'))) {echo "SELECTED";} ?>>Permiso por Defunci&oacute;n</option>
                                                <option value="7" <?php if (!(strcmp($row_puestos['garantizado'], '7'))) {echo "SELECTED";} ?>>Vacaciones</option>
                                                <option value="11" <?php if (!(strcmp($row_puestos['garantizado'], '11'))) {echo "SELECTED";} ?>>Acapulco apoyo</option>
												<?php if($row_puestos['IDmatriz'] == 30 or $row_puestos['IDmatriz'] == 4 or $row_puestos['IDmatriz'] == 10) { ?>
                                                <option value="8" <?php if (!(strcmp($row_puestos['garantizado'], '8'))) {echo "SELECTED";} ?>>Tamemes-CEDA MX (40%)</option>
												<?php } ?>
    									    </select>
                                            </div>
                                          </div>
	                                    </div>
 									</div>
                                        
									<div class="form-group">
											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="% DE PAGO ADICIONAL">ADICIONAL (%)</label>
												<div class="col-sm-8">
													<input type="number" name="adicional" <?php if( $row_matriz['adicionales'] == 0) { echo "disabled='disabled'";} ?>  maxlength="2" value="<?php echo htmlentities($row_puestos['adicional'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												<span class="help-block">Los motivos de suplencia, horas extras y apoyos adicionales se reportan y pagan en incidencias semanales, previa autorizaci&oacute;n del Gerente Regional de Operaciones.</span>
                                                </div>
												<div class="col-sm-1">
													%
                                                </div>
											</div>
										</div>


										<?php if($el_puesto == 41) { ?>
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="OBSERVACIONES">CAJAS CARGADAS (PARA ESTADISTICA)</label>
												<div class="col-sm-9">
                                                  <div class="row">
												<div class="col-sm-2">
													Recibidas<input type="number"  maxlength="4" name="a25" value="<?php echo htmlentities($row_puestos['a25'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas<input type="number"  maxlength="4" name="a26" value="<?php echo htmlentities($row_puestos['a26'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas<input type="number"  maxlength="4" name="a27" value="<?php echo htmlentities($row_puestos['a27'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas<input type="number"  maxlength="4" name="a28" value="<?php echo htmlentities($row_puestos['a28'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												</div>
											</div>
										</div>
                                    </div>
										<?php } ?>


                                        
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="OBSERVACIONES">OBSERVACIONES</label>
												<div class="col-sm-9">
                                                  <textarea name="observaciones" class="form-control"></textarea>
												</div>
											</div>
										</div>
                                    </div>


									<p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

											<?php if ($prod_captura_tipo != 2) { ?>
                                        	<input type="submit" class="btn btn-primary" value="Capturar">
                                            <?php } ?>   

                                    </div>
								</form>
                                
                            <?php } else { ?>   
                                
            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="productividad_captura_puesto_all.php?IDpuesto=<?php echo $row_puestos['IDpuesto']; ?>" >
									<div class="modal-body">
                                    
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDcaptura" value="<?php echo $row_puestos['IDcaptura']; ?>">                
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_puestos['IDempleado']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_puestos['IDpuesto']; ?>" >
                                    <input type="hidden" name="denominacion" value="<?php echo $row_puestos['denominacion']; ?>" >
                                    <input type="hidden" name="sueldo_total" value="<?php echo $row_puestos['sueldo_total_productividad']; ?>" >
                                    <input type="hidden" name="emp_paterno" value="<?php echo $row_puestos['emp_paterno']; ?>" >
                                    <input type="hidden" name="emp_materno" value="<?php echo $row_puestos['emp_materno']; ?>" >
                                    <input type="hidden" name="emp_nombre" value="<?php echo $row_puestos['emp_nombre']; ?>" >
                                    <input type="hidden" name="capturador" value="<?php echo $row_usuario['IDusuario']; ?>" >
                                    <input type="hidden" name="fecha_captura" value="<?php echo date('Y/m/d'); ?>" >
                                    <input type="hidden" name="semana" value="<?php echo $semana; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $anio; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $row_puestos['IDmatriz']; ?>" >
                                    <input type="hidden" name="IDsucursal" value="<?php echo $row_puestos['IDsucursal']; ?>" >
                                    <input type="hidden" name="IDarea" value="<?php echo $row_puestos['IDarea']; ?>" >
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="QUITAR INCAPACIDADES O DESCANSOS CON PERMISO">DIAS LABORADOS</label>
											<div class="col-sm-1">
											<div class="checkbox">
												<label>
													<input type="checkbox" name="lun" value="1"  <?php if (!(strcmp(htmlentities($row_puestos['lun'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />
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
                                            <div class="checkbox">    
													<input type="hidden" name="dom" value="1" />
                                            </div>
                                             </div>
									 </div><!-- /basic singlecheckbox -->
                                            
											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi12['c']?>"><?php echo $row_kpi11['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a1" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi13['p']?>" <?php if (!(strcmp($row_kpi13['p'], htmlentities($row_puestos['a1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi13['c']?></option>
        <?php } while ($row_kpi13 = mysql_fetch_assoc($kpi13)); ?>
      </select>
												</div>
											</div>
										</div>

										<?php if($totalRows_kpi23 != 0) { ?>
											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi22['c']?>"><?php echo $row_kpi21['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a2" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi23['p']?>" <?php if (!(strcmp($row_kpi23['p'], htmlentities($row_puestos['a2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi23['c']?></option>
        <?php } while ($row_kpi23 = mysql_fetch_assoc($kpi23)); ?>
      </select>
												</div>
											</div>
										</div>
										<?php } ?>

										<?php if($totalRows_kpi33 != 0) { ?>
											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi32['c']?>"><?php echo $row_kpi31['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a3" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi33['p']?>" <?php if (!(strcmp($row_kpi33['p'], htmlentities($row_puestos['a3'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi33['c']?></option>
        <?php } while ($row_kpi33 = mysql_fetch_assoc($kpi33)); ?>
      </select>
												</div>
											</div>
										</div>
										<?php } ?>

										<?php if($totalRows_kpi43 != 0) { ?>
											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi42['c']?>"><?php echo $row_kpi41['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a4" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi43['p']?>" <?php if (!(strcmp($row_kpi43['p'], htmlentities($row_puestos['a4'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi43['c']?></option>
        <?php } while ($row_kpi43 = mysql_fetch_assoc($kpi43)); ?>
      </select>
												</div>
											</div>
										</div>
										<?php } ?>


										<?php if($totalRows_kpi53 != 0) { ?>
											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi52['c']?>"><?php echo $row_kpi51['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a5" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi53['p']?>" <?php if (!(strcmp($row_kpi53['p'], htmlentities($row_puestos['a5'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi53['c']?></option>
        <?php } while ($row_kpi53 = mysql_fetch_assoc($kpi53)); ?>
      </select>
												</div>
											</div>
										</div>
										<?php } ?>


										<?php if($totalRows_kpi63 != 0) { ?>
											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="<?php echo $row_kpi52['c']?>"><?php echo $row_kpi51['c']?>:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="a6" class="form-control" required="required">
        <?php  do {  ?>
        <option value="<?php echo $row_kpi63['p']?>" <?php if (!(strcmp($row_kpi63['p'], htmlentities($row_puestos['a6'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_kpi63['c']?></option>
        <?php } while ($row_kpi63 = mysql_fetch_assoc($kpi63)); ?>
      </select>
												</div>
											</div>
										</div>
										<?php } ?>


									<div class="form-group">
 									 <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="PAGO SEMANAL GARANTIZADO">GARANTIZADO</label>
											<div class="col-sm-9">
											<div class="checkbox">
											<select name="garantizado" class="form-control">
                                                <option value="" <?php if (!(strcmp($row_puestos['garantizado'],  ''))) {echo "SELECTED";} ?>>No aplica</option>
                                                <option value="1" <?php if (!(strcmp($row_puestos['garantizado'], '1'))) {echo "SELECTED";} ?>>En proceso de promoci&oacute;n</option>
                                                <option value="2" <?php if (!(strcmp($row_puestos['garantizado'], '2'))) {echo "SELECTED";} ?>>Funciones Administrativas</option>
                                                <option value="3" <?php if (!(strcmp($row_puestos['garantizado'], '3'))) {echo "SELECTED";} ?>>Incapacidad COVID</option>
                                                <option value="4" <?php if (!(strcmp($row_puestos['garantizado'], '4'))) {echo "SELECTED";} ?>>Nuevo Ingreso</option>
                                                <option value="5" <?php if (!(strcmp($row_puestos['garantizado'], '5'))) {echo "SELECTED";} ?>>Permiso por Paternidad</option>
                                                <option value="6" <?php if (!(strcmp($row_puestos['garantizado'], '6'))) {echo "SELECTED";} ?>>Permiso por Defunci&oacute;n</option>
                                                <option value="7" <?php if (!(strcmp($row_puestos['garantizado'], '7'))) {echo "SELECTED";} ?>>Vacaciones</option>
												<?php if($row_puestos['IDmatriz'] == 30 or $row_puestos['IDmatriz'] == 4 or $row_puestos['IDmatriz'] == 10) { ?>
                                                <option value="8" <?php if (!(strcmp($row_puestos['garantizado'], '8'))) {echo "SELECTED";} ?>>Tamemes-CEDA MX (40%)</option>
												<?php } ?>
    									    </select>
                                            </div>
                                         </div>
                                       </div>
 									</div>
                                        
									<div class="form-group">
											<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="% DE PAGO ADICIONAL">ADICIONAL (%)</label>
												<div class="col-sm-8">
													<input type="number" <?php if( $row_matriz['adicionales'] == 0) { echo "disabled='disabled'";} ?> name="adicional" maxlength="2" value="<?php echo htmlentities($row_puestos['adicional'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												<span class="help-block">Recuerda que no se puede rebasar el presupuesto semanal.</span>
                                                </div>
												<div class="col-sm-1">
														%
                                                </div>
											</div>
										</div>

										<?php if($el_puesto == 41) { ?>
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="OBSERVACIONES">CAJAS CARGADAS (PARA ESTADISTICA)</label>
												<div class="col-sm-9">
                                                  <div class="row">
												<div class="col-sm-2">
													Recibidas<input type="number"  maxlength="4" name="a25" value="<?php echo htmlentities($row_puestos['a25'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Cargadas<input type="number"  maxlength="4" name="a26" value="<?php echo htmlentities($row_puestos['a26'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Estibadas<input type="number"  maxlength="4" name="a27" value="<?php echo htmlentities($row_puestos['a27'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												<div class="col-sm-2">
													Distribuidas<input type="number"  maxlength="4" name="a28" value="<?php echo htmlentities($row_puestos['a28'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
												</div>
											</div>
										</div>
                                    </div>
										<?php } ?>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="OBSERVACIONES">OBSERVACIONES</label>
												<div class="col-sm-9">
                                                  <textarea name="observaciones" class="form-control"><?php echo htmlentities($row_puestos['observaciones'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>


                                            <p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

											<?php if ($prod_captura_tipo != 2) { ?>
                                        	<input type="submit" class="btn btn-primary" value="Capturar">
                                            <?php } ?>   

                                    </div>
								</form>
                                
                            <?php } ?>   
