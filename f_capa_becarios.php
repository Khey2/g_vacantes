<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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
$elmes = date("m"); 

$el_corte = $elmes;
if (date("d") < 20) {$el_corte = $elmes -1;}

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$IDllave = $row_usuario['IDllave'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

if(isset($_POST['el_programa']) && ($_POST['el_programa']  > 0)) {
$_SESSION['el_programa'] = $_POST['el_programa']; } else { $_SESSION['el_programa'] = "1,2,3,4,5,6";}

if(isset($_POST['el_estatus'])) {
$_SESSION['el_estatus'] = $_POST['el_estatus']; } else { $_SESSION['el_estatus'] = 1;}

if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } 
if(!isset($_SESSION['el_mes'])) {$_SESSION['el_mes'] = $el_corte;}

$el_programa = $_SESSION['el_programa'];
$el_estatus = $_SESSION['el_estatus'];
$el_mes = $_SESSION['el_mes'];

$Fecha = $anio.'-'.$el_mes.'-01';
$fini = new DateTime($Fecha);
$fini->modify('first day of this month');
$finik = $fini->format('Y/m/d'); 

$fter = new DateTime($Fecha);
$fter->modify('last day of this month');
$fterk = $fter->format('Y/m/d'); 


if (isset($_POST['buscado'])) {	
$arreglo = '';
$array = explode(" ", $_POST['buscado']);
$contar = substr_count($_POST['buscado'], ' ') + 1;
$i = 0;
while($contar > $i) {
$arreglo .= " AND (capa_becarios.emp_paterno LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR capa_becarios.emp_materno LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR capa_becarios.emp_nombre LIKE '%" . $array[$i] . "%' )"; 
    $i++; } }
	
if (!isset($_POST['buscado'])) { $filtroBuscado = ''; }  else { $filtroBuscado = $arreglo; $IDvisible = 1;}


$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_becarios  = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, vac_matriz.matriz, vac_areas.area, vac_subareas.subarea FROM capa_becarios LEFT JOIN vac_subareas ON capa_becarios.IDsubarea = vac_subareas.IDsubarea LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo LEFT JOIN vac_matriz ON capa_becarios.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON capa_becarios.IDarea = vac_areas.IDarea WHERE DATE(capa_becarios.fecha_alta) <= '$fterk' AND capa_becarios.activo = '$el_estatus' AND capa_becarios.IDtipo IN ( $el_programa ) AND capa_becarios.IDempleadoJ = '$el_usuario' ".$filtroBuscado; 
mysql_query("SET NAMES 'utf8'");
$becarios = mysql_query($query_becarios , $vacantes) or die(mysql_error());
$row_becarios = mysql_fetch_assoc($becarios);
$totalRows_becarios  = mysql_num_rows($becarios );

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT * FROM vac_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

mysql_select_db($database_vacantes, $vacantes);
$query_programa = "SELECT * FROM capa_becarios_tipo";
$programa = mysql_query($query_programa, $vacantes) or die(mysql_error());
$row_programa = mysql_fetch_assoc($programa);
$totalRows_programa = mysql_num_rows($programa);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDempleado_n = $_POST["IDempleado"]; 
	$IDestatus_p = $_POST["activo"]; 
	$query1 = "UPDATE capa_becarios SET activo = '$IDestatus_p' WHERE prod_activos.IDempleado = '$IDempleado_n'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: capa_becarios_activos.php?info=9"); 	
}

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
	<script src="global_assets/js/plugins/tables/datatables/bec_datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html_bec.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
	<!-- /theme JS files -->
</head>
<body class="has-detached-left" > 
		<?php require_once('assets/f_mainnav.php'); ?>
		<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/f_pheader.php'); ?>

				<!-- Content area -->
				<div class="content">
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el estatus del Becario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 99))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la evaluación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 88))) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El evaluado aún no captura actividades del mes.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">

								<!-- Latest searches -->
								<div class="panel panel-white">
									<div class="panel-heading">
										<div class="panel-title text-semibold">
											Instrucciones
										</div>
									</div>

									<div class="list-group no-border">
										<div class="list-group-item">
										<p>Las evaluaciones se muestran por mes.<p/>
										<p>Utiliza el filtro para encontrar becarios por <strong>Mes</strong>, <strong>Programa</strong> o <strong>Estatus</strong>.<p/>
										<p>Dando clic en <strong>Evaluar</strong>, puedes evaluar el mes actual, para ver otros meses, da clic en <strong>Evaluaciones</strong>.<p/>
										<p>Para cualquier duda o aclaración, contáctate con Esperanza Flores al correo <a href="amilto:egflores@sahuayo.mx">egflores@sahuayo.mx</a>.</p>
										</div>
									</div>
								</div>
								<!-- /latest searches -->

								<!-- Sidebar search -->
								<div class="panel panel-white">
									<div class="panel-heading">
										<div class="panel-title text-semibold">
											Filtro 
										</div>
									</div>

									<div class="panel-body">

											<div class="form-group">
												<div class="has-feedback has-feedback-left">
													<label class="display-block">Mes actual: <strong><?php echo $elmes;?></strong>
													</label>
												</div>
											</div>



										<form action="f_capa_becarios.php" method="POST">
											<div class="form-group">
												<div class="has-feedback has-feedback-left">
													<input type="search" class="form-control" name="buscado" placeholder="<?php 
													if (isset($_POST['buscado']) AND $_POST['buscado'] != '') {echo $_POST['buscado']; } else {echo "Nombre"; } ?>">
													<div class="form-control-feedback">
													</div>
												</div>
											</div>

											<div class="form-group">
												<div class="has-feedback has-feedback-left">
													<label class="display-block">
													<select class="form-control" name="el_mes">
													   <?php do { ?>
													   <option value="<?php echo $row_meses['IDmes']?>"<?php if ($row_meses['IDmes'] == $el_mes) {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']; ?></option>
													   <?php } while ($row_meses = mysql_fetch_assoc($meses)); $rows = mysql_num_rows($meses);  if($rows > 0) { mysql_data_seek($meses, 0); $row_meses = mysql_fetch_assoc($meses); } ?> 
													</select>
													</label>
												</div>
											</div>

											<div class="form-group">
												<div class="has-feedback has-feedback-left">
													<label class="display-block">
													<select class="form-control" name="el_programa">
													   <option value="">Programa (Todos)</option>
													   <?php do { ?>
													   <option value="<?php echo $row_programa['IDtipo']?>"<?php if (!(strcmp($row_programa['IDtipo'], $el_programa))) {echo "selected=\"selected\"";} ?>><?php echo $row_programa['tipo']?></option>
													   <?php } while ($row_programa = mysql_fetch_assoc($programa)); $rows = mysql_num_rows($programa);  if($rows > 0) { mysql_data_seek($programa, 0); $row_programa = mysql_fetch_assoc($programa); } ?> 
													</select>
													</label>
												</div>
											</div>
											
											<div class="form-group">
												<div class="has-feedback has-feedback-left">
													<label class="display-block">
													<select name="el_estatus" class="form-control">
													   <option value="1" <?php if ($el_estatus == 1) {echo "selected=\"selected\"";} ?>>Activos</option>
													   <option value="0" <?php if ($el_estatus == 0) {echo "selected=\"selected\"";} ?>>Inactivos</option>
													</select>
													</label>
												</div>
											</div>
											
											<button type="submit" class="btn bg-blue btn-block"><i class="icon-search4 text-size-base position-left"></i>Filtrar</button>
											<a href="f_capa_becarios.php" class="btn bg-danger btn-block"><i class="icon-eraser2 text-size-base position-left"></i>Borrar Filtro</a>
										</form>
									</div>
								</div>
								<!-- /sidebar search -->


							</div>
						</div>
					</div>
		            <!-- /detached sidebar -->


					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

							<!-- Cards layout -->
							<div class="panel panel-white">

						
					<table class="table datatable-button-html5-columns">
						<thead>
							<tr>
							    <th><h6 class="panel-title text-semibold">Becarios</h6></th>
							    <th></th>
						    </tr>
					    </thead>
						<tbody>							  
							<?php if ($totalRows_becarios > 0) { ?>
							<?php do { ?>
							<?php
							$IDempleado = $row_becarios['IDempleado'];
							mysql_select_db($database_vacantes, $vacantes);
							$query_results = "SELECT * FROM capa_becarios_evaluacion WHERE IDempleado = $IDempleado AND anio = $anio AND IDmes = $el_mes";
							$results = mysql_query($query_results, $vacantes) or die(mysql_error());
							$row_results = mysql_fetch_assoc($results);
							$totalRows_results = mysql_num_rows($results);
							?>

							<tr>
							<td style="width: 60%;">							
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
											<?php if ($row_becarios['activo'] == 1) { ?>
											<a  class="text text-semibold text-primary" href="capa_becarios_edit.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>"><?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?></a>
											<?php } else { ?>
											<a  class="text text-semibold text-danger" href="capa_becarios_edit.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>"><?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?></a>
											<?php } ?>											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becarios['tipo']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becarios['fecha_alta'])); ?></li>
												<li><strong>Fecha baja:</strong> <?php if ($row_becarios['fecha_baja'] != '') {echo date('d/m/Y', strtotime($row_becarios['fecha_baja']));} else { echo "No aplica";} ?></li>
												<li><strong>Fecha nacimiento:</strong> <?php if ($row_becarios['fecha_nacimiento'] != '') {echo date('d/m/Y', strtotime($row_becarios['fecha_nacimiento']));} else { echo "-";} ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Área:</strong> <?php echo $row_becarios['area']; ?></li>
												<li><strong>Subárea:</strong> <?php echo $row_becarios['subarea']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Estatus:</strong> <?php if ($row_becarios['activo'] == 1) {echo "<span class='text-success'>Activo</span>";} else {echo "<span class='text-danger'>Inactivo</span>";} ?></li>
											<?php if ($row_results['IDcalificacion'] != '') { ?>
												<li><strong>Resultado:</strong> <?php if ($row_results['IDcalificacion'] > 1) { for ($x = 0; $x < $row_results['IDcalificacion']; $x++) { echo "<i class='icon-star-full2 text-success'></i>"; }} else { echo "<i class='icon-star-full2 text-success'></i>"; }?></li>
											<?php } ?>
											</ul>												
										</div>
											
									</li>
								</ul>							
							</td>	
							<td style="width: 40%;">
							<div class="media-right text-center">
							
							<?php
							$IDempleado = $row_becarios['IDempleado'];
							mysql_select_db($database_vacantes, $vacantes);
							$query_results = "SELECT * FROM capa_becarios_evaluacion WHERE IDempleado = $IDempleado AND anio = $anio AND IDmes = $el_mes";
							$results = mysql_query($query_results, $vacantes) or die(mysql_error());
							$row_results = mysql_fetch_assoc($results);
							$totalRows_results = mysql_num_rows($results);

							$query_actividades = "SELECT * FROM capa_becarios_actividades WHERE IDempleado = $IDempleado AND anio = $anio AND IDmes = $el_mes";
							$actividades = mysql_query($query_actividades, $vacantes) or die(mysql_error());
							$row_actividades = mysql_fetch_assoc($actividades);
							$totalRows_actividades = mysql_num_rows($actividades);
							?>
							
							<?php if($totalRows_results > 0) { ?>
							<a href="f_capa_becarios_evaluar.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>&IDmes=<?php echo $el_mes; ?>&anio=<?php echo $anio; ?>" class="btn bg-warning-400 btn-block"><i class="icon-checkbox-checked position-left"></i></i>Evaluado</a>
							
							<?php if($totalRows_actividades > 0) { ?>
							<a href="f_capa_becarios_evaluar_new.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>&corte=1" class="btn bg-info btn-block"><i class="icon-list position-left"></i></i>Actividades</a>
							<?php } ?>

							<?php } else { ?>
							<a href="f_capa_becarios_evaluar.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>&IDmes=<?php echo $el_mes; ?>&anio=<?php echo $anio; ?>" class="btn bg-warning-400 btn-block"><i class="icon-checkbox-unchecked position-left"></i></i>Evaluar Mes</a>
							<?php } ?>
							<a href="f_capa_becarios_edit.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>" class="btn bg-primary-400 btn-block"><i class="icon-file-eye2 position-left"></i></i>Ver Datos</a>
							<?php
							mysql_select_db($database_vacantes, $vacantes);
							$query_resultsT = "SELECT * FROM capa_becarios_evaluacion WHERE IDempleado = $IDempleado";
							$resultsT = mysql_query($query_resultsT, $vacantes) or die(mysql_error());
							$row_resultsT = mysql_fetch_assoc($resultsT);
							$totalRows_resultsT = mysql_num_rows($resultsT);
							?>
							<?php if($totalRows_resultsT > 0) { ?>
							<a href="f_capa_becarios_historico.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>" class="btn bg-success  btn-block"><i class="icon-list3 position-left"></i></i>Evaluaciones</a>
							<?php }  ?>
							</div>
							</td>
							</tr>
							
							
							<?php } while ($row_becarios = mysql_fetch_assoc($becarios)); ?>
							<?php } else { ?>
							<tr>
							<td><h5><span class="text text-danger">Nada que mostar con el filtro seleccionado.</span><h5></td>	
							</tr>
							<?php } ?>
                        </tbody>
                   </table> 


							</div>
							<!-- /cards layout -->



						</div>
					</div>
					<!-- /detached content -->


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