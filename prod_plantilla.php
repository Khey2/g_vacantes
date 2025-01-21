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

$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . " -2 day")); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $_GET['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT prod_plantilla.IDpuesto, vac_puestos.denominacion, vac_areas.area, prod_plantilla.autorizados FROM prod_plantilla INNER JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto INNER JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE prod_plantilla.IDmatriz = '$la_matriz' ";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);


//total gastado
mysql_select_db($database_vacantes, $vacantes);
$query_plantilla = "SELECT prod_plantilla.IDplantilla, prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.autorizados, prod_plantilla.sueldo_diario FROM prod_plantilla WHERE prod_plantilla.IDmatriz = '$la_matriz'";
$plantilla = mysql_query($query_plantilla, $vacantes) or die(mysql_error());
$row_plantilla = mysql_fetch_assoc($plantilla);
$totalRows_plantilla = mysql_num_rows($plantilla);

//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT Count(prod_activos.IDempleado) as TActivos FROM prod_activos WHERE prod_activos.IDmatriz = '$IDmatriz'";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);



$_diario = 0;
$_semanal = 0;
$_mensual = 0;
$_puestos = 0;
do { 

$a = $row_plantilla['autorizados'] * $row_plantilla['sueldo_diario'];
$_diario = $_diario + $a; 
$_puestos = $_puestos + $row_plantilla['autorizados'];
} while ($row_plantilla = mysql_fetch_assoc($plantilla));
$_semanal = $_diario * 7;
$_mensual = $_diario * 30;

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
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
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
</head>

<body>

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

							<?php if( $row_usuario['productividad'] > 0) {?>
								<li class="active">
                                    <a href="#"><i class="icon-vcard"></i> <span>Productividad <span class="label bg-blue-400">New</span></span></a>
									<ul>
							<?php 		if( $row_usuario['productividad'] > 0) {?>
										<li><a href="productividad_captura.php">Capturar</a></li>
							<?php } if( $row_usuario['productividad'] > 1) {?>
										<li><a href="productividad_valida.php">Validar</a></li>
							<?php } if( $row_usuario['productividad'] > 2 ) {?>
										<li><a href="productividad_autoriza_sucursal.php">Autorizar</a></li>
							<?php  } ?>
										<li><a href="productividad_reporte.php">Reporte</a></li>

									</ul>
								</li>
							<?php  } ?>
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
										<li><a href="productividad_importar.php">Importar Prod.</a></li>
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
							<li><a href="productividad_captura.php">Productividad</a></li>
							<li class="active"><a href="#">Plantilla</a></li>
						</ul>

					</div>
				    </div>				
             <!-- /page header -->
            <p>&nbsp;</p>


<!-- Content area -->
				<div class="content">

					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Plantilla Autorizada</h6>
								</div>

								<div class="panel-body">
								<p>A continuación se muestra la plantilla autorizada de la Sucursal.</br>


				<!-- Statistics with progress bar -->
					<div class="row">

						<div class="col-sm-6 col-md-6">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-users2  icon-2x text-primary-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Empleados</h6>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
										<span class="text-muted"><strong>Sucursal:  </strong><?php echo $row_lmatriz['matriz']; ?></span>&nbsp; &nbsp; &nbsp; | &nbsp; &nbsp; &nbsp;
										<span class="text-muted"><strong>Activos:  </strong><?php echo $row_activos['TActivos']; ?></span>
							</div>
						</div>

						<div class="col-sm-6 col-md-6">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Costo Plantilla Autorizada</h6>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cash3 icon-2x text-primary-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-primary-400" style="width: 100%">
									</div>
								</div>
				              	<span class="text-muted"><strong>Mensual: </strong><?php echo "$" . number_format($_mensual);?></span>&nbsp; &nbsp; &nbsp; | &nbsp; &nbsp; &nbsp;
								<span class="text-muted"><strong>Semanal: </strong> <?php echo "$" . number_format($_semanal);?></span>&nbsp; &nbsp; &nbsp; | &nbsp; &nbsp; &nbsp;
								<span class="text-muted"><a href="prod_plantilla.php?IDmatriz=<?php echo $la_matriz;?>"><strong>Puestos: </strong><?php echo $_puestos;?></a></span>
							</div>
						</div>



					</div>

					<!-- /statistics with progress bar -->


								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>IDPuesto</th>
                                    <th>Denominación</th>
                                    <th>Área</th>
                                    <th>Autorizados</th>
                                  </tr>
                                  </thead>
                                <tfoot>
                                <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><strong>Total: </strong><?php echo $_puestos; ?></th>
                                </tr>
                                </tfoot>                                  
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_autorizados['IDpuesto']; ?>&nbsp;</td>
                                      <td><?php echo $row_autorizados['denominacion']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['area']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['autorizados']; ?>&nbsp; </td>
                                    </tr>
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados)); ?>
                                  </tbody>
                                </table>
								</div>
							</div>
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