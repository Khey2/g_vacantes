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
if(isset($_GET['anio'])) { $anio = $_GET['anio'];} else {$anio = $row_variables['anio'];}
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

if(isset($_POST['la_semana']) && ($_POST['la_semana']  > 0)) {
$_SESSION['la_semana'] = $_POST['la_semana']; } else { $_SESSION['la_semana'] = $semana - 1;}

if(isset($_POST['el_anio']) && ($_POST['el_anio']  > 0)) {
$_SESSION['el_anio'] = $_POST['el_anio']; } else { $_SESSION['el_anio'] = $row_variables['anio'];}

if(isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz']; } else { $_SESSION['la_matriz'] = $IDmatriz;}


$el_anio = $_SESSION['el_anio'];
$la_semana = $_SESSION['la_semana'];
$la_matriz = $_SESSION['la_matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_INC = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_semana = "SELECT * FROM vac_semana";
$semana = mysql_query($query_semana, $vacantes) or die(mysql_error());
$row_semana = mysql_fetch_assoc($semana);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

if($el_anio == '2020') { 

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_captura_2020.IDempleado, prod_captura_2020.emp_paterno, prod_captura_2020.emp_materno, prod_captura_2020.emp_nombre, prod_captura_2020.denominacion, prod_captura_2020.lun, prod_captura_2020.mar, prod_captura_2020.mie, prod_captura_2020.jue, prod_captura_2020.vie, prod_captura_2020.sab, prod_captura_2020.dom  FROM prod_captura_2020 WHERE prod_captura_2020.IDmatriz = '$la_matriz' AND prod_captura_2020.semana = '$la_semana'  ";   
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

} else {

//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_captura.IDempleado, prod_captura.emp_paterno, prod_captura.emp_materno, prod_captura.emp_nombre, prod_captura.denominacion, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom  FROM prod_captura WHERE prod_captura.IDmatriz = '$la_matriz' AND prod_captura.semana = '$la_semana' AND prod_captura.anio = '$anio'";  
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);

}

mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT * FROM prod_semanas";
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
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
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
									<h6 class="panel-title">Consulta semanal de asistencia</h6>
								</div>

								<div class="panel-body">
 				<form method="POST" action="inc_faltas_detalle.php">
                	<table class="table">
						<tbody>							  
							<tr>
							<td>
                             <select name="la_semana" class="form-control">
                               <option value="" <?php if (!(strcmp("", $la_semana))) {echo "selected=\"selected\"";} ?>>Semana: Actual</option>
                               <?php do {  ?>
                               <option value="<?php echo $row_semana['IDsemana']?>"<?php if (!(strcmp($row_semana['IDsemana'], $la_semana)))
							   {echo "selected=\"selected\"";} ?>><?php echo $row_semana['semana']?></option>
                               <?php
                              } while ($row_semana = mysql_fetch_assoc($semana));
                              $rows = mysql_num_rows($semana);
                              if($rows > 0) {
                                  mysql_data_seek($semana, 0);
                                  $row_semana = mysql_fetch_assoc($semana);
                              } ?></select>
                            </td>
                            <td>
                             <select name="el_anio" class="form-control">
                               <option value="2020"<?php if (!(strcmp($anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
                               <option value="2021"<?php if (!(strcmp($anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
                             </select>
                            </td>
							<td>
                             <select name="la_matriz" class="form-control">
                               <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>Matriz: Activa</option>
                               <?php do {  ?>
                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
							   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                               <?php
                              } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                              $rows = mysql_num_rows($lmatriz);
                              if($rows > 0) {
                                  mysql_data_seek($lmatriz, 0);
                                  $row_lmatriz = mysql_fetch_assoc($lmatriz);
                              } ?></select>
                            </td>
							<td>
                          <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
							<td>

                             </tr>
					    </tbody>
				    </table>
				</form>
                                
                                                            
                                
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-primary"> 
                      <th>No. Emp</th>
                      <th>Nombre</th>
                      <th>Puesto</th>
                      <th>Lunes</th>
                      <th>Martes</th>
                      <th>Miercoles</th>
                      <th>Jueves</th>
                      <th>Viernes</th>
                      <th>Sabado</th>
               		 </tr>
                    </thead>
                    <tbody>
                                        <?php do { 	?>
									      <tr>
									        <td><?php echo $row_detalle['IDempleado'];  ?>&nbsp; </td>
									        <td><?php echo $row_detalle['emp_paterno'] ." ". $row_detalle['emp_materno'] ." ". $row_detalle['emp_nombre'];  ?>&nbsp; </td>
									        <td><?php echo $row_detalle['denominacion'];  ?>&nbsp; </td>
									        <td><?php if ($row_detalle['lun'] == 0) {echo "<span class='label label-warning'>F</span>"; } else {echo "<span class='label label-success'>A</span>";};  ?></td>
									        <td><?php if ($row_detalle['mar'] == 0) {echo "<span class='label label-warning'>F</span>"; } else {echo "<span class='label label-success'>A</span>";};  ?></td>
									        <td><?php if ($row_detalle['mie'] == 0) {echo "<span class='label label-warning'>F</span>"; } else {echo "<span class='label label-success'>A</span>";};  ?></td>
									        <td><?php if ($row_detalle['jue'] == 0) {echo "<span class='label label-warning'>F</span>"; } else {echo "<span class='label label-success'>A</span>";};  ?></td>
									        <td><?php if ($row_detalle['vie'] == 0) {echo "<span class='label label-warning'>F</span>"; } else {echo "<span class='label label-success'>A</span>";};  ?></td>
									        <td><?php if ($row_detalle['sab'] == 0) {echo "<span class='label label-warning'>F</span>"; } else {echo "<span class='label label-success'>A</span>";};  ?></td>
                    					</tr>
									      <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                                    </tbody>
                                   </table> 

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