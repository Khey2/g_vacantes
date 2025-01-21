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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];


if($row_usuario['password'] == md5($row_usuario['IDusuario'])) { header("Location: cambio_pass.php?info=4"); }


if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } else { $_SESSION['el_mes'] = "";}

$el_mes = $_SESSION['el_mes'];

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT DISTINCT vac_matriz.matriz as LaMatriz, vac_vacante.IDvacante, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.IDmatriz, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDrequi = 0 AND vac_vacante.IDmatriz IN ($mis_matrizes) AND vac_vacante.IDarea IN ($mis_areas) GROUP BY vac_matriz.matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//fechas
require_once('assets/dias.php');

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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la vacante.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Total de vacantes</h5>
						</div>

					<div class="panel-body">
							<p>A continuación se muestra el total de vacantes por Sucursal y su estatus actual.</p>
							<p>Las vacantes se reportan por el tiempo de cobertura transcurrido de acuerdo a los días que tienen los Jefes de RH para cubrirlos (consultar Manual de RyS). </p>
							<p>Las vacantes con estatus fuera de tiempo (+1 día despues de dias autorizados) y muy fuera de tiempo (+4 días); son atendidas también por la Gerencia Regional y Reclutamiento Corporativo. </p>
			     </div>
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-blue"> 
                    <th>Matriz</th>
                    <th>Total Vacantes</th>
                    <th>Vacantes antes de tiempo</th> 
                    <th>Vacantes a tiempo</th> 
                    <th>Vacantes fuera de tiempo</th> 
                    <th>Vacantes muy fuera de tiempo</th>
                    <th class="bg-warning">Vacantes Temporales</th>
                    </tr> 
                    </thead>
                    <tbody>
                    <?php 
					
					$Xactivas = 0; 
					$Xantes_tiempo = 0;
                    $Xa_tiempo = 0;
                    $Xfuera_tiempo = 0;
                    $Xmuy_fuera_tiempo = 0;
					$AtotalRows_global_temp = 0;
					
					do {
	
						$LaMatriz = $row_lmatriz['LaMatriz'];
						$IDmatriz = $row_lmatriz['IDmatriz'];

						mysql_select_db($database_vacantes, $vacantes);
						$query_global = "SELECT vac_vacante.IDvacante, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus,  vac_vacante.IDmotivo_v, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz WHERE vac_vacante.IDestatus = 1  AND vac_vacante.IDrequi = 0 AND vac_matriz.IDmatriz = '$IDmatriz' AND vac_vacante.IDarea IN ($mis_areas) AND vac_vacante.IDmotivo_v != 3";
						$global = mysql_query($query_global, $vacantes) or die(mysql_error());
						$row_global = mysql_fetch_assoc($global);
						$totalRows_global = mysql_num_rows($global);
						
												mysql_select_db($database_vacantes, $vacantes);
						$query_global_temp = "SELECT vac_vacante.IDvacante, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus,  vac_vacante.IDmotivo_v, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz WHERE vac_vacante.IDestatus = 1  AND vac_vacante.IDrequi = 0 AND vac_matriz.IDmatriz = '$IDmatriz' AND vac_vacante.IDarea IN ($mis_areas) AND vac_vacante.IDmotivo_v = 3";
						$global_temp = mysql_query($query_global_temp, $vacantes) or die(mysql_error());
						$row_global_temp = mysql_fetch_assoc($global_temp);
						$totalRows_global_temp = mysql_num_rows($global_temp);

						$antes_tiempo = 0;
						$a_tiempo = 0;
						$fuera_tiempo = 0;
						$muy_fuera_tiempo = 0;

							do { 

							 $startdate = date('Y/m/d', strtotime($row_global['fecha_requi']));
							 $end_date =  date('Y/m/d');
							 $previo = getWorkingDays($startdate, $end_date, $holidays);
							 $ajuste_dias = $row_global['ajuste_dias'];
							 if ($ajuste_dias != 0) { $previo = $previo - $ajuste_dias; } 
							 if ($previo < 4) {  
							 $antes_tiempo = $antes_tiempo + 1;
							 } else if ($previo <  $row_global['dias']) {   
							 $a_tiempo = $a_tiempo + 1;
							 } else if ($previo < $row_global['dias'] + 4) {  
							 $fuera_tiempo = $fuera_tiempo + 1;
							 } else if ($previo < $row_global['dias'] + 120) {
							 $muy_fuera_tiempo = $muy_fuera_tiempo + 1;  }
							 $activas = $antes_tiempo +  $a_tiempo +  $fuera_tiempo + $muy_fuera_tiempo;

							} while ($row_global = mysql_fetch_assoc($global));
					
					$Xactivas = $Xactivas + $activas; 
					$Xantes_tiempo = $Xantes_tiempo + $antes_tiempo;
                    $Xa_tiempo = $Xa_tiempo + $a_tiempo;
                    $Xfuera_tiempo = $Xfuera_tiempo + $fuera_tiempo;
                    $Xmuy_fuera_tiempo = $Xmuy_fuera_tiempo + $muy_fuera_tiempo;
					$AtotalRows_global_temp = $AtotalRows_global_temp + $totalRows_global_temp;

					
					?>
                    <tr>
                    <td><form method="POST" action="admin_vacantes.php"><input type="hidden" name="la_matriz[]" id="la_matriz[]" value="<?php echo $IDmatriz;?>" />
                    <button type="submit" class="btn btn-info btn-sm"><?php echo $row_lmatriz['LaMatriz']; ?><i class="icon-arrow-right14 position-right"></i></button> </form>  </td>
                    <td align="center"><?php echo $activas + $totalRows_global_temp; ?></td>
                    <td align="center"><?php echo $antes_tiempo; ?></td>
                    <td align="center"><?php echo $a_tiempo; ?></td>
                    <td align="center"><?php echo $fuera_tiempo; ?></td>
                    <td align="center"><?php  echo $muy_fuera_tiempo; ?></td>
                    <td align="center"><?php  echo $totalRows_global_temp; ?></td>
                    </tr>
                    <?php } while($row_lmatriz = mysql_fetch_array($lmatriz)); ?>
                    <tr>
                    <td class="success"><strong>TOTAL</strong></td>
                    <td align="center" class="success"><strong><?php echo $Xactivas + $AtotalRows_global_temp; ?></strong></td>
                    <td align="center" class="success"><strong><?php echo $Xantes_tiempo; ?></strong></td>
                    <td align="center" class="success"><strong><?php echo $Xa_tiempo; ?></strong></td>
                    <td align="center" class="success"><strong><?php echo $Xfuera_tiempo; ?></strong></td>
                    <td align="center" class="success"><strong><?php echo $Xmuy_fuera_tiempo; ?></strong></td>
                    <td align="center" class="success"><strong><?php echo $AtotalRows_global_temp; ?></strong></td>
                    </tr>
                    </tbody>
                   </table> 
                  
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