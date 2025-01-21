<?php require_once('Connections/vacantes.php'); ?>
<?php 

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$IDempleado = $_GET['IDempleado'];
$Tipo = $_GET['Tipo'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el anio anterior 
$semana = date("W", strtotime($la_fecha));

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDarea, prod_activos.denominacion,  prod_activos.fecha_antiguedad,  prod_activos.IDpuesto, inc_captura.transporte, inc_captura.IDcaptura, inc_captura.IDprod, inc_captura.perc, inc_captura.prima, inc_captura.dias1, inc_captura.dias2, inc_captura.horas1, inc_captura.horas2, inc_captura.pprueba, inc_captura.obs1, inc_captura.obs6, inc_captura.obs2, inc_captura.obs3, inc_captura.obs4, inc_captura.obs5, inc_captura.IDmotivo1, inc_captura.IDmotivo2, inc_captura.IDmotivo3, inc_captura.inc1 AS INC1, inc_captura.inc2 AS INC2, inc_captura.inc3 AS INC3, inc_captura.inc6 AS INC6, inc_captura.inc3, inc_captura.inc6, inc_captura.diasf, inc_captura.inc4 AS INC4, inc_captura.inc5 AS INC5, prod_activos.IDempleado, prod_activos.sueldo_diario, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, inc_captura.lul, inc_captura.mal, inc_captura.mil, inc_captura.jul, inc_captura.vil, inc_captura.sal, inc_captura.dol, inc_captura.luf, inc_captura.maf, inc_captura.mif, inc_captura.juf, inc_captura.vif, inc_captura.saf, inc_captura.dof, prod_activos.IDmatriz FROM prod_activos LEFT JOIN inc_captura ON inc_captura.IDempleado = prod_activos.IDempleado AND inc_captura.semana = '$semana' AND inc_captura.anio = '$anio' WHERE prod_activos.IDempleado = '$IDempleado'"; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
$IDmatriz = $row_detalle['IDmatriz'];
$fecha_antiguedad = $row_detalle['fecha_antiguedad'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_dias = "SELECT * FROM inc_dias";
$dias = mysql_query($query_dias, $vacantes) or die(mysql_error());
$row_dias = mysql_fetch_assoc($dias);
$totalRows_dias = mysql_num_rows($dias);

$query_horas = "SELECT * FROM inc_horas";
$horas = mysql_query($query_horas, $vacantes) or die(mysql_error());
$row_horas = mysql_fetch_assoc($horas);
$totalRows_horas = mysql_num_rows($horas);

$query_motivos1 = "SELECT * FROM inc_motivos WHERE IDmotivo_tipo = 1 order by MOTIVO";
$motivos1 = mysql_query($query_motivos1, $vacantes) or die(mysql_error());
$row_motivos1 = mysql_fetch_assoc($motivos1);
$totalRows_motivos1 = mysql_num_rows($motivos1);

$query_motivos2 = "SELECT * FROM inc_motivos WHERE IDmotivo_tipo = 2 order by MOTIVO";
$motivos2 = mysql_query($query_motivos2, $vacantes) or die(mysql_error());
$row_motivos2 = mysql_fetch_assoc($motivos2);
$totalRows_motivos2 = mysql_num_rows($motivos2);

$query_motivos3 = "SELECT * FROM inc_motivos WHERE IDmotivo_tipo = 3 order by MOTIVO";
$motivos3 = mysql_query($query_motivos3, $vacantes) or die(mysql_error());
$row_motivos3 = mysql_fetch_assoc($motivos3);
$totalRows_motivos3 = mysql_num_rows($motivos3);

$query_pprueba = "SELECT * FROM pp_prueba WHERE IDempleado = $IDempleado AND IDpuesto_destino in (42, 43, 44, 45, 57, 372, 313) AND DATE(fecha_inicio) < '$la_fecha' AND DATE(fecha_fin) > '$la_fecha'";
$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);

if ($totalRows_pprueba > 0) {
$el_puesto = $row_pprueba['IDpuesto_destino']; $obspp = 'El empleado tiene Periodo de Prueba activo';
} else {
$el_puesto = $row_detalle['IDpuesto']; $obspp = '';
}

$query_pxv_loc = "SELECT * FROM inc_pxv WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND tipo = 1";
$pxv_loc = mysql_query($query_pxv_loc, $vacantes) or die(mysql_error());
$row_pxv_loc = mysql_fetch_assoc($pxv_loc);
$totalRows_pxv_loc = mysql_num_rows($pxv_loc);

$query_pxv_for = "SELECT * FROM inc_pxv WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$IDmatriz' AND tipo = 2";
$pxv_for = mysql_query($query_pxv_for, $vacantes) or die(mysql_error());
$row_pxv_for = mysql_fetch_assoc($pxv_for);
$totalRows_pxv_for = mysql_num_rows($pxv_for);

$query_perc = "SELECT * FROM inc_perc";
mysql_query("SET NAMES 'utf8'");
$perc = mysql_query($query_perc, $vacantes) or die(mysql_error());
$row_perc = mysql_fetch_assoc($perc);
$totalRows_perc = mysql_num_rows($perc);

$query_prima = "SELECT * FROM inc_prima";
mysql_query("SET NAMES 'utf8'");
$prima = mysql_query($query_prima, $vacantes) or die(mysql_error());
$row_prima = mysql_fetch_assoc($prima);
$totalRows_prima = mysql_num_rows($prima);

$query_incents = "SELECT inc_captura.IDempleado, Count(inc_captura.semana) AS veces, Sum(inc_captura.inc3) AS Monto, inc_captura.IDmatriz FROM inc_captura WHERE inc_captura.IDempleado = '$IDempleado' AND inc_captura.inc3 > 0 AND inc_captura.anio = '$anio'";  
$incents = mysql_query($query_incents, $vacantes) or die(mysql_error());
$row_incents = mysql_fetch_assoc($incents);
$totalRows_incents = mysql_num_rows($incents);

$semana_recorre = $semana - 4;
$query_domingos = "SELECT inc_captura.IDempleado, Count(inc_captura.semana) AS veces, Sum(inc_captura.inc4) AS Monto FROM inc_captura WHERE inc_captura.IDempleado = '$IDempleado' AND inc_captura.semana > $semana_recorre AND inc_captura.anio = '$anio' AND inc_captura.perc = 2";  
$domingos = mysql_query($query_domingos, $vacantes) or die(mysql_error());
$row_domingos = mysql_fetch_assoc($domingos);
$totalRows_domingos = mysql_num_rows($domingos);

$semana_recorre2 = $semana - 8;
$query_suplencias = "SELECT inc_captura.IDempleado, inc_captura.semana, Count(inc_captura.semana) AS veces, Sum(inc_captura.inc2) AS Monto FROM inc_captura WHERE inc_captura.IDempleado = '$IDempleado' AND inc_captura.inc2 > 0 AND inc_captura.semana > '$semana_recorre2' AND inc_captura.anio = '$anio'";  
$suplencias = mysql_query($query_suplencias, $vacantes) or die(mysql_error());
$row_suplencias = mysql_fetch_assoc($suplencias);
$totalRows_suplencias = mysql_num_rows($suplencias);

$supers = array(58, 56, 270, 17);
$chofers = array(42, 43, 44, 45, 57, 372);
$matrices = array(4, 25, 28);
$op_sistemas = array(154, 155, 157, 159, 500, 267);
//$supers = array(0);

// nuevo pago de Premio Transporte
mysql_select_db($database_vacantes, $vacantes);
$query_transporte = "SELECT * FROM inc_transporte WHERE IDpuesto = $el_puesto AND IDmatriz = $IDmatriz";
$transporte = mysql_query($query_transporte, $vacantes) or die(mysql_error());
$row_transporte = mysql_fetch_assoc($transporte);
$totalRows_transporte = mysql_num_rows($transporte);
$monto_transporte = $row_transporte['monto'];


?>
                                  (<?php echo $row_detalle['IDempleado']; ?>) <?php echo $row_detalle['emp_paterno']; ?> <?php echo $row_detalle['emp_materno']; ?> <?php echo $row_detalle['emp_nombre']; ?>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="inc_cap_puesto.php" >
									<div class="modal-body">
                                                                       
                                    <input type="hidden" name="IDcaptura" value="<?php echo $row_detalle['IDcaptura']; ?>">                
                                    <input type="hidden" name="emp_paterno" value="<?php echo $row_detalle['emp_paterno']; ?>" >
                                    <input type="hidden" name="emp_materno" value="<?php echo $row_detalle['emp_materno']; ?>" >
                                    <input type="hidden" name="emp_nombre" value="<?php echo $row_detalle['emp_nombre']; ?>" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_detalle['IDempleado']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_detalle['IDpuesto']; ?>" >
                                    <input type="hidden" name="capturador" value="<?php echo $row_usuario['IDusuario']; ?>" >
                                    <input type="hidden" name="fecha_captura" value="<?php echo date('Y/m/d'); ?>" >
                                    <input type="hidden" name="semana" value="<?php echo $semana; ?>" >
                                    <input type="hidden" name="anio" value="<?php echo $anio; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $row_detalle['IDmatriz']; ?>" >

									<?php if ($row_detalle['IDcaptura'] == '') { // captura o actualiza  ?>
        
                                    <?php if ($Tipo == 'a1') { // Horas Extra  ?>
                                    <H6>HORAS EXTRAS</H6>
									
									<?php if ($row_matriz['hextra'] == 0) { //Historico ?>   
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-danger text-danger-600">
                                             No se tienen autorizadas las Horas Extra para &eacute;sta sucursal. Solicita la captura directamente a la Jefatura de Compensaciones.</span>
                                             </div>
                                    <?php } ?>

                                	<input type="hidden" name="MM_insert" value="form1">

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Dias:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="dias1" class="form-control" required="required">
                                            	<option value="1">1</option>
                                            	<option value="2">2</option>
                                            	<option value="3">3</option>
                                            </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Horas">Horas:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="horas1" class="form-control" required="required">
                                            	<option value="1">1</option>
                                            	<option value="2">2</option>
                                            	<option value="3">3</option>
                                            	<option value="4">4</option>
                                            	<option value="5">5</option>
                                            	<option value="6">6</option>
                                            	<option value="7">7</option>
                                            	<option value="8">8</option>
                                            	<option value="9">9</option>
     										</select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDmotivo1" class="form-control" required="required">
												<?php  do {  ?>
                                                <option value="<?php echo $row_motivos1['IDmotivo']?>"><?php echo $row_motivos1['motivo']?></option>
                                                <?php } while ($row_motivos1 = mysql_fetch_assoc($motivos1)); ?>
                                              </select>
                                             </div>
											</div>
	                                    </div>
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="obs1" class="form-control" required="required"></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Topado a 9, m&aacute;ximo 3 dias consecutivos.</p>
                                                  <p>En general el pago de horas extras debe evitarse. </br>
                                                  Para Aux. de Almac&eacute;n se deber&aacute; pagar a trav&eacute;s de las cajas cargadas en Productividad.</p>
												</div>
											</div>
										</div>
                                    </div>
									 </div>
                                    
                                     </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<?php if ($row_matriz['hextra'] == 1) { //Historico ?>   
                                    <?php if (!in_array($el_puesto, $supers)) { // Supervisor ?>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                    <?php }  } ?>
                                            </div>
                                 </form>

                                    
                                    <?php } else if ($Tipo == 'a2') { // Suplencias  ?>




                                	<input type="hidden" name="MM_insert" value="form2">
                                    <H6>SUPLENCIAS</H6>

									<?php if ($row_suplencias['veces'] > 2 and $row_suplencias['veces'] < 4) { //Historico ?>
											<div class="form-group">
											<span class="label label-flat label-block border-warning text-warning-600">
											En las &uacute;ltimas 6 semanas, se han capturado <?php echo $row_suplencias['veces']; ?> suplencias por un monto de <?php echo $row_suplencias['Monto']; ?>.</span>
											</div>
									<?php } ?>

									<?php if( $row_suplencias['veces'] >= 4) { ?>
										<div class="form-group">
										<span class="label label-flat label-block border-danger text-danger-600">
										Se han excedido las veces consectivas de suplencia.</br>
										Solicita el pago directamente con la Jefatura de Compensaciones.</span>
										</div>
									<?php } ?>

									<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">D&iacute;as:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="dias2" class="form-control" required="required">
                                            	<option value="0">0</option>
                                            	<option value="1">1</option>
                                            	<option value="2">2</option>
                                            	<option value="3">3</option>
                                            	<option value="4">4</option>
                                            	<option value="5">5</option>
                                            	<option value="6">6</option>
											</select>
												</div>
											</div>
	                                    </div>


										<?php if (!in_array($el_puesto, $op_sistemas)) { // Suplencias  ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Horas">Horas:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="horas2" class="form-control" required="required">
                                            	<option value="0">0</option>
                                            	<option value="1">1</option>
                                            	<option value="2">2</option>
                                            	<option value="3">3</option>
                                            	<option value="4">4</option>
                                            	<option value="5">5</option>
                                            	<option value="6">6</option>
                                            	<option value="7">7</option>
                                            	<option value="8">8</option>
                                            	<option value="9">9</option>
                                            	<option value="10">10</option>
                                            	<option value="11">11</option>
                                            	<option value="12">12</option>
                                            	<option value="13">13</option>
                                            	<option value="14">14</option>
                                            	<option value="15">15</option>
                                            	<option value="16">16</option>
                                            	<option value="17">17</option>
                                            	<option value="18">18</option>
                                            	<option value="19">19</option>
                                            	<option value="20">20</option>
                                            	<option value="21">21</option>
                                            	<option value="22">22</option>
                                            	<option value="23">23</option>
                                            	<option value="24">24</option>
                                            	<option value="25">25</option>
                                            	<option value="26">26</option>
                                            	<option value="27">27</option>
                                            	<option value="28">28</option>
                                            	<option value="29">29</option>
                                            	<option value="30">30</option>
                                            	<option value="31">31</option>
                                            	<option value="32">32</option>
                                            	<option value="33">33</option>
                                            	<option value="34">34</option>
                                            	<option value="35">35</option>
                                            	<option value="36">36</option>
                                            	<option value="37">37</option>
                                            	<option value="38">38</option>
                                            	<option value="39">39</option>
                                            	<option value="40">40</option>
                                            	<option value="41">41</option>
                                            	<option value="42">42</option>
                                            	<option value="43">43</option>
                                            	<option value="44">44</option>
                                            	<option value="45">45</option>
                                            	<option value="46">46</option>
									      </select>
												</div>
											</div>
	                                    </div>

									<?php  }   ?>


									<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDmotivo2" class="form-control" required="required">
													<?php  do {  ?>
                                                    <option value="<?php echo $row_motivos2['IDmotivo']?>" 
                                                    <?php if (!(strcmp($row_motivos2['IDmotivo'], htmlentities($row_detalle['IDmotivo2'], ENT_COMPAT, 'utf-8')))) 
                                                    {echo "SELECTED";} ?>><?php echo $row_motivos2['motivo']?></option>
                                                    <?php } while ($row_motivos2 = mysql_fetch_assoc($motivos2)); ?>
                                                  </select>
												</div>
											</div>
	                                    </div>
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="obs2" class="form-control"  required="required"></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
												<p>Sujeto a validaci&oacute;n por RH Corporativo.<br/>
                                                  No se deben asignar m&aacute;s de 2 semanas consecutivas de suplencia.</p>

												<?php if (in_array($el_puesto, $op_sistemas)) { // Suplencias  ?>
													El pago por d&iacute;a es del 50% del sueldo diario.
												<?php }  ?>

												</div>
											</div>
										</div>
                                    </div>
									 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <?php if( $row_suplencias['veces'] < 4) { ?>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                                <?php } ?>
                                            </div>
                                 </form>

        
                                    <?php } else if ($Tipo == 'a3') { // Incentivos ?>
                                	<input type="hidden" name="MM_insert" value="form3">
                                    <H6>INCENTIVOS</H6>

                                    <?php if ($row_incents['Monto'] > 0) { //Historico ?>
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-danger text-danger-600">
                                             En el <?php echo $anio; ?>, se han capturado <?php echo $row_incents['veces']; ?> incentivos por un monto de <?php echo $row_incents['Monto']; ?>.</span>
                                             </div>
                                    <?php } ?>

                                    <?php if ($row_incents['Monto'] == 0) { //Historico ?>
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-primary text-primary">
                                             Recuerda que debes solicitar justificaci&oacute;n y validar los incentivos capturados.</span>
                                             </div>
                                    <?php } ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo:</label>
												<div class="col-sm-9">
											<select name="IDmotivo3" class="form-control">
												<?php  do {  ?>
                                                <option value="<?php echo $row_motivos3['IDmotivo']?>"><?php echo $row_motivos3['motivo']?></option>
                                                <?php } while ($row_motivos3 = mysql_fetch_assoc($motivos3)); ?>
                                              </select>
												</div>
											</div>
	                                    </div>
									
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Monto">Monto ($):</label>
												<div class="col-sm-9">
											<input type="number" name="inc3" min="0" max="5000" value="" class="form-control" />
												</div>
											</div>
	                                    </div>

                                    <?php if ($monto_transporte > 0) { //especial Bono Transporte  ?>
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Bono de Transporte">Bono de Transporte (<?php echo $monto_transporte; ?>):</label>
												<div class="col-sm-9">
											<select name="transporte" class="form-control">
                                            	<option value="0">No</option>
                                            	<option value="1">Si</option>
									      </select>
											<span class="help-block">Justifica la captura.</span>
												</div>
											</div>
	                                    </div>
                                    <?php } ?>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Justificaci&oacute;n:</label>
												<div class="col-sm-9">
                                                  <textarea name="obs3" class="form-control" required="required"></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Debes justificar el concepto de pago.</br>
												</div>
											</div>
										</div>
                                    </div>
                                         </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                            </div>
                                 </form>

                                    
                                    <?php } else if ($Tipo == 'a4') { // Domingos  ?>
                                	<input type="hidden" name="MM_insert" value="form4">
                                    <H6>DOMINGOS TRABAJADOS</H6>

                                    <?php if ($row_domingos['veces'] > 2) { //Historico ?>   
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-danger text-danger-600">
                                             El empelado lleva <?php echo $row_domingos['veces']; ?> semanas consecutivas sin descanso. Solicita la captura directamente a la Jefatura de Compensaciones.</span>
                                             </div>
                                    <?php } ?>

									  <div class="form-group">
			                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Percepcion">Percepci&oacuten Dominical:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="perc" class="form-control">
                                                <option value="0">NO</option>
											<?php if ($row_domingos['veces'] < 2 OR $row_matriz['domingos'] == 1) { ?>
                                                <option value="2">SI</option>
											<?php }  ?>
                                              </select>
												</div>
											</div>
	                                    </div>

                                        <div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="prima">Prima Dominical:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="prima" class="form-control" required="required">
											<?php  do {  ?>
                                            <option value="<?php echo $row_prima['IDprima']?>" ><?php echo $row_prima['prima']?></option>
                                            <?php } while ($row_prima = mysql_fetch_assoc($prima)); ?>
                                            </select>
												</div>
											</div>
	                                    </div>
								
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Justificaci&oacute;n:</label>
												<div class="col-sm-9">
                                                  <textarea name="obs4" class="form-control" required="required"></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Debes justificar el pago. </br>
                                                  Solo se paga la Percepci&oacuten Dominical si el empleado trabaj&oacute; de lunes a domingo sin descanso.</br>
                                                  Se debe priorizar el pago por tiempo. </p>
												</div>
											</div>
										</div>
                                    </div>
                                         </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                            </div>
                                 </form>

        
                                    <?php } else if ($Tipo == 'a6') { // Dias Festivos  ?>
                                	<input type="hidden" name="MM_insert" value="form6">
                                    <H6>DIAS FESTIVOS</H6>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Monto">D&iacute;as Festivos (#):</label>
												<div class="col-sm-9">
												<input type="number" name="diasf" min="1" max="1" value="" class="form-control"  required="required" />
												</div>
											</div>
	                                    </div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">D&iacute;a Festivo a pagar (fecha):</label>
												<div class="col-sm-9">
                                                  <textarea name="obs6" class="form-control"><?php echo htmlentities($row_detalle['obs6'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Solo se paga doble el d&iacute;a si es festivo. Se debe priorizar el descanso en d&iacute;a festivo.</p>
												</div>
											</div>
										</div>
                                    </div>
                                         </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                            </div>
                                 </form>

        
                                    <?php } else if ($Tipo == 'a5') { // PXV  ?>
                                	<input type="hidden" name="MM_insert" value="form5">
                                    <H6>PREMIOS POR VIAJE</H6>

                                    <?php if ($row_detalle['IDpuesto'] == 2 AND ($IDmatriz == 26 OR $IDmatriz == 29)) { // PXV Aux Tux  ?>
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">D&iacute;as!!</label>
											<div class="col-sm-1">
											<div class="checkbox">
												<label><input type="checkbox" name="lul" value="1" />Lun</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="mal" value="1" />Mar</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
                                            	<label><input type="checkbox" name="mil" value="1" />Mie</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="jul" value="1" />Jue</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="vil" value="1" />Vie</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="sal" value="1" />Sab</label>
                                            </div>
                                             </div>
                                             
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

									<?php } else { // PXV Aux Tux  ?>
									
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Locales</label>

                                    <div class="form-group row">
                                          <div class="col-lg-1">
                                            <label for="lul">Lun</label>
                                            <input class="form-control" name="lul" min="0" max="3"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="mal">Mar</label>
                                            <input class="form-control" name="mal" min="0" max="3"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="mil">Mie</label>
                                            <input class="form-control" name="mil" min="0" max="3"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="jul">Jue</label>
                                            <input class="form-control" name="jul" min="0" max="3"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="vil">Vie</label>
                                            <input class="form-control" name="vil" min="0" max="3"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="sal">Sab</label>
                                            <input class="form-control" name="sal" min="0" max="3"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="dol">Dom</label>
                                            <input class="form-control" name="dol" min="0" max="3"  type="number" value="">
                                          </div>
                                         </div>

                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->


                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Foraneos</label>

                                    <div class="form-group row">
                                          <div class="col-lg-1">
                                            <input class="form-control" name="luf" min="0" max="1"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="maf" min="0" max="1"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="mif" min="0" max="1"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="juf" min="0" max="1"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="vif" min="0" max="1"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="saf" min="0" max="1"  type="number" value="">
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="dof" min="0" max="1"  type="number" value="">	
                                          </div>
                                         </div>
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->
									 
									 
                                    <?php } // PXV Aux Tux  ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Prueba">Periodo de Prueba:</label>
												<div class="col-sm-9">
											<select name="pprueba" class="form-control">
                                                <option value="0"   <?php if (!(strcmp(0,  htmlentities($row_pprueba['IDpuesto_destino'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO APLICA</option>
												<option value="42" <?php if (!(strcmp(42, htmlentities($row_pprueba['IDpuesto_destino'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER CAMIONETA</option>
												<option value="43" <?php if (!(strcmp(43, htmlentities($row_pprueba['IDpuesto_destino'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER TORTON</option> 
												<option value="44" <?php if (!(strcmp(44, htmlentities($row_pprueba['IDpuesto_destino'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER RABON</option> 
												<option value="45" <?php if (!(strcmp(45, htmlentities($row_pprueba['IDpuesto_destino'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER TRAILER</option> 
                                              </select>
													<span class="text text-muted">El Periodo de Prueba debe est&aacute;r capturado y vigente en el SGRH para que se habilite el monto.</span>
													<span> <?php echo "<br/>".$obspp; ?></span>
												</div>
												</div>
	                                    </div>

									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="obs5" class="form-control"></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motos">Montos</label>
												<div class="col-sm-9">
                                                
                                                <?php 	$loc_max = $row_pxv_loc['maximo'];
														$for_max = $row_pxv_for['maximo'];
														$loc_monto = $row_pxv_loc['monto'];
														$for_monto = $row_pxv_for['monto'];	
												?>

                                    <?php if ($row_detalle['IDpuesto'] == 2 AND ($IDmatriz == 26 OR $IDmatriz == 29)) { // PXV Aux Tux  ?>

                                                  <p>M&aacute;ximos semanales: <?php  if($loc_max > 0) { echo $loc_max;} else { echo 0;} ?> | Monto:  <?php if($loc_monto > 0) { echo $loc_monto;} else { echo 0;} ?></p>

                                    <?php } else {  ?>

                                                  <p>M&aacute;ximo Locales:  <?php  if($loc_max > 0) { echo $loc_max;} else { echo 0;} ?> | Monto Locales:  $<?php if($loc_monto > 0) { echo $loc_monto;} else { echo 0;} ?></p>
                                                  <p>M&aacute;ximo Foraneos: <?php  if($for_max > 0) { echo $for_max;} else { echo 0;} ?> | Monto Foraneos: $<?php if($for_monto > 0) { echo $for_monto;} else { echo 0;} ?></p>
												  
									<?php }  ?>
			  
												  
												</div>
											</div>
										</div>
                                    </div>
										 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                            </div>
                                 </form>
        
                                    <?php }  ?>
        
        
        
                                    <?php } else { // captura o actualiza ?>
                                    
                                    
                                    
                                    <?php if ($Tipo == 'a1') { // Horas Extra  ?>
                                	<input type="hidden" name="MM_update" value="form1">
                                    <H6>HORAS EXTRAS</H6>

									<?php if ($row_matriz['hextra'] == 0) { //Historico ?>   
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-danger text-danger-600">
                                             No se tienen autorizadas las Horas Extra para &eacute;sta sucursal. Solicita la captura directamente a la Jefatura de Compensaciones.</span>
                                             </div>
                                    <?php } ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">D&iacuteas:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="dias1" class="form-control" required="required">
                                            	<option value="1" <?php if (!(strcmp(1, htmlentities($row_detalle['dias1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1</option>
                                            	<option value="2" <?php if (!(strcmp(2, htmlentities($row_detalle['dias1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2</option>
                                            	<option value="3" <?php if (!(strcmp(3, htmlentities($row_detalle['dias1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3</option>
									      </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Horas">Horas:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="horas1" class="form-control" required="required">
                                            	<option value="1" <?php if (!(strcmp(1, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1</option>
                                            	<option value="2" <?php if (!(strcmp(2, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2</option>
                                            	<option value="3" <?php if (!(strcmp(3, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3</option>
                                            	<option value="4" <?php if (!(strcmp(4, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4</option>
                                            	<option value="5" <?php if (!(strcmp(5, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5</option>
                                            	<option value="6" <?php if (!(strcmp(6, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>6</option>
                                            	<option value="7" <?php if (!(strcmp(7, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>7</option>
                                            	<option value="8" <?php if (!(strcmp(8, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>8</option>
                                            	<option value="9" <?php if (!(strcmp(9, htmlentities($row_detalle['horas1'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>9</option>
									        </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDmotivo1" class="form-control" required="required">
												<?php  do {  ?>
                                                <option value="<?php echo $row_motivos1['IDmotivo']?>" 
                                                <?php if (!(strcmp($row_motivos1['IDmotivo'], htmlentities($row_detalle['IDmotivo1'], ENT_COMPAT, 'utf-8'))))
                                                {echo "SELECTED";} ?>><?php echo $row_motivos1['motivo']?></option>
                                                <?php } while ($row_motivos1 = mysql_fetch_assoc($motivos1)); ?>
                                              </select>
												</div>
											</div>
	                                    </div>
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="obs1" class="form-control"  required="required"><?php echo htmlentities($row_detalle['obs1'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>
                                    
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Topado a 9 horas por semana, maximo 3 por d&iacute;a</p>
                                                  <p>En general el pago de horas extras debe evitarse. </br>
                                                  Para Aux. de Almac&eacute;n se deber&aacute; pagar a trav&eacute;s de las cajas cargadas en Productividad.</p>
												</div>
											</div>
										</div>
                                    </div>
											 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<?php if ($row_matriz['hextra'] == 1) { //Historico ?>   
                                    <?php if (!in_array($el_puesto, $supers)) { // Supervisor ?>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                    <?php }  } ?>

                                            <?php if ($row_detalle['INC1'] > 0) { ?>
											<a href="inc_cap_puesto_calb.php?IDcaptura=<?php echo $row_detalle['IDcaptura']; ?>&tipo=1" class="btn btn-danger" role="button">Borrar</a>
                                    		<?php } ?>

                                            </div>
                                 </form>

                                    <?php } else if ($Tipo == 'a2') { // Suplencias  ?>

								

                                	<input type="hidden" name="MM_update" value="form2">
                                    <H6>SUPLENCIAS</H6>

                                    <?php if ($row_suplencias['veces'] > 2 and $row_suplencias['veces'] < 4) { //Historico ?>
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-warning text-warning-600">
                                             En las &uacute;ltimas 6 semanas, se han capturado <?php echo $row_suplencias['veces']; ?> suplencias por un monto de <?php echo $row_suplencias['Monto']; ?>.</span>
                                             </div>
                                    <?php } ?>

                                                <?php if( $row_suplencias['veces'] >= 4) { ?>
                                             <div class="form-group">
                                                <span class="label label-flat label-block border-danger text-danger-600">
                                                Se han excedido las veces consectivas de suplencia. </br>
                                                Solicita el pago directamente con la Jefatura de Compensaciones.</span>
                                                </div>
												<?php } ?>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">D&iacute;as:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="dias2" class="form-control" required="required">
                                            	<option value="0" <?php if (!(strcmp(0, htmlentities($row_detalle['dias2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>0</option>
                                            	<option value="1" <?php if (!(strcmp(1, htmlentities($row_detalle['dias2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1</option>
                                            	<option value="2" <?php if (!(strcmp(2, htmlentities($row_detalle['dias2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2</option>
                                            	<option value="3" <?php if (!(strcmp(3, htmlentities($row_detalle['dias2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3</option>
                                            	<option value="4" <?php if (!(strcmp(4, htmlentities($row_detalle['dias2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4</option>
                                            	<option value="5" <?php if (!(strcmp(5, htmlentities($row_detalle['dias2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5</option>
                                            	<option value="6" <?php if (!(strcmp(6, htmlentities($row_detalle['dias2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>6</option>
											</select>
												</div>
											</div>
	                                    </div>

										<?php if (!in_array($el_puesto, $op_sistemas)) { // Suplencias  ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Horas">Horas:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="horas2" class="form-control" required="required">
                                            	<option value="0" <?php if (!(strcmp(0, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>0</option>
                                            	<option value="1" <?php if (!(strcmp(1, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>1</option>
                                            	<option value="2" <?php if (!(strcmp(2, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>2</option>
                                            	<option value="3" <?php if (!(strcmp(3, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>3</option>
                                            	<option value="4" <?php if (!(strcmp(4, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4</option>
                                            	<option value="5" <?php if (!(strcmp(5, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5</option>
                                            	<option value="6" <?php if (!(strcmp(6, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>6</option>
                                            	<option value="7" <?php if (!(strcmp(7, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>7</option>
                                            	<option value="8" <?php if (!(strcmp(8, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>8</option>
                                            	<option value="9" <?php if (!(strcmp(9, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>9</option>
                                            	<option value="10" <?php if (!(strcmp(10, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>10</option>
                                            	<option value="11" <?php if (!(strcmp(11, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>11</option>
                                            	<option value="12" <?php if (!(strcmp(12, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>12</option>
                                            	<option value="13" <?php if (!(strcmp(13, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>13</option>
                                            	<option value="14" <?php if (!(strcmp(14, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>14</option>
                                            	<option value="15" <?php if (!(strcmp(15, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>15</option>
                                            	<option value="16" <?php if (!(strcmp(16, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>16</option>
                                            	<option value="17" <?php if (!(strcmp(17, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>17</option>
                                            	<option value="18" <?php if (!(strcmp(18, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>18</option>
                                            	<option value="19" <?php if (!(strcmp(19, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>19</option>
                                            	<option value="20" <?php if (!(strcmp(20, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>20</option>
                                            	<option value="21" <?php if (!(strcmp(21, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>21</option>
                                            	<option value="22" <?php if (!(strcmp(22, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>22</option>
                                            	<option value="23" <?php if (!(strcmp(23, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>23</option>
                                            	<option value="24" <?php if (!(strcmp(24, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>24</option>
                                            	<option value="25" <?php if (!(strcmp(25, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>25</option>
                                            	<option value="26" <?php if (!(strcmp(26, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
                                            	<option value="27" <?php if (!(strcmp(27, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>27</option>
                                            	<option value="28" <?php if (!(strcmp(28, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
                                            	<option value="29" <?php if (!(strcmp(29, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>29</option>
                                            	<option value="30" <?php if (!(strcmp(30, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
                                            	<option value="31" <?php if (!(strcmp(31, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>31</option>
                                            	<option value="32" <?php if (!(strcmp(32, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>32</option>
                                            	<option value="33" <?php if (!(strcmp(33, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>33</option>
                                            	<option value="34" <?php if (!(strcmp(34, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>34</option>
                                            	<option value="35" <?php if (!(strcmp(35, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>35</option>
                                            	<option value="36" <?php if (!(strcmp(36, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>36</option>
                                            	<option value="37" <?php if (!(strcmp(37, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>37</option>
                                            	<option value="38" <?php if (!(strcmp(38, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>38</option>
                                            	<option value="39" <?php if (!(strcmp(39, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>39</option>
                                            	<option value="40" <?php if (!(strcmp(40, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40</option>
                                            	<option value="41" <?php if (!(strcmp(41, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>41</option>
                                            	<option value="42" <?php if (!(strcmp(42, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>42</option>
                                            	<option value="43" <?php if (!(strcmp(43, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>43</option>
                                            	<option value="44" <?php if (!(strcmp(44, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>44</option>
                                            	<option value="45" <?php if (!(strcmp(45, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>45</option>
                                            	<option value="46" <?php if (!(strcmp(46, htmlentities($row_detalle['horas2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>46</option>
											</select>
												</div>
											</div>
	                                    </div>

									<?php }  ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDmotivo2" class="form-control" required="required">
												<?php  do {  ?>
                                                <option value="<?php echo $row_motivos2['IDmotivo']?>"
                                                 <?php if (!(strcmp($row_motivos2['IDmotivo'], htmlentities($row_detalle['IDmotivo2'], ENT_COMPAT, 'utf-8'))))
                                                 {echo "SELECTED";} ?>><?php echo $row_motivos2['motivo']?></option>
                                                <?php } while ($row_motivos2 = mysql_fetch_assoc($motivos2)); ?>
                                              </select>
												</div>
											</div>
	                                    </div>
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="obs2" class="form-control"  required="required"><?php echo htmlentities($row_detalle['obs2'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

                                    
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Sujeto a validaci&oacute;n por Jefatura de Compensaciones.<br/>
                                                  No se deben asignar m&aacute;s de 2 semanas consecutivas de suplencia.</p>
												  <?php if (in_array($el_puesto, $op_sistemas)) { // Suplencias  ?>
													El pago por d&iacute;a es del 50% del sueldo diario.
												<?php } ?>
												</div>
											</div>
										</div>
                                    </div>
                                    
											 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <?php if( $row_suplencias['veces'] < 4) { ?>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                                <?php } ?>

                                            <?php if ($row_detalle['INC2'] > 0) { ?>
											<a href="inc_cap_puesto_calb.php?IDcaptura=<?php echo $row_detalle['IDcaptura']; ?>&tipo=2" class="btn btn-danger" role="button">Borrar</a>
                                    		<?php } ?>

                                            </div>

                                    <?php } else if ($Tipo == 'a3') { // Incentivos ?>
                                	<input type="hidden" name="MM_update" value="form3">
                                    <H6>INCENTIVOS</H6>


                                    <?php if ($row_incents['Monto'] > 0) { //Historico ?>
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-danger text-danger-600">
                                             En el <?php echo $anio; ?>, se han capturado <?php echo $row_incents['veces']; ?> incentivos por un monto de <?php echo $row_incents['Monto']; ?>.</span>
                                             </div>
                                    <?php } ?>

                                    <?php if ($row_incents['Monto'] == 0) { //Historico ?>
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-primary text-primary">
                                             Recuerda que debes solicitar justificaci&oacute;n y validar los incentivos capturados.</span>
                                             </div>
                                    <?php } ?>

                                             
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Motivo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDmotivo3" class="form-control">
												<?php  do {  ?>
                                                <option value="<?php echo $row_motivos3['IDmotivo']?>"
                                                 <?php if ($row_motivos3['IDmotivo'] == $row_detalle['IDmotivo3'])
                                                 {echo "SELECTED";} ?>><?php echo $row_motivos3['motivo']?></option>
                                                <?php } while ($row_motivos3 = mysql_fetch_assoc($motivos3)); ?>
                                              </select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Monto">Monto ($):</label>
												<div class="col-sm-9">
												<input type="number" name="inc3" min="0" max="5000" value="<?php echo htmlentities($row_detalle['inc3'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											</div>
	                                    </div>
										
										
										
                                    <?php if ($monto_transporte > 0) { //especial Bono Transporte  ?>
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Bono de Transporte">Bono de Transporte (<?php echo $monto_transporte; ?>):</label>
												<div class="col-sm-9">
											<select name="transporte" class="form-control">
                                            	<option value="0" <?php if (!(strcmp(0, htmlentities($row_detalle['transporte'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>No</option>
                                            	<option value="1" <?php if (!(strcmp(1, htmlentities($row_detalle['transporte'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Si</option>
									      </select>
											<span class="help-block">Justifica la captura.</span>
												</div>
											</div>
	                                    </div>
                                    <?php } ?>


										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Justificaci&oacute;n:</label>
												<div class="col-sm-9">
                                                  <textarea name="obs3" class="form-control" required="required"><?php echo htmlentities($row_detalle['obs3'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
 												<p>Debes justificar el concepto de pago.</p>
                                                  </div>
											</div>
										</div>
                                    </div>
                                    
											 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">

                                            <?php if ($row_detalle['INC3'] > 0) { ?>
											<a href="inc_cap_puesto_calb.php?IDcaptura=<?php echo $row_detalle['IDcaptura']; ?>&tipo=3" class="btn btn-danger" role="button">Borrar</a>
                                    		<?php } ?>

                                            </div>

                                    <?php } else if ($Tipo == 'a4') { // Domingos  ?>
                                	<input type="hidden" name="MM_update" value="form4">
                                    <H6>DOMINGOS TRABAJADOS</H6>

                                    <?php if ($row_domingos['veces'] > 2) { //Historico ?>
                                             <div class="form-group">
                                             <span class="label label-flat label-block border-danger text-danger-600">
                                             El empelado lleva <?php echo $row_domingos['veces']; ?> semanas consecutivas sin descanso. Solicita la captura directamente a la Jefatura de Compensaciones.</span>
                                             </div>
                                    <?php } ?>

									  <div class="form-group">
			                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Percepcion">Percepci&oacuten Dominical:</label>
												<div class="col-sm-9">
											<select name="perc" class="form-control" required="required">
                                                <option value="0" <?php if (!(strcmp(0, htmlentities($row_detalle['perc'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO</option>
												<?php if ($row_domingos['veces'] < 2 OR $row_matriz['domingos']) { ?>
                                                <option value="2" <?php if (!(strcmp(2, htmlentities($row_detalle['perc'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SI</option>
                                                <?php } ?>
                                              </select>
												</div>
											</div>
	                                    </div>
                                        
                                        <div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="prima">Prima Dominical:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="prima" class="form-control" required="required">
												<?php  do {  ?>
                                                <option value="<?php echo $row_prima['IDprima']?>"
                                                 <?php if (!(strcmp($row_prima['IDprima'], htmlentities($row_detalle['prima'], ENT_COMPAT, 'utf-8'))))
												 {echo "SELECTED";} ?>><?php echo $row_prima['prima']?></option>
                                                <?php } while ($row_prima = mysql_fetch_assoc($prima)); ?>
                                              </select>
												</div>
											</div>
	                                    </div>
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Justificaci&oacute;n:</label>
												<div class="col-sm-9">
                                                  <textarea name="obs4" class="form-control" required="required"><?php echo htmlentities($row_detalle['obs4'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>


										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Debes justificar el pago. </br>
                                                  Solo se paga la Percepci&oacuten Dominical si el empleado trabaj&oacute; de lunes a domingo sin descanso.</br>
                                                  Se debe priorizar el pago por tiempo. </p>
												</div>
											</div>
										</div>
        
                                    
											 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">

                                            <?php if ($row_detalle['INC4'] > 0) { ?>
											<a href="inc_cap_puesto_calb.php?IDcaptura=<?php echo $row_detalle['IDcaptura']; ?>&tipo=4" class="btn btn-danger" role="button">Borrar</a>
                                    		<?php } ?>
                                                
                                            </div>

                                    <?php } else if ($Tipo == 'a6') { // Festivos  ?>
                                	<input type="hidden" name="MM_update" value="form6">
                                    <H6>DIAS FESTIVOS</H6>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Monto">D&iacute;as Festivos (#):</label>
												<div class="col-sm-9">
												<input type="number" name="diasf" min="1" max="1" value="<?php echo htmlentities($row_detalle['diasf'], ENT_COMPAT, 'utf-8'); ?>" class="form-control"  required="required"/>
												</div>
											</div>
	                                    </div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">D&iacute;a Festivo a pagar (fecha):</label>
												<div class="col-sm-9">
                                                  <textarea name="obs6" class="form-control"><?php echo htmlentities($row_detalle['obs6'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Politica">Pol&iacute;tica</label>
												<div class="col-sm-9">
                                                  <p>Solo se paga doble el d&iacute;a si es festivo. Se debe priorizar el descanso en d&iacute;a festivo.</p>
												</div>
											</div>
										</div>
										</div>
											 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">

                                            <?php if ($row_detalle['INC6'] > 0) { ?>
											<a href="inc_cap_puesto_calb.php?IDcaptura=<?php echo $row_detalle['IDcaptura']; ?>&tipo=6" class="btn btn-danger" role="button">Borrar</a>
                                    		<?php } ?>
                                                
                                            </div>

                                    <?php } else if ($Tipo == 'a5') { // PXV  ?>
                                	<input type="hidden" name="MM_update" value="form5">
                                    <H6>PREMIOS POR VIAJE </H6>

                                    <?php if ($row_detalle['IDpuesto'] == 2 AND ($IDmatriz == 26 OR $IDmatriz == 29)) { // PXV Aux Tux y VHSA ?>
                                    
                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">D&iacute;as</label>
											<div class="col-sm-1">
											<div class="checkbox">
												<label><input type="checkbox" name="lul" value="1"  <?php if (!(strcmp(htmlentities($row_detalle['lul'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />Lun</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="mal" value="1"  <?php if (!(strcmp(htmlentities($row_detalle['mal'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />Mar</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
                                            	<label><input type="checkbox" name="mil" value="1"  <?php if (!(strcmp(htmlentities($row_detalle['mil'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />Mie</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="jul" value="1"  <?php if (!(strcmp(htmlentities($row_detalle['jul'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />Jue</label>
                                            </div>
                                             </div>
                                             
											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="vil" value="1"  <?php if (!(strcmp(htmlentities($row_detalle['vil'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />Vie</label>
                                            </div>
                                             </div>

											<div class="col-sm-1">
                                            <div class="checkbox">    
												<label><input type="checkbox" name="sal" value="1"  <?php if (!(strcmp(htmlentities($row_detalle['sal'], ENT_COMPAT, 'utf-8'),"1"))) {echo "checked=\"checked\"";} ?> />Sab</label>
                                            </div>
                                             </div>
                                             
                                         </div>
									 </div>
									 <!-- /basic singlecheckbox -->

									<?php } else { // PXV Aux Tux  ?>

                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Locales</label>

                                    <div class="form-group row">
                                          <div class="col-lg-1">
                                            <label for="lul">Lun</label>
                                            <input class="form-control" name="lul" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['lul'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <label for=" mal">Mar</label>
                                            <input class="form-control" name="mal" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['mal'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="mil">Mier</label>
                                            <input class="form-control" name="mil" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['mil'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="jul">Jue</label>
                                            <input class="form-control" name="jul" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['jul'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="vil">Vie</label>
                                            <input class="form-control" name="vil" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['vil'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="sal">Sab</label>
                                            <input class="form-control" name="sal" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['sal'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <label for="dol">Dom</label>
                                            <input class="form-control" name="dol" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['dol'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                         </div>

                                         </div>
									 </div><!-- /basic singlecheckbox -->


                                    <!-- Basic single checkbox -->
									<div class="form-group">
                                    <div class="row">
										<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Locales">Foraneos</label>

                                    <div class="form-group row">
                                          <div class="col-lg-1">
                                            <input class="form-control" name="luf" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['luf'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="maf" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['maf'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="mif" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['mif'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="juf" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['juf'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="vif" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['vif'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="saf" min="0" max="3"  type="number" value="<?php echo htmlentities($row_detalle['saf'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                          <div class="col-lg-1">
                                            <input class="form-control" name="dof" min="0" max="3"  type="number"  value="<?php echo htmlentities($row_detalle['dof'], ENT_COMPAT, 'utf-8'); ?>" >
                                          </div>
                                         </div>

									<?php } ?>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Prueba">Periodo de Prueba:</label>
												<div class="col-sm-9">
											<select name="pprueba" class="form-control">
                                                <option value="0"   <?php if (!(strcmp(0,  htmlentities($row_detalle['pprueba'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO APLICA</option>
												<option value="42" <?php if (!(strcmp(42, htmlentities($row_detalle['pprueba'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER CAMIONETA</option> 
												<option value="43" <?php if (!(strcmp(43, htmlentities($row_detalle['pprueba'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER TORTON</option>
												<option value="44" <?php if (!(strcmp(44, htmlentities($row_detalle['pprueba'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER RABON</option> 
												<option value="45" <?php if (!(strcmp(45, htmlentities($row_detalle['pprueba'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CHOFER TRAILER</option> 
                                              </select>
                                                 <span class="text text-muted">El Periodo de Prueba debe est&aacute;r capturado y vigente en el SGRH para que se habilite el monto.</span>
													<span>  <?php echo "<br/>".$obspp; ?></span></select>
												</div>
											</div>
	                                    </div>
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Observaciones">Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="obs5" class="form-control"><?php echo htmlentities($row_detalle['obs5'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>


										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motos">Montos</label>
												<div class="col-sm-9">
                                                
                                                <?php 	$loc_max = $row_pxv_loc['maximo'];
														$for_max = $row_pxv_for['maximo'];
														$loc_monto = $row_pxv_loc['monto'];
														$for_monto = $row_pxv_for['monto'];	?>

                                    <?php if ($row_detalle['IDpuesto'] == 2 AND ($IDmatriz == 26 OR $IDmatriz == 29)) { // PXV Aux Tux  ?>

                                                  <p>M&aacute;ximos semanales: <?php  if($loc_max > 0) { echo $loc_max;} else { echo 0;} ?> | Monto:  <?php if($loc_monto > 0) { echo $loc_monto;} else { echo 0;} ?></p>

                                    <?php } else {  ?>

                                                  <p>M&aacute;ximo Locales:  <?php  if($loc_max > 0) { echo $loc_max;} else { echo 0;} ?> | Monto Locales:  $<?php if($loc_monto > 0) { echo $loc_monto;} else { echo 0;} ?></p>
                                                  <p>M&aacute;ximo Foraneos: <?php  if($for_max > 0) { echo $for_max;} else { echo 0;} ?> | Monto Foraneos: $<?php if($for_monto > 0) { echo $for_monto;} else { echo 0;} ?></p>
												  <p>Solo se puede capturar un tipo de viaje en cada d&iacute;a.</p>
												  
									<?php }  ?>
												</div>
											</div>
										</div>
                                    </div>

											 </div>
                                           
                                                
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                                
                                            <?php if ($row_detalle['INC5'] > 0) { ?>
											<a href="inc_cap_puesto_calb.php?IDcaptura=<?php echo $row_detalle['IDcaptura']; ?>&tipo=5" class="btn btn-danger" role="button">Borrar</a>
                                    		<?php } ?>
                                                
                                            </div>
                                    <?php }  ?>
                                    
                                    <?php } // captura o actualiza ?>
                                          