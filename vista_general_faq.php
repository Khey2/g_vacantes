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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


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
	<script src="assets/js/app.js"></script>
	<!-- /theme JS files -->

</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="vista_panel.php"><img src="global_assets/images/logo_light.png" alt=""></a>

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
							<li><a href="vista_mi_perfil.php"><i class="icon-user-plus"></i>Mi Perfil</a></li>							
							<li><a href="vista_general_faq.php"><i class="icon-help"></i>Ayuda</a></li>
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
										<i class="icon-pin text-size-small"></i>Consulta
									</div>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
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
								<li class="active"><a href="vista_panel.php"><i class="icon-home4"></i> <span>Inicio</span></a></li>
								<li>
									<a href="#"><i class="icon-stack2"></i> <span>Consulta Vacantes</span></a>
									<ul>
										<li><a href="vista_activas.php">Activas</a></li>
										<li><a href="vista_totales.php">Totales</a></li>
									</ul>
								</li>
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
							<li><a href="vista_panel.php"><i class="icon-home2 position-left"></i> Inicio</a></li>
							<li class="active"><a href="vista_general_faq.php">FAQs</a></li>
						</ul>

					</div>
				    </div>				
             <!-- /page header -->
            <p>&nbsp;</p>


<!-- Content area -->
				<div class="content">



					<!-- Questions area -->
					<h4 class="text-center content-group">
						Preguntas Frecuentes
						<small class="display-block">Si no encuentras la respuesta a tu pregunta, contactanos por correo.</small>
					</h4>

					<div class="row">
					  <div class="col-lg-9">

							<!-- Questions list -->
						<div class="panel-group panel-group-control panel-group-control-right">
								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question1">
												<i class="icon-help position-left text-slate"></i>¿Cada cuándo se tiene que reportar una vacante?
										  </a>
										</h6>
									</div>

									<div id="question1" class="panel-collapse collapse">
										<div class="panel-body">
											Lo recomendable es que reportes cada vacante el mismo día en la que se te notifica de la misma. Si no te es posible, deberás actualizar al menos cada 3 días, para mantener el sistema actualizado.
					          </div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span>

										  </div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question2">
												<i class="icon-help position-left text-slate"></i>¿Cuantos días tengo para cubrir una vacante?
										  </a>
										</h6>
									</div>

									<div id="question2" class="panel-collapse collapse">
										<div class="panel-body">
											Los días de cobertura de vacantes dependen del tipo de vacante, para los puestos de Almacén son 7 días, para los puestos de Distribución 10, para puestos de Ventas 15 y para administrativos 20 días.
					          </div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span>

										  </div>
										</div>
									</div>
								</div>

								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question3">
												<i class="icon-help position-left text-slate"></i>¿Los días reportados en el sistema son naturales o laborales?
										  </a>
										</h6>
									</div>

									<div id="question3" class="panel-collapse collapse">
										<div class="panel-body">
											El sistema considera solo días laborales; además no cuenta días festivos.
					          </div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span>

										  </div>
										</div>
								  </div>
								</div>

<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a class="collapsed" data-toggle="collapse" href="#question4">
												<i class="icon-help position-left text-slate"></i>¿Qué significan los días de ajuste?
										  </a>
										</h6>
									</div>

									<div id="question4" class="panel-collapse collapse">
										<div class="panel-body">
											Son los días en los que se retrasó la cobertura por cuestiones agenas a Recursos Humanos, como por ejemplo los días que tarda el jefe inmediato en confirmar la entrevista funcional. Cuando indiques días de ajuste, debes explicar la razón de los mismos. Los días de ajuste se descuentan del conteo de días de cobertura.
					          </div>

										<div class="panel-footer panel-footer-transparent">
											<div class="heading-elements">
												<span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span>

										  </div>
										</div>
									</div>
								</div>
						  <div class="panel panel-white">
								  <div class="panel-heading">
								    <h6 class="panel-title"> <a class="collapsed" data-toggle="collapse" href="#question5">
                                     <i class="icon-help position-left text-slate"></i>¿Cuando puedo solicitar apoyo a Corporativo? 
                                     </a> 
                                     </h6>
							      </div>
                                  
								  <div id="question5" class="panel-collapse collapse">
								    <div class="panel-body"> El sistema automáticamente envía una alerta a Reclutamiento Corporativo cuanto han pasado más de 10 días de retraso en la cobertura de una vacante. No obstante, puedes solicitar apoyo en el momento que consideres que es necesario.
                                     </div>
								    <div class="panel-footer panel-footer-transparent">
								      <div class="heading-elements"> <span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span> </div>
							        </div>
							      </div>
						  </div>
						  <div class="panel panel-white">
								  <div class="panel-heading">
								    <h6 class="panel-title"> <a class="collapsed" data-toggle="collapse" href="#question6">
                                     <i class="icon-help position-left text-slate"></i>¿Cuantas veces puedo actualizar una vacante?
                                      </a> 
                                      </h6>
							      </div>
                                  
								  <div id="question6" class="panel-collapse collapse">
								    <div class="panel-body">
                                     El sistema guarda registro de cada vez que haces alguna modificación a una vacante. Lo recomendable es que solo modifiques la vacante cuando sea cubierta. Algunos campos, como la fecha de requisición no se pueden modificar.
                                      </div>
								    <div class="panel-footer panel-footer-transparent">
								      <div class="heading-elements"> <span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span> </div>
							        </div>
							      </div>
						  </div>
						  <div class="panel panel-white">
								  <div class="panel-heading">
								    <h6 class="panel-title"> <a class="collapsed" data-toggle="collapse" href="#question7"> 
                                    <i class="icon-help position-left text-slate"></i>¿Cómo puedo cambiar el mes o la sucursal actual? 
                                    </a>
                                     </h6>
							      </div>
                                  
								  <div id="question7" class="panel-collapse collapse">
								    <div class="panel-body">
                                     Cada usuario tiene asignada las sucursales de las que es el encargado, si requieres que te asignen una adicional, solicitalo al correo jacardenas@sahuayo.mx. Puedes cambiar la sucursal y el mes de consulta en la sección de Sucursales en el menú superior del lado derecho. 
                                     </div>
								    <div class="panel-footer panel-footer-transparent">
								      <div class="heading-elements"> <span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span> </div>
							        </div>
							      </div>
						  </div>
								<div class="panel panel-white">
								  <div class="panel-heading">
								    <h6 class="panel-title"> <a class="collapsed" data-toggle="collapse" href="#question8"> 
                                    <i class="icon-help position-left text-slate"></i>¿Con quien puedo solictar apoyo para trámites relacionados con Reclutamiento y Selección? 
                                    </a>
                                     </h6>
							      </div>
                                  
								  <div id="question8" class="panel-collapse collapse">
								    <div class="panel-body"> 
                                   Con Claudia Gaona, Gerente Regional de Recursos Humanos o Alicia López, Jefa de Reclutamiento Corporativo puedes solicitar asesoría en temas como Estudios Socioeconómicos, Evaluaciones Psicométricas, Porveedores, Etc.
                                    </div>
								    <div class="panel-footer panel-footer-transparent">
								      <div class="heading-elements"> <span class="text-muted heading-text">Última actualización: 4 de diciembre de 2019.</span> </div>
							        </div>
							      </div>
						  </div>
                        </div>
						  <!-- /questions list -->

						</div>

						<div class="col-lg-3">

							<!-- Navigation -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Accesos</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="list-group no-border mb-5">
									<a href="vista_cambio_pass.php" class="list-group-item"><i class="icon-lock2"></i>Cambiar Password</a>
									<a href="vista_mi_perfil.php" class="list-group-item"><i class="icon-user-check"></i>  Mi Perfil</a>
									<div class="list-group-divider"></div>
									<a href="mailto:<?php echo $row_variables['contacto_interno']; ?>" class="list-group-item"><i class="icon-envelop2"></i> Enviar correo al Admin</a>
								</div>
							</div>
							<!-- /navigation -->

						</div>
						</div>
					<!-- /questions area -->

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