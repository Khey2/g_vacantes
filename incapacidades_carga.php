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
$anio = $row_variables['anio'];
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
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
set_time_limit(0);


// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
//borramos para cargar de nuevo
$query_borrar = "DELETE FROM incapacidades_certificados_temp WHERE IDcertificado > 0";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
//header("Location: incapacidades_carga.php?info=3");
}

// borrar alternativo
if (isset($_GET['borrado'])) {
  
//borramos para cargar de nuevo
$borrado = $_GET['borrado'];
$query_borrar = "DELETE FROM incapacidades_certificados_temp WHERE IDcertificado = $borrado";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
//header("Location: incapacidades_carga.php?info=4");
}
	
// borrar alternativo
if ((isset($_GET['borrar_all'])) && ($_GET['borrar_all'] == 1)) {
  
	//borramos para cargar de nuevo
	$query_borrar = "DELETE FROM incapacidades_certificados_temp WHERE IDempleado = 'NA' OR empleado_activo = 0 OR empleado_accidente = 0";
	$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
	header("Location: incapacidades_carga.php?info=5");
}
	
	
extract($_POST);
$status = "";
    if (isset($_POST["import"])){
		
//borramos para cargar de nuevo
$query_borrar = "DELETE FROM incapacidades_certificados_temp WHERE IDcertificado > 0";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
		
		
//cargamos el archivo al servidor con el mismo nombre(solo le agregue el sufijo bak_)
$archivo = $_FILES['file']['name']; //captura el nombre del archivo
$tipo = $_FILES['file']['type']; //captura el tipo de archivo (2003 o 2007)
 
$destino = "bak_".$archivo; //lugar donde se copiara el archivo


if (copy($_FILES['file']['tmp_name'],$destino)) //si dese copiar la variable excel (archivo).nombreTemporal a destino (bak_.archivo) (si se ha dejado copiar)
{
//echo "Archivo Cargado Con Exito"; 
header("Location: incapacidades_carga.php?info=1"); 	
}
else
{
//echo "Error Al Cargar el Archivo"; 
header("Location: incapacidades_carga.php?info=2"); 
}
 
////////////////////////////////////////////////////////
if (file_exists ("bak_".$archivo)) //validacion para saber si el archivo ya existe previamente
{
/*INVOCACION DE CLASES Y CONEXION A BASE DE DATOS*/
/** Invocacion de Clases necesarias */
chmod("bak_".$archivo, 0755);
require_once('assets/PHPExcel.php');
require_once('assets/PHPExcel/Reader/Excel5.php');
$conn = mysqli_connect($hostname_vacantes, $username_vacantes ,$password_vacantes, $database_vacantes);


// Cargando la hoja de calculo
$objReader = new PHPExcel_Reader_Excel5(); //instancio un objeto como PHPExcelReader(objeto de captura de datos de excel)
$objPHPExcel = $objReader->load("bak_".$archivo); //carga en objphpExcel por medio de objReader,el nombre del archivo
$objFecha = new PHPExcel_Shared_Date();

// Asignar hoja de excel activa
$objPHPExcel->setActiveSheetIndex(0); //objPHPExcel tomara la posicion de hoja (en esta caso 0 o 1) con el setActiveSheetIndex(numeroHoja)

// Llenamos un arreglo con los datos del archivo xlsx
$i=2; //celda inicial en la cual empezara a realizar el barrido de la grilla de excel
$param=0;
$contador=0;
while($param==0) //mientras el parametro siga en 0 (iniciado antes) que quiere decir que no ha encontrado un NULL entonces siga metiendo datos
{


$chek1 =	$objPHPExcel->getActiveSheet()->getCell('A1')->getCalculatedValue();
if ($chek1 != 'NOMBRE') 		  {
	header("Location: incapacidades_carga.php?info=9"); 
}
$chek2 =	$objPHPExcel->getActiveSheet()->getCell('I1')->getCalculatedValue();
if ($chek2 != 'Dias Autorizados') {
	header("Location: incapacidades_carga.php?info=9"); 
}
$chek3 =	$objPHPExcel->getActiveSheet()->getCell('A2')->getCalculatedValue();
if ($chek3 == '')				  {
	header("Location: incapacidades_carga.php?info=9");
 }


$nombre = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
$nss = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
$folio_certificado =  $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
$ramo = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
$tipo_riesgo = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
$IDtipo_certificado_ = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
$fecha_inicio = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
$fecha_fin = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
$dias = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();

if($IDtipo_certificado_ == "INICIAL"){$IDtipo_certificado = 1;} else {$IDtipo_certificado = 2;}
	if($ramo == "EG") {$IDtipo_incapacidad = 1;} 
	else if($ramo == "AT") {$IDtipo_incapacidad = 2;}
	else if($ramo == "RT") {$IDtipo_incapacidad = 2;}
	else if($ramo == "MA") {$IDtipo_incapacidad = 3;}
	else if($ramo == "HC") {$IDtipo_incapacidad = 4;}

$fecha1b = explode("/",$fecha_inicio);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2b = explode("/",$fecha_fin);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];


// Buscamos que este activo 
$query_elempleado = "SELECT * FROM prod_activos WHERE imss = $nss";
$elempleado = mysql_query($query_elempleado, $vacantes) or die(mysql_error());
$row_elempleado = mysql_fetch_assoc($elempleado);
$totalRows_elempleado = mysql_num_rows($elempleado); 
$IDempleado = $row_elempleado['IDempleado'];
$IDmatriz_emp = $row_elempleado['IDmatriz'];

// Buscamos que tenga incapacidad
$query_elempleado2 = "SELECT * FROM incapacidades_accidentes WHERE nss = $nss AND IDestatus = 1";
$elempleado2 = mysql_query($query_elempleado2, $vacantes) or die(mysql_error());
$row_elempleado2 = mysql_fetch_assoc($elempleado2);
$totalRows_elempleado2 = mysql_num_rows($elempleado2); 
$IDincapacidad = $row_elempleado2['IDincapacidad'];

// Buscamos que no este duplicado
$query_elempleado3 = "SELECT * FROM incapacidades_certificados WHERE folio_certificado = '$folio_certificado'";
$elempleado3 = mysql_query($query_elempleado3, $vacantes) or die(mysql_error());
$row_elempleado3 = mysql_fetch_assoc($elempleado3);
$totalRows_elempleado3 = mysql_num_rows($elempleado3); 

if ($totalRows_elempleado3 > 0) {$duplicado = 1;} else {$duplicado = 0; }
if ($totalRows_elempleado == 0) {$IDmatriz_emp = 0;} else {$IDmatriz_emp = 0; }
if ($totalRows_elempleado == 0) {$IDempleado = "NA";}
if ($totalRows_elempleado > 0){$empleado_activo = 1;} else {$empleado_activo = 0; }
if ($totalRows_elempleado2 > 0){$empleado_accidente = 1;} else {$empleado_accidente = 0; }

//insertamos
$query = "insert into incapacidades_certificados_temp (IDmatriz, empleado_activo, IDempleado, empleado_accidente, nombre, IDincapacidad, nss, IDtipo_certificado, IDtipo_incapacidad, folio_certificado, fecha_inicio, fecha_fin, dias, duplicado, IDestatus) values ('".$IDmatriz_emp."','".$empleado_activo."','".$IDempleado."','".$empleado_accidente."', '".$nombre."', '".$IDincapacidad."', '".$nss."', '".$IDtipo_certificado."', '". $IDtipo_incapacidad."', '". $folio_certificado."', '". $fecha1."', '". $fecha2."', '".$dias."', '".$duplicado."', 2)"; 
$result = mysqli_query($conn, $query) or die(mysql_error());
					
if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()==NULL) //pregunto que si ha encontrado un valor null en una columna inicie un parametro en 1 que indicaria el fin del ciclo while
{
$param=1; //para detener el ciclo cuando haya encontrado un valor NULL
}
$i++;
$contador=$contador+1;
}
$totalIngresados=$contador-1; //(porque se se para con un NULL y le esta registrando como que tambien un dato)
echo "Total elementos subidos: $totalIngresados "; 
header("Location: incapacidades_carga.php?info=3"); 
}
else//si no se ha cargado el bak
{
echo "Necesitas primero importar el archivo"; 
header("Location: incapacidades_carga.php?info=2"); 
}
unlink($destino); //desenlazar a destino el lugar donde salen los datos(archivo)
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
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
<?php if(isset($_GET['info'])) {$info = $_GET['info']; } else {$info = 0;} ?>

						<?php if($info == 1) {  ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han pre cargado correctamente las Incapacidades.</a>
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 2) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Problema al importar Incapacidades.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 3) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado la carga inicial.
					    </div>
					    <!-- /basic alert -->


						<?php } else if($info == 4) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 5) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente los registros.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 6) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Datos cargados correctamente.
					    </div>
					    <!-- /basic alert -->



					<?php } ?>

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Carga de Incapacidades</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">Instrucciones:</br>
                                    1. Importar Excel (archivo .xls)</br>
                                    2. Confirmar resultado.<p>

                             <!-- Basic text input -->
                             <form action="incapacidades_carga.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
							 
							 <fieldset class="content-group">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Archivo (.xls):</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xls" class="file-styled" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->
                              
  							 </fieldset>

                              
  						<?php
						mysql_select_db($database_vacantes, $vacantes);
						$query_puestos = "SELECT * FROM incapacidades_certificados_temp";
						mysql_query("SET NAMES 'utf8'");
						$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
						$row_puestos = mysql_fetch_assoc($puestos);
						$totalRows_puestos = mysql_num_rows($puestos);
						?>  

							  
                            <div>
						<input type="hidden" name="importar" id="importar" value"">
                         <button type="submit" id="submit" name="import" class="btn btn-primary">Cargar archivo</button> 
						 <?php if ($totalRows_puestos > 0 ) { ?>
						 <button type="button" onClick="window.location.href='incapacidades_carga.php?borrar=1'" class="btn btn-danger">Borrar Carga</button>
						 <button type="button" onClick="window.location.href='incapacidades_carga.php?borrar_all=1'" class="btn btn-warning">Borrar N/A</button>
						 <?php } ?> &nbsp; &nbsp;
						 <?php if ($totalRows_puestos > 0) { ?>
						 <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-success">Cargar Resultado</button>
						 <?php } ?>
                            </div>
                             </form>

						</div>
						</div>

							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Resultado</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">

					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Activo</th>
                          <th>Con Accidente</th>
                          <th>NSS</th>
                          <th>Folio Incapacidad</th>
                          <th>IoS</th>
                          <th>Tipo Riesgo</th>
                          <th>Tipo</th>
                          <th>Fecha Inicio</th>
                          <th>Fecha Fin</th>
                          <th>Dias</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do {  ?>
						<tr>
						   <td><?php if ($row_puestos['IDempleado'] == "NA") { ?><a class="text text-danger text-semibold" href="incapacidades_carga.php?borrado=<?php echo $row_puestos['IDcertificado']; ?>">N/A</a><?php } else {echo $row_puestos['IDempleado'];} ?></td>
						   <td><?php echo $row_puestos['nombre'];?></td>
						   <td><?php if ($row_puestos['empleado_activo'] == 1) {echo "Si";} else { ?><a class="text text-danger text-semibold" href="incapacidades_carga.php?borrado=<?php echo $row_puestos['IDcertificado']; ?>">N/A</a><?php } ?></td>
						   <td><?php if ($row_puestos['empleado_accidente'] == 1) {echo "Si";} else { ?><a class="text text-danger text-semibold" href="incapacidades_carga.php?borrado=<?php echo $row_puestos['IDcertificado']; ?>">N/A</a><?php } ?></td>
						   <td><?php echo $row_puestos['nss']; ?></td>
                           <td><?php if ($row_puestos['duplicado'] == 1) { ?><i class="icon icon-check text-success"></i><?php } ?> <?php echo $row_puestos['folio_certificado']; ?></td>
                           <td><?php if ($row_puestos['IDtipo_certificado'] == 1) {echo "Inicial";} else if ($row_puestos['IDtipo_certificado'] == 2) {echo "Subsec.";} else {echo "-";}?></td>
                           <td></td>
                           <td>
							<?php if ($row_puestos['IDtipo_incapacidad'] == 1) {echo "(EG)";} 
								else if ($row_puestos['IDtipo_incapacidad'] == 2) {echo "(AT)";} 
								else if ($row_puestos['IDtipo_incapacidad'] == 3) {echo "(MA)";} 
								else if ($row_puestos['IDtipo_incapacidad'] == 4) {echo "(HC)";} 
								else if ($row_puestos['IDtipo_incapacidad'] == 2) {echo "(RT)";} 
								else {echo "-";}
						  ?>
						   </td>
                           <td><?php echo date( 'd/m/Y' , strtotime($row_puestos['fecha_inicio'])) ?></td>
                           <td><?php echo date( 'd/m/Y' , strtotime($row_puestos['fecha_fin'])) ?></td>
                           <td><?php echo $row_puestos['dias']; ?></td>
                           </tr>                         
						
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>

                         <?php } else { ?>
                         <td colspan="10">Cargue el layout.</td>
                         <?php } ?>
					    </tbody>
				    </table>
				</div>                   
	</div>
	</div>
    </div>


						                <!-- danger modal -->
										<div id="modal_theme_danger" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Carga de Incapacidades</h6>
												</div>
												<div class="modal-body">
												¿Estas seguro que quieres cargar las incapacidades?
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-success" href="incapacidades_cargar.php">Si cargar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->



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