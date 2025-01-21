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

$IDavance = $_GET['IDavance'];	
$IDempleado = $_GET['IDempleado'];	

?>    								<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
<a class="btn btn-danger" href="plan_carrera_tablero_edit.php?IDavance=<?php echo $IDavance; ?>&IDempleado=<?php echo $IDempleado; ?>&borrar=1">Si borrar</a>



