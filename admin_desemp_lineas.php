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
$IDperiodovar = $row_variables['IDperiodo'];


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


$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$query_periodos = "SELECT * FROM sed_periodos_sed"; 
mysql_query("SET NAMES 'utf8'");
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];

if (isset($_POST['IDmatriz'])) {$_SESSION['IDmatriz'] = $_POST['IDmatriz'];} 
elseif (!isset($_SESSION['IDmatriz'])){$_SESSION['IDmatriz'] = $IDmatriz;}

$IDmatriz = $_SESSION['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, boss.IDempleado AS IDempleadoJ, boss.emp_paterno AS emp_paternoJ, boss.emp_materno AS emp_maternoJ, boss.emp_nombre AS emp_nombreJ, boss.denominacion AS denominacionJ FROM prod_activos left JOIN prod_activos AS boss ON prod_activos.IDempleadoJ = boss.IDempleado WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDaplica_SED = 1";
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

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

	<script src="assets/js/app.js"></script>
   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el usuario.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona el resultado que requiera editar.</p>
							<p>Especial puede evaluar aún cuando esté cerrado el Periodo.</p>
							<p><a href="admin_desemp_lineas2.php">Ver todos los que no están asignados.</a></p>
					</div>
                    
                    
                    
                    <form method="POST" action="admin_desemp_lineas.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <div class="col-lg-9"><strong>Matriz:</strong>
                                             <select name="IDmatriz" class="form-control">
                                               <?php do {  ?>
                                               <option value="<?php echo $row_matrizes['IDmatriz']?>"<?php if (!(strcmp($row_matrizes['IDmatriz'], $IDmatriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_matrizes['matriz']?></option>
                                               <?php
											  } while ($row_matrizes = mysql_fetch_assoc($matrizes));
											  $rows = mysql_num_rows($matrizes);
											  if($rows > 0) {
												  mysql_data_seek($matrizes, 0);
												  $row_matrizes = mysql_fetch_assoc($matrizes);
											  } ?></select>
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
						 <tr class="bg-blue">
                          <th>Nombre Empelado</th>
                          <th>IDempleado</th>
                          <th>Puesto Empleado</th>
                          <th>Nombre Jefe</th>
                          <th>IDempleado</th>
                          <th>Puesto Jefe</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_resultados['emp_paterno'] . " " . $row_resultados['emp_materno'] . " " . $row_resultados['emp_nombre']; ?></td>
                          <td><?php echo $row_resultados['IDempleado']; ?></td>
                          <td><?php echo $row_resultados['denominacion']; ?></td>
                          <td><?php echo $row_resultados['emp_paternoJ'] . " " . $row_resultados['emp_maternoJ'] . " " . $row_resultados['emp_nombreJ']; ?></td>
                          <td><?php echo $row_resultados['IDempleadoJ']; ?></td>
                          <td><?php echo $row_resultados['denominacionJ']; ?></td>
                         <td><button type="button" class="btn btn-primary" onClick="window.location.href='admin_desemp_lineas_edit.php?IDempleado=<?php echo $row_resultados['IDempleado']; ?>'">Editar</button>
                        </td>
                        </tr>                       
                        <?php } while ($row_resultados = mysql_fetch_assoc($resultados)); ?>
                   	</tbody>							  
                 </table>

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