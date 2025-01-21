<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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

$mis_areas = $row_usuario['IDareas'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuarios = "SELECT  vac_usuarios.IDusuario, vac_usuarios.usuario, vac_usuarios.activo, vac_usuarios.usuario_nombre, vac_usuarios.calendario, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, vac_usuarios.nivel_acceso, vac_usuarios.IDmatrizes, vac_matriz.matriz, vac_puestos.denominacion, prod_activos.IDempleado FROM vac_usuarios LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = vac_usuarios.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_usuarios.IDusuario_puesto LEFT JOIN prod_activos ON prod_activos.IDempleado = vac_usuarios.IDusuario ";
mysql_query("SET NAMES 'utf8'");
$usuarios = mysql_query($query_usuarios, $vacantes) or die(mysql_error());
$row_usuarios = mysql_fetch_assoc($usuarios);
$totalRows_usuarios = mysql_num_rows($usuarios);
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 
$IDmatrizes = $row_usuario['IDmatrizes'];

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

	<script src="assets/js/app.js"></script>
   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
    <!-- /theme JS files -->
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
	<?php require_once('assets/mainnav.php'); ?>
	<!-- Page container -->
	<div class="page-container">

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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El usuario ya existe.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona al usuario para reasignar matrices o cambiar sus información.</p>
					</div>


					<!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-primary" href="master_admin_usuarios_edit.php">Agregar Usuario<i class="icon-arrow-right14 position-right"></i></a>
                    </div>
					</div>
					<!-- /colored button -->                   

			     		<table class="table datatable-show-all">
						<thead>
						 <tr class="bg-blue">
                          <th>Usuario</th>
                          <th>No. Emp</th>
                          <th>Nombre Completo</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
					      <th>Activo</th>
					      <th>Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_usuarios['usuario']; ?></td>
                          <td><?php echo $row_usuarios['IDusuario']; ?></td>
                          <td><?php echo $row_usuarios['usuario_nombre'] . " " . $row_usuarios['usuario_parterno'] . " " . $row_usuarios['usuario_materno']; ?>
						  <?php if($row_usuarios['IDempleado'] == '') { echo "*" ;}?></td>
                          <td><?php echo $row_usuarios['denominacion']; ?></td>
                          <td><?php echo $row_usuarios['matriz']; ?></td>
                          <td><?php if($row_usuarios['activo'] == 1) { echo "SI"; } else { echo "NO"; } ?></td>
                          <td>
                         <button type="button" class="btn btn-primary" onClick="window.location.href='master_admin_usuarios_edit.php?IDusuario=<?php echo $row_usuarios['IDusuario']; ?>'">E</button> 
                         <button type="button" data-target="#modal_theme_danger<?php echo $row_usuarios['IDusuario']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">B</button>
                         <button type="button" class="btn bg-indigo btn-icon" onClick="window.location.href='master_admin_usuarios_asigdar.php?IDusuario=<?php echo $row_usuarios['IDusuario']; ?>'">M</button>
                         <button type="button" class="btn bg-indigo btn-icon" onClick="window.location.href='master_admin_usuarios_asignar.php?IDusuario=<?php echo $row_usuarios['IDusuario']; ?>'">A</button>
                         <button type="button" class="btn bg-success btn-icon" onClick="window.location.href='master_admin_usuarios_asigdar_kpis.php?IDusuario=<?php echo $row_usuarios['IDusuario']; ?>'">
                         Mk</button>
                         <button type="button" class="btn bg-success btn-icon" onClick="window.location.href='master_admin_usuarios_asignar_kpis.php?IDusuario=<?php echo $row_usuarios['IDusuario']; ?>'">
                         Ak</button>
                         </td>
                        </tr>      

                  <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_usuarios['IDusuario']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el usuario?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="master_admin_usuarios_edit.php?IDusuario=<?php echo $row_usuarios['IDusuario']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
						
                        <?php } while ($row_usuarios = mysql_fetch_assoc($usuarios)); ?>
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
<?php
mysql_free_result($variables);

mysql_free_result($usuarios);
?>
