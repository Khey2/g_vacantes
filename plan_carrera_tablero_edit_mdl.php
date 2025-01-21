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
$IDevaluador = $_GET['IDevaluador'];	
$IDciclo  = $_GET['IDciclo'];	

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM prod_activos WHERE prod_activos.IDempleado = '$IDempleado'";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

//modulo 7 OP
$query_M1i = "SELECT * FROM pc_modulovii WHERE IDempleado = $IDempleado  AND IDevaluador = $IDevaluador AND IDciclo = $IDciclo"; 
$M1i = mysql_query($query_M1i, $vacantes) or die(mysql_error());
$row_M1i = mysql_fetch_assoc($M1i);
$totalRows_M1i = mysql_num_rows($M1i);

?>
                            <?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre'];?>													

                            <?php if ($totalRows_M1i > 0) { ?>


            					<form method="post" name="form1" action="plan_carrera_tablero_edit.php?IDempleado=<?php echo $IDempleado?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data" 
                                class="form-horizontal form-validate-jquery">
									<div class="modal-body">
                                    
                                	<input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">                
                                    <input type="hidden" name="IDciclo" value="<?php echo $IDciclo; ?>">                
                                    <input type="hidden" name="IDevaluador" value="<?php echo $IDevaluador; ?>">

											<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-2">Viable</label>
											<div class="col-sm-9">
											<select name="viable" class="form-control">
                                                <option value="1" <?php if (!(strcmp($row_M1i['viable'], '2'))) {echo "SELECTED";} ?>>Apto</option>
                                                <option value="0" <?php if (!(strcmp($row_M1i['viable'], '1'))) {echo "SELECTED";} ?>>No Apto</option>
    									    </select>
												</div>
											</div>
										</div>

										<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-2">Documento</label>
												<div class="col-sm-9">
												<input type="file" class="file-styled" name="foto" id="foto">
												<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</br>
												</div>
											</div>
										</div>

										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2">Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="observaciones" class="form-control"><?php echo htmlentities($row_M1i['observaciones'], ENT_COMPAT, 'utf-8'); ?></textarea>
												</div>
											</div>
										</div>

									
									
                                            <p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        	<input type="submit" class="btn btn-primary" value="Cargar">
                                    </div>
								</form>

                            <?php } else { ?>   
                                
            					<form method="post" name="form1" action="plan_carrera_tablero_edit.php?IDempleado=<?php echo $IDempleado?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data" 
                                class="form-horizontal form-validate-jquery">
									<div class="modal-body">
                                	<input type="hidden" name="MM_insert" value="form1" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">                
                                    <input type="hidden" name="IDciclo" value="<?php echo $IDciclo; ?>">                
                                    <input type="hidden" name="IDevaluador" value="<?php echo $IDevaluador; ?>">
                                    

									<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-2">Viable</label>
											<div class="col-sm-9">
											<select name="viable" class="form-control">
                                                <option value="">Seleccione</option>
                                                <option value="1">Apto</option>
                                                <option value="0">No Apto</option>
    									    </select>
												</div>
											</div>
										</div>

										<div class="form-group">
		 									 <div class="row">
												<label class="control-label col-sm-2">Documento</label>
												<div class="col-sm-9">
												<input type="file" class="file-styled" name="foto" id="foto">
												<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</br>
												</div>
											</div>
										</div>
									
										<div class="form-group">
											 <div class="row">
												<label class="control-label col-sm-2" >Observaciones</label>
												<div class="col-sm-9">
                                                  <textarea name="observaciones" class="form-control"></textarea>
												</div>
											</div>
										</div>


                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                       		<input type="submit" class="btn btn-primary" value="Cargar">
                                    </div>
								</form>
                                


                            <?php } ?>   
