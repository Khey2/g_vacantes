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

$query_puesto_general = "SELECT sed_dps.IDpuesto, sed_dps.captura_a, sed_dps.IDcriterio, sed_dps.b_mision, sed_dps.e_jefe_de_jefe, sed_dps.e_jefe, sed_dps.e_pares, sed_dps.e_colaboradores, sed_dps.f_escolaridad, sed_dps.f_avance, sed_dps.f_carreras, sed_dps.f_idioma, sed_dps.f_idioma_nivel, sed_dps.f_otros_estudios, sed_dps.f_conocimientos1, sed_dps.f_conocimientos2, sed_dps.f_conocimientos3, sed_dps.f_conocimientos4, sed_dps.f_conocimientos5, sed_dps.f_exp_areas, sed_dps.f_exp_anios, sed_dps.f_viajar, sed_dps.f_frecuencia, sed_dps.f_edad, sed_dps.f_turnos, sed_dps.IDplaza, vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.descrito FROM sed_dps LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = sed_dps.IDpuesto WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_general = mysql_query($query_puesto_general, $vacantes) or die(mysql_error());
$row_puesto_general = mysql_fetch_assoc($puesto_general);
$totalRows_puesto_general = mysql_num_rows($puesto_general);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO sed_dps (captura_a, IDpuesto, b_mision, e_jefe_de_jefe, e_jefe, e_pares, e_colaboradores) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['captura_a'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['b_mision'], "text"),
                       GetSQLValueString($_POST['e_jefe_de_jefe'], "text"),
                       GetSQLValueString($_POST['e_jefe'], "text"),
                       GetSQLValueString($_POST['e_pares'], "text"),
                       GetSQLValueString($_POST['e_colaboradores'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  header("Location: admin_dps_a.php?info=1&IDpuesto=$IDpuesto");
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE sed_dps SET b_mision=%s, e_jefe_de_jefe=%s, e_jefe=%s, e_pares=%s, captura_a=%s, e_colaboradores=%s WHERE IDpuesto=%s",
                       GetSQLValueString($_POST['b_mision'], "text"),
                       GetSQLValueString($_POST['e_jefe_de_jefe'], "text"),
                       GetSQLValueString($_POST['e_jefe'], "text"),
                       GetSQLValueString($_POST['e_pares'], "text"),
                       GetSQLValueString($_POST['captura_a'], "int"),
                       GetSQLValueString($_POST['e_colaboradores'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  header("Location: admin_dps_a.php?info=2&IDpuesto=$IDpuesto");
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
	<script src="global_assets/js/plugins/extensions/cookie.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_all.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_childcounter.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/extra_trees.js"></script>
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
							<h5 class="panel-title">Descriptivo de Puesto - Datos Generales y Misión</h5>
						</div>

					<div class="panel-body">

			<p><strong>Instrucciones: </strong><br/>
            La misión se debe redactar conforme al propósito fundamental &quot;su razón de ser&quot;.<br/>
            Debe reflejar la congruencia y contribución con el puesto inmediato superior.<br/>
            Debe contestar a la pregunta ¿Para qué existe el puesto?.</p>
			<p>En la sección del Organigrama, indique los puestos, no las personas.</p>
			<p>&nbsp;</p>


		     	
				<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" class="form-horizontal form-validate-jquery">
                    <fieldset class="content-group">


									<legend class="text-bold">Misión</legend>

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Misión del Puesto:*</label>
										<div class="col-lg-10">
                                        <?php $totalRows_puesto_general; if ($totalRows_puesto_general > 0) {?>
                                        <textarea rows="4" class="form-control" id="b_mision" name="b_mision" placeholder="Propósito general del puesto, describe su razón de ser."
                                             required="required"><?php echo $row_puesto_general['b_mision']; ?></textarea>                         
                                        <?php } else { ?>
                                        <textarea rows="4" class="form-control" id="b_mision" name="b_mision" placeholder="Propósito general del puesto, describe su razón de ser."
                                             required="required"></textarea>                         
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->


									<legend class="text-bold">Organigrama</legend>
                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Jefe Inmediato:*</label>
										<div class="col-lg-10">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="e_jefe" id="e_jefe" class="form-control" placeholder="Describe el nombre y puesto del Jefe Inmediato." 
                                        value="<?php echo $row_puesto_general['e_jefe']; ?>" required="required">                                 
                                        <?php } else { ?>
                                        <input type="text" name="e_jefe" id="e_jefe" class="form-control" placeholder="Describe el nombre y puesto del Jefe Inmediato." 
                                        value="" required="required">                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->


                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Jefe del Jefe:*</label>
										<div class="col-lg-10">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <input type="text" name="e_jefe_de_jefe" id="e_jefe_de_jefe" class="form-control" placeholder="Describe el nombre y puesto del Jefe del Jefe Inmediato." 
                                        value="<?php echo $row_puesto_general['e_jefe_de_jefe']; ?>" required="required">                                 
                                        <?php } else { ?>
                                        <input type="text" name="e_jefe_de_jefe" id="e_jefe_de_jefe" class="form-control" placeholder="Describe el nombre y puesto del Jefe del Jefe Inmediato." 
                                        value="" required="required">                                 
                                        <?php } ?>
										</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Pares:*</label>
										<div class="col-lg-10">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <textarea name="e_pares" rows="3" class="form-control" id="e_pares" placeholder="Describe el nombre y puesto de los Pares." required="required"><?php echo $row_puesto_general['e_pares']; ?></textarea>                                 
                                        <?php } else { ?>
                                        <textarea name="e_pares" rows="3" class="form-control" id="e_pares" placeholder="Describe el nombre y puesto de los Pares." required="required"></textarea>                                 
                                        <?php } ?>
									</div>
									</div>
									<!-- /basic text input -->

                                       <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Colaboradores:*</label>
										<div class="col-lg-10">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                        <textarea name="e_colaboradores" rows="3" class="form-control" id="e_colaboradores" placeholder="Describe el nombre y puesto de los Colaboradores" required="required"><?php echo $row_puesto_general['e_colaboradores']; ?></textarea>                                 
                                        <?php } else { ?>
                                        <textarea name="e_colaboradores" rows="3" class="form-control" id="e_colaboradores" placeholder="Describe el nombre y puesto de los Colaboradores" required="required"></textarea>                                 
                                        <?php } ?>
									</div>
									</div>
									<!-- /basic text input -->

								<div class="modal-footer">
                                        <?php  if ($totalRows_puesto_general > 0) {?>
                                 	<input type="hidden" name="MM_update" value="form1">
                                    <input type="submit" class="btn btn-primary" value="Actualizar">
										<?php } else { ?>
                                 	<input type="hidden" name="MM_insert" value="form1">
                                    <input type="submit" class="btn btn-primary" value="Agregar">
                                        <?php } ?>
                                 	<input type="hidden" name="captura_a" value="1">
                                 	<input type="hidden" name="IDpuesto" value="<?php echo $IDpuesto; ?>">
								</div>
                           
                    </fieldset>
				</form>
                    
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


</div></body>
</html>