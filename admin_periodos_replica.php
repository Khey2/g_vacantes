<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

$restrict->addLevel("3");
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->Execute();
//End Restrict Access To Page

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$IDperiodovar = $row_variables['IDperiodo'];


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];


$query_periodos = "SELECT * FROM sed_periodos_sed WHERE IDperiodo > 6"; 
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);

$query_periodos2 = "SELECT * FROM sed_periodos_sed WHERE IDperiodo > 6"; 
$periodos2 = mysql_query($query_periodos2, $vacantes) or die(mysql_error());
$row_periodos2 = mysql_fetch_assoc($periodos2);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT * FROM vac_puestos WHERE vac_puestos.IDaplica_SED = 1 ORDER BY vac_puestos.denominacion ASC";  
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatriz)";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$vista = 0;
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
foreach ($_POST['IDpuesto'] as $puests)	{ $Puestos = implode(", ", $_POST['IDpuesto']);  $PuestosLink = implode(",", $_POST['IDpuesto']); } 
$IDaccion = $_POST['IDaccion'];
$IDperiodo1 = $_POST['IDperiodo1'];
$IDperiodo2 = $_POST['IDperiodo2'];
$vista = 1;

if ($IDaccion == 3) {
mysql_select_db($database_vacantes, $vacantes);
$query_resultadosA = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.denominacion, vac_matriz.matriz, sed_individuales.IDmeta, sed_individuales.IDperiodo, sed_periodos_sed.periodo  FROM prod_activos LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN sed_individuales ON prod_activos.IDempleado = sed_individuales.IDempleado LEFT JOIN sed_periodos_sed ON sed_individuales.IDperiodo = sed_periodos_sed.IDperiodo WHERE prod_activos.IDpuesto IN ($Puestos)  GROUP BY prod_activos.IDempleado";   
mysql_query("SET NAMES 'utf8'");
$resultadosA = mysql_query($query_resultadosA, $vacantes) or die(mysql_error());
$row_resultadosA = mysql_fetch_assoc($resultadosA);
$totalRows_resultadosA = mysql_num_rows($resultadosA);	

} else {
mysql_select_db($database_vacantes, $vacantes);
$query_resultadosA = "SELECT sed_individuales.IDmeta, sed_individuales.IDperiodo, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.denominacion, vac_matriz.matriz, sed_periodos_sed.periodo FROM sed_individuales LEFT JOIN prod_activos ON sed_individuales.IDempleado = prod_activos.IDempleado LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN sed_periodos_sed ON sed_individuales.IDperiodo = sed_periodos_sed.IDperiodo WHERE sed_individuales.IDperiodo = $IDperiodo1 AND prod_activos.IDpuesto IN ($Puestos) GROUP BY sed_individuales.IDempleado";  
mysql_query("SET NAMES 'utf8'");
$resultadosA = mysql_query($query_resultadosA, $vacantes) or die(mysql_error());
$row_resultadosA = mysql_fetch_assoc($resultadosA);
$totalRows_resultadosA = mysql_num_rows($resultadosA);

}

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/inputs/duallistbox.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_dual_listboxes.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
		<?php require_once('assets/mainnav.php'); ?>
		<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/pheader.php'); ?>

<!-- Content area -->
<div class="content">
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha aplicado el cambio correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
					
					
					<?php if ($vista == 0) { ?>
					
					
					<p>Selecciona el o los puestos a los que se les aplica la acción masiva.</p>                    

                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
								<fieldset class="content-group">
                         
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puestos:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto[]" id="IDpuesto" multiple="multiple" class="form-control listbox" required="required">
												  <?php  do { ?>
												  <option value="<?php echo $row_resultados['IDpuesto']?>"><?php echo $row_resultados['denominacion']?></option>
												  <?php
												 } while ($row_resultados = mysql_fetch_assoc($resultados));
												   $rows = mysql_num_rows($resultados);
												   if($rows > 0) {
												   mysql_data_seek($resultados, 0);
												   $row_resultados = mysql_fetch_assoc($resultados);
												 } ?>
											</select>
										</div>
									</div>


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Periodo A:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDperiodo1" id="IDperiodo1" class="form-control" required="required">
												<option value="">Selecciona el Periodo A</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_periodos['IDperiodo']?>"><?php echo $row_periodos['periodo']." (".$row_periodos['IDperiodo'].")";?></option>
												  <?php
												 } while ($row_periodos = mysql_fetch_assoc($periodos));
												   $rows = mysql_num_rows($periodos);
												   if($rows > 0) {
												   mysql_data_seek($periodos, 0);
												   $row_periodos = mysql_fetch_assoc($periodos);
												 } ?>
											</select>
										</div>
									</div>
									<!-- Basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Periodo B:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDperiodo2" id="IDperiodo2" class="form-control" required="required">
												<option value="">Selecciona el Periodo B</option> 
												<option value="0">No aplica</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_periodos2['IDperiodo']?>"><?php echo $row_periodos2['periodo']." (".$row_periodos2['IDperiodo'].")";?></option>
												  <?php
												 } while ($row_periodos2 = mysql_fetch_assoc($periodos2));
												   $rows = mysql_num_rows($periodos2);
												   if($rows > 0) {
												   mysql_data_seek($periodos2, 0);
												   $row_periodos2 = mysql_fetch_assoc($periodos2);
												 } ?>
											</select>
										</div>
									</div>
									<!-- Basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Acciones:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDaccion" id="IDaccion" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <option value="1">A. Poner periodo A como "En proceso"</option>
												  <option value="2">B. Copiar Metas de Periodo A a Periodo B</option>
												  <option value="3">C. Capturar Metas de Periodo A</option>
											</select>
										</div>
									</div>
									<!-- Basic select -->


                          <div class="text-right">
                            <div>
                                <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Ejecutar" />
                       		    <input type="hidden" name="MM_insert" value="form1">
                            </div>
                          </div>

                       </fieldset>
                      </form>
					  
					  
					  
					<?php } else { ?>


					<p>A continuación se muestran los empleados que serán afectados.<br/>
					Solo se muestras empleados con metas capturadas.<br/>
					Deshabilita la casilla de verificación de los Empleados que quieras <strong>EXCLUIR</strong>.<br/>
					Si el empleado ya tiene metas capturas o resultados, éstos serán borrados.</p>
					
					<?php 
					$query_periodosA = "SELECT * FROM sed_periodos_sed WHERE IDperiodo = $IDperiodo1"; 
					$periodosA = mysql_query($query_periodosA, $vacantes) or die(mysql_error());
					$row_periodosA = mysql_fetch_assoc($periodosA);

					$query_periodosA2 = "SELECT * FROM sed_periodos_sed WHERE IDperiodo = $IDperiodo2"; 
					$periodosA2 = mysql_query($query_periodosA2, $vacantes) or die(mysql_error());
					$row_periodosA2 = mysql_fetch_assoc($periodosA2); 

					$ElPeriodo1 = $row_periodosA['periodo'];
					$ElPeriodo2 = $row_periodosA2['periodo'];
					?>	
					
					<?php if ($IDaccion == 1) { echo "Acciones: Se cambiará el estatus de las metas a <strong>EN PROCESO</strong> y el estatus de la evaluación como <strong>EVALUADO</strong> del <strong>". $ElPeriodo1 ."</strong>"; }  ?>
					<?php if ($IDaccion == 2) { echo "Acciones: Se copiarán las Metas del <strong>". $ElPeriodo1 ."</strong> al <strong>" . $ElPeriodo2."</strong>"; }  ?>				
					<?php if ($IDaccion == 3) { echo "Acciones: Se capturarán las Metas del <strong>". $ElPeriodo1 ."</strong>"; }  ?>				

					
					<?php if ($IDaccion != 3) { ?>
                    <form method="post" name="form1" action="admin_periodos_replica2.php">
					<?php } else { ?>
                    <form method="post" name="form1" action="admin_periodos_replica3.php">
					<?php } ?>				
					<input type="hidden" value="<?php echo $IDaccion; ?>" name="IDaccion" id="IDaccion">
					<input type="hidden" value="<?php echo $IDperiodo1; ?>" name="IDperiodo1" id="IDperiodo1">
					<input type="hidden" value="<?php echo $IDperiodo2; ?>" name="IDperiodo2" id="IDperiodo2">
					
					<div class="text text-right">
					<?php if ($totalRows_resultadosA > 0) { ?>
					<button type="submit" class="btn btn-success">Aplicar Acciones</button>
					<?php } ?>									
					 &nbsp;<a class="btn btn-default" href="admin_periodos_replica.php">Cancelar / Regresar</a>
					 </div>
					 
					<p>&nbsp;</p>					
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th></th>
							    <th>No. Emp.</th>
							    <th>Nombre</th>
							    <th>Puesto</th>
							    <th>Matriz</th>
								<th><?php if ($IDaccion == 1) { echo "Estatus"; } else { echo "Metas"; }?></th>		
								<?php if ($IDaccion == 1) { echo "<th>Periodo</th>"; } ?>	
						    </tr>
					    </thead>
						<tbody>							  
						<?php if ($totalRows_resultadosA > 0) { do { 
						
						$el_empleado = $row_resultadosA['IDempleado'];
						
						$query_resultadosB1 = "SELECT * FROM sed_individuales_resultados WHERE IDempleado = $el_empleado AND IDperiodo = $IDperiodo1";  
						mysql_query("SET NAMES 'utf8'");
						$resultadosB1 = mysql_query($query_resultadosB1, $vacantes) or die(mysql_error());
						$row_resultadosB1 = mysql_fetch_assoc($resultadosB1);
						$totalRows_resultadosB1 = mysql_num_rows($resultadosB1);	

										 if ($row_resultadosB1['estatus'] == 0) {$estado = "Sin Captura";} 
									else if ($row_resultadosB1['estatus'] == 1) {$estado = "Capturado";} 
									else if ($row_resultadosB1['estatus'] == 2) {$estado = "Propuesto";} 
									else if ($row_resultadosB1['estatus'] == 3) {$estado = "Evaluado";} 
									else {$estado = "Sin Evaluacion";}

						$query_resultadosB2 = "SELECT * FROM sed_individuales WHERE IDempleado = $el_empleado AND IDperiodo = $IDperiodo2";  
						mysql_query("SET NAMES 'utf8'");
						$resultadosB2 = mysql_query($query_resultadosB2, $vacantes) or die(mysql_error());
						$row_resultadosB2 = mysql_fetch_assoc($resultadosB2);
						$totalRows_resultadosB2 = mysql_num_rows($resultadosB2);	
						?>
							<tr>
								<td><input type="checkbox" name="IDempleado[]" value="<?php echo $row_resultadosA['IDempleado']; ?>" checked="checked"></td>
								<td><?php echo $row_resultadosA['IDempleado']; ?>&nbsp; </td>
								<td><?php echo $row_resultadosA['emp_paterno'] . " " . $row_resultadosA['emp_materno'] . " " . $row_resultadosA['emp_nombre']; ?></td>
								<td><?php echo $row_resultadosA['denominacion']; ?></td>
								<td><?php echo $row_resultadosA['matriz']; ?></td>
								<td><?php 
									 if ($IDaccion == 1) { echo $estado; } 
								else if ($IDaccion == 2) { if($totalRows_resultadosB2 > 0) { echo "Con Metas";} else { echo "Sin Metas";} } 
								else if ($IDaccion == 3) { if($totalRows_resultadosB2 > 0) { echo "Con Metas";} else { echo "Sin Metas";} }
								?>
								<?php if ($IDaccion == 1) { echo "<td>".$ElPeriodo1."</td>"; } ?>	
								</td>
						    </tr>
					    <?php } while ($row_resultadosA = mysql_fetch_assoc($resultadosA)); 
						} else { ?>
							<tr>
								<td colspan="6">No hay criterios con el filtro seleccionado.</td>
						    </tr>
						<?php } ?>
					    </tbody>
				    </table>
					</form> 
					  
					<?php } ?>


				</div>



					<!-- /Contenido -->

					<!-- Footer -->
					<div class="footer text-muted">
						&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>