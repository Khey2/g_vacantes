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
$fecha = date("d-m-Y"); // la fecha actual

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
$las_matrizes = $row_usuario['IDmatrizes'];
$usrr = $row_usuario['IDusuario'];


mysql_select_db($database_vacantes, $vacantes);
$query_candidatos = "SELECT cv_activos.IDusuario, cv_activos.a_paterno, cv_activos.capturador, cv_activos.a_materno, cv_activos.a_nombre, cv_activos.enviado_msg, cv_activos.enviado_mail, vac_puestos.IDarea, cv_activos.fecha_captura, cv_activos.a_correo,  cv_activos.fecha_entrevista, cv_activos.hora_entrevista, cv_activos.IDentrevista, cv_activos.IDmatriz, cv_activos.IDpuesto, cv_activos.estatus, cv_activos.tipo, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area, vac_matriz.matriz FROM cv_activos left JOIN vac_puestos ON vac_puestos.IDpuesto = cv_activos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = vac_puestos.IDarea  LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = cv_activos.IDmatriz WHERE cv_activos.borrado != 0 AND (cv_activos.IDmatriz = '$la_matriz' OR cv_activos.capturador = '$usrr')";
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

$query_puestos = "SELECT cv_activos.IDpuesto, vac_puestos.denominacion FROM cv_activos LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = cv_activos.IDpuesto WHERE cv_activos.IDmatriz = '$la_matriz' AND cv_activos.IDpuesto IS NOT NULL GROUP BY cv_activos.IDpuesto";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);

$query_area = "SELECT cv_activos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM cv_activos LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = cv_activos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = vac_puestos.IDarea WHERE cv_activos.IDmatriz = '$la_matriz' GROUP BY vac_puestos.IDarea";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
    
    
   	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
    
    
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

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Candidatos registrados</h5>
						</div>

					<div class="panel-body">
							<p>A continuación se los muestran los candidatos borrados en el sistema para la Sucursal <?php echo $row_matriz['matriz']?>.</p>

                            
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Folio</th>
							    <th>Nombre</th>
							    <th>Puesto</th>
							    <th>Matriz</th>
							    <th>Área</th>
							    <th>Reclutador</th>
							    <th>Fecha<br/>Entrevista</th>
							    <th>Estatus</th>
						    </tr>
					    </thead>
						<tbody>							  
						<?php if ($totalRows_candidatos > 0) { ?>
						<?php do { 

						$capturador = $row_candidatos['capturador'];
						if ($capturador != 0) {	
						$query_reclutador = "SELECT * FROM vac_usuarios WHERE IDusuario = $capturador";
						$reclutador = mysql_query($query_reclutador, $vacantes) or die(mysql_error());
						$row_reclutador = mysql_fetch_assoc($reclutador);
						$el_reclutador = $row_reclutador['usuario_nombre']." ".$row_reclutador['usuario_parterno'];	
						} else {$el_reclutador = "Candidato";}
						?>
                        
							<tr>
							<td><?php echo $row_candidatos['IDusuario']; ?></td>
							<td><?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno'] . " " . $row_candidatos['a_nombre']; ?>&nbsp; 
                             <?php if ($row_candidatos['enviado_msg'] == 1) { echo "<i class='icon-comment text-primary'></i>";} ?>
                             <?php if ($row_candidatos['enviado_mail'] == 1) { echo "<i class='icon-mail5 text-primary'></i>";} ?>
                             </td>
							<td><?php echo $row_candidatos['denominacion']; ?>&nbsp; </td>
							<td><?php echo $row_candidatos['matriz']; ?>&nbsp; </td>
							<td><?php echo $row_candidatos['area']; ?>&nbsp; </td>
							<td><?php echo $el_reclutador; ?>&nbsp; </td>
							<td>
							<?php echo  date('d/m/Y', strtotime($row_candidatos['fecha_entrevista'])) ." | ". date('g:i A', strtotime($row_candidatos['hora_entrevista']))  ?>
                            <?php if (date('d/m/Y', strtotime($row_candidatos['fecha_entrevista'])) == date('d/m/Y', strtotime($fecha))) { echo "<i class='icon-notification2 text-danger'></i>";}
						      elseif( date('d/m/Y', strtotime($row_candidatos['fecha_entrevista'])) == date('d/m/Y', strtotime($fecha."+ 1 days")))  { echo "<i class='icon-notification2 text-success'></i>";}
						      elseif( date('d/m/Y', strtotime($row_candidatos['fecha_entrevista'])) == date('d/m/Y', strtotime($fecha."+ 2 days")))  { echo "<i class='icon-notification2 text-success'></i>";}
							 ?>
                              </td>
							<td><?php      if($row_candidatos['IDentrevista'] == 1) {echo "<span class='label label-primary'>Si es viable</span>";}
									  else if($row_candidatos['IDentrevista'] == 2) {echo "<span class='label label-warning'>No es viable</span>";}
									  else if($row_candidatos['IDentrevista'] == 3) {echo "<span class='label label-danger'>No contestó</span>";}
									  else if($row_candidatos['IDentrevista'] == 0) {echo "<span class='label label-default'>No definido</span>";}
									  else {echo "<span class='label label-default'>No definido</span>";} ?>&nbsp; </td>
					    <?php } while ($row_candidatos = mysql_fetch_assoc($candidatos)); ?>
					    <?php } else { ?>
					     </tr>
                        <td colspan="6">No se encontraron candidatos registrados</td>
					     </tr>
						 <?php } ?>
					    </tbody>
				    </table>

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