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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$mis_areas = $row_usuario['IDareas'];

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $IDmatriz";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos); 

if(isset($_POST['IDperiodo'])) {$_SESSION['IDperiodo'] = $_POST['IDperiodo'];} 
if(!isset($_SESSION['IDperiodo'])) {$_SESSION['IDperiodo'] = 0;} 
$IDperiodo = $_SESSION['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_elperiodo = "SELECT * FROM sed_clima_periodos WHERE IDperiodo = $IDperiodo";
$elperiodo = mysql_query($query_elperiodo, $vacantes) or die(mysql_error());
$row_elperiodo = mysql_fetch_assoc($elperiodo);
$totalRows_elperiodo = mysql_num_rows($elperiodo); 

$el_usuario = $_GET['IDempleado'];
$query_evaluado = "SELECT * FROM prod_activos WHERE IDempleado = '$el_usuario'";
$evaluado = mysql_query($query_evaluado, $vacantes) or die(mysql_error());
$row_evaluado = mysql_fetch_assoc($evaluado);
$totalRows_evaluado = mysql_num_rows($evaluado);
$IDpuesto = $row_evaluado['IDpuesto'];
$IDarea = $row_evaluado['IDarea'];
$IDsucursal = $row_evaluado['IDsucursal'];
$_SESSION['IDmatriz'] = $IDmatriz;

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];
// saber si se muestran resultados
$resultados = $row_matriz['clima'];

// Resultados generales
mysql_select_db($database_vacantes, $vacantes);
$query_resultadogeneral = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) * 100 AS Calificacion FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta =  sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 1 AND sed_clima.IDjefe = '$el_usuario' AND sed_clima.IDperiodo = '$IDperiodo'";  
$resultadogeneral = mysql_query($query_resultadogeneral, $vacantes) or die(mysql_error());
$row_resultadogeneral = mysql_fetch_assoc($resultadogeneral);
$totalRows_resultadogeneral = mysql_num_rows($resultadogeneral);
$resultado_general = round($row_resultadogeneral['Calificacion']/100,2);

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_subdimension1 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_subdimension = 1 AND sed_clima.IDperiodo = '$IDperiodo'AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimension1  = mysql_query($query_subdimension1 , $vacantes) or die(mysql_error());
$row_subdimension1  = mysql_fetch_assoc($subdimension1 );
$totalRows_subdimension1  = mysql_num_rows($subdimension1 );

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_subdimension2 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_subdimension = 2 AND sed_clima.IDperiodo = '$IDperiodo'AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimension2  = mysql_query($query_subdimension2 , $vacantes) or die(mysql_error());
$row_subdimension2  = mysql_fetch_assoc($subdimension2 );
$totalRows_subdimension2  = mysql_num_rows($subdimension2);
$accion_y_resultados = round($row_subdimension2['Calificacion']/100,2);
$comunicacion = round($row_subdimension1['Calificacion']/100,2);

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_subdimension3 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_subdimension = 3 AND sed_clima.IDperiodo = '$IDperiodo'AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimension3  = mysql_query($query_subdimension3 , $vacantes) or die(mysql_error());
$row_subdimension3  = mysql_fetch_assoc($subdimension3 );
$totalRows_subdimension3  = mysql_num_rows($subdimension3 );
$participacion = round($row_subdimension3['Calificacion']/100,2);

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_subdimension4 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_subdimension = 4 AND sed_clima.IDperiodo = '$IDperiodo'AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimension4  = mysql_query($query_subdimension4 , $vacantes) or die(mysql_error());
$row_subdimension4  = mysql_fetch_assoc($subdimension4 );
$totalRows_subdimension4  = mysql_num_rows($subdimension4 );
$liderazgo_con_valores = round($row_subdimension4['Calificacion']/100,2);

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_subdimension5 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_subdimension = 5 AND sed_clima.IDperiodo = '$IDperiodo'AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimension5  = mysql_query($query_subdimension5 , $vacantes) or die(mysql_error());
$row_subdimension5  = mysql_fetch_assoc($subdimension5 );
$totalRows_subdimension5  = mysql_num_rows($subdimension5);
$relaciones_interpersonales = round($row_subdimension5['Calificacion']/100,2);

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_subdimension6 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_subdimension = 6 AND sed_clima.IDperiodo = '$IDperiodo'AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimension6  = mysql_query($query_subdimension6 , $vacantes) or die(mysql_error());
$row_subdimension6  = mysql_fetch_assoc($subdimension6 );
$totalRows_subdimension6  = mysql_num_rows($subdimension6 );
$motivacion_y_reconocimiento = round($row_subdimension6['Calificacion']/100,2);

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_mayores = "SELECT sed_clima.IDpregunta, sed_clima_preguntas.IDpregunta_dimension, sed_clima_preguntas.IDpregunta_subdimension, Avg(sed_clima.IDrespuesta) AS Resultado, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_texto FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta  = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 1 AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima.IDpregunta ORDER BY Resultado DESC LIMIT 3";
mysql_query("SET NAMES 'utf8'");
$mayores  = mysql_query($query_mayores , $vacantes) or die(mysql_error());
$row_mayores  = mysql_fetch_assoc($mayores );
$totalRows_mayores  = mysql_num_rows($mayores );

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_menores = "SELECT sed_clima.IDpregunta, sed_clima_preguntas.IDpregunta_dimension, sed_clima_preguntas.IDpregunta_subdimension, Avg(sed_clima.IDrespuesta) AS Resultado, sed_clima_preguntas.pregunta_subdimension, sed_clima_preguntas.pregunta_texto FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta  = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 1 AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima.IDpregunta ORDER BY Resultado ASC LIMIT 3";
mysql_query("SET NAMES 'utf8'");
$menores = mysql_query($query_menores , $vacantes) or die(mysql_error());
$row_menores  = mysql_fetch_assoc($menores );
$totalRows_menores  = mysql_num_rows($menores );

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_recomienda = "SELECT  sed_clima_preguntas.pregunta_subdimension,  sed_clima_preguntas.IDpregunta,  sed_clima_preguntas.IDpregunta_subdimension, Avg(sed_clima.IDrespuesta) AS Resultado, sed_clima_recomienda.IDtipo, sed_clima_recomienda.recomendacion FROM sed_clima LEFT JOIN sed_clima_preguntas ON  sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta LEFT JOIN sed_clima_recomienda ON sed_clima_recomienda.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 1 AND sed_clima.IDjefe = '$el_usuario' GROUP BY sed_clima_preguntas.IDpregunta ORDER BY Resultado ASC LIMIT 5";
mysql_query("SET NAMES 'utf8'");
$recomienda = mysql_query($query_recomienda , $vacantes) or die(mysql_error());
$row_recomienda  = mysql_fetch_assoc($recomienda );
$totalRows_recomienda  = mysql_num_rows($recomienda );

// cantidad de evaluadores
mysql_select_db($database_vacantes, $vacantes);
$query_evaluadores = "SELECT Count(sed_clima.IDempleado) AS Contador FROM sed_clima WHERE sed_clima.IDpregunta = 1 AND sed_clima.IDjefe = '$el_usuario' AND sed_clima.IDperiodo = '$IDperiodo'";  
$evaluadores = mysql_query($query_evaluadores, $vacantes) or die(mysql_error());
$row_evaluadores = mysql_fetch_assoc($evaluadores);


if ($row_resultadogeneral['Calificacion'] >= 85) {$color1 = '"#74AF57"';}
else if ($row_resultadogeneral['Calificacion'] >= 75) {$color1 = '"#6ABCD3"';} 
else if ($row_resultadogeneral['Calificacion']  >= 5) {$color1 = '"#E05726"';} 

if ($row_subdimension4['Calificacion'] >= 85) {$color2 = '"#74AF57"';}
else if ($row_subdimension4['Calificacion'] >= 75) {$color2 = '"#6ABCD3"';} 
else if ($row_subdimension4['Calificacion']  >= 5) {$color2 = '"#E05726"';} 

if ($row_subdimension5['Calificacion'] >= 85) {$color3 = '"#74AF57"';}
else if ($row_subdimension5['Calificacion'] >= 75) {$color3 = '"#6ABCD3"';} 
else if ($row_subdimension5['Calificacion']  >= 5) {$color3 = '"#E05726"';} 

if ($row_subdimension6['Calificacion'] >= 85) {$color4 = '"#74AF57"';}
else if ($row_subdimension6['Calificacion'] >= 75) {$color4 = '"#6ABCD3"';} 
else if ($row_subdimension6['Calificacion']  >= 5) {$color4 = '"#E05726"';} 

if ($row_subdimension2['Calificacion'] >= 85) {$color5 = '"#74AF57"';}
else if ($row_subdimension2['Calificacion'] >= 75) {$color5 = '"#6ABCD3"';} 
else if ($row_subdimension2['Calificacion']  >= 5) {$color5 = '"#E05726"';} 

if ($row_subdimension3['Calificacion'] >= 85) {$color6 = '"#74AF57"';}
else if ($row_subdimension3['Calificacion'] >= 75) {$color6 = '"#6ABCD3"';} 
else if ($row_subdimension3['Calificacion']  >= 5) {$color6 = '"#E05726"';} 

if ($row_subdimension1['Calificacion'] >= 85) {$color7 = '"#74AF57"';}
else if ($row_subdimension1['Calificacion'] >= 75) {$color7 = '"#6ABCD3"';} 
else if ($row_subdimension1['Calificacion']  >= 5) {$color7 = '"#E05726"';} 



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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/visualization/d3/d3.min.js"></script>
	<script src="global_assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/general_widgets_stats3.js"></script>
	<script src="global_assets/js/demo_pages/gallery.js"></script>

	<script type="text/javascript">
    var  resultado_general = <?php echo  $resultado_general; ?>;
    var  liderazgo_con_valores = <?php echo  $liderazgo_con_valores; ?>;
    var  relaciones_interpersonales = <?php echo  $relaciones_interpersonales ?>;
    var  motivacion_y_reconocimiento = <?php echo  $motivacion_y_reconocimiento; ?>;
    var  accion_y_resultados = <?php echo  $accion_y_resultados; ?>;
    var  comunicacion = <?php echo $comunicacion; ?>;
    var  participacion = <?php echo  $participacion; ?>;
	var  color1 = <?php echo  $color1; ?>;
	var  color2 = <?php echo  $color2; ?>;
	var  color3 = <?php echo  $color3; ?>;
	var  color4 = <?php echo  $color4; ?>;
	var  color5 = <?php echo  $color5; ?>;
	var  color6 = <?php echo  $color6; ?>;
	var  color7 = <?php echo  $color7; ?>;

	</script>

</head>
<body class="has-detached-right" onLoad="window.print()">
	<!-- Page container -->

	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">



			<!-- Main content -->
			<div class="content-wrapper">		

				
				<div class="content">
					
					
					<h1 class="text-center content-group text-danger">
						Reporte de Liderazgo
						<small class="display-block"><?php echo $row_evaluado['emp_paterno']. " ". $row_evaluado['emp_materno']. " ". $row_evaluado['emp_nombre'];?></small>
					</h1>


				
				<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Introducción</h5>
						</div>

							<div class="panel-body">
								
								<div class="row">
									<div class="col-md-4">

                 <p>Para Sahuayo, la identificación de los diferentes <strong>estilos de liderazgo</strong> es esencial para asegurar que las estrategias de capacitación y desarrollo estén orientadas a fortalecer las habilidades, competencias laborales y valores de los líderes en la Organización.</p>

<p>De la misma forma en la que ser jefe no tiene los mismos resultados que ser un líder, tampoco todas las formas de liderar son iguales y tienen los mismos efectos. Liderar exitosamente a un equipo de colaboradores hacia el cumplimiento de objetivos siempre supone que los líderes enfrenten realidades y retos cambiantes todos los días.</p>
						

									</div>
									<div class="col-md-4">
										
								<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-heart5 icon-2x text-green-700 no-edge-top"></i>
									</div>

									<div class="media-body">
										<h5 class="media-heading text-semibold text-green-700">Liderazgo con valores</h5>
										<span class="text-muted">Ser líder ejemplo de los valores corporativos; trabajando con ética, integridad e imparcialidad.</span>
									</div>
								</div>

									<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-user-tie icon-2x text-green-700 no-edge-top"></i>
									</div>

									<div class="media-body">
										<h5 class="media-heading text-semibold text-green-700">Relaciones Interpersonales</h5>
										<span class="text-muted">Generar una relación de confianza, mostrando interés sincero en sus colaboradores.</span>
									</div>
								</div>

									<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-checkmark icon-2x text-green-700 no-edge-top"></i>
									</div>

									<div class="media-body">
										<h5 class="media-heading text-semibold text-green-700">Motivación y Reconocimiento</h5>
										<span class="text-muted">Motivar el alto desempeño del equipo, reconociendo los logros.</span>
									</div>
								</div>

									<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-graph icon-2x text-green-700 no-edge-top"></i>
									</div>

									<div class="media-body">
										<h5 class="media-heading text-semibold text-green-700">Acción y Resultados</h5>
										<span class="text-muted">Coordinar eficientemente las actividades del área; empoderando y retroalimentando al personal.</span>
									</div>
								</div>

									<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-bubbles3 icon-2x text-green-700 no-edge-top"></i>
									</div>

									<div class="media-body">
										<h5 class="media-heading text-semibold text-green-700">Comunicación</h5>
										<span class="text-muted">Comunicar clara y oportunamente, escuchando con apertura y accesibilidad.</span>
									</div>
								</div>

									<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-cloud-check icon-2x text-green-700 no-edge-top"></i>
									</div>

									<div class="media-body">
										<h5 class="media-heading text-semibold text-green-700">Participación</h5>
										<span class="text-muted">Fomentar la participación activa del equipo en la toma de decisiones y solución de problemas.</span>
									</div>
								</div>
									</div>
									
									
									<div class="col-md-4">
										<img src="assets/img/modelo_liderazgo.jpg" class="img-responsive" alt="" style="width: 400px; height: 400px;">
									</div>
							</div>									
									
						</div>			
									
				</div>




					<!-- Stats with progress -->

					<div class="row">
						<div class="col-md-12">

							<!-- Satisfaction rate -->
							<div class="panel panel-body text-center">
                            
                              <h1 class="content-group text-semibold">
						Resultado General
							</h1>
                            
								<div class="svg-center position-relative" id="resultado_general"></div>
								<h2 class="progress-percentage mt-15 mb-5 text-semibold"><?php echo round($row_resultadogeneral['Calificacion'],0);?>%</h2>

								<?php if ($row_resultadogeneral['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_resultadogeneral['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_resultadogeneral['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
							</div>
							<!-- /satisfaction rate -->

						</div>
					</div>

					<div class="row">
						<div class="col-md-2">

							<!-- Satisfaction rate -->
							<div class="panel panel-body text-center">
								<div class="svg-center position-relative" id="liderazgo_con_valores"></div>
								<h2 class="progress-percentage mt-15 mb-5 text-semibold"><?php echo round($row_subdimension4['Calificacion'],0);?>%</h2>

								Liderazgo con valores
								<div class="text-size-small text-muted">
									<?php if ($row_subdimension4['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimension4['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimension4['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
								</div>
							</div>
							<!-- /satisfaction rate -->

						</div>

						<div class="col-md-2">

							<!-- Productivity goal  -->
							<div class="panel panel-body text-center">
								<div class="svg-center position-relative" id="relaciones_interpersonales"></div>
								<h2 class="progress-percentage mt-15 mb-5 text-semibold"><?php echo round($row_subdimension5['Calificacion'],0);?>%</h2>

								Relaciones Interpersonales
								<div class="text-size-small text-muted">
									<?php if ($row_subdimension5['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimension5['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimension5['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
								</div>
							</div>
							<!-- /productivity goal -->

						</div>

						<div class="col-md-2">

							<!-- Productivity goal  -->
							<div class="panel panel-body text-center">
								<div class="svg-center position-relative" id="motivacion_y_reconocimiento"></div>
								<h2 class="progress-percentage mt-15 mb-5 text-semibold"><?php echo round($row_subdimension6['Calificacion'],0);?>%</h2>

								Motivación y Reconocimiento
								<div class="text-size-small text-muted">
									<?php if ($row_subdimension6['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimension6['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimension6['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
								</div>
							</div>
							<!-- /productivity goal -->

						</div>

						<div class="col-md-2">

							<!-- Productivity goal  -->
							<div class="panel panel-body text-center">
								<div class="svg-center position-relative" id="accion_y_resultados"></div>
								<h2 class="progress-percentage mt-15 mb-5 text-semibold"><?php echo round($row_subdimension2['Calificacion'],0);?>%</h2>

								Acción y Resultados
								<div class="text-size-small text-muted">
									<?php if ($row_subdimension2['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimension2['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimension2['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
								</div>
							</div>
							<!-- /productivity goal -->

						</div>

						<div class="col-md-2">

							<!-- Productivity goal  -->
							<div class="panel panel-body text-center">
								<div class="svg-center position-relative" id="comunicacion"></div>
								<h2 class="progress-percentage mt-15 mb-5 text-semibold"><?php echo round($row_subdimension1['Calificacion'],0);?>%</h2>

								Comunicación
								<div class="text-size-small text-muted">
									<?php if ($row_subdimension1['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimension1['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimension1['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
								</div>
							</div>
							<!-- /productivity goal -->

						</div>

						<div class="col-md-2">

							<!-- Productivity goal  -->
							<div class="panel panel-body text-center">
								<div class="svg-center position-relative" id="participacion"></div>
								<h2 class="progress-percentage mt-15 mb-5 text-semibold"><?php echo round($row_subdimension3['Calificacion'],0);?>%</h2>

								Participación
								<div class="text-size-small text-muted">
									<?php if ($row_subdimension3['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimension3['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimension3['Calificacion']  >= 5) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?>
								</div>
							</div>
							<!-- /productivity goal -->

						</div>

					</div>

  <div class="saltopagina"></div>



					<div class="row">
						<div class="col-md-6">

							<!-- Satisfaction rate -->
							<div class="panel panel-body text-center">
                            
                              <h4 class="content-group text-semibold">
						Fortalezas
							</h4>
                            
								<div>
								
							<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-success-700"> 
                                    <th>Pregunta</th>
                                    <th>Resultado %</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php if (round($row_mayores['Resultado']*33+1,0) > 80) { ?>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_mayores['pregunta_texto']; ?></td>
                                      <td><?php echo round($row_mayores['Resultado']*33+1,0); ?>%</td>
                                    </tr>
                                    <?php } while ($row_mayores = mysql_fetch_assoc($mayores)); ?>
								  <?php } else { ?>
                                    <tr>
                                      <td colspan="2">No se encontraron fortalezas.</td>
                                    </tr>
								  <?php }  ?>
                                  </tbody>
                                </table>
							</div>
								
								
								
								</div>
							</div>
							<!-- /satisfaction rate -->

						</div>
						<div class="col-md-6">

							<!-- Satisfaction rate -->
							<div class="panel panel-body text-center">
                            
                              <h4 class="content-group text-semibold">
						A mejorar
							</h4>
                            
								<div>
								
								<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-danger-700"> 
                                    <th>Pregunta</th>
                                    <th>Resultado %</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_menores['pregunta_texto']; ?></td>
                                      <td><?php echo round($row_menores['Resultado']*33+1,0); ?>%</td>
                                    </tr>
                                    <?php } while ($row_menores = mysql_fetch_assoc($menores)); ?>
                                  </tbody>
                                </table>
							</div>
								
								
								</div>
							</div>
							<!-- /satisfaction rate -->

						</div>

					</div>



					<div class="row">
						<div class="col-md-12">

							<!-- Satisfaction rate -->
							<div class="panel panel-body text-center">
                            
                              <h4 class="content-group text-semibold">
						Recomendaciones
							</h4>
                            
								<div>
								
								<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-danger-700"> 
                                    <th>Subdimensión</th>
                                    <th>Recomendación</th>
                                    <th>Tipo</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><p class="text-left"><?php echo $row_recomienda['pregunta_subdimension']; ?></p></td>
                                      <td><p class="text-left"><?php echo $row_recomienda['recomendacion']; ?></p></td>
                                      <td><?php echo $row_recomienda['IDtipo']; ?></td>                                    
									</tr>
                                    <?php } while ($row_recomienda = mysql_fetch_assoc($recomienda)); ?>
                                  </tbody>
                                </table>
							</div>
								
								</div>
							</div>
							<!-- /satisfaction rate -->

						</div>

					</div>



				<div class="panel panel-flat">
						<div class="panel-heading">
						</div>

					<div class="row">
						<div class="col-lg-6 col-sm-6">
							<div class="thumbnail">
								<div class="thumb">
									<img src="assets/img/modelo.jpg" alt="">
									<div class="caption-overflow">
										<span>
											<a href="assets/img/modelo.jpg" data-popup="lightbox" class="btn border-white text-white btn-flat btn-icon btn-rounded">
                                            <i class="icon-plus3"></i></a>
										</span>
									</div>
								</div>
								
							</div>
						</div>                        
                        
                        
						<div class="col-sm-6 col-md-6">
							<div class="panel-body">
								<div class="embed-responsive embed-responsive-16by9">
											<iframe class="embed-responsive-item" src="https://player.vimeo.com/video/477675211" 
                                            frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								</div>
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
</body>
</html>