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
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

$el_mes = date("m");
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

//Mes Actual Motivos Involuntarios
mysql_select_db($database_vacantes, $vacantes);
$query_mot1 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 1 AND month(fecha_requi) = '$el_mes'";
$mot1 = mysql_query($query_mot1, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot1);

mysql_select_db($database_vacantes, $vacantes);
$query_mot2 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 2 AND month(fecha_requi) = '$el_mes'";
$mot2 = mysql_query($query_mot2, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot2);

mysql_select_db($database_vacantes, $vacantes);
$query_mot3 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 3 AND month(fecha_requi) = '$el_mes'";
$mot3 = mysql_query($query_mot3, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot3);

mysql_select_db($database_vacantes, $vacantes);
$query_mot4 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 4 AND month(fecha_requi) = '$el_mes'";
$mot4 = mysql_query($query_mot4, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot4);

mysql_select_db($database_vacantes, $vacantes);
$query_mot5 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 5 AND month(fecha_requi) = '$el_mes'";
$mot5 = mysql_query($query_mot5, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot5);

mysql_select_db($database_vacantes, $vacantes);
$query_mot6 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 6 AND month(fecha_requi) = '$el_mes'";
$mot6 = mysql_query($query_mot6, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot6);

mysql_select_db($database_vacantes, $vacantes);
$query_mot7 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 7 AND month(fecha_requi) = '$el_mes'";
$mot7 = mysql_query($query_mot7, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot7);

mysql_select_db($database_vacantes, $vacantes);
$query_mot8 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 8 AND month(fecha_requi) = '$el_mes'";
$mot8 = mysql_query($query_mot8, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot8);

mysql_select_db($database_vacantes, $vacantes);
$query_mot9 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 9 AND month(fecha_requi) = '$el_mes'";
$mot9 = mysql_query($query_mot9, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot9);

mysql_select_db($database_vacantes, $vacantes);
$query_mot10 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 10 AND month(fecha_requi) = '$el_mes'";
$mot10 = mysql_query($query_mot10, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot10);

mysql_select_db($database_vacantes, $vacantes);
$query_mot11 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 11 AND month(fecha_requi) = '$el_mes'";
$mot11 = mysql_query($query_mot11, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot11);

mysql_select_db($database_vacantes, $vacantes);
$query_mot12 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 12 AND month(fecha_requi) = '$el_mes'";
$mot12 = mysql_query($query_mot12, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot12);

mysql_select_db($database_vacantes, $vacantes);
$query_mot13 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 13 AND month(fecha_requi) = '$el_mes'";
$mot13 = mysql_query($query_mot13, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot13);

mysql_select_db($database_vacantes, $vacantes);
$query_mot14 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 14 AND month(fecha_requi) = '$el_mes'";
$mot14 = mysql_query($query_mot14, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot14);

mysql_select_db($database_vacantes, $vacantes);
$query_mot15 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 15 AND month(fecha_requi) = '$el_mes'";
$mot15 = mysql_query($query_mot15, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot15);

mysql_select_db($database_vacantes, $vacantes);
$query_mot16 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 16 AND month(fecha_requi) = '$el_mes'";
$mot16 = mysql_query($query_mot16, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot16);

mysql_select_db($database_vacantes, $vacantes);
$query_mot17 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 17 AND month(fecha_requi) = '$el_mes'";
$mot17 = mysql_query($query_mot17, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot17);

mysql_select_db($database_vacantes, $vacantes);
$query_mot18 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 18 AND month(fecha_requi) = '$el_mes'";
$mot18 = mysql_query($query_mot18, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot18);

mysql_select_db($database_vacantes, $vacantes);
$query_mot19 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 19 AND month(fecha_requi) = '$el_mes'";
$mot19 = mysql_query($query_mot19, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot19);

mysql_select_db($database_vacantes, $vacantes);
$query_mot20 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 20 AND month(fecha_requi) = '$el_mes'";
$mot20 = mysql_query($query_mot20, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot20);

mysql_select_db($database_vacantes, $vacantes);
$query_mot21 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 21 AND month(fecha_requi) = '$el_mes'";
$mot21 = mysql_query($query_mot21, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot21);

mysql_select_db($database_vacantes, $vacantes);
$query_mot22 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 22 AND month(fecha_requi) = '$el_mes'";
$mot22 = mysql_query($query_mot22, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot22);

mysql_select_db($database_vacantes, $vacantes);
$query_mot23 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 23 AND month(fecha_requi) = '$el_mes'";
$mot23 = mysql_query($query_mot23, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot23);

mysql_select_db($database_vacantes, $vacantes);
$query_mot24 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 24 AND month(fecha_requi) = '$el_mes'";
$mot24 = mysql_query($query_mot24, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot24);

mysql_select_db($database_vacantes, $vacantes);
$query_mot25 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 25 AND month(fecha_requi) = '$el_mes'";
$mot25 = mysql_query($query_mot25, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot25);



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

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

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
	<script src="https://www.gstatic.com/charts/loader.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="assets/motivos.js"></script>
	<script src="assets/areas.js"></script>
	<!-- /theme JS files -->

	
    <!-- /theme JS files -->
    <script type="text/javascript">
    var antes_tiempo = <?php echo $antes_tiempoy; ?>;
    var a_tiempo = <?php echo $a_tiempoy; ?>;
    var fuera_tiempo = <?php echo $fuera_tiempoy; ?>;
    var muy_fuera_tiempo = <?php echo $muy_fuera_tiempoy; ?>;
	</script>
</head>
<body>
	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="panel.php"><img src="global_assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

			<p class="navbar-text">
				<span class="label bg-success">Online</span>
			</p>

			<div class="navbar-right">
				<ul class="nav navbar-nav">


					<li class="dropdown dropdown-user">
						<a class="dropdown-toggle" data-toggle="dropdown">
							<img src="global_assets/images/placeholders/placeholder.jpg" alt="">
							<span><?php echo $row_usuario['usuario_nombre']; ?></span>
							<i class="caret"></i>
						</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="mi_perfil.php"><i class="icon-user-plus"></i>Mi Perfil</a></li>
							<li><a href="mi_matriz.php"><i class="icon-cog5"></i>Sucursales</a></li>
							<li><a href="general_faq.php"><i class="icon-help"></i>Ayuda</a></li>
							<li class="divider"></li>
							<li><a href="logout.php"><i class="icon-switch2"></i> Salir</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></a>
								<div class="media-body">
									<span class="media-heading text-semibold"><?php echo $row_usuario['usuario_nombre']; ?></span>
									<div class="text-size-mini text-muted">
										<i class="icon-pin text-size-small"></i> <?php echo $row_matriz['matriz']; ?>
									</div>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<a href="mi_matriz.php"><i class="icon-cog3"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<!-- Main -->
								<li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
								<li><a href="panel.php"><i class="icon-home4"></i> <span>Inicio</span></a></li>
								<li>
									<a href="#"><i class="icon-stack2"></i> <span>Vacantes</span></a>
									<ul>
										<li><a href="vacantes_activas.php">Activas</a></li>
										<li><a href="vacantes_cerradas.php">Cerradas</a></li>
										<li><a href="vacantes_totales.php">Todas</a></li>
                                        <li><a href="vacante_edit.php">Agregar</a></li>
									</ul>
								</li>
								<li>
									<a href="#"><i class="icon-pie-chart"></i> <span>Indicadores</span></a>
									<ul>
										<li class="active"><a href="indicadores_actual.php">Mes actual</a></li>										
                                        <li><a href="indicadores_anteriores.php">Meses anteriores</a></li>
									</ul>
								</li>
								<?php if( $row_usuario['nivel_acceso'] > 1) {?>
								<li>
									<a href="#"><i class="icon-wrench"></i> <span>Administración</span></a>
									<ul>
										<li><a href="admin_usuarios.php">Usuarios</a></li>
										<li><a href="admin_vacantes.php">Vacantes</a></li>
									</ul>
								</li>
								<?php } ?>
								<?php if( $row_usuario['nivel_acceso'] == 3) {?>
								<li>
									<a href="#"><i class="icon-wrench2"></i> <span>Master Admin</span></a>
									<ul>
										<li><a href="admin_usuariosm.php">Usuarios</a></li>
										<li><a href="admin_vacantesm.php">Vacantes</a></li>
										<li><a href="admin_areasm.php">Areas</a></li>
										<li><a href="admin_estatusm.php">Estatus</a></li>
										<li><a href="admin_fuentesm.php">Fuentes</a></li>
										<li><a href="admin_causasm.php">Causas Baja</a></li>
										<li><a href="admin_motivosm.php">Motivos Baja</a></li>
										<li><a href="admin_mesesm.php">Meses</a></li>
										<li><a href="admin_matricesm.php">Matrices</a></li>
										<li><a href="admin_tiposm.php">Tipos Vacantes</a></li>
										<li><a href="admin_turnosm.php">Turnos</a></li>
										<li><a href="admin_variablesm.php">Variables</a></li>
									</ul>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header page-header-default">
					<div class="page-header-content">

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li class="active"><a href="panel.php"><i class="icon-home2 position-left"></i> Inicio</a></li>
						</ul>

					</div>
				</div>				
             <!-- /page header -->
            <p>&nbsp;</p>

	                <!-- Content area -->
				<div class="content">
                
				  <!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
					<div class="label bg-danger-400"><strong><h5> Esta sección está en construccion... </h5></strong></div></p>
                            <p>A continuación podrás consultar el reporte de indicadores, mismo que está basado en las vacantes reportadas en el Sistema.</p>
							<p>&nbsp;</p>
                            <div class="row">
                  <!-- inicia seccion izquierda -->
                            <div class="col-md-6">
                            
					<!-- Column chart -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Motivos de Rotación</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">Los motivos de rotación pueden ser de dos tipos.</br> 
                            <strong>Voluntarios</strong>: cuando Sahuayo decide separar del puesto al colaborador.</br> 
                            <strong>Involuntarios</strong>: cuando es el colaborador el que decide salir de la Compañía.</p>

							<div class="chart-container">
								<div class="chart" id="google-column"></div>
							</div>
						</div>
					</div>
					<!-- /column chart --> 
                                               
                            </div>
                            
                  <!-- termina seccion izquierda -->
                  <!-- inicia seccion derecha -->
                            <div class="col-md-6">
                            
					<!-- Column chart -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación por Motivos</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">Los motivos de rotación pueden ser de dos tipos.</p>
                            <p><div class="label bg-danger-600"><strong>Voluntarios</strong></div> cuando Sahuayo decide separar del puesto al colaborador.</p>
                            <p><div class="label bg-primary-700"><strong>Involuntarios</strong></div> cuando es el colaborador el que decide salir de la Compañía.</p>

							<div class="chart-container">
								<div class="chart" id="google-bar"></div>
							</div>
						</div>
					</div>
					<!-- /column chart -->                            
                            
                            
                            </div>
                  <!-- termina seccion derecha -->
							</div>
                       </div>
                  </div>
                  <!-- Statistics with progress bar -->
                  <!-- /statistics with progress bar -->
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