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

extract($_POST);
$status = "";
    if (isset($_POST["import"])){
//cargamos el archivo al servidor con el mismo nombre(solo le agregue el sufijo bak_)
$archivo = $_FILES['file']['name']; //captura el nombre del archivo
$tipo = $_FILES['file']['type']; //captura el tipo de archivo (2003 o 2007)
 
$destino = "bak_".$archivo; //lugar donde se copiara el archivo


if (copy($_FILES['file']['tmp_name'],$destino)) //si dese copiar la variable excel (archivo).nombreTemporal a destino (bak_.archivo) (si se ha dejado copiar)
{
echo "Archivo Cargado Con Exito";  
header("Location: productividad_importar_updatefaltas.php?info=1"); 	
}
else
{
echo "Error Al Cargar el Archivo"; 
header("Location: productividad_importar_updatefaltas.php?info=2"); 
}
 
////////////////////////////////////////////////////////
if (file_exists ("bak_".$archivo)) //validacion para saber si el archivo ya existe previamente
{
/*INVOCACION DE CLASES Y CONEXION A BASE DE DATOS*/
/** Invocacion de Clases necesarias */
chmod("bak_".$archivo, 0755);
require_once('assets/PHPExcel.php');
require_once('assets/PHPExcel/Reader/Excel2007.php');
$conn = mysqli_connect($hostname_vacantes, $username_vacantes ,$password_vacantes, $database_vacantes);


	// respaldo					
	//require_once('respaldo/backup_activos.php');


mysql_select_db($database_vacantes, $vacantes);
$query_resultado1 = "DELETE FROM prod_activosfaltas";
$resultado1 = mysql_query($query_resultado1, $vacantes) or die(mysql_error());

	// borrado
	//mysqli_query($conn, "DELETE FROM prod_activosfaltas WHERE prod_activosfaltas.manual = ''");


// Cargando la hoja de calculo
$objReader = new PHPExcel_Reader_Excel2007(); //instancio un objeto como PHPExcelReader(objeto de captura de datos de excel)
$objPHPExcel = $objReader->load("bak_".$archivo); //carga en objphpExcel por medio de objReader,el nombre del archivo
$objFecha = new PHPExcel_Shared_Date();

// Asignar hoja de excel activa
$objPHPExcel->setActiveSheetIndex(0); //objPHPExcel tomara la posicion de hoja (en esta caso 0 o 1) con el setActiveSheetIndex(numeroHoja)

// Llenamos un arreglo con los datos del archivo xlsx
$i=1; //celda inicial en la cual empezara a realizar el barrido de la grilla de excel
$param=0;
$contador=0;
while($param==0) //mientras el parametro siga en 0 (iniciado antes) que quiere decir que no ha encontrado un NULL entonces siga metiendo datos
{

$IDempleado = 		$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
$emp_paterno_ =		$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
$emp_paterno =  utf8_decode($emp_paterno_);
$emp_materno_ =		$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
$emp_materno =  utf8_decode($emp_materno_);
$emp_nombre_ = 		$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
$emp_nombre =  utf8_decode($emp_nombre_);
$rfc_ =		 		$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
$rfc =  str_replace("-", "", $rfc_);
$fecha_alta_ =		$objPHPExcel->getActiveSheet()->getCell('R'.$i)->getFormattedValue();
$y1 = substr( $fecha_alta_, 8, 2 );
$m1 = substr( $fecha_alta_, 3, 2 );
$d1 = substr( $fecha_alta_, 0, 2 );
$fecha_alta =  $y1."-".$m1."-".$d1;

$fecha_antiguedad_ =	$objPHPExcel->getActiveSheet()->getCell('S'.$i)->getFormattedValue();
$y2 = substr( $fecha_antiguedad_, 8, 2 );
$m2 = substr( $fecha_antiguedad_, 3, 2 );
$d2 = substr( $fecha_antiguedad_, 0, 2 );
$fecha_antiguedad =  $y2."-".$m2."-".$d2;

$fecha_baja_ =	$objPHPExcel->getActiveSheet()->getCell('AT'.$i)->getFormattedValue();
$y3 = substr( $fecha_baja_, 8, 2 );
$m3 = substr( $fecha_baja_, 3, 2 );
$d3 = substr( $fecha_baja_, 0, 2 );
$fecha_baja =  $y3."-".$m3."-".$d3;

$descripcion_nomina =$objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getCalculatedValue();
$descripcion_nivel =$objPHPExcel->getActiveSheet()->getCell('AI'.$i)->getCalculatedValue();
$denominacion1 =		$objPHPExcel->getActiveSheet()->getCell('AC'.$i)->getCalculatedValue();
$denominacion2 =  utf8_decode(trim(strtoupper($denominacion1)));

$originales = 'ÁÉÍÓÚáéíóúÑñ';
$originales = utf8_decode($originales); 
$modificadas ='AEIOUAEIOUNN'; 
$denominacion = strtr($denominacion2, $originales, $modificadas);
$estado = $objPHPExcel->getActiveSheet()->getCell('AS'.$i)->getCalculatedValue(); 

//ver si ya esta
$query_yasta = "SELECT * FROM prod_activosfaltas WHERE IDempleado = '$IDempleado'";
$yasta = mysql_query($query_yasta, $vacantes) or die(mysql_error());
$row_yasta = mysql_fetch_assoc($yasta);

if($IDempleado > 0 and $row_yasta['IDempleado'] == ''){
$query = "insert into prod_activosfaltas (IDempleado, emp_paterno, emp_materno, emp_nombre, fecha_alta, fecha_antiguedad, fecha_baja, descripcion_nomina, descripcion_nivel, denominacion, estado) values ('".$IDempleado."', '". $emp_paterno."', '". $emp_materno."','". $emp_nombre."','". $fecha_alta."','". $fecha_antiguedad."','".$fecha_baja."','". $descripcion_nomina."','".$descripcion_nivel ."','".$denominacion."','".$estado."')";
$result = mysqli_query($conn, $query) or die(mysql_error());
}

					
if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()==NULL) //pregunto que si ha encontrado un valor null en una columna inicie un parametro en 1 que indicaria el fin del ciclo while
{
$param=1; //para detener el ciclo cuando haya encontrado un valor NULL
}
$i++;
$contador=$contador+1;
}
$totalIngresados=$contador-1; //(porque se se para con un NULL y le esta registrando como que tambien un dato)
echo "Total elementos subidos: $totalIngresados "; 
header("Location: productividad_importar_updatefaltas.php?info=3"); 
}
else//si no se ha cargado el bak
{
echo "Necesitas primero importar el archivo"; 
header("Location: productividad_importar_updatefaltas.php?info=2"); 
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
<?php if(isset($_GET['info'])) {$info = $_GET['info']; } else {$info = 0;} ?>

						<?php if($info == 1) {  ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han importado correctamente los empleados. <a href="productividad_importar_updatefaltas3.php">Importa los activos a la tabla de productividad.</a>
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 2) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Problema al importar Empleados.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 3) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Archivo no permitido.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 4) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Puesto agregado.
					    </div>
					    <!-- /basic alert -->


						<?php } else if($info == 5) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Empleado Borrado.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Importar Productividad V2.0</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">Instrucciones para importar:</p>
									<p class="content-group">En el archivo de CATEM:</br>
                                    1.- Utiliza la pestaña ACTIVOS Y SUSPENDIDOS.</br>
                                    2.- Al terminar, validar los que aparecen sin datos <strong>aqui</strong>.</br>


                             <!-- Basic text input -->
                             <form action="productividad_importarfaltas.php" method="post" name="importar" id="importar" enctype="multipart/form-data">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Archivo (.xlsx, .xls):</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xls,.xlsx" class="form-control" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->
                              
                            <p>&nbsp;</p>
                              
                            <div>
						<input type="hidden" name="importar" id="importar" value"">
                         <button type="submit" id="submit" name="import" class="btn btn-primary">Importar Empleados</button>
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
    
 <?php
mysql_select_db($database_vacantes, $vacantes);
$query_resultado1 = "SELECT * FROM prod_activosfaltas WHERE IDllave is null";
mysql_query("SET NAMES 'utf8'");
$resultado1 = mysql_query($query_resultado1, $vacantes) or die(mysql_error());
$row_resultado1 = mysql_fetch_assoc($resultado1);
$totalRows_resultado1 = mysql_num_rows($resultado1);
?>  
    
    
    					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>Puesto</th>
                      <th>Nivel Nomina</th>
                      <th>Tipo Nomina</th>
                      <th>IDempleado</th>
                      <th>Nombre</th>
                      <th>Fecha Alta</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalRows_resultado1 > 0) { ?>
                        <?php do { ?>
                          <tr>
                            <td><?php echo $row_resultado1['denominacion']; ?>&nbsp; </td>
                            <td><?php echo $row_resultado1['descripcion_nivel']; ?>&nbsp; </td>
                            <td><?php echo $row_resultado1['descripcion_nomina']; ?>&nbsp; </td>
                            <td><?php echo $row_resultado1['IDempleado']; ?>&nbsp; </td>
                            <td><?php echo $row_resultado1['emp_paterno']. " " .$row_resultado1['emp_materno']. " ". $row_resultado1['emp_nombre']; ?>&nbsp; </td>
                            <td><?php echo $row_resultado1['fecha_alta']; ?>&nbsp; </td>
                            <td>
                            <a class="btn btn-warning" href="productividad_importar_corregirfaltas.php?IDempleado=<?php echo $row_resultado1['IDempleado']; ?>">Agregar Puesto</a>&nbsp;
                       <button type="button" data-target="#borrar<?php echo $row_resultado1['IDempleado']; ?>" data-toggle="modal" class="btn btn-danger btn-xs">Borrar Empleado</button></td>
                          </tr>	
                          
                    <!-- Modal de Borrado -->
					<div id="borrar<?php echo $row_resultado1['IDempleado']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Borrar Objetivo de Desempeño</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el Objetivo?.</p>
									<p>Solo borrar si no hay puestos faltantes.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="productividad_importar_corregirfaltas.php?IDempleado=<?php echo $row_resultado1['IDempleado']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- //Modal de Borrado -->


                          
                          <?php } while ($row_resultado1 = mysql_fetch_assoc($resultado1)); ?>
                        <?php } else { ?>
                          <tr>
                            <td>No se encontraron errores.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
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