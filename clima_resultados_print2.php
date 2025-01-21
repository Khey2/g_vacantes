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
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

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



$_SESSION['IDmatriz'] = $IDmatriz;

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];
// saber si se muestran resultados
$resultados = $row_matriz['clima'];


//plantilla
mysql_select_db($database_vacantes, $vacantes);
$query_plantilla = "SELECT DISTINCT Count(prod_activos.rfc) as plantilla FROM prod_activos WHERE prod_activos.IDmatriz ='$la_matriz' AND fecha_alta <= now()-interval 3 month";
$plantilla = mysql_query($query_plantilla, $vacantes) or die(mysql_error());
$row_plantilla = mysql_fetch_assoc($plantilla);
$totalRows_plantilla = mysql_num_rows($plantilla);

//plantilla
mysql_select_db($database_vacantes, $vacantes);
$query_encuestas = "SELECT DISTINCT sed_clima.IDempleado FROM sed_clima  WHERE sed_clima.IDmatriz ='$la_matriz' AND IDperiodo = '$IDperiodo'";
$encuestas = mysql_query($query_encuestas, $vacantes) or die(mysql_error());
$row_encuestas = mysql_fetch_assoc($encuestas);
$totalRows_encuestas = mysql_num_rows($encuestas);

//Si tiene resultados
mysql_select_db($database_vacantes, $vacantes);
$query_clima = "SELECT * FROM sed_clima WHERE IDempleado = '$el_usuario' AND IDperiodo = '$IDperiodo'AND IDpregunta = 1";
$clima = mysql_query($query_clima, $vacantes) or die(mysql_error());
$row_clima = mysql_fetch_assoc($clima);
$totalRows_clima = mysql_num_rows($clima);

// Resultados abiertos positivos
mysql_select_db($database_vacantes, $vacantes);
$query_abiertos_mas = "SELECT IDrespuesta AS respuestas FROM sed_clima WHERE sed_clima.IDmatriz = '$IDmatriz' AND sed_clima.IDpregunta = 93 AND IDperiodo = '$IDperiodo'"; 
$abiertos_mas = mysql_query($query_abiertos_mas, $vacantes) or die(mysql_error());
$row_abiertos_mas = mysql_fetch_assoc($abiertos_mas);
$totalRows_abiertos_mas = mysql_num_rows($abiertos_mas);

// Resultados abiertos negativos
mysql_select_db($database_vacantes, $vacantes);
$query_abiertos_menos = "SELECT IDrespuesta AS respuestas FROM sed_clima WHERE sed_clima.IDmatriz = '$IDmatriz' AND sed_clima.IDpregunta = 94 AND IDperiodo = '$IDperiodo'"; 
$abiertos_menos = mysql_query($query_abiertos_menos, $vacantes) or die(mysql_error());
$row_abiertos_menos = mysql_fetch_assoc($abiertos_menos);
$totalRows_abiertos_menos = mysql_num_rows($abiertos_menos);


// Resultados por Sucursal
mysql_select_db($database_vacantes, $vacantes);
$query_subdimensiones = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_dimension IS NOT NULL"; 
$subdimensiones = mysql_query($query_subdimensiones, $vacantes) or die(mysql_error());
$row_subdimensiones = mysql_fetch_assoc($subdimensiones);
$totalRows_subdimensiones = mysql_num_rows($subdimensiones);
$calificacion_sucursal = round($row_subdimensiones['Calificacion']/100,2);

// Resultados por Dimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_dimensiones1 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 1 AND IDperiodo = '$IDperiodo'AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_dimension IS NOT NULL GROUP BY sed_clima_preguntas.pregunta_dimension";
$dimensiones1 = mysql_query($query_dimensiones1, $vacantes) or die(mysql_error());
$row_dimensiones1 = mysql_fetch_assoc($dimensiones1);
$totalRows_dimensiones1 = mysql_num_rows($dimensiones1);

// Resultados por Subdimension liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_subdimensiones1 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 1 AND IDperiodo = '$IDperiodo' AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_subdimension IS NOT NULL GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimensiones1 = mysql_query($query_subdimensiones1, $vacantes) or die(mysql_error());
$row_subdimensiones1 = mysql_fetch_assoc($subdimensiones1);
$totalRows_subdimensiones1 = mysql_num_rows($subdimensiones1);

// Resultados por Dimension Compañeros
mysql_select_db($database_vacantes, $vacantes);
$query_dimensiones2 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 2 AND IDperiodo = '$IDperiodo'AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_dimension IS NOT NULL GROUP BY sed_clima_preguntas.pregunta_dimension";
$dimensiones2 = mysql_query($query_dimensiones2, $vacantes) or die(mysql_error());
$row_dimensiones2 = mysql_fetch_assoc($dimensiones2);
$totalRows_dimensiones2 = mysql_num_rows($dimensiones2);

// Resultados por Subdimension Compañeros
mysql_select_db($database_vacantes, $vacantes);
$query_subdimensiones2 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 2 AND sed_clima.IDperiodo = '$IDperiodo'AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimensiones2 = mysql_query($query_subdimensiones2, $vacantes) or die(mysql_error());
$row_subdimensiones2 = mysql_fetch_assoc($subdimensiones);
$totalRows_subdimensiones = mysql_num_rows($subdimensiones);

// Resultados por Dimension Empresa
mysql_select_db($database_vacantes, $vacantes);
$query_dimensiones3 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 3 AND IDperiodo = '$IDperiodo'AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_dimension IS NOT NULL GROUP BY sed_clima_preguntas.pregunta_dimension";
$dimensiones3 = mysql_query($query_dimensiones3, $vacantes) or die(mysql_error());
$row_dimensiones3 = mysql_fetch_assoc($dimensiones3);
$totalRows_dimensiones3 = mysql_num_rows($dimensiones3);

// Resultados por Subdimension Empresa
mysql_select_db($database_vacantes, $vacantes);
$query_subdimensiones3 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 3 AND IDperiodo = '$IDperiodo' AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_subdimension IS NOT NULL GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimensiones3 = mysql_query($query_subdimensiones3, $vacantes) or die(mysql_error());
$row_subdimensiones3 = mysql_fetch_assoc($subdimensiones3);
$totalRows_subdimensiones3 = mysql_num_rows($subdimensiones3);

// Resultados por Dimension Colaborador
mysql_select_db($database_vacantes, $vacantes);
$query_dimensiones4 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_dimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 4 AND IDperiodo = '$IDperiodo'AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_dimension IS NOT NULL GROUP BY sed_clima_preguntas.pregunta_dimension";
$dimensiones4 = mysql_query($query_dimensiones4, $vacantes) or die(mysql_error());
$row_dimensiones4 = mysql_fetch_assoc($dimensiones4);
$totalRows_dimensiones4 = mysql_num_rows($dimensiones4);

// Resultados por Subdimension Colaborador
mysql_select_db($database_vacantes, $vacantes);
$query_subdimensiones4 = "SELECT (Avg(sed_clima.IDrespuesta) / 3 ) *100 AS Calificacion, sed_clima_preguntas.pregunta_subdimension FROM sed_clima LEFT JOIN sed_clima_preguntas ON sed_clima_preguntas.IDpregunta = sed_clima.IDpregunta WHERE sed_clima_preguntas.IDpregunta_dimension = 4 AND IDperiodo = '$IDperiodo' AND sed_clima.IDmatriz = '$IDmatriz' AND sed_clima_preguntas.pregunta_tipo = 1 AND sed_clima_preguntas.pregunta_subdimension IS NOT NULL GROUP BY sed_clima_preguntas.pregunta_subdimension";
$subdimensiones4 = mysql_query($query_subdimensiones4, $vacantes) or die(mysql_error());
$row_subdimensiones4 = mysql_fetch_assoc($subdimensiones4);
$totalRows_subdimensiones4 = mysql_num_rows($subdimensiones4);

$calificacion_sucursalx100 = $calificacion_sucursal * 100; 

if ($calificacion_sucursalx100 >= 85) {$color = '"#74AF57"';}
else if ($calificacion_sucursalx100 >= 75) {$color = '"#6ABCD3"';} 
else if ($calificacion_sucursalx100  >= 50) {$color = '"#E05726"';} 

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
	<script src="global_assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
	<script src="global_assets/js/plugins/visualization/d3/d3.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/2general_widgets_stats.js"></script>

    <!-- /theme JS files -->
	<script type="text/javascript">
    var  calificacion_sucursal = <?php echo  $calificacion_sucursal; ?>;
    var  color = <?php echo  $color; ?>;
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
                
					<!-- Questions area -->
					<h1 class="text-center content-group text-danger">
						Resultados de la Encuesta de Clima Laboral
						<small class="display-block">Sucursal: <?php echo $row_matriz['matriz']?></small>
					</h1>


<?php if ($resultados == 1) { ?>


					<div class="row">
						<div class="col-lg-9">

						<div class="panel panel-flat">	
							<div class="panel-body">
								
								<p><strong>Bienvenido</strong>, tu opinión es muy importante para  nosotros.!!</p>
								<p>Grupo Sahuayo ha diseñado un modelo para el estudio de su Clima Organizacional, basado en los modelos de la Empresas Internacionales <strong>Great Place to Work</strong> y <strong>Top Companies</strong>, fundamentado en el desarrollo e interacción de cuatro principales relaciones en el lugar de trabajo.</p>
								
								<p>Conforme al resultado, se describen tres tipos de situaciones para cada indicador:			
							  </p>
								<p><strong>Resultado Sobresaliente:</strong> Se presenta cuando la línea actual sobrepasa el ideal.  El clima percibido es óptimo y estos resultados se consideran fortalezas del clima del área.</p>
								<p><strong> Resultado Satisfactorio:</strong> Se identifica cuando la línea actual está por debajo de la calificación de ochenta y cinco puntos  y por arriba de los setenta y cinco puntos. Si la brecha tiende a disminuir, en este tipo de situación el clima percibido es positivo, de lo contrario, genera evidencia de pérdida de motivación por parte de las personas y por lo tanto exige una mayor atención para evitar clima bajo o negativo. </p>
								<p><strong>Resultado Deficiente:</strong> Se presenta cuando la línea actual cae por debajo de la calificación de sesenta y cinco puntos indicando una pérdida de potencial y de rendimiento en el desempeño. El clima percibido es negativo por lo que requiere atención inmediata.</p>
								
							</div>
						</div>
                        
							
							
							<div class="panel panel-flat">	
								<div class="panel-heading">
									<h1 class="panel-title text-warning">Dimensión Liderazgo</h1>
								</div>
							<div class="panel-body">
								
                                <p>Resultado Global: <strong><?php echo round($row_dimensiones1['Calificacion'],0);?>% 
                                		  (<?php if ($row_dimensiones1['Calificacion'] > 85) {echo "Sobresaliente";}
										   else if ($row_dimensiones1['Calificacion'] > 75) {echo "Satisfactorio";} 
										   else if ($row_dimensiones1['Calificacion'] > 50) {echo "Deficiente";} 
										   else {echo "Sin resultados";} ?>)</strong></p>
                                
							<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-orange-700"> 
                                    <th>Subdimensión</th>
                                    <th>Resultado %</th>
                                    <th>Resultado</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_subdimensiones1['pregunta_subdimension']; ?></td>
                                      <td><?php echo round($row_subdimensiones1['Calificacion'],0); ?>%</td>
                                      <td><?php if ($row_subdimensiones1['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimensiones1['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimensiones1['Calificacion'] >= 50) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?></td>
                                    </tr>
                                    <?php } while ($row_subdimensiones1 = mysql_fetch_assoc($subdimensiones1)); ?>
                                  </tbody>
                                </table>
							</div>
							</div>
						</div>


							<div class="panel panel-flat">	
								<div class="panel-heading">
									<h1 class="panel-title text-slate">Dimensión Compañeros</h1>
								</div>
							<div class="panel-body">
								
                                <p>Resultado Global: <strong><?php echo round($row_dimensiones2['Calificacion'],0);?>% 
                                		  (<?php if ($row_dimensiones2['Calificacion'] >= 85) {echo "Sobresaliente";}
										   else if ($row_dimensiones2['Calificacion'] >= 75) {echo "Satisfactorio";} 
										   else if ($row_dimensiones2['Calificacion'] >= 50) {echo "Deficiente";} 
										   else {echo "Sin resultados";} ?>)</strong></p>
                                 
								<div>
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-info-700"> 
                                    <th>Subdimensión</th>
                                    <th>Resultado %</th>
                                    <th>Resultado</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
								  <?php if ($row_subdimensiones2['Calificacion'] != 0) { ?>
                                    <tr>
                                      <td><?php echo $row_subdimensiones2['pregunta_subdimension']; ?></td>
                                      <td><?php echo round($row_subdimensiones2['Calificacion'],0); ?>%</td>
                                      <td><?php if ($row_subdimensiones2['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimensiones2['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimensiones2['Calificacion'] >= 50) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?></td>
                                    </tr>
								  <?php } ?>
                                    <?php } while ($row_subdimensiones2 = mysql_fetch_assoc($subdimensiones2)); ?>
                                  </tbody>
                                </table>
								</div>
							</div>
						</div>

							<div class="panel panel-flat">	
								<div class="panel-heading">
									<h1 class="panel-title text-danger">Dimensión Colaboradores</h1>
								</div>
							<div class="panel-body">
								
                                <p>Resultado Global: <strong><?php echo round($row_dimensiones3['Calificacion'],0);?>% 
                                		  (<?php if ($row_dimensiones3['Calificacion'] >= 85) {echo "Sobresaliente";}
										   else if ($row_dimensiones3['Calificacion'] >= 75) {echo "Satisfactorio";} 
										   else if ($row_dimensiones3['Calificacion'] >= 50) {echo "Deficiente";} 
										   else {echo "Sin resultados";} ?>)</strong></p>

							<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-warning-700"> 
                                    <th>Subdimensión</th>
                                    <th>Resultado %</th>
                                    <th>Resultado</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_subdimensiones3['pregunta_subdimension']; ?></td>
                                      <td><?php echo round($row_subdimensiones3['Calificacion'],0); ?>%</td>
                                      <td><?php if ($row_subdimensiones3['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimensiones3['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimensiones3['Calificacion'] >= 50) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?></td>
                                    </tr>
                                    <?php } while ($row_subdimensiones3 = mysql_fetch_assoc($subdimensiones3)); ?>
                                  </tbody>
                                </table>
								</div>
							</div>
						</div>

							<div class="panel panel-flat">	
								<div class="panel-heading">
									<h1 class="panel-title text-success">Dimensión Empresa</h1>
								</div>
							<div class="panel-body">
								
                                <p>Resultado Global: <strong><?php echo round($row_dimensiones4['Calificacion'],0);?>% 
                                		  (<?php if ($row_dimensiones4['Calificacion'] >= 85) {echo "Sobresaliente";}
										   else if ($row_dimensiones4['Calificacion'] >= 75) {echo "Satisfactorio";} 
										   else if ($row_dimensiones4['Calificacion'] >= 50) {echo "Deficiente";} 
										   else {echo "Sin resultados";} ?>)</strong></p>

							<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-success-700"> 
                                    <th>Subdimensión</th>
                                    <th>Resultado %</th>
                                    <th>Resultado</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_subdimensiones4['pregunta_subdimension']; ?></td>
                                      <td><?php echo round($row_subdimensiones4['Calificacion'],0); ?>%</td>
                                      <td><?php if ($row_subdimensiones4['Calificacion'] >= 85) {echo "<span class='label label-success'>Sobresaliente</span>";}
										   else if ($row_subdimensiones4['Calificacion'] >= 75) {echo "<span class='label label-info'>Satisfactorio</span>";} 
										   else if ($row_subdimensiones4['Calificacion'] >= 50) {echo "<span class='label label-warning'>Deficiente</span>";} 
										   else {echo "Sin resultados";} ?></td>
                                    </tr>
                                    <?php } while ($row_subdimensiones4 = mysql_fetch_assoc($subdimensiones4)); ?>
                                  </tbody>
                                </table>
								</div>
							</div>
						</div>
						
						
						<div class="panel panel-flat">	
								<div class="panel-heading">
									<h1 class="panel-title text-slate">Comentarios</h1>
								</div>
							<div class="panel-body">
							
							<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-slate-700"> 
                                    <th>Positivos</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
								  <?php if( $row_abiertos_mas['respuestas'] != '') { ?>
                                    <tr>
                                      <td><?php echo $row_abiertos_mas['respuestas']; ?></td>
                                    </tr>
								  <?php } ?>
                                    <?php } while ($row_abiertos_mas = mysql_fetch_assoc($abiertos_mas)); ?>
                                  </tbody>
                                </table>
								</div>
								
								
								
								
							<div class="table-responsive">
							<table class="table table-hover">
                    			<thead>
                                	<tr class="bg-slate-700"> 
                                    <th>Negativos</th>
                                  </tr>
</thead>
                                <tbody>
								  <?php do { ?>
								  <?php if( $row_abiertos_menos['respuestas'] != '') { ?>
                                    <tr>
                                      <td><?php echo $row_abiertos_menos['respuestas']; ?></td>
								  <?php } ?>
                                    <?php } while ($row_abiertos_menos = mysql_fetch_assoc($abiertos_menos)); ?>
                                  </tbody>
                                </table>
								</div>
								
							</div>
						</div>

						
					</div>



					<div class="col-lg-3">

							<!-- Online staff members -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Resultado de la Sucursal</h6>
								</div>

								<div class="panel-body text-center">

								<h2> <?php echo $calificacion_sucursal * 100;?>%</h2>

								<?php $calificacion_sucursalx100 = $calificacion_sucursal * 100; 
												if ($calificacion_sucursalx100 >= 85) {echo "<h5 class='panel-title text-success'>Sobresaliente</h5>";}
										   else if ($calificacion_sucursalx100 >= 75) {echo "<h5 class='panel-title text-info'>Satisfactorio</h5>";} 
										   else if ($calificacion_sucursalx100 >= 50) {echo "<h5 class='panel-title text-danger'>Deficiente</h5>";} 
										   else {echo "Sin resultados";} ?>
								
								</div>
							</div>
							<!-- /online staff members -->


							<!-- Navigation -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Datos de la Encuesta</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="list-group no-border mb-5">
                <a href="#" class="list-group-item"><i class="icon-calendar2"></i> 
                Periodo de Evaluación <span class="label border-right-info label-striped label-striped-right"><?php echo $row_elperiodo['periodo']; ?></span></a>
                <a href="#" class="list-group-item"><i class="icon-users"></i> 
                Encuestas <span class="label border-right-info label-striped label-striped-right"><?php  echo $totalRows_encuestas; ?></span></a>
								</div>
							</div>
							<!-- /navigation -->

						</div>
					</div>
					<!-- /questions area -->
					
					
					<?php } else { ?>
					
							<div class="panel">
								<a href="#">
									<img src="assets/img/69132.jpg" class="img-responsive" alt="">
								</a>

							</div>
					<?php } ?>
					

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