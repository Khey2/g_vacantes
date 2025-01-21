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
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDresultado = $_GET['IDresultado'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);




$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE nom35_resultados SET plan_accion=%s WHERE IDresultado=%s",
                       GetSQLValueString($_POST['plan_accion'], "text"),
                       GetSQLValueString($_POST['IDresultado'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_n35e_resultado.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}




mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT * FROM nom35_resultados WHERE IDresultado = $IDresultado";
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);
$el_evaluado = $row_resultado['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//el examen contestado
$IDexmen = 1;

mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_periodo = "SELECT * FROM nom35_periodos WHERE IDperiodo = $IDperiodo";
$periodo = mysql_query($query_periodo, $vacantes) or die(mysql_error());
$row_periodo = mysql_fetch_assoc($periodo);
$totalRows_periodo = mysql_num_rows($periodo);


//RESULTADOS TOTALES
mysql_select_db($database_vacantes, $vacantes);
$query_total_c = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, nom35_respuestas.respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdimension, nom35_respuestas.pregunta_tipo, nom35_dominios.dominio, nom35_dominios.IDdominio 
FROM nom35_respuestas LEFT JOIN nom35_dominios ON nom35_respuestas.IDdominio = nom35_dominios.IDdominio WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDempleado = $el_evaluado AND nom35_respuestas.IDperiodo = $IDperiodo AND nom35_respuestas.IDpregunta = 1"; 
mysql_query("SET NAMES 'utf8'");
$total_c = mysql_query($query_total_c, $vacantes) or die(mysql_error());
$row_total_c = mysql_fetch_assoc($total_c);
$totalRows_total_c = mysql_num_rows($total_c);
$respuesta = $row_total_c['respuesta'];

//RESULTADOS TOTALES
mysql_select_db($database_vacantes, $vacantes);
$query_total_B = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, nom35_respuestas.respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdimension, nom35_respuestas.pregunta_tipo, nom35_dominios.dominio, nom35_dominios.IDdominio, nom35_preguntas.pregunta_texto FROM nom35_respuestas LEFT JOIN nom35_dominios ON nom35_respuestas.IDdominio = nom35_dominios.IDdominio LEFT JOIN nom35_preguntas ON nom35_respuestas.IDpregunta = nom35_preguntas.IDpregunta AND nom35_respuestas.IDexamen = nom35_preguntas.IDexamen WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDempleado = $el_evaluado AND nom35_respuestas.IDperiodo = $IDperiodo"; 
$total_B = mysql_query($query_total_B, $vacantes) or die(mysql_error());
$row_total_B = mysql_fetch_assoc($total_B);
$totalRows_total_B = mysql_num_rows($total_B);

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
	<script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>

	<script src="assets/js/app.js"></script>
	<!-- /theme JS files -->

  <style> 
    table td:first-child {  width: 40%; } 
    table td:second-child {  width: 40%; } 
    div.a { text-align: center; }
    th { text-align: center; }
    </style>

</head>
<body class="has-detached-left">

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
					<div class="panel-body">
          <h5>Política de Prevención de Riesgos Psicosociales</h5>
        <p><b>Impulsora Sahuayo S.A. de C.V. </b>promueve la prevención de los factores de riesgo psicosocial; la prevención de la violencia laboral, y la promoción de un entorno organizacional favorable, la diversidad e inclusión, facilitando a los colaboradores acciones de sensibilización, programas de comunicación, capacitación y espacios de participación y consulta, quedando estrictamente prohibidos los actos de violencia laboral, represalias, abusos, discriminación por creencias, raza, sexo, religión, etnia o edad, preferencia sexual o cualquier otra condición que derive en riesgo psicosocial o acciones en contra del favorable entorno organizacional.</p>

        <a href="admin_n35e_resultado2_print.php?IDresultado=<?php echo $IDresultado; ?>" target="_blank" class="btn btn-success">Imprimir</a>
					</div>
                    
					<!-- /Contenido -->
					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">

								<!-- User details -->
								<div class="content-group">
									<div class="panel-body bg-indigo-400 border-radius-top text-center" style="background-image:
                                     url(http://demo.interface.club/limitless/assets/images/bg.png); background-size: contain;">
										<div class="content-group-sm">
											<h6 class="text-semibold no-margin-bottom">
												<?php echo $row_resultado['emp_paterno'] . " " .  $row_resultado['emp_materno'] . "<br/> " .  $row_resultado['emp_nombre']; ?>
											</h6>
											<span class="display-block">No. Emp. <?php echo $row_resultado['IDempleado']; ?></span>
										</div>
										<a href="#" class="display-inline-block content-group-sm">
											<img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-responsive" alt="" style="width: 110px; height: 110px;">
										</a>
									</div>

									<div class="panel no-border-top no-border-radius-top">
										<ul class="navigation">
											<li class="navigation-header">Datos</li>
											<li><a href="#" data-toggle="tab">No. Emp.: <?php echo $row_resultado['IDempleado']; ?></a></li>
											<li><a href="#" data-toggle="tab">Paterno: <?php echo $row_resultado['emp_paterno']; ?></a></li>
											<li><a href="#" data-toggle="tab">Materno: <?php echo $row_resultado['emp_materno']; ?></a></li>
											<li><a href="#" data-toggle="tab">Nombres: <?php echo $row_resultado['emp_nombre']; ?></a></li>
											<li><a href="#" data-toggle="tab">Sucursal: <?php echo $row_matriz['matriz']; ?></a></li>
											<li><a href="#" data-toggle="tab">Puesto: <?php echo $row_resultado['denominacion']; ?></a></li>
											<li><a href="#" data-toggle="tab">Ingreso: <?php 
											 $afecha = date('d/m/Y', strtotime($row_resultado['fecha_alta'])); echo $afecha; ?></a></li>
											<li><a href="#" data-toggle="tab">Fecha: <?php 
											 $afecha = date('d/m/Y', strtotime($row_resultado['fecha_aplicacion'])); echo $afecha; ?></a></li>
										</ul>
									</div>
								</div>
								<!-- /user details -->


							</div>
						</div>
					</div>
					</div>
		            <!-- /detached sidebar -->


					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

							<!-- Tab content -->
							<div class="tab-content">
							  <div class="tab-pane fade in active" id="profile">

									<div class="panel panel-flat">                                        
                      <div class="panel-body">
        <legend class="text-bold">Resultado General</legend>


        <p><?php if ($respuesta == 5) {echo "<span class='text text-success'>No requiere valoración ni atención clínica</span>";} else { ?>
		

			<table class="table table-bordered table-condensed">
    <thead>
      <tr style="text-align: center;">
            <th>Sección</th>
            <th>Pregunta</th>
            <th>Respuesta</th>
       </tr>
    </thead>
    <tbody class="border border-neutral">
<?php do { ?>
    <tr>
        <td class="font-semibold"><?php echo $row_total_B['dominio']; ?></td>
        <td><?php echo $row_total_B['pregunta_texto']; ?></td>
        <td><?php if ($row_total_B['respuesta'] == 6) { echo "<span class='text text-danger text-strong'>SI</span>";} else{echo "No";} ?></td>
      </tr>
      <?php } while ($row_total_B = mysql_fetch_assoc($total_B));  ?>
    </tbody>
</table>

		
			<?php } ?>
		</p>
 

                      </div>
                  </div>
                                    
                  

									<!-- Share your thoughts -->
									<div class="panel panel-flat">
										<div class="panel-heading">
                    <legend class="text-bold">Plan de Acción</legend>
										</div>

										<div class="panel-body">
											<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
												<div class="form-group">
                            <textarea name="plan_accion" class="form-control mb-15" rows="3" cols="1"><?php echo htmlentities($row_resultado['plan_accion'], ENT_COMPAT, 'utf-8'); ?></textarea>
                            <input type="hidden" name="MM_update" value="form1" />
                            <input type="hidden" name="IDresultado" value="<?php echo $row_resultado['IDresultado']; ?>" />
												</div>

												      <div class="row">
						                    <div class="col-xs-6">
						                    </div>

                            <div class="col-xs-6 text-right">
                              <input type="submit"  class="btn btn-primary" value="Guardar" />
                            </div>
						              </div>
					            </form>
				                    	</div>
									</div>
									<!-- /share your thoughts -->

								</div>
								</div>
							</div>
							<!-- /tab content -->

						</div>
					<!-- /detached content -->                    
                    
                    

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