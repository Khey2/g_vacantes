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
$IDperiodo = $row_variables['IDperiodoN35'];
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
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

if (isset($_POST['IDexamen'])){$_SESSION['IDexamen'] = $_POST['IDexamen'];} else { $_SESSION['IDexamen'] = 1;}
$IDexamen_ = $_SESSION['IDexamen'];

$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuarios = "SELECT * FROM prod_activos WHERE prod_activos.IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$usuarios = mysql_query($query_usuarios, $vacantes) or die(mysql_error());
$row_usuarios = mysql_fetch_assoc($usuarios);
$totalRows_usuarios = mysql_num_rows($usuarios);


if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$IDexam = $row_matriz['nom35_g2'];

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
    <link rel="stylesheet" type="text/css" href="assets/print.css" media="print" />
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la captura manual.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
					<p>Selecciona al empleado para ver el detalle de su resultado de la aplicación de la encuesta de la NOM035.</p>
					<p><a href="admins_n35m.php" class="btn btn-success">Captura Manual</a> &nbsp;  &nbsp;  </p>
					</div>


		<form method="POST" action="admin_n35ep.php">
			<table class="table">
				<tr>
                	<td>
					<select name="IDexamen" class="form-control">Filtro:
					<option value="1" <?php if ($IDexamen_ == 1) {echo "SELECTED";} ?>>Guia 1: Cuestionario de Acontecimientos Traumáticos Severos.</option>
					<?php if($IDexam == 1) { ?>
					<option value="2" <?php if ($IDexamen_ == 2) {echo "SELECTED";} ?>>Guia 2: Cuestionario de factores de Riesgo Psicosocial.</option>
					<?php } else { ?>
					<option value="3" <?php if ($IDexamen_ == 3) {echo "SELECTED";} ?>>Guia 3: Cuestionario de factores de Riesgo Psicosocial y Entorno organizacional.</option>
					<?php } ?>
                     </select>
            		</td>
                    <td>
                    <button type="submit" class="btn btn-primary">Filtrar</button> &nbsp;  <button type="button" class="btn btn-success" onClick="window.location.href='admin_n35e.php'">Capturados</button>
					</td>
					</tr>
		    </table>
		</form>


                    
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>IDempleado</th>
                          <th>Nombre Completo</th>
                          <th>Puesto</th>
                          <th>Sucursal</th>
                          <th>Estatus</th>
                          <th>Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php if ($totalRows_usuarios > 0)  { ?> 
						<?php do { 
						$IDempleado = $row_usuarios['IDempleado']; 
						$query_avance = "SELECT * FROM nom35_resultados WHERE IDempleado = $IDempleado AND IDexamen = $IDexamen_ AND IDperiodo = $IDperiodo";
						$avance = mysql_query($query_avance, $vacantes) or die(mysql_error());
						$row_avance = mysql_fetch_assoc($avance);
						$totalRows_avance = mysql_num_rows($avance);
						if ($totalRows_avance == 0) { ?>
                        <tr>
                          <td><?php echo $row_usuarios['IDempleado']; ?></td>
                          <td><?php echo $row_usuarios['emp_nombre'] . " " . $row_usuarios['emp_paterno'] . " " . $row_usuarios['emp_materno']; ?></td>
                          <td><?php echo $row_matriz['matriz']; ?></td>
                          <td><?php echo $row_usuarios['denominacion']; ?></td>
                          <td>No capturado</td>
                          <td><a class="btn btn-info" href="admins_n35m.php?IDempleado=<?php echo $row_usuarios['IDempleado']; ?>">Capturar</a></td>
                        </tr>                       
                        <?php } } while ($row_usuarios = mysql_fetch_assoc($usuarios)); ?>
						<?php } else { ?>
							<tr>
                          <td colspan="5">No se tienen resultados para el periodo seleccionado.</td>
                        	</tr>                       
						<?php }  ?>

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