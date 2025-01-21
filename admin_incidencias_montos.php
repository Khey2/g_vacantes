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
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

if (!isset($_SESSION['la_matriz'])) {$_SESSION['la_matriz'] = '';}
if(isset($_POST['la_matriz'])) { $_SESSION['la_matriz'] = $_POST['la_matriz']; }
$la_matriz = $_SESSION['la_matriz'];

if($la_matriz == "") { $x = ""; } else { $x = " WHERE inc_pxv.IDmatriz = " . $la_matriz; } 

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT DISTINCT inc_pxv.IDpxv, vac_matriz.matriz, vac_puestos.denominacion, inc_pxv.IDmatriz, inc_pxv.tipo, inc_pxv.maximo, inc_pxv.monto, inc_pxv.IDpuesto FROM inc_pxv LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = inc_pxv.IDmatriz INNER JOIN vac_puestos ON vac_puestos.IDpuesto = inc_pxv.IDpuesto" . $x ; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);


// actualizar 1
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$updateSQL = sprintf("UPDATE inc_pxv SET monto=%s, maximo=%s WHERE IDpxv=%s",
                       GetSQLValueString($_POST['monto'], "double"),
                       GetSQLValueString($_POST['maximo'], "int"),
                       GetSQLValueString($_POST['IDpxv'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_incidencias_montos.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

// actualizar 1
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$updateSQL = sprintf("INSERT INTO inc_pxv (IDmatriz, IDpuesto, tipo, maximo, monto) VALUES (%s, %s, %s, %s, %s)",
							GetSQLValueString($_POST['IDmatriz'], "int"),
							GetSQLValueString($_POST['IDpuesto'], "int"),
							GetSQLValueString($_POST['tipo'], "int"),
							GetSQLValueString($_POST['maximo'], "int"),
						   	GetSQLValueString($_POST['monto'], "double"));
	
	  mysql_select_db($database_vacantes, $vacantes);
	  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	
	  $updateGoTo = "admin_incidencias_montos.php?info=2";
	  if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	  }
	 header(sprintf("Location: %s", $updateGoTo));
	}
	
// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
	$borrado = $_GET['IDpxv'];
	$deleteSQL = "DELETE FROM inc_pxv WHERE IDpxv ='$borrado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_incidencias_montos.php?info=3");
}
  

mysql_select_db($database_vacantes, $vacantes);
$query_matrize = "SELECT * FROM vac_matriz";
$matrize = mysql_query($query_matrize, $vacantes) or die(mysql_error());
$row_matrize = mysql_fetch_assoc($matrize);
$totalRows_matrize = mysql_num_rows($matrize);

$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1 ORDER BY denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);	

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

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html59.js"></script>
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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
						   <?php if(isset($_GET['info']) && ($_GET['info'] == 1)) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han actualizado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

               			<!-- Basic alert -->
						   <?php if(isset($_GET['info']) && ($_GET['info'] == 2)) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

               			<!-- Basic alert -->
						   <?php if(isset($_GET['info']) && ($_GET['info'] == 3)) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


	                <!-- Content area -->
				<div class="content">
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte semanal de incidencias</h5>
						</div>
                        
					<div class="panel-body">
							<p>Selecciona el monto a editar.</p>

                       <form method="POST" action="admin_incidencias_montos.php">

					<table class="table">
						<tbody>							  
							<tr>
							<td> <div class="col-lg-9 no-prints">
										<select name="la_matriz" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz Todas</option>
                                          <?php do {  ?>
                                           <option value="<?php echo $row_matrize['IDmatriz']?>"<?php if (!(strcmp($row_matrize['IDmatriz'], $la_matriz)))
										   {echo "selected=\"selected\"";} ?>><?php echo $row_matrize['matriz']?></option>
											<?php
                                            } while ($row_matrize = mysql_fetch_assoc($matrize));
                                              $rows = mysql_num_rows($matrize);
                                              if($rows > 0) {
                                                  mysql_data_seek($matrize, 0);
                                                  $row_matrize = mysql_fetch_assoc($matrize);
                                              } ?></select>
										</div>
                              </td>
									<td>
                                <button type="submit" class="btn btn-primary">Filtrar <i class="icon-filter3  position-right"></i></button>	
								<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-success btn-icon">Agregar</button>
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	



					<table class="table table-condensed datatable-button-html5-columns">

                    <thead> 
                    <tr class="bg-primary"> 
                      <th>Sucursal</th>
                      <th>Puesto</th>
                      <th>Tipo</th>
                      <th>Maximo</th>
                      <th>Monto</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
					<?php do { 	?>
                      <tr>
                        <td><?php echo $row_detalle['matriz']; ?>&nbsp; </td>
                        <td><?php echo $row_detalle['denominacion']; ?></td>
                        <td><?php if($row_detalle['tipo'] == 1) {echo "LOCAL";} else {echo "FORANEO";}; ?></td>
                        <td><?php echo $row_detalle['maximo']; ?></td>
                        <td>$<?php echo $row_detalle['monto']; ?></td>
                        <td>
						<button type="button" data-target="#modal_form_inline<?php echo $row_detalle['IDpxv']; ?>" data-toggle="modal" class="btn btn-info btn-icon">Editar</button>
						<button type="button" data-target="#modal_theme_dangers<?php echo $row_detalle['IDpxv']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
						</td>                                           
						</tr>
                        
                      <!-- danger modal -->
					  <div id="modal_theme_dangers<?php echo $row_detalle['IDpxv']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_incidencias_montos.php?IDpxv=<?php echo $row_detalle['IDpxv']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


					

                    <!-- Inline form modal -->
					<div id="modal_form_inline<?php echo $row_detalle['IDpxv']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Actualizar monto PXV</h5>
								</div>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="admin_incidencias_montos.php" >
									<div class="modal-body">
                                	<input type="hidden" name="MM_update" value="form1">
                 		         <p>&nbsp;</p>
                                  <?php echo $row_detalle['matriz']; ?> - <?php echo $row_detalle['denominacion']; ?> - <?php if($row_detalle['tipo'] == 1) {echo "LOCAL";} else {echo "FORANEO";}; ?>
 								<p>&nbsp;</p>

										<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Monto">Monto ($):<span class="text-danger">*</span></label>
												<div class="col-sm-9">
												<input type="text" name="monto" value="<?php echo htmlentities($row_detalle['monto'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											</div>
	                                    </div>
                                        
                                        <div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Monto">Máximo:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
												<input type="number" name="maximo" min="0" max="10" value="<?php echo htmlentities($row_detalle['maximo'], ENT_COMPAT, 'utf-8'); ?>" class="form-control" />
												</div>
											</div>
	                                    </div>
									

									<p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                   		<input type="hidden" name="IDpxv" value="<?php echo $row_detalle['IDpxv']; ?>">
                                        <input type="submit" class="btn btn-primary" value="Actualizar">
								</div>
								</form>
                                </div>
                                </div>
                        
					<?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                    </tbody>
                   </table> 


					</div>
					</div>
					<!-- /panel heading options -->

					


                    <!-- Inline form modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-success">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Agregar PXV</h5>
							</div>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="admin_incidencias_montos.php" >
									<div class="modal-body">
                                       
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_matrize['IDmatriz']?>"><?php echo $row_matrize['matriz']?></option>
													  <?php
													 } while ($row_matrize = mysql_fetch_assoc($matrize));
													 $rows = mysql_num_rows($matrize);
													 if($rows > 0) {
													 mysql_data_seek($matrize, 0);
													 $row_matrize = mysql_fetch_assoc($matrize);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

                                        
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto" id="IDpuesto" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_puesto['IDpuesto']?>"><?php echo $row_puesto['denominacion']?></option>
													  <?php
													 } while ($row_puesto = mysql_fetch_assoc($puesto));
													 $rows = mysql_num_rows($puesto);
													 if($rows > 0) {
													 mysql_data_seek($puesto, 0);
													 $row_puesto = mysql_fetch_assoc($puesto);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

                                        
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="tipo" id="tipo" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
												<option value="1">Local</option>
												<option value="2">Foráneo</option>
                                          	</select>
										</div>
									</div>
									<!-- /basic select -->

                                        
 									<!-- Basic select -->
									 <div class="form-group">
										<label class="control-label col-lg-3">Monto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<input type="monto" name="monto" required="required" class="form-control" />
										</div>
									</div>
									<!-- /basic select -->
                                        
 									<!-- Basic select -->
									 <div class="form-group">
										<label class="control-label col-lg-3">Maximo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<input type="number" name="maximo" min="0" max="10" class="form-control" required="required"/>
										</div>
									</div>
									<!-- /basic select -->
									

									<p>&nbsp;</p>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
										<input type="hidden" name="MM_insert" value="form1">
                                        <input type="submit" class="btn btn-success" value="Agregar">
								</div>
								</form>
                        </div>
						</div>
						</div>





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