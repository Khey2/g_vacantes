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

$IDempleado = $_GET['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);


mysql_select_db($database_vacantes, $vacantes);
$query_encuesta = "SELECT * FROM encuesta_2023 WHERE IDempleado = $IDempleado";
$encuesta = mysql_query($query_encuesta, $vacantes) or die(mysql_error());
$row_encuesta = mysql_fetch_assoc($encuesta);
$totalRows_encuesta = mysql_num_rows($encuesta);
?>
							<div class="modal-body">
    

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="empleados_print23b.php">
								<div class="modal-body">
                                    <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" >
                                    
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:</label>
											<div class="col-lg-9">
											<b><?php echo $row_activos['emp_paterno']; ?> <?php echo $row_activos['emp_materno']; ?> <?php echo $row_activos['emp_nombre']; ?></b>
											 </div>
									 </div>
									
									<div class="form-group">
										<label class="control-label col-lg-3">Periodo:</label>
										<div class="col-lg-9">
											<input type="text" name="periodo" id="periodo" class="form-control" placeholder="Periodo AAAA-AAAA." required="required">
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-3">Días del Periodo:</label>
										<div class="col-lg-9">
											<input type="number" name="diasp" id="diasp" class="form-control" placeholder="Días disfrutados." required="required">
										</div>
									</div>								

									<div class="form-group">
										<label class="control-label col-lg-3">Días disfrutados:</label>
										<div class="col-lg-9">
											<input type="number" name="diasd" id="diasd" class="form-control" placeholder="Días disfrutados." required="required">
										</div>
									</div>								

									<div class="form-group">
										<label class="control-label col-lg-3">Días restantes:</label>
										<div class="col-lg-9">
											<input type="number" name="diasr" id="diasr" class="form-control" placeholder="Días restantes." required="required">
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-3">Fecha Inicio Periodo:</label>
										<div class="col-lg-9">
											<input type="text" name="fecha3" id="fecha3" class="form-control" placeholder="Fecha de inicio del Periodo DD/MM/AAAA." required="required">
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-3">Fecha Expiración Periodo:</label>
										<div class="col-lg-9">
											<input type="text" name="fecha1" id="fecha1" class="form-control" placeholder="Fecha de expiracion del Periodo DD/MM/AAAA." required="required">
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-3">Fecha del Formato:</label>
										<div class="col-lg-9">
											<input type="text" name="fecha2" id="fecha2" class="form-control" placeholder="Fecha de expedición DD/MM/AAAA." required="required">
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-3">Formato:</label>
										<div class="col-lg-9">
											<select name="formato" class="form-control" required="required">
											  <option value="1">Constancia de días de Vacaciones</option>
											  <option value="2">Carta de Vacaciones Pendientes</option>
											</select>
											</div>
									</div>

                                    <div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
											<button type="submit" class="btn btn-success"  id="send">Imprimir</button>
                                    </div>
                                    </div>
									</form>

                               