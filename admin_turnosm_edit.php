<?php require_once('Connections/vacantes.php'); ?> 
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}
$el_mes = $_SESSION['el_mes'];
$nivel = $_SESSION['kt_login_level'];

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_fuente = "SELECT * FROM vac_fuentes";
$fuente = mysql_query($query_fuente, $vacantes) or die(mysql_error());
$row_fuente = mysql_fetch_assoc($fuente);
$totalRows_fuente = mysql_num_rows($fuente);
?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
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
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>


	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>

	<!-- /theme JS files -->
	<script src="includes/common/js/base.js" type="text/javascript"></script>
	<script src="includes/common/js/utility.js" type="text/javascript"></script>
</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="panel.php"><img src="global_assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

			<p class="navbar-text">
				<span class="label bg-success">Online</span>
			</p>

			<div class="navbar-right">
				<ul class="nav navbar-nav">


					<li class="dropdown dropdown-user">
						<a class="dropdown-toggle" data-toggle="dropdown">
							<img src="global_assets/images/placeholders/placeholder.jpg" alt="">
							<span><?php echo $row_usuario['usuario_nombre']; ?></span>
							<i class="caret"></i>
						</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="mi_perfil.php"><i class="icon-user-plus"></i>Mi Perfil</a></li>
                            <li><a href="mi_matriz.php"><i class="icon-cog5"></i>Sucursales</a></li>
							<li><a href="general_faq.php"><i class="icon-help"></i>Ayuda</a></li>
                            <li class="divider"></li>
							<li><a href="logout.php"><i class="icon-switch2"></i> Salir</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></a>
								<div class="media-body">
									<span class="media-heading text-semibold"><?php echo $row_usuario['usuario_nombre']; ?></span>
									<div class="text-size-mini text-muted">
										<i class="icon-pin text-size-small"></i> <?php echo $row_sucursal['sucursal']; ?>
									</div>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<a href="mi_matriz.php"><i class="icon-cog3"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<!-- Main -->
								<li class="navigation-header"><span>Menu</span> <i class="icon-menu" title="Main pages"></i></li>
								<li><a href="panel.php"><i class="icon-home4"></i> <span>Inicio</span></a></li>
																<li>
									<a href="#"><i class="icon-stack2"></i> <span>Vacantes</span></a>
									<ul>
										<li><a href="vacantes_activas.php">Activas</a></li>										
                                        <li><a href="vacantes_cerradas.php">Cerradas</a></li>
										<li><a href="vacantes_totales.php">Todas</a></li>
                                        <li><a href="vacante_edit.php">Agregar</a></li>
									</ul>
								</li>
								<li>
									<a href="indicadores.php"><i class="icon-pie-chart"></i> <span>Indicadores</span></a>
								</li>
								<li>
									<a href="descriptivos.php"><i class="icon-file-text2"></i> <span>Descriptivos</span></a>
								</li>
												</li>
								<?php if( $row_usuario['nivel_acceso'] > 2) {?>
								<li>
									<a href="#"><i class="icon-wrench"></i> <span>Administración</span></a>
									<ul>
										<li><a href="admin_usuarios.php">Usuarios</a></li>
										<li><a href="admin_vacantes.php">Vacantes</a></li>
										<li><a href="admin_vacantes_tabla.php">Totales</a></li>
										<li><a href="admin_indicadores.php">Resultados</a></li>
								<?php if( $row_usuario['corpo'] == 1) {?>
										<li><a href="admin_usuarios_log.php">Log de Usuarios</a></li>
								<?php } ?>
									</ul>
								</li>
								<?php } ?>
								<?php if( $row_usuario['nivel_acceso'] == 4) {?>
								<li>
									<a href="#"><i class="icon-wrench2"></i> <span>Master Admin</span></a>
									<ul>
										<li><a href="admin_usuariosm.php">Usuarios</a></li>
										<li><a href="admin_vacantesm.php">Vacantes</a></li>
										<li><a href="admin_areasm.php">Areas</a></li>
										<li><a href="admin_estatusm.php">Estatus</a></li>
										<li><a href="admin_fuentesm.php">Fuentes</a></li>
										<li><a href="admin_causasm.php">Causas Baja</a></li>
										<li><a href="admin_motivosm.php">Motivos Baja</a></li>
										<li><a href="admin_mesesm.php">Meses</a></li>
										<li><a href="admin_matricesm.php">Matrices</a></li>
										<li><a href="admin_tiposm.php">Tipos Vacantes</a></li>
										<li><a href="admin_turnosm.php">Turnos</a></li>
										<li><a href="admin_respaldos.php">Respaldos</a></li>
										<li><a href="admin_variablesm.php">Variables</a></li>
									</ul>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header page-header-default">
					<div class="page-header-content">

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="panel.php"><i class="icon-home2 position-left"></i> Inicio</a></li>
							<li><a href="vacantes_activas.php">Vacantes</a></li>
							<li class="active">Editar Vacante</li>
						</ul>

					</div>
				</div>				
                <!-- /page header -->
              <p>&nbsp;</p>
               <!-- Content area -->
				<div class="content">

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title"><?php if (@$_GET['IDvacante'] == "") { echo "Agregar Vacante"; } else { echo "Actualizar Vacante"; } ?></h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
  					Una vez guardada la vacante, algunos campos no se pueden editar.</p>
                  <p>&nbsp;</p>
                    <div>
                    <div>
                    
                    Contenido aqui
                    
                    
                    
                      </div>
                      <br />
                    </div>
                    <p>&nbsp;</p>
                  </div>
					</div>
					<!-- /Contenido -->
                    
                    
                     <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la vacante?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_vacantes.php?IDvacante_borrar=<?php echo $row_vacante['IDvacante']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


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