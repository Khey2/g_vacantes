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
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrize = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrize = mysql_query($query_matrize, $vacantes) or die(mysql_error());
$row_matrize = mysql_fetch_assoc($matrize);
$totalRows_matrize = mysql_num_rows($matrize);
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
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
                    
			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>Matriz</th>
                          <th>Activos</th>
                          <th>Evaluados G1</th>
                          <th>Evaluados G2</th>
                          <th>Evaluados G3</th>
                          <th>Manual</th>
                          <th>Muestra</th>
                          <th>Calificacion</th>
						 </tr>
					    </thead>
						<tbody>							  
						<?php do { ?>
                        <tr>
                          <td><?php echo $row_matrize['matriz']; ?></td>
						<?php 
						$la_matriz = $row_matrize['IDmatriz'];
						$IDexmen = $row_matrize['nom35_g2'] + 1;
						$query_activos = "SELECT * FROM prod_activos WHERE IDmatriz = $la_matriz";
						$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
						$row_activos = mysql_fetch_assoc($activos);
						$totalRows_activos = mysql_num_rows($activos);

						$query_evaluadosg1 = "SELECT DISTINCT IDresultado FROM nom35_resultados WHERE IDmatriz = $la_matriz AND IDexamen = 1";
						$evaluadosg1 = mysql_query($query_evaluadosg1, $vacantes) or die(mysql_error());
						$row_evaluadosg1 = mysql_fetch_assoc($evaluadosg1);
						$totalRows_evaluadosg1 = mysql_num_rows($evaluadosg1);

						$query_evaluadosg2 = "SELECT DISTINCT IDresultado FROM nom35_resultados WHERE IDmatriz = $la_matriz AND IDexamen = 2";
						$evaluadosg2 = mysql_query($query_evaluadosg2, $vacantes) or die(mysql_error());
						$row_evaluadosg2 = mysql_fetch_assoc($evaluadosg2);
						$totalRows_evaluadosg2 = mysql_num_rows($evaluadosg2);

						$query_evaluadosg3 = "SELECT DISTINCT IDresultado FROM nom35_resultados WHERE IDmatriz = $la_matriz AND IDexamen = 3";
						$evaluadosg3 = mysql_query($query_evaluadosg3, $vacantes) or die(mysql_error());
						$row_evaluadosg3 = mysql_fetch_assoc($evaluadosg3);
						$totalRows_evaluadosg3 = mysql_num_rows($evaluadosg3);

						$query_evaluadosgm = "SELECT DISTINCT IDresultado FROM nom35_resultados WHERE IDmatriz = $la_matriz AND manual = 1";
						$evaluadosgm = mysql_query($query_evaluadosgm, $vacantes) or die(mysql_error());
						$row_evaluadosgm = mysql_fetch_assoc($evaluadosgm);
						$totalRows_evaluadosgm = mysql_num_rows($evaluadosgm);

						//RESULTADOS TOTALES
						mysql_select_db($database_vacantes, $vacantes);
						$query_total_c = "SELECT DISTINCT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND prod_activos.IDmatriz = $la_matriz  AND nom35_respuestas.pregunta_tipo <> 3 GROUP BY nom35_respuestas.IDempleado"; 
						$total_c = mysql_query($query_total_c, $vacantes) or die(mysql_error());
						$row_total_c = mysql_fetch_assoc($total_c);
						$totalRows_total_c = mysql_num_rows($total_c);

						// el examen es 3, se repite todo
						$ct_MA = 140;
						$ct_A  = 99;
						$ct_M  = 75;
						$ct_B  = 50;
						$ct_N  = 0;

						$ct_MA_r = 0;
						$ct_A_r  = 0;
						$ct_M_r  = 0;
						$ct_B_r  = 0;
						$ct_N_r  = 0;
						$promedio = 0;

						do { 
						if($row_total_c['Respuesta'] >= $ct_MA)                                      	   {$ct_MA_r++;}
						else if($row_total_c['Respuesta'] >= $ct_A AND $row_total_c['Respuesta'] < $ct_MA) {$ct_A_r++;}
						else if($row_total_c['Respuesta'] >= $ct_M AND $row_total_c['Respuesta'] < $ct_A)  {$ct_M_r++;}
						else if($row_total_c['Respuesta'] >= $ct_B AND $row_total_c['Respuesta'] < $ct_M)  {$ct_B_r++;}
						else if($row_total_c['Respuesta'] >= $ct_N AND $row_total_c['Respuesta'] < $ct_B)  {$ct_N_r++;}
						$promedio = $promedio + $row_total_c['Respuesta'];
						} while ($row_total_c = mysql_fetch_assoc($total_c));

						if(($ct_MA_r >= $ct_A_r)  AND ($ct_MA_r >= $ct_M_r) AND ($ct_MA_r >= $ct_B_r) AND ($ct_MA_r >= $ct_N_r)){ $val_max = "Muy Alto"; $IDnivel = 5; }
						if(($ct_A_r  >= $ct_MA_r) AND ($ct_A_r  >= $ct_M_r) AND ($ct_A_r  >= $ct_B_r) AND ($ct_A_r  >= $ct_N_r)){ $val_max = "Alto";     $IDnivel = 4; }
						if(($ct_M_r  >= $ct_MA_r) AND ($ct_M_r  >= $ct_A_r) AND ($ct_M_r  >= $ct_B_r) AND ($ct_M_r  >= $ct_N_r)){ $val_max = "Medio";    $IDnivel = 3; }
						if(($ct_B_r  >= $ct_MA_r) AND ($ct_B_r  >= $ct_A_r) AND ($ct_B_r  >= $ct_M_r) AND ($ct_B_r  >= $ct_N_r)){ $val_max = "Bajo";     $IDnivel = 2; }
						if(($ct_N_r  >= $ct_MA_r) AND ($ct_N_r  >= $ct_A_r) AND ($ct_N_r  >= $ct_M_r) AND ($ct_N_r  >= $ct_B_r)){ $val_max = "Nulo";     $IDnivel = 1; }

						If ($IDexmen == 2) {$muestra = "Todos"; } else { $muestra = round((0.9604 * $totalRows_activos) / ( 0.0025 * ($totalRows_activos - 1) + 0.9604), 0 , PHP_ROUND_HALF_UP); }
						?>
						<td><?php echo $totalRows_activos; ?></td>
						<td><?php echo $totalRows_evaluadosg1; ?></td>
						<td><?php echo $totalRows_evaluadosg2; ?></td>
						<td><?php echo $totalRows_evaluadosg3; ?></td>
						<td><?php echo $totalRows_evaluadosgm; ?></td>
						<td><?php if ($muestra >= $totalRows_evaluadosg3 AND $IDexmen == 3) {echo '<span class="text text-danger">'.$muestra."</span>";} 
							 else if ($muestra >= $totalRows_activos AND $IDexmen == 2) {echo '<span class="text text-danger">'.$muestra."</span>";} 
							 else {echo '<span class="text text-success">'.$muestra."</span>";} ?></td>
						<td><?php echo $val_max; ?></td>
                        <?php } while ($row_matrize = mysql_fetch_assoc($matrize)); ?>

                   	</tbody>							  
                 </table>

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
