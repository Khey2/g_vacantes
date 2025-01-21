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
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


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
$IDusuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
mysql_query("SET NAMES 'utf8'");
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT sed_uniformes_inventario.*,  sed_uniformes_tipos.tipo FROM sed_uniformes_inventario LEFT JOIN sed_uniformes_tipos ON sed_uniformes_inventario.IDtipo = sed_uniformes_tipos.IDtipo WHERE IDmatriz = $IDmatriz";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$updateSQL = sprintf("UPDATE sed_uniformes_inventario SET 28a=%s, 30a=%s, 32a=%s, 34a=%s, 36a=%s, 38a=%s, 40a=%s, 42a=%s, 44a=%s, 46a=%s, CH=%s, M=%s, G=%s, XG=%s, XXG=%s, XXXG=%s, XXXXG=%s, 21b=%s, 22b=%s, 23b=%s, 24b=%s, 25b=%s, 26b=%s, 27b=%s, 28b=%s, 29b=%s, 30b=%s, IDusuario=%s WHERE IDinventario=%s",
        GetSQLValueString($_POST['28a'], "text"),
        GetSQLValueString($_POST['30a'], "text"),
        GetSQLValueString($_POST['32a'], "text"),
        GetSQLValueString($_POST['34a'], "text"),
        GetSQLValueString($_POST['36a'], "text"),
        GetSQLValueString($_POST['38a'], "text"),
        GetSQLValueString($_POST['40a'], "text"),
        GetSQLValueString($_POST['42a'], "text"),
        GetSQLValueString($_POST['44a'], "text"),
        GetSQLValueString($_POST['46a'], "text"),
		GetSQLValueString($_POST['CH'], "text"),
		GetSQLValueString($_POST['M'], "text"),
		GetSQLValueString($_POST['G'], "text"),
		GetSQLValueString($_POST['XG'], "text"),
		GetSQLValueString($_POST['XXG'], "text"),
		GetSQLValueString($_POST['XXXG'], "text"),
		GetSQLValueString($_POST['XXXXG'], "text"),
        GetSQLValueString($_POST['21b'], "text"),
        GetSQLValueString($_POST['22b'], "text"),
        GetSQLValueString($_POST['23b'], "text"),
        GetSQLValueString($_POST['24b'], "text"),
        GetSQLValueString($_POST['25b'], "text"),
        GetSQLValueString($_POST['26b'], "text"),
        GetSQLValueString($_POST['27b'], "text"),
        GetSQLValueString($_POST['28b'], "text"),
        GetSQLValueString($_POST['29b'], "text"),
        GetSQLValueString($_POST['30b'], "text"),
        GetSQLValueString($_POST['IDusuario'], "text"),
        GetSQLValueString($_POST['IDinventario'], "int"));

	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	header("Location: uniformes_inventario.php?info=2");
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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_inputs.js"></script>
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
				

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Uniforme.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el Uniforme.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Uniformes</h6>
								</div>

								<div class="panel-body">
								<p>Captura la información solicitada.</p>


								<table class="table table-condensed table-bordered datatable-button-html5-columns">
                                <thead>
                                	<tr class="bg-warning"> 
                                    <th>Sexo</th>
                                    <th>Tipo</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                  </tr>
                                  </thead>

                                <tbody>
							<?php do { 
							  
                                $IDsexo = $row_tipos['IDsexo']; 
                                $IDtipo = $row_tipos['IDtipo']; 
                                $query_inventario = "SELECT *  FROM sed_uniformes_inventario WHERE IDmatriz = $IDmatriz AND IDtipo = $IDtipo AND IDsexo = $IDsexo";
								$inventario = mysql_query($query_inventario, $vacantes) or die(mysql_error());
								$row_inventario = mysql_fetch_assoc($inventario);
								$totalRows_inventario = mysql_num_rows($inventario);
								?>
                                
                                <?php if ($row_tipos['IDtipo'] == 1 OR $row_tipos['IDtipo'] == 2 OR $row_tipos['IDtipo'] == 5) { ?>
                                    <tr>
                                    <td><b><?php if ($row_tipos['IDsexo'] == 1) {echo " Hombre";} else {echo " Mujer";}; ?></td>
                                    <td><?php echo $row_tipos['tipo']; ?></td>
                                    <td>T28: <span class="text text-danger text-semibold"><?php echo $row_inventario['28a']; ?></span></td>
                                    <td>T30: <span class="text text-danger text-semibold"><?php echo $row_inventario['30a']; ?></span></td>
                                    <td>T32: <span class="text text-danger text-semibold"><?php echo $row_inventario['32a']; ?></span></td>
                                    <td>T34: <span class="text text-danger text-semibold"><?php echo $row_inventario['34a']; ?></span></td>
                                    <td>T36: <span class="text text-danger text-semibold"><?php echo $row_inventario['36a']; ?></span></td>
                                    <td>T38: <span class="text text-danger text-semibold"><?php echo $row_inventario['38a']; ?></span></td>
                                    <td>T40: <span class="text text-danger text-semibold"><?php echo $row_inventario['40a']; ?></span></td>
                                    <td>T42: <span class="text text-danger text-semibold"><?php echo $row_inventario['42a']; ?></span></td>
                                    <td>T44: <span class="text text-danger text-semibold"><?php echo $row_inventario['44a']; ?></span></td>
                                    <td>T46: <span class="text text-danger text-semibold"><?php echo $row_inventario['46a']; ?></span></td>
                                    <td><div onClick="loadDynamicContentModal2('<?php echo $row_inventario['IDinventario']; ?>')" class="btn btn-xs btn-warning btn-icon">Actualizar</div></td>
                                    </tr>
                                <?php } else if ($row_tipos['IDtipo'] == 6) { ?>
                                    <tr>
                                    <td><b><?php if ($row_tipos['IDsexo'] == 1) {echo " Hombre";} else {echo " Mujer";}; ?></b></td>
                                    <td><?php echo $row_tipos['tipo']; ?></td>
                                    <td>T21: <span class="text text-danger text-semibold"><?php echo $row_inventario['21b']; ?></span></td>
                                    <td>T22: <span class="text text-danger text-semibold"><?php echo $row_inventario['22b']; ?></span></td>
                                    <td>T23: <span class="text text-danger text-semibold"><?php echo $row_inventario['23b']; ?></span></td>
                                    <td>T24: <span class="text text-danger text-semibold"><?php echo $row_inventario['24b']; ?></span></td>
                                    <td>T25: <span class="text text-danger text-semibold"><?php echo $row_inventario['25b']; ?></span></td>
                                    <td>T26: <span class="text text-danger text-semibold"><?php echo $row_inventario['26b']; ?></span></td>
                                    <td>T27: <span class="text text-danger text-semibold"><?php echo $row_inventario['27b']; ?></span></td>
                                    <td>T28: <span class="text text-danger text-semibold"><?php echo $row_inventario['28b']; ?></span></td>
                                    <td>T29: <span class="text text-danger text-semibold"><?php echo $row_inventario['29b']; ?></span></td>
                                    <td>T30:<span class="text text-danger text-semibold"> <?php echo $row_inventario['30b']; ?></span></td>
                                    <td><div onClick="loadDynamicContentModal2('<?php echo $row_inventario['IDinventario']; ?>')" class="btn btn-xs btn-warning btn-icon">Actualizar</div></td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                    <td><b><?php if ($row_tipos['IDsexo'] == 1) {echo " Hombre";} else {echo " Mujer";}; ?></b></td>
                                    <td><?php echo $row_tipos['tipo']; ?></td>
                                    <td>CH:    <span class="text text-danger text-semibold"><?php echo $row_inventario['CH']; ?></span></td>
                                    <td>M:     <span class="text text-danger text-semibold"><?php echo $row_inventario['M']; ?></span></td>
                                    <td>G:     <span class="text text-danger text-semibold"><?php echo $row_inventario['G']; ?></span></td>
                                    <td>XG:    <span class="text text-danger text-semibold"><?php echo $row_inventario['XG']; ?></span></td>
                                    <td>XXG:   <span class="text text-danger text-semibold"><?php echo $row_inventario['XXG']; ?></span></td>
                                    <td>XXXG:  <span class="text text-danger text-semibold"><?php echo $row_inventario['XXXG']; ?></span></td>
                                    <td>XXXXG: <span class="text text-danger text-semibold"><?php echo $row_inventario['XXXXG']; ?></span></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><div onClick="loadDynamicContentModal2('<?php echo $row_inventario['IDinventario']; ?>')" class="btn btn-xs btn-warning btn-icon">Actualizar</div></td>
                                    </tr>
                                <?php } ?>
                            <?php } while ($row_tipos = mysql_fetch_assoc($tipos)); ?>
                                  </tbody>
                                </table>


					<!-- Inline form modal -->
					<div id="bootstrap-modal2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h5 class="modal-title">Formulario Uniformes</h5>
								</div>
								   <div id="conte-modal2">
								   </div>
							</div>
						</div>
					<!-- /inline form modal -->
					</div>


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
<script>
function loadDynamicContentModal2(modal){
	var options = { modal: true };
	$('#conte-modal2').load('uniforme_inventario.php?IDinventario='+ modal, function() {
		$('#bootstrap-modal2').modal({show:true});
  });  
}
</script> 
</body>
</html>

