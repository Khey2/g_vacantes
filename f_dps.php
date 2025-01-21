<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);  $IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$user = $_SESSION['kt_login_user'];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto_1 = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE vac_puestos.IDpuesto = '$IDpuesto' GROUP BY vac_puestos.IDpuesto";
mysql_query("SET NAMES 'utf8'");
$puesto_1 = mysql_query($query_puesto_1, $vacantes) or die(mysql_error());
$row_puesto_1 = mysql_fetch_assoc($puesto_1);
$totalRows_puesto_1 = mysql_num_rows($puesto_1);
$llave = $row_puesto_1['IDllave'];

mysql_select_db($database_vacantes, $vacantes);
$query_puesto_2 = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, vac_puestos.tipo, prod_activos.denominacion,	prod_activos.fecha_alta, vac_areas.area, vac_matriz.matriz, prod_llave.IDllaveJ, prod_llave.IDaplica_SED, prod_llave.IDllave, vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.descrito FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN prod_llave ON prod_llave.IDllave = prod_activos.IDllave INNER JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDempleadoJ = '$el_usuario' AND prod_activos.IDaplica_SED = 1";
$puesto_2 = mysql_query($query_puesto_2, $vacantes) or die(mysql_error());
$row_puesto_2 = mysql_fetch_assoc($puesto_2);
$totalRows_puesto_2 = mysql_num_rows($puesto_2);

// cierre de captura
if ((isset($_GET['descrito'])) && ($_GET['descrito'] != "")) {
  
  $estatus = $_GET['descrito'];
  $updateSQL = "UPDATE vac_puestos SET descrito = '$estatus' WHERE IDpuesto ='$IDpuesto'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: f_dps.php?info=4");
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
	<script src="global_assets/js/core/libraries/jquery_ui/core.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/effects.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/extensions/cookie.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_all.min.js"></script>
	<script src="global_assets/js/plugins/trees/fancytree_childcounter.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/extra_trees.js"></script>
	<!-- /theme JS files -->
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>


	<!-- Page container -->
	<div class="page-container">


		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

			<!-- Content area -->
			  <div class="content">
              

                      	<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Actualizado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Borrado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El Descriptivo se ha enviado a revisión de RH. Gracias por tu participación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Descriptivo de Puesto</h5>
						</div>

					<div class="panel-body">
                    
                    <p> La descripción de un puesto permite ubicarlo dentro de la organización, así como determinar los requisitos y cualificaciones personales mínimas que deben exigirse para un cumplimiento satisfactorio de las tareas: nivel de estudios, experiencia, requerimientos personales, entre otros.</p>
            <p>Los diferentes estatis son:<br />
            <ul>
            <li><strong>Pendiente</strong> = El Descriptivo no está capturado. </li>
            <li><strong>En captura</strong> = El Descriptivo está en proceso de captura. </li>
            <li><strong>Capturado: en revisión</strong> = En proceso de revisión por parte de RH.</li>
            <li><strong>Capturado: terminado</strong> = El descriptivo ha sido validado por RH y puede imprimirse.</li>
            </ul>

            <p>Solo los Descriptivos completos y validados por Recursos Humanos se pueden imprimir.</p>
            
                        
						<legend class="text-bold">Mi Descriptivo</legend>


			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary-600"> 
                          <th class="col-xs-1">IDpuesto</th>
                          <th class="col-xs-3">Denominacion</th>
                          <th class="col-xs-3">Ocupante</th>
                          <th class="col-xs-2">Area</th>
                          <th class="col-xs-1">Puesto Tipo</th>
                          <th class="col-xs-1">Estatus</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  

                      <?php do { ?>
                        <tr>
                          <td><?php echo $row_puesto_1['IDpuesto']; ?></td>
                          <td><?php echo $row_puesto_1['denominacion']; ?></td>
                          <td><?php echo $row_usuario['emp_paterno']." ". $row_usuario['emp_materno']." ". $row_usuario['emp_nombre']; ?></td>
                          <td><?php echo $row_puesto_1['area']; ?></td>
                          <td><?php if ($row_puesto_1['tipo'] == 1) {echo "Si";} else {echo "No";} ?></td>
                          <td><?php if ($row_puesto_1['descrito'] == 3) {echo "Capturado";}
						  elseif ($row_puesto_1['descrito'] == 2) {echo "Capturado:<br/> en revisión";}
						  elseif ($row_puesto_1['descrito'] == 1) {echo "En captura";}
						  else {echo "Pendiente";} ?></td>
                         <td>
               <?php if ($row_puesto_1['descrito'] == '' or $row_puesto_1['descrito'] == 1) { ?>
                <button type="button" class="btn btn-primary" onClick="window.location.href='f_dps_desc.php?IDpuesto=<?php echo $row_puesto_1['IDpuesto']; ?>'">Describir</button>
               <?php } elseif ($row_puesto_1['descrito'] == 3) { ?>
                <a class="btn btn-xs btn-success btn-block content-group" href="f_imprimir.php?IDpuesto=<?php echo $IDpuesto; ?>">Ver</a>
               <?php }  ?>
                		</td>
                        </tr>                       
                        <?php } while ($row_puesto_1 = mysql_fetch_assoc($puesto_1)); ?>
                   	</tbody>							  
                 </table>
<p>&nbsp; </p>



						<legend class="text-bold">Mis Colaboradores</legend>

			     		<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary-600"> 
                          <th class="col-xs-1">IDpuesto</th>
                          <th class="col-xs-3">Denominacion</th>
                          <th class="col-xs-3">Ocupante</th>
                          <th class="col-xs-2">Area</th>
                          <th class="col-xs-1">Puesto Tipo</th>
                          <th class="col-xs-1">Estatus</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php if($totalRows_puesto_2 > 0) { ?>
                      <?php do { 
					  
						$IDpuesto = $row_puesto_2['IDpuesto'];
						mysql_select_db($database_vacantes, $vacantes);
						$query_elusuario = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, Count(prod_activos.IDpuesto) AS Totales
						FROM prod_activos WHERE prod_activos.IDpuesto = '$IDpuesto'";
						$elusuario = mysql_query($query_elusuario, $vacantes) or die(mysql_error());
						$row_elusuario = mysql_fetch_assoc($elusuario);
						$elmpleado = $row_elusuario['Totales'];
						
					  	    if ($elmpleado == ''){ $ocupante = "SIN OCUPANTE";}
					  	elseif ($elmpleado > 1  ){ $ocupante = "PUESTO TIPO";}
					  	  else { $ocupante =  $row_elusuario['emp_paterno'] . " ".$row_elusuario['emp_materno']." ".$row_elusuario['emp_nombre'];}
					  
					  ?>
                        <tr>
                          <td><?php echo $row_puesto_2['IDpuesto']; ?></td>
                          <td><?php echo $row_puesto_2['denominacion']; ?></td>
                          <td><?php echo $ocupante; ?></td>
                          <td><?php echo $row_puesto_2['area']; ?></td>
                          <td><?php if ($row_puesto_2['tipo'] == 1) {echo "Si";} else {echo "No";} ?></td>
                          <td><?php if ($row_puesto_2['descrito'] == 3) {echo "Capturado:<br/> terminado";}
						  elseif ($row_puesto_2['descrito'] == 2) {echo "Capturado:<br/> en revisión";}
						  elseif ($row_puesto_2['descrito'] == 1) {echo "En captura";}
						  else {echo "Pendiente";} ?></td>
                         <td>
				<?php if ($row_puesto_2['descrito'] == '' or $row_puesto_2['descrito'] == 1) { ?>
                <button type="button" class="btn btn-primary" onClick="window.location.href='f_dps_desc.php?IDpuesto=<?php echo $row_puesto_2['IDpuesto']; ?>'">Describir</button>
               <?php } elseif ($row_puesto_2['descrito'] == 3) { ?>
                <a class="btn btn-xs btn-success btn-block content-group" href="f_imprimir.php?IDpuesto=<?php echo $row_puesto_2['IDpuesto']; ?>">Ver</a>
               <?php }  ?>                		</td>
                        </tr>                       
                        <?php } while ($row_puesto_2 = mysql_fetch_assoc($puesto_2)); ?>
                      <?php } else { ?>
                        <tr>
                          <td></td>
                          <td>No tiene colaboradores asignados.</td>
                          <td></td>
                          <td></td>
                          <td></td>
                         <td>
                		</td>
                        </tr>                       
                      <?php } ?>
                   	</tbody>							  
                 </table>
                    
                    
                    </div>

					<!-- /Contenido -->
                </div>
				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
			    </div>
				    <!-- /footer -->
                </div>
				<!-- /content area -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>