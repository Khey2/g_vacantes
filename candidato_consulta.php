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
$mis_areas = $row_usuario['IDmatrizes'];$la_matriz = $row_usuario['IDmatriz'];
$las_matrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];

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

$IDusuario = $_GET['IDusuario'];
mysql_select_db($database_vacantes, $vacantes);
$query_candidatos = "SELECT * FROM cv_activos WHERE IDusuario = '$IDusuario'";
$candidatos = mysql_query($query_candidatos, $vacantes) or die(mysql_error());
$row_candidatos = mysql_fetch_assoc($candidatos);
$totalRows_candidatos = mysql_num_rows($candidatos);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);
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
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/select.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_print.js"></script>
	<!-- /theme JS files -->
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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
							<h5 class="panel-title">Candidatos registrados</h5>
						</div>

					<div class="panel-body">
							<p>A continuación se muestra el detalle del candidato.</br>
                            <a class="btn btn-success btn-xs" href="candidato_reporte.php?IDusuario=<?php echo $IDusuario; ?>">Descargar información completa</a></p>
                            
                      

							    <form method="post" id="form1" class="form-horizontal form-validate-jquery">
								<fieldset class="content-group">
        <h6><strong>Datos Personales</strong></h6>
      <div class="form-group"><label class="control-label col-lg-3">Nombre:</label><div class="col-lg-9">
      <?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno'] . " " . $row_candidatos['a_nombre']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Puesto de interés:</label><div class="col-lg-9"><?php echo $row_candidatos['puesto']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Fecha de Captura:</label><div class="col-lg-9"><?php echo date('d/m/Y', strtotime($row_candidatos['fecha_captura']));?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">RFC:</label><div class="col-lg-9"><?php echo $row_candidatos['a_rfc']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Sexo:</label><div class="col-lg-9"><?php if ($row_candidatos['a_sexo'] == 1) {echo "Hombre";} else {echo "Mujer";}; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Licencia:</label><div class="col-lg-9"><?php echo $row_candidatos['a_licencia']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Fecha Nacimiento:</label><div class="col-lg-9"><?php echo date('d/m/Y', strtotime($row_candidatos['c_fecha_nacimiento']));?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Escolaridad:</label><div class="col-lg-9"><?php switch ($row_candidatos['IDescolaridad']) {
																														case 0:  $escolaridad = "-";      break;     
																														case 1:  $escolaridad = "Primaria";      break;     
																														case 2:  $escolaridad = "Secundaria";    break;    
																														case 3:  $escolaridad = "Preparatoria / Técnico";      break;    
																														case 4:  $escolaridad = "Universidad";      break;    
																														case 5:  $escolaridad = "Especialidad / Diplomado";       break;    
																														case 6:  $escolaridad = "Maestría";      break;    
																														case 7:  $escolaridad = "Doctorado";      break;    
																														  }
																													echo $escolaridad; ?></div></div>
        <h6><strong>Direccion</strong></h6>
      <div class="form-group"><label class="control-label col-lg-3">Calle:</label><div class="col-lg-9"><?php echo $row_candidatos['d_calle']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">No.:</label><div class="col-lg-9"><?php echo $row_candidatos['d_numero_calle']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Colonia:</label><div class="col-lg-9"><?php echo $row_candidatos['d_colonia']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Municipio:</label><div class="col-lg-9"><?php echo $row_candidatos['d_delegacion_municipio']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Estado:</label><div class="col-lg-9"><?php echo $row_candidatos['d_estado']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">C.P.:</label><div class="col-lg-9"><?php echo $row_candidatos['d_codigo_postal']; ?></div></div>
        <h6><strong>Contacto</strong></h6>
      <div class="form-group"><label class="control-label col-lg-3">Correo:</label><div class="col-lg-9"><?php echo $row_candidatos['a_correo']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Teléfono 1:</label><div class="col-lg-9"><?php echo $row_candidatos['telefono_1']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Teléfono 2:</label><div class="col-lg-9"><?php echo $row_candidatos['telefono_2']; ?></div></div>
      <div class="form-group"><label class="control-label col-lg-3">Medio de contacto:</label><div class="col-lg-9"><?php echo $row_candidatos['medio_vacante']; ?></div></div>
							
                            </fieldset>
                            </form>
                      <p>&nbsp;</p>
                    
                    
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
<?php
mysql_free_result($variables);
?>
