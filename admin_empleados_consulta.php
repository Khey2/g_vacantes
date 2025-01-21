<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if(isset($_POST['el_estatus'])) {$_SESSION['el_estatus'] = $_POST['el_estatus']; } 
if(!isset($_SESSION['el_estatus'])) { $_SESSION['el_estatus'] = 1;}

if (isset($_POST['la_empresa'])) {	foreach ($_POST['la_empresa'] as $empres)
	{	$_SESSION['la_empresa'] = implode(",", $_POST['la_empresa']);}	}  else { $_SESSION['la_empresa'] = $IDmatrizes;}


$la_empresa = $_SESSION['la_empresa'];
$el_estatus = $_SESSION['el_estatus'];

$query_contratos = "SELECT con_empleados.IDempleado, con_empleados.IDestatus, con_empleados.a_rfc, con_empleados.IDpuesto, con_empleados.a_cuenta_bancaria, con_empleados.escolaridad, con_empleados.a_correo, con_empleados.fecha_alta, con_empleados.a_paterno, con_empleados.a_curp, con_empleados.a_imss, con_empleados.d_codigo_postal, con_empleados.d_calle, con_empleados.a_materno, con_empleados.a_nombre, con_empleados.b_sueldo_mensual, con_empleados.b_sueldo_diario, con_empleados.a_rfc, con_empleados.estatus, con_empleados.IDmatriz, vac_matriz.IDmatriz, vac_matriz.matriz, vac_empresas.empresa, vac_puestos.denominacion, vac_puestos.IDarea_contratos As ElArea FROM con_empleados left JOIN vac_matriz ON vac_matriz.IDmatriz = con_empleados.IDmatriz LEFT JOIN vac_empresas ON con_empleados.IDempresa = vac_empresas.IDempresa LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDmatriz IN ($la_empresa)";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$elarea = $row_contratos['ElArea'];

$query_empresas = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)";
$empresas = mysql_query($query_empresas, $vacantes) or die(mysql_error());
$row_empresas = mysql_fetch_assoc($empresas);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDempleado_n = $_POST["IDempleado"]; 
	$IDestatus_p = $_POST["IDestatus"]; 
	$query1 = "UPDATE con_empleados SET IDestatus = '$IDestatus_p' WHERE con_empleados.IDempleado = '$IDempleado_n'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: admin_empleados_consulta.php?info=3"); 	
}

if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
	$IDempleado_n = $_GET["IDempleado"]; 
	$query1 = "UPDATE con_empleados SET IDestatus = 2 WHERE IDempleado = '$IDempleado_n'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: admin_empleados_consulta.php?info=5"); 	
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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /theme JS files -->
<script>
$(document).on('shown.bs.dropdown', '.table-responsive', function (e) {
    // The .dropdown container
    var $container = $(e.target);

    // Find the actual .dropdown-menu
    var $dropdown = $container.find('.dropdown-menu');
    if ($dropdown.length) {
        // Save a reference to it, so we can find it after we've attached it to the body
        $container.data('dropdown-menu', $dropdown);
    } else {
        $dropdown = $container.data('dropdown-menu');
    }

    $dropdown.css('top', ($container.offset().top + $container.outerHeight()) + 'px');
    $dropdown.css('left', $container.offset().left + 'px');
    $dropdown.css('position', 'absolute');
    $dropdown.css('display', 'block');
    $dropdown.appendTo('body');
});

$(document).on('hide.bs.dropdown', '.table-responsive', function (e) {
    // Hide the dropdown menu bound to this button
    $(e.target).data('dropdown-menu').css('display', 'none');
});</script>

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
			<!-- Content area -->
				<div class="content">


	                <!-- Content area -->
				<div class="content">
               
               
                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-warning-400 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Empleado. Revisa las demás secciónes.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 6))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han importado correctamente los Empleados.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El empleado ya existe, por favor validar No. de Empleado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se actualizó correctamente el Estatus.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 5))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se borró correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
					    
					    
					    <!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Candidatos para Contratación</h5>
						</div>

					<div class="panel-body">
					  <p>A continuación se muestran los empleados registrados en el sistema para la Matriz de <?php echo $row_matriz['matriz']; ?>.<br/>
					  <p class="text text-semibold">Instrucciones:
					  <ul>
					  <li>La información del candidato está dividida en cuatro secciones. Accede y captura la información solicitada en cada sección.</li>
					  <li>Todas las secciones tienen campos obligatorios <span class="text-danger">*</span> que son necesarios para poder imprimir los documentos.</li>
					  <li>Una vez capturados todos los campos obligatorios, aparecerá el botón para imprimir los documentos.</li>
					  <li>Puedes cambiar el estatus para que solo se muestren los candidatos en proceso y omitir los que ya están contratados.</li>
					  <li>Los contratos se deben imprimir y firmar por duplicado.</li>
					  <li>Si el C.P. no aparece en el listado, solicita que se agregue a Desarrollo Organizacional.</li>
					  <li><strong>Si los datos de un candidato coincide con una empleado activo, baja u otro candidato, aparecerá el icono <i class="icon-warning text-danger"></i>.</strong></li>
					  </p>
					  </ul>
                     <p>&nbsp;</p>

                    <form method="POST" action="admin_empleados_consulta.php">
					<table class="table">
						<tbody>							  
							<tr>
								<td>
								<div class="col-lg-9">
								 <select class="multiselect" multiple="multiple" name="la_empresa[]">
								<?php do { ?>
								   <option value="<?php echo $row_empresas['IDmatriz']?>"<?php if (!(strcmp($row_empresas['IDmatriz'], $la_empresa))) {echo "selected=\"selected\"";} ?>><?php echo $row_empresas['matriz']?></option>
								   <?php
								  } while ($row_empresas = mysql_fetch_assoc($empresas));
								  $rows = mysql_num_rows($empresas);
								  if($rows > 0) {
									  mysql_data_seek($empresas, 0);
									  $row_empresas = mysql_fetch_assoc($empresas);
								  } ?>
								  </select>
								</div>
								</td>
							<td>
							<div class="col-lg-4">
							 <select class="form-control"  name="el_estatus">
							   <option value="1"<?php if (!(strcmp(1, $el_estatus))) {echo "selected=\"selected\"";} ?>>EN PROCESO</option>
							   <option value="0"<?php if (!(strcmp(0, $el_estatus))) {echo "selected=\"selected\"";} ?>>CONTRATADOS</option>
							</select>
						    </div>
							</td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                            </td>
					      </tr>
					    </tbody>
				    </table>
				</form>

                      
					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead> 
                    <tr class="bg-success"> 
                      <th>Acciones</th>
                      <th>Estatus</th>
                      <th>Fecha Alta</th>
                      <th>Nombre</th>
                      <th>Puesto</th>
               		 </tr>
                    </thead>
                    <tbody>
                        <?php if($totalRows_contratos >0){ ?>
                        <?php do { 
						$el_nombre = $row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre'];
						$el_empleado = $row_contratos['IDempleado'];
						$el_rfc = $row_contratos['a_rfc'];
						$el_imss = $row_contratos['a_imss'];
						$el_curp = $row_contratos['a_curp'];
						$el_domicilio = $row_contratos['d_calle'].$row_contratos['d_codigo_postal'];
						$el_correo = $row_contratos['a_correo'];

						$query_consulta1 = "SELECT sum(con_dependientes.observaciones) AS Total FROM con_dependientes WHERE IDempleado = $el_empleado AND emergencias IN (1,3)";
						$consulta1 = mysql_query($query_consulta1, $vacantes) or die(mysql_error());
						$row_consulta1 = mysql_fetch_assoc($consulta1);
						$totalRows_consulta1 = mysql_num_rows($consulta1);
						$total_b = $row_consulta1['Total'];

						$query_consulta2 = "SELECT * FROM con_dependientes WHERE IDempleado = $el_empleado AND emergencias IN (2,3)";
						$consulta2 = mysql_query($query_consulta2, $vacantes) or die(mysql_error());
						$row_consulta2 = mysql_fetch_assoc($consulta2);
						$totalRows_consulta2 = mysql_num_rows($consulta2);
						$total_c = $totalRows_consulta2;

						$query_consulta3 = "SELECT a_correo, a_rfc, a_curp, a_imss, a_paterno, a_materno, a_nombre FROM con_empleados WHERE a_correo = '$el_correo' OR a_rfc = '$el_rfc' OR a_curp = '$el_curp' OR a_imss = '$el_imss' OR concat(a_paterno, ' ', a_materno, ' ', a_nombre) = '$el_nombre'";
						$consulta3 = mysql_query($query_consulta3, $vacantes) or die(mysql_error());
						$row_consulta3 = mysql_fetch_assoc($consulta3);
						$totalRows_consulta3 = mysql_num_rows($consulta3);

						$query_reingreso = "SELECT RFC, curp, calle, cp FROM ind_bajas WHERE (RFC = '$el_rfc' OR curp = '$el_curp' OR concat( calle, cp ) = '$el_domicilio') AND fecha_baja = ''";
						$reingreso = mysql_query($query_reingreso, $vacantes) or die(mysql_error());
						$row_reingreso = mysql_fetch_assoc($reingreso);
						$totalRows_reingreso = mysql_num_rows($reingreso);


						$validador = 0;
						if($row_contratos['IDpuesto'] != '') {$validador = $validador + 1;} 
						if($row_contratos['escolaridad'] != ''){$validador = $validador + 1;} 
						if($total_b == 100) {$validador = $validador + 1;} 
						if($total_c > 0) {$validador = $validador + 1;} 
						?>
                          <tr>
                            <td>
							<div class="btn-group">
								<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" data-boundary="window">
								Editar<span class="caret"></span></button>
									<ul class="dropdown-menu">
										<li><a href="empleados_actualizar.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>">
										<span class="text text-success"><i class="icon-file-check"></i> 
										Datos Básicos</span></a></li>
										<li><a href="empleados_pago.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>">
										<?php if ($row_contratos['IDpuesto'] != '') {?>
										<span class="text text-success"><i class="icon-file-check"></i> 
										<?php } else { ?>
										<span class="text text-danger"><i class="icon-file-minus"></i> 
										<?php } ?>
										Datos de pago</span></a></li>
										<li class="divider"></li>
										<li><a href="empleados_adicionales.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>">
										<?php if ($row_contratos['escolaridad'] != '') {?>
										<span class="text text-success"><i class="icon-file-check"></i> 
										<?php } else { ?>
										<span class="text text-danger"><i class="icon-file-minus"></i> 
										<?php } ?>
										Datos Adicionales</a></li>
										<li><a href="empleados_beneficiarios.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>">
										<?php if ($total_b == 100 AND $total_c > 0) {?>
										<span class="text text-success"><i class="icon-file-check"></i> 
										<?php } else { ?>
										<span class="text text-danger"><i class="icon-file-minus"></i> 
										<?php } ?>
										Beneficiarios</a></li>
										</ul>
							</div>
							<?php if($validador > 3) { ?>
							<button type="button" data-target="#modal_print_<?php echo $row_contratos['IDempleado']; ?>"  data-toggle="modal" class="btn btn-success">Imprimir</button>
							<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-info">Estatus</button>
							<?php } ?>
							<button type="button" data-target="#modal_<?php echo $row_contratos['IDempleado']; ?>"  data-toggle="modal" class="btn btn-danger">Borrar</button>
							
									 <!-- danger modal -->
									<div id="modal_theme_danger" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-info">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cambiar estatus</h6>
												</div>
												<div class="modal-body">
																			
														<form action="admin_empleados_consulta.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														 
														 Los candidatos con estatus "Contratado" se ocultan del listado.
														<p>&nbsp;</p>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Estatus:</label>
															  <div class="col-lg-9">
															<select name="IDestatus" id="IDestatus" class="form-control" >
																<option value="1"<?php if (!(strcmp($row_contratos['IDestatus'], 1))) {echo "selected=\"selected\"";} ?>>En Proceso</option>
																<option value="0"<?php if (!(strcmp($row_contratos['IDestatus'], 0))) {echo "selected=\"selected\"";} ?>>Contratado</option>
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
													<button type="submit" id="submit" name="import" class="btn btn-info">Actualizar</button> 
													<input type="hidden" name="MM_insert" value="form1" />
													<input type="hidden" name="IDempleado" value="<?php echo $row_contratos['IDempleado']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->
							
									<!-- danger modal -->
									<div id="modal_<?php echo $row_contratos['IDempleado']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de borrado</h6>
												</div>
												<div class="modal-body">
												<p>¿Estas seguro que quieres borrar el registro?</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="admin_empleados_consulta.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>&borrar=1">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->

									<!-- danger modal -->
									<div id="modal_print_<?php echo $row_contratos['IDempleado']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Impresión de Formatos</h6>
												</div>
												<div class="modal-body">
												<p>
												<h6 class="text-semibold">Candidato</h6>
							<p><span class="text text-semibold">Nombre:</span> <?php echo $row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre']; ?><br/>
							<span class="text text-semibold">Puesto:</span> <?php echo $row_contratos['denominacion']; ?></p>
							<hr>										


							<h6 class="text-semibold">Generales</h6>
							<div class="list list-icons no-border">
							<div class="row">
							  <div class="col-xs-6 col-md-6">
							<li><a href="empleados_print2.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-excel text-semibold"></i>Requisición</a></li>
							<li><a href="empleados_print1.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>&IDtipo=1"><i class="icon-file-pdf text-semibold"></i>Contrato Temporal</a></li>
							<li><a href="empleados_print1.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>&IDtipo=2"><i class="icon-file-pdf text-semibold"></i>Contrato Permanente</a></li>
							<li><a href="empleados_print3.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Aviso de Privacidad</a></li>
							<li><a href="empleados_print4.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Política sobre conflicto de intereses</a></li>
							<li><a href="empleados_print5.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Prestaciones</a></li>
							<li><a href="empleados_print6.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Conocimiento R. Interior de Trabajo</a></li>
							<li><a href="empleados_print7.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Cuenta de Banco</a></li>
							  </div>	
							  <div class="col-xs-6 col-md-6">
							<li><a href="empleados_print8.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Renuncia anticipada</a></li>
							<li><a href="empleados_print10.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Carta compromiso</a></li>
							<li><a href="empleados_print11.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Correo electrónico</a></li>
							<li><a href="empleados_print15.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Carta responsiva COVID-19</a></li>
							<li><a href="empleados_print16.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Acuse de Políticas y Procedimientos</a></li>
							<li><a href="empleados_print17.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Ficha de Emergencia</a></li>
							<li><a href="empleados_print100.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>" target="_blank"><i class="icon-file-pdf text-semibold"></i>Seguro Vida</a></li>
							<li><a href="empleados_print101.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>" target="_blank"><i class="icon-file-pdf text-semibold"></i>Finanza</a></li>
							  </div>	
							</div>	
							</div>		
							<hr>										
							
							<h6 class="text-semibold">Choferes</h6>
							<div class="list list-icons no-border">
							<div class="row">
							  <div class="col-xs-12 col-md-12">
							<li><a href="empleados_print14.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Responsiva Choferes</a></li>
							<li><a href="empleados_print13.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Conocimiento cobertura y deducible</a></li>
							<li><a href="empleados_print9.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Pagaré</a></li>
							</div>		
							  </div>	
							</div>	
							<hr>										

							<h6 class="text-semibold">Ventas</h6>
							<div class="list list-icons no-border">
							<div class="row">
							  <div class="col-xs-12 col-md-12">
							<li><a href="empleados_print9.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Pagaré</a></li>
							</div>		
							  </div>	
							</div>	
							<hr>										
												
							<h6 class="text-semibold">Otros</h6>
							<div class="list list-icons no-border">
							<div class="row">
							  <div class="col-xs-12 col-md-12">
							<li><a href="empleados_print18.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>"><i class="icon-file-pdf text-semibold"></i>Carta Patronal IMSS <span class="label bg-success-400">New</span></a></li>
							<li><a href="CONTS/historia_clinica.pdf" target="_blank"><i class="icon-file-pdf  text-semibold"></i>Historia Clínica</a></li>
							</div>		
							  </div>	
							</div>	
											
												</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->



							</td>
                            <td><?php if ($row_contratos['IDestatus'] == 1) { echo "En Proceso";} else { echo "Contratado";} ?></td>
                            <td><?php echo date('d/m/Y', strtotime( $row_contratos['fecha_alta'])); ?>&nbsp; </td>
                            <td><?php echo $row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre']; ?>&nbsp;
							<?php if($totalRows_reingreso > 0 OR $totalRows_consulta3 > 1){ ?><i class="icon-warning text-danger"></i><?php }  ?>
							</td>
                            <td><?php echo $row_contratos['denominacion']; ?>&nbsp; </td>
                          </tr>
                          <?php } while ($row_contratos = mysql_fetch_assoc($contratos)); ?>
                        <?php } else { ?>
                           <tr>
                            <td>No existen ingresos con el criterio seleccionado.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                        <?php } ?>
                     </tbody>
					</table>
				  </div>
                      
				  </div>

					<!-- Footer -->
					<div class="footer text-muted">
	&copy; 2022. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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