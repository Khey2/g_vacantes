<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

$IDempleado = $_SESSION['ElEmpleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT * FROM con_empleados WHERE IDempleado = '$IDempleado'";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);


if((isset($_GET['p']) AND $_GET['p'] == 1) OR $row_contratos['a_banco'] == 1) {$p = 1; } else {$p = 0;}

header("Content-Type: text/html;charset=utf-8");

if ( $p != 2) { ?>

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cuenta de Banco:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="a_cuenta_bancaria" id="a_cuenta_bancaria" class="form-control" placeholder="Cuenta bancaria" value="<?php echo htmlentities($row_contratos['a_cuenta_bancaria'], ENT_COMPAT, ''); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Clabe Interbancaria:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="a_cuenta_bancaria_clabe" id="a_cuenta_bancaria_clabe" class="form-control" placeholder="Clabe interbancaria" value="<?php echo htmlentities($row_contratos['a_cuenta_bancaria_clabe'], ENT_COMPAT, ''); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

     
<?php } ?>
