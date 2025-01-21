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
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


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
$IDusuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

 	 if (isset($_POST['el_tipo']) && ($_POST['el_tipo'] == 1)) {$_SESSION['el_tipo'] = 1; } 
else if (isset($_POST['el_tipo']) && ($_POST['el_tipo'] == 2)) {$_SESSION['el_tipo'] = 2; } 
else  { $_SESSION['el_tipo'] = 1;}
$el_tipo = $_SESSION['el_tipo'];


if ($_SESSION['el_tipo'] == 1) {

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc13, prod_activos.fecha_alta, prod_activos.descripcion_nomina, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDarea, vac_areas.area FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea WHERE prod_activos.IDmatriz = '$la_matriz' AND prod_activos.IDarea in ($mis_areas) ORDER BY prod_activos.IDpuesto ASC";
mysql_query("SET NAMES 'utf8'");
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);


} else {

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT ind_bajas.IDempleado, ind_bajas.emp_paterno, ind_bajas.emp_materno, ind_bajas.emp_nombre, ind_bajas.RFC, ind_bajas.fecha_alta, ind_bajas.fecha_baja, ind_bajas.descripcion_nomina, ind_bajas.descripcion_puesto, ind_bajas.IDmatriz, ind_bajas.IDpuesto, ind_bajas.IDarea, vac_areas.area  FROM ind_bajas LEFT JOIN vac_areas ON vac_areas.IDarea = ind_bajas.IDarea WHERE ind_bajas.IDmatriz = '$la_matriz'  AND ind_bajas.IDarea IN ($mis_areas) AND ind_bajas.baja_anio = 2024 AND ind_bajas.baja_mes > 10 ORDER BY ind_bajas.IDpuesto ASC"; 
mysql_query("SET NAMES 'utf8'");
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

}

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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_inputs.js"></script>
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
									<h6 class="panel-title">Plantilla Activa</h6>
								</div>

								<div class="panel-body">

								<form method="POST" action="plantilla_activos_a.php">
									<table class="table">
										<tr>
											<td>
												<select class="form-control" name="el_tipo">
													<option value="1"<?php if (!(strcmp($el_tipo, 1))) {echo "selected=\"selected\"";} ?>>Activos</option>
													<option value="2"<?php if (!(strcmp($el_tipo, 2))) {echo "selected=\"selected\"";} ?>>Bajas</option>												
												</select>
											</td>
											<td>
											<button type="submit" class="btn btn-primary">Cambiar</button> 
											</td>
										</tr>
									</table>
								</form>


								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>Formatos</th>
                                    <th>IDEmp.</th>
                                    <th>Nombre</th>
                                    <th>Fecha Alta</th>
                                    <?php if ($el_tipo == 2) { ?>
									<th>Fecha Baja</th>
                                    <?php } ?>
                                    <th>Area</th>
                                    <th>Puesto</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><div onClick="loadDynamicContentModal('<?php echo $row_autorizados['IDempleado']; ?>')" class="btn btn-xs btn-primary btn-icon"><i class="icon icon-file-download"></i> Descarga</div></td>
                                      <td><?php echo $row_autorizados['IDempleado']; ?>&nbsp;</td>
                                      <td><a href="prev_plantilla_detalle.php?IDempleado=<?php echo $row_autorizados['IDempleado']; ?>">
									  <?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?></a></td>
                                      <td><?php echo date('d/m/Y', strtotime($row_autorizados['fecha_alta'])); ?></td>
										<?php if ($el_tipo == 2) { ?>
									  <td><?php echo date('d/m/Y', strtotime($row_autorizados['fecha_baja'])); ?></td>
										<?php } ?>
                                      <td><?php echo $row_autorizados['area']; ?>&nbsp; </td>
                                      <td><?php echo $row_autorizados['descripcion_puesto']; ?>&nbsp; </td>
                                    </tr>
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados)); ?>
                                  </tbody>
                                </table>
								</div>
							</div>
						</div>
                                    
					<!-- /Contenido -->

					<!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h5 class="modal-title">Descargas</h5>
								</div>
							<div id="conte-modal">

                            <form method="post" class="form-horizontal form-validate-jquery" name="form1" action="empleados_print23b.php">
								<div class="modal-body">
                                    
								<div class="form-group">
									<label class="control-label col-lg-3">Nombre:</label>
									<div class="col-lg-9">
											<b><?php echo $row_activos['emp_paterno']; ?> <?php echo $row_activos['emp_materno']; ?> <?php echo $row_activos['emp_nombre']; ?></b>
									</div>
								</div>
									

								<div class="form-group">
									<label class="control-label col-lg-3">Fecha Inicio Periodo:</label>
									<div class="col-lg-9">
											<input type="text" name="fecha3" id="fecha3" class="form-control" placeholder="Fecha de inicio del Periodo DD/MM/AAAA." required="required">
									</div>
								</div>


                                    <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
										<button type="submit" class="btn btn-success"  id="send">Imprimir</button>
                                    </div>
                                </div>
							</form>

						</div>
					</div>
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
<?php if ($el_tipo == 1) { ?>

<script>
function loadDynamicContentModal(modal){
	var options = { modal: true };
	$('#conte-modal').load('encuesta2.php?IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
  });  
}
</script> 

<?php } else { ?>

<script>
function loadDynamicContentModal(modal){
	var options = { modal: true };
	$('#conte-modal').load('encuesta3.php?IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
  });  
}
</script> 


<?php } ?>
