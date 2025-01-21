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

$currentPage = $_SERVER["PHP_SELF"];
set_time_limit(0);


mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
if(isset($_POST['el_anio'])) { $anio = $_POST['el_anio'];} else {$anio = $row_variables['anio'];}
$desfase = $row_variables['dias_desfase'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id']; }
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];


// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));


mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

if (isset($_POST['el_anio']) && $_POST['el_anio'] > 0) {$el_anio = $_POST['el_anio']; }
else {$el_anio = $anio;} 

if (isset($_POST['la_matriz']) && $_POST['la_matriz'] > 0) {foreach ($_POST['la_matriz'] as $matriz) {$la_matriz = implode(", ", $_POST['la_matriz']); }}
else {$la_matriz = $IDmatriz;} 

if (isset($_POST['mi_semana']) && $_POST['mi_semana'] > 0) {$la_semana = $_POST['mi_semana']; }
else {$la_semana = $semana - 1;} 

if($_POST['tipo'] == 1) {$areas = '1';}
else if($_POST['tipo'] == 2) {$areas = '2';}
else if($_POST['tipo'] == 3) {$areas = '3';}
else {$areas = '1';}

echo "semana: ".$la_semana."|";
echo "matriz: ".$la_matriz."|";
echo "anio: ".$el_anio;
echo "areas: ".$areas;

header("Location: productividad_reportes_new_t.php?IDmatriz=$la_matriz&anio=$el_anio&semana=$la_semana&areas=$areas&tipo=0"); 	
} else {
	
$la_semana = $semana;
$la_matriz = $IDmatriz;
$el_anio = $anio;

}
//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT *  FROM prod_semanas WHERE anio = 2025";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);
$totalRows_semanas = mysql_num_rows($semanas);


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
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<!-- Theme JS files -->

	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect3.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
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


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Consulta semanal de productividad</h6>
								</div>

								<div class="panel-body">
                                <p>Filtre el reporte esperado:<br>
								<strong>El reporte muestra la captura de empleados de la semana seleccionada, no incluye empleados que no se les haya reportado productividad.</strong><br>
								&nbsp;</p>
                                
                       <form method="POST" action="productividad_reporte.php" class="form-horizontal form-validate-jquery">

								<fieldset class="content-group">

									<div class="form-group">
										<label class="control-label col-lg-3">Año:<span class="text-danger">*</span></label>
										<div class="col-lg-9">

                             <select name="el_anio" class="form-control">
                               <option value="2024"<?php if (!(strcmp($anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
                               <option value="2025"<?php if (!(strcmp($anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
                             </select>
                             
										</div>
									</div>


									<div class="form-group">
										<label class="control-label col-lg-3">Semana:<span class="text-danger">*</span></label>
										<div class="col-lg-9">

										<select class="form-control" id="mi_semana" name="mi_semana" >
                                          <?php do {  ?>
                                           <option value="<?php echo $row_semanas['semana']?>"<?php if (!(strcmp($row_semanas['semana'], $la_semana)))
										   {echo "selected=\"selected\"";} ?>>Semana <?php echo $row_semanas['semana']?><?php if ($row_semanas['semana'] == $semana) { echo " (actual)";} ?></option>
											<?php
                                            } while ($row_semanas = mysql_fetch_assoc($semanas));
                                              $rows = mysql_num_rows($semanas);
                                              if($rows > 0) {
                                                  mysql_data_seek($semanas, 0);
                                                  $row_semanas = mysql_fetch_assoc($semanas);
                                              } ?></select>
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">

											<select class="multiselect" multiple="multiple" id="la_matriz[]" name="la_matriz[]">
                                          <?php do {  ?>
                                           <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
										   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
											<?php
                                            } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                                              $rows = mysql_num_rows($lmatriz);
                                              if($rows > 0) {
                                                  mysql_data_seek($semanas, 0);
                                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
                                              } ?></select>
										</div>
									</div>


									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Reporte:<span class="text-danger">*</span></label>
										<div class="col-lg-9">

											<select name="tipo" id="tipo"  class="form-control">
                                           <option value="1">Global</option>
                                           <option value="2">Almacen</option>
                                           <option value="3">Distrbucion</option>
                                           </select>
										</div>
									</div>
                               	<input type="hidden" name="MM_update" value="form1">
                                <button type="submit" class="btn btn-success">Descargar</button>
                    </form>	


								</div>
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