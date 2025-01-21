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
$el_usuario = $row_usuario['IDusuario'];

if($row_usuario['password'] == md5($row_usuario['IDusuario'])) { header("Location: cambio_pass.php?info=4"); }

$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$semana = date("W"); //la semana empieza ayer 
if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

//mes actual reporte del mes actual
mysql_select_db($database_vacantes, $vacantes);
$query_delmes = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.IDmotivo_baja, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE month(fecha_ocupacion) = '$el_mes' AND (vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') AND vac_vacante.IDestatus IN (2,3)";
$delmes = mysql_query($query_delmes, $vacantes) or die(mysql_error());
$row_delmes = mysql_fetch_assoc($delmes);
$totalRows_delmes = mysql_num_rows($delmes);

//fechas
require_once('assets/dias.php');


//variables en 0
$antes_tiempo = 0;
$a_tiempo = 0;
$fuera_tiempo = 0;
$muy_fuera_tiempo = 0;


// recorremos cada vacante
do { 

 $startdate = date('Y/m/d', strtotime($row_delmes['fecha_requi']));
 $end_date =  date('Y/m/d', strtotime($row_delmes['fecha_ocupacion']));

 $previo = getWorkingDays($startdate, $end_date, $holidays);
                             
  // aplicamos ajuste de dias;
  $ajuste_dias = $row_delmes['ajuste_dias'];
  if ($ajuste_dias != 0) { $previo = $previo - $ajuste_dias; } 
  
  // resultado grafica
     if ($previo < 4) {  
	 $antes_tiempo = $antes_tiempo + 1;
	} else if ($previo <  $row_delmes['dias']) {   
	 $a_tiempo = $a_tiempo + 1;
	} else if ($previo < $row_delmes['dias'] + 4) {  
	 $fuera_tiempo = $fuera_tiempo + 1;
	} else if ($previo >= $row_delmes['dias'] + 4) {
	 $muy_fuera_tiempo = $muy_fuera_tiempo + 1; 
	}
	
} while ($row_delmes = mysql_fetch_assoc($delmes)); 

if($totalRows_delmes != 0) {$antes_tiempop = round(($antes_tiempo / $totalRows_delmes) *100);} else {$antes_tiempop = 0;}
if($totalRows_delmes != 0) {$a_tiempop = round(($a_tiempo / $totalRows_delmes) *100);} else {$a_tiempop = 0;}
if($totalRows_delmes != 0) {$fuera_tiempop = round(($fuera_tiempo / $totalRows_delmes) *100);} else {$fuera_tiempop = 0;}
if($totalRows_delmes != 0) {$muy_fuera_tiempop = round(($muy_fuera_tiempo / $totalRows_delmes) *100);} else {$muy_fuera_tiempop = 0;}



//mes actual reporte global
mysql_select_db($database_vacantes, $vacantes);
$query_global = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus,  vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE (vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') AND vac_vacante.IDestatus IN (2,3)";
$global = mysql_query($query_global, $vacantes) or die(mysql_error());
$row_global = mysql_fetch_assoc($global);
$totalRows_global = mysql_num_rows($global);


//variables en 0
$antes_tiempot = 0;
$a_tiempot = 0;
$fuera_tiempot = 0;
$muy_fuera_tiempot = 0;


// recorremos cada vacante
do { 

 $startdatet = date('Y/m/d', strtotime($row_global['fecha_requi']));
 $end_datet =  date('Y/m/d', strtotime($row_global['fecha_ocupacion']));

 $previot = getWorkingDays($startdatet, $end_datet, $holidays);
                             
  // aplicamos ajuste de dias;
  $ajuste_diast = $row_global['ajuste_dias'];
  if ($ajuste_diast != 0) { $previot = $previot - $ajuste_diast; } 
  
  // resultado grafica
     if ($previot < 4) {  
	 $antes_tiempot = $antes_tiempot + 1;
	} else if ($previot <  $row_global['dias']) {   
	 $a_tiempot = $a_tiempot + 1;
	} else if ($previot < $row_global['dias'] + 4) {  
	 $fuera_tiempot = $fuera_tiempot + 1;
	} else if ($previot >= $row_global['dias'] + 4) {
	 $muy_fuera_tiempot = $muy_fuera_tiempot + 1; 
	}
	
} while ($row_global = mysql_fetch_assoc($global)); 

if ($totalRows_global != 0) {
$antes_tiempotp = round(($antes_tiempot / $totalRows_global) *100);
$a_tiempotp = round(($a_tiempot / $totalRows_global) *100);
$fuera_tiempotp = round(($fuera_tiempot / $totalRows_global) *100);
$muy_fuera_tiempotp = round(($muy_fuera_tiempot / $totalRows_global) *100);
} else{
$antes_tiempotp = 0;
$a_tiempotp = 0;
$fuera_tiempotp = 0;
$muy_fuera_tiempotp = 0;
}

// activas
mysql_select_db($database_vacantes, $vacantes);
$query_vacantes_activas = "SELECT * FROM vac_vacante WHERE (vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') AND vac_vacante.IDestatus = 1";
$vacantes_activas = mysql_query($query_vacantes_activas, $vacantes) or die(mysql_error());
$row_vacantes_activas = mysql_fetch_assoc($vacantes_activas);
$totalRows_vacantes_activas = mysql_num_rows($vacantes_activas);

// cubiertas
mysql_select_db($database_vacantes, $vacantes);
$query_vacantes_cubiertas = "SELECT * FROM vac_vacante WHERE (vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') AND vac_vacante.IDestatus IN (2,3)";
$vacantes_cubiertas = mysql_query($query_vacantes_cubiertas, $vacantes) or die(mysql_error());
$row_vacantes_cubiertas = mysql_fetch_assoc($vacantes_cubiertas);
$totalRows_vacantes_cubiertas = mysql_num_rows($vacantes_cubiertas);

// activas fuera de tiempo
mysql_select_db($database_vacantes, $vacantes);
$query_vacantes_fueras = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus,  vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE (vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') AND vac_vacante.IDestatus = 1";
$vacantes_fueras = mysql_query($query_vacantes_fueras, $vacantes) or die(mysql_error());
$row_vacantes_fueras = mysql_fetch_assoc($vacantes_fueras);
$totalRows_vacantes_fueras = mysql_num_rows($vacantes_fueras);

//variables en 0
$fuera_tiempox = 0;
$muy_fuera_tiempox = 0;


// recorremos cada vacante
do { 

 $startdatex = date('Y/m/d', strtotime($row_vacantes_fueras['fecha_requi']));
 $end_datex =  date('Y/m/d');

 $previox = getWorkingDays($startdatex, $end_datex, $holidays);
                             
  
  // resultado grafica
	if (($previox > $row_vacantes_fueras['dias']) &&  ($previox < $row_vacantes_fueras['dias']+ 4)) {  
	 $fuera_tiempox = $fuera_tiempox + 1;
	} else if ($previox > $row_vacantes_fueras['dias'] + 4) {
	 $muy_fuera_tiempox = $muy_fuera_tiempox + 1; 
	}
	
} while ($row_vacantes_fueras = mysql_fetch_assoc($vacantes_fueras)); 

//mes diferente para grafica
mysql_select_db($database_vacantes, $vacantes);
$query_otromes = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus,  vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE month(fecha_ocupacion) = '$otro_mes' AND (vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')";
$otromes = mysql_query($query_otromes, $vacantes) or die(mysql_error());
$row_otromes = mysql_fetch_assoc($otromes);
$totalRows_otromes = mysql_num_rows($otromes);



//variables en 0
$antes_tiempoy = 0;
$a_tiempoy = 0;
$fuera_tiempoy = 0;
$muy_fuera_tiempoy = 0;


// recorremos cada vacante
do { 

 $startdatey = date('Y/m/d', strtotime($row_otromes['fecha_requi']));
 $end_datey =  date('Y/m/d', strtotime($row_otromes['fecha_ocupacion']));

 $previoy = getWorkingDays($startdatey, $end_datey, $holidays);
                             
  // aplicamos ajuste de dias;
  $ajuste_diasy = $row_otromes['ajuste_dias'];
  if ($ajuste_diasy != 0) { $previoy = $previoy - $ajuste_diasy; } 
  
  // resultado grafica
     if ($previoy < 4) {  
	 $antes_tiempoy = $antes_tiempoy + 1;
	} else if ($previoy <  $row_otromes['dias']) {   
	 $a_tiempoy = $a_tiempoy + 1;
	} else if ($previoy < $row_otromes['dias'] + 4) {  
	 $fuera_tiempoy = $fuera_tiempoy + 1;
	} else if ($previoy 	>= $row_otromes['dias']) {
	 $muy_fuera_tiempoy = $muy_fuera_tiempoy + 1; 
	}
	
} while ($row_otromes = mysql_fetch_assoc($otromes)); 



if ($totalRows_otromes != 0) {
$a = $antes_tiempoy * 2;
$b = $a_tiempoy * 1;
$c = 0;
$d = $muy_fuera_tiempoy * -0.5;
$e = $a + $b + $c + $d;
$f = ($e / $totalRows_otromes) *100;
if ( $f > 90) {$calificacion = "Sobresaliente";} elseif ( $f > 80) {$calificacion = "Satisfactorio"; } elseif ( $f > 70) {$calificacion = "Suficiente"; } else {$calificacion = "Deficiente"; }
if ( $f > 90) {$color = "info";} elseif ( $f > 80) {$color = "success"; } elseif ( $f > 70) {$color = "warning"; } else {$color  = "danger"; }
} else {
$calificacion = "Sin vacantes en el mes";
}

// el mes
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

// el mes
  switch ($otro_mes) {
    case 1:  $otromes = "Enero";      break;     
    case 2:  $otromes = "Febrero";    break;    
    case 3:  $otromes = "Marzo";      break;    
    case 4:  $otromes = "Abril";      break;    
    case 5:  $otromes = "Mayo";       break;    
    case 6:  $otromes = "Junio";      break;    
    case 7:  $otromes = "Julio";      break;    
    case 8:  $otromes = "Agosto";     break;    
    case 9:  $otromes = "Septiembre"; break;    
    case 10: $otromes = "Octubre";    break;    
    case 11: $otromes = "Noviembre";  break;    
    case 12: $otromes = "Diciembre";  break;   
      }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
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

	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/sucursal.js"></script>
	<script src="global_assets/js/sucursal2.js"></script>
	<script src="global_assets/js/area.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	
    <!-- /theme JS files -->
    <script type="text/javascript">
    var antes_tiempo = <?php echo $antes_tiempoy; ?>;
    var a_tiempo = <?php echo $a_tiempoy; ?>;
    var fuera_tiempo = <?php echo $fuera_tiempoy; ?>;
    var muy_fuera_tiempo = <?php echo $muy_fuera_tiempoy; ?>;
	</script>
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
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Bienvenido(a) <?php echo $row_usuario['usuario_nombre']; ?> al <?php echo $row_variables['nombre_sistema']; ?>.</p>
							<p>El objetivo del Sistema es lograr un adecuado control y seguimiento del proceso de reclutamiento y selección, asegurando que la contratación del personal cubra con el perfil requerido y 
                            en el tiempo establecido en el Manual de Reclutamiento y Selección.</p>
                            <p>Asimismo, el sistema permite determinar acciones de acuerdo al tiempo del cubrimiento de vacantes, tales como:</p>
												<ul>
                                                <li>Apoyo corporativo en las Sucursales.</li>
												<li>Programa de retención asistida.</li>
												<li>Apoyo de la Gerencia de Sucursal.</li>
                                                </ul> 
                   </div>
                  </div>
                  <!-- Statistics with progress bar -->
                  <!-- /statistics with progress bar -->


				  <div class="row">
                    <div class="col-md-6">
                                            
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="text-semibold panel-title">
										<i class="icon-folder6 position-left"></i>
										Archivos disponibles para descarga
									</h6>
								</div>
								
								<div class="list-group no-border">
									<a href="http://intranet.sahuayo.mx/rys/15.%20Procedimientos/MANUAL.pdf" target="_blank" class="list-group-item">
										<i class="icon-file-pdf"></i> Manual de Reclutamiento y Selección.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/4.%20Entrevista%20General/corporativo/Reporte%20de%20Entrevista.pptx" target="_blank"  class="list-group-item">
										<i class=" icon-file-presentation"></i> Guia de Entrevista por Competencias.ppt <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/6.%20Entrevista%20Competencias/Entrevista%20por%20Competencias.xlsx" target="_blank"  class="list-group-item">
										<i class="icon-file-excel"></i> Formato de Entrevista por Competencias.xlsx <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/6.%20Entrevista%20Competencias/Catalogo%20de%20Competencias.pdf" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Catálogo de Competencias Laborales.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/6.%20Entrevista%20Competencias/Diccionario%20de%20Competencias%20y%20Comportamientos%20Sahuayo.pdf" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Diccionario de Competencias Laborales.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/16.%20Requi/formato.xls" target="_blank"  class="list-group-item">
										<i class="icon-file-pdf"></i> Formato de Requisición de Personal.pdf <span class="label bg-success-400">Nuevo</span>
									</a>

									<a href="http://intranet.sahuayo.mx/rys/16.%20Requi/Reporte.xls"  target="_blank" class="list-group-item">
										<i class="icon-file-excel"></i> Versión previa del Reporte de Vacantes.xlsx
									</a>
								</div>
							</div>
                     
                     
                     </div>

						<div class="col-md-6">
							<div class="panel panel-flat">
                            <!-- Simple inline block with icon and button -->
								<div class="media no-margin stack-media-on-mobile">
									<div class="media-left media-middle">
										<i class="icon-lifebuoy icon-3x text-muted no-edge-top"></i>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-semibold">¿Tienes dudas o sugerencias?</h6>
										<span class="text-muted">Contactanos por correo electrónico a <a href="mailto:jacardenas@sahuayo.mx">jacardenas@sahuayo.mx</a></br>
                                                                 O por teléfono a la extensión 1221.</br>
                                                                 Nos pondremos en contacto contigo a la brevedad.</span>
									</div>
								</div>
							<!-- /simple inline block with icon and button -->
                          </div>

							<div class="panel panel-flat">
                            <!-- Widget with centered text and colored icon -->
								<div class="panel-body text-center">
									<div class="content-group mt-5">
									</div>
									<a href="general_faq.php"><div class="icon-object border-success text-success"><i class="icon-book"></i></div></a>
									<h6 class="text-semibold"><a href="general_faq.php" class="text-default">Preguntas Frecuentes</a></h6>
									<p class="mb-15">Todas las dudas acerca del uso del sistema, las puedes consultar en las <a href="general_faq.php">FAQs &rarr;</a></p>
								</div>
							<!-- /widget with centered text and colored icon -->							
                            </div>


						</div>
				  </div>
					<!-- /panel heading options -->

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