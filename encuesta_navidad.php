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
$el_mes = date("m"); 


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];
$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$IDllave = $row_usuario['IDllave'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_encuesta = "SELECT encuesta.IDencuesta, encuesta.observaciones, encuesta.Evaluador AS Evar, encuesta.Evaluado AS Eva, encuesta.Estatus As Estatus, evaluado.IDusuario AS IDevaluador, evaluado.usuario_nombre AS Evaluado, evaluado.usuario_parterno AS Paterno, evaluador.IDusuario AS IDevaluado, evaluador.usuario_nombre AS Evaluador, vac_puestos.denominacion FROM encuesta INNER JOIN vac_usuarios AS evaluador ON encuesta.Evaluador = evaluador.IDusuario INNER JOIN vac_usuarios AS evaluado ON encuesta.Evaluado = evaluado.IDusuario INNER JOIN vac_puestos ON evaluado.IDusuario_puesto = vac_puestos.IDpuesto WHERE evaluador.IDusuario = $el_usuario ORDER BY Evaluado ASC";
$encuesta = mysql_query($query_encuesta, $vacantes) or die(mysql_error());
$row_encuesta = mysql_fetch_assoc($encuesta);
$totalRows_encuesta = mysql_num_rows($encuesta);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$IDencuesta = $_POST["IDencuesta"];
$updateSQL = sprintf("UPDATE encuesta SET observaciones=%s, Estatus=1 WHERE IDencuesta = '$IDencuesta'",
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($IDencuesta, "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location: encuesta_navidad.php?info=1"); 

}

$query_cierre = "SELECT encuesta.IDencuesta, encuesta.observaciones, encuesta.Evaluador AS Evar, encuesta.Evaluado AS Eva, encuesta.Estatus, evaluado.IDusuario AS IDevaluador, evaluado.usuario_nombre AS Evaluado, evaluador.IDusuario AS IDevaluado, evaluador.usuario_nombre AS Evaluador FROM encuesta INNER JOIN vac_usuarios AS evaluador ON encuesta.Evaluador = evaluador.IDusuario INNER JOIN vac_usuarios AS evaluado ON encuesta.Evaluado = evaluado.IDusuario WHERE evaluador.IDusuario = $el_usuario AND encuesta.observaciones IS NOT NULL";
$cierre = mysql_query($query_cierre, $vacantes) or die(mysql_error());
$row_cierre = mysql_fetch_assoc($cierre);
$totalRows_cierre = mysql_num_rows($cierre);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    
    <script src="assets/js/app.js"></script>
	<!-- /Theme JS files -->
 </head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>



			<!-- Content area -->
			  <div class="content">

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							<span class="text-semibold">Gracias.</span> por dejar tus comentarios.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<?php if ($totalRows_cierre > 1 AND ($totalRows_cierre == $totalRows_encuesta)) { ?>
					    <div class="alert bg-primary alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
                                <span class="text-semibold">Gracias.</span> por dejar tus comentarios. <a href="logout.php"> Ya has terminado.</a>
                            </div>
                        <?php } ?>
					    <!-- /basic alert -->
	
	
					<!-- Contenido -->
                  <div class="panel panel-flat">
                    <h4 class="text-center content-group-lg">
								ENCUESTA FIN DE AÑO
								<small class="display-block">2024</small>
					</h4>
                   </div>

		
					<!-- Registration form -->
						<div class="row">
							<div class="col-lg-12">
								<div class="panel registration-form">
									<div class="panel-body">

			<p>Estimado/a <?php echo $row_usuario['emp_nombre'];  ?>,<br/>

En este cierre de año, queremos tomarnos un momento para reflexionar sobre el esfuerzo, dedicación y apoyo mutuo que hemos compartido como equipo. Sabemos que detrás de cada logro están las personas que lo hicieron posible, y es momento de reconocernos mutuamente por todo lo que hemos construido juntos.</p>

<p>Te invitamos a participar en esta actividad especial:<br/>

Dedica un mensaje de reconocimiento a cada una de las personas en el listado.<br/>
Este es un espacio para expresar gratitud, destacar fortalezas o valorar el impacto que han tenido en tu experiencia este año.</p>

<p>&nbsp;</p>

    <div class="col-md-4"> <img class="img-circle img-responsive" src="img/navideno.jpg"> </div>
    <div class="col-md-8">
	
						<div class="table-responsive">
					<table class="table">
						<thead>
						 <tr class="bg-primary">
                          <th width="50%">Nombre</th>
                          <th width="25%">Estatus</th>
                          <th width="25%"></th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php do { ?>
                          <tr>
						  <td><?php echo $row_encuesta['Evaluado']." ".$row_encuesta['Paterno']; ?></td>
						  <td><?php if ($row_encuesta['Estatus'] != 0) { echo "<span class='text text-success'>Guardado</span>"; } else { echo "<span class='text text-warning'>Pendiente</span>";} ?></td>
							<td>
							<?php if ($row_encuesta['observaciones'] == '') { ?>
							<button type="button" data-target="#modal_theme_danger3<?php echo $row_encuesta['IDencuesta']; ?>"  data-toggle="modal" class="btn btn-warning"><i class="icon-gift"></i></button>
							<?php } else {?>
							<button type="button" data-target="#modal_theme_danger3<?php echo $row_encuesta['IDencuesta']; ?>"  data-toggle="modal" class="btn btn-success"><i class="icon-gift"></i></button>
							<?php } ?>
							</td>
                           </tr>     

					<!-- danger modal -->
					<div id="modal_theme_danger3<?php echo $row_encuesta['IDencuesta']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h1 class="modal-title">Mensaje para <?php echo $row_encuesta['Evaluado']; ?></h1>
								</div>

								<div class="modal-body">
								
										<form method="post" name="form1" action="encuesta_navidad.php" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
											<fieldset class="content-group">
												
												<!-- Basic text input -->
												  <div class="form-group">
														<div class="col-lg-12">
														  <textarea name="observaciones" required="required" rows="4" class="form-control" id="observaciones" placeholder="Escribe aqui tu mensaje."><?php echo $row_encuesta['observaciones']; ?></textarea>

														</div>
													</div>
												<!-- /basic text input -->
													
												</div>

												<div class="modal-footer">
													 <button type="submit"  name="KT_Update1" class="btn btn-primary">Guardar</button>
													 <input type="hidden" name="MM_update" value="form1">
													 <input type="hidden" name="IDusuario" value="<?php echo $el_usuario; ?>">
													 <input type="hidden" name="IDencuesta" value="<?php echo $row_encuesta['IDencuesta']; ?>">
													 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
												
											</fieldset>
										</form>
							</div>
						</div>
					</div>
					<!-- danger modal -->


						   
                		 <?php } while ($row_encuesta = mysql_fetch_assoc($encuesta)); ?>
					    </tbody>
				    </table>
				</div>                   

	
	</div>

											
											</div>
										</div>
									</div>
								</div>
					<!-- /registration form -->

				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
			    </div>
				    <!-- /footer -->

					</div>

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