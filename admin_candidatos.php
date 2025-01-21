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

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];$la_matriz = $row_usuario['IDmatriz'];
$las_matrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];

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
$query_candidatos = "SELECT cv_activos.IDusuario, cv_activos.a_paterno, cv_activos.a_rfc,  cv_activos.d_colonia,  cv_activos.bienes, cv_activos.IDescolaridad, cv_activos.a_materno, cv_activos.a_nombre, cv_activos.activo, cv_activos.puesto, cv_activos.fecha_captura, cv_estados.estado as Estado, vac_matriz.IDmatriz, vac_matriz.matriz FROM cv_activos left JOIN cv_estados ON cv_estados.estado_ = cv_activos.IDestado left JOIN vac_matriz ON vac_matriz.estado = cv_estados.estado_ ";
mysql_query("SET NAMES 'utf8'");
$candidatos = mysql_query($query_candidatos, $vacantes) or die(mysql_error());
$row_candidatos = mysql_fetch_assoc($candidatos);
$totalRows_candidatos = mysql_num_rows($candidatos);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);

$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);

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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/select.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_print.js"></script>
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la Matriz.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Candidatos registrados</h5>
						</div>

					<div class="panel-body">
							<p>A continuación se los muestran los candidatos registrados en el sistema para la Sucursal <?php echo $row_matriz['matriz']?>.</br>
                      <p>&nbsp;</p>
					  
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Folio</th>
							    <th>Nombre</th>
							    <th>Puesto</th>
							    <th>Fecha captura</th>
							    <th>Estado residencia</th>
							    <th>Secciones</th>
							    <th class="text-center">Acciones</th>
						    </tr>
					    </thead>
						<tbody>							  
						<?php if ($totalRows_candidatos > 0) { ?>
						<?php do { ?>
							<tr>
							<td><?php echo $row_candidatos['IDusuario']; ?>&nbsp; </td>
							<td><?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno'] . " " . $row_candidatos['a_nombre']; ?>&nbsp; </td>
							<td><?php echo $row_candidatos['puesto']; ?>&nbsp; </td>
							<td><?php echo $row_candidatos['fecha_captura']; ?>&nbsp; </td>
							<td><?php echo $row_candidatos['Estado']; ?></td>
							<td>
                            
                            
                            <?php
$IDusuario = $row_candidatos['IDusuario'];
							
$query_candidatos_1 = "SELECT Count(cv_activos.IDusuario) as Total, cv_activos.IDusuario, cv_dependientes.IDref FROM cv_activos INNER JOIN cv_dependientes ON cv_dependientes.IDusuario = cv_activos.IDusuario WHERE cv_dependientes.IDref = 'f' AND cv_activos.IDusuario = '$IDusuario' AND cv_activos.borrado = 0";
$candidatos_1 = mysql_query($query_candidatos_1, $vacantes) or die(mysql_error());
$row_candidatos_1 = mysql_fetch_assoc($candidatos_1);

$query_candidatos_2 = "SELECT Count(cv_activos.IDusuario) as Total, cv_activos.IDusuario, cv_dependientes.IDref FROM cv_activos INNER JOIN cv_dependientes ON cv_dependientes.IDusuario = cv_activos.IDusuario WHERE cv_dependientes.IDref = 'e' AND cv_activos.IDusuario = '$IDusuario' AND cv_activos.borrado = 0";
$candidatos_2 = mysql_query($query_candidatos_2, $vacantes) or die(mysql_error());
$row_candidatos_2 = mysql_fetch_assoc($candidatos_2);

$query_candidatos_3 = "SELECT Count(cv_activos.IDusuario )as Total, cv_activos.IDusuario, cv_dependientes.IDref FROM cv_activos INNER JOIN cv_dependientes ON cv_dependientes.IDusuario = cv_activos.IDusuario WHERE cv_dependientes.IDref = 'r' AND cv_activos.IDusuario = '$IDusuario' AND cv_activos.borrado = 0";
$candidatos_3 = mysql_query($query_candidatos_3, $vacantes) or die(mysql_error());
$row_candidatos_3 = mysql_fetch_assoc($candidatos_3);
							
	?>
    <ul>
    <li>Datos Personales
	<?php if ($row_candidatos['a_rfc'] != NULL)
	{echo "<i class='icon-checkmark4 icon-primary'></i> Captuardo";} 
	else {echo "<i class='icon-notification2'></i> Pendiente";} ?>
    </li>  
    <li>Datos Generales
	<?php if ($row_candidatos['d_colonia'] != NULL)
	{echo "<i class='icon-checkmark4'></i> Capturado";}
	else {echo "<i class='icon-notification2'></i> Pendiente";} ?>
    </li> 
    <li>Datos Escolares
	<?php if ($row_candidatos['IDescolaridad'] != NULL)
	{echo "<i class='icon-checkmark4'></i> Capturado";}
	else {echo "<i class='icon-notification2'></i> Pendiente";} ?>
    </li>  
    <li>Datos Familiares
	<?php if ($row_candidatos_1['Total'] > 0)
	{echo " <i class='icon-checkmark4'></i> Capturado (personas reportadas: " . $row_candidatos_1['Total'] . ")";}
	else {echo "<i class='icon-notification2'></i> Pendiente";} ?></div></div>
    </li> 
    <li>Datos Económicos
	<?php if ($row_candidatos['bienes'] != NULL) 
	{echo "<i class='icon-checkmark4'></i> Capturado";}
	else {echo "<i class='icon-notification2'></i> Pendiente";} ?>
    </li>  
   	<li> Empleos
	<?php if ($row_candidatos_2['Total'] > 0)
	{echo "<i class='icon-checkmark4'></i> Capturado (empleos reportados: " . $row_candidatos_2['Total'] . ")";}
	else {echo "<i class='icon-notification2'></i> Pendiente";} ?>
    </li>  
    <li>Referencias
	<?php if ($row_candidatos_3['Total'] > 0)
	{echo "<i class='icon-checkmark4'></i> Capturado (referencias reportadas: " . $row_candidatos_3['Total'] . ")";}
	else {echo "<i class='icon-notification2'></i> Pendiente";}
	?>
    </li>
    </ul>
                            </td>
							<td>
                         <div class="btn-group">
								<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">Consultas<span class="caret"></span></button>
									<ul class="dropdown-menu">
										<li><a href="cv_a.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Datos Personales</a></li>
										<li><a href="cv_b.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Datos Generales</a></li>
										<li><a href="cv_c.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Datos Escolares</a></li>
										<li><a href="cv_d.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Datos Familiares</a></li>
										<li><a href="cv_e.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Datos Económicos</a></li>
										<li><a href="cv_f.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Empleos</a></li>
										<li><a href="cv_g.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Referencias</a></li>
									</ul>
							</div>
                            <button type="button" class="btn btn-info btn-icon" onClick="window.location.href='candidato_reporte.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>'">Descargar Reporte</button>
                            </td>
						    </tr>
					    <?php } while ($row_candidatos = mysql_fetch_assoc($candidatos)); ?>
					    <?php } else { ?>
                         <tr><td colspan="7">No se encontraron candidatos registrados</td></tr>
					    <?php } ?>
					    </tbody>
				    </table>
                      <p>&nbsp;</p>
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
<?php
mysql_free_result($variables);
?>
