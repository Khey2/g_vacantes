<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/b_tNG.inc.php');

// Make unified connection variable
$conn_nom35 = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
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
$el_mes = date("m"); 

$mes_actual = date("m")-1;

if (isset($_GET['IDmes'])) {$el_mes = $_GET['IDmes'];} else {$el_mes = $mes_actual;}
if (isset($_GET['anio'])) {$anio = $_GET['anio'];}

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM capa_becarios WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

$IDsucursal = $row_usuario['IDsucursal'];
$el_usuario = $row_usuario['IDempleado'];


$query_catalogo = "SELECT * FROM capa_becarios_texto WHERE IDcapa = 1";
mysql_query("SET NAMES 'utf8'");
$catalogo = mysql_query($query_catalogo, $vacantes) or die(mysql_error());
$row_catalogo = mysql_fetch_assoc($catalogo);



//campos a completar
$validar = 0;
if($row_usuario['rfc'] == '') {$validar = $validar + 1;}
if($row_usuario['curp'] == '') {$validar = $validar + 1;}
if($row_usuario['telefono'] == '') {$validar = $validar + 1;}  
if($row_usuario['emergencias_a'] == '') {$validar = $validar + 1;}  
if($row_usuario['emergencias_b'] == '') {$validar = $validar + 1;}  

if($row_usuario['password'] == '0aab3e28d9e60055ea28acb2338b2676') { header("Location: b_cambio_pass.php?info=4"); }

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE capa_becarios SET rfc=%s, curp=%s, telefono=%s, emergencias_a=%s, emergencias_b=%s WHERE IDempleado=%s",
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['curp'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['emergencias_a'], "text"),
                       GetSQLValueString($_POST['emergencias_b'], "text"),
                       GetSQLValueString($el_usuario, "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: b_panel.php?info=1"); 	
}


mysql_select_db($database_vacantes, $vacantes);
$query_becarios  = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo FROM capa_becarios LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$becarios = mysql_query($query_becarios , $vacantes) or die(mysql_error());
$row_becarios = mysql_fetch_assoc($becarios);
$totalRows_becarios  = mysql_num_rows($becarios );

  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }


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

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>

	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
 	<script>
	<?php if ($validar > 0) { ?> 
	 $(document).ready(function(){ $("#importar").modal('show'); }); 
	<?php } ?>
	</script>
</head>

<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/b_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/b_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/b_pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han guardado correctamente tus datos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


	<div class="panel panel-flat">

	<div class="media panel-body no-margin">
		<div class="media-body">
                                    

								<ul class="media-list">
									<li class="media panel-body stack-media-on-mobile">
										<div class="media-left">
											<a href="#">
												<?php if ($row_becarios['Fotografia'] != '') { ?>
												<img src="<?php echo 'becariosfiles/'.$row_becarios['ELempleado'].'/'.$row_becarios['Fotografia']; ?>" alt="Fotografia" width="80" height="100"><br/>
												<?php } else { ?>
												<img src="files/foto.jpg" alt="Fotografia" width="80" height="100"><br/>
												<?php } ?>
											</a>
										</div>

										<div class="media-body">
											<h6 class="media-heading text-semibold">
												<a href="#"><?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?></a>
											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becarios['tipo']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becarios['fecha_alta'])); ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Modalidad:</strong> <?php if ($row_becarios['IDmodalidad'] == 1) {echo "Presencial";} else if ($row_becarios['IDmodalidad'] == 2) {echo "Remoto ";} else {echo "Mixto";} ?></li>
											</ul>												
										</div>
											
									</li>
								</ul>							
			</div>
		</div>
	</div>
                        
	<div class="panel panel-flat">

	<div class="media panel-body no-margin">
				<div class="media-body">			
				<p><?php echo $row_catalogo['texto_a']; ?></p>                           

				</div>
				</div>


	</div>
	<div class="panel panel-flat">

	<div class="media panel-body no-margin">
				<div class="media-body">			
				<p><img src="becariosfiles/infografia.jpg" alt="Infografia"></p>                           

				</div>
				</div>


	</div>

					<!-- /Contenido -->
					
					
					
					<!-- Modal Importar -->
					<div id="importar" class="modal fade" tabindex="-2">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Captura de información pendiente.</h6>
								</div>

								<div class="modal-body">
									<p>Estimado Becario, existe información de tu perfil que no tenemos, por favor captura los siguientes campos.</p>
									<p>&nbsp;</p>
								
									<form action="b_panel.php" method="post" name="importar" id="importar" class="form-horizontal form-validate-jquery">
									<div class="modal-body">
									<fieldset>
										 
										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">CURP:</label>
											  <div class="col-lg-8">
												<input type="text" name="curp" id="curp" class="form-control" onKeyUp="this.value=this.value.toUpperCase()"value="<?php echo $row_usuario['curp']; ?>" placeholder="CURP" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">RFC:</label>
											  <div class="col-lg-8">
												<input type="text" name="rfc" id="rfc" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo $row_usuario['rfc']; ?>" placeholder="RFC con Homoclave" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Teléfono Personal:</label>
											  <div class="col-lg-8">
												<input type="text" name="telefono" id="telefono" class="form-control" value="<?php echo $row_usuario['telefono']; ?>" placeholder="Teléfono Personal" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Teléfono Emergencias:</label>
											  <div class="col-lg-8">
												<input type="text" name="emergencias_a" id="emergencias_a" class="form-control" value="<?php echo $row_usuario['emergencias_a']; ?>" placeholder="Teléfono de contacto para Emergencias" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->

										 <!-- Basic text input -->
										  <div class="form-group">
											  <label class="control-label col-lg-4">Nombre Emergencias:</label>
											  <div class="col-lg-8">
												<input type="text" name="emergencias_b" id="emergencias_b" class="form-control" value="<?php echo $row_usuario['emergencias_b']; ?>" placeholder="Nombre y parentezco de contacto para Emergencias" required="required">
											 </div>
										  </div>
										  <!-- /basic text input -->
									</fieldset>
									</div>
									<div class="modal-footer">
										<button type="submit" id="submit" name="import" class="btn btn-primary">Actualizar</button> 
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
										<input type="hidden" name="MM_update" value="form1" />
									</div>
									</form>
                                  
                                
                                </div>
							</div>
						</div>
					</div>
					<!-- //Importar  -->

					

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