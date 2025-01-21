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
$tipo = $_GET['tipo'];	
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 

//echo "Empleado:". $IDempleado;	
//echo ", Puesto: ". $el_puesto;	
//echo ", Matriz: ". $IDmatriz;	
//echo ", Semana: ". $semana;	

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT com_vd.IDvd, com_vd.IDmatriz, com_vd.IDempleadoS, com_vd.IDempleado, com_vd.Clave, com_vd.VentaNeta, com_vd.VentaNetaCajas, com_vd.VentaNetaPieza, com_vd.ClientesVenta, com_vd.NoPedidos, com_vd.Visitas, com_vd.DevImporte, com_vd.DevPorc, com_vd.Presupuesto, com_vd.Cubrimiento, com_vd.MargenBruto, com_vd.IDsemana, com_vd.bt_01, com_vd.bt_02, com_vd.bt_03, com_vd.bt_04, com_vd.bt_05, com_vd.bt_garantizado, com_vd.bt_adicional,  com_vd.bt_observaciones, com_vd.bt_capturador, com_vd.bt_fecha_captura, com_vd.BonoProductividad, com_vd.Premios, com_vd.Comisiones, vac_matriz.matriz, Empleados.IDempleado, Empleados.emp_paterno AS emp_paterno, Empleados.emp_materno AS emp_materno, Empleados.emp_nombre AS emp_nombre, Empleados.denominacion AS emp_denominacion, Empleados.IDpuesto AS emp_IDpuesto, Jefes.IDempleado AS jefe_IDempleado, Jefes.emp_paterno AS jefe_paterno, Jefes.emp_materno AS jefe_materno, Jefes.emp_nombre AS jefe_nombre, Jefes.denominacion AS jefe_denominacion, Jefes.IDpuesto  AS jefe_IDpuesto FROM com_vd LEFT JOIN prod_activos AS Empleados ON com_vd.IDempleado = Empleados.IDempleado LEFT JOIN prod_activos AS Jefes ON com_vd.IDempleadoS = Jefes.IDempleado LEFT JOIN vac_matriz ON com_vd.IDmatriz = vac_matriz.IDmatriz WHERE Empleados.IDempleado = '$IDempleado'";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

?>
                            <?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?>                            

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="vd_captura.php" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="hidden" name="bt_capturador" value="<?php echo $row_usuario['IDusuario']; ?>" >
                                    <input type="hidden" name="bt_fecha_captura" value="<?php echo date('Y/m/d'); ?>" >

		<?php if ($tipo == 'a1') { ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-2">BONO TRANSPORTE:<span class="text-danger">*</span></label>
												<div class="col-sm-2">
													Semana 1<br/>
													<input type="text" class="form-control" name="bt_01" id="bt_01" value="<?php echo htmlentities($row_puestos['bt_01'], ENT_COMPAT, 'utf-8'); ?>">
												</div>
												<div class="col-sm-2">
													Semana 2<br/>
													<input type="text" class="form-control" name="bt_02" id="bt_02" value="<?php echo htmlentities($row_puestos['bt_02'], ENT_COMPAT, 'utf-8'); ?>">
												</div>
												<div class="col-sm-2">
													Semana 3<br/>
													<input type="text" class="form-control" name="bt_03" id="bt_03" value="<?php echo htmlentities($row_puestos['bt_03'], ENT_COMPAT, 'utf-8'); ?>">
												</div>
												<div class="col-sm-2">
													Semana 4<br/>
													<input type="text" class="form-control" name="bt_04" id="bt_04" value="<?php echo htmlentities($row_puestos['bt_04'], ENT_COMPAT, 'utf-8'); ?>">
												</div>
												<div class="col-sm-2">
													Semana 5<br/>
													<input type="text" class="form-control" name="bt_05" id="bt_05" value="<?php echo htmlentities($row_puestos['bt_05'], ENT_COMPAT, 'utf-8'); ?>">
												</div>
											</div>
	                                    </div>

									<div class="form-group">
                                     <div class="row">
										<label class="control-label col-sm-2" data-popup="tooltip-custom" title="Grantizado">GARANTIZADO:</label>
											<div class="col-sm-10">
											<div class="checkbox">
											<select name="bt_garantizado" class="form-control">
                                                <option value="" <?php if (!(strcmp($row_puestos['bt_garantizado'],  ''))) {echo "SELECTED";} ?>>No aplica</option>
                                                <option value="1" <?php if (!(strcmp($row_puestos['bt_garantizado'], '1'))) {echo "SELECTED";} ?>>En proceso de promoci&oacute;n</option>
                                                <option value="2" <?php if (!(strcmp($row_puestos['bt_garantizado'], '2'))) {echo "SELECTED";} ?>>Nuevo Ingreso</option>
    									    </select>
                                            </div>
                                          </div>
	                                    </div>
 									</div>
                                        
									<div class="form-group">
	                                    <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="% DE PAGO ADICIONAL">ADICIONAL ($)</label>
												<div class="col-sm-2">
													<input type="text" class="form-control" name="bt_adicional" id="bt_adicional" value="<?php echo htmlentities($row_puestos['bt_adicional'], ENT_COMPAT, 'utf-8'); ?>">
											  </div>
											</div>
	                                    </div>

                                        
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" data-popup="tooltip-custom" title="OBSERVACIONES">OBSERVACIONES</label>
												<div class="col-sm-10">
                                                  <textarea name="bt_observaciones"  rows="4" class="form-control"><?php echo htmlentities($row_puestos['bt_observaciones'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>

									<p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                       	<input type="submit" class="btn btn-primary" value="Capturar">
										<input type="hidden" name="IDtipo" value="a1" >
										<input type="hidden" name="IDvd" value="<?php echo $row_puestos['IDvd']; ?>" >
										
                                    </div>
								</form>
                                
		<?php } else if ($tipo == 'a2') { ?> Bono Productividad
		<?php } else if ($tipo == 'a3') { ?> Premios
		<?php } else if ($tipo == 'a4') { ?> Comisiones
		<?php } ?>
