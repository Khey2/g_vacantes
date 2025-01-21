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
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio_kpis'];
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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatriz_kpi = $row_usuario['IDmatriz_kpi'];
$IDmatrizes = $row_usuario['IDmatriz_kpis'];
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];
$IDareas = $row_usuario['IDareas_kpis'];
$IDarea_kpi = $row_usuario['IDarea_kpi'];

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$fecha = date("Y-m-d");
$el_mes = date("m") - 1;
unset($_SESSION['mi_mes']);


$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if (isset($_POST['mi_area'])) {$_SESSION['mi_area'] = $_POST['mi_area'];} 
elseif (!isset($_SESSION['mi_area'])) {$_SESSION['mi_area'] = $IDarea_kpi;} 

if (isset($_POST['el_anio'])) {$_SESSION['el_anio'] = $_POST['el_anio'];} 
elseif (!isset($_SESSION['el_anio'])) {$_SESSION['el_anio'] = $anio;} 

if (isset($_POST['mi_mes'])) {$_SESSION['mi_mes'] = $_POST['mi_mes'];} 
elseif (!isset($_SESSION['mi_mes'])) {$_SESSION['mi_mes'] = $el_mes;} 

if (isset($_POST['mi_matriz'])) {$_SESSION['mi_matriz'] = $_POST['mi_matriz'];} 
elseif (!isset($_SESSION['mi_matriz'])) {$_SESSION['mi_matriz'] = $la_matriz;} 

$mi_area = $_SESSION['mi_area'];
$mi_mes = $_SESSION['mi_mes'];
$mi_matriz = $_SESSION['mi_matriz'];
$el_anio = $_SESSION['el_anio'];

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_lmatriz = "SELECT * FROM kpi_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
mysql_query("SET NAMES 'utf8'");
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);

$query_larea = "SELECT * FROM kpis_areas WHERE IDarea IN ($IDareas) ";
mysql_query("SET NAMES 'utf8'");
$larea = mysql_query($query_larea, $vacantes) or die(mysql_error());
$row_larea = mysql_fetch_assoc($larea);

$query_lmeses = "SELECT * FROM kpis_meses WHERE visible = 1";
mysql_query("SET NAMES 'utf8'");
$lmeses = mysql_query($query_lmeses, $vacantes) or die(mysql_error());
$row_lmeses = mysql_fetch_assoc($lmeses);


	if ($mi_area == 1) { $paratipo = "venTip";} 
elseif ($mi_area == 2) { $paratipo = "comTip";} 
elseif ($mi_area == 3) { $paratipo = "logTip";} 
elseif ($mi_area == 4) { $paratipo = "vdTip";} 
elseif ($mi_area == 5) { $paratipo = "ceTip";} 
elseif ($mi_area == 6) { $paratipo = "comTip";} 
elseif ($mi_area == 0) { $paratipo = "venTip";} 

//echo "Area: ".$_SESSION['mi_area'];
//echo "<br/>Mes: ".$_SESSION['mi_mes'];
//echo "<br/>Matriz: ".$_SESSION['mi_matriz'];
//echo "<br/>Tipo: " . $paratipo;

mysql_select_db($database_vacantes, $vacantes);
$query_kpis = "SELECT kpis_resultados.IDresultado, kpis_resultados.IDkpi, kpis_resultados.IDarea, kpis_resultados.IDmatriz, kpis_resultados.mes, kpis_resultados.v1, kpis_resultados.v2, kpis_resultados.v3, kpis_resultados.v4, kpis_resultados.v5, kpis_resultados.v6, kpis_resultados.v7, kpis_resultados.v8, kpis_resultados.objetivo, kpis_resultados.resultado, kpis_resultados.venRes, kpis_resultados.venPon, kpis_resultados.comRes, kpis_resultados.comPon, kpis_resultados.logRes, kpis_resultados.logPon, kpis_resultados.vdRes, kpis_resultados.vdPon, kpis_resultados.ceRes, kpis_resultados.cePon, kpis_resultados.venTip, kpis_resultados.comTip, kpis_resultados.logTip, kpis_resultados.vdTip, kpis_resultados.ceTip, kpis_kpis.kpi, kpis_descripcion.explicacion, kpis_descripcion.v1_, kpis_descripcion.v2_, kpis_descripcion.v3_, kpis_descripcion.v4_, kpis_descripcion.v5_, kpis_descripcion.v6_, kpis_descripcion.v7_, kpis_descripcion.v8_ FROM kpis_resultados LEFT JOIN kpis_kpis ON kpis_resultados.IDkpi = kpis_kpis.IDkpi LEFT JOIN kpis_descripcion ON kpis_descripcion.IDkpi = kpis_resultados.IDkpi WHERE anio = $el_anio AND IDmatriz = '$mi_matriz' AND mes = '$mi_mes' AND IDarea LIKE '%$mi_area%' and $paratipo = 1";
mysql_query("SET NAMES 'utf8'"); 
$kpis = mysql_query($query_kpis, $vacantes) or die(mysql_error());
$row_kpis = mysql_fetch_assoc($kpis);
$totalRows_kpis = mysql_num_rows($kpis);

mysql_select_db($database_vacantes, $vacantes);
$query_kpis_t = "SELECT * FROM kpis_resultados_t WHERE IDmatriz = '$mi_matriz' AND mes = '$mi_mes' AND anio = $el_anio AND IDarea LIKE '%$mi_area%'";
mysql_query("SET NAMES 'utf8'");
$kpis_t = mysql_query($query_kpis_t, $vacantes) or die(mysql_error());
$row_kpis_t = mysql_fetch_assoc($kpis_t);
$totalRows_kpis_t = mysql_num_rows($kpis_t);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/natural_sort.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/gallery_library.js"></script>
	<!-- /theme JS files -->
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>

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

<!-- Dashboard content -->
					<div class="row">
						<div class="col-lg-8">

							<!-- Marketing campaigns -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h4 class="panel-title">KPIs <?php echo $anio; ?></h4>

								<?php 
                                $query_txt1 = "SELECT * FROM kpi_matriz WHERE IDmatriz = '$mi_matriz'";
                                mysql_query("SET NAMES 'utf8'");
                                $txt1 = mysql_query($query_txt1, $vacantes) or die(mysql_error());
                                $row_txt1 = mysql_fetch_assoc($txt1);
                                
                                $query_txt2 = "SELECT * FROM kpis_areas WHERE IDarea = $mi_area";
                                mysql_query("SET NAMES 'utf8'");
                                $txt2 = mysql_query($query_txt2, $vacantes) or die(mysql_error());
                                $row_txt2 = mysql_fetch_assoc($txt2);
                                
                                $query_txt3 = "SELECT * FROM kpis_meses WHERE IDmes = $mi_mes";
                                mysql_query("SET NAMES 'utf8'");
                                $txt3 = mysql_query($query_txt3, $vacantes) or die(mysql_error());
                                $row_txt3 = mysql_fetch_assoc($txt3);
                                ?>

							<p>Bienvenido.<br/> 
                            A continuación se muestran los resultados del año <strong><?php echo $el_anio; ?></strong>, mes de <strong><?php echo $row_txt3['denominacion']; ?></strong> de la Matriz de <strong> <?php echo $row_txt1['matriz']; ?></strong> del área de <strong><?php echo $row_txt2['area'];?></strong>.</p>
								</div>
								
								<div class="table-responsive">
									<table class="table text-nowrap table-xlg">
										<thead>
											<tr>
												<th>KPI</th>
												<th class="col-md-2">Objetivo</th>
												<th class="col-md-2">Resultado</th>
												<th class="col-md-2">Ponderacion</th>
												<th class="col-md-2">Resultado Ponderado</th>
												<th class="text-center" style="width: 20px;">Detalle</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th>Total</th>
												<th class="col-md-2"></th>
												<th class="col-md-2"><?php echo $row_kpis_t['directo']; ?></th>
												<th class="col-md-2"><?php echo $row_kpis_t['ponderado']; ?></th>
												<th class="col-md-2"><?php echo $row_kpis_t['total_ponderado']; ?></th>
												<th></th>
											</tr>
										</tfoot>
										<tbody>
				  <?php if( $totalRows_kpis > 0) { ?>
				  <?php do { ?>
											<tr>
												<td>
													<div class="media-left media-middle">
													<?php if (($mi_area == 1 and $row_kpis['venRes'] != 0)
														or ($mi_area == 2 and $row_kpis['comRes'] != 0)
														or ($mi_area == 3 and $row_kpis['logRes'] != 0)
														or ($mi_area == 4 and $row_kpis['vdRes'] != 0) 
														or ($mi_area == 5 and $row_kpis['ceRes'] != 0)
														or ($mi_area == 6 and $row_kpis['comRes'] != 0)){ ?>
                                                    	<i class="icon-stats-growth text-success-600"></i>
													<?php } else { ?>
                                                    	<i class=" icon-stats-decline text-warning-600"></i>
													<?php } ?>
                                                    </div>
													<div class="media-left">
														<div class=""><a href="#" class="text-default text-semibold">
														<?php echo $row_kpis['kpi']; ?></a></div>
													</div>
												</td>
												<td><span class="text-success-600"><?php echo $row_kpis['objetivo']; ?></span></td>
												<td><span class="text-success-600"><?php echo $row_kpis['resultado']; ?></span></td>
													<?php 	  	 if ($mi_area == 1) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['venPon']; ?></span></td>
													<?php } else if ($mi_area == 2) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['comPon']; ?></span></td>
													<?php } else if ($mi_area == 3) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['logPon']; ?></span></td>
													<?php } else if ($mi_area == 4) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['vdPon']; ?></span></td>
													<?php } else if ($mi_area == 5) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['cePon']; ?></span></td>
													<?php } else if ($mi_area == 6) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['comPon']; ?></span></td>
													<?php }?>


													<?php 	  	 if ($mi_area == 1) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['venRes']; ?></span></td>
													<?php } else if ($mi_area == 2) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['comRes']; ?></span></td>
													<?php } else if ($mi_area == 3) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['logRes']; ?></span></td>
													<?php } else if ($mi_area == 4) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['vdRes']; ?></span></td>
													<?php } else if ($mi_area == 5) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['ceRes']; ?></span></td>
													<?php } else if ($mi_area == 6) { ?>
												<td><span class="text-primary-600"><?php echo $row_kpis['comRes']; ?></span></td>
													<?php } ?>

												<td class="text-center">
                                                <button type="button" class="btn btn-default btn-sm" data-toggle="modal" 
                                                data-target="#modal_form_horizontal<?php echo $row_kpis['IDkpi']; ?>"><i class="icon-menu7"></i></button></td>
											
                                            
                                            
                                            
                    <!-- Horizontal form modal -->
					<div id="modal_form_horizontal<?php echo $row_kpis['IDkpi']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title"><?php echo $row_kpis['kpi']; ?></h4>
								</div>

								<form action="#" class="form-horizontal">
                               <fieldset class="content-group">
									<div class="modal-body">

								<?php  if ($row_kpis['explicacion'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold">Descripción:</label>
										<div class="col-lg-8">
											<div class="input-group">
												<?php echo $row_kpis['explicacion']; ?>
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   


								<?php  if ($row_kpis['v1_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v1_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v1']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
								<?php  if ($row_kpis['v2_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v2_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v2']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
								<?php  if ($row_kpis['v3_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v3_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v3']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
								<?php  if ($row_kpis['v4_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v4_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v4']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
								<?php  if ($row_kpis['v5_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v5_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v5']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
								<?php  if ($row_kpis['v6_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v6_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v6']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
								<?php  if ($row_kpis['v7_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v7_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v7']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
								<?php  if ($row_kpis['v8_'] != '') { ?>                                   
									<div class="form-group">
										<label class="control-label col-lg-4 text-semibold"><?php echo $row_kpis['v8_']; ?></label>
										<div class="col-lg-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="icon-arrow-right5"></i></span>
												<input type="text" class="form-control" readonly="readonly" value="<?php echo $row_kpis['v8']; ?>">
											</div>
										</div>
									</div>
									<p>&nbsp;</p>
								<?php } ?>                                   
                                    
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									</div>
								</fieldset>
                                </form>
							</div>
						</div>
					</div>
					<!-- /horizontal form modal -->
                                            
                                            
                                            
                                            </tr>
				  <?php } while ($row_kpis = mysql_fetch_assoc($kpis)); ?>
				  <?php } else { ?>
							<td colspan="6"><span class="text-warning-400">Aún no se publican resultados para el mes seleccionado.</span></td>
				  <?php }  ?>
										</tbody>
									</table>
								</div>
							</div>
							<!-- /marketing campaigns -->


						</div>

						<div class="col-lg-4">

							<!-- Daily financials -->
               				<form method="POST" action="kpis.php">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Filtro</h6>
								</div>

								<div class="panel-body">
									<div class="content-group-xs" id="bullets">Selecciona las opciones de filtrado:</div>

									<ul class="media-list">
										<li class="media">
											<div class="media-body">Año:
                             <select class="form-control" name="el_anio" id="el_anio">
                               <option value="2021"<?php if (!(strcmp(2021, $el_anio))) {echo "selected=\"selected\"";} ?>>2021</option>
                               <option value="2022"<?php if (!(strcmp(2022, $el_anio))) {echo "selected=\"selected\"";} ?>>2022</option>
                               <option value="2023"<?php if (!(strcmp(2023, $el_anio))) {echo "selected=\"selected\"";} ?>>2023</option>
                               <option value="2024"<?php if (!(strcmp(2024, $el_anio))) {echo "selected=\"selected\"";} ?>>2024</option>
                               <option value="2025"<?php if (!(strcmp(2025, $el_anio))) {echo "selected=\"selected\"";} ?>>2025</option>
                              </select>
											</div>
										</li>

										<li class="media">
											<div class="media-body">Mes:
                             <select class="bootstrap-select" data-live-search="true" data-width="100%" name="mi_mes" id="mi_mes">
                            <?php do { ?>
                               <option value="<?php echo $row_lmeses['IDmes']?>"<?php if (!(strcmp($row_lmeses['IDmes'], $mi_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmeses['denominacion']?></option>
                               <?php
                              } while ($row_lmeses = mysql_fetch_assoc($lmeses));
                              $rows = mysql_num_rows($lmeses);
                              if($rows > 0) {
                                  mysql_data_seek($lmeses, 0);
                                  $row_lmeses = mysql_fetch_assoc($lmeses);
                              } ?> 
                              </select>
											</div>
										</li>

										<li class="media">
											<div class="media-body">Matriz:
                             <select class="bootstrap-select" data-live-search="true" data-width="100%" name="mi_matriz" id="mi_matriz">
                            <?php do { ?>
                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $mi_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                               <?php
                              } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                              $rows = mysql_num_rows($lmatriz);
                              if($rows > 0) {
                                  mysql_data_seek($lmatriz, 0);
                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
                              } ?> 
                              </select>
											</div>
										</li>

										<li class="media">
											<div class="media-body">Area:
                             <select class="bootstrap-select" data-live-search="true" data-width="100%" name="mi_area" id="mi_area">
                            <?php do { ?>
                               <option value="<?php echo $row_larea['IDarea']?>"<?php if (!(strcmp($row_larea['IDarea'], $mi_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_larea['area']?></option>
                               <?php
                              } while ($row_larea = mysql_fetch_assoc($larea));
                              $rows = mysql_num_rows($larea);
                              if($rows > 0) {
                                  mysql_data_seek($larea, 0);
                                  $row_larea = mysql_fetch_assoc($larea);
                              } ?> 
                              </select>
											</div>
										</li>

										<li class="media">
											<div class="media-body">
                         	 <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
											</div>
										</li>

									</ul>

								</div>
							</div>
						</form>
							<!-- /daily financials -->

							<!-- Daily financials -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Contacto</h6>
								</div>

								<div class="panel-body">
									<div class="content-group-xs" id="bullets"></div>

									<ul class="media-list">
										<li class="media">
											<div class="media-left">
												<a href="mailto:gortiz@sahuayo.mx" class="btn border-danger text-danger btn-flat btn-rounded btn-icon btn-xs"><i class="icon-mail5"></i></a>
											</div>
											
											<div class="media-body">
												<strong> Germán Ortiz.</strong> Cálculo de Resultados
												<div class="media-annotation">gortiz@sahuayo.mx</div>
											</div>
										</li>

										<li class="media">
											<div class="media-left">
												<a href="mailto:jacardenas@sahuayo.mx" class="btn border-danger text-danger btn-flat btn-rounded btn-icon btn-xs"><i class="icon-mail5"></i></a>
											</div>
											
											<div class="media-body">
												<strong> Juan Cardenas.</strong> Reporte de Resultados
												<div class="media-annotation">jacardenas@sahuayo.mx</div>
											</div>
										</li>

									</ul>
								</div>
							</div>
							<!-- /daily financials -->

							<!-- Daily financials -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">KPIs</h6>
								</div>

								<div class="panel-body">
									<div class="media-body">
									</div>
								</div>
							</div>
							<!-- /daily financials -->

                            
						</div>
					</div>
					<!-- /dashboard content -->


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