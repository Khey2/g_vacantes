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
$desfase = $row_variables['dias_desfase'];
$mi_fecha =  date('Y/m/d');


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
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
if (isset($_POST['anio'])) {$anio = $_POST['anio'];} else {$anio = $row_variables['anio'];}
if (isset($_POST['la_matriz'])) {$la_matriz = $_POST['la_matriz'];} 
if (isset($_POST['IDestatus'])) {$IDestatus = $_POST['IDestatus'];} else {$IDestatus = 1;}

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];

mysql_select_db($database_vacantes, $vacantes);
$query_casos = "SELECT * FROM incapacidades_accidentes WHERE IDestatus IN ($IDestatus) AND anio = $anio AND IDmatriz = $la_matriz AND IDincapacidad_accidente = 2"; 
mysql_query("SET NAMES 'utf8'");
$casos = mysql_query($query_casos, $vacantes) or die(mysql_error());
$row_casos = mysql_fetch_assoc($casos);
$totalRows_casos = mysql_num_rows($casos);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 12";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
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
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<!-- /theme JS files -->
</head>

	<style>
	.hiddenRow {
    padding: 0 !important;
	}
	</style>

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
                        <?php if(isset($_GET['info']) && $_GET['info'] == 3) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cambiado el estatus de forma correcta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 99) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cerrado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 4) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 11))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 12))) { ?>
					    <div class="alert bg-info-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 13) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						
					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Incapacidades</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group"><strong>Instrucciones:</strong></br>
									<div class="row show-grid">
									<div class="col-md-5"><div>
									<b>Accidentes:</b><br/>
									1. Utiliza el filtro para buscar los incidentes por año, matriz o estatus.</br>
                                    2. Da clic en el botón <span class="text-sucess">Agregar</span> para agregar un incidente.</br>
									3. Da clic en el botón <i class="icon-file-check text-primary"></i> para actualizar o cerrar el incidente de trabajo.</br>
									4. Da clic en el botón <i class="icon-file-plus text-success"></i> para agregar una incapacidad al incidente de trabajo.</br>
									5. Da clic en el botón <i class="icon-file-eye text-danger"></i> para ver las incapacidades existentes.</br>

									</div></div>
									<div class="col-md-7"><div>
									<b>Incapacidades:</b><br/>									
									1. Da clic en el botón <i class="icon-file-pdf text-danger"></i> para descargar el PDF de la incapacidad.</br>
									2. Da clic en el botón <i class="icon-file-check text-info"></i> para actualizar la incapacidad.</br>
									3. El tipo de Incapacidad utiliza las siguientes abreviaciones:</br>
									<em>EG: Enfermedad General</em> | <em>AT: Accidente de Trabajo</em> | <em>MA: Maternidad</em> | <em>HC: Hijos con cáncer</em></br>

									</div></div>
									</div>
									</p>

	<div class="content-group border-top-lg border-top-primary">
	<form method="POST" action="incapacidades_z.php">
		<table class="table">
			<tr>
			<td>Año:
				<select class="form-control" name="anio">
				<option value="2025"<?php if ($anio == 2025) {echo "selected=\"selected\"";} ?>>2025</option>
				<option value="2024"<?php if ($anio == 2024) {echo "selected=\"selected\"";} ?>>2024</option>
					<option value="2023"<?php if ($anio == 2023) {echo "selected=\"selected\"";} ?>>2023</option>
				</select>
			</td>
			<td> Matriz: 
				<select name="la_matriz" class="form-control">
					<?php do {  ?>
					<option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>> <?php echo $row_lmatriz['matriz']?></option>
					<?php } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
					$rows = mysql_num_rows($lmatriz);
					if($rows > 0) { mysql_data_seek($lmatriz, 0); 
					$row_lmatriz = mysql_fetch_assoc($lmatriz);  } ?>
				</select>
			</td>
			<td>Estatus:
				<select class="form-control" name="IDestatus">
					<option value="1"<?php if ($IDestatus == 1) {echo "selected=\"selected\"";} ?>>En proceso</option>
					<option value="2"<?php if ($IDestatus == 2) {echo "selected=\"selected\"";} ?>>Cerrado</option>
				</select>
			</td>
			<td>
			<button type="submit" class="btn btn-primary">Filtrar</button> 
			<a href="incapacidades_edit_z.php" class="btn btn-success">Agregar</a>
			</td>
			</tr>
		</table>
	</form>
	</div>					


<table class="table">
	<thead>
		<tr class="bg-primary">
			<th>Folio</th>
			<th>No. Emp.</th>
			<th>NSS</th>
			<th>Empleado</th>
			<th>Fecha accidente</th>
			<th>Semana</th>
			<th>Estatus</th>
			<th>Acciones</th>
		</tr>
	</thead>
    <tbody>
	<?php if ($totalRows_casos > 0) { ?>

	<?php do { 
	$IDincapacidad = $row_casos['IDincapacidad'];
	$IDempleado = $row_casos['IDempleado']; 

	$query_casos_seguimientos = "SELECT * FROM incapacidades_certificados WHERE IDincapacidad = $IDincapacidad";
	$casos_seguimientos = mysql_query($query_casos_seguimientos, $vacantes) or die(mysql_error());
	$row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos);
	$totalRows_casos_seguimientos = mysql_num_rows($casos_seguimientos);

	$query_activo = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
	$activo = mysql_query($query_activo, $vacantes) or die(mysql_error());
	$row_activo = mysql_fetch_assoc($activo);
	$totalRows_activo = mysql_num_rows($activo);
	?>
        <tr class="alpha-teal border-top-primary">
			<td><?php echo $row_casos['IDincapacidad']; ?></td>
			<td><?php echo $row_casos['IDempleado']; ?><?php if ($totalRows_activo = 0) { ?>(baja)<?php } ?></td>
			<td><?php echo $row_casos['nss']; ?></td>
			<td><?php echo $row_casos['emp_paterno']." ".$row_casos['emp_materno']." ".$row_casos['emp_nombre']; ?></td>
			<td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_inicio']))?></td>
			<td><?php echo $row_casos['semana'] ?></td>
			<td>
				<?php if ($row_casos['IDestatus'] == 1) { echo "En proceso"; } 
					else if ($row_casos['IDestatus'] == 2) { echo "Cerrado"; } 
					else { echo "Sin Estatus"; } 
				?>
			</td>
			<td>
			<a href="incapacidades_edit_2_z.php?IDincapacidad=<?php echo $row_casos['IDincapacidad']; ?>"class="btn btn-primary btn-xs"><i class="icon-file-check"></i></a>
				<a href="incapacidades_certificados_z.php?IDincapacidad=<?php echo $row_casos['IDincapacidad']; ?>" class="btn btn-success btn-xs"><i class="icon-file-plus"></i></a>
				<?php if ($totalRows_casos_seguimientos > 0) { ?>
					<button class="btn btn-warning btn-xs"  data-toggle="collapse" data-target="#demo<?php echo $IDincapacidad; ?>"><i class="icon-file-eye"></i></button>
				<?php } ?>
			</td>
        </tr>
        <tr>
        <td colspan="8" class="hiddenRow">
			<div class="accordian-body collapse" id="demo<?php echo $IDincapacidad; ?>"> 
              	<table class="table">
                    <thead>
						<tr>
							<th>Folio</th>		
							<th>IoS</th>
							<th>Tipo</th>
							<th>Fecha Inicio</th>	
							<th>Fecha Fin</th>	
							<th>Dias</th>	
							<th>Acciones</th>	
						</tr>
					</thead>	
					<tbody>
					<?php if ($totalRows_casos_seguimientos > 0) { ?>
						<?php do { ?>
                        <tr>
							<td><?php if ($row_casos_seguimientos['IDestatus'] == 2) { ?><i class="text text-success icon-checkmark5"></i><?php } else {?><i class="text text-danger icon-cross"></i><?php } ?> <?php echo $row_casos_seguimientos['folio_certificado']; ?></td>
							<td><?php if ($row_casos_seguimientos['IDtipo_certificado'] == 1) {echo "Inicial";} else { echo "Subsecuente";}  ?></td>
							<td><?php if ($row_casos_seguimientos['IDtipo_incapacidad'] == 1) {echo "EG";} 
								 else if ($row_casos_seguimientos['IDtipo_incapacidad'] == 2) {echo "AT";} 
								 else if ($row_casos_seguimientos['IDtipo_incapacidad'] == 3) {echo "MA";} 
								 else if ($row_casos_seguimientos['IDtipo_incapacidad'] == 4) {echo "HC";}  ?></td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_casos_seguimientos['fecha_inicio'])) ?></td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_casos_seguimientos['fecha_fin'])) ?></td>
							<td><?php echo $row_casos_seguimientos['dias']; ?></td>
							<td>
							<?php if ($row_casos_seguimientos['file_certificado'] != '') { ?>
								<a href="incp/<?php echo $row_casos_seguimientos['IDempleado']; ?>/<?php echo $row_casos_seguimientos['file_certificado']; ?>" class="btn btn-danger btn-xs" target="_blank"><i class ="icon-file-pdf" ></i></a>
							<?php } ?>
								<a href="incapacidades_certificados_z.php?IDincapacidad=<?php echo $IDincapacidad; ?>&IDcertificado=<?php echo $row_casos_seguimientos['IDcertificado']; ?>" class="btn btn-info btn-xs"><i class="icon-file-check"></i></a>
							</td>
						</tr>
						<?php } while ($row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos)); } ?>
                    </tbody>
               	</table>
            </div> 
        </td>
        </tr>
		<?php  }  while ($row_casos = mysql_fetch_assoc($casos)); ?>
			<?php } else { ?>
			<tr><td colspan="8">Sin incapacidades con el filtro seleccionado.</td></tr>
			<?php } ?>
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