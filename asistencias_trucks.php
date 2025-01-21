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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$IDmatriz = $_POST['IDmatriz'];
$IDfecha = $_POST['IDfecha'];

$updateSQL = sprintf("UPDATE ind_asistencia_trucks SET disponibles=%s, en_ruta=%s, cargados_sin_salir=%s, en_taller=%s, IDcapturador=%s, comentarios=%s WHERE IDmatriz = $IDmatriz AND IDfecha = $IDfecha",
                       GetSQLValueString($_POST['disponibles'], "int"),
                       GetSQLValueString($_POST['en_ruta'], "int"),
                       GetSQLValueString($_POST['cargados_sin_salir'], "int"),
                       GetSQLValueString($_POST['en_taller'], "int"),
                       GetSQLValueString($_POST['IDcapturador'], "text"),
                       GetSQLValueString($_POST['comentarios'], "text"),
                       GetSQLValueString($_POST['IDmatriz'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: asistencias_trucks.php?info=2");
}


$query_fecha1 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 1";
$fecha1 = mysql_query($query_fecha1, $vacantes) or die(mysql_error());
$row_fecha1 = mysql_fetch_assoc($fecha1);
$fecha1 = $row_fecha1['IDcapturador'];

$query_fecha2 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 2";
$fecha2 = mysql_query($query_fecha2, $vacantes) or die(mysql_error());
$row_fecha2 = mysql_fetch_assoc($fecha2);
$fecha2 = $row_fecha2['IDcapturador'];

$query_fecha3 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 3";
$fecha3 = mysql_query($query_fecha3, $vacantes) or die(mysql_error());
$row_fecha3 = mysql_fetch_assoc($fecha3);
$fecha3 = $row_fecha3['IDcapturador'];

$query_fecha4 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 4";
$fecha4 = mysql_query($query_fecha4, $vacantes) or die(mysql_error());
$row_fecha4 = mysql_fetch_assoc($fecha4);
$fecha4 = $row_fecha4['IDcapturador'];

$query_fecha5 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 5";
$fecha5 = mysql_query($query_fecha5, $vacantes) or die(mysql_error());
$row_fecha5 = mysql_fetch_assoc($fecha5);
$fecha5 = $row_fecha5['IDcapturador'];

$query_fecha6 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 6";
$fecha6 = mysql_query($query_fecha6, $vacantes) or die(mysql_error());
$row_fecha6 = mysql_fetch_assoc($fecha6);
$fecha6 = $row_fecha6['IDcapturador'];

$query_fecha7 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 7";
$fecha7 = mysql_query($query_fecha7, $vacantes) or die(mysql_error());
$row_fecha7 = mysql_fetch_assoc($fecha7);
$fecha7 = $row_fecha7['IDcapturador'];

$query_fecha8 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 8";
$fecha8 = mysql_query($query_fecha8, $vacantes) or die(mysql_error());
$row_fecha8 = mysql_fetch_assoc($fecha8);
$fecha8 = $row_fecha8['IDcapturador'];

$query_fecha9 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 9";
$fecha9 = mysql_query($query_fecha9, $vacantes) or die(mysql_error());
$row_fecha9 = mysql_fetch_assoc($fecha9);
$fecha9 = $row_fecha9['IDcapturador'];

$query_fecha10 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 10";
$fecha10 = mysql_query($query_fecha10, $vacantes) or die(mysql_error());
$row_fecha10 = mysql_fetch_assoc($fecha10);
$fecha10 = $row_fecha10['IDcapturador'];

$query_fecha11 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 11";
$fecha11 = mysql_query($query_fecha11, $vacantes) or die(mysql_error());
$row_fecha11 = mysql_fetch_assoc($fecha11);
$fecha11 = $row_fecha11['IDcapturador'];

$query_fecha12 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 12";
$fecha12 = mysql_query($query_fecha12, $vacantes) or die(mysql_error());
$row_fecha12 = mysql_fetch_assoc($fecha12);
$fecha12 = $row_fecha12['IDcapturador'];

$query_fecha13 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 13";
$fecha13 = mysql_query($query_fecha13, $vacantes) or die(mysql_error());
$row_fecha13 = mysql_fetch_assoc($fecha13);
$fecha13 = $row_fecha13['IDcapturador'];

$query_fecha14 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 14";
$fecha14 = mysql_query($query_fecha14, $vacantes) or die(mysql_error());
$row_fecha14 = mysql_fetch_assoc($fecha14);
$fecha14 = $row_fecha14['IDcapturador'];

$query_fecha15 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 15";
$fecha15 = mysql_query($query_fecha15, $vacantes) or die(mysql_error());
$row_fecha15 = mysql_fetch_assoc($fecha15);
$fecha15 = $row_fecha15['IDcapturador'];

$query_fecha16 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 16";
$fecha16 = mysql_query($query_fecha16, $vacantes) or die(mysql_error());
$row_fecha16 = mysql_fetch_assoc($fecha16);
$fecha16 = $row_fecha16['IDcapturador'];

$query_fecha17 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 17";
$fecha17 = mysql_query($query_fecha17, $vacantes) or die(mysql_error());
$row_fecha17 = mysql_fetch_assoc($fecha17);
$fecha17 = $row_fecha17['IDcapturador'];

$query_fecha18 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 18";
$fecha18 = mysql_query($query_fecha18, $vacantes) or die(mysql_error());
$row_fecha18 = mysql_fetch_assoc($fecha18);
$fecha18 = $row_fecha18['IDcapturador'];

$query_fecha19 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 19";
$fecha19 = mysql_query($query_fecha19, $vacantes) or die(mysql_error());
$row_fecha19 = mysql_fetch_assoc($fecha19);
$fecha19 = $row_fecha19['IDcapturador'];

$query_fecha20 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 20";
$fecha20 = mysql_query($query_fecha20, $vacantes) or die(mysql_error());
$row_fecha20 = mysql_fetch_assoc($fecha20);
$fecha20 = $row_fecha20['IDcapturador'];

$query_fecha21 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 21";
$fecha21 = mysql_query($query_fecha21, $vacantes) or die(mysql_error());
$row_fecha21 = mysql_fetch_assoc($fecha21);
$fecha21 = $row_fecha21['IDcapturador'];

$query_fecha22 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 22";
$fecha22 = mysql_query($query_fecha22, $vacantes) or die(mysql_error());
$row_fecha22 = mysql_fetch_assoc($fecha22);
$fecha22 = $row_fecha22['IDcapturador'];

$query_fecha23 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 23";
$fecha23 = mysql_query($query_fecha23, $vacantes) or die(mysql_error());
$row_fecha23 = mysql_fetch_assoc($fecha23);
$fecha23 = $row_fecha23['IDcapturador'];

$query_fecha24 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 24";
$fecha24 = mysql_query($query_fecha24, $vacantes) or die(mysql_error());
$row_fecha24 = mysql_fetch_assoc($fecha24);
$fecha24 = $row_fecha24['IDcapturador'];

$query_fecha25 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 25";
$fecha25 = mysql_query($query_fecha25, $vacantes) or die(mysql_error());
$row_fecha25 = mysql_fetch_assoc($fecha25);
$fecha25 = $row_fecha25['IDcapturador'];

$query_fecha26 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 26";
$fecha26 = mysql_query($query_fecha26, $vacantes) or die(mysql_error());
$row_fecha26 = mysql_fetch_assoc($fecha26);
$fecha26 = $row_fecha26['IDcapturador'];

$query_fecha27 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 27";
$fecha27 = mysql_query($query_fecha27, $vacantes) or die(mysql_error());
$row_fecha27 = mysql_fetch_assoc($fecha27);
$fecha27 = $row_fecha27['IDcapturador'];

$query_fecha28 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 28";
$fecha28 = mysql_query($query_fecha28, $vacantes) or die(mysql_error());
$row_fecha28 = mysql_fetch_assoc($fecha28);
$fecha28 = $row_fecha28['IDcapturador'];

$query_fecha29 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 29";
$fecha29 = mysql_query($query_fecha29, $vacantes) or die(mysql_error());
$row_fecha29 = mysql_fetch_assoc($fecha29);
$fecha29 = $row_fecha29['IDcapturador'];

$query_fecha30 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 30";
$fecha30 = mysql_query($query_fecha30, $vacantes) or die(mysql_error());
$row_fecha30 = mysql_fetch_assoc($fecha30);
$fecha30 = $row_fecha30['IDcapturador'];

$query_fecha31 = "SELECT * FROM ind_asistencia_trucks WHERE IDmatriz = $IDmatriz AND IDfecha = 31";
$fecha31 = mysql_query($query_fecha31, $vacantes) or die(mysql_error());
$row_fecha31 = mysql_fetch_assoc($fecha31);
$fecha31 = $row_fecha31['IDcapturador'];



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

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha guardado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Sorting data -->
					<div class="panel panel-flat">

						<div class="panel-body">
							<p class="content-group"><b>Instrucciones</b><br />
							Selecciona el día del mes de noviembre para capturar la disponibildiad de Unidades (Camioneta, Torton, Rabón, Trailer).<br />
							<span class="text text-success"><i class="icon icon-truck"></i> </span> = días capturados.<br />
							<span class="text text-danger"><i class="icon icon-truck"></i> </span> = días no capturados.</p>
														
								<table height='600' class="table table-bordered table-striped">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th style="height:15px">Lunes</th>
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
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '31')">31 
									  <?php if($fecha31 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '1')">1 
									  <?php if($fecha1 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '2')">2 
									  <?php if($fecha2 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '3')">3 
									  <?php if($fecha3 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '4')">4 
									  <?php if($fecha4 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '5')">5 
									  <?php if($fecha5 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '6')">6 
									  <?php if($fecha6 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                    </tr>
                                    <tr>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '7')">7 
									  <?php if($fecha7 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '8')">8 
									  <?php if($fecha8 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '9')">9 
									  <?php if($fecha9 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '10')">10 
									  <?php if($fecha10 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '11')">11 
									  <?php if($fecha11 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '12')">12 
									  <?php if($fecha12 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '13')">13 
									  <?php if($fecha13 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                    </tr>
                                    <tr>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '14')">14 
									  <?php if($fecha14 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '15')">15 
									  <?php if($fecha15 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '16')">16 
									  <?php if($fecha16 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '17')">17 
									  <?php if($fecha17 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '18')">18 
									  <?php if($fecha18 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '19')">19 
									  <?php if($fecha19 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '20')">20 
									  <?php if($fecha20 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                    </tr>
                                    <tr>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '21')">21 
									  <?php if($fecha21 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '22')">22 
									  <?php if($fecha22 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '23')">23 
									  <?php if($fecha23 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '24')">24 
									  <?php if($fecha24 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '25')">25 
									  <?php if($fecha25 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '26')">26 
									  <?php if($fecha26 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
									  <?php } ?>
									  </div></a></td>
                                      <td><a><div style="height:100%;width:100%" onClick="loadDynamicContentModal('<?php echo $IDmatriz; ?>', '27')">27 
									  <?php if($fecha27 != '') { ?>
										<span class="text text-success"><i class="icon icon-truck"></i></span>
									  <?php } else { ?>
										<span class="text text-warning"><i class="icon icon-truck"></i></span>
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
							
						</div>
					</div>
					<!-- /sorting data -->
					
					
					<!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h3 class="modal-title">Unidades</h3>
								</div>
			              <div id="conte-modal"></div>
						</div>
					</div>
					<!-- /inline form modal -->
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
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>
<script>
function loadDynamicContentModal(modal, IDfecha){
	var options = { modal: true };
	$('#conte-modal').load('asistencias_mdlC.php?IDfecha=' + IDfecha + '&IDMatriz='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
