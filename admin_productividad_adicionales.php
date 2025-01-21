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


mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
$desfase = $row_variables['dias_desfase'];
date_default_timezone_set("America/Mexico_City");
$fecha = date("Y-m-d"); // la fecha actual
$anio = $row_variables['anio'];
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
echo "update";

$updateSQL = sprintf("UPDATE prod_meses_presupesto_adicional SET justificacion=%s, resultado=%s, IDusuario=%s  WHERE IDadicional=%s",
                       GetSQLValueString($_POST['justificacion'], "text"),
                       GetSQLValueString($_POST['resultado'], "text"),
                       GetSQLValueString($_POST['IDusuario'], "text"),
                       GetSQLValueString($_POST['IDadicional'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_productividad_adicionales.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
echo "insert";

$Eanio = $_POST['IDanio'];
$Esemana = $_POST['IDsemana'];
$Ematriz = $_POST['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_adicionalesp = "SELECT * FROM prod_meses_presupesto_adicional WHERE IDestatus = 1 AND IDanio = $Eanio AND IDsemana = $Esemana AND IDmatriz = $Ematriz AND IDusuario <> 1413";
$adicionalesp = mysql_query($query_adicionalesp, $vacantes) or die(mysql_error());
$row_adicionalesp = mysql_fetch_assoc($adicionalesp);
$totalRows_adicionalesp = mysql_num_rows($adicionalesp);
echo $query_adicionalesp;

if($totalRows_adicionalesp > 0) {
header("Location: admin_productividad_adicionales.php?info=4"); 
} else {
	
$insertSQL = sprintf("INSERT INTO prod_meses_presupesto_adicional (justificacion, IDestatus, IDsemana, IDanio, IDmatriz, resultado, IDusuario) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['justificacion'], "text"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['IDsemana'], "int"),
                       GetSQLValueString($_POST['IDanio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['resultado'], "int"),
                       GetSQLValueString($_POST['IDusuario'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "admin_productividad_adicionales.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}}

if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
	$IDadicional_b = $_GET["IDadicional"]; 
	$query1 = "UPDATE prod_meses_presupesto_adicional SET IDestatus = 0, IDusuario = $el_usuario WHERE IDadicional = '$IDadicional_b'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: admin_productividad_adicionales.php?info=3"); 	
}

if (!isset($_POST['el_anio'])) { $el_anio = $anio;} else { $el_anio = $_POST['el_anio'];}
$query_adicionales = "SELECT vac_matriz.matriz,  prod_meses_presupesto_adicional.IDadicional, prod_meses_presupesto_adicional.justificacion, prod_meses_presupesto_adicional.IDsemana,  prod_meses_presupesto_adicional.IDanio,  prod_meses_presupesto_adicional.IDmatriz,  prod_meses_presupesto_adicional.resultado FROM prod_meses_presupesto_adicional INNER JOIN vac_matriz ON  prod_meses_presupesto_adicional.IDmatriz = vac_matriz.IDmatriz WHERE prod_meses_presupesto_adicional.IDestatus = 1 AND prod_meses_presupesto_adicional.resultado > 0 AND prod_meses_presupesto_adicional.IDusuario != 1413 AND prod_meses_presupesto_adicional.IDanio = $el_anio";
mysql_query("SET NAMES 'utf8'"); 
$adicionales = mysql_query($query_adicionales, $vacantes) or die(mysql_error());
$row_adicionales = mysql_fetch_assoc($adicionales);
$totalRows_adicionales = mysql_num_rows($adicionales);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz1 = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)  ORDER BY matriz ASC";
$matriz1 = mysql_query($query_matriz1, $vacantes) or die(mysql_error());
$row_matriz1 = mysql_fetch_assoc($matriz1);
$totalRows_matriz1 = mysql_num_rows($matriz1);

mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT DISTINCT prod_captura.semana FROM prod_captura WHERE prod_captura.anio = $anio AND semana > 0 GROUP BY prod_captura.semana ORDER BY semana DESC";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);
$totalRows_semanas = mysql_num_rows($semanas);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<!-- /theme JS files -->
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


	                <!-- Content area -->
				<div class="content">
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han agregado correctamente el adicional.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han actualizado correctamente el adicional.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han borrado correctamente el adicional.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Ya existe un monto en la semana indicada.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Gasto adicional</h5>
						</div>

					<div class="panel-body">
					  <p>A continuación se muestran los montos adicionales a presupuesto para el pago de Productividad.<br/>
					  Para agregar algún adicional, se debe contar con la autorización por correo del <strong>Gerente Nacional de Almacén</strong> y/o del <strong>Gerente Nacional de Distribución</strong>, según sea el caso.<br/>
					  Se debe agregar la jutificación correspondiente en cada captura.</p>
					<button type="button" data-target="#modal_agregar"  data-toggle="modal" class="btn btn-success">Agregar</button>
					  <p>&nbsp;</p>

                    
					  <form method="POST" action="admin_productividad_adicionales.php">
					<table class="table">
							<tr>
                           <td><select name="el_anio" class="form-control">
						   <option value="2025"<?php if ($el_anio == 2025) {echo "selected=\"selected\"";} ?>>2025</option>
						   <option value="2024"<?php if ($el_anio == 2024) {echo "selected=\"selected\"";} ?>>2024</option>
						   <option value="2023"<?php if ($el_anio == 2023) {echo "selected=\"selected\"";} ?>>2023</option>
						   <option value="2022"<?php if ($el_anio == 2022) {echo "selected=\"selected\"";} ?>>2022</option>
									</select>
                            </td>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button> 
							</td>
					      </tr>
				    </table>
				</form>



					  <table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-primary"> 
                      <th>ID</th>
                      <th>Matriz</th>
                      <th>Año</th>
                      <th>Semana</th>
                      <th>Monto</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
						<?php do { ?>
                        <tr>
                            <td><?php echo $row_adicionales['IDadicional']; ?></td>
                            <td><?php echo $row_adicionales['matriz']; ?></td>
                            <td><?php echo $row_adicionales['IDanio']; ?></td>
                            <td><?php echo $row_adicionales['IDsemana']; ?></td>
                            <td>$<?php echo number_format($row_adicionales['resultado'],2); ?></td>
                            <td>
							<button type="button" data-target="#modal_actualizar<?php echo $row_adicionales['IDadicional']; ?>"  data-toggle="modal" class="btn btn-primary  btn-sm">Actualizar</button>
							<button type="button" data-target="#modal_borrar<?php echo $row_adicionales['IDadicional']; ?>"  data-toggle="modal" class="btn btn-danger btn-sm">Borrar</button>
							</td>
                        </tr>
						
						
									<!-- danger modal -->
									<div id="modal_borrar<?php echo $row_adicionales['IDadicional']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de borrado</h6>
												</div>
												<div class="modal-body">
												<p>¿Estas seguro que quieres borrar el registro?</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="admin_productividad_adicionales.php?IDadicional=<?php echo $row_adicionales['IDadicional']; ?>&borrar=1">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->



									 <!-- danger modal -->
									<div id="modal_actualizar<?php echo $row_adicionales['IDadicional']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Actualizar Monto</h6>
												</div>
												<div class="modal-body">
																			
														<form action="admin_productividad_adicionales.php" method="post" name="form1" id="form1" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Monto:</label>
															  <div class="col-lg-9">
																	<input name="resultado" id="resultado" type="number" class="form-control" value="<?php echo $row_adicionales['resultado']; ?>" required="required" placeholder="Monto adicional">
															 </div>
														  </div>
														  <!-- /basic text input -->
					  <p>&nbsp;</p>
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Jutificación:</label>
															  <div class="col-lg-9">
																<textarea name="justificacion" rows="3" class="form-control" id="justificacion" required="required" placeholder="Justificación del incremento en el Presupuesto."><?php echo $row_adicionales['justificacion']; ?></textarea>															 
															  </div>
														  </div>
														  <!-- /basic text input -->


														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-primary">Actualizar</button> 
													<input type="hidden" name="MM_update" value="form1" />
													<input type="hidden" name="IDadicional" value="<?php echo $row_adicionales['IDadicional']; ?>" />
													<input type="hidden" name="IDusuario" value="<?php echo $row_usuario['IDusuario']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->
						
					 <?php } while ($row_adicionales = mysql_fetch_assoc($adicionales)); ?>
                    </tbody>
                   </table> 



									 <!-- danger modal -->
									<div id="modal_agregar" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Agregar Monto</h6>
												</div>
												<div class="modal-body">
																			
														<form action="admin_productividad_adicionales.php" method="post" name="form1" id="form1" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Año:</label>
															  <div class="col-lg-9">
															<select name="IDanio" id="IDanio" class="form-control" required="required">
															<option value="2025">2025</option>
															<option value="2024">2024</option>
																<option value="2023">2023</option>
																<option value="2022">2022</option>
															</select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Matriz:</label>
															  <div class="col-lg-9">
																<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
																	<option value="">Seleccione una opción</option> 
																		  <?php do {  ?>
																		  <option value="<?php echo $row_matriz1['IDmatriz']?>"><?php echo $row_matriz1['matriz']?></option>
																		  <?php
																		 } while ($row_matriz1 = mysql_fetch_assoc($matriz1));
																		 $rows = mysql_num_rows($matriz1);
																		 if($rows > 0) {
																		 mysql_data_seek($matriz1, 0);
																		 $row_matriz1 = mysql_fetch_assoc($matriz1);
																		 } ?>
															  </select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Semana:</label>
															  <div class="col-lg-9">
																<select name="IDsemana" id="IDsemana" class="form-control" required="required">
																	<option value="">Seleccione una opción</option> 
																		  <?php do {  ?>
																		  <option value="<?php echo $row_semanas['semana']?>"><?php echo $row_semanas['semana']; if ($row_semanas['semana'] == $semana) { echo " (actual)"; } ?></option>
																		  <?php
																		 } while ($row_semanas = mysql_fetch_assoc($semanas));
																		 $rows = mysql_num_rows($semanas);
																		 if($rows > 0) {
																		 mysql_data_seek($semanas, 0);
																		 $row_semanas = mysql_fetch_assoc($semanas);
																		 } ?>
															  </select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Monto:</label>
															  <div class="col-lg-9">
																	<input name="resultado" id="resultado" type="number" class="form-control" value="" required="required" placeholder="Monto adicional">
															 </div>
														  </div>
														  <!-- /basic text input -->

														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Jutificación:</label>
															  <div class="col-lg-9">
																<textarea name="justificacion" rows="3" class="form-control" id="justificacion" required="required" placeholder="Justificación del incremento en el Presupuesto."></textarea>
															  </div>
														  </div>
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-success">Agregar</button> 
													<input type="hidden" name="IDestatus" value="1" />
													<input type="hidden" name="IDusuario" value="<?php echo $row_usuario['IDusuario']; ?>" />
													<input type="hidden" name="MM_insert" value="form1" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->                                     

                    </div>
				  </div>


<!-- Footer -->
					<div class="footer text-muted">
	&copy; 2020. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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