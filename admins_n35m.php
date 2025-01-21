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
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$currentPage = $_SERVER["PHP_SELF"];
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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$nom35_g1 = $row_matriz['nom35_g1'];
$nom35_g2 = $row_matriz['nom35_g2'];
$IDexamen = 0;
$IDperiodo = $row_variables['IDperiodoN35'];

if($nom35_g2 == 1) { $IDexamen_ = 2;} else {$IDexamen_ = 3; }

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $IDpregunta = 1;
	$IDexamen = $_POST['IDexamen']; 

	foreach($_POST['IDrespuesta'] as $selected){
		
		$IDempleado = $_POST['IDempleado']; 

		$query_resto = "SELECT * FROM nom35_preguntas WHERE IDpregunta = $IDpregunta AND IDexamen = $IDexamen";
		$resto = mysql_query($query_resto, $vacantes) or die(mysql_error());
		$row_resto = mysql_fetch_assoc($resto);
		$IDcategoria = $row_resto['IDcategoria']; 
		$IDdominio = $row_resto['IDdominio'];
		$IDdimension = $row_resto['IDdimension'];
		$pregunta_tipo = $row_resto['pregunta_tipo'];

		$updateSQL = "INSERT INTO nom35_respuestas (IDempleado, IDexamen, IDpregunta, IDperiodo, respuesta, IDcategoria, IDdominio, IDdimension, pregunta_tipo, manual) VALUES ('$IDempleado', '$IDexamen', '$IDpregunta', '$IDperiodo', '$selected', '$IDcategoria', '$IDdominio', '$IDdimension', '$pregunta_tipo', 1)"; 
		mysql_select_db($database_vacantes, $vacantes);
		$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

		$IDpregunta = $IDpregunta + 1;
	}

	header("Location: admins_n35m2.php?IDempleado=".$IDempleado."&IDperiodo=".$IDperiodo."&IDexamen=".$IDexamen);
}

$avalidar = 0;
if (isset($_POST['IDempleado'])) { 
	
$IDexamen = $_POST['IDexamen']; 
$IDempleado = $_POST['IDempleado'];
$avalidar = 1;

//Preguntas
$query_pregunta = "SELECT * FROM nom35_preguntas WHERE IDexamen = $IDexamen";
mysql_query("SET NAMES 'utf8'");
$pregunta = mysql_query($query_pregunta, $vacantes) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$pregunta_texto = $row_pregunta['pregunta_texto'];

$query_empleado_dob = "SELECT * FROM nom35_resultados WHERE IDempleado = $IDempleado AND IDperiodo = $IDperiodo AND IDexamen = $IDexamen";
$empleado_dob = mysql_query($query_empleado_dob, $vacantes) or die(mysql_error()); 
$row_empleado_dob = mysql_fetch_assoc($empleado_dob);
$totalRows_empleado_dob = mysql_num_rows($empleado_dob);

$query_empleado = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_antiguedad, prod_activos.sueldo_total, prod_activos.emp_nombre, vac_puestos.denominacion, vac_areas.area, vac_matriz.matriz, prod_activos.IDpuesto, prod_activos.IDarea, prod_activos.IDmatriz FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE IDempleado = $IDempleado";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);
$el_estatus = 1;
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

	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/general_widgets_stats.js"></script>
	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>
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
                        <?php if((isset($totalRows_empleado_dob) && ($totalRows_empleado_dob > 0))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El empleado ya tiene esa encuesta capturada en el Periodo actual.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
						<?php if((isset($totalRows_empleado) && ($totalRows_empleado == 0))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El empleado no existe o no está activo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Resultados NOM035 - Captura Manual</h5>
								</div>

								<div class="panel-body">
								<p>Instrucciones.<br/>
								<ul>
									<li>Ingrese las respuestas de acuerdo a la seleccion del colaborador: <strong>4. Siempre | 3. Casi siempre. | 2. Algunas Veces. |  1. Casi nunca. |  0. Nunca.</strong></li>
									<li>Si el empleado no conestó la pregunta por que no aplica, <b>capura 0 como respuesta</b>.</li>
									<li>En la Guía 1, si el empleado conestó la primer pregunta con <b>NO</b>, las demás preguntas no aplican (dejar respuesta en NO).</li>
									<li>Descarga <a href="CONTS/NOM035_encuesta.pdf" class="text text-success text-bold" target="_blank">AQUI</a> la encuesta en formato impreso.</li>
								</ul>
								<p>&nbsp;</p>



							<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
							<fieldset class="content-group">

							<legend class="text-semibold">Empleado</legend>

							<div class="form-group row">
								<div class="col-xs-4">
									<label> No Empleado:</label>
								</div>
								<div class="col-xs-2">

								<?php  if ($avalidar == 1) { ?>
									<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="<?php echo $row_empleado['IDempleado']; ?>">
								<?php } else if (isset($_GET['IDempleado'])) { ?>
									<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="<?php echo $_GET['IDempleado']; ?>">
								<?php } else { ?>
									<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="" required="required">
								<?php }  ?> 
								</div>
							</div>
								<br/>

							<div class="form-group row">
								<div class="col-xs-4">
									<label> Guia:</label>
								</div>
								<div class="col-xs-6">
								<td> 
								<?php  if ($avalidar == 1) { ?>
									<select name="IDexamen" id="IDexamen" class="form-control">
                                    <option value="1"<?php if ($IDexamen == 1) {echo "selected=\"selected\"";} ?>>Guía 1. Acontecimientos Traumáticos Severos.</option>
									<?php  if ($IDexamen_ == 2) { ?>
                                    <option value="2"<?php if ($IDexamen == 2) {echo "selected=\"selected\"";} ?>>Guía 2. Factores de Riesgo Psicosocial.</option>
									<?php } else { ?>
                                    <option value="3"<?php if ($IDexamen == 3) {echo "selected=\"selected\"";} ?>>Guía 3. Factores de Riesgo Psicosocial y Entorno Organizacional.</option>
									<?php  } ?>
									</select>
								<?php } else { ?>
									<select name="IDexamen" id="IDexamen" class="form-control">
                                    <option value="1">Guía 1. Acontecimientos Traumáticos Severos.</option>
									<?php  if ($IDexamen_ == 2) { ?>
                                    <option value="2">Guía 2. Factores de Riesgo Psicosocial.</option>
									<?php } else { ?>
                                    <option value="3">Guía 3. Factores de Riesgo Psicosocial y Entorno organizacional.</option>
									<?php } ?>
									</select>
								<?php }  ?> 
								</div>
							</div>


							    <!-- Basic text input -->
								<div class="form-group row">
										<label class="col-xs-4"></label>
										<div class="col-xs-6">
										<input type="submit" name="KT_valida1" class="btn btn-primary" id="KT_valida1" value="Validar Empleado" />
										<a class="btn btn-default" href="admins_n35m.php">Limpiar filtro</a>
										</div>
									</div>
									<!-- /basic text input -->


								<?php  if ($avalidar == 1) { ?>

                                   <!-- Basic text input -->
									<div class="form-group row">
										<label class="col-xs-4">Nombre Empleado:</label>
										<div class="col-xs-6">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group row">
										<label class="col-xs-4">Matriz:</label>
										<div class="col-xs-6">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['matriz']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->
                                   <!-- Basic text input -->
								   
									<div class="form-group row">
										<label class="col-xs-4">Area:</label>
										<div class="col-xs-6">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['area']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group row">
										<label class="col-xs-4">Puesto:</label>
										<div class="col-xs-6">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['denominacion']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->
																							 						 
						</fieldset>
                        </form>



						<?php if((isset($totalRows_empleado_dob) && ($totalRows_empleado_dob > 0))) { "-"; } else { ?>


						<legend class="text-semibold">Respuestas</legend>
                                
    						<form method="post" id="form1" action="admins_n35m.php" class="form-horizontal form-validate-jquery">

								<fieldset class="content-group">
									
                        <?php do { ?>
                            <div class="form-group row">
                              <div class="col-xs-8">
                                <label>
							                            
                                <?php echo "Pregunta ".$row_pregunta['IDpregunta'];?>
								<?php echo $row_pregunta['pregunta_texto'];?>.
                                
                            </label>
                              </div>
                              <div class="col-xs-2">

							<?php if($IDexamen == 1) { ?>

								<input type="radio" name="IDrespuesta[]<?php echo $row_pregunta['IDpregunta']; ?>" id="respuesta_2" checked="checked" value="5" required="required"/> No &nbsp; &nbsp; &nbsp;
								<input type="radio" name="IDrespuesta[]<?php echo $row_pregunta['IDpregunta']; ?>" id="respuesta_1" value="6" /> Si  

							<?php } else { ?>


							<?php if( $row_pregunta['pregunta_tipo'] == 3 ) {  ?>
									<input type="radio" name="IDrespuesta[]<?php echo $row_pregunta['IDpregunta']; ?>" id="respuesta_1" value="6" required="required" /> Si  &nbsp; &nbsp; &nbsp;
									<input type="radio" name="IDrespuesta[]<?php echo $row_pregunta['IDpregunta']; ?>" id="respuesta_2" value="5" /> No
                            <?php } else { ?>
								<div class="input-group input-group-sm">
                                    <input class="form-control" id="IDrespuesta[]" name="IDrespuesta[]" type="number" maxlength="1" min="0" max="4" required>
								</div>
                            <?php } ?>

							<?php } ?>
                            </div>
                            </div>


							
						<?php } while ($row_pregunta = mysql_fetch_assoc($pregunta)); ?>

								<p>&nbsp;</p>
                                <input type="hidden" name="manual" value="1" />
                                <input type="hidden" name="IDpregunta" value="<?php echo $row_pregunta['IDpregunta']; ?>" />
                                <input type="hidden" name="IDexamen" value="<?php echo $IDexamen; ?>" />
                                <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" />
					            <input type="submit" class="btn bg-primary-700" name="MM_insert" value="Agregar Encuesta" />
                                <a class="btn btn-default" href="admins_n35e.php">Cancelar</a>
								<input type="hidden" name="MM_insert" value="form1" />

								</fieldset>
                            </form>                             
                    
							
							<?php }  } ?>						 

                                
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
			<!-- /main content -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->
</body>
</html>