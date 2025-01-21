<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_POST['IDcaptura'];
	
  $updateSQL = sprintf("UPDATE prod_captura_a SET IDempleado=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, anio=%s, IDmatriz=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, a6=%s, a7=%s, a8=%s, a9=%s, a10=%s, a11=%s, a12=%s, a13=%s, a14=%s, a15=%s, a16=%s, a17=%s, a18=%s, a19=%s, a20=%s, a21=%s, a22=%s, a23=%s, a24=%s, a25=%s, a26=%s, a27=%s, a28=%s, garantizado=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s,lun_g=%s, mar_g=%s, mi_ge=%s, jue_g=%s, vie_g=%s, sab_g=%s, dom=%s WHERE IDcaptura=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['a1'], "int"),
                       GetSQLValueString($_POST['a2'], "int"),
                       GetSQLValueString($_POST['a3'], "int"),
                       GetSQLValueString($_POST['a4'], "int"),
                       GetSQLValueString($_POST['a5'], "int"),
                       GetSQLValueString($_POST['a6'], "int"),
                       GetSQLValueString($_POST['a7'], "int"),
                       GetSQLValueString($_POST['a8'], "int"),
                       GetSQLValueString($_POST['a9'], "int"),
                       GetSQLValueString($_POST['a10'], "int"),
                       GetSQLValueString($_POST['a11'], "int"),
                       GetSQLValueString($_POST['a12'], "int"),
                       GetSQLValueString($_POST['a13'], "int"),
                       GetSQLValueString($_POST['a14'], "int"),
                       GetSQLValueString($_POST['a15'], "int"),
                       GetSQLValueString($_POST['a16'], "int"),
                       GetSQLValueString($_POST['a17'], "int"),
                       GetSQLValueString($_POST['a18'], "int"),
                       GetSQLValueString($_POST['a19'], "int"),
                       GetSQLValueString($_POST['a20'], "int"),
                       GetSQLValueString($_POST['a21'], "int"),
                       GetSQLValueString($_POST['a22'], "int"),
                       GetSQLValueString($_POST['a23'], "int"),
                       GetSQLValueString($_POST['a24'], "int"),
                       GetSQLValueString($_POST['a25'], "int"),
                       GetSQLValueString($_POST['a26'], "int"),
                       GetSQLValueString($_POST['a27'], "int"),
                       GetSQLValueString($_POST['a28'], "int"),
                       GetSQLValueString($_POST['garantizado'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString(isset($_POST['lun']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['lun_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "productividad_valida_puesto_uptdate_a.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_activos.emp_paterno, prod_activos.IDempleado, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado, prod_activos.descripcion_nomina, prod_captura_a.IDcaptura, prod_captura_a.pago, prod_captura_a.pago2, prod_captura_a.garantizado,  prod_captura_a.lun, prod_captura_a.mar, prod_captura_a.mie, prod_captura_a.jue, prod_captura_a.vie, prod_captura_a.sab, prod_captura_a.lun_g, prod_captura_a.mar_g, prod_captura_a.mie_g, prod_captura_a.jue_g, prod_captura_a.vie_g, prod_captura_a.sab_g, prod_captura_a.dom, prod_captura_a.a1, prod_captura_a.observaciones, prod_captura_a.a2, prod_captura_a.a3, prod_captura_a.a4, prod_captura_a.a5, prod_captura_a.a6, prod_captura_a.a7, prod_captura_a.a8, prod_captura_a.a9, prod_captura_a.a10, prod_captura_a.a11, prod_captura_a.a12, prod_captura_a.a13, prod_captura_a.a14, prod_captura_a.a15, prod_captura_a.a16, prod_captura_a.a17, prod_captura_a.a18, prod_captura_a.a19, prod_captura_a.a20, prod_captura_a.a21, prod_captura_a.a22, prod_captura_a.a23, prod_captura_a.a24, prod_captura_a.a25, prod_captura_a.a26, prod_captura_a.a27, prod_captura_a.a28, prod_captura_a.semana,  prod_captura_a.reci,  prod_captura_a.carg,  prod_captura_a.dist,  prod_captura_a.esti, prod_captura_a.fecha_captura, vac_puestos.denominacion, vac_puestos.modal FROM prod_activos LEFT JOIN prod_captura_a ON prod_captura_a.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = '$la_matriz' AND vac_puestos.modal = 200";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);
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
	<!-- /theme JS files -->

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>

	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script><body>
	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<?php if( $row_usuario['nivel_acceso'] == 2) { ?>
            <a class="navbar-brand" href="vista_panel.php"><img src="global_assets/images/logo_light.png" alt=""></a>
			<?php } else { ?>
            <a class="navbar-brand" href="panel.php"><img src="global_assets/images/logo_light.png" alt=""></a>
			<?php } ?>

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
                         <?php if( $row_usuario['nivel_acceso'] != 2) { ?>
							<li><a href="mi_matriz.php"><i class="icon-cog5"></i>Sucursales</a></li>
                         <?php } ?>
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
                                    	<?php if( $row_usuario['nivel_acceso'] == 2) { ?>
										<i class="icon-pin text-size-small"></i> Vista <?php echo $row_matriz['matriz']; ?>
                                    	<?php } elseif ( $row_usuario['nivel_acceso'] > 2 ) { ?>
										<i class="icon-pin text-size-small"></i> Administración
                                    	<?php } else { ?>
										<i class="icon-pin text-size-small"></i> <?php echo $row_matriz['matriz']; ?>
                                    	<?php } ?>
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
								<li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
                                
							<?php if( $row_usuario['nivel_acceso'] == 2) { ?>
								<li><a href="vista_panel.php"><i class="icon-home4"></i> <span>Inicio</span></a></li>
							<?php } else { ?>
								<li><a href="panel.php"><i class="icon-home4"></i> <span>Inicio</span></a></li>
							<?php } ?>

							<?php if( $row_usuario['nivel_acceso'] != 2) { ?>
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
							<?php } ?>

							<?php if( $row_usuario['productividad'] == 1) {?>
								<li>
									<a href="#"><i class="icon-vcard"></i> <span>Productividad <span class="label bg-blue-400">New</span></span></a>
									<ul>
										<li><a href="productividad_captura.php">Capturar</a></li>
										<li>
											<a href="#">Validar</a>
											<ul>
												<li class="active"><a href="productividad_valida_a.php">Aux. Almacén</a></li>
												<li><a href="productividad_valida.php"> Otros Puestos</a></li>
											</ul>
										</li>
										<li><a href="productividad_autoriza.php">Autorizar</a></li>
										<li><a href="productividad_reporte.php">Reporte</a></li>
									</ul>
								</li>
							<?php } ?>
                                
							<?php if( $row_usuario['nivel_acceso'] == 2) {?>
								<li>
									<a href="#"><i class="icon-stack2"></i> <span>Consulta Vacantes</span></a>
									<ul>
										<li><a href="vista_activas.php">Activas</a></li>
										<li><a href="vista_totales.php">Totales</a></li>
									</ul>
								</li>
							<?php } ?>

								<li>
									<a href="descriptivos.php"><i class="icon-file-text2"></i> <span>Descriptivos</span></a>
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
										<li><a href="master_admin_usuarios.php">Usuarios</a></li>
										<li><a href="master_admin_vacantes.php">Vacantes</a></li>
										<li><a href="master_admin_areas.php">Areas</a></li>
										<li><a href="master_admin_estatus.php">Estatus</a></li>
										<li><a href="master_admin_fuentes.php">Fuentes</a></li>
										<li><a href="master_admin_causas.php">Causas Baja</a></li>
										<li><a href="master_admin_motivos.php">Motivos Baja</a></li>
										<li><a href="master_admin_meses.php">Meses</a></li>
										<li><a href="master_admin_matrices.php">Matrices</a></li>
										<li><a href="master_admin_tipos.php">Tipos Vacantes</a></li>
										<li><a href="master_admin_turnos.php">Turnos</a></li>
										<li><a href="master_admin_respaldos.php">Respaldos</a></li>
										<li><a href="master_admin_variables.php">Variables</a></li>
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
							<?php if( $row_usuario['nivel_acceso'] == 2) {?>
							<li class="active"><a href="vista_panel.php"><i class="icon-home2 position-left"></i> Inicio</a></li>
							<?php } else {?>
							<li class="active"><a href="panel.php"><i class="icon-home2 position-left"></i> Inicio</a></li>
							<?php } ?>
							<li><a href="#">Productividad</a></li>
							<li>Captura</li>
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
							<h5 class="panel-title">Valida Productividad</h5></br>
                               Sucursal: <?php echo $row_matriz['matriz']; ?> </p>
						</div>

					<div class="panel-body"> 
                    <p>Selecciona el empelado para validar su productividad.</p>
					<table class="table datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>No. Empleado</th>
                          <th>Empleado</th>
                          <th>Sueldo Semanal</th>
                          <th>Calculado (%)</th>
                          <th>Pago ($)</th>
                          <th>Garantizado</th>
                          <th>Reci</th>
                          <th>Carg</th>
                          <th>Esti</th>
                          <th>Dist</th>
                          <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>							  

                        <?php do { 
						$el_puesto = $row_puestos['IDpuesto'];
						?>
                          <tr>
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></td>
                            <td><?php echo "$" . $row_puestos['sueldo_diario'] * 7; ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo $row_puestos['pago']. "%";} ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo "$" . $row_puestos['pago2'];} ?></td>
                            <td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo $row_puestos['reci'];} ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo $row_puestos['carg'];} ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo $row_puestos['esti'];} ?></td>
                            <td><?php if ($row_puestos['IDcaptura'] == 0) 	{ echo "-"; } else { echo $row_puestos['dist'];} ?></td>
                          <td>
                           <?php if ($row_puestos['IDcaptura'] == "") { ?>
							Sin captura
						   <?php } else {  ?>  
                          <button type="button" data-target="#modal_form_inline<?php echo $row_puestos['IDcaptura']; ?>"  data-toggle="modal" class="btn btn-primary btn-icon">Ver detalle</button>
                           <?php } ?>
                           </td>  
                           </tr>
                            <?php // agregamos el modal especifico
                           		  $modal = "assets/modals/2000.php";
								  require($modal); ?>

                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
					    </tbody>
				    </table>
                       </div>

					<!-- /panel heading options -->
                  <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-primary" href="prod_empleado_edit.php">Agregar Empleado<i class="icon-arrow-right14 position-right"></i></a> 
                    </div>
					</div>
					<!-- /colored button -->

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
<?php
mysql_free_result($variables);

mysql_free_result($puestos);
?>
