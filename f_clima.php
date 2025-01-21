<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');


// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDempleado'];
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }


$IDpuesto = $row_usuario['IDpuesto'];
$IDarea = $row_usuario['IDarea'];
$IDsucursal = $row_usuario['IDsucursal'];
$_SESSION['IDmatriz'] = $IDmatriz;

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $IDmatriz AND estatus = 1";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);
$IDperiodo = $row_periodos['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_elperiodo = "SELECT * FROM sed_clima_periodos WHERE IDperiodo = $IDperiodo";
mysql_query("SET NAMES 'utf8'");
$elperiodo = mysql_query($query_elperiodo, $vacantes) or die(mysql_error());
$row_elperiodo = mysql_fetch_assoc($elperiodo);
$totalRows_elperiodo = mysql_num_rows($elperiodo); 

//Si tiene resultados
mysql_select_db($database_vacantes, $vacantes);
$query_clima = "SELECT * FROM sed_clima WHERE IDempleado = '$el_usuario' AND IDperiodo = '$IDperiodo' AND IDpregunta = 1";
$clima = mysql_query($query_clima, $vacantes) or die(mysql_error());
$row_clima = mysql_fetch_assoc($clima);
$totalRows_clima = mysql_num_rows($clima);
$el_boss = $row_clima['IDrespuesta'];
$el_user_matriz = $row_clima['IDmatriz'];

//si no esta su jefe en sucursal
mysql_select_db($database_vacantes, $vacantes);
$query_boss_matriz = "SELECT * FROM prod_activos WHERE IDempleado = '$el_boss'";
$boss_matriz = mysql_query($query_boss_matriz, $vacantes) or die(mysql_error());
$row_boss_matriz = mysql_fetch_assoc($boss_matriz);
$totalRows_boss_matriz = mysql_num_rows($boss_matriz);
$el_boss_matriz = $row_boss_matriz['IDmatriz'];
if ($el_user_matriz == $el_boss_matriz){ $diferente_matriz = 0;} else { $diferente_matriz = 1;}

$los_puestos = "87, 145, 146, 147, 148, 149, 150, 120, 250, 252, 95, 96, 176, 253, 254, 121, 154, 177, 97, 98, 203, 221, 211, 202, 209, 255, 220, 207, 227, 232, 218, 219, 222, 204, 225, 214, 217, 233, 256, 215, 234, 272, 241, 257, 205, 224, 262, 223, 261, 258, 208, 231, 216, 99, 100, 101, 102, 122, 10, 123, 36, 103, 124, 37, 125, 180, 181, 126, 11, 12, 13, 182, 201, 127, 128, 129, 51, 130, 131, 183, 184, 265, 264, 266, 267, 191, 213, 192, 17, 270, 56, 58, 193, 198, 235, 237, 238, 239, 240";

// select para Jefe
if(isset($_GET['noboss']) OR $diferente_matriz == 1) {
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE (IDempleado <> '$el_usuario' AND IDpuesto IN ($los_puestos)) OR manual IS NOT NULL  ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);

} else {
	
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE (IDempleado <> '$el_usuario' AND IDmatriz = '$la_matriz' AND IDpuesto IN ($los_puestos)) OR manual IS NOT NULL AND IDmatriz = '$la_matriz' ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);
}

// saber si se muestran resultados
$resultados = $row_matriz['clima'];


//Ultima Respuesta
mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_ultima = "SELECT DISTINCT Max(sed_clima.IDpregunta) AS IDpregunta FROM sed_clima WHERE IDempleado = '$el_usuario' AND IDperiodo = '$IDperiodo'";
$respuesta_ultima = mysql_query($query_respuesta_ultima, $vacantes) or die(mysql_error());
$row_respuesta_ultima = mysql_fetch_assoc($respuesta_ultima);
$totalRows_respuesta_ultima = mysql_num_rows($respuesta_ultima);

// Para Ubicarse
$la_pregunta_ultima = $row_respuesta_ultima['IDpregunta'];
if(isset($_GET['IDpregunta'])) {$la_pregunta_actual = $_GET['IDpregunta'];} else {$la_pregunta_actual = 1;}
$la_pregunta_siguiente = $la_pregunta_actual + 1;
$la_pregunta_anterior = $la_pregunta_actual - 1;

//Preguntas
$query_pregunta = "SELECT * FROM sed_clima_preguntas WHERE IDpregunta = '$la_pregunta_actual'";
mysql_query("SET NAMES 'utf8'");
$pregunta = mysql_query($query_pregunta, $vacantes) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$pregunta_texto = $row_pregunta['pregunta_texto'];
$pregunta_dimension = $row_pregunta['pregunta_dimension'];

$query_maxima = "SELECT MAX(IDpregunta) AS max_preg FROM sed_clima_preguntas";
$maxima = mysql_query($query_maxima, $vacantes) or die(mysql_error());
$row_maxima = mysql_fetch_assoc($maxima);
$max_preg = $row_maxima['max_preg'];

//Respuestas
mysql_select_db($database_vacantes, $vacantes);
$query_respuesta = "SELECT * FROM sed_clima WHERE IDpregunta = '$la_pregunta_actual' AND IDempleado = '$el_usuario' AND IDperiodo = '$IDperiodo'";
$respuesta = mysql_query($query_respuesta, $vacantes) or die(mysql_error());
$row_respuesta = mysql_fetch_assoc($respuesta);
$totalRows_respuesta = mysql_num_rows($respuesta);
$la_respuesta = $row_respuesta['IDrespuesta'];


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $actualiza_respuesta = $_POST['IDrespuesta'];	
  $updateSQL = "UPDATE sed_clima SET IDrespuesta = '$actualiza_respuesta' WHERE IDpregunta = '$la_pregunta_actual' AND IDempleado = '$el_usuario' AND IDperiodo = '$IDperiodo'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: f_clima.php?IDpregunta=$la_pregunta_siguiente&activar=1");
}

//insertar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$emp_paterno = $row_usuario['emp_paterno'];
$emp_materno = $row_usuario['emp_materno'];
$emp_nombre = $row_usuario['emp_nombre'];
$denominacion = $row_usuario['denominacion'];
$IDpuesto = $row_usuario['IDpuesto'];

  $insertSQL = sprintf("INSERT INTO sed_clima (IDempleado, anio, IDperiodo, fecha, IDpregunta, IDmatriz, IDarea, IDrespuesta, emp_paterno, emp_materno, emp_nombre, denominacion, IDpuesto) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['anio'], "text"),
                       GetSQLValueString($IDperiodo, "int"),
                       GetSQLValueString($_POST['fecha'], "text"),
                       GetSQLValueString($_POST['IDpregunta'], "int"),
                       GetSQLValueString($IDmatriz, "int"),
                       GetSQLValueString($IDarea, "int"),
                       GetSQLValueString($_POST['IDrespuesta'], "text"),
                       GetSQLValueString($emp_paterno, "text"),
                       GetSQLValueString($emp_materno, "text"),
                       GetSQLValueString($emp_nombre, "text"),
                       GetSQLValueString($denominacion, "text"),
                       GetSQLValueString($IDpuesto, "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  $captura = mysql_insert_id();
  header("Location: f_clima.php?IDpregunta=$la_pregunta_siguiente&activar=1");
}

//cierre de la encuesta
	if ($la_pregunta_ultima == $max_preg) {
	$max_preg_extra = $max_preg + 1;
	$updateSQL = "INSERT INTO sed_clima (IDempleado, anio, IDperiodo, fecha, IDpregunta, IDrespuesta, IDmatriz, IDarea) VALUES 
	('$el_usuario', '$anio', '$IDperiodo', '$fecha', '$max_preg_extra', 9, '$IDmatriz', '$IDarea')"; 
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

//agregamos IDjefe
	$IDjefe = $row_clima['IDrespuesta'];
	$query_boss = "SELECT * FROM prod_activos WHERE IDempleado = '$IDjefe'";
	$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
	$row_boss = mysql_fetch_assoc($boss);
	
	$j_emp_paterno = $row_boss['emp_paterno'];
	$j_emp_materno = $row_boss['emp_materno'];
	$j_emp_nombre = $row_boss['emp_nombre'];
	$j_denominacion = $row_boss['denominacion'];
	$j_IDpuesto = $row_boss['IDpuesto'];
	$j_IDarea = $row_boss['IDarea'];

$updateSQL2 = "UPDATE sed_clima SET IDjefe = '$IDjefe', j_emp_paterno = '$j_emp_paterno', j_emp_materno = '$j_emp_materno', j_emp_nombre = '$j_emp_nombre', j_denominacion = '$j_denominacion', j_IDpuesto = '$j_IDpuesto', j_IDarea = '$j_IDarea' WHERE IDempleado = '$el_usuario' AND IDperiodo = '$IDperiodo'"; 
	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL2, $vacantes) or die(mysql_error());

  header("Location: f_clima.php");
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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/form_checkboxes_radios.js"></script>
	
	<script>
	<?php if ($_GET['activar'] == 1) { ?> 
	 $(document).ready(function(){ $("#ModalPreguntas").modal('show'); }); 
	<?php } ?>
	</script>


</head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">		

			<?php require_once('assets/f_pheader.php'); ?>

				
				<div class="content">
				
				
				    <div class="panel panel-flat">

					<div class="row">
						<div class="col-sm-6 col-md-2">
							<div class="panel-body">

								<?php if($la_pregunta_ultima > $max_preg) { // Si termino la encuesta ?>
                 
									<a class="btn btn-success btn-float btn-float-lg"><i class="icon-checkmark4"></i> <span>Encuesta Terminada</span></a>
                 
								<?php } elseif($totalRows_clima > 0) { // Si no termino la encuesta ?>

									<a class="btn btn-warning btn-float btn-float-lg"  href="f_clima.php?IDpregunta=<?php echo $la_pregunta_ultima + 1;?>&activar=1"><i class="icon-forward2"></i> <span>Continuar con la Encuesta</span> </a>
							
								<?php } else {  // Si no ha contestado nada ?>

									<a class="btn btn-info btn-float btn-float-lg" href="f_clima.php?IDpregunta=<?php echo $la_pregunta_actual;?>&activar=1"><i class="icon-play3"></i> <span>Iniciar la Encuesta</span></a>
									
								<?php } ?>

							</div>
						</div>
						<div class="col-sm-6 col-md-10">
							<div class="panel-body">

                 <p>A continuación te presentamos una serie de oraciones  que conforman nuestra encuesta de <strong>clima laboral</strong>; por cada frase,  selecciona la opción que refleja tú opinión. </p>
                 <p>Tus respuestas serán  tratadas de forma <strong>CONFIDENCIAL</strong> Y <strong>ANÓNIMA</strong> y no serán utilizadas para ningún  propósito distinto al de ayudarnos a mejorar 
                  y formar parte de la nueva cultura  para Evaluar el Desempeño.</p>
                 <p>Recuerda &nbsp;el clima organizacional lo  hacemos todos. ¡Sahuayo eres tú!</p>
								
							</div>
						</div>
					</div>
				</div>
				
                <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Introducción</h5>
						</div>

					<div class="row">
						<div class="col-sm-6 col-md-6">
							<div class="panel-body">

                 <p><strong>Bienvenido</strong>, tu opinión es muy importante para  nosotros.!!</p>
				 <p>Periodo de Evaluación: <b><?php echo $row_elperiodo['periodo']; ?></b>.</p>

			     <p>Sahuayo cuenta con un modelo de <strong>clima Laboral</strong> que toma como  base la metodología de empresas internacionales dedicadas a evaluar el Clima Organizacional, tales como Great Place to Work y Top Companies.</p>
			     <p>El buen <strong>clima laboral</strong> se construyen día a día a través de relaciones; por ello, el modelo está fundamentado en la interacción de 4 principales relaciones en el lugar de trabajo:
				 <ul>
				 <li>Compañeros</li>
				 <li>Sahuayo</li>
				 <li>Líder</li>
				 <li>Colaborador</li>
				 </ul>
				 </p>
			     <p>Los principales beneficios que se obtienen con la evaluación de <strong>clima laboral</strong> son: </p>
				 <ul>
				 <li>Mejora la Productividad laboral.</li>
				 <li>Menor absentismo laboral.</li>
				 <li>Se favorece el trabajo en equipo.</li>
				 <li>Mayor satisfacción en el trabajo.</li>
				 <li>Los talentos permanecen en la empresa.</li>
				 <li>Menor rotación de trabajadores.</li>
				 <li>Mayor integración por parte de los trabajadores.</li>
				 <li>La empresa se adapta mejor a entornos competitivos y se enfrenta mejor a los cambios.</li>
				 <li>Se consiguen los resultados propuestos.</li>
				 <li>Se evalúa y desarrolla a los Líderes en la Organizacion.</li>
				 </ul>

							</div>
						</div>
						<div class="col-sm-6 col-md-6">
							<div class="panel-body">
								<img src="assets/img/6913.jpg" class="img-responsive" alt="">
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
		<!-- /page content -->

</div>
	<!-- /page container -->

                    <div id="ModalPreguntas" class="modal fade" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header bg-primary-700">
										
										<div class="col-md-12">
									<h6>Clima Laboral Sahuayo <?php echo $anio; ?></h6>										</div>

                          </div>
						  
                          <div class="modal-body">
						 <form method="post" id="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">


								<fieldset class="content-group">

                                    <div class="form-group pt-15">
								        <span class="display-block text-muted"><strong>Pregunta</strong> <?php echo $row_pregunta['IDpregunta']. " de ".$max_preg; ?></span>
										<h6>&nbsp;<?php echo $pregunta_texto; ?></h6>
                                        
                                <?php if ($row_pregunta['pregunta_tipo'] == 1) { // si es pregunta cerrada ?>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> type="radio" name="IDrespuesta" id="respuesta_1" value="3" class="control-primary"  required="required" />
												Totalmente de acuerdo.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> type="radio" name="IDrespuesta" id="respuesta_2" value="2" class="control-success"/>
												Parcialmente de acuerdo.
											</label>
										</div>

										<div class="radio">
											<label>
												<input <?php if (!(strcmp(htmlentities($row_respuesta['IDrespuesta'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> type="radio" name="IDrespuesta" id="respuesta_3" value="1" class="control-warning"/>
												En desacuerdo.
											</label>
										</div>
									</div>
									
								<?php } else if ($row_pregunta['pregunta_tipo'] == 3) { // combo jefe inmediato ?>	
								
									<!-- Live search support -->
								<div class="form-group">
									<div class="col-lg-9">
										<select class="bootstrap-select" data-live-search="true" data-width="100%" name="IDrespuesta" id="IDrespuesta" required="required">
													  <option value="">Selecciona el nombre de tu jefe inmediato</option>
													  <?php  do { ?>
													  <option value="<?php echo $row_jefes['IDempleado']?>"<?php if (!(strcmp($row_jefes['IDempleado'], $row_respuesta['IDrespuesta']))) 
													  {echo "SELECTED";} ?>><?php echo $row_jefes['emp_nombre'] . " " . $row_jefes['emp_paterno'] . " " . $row_jefes['emp_materno']. " (" . $row_jefes['denominacion'] . ")";?></option>
													  <?php
													 } while ($row_jefes = mysql_fetch_assoc($jefes));
													   $rows = mysql_num_rows($jefes);
													   if($rows > 0) {
													   mysql_data_seek($jefes, 0);
													   $row_jefes = mysql_fetch_assoc($jefes);
													 } ?>
										</select>
									</div>
									<!-- /live search support -->
									</div>
								</div>

								Si no aparece el nombre de tu jefe inmediato, <a href="f_clima.php?noboss=1&IDpregunta=1&activar=1" class="label label-warning">haz clic aqui </a> para ampliar la lista.		 
							 
								<?php } else { // si es pregunta abierta ?>
                                
								  <div class="form-group">
										<div class="col-lg-12">
                                          <textarea name="IDrespuesta" id="IDrespuesta" rows="3" class="form-control" placeholder="Pregunta abierta."><?php echo $row_respuesta['IDrespuesta']; ?></textarea>
										</div>
									</div>

								<?php }  ?>
								


					        <input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>" />
					        <input type="hidden" name="IDpregunta" value="<?php echo $la_pregunta_actual; ?>" />
					        <input type="hidden" name="anio" value="<?php echo $anio; ?>" />
					        <input type="hidden" name="fecha" value="<?php echo $fecha; ?>" />

      							<div class="modal-footer">
                                
								<?php  if ($la_pregunta_actual > 1) { ?> 
                                <a class="btn bg-primary-300" href="f_clima.php?IDpregunta=1&activar=1"><<- Primera</a>
                                <?php } ?>


								<?php  if ($la_pregunta_actual > 1) { ?> 
                                <a class="btn bg-primary-700" href="f_clima.php?IDpregunta=<?php echo $la_pregunta_anterior; ?>&activar=1"><- Anterior</a>
                                <?php } ?>

                                <?php if ($la_respuesta == "") { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_insert" value="<?php if ($la_pregunta_actual != $max_preg) { echo "Siguiente ->"; } else {echo "Terminar"; } ?>" />
							    <input type="hidden" name="MM_insert" value="form1" />
					            
								<?php } else { ?>
					            <input type="submit" class="btn bg-primary-700" name="MM_update" value="<?php if ($la_pregunta_actual != $max_preg) { echo "Siguiente ->"; } else {echo "Terminar"; } ?>" />
							    <input type="hidden" name="MM_update" value="form1" />
					            <?php }  ?>

								<?php  if ($la_pregunta_ultima > 0 && $la_pregunta_actual != $max_preg) { ?> 
                                <a class="btn bg-primary-300" href="f_clima.php?IDpregunta=<?php echo $la_pregunta_ultima + 1; ?>&activar=1">Última ->></a>
                                <?php } ?>

                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                              </div>
								</fieldset>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

</body>
</html>