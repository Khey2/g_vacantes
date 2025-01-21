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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));


// definir semana
if (isset($_POST['mi_semana']) && $_POST['mi_semana'] > 0) {$_SESSION['mi_semana'] = $_POST['mi_semana']; } 

if (!isset($_SESSION['mi_semana'])) {$_SESSION['mi_semana'] = $semana;} 


$la_semana = $_SESSION['mi_semana'];


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


//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

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
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

if(isset($_POST['el_anio']) && $_POST['el_anio'] == '2020') { 


//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT Count( prod_activos.IDempleado ) AS Ocupados, Count( prod_captura_2020.capturador ) AS TCapturados, 	Count( prod_captura_2020.validador ) AS TValidados, Count( prod_captura_2020.autorizador ) AS TAutorizados, Sum( prod_captura_2020.pago_total ) AS TTotal, Sum( prod_captura_2020.adicional2 ) AS TAdicional, vac_matriz.matriz, 	vac_matriz.IDmatriz, 	vac_puestos.IDpuesto FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN prod_captura_2020 ON prod_captura_2020.IDempleado = prod_activos.IDempleado AND prod_captura_2020.semana = '$la_semana' WHERE vac_puestos.IDaplica_PROD = 1 AND vac_matriz.IDmatriz IN ($IDmatrizes) AND  prod_activos.IDarea IN ($mis_areas) GROUP BY prod_activos.IDmatriz";  
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);


mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT DISTINCT vac_semana.IDsemana, prod_captura_2020.semana FROM vac_semana RIGHT JOIN prod_captura_2020 ON prod_captura_2020.semana = vac_semana.semana";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);
$totalRows_semanas = mysql_num_rows($semanas);

} else {
	
//Detalle
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT Count( prod_activos.IDempleado ) AS Ocupados, Count( prod_captura.capturador ) AS TCapturados, 	Count( prod_captura.validador ) AS TValidados, Count( prod_captura.autorizador ) AS TAutorizados, Sum( prod_captura.pago_total ) AS TTotal, Sum( prod_captura.adicional2 ) AS TAdicional, vac_matriz.matriz, 	vac_matriz.IDmatriz, 	vac_puestos.IDpuesto FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$la_semana'  AND prod_captura.anio = '$anio'  WHERE vac_puestos.IDaplica_PROD = 1 AND vac_matriz.IDmatriz IN ($IDmatrizes) AND  prod_activos.IDarea IN ($mis_areas) GROUP BY prod_activos.IDmatriz";  
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);


mysql_select_db($database_vacantes, $vacantes);
$query_semanas = "SELECT * FROM prod_semanas";
$semanas = mysql_query($query_semanas, $vacantes) or die(mysql_error());
$row_semanas = mysql_fetch_assoc($semanas);
$totalRows_semanas = mysql_num_rows($semanas);

	
}

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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Consulta semanal de productividad</h6>
								</div>

								<div class="panel-body">
                                <p>Seleccione la Sucursal para ver el detalle por área.<br>
                                Filtre la sucursal que requiera consultar.<br>
                                Semana Actual: <strong><?php echo $la_semana; ?></strong></p>
                                
                                
                       <form method="POST" action="productividad_reportevd.php">

					<table class="table">
						<tbody>							  
							<tr>
                            <td>
                             <select name="el_anio" class="form-control">
                               <option value="2020"<?php if (!(strcmp($anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
                               <option value="2021"<?php if (!(strcmp($anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
                               <option value="2022"<?php if (!(strcmp($anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
                               <option value="2023"<?php if (!(strcmp($anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
                               <option value="2024"<?php if (!(strcmp($anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
                               <option value="2025"<?php if (!(strcmp($anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
                             </select>
                            </td>
							<td> <div class="col-lg-9 no-prints">
										<select name="mi_semana" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_semana))) {echo "selected=\"selected\"";} ?>>Semana: Actual</option>
                                          <?php do {  ?>
                                           <option value="<?php echo $row_semanas['IDsemana']?>"<?php if (!(strcmp($row_semanas['IDsemana'], $la_semana)))
										   {echo "selected=\"selected\"";} ?>><?php echo $row_semanas['semana']?></option>
											<?php
                                            } while ($row_semanas = mysql_fetch_assoc($semanas));
                                              $rows = mysql_num_rows($semanas);
                                              if($rows > 0) {
                                                  mysql_data_seek($semanas, 0);
                                                  $row_semanas = mysql_fetch_assoc($semanas);
                                              } ?></select>
										</div>
                              </td>
									<td>
                                <button type="submit" class="btn btn-success">Filtrar <i class="icon-filter3  position-right"></i></button>	
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	

					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>Acciones</th>
                      <th>Sucursal</th>
                      <th>Activos</th>
                      <th>Capturados</th>
                      <th>Validados</th>
                      <th>Autorizados</th>
                      <th>Monto Total</th>
               		 </tr>
                    </thead>
                    <tbody>
									    <?php do { 	?>
									      <tr>
                                            <td>
                                            <button type="button" class="btn btn-info btn-xs" 
                                            onClick="window.location.href='productividad_reporte_s.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>'"><i class="icon-arrow-right6"></i> Ver Detalle</button> 
											
                                            <a href="productividad_reportes_d.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>" 
                                            class="btn btn-info btn-xs">
											<i class="icon-file-excel position-left"></i>
											Reporte Activos
                                            </a>
                                            
                                            <a href="productividad_reportes_v.php?IDmatriz=<?php echo $row_detalle['IDmatriz']; ?>&semana=<?php echo $la_semana; ?>&anio=<?php echo $anio;?>" 
                                            class="btn btn-info btn-xs">
											<i class="icon-file-excel position-left"></i>
											Reporte > 30%
                                            </a>
                                            
                                            </td>
									        <td><?php echo $row_detalle['matriz'];  ?>&nbsp; </td>
									        <td><?php echo $row_detalle['Ocupados']; ?>&nbsp; </td>
									        <td><?php echo $row_detalle['TCapturados']; ?></td>
									        <td><?php echo $row_detalle['TValidados']; ?></td>
									        <td><?php echo $row_detalle['TAutorizados']; ?></td>
									        <td><?php echo "$" . number_format($row_detalle['TTotal'] + $row_detalle['TAdicional']); ?></td>
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