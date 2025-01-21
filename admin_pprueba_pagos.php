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
$fechapp = date("YmdHis"); // la fecha actual

// mes y semana
$el_mes = date("m");
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
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

	 if(isset($_POST['IDestatus_f']) && ($_POST['IDestatus_f'] == 0)) {$_SESSION['IDestatus'] = " "; } 
else if(isset($_POST['IDestatus_f']) && ($_POST['IDestatus_f'] == 1)) {$_SESSION['IDestatus'] = " AND (DATE(pp_prueba_pagos.fecha_pago) <= '$fecha') "; } 
else if(isset($_POST['IDestatus_f']) && ($_POST['IDestatus_f'] == 2)) {$_SESSION['IDestatus'] = " AND (DATE(pp_prueba_pagos.fecha_pago) >= '$fecha') "; } 
else { $_SESSION['IDestatus'] = ' '; } 

if(isset($_POST['IDmatriz']) && ($_POST['IDmatriz']  > 0)) {
$_SESSION['IDmatriz'] = $_POST['IDmatriz']; } else { $_SESSION['IDmatriz'] = $IDmatrizes;}

$la_matriz = $_SESSION['IDmatriz'];
$IDestatus = $_SESSION['IDestatus']; 
$IDestatus_f = $_POST['IDestatus_f']; 

mysql_select_db($database_vacantes, $vacantes);
$query_pprueba = "SELECT pp_prueba_pagos.IDpprueba_pagos, pp_prueba_pagos.fecha_pago, pp_prueba_pagos.monto_pago, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDmatriz,  pp_prueba.IDpuesto_destino, pp_prueba.fecha_inicio, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, destino.denominacion AS PuestoDestino, Origen.denominacion AS PuestoOrigen, vac_matriz.matriz  FROM pp_prueba_pagos INNER JOIN pp_prueba ON pp_prueba_pagos.IDpprueba = pp_prueba.IDpprueba INNER JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado INNER JOIN vac_puestos AS Origen ON pp_prueba.IDpuesto = Origen.IDpuesto INNER JOIN vac_puestos AS destino ON pp_prueba.IDpuesto_destino = destino.IDpuesto INNER JOIN vac_matriz ON pp_prueba.IDmatriz = vac_matriz.IDmatriz WHERE pp_prueba.IDmatriz in ($la_matriz)".$IDestatus;  
mysql_query("SET NAMES 'utf8'");
$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
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

					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Periodos de Prueba</h5>
								</div>

								<div class="panel-body">
								<p>A continuación se muestran los Pagos Programados.</p>
								
                                
                    <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    
                    </div>
					</div>
					<!-- /colored button -->
	

<form method="POST" action="admin_pprueba_pagos.php">
<table class="table">
<tbody>							  
	<tr>
	<td><div class="col-lg-9">Periodo
		 <select class="form-control" name="IDestatus_f">
		   <option value="0"<?php if (!(strcmp($IDestatus_f, 0))) {echo "selected=\"selected\"";} ?>>Todos</option>
		   <option value="1"<?php if (!(strcmp($IDestatus_f, 1))) {echo "selected=\"selected\"";} ?>> Anteriores </option>
		   <option value="2"<?php if (!(strcmp($IDestatus_f, 2))) {echo "selected=\"selected\"";} ?>> Siguientes </option>
		 </select>
	</div></td>
	<td><div class="col-lg-9">Matriz
		 <select name="IDmatriz" class="form-control">
         <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>TODAS</option>
		   <?php do {  ?>
		   <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
		   <?php
		  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
		  $rows = mysql_num_rows($lmatriz);
		  if($rows > 0) {
			  mysql_data_seek($lmatriz, 0);
			  $row_lmatriz = mysql_fetch_assoc($lmatriz);
		  } ?>
		  </select>
	</div></td>
	<td>
	<button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right13 position-right"></i></button>
	</td>
  </tr>
</tbody>
</table>
</form>



								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>No. Emp.</th>
                                    <th>Matriz</th>
                                    <th>Nombre</th>
                                    <th>Puesto Destino</th>
                                    <th>Fecha Pago</th>
                                    <th>Monto Pago</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_pprueba > 0 ) { ?>
								  <?php do { 
									$Elperiodo = $row_pprueba['IDpprueba'];								  
									$query_pagos = "SELECT * FROM pp_prueba_pagos WHERE IDpprueba = $Elperiodo"; 
									$pagos = mysql_query($query_pagos, $vacantes) or die(mysql_error());
									$row_pagos = mysql_fetch_assoc($pagos);
									$totalRows_pagos = mysql_num_rows($pagos); 
								  ?>
                                    <tr>
                                      <td><?php echo $row_pprueba['IDempleado'];?></td>
                                      <td><?php echo $row_pprueba['matriz'];?></td>
                                      <td><?php echo $row_pprueba['emp_paterno'] . " " . $row_pprueba['emp_materno'] . " " . $row_pprueba['emp_nombre'];?></td>
                                      <td><?php echo $row_pprueba['PuestoDestino']; ?></td>
                                      <td><?php echo date("d/m/Y",strtotime($row_pprueba['fecha_pago']));  ?></td>
                                      <td><?php echo "$" . number_format($row_pprueba['monto_pago']);  ?></td>
									  <td>
									  <a class="btn btn-xs btn-primary" href="admin_pprueba_edit_3.php?IDpprueba=<?php echo $row_pprueba['IDpprueba']; ?>"><i class="icon-pencil4"></i></a>
									  <a class="btn btn-xs btn-danger" href="admin_pprueba_edit_pagos3.php?IDpprueba=<?php echo $row_pprueba['IDpprueba']; ?>"><i class="icon-coin-dollar"></i></a>
									  </td>
                                    </tr>									
                                    <?php } while ($row_pprueba = mysql_fetch_assoc($pprueba)); ?>
 							  <?php } else { ?>
<tr>
                                      <td>No se tienen Periodos de Prueba .</td>
                                      <td></td>
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