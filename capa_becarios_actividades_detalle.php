<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
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
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

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
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

if (date("m") == 1) {$mes_actual = 1;} else {$mes_actual = date("m")-1;}
if (isset($_GET['IDmes'])) {$el_mes = $_GET['IDmes'];} else {$el_mes = $mes_actual;}
if (isset($_GET['anio'])) {$anio = $_GET['anio'];}


$el_becario = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_becario = "SELECT capa_becarios.*,  capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, capa_becarios_evaluacion.IDevaluacion, capa_becarios_evaluacion.IDcalificacion, capa_becarios_evaluacion.anio, capa_becarios_evaluacion.IDmes, vac_meses.mes FROM capa_becarios LEFT JOIN capa_becarios_evaluacion ON capa_becarios.IDempleado = capa_becarios_evaluacion.IDempleado LEFT JOIN vac_meses ON capa_becarios_evaluacion.IDmes = vac_meses.IDmes LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = $el_becario";
mysql_query("SET NAMES 'utf8'");
$becario = mysql_query($query_becario, $vacantes) or die(mysql_error());
$row_becario = mysql_fetch_assoc($becario);
$totalRows_becario = mysql_num_rows($becario);
$IDsubarea = $row_becario['IDsubarea'];
$IDarea = $row_becario['IDarea'];
$IDmatriz_b = $row_becario['IDmatriz'];
$IDsucursal = $row_becario['IDsucursal'];
$IDtipo = $row_becario['IDtipo'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT * FROM capa_becarios_actividades WHERE IDempleado = $el_becario AND IDmes = $el_mes AND anio = $anio";
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar 1
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
foreach($_POST as $key => $value) {
if ($key != 'KT_Update1' AND $key != 'MM_update') {	
$query1 = "UPDATE capa_becarios_actividades SET resultado = '$value', estatus = 1 WHERE IDempleado = $el_becario AND IDmes = $el_mes AND anio = $anio AND IDactividad = $key";
$result1 = mysql_query($query1) or die(mysql_error());  	
  echo "Llave: '$key' Valor: '$value'";
}
}
header("Location: capa_becarios_actividades.php?IDempleado=$el_becario&info=99"); 
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    
    <script src="assets/js/app.js"></script>
	<!-- /Theme JS files -->
 </head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

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
							Se guardó correctamente la evaluación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se actualizó correctamente la evaluación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


	<div class="panel panel-flat">
	<div class="media panel-body no-margin">
		<div class="media-body">
		
								<ul class="media-list">
									<li class="media panel-body stack-media-on-mobile">
										<div class="media-left">
											<a href="#">
												<?php if ($row_becario['Fotografia'] != '') { ?>
												<img src="<?php echo 'becariosfiles//'.$row_becario['ELempleado'].'/'.$row_becario['Fotografia']; ?>" alt="Fotografia" width="80" height="100"><br/>
												<?php } else { ?>
												<img src="files/foto.jpg" alt="Fotografia" width="80" height="100"><br/>
												<?php } ?>
											</a>
										</div>

										<div class="media-body">
											<h6 class="media-heading text-semibold">
												<a href="#"><?php echo $row_becario['emp_paterno']." ". $row_becario['emp_materno']." ". $row_becario['emp_nombre']; ?></a>
											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becario['tipo']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becario['fecha_alta'])); ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Modalidad:</strong> <?php if ($row_becario['IDmodalidad'] == 1) {echo "Presencial";} else if ($row_becario['IDmodalidad'] == 2) {echo "Remoto ";} else {echo "Mixto";} ?></li>
											</ul>												
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Mes de Evaluación:</strong> <?php echo $elmes; ?></li>
												<li><strong>Año de Evaluación:</strong> <?php echo $anio; ?></li>
											</ul>												
										</div>
											
									</li>
								</ul>							
								
				<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
					<span class="display-block text-semibold"><h5>Evaluación de Actividades</h5></span>


                    <table class="table table datatable-condensed table-bordered">
                    <thead> 
                    <tr class="bg-primary-600"> 
                      <th width="50%" class="text-center">Actividad</th>
                      <th class="text-center">Sobresaliente</th>
                      <th class="text-center">Satisfactorio</th>
                      <th class="text-center">Suficiente</th>
                      <th class="text-center">Deficiente</th>
               		 </tr>
                    </thead>
                    <tbody>
							<?php do {	?>

                          <tr>
                            <td><strong>Actividad: </strong><?php echo $row_resultado['actividad']; ?></td>
                            <td><p class="text-center"> <input required type="radio" name="<?php echo $row_resultado['IDactividad']; ?>" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_resultado['resultado'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="<?php echo $row_resultado['IDactividad']; ?>" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_resultado['resultado'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="<?php echo $row_resultado['IDactividad']; ?>" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_resultado['resultado'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="<?php echo $row_resultado['IDactividad']; ?>" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_resultado['resultado'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
						  
							<?php } while ($row_resultado = mysql_fetch_assoc($resultado)); ?>
						  
                            <td colspan="5">
                            	<div class="modal-footer">
                            <input type="submit" name="KT_Update1" class="btn btn-primary" id="KT_Update1" value="Evaluar" />
							<input type="hidden" name="MM_update" value="form1">
							<button type="button" onClick="window.location.href='capa_becarios_actividades.php?IDempleado=<?php echo $row_becario['ELempleado'] ?>'" class="btn btn-default btn-icon">Regresar</button>
			                    </div>
							 </td>
                          </tr>
                     </tbody>
					</table>
                </form>
									
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
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>