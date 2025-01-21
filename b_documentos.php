<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/b_tNG.inc.php');

// Make unified connection variable
$conn_nom35 = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
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
$query_usuario = sprintf("SELECT * FROM capa_becarios WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

$IDsucursal = $row_usuario['IDsucursal'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_becarios  = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo FROM capa_becarios LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$becarios = mysql_query($query_becarios , $vacantes) or die(mysql_error());
$row_becarios = mysql_fetch_assoc($becarios);
$totalRows_becarios  = mysql_num_rows($becarios );
$ELempleado = $row_becarios['ELempleado'];
$Eltipo = $row_becarios['IDtipo'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

//variables
$IDmes = $_POST['IDmes'];
$IDanio = $_POST['IDanio'];
$IDtipo_file = $_POST['IDtipo_file'];
$fecha_doc = date("Y_m_d_hms"); 
$comentarios = $_POST['comentarios'];

$query_tipos_ = "SELECT extensiones FROM capa_becarios_tipo_file WHERE IDtipo_file = $IDtipo_file";
$tipos_ = mysql_query($query_tipos_, $vacantes) or die(mysql_error());
$row_tipos_ = mysql_fetch_assoc($tipos_);

// agregar PDF
$formatos_permitidos =  explode(",",$row_tipos_['extensiones']); 
print_r($formatos_permitidos);
$IDempleado_carpeta = 'becariosfiles/'.$ELempleado;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDtipo_file."_".$fecha_doc."_".$ELempleado.'.'.$extension;
$targetPath = 'becariosfiles/'.$ELempleado.'/'.$name_new;
// si se mandó archivo
if ($name != '') {	
	
	echo "Ext. ".$extension;

	
if(!in_array($extension, $formatos_permitidos) ) { echo "error archivos"; 
//header("Location: b_documentos.php?info=9"); 
exit;
}
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}
if ($name != '') {$name_new = $name_new;} else {$name_new = $foto_anterior; }

$query2 = "INSERT INTO capa_becarios_files (IDempleado, IDmes, anio, file, fecha, IDtipo_file, borrado, comentarios) VALUES ('$ELempleado', '$IDmes', '$IDanio', '$name_new', '$fecha', '$IDtipo_file', 0, '$comentarios')"; 
$result2 = mysql_query($query2) or die(mysql_error());  
header("Location: b_documentos.php?info=1"); 
	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDfile'];
  $deleteSQL = "UPDATE capa_becarios_files SET borrado = 1 WHERE IDfile ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: b_documentos.php?info=3");
}

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_meses = "SELECT * FROM vac_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_tipos = "SELECT * FROM capa_becarios_tipo_file";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

$query_doctosF = "SELECT * FROM capa_becarios_tipo_file WHERE  FIND_IN_SET($Eltipo, IDtipo)";
$doctosF = mysql_query($query_doctosF, $vacantes) or die(mysql_error());
$row_doctosF = mysql_fetch_assoc($doctosF);
$totalRows_doctosF = mysql_num_rows($doctosF);						

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
	<script src="global_assets/js/plugins/tables/datatables/bec_datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html_bec.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
	<!-- /Theme JS files -->
 </head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/b_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/b_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/b_pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El documento tiene un formato no autorizado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



	<div class="panel panel-flat">

	<div class="media panel-body no-margin">
		<div class="media-body">
                                    

								<ul class="media-list">
									<li class="media panel-body stack-media-on-mobile">
										<div class="media-left">
											<a href="#">
												<?php if ($row_becarios['Fotografia'] != '') { ?>
												<img src="<?php echo 'becariosfiles/'.$row_becarios['ELempleado'].'/'.$row_becarios['Fotografia']; ?>" alt="Fotografia" width="80" height="100"><br/>
												<?php } else { ?>
												<img src="files/foto.jpg" alt="Fotografia" width="80" height="100"><br/>
												<?php } ?>
											</a>
										</div>

										<div class="media-body">
											<h6 class="media-heading text-semibold">
												<a href="#"><?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?></a>
											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becarios['tipo']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becarios['fecha_alta'])); ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Modalidad:</strong> <?php if ($row_becarios['IDmodalidad'] == 1) {echo "Presencial";} else if ($row_becarios['IDmodalidad'] == 2) {echo "Remoto ";} else {echo "Mixto";} ?></li>
											</ul>												
										</div>
											
									</li>
								</ul>							

					<span class="display-block text-semibold"><h6>Mis Documentos</h6></span>
					<p>A continuación se muestran los documentos que has cargado.<br/>
					Da clic en <strong>Agregar Documento</strong> para agregar uno nuevo.</p>
					<h6>Documentos disponibles</h6>
					<ul><?php do { ?>
					<li><a target="_blank" href="becariosfiles/<?php echo $row_doctosF['file']; ?>"><?php echo $row_doctosF['tipo_file']; ?></a></li>
					<?php } while ($row_doctosF = mysql_fetch_assoc($doctosF));  ?></ul>
					

						<span class="pull-right"><button type="button" data-target="#modal_cargar_documento"  data-toggle="modal" class="btn btn-success">Agregar Documento</button></span>
					
						<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Año</th>
							    <th>Mes</th>
							    <th>Tipo</th>
							    <th>Fecha carga</th>
							    <th class="text-center">Acciones</th>
						    </tr>
					    </thead>
						<tbody>							  
						<?php			
						$query_doctos = "SELECT capa_becarios_tipo_file.tipo_file, capa_becarios_files.*  FROM capa_becarios_files LEFT JOIN capa_becarios_tipo_file ON capa_becarios_files.IDtipo_file = capa_becarios_tipo_file.IDtipo_file WHERE IDempleado = $el_usuario AND borrado = 0";
						$doctos = mysql_query($query_doctos, $vacantes) or die(mysql_error());
						$row_doctos = mysql_fetch_assoc($doctos);
						$totalRows_doctos = mysql_num_rows($doctos);						
				
						if ($totalRows_doctos) { do { 
						?>
							<tr>
							<td><?php echo $row_doctos['anio']; ?></td>
							<td><?php 
							
							  switch ($row_doctos['IDmes']) {
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

							echo $elmes; ?></td>
							<td><?php echo $row_doctos['tipo_file']; ?></td>
							<td><?php echo date( 'd/m/Y' , strtotime($row_doctos['fecha'])) ; ?></a></td>
							<td>
								<a target="_blank" href="becariosfiles/<?php echo $row_doctos['IDempleado']; ?>/<?php echo $row_doctos['file']; ?>" class="btn btn-primary">Descargar</a>
								<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger">Borrar</button>
								</td>
						    </tr>
							
							
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el documento?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="b_documentos.php?IDfile=<?php echo $row_doctos['IDfile']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

							
							<?php } while ($row_doctos = mysql_fetch_assoc($doctos)); } ?>
					    </tbody>
				    </table>


									<!-- danger modal -->
									<div id="modal_cargar_documento" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cargar Documento</h6>
												</div>
												<div class="modal-body">
																			
														<form action="b_documentos.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														<p>&nbsp;</p>


														<!-- Fecha -->
														<div class="form-group">
															<label class="control-label col-lg-3">Año:<span class="text-danger">*</span></label>
														<div class="col-lg-9">
																<select name="IDanio" id="IDanio" class="form-control" required="required">
																		  <option value="2022">2022</option>
																		  <option value="2023">2023</option>
																		  <option value="2024">2024</option>
																		  <option value="2025">2025</option>
																</select>
													   </div>
													  </div> 
														<!-- Fecha -->


														<!-- Fecha -->
														<div class="form-group">
															<label class="control-label col-lg-3">Mes:<span class="text-danger">*</span></label>
														<div class="col-lg-9">
																<select name="IDmes" id="IDmes" class="form-control" required="required">
																	<option value="">Seleccione una opción</option> 
																		  <?php do {  ?>
																		  <option value="<?php echo $row_meses['IDmes']?>"><?php echo $row_meses['mes']?></option>
																		  <?php
																		 } while ($row_meses = mysql_fetch_assoc($meses));
																		 $rows = mysql_num_rows($meses);
																		 if($rows > 0) {
																		 mysql_data_seek($meses, 0);
																		 $row_meses = mysql_fetch_assoc($meses);
																		 } ?>
															  </select>
													   </div>
													  </div> 
														<!-- Fecha -->


														<!-- Fecha -->
														<div class="form-group">
															<label class="control-label col-lg-3">Tipo:<span class="text-danger">*</span></label>
														<div class="col-lg-9">
																<select name="IDtipo_file" id="IDtipo_file" class="form-control" required="required">
																	<option value="">Seleccione una opción</option> 
																		  <?php do {  ?>
																		  <option value="<?php echo $row_tipos['IDtipo_file']?>"><?php echo $row_tipos['tipo_file']." (".$row_tipos['extensiones'].")"?></option>
																		  <?php
																		 } while ($row_tipos = mysql_fetch_assoc($tipos));
																		 $rows = mysql_num_rows($tipos);
																		 if($rows > 0) {
																		 mysql_data_seek($tipos, 0);
																		 $row_tipos = mysql_fetch_assoc($tipos);
																		 } ?>
															  </select>
													   </div>
													  </div> 
														<!-- Fecha -->

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Documento:<span class="text-danger">*</span></label>
															  <div class="col-lg-9">
															<input type="file" name="file" id="file" class="file-styled" required="required">
															 </div>
														  </div>
														  <!-- /basic text input -->


														 
															<input type="hidden" name="comentarios" id="comentarios" class="form-control" placeholder="Comentarios">

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-success">Cargar documento</button> 
													<input type="hidden" name="MM_insert" value="form1" />
												</div>
												</form>
											</div>
										</div>
									</div>
									<!-- danger modal -->


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
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>