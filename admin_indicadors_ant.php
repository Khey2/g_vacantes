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
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fecha = date("Y-m-d"); // la fecha actual
$mes_actual = date("m"); // la fecha actual
if ($mes_actual = 01) {$mes_actual = 12;} else {$mes_actual = $mes_actual - 1;}
$anio_actual = $row_variables['anio']; // la fecha actual

//echo $fecha_inicio_mes_ok; 

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
$mamatriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

//echo $_SESSION['la_matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $mamatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$activa = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_anio = "SELECT * FROM vac_anios ORDER BY vac_anios.IDanio DESC";
$anio = mysql_query($query_anio, $vacantes) or die(mysql_error());
$row_anio = mysql_fetch_assoc($anio);
$totalRows_anio = mysql_num_rows($anio);

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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Indicadores de Recursos Humanos.</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">A continuación, se muestra el ranking de rotación por antiguedad del año actual.</p>


				<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th class="col-lg-2">SUCURSAL</th>
                      <th class="col-lg-1">0 a 3 meses %</th>
                      <th class="col-lg-1"> #</th>
                      <th class="col-lg-1">4 a 6 meses %</th>
                      <th class="col-lg-1"> #</th>
                      <th class="col-lg-1">7 a 12 meses %</th>
                      <th class="col-lg-1">#</th>
                      <th class="col-lg-1">12 meses o mas %</th>
                      <th class="col-lg-1">#</th>
                      <th class="col-lg-">Grafico</th>
               		 </tr>
                    </thead>
                    <tbody>					
					<?php do {
					$IDmatriz = $row_lmatriz['IDmatriz'];
					$cada_matriz = $row_lmatriz['matriz'];
					$el_mes = $mes_actual;
					$el_anio = $anio_actual;
					
// por antiguedad año acutal
mysql_select_db($database_vacantes, $vacantes);
$query_por_antig0 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.baja_anio = '$el_anio' AND IDantig = 0 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC";
$por_antig0 = mysql_query($query_por_antig0, $vacantes) or die(mysql_error());
$row_por_antig0 = mysql_fetch_assoc($por_antig0);
$totalRows_por_antig0 = mysql_num_rows($por_antig0);
if($totalRows_por_antig0 == 0){$menosdeuno = 0;} else {$menosdeuno = $row_por_antig0['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antig1 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.baja_anio = '$el_anio' AND IDantig = 1 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC";
$por_antig1 = mysql_query($query_por_antig1, $vacantes) or die(mysql_error());
$row_por_antig1 = mysql_fetch_assoc($por_antig1);
$totalRows_por_antig1 = mysql_num_rows($por_antig1);
if($totalRows_por_antig1 == 0){$ceroatres = 0;} else {$ceroatres = $row_por_antig1['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antig2 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.baja_anio = '$el_anio' AND IDantig = 2 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antig2 = mysql_query($query_por_antig2, $vacantes) or die(mysql_error());
$row_por_antig2 = mysql_fetch_assoc($por_antig2);
$totalRows_por_antig2 = mysql_num_rows($por_antig2);
if($totalRows_por_antig2 == 0){$tresaseis = 0;} else {$tresaseis = $row_por_antig2['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antig3 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.baja_anio = '$el_anio' AND IDantig = 3 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antig3 = mysql_query($query_por_antig3, $vacantes) or die(mysql_error());
$row_por_antig3 = mysql_fetch_assoc($por_antig3);
$totalRows_por_antig3 = mysql_num_rows($por_antig3);
if($totalRows_por_antig3 == 0){$seisadoce = 0;} else {$seisadoce = $row_por_antig3['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antig4= "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND ind_bajas.IDmatriz = '$IDmatriz' AND ind_bajas.baja_anio = '$el_anio' AND IDantig = 4 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antig4= mysql_query($query_por_antig4, $vacantes) or die(mysql_error());
$row_por_antig4= mysql_fetch_assoc($por_antig4);
$totalRows_por_antig4= mysql_num_rows($por_antig4);
if($totalRows_por_antig4 == 0){$masdedoce = 0;} else {$masdedoce = $row_por_antig4['antiguedad'];}

$total = $menosdeuno + 	$ceroatres + $tresaseis + $seisadoce + $masdedoce;
$ceroatres_ = $menosdeuno + $ceroatres;
if ($total > 0) {
?>
					<tr>
                    <td><?php echo $row_lmatriz['matriz'];?></td>
                    <td><?php if($ceroatres_ != 0){ echo round(($ceroatres_/$total)*100,0);?>%  <?php } else { echo "0%"; }?></td>
                    <td><?php if($ceroatres_ != 0){ echo $ceroatres_;?> <?php } else { echo "0"; }?></td>
                    <td><?php if($tresaseis != 0) { echo round(($tresaseis/$total)*100,0);?>%   <?php } else { echo "0%"; }?></td>
                    <td><?php if($tresaseis != 0) { echo $tresaseis;?>  <?php } else { echo "0"; }?></td>
                    <td><?php if($seisadoce != 0) { echo round(($seisadoce/$total)*100,0);?>%   <?php } else { echo "0%"; }?></td>
                    <td><?php if($seisadoce != 0) { echo $seisadoce;?>  <?php } else { echo "0"; }?></td>
                    <td><?php if($masdedoce != 0) { echo round(($masdedoce/$total)*100,0);?>%   <?php } else { echo "0%"; }?></td>
                    <td><?php if($masdedoce != 0) { echo $masdedoce;?>  <?php } else { echo "0"; }?></td>
                    <td><div class="progress">
                     <div class="progress-bar progress-bar-danger"  style="width: <?php echo round(($ceroatres_/$total)*100,0);?>%;"></div>
                     <div class="progress-bar progress-bar-warning" style="width: <?php echo round(($tresaseis/$total)*100,0);?>%;"></div>
                     <div class="progress-bar progress-bar-info"    style="width: <?php echo round(($seisadoce/$total)*100,0);?>%;"></div>
                     <div class="progress-bar progress-bar-sucess"  style="width: <?php echo round(($masdedoce/$total)*100,0);?>%;"></div>
                     </div></td>
					<?php }	} while ($row_lmatriz = mysql_fetch_assoc($lmatriz)); ?>
                    </tr>
                    </tbody>
                   </table> 

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
			<!-- /main content -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>