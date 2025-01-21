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
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$mi_fecha =  date('Y/m/d');


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
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$IDmes = $el_mes = date("m") - 1;
set_time_limit(0);


// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];

if (isset($_POST['la_matriz'])) {  $_SESSION['la_matriz'] = $_POST['la_matriz']; } 
elseif (!isset($_SESSION['la_matriz'])) {  $_SESSION['la_matriz'] =  $IDmatriz; } 
$la_matriz = $_SESSION['la_matriz'];


if (isset($_POST['buscado'])) {	
$arreglo = '';
$array = explode(" ", $_POST['buscado']);
$contar = substr_count($_POST['buscado'], ' ') + 1;
$i = 0;
while($contar > $i) {
$arreglo .= " AND (casos_sindicato.asunto LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR casos_sindicato.descripcion_cierre LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR casos_sindicato.descripcion LIKE '%" . $array[$i] . "%' )"; 
    $i++; } }
	
if (!isset($_POST['buscado'])) { $filtroBuscado = ''; }  else { $filtroBuscado = $arreglo; $IDvisible = 1;}

if ($row_usuario['IDusuario_puesto'] == 511) { $filtroBuscado2 = ' AND IDsindicable = 1 '; }  else { $filtroBuscado2 = '';}


mysql_select_db($database_vacantes, $vacantes);
$query_casos = "SELECT * FROM casos_sindicato WHERE IDestatus IN (1,2) AND IDmatriz = $la_matriz".$filtroBuscado2.$filtroBuscado;
mysql_query("SET NAMES 'utf8'");
$casos = mysql_query($query_casos, $vacantes) or die(mysql_error());
$row_casos = mysql_fetch_assoc($casos);
$totalRows_casos = mysql_num_rows($casos);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 12";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDsindicato_n = $_POST["IDsindicato"]; 
	$descripcion_cierre = $_POST["descripcion_cierre"]; 
	
	if($_POST['fecha_fin'] == ''){$fecha1 = date("Y-m-d");} else { $fecha1a = $_POST['fecha_fin']; 	$fecha1b = explode("-",$fecha1a); $fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0]; }
	echo "fecha ".$_POST['fecha_fin'];
	
	$query1 = "UPDATE casos_sindicato SET IDestatus = 2, descripcion_cierre = '$descripcion_cierre', fecha_fin = '$fecha1' WHERE IDsindicato = '$IDsindicato_n'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	
	//redirecto
	header("Location: casos_sindicato_cierre.php?IDsindicato=$IDsindicato_n"); 	
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>  
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5Sin.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<!-- /theme JS files -->
<script>
// EVITAR REENVIO DE DATOS.
    if (window.history.replaceState) { // verificamos disponibilidad
    window.history.replaceState(null, null, window.location.href);
}
</script>
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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
                        <?php if(isset($_GET['info']) && $_GET['info'] == 3) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cambiado el estatus de forma correcta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 99) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cerrado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 88) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha reactivado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 4) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 11))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el seguimiento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 12))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el seguimiento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 13) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el seguimiento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Atención de Temas Sindicales</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group"><strong>Instrucciones:</strong></br>
                                    1. Utiliza el filtro para identificar los casos por Sucursal.</br>
                                    2. Dando clic en <i>"Seguimiento(s)"</i> puedes ver el listado de reportes de avance:</br>
									&nbsp;&nbsp;&nbsp;&nbsp;2.1 Da clic en cada fecha para ver el detalle del seguimiento.<br />
									&nbsp;&nbsp;&nbsp;&nbsp;2.2 Da clic en <i>"Agregar"</i> para crear un nuevo seguimiento.</br>
                                    3. Dando clic en el botón <i>"Descripción"</i> puedes ver la información detallada del caso.</br>
                                    4. Da clic en <i>"Agregar"</i> o <i>"Editar"</i> casos, segín sea necesario.</br>
                                    5. Utiliza el <i>"Filtro"</i> para buscar casos según su nombre o descripción detallada.</br>
                                    6. En la sección de Editar, puedes cambiar el estatus a Atendido.</br>
                                    7. El icono <i style="font-size:10px;" class='icon-warning text-danger'></i> selaña los casos que han vencido, según la fecha de atención esperada.</br>
									</p>


                    <!-- Search field -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Buscador</h5>
						</div>

						<div class="panel-body">
							<form action="casos_sindicato.php" method="post" class="main-search">
								<div class="input-group content-group">
									<div class="has-feedback has-feedback-left">
										<input type="text" class="form-control input-xlg" name="buscado" required id="buscado" value="" placeholder="<?php 
										if (isset($_POST['buscado'])) {echo $_POST['buscado']; } else {echo "Ingresa algun dato a buscar..."; } ?>">
										<div class="form-control-feedback">
											<i class="icon-search4 text-muted text-size-base"></i>
										</div>
									</div>

									<div class="input-group-btn">
										<button type="submit" class="btn btn-primary btn-xlg">Buscar</button>
									</div>
								</div>
                                
                               <?php if (isset($_POST['buscado']) && $totalRows_casos  > 0) { ?> 
							   
    							<ul class="list-inline list-inline-condensed no-margin-bottom">
								<li><a href="#" class="btn btn-default"><i class="icon-filter4"></i><strong>  <?php echo $totalRows_casos; ?></strong> Documentos encontrados.</a></li>
								<li><a href="casos_sindicato.php" class="btn btn-danger btn-xs">Borrar Filtro</a></li>
								</ul>


							   <?php } else if (isset($_POST['buscado']) && $totalRows_casos == 0) {  ?> 
							   <div class="alert alert-warning no-border">
										<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
										<span class="text-semibold">Ups!</span> No se encontraron documentos con el filtro seleccionado.
								    </div>
									<?php } ?>

							</form>
							<ul class="list-inline list-inline-condensed no-margin-bottom">
							<?php if ($totalRows_direcciona > 0 OR $totalRows_areaa > 0 OR $totalRows_subareaa > 0) { ?>
								<li><a href="#" class="btn btn-default"><i class="icon-filter4"></i><strong> Filtro Actual:</strong> <?php echo $totalRows_casos; ?> Documentos </a></li>
							<?php } ?>
							
							</ul>
							
						</div>
						</div>
					<!-- /search field -->

					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
							  <th>No. Caso</th>
							  <th class="text text-center">Asunto General</th>
							  <th># Responsables</th>
							  <th>Fecha inicio</th>
							  <th>Fecha solicitada</th>
							  <th class="text text-center">Estatus</th>
							  <th class="text text-center">Seguimiento(s)</th>
							  <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_casos > 0) { ?>

                        <?php do { 

						$IDsindicato = $row_casos['IDsindicato'];
						mysql_select_db($database_vacantes, $vacantes);
						$query_casos_seguimientos = "SELECT * FROM casos_sindicato_seguimientos WHERE IDsindicato = $IDsindicato AND IDestatus_seguimiento = 1";
						$casos_seguimientos = mysql_query($query_casos_seguimientos, $vacantes) or die(mysql_error());
						$row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos);
						$totalRows_casos_seguimientos = mysql_num_rows($casos_seguimientos);

						mysql_select_db($database_vacantes, $vacantes);
						$query_casos_responsables = "SELECT vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, casos_responsables.IDtipo, casos_responsables.IDusuario, vac_puestos.denominacion FROM casos_responsables LEFT JOIN vac_usuarios ON casos_responsables.IDusuario = vac_usuarios.IDusuario LEFT JOIN vac_puestos ON vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE IDsindicato = $IDsindicato";					$casos_responsables = mysql_query($query_casos_responsables, $vacantes) or die(mysql_error());
						$row_casos_responsables = mysql_fetch_assoc($casos_responsables);
						$totalRows_casos_responsables = mysql_num_rows($casos_responsables);

						?>

                          <tr>
                            <td><?php echo $row_casos['IDsindicato']; ?></td>
                            <td><?php echo $row_casos['asunto']; ?></td>
                            <td><?php if ($totalRows_casos_responsables > 0) { echo $totalRows_casos_responsables;} else { echo "<span class='text text-semibold text-danger'>Asignar</span>"; } ?></td>
                            <td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_inicio']))?></td>
                            <td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_esperada']))?>
							
							
							
							<?php if (strtotime($mi_fecha) > strtotime($row_casos['fecha_esperada']) AND $row_casos['IDestatus'] != 3) echo "<i style='font-size:10px;' class='icon-warning text-danger'></i>"; ?>
							
							
							</td>
                            <td><?php if ($row_casos['IDestatus'] == 1) { echo "En proceso"; } 
								 else if ($row_casos['IDestatus'] == 2) { echo "Atendido"; } 
								 else { echo "Sin Estatus"; } ?></td>
							<td>
							<a class="collapsed text-info text-semibold" data-toggle="collapse" href="#collapse-group<?php echo $row_casos['IDsindicato']; ?>E1"><?php echo $totalRows_casos_seguimientos; ?> seguimiento(s)<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_casos['IDsindicato']; ?>E1" class="panel-collapse collapse">
								<ul class="list list-icons">
							<?php if ($totalRows_casos_seguimientos > 0) { ?>
							<?php do { ?>
								<li><i class="icon-files-empty2 text-info position-left"></i><a class="text-info" href="casos_sindicato_seguimientos.php?IDsindicato=<?php echo $row_casos['IDsindicato'];?>&IDsindicato_seguimientos=<?php echo $row_casos_seguimientos['IDsindicato_seguimientos']; ?>"><?php echo date( 'd/m/Y', strtotime($row_casos_seguimientos['fecha_reporte'])); ?></a></li>
							<?php } while ($row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos)); ?>
							<?php } ?>
								<li><i class="icon-file-plus2 text-info position-left"></i><a class="text-info" href="casos_sindicato_seguimientos.php?IDsindicato=<?php echo $row_casos['IDsindicato']; ?>">Agregar</a></li>
								</ul>
							</div>
							</td>
							 <td>
							 <button type="button" data-target="#modal_theme_danger2<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-primary">Descripción</button>
							 <button type="button" data-target="#modal_theme_danger4<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-info">Responsables</button>						
							<?php if ($row_usuario['user_casos_sindicato'] == 2 OR $row_usuario['IDusuario'] == $row_casos['IDusuario']) { ?>
							<a href="casos_sindicato_edit.php?IDsindicato=<?php echo $row_casos['IDsindicato']; ?>" class="btn btn-success">Editar</a>	


							<?php if ($totalRows_casos_responsables > 0 OR $row_casos['IDestatus'] == 2) { ?>
							<button type="button" data-target="#modal_theme_danger3<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-warning">Cerrar</button>
							<?php } ?>
							<?php } ?>
							</td>
                           </tr>

									<!-- danger modal -->
									<div id="modal_theme_danger2<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
												</div>
												<div class="modal-body">
												<p><strong>Descripción del Caso:</strong><br />
												<?php echo $row_casos['descripcion']; ?><br /></p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-success" href="casos_sindicato_edit.php?IDsindicato=<?php echo $row_casos['IDsindicato']; ?>">Editar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->
						   
									<!-- danger modal -->
									<div id="modal_theme_danger4<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-info">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Responsables</h6>
												</div>
												<div class="modal-body">
												
												<?php if ($totalRows_casos_responsables > 0) {  ?>
												<?php do {  ?>

												<?php echo $row_casos_responsables['usuario_nombre']." ".$row_casos_responsables['usuario_parterno']." (".$row_casos_responsables['denominacion'].")";  ?><br /> 
												
												<?php } while ($row_casos_responsables = mysql_fetch_assoc($casos_responsables)); ?>
												<?php }  ?>
												
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-success" href="casos_sindicato_responsables.php?IDsindicato=<?php echo $row_casos['IDsindicato']; ?>">Editar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->


						   

									<!-- danger modal -->
									<div id="modal_theme_danger3<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-warning">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cierre de Caso</h6>
												</div>
												<div class="modal-body">
											
												
												<form action="casos_sindicato.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">

												<!-- Fecha -->
												<div class="form-group">
													<label class="control-label col-lg-3">Asunto:</label>
												<div class="col-lg-9">
												<div class="input-group">
													<?php echo $row_casos['asunto']; ?><p>&nbsp;</p>
												</div>
											   </div>
											  </div> 
												<!-- Fecha -->
 
												<!-- Fecha -->
												<div class="form-group">
													<label class="control-label col-lg-3">Fecha de cierre:<span class="text-danger">*</span></label>
												<div class="col-lg-9">
												<div class="input-group">
												<span class="input-group-addon"><i class="icon-calendar22"></i></span>
													<input type="text" required="required" class="form-control pickadate-format" placeholder="Seleccione la fecha" name="fecha_fin" id="fecha_fin" value="<?php if ($row_casos['fecha_fin'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_casos['fecha_fin'])); }?>">
												</div>
											   </div>
											  </div> 
												<!-- Fecha -->

												<p>&nbsp;</p>

												<!-- Basic text input -->
											  <div class="form-group">
													<label class="control-label col-lg-3">Detalle del cierre:</label>
													<div class="col-lg-9">
													  <textarea name="descripcion_cierre" required="required" rows="3" class="form-control" id="descripcion_cierre" placeholder="Indique los acuerdos y soluciones obtenidas en el caso."><?php echo $row_casos['descripcion_cierre']; ?></textarea>
													</div>
												</div>
												<!-- /basic text input -->



														<div>
														</div>
												<p>&nbsp;</p>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-warning">Cerrar Caso</button> 
													<input type="hidden" name="MM_insert" value="form1" />
													<input type="hidden" name="IDsindicato" value="<?php echo $row_casos['IDsindicato']; ?>" />
												</div>
												
												</form>
												
											</div>
										</div>
									</div>
									<!-- danger modal -->
						   

                          <?php } while ($row_casos = mysql_fetch_assoc($casos)); ?>
                         <?php } else { ?>
							<tr><td colspan="8">Sin casos con el filtro seleccionado. <a href="casos_sindicato_edit.php" class="btn btn-success btn-xs">Agregar Nuevo</a></td></tr>
                         <?php } ?>
					    </tbody>
				    </table>
				</div>                   
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