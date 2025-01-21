<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');

// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Load the KT_back class
require_once('includes/nxt/KT_back.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

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

// Start trigger
$formValidation = new tNG_FormValidation();
$tNGs->prepareValidation($formValidation);
// End trigger

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
$IDmatriz = $row_usuario['IDmatriz'];

$IDusuario = $row_usuario['IDusuario'];
$las_matrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];
$nivel = $_SESSION['kt_login_level'];

// Make an update transaction instance
$upd_vac_vacante = new tNG_multipleUpdate($conn_vacantes);
$tNGs->addTransaction($upd_vac_vacante);
// Register triggers
$upd_vac_vacante->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_vac_vacante->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_vac_vacante->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_vacantesm.php?IDvacante={IDvacante}");
// Add columns
$upd_vac_vacante->setTable("vac_vacante");
$upd_vac_vacante->addColumn("IDarea", "NUMERIC_TYPE", "POST", "IDarea");
$upd_vac_vacante->addColumn("IDpuesto", "NUMERIC_TYPE", "POST", "IDpuesto");
$upd_vac_vacante->addColumn("IDmotivo_v", "NUMERIC_TYPE", "POST", "IDmotivo_v");
$upd_vac_vacante->addColumn("IDusuario", "NUMERIC_TYPE", "POST", "IDusuario");
$upd_vac_vacante->addColumn("IDmatriz", "NUMERIC_TYPE", "POST", "IDmatriz");
$upd_vac_vacante->addColumn("IDsucursal", "NUMERIC_TYPE", "POST", "IDsucursal");
$upd_vac_vacante->addColumn("IDturno", "NUMERIC_TYPE", "POST", "IDturno");
$upd_vac_vacante->addColumn("sueldo", "DOUBLE_TYPE", "POST", "sueldo");
$upd_vac_vacante->addColumn("fecha_baja", "DATE_TYPE", "POST", "fecha_baja");
$upd_vac_vacante->addColumn("fecha_requi", "DATE_TYPE", "POST", "fecha_requi");
$upd_vac_vacante->addColumn("fecha_ocupacion", "DATE_TYPE", "POST", "fecha_ocupacion");
$upd_vac_vacante->addColumn("IDestatus", "NUMERIC_TYPE", "POST", "IDestatus");
$upd_vac_vacante->addColumn("ajuste_dias", "NUMERIC_TYPE", "POST", "ajuste_dias");
$upd_vac_vacante->addColumn("IDfuente", "NUMERIC_TYPE", "POST", "IDfuente");
$upd_vac_vacante->addColumn("observaciones", "STRING_TYPE", "POST", "observaciones");
$upd_vac_vacante->addColumn("IDusuario2", "NUMERIC_TYPE", "POST", "IDusuario2");
$upd_vac_vacante->addColumn("IDusuario3", "NUMERIC_TYPE", "POST", "IDusuario3");
$upd_vac_vacante->addColumn("IDapoyo", "STRING_TYPE", "POST", "IDapoyo");
$upd_vac_vacante->setPrimaryKey("IDvacante", "NUMERIC_TYPE", "GET", "IDvacante");

// borrar alternativo
if ((isset($_GET['IDvacante_borrar'])) && ($_GET['IDvacante_borrar'] != "")) {
  
  $borrado = $_GET['IDvacante_borrar'];
  $deleteSQL = "DELETE FROM vac_vacante WHERE IDvacante ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_vacantesm.php?info=3");
}

  $la_vacante = $_GET['IDvacante'];
  mysql_select_db($database_vacantes, $vacantes);
  $query_vacante = "SELECT * FROM vac_vacante WHERE IDvacante = '$la_vacante'";
  $vacante = mysql_query($query_vacante, $vacantes) or die(mysql_error());
  $row_vacante = mysql_fetch_assoc($vacante);
  $totalRows_vacante = mysql_num_rows($vacante);
  $usuario_actual = $row_vacante['IDusuario'];
  
  mysql_select_db($database_vacantes, $vacantes);
$query_usuarioact = "SELECT * FROM vac_usuarios WHERE IDusuario = '$usuario_actual'";
$usuarioact = mysql_query($query_usuarioact, $vacantes) or die(mysql_error());
$row_usuarioact = mysql_fetch_assoc($usuarioact);
$totalRows_usuarioact = mysql_num_rows($usuarioact);


// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsvac_vacante = $tNGs->getRecordset("vac_vacante");
$row_rsvac_vacante = mysql_fetch_assoc($rsvac_vacante);
$totalRows_rsvac_vacante = mysql_num_rows($rsvac_vacante);

mysql_select_db($database_vacantes, $vacantes);
$query_motivos_v = "SELECT * FROM vac_motivo_v";
$motivos_v = mysql_query($query_motivos_v, $vacantes) or die(mysql_error());
$row_motivos_v = mysql_fetch_assoc($motivos_v);
$totalRows_motivos_v = mysql_num_rows($motivos_v);

mysql_select_db($database_vacantes, $vacantes);
$query_motivos_baja = "SELECT * FROM vac_motivo_baja";
$motivos_baja = mysql_query($query_motivos_baja, $vacantes) or die(mysql_error());
$row_motivos_baja = mysql_fetch_assoc($motivos_baja);
$totalRows_motivos_baja = mysql_num_rows($motivos_baja);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT * FROM vac_tipo_vacante";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

mysql_select_db($database_vacantes, $vacantes);
$query_apoyo = "SELECT * FROM vac_apoyo";
$apoyo = mysql_query($query_apoyo, $vacantes) or die(mysql_error());
$row_apoyo = mysql_fetch_assoc($apoyo);
$totalRows_apoyo = mysql_num_rows($apoyo);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_estatus = "SELECT * FROM vac_estatus";
$estatus = mysql_query($query_estatus, $vacantes) or die(mysql_error());
$row_estatus = mysql_fetch_assoc($estatus);
$totalRows_estatus = mysql_num_rows($estatus);

mysql_select_db($database_vacantes, $vacantes);
$query_fuente = "SELECT * FROM vac_fuentes";
$fuente = mysql_query($query_fuente, $vacantes) or die(mysql_error());
$row_fuente = mysql_fetch_assoc($fuente);
$totalRows_fuente = mysql_num_rows($fuente);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE tipo <= '$nivel'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_turno= "SELECT * FROM vac_turnos";
$turno= mysql_query($query_turno, $vacantes) or die(mysql_error());
$row_turno= mysql_fetch_assoc($turno);
$totalRows_turno= mysql_num_rows($turno);

mysql_select_db($database_vacantes, $vacantes);
$query_motivos = "SELECT * FROM vac_motivo_v";
$motivos = mysql_query($query_motivos, $vacantes) or die(mysql_error());
$row_motivos = mysql_fetch_assoc($motivos);
$totalRows_motivos = mysql_num_rows($motivos);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_usuarios = "SELECT vac_matriz.matriz, vac_usuarios.IDusuario, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno FROM vac_usuarios INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_usuarios.IDmatriz ORDER BY vac_usuarios.usuario_nombre ASC";
$usuarios = mysql_query($query_usuarios, $vacantes) or die(mysql_error());
$row_usuarios = mysql_fetch_assoc($usuarios);
$totalRows_usuarios = mysql_num_rows($usuarios);

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
<script type="text/javascript" src="includes/common/js/sigslot_core.js"></script>
<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js"></script>
<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js.php"></script>
<script type="text/javascript" src="includes/wdg/classes/JSRecordset.js"></script>
<script type="text/javascript" src="includes/wdg/classes/DependentDropdown.js"></script>

<?php
//begin JSRecordset
$jsObject_sucursal = new WDG_JsRecordset("sucursal");
echo $jsObject_sucursal->getOutput();
//end JSRecordset

//begin JSRecordset
$jsObject_puesto = new WDG_JsRecordset("puesto");
echo $jsObject_puesto->getOutput();
//end JSRecordset
?>
<script src="includes/skins/style.js" type="text/javascript"></script>
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
										<li class="active"><a href="admin_vacantesm.php">Vacantes</a></li>
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
				</div>				<!-- /page header -->
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
                        <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" class="form-horizontal form-validate-jquery">
                          <?php $cnt1 = 0; ?>
                          <?php do { ?>
                      <?php $cnt1++; ?>
                            <?php if (@$totalRows_rsvac_vacante > 1) { ?>
                              <h2><?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?></h2>
                              <?php } ?>
								<fieldset class="content-group">


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea_<?php echo $cnt1; ?>" id="IDarea_<?php echo $cnt1; ?>" class="form-control" >
												<option value="">Seleccione una opción</option> 
              <?php  do { ?>
              <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $row_rsvac_vacante['IDarea']))) 
			  {echo "SELECTED";} ?>><?php echo $row_area['area']?></option>
              <?php
             } while ($row_area = mysql_fetch_assoc($area));
               $rows = mysql_num_rows($area);
               if($rows > 0) {
               mysql_data_seek($area, 0);
	           $row_area = mysql_fetch_assoc($area);
             } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto_<?php echo $cnt1; ?>" class="form-control" id="IDpuesto_<?php echo $cnt1; ?>" wdg:subtype="DependentDropdown" required="required" wdg:type="widget" wdg:recordset="puesto" wdg:displayfield="denominacion" wdg:valuefield="IDpuesto" wdg:fkey="IDarea" wdg:triggerobject="IDarea_<?php echo $cnt1; ?>" wdg:selected="<?php echo $row_rsvac_vacante['IDpuesto'] ?>">
											  <option value="">Seleccione una opción</option>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha Baja:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_baja_<?php echo $cnt1; ?>" id="fecha_baja_<?php echo $cnt1; ?>" value="<?php if ($row_rsvac_vacante['fecha_baja'] == "") { echo "";} else  { echo KT_formatDate($row_rsvac_vacante['fecha_baja']); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha Requi:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_requi_<?php echo $cnt1; ?>" id="fecha_requi_<?php echo $cnt1; ?>" value="<?php if ($row_rsvac_vacante['fecha_requi'] == "") { echo "";} else  { echo KT_formatDate($row_rsvac_vacante['fecha_requi']); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
							<!-- Fecha -->
                                    
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha Ocupación:</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_ocupacion_<?php echo $cnt1; ?>" id="fecha_ocupacion_<?php echo $cnt1; ?>" value="<?php if ($row_rsvac_vacante['fecha_ocupacion'] == "") { echo "";} else  { echo KT_formatDate($row_rsvac_vacante['fecha_ocupacion']); }?>">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->
									 
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Motivo de la Vacante:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmotivo_v_<?php echo $cnt1; ?>" id="IDmotivo_v_<?php echo $cnt1; ?>" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
              <?php  do { ?>
              <option value="<?php echo $row_motivos_v['IDmotivo_v']?>"<?php if (!(strcmp($row_motivos_v['IDmotivo_v'], $row_rsvac_vacante['IDmotivo_v']))) 
			  {echo "SELECTED";} ?>><?php echo $row_motivos_v['motivo_v']?></option>
              <?php
             } while ($row_motivos_v = mysql_fetch_assoc($motivos_v));
               $rows = mysql_num_rows($motivos_v);
               if($rows > 0) {
               mysql_data_seek($motivos_v, 0);
	           $row_motivos_v = mysql_fetch_assoc($motivos_v);
             } ?>
											</select>
										</div>
									</div>

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Ajuste de días (por motivos externos):</label>
										<div class="col-lg-9">
						<input type="number" name="ajuste_dias_<?php echo $cnt1; ?>" id="ajuste_dias_<?php echo $cnt1; ?>" class="form-control" placeholder="Indica los días de ajuste." value="<?php echo KT_escapeAttribute($row_rsvac_vacante['ajuste_dias']); ?>">
										</div>
									</div> 
									<!-- /basic text input -->
                                    

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="number" name="sueldo_<?php echo $cnt1; ?>" id="sueldo_<?php echo $cnt1; ?>" class="form-control" placeholder="Ingresa el sueldo autorizado."  required="required" value="<?php echo KT_escapeAttribute($row_rsvac_vacante['sueldo']); ?>">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz_<?php echo $cnt1; ?>" id="IDmatriz_<?php echo $cnt1; ?>" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
              <?php do {  ?>
              <option value="<?php echo $row_matriz['IDmatriz']?>"<?php if (!(strcmp($row_matriz['IDmatriz'], $row_rsvac_vacante['IDmatriz']))) {echo "SELECTED";} ?>><?php echo $row_matriz['matriz']?></option>
              <?php
             } while ($row_matriz = mysql_fetch_assoc($matriz));
             $rows = mysql_num_rows($matriz);
             if($rows > 0) {
             mysql_data_seek($matriz, 0);
	         $row_matriz = mysql_fetch_assoc($matriz);
             } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->



									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sucursal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsucursal_<?php echo $cnt1; ?>" class="form-control" id="IDsucursal_<?php echo $cnt1; ?>" wdg:subtype="DependentDropdown" required="required" wdg:type="widget" wdg:recordset="sucursal" wdg:displayfield="sucursal" wdg:valuefield="IDsucursal" wdg:fkey="IDmatriz" wdg:triggerobject="IDmatriz_<?php echo $cnt1; ?>" wdg:selected="<?php echo $row_rsvac_vacante['IDsucursal'] ?>">
											  <option value="">Seleccione una opción</option>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->



									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus general:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDestatus_<?php echo $cnt1; ?>" id="IDestatus_<?php echo $cnt1; ?>" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
              <?php  do { ?>
              <option value="<?php echo $row_estatus['IDestatus']?>"<?php if (!(strcmp($row_estatus['IDestatus'], $row_rsvac_vacante['IDestatus']))) 
			  {echo "SELECTED";} ?>><?php echo $row_estatus['estatus']?></option>
              <?php
             } while ($row_estatus = mysql_fetch_assoc($estatus));
               $rows = mysql_num_rows($estatus);
               if($rows > 0) {
               mysql_data_seek($estatus, 0);
	           $row_estatus = mysql_fetch_assoc($estatus);
             } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

																		<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus específico:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDapoyo_<?php echo $cnt1; ?>" id="IDapoyo_<?php echo $cnt1; ?>" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
              <?php do {  ?>
              <option value="<?php echo $row_apoyo['IDapoyo']?>"<?php if (!(strcmp($row_apoyo['IDapoyo'], $row_rsvac_vacante['IDapoyo']))) {echo "SELECTED";} ?>><?php echo $row_apoyo['apoyo']?></option>
              <?php
             } while ($row_apoyo = mysql_fetch_assoc($apoyo));
             $rows = mysql_num_rows($apoyo);
             if($rows > 0) {
             mysql_data_seek($apoyo, 0);
	         $row_apoyo = mysql_fetch_assoc($apoyo);
             } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Turno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDturno_<?php echo $cnt1; ?>" id="IDturno_<?php echo $cnt1; ?>" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
              <?php  do { ?>
              <option value="<?php echo $row_turno['IDturno']?>"<?php if (!(strcmp($row_turno['IDturno'], $row_rsvac_vacante['IDturno']))) 
			  {echo "SELECTED";} ?>><?php echo $row_turno['turno']?></option>
              <?php
             } while ($row_turno = mysql_fetch_assoc($turno));
               $rows = mysql_num_rows($turno);
               if($rows > 0) {
               mysql_data_seek($turno, 0);
	           $row_turno = mysql_fetch_assoc($turno);
             } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fuente de Reclutamiento:</label>
										<div class="col-lg-9">
											<select name="IDfuente_<?php echo $cnt1; ?>" id="IDfuente_<?php echo $cnt1; ?>" class="form-control" >
												<option value="">Seleccione una opción</option> 
              <?php  do { ?>
              <option value="<?php echo $row_fuente['IDfuente']?>"<?php if (!(strcmp($row_fuente['IDfuente'], $row_rsvac_vacante['IDfuente']))) 
			  {echo "SELECTED";} ?>><?php echo $row_fuente['fuente']?></option>
              <?php
             } while ($row_fuente = mysql_fetch_assoc($fuente));
               $rows = mysql_num_rows($fuente);
               if($rows > 0) {
               mysql_data_seek($fuente, 0);
	           $row_fuente = mysql_fetch_assoc($fuente);
             } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

	
                            <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Observaciones:</label>
										<div class="col-lg-9">
                                          <textarea name="observaciones_<?php echo $cnt1; ?>" rows="3" class="form-control" id="observaciones_<?php echo $cnt1; ?>" placeholder="Observaciones y si aplica, explicación de ajuste de dias."><?php echo KT_escapeAttribute($row_rsvac_vacante['observaciones']); ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    
                                    
                                    									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario asignado:</label>
										<div class="col-lg-9">
											<select name="IDusuario_<?php echo $cnt1; ?>" id="IDusuario_<?php echo $cnt1; ?>" class="form-control" >
												<option value="">Seleccione una opción</option> 
						  <?php  do { ?>
                          <option value="<?php echo $row_usuarios['IDusuario']?>"<?php if (!(strcmp($row_usuarios['IDusuario'], $row_rsvac_vacante['IDusuario']))) 
                          {echo "SELECTED";} ?>><?php echo $row_usuarios['usuario_nombre']  . " " . $row_usuarios['usuario_parterno'] . " " . $row_usuarios['usuario_materno'] . " (" . $row_usuarios['matriz'] . ")";?></option>
                          <?php
                         } while ($row_usuarios = mysql_fetch_assoc($usuarios));
                           $rows = mysql_num_rows($usuarios);
                           if($rows > 0) {
                           mysql_data_seek($usuarios, 0);
                           $row_usuarios = mysql_fetch_assoc($usuarios);
                         } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                    
                                    
                                    									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario asignado 2:</label>
										<div class="col-lg-9">
											<select name="IDusuario2_<?php echo $cnt1; ?>" id="IDusuario2_<?php echo $cnt1; ?>" class="form-control" >
												<option value="">Seleccione una opción</option> 
						  <?php  do { ?>
                          <option value="<?php echo $row_usuarios['IDusuario']?>"<?php if (!(strcmp($row_usuarios['IDusuario'], $row_rsvac_vacante['IDusuario2']))) 
                          {echo "SELECTED";} ?>><?php echo $row_usuarios['usuario_nombre']  . " " . $row_usuarios['usuario_parterno'] . " " . $row_usuarios['usuario_materno'] . " (" . $row_usuarios['matriz'] . ")";?></option>
                          <?php
                         } while ($row_usuarios = mysql_fetch_assoc($usuarios));
                           $rows = mysql_num_rows($usuarios);
                           if($rows > 0) {
                           mysql_data_seek($usuarios, 0);
                           $row_usuarios = mysql_fetch_assoc($usuarios);
                         } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario asignado 3:</label>
										<div class="col-lg-9">
											<select name="IDusuario3_<?php echo $cnt1; ?>" id="IDusuario3_<?php echo $cnt1; ?>" class="form-control" >
												<option value="">Seleccione una opción</option> 
              <?php  do { ?>
              <option value="<?php echo $row_usuarios['IDusuario']?>"<?php if (!(strcmp($row_usuarios['IDusuario'], $row_rsvac_vacante['IDusuario3']))) 
			  {echo "SELECTED";} ?>><?php echo $row_usuarios['usuario_nombre']  . " " . $row_usuarios['usuario_parterno'] . " " . $row_usuarios['usuario_materno'] . " (" . $row_usuarios['matriz'] . ")";?></option>
              <?php
             } while ($row_usuarios = mysql_fetch_assoc($usuarios));
               $rows = mysql_num_rows($usuarios);
               if($rows > 0) {
               mysql_data_seek($usuarios, 0);
	           $row_usuarios = mysql_fetch_assoc($usuarios);
             } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->





									
 <input type="hidden" name="kt_pk_vac_vacante_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rsvac_vacante['kt_pk_vac_vacante']); ?>" />
 <input type="hidden" name="IDusuario_<?php echo $cnt1; ?>" id="IDusuario_<?php echo $cnt1; ?>" class="id_field" value="<?php echo $IDusuario ?>" />
 
                            
                            <?php } while ($row_rsvac_vacante = mysql_fetch_assoc($rsvac_vacante)); ?>
                          <div class="text-right">
                            <div>
                              <?php  if (@$_GET['IDvacante'] == "") { ?>
                                <input type="submit" name="KT_Insert1" class="btn btn-primary" id="KT_Insert1" value="<?php echo NXT_getResource("Insert_FB"); ?>" />
                              <?php } else { ?>
                         <button type="submit"  name="KT_Update1" class="btn btn-primary">Actualizar</button>
                         <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                         <button type="button" onClick="window.location.href='admin_vacantesm.php'" class="btn btn-default btn-icon">Cancelar</button>
                                <?php }  ?>
                            </div>
                          </div>
                       </fieldset>
                       </form>
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
                                    <a class="btn btn-danger" href="admin_vacantem_edit.php?IDvacante_borrar=<?php echo $la_vacante; ?>">Si borrar</a>
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
	<?php
	echo $tNGs->getErrorMsg();
?>
</body>
</html>
<?php
mysql_free_result($variables);

mysql_free_result($motivos);
?>
