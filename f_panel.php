<?php require_once('Connections/vacantes.php'); ?>
<?php

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1"); 
$restrict->addLevel("2");
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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT prod_activosj.IDturno AS Elturno, prod_activosj.IDsucursal AS Lasucursal, prod_activosj.IDjornada, prod_activosj.IDhoffice, prod_activosj.comentarios, prod_activosj.IDempleadoJ AS Eljefe, prod_activos.*  FROM prod_activos INNER JOIN prod_activosj ON prod_activos.IDempleado = prod_activosj.IDempleado WHERE prod_activos.IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];
$el_boss = $row_usuario['Eljefe']; 
$el_turno = $row_usuario['Elturno'];
$la_sucursal = $row_usuario['Lasucursal']; 
$la_jornada = $row_usuario['IDjornada']; 
$hoffice = $row_usuario['IDhoffice'];

$lanzar = 0;
 if ($el_boss == 0 OR $el_turno == 0 OR $la_sucursal == 0 OR $la_jornada == 0) { $lanzar = 1; }

 
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE IDempleado <> '$el_usuario' OR manual IS NOT NULL AND IDmatriz = '$IDmatriz' ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$IDempleadoJ = $_POST['IDempleadoJ'];
$IDturno = $_POST['IDturno'];
$IDsucursal = $_POST['IDsucursal'];
$IDjornada = $_POST['IDjornada'];
$IDhoffice = $_POST['IDhoffice'];
$comentarios = $_POST['comentarios'];

$query1 = "UPDATE prod_activos SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$el_usuario'"; 
$result1 = mysql_query($query1) or die(mysql_error());  
  
mysql_select_db($database_vacantes, $vacantes);
$query_boss = "SELECT * FROM prod_activosj WHERE IDempleado = '$el_usuario'"; 
$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
$totalRows_boss = mysql_num_rows($boss);
  
if ($totalRows_boss == 0 ) {
$query2 = "INSERT INTO prod_activosj (IDempleado, IDempleadoJ, IDturno, IDsucursal, IDjornada, IDhoffice, comentarios) VALUES ($el_usuario, $IDempleadoJ, $IDturno, $IDsucursal, $IDjornada, $IDhoffice, '$comentarios')"; 
$result2 = mysql_query($query2) or die(mysql_error());  
} else {
$query2 = "UPDATE prod_activosj SET IDempleadoJ = $IDempleadoJ, IDturno =  $IDturno, IDsucursal = $IDsucursal, IDjornada = $IDjornada, IDhoffice = $IDhoffice, comentarios = '$comentarios' WHERE IDempleado = '$el_usuario'"; 
$result2 = mysql_query($query2) or die(mysql_error());  
}
header("Location: f_panel.php?info=2"); 	
}

$cambio_pass = $row_variables['cambio_pass'];
if ($row_usuario['password'] == md5($row_usuario['IDempleado'])) { header("Location: f_cambio_pass.php?info=6"); } 
if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = '$IDmatriz'";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>

 	<script>
	<?php if ($lanzar == 1) { ?> 
	 $(document).ready(function(){ $("#modal_theme_danger").modal('show'); }); 
	<?php } ?>
	</script>

</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
	<?php require_once('assets/f_mainnav.php'); ?>
	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">


		<?php require_once('assets/f_menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>
			
            <!-- Content area -->
				<div class="content">
                


                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 3)) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cambiado el password correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 6)) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Gracias por tu participación en la Encuesta de la NOM-035, tus respuestas se han guardado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-success alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente tu información.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                  <div class="panel panel-flat">

					<div class="panel-body">
					 <h5>Datos Personales</h5>
					 <p>
					Da clic en el siguiente botón para consultar o actualizar tus datos.
					
					 <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-info btn-icon">Datos</button>

					
					</p>
					</div>
                  </div>




					<!-- Contenido -->
                  <div class="panel panel-flat">

					<div class="panel-body">
                    <h4 class="text-center text-danger content-group-lg">
								Bienvenido(a) al <?php echo $row_variables['nombre_sistema']; ?>
								<small class="display-block">Sahuayo 2020</small>
							</h4>

			<p><strong>¿Sabías que?</strong> </p>
		    <p>La Evaluación  de Desempeño es un  proceso sistemático y periódico que sirve para estimar cuantitativamente y  cualitativamente el grado de eficacia y eficiencia de las personas en el desempeño de sus puestos de  trabajo, mostrándoles sus fortalezas y áreas de oportunidad con el fin de  generar un proceso de mejora continúa. <br />
		      </p>
               <p>&nbsp;</p>
		    <p>1.-  Beneficios para el jefe inmediato. </p>
		    <ul>
		      <li>Tiene la  oportunidad de evaluar el desempeño y comportamiento de sus subordinados con un  sistema objetivo.</li>
		      <li>Podrá  generar planes de acción con el fin de mejorar el desempeño de sus  colaboradores.</li>
		      <li>Alcanza  una mejor comunicación con sus subordinados y fomenta la retroalimentación.  </li>
		    </ul>
               <p>&nbsp;</p>
		    <p>2.-  Beneficios para ti.</p>
		    <ul>
		      <li>Conocerás  el nivel de expectativas que se tienen respecto a tu desempeño.</li>
		      <li>Mantendrás  una relación de equidad y justicia con tus  compañeros.</li>
		      <li>Estimulará a que realices tus  mejores esfuerzos.</li>
		    </ul>
               <p>&nbsp;</p>
		    <p>3.-  Beneficios para la empresa. </p>
		    <ul>
		      <li>Alinear las metas estratégicas de crecimiento, rentabilidad y liqudez a los objetivos de desempeño individual.</li>
		      <li>Identificar a los colaboradores  que requieran perfeccionamiento en determinadas áreas.</li>
		      <li>Considerar a los empleados con mejores resultados en los programas de promoción y desarrollo.</li>
              </ul>
                   </div>
                  </div>


					<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder6 position-left"></i>
										Archivos disponibles para descarga
									</h6>
								</div>
								
								<div class="list-group no-border">
									<a href="files/sed_captura_objetivos.pdf" target="_blank" class="list-group-item">
										<i class="icon-file-pdf"></i> Guia de Captura de Objetivos de Desempeño<span class="label bg-success-400">Nuevo</span>
									</a>
									<a href="files/sed_revision_final.pdf" target="_blank" class="list-group-item">
										<i class="icon-file-pdf"></i> Guia de Revisión Final de Desempeño <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="files/sed_revision_semestral.pdf" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Guia de Revisión Semestral de Desempeño
									</a>

								</div>
					</div>
                     


                <div class="panel panel-flat">
                    		<!-- Simple inline block with icon and button -->
							<div class="panel-body">
								<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-lifebuoy icon-3x text-muted no-edge-top"></i>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold">Contacto</h6>
										<span class="text-muted">Para cualquier duda o sugerencia, favor de contactarnos al  correo electrónico: <a href="mailto:<?php echo $row_variables['contacto_interno']; ?>"><?php echo $row_variables['contacto_interno']; ?></a>	</span>
									</div>

									<div class="media-right media-middle">
										<a href="mailto:<?php echo $row_variables['contacto_interno']; ?>" class="btn btn-primary">Enviar correo</a>
									</div>
								</div>
							</div>
							<!-- /simple inline block with icon and button -->
                    
				</div>


                  <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-info">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualización de datos personales</h6>
								</div>

								<div class="modal-body">


                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                                   <!-- Basic text input -->
									<div class="form-group">
										Por favor proporciona la siguiente información.
									</div>
									<!-- /basic text input -->

			<!-- Basic select -->
			<div class="form-group">
				<div class="col-lg-12">Jefe Inmediato:
					<select class="bootstrap-select" data-live-search="true" data-width="100%" name="IDempleadoJ" id="IDempleadoJ" required="required">
								<option value="">Selecciona el nombre de tu jefe inmediato</option>
								<?php  do { ?>
								<option value="<?php echo $row_jefes['IDempleado']?>"<?php if (!(strcmp($row_jefes['IDempleado'], $el_boss))) 
								{echo "SELECTED";} ?>>
								<?php echo $row_jefes['emp_nombre'] . " " . $row_jefes['emp_paterno'] . " " . $row_jefes['emp_materno']. " (" . $row_jefes['denominacion'] . ")";?></option>
								<?php
								} while ($row_jefes = mysql_fetch_assoc($jefes));
								$rows = mysql_num_rows($jefes);
								if($rows > 0) {
								mysql_data_seek($jefes, 0);
								$row_jefes = mysql_fetch_assoc($jefes);
								} ?>
				</select>
				</div>
			</div>
			<!-- /basic select -->

<?php if ($row_usuario['IDarea'] > 4) { ?>
									<!-- Basic select -->
									<div class="form-group">
										<div class="col-lg-12">Horario:
 											<select class="form-control" name="IDturno" id="IDturno" required="required">
													  <option value="">Selecciona una opción...</option>
													  <option value="1"<?php if ($row_usuario['Elturno']== 1)  {echo "SELECTED";} ?>>8:00 a 17:00 Horas</option>
													  <option value="2"<?php if ($row_usuario['Elturno']== 2)  {echo "SELECTED";} ?>>9:00 a 18:00 Horas</option>
													  <option value="3"<?php if ($row_usuario['Elturno']== 3)  {echo "SELECTED";} ?>>Otro (especificar en comentarios)</option>
										</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<div class="col-lg-12">Jornada Laboral:
 											<select class="form-control" name="IDjornada" id="IDjornada" required="required">
													  <option value="">Selecciona una opción...</option>
													  <option value="1"<?php if ($row_usuario['IDjornada']== 1)  {echo "SELECTED";} ?>>Lunes a Viernes</option>
													  <option value="2"<?php if ($row_usuario['IDjornada']== 2)  {echo "SELECTED";} ?>>Lunes a Sabado</option>
													  <option value="3"<?php if ($row_usuario['IDjornada']== 3)  {echo "SELECTED";} ?>>	 (especificar en comentarios)</option>
										</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<div class="col-lg-12">¿Laboras en Home Office?:
 											<select class="form-control" name="IDhoffice" id="IDhoffice" required="required">
													  <option value="">Selecciona una opción...</option>
													  <option value="1"<?php if ($row_usuario['IDhoffice']== 1)  {echo "SELECTED";} ?>>Si</option>
													  <option value="2"<?php if ($row_usuario['IDhoffice']== 2)  {echo "SELECTED";} ?>>No</option>
													  <option value="3"<?php if ($row_usuario['IDhoffice']== 3)  {echo "SELECTED";} ?>>Parcial (especificar en comentarios)</option>
										</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<div class="col-lg-12">Ubicacion:
											<select name="IDsucursal" id="IDsucursal" class="form-control" required="required">
                                            	<option value="">Seleccione tu ubicación</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_sucursal['IDsucursal']?>"<?php if (!(strcmp($row_sucursal['IDsucursal'], $row_usuario['Lasucursal']))) {echo "SELECTED";} ?>><?php echo $row_sucursal['sucursal']?></option>
													  <?php
													 } while ($row_sucursal = mysql_fetch_assoc($sucursal));
													 $rows = mysql_num_rows($sucursal);
													 if($rows > 0) {
													 mysql_data_seek($sucursal, 0);
													 $row_sucursal = mysql_fetch_assoc($sucursal);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic text input -->
									<div class="form-group">
									<div class="col-lg-12">Comentarios:
										<div class="col-lg-12">
                                          <textarea name="comentarios" rows="3" class="form-control" id="comentarios" placeholder="Comentarios."><?php echo KT_escapeAttribute($row_usuario['comentarios']); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->


<?php }  ?>



								</div>

								<div class="modal-footer">
									<input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>">
									<button type="submit"  name="KT_Update1" class="btn btn-info">Actualizar</button>
									<input type="hidden" name="MM_update" value="form1">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							 </form>

							</div>
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
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>