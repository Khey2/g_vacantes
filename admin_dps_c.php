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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$IDpuesto = $_GET['IDpuesto'];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE vac_puestos.IDpuesto = '$IDpuesto'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);

$query_puesto_general = "SELECT sed_dps.IDpuesto, sed_dps.IDcriterio, sed_dps.b_mision, sed_dps.e_jefe_de_jefe, sed_dps.e_jefe, sed_dps.e_pares, sed_dps.e_colaboradores, sed_dps.f_escolaridad, sed_dps.f_avance, sed_dps.f_carreras, sed_dps.f_idioma, sed_dps.f_idioma_nivel, sed_dps.f_otros_estudios, sed_dps.f_conocimientos1, sed_dps.f_conocimientos2, sed_dps.f_conocimientos3, sed_dps.f_conocimientos4, sed_dps.f_conocimientos5, sed_dps.f_exp_areas, sed_dps.f_exp_anios, sed_dps.f_viajar, sed_dps.f_frecuencia, sed_dps.f_edad, sed_dps.f_turnos, sed_dps.IDplaza, vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.descrito FROM sed_dps LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = sed_dps.IDpuesto WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_general = mysql_query($query_puesto_general, $vacantes) or die(mysql_error());
$row_puesto_general = mysql_fetch_assoc($puesto_general);

$query_puesto_catalogos = "SELECT sed_dps_catalogos.IDcriterio, sed_dps_catalogos.IDpuesto, sed_dps_catalogos.criterio, sed_dps_catalogos.criterio_a, sed_dps_catalogos.criterio_b, 	sed_dps_catalogos.criterio_c, sed_dps_catalogos.criterio_d, sed_dps_catalogos.IDplaza FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'd' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos = mysql_query($query_puesto_catalogos, $vacantes) or die(mysql_error());
$row_puesto_catalogos = mysql_fetch_assoc($puesto_catalogos);
$totalRows_puesto_catalogos = mysql_num_rows($puesto_catalogos);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
  $insertSQL = sprintf("INSERT INTO sed_dps_catalogos (IDcriterio, IDpuesto, criterio, criterio_a, criterio_b, criterio_c) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDcriterio'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "text"),
                       GetSQLValueString($_POST['criterio'], "text"),
                       GetSQLValueString($_POST['criterio_a'], "text"),
                       GetSQLValueString($_POST['criterio_b'], "text"),
                       GetSQLValueString($_POST['criterio_c'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  header("Location: admin_dps_c.php?info=1&IDpuesto=$IDpuesto");
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE sed_dps_catalogos SET criterio_a=%s, criterio_b=%s, criterio_c=%s WHERE IDcriterio=%s",
                       GetSQLValueString($_POST['criterio_a'], "text"),
                       GetSQLValueString($_POST['criterio_b'], "text"),
                       GetSQLValueString($_POST['criterio_c'], "text"),
                       GetSQLValueString($_POST['IDcriterio'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  header("Location: admin_dps_c.php?info=2&IDpuesto=$IDpuesto");
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
	<script src="global_assets/js/core/libraries/jquery_ui/core.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/effects.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_all.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_childcounter.js"></script>
    
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
		<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
	<!-- /theme JS files -->
	<!-- /theme JS files -->
</head>

 <body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

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
							El registro se ha agregado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                      	<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro se ha actualizado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro se ha borrado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                                                    
				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
                        
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Descriptivo de Puesto - Relaciones</h5>
						</div>

					<div class="panel-body">

<p><strong>Instrucciones</strong></p>
			<p>La mayoria de los puestos tienen relaciones tanto internas, como externas, con las que se establece una relación funcional con motivo de su desempeño, tanto dentro de la propia organización, como fuera de ella.</p>
            <p>Recuerda que debse mencionar de manera breve, en qué consiste la relación del puesto. Solo agrega relaciones relacionadas con los resultados del puesto.</p>
            <p>Las relaciones internas son con personal dentro de Sahuayo, las externas con personal que no es de la empresa (proveedores, clientes, servicios, gobierno, etc).

				<table class="table datatable-button-html5-basic">
		          <thead>
                    <tr> 
		              <th class="col-lg-2">Tipo</th>
		              <th class="col-lg-3">Con quien</th>
		              <th class="col-lg-5">Para que</th>
		              <th class="col-lg-2">Acciones</th>
		            </tr>
		            </thead>
		          <tbody>
                <?php 	if ($totalRows_puesto_catalogos == 0) { ?>
                   <tr>
                     <td colspan="4">No se encontraron relaciones capturadas.</td>
                   </tr>
                <?php } else { ?> 
                 <?php  do {  ?>
		          <tr>
		            <td><?php if ($row_puesto_catalogos['criterio_a'] == 1) { echo "Internas";} else { echo "Externas";}?></td>
		            <td><?php echo $row_puesto_catalogos['criterio_b'];?></td>
		            <td><?php echo $row_puesto_catalogos['criterio_c'];?></td>
                    <td>
                     <button type="button" data-target="#modal_theme_ver<?php echo $row_puesto_catalogos['IDcriterio']; ?>"  data-toggle="modal" class="btn btn-primary btn-icon">Editar</button>
                     <button type="button" data-target="#modal_theme_danger<?php echo $row_puesto_catalogos['IDcriterio']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                     </td>

                    <!-- vista video modal -->
					<div id="modal_theme_ver<?php echo $row_puesto_catalogos['IDcriterio']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar DPs</h6>
								</div>


				<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="form-horizontal form-validate-jquery">
                    <fieldset class="content-group">

								<div class="modal-body">

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Tipo:*</label>
										<div class="col-lg-9">
                                        <select name="criterio_a" id="criterio_a" class="form-control" required="required">
											<option value="1"<?php if (!(strcmp($row_puesto_catalogos['criterio_a'], 1))) {echo "SELECTED";} ?>>Internas</option>
											<option value="2"<?php if (!(strcmp($row_puesto_catalogos['criterio_a'], 2))) {echo "SELECTED";} ?>>Externas</option>
                                            </select>            
										</div>
									</div>
									<!-- /basic text input -->
<p>&nbsp;</p>

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Desglose de actividades: <br/>(Cómo lo hace):*</label>
										<div class="col-lg-9">
                                        <textarea rows="4" class="form-control" id="criterio_b" name="criterio_b" placeholder="Cómo lo hace"
                                             required="required"><?php echo $row_puesto_catalogos['criterio_b']; ?></textarea>                         
										</div>
									</div>
									<!-- /basic text input -->
<p>&nbsp;</p>

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Finalidad <br/>(Para que lo hace):*</label>
										<div class="col-lg-9">
                                        <textarea rows="4" class="form-control" id="criterio_c" name="criterio_c" placeholder="Para que lo hace"
                                             required="required"><?php echo $row_puesto_catalogos['criterio_c']; ?></textarea>                          
										</div>
									</div>
									<!-- /basic text input -->

								</div>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<hr>

								<div class="modal-footer">
                                 	<input type="hidden" name="IDcriterio" value="<?php  echo $row_puesto_catalogos['IDcriterio']; ?>">
                                 	<input type="hidden" name="MM_update" value="form1">
                                    <input type="submit" class="btn btn-primary" value="Actualizar">
                                 	<input type="hidden" name="criterio" value="d">
                                 	<input type="hidden" name="IDpuesto" value="<?php echo $IDpuesto; ?>">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
                           
                    </fieldset>
				</form>
							</div>
						</div>
					</div>
					<!-- /vista video modal -->

                    
                    <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_puesto_catalogos['IDcriterio']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la información capturada?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                     <a class="btn btn-danger" href="admin_dps_borrar.php?IDcriterio=<?php echo $row_puesto_catalogos['IDcriterio']; ?>&IDpuesto=<?php echo $IDpuesto; ?>&pagina=c">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                    
		          </tr>
				<?php  } while ($row_puesto_catalogos = mysql_fetch_assoc($puesto_catalogos)); ?>	
                <?php }?>

                  </tbody>
		          </table>                    



                    <!-- vista video modal -->
					<div id="modal_theme_ver" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualizar DPs</h6>
								</div>


				<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="form-horizontal form-validate-jquery">
                    <fieldset class="content-group">

								<div class="modal-body">

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Tipo:*</label>
										<div class="col-lg-9">
                                        <select name="criterio_a" id="criterio_a" class="form-control" required="required">
											<option value="1">Internas</option>
											<option value="2">Externas</option>
                                            </select>            
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">¿Con quien?:*</label>
										<div class="col-lg-9">
											<textarea rows="4" class="form-control" id="criterio_b" name="criterio_b" placeholder="Proveedores"
                                             required="required"></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">¿Para que?:*</label>
										<div class="col-lg-9">
											<textarea rows="4" class="form-control" id="criterio_c" name="criterio_c" placeholder="Para la negociación de precios."
                                             required="required"></textarea>
										</div>
									</div>
									<!-- /basic text input -->

								</div>

<hr>
								<div class="modal-footer">
                                 	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="submit" class="btn btn-success" value="Agregar">
                                 	<input type="hidden" name="criterio" value="d">
                                 	<input type="hidden" name="IDpuesto" value="<?php echo $IDpuesto; ?>">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
                           
                    </fieldset>
				</form>
							</div>
						</div>
					</div>
					<!-- /vista video modal -->








                    
                    </div>
                    </div>

					<!-- /Contenido -->



                            
						</div>
					</div>
					<!-- /detached content -->


					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">


								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Acciones</span>
									</div>

									<div class="category-content">

									
										<div class="form-group">
									<button type="button" data-target="#modal_theme_ver"  data-toggle="modal" class="btn btn-xs btn-warning btn-block content-grou">Agregar relacion</button>
										</div>

									
										<div class="form-group">
									<a class="btn btn-xs btn-primary btn-block content-group" href="admin_dps_desc.php?IDpuesto=<?php echo $IDpuesto; ?>">Regresar al resumen</a>
										</div>

										<div class="form-group">
                                        <a class="btn btn-xs btn-success btn-block content-group" href="dps/imprimir.php?IDpuesto=<?php echo $IDpuesto; ?>">Imprimir</a>
                                        </div>

									</div>
								</div>
								<!-- /course details -->


								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Información del Puesto</span>
									</div>

									<div class="category-content">

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Denominacióm:</label>
											<div><?php echo $row_puesto['denominacion']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $row_puesto['area']; ?></div>
										</div>
                                        
										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto Tipo:</label>
											<div><?php if ($row_puesto['tipo'] == 1) {echo "SI";} else {echo "NO";} ?></div>
										</div>

                                        </div>
								</div>
								<!-- /course details -->

							</div>
						</div>
					</div>
		            <!-- /detached sidebar -->


					<!-- /Contenido -->

				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
			    </div>
				    <!-- /footer -->
                </div>
				<!-- /content area -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>