<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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
//$anio = $row_variables['anio'];
$anio = 2024;
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

$IDmatriz = $row_usuario['IDmatriz'];


if(isset($_POST['el_area']) && ($_POST['el_area']  > 0)) {
$_SESSION['el_area'] = $_POST['el_area']; } else { $_SESSION['el_area'] = "1,2,3,4,5,6,7,8,9,10,11";}

if(isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = "0";}

if(isset($_POST['el_programa']) && ($_POST['el_programa']  > 0)) {
$_SESSION['el_programa'] = $_POST['el_programa']; } else { $_SESSION['el_programa'] = "1,2,3,4,5,6";}

if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } else { $_SESSION['el_mes'] = date("m");}

if(isset($_POST['el_estatus'])) {
$_SESSION['el_estatus'] = $_POST['el_estatus']; } else { $_SESSION['el_estatus'] = 1;}

$el_programa = $_SESSION['el_programa'];
$el_mes = $_SESSION['el_mes'];
$el_estatus = $_SESSION['el_estatus'];
$el_area = $_SESSION['el_area'];
$la_matriz = $_SESSION['la_matriz'];

if ($la_matriz != 0) { $filtroMatriz = ' AND capa_becarios.IDmatriz  = $la_matriz';} else { $filtroMatriz = '';}

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
$arreglo .= " OR vac_areas.area LIKE '%" . $array[$i] . "%' "; 
$arreglo .= " OR vac_subareas.subarea LIKE '%" . $array[$i] . "%' "; 
$arreglo .= " OR capa_becarios.emp_materno LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR capa_becarios.emp_nombre LIKE '%" . $array[$i] . "%' )"; 
    $i++; } }
	
if (!isset($_POST['buscado'])) { $filtroBuscado = ''; }  else { $filtroBuscado = $arreglo; $IDvisible = 1;}

$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_becarios  = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, vac_matriz.matriz, vac_areas.area, vac_subareas.subarea FROM capa_becarios LEFT JOIN vac_subareas ON capa_becarios.IDsubarea = vac_subareas.IDsubarea LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo LEFT JOIN vac_matriz ON capa_becarios.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON capa_becarios.IDarea = vac_areas.IDarea WHERE DATE(capa_becarios.fecha_alta) <= '$fterk' AND capa_becarios.activo = '$el_estatus' AND capa_becarios.IDtipo in ($el_programa) AND capa_becarios.IDarea in ($el_area)".$filtroMatriz.$filtroBuscado;
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
$query_matrizl = "SELECT DISTINCT vac_matriz.matriz, vac_matriz.IDmatriz FROM vac_matriz RIGHT JOIN capa_becarios ON vac_matriz.IDmatriz = capa_becarios.IDmatriz ORDER BY vac_matriz.matriz ASC";
$matrizl = mysql_query($query_matrizl, $vacantes) or die(mysql_error());
$row_matrizl = mysql_fetch_assoc($matrizl);
$totalRows_matrizl = mysql_num_rows($matrizl);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT * FROM capa_becarios_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_programa = "SELECT * FROM capa_becarios_tipo";
$programa = mysql_query($query_programa, $vacantes) or die(mysql_error());
$row_programa = mysql_fetch_assoc($programa);
$totalRows_programa = mysql_num_rows($programa);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDempleado_n = $_POST["IDempleado"]; 
	$IDestatus_p = $_POST["activo"]; 
	$query1 = "UPDATE capa_becarios SET activo = '$IDestatus_p' WHERE capa_becarios.IDempleado = '$IDempleado_n'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: capa_becarios_activos.php?info=9"); 	
}

// borrar alternativo
if ((isset($_GET['restablecer'])) && ($_GET['restablecer'] == 1)) {
  
  $borrado = $_GET['IDempleado'];
  $deleteSQL = "UPDATE capa_becarios SET password = '0aab3e28d9e60055ea28acb2338b2676' WHERE IDempleado ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_becarios_activos.php?info=4");
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha restablecido correctamente el Password.
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
										<p>Utiliza el filtro para encontrar becarios por <strong>Mes</strong>, <strong>Área</strong> o <strong>Nombre</strong>.<p/>
										<p>Dando clic en <strong>Evaluar</strong>, puedes evaluar el mes actual, para ver otros meses, da clic en <strong>Evaluaciones</strong>.<p/>
										<p><i class='icon-user-cancel text-warning'></i> = Becarios sin acceso por falta de correo.<p/>
										
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
										<form action="capa_becarios_activos.php" method="POST">

											<div class="form-group">
												<div class="has-feedback has-feedback-left">
													<label class="display-block">Mes actual: <strong><?php echo $elmes;?></strong>
													</label>
												</div>
											</div>

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
													   <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $el_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']; ?></option>
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
													<select class="form-control" name="el_area">
													   <option value="">Matriz (Todas)</option>
													   <?php do { ?>
													   <option value="<?php echo $row_matrizl['IDmatriz']?>"<?php if (!(strcmp($row_matrizl['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_matrizl['matriz']?></option>
													   <?php } while ($row_matrizl = mysql_fetch_assoc($matrizl)); $rows = mysql_num_rows($matrizl);  if($rows > 0) { mysql_data_seek($matrizl, 0); $row_matrizl = mysql_fetch_assoc($matrizl); } ?> 
													</select>
													</label>
												</div>
											</div>
											
											<div class="form-group">
												<div class="has-feedback has-feedback-left">
													<label class="display-block">
													<select class="form-control" name="el_area">
													   <option value="">ÁREA (Todas)</option>
													   <?php do { ?>
													   <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_area['area']?></option>
													   <?php } while ($row_area = mysql_fetch_assoc($area)); $rows = mysql_num_rows($area);  if($rows > 0) { mysql_data_seek($area, 0); $row_area = mysql_fetch_assoc($area); } ?> 
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
											<a href="capa_becarios_activos.php" class="btn bg-danger btn-block"><i class="icon-eraser2 text-size-base position-left"></i>Borrar Filtro</a>
											<p>&nbsp;</p>
											<div class="list-group-divider"></div>
											<p>&nbsp;</p>
											<a href="capa_becarios_edit.php" class="btn bg-primary btn-block"><i class="icon-user-plus text-size-base position-left"></i>Agregar Becario</a>
										</form>
									</div>
								</div>
								<!-- /sidebar search -->

								<!-- Latest searches -->
								<div class="panel panel-white">
									<div class="panel-heading">
										<div class="panel-title text-semibold">
											Reportes
										</div>
									</div>
										<div class="list-group-item">
										<p>Selecciona el reporte para descargarlo.<p/>
										</div>
									<div class="list-group no-border">
										<div class="list-group-item">
											<a href="capa_reporte1.php" class="btn bg-success btn-block"><i class="icon-file-excel text-size-base position-left"></i>Activos</a>
											<a href="capa_reporte2.php" class="btn bg-success btn-block"><i class="icon-file-excel text-size-base position-left"></i>Inactivos</a>
											<a href="capa_reporte3.php" class="btn bg-success btn-block"><i class="icon-file-excel text-size-base position-left"></i>Evaluaciones</a>
											<a href="capa_reporte4.php" class="btn bg-success btn-block"><i class="icon-file-excel text-size-base position-left"></i>Actividades</a>
										</div>
									</div>
								</div>
								<!-- /latest searches -->


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
							$IDempleado = $row_becarios['ELempleado'];								
							$query_evaluaciones = "SELECT * FROM capa_becarios_evaluacion WHERE IDempleado = $IDempleado";
							$evaluaciones = mysql_query($query_evaluaciones, $vacantes) or die(mysql_error());
							$row_evaluaciones = mysql_fetch_assoc($evaluaciones);
							$totalRows_evaluaciones = mysql_num_rows($evaluaciones); 

							$query_actividades = "SELECT * FROM capa_becarios_actividades WHERE IDempleado = $IDempleado AND anio = $anio AND IDmes = $el_mes";
							$actividades = mysql_query($query_actividades, $vacantes) or die(mysql_error());
							$row_actividades = mysql_fetch_assoc($actividades);
							$totalRows_actividades = mysql_num_rows($actividades); 

							?>
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
											<a  class="text text-semibold text-primary" href="capa_becarios_edit.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>"><?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?> <?php if ($row_becarios['correo'] == '') { echo "<i class='icon-user-cancel text-warning'></i>";} ?></a>
											<?php } else { ?>
											<a  class="text text-semibold text-danger" href="capa_becarios_edit.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>"><?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?></a>
											<?php } ?>
											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becarios['tipo']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becarios['fecha_alta'])); ?></li>
												<li><strong>Fecha baja:</strong> <?php if ($row_becarios['fecha_baja'] != '') {echo date('d/m/Y', strtotime($row_becarios['fecha_baja']));} else { echo "No aplica";} ?></li>
												<li><strong>Fecha nacimiento:</strong> <?php if ($row_becarios['fecha_nacimiento'] != '') {echo date('d/m/Y', strtotime($row_becarios['fecha_nacimiento']));} else { echo "-";} ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Sucursal:</strong> <?php echo $row_becarios['matriz']; ?></li>
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
							<a href="capa_becarios_edit.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>" class="btn btn-xs bg-primary-400 btn-block"><i class="icon-file-eye2 position-left"></i>Editar datos</a>
							
							<?php if ($totalRows_results > 0) { ?>
							<a href="capa_becarios_evaluar.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>&IDmes=<?php echo $el_mes; ?>&anio=<?php echo $anio; ?>" class="btn btn-xs bg-success-400 btn-block"><i class="icon-checkbox-checked position-left"></i>Evaluado</a>
							<?php } else { ?>
							<a href="capa_becarios_evaluar.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>&IDmes=<?php echo $el_mes; ?>&anio=<?php echo $anio; ?>" class="btn btn-xs bg-success-400 btn-block"><i class="icon-checkbox-unchecked position-left"></i>Sin evaluacion</a>
							<?php }  ?>
							<?php if ($totalRows_evaluaciones > 0) { ?>
							<a href="capa_becarios_historico.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>" class="btn bg-info-400 btn-block"><i class="icon-list3 position-left"></i></i>Evaluaciones</a>
							<?php }  ?>
							<a href="capa_becarios_actividades.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>" class="btn bg-teal-400 btn-block"><i class="icon-list position-left"></i></i>Actividades (<?php echo $totalRows_actividades; ?>)</a>
							<button type="button" data-target="#modal_theme_danger<?php echo $row_becarios['ELempleado']; ?>"  data-toggle="modal" class="btn btn-xs bg-danger-400 btn-block"><i class="icon-users2 position-left"></i>Estatus</button>
							<button type="button" data-target="#modal_theme_password<?php echo $row_becarios['ELempleado']; ?>"  data-toggle="modal" class="btn btn-xs bg-warning btn-block"><i class="icon-key position-left"></i>Restablecer</button>
							</div>
							</td>
							</tr>
							
							
									<!-- danger modal -->
									<div id="modal_theme_danger<?php echo $row_becarios['ELempleado']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cambiar estatus</h6>
												</div>
												<div class="modal-body">
																			
														<form action="capa_becarios_activos.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														 
														 Los Becarios con estatus "Inactivo" se ocultan del listado.
														<p>&nbsp;</p>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Estatus:</label>
															  <div class="col-lg-9">
															<select name="activo" id="activo" class="form-control" >
																<option value="1"<?php if (!(strcmp($row_becarios['activo'], 1))) {echo "selected=\"selected\"";} ?>>Activo</option>
																<option value="0"<?php if (!(strcmp($row_becarios['activo'], 0))) {echo "selected=\"selected\"";} ?>>Inactivo</option>
															</select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-danger">Actualizar</button> 
													<input type="hidden" name="MM_insert" value="form1" />
													<input type="hidden" name="IDempleado" value="<?php echo $row_becarios['ELempleado']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->

									<!-- danger modal -->
									<div id="modal_theme_password<?php echo $row_becarios['ELempleado']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-warning">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Restablecer Password</h6>
												</div>
												<div class="modal-body">
														 
														Se restablecerá el Password de <?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?>.
														<p>&nbsp;</p>
												</div>
												<div class="modal-footer">
														<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
														<a class="btn btn-warning" href="capa_becarios_activos.php?IDempleado=<?php echo $row_becarios['ELempleado']; ?>&restablecer=1">Si restablecer</a>
												</div>

											</div>
										</div>
									</div>
									<!-- danger modal -->
							
							
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