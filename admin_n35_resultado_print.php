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



$resultado = $_GET['IDresultado'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuarios = "SELECT * FROM nom35_resultados WHERE IDresultado = '$resultado'";
mysql_query("SET NAMES 'utf8'");
$usuarios = mysql_query($query_usuarios, $vacantes) or die(mysql_error());
$row_usuarios = mysql_fetch_assoc($usuarios);
$totalRows_usuarios = mysql_num_rows($usuarios);
$IDmatriz = $row_usuarios['IDmatriz'];
$la_matriz = $row_usuarios['IDmatriz'];

$total = $row_usuarios['ambiente'] + $row_usuarios['propios'] + $row_usuarios['tiempo'] +  $row_usuarios['liderazgo'] + $row_usuarios['ambiente2'] +  $row_usuarios['propios2'] +  $row_usuarios['control'] + $row_usuarios['jornada'] + $row_usuarios['familia'] + $row_usuarios['liderazgo2'] + $row_usuarios['relaciones'] + $row_usuarios['violencia'];

$ambiente = $row_usuarios['ambiente'];
$propios = $row_usuarios['propios'];
$tiempo = $row_usuarios['tiempo'];
$liderazgo = $row_usuarios['liderazgo'];
$ambiente2 = $row_usuarios['ambiente2'];
$propios2 = $row_usuarios['propios2'];
$control = $row_usuarios['control'];
$jornada = $row_usuarios['jornada'];
$familia = $row_usuarios['familia'];
$liderazgo2 = $row_usuarios['liderazgo2'];
$relaciones = $row_usuarios['relaciones'];
$violencia = $row_usuarios['violencia'];

mysql_select_db($database_vacantes, $vacantes);
$query_ambienter = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'ambiente' AND nom35_escalas.calif = '$ambiente'";
$ambienter = mysql_query($query_ambienter, $vacantes) or die(mysql_error());
$row_ambienter = mysql_fetch_assoc($ambienter);
$resultado_ambiente = $row_ambienter['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_propiosr = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'propios' AND nom35_escalas.calif = '$propios'";
$propiosr = mysql_query($query_propiosr, $vacantes) or die(mysql_error());
$row_propiosr = mysql_fetch_assoc($propiosr);
$resultado_propios = $row_propiosr['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_tiempor = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'tiempo' AND nom35_escalas.calif = '$tiempo'";
$tiempor = mysql_query($query_tiempor, $vacantes) or die(mysql_error());
$row_tiempor = mysql_fetch_assoc($tiempor);
$resultado_tiempo = $row_tiempor['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_liderazgor = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'liderazgo' AND nom35_escalas.calif = '$liderazgo'";
$liderazgor = mysql_query($query_liderazgor, $vacantes) or die(mysql_error());
$row_liderazgor = mysql_fetch_assoc($liderazgor);
$resultado_liderazgo = $row_liderazgor['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_ambiente2r = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'ambiente2' AND nom35_escalas.calif = '$ambiente2'";
$ambiente2r = mysql_query($query_ambiente2r, $vacantes) or die(mysql_error());
$row_ambiente2r = mysql_fetch_assoc($ambiente2r);
$resultado_ambiente2 = $row_ambiente2r['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_propios2r = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'propios2' AND nom35_escalas.calif = '$propios2'";
$propios2r = mysql_query($query_propios2r, $vacantes) or die(mysql_error());
$row_propios2r = mysql_fetch_assoc($propios2r);
$resultado_propios2 = $row_propios2r['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_controlr = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'control' AND nom35_escalas.calif = '$control'";
$controlr = mysql_query($query_controlr, $vacantes) or die(mysql_error());
$row_controlr = mysql_fetch_assoc($controlr);
$resultado_control = $row_controlr['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_jornadar = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'jornada' AND nom35_escalas.calif = '$jornada'";
$jornadar = mysql_query($query_jornadar, $vacantes) or die(mysql_error());
$row_jornadar = mysql_fetch_assoc($jornadar);
$resultado_jornada = $row_jornadar['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_familiar = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'familia' AND nom35_escalas.calif = '$familia'";
$familiar = mysql_query($query_familiar, $vacantes) or die(mysql_error());
$row_familiar = mysql_fetch_assoc($familiar);
$resultado_familia = $row_familiar['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_liderazgo2r = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'liderazgo2' AND nom35_escalas.calif = '$liderazgo2'";
$liderazgo2r = mysql_query($query_liderazgo2r, $vacantes) or die(mysql_error());
$row_liderazgo2r = mysql_fetch_assoc($liderazgo2r);
$resultado_liderazgo2 = $row_liderazgo2r['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_relacionesr = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'relaciones' AND nom35_escalas.calif = '$relaciones'";
$relacionesr = mysql_query($query_relacionesr, $vacantes) or die(mysql_error());
$row_relacionesr = mysql_fetch_assoc($relacionesr);
$resultado_relaciones = $row_relacionesr['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_violenciar = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'violencia' AND nom35_escalas.calif = '$violencia'";
$violenciar = mysql_query($query_violenciar, $vacantes) or die(mysql_error());
$row_violenciar = mysql_fetch_assoc($violenciar);
$resultado_violencia = $row_violenciar['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_totalr = "SELECT * FROM nom35_escalas WHERE nom35_escalas.escala = 'general' AND nom35_escalas.calif = '$total'";
$totalr = mysql_query($query_totalr, $vacantes) or die(mysql_error());
$row_totalr = mysql_fetch_assoc($totalr);
$resultado_totalr = $row_totalr['resultado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE nom35_resultados SET plan_accion=%s WHERE IDresultado=%s",
                       GetSQLValueString($_POST['plan_accion'], "text"),
                       GetSQLValueString($_POST['IDresultado'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_n35_resultado.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_planaccion = "-1";
if (isset($_GET['IDresultado'])) {
  $colname_planaccion = $_GET['IDresultado'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_planaccion = sprintf("SELECT * FROM nom35_resultados WHERE IDresultado = %s", GetSQLValueString($colname_planaccion, "int"));
$planaccion = mysql_query($query_planaccion, $vacantes) or die(mysql_error());
$row_planaccion = mysql_fetch_assoc($planaccion);
$totalRows_planaccion = mysql_num_rows($planaccion);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title>Reporte de Resultados NOM35</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="assets/print.css" media="print" />
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/ui/fullcalendar/fullcalendar.min.js"></script>
	<script src="global_assets/js/plugins/visualization/echarts/echarts.min.js"></script>
	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="assets/n35_total_print.js"></script>
    <script type="text/javascript">
	
    var totala = <?php echo $total / 184; ?>;
    var totalb = <?php echo "'" . $resultado_totalr . "'"; ?>;
    var ambientea = <?php echo $ambiente / 12 ; ?>;
    var ambienteb = <?php echo "'" . $resultado_ambiente . "'"; ?>;
    var propiosa = <?php echo $propios / 36 ; ?>;
    var propiosb = <?php echo "'" . $resultado_propios . "'"; ?>;
    var tiempoa = <?php echo $tiempo / 16 ; ?>;
    var tiempob = <?php echo "'" . $resultado_tiempo . "'"; ?>;
    var liderazgoa = <?php echo $liderazgo / 44 ; ?>;
    var liderazgob = <?php echo "'" . $resultado_liderazgo . "'"; ?>;
	
    var ambiente2a = <?php echo $ambiente2 / 12 ; ?>;
    var ambiente2b = <?php echo "'" . $resultado_ambiente2 . "'"; ?>;
    var propios2a = <?php echo $propios2 / 36 ; ?>;
    var propios2b = <?php echo "'" . $resultado_propios2 . "'"; ?>;
    var controla = <?php echo $control / 28 ; ?>;
    var controlb = <?php echo "'" . $resultado_control . "'"; ?>;
    var jornadaa = <?php echo $jornada / 16 ; ?>;
    var jornadab = <?php echo "'" . $resultado_jornada . "'"; ?>;
    var familiaa = <?php echo $familia / 8 ; ?>;
    var familiab = <?php echo "'" . $resultado_familia . "'"; ?>;
    var liderazgo2a = <?php echo $liderazgo2 / 20 ; ?>;
    var liderazgo2b = <?php echo "'" . $resultado_liderazgo2 . "'"; ?>;
    var relacionesa = <?php echo $relaciones / 24 ; ?>;
    var relacionesb = <?php echo "'" . $resultado_relaciones . "'"; ?>;
    var violenciaa = <?php echo $violencia / 32 ; ?>;
    var violenciab = <?php echo "'" . $resultado_violencia . "'"; ?>;
	</script>
	<!-- /theme JS files -->

    <!-- /theme JS files -->
</head>
<body class="has-detached-left" onLoad="window.print()">

<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">



			<!-- Main content -->
			<div class="content-wrapper">		

            <!-- Content area -->
            <div class="content">
                            
					<!-- Contenido -->
                  <div class="panel panel-flat">

						<div class="panel-heading">
                    <img src="assets/img/logo_sahuayo.png" width="219" height="60">
						<h5 class="panel-title">Reporte de Resultados NOM35</h5>
						</div>

					<div class="panel-body">
                    <p>A continuación, se muestran los resultados de la evaluación, por rubro de la Encuesta NOM35.</p>
					</div>
                    
					<!-- /Contenido -->
					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">

								<!-- User details -->
								<div class="content-group">
									<div class="panel-body bg-indigo-400 border-radius-top text-center" style="background-image:
                                     url(http://demo.interface.club/limitless/assets/images/bg.png); background-size: contain;">
										<div class="content-group-sm">
											<h6 class="text-semibold no-margin-bottom">
												<?php echo $row_usuarios['emp_paterno'] . " " .  $row_usuarios['emp_materno'] . " " .  $row_usuarios['emp_nombre']; ?>
											</h6>
											<span class="display-block"><?php echo $row_usuarios['IDempleado']; ?></span>
										</div>
										<a href="#" class="display-inline-block content-group-sm">
											<img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-responsive" alt="" style="width: 110px; height: 110px;">
										</a>
									</div>

									<div class="panel no-border-top no-border-radius-top">
										<ul class="navigation">
											<li class="navigation-header">Datos</li>
											<li><a href="#" data-toggle="tab">No. Emp.: <?php echo $row_usuarios['IDempleado']; ?></a></li>
											<li><a href="#" data-toggle="tab">Paterno: <?php echo $row_usuarios['emp_paterno']; ?></a></li>
											<li><a href="#" data-toggle="tab">Materno: <?php echo $row_usuarios['emp_materno']; ?></a></li>
											<li><a href="#" data-toggle="tab">Nombres: <?php echo $row_usuarios['emp_nombre']; ?></a></li>
											<li><a href="#" data-toggle="tab">Sucursal: <?php echo $row_matriz['matriz']; ?></a></li>
											<li><a href="#" data-toggle="tab">Puesto: <?php echo $row_usuarios['denominacion']; ?></a></li>
											<li><a href="#" data-toggle="tab">Ingreso: <?php 
											 $afecha = date('d/m/Y', strtotime($row_usuarios['fecha_alta'])); echo $afecha; ?></a></li>
											<li><a href="#" data-toggle="tab">Fecha: <?php 
											 $afecha = date('d/m/Y', strtotime($row_usuarios['fecha_aplicacion'])); echo $afecha; ?></a></li>
										</ul>
									</div>
								</div>
								<!-- /user details -->


							</div>
						</div>
					</div>
			  </div>
		            <!-- /detached sidebar -->


					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

							<!-- Tab content -->
							<div class="tab-content">
							  <div class="tab-pane fade in active" id="profile">

									<!-- Daily stats -->
									<div class="panel panel-flat">
										<div class="panel-heading">
											<h3 class="panel-title">Resultados</h3>
										</div>

										<div class="panel-body">
											<div class="chart-container text-center content-group">
										<div class="chart" id="n35_total"></div>
											</div>
										</div>
									</div>
									<!-- /daily stats -->
                                    
									<div class="panel panel-flat">
										<div class="panel-heading">
											<h3 class="panel-title">Resultados por Rubro</h3>
										</div>
                                        
                                        <div class="panel-body">
                                        
                                        <table class="table">
                                          <thead>
                                            <th>Rubro</th>
                                            <th>Calificación</th>
                                            <th>Resultado</th>
                                          </thead>
                                          <tr class="warning">
                                            <td><strong>Calificación Final</strong></td>
                                            <td><strong><?php echo $total; ?></strong></td>
                                            <td><strong><?php echo $resultado_totalr; ?></strong></td>
                                          </tr>
                                          <tr class="info">
                                            <td>Ambiente de trabajo</td>
                                            <td><?php echo $ambiente; ?></td>
                                            <td><?php echo $resultado_ambiente; ?></td>
                                          </tr>
                                          <tr class="info">
                                            <td>Factores Propios de la actividad</td>
                                            <td> <?php echo $propios; ?></td>
                                            <td><?php echo $resultado_propios; ?></td>
                                          </tr>
                                          <tr class="info">
                                            <td>Organización tiempo de trabajo</td>
                                            <td><?php echo $tiempo; ?></td>
                                            <td><?php echo $resultado_tiempo; ?></td>
                                          </tr>
                                          <tr class="info">
                                            <td>Liderazgo</td>
                                            <td><?php echo $liderazgo; ?></td>
                                            <td><?php echo $resultado_liderazgo; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Condiciones ambiente de trabajo</td>
                                            <td> <?php echo $ambiente2; ?></td>
                                            <td><?php echo $resultado_ambiente2; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Carga de trabajo</td>
                                            <td><?php echo $propios2; ?></td>
                                            <td><?php echo $resultado_propios2; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Falta de control del trabajo</td>
                                            <td><?php echo $control; ?></td>
                                            <td><?php echo $resultado_control; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Jornada de trabajo</td>
                                            <td><?php echo $jornada; ?></td>
                                            <td><?php echo $resultado_jornada; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Interferencia trabajo y familia</td>
                                            <td> <?php echo $familia; ?></td>
                                            <td><?php echo $resultado_familia; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Liderazgo</td>
                                            <td><?php echo $liderazgo2; ?></td>
                                            <td><?php echo $resultado_liderazgo2; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Relaciones en el trabajo</td>
                                            <td><?php echo $relaciones; ?></td>
                                            <td><?php echo $resultado_relaciones; ?></td>
                                          </tr>
                                          <tr class="alpha-primary">
                                            <td>Violencia</td>
                                            <td><?php echo $violencia; ?></td>
                                            <td><?php echo $resultado_violencia; ?></td>
                                          </tr>
                                        </table>
										</div>										</div>
                                        
									<!-- Share your thoughts -->
									<div class="panel panel-flat">
										<div class="panel-heading">
											<h6 class="panel-title">Plan de Accion</h6>
										</div>

										<div class="panel-body">
											<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
												<div class="form-group">
												<textarea name="plan_accion" class="form-control mb-15" rows="3" cols="1"><?php echo htmlentities($row_planaccion['plan_accion'], ENT_COMPAT, 'utf-8'); ?></textarea>
                                                      <input type="hidden" name="MM_update" value="form1" />
                                                      <input type="hidden" name="IDresultado" value="<?php echo $row_planaccion['IDresultado']; ?>" />
												</div>

												<div class="row">
						                    		<div class="col-xs-6">
						                    		</div>

						                    	</div>
					                    	</form>
				                    	</div>
									</div>
									<!-- /share your thoughts -->

								</div>
								</div>
							</div>
							<!-- /tab content -->

						</div>
					<!-- /detached content -->                    
                    
                    


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