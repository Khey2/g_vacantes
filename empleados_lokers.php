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
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if(isset($_POST['el_area']) && ($_POST['el_area']  > 0)) { $_SESSION['el_area'] = $_POST['el_area']; } 
if (!isset($_SESSION['el_area'])) { $_SESSION['el_area'] = "1,2";}
$el_area = $_SESSION['el_area'];

mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc13, prod_activos.fecha_alta, prod_activos.descripcion_nomina, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDarea, vac_areas.area, prod_activos_fotos.file, prod_activos_fotos.campo1_dato, prod_activos_fotos.campo2_dato, prod_activos_fotos.campo3_dato, prod_activos_fotos.campo4_dato FROM prod_activos LEFT JOIN prod_activos_fotos ON prod_activos.IDempleado = prod_activos_fotos.IDempleado LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea WHERE prod_activos.IDmatriz = '$IDmatriz' AND prod_activos.IDarea in ($el_area) ORDER BY prod_activos.IDpuesto ASC";
mysql_query("SET NAMES 'utf8'");
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);

$fondo = 0;
	if($el_area == 1 OR $el_area == 2) {$fondo = 'ALM';}
elseif($el_area == 3 OR $el_area == 4) {$fondo = 'DIS';}
elseif($el_area == 5 OR $el_area == 6) {$fondo = 'VEN';}
else {$fondo = 'ADM';}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	
// agregar FOTO
$Elempleado = $_POST['IDempleado'];
$formatos_permitidos =  array('jpeg', 'png', 'jpg');
$IDempleado_carpeta = 'files/'.$Elempleado;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$Elempleado.$fecha.'.'.$extension;
$targetPath = 'files/'.$Elempleado.'/'.$name_new;

if ($name != '') {	
if(!in_array($extension, $formatos_permitidos) ) { 
header("Location: empleados_credenciales.php?info=9"); 
}
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}
if ($name != '') {$name_new = $name_new;} else {$name_new = $foto_anterior; }

if ($row_autorizados['file'] != ''){ 	

$updateSQL = "UPDATE prod_activos_fotos SET file = '$name_new' WHERE IDempleado = '$Elempleado'";
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: empleados_credenciales.php?info=1");

} else {

$updateSQL = "INSERT INTO prod_activos_fotos (IDempleado, file) VALUES ('$Elempleado', '$name_new')";
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: empleados_credenciales.php?info=1");
	
}
}

mysql_select_db($database_vacantes, $vacantes);
$query_areal = "SELECT * FROM vac_areas WHERE IDarea = '$el_area'";
$areal = mysql_query($query_areal, $vacantes) or die(mysql_error());
$row_areal = mysql_fetch_assoc($areal);
$totalRows_areal = mysql_num_rows($areal);
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
	<script src="global_assets/js/plugins/tables/datatables/extensions/col_reorder.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_reorder.js"></script>	
	<!-- /theme JS files -->
	
 <script>
    function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i] != source)
    checkboxes[i].checked = source.checked;
    }
    }
    </script>
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
									<h6 class="panel-title">Impresión de Credenciales</h6>
								</div>

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							No se encontraron empleados seleccionados.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se guardó correctamente la Foto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo es incorrecto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

								<div class="panel-body">
								<p><strong>Instrucciones:</strong></a>
								<ul>
								<li>Selecciona el área y los empleados para generar su credencial.</li>
								<li>Agrega la foto de la credencial dando clic en el botón <i class="icon-user"></i>.</li>
								<li>Los usuarios con Foto se muestran con el botón <i class="icon-user-check text-success"></i>.</li>
								<li>Da clic en <i class="icon-printer2"></i> para imprimir una credencial individual.</li>
								<li>Da clic en <strong>"Imprimir Seleccionadas"</strong> para imprimir credenciales en grupos de 4.</li>
								<li>Los empleados sin fotografía se generan con una imagen genérica.</li>
								</ul>
                     <p><strong>Área actual:</strong> <?php echo $row_areal['area']; ?></p>
                     <p>&nbsp;</p>


							<form method="POST" action="empleados_credenciales.php">
								<table class="table">
								<tbody>							  
									<tr>
									<td><div class="col-md-12">
											<select name="el_area"  class="form-control">
											<?php do { ?>
											   <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_area['area']?></option>
											   <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) {
												  mysql_data_seek($area, 0);
												  $row_area = mysql_fetch_assoc($area);
											  } ?> 
											</select>
									</div></td>
									<td><div class="col-md-12"><button type="submit" class="btn btn-primary">Filtrar</button></div></td>	
								  </tr>
								</tbody>
							</table>
							</form>


				<form method="post" name="form1" action="empleados_print1000.php">
				<input type="hidden" value="<?php echo $fondo; ?>" name="IDfondo" id="IDfondo">

					<div class="text text-right">
					<button type="submit" class="btn btn-success"><i class="icon-printer2"></i> Imprimir Seleccionadas</button>
					</div>

					<div class="form-group pt-2">

							<div class="table-responsive">
							<table class="table datatable-reorder-state-saving">
                    			<thead>
                                	<tr class="bg-success"> 
									<th><input type="checkbox" id="selectall" name="selectall" class="form-check-input-styled" onclick="toggle(this)" autocomplete="off"></th>
                                    <th>Acciones</th>
                                    <th>No.Emp.</th>
                                    <th>Nombre</th>
                                    <th>Fecha Alta</th>
                                    <th>RFC</th>
                                    <th>Puesto</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_autorizados > 0) { do { ?>
									<tr>
									<td><input type="checkbox" name="IDempleado[]" value="<?php echo $row_autorizados['IDempleado']; ?>" data-fouc></td>
									<td>
									  <?php if ($row_autorizados['file'] == '') {  ?> 
									  <a href="empleados_credenciales_cargar.php?IDempleado=<?php echo $row_autorizados['IDempleado']; ?>" class='btn btn-primary btn-sm'><i class="icon-user"></i></a>
									  <?php } else {  ?> 
									  <a href="empleados_credenciales_cargar.php?IDempleado=<?php echo $row_autorizados['IDempleado']; ?>" class='btn btn-success btn-sm'><i class="icon-user-check"></i></a>
									  <?php }  ?> 
									  <?php if ($row_usuario['user_credenciales'] != 2 ) {  ?> 
									  <a href="empleados_print1001.php?IDempleado=<?php echo $row_autorizados['IDempleado']; ?>&IDfondo=<?php echo $fondo; ?>" class='btn btn-info btn-sm'><i class="icon-printer2"></i></a></td>
									  <?php } else {  ?> 
									 <div class="btn-group">
									<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" data-boundary="window">
									<i class="icon-printer2"></i><span class="caret"></span></button>
										<ul class="dropdown-menu">
											<li><a href="empleados_print1001_t.php?IDempleado=<?php echo $row_autorizados['IDempleado']; ?>&IDfondo=<?php echo $fondo; ?>"><span class="text text-success">Impresora Credenciales</span></a></li>
											<li><a href="empleados_print1001.php?IDempleado=<?php echo $row_autorizados['IDempleado']; ?>&IDfondo=<?php echo $fondo; ?>"><span class="text text-success">Normal</span></a></li>
										</ul>
									</div>
									  <?php }  ?> 
                                      <td><?php echo $row_autorizados['IDempleado']; ?></td>
                                      <td><?php echo $row_autorizados['emp_paterno'] . " " . $row_autorizados['emp_materno'] . " " . $row_autorizados['emp_nombre']; ?></td>
                                      <td><?php echo $row_autorizados['fecha_alta']; ?></td>
                                      <td><?php echo $row_autorizados['rfc13']; ?></td>
                                      <td><?php echo $row_autorizados['denominacion']; ?></td>
									  </td>
                                    </tr>
                                    <?php } while ($row_autorizados = mysql_fetch_assoc($autorizados)); } else { ?>
									 <tr><td colspan="7">No se encontraron activos en el área seleccionada.</td></tr>
                                    <?php } ?>
                                  </tbody>
                                </table>
							</div>
				</form> 
												
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
</body>
</html>