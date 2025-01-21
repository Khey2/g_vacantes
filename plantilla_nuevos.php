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
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


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
$IDusuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_usuarios = "SELECT * FROM vac_usuarios WHERE IDmatriz = '$IDmatriz' AND ( contratos > 0)";
$usuarios = mysql_query($query_usuarios, $vacantes) or die(mysql_error());
$row_usuarios = mysql_fetch_assoc($usuarios);
$totalRows_usuarios = mysql_num_rows($usuarios);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

	 if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 1) {	$_SESSION['el_estatus'] = 1; $_SESSION['filtro1'] = ' AND prod_activosfaltas.fecha_alta BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW() '; $_SESSION['filtr'] = 1;} 
else if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 2) {	$_SESSION['el_estatus'] = 2; $_SESSION['filtro1'] = ' AND prod_activosfaltas.fecha_alta BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() '; $_SESSION['filtr']= 2;}
else if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 0) {	$_SESSION['el_estatus'] = 0; $_SESSION['filtro1'] = ' AND prod_activosfaltas.fecha_alta < DATE_SUB(NOW(), INTERVAL 60 DAY) AND reclu_exp_sahuayo.IDpaso = 1'; $_SESSION['filtr'] = 0;}
else  {	$_SESSION['el_estatus'] = 2; $_SESSION['filtro1'] = ' AND prod_activosfaltas.fecha_alta BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND NOW() '; $_SESSION['filtr'] = 2;}


	 if (isset($_POST['estatus']) && $_POST['estatus'] == 1) {	$_SESSION['estatus'] = 1; $_SESSION['estatusf'] = ' AND prod_activosfaltas.estatus IN (1,3,4) ';} 
else if (isset($_POST['estatus']) && $_POST['estatus'] == 2) {	$_SESSION['estatus'] = 2; $_SESSION['estatusf'] = ' AND prod_activosfaltas.estatus = 2 ';}
else if (isset($_POST['estatus']) && $_POST['estatus'] == 3) {	$_SESSION['estatus'] = 3; $_SESSION['estatusf'] = ' AND prod_activosfaltas.estatus IN (1,2,3,4) ';}
													   else  {  $_SESSION['estatus'] = 3; $_SESSION['estatusf'] = ' AND prod_activosfaltas.estatus IN (1,2,3,4) ';}


$el_estatus = $_SESSION['el_estatus'];
$filtro1 =  $_SESSION['filtro1'];
$filtr = $_SESSION['filtr']; 
$estatus = $_SESSION['estatus']; 
$estatusf = $_SESSION['estatusf']; 

if ($row_usuario['contratos'] == 2)  { $filtro2 = "";} else { $filtro2 = " AND prod_activosj.IDusuario_segimiento = '$IDusuario' ";}

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT DISTINCT prod_activosfaltas.*, vac_areas.area, prod_activosj.IDusuario_segimiento FROM prod_activosfaltas LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activosfaltas.IDarea LEFT JOIN reclu_exp_sahuayo ON prod_activosfaltas.IDempleado = reclu_exp_sahuayo.IDempleado LEFT JOIN prod_activosj ON prod_activosfaltas.IDempleado = prod_activosj.IDempleado WHERE prod_activosfaltas.IDmatriz =  $la_matriz  ".$filtro1.$filtro2.$estatusf." ORDER BY prod_activosfaltas.fecha_alta DESC";
mysql_query("SET NAMES 'utf8'"); 
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$IDempleado = $_POST['IDempleado'];	
$IDpaso  = $_POST['IDpaso'];	
$insertSQL = sprintf("INSERT INTO reclu_exp_sahuayo (IDempleado, IDpaso, preg1, preg2, preg3, preg4, observaciones, IDusuario) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($_POST['IDempleado'], "int"),
						GetSQLValueString($_POST['IDpaso'], "int"),
						GetSQLValueString($_POST['preg1'], "int"),
						GetSQLValueString($_POST['preg2'], "int"),
						GetSQLValueString($_POST['preg3'], "int"),
						GetSQLValueString($_POST['preg4'], "int"),
						GetSQLValueString($_POST['observaciones'], "text"),
						GetSQLValueString($IDusuario, "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

$last_id =  mysql_insert_id();
header('Location: plantilla_nuevos.php?info=1');
}
	
	
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$IDempleado = $_POST['IDempleado'];	
$IDpaso  = $_POST['IDpaso'];	
$updateSQL = sprintf("UPDATE reclu_exp_sahuayo SET preg1=%s, preg2=%s, preg3=%s, preg4=%s, observaciones=%s, IDusuario=%s WHERE IDempleado = $IDempleado AND IDpaso= $IDpaso",
						GetSQLValueString($_POST['preg1'], "int"),
						GetSQLValueString($_POST['preg2'], "int"),
						GetSQLValueString($_POST['preg3'], "int"),
						GetSQLValueString($_POST['preg4'], "int"),
						GetSQLValueString($_POST['observaciones'], "text"),
						GetSQLValueString($IDusuario, "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

$last_id =  mysql_insert_id();
header('Location: plantilla_nuevos.php?info=2');
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {

	$IDusuario = $_POST['IDusuario'];	
	$IDempleado  = $_POST['IDempleado'];	

	$query_consulta1 = "SELECT * FROM prod_activosj WHERE IDempleado = '$IDempleado'";
	$consulta1 = mysql_query($query_consulta1, $vacantes) or die(mysql_error());
	$row_consulta1 = mysql_fetch_assoc($consulta1);
	$totalRowsconsulta1 = mysql_num_rows($consulta1);
	
if ($totalRowsconsulta1 > 0){ 
	$deleteSQL = "UPDATE prod_activosj SET IDusuario_segimiento = '$IDusuario' WHERE IDempleado ='$IDempleado'";
	mysql_select_db($database_vacantes, $vacantes);
	$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
	header("Location: plantilla_nuevos.php?info=3");
} else {
	$deleteSQL = "INSERT INTO  prod_activosj (IDusuario_segimiento, IDempleado) VALUES ('$IDusuario', '$IDempleado')";
	mysql_select_db($database_vacantes, $vacantes);
	$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
	header("Location: plantilla_nuevos.php?info=3");
}
}
	
	
	
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
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_inputs.js"></script>
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
				
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 1) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 2) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se actualizó correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
					
                        <!-- Basic alert -->
                        <?php if(isset($_GET['info']) and $_GET['info'] == 3) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente el usuario para seguimiento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Seguimiento Nuevos Ingresos</h6>
								</div>

								<div class="panel-body">
								<p>
								<b>Instrucciones:</b><br/>
								<ul>
									<li>En esta sección se reportan las entrevistas de corto plazo para empleados de nuevo ingreso.</li>
									<li>Se pueden reportar hasta 5 entrevistas: al tercer día, la primera semana, primer quincena, primer mes y primer bimestre del ingreso.</li>
									<li>Da clic en seguimiento y selecciona el periodo a capturar.</li>
									<li>En el botón asignar, se puede asignar el seguimiento a un colaborador del área de Recursos Humanos en específico.</li>
									<li>Puedes descargar un reporte detallado dando clic en el botón de "Descargar Reporte".</li>
									<li>Utiliza los filtros para mostrar activos, bajas y periodos; en la opción Anteriores, solo se muestran empleados que tienen información capturada.</li>
								</ul>
								</p>


								<p>&nbsp; </p>
                            

						<form method="POST" action="plantilla_nuevos.php">
							  <table class="table">
								  <tbody>							  
									<tr>
									<td>
										<div class="col-md-9">Antiguedad:
												<select class="form-control"  name="el_estatus">
												<option value="1" <?php if ($el_estatus == 1) {echo "selected=\"selected\"";} ?>>30 días</option>
												<option value="2" <?php if ($el_estatus == 2) {echo "selected=\"selected\"";} ?>>60 días</option>
												<option value="0" <?php if ($el_estatus == 0) {echo "selected=\"selected\"";} ?>>Anteriores</option>
												</select>
										</div>
									</td>
									<td>
										<div class="col-md-9">Estatus:
												<select class="form-control"  name="estatus">
												<option value="3" <?php if ($estatus == 3) {echo "selected=\"selected\"";} ?>>Todos</option>
												<option value="2" <?php if ($estatus == 2) {echo "selected=\"selected\"";} ?>>Bajas</option>
												<option value="1" <?php if ($estatus == 1) {echo "selected=\"selected\"";} ?>>Activos</option>
												</select>
										</div>
									</td>
									<td>
										<button type="submit" class="btn btn-primary">Filtrar</button>
										<a class="btn btn-info" href="reporte_rys.php">Descargar Reporte</a>
									</td>	
									</tr>
								  </tbody>
							  </table>
					  	</form		  


								<div class="table-responsive">
								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-danger"> 
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Fecha Alta</th>
                                    <th>Fecha Baja</th>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                    <th>#Capt</th>
                                    <?php if ($row_usuario['contratos'] == 2) { ?><th>Asignar</th><?php } ?>
                                    <th>Seguimiento</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_autorizados> 0) { do { 
									$IDempleado = $row_autorizados['IDempleado'];
									$query_capturas = "SELECT * FROM reclu_exp_sahuayo WHERE IDempleado = $IDempleado";
									$capturas = mysql_query($query_capturas, $vacantes) or die(mysql_error());
									$row_capturas = mysql_fetch_assoc($capturas);
									$totalRows_capturas = mysql_num_rows($capturas);
									?>
                                    <tr>
                                      <td><?php echo $row_autorizados['IDempleado']; ?></td>
                                      <td><?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?></td>
                                      <td><?php echo date('d/m/Y', strtotime($row_autorizados['fecha_alta'])); ?></td>
                                      <td><?php if($row_autorizados['fecha_baja'] != '0000-00-00') {echo date('d/m/Y', strtotime($row_autorizados['fecha_baja'])); } else { echo "-"; }?></td>
                                      <td><?php echo $row_autorizados['area']; ?></td>
                                      <td><?php echo $row_autorizados['denominacion']; ?></td>
                                      <td><?php echo $totalRows_capturas; ?></td>
									  <?php if ($row_usuario['contratos'] == 2) {
										$query_asignado = "SELECT prod_activosj.IDusuario_segimiento, prod_activosj.IDempleado, vac_usuarios.usuario_correo, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno FROM prod_activosj LEFT JOIN vac_usuarios ON prod_activosj.IDusuario_segimiento = vac_usuarios.IDusuario WHERE prod_activosj.IDempleado = $IDempleado";
										$asignado = mysql_query($query_asignado, $vacantes) or die(mysql_error());
										$row_asignado = mysql_fetch_assoc($asignado);
										$totalRows_asignado = mysql_num_rows($asignado);
										?>
									  <td> 
										<?php if ($row_asignado['IDusuario_segimiento'] == '' OR $row_asignado['IDusuario_segimiento'] == 0) { ?>
											<a data-target="#modal_theme_danger<?php echo $row_autorizados['IDempleado']; ?>" data-toggle="modal" class="text-danger"><i class="icon-pencil7"></i></a>
									  <?php } else { ?> 
										<a data-target="#modal_theme_danger<?php echo $row_autorizados['IDempleado']; ?>" data-toggle="modal" class="text-success"><i class="icon-pencil7"></i></a>
										<?php } ?> 
									  </td>
									  <?php } ?>
									  <td class="text-center">
										<select  class="form-control" onchange="loadDynamicContentModal('<?php echo $row_autorizados['IDempleado']; ?>', this.value)">
											<option ...>Periodo</option>
											<option  value="1">03 días</option>
											<option  value="2">07 días</option>
											<option  value="3">15 días</option>
											<option  value="4">30 días</option>
											<option  value="5">60 días</option>
										</select>
									  </td>
                                    </tr>


									 <!-- danger modal -->
									 <div id="modal_theme_danger<?php echo $row_autorizados['IDempleado']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Asignar Responsable</h6>
												</div>
												<div class="modal-body">
																			
														<form action="plantilla_nuevos.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														 
														<p>Seleccione el responsable del seguimiento, puede ser diferente al responsable de la contratación:&nbsp;</p>
													 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-4">Responsable:</label>
															  <div class="col-lg-8">
															  	<select name="IDusuario" id="IDusuario" class="form-control" required="required">
																  <option value="">Seleccione una opción</option> 
																  <option value="0" <?php if ($row_autorizados['IDusuario_segimiento'] == 0) {echo "SELECTED";} ?>>No asignar</option> 
																	<?php do {  ?>
																	<option value="<?php echo $row_usuarios['IDusuario']?>"<?php if (!(strcmp($row_usuarios['IDusuario'], $row_autorizados['IDusuario_segimiento']))) {echo "SELECTED";} ?>><?php echo $row_usuarios['usuario_nombre']." ".$row_usuarios['usuario_parterno']." ".$row_usuarios['usuario_materno']?></option>
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
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-danger">Asignar</button> 
													<input type="hidden" name="MM_insert" value="form2" />
													<input type="hidden" name="IDempleado" value="<?php echo $row_autorizados['IDempleado']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->




                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados)); ?>
									<?php } else { ?>
										<tr>
                                      <td colspan="6">No se encontraron registros.</td>
                                    </tr>
									<?php } ?>
                                  </tbody>
                                </table>
								</div>

							</div>
							</div>
						</div>
                                    
					<!-- /Contenido -->






					<!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h5 class="modal-title">Experiencia Sahuayo</h5>
								</div>
								   <div id="conte-modal">
								   </div>
							</div>
						</div>
					<!-- /inline form modal -->
					</div>


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
<script>
function loadDynamicContentModal(modal, value){
	var options = { modal: true };
	$('#conte-modal').load('plantilla_nuevos_md.php?IDempleado='+ modal + '&IDpaso='+ value, function() {
		$('#bootstrap-modal').modal({show:true});
  });  
}
</script> 
