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


if(isset($_SESSION['el_mes'])){ $el_mes = $_SESSION['el_mes']; } else { $el_mes = date("m");} 
$_SESSION['el_mes'] = $el_mes;

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);



//mensual
// a tiempo
mysql_select_db($database_vacantes, $vacantes);
$query_atiempo = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_areas.dias, vac_vacante.IDestatus,  vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea WHERE month(fecha_requi) = '$el_mes' AND vac_vacante.IDusuario = '$el_usuario' AND vac_vacante.IDestatus IN (3,4)";
$atiempo = mysql_query($query_atiempo, $vacantes) or die(mysql_error());
$row_atiempo = mysql_fetch_assoc($atiempo);
$totalRows_atiempo = mysql_num_rows($atiempo);

// activas
mysql_select_db($database_vacantes, $vacantes);
$query_vacantes_activas = "SELECT * FROM vac_vacante WHERE month(fecha_requi) = '$el_mes' AND vac_vacante.IDmatriz = '$la_matriz'";
$vacantes_activas = mysql_query($query_vacantes_activas, $vacantes) or die(mysql_error());
$row_vacantes_activas = mysql_fetch_assoc($vacantes_activas);
$totalRows_vacantes_activas = mysql_num_rows($vacantes_activas);

// cubiertas
mysql_select_db($database_vacantes, $vacantes);
$query_vacantes_cubiertas = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_areas.dias, vac_vacante.IDestatus,  vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea WHERE month(fecha_ocupacion) = '$el_mes' AND vac_vacante.IDmatriz = '$la_matriz' AND fecha_ocupacion IS NOT NULL";
$vacantes_cubiertas = mysql_query($query_vacantes_cubiertas, $vacantes) or die(mysql_error());
$row_vacantes_cubiertas = mysql_fetch_assoc($vacantes_cubiertas);
$totalRows_vacantes_cubiertas = mysql_num_rows($vacantes_cubiertas);

//estatus
mysql_select_db($database_vacantes, $vacantes);
$query_vacantes = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_areas.dias, vac_vacante.IDestatus,  vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea WHERE month(fecha_requi) = '$el_mes' AND vac_vacante.IDmatriz = '$la_matriz'";
$vacantes = mysql_query($query_vacantes, $vacantes) or die(mysql_error());
$row_vacantes = mysql_fetch_assoc($vacantes);
$totalRows_vacantes = mysql_num_rows($vacantes);

//fechas
require_once('assets/dias.php');

$antes_tiempo = 0;
$a_tiempo = 0;
$fuera_tiempo = 0;
$muy_fuera_tiempo = 0;

do { 

  $startdate = date('Y/m/d', strtotime($row_vacantes['fecha_requi']));

  if ($row_vacantes['fecha_ocupacion'] > 0) { $end_date =  date('Y/m/d', strtotime($row_vacantes['fecha_ocupacion']));
  } else { $end_date = date('Y/m/d'); }

  $previo = getWorkingDays($startdate, $end_date, $holidays);
                             
  // aplicamos ajuste de dias;
  $ajuste_dias = $row_vacantes['ajuste_dias'];
  if ($ajuste_dias != 0) { $previo = $previo - $ajuste_dias; } 
  
  // resultado grafica
            if ($previo < 4) {  
	 $antes_tiempo = $antes_tiempo + 1;
	} else if ($previo <  $row_vacantes['dias']) {   
	 $a_tiempo = $a_tiempo + 1;
	} else if ($previo < $row_vacantes['dias'] + 4) {  
	 $fuera_tiempo = $fuera_tiempo + 1;
	} else if ($previo >= $row_vacantes['dias'] + 4) {
	 $muy_fuera_tiempo = $muy_fuera_tiempo + 1; 
	}
	
	
} while ($row_vacantes = mysql_fetch_assoc($vacantes)); 

//a tiempo
$total_a_tiempo = 0;
$previo2 = 0;

do { 
  $startdate2 = date('Y/m/d', strtotime($row_atiempo['fecha_requi']));
  $end_date2 =  date('Y/m/d', strtotime($row_atiempo['fecha_ocupacion']));
  $previo2 = getWorkingDays($startdate2, $end_date2, $holidays);
  $ajuste_dias2 = $row_atiempo['ajuste_dias'];
  if ($ajuste_dias2 != 0) { $previo2 = $previo2 - $ajuste_dias2; } 
    if ($previo2 < $row_atiempo['dias']) {  
	 $total_a_tiempo = $total_a_tiempo + 1;
	}
} while ($row_atiempo = mysql_fetch_assoc($atiempo)); 

//fuera de tiempo
$total_no_tiempo = 0;
$previo3 = 0;

do { 
  $startdate3 = date('Y/m/d', strtotime($row_vacantes_cubiertas['fecha_requi']));
  $end_date3 =  date('Y/m/d', strtotime($row_vacantes_cubiertas['fecha_ocupacion'])); 
  $previo3 = getWorkingDays($startdate3, $end_date3, $holidays);
  $ajuste_dias3 = $row_vacantes_cubiertas['ajuste_dias'];
  if ($ajuste_dias3 != 0) { $previo3 = $previo3 - $ajuste_dias3; } 
    if ($previo3 > $row_vacantes_cubiertas['dias']) {  
	 $total_no_tiempo = $total_no_tiempo + 1;
	}
} while ($row_vacantes_cubiertas = mysql_fetch_assoc($vacantes_cubiertas)); 

// el mes
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

	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/sucursal.js"></script>
	<script src="global_assets/js/sucursal2.js"></script>
	<script src="global_assets/js/area.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	
    <!-- /theme JS files -->
    <script type="text/javascript">
    var antes_tiempo = <?php echo $antes_tiempo; ?>;
    var a_tiempo = <?php echo $a_tiempo; ?>;
    var fuera_tiempo = <?php echo $fuera_tiempo; ?>;
    var muy_fuera_tiempo = <?php echo $muy_fuera_tiempo; ?>;
	</script>
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
										<i class="icon-pin text-size-small"></i> <?php echo $row_matriz['matriz']; ?>
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
								<li class="active"><a href="panel.php"><i class="icon-home4"></i> <span>Inicio</span></a></li>
								<li>
									<a href="#"><i class="icon-stack2"></i> <span>Vacantes</span></a>
									<ul>
										<li><a href="vacantes_activas.php">Activas</a></li>
										<li><a href="vacantes_cerradas.php">Cerradas</a></li>
										<li><a href="vacantes_totales.php">Todas</a></li>
                                        <li><a href="vacante_edit.php">Agregar</a></li>
									</ul>
								</li>
								<?php if( $row_usuario['nivel_acceso'] == 3) {?>
								<li>
									<a href="#"><i class="icon-wrench3"></i> <span>Administración</span></a>
									<ul>
										<li><a href="admin_usuarios.php">Usuarios</a></li>
										<li><a href="admin_vacantes.php">Vacantes</a></li>
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
							<li class="active"><a href="panel.php"><i class="icon-home2 position-left"></i> Inicio</a></li>
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
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido(a) <?php echo $row_usuario['usuario_nombre']; ?> al <?php echo $row_variables['nombre_sistema']; ?>.</p>
							<p>El objetivo del Sistema es lograr un adecuado control y seguimiento del proceso de reclutamiento y selección, asegurando que la contratación del personal cubra con el perfil requerido y 
                            en el tiempo establecido en el Manual de Reclutamiento y Selección.</p>
                            <p>Asimismo, el sistema permite determinar acciones de acuerdo al tiempo del cubrimiento de vacantes, tales como:</p>
												<ul>
                                                <li>Apoyo corporativo en las Sucursales.</li>
												<li>Programa de retención asistida.</li>
												<li>Apoyo de la Gerencia de Sucursal.</li>
                                                </ul> 
							<p>No olvides que los Jefes de RH son los responsables de llevar un control adecuado de las vacantes vigentes.</p>
                            <p><strong>Las vacantes con más de 20 días de retraso, serán canalizadas al Departamento de Reclutamiento y Selección Corporativo para su seguimiento hasta la cobertura.</strong></p>
                   </div>
                   </div>
                   
                   
                   					<!-- Statistics with progress bar -->
					<h6 class="content-group text-semibold">
						Reporte de Cobertura
						<small class="display-block">Global</small>
					</h6>

					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Antes de Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cog3 icon-2x text-indigo-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-indigo-400" style="width: 67%">
										<span class="sr-only">67% Complete</span>
									</div>
								</div>
				                <span class="pull-right">67%</span>
				                9 Vacantes
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">A Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>

									<div class="media-right media-middle">
										<i class="icon-pulse2 icon-2x text-danger-400 opacity-75"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-danger-400" style="width: 80%">
										<span class="sr-only">80% Complete</span>
									</div>
								</div>
				                <span class="pull-right">80%</span>
				                 29 Vacantes
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-cog3 icon-2x text-blue-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Fuera de Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-blue" style="width: 67%">
										<span class="sr-only">67% Complete</span>
									</div>
								</div>
				                <span class="pull-right">67%</span>
				                 47 Vacantes
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-pulse2 icon-2x text-success-400 opacity-75"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Muy Fuera de Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar bg-success-400" style="width: 80%">
										<span class="sr-only">80% Complete</span>
									</div>
								</div>
				                <span class="pull-right">80%</span>
				                 79 Vacantes
							</div>
						</div>
					</div>

					<h6 class="content-group text-semibold">
						Reporte de Cobertura por Mes
						<small class="display-block">Noviembre</small>
					</h6>

					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-blue-400 has-bg-image">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">Antes de Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>

									<div class="media-right media-middle">
										<i class="icon-cog3 icon-2x"></i>
									</div>
								</div>

								<div class="progress progress-micro bg-blue mb-10">
									<div class="progress-bar bg-white" style="width: 67%">
										<span class="sr-only">67% Complete</span>
									</div>
								</div>
				                <span class="pull-right">67%</span>
				                9 Vacantes
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-danger-400 has-bg-image">
								<div class="media no-margin-top content-group">
									<div class="media-body">
										<h6 class="no-margin text-semibold">A Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>

									<div class="media-right media-middle">
										<i class="icon-pulse2 icon-2x"></i>
									</div>
								</div>

								<div class="progress progress-micro mb-10 bg-danger">
									<div class="progress-bar bg-white" style="width: 80%">
										<span class="sr-only">80% Complete</span>
									</div>
								</div>
				                <span class="pull-right">80%</span>
				                 5 Vacantes
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-success-400 has-bg-image">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-cog3 icon-2x"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Fuera de Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>
								</div>

								<div class="progress progress-micro mb-10 bg-success">
									<div class="progress-bar bg-white" style="width: 67%">
										<span class="sr-only">67% Complete</span>
									</div>
								</div>
				                <span class="pull-right">67%</span>
				                2 Vacantes
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-indigo-400 has-bg-image">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
										<i class="icon-pulse2 icon-2x"></i>
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Muy Fuera de Tiempo</h6>
										<span class="text-muted">Vacantes</span>
									</div>
								</div>

								<div class="progress progress-micro mb-10 bg-indigo">
									<div class="progress-bar bg-white" style="width: 80%">
										<span class="sr-only">80% Complete</span>
									</div>
								</div>
				                <span class="pull-right">80%</span>
				                14 Vacantes
							</div>
						</div>
					</div>
					<!-- /statistics with progress bar -->


					<div class="row">
						<div class="col-md-6">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Reporte de Cobertura Mensual</h5>
								</div>
								
									<div class="panel-body">
									<p class="content-group">A continuación se muestra el reporte del estatus de las vacantes del mes de <strong><?php echo $elmes; ?></strong>.</br>
                                    Las gráfica incluye vacantes no cubiertas.</p>

									<div class="chart-container text-center">
										<div class="display-inline-block" id="google-sucursal"></div>
									</div>
                                
									<!-- Basic select -->
									<div class="form-group">
											<form  method="POST" action="elmes.php?r=p">
                                    <div class="col-lg-6">
                                             <select name="el_mes" class="form-control">
                                               <option value=""    <?php if (!(strcmp("", $el_mes))) {echo "selected=\"selected\"";} ?>>Selecciona el mes</option>
                                               <?php do {  ?>
                                               <option value="<?php echo $row_mes['IDmes']?>"<?php if (!(strcmp($row_mes['IDmes'], $el_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_mes['mes']?></option>
                                               <?php
											 } while ($row_mes = mysql_fetch_assoc($mes));
											  $rows = mysql_num_rows($mes);
											  if($rows > 0) {
												  mysql_data_seek($mes, 0);
												  $row_mes = mysql_fetch_assoc($mes);
											  } ?> </select>
										</div>
                                      <button type="submit" class="btn btn-primary">Ver <i class="icon-arrow-right14 position-right"></i></button>										
									</form>
                                    </div>
                                    <!-- /basic select -->

                                </div>							</div>

							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder6 position-left"></i>
										Archivos disponibles para descarga
									</h6>
								</div>
								
								<div class="list-group no-border">
									<a href="http://intranet.sahuayo.mx/rys/15.%20Procedimientos/MANUAL.pdf" target="_blank" class="list-group-item">
										<i class="icon-file-pdf"></i> Manual de Reclutamiento y Selección.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/4.%20Entrevista%20General/corporativo/Reporte%20de%20Entrevista.pptx" target="_blank"  class="list-group-item">
										<i class=" icon-file-presentation"></i> Guia de Entrevista por Competencias.ppt <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/6.%20Entrevista%20Competencias/Entrevista%20por%20Competencias.xlsx" target="_blank"  class="list-group-item">
										<i class="icon-file-excel"></i> Formato de Entrevista por Competencias.xlsx <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/6.%20Entrevista%20Competencias/Catalogo%20de%20Competencias.pdf" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Catálogo de Competencias Laborales.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/6.%20Entrevista%20Competencias/Diccionario%20de%20Competencias%20y%20Comportamientos%20Sahuayo.pdf" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Diccionario de Competencias Laborales.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/16.%20Requi/formato.xls" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Formato de Requisición de Personal.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/16.%20Requi/Reporte.xls"  target="_blank" class="list-group-item">
										<i class="icon-file-excel"></i> Versión previa del Reporte de Vacantes.xlsx
									</a>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Reporte de Cobertura Total</h5>
								</div>
								
								<div class="panel-body">
									<p class="content-group">A continuación se muestra el reporte del estatus de las vacantes del mes de <strong><?php echo $elmes; ?></strong>.</br>
                                    Las gráfica incluye vacantes no cubiertas.</p>

									<div class="chart-container text-center">
										<div class="display-inline-block" id="google-sucursal2"></div>
									</div>
                                </div>							
                               </div>

							<div class="panel panel-flat">
                            <!-- Widget with centered text and colored icon -->
								<div class="panel-body text-center">
									<div class="content-group mt-5">
									</div>
									<div class="icon-object border-success text-success"><i class="icon-book"></i></div>
									<h6 class="text-semibold"><a href="#" class="text-default">Preguntas Frecuentes</a></h6>
									<p class="mb-15">Todas las dudas acerca del uso del sistema, las puedes consultar en las <a href="general_faq.php">FAQs &rarr;</a></p>
								</div>
							<!-- /widget with centered text and colored icon -->							
                            </div>


							<div class="panel panel-flat">
                            <!-- Simple inline block with icon and button -->
								<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-lifebuoy icon-3x text-muted no-edge-top"></i>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold">¿Tienes dudas o sugerencias?</h6>
										<span class="text-muted">Contactanos por correo electrónico a <a href="mailto:jacardenas@sahuayo.mx">jacardenas@sahuayo.mx</a></br>
                                                                 O por teléfono a la extensión 1221.</br>
                                                                 Nos pondremos en contacto contigo a la brevedad.</span>
									</div>
								</div>
							<!-- /simple inline block with icon and button -->
							</div>
						</div>
					</div>
					<!-- /panel heading options -->
                    

					<div class="row">
						<div class="col-md-6">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Resultado mensual de cubrimiento primer semestre</h6>
								</div>
								
								<div class="panel-body">
							<!-- Application status -->
									<ul class="progress-list">
							            <li>
							                <label>Enero<span>Suficiente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-info" style="width: 65%">
													<span class="sr-only">65% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Febrero<span>Deficiente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-danger" style="width: 50%">
													<span class="sr-only">50% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Marzo<span>Sobresaliente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-success" style="width: 90%">
													<span class="sr-only">90% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Abril<span>Sobresaliente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-success" style="width: 90%">
													<span class="sr-only">90% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Mayo<span>Sobresaliente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-success" style="width: 95%">
													<span class="sr-only">95% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Junio<span>Satisfactorio</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-primary" style="width: 80%">
													<span class="sr-only">80% Complete</span>
												</div>
											</div>
							            </li>
							        </ul>
								</div>
							</div>
							<!-- /application status -->
						</div>

						<div class="col-md-6">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Resultado mensual de cubrimiento segundo semestre</h6>
								</div>
							<!-- Application status -->
								<div class="panel-body">
									<ul class="progress-list">
							            <li>
							                <label>Julio<span>Suficiente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-info" style="width: 70%">
													<span class="sr-only">50% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Agosto<span>Deficiente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-danger" style="width: 50%">
													<span class="sr-only">70% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Septiembre<span>Sobresaliente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-success" style="width: 90%">
													<span class="sr-only">80% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Octubre<span>Sobresaliente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-success" style="width: 90%">
													<span class="sr-only">80% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Noviembre<span>Sobresaliente</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-success" style="width: 90%">
													<span class="sr-only">80% Complete</span>
												</div>
											</div>
							            </li>

							            <li>
							                <label>Diciembre<span>Satisfactorio</span></label>
											<div class="progress progress-xxs">
												<div class="progress-bar progress-bar-primary" style="width: 80%">
													<span class="sr-only">60% Complete</span>
												</div>
											</div>
							            </li>
							        </ul>
								</div>
							</div>
							<!-- /application status -->
						</div>
					</div>
					<!-- /panel heading options -->

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