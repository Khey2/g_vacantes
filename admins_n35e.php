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
mysql_query("SET NAMES 'utf8'");
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

if (isset($_POST['la_matriz'])) { foreach ($_POST['la_matriz'] as $matrizes)
    {	$_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);} } 
if (!isset($_SESSION['la_matriz'])) { $_SESSION['la_matriz'] = $IDmatriz;}

$la_matriz = $_SESSION['la_matriz'];

if (isset($_POST['IDexamen'])){$_SESSION['IDexamen'] = $_POST['IDexamen'];} else{ $_SESSION['IDexamen'] = 1;}
$IDexamen_ = $_SESSION['IDexamen'];


$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuarios = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.denominacion, nom35_resultados.IDresultado, nom35_resultados.IDexamen, vac_matriz.matriz FROM prod_activos LEFT JOIN nom35_resultados ON nom35_resultados.IDempleado = prod_activos.IDempleado LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE nom35_resultados.IDperiodo = $IDperiodo AND nom35_resultados.IDexamen = $IDexamen_ AND prod_activos.IDmatriz IN ($la_matriz)";
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

mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

// borrar alternativo
if (isset($_GET['duplicados'])) {
  
	$deleteSQL = "DELETE t1 FROM nom35_resultados t1 JOIN nom35_resultados t2 ON t2.IDempleado = t1.IDempleado AND t1.IDexamen = t2.IDexamen AND t2.IDresultado < t1.IDresultado WHERE t1.IDmatriz IN ($la_matriz)";
  	mysql_select_db($database_vacantes, $vacantes);
	$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

	//$deleteSQL2 = "DELETE t1 FROM nom35_respuestas t1 JOIN nom35_respuestas t2 ON t2.IDempleado = t1.IDempleado AND t1.IDexamen = t2.IDexamen AND t1.IDpregunta = t2.IDpregunta AND t2.IDrespuesta < t1.IDrespuesta WHERE t1.IDmatriz IN ($la_matriz)";
  	//mysql_select_db($database_vacantes, $vacantes);
	//$result = mysql_query($deleteSQL2, $vacantes) or die(mysql_error());

	header("Location: admins_n35e.php?info=1");
  }
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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/print.css" media="print" />
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
						<?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-info alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han borrado correctamente los repetidos.
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
							<p><b>Sucursal actual:</b> <?php 
							$cadena3 = $la_matriz; $array3 = explode(", ", $cadena3); 
							foreach ($array3 as $lamatriz2) {
							$query_mmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz  = $lamatriz2";
							$mmatriz = mysql_query($query_mmatriz, $vacantes) or die(mysql_error());
							$row_mmatriz = mysql_fetch_assoc($mmatriz);
							echo $row_mmatriz['matriz']."; ";
							} ?></p>

        <form method="POST" action="admins_n35e.php">
			<table class="table">
				<tr>
                	<td> <select name="la_matriz[]" class="multiselect" multiple="multiple">
                            <?php $cadena2 = $la_matriz; $array2 = explode(", ", $cadena2);  do { ?>
                            <option value="<?php echo $row_amatriz['IDmatriz']?>"<?php foreach ($array2 as $lamatriz) { if (!(strcmp($row_amatriz['IDmatriz'], $lamatriz))) {echo "SELECTED";} } ?>><?php echo $row_amatriz['matriz']?></option>
                            <?php
                            } while ($row_amatriz = mysql_fetch_assoc($amatriz));
                                $rows = mysql_num_rows($amatriz);
                                if($rows > 0) {
                                    mysql_data_seek($amatriz, 0);
                                    $row_amatriz = mysql_fetch_assoc($amatriz);
                            } ?>
                     </select>
            		</td>
                    <td>
					<select name="IDexamen" class="form-control">Filtro:
					<option value="1" <?php if ($IDexamen_ == 1) {echo "SELECTED";} ?>>Guia 1: Cuestionario de Acontecimientos Traumáticos Severos.</option>
					<option value="2" <?php if ($IDexamen_ == 2) {echo "SELECTED";} ?>>Guia 2: Cuestionario de factores de Riesgo Psicosocial.</option>
					<option value="3" <?php if ($IDexamen_ == 3) {echo "SELECTED";} ?>>Guia 3: Cuestionario de factores de Riesgo Psicosocial y Entorno organizacional.</option>
                     </select>
            		</td>

                    <td>
                    <button type="submit" class="btn btn-primary">Filtrar</button> &nbsp;
					<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar duplicados</button>
					</td>
					</tr>
		    </table>
		</form>



                  <!-- danger modal -->
				  <div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar duplicados?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admins_n35e.php?duplicados=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
                  <!-- danger modal -->



					</div>
                    
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>IDempleado</th>
                          <th>Nombre Completo</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
                          <th>Encuesta</th>
                          <th>Estatus</th>
                          <th>Manual</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php if ($totalRows_usuarios > 0)  { ?> 
						<?php do { ?>
                        <tr>
                          <td><?php echo $row_usuarios['IDempleado']; ?></td>
                          <td><?php echo $row_usuarios['emp_nombre'] . " " . $row_usuarios['emp_paterno'] . " " . $row_usuarios['emp_materno']; ?></td>
                          <td><?php echo $row_usuarios['denominacion']; ?></td>
                          <td><?php echo $row_usuarios['matriz']; ?></td>
                          <td><?php echo "Guia ".$row_usuarios['IDexamen']; ?></td>
                          <td><?php 
						  
						  $IDempleado = $row_usuarios['IDempleado']; 
						  $IDexamen = $row_usuarios['IDexamen']; 

							if($IDexamen == 1) {

							$query_resultado = "SELECT nom35_respuestas.respuesta AS Respuesta, nom35_respuestas.manual FROM nom35_respuestas WHERE IDperiodo = '$IDperiodo' AND IDempleado = '$IDempleado' AND IDexamen = '$IDexamen' AND IDpregunta = 1";
							$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
							$row_resultado = mysql_fetch_assoc($resultado);
							$totalRows_resultado = mysql_num_rows($resultado);
	
							} else {

							$query_resultado = "SELECT nom35_respuestas.*, SUM(nom35_respuestas.respuesta) AS Respuesta FROM nom35_respuestas WHERE IDperiodo = '$IDperiodo' AND IDempleado = '$IDempleado' AND IDexamen = '$IDexamen'";
							$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
							$row_resultado = mysql_fetch_assoc($resultado);
							$totalRows_resultado = mysql_num_rows($resultado);
	  
							}

						  
						  if ($row_resultado['Respuesta'] != 5 AND $IDexamen == 1) {echo "<span class='text text-danger'>Si</i>";} 
						  if ($row_resultado['Respuesta'] == 5 AND $IDexamen == 1) {echo "<span class='text text-success'>No</i>";} 

						  if ($row_resultado['Respuesta'] <= 20 AND $IDexamen == 2) {echo "<span class='text text-info'>Nulo</i>";} 
						  if (($row_resultado['Respuesta'] > 20 AND $row_resultado['Respuesta'] <= 45) AND $IDexamen == 2) {echo "<span class='text text-success'>Bajo</i>";} 
						  if (($row_resultado['Respuesta'] > 45 AND $row_resultado['Respuesta'] <= 70) AND $IDexamen == 2) {echo "<span class='text text-warning'>Medio</i>";} 
						  if (($row_resultado['Respuesta'] > 70 AND $row_resultado['Respuesta'] <= 90) AND $IDexamen == 2) {echo "<span class='text text-warning'>Alto</i>";} 
						  if ($row_resultado['Respuesta'] > 90 AND $IDexamen == 2) {echo "<span class='text text-danger'>Muy Alto</i>";} 
						  
						  if ($row_resultado['Respuesta'] <= 50 AND $IDexamen == 3) {echo "<span class='text text-info'>Nulo</i>";} 
						  if (($row_resultado['Respuesta'] > 50 AND $row_resultado['Respuesta'] <= 75) AND $IDexamen == 3) {echo "<span class='text text-success'>Bajo</i>";} 
						  if (($row_resultado['Respuesta'] > 75 AND $row_resultado['Respuesta'] <= 99) AND $IDexamen == 3) {echo "<span class='text text-warning'>Medio</i>";} 
						  if (($row_resultado['Respuesta'] > 99 AND $row_resultado['Respuesta'] <= 140) AND $IDexamen == 3) {echo "<span class='text text-warning'>Alto</i>";} 
						  if ($row_resultado['Respuesta'] > 140 AND $IDexamen == 3) {echo "<span class='text text-danger'>Muy Alto</i>";} 
						  
						 ?></td>
                         <td><?php if ($row_resultado['manual'] == 1){ echo "Si";} else { echo "No";} ?></td>
                         <td>

						 <?php if ($row_usuarios['IDexamen'] == 1) { ?>
								<button type="button" class="btn btn-primary" onClick="window.location.href='admins_n35e_resultado2.php?IDresultado=<?php echo $row_usuarios['IDresultado']; ?>'">Ver Detalle</button>
								<button type="button" class="btn btn-success" onClick="window.location.href='admins_n35e_resultado2_print.php?IDresultado=<?php echo $row_usuarios['IDresultado']; ?>'">Imprimir</button>
						<?php } else { ?>
								<button type="button" class="btn btn-primary" onClick="window.location.href='admins_n35e_resultado.php?IDresultado=<?php echo $row_usuarios['IDresultado']; ?>'">Ver Detalle</button>
								<button type="button" class="btn btn-success" onClick="window.location.href='admins_n35e_resultado_print.php?IDresultado=<?php echo $row_usuarios['IDresultado']; ?>'">Imprimir</button>
						<?php } ?>
						</td>
                        </tr>                       
                        <?php } while ($row_usuarios = mysql_fetch_assoc($usuarios)); ?>
						<?php } else { ?>
							<tr>
                          <td colspan="5">No se tienen resultados para la matriz seleccionada.</td>
                        	</tr>                       
						<?php }  ?>

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
