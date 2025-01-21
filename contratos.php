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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$fecha_tope = date("d-m-Y",strtotime($fecha."- 30 days"));

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];

if (isset($_POST['mi_areaP'])) {  $_SESSION['mi_areaP'] = $_POST['mi_areaP']; } 
elseif (!isset($_SESSION['mi_areaP'])) {  $_SESSION['mi_areaP'] =  '1,2,3,4,5,6,7,8,9,10'; } 
$mi_areaP = $_SESSION['mi_areaP'];

if (isset($_POST['la_matrizP'])) {  $_SESSION['la_matrizP'] = $_POST['la_matrizP']; } 
elseif (!isset($_SESSION['la_matrizP'])) {  $_SESSION['la_matrizP'] =  $IDmatriz; } 
$la_matrizP = $_SESSION['la_matrizP'];

mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT DISTINCT  prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.emp_nombre, prod_activos.rfc, Count(exp_files.id) AS Docs FROM prod_activos LEFT JOIN exp_files ON prod_activos.IDempleado = exp_files.IDempleado WHERE prod_activos.IDmatriz = $la_matrizP AND prod_activos.IDarea IN ($mi_areaP) GROUP BY prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 12";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
			<!-- Content area -->
				<div class="content">


	                <!-- Content area -->
				<div class="content">
               
               
                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Expendientes Digitales</h5>
						</div>

					<div class="panel-body">
					  <p>A continuación se muesta el listado de empleados activos de la Sucursal <strong><?php echo $row_matriz['matriz']; ?></strong>.</br>
                      En la columna "<strong># Doctos.</strong>" se indica la cantidad de documentos cargados enb el Expediente Digial del empleado.</br>
                      Los Expedientes con "<strong>Fecha de Alta</strong>" marcados en <span class='text text-warning text-bold'>Naranja</span> rebasan el tiempo esperado para su carga.</br>
                      Los Expedientes con "<strong>IDempleado</strong>" marcados en <span class='text text-danger text-bold'>Rojo</span> se deben cargar nuevamente.</br>
                      Utiliza el filtro para cambiar de Sucursal o Área. Da clic en <strong>Editar</strong> para consultar el detalle de documentos cargados y agregar documentos adicionales.</p>



                    <form method="POST" action="expedientes_consulta.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td><div class="col-lg-9">
                                 <select class="form-control" name="el_areaP">
                                   <option value="1,2,3,4,5,6,7,8,9,10">Todas</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_areas['IDarea']?>"<?php if (!(strcmp($row_areas['IDarea'], $mi_areaP)))
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_areas['area']?></option>
                                   <?php
                                  } while ($row_areas = mysql_fetch_assoc($areas));
                                  $rows = mysql_num_rows($areas);
                                  if($rows > 0) {
                                      mysql_data_seek($areas, 0);
                                      $row_areas = mysql_fetch_assoc($areas);
                                  } ?> </select>
						    </div></td>
							<td><div class="col-lg-9">
                                 <select class="form-control" name="la_matrizP">
                                   <option value="1,2,3,4,5,6,7,8,9,10">Todas</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matrizP)))
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                                   <?php
                                  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                                  $rows = mysql_num_rows($lmatriz);
                                  if($rows > 0) {
                                      mysql_data_seek($lmatriz, 0);
                                      $row_lmatriz = mysql_fetch_assoc($lmatriz);
                                  } ?> </select>
						    </div></td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                            </td>
							<td>
                            </td>
					      </tr>
					    </tbody>
				    </table>
				</form>
                
                
                
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>IDempleado</th>
                      <th>Nombre</th>
                      <th>Puesto</th>
                      <th>RFC</th>
                      <th>Fecha Alta</th>
                      <th># Doctos.</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalRows_contratos > 0) { ?>
                        <?php do { ?>
                          <tr>
                            <td><?php $IDempleado_carpeta = 'files/'.$row_contratos['IDempleado']; 
								if (!file_exists($IDempleado_carpeta) AND $row_contratos['Docs'] > 0) {echo "<div class='label label-danger'>".$row_contratos['IDempleado']."</div>";} else {echo $row_contratos['IDempleado'];} ?></td>
                            <td><?php echo $row_contratos['emp_paterno']." ".$row_contratos['emp_materno']." ".$row_contratos['emp_nombre']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['denominacion']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['rfc']; ?>&nbsp; </td>
                            <td><?php $la_fecha = date("d/m/Y", strtotime($row_contratos['fecha_alta'])); if($la_fecha < $fecha_tope AND $row_contratos['Docs'] == 0) {echo "<div class='label label-warning'>".$la_fecha."</div>";} else {echo $la_fecha;} ?></td>
							<td><?php if ($row_contratos['Docs'] > 0) {echo $row_contratos['Docs']." cargados";} else{ echo "Sin carga";} ?></td>
                            <td><a class="btn btn-primary" href="expedientes_nuevo.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>">Editar</a></td>
                          </tr>
                          <?php } while ($row_contratos = mysql_fetch_assoc($contratos)); ?>
                        <?php } else { ?>
                           <td colpsan="6">No hay empleado con el filtro seleccionado.</td>
                        <?php }  ?>
                     </tbody>
					</table>
                      
                      
                      <p>&nbsp;</p>
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