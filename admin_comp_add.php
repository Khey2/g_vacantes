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

if(isset($_GET['IDempleado'])) {$IDempleado = $_GET['IDempleado'];}
if(isset($_GET['Tipo'])) {$Tipo = $_GET['Tipo'];}

$query_activos = "SELECT * FROM prod_activos ORDER BY emp_paterno ASC";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);



?>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="admin_comp_agregar.php" >
								<fieldset class="content-group">
                                	<div class="modal-body">
                                                                       
                                    <input type="hidden" name="IDcaptura" value="<?php echo $row_detalle['IDcaptura']; ?>">                
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_detalle['IDempleado']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_detalle['IDpuesto']; ?>" >
                                    <input type="hidden" name="capturador" value="<?php echo $row_usuario['IDusuario']; ?>" >
                                    <input type="hidden" name="fecha_captura" value="<?php echo date('Y/m/d'); ?>" >
                                    <input type="hidden" name="semana" value="<?php echo $semana; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $row_detalle['IDmatriz']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_detalle['IDpuesto']; ?>">


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Empleado:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDempleado" class="form-control">
												<?php  do {  ?>
                                                <option value="<?php echo $row_activos['IDempleado']?>">
												<?php echo "(".$row_activos['IDempleado'].") ".$row_activos['emp_paterno']." ".$row_activos['emp_materno']." ".$row_activos['emp_nombre']." - ".$row_activos['denominacion'];?></option>
                                                <?php } while ($row_activos = mysql_fetch_assoc($activos)); ?>
                                              </select>
												</div>
											</div>
	                                    </div>


                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <input type="submit" class="btn btn-primary" value="Capturar">
                                            </div>
									
                                    
                                    </div>
                                </fieldset>
                                </form>

                                    
