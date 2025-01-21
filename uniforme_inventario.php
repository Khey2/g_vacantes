<?php require_once('Connections/vacantes.php'); ?> 
<?php

require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$actualusuario = $_SESSION['kt_login_id'];
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$actualusuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$IDusuario = $row_usuario['IDusuario'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDinventario = $_GET['IDinventario'];
$query_inventario = "SELECT sed_uniformes_inventario.*,  sed_uniformes_tipos.tipo FROM sed_uniformes_inventario LEFT JOIN sed_uniformes_tipos ON sed_uniformes_inventario.IDtipo = sed_uniformes_tipos.IDtipo WHERE IDinventario = $IDinventario";
mysql_query("SET NAMES 'utf8'");
	$inventario = mysql_query($query_inventario, $vacantes) or die(mysql_error());
$row_inventario = mysql_fetch_assoc($inventario);
$totalRows_inventario = mysql_num_rows($inventario);
?>


<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="uniformes_inventario.php">
	<div class="modal-body">                                   

							<div class="form-group">
								<label class="control-label col-lg-3">Tipo:</label>
									<div class="col-lg-9">
									<b><?php if ($row_inventario['IDsexo'] == 1) {echo " Hombre";} else {echo " Mujer";}; ?></b> <?php echo $row_inventario['tipo']; ?>
									</div>
							</div>


	<?php  if ($row_inventario['IDtipo'] == 1 OR $row_inventario['IDtipo'] == 2 OR $row_inventario['IDtipo'] == 5) { ?>						

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 28:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="28a" id="28a" value="<?php echo $row_inventario['28a'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 30:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="30a" id="30a" value="<?php echo $row_inventario['30a'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 32:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="32a" id="32a" value="<?php echo $row_inventario['32a'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 34:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="34a" id="34a" value="<?php echo $row_inventario['34a'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 36:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="36a" id="36a" value="<?php echo $row_inventario['36a'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 38:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="38a" id="38a" value="<?php echo $row_inventario['38a'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 40:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="40a" id="40a" value="<?php echo $row_inventario['40a'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 42:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="42a" id="42a" value="<?php echo $row_inventario['42a'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 44:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="44a" id="44a" value="<?php echo $row_inventario['44a'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 46:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="46a" id="46a" value="<?php echo $row_inventario['46a'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


<?php } else if ($row_inventario['IDtipo'] == 6) {?>						


								<!-- Fecha -->
								<div class="form-group">
								<label class="control-label col-lg-2">Talla 21:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="21b" id="21b" value="<?php echo $row_inventario['21b'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 22:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="22b" id="22b" value="<?php echo $row_inventario['22b'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 23:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="23b" id="23b" value="<?php echo $row_inventario['23b'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 24:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="24b" id="24b" value="<?php echo $row_inventario['24b'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 25:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="25b" id="25b" value="<?php echo $row_inventario['25b'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 26:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="26b" id="26b" value="<?php echo $row_inventario['26b'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 27:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="27b" id="27b" value="<?php echo $row_inventario['27b'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 28:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="28b" id="28b value="<?php echo $row_inventario['28b'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->

							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla 29:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="29b" id="29b" value="<?php echo $row_inventario['29b'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla 30:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="30b" id="30b" value="<?php echo $row_inventario['30b'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


<?php } else { ?>						

								<!-- Fecha -->
								<div class="form-group">
								<label class="control-label col-lg-2">Talla CH:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="CH" id="CH" value="<?php echo $row_inventario['CH'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla M:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="M" id="M" value="<?php echo $row_inventario['M'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla G:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="G" id="G" value="<?php echo $row_inventario['G'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla XG:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="XG" id="XG" value="<?php echo $row_inventario['XG'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla XXG:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="XXG" id="XXG" value="<?php echo $row_inventario['XXG'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2">Talla XXXG:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="XXXG" id="XXXG" value="<?php echo $row_inventario['XXXG'] ; ?>">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


							<!-- Fecha -->
							<div class="form-group">
								<label class="control-label col-lg-2">Talla XXXXG:</label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
                                    	<input type="number" class="form-control" name="XXXXG" id="XXXXG" value="<?php echo $row_inventario['XXXXG'] ; ?>">
									</div>
                                   </div>
								<label class="control-label col-lg-2"></label>
			                        <div class="col-lg-4">
			                        <div class="input-group">
									</div>
                                   </div>
                            </div> 
							<!-- Fecha -->


<?php }  ?>						
								<div class="modal-footer">
								<input type="hidden" name="MM_update" value="form1">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-warning">Actualizar</button>
									<input type="hidden" name="IDinventario" value="<?php echo $IDinventario; ?>">
								</div>
    </div>
</form>

