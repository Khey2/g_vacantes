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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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
$IDusuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


	
for ($fecha_filtro = 1; $fecha_filtro <= 31; $fecha_filtro++) {

	if ($fecha_filtro == 31) {$fecha_filtro2 = '2022-10-31';} else {$fecha_filtro2 = '2022-11-'.$fecha_filtro;}

	$query_activostotales = "SELECT * FROM prod_activosfaltas WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' OR DATE(prod_activosfaltas.fecha_antiguedad) > '$fecha_filtro2')";
	$activostotales = mysql_query($query_activostotales, $vacantes) or die(mysql_error());
	$row_activostotales = mysql_fetch_assoc($activostotales);
	$totalRows_activostotales = mysql_num_rows($activostotales);

	$query_activoscapturados = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.fecha_baja, prod_activosfaltas.fecha_alta, ind_asistencia.IDvalidador,  prod_activosfaltas.IDmatriz, prod_activosfaltas.IDpuesto, prod_activosfaltas.IDarea, ind_asistencia.IDasistencia, ind_asistencia.IDcapturador,  ind_asistencia.IDvalidador, ind_asistencia.IDestatus FROM prod_activosfaltas LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado AND ind_asistencia.IDfecha = '$fecha_filtro' WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND ind_asistencia.IDcapturador <> ''";
	$activoscapturados = mysql_query($query_activoscapturados, $vacantes) or die(mysql_error());
	$row_activoscapturados = mysql_fetch_assoc($activoscapturados);
	$totalRows_activoscapturados = mysql_num_rows($activoscapturados);


	if($totalRows_activostotales == $totalRows_activoscapturados) { ${"fecha".$fecha_filtro} = 1; } else { ${"fecha".$fecha_filtro} = 0; }

	 echo "Fecha Filtro: ".$fecha_filtro."<br/>";
	 echo "Fecha : ".${"fecha".$fecha_filtro}."<br/>";
	 echo "Activos: ".$totalRows_activostotales."<br/>";
	 echo "Capturados: ".$totalRows_activoscapturados."<br/>"."<br/>";

}



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	</head>
<body>
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


					<!-- Sorting data -->
					<div class="panel panel-flat">

						<div class="panel-body">
							<H3>Asistencia Operaciones</H3>
							<p class="content-group"><b>Instrucciones</b><br />
							Selecciona el día del mes de noviembre para VALIDAR la asistencia de todos los Empleados.<br />
							Solamente aplica para puestos del área de Operaciones (Almacén y Distribución).<br />
							<span class="text text-success"><i class="icon-user-check"></i> </span> = días capturados.<br />
							<span class="text text-danger"><i class="icon-user-cancel"></i> </span> = días no capturados.</p>
							
							<h5>Noviembre</h5>
							
							
							 <form method="post">
								<table height='600' class="table table-bordered table-striped">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>Lunes</th>
                                    <th>Martes</th>
                                    <th>Miercoles</th>
                                    <th>Jueves</th>
                                    <th>Viernes</th>
                                    <th>Sabado</th>
                                    <th>Domingo</th>
                                  </tr>
                                  </thead>
                                <tbody>
                                    <tr>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=10&IDfecha=31"><div style="height:100%;width:100%">31 
									  <?php if($fecha31 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=1"><div style="height:100%;width:100%">1 
									  <?php if($fecha1 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=2"><div style="height:100%;width:100%">2
									  <?php if($fecha2 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=3"><div style="height:100%;width:100%">3
									  <?php if($fecha3 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=4"><div style="height:100%;width:100%">4
									  <?php if($fecha4 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=5"><div style="height:100%;width:100%">5
									  <?php if($fecha5 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=6"><div style="height:100%;width:100%">6
									  <?php if($fecha6 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                    </tr>
                                    <tr>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=7"><div style="height:100%;width:100%">7
									  <?php if($fecha7 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=8"><div style="height:100%;width:100%">8
									  <?php if($fecha8 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=9"><div style="height:100%;width:100%">9	
									  <?php if($fecha9 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=10"><div style="height:100%;width:100%">10		
									  <?php if($fecha10 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=11"><div style="height:100%;width:100%">11	
									  <?php if($fecha11 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=12"><div style="height:100%;width:100%">12	
									  <?php if($fecha12 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=13"><div style="height:100%;width:100%">13	
									  <?php if($fecha13 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                    </tr>
                                    <tr>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=14"><div style="height:100%;width:100%">14	
									  <?php if($fecha14 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=15"><div style="height:100%;width:100%">15		
									  <?php if($fecha15 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=16"><div style="height:100%;width:100%">16	
									  <?php if($fecha16 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=17"><div style="height:100%;width:100%">17	
									  <?php if($fecha17 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=18"><div style="height:100%;width:100%">18	
									  <?php if($fecha18 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=19"><div style="height:100%;width:100%">19		
									  <?php if($fecha19 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=20"><div style="height:100%;width:100%">20	
									  <?php if($fecha20 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                    </tr>
                                    <tr>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=21"><div style="height:100%;width:100%">21		
									  <?php if($fecha21 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=22"><div style="height:100%;width:100%">22	
									  <?php if($fecha22 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=23"><div style="height:100%;width:100%">23
									  <?php if($fecha23 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=24"><div style="height:100%;width:100%">24
									  <?php if($fecha24 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=25"><div style="height:100%;width:100%">25
									  <?php if($fecha25 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=26"><div style="height:100%;width:100%">26
									  <?php if($fecha26 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a href="asistencias_editB.php?IDmatriz=<?php echo $IDmatriz ?>&mes=11&IDfecha=27"><div style="height:100%;width:100%">27	
									  <?php if($fecha27 == 1) { ?>
										<span class="text text-success"><i class="icon-user-check"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon-user-cancel"></i></span>
									  <?php } ?>
									  </div></a></td>
                                    </tr>
                                    <tr>
                                      <td><div style="height:100%;width:100%"><span class="text text-muted">28</span></div></td>
                                      <td><div style="height:100%;width:100%"><span class="text text-muted">29</span></div></td>
                                      <td><div style="height:100%;width:100%"><span class="text text-muted">30</span></div></td>
                                      <td><div style="height:100%;width:100%"><span class="text text-muted">1</span></div></td>
                                      <td><div style="height:100%;width:100%"><span class="text text-muted">2</span></div></td>
                                      <td><div style="height:100%;width:100%"><span class="text text-muted">3</span></div></td>
                                      <td><div style="height:100%;width:100%"><span class="text text-muted">4</span></div></td>
                                    </tr>
                                  </tbody>
                                </table>
							</form> 							
							
						</div>
					</div>
					<!-- /sorting data -->

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
