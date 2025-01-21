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
$IDmes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar 1
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {

$IDobjetivo = $_POST['IDobjetivo'];
$updateSQL = sprintf("UPDATE com_vd_objetivo_mes SET IDmes=%s, anio=%s, objetivo_venta=%s, objetivo_clientes_venta=%s WHERE IDobjetivo='$IDobjetivo'",
                       GetSQLValueString($_POST['IDmes'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['objetivo_venta'], "int"),
                       GetSQLValueString($_POST['objetivo_clientes_venta'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
   header('Location: vd_objetivos.php?info=2');
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {


$insertSQL = sprintf("INSERT INTO com_vd_objetivo_mes (IDmes, anio, objetivo_venta, objetivo_clientes_venta, IDmatriz) 
												   VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDmes'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['objetivo_venta'], "int"),
                       GetSQLValueString($_POST['objetivo_clientes_venta'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();
  header('Location: vd_objetivos.php?info=2');
}


// borrar alternativo
if ((isset($_GET['borrado'])) && ($_GET['borrado'] == 1)) {

	$borrado = $_GET['IDobjetivo'];	
	$query_borrar = "DELETE FROM com_vd_objetivo_mes WHERE IDobjetivo = $borrado";
	$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
	header('Location: vd_objetivos.php?info=3');
}

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz = '$IDmatriz'";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz  WHERE IDmatriz = '$IDmatriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz2 = "SELECT * FROM vac_matriz  WHERE IDmatriz IN (3,12,23,13,14,16,20,18,26,25)";
$lmatriz2 = mysql_query($query_lmatriz2, $vacantes) or die(mysql_error());
$row_lmatriz2 = mysql_fetch_assoc($lmatriz2);
$totalRows_lmatriz2 = mysql_num_rows($lmatriz2);

if (isset($_POST['IDmes'])) { $_SESSION['IDmes'] = $_POST['IDmes']; } 
else if (!isset($_SESSION['IDmes'])) { $_SESSION['IDmes'] = date("m") - 1; } 

if (isset($_POST['anio'])) { $_SESSION['anio'] = $_POST['anio']; } 
else if (!isset($_SESSION['anio'])) { $_SESSION['anio'] = $anio; } 


if (isset($_POST['IDmatriz']) AND $_POST['IDmatriz'] > 0) 	
	{	
	$_SESSION['IDmatriz'] = $_POST['IDmatriz']; 
	} else if (!isset($_SESSION['IDmatriz'])) { $_SESSION['IDmatriz'] = $IDmatriz; 
	$_SESSION['a1'] = ' AND com_vd_objetivo_mes.IDmatriz IN (3,12,23,13,14,16,18,26,25) ';
	}


$IDmes = $_SESSION['IDmes'];
$IDmatriz = $_SESSION['IDmatriz'];
$a1 = $_SESSION['a1'];
$anio = $_SESSION['anio'];

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT com_vd_objetivo_mes.IDobjetivo, com_vd_objetivo_mes.IDmes, com_vd_objetivo_mes.anio, com_vd_objetivo_mes.IDmatriz, com_vd_objetivo_mes.objetivo_venta, com_vd_objetivo_mes.objetivo_clientes_venta, vac_matriz.matriz, vac_meses.mes  FROM com_vd_objetivo_mes LEFT JOIN vac_matriz ON com_vd_objetivo_mes.IDmatriz = vac_matriz.IDmatriz INNER JOIN vac_meses ON com_vd_objetivo_mes.IDmes = vac_meses.IDmes WHERE com_vd_objetivo_mes.anio = $anio AND com_vd_objetivo_mes.IDmes = $IDmes AND com_vd_objetivo_mes.IDmatriz IN (3,12,23,13,14,16,20,18,26,25)";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

  switch ($IDmes) {
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


$query_meses = "SELECT * FROM vac_meses";
mysql_query("SET NAMES 'utf8'");
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

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
    
    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<!-- /theme JS files -->
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente  el objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Objetivos Mensuales Comisiones Ventas Detalle</h6>
								</div>

							<div class="panel-body">
								<p>A continuación se muestran los objetivos de cada Sucursal.</br>
								<p>&nbsp;</br>
                                      <button type="button" data-target="#modal_theme_danger3" data-toggle="modal" class="btn btn-success btn-icon">Agregar Objetivo</button>
  									<a class="btn btn-default" href="admin_plantilla.php">Regresar</a>
  
  
 
                     <form method="POST" action="vd_objetivos.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <div class="col-lg-6">Mes:
										 <select name="IDmes" class="form-control">
										   <?php do {  ?>
										   <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $IDmes))) {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
										   <?php
										  } while ($row_meses = mysql_fetch_assoc($meses));
										  $rows = mysql_num_rows($meses);
										  if($rows > 0) {
											  mysql_data_seek($meses, 0);
											  $row_meses = mysql_fetch_assoc($meses);
										  } ?></select>
						    </div>
                            </td>
							<td>
                            <div class="col-lg-6">Año:
										 <select name="anio" class="form-control">
										 <option value="2025"<?php if (!(strcmp(2025, $anio))) {echo "selected=\"selected\"";} ?>>2025</option>
										 <option value="2024"<?php if (!(strcmp(2024, $anio))) {echo "selected=\"selected\"";} ?>>2024</option>
										 <option value="2023"<?php if (!(strcmp(2023, $anio))) {echo "selected=\"selected\"";} ?>>2023</option>
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

 
 
 
 
 					<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                  <tr class="bg-primary"> 
                                    <th>Matriz</th>
                                    <th>Mes</th>
                                    <th>Año</th>
                                    <th>Objetivo Venta</th>
                                    <th>Objetivo Clientes con Venta</th>
                                  <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_autorizados > 0)  { 
									do { ?>
                                    <tr>
                                      <td><?php echo $row_autorizados['matriz']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['mes']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['anio']; ?>&nbsp; </td>
                                      <td><?php echo number_format($row_autorizados['objetivo_venta']); ?>&nbsp; </td>
                                      <td><?php echo number_format($row_autorizados['objetivo_clientes_venta']); ?>
                                      <td>
                                      <button type="button" data-target="#modal_theme_danger<?php echo $row_autorizados['IDobjetivo']; ?>"  data-toggle="modal" class="btn btn-primary btn-icon">Editar</button>
                                      <button type="button" data-target="#modal_theme_danger<?php echo $row_autorizados['IDobjetivo']; ?>2"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                                      </tr>
                                      
                    <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_autorizados['IDobjetivo']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Editar Monto</h6>
								</div>

								<div class="modal-body">
            					<form method="post" class="form-horizontal form-validate-jquery" name="form2"  action="<?php echo $editFormAction; ?>" >

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Matriz:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="IDmatriz" id="IDmatriz"  value="<?php echo $row_lmatriz['matriz']; ?>" readonly>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Año:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="anio" id="anio"  value="<?php echo $anio; ?>" readonly>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Mes:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
													<select name="IDmes" id="IDmes" class="form-control" >
													<?php do { ?>
													   <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $IDmes))) 
													   {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
													   <?php
													  } while ($row_meses = mysql_fetch_assoc($meses));
													  $rows = mysql_num_rows($meses);
													  if($rows > 0) {
														  mysql_data_seek($meses, 0);
														  $row_meses = mysql_fetch_assoc($meses);
													  } ?> 
													</select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Objetivo de Venta:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="number" class="form-control" name="objetivo_venta" id="objetivo_venta"  value="<?php  echo $row_autorizados['objetivo_venta']; ?>" required="required" placeholder="Indica el motivo.">
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Objetivo Clientes con Venta:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="number" class="form-control" name="objetivo_clientes_venta" id="objetivo_clientes_venta"  value="<?php  echo $row_autorizados['objetivo_clientes_venta']; ?>" required="required" placeholder="Indica el motivo.">
												</div>
											</div>
	                                    </div>



								<div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			                                	<input type="hidden" name="IDestatus" value="1">
			                                	<input type="hidden" name="MM_update" value="form2">
			                                	<input type="hidden" name="IDobjetivo" value="<?php echo $row_autorizados['IDobjetivo']; ?>">
                                                <input type="submit" class="btn btn-primary" value="Actualizar">
								</div>
                                
                                </form>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                    
                    <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_autorizados['IDobjetivo']; ?>2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
								¿Estas seguro que quieres borrar el Objetivo
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="vd_objetivos.php?IDobjetivo=<?php echo $row_autorizados['IDobjetivo']; ?>&borrado=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
                                      
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados));    ?>
									
									<?php } else {   ?>
										<td colspan=6>Sin resultados con el filtro actual</td>
									<?php }   ?>

									
                                  </tbody>
                                </table>
                                
                                
                                                    
                    <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_autorizados['IDobjetivo']; ?>3" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Agregar Montos</h6>
								</div>

								<div class="modal-body">
            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="<?php echo $editFormAction; ?>" >

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Matriz:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_lmatriz2['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz2['IDmatriz'], $IDmatriz))) {echo "SELECTED";} ?>><?php echo $row_lmatriz2['matriz']?></option>
													  <?php
													 } while ($row_lmatriz2 = mysql_fetch_assoc($lmatriz2));
													 $rows = mysql_num_rows($lmatriz2);
													 if($rows > 0) {
													 mysql_data_seek($lmatriz2, 0);
													 $row_lmatriz2 = mysql_fetch_assoc($lmatriz2);
													 } ?>
                                          </select>
												</div>
											</div>
	                                    </div>


											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Año:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" class="form-control" name="anio" id="anio"  value="<?php echo $anio; ?>" readonly>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Mes:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
													<select name="IDmes" id="IDmes" class="form-control" >
													<?php do { ?>
													   <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $IDmes))) 
													   {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
													   <?php
													  } while ($row_meses = mysql_fetch_assoc($meses));
													  $rows = mysql_num_rows($meses);
													  if($rows > 0) {
														  mysql_data_seek($meses, 0);
														  $row_meses = mysql_fetch_assoc($meses);
													  } ?> 
													</select>
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Objetivo de Venta:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="number" class="form-control" name="objetivo_venta" id="objetivo_venta"  value="" placeholder="Indica el monto." required="required">
												</div>
											</div>
	                                    </div>

											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Dias">Objetivo Clientes con Venta:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="number" class="form-control" name="objetivo_clientes_venta" id="objetivo_clientes_venta"  value="" placeholder="Indica el monto." required="required">
												</div>
											</div>
	                                    </div>



								<div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			                                	<input type="hidden" name="IDestatus" value="1">
			                                	<input type="hidden" name="MM_insert" value="form1">
                                                <input type="submit" class="btn btn-primary" value="Agregar Objetivo">
								</div>
                                
                                </form>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


						</div>
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