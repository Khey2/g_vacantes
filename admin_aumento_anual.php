<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fecha_limite = date( 'Y-m-d' , strtotime("2022-08-01"));

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 
$el_usuario = $row_usuario['IDusuario'];
//$el_usuario = 1395;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

// variables
$tipo = $_POST['tipo'];
$monto = $_POST['aumento'];
$fecha = date("Y-m-d");
$IDempleadox = $_POST['IDempleado'];
$sueldo_mensual = $_POST['sueldo_mensual'];
$comentarios = $_POST['comentarios'];

if ($tipo == 1) {
$aumento_monto = $monto;
$aumento_porcentaje = ($monto / $sueldo_mensual) * 100;
} elseif ($tipo == 2){
$aumento_porcentaje = $monto;
$aumento_monto = $sueldo_mensual * ($aumento_porcentaje / 100);
}

//echo "Sueldo mensual".$sueldo_mensual."<br/>";
//echo "Monto Capturado".$monto."<br/>";
//echo "%".$aumento_porcentaje."<br/>";
//echo "$".$aumento_monto."<br/>";

$updateSQL = "UPDATE prod_activos_anual SET aumento_monto = '$aumento_monto', aumento_porcentaje = '$aumento_porcentaje', fecha_captura = '$fecha', IDusuario = '$el_usuario', comentarios = '$comentarios', ajustado = 1 WHERE IDempleado = '$IDempleadox'";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header('Location: admin_aumento_anual.php?info=2');
}


if ((isset($_GET["restablecer"])) && ($_GET["restablecer"] == "1")) {
	
$IDempleadox = $_GET['IDempleado'];

$query_restablecer = "SELECT * FROM prod_activos_anual WHERE IDempleado = $IDempleadox"; 
$restablecer = mysql_query($query_restablecer, $vacantes) or die(mysql_error());
$row_restablecer = mysql_fetch_assoc($restablecer);
$aumento_monto = $row_restablecer['aumento_monto_original'];
$aumento_porcentaje = $row_restablecer['aumento_porcentaje_original'];
$fecha = date("Y-m-d");

$updateSQL = "UPDATE prod_activos_anual SET aumento_monto = '$aumento_monto', aumento_porcentaje = '$aumento_porcentaje', fecha_captura = '$fecha', IDusuario = '$el_usuario',  ajustado = '' WHERE IDempleado = '$IDempleadox'";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header('Location: admin_aumento_anual.php?info=2');
}

if(isset($_POST['IDareax']) AND $_POST['IDareax'] > 0)
{ $_SESSION['IDareax'] = $_POST['IDareax']; $_SESSION['filtro4'] = " AND prod_activos_anual.IDarea = ".$_POST['IDareax'];}

if(isset($_POST['IDareax']) AND $_POST['IDareax'] == 0)
{ $_SESSION['IDareax'] = ""; $_SESSION['filtro4'] = "";}

if(!isset($_SESSION['IDareax'])) { $_SESSION['IDareax'] = ""; $_SESSION['filtro4'] = "";}

if(isset($_POST['IDmatrix']) AND $_POST['IDmatrix'] > 0)
{ $_SESSION['IDmatrix'] = $_POST['IDmatrix']; $_SESSION['filtro1'] = " AND prod_activos_anual.IDmatriz = ".$_POST['IDmatrix'];}

if(isset($_POST['IDmatrix']) AND $_POST['IDmatrix'] == 0)
{ $_SESSION['IDmatrix'] = ""; $_SESSION['filtro1'] = "";}

if(!isset($_SESSION['IDmatrix'])) { $_SESSION['IDmatrix'] = ""; $_SESSION['filtro1'] = "";}

if(isset($_POST['IDajustes']) AND $_POST['IDajustes'] > 0)
{ $_SESSION['IDajustes'] = $_POST['IDajustes']; $_SESSION['filtro2'] = " AND prod_activos_anual.ajustado = 1";}

if(isset($_POST['IDajustes']) AND $_POST['IDajustes'] == 0)
{ $_SESSION['IDajustes'] = ""; $_SESSION['filtro2'] = "";}

if(!isset($_SESSION['IDajustes'])) { $_SESSION['IDajustes'] = ""; $_SESSION['filtro2'] = "";}

if(isset($_POST['IDpuestox']) AND $_POST['IDpuestox'] > 0)
{ $_SESSION['IDpuestox'] = $_POST['IDpuestox']; $_SESSION['filtro3'] = " AND prod_activos_anual.IDpuesto = ".$_POST['IDpuestox'];}

if(isset($_POST['IDpuestox']) AND $_POST['IDpuestox'] == 0)
{ $_SESSION['IDpuestox'] = ""; $_SESSION['filtro3'] = "";}

if(!isset($_SESSION['IDpuestox'])) { $_SESSION['IDpuestox'] = ""; $_SESSION['filtro3'] = "";}

if(isset($_POST['IDsubareax']) AND $_POST['IDsubareax'] > 0)
{ $_SESSION['IDsubareax'] = $_POST['IDsubareax']; $_SESSION['filtro5'] = " AND prod_activos_anual.IDsubarea = ".$_POST['IDsubareax'];}

if(isset($_POST['IDsubareax']) AND $_POST['IDsubareax'] == 0)
{ $_SESSION['IDsubareax'] = ""; $_SESSION['filtro5'] = "";}

if(!isset($_SESSION['IDsubareax'])) { $_SESSION['IDsubareax'] = ""; $_SESSION['filtro5'] = "";}

$IDsubareax = $_SESSION['IDsubareax'];
$IDareax = $_SESSION['IDareax'];
$IDmatrix = $_SESSION['IDmatrix'];
$IDajustes = $_SESSION['IDajustes'];
$IDpuestox = $_SESSION['IDpuestox'];
$filtro1 = $_SESSION['filtro1'];
$filtro2 = $_SESSION['filtro2'];
$filtro3 = $_SESSION['filtro3'];
$filtro4 = $_SESSION['filtro4'];
$filtro5 = $_SESSION['filtro5'];

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT * FROM prod_activos_anual WHERE prod_activos_anual.IDempleado >0 ".$filtro1.$filtro2.$filtro3.$filtro4.$filtro5;
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
//echo $query_detalle;

//PRESUPUESTO no cambia con el filtro
mysql_select_db($database_vacantes, $vacantes);
$query_detalleT = "SELECT SUM(prod_activos_anual.sueldo_mensual) AS sueldo_mensualT, SUM(prod_activos_anual.aumento_monto) AS aumento_montoT,  AVG(prod_activos_anual.aumento_porcentaje) AS aumento_porcentajeT FROM prod_activos_anual WHERE prod_activos_anual.IDempleado > 0".$filtro1.$filtro2.$filtro3.$filtro4.$filtro5; 
mysql_query("SET NAMES 'utf8'");
$detalleT = mysql_query($query_detalleT, $vacantes) or die(mysql_error());
$row_detalleT = mysql_fetch_assoc($detalleT);
$totalRows_detalleT = mysql_num_rows($detalleT);

// filtros
$query_matrix = "SELECT vac_matriz.matriz, vac_matriz.IDmatriz, prod_activos_anual.IDempleado FROM prod_activos_anual LEFT JOIN vac_matriz ON prod_activos_anual.IDmatriz = vac_matriz.IDmatriz WHERE prod_activos_anual.IDempleado >0 GROUP BY vac_matriz.matriz"; 
mysql_query("SET NAMES 'utf8'");
$matrix = mysql_query($query_matrix, $vacantes) or die(mysql_error());
$row_matrix = mysql_fetch_assoc($matrix);

$query_puestox = "SELECT vac_puestos.denominacion, vac_puestos.IDpuesto, prod_activos_anual.IDpuesto FROM prod_activos_anual LEFT JOIN vac_puestos ON prod_activos_anual.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos_anual.IDempleado >0 GROUP BY vac_puestos.denominacion"; 
mysql_query("SET NAMES 'utf8'");
$puestox = mysql_query($query_puestox, $vacantes) or die(mysql_error());
$row_puestox = mysql_fetch_assoc($puestox);

$query_areax = "SELECT vac_areas.area, vac_areas.IDarea, prod_activos_anual.IDarea FROM prod_activos_anual LEFT JOIN vac_areas ON prod_activos_anual.IDarea = vac_areas.IDarea WHERE prod_activos_anual.IDempleado > 0 GROUP BY vac_areas.area"; 
mysql_query("SET NAMES 'utf8'");
$areax = mysql_query($query_areax, $vacantes) or die(mysql_error());
$row_areax = mysql_fetch_assoc($areax);

$query_subareax = "SELECT vac_subareas.subarea, vac_subareas.IDsubarea, prod_activos_anual.IDsubarea FROM prod_activos_anual LEFT JOIN vac_subareas ON prod_activos_anual.IDsubarea = vac_subareas.IDsubarea WHERE prod_activos_anual.IDempleado > 0 GROUP BY vac_subareas.subarea"; 
mysql_query("SET NAMES 'utf8'");
$subareax = mysql_query($query_subareax, $vacantes) or die(mysql_error());
$row_subareax = mysql_fetch_assoc($subareax);
$aumento_porcentajeT = round($row_detalleT['aumento_montoT'] / $row_detalleT['sueldo_mensualT'],3) * 100;

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<!-- /theme JS files -->

<?php if ($row_detalle['estatus'] == 1) { ?>
	<script>
      function load() {
       new Noty({
            text: 'Da clic en <b>"Descargar Reporte"</b> para descargar el archivo en Excel con los ajustes aplicados.',
            type: 'info'
        }).show();
    }
	 window.onload = load;
     </script>
	 
<?php } ?>


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


	                <!-- Content area -->
				<div class="content">
				
				
				                        <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-success alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
                            Ajuste guardado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                        <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 8))) { ?>
					    <div class="alert bg-success alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
                            Se ha cerrado correctamente la captura.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                        <!-- Basic alert -->
                        <?php if($aumento_porcentajeT > 8) { ?>
					    <div class="alert bg-danger alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
                            El presupuesto está rebasado, por favor validar.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><span class="text text-semibold">Instrucciones</span></h6>
								</div>
  
					<div class="panel-body">
							<p>
							En la siguiente tabla se muestran los ajustes anuales predeterminados <b>(8% anual)</b> para el personal Administrativo en Corporativo y Sucursales.<br/>
							Los empleados deben cumplir los siguientes criterios:
							<ul>
							<li><b>A. Criterio por Ajuste.</b> Que no hayan tenido un ajuste de sueldo en el último año.</li>
							<li><b>B. Criterio por Antigüedad.</b> Contar con más de 8 meses de antigüedad.</li>
							<li><b>C. Criterio por Desempeño.</b> Contar con resultado satisfactorio o sobresaliente en la evaluación anual.</li>
							</ul>
							
							Para aplicar una excepción, da clic en el icono de <span class='text-bold'>Moneda ($)</span> o <span class='text-bold'>Porcentaje (%)</span>, según corresponda (se requiere justificación en cada excepción).<br/>
							
							<ul>
							<li>Color<span class='text-warning text-semibold'> Naranja</span> en el nombre. No cubre el criterio A o B.</li>
							<li>Color<span class='text-success text-semibold'> Verde</span> en el nombre. Empleado con excepción aplicada.</li>
							<li>Color<span class='text-danger text-semibold'> Rojo</span> en el nombre. No cubre el criterio C, por lo tanto no es sujeto a una excepción.</li>
							</ul>
													
							<b>El sistema te indicará cuando hayas rebasado el presupuesto.</b></p>
							<p>Da clic en <span class='text-success text-semibold'>Descargar Reporte </span>para descargar el archivo en Excel.<br />
							Una vez finalizada la revisión, dar clic en <span class='text-danger text-semibold'>Terminar </span>para cerrar y autorizar los ajustes.</p>

							
							
					</div>
				</div>
				<!-- Contenido -->
                  <div class="panel panel-flat">

					<div class="panel-body">



				<!-- Statistics with progress bar -->
					<div class="row">

						<div class="col-sm-4 col-md-4">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<span>Costo Total Actual</span>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar info" style="width: 100%">
									</div>
								</div>
										<h6 class="no-margin text-semibold"><?php echo "$".number_format($row_detalleT['sueldo_mensualT'], 2, '.', ',') ?></span>
							</div>
						</div>

						<div class="col-sm-4 col-md-4">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<span>Valor Aumento</span>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar info" style="width: 100%">
									</div>
								</div>
										<h6 class="no-margin text-semibold">
										<?php echo "$".number_format($row_detalleT['aumento_montoT'], 2, '.', ',') ?> &nbsp;&nbsp; = &nbsp;&nbsp; <?php echo $aumento_porcentajeT;?>%
</h6>
							</div>
						</div>


						<div class="col-sm-4 col-md-4">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<span>Costo Total Nuevo</span>
									</div>
								</div>

								<div class="progress progress-micro mb-10">
									<div class="progress-bar info" style="width: 100%">
									</div>
								</div>
										<h6 class="no-margin text-semibold"><?php echo "$".number_format($row_detalleT['sueldo_mensualT'] + $row_detalleT['aumento_montoT'], 2, '.', ',') ?></span>
							</div>
						</div>


					</div>

					<!-- /statistics with progress bar -->

				<!-- Statistics with progress bar -->
					<div class="row">

						<div class="col-sm-12 col-md-12">
							<div class="panel panel-body">
								<div class="media no-margin-top content-group">
									<div class="media-left media-middle">
									</div>

									<div class="media-body">
										<h6 class="no-margin text-semibold">Filtros</h6>
									</div>
								</div>

								<form action="admin_aumento_anual.php" class="form-inline" method="POST">
										<div class="form-group has-feedback">
											<label class="control-label col-lg-3">Sucursal: </label>
												<select name="IDmatrix" id="IDmatrix" class="bootstrap-select" data-live-search="true" data-width="100%">
													<option value="0">TODAS</option> 
													<?php  do { ?>
													<option value="<?php echo $row_matrix['IDmatriz']?>"<?php if (!(strcmp($row_matrix['IDmatriz'], $IDmatrix))) 
													{echo "SELECTED";} ?>><?php echo $row_matrix['matriz']?></option>
													<?php
													} while ($row_matrix = mysql_fetch_assoc($matrix));
													$rows = mysql_num_rows($matrix);
													if($rows > 0) {
													mysql_data_seek($matrix, 0);
													$row_matrix = mysql_fetch_assoc($matrix);
													} ?>
												</select>
										</div>

										<div class="form-group has-feedback">
											<label class="control-label col-lg-3">Puesto: </label>
												<select name="IDpuestox" id="IDpuestox" class="bootstrap-select" data-live-search="true" data-width="100%">
													<option value="0">TODOS</option> 
													<?php  do { ?>
													<option value="<?php echo $row_puestox['IDpuesto']?>"<?php if (!(strcmp($row_puestox['IDpuesto'], $IDpuestox))) 
													{echo "SELECTED";} ?>><?php echo $row_puestox['denominacion']?></option>
													<?php
													} while ($row_puestox = mysql_fetch_assoc($puestox));
													$rows = mysql_num_rows($puestox);
													if($rows > 0) {
													mysql_data_seek($puestox, 0);
													$row_puestox = mysql_fetch_assoc($puestox);
													} ?>
												</select>
										</div>
										
										<div class="form-group has-feedback">
											<label class="control-label col-lg-3">Area: </label>
												<select name="IDareax" id="IDareax" class="bootstrap-select" data-live-search="true" data-width="100%">
													<option value="0">TODAS</option> 
													<?php  do { ?>
													<option value="<?php echo $row_areax['IDarea']?>"<?php if (!(strcmp($row_areax['IDarea'], $IDareax))) 
													{echo "SELECTED";} ?>><?php echo $row_areax['area']?></option>
													<?php
													} while ($row_areax = mysql_fetch_assoc($areax));
													$rows = mysql_num_rows($areax);
													if($rows > 0) {
													mysql_data_seek($areax, 0);
													$row_areax = mysql_fetch_assoc($areax);
													} ?>
												</select>
										</div>

										<div class="form-group has-feedback">
											<label class="control-label col-lg-3">Subarea: </label>
												<select name="IDsubareax" id="IDsubareax" class="bootstrap-select" data-live-search="true" data-width="100%">
													<option value="0">TODAS</option> 
													<?php  do { ?>
													<option value="<?php echo $row_subareax['IDsubarea']?>"<?php if (!(strcmp($row_subareax['IDsubarea'], $IDsubareax))) 
													{echo "SELECTED";} ?>><?php echo $row_subareax['subarea']?></option>
													<?php
													} while ($row_subareax = mysql_fetch_assoc($subareax));
													$rows = mysql_num_rows($subareax);
													if($rows > 0) {
													mysql_data_seek($subareax, 0);
													$row_subareax = mysql_fetch_assoc($subareax);
													} ?>
												</select>
										</div>

										<div class="form-group has-feedback">
											<label class="control-label col-lg-3">Excepciones: </label>
												<select name="IDajustes" id="IDajustes" class="bootstrap-select" data-live-search="true" data-width="100%">
													<option value="0"<?php if ($IDajustes == 0){echo "SELECTED";} ?>>TODOS</option>
													<option value="1"<?php if ($IDajustes == 1){echo "SELECTED";} ?>>EXEPCIONES</option>
												</select>
										</div>

										<div class="form-group has-feedback">
										<button type="submit" class="btn btn-primary">Filtrar </button>&nbsp;
										<a href="admin_aumento_anual.php" class="btn btn-default"> Borrar Filtro </a>&nbsp;
										<a href="admin_aumento_anual_reporte.php" class="btn btn-success"> Descargar Reporte</a>&nbsp;
										</div>
								</form>
							</div>
						</div>


					</div>

					<!-- /statistics with progress bar -->

							
				</div>
				</div>
				<!-- Contenido -->
                  <div class="panel panel-flat">

					<div class="panel-body">

				<div class="table-responsive">
				<table class="table table-bordered table-hover datatable-highlight">				
                    <thead> 
                    <tr class="bg-primary"> 
                      <th class="text-center"><div class="size">Ajustes</div></th>
                      <th class="text-center">Empleado</th>
                      <th class="text-center">Puesto</th>
                      <th class="text-center">Fecha Ingreso</th>
                      <th class="bg-warning text-center">Criterios (A.B.C)</th>
                      <th class="bg-danger  text-center">Sueldo Actual</th>
                      <th class="bg-danger  text-center">Incremento $</th>
                      <th class="bg-danger  text-center">Incremento %</th>
                      <th class="bg-danger  text-center">Sueldo Nuevo</th>
               		 </tr>
                    </thead>
                    <tfoot> 
                    <tr> 
                      <th>Totales</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th><?php echo "$".number_format($row_detalleT['sueldo_mensualT'], 2, '.', ',') ?></th>
                      <th><?php echo "$".number_format($row_detalleT['aumento_montoT'], 2, '.', ',') ?></th>
                      <th><?php echo $aumento_porcentajeT;?>%</th>
                      <th><?php echo "$".number_format($row_detalleT['sueldo_mensualT'] + $row_detalleT['aumento_montoT'], 2, '.', ',') ?></th>
               		 </tr>
                    </tfoot>
                    <tbody>
						<?php if ($totalRows_detalle > 0) {?>
						<?php  do { 

						//desempeño
						$IDempleado = $row_detalle['IDempleado'];
						$query_sed = "SELECT estatus FROM sed_individuales_resultados WHERE IDempleado = $IDempleado AND IDperiodo = 10"; 
						$sed = mysql_query($query_sed, $vacantes) or die(mysql_error());
						$row_sed = mysql_fetch_assoc($sed);
						$estatus = $row_sed['estatus'];
						$nombre = $row_detalle['emp_paterno']." ".$row_detalle['emp_materno']." ".$row_detalle['emp_nombre'];
						
						if ($estatus == 3 OR $estatus == 2 OR $row_detalle['criterio3'] == 0 OR $row_detalle['fecha_antiguedad'] > $fecha_limite) {$criterio3 = 1;} else {$criterio3 = 0;}
						
						?>
                        <tr>
							<td>
							<div onClick="loadDynamicContentModal('<?php echo $IDempleado; ?>', '1')" class="btn btn-xs btn-info">$</div>
							<div onClick="loadDynamicContentModal('<?php echo $IDempleado; ?>', '2')" class="btn btn-xs btn-info">%</div>
							</td>
							<td><?php if ($row_detalle['ajustado'] == 1){ ?>
							<span class='text-success'><?php echo $nombre; ?> Ajustado</span>
							<?php } else if (($row_detalle['criterio1'] == 0 OR $row_detalle['criterio2'] == 0 ) AND $criterio3 == 1){ ?>
							<span class='text-warning'><?php echo $nombre; ?> No Aplica</span
							<?php } else if ($criterio3 == 0){ ?>
							<span class='text-danger'><?php echo $nombre; ?> Sin SED</span
							<?php } else { ?>
							<span class='text-default'><?php echo $nombre; ?></span>
							<?php }  ?>
							
							</td>
                            <td><?php echo $row_detalle['denominacion']; ?></td>
                            <td><?php echo date( 'd/m/Y' , strtotime($row_detalle['fecha_antiguedad'])) ; ?></td>
                            <td align="center">
							<?php if ($row_detalle['criterio1'] == 1){ ?><i class="text-success-600 icon-checkmark2"></i><?php } else { ?><i class="text-danger-600 icon-cross3"></i><?php } ?>
							<?php if ($row_detalle['criterio2'] == 1){ ?><i class="text-success-600 icon-checkmark2"></i><?php } else { ?><i class="text-danger-600 icon-cross3"></i><?php } ?>
							<?php if ($criterio3 == 1){ ?><i class="text-success-600 icon-checkmark2"></i><?php } else { ?><i class="text-danger-600 icon-cross3"></i>
							<?php } ?>
							</td>
                            <td><?php echo "$".number_format($row_detalle['sueldo_mensual'], 2, '.', ','); ?></td>
                            <td><?php echo "$".number_format($row_detalle['aumento_monto'], 2, '.', ','); ?></td>
                            <td><?php echo round($row_detalle['aumento_porcentaje'],1); ?>%</td>
                            
							<td><?php if ($row_detalle['aumento_porcentaje'] != '') {echo "$".number_format($row_detalle['sueldo_mensual'] + $row_detalle['aumento_monto'], 2, '.', ',');} ?></td>
                        </tr>
						
					 <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
					 <?php } else { ?>
							<td colspan="9">No existen empleados con el filtro seleccionado.</td>
					 <?php }  ?>
                    </tbody>
                   </table> 
					  </div>


                   <!-- Inline form modal -->
					<div id="myModal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Ajuste Anual de Sueldos 2023</h5>
								</div>
							<div class="modal-body">
								Indica el Monto o Porcentaje de aumento anual, según corresponda. <br/>
								<p>&nbsp;</p>

			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->


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
<script>
function loadDynamicContentModal(modal, Tipo){
	var options = {
			modal: true
		};
	$('#conte-modal').load('admin_aumento_anual_mdl.php?Tipo=' + Tipo + '&IDempleado='+ modal, function() {
		$('#myModal').modal({show:true});
    });    
}
</script> 

</body>
</html>

