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
$IDusuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
set_time_limit(0);

extract($_POST);
$status = "";


if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $deleteSQL = "DELETE FROM capa_avance_temp WHERE IDC_capa > 0";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_cargaa.php?info=3");
}


if (isset($_POST["import"])){
		
//borramos para cargar de nuevo
$query_borrar = "DELETE FROM capa_avance_temp WHERE IDC_capa > 0";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
		
		
//cargamos el archivo al servidor con el mismo nombre(solo le agregue el sufijo bak_)
$archivo = $_FILES['file']['name']; //captura el nombre del archivo
$tipo = $_FILES['file']['type']; //captura el tipo de archivo (2003 o 2007)
 
$destino = "capa/bak_".$archivo; //lugar donde se copiara el archivo


if (copy($_FILES['file']['tmp_name'],$destino)) //si dese copiar la variable excel (archivo).nombreTemporal a destino (bak_.archivo) (si se ha dejado copiar)
{
//echo "Archivo Cargado Con Exito"; 
//header("Location: capa_cargaa.php?info=1"); 	
}
else
{
//echo "Error Al Cargar el Archivo"; 
//header("Location: capa_cargaa.php?info=4"); 
}
 
////////////////////////////////////////////////////////
if (file_exists ("capa/bak_".$archivo)) //validacion para saber si el archivo ya existe previamente
{
/*INVOCACION DE CLASES Y CONEXION A BASE DE DATOS*/
/** Invocacion de Clases necesarias */
chmod("capa/bak_".$archivo, 0755);
require_once('assets/PHPExcel.php');
require_once('assets/PHPExcel/Reader/Excel2007.php');
$conn = mysqli_connect($hostname_vacantes, $username_vacantes ,$password_vacantes, $database_vacantes);

// Cargando la hoja de calculo
$objReader = new PHPExcel_Reader_Excel2007(); //instancio un objeto como PHPExcelReader(objeto de captura de datos de excel)
$objPHPExcel = $objReader->load("capa/bak_".$archivo); //carga en objphpExcel por medio de objReader,el nombre del archivo
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
if ($chek1 != 'IDdempleado')	  {
	//header("Location: capa_cargaa.php?info=9"); 
	}
$chek2 =	$objPHPExcel->getActiveSheet()->getCell('B1')->getCalculatedValue();
if ($chek2 != 'Nombre del Curso') {
	//header("Location: capa_cargaa.php?info=9"); 
	}
$chek3 =	$objPHPExcel->getActiveSheet()->getCell('E1')->getCalculatedValue();
if ($chek3 != 'Programado') 	  {
	//header("Location: capa_cargaa.php?info=9"); 
	}

$IDempleado =	$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue(); 
$nombre_curso =	$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
$fecha_evento =	$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
$calificacion =	$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
$programado =	$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();

// datos del curso
if($nombre_curso != ''){

$nombre_curso_ = utf8_decode(htmlspecialchars($nombre_curso, ENT_NOQUOTES, "UTF-8")); 
//$nombre_curso_ = utf8_decode($nombre_curso); 

//echo $nombre_curso;
//echo " : ".$nombre_curso_." ";

$query_curso = "SELECT * FROM capa_cursos WHERE nombre_curso = '$nombre_curso'";
$curso = mysql_query($query_curso, $vacantes) or die(mysql_error());
$row_curso = mysql_fetch_assoc($curso);
$totalRows_curso = mysql_num_rows($curso);

if ($totalRows_curso > 0){
$IDcapa_eventos = $row_curso['IDC_capa_cursos']; 
$IDC_tipo_curso = $row_curso['IDC_tipo_curso'];
$IDtematicaSTPS = $row_curso['IDtematicaSTPS'];
$duracion = $row_curso['duracion'];
} else {
$IDcapa_eventos = 999; 
$IDC_tipo_curso = 999;
$IDtematicaSTPS = 999;
$duracion = 999;
}
}

//datos del empleado
if($IDempleado != ''){

$query_empleado = "SELECT * FROM ind_bajas WHERE IDempleado = '$IDempleado' ORDER BY fecha_baja asc LIMIT 1";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);

// si no está en bajas
if($totalRows_empleado == 0){

$query_empleado2 = "SELECT * FROM prod_activos WHERE IDempleado = '$IDempleado' ORDER BY fecha_baja asc LIMIT 1";
$empleado2 = mysql_query($query_empleado2, $vacantes) or die(mysql_error());
$row_empleado2 = mysql_fetch_assoc($empleado2);
$totalRows_empleado2 = mysql_num_rows($empleado2);
	
$la_curp = utf8_decode($row_empleado2['curp']);
$emp_paterno = utf8_decode($row_empleado2['emp_paterno']);
$emp_materno = utf8_decode($row_empleado2['emp_materno']);
$emp_nombre = utf8_decode($row_empleado2['emp_nombre']);
$denominacion = $row_empleado2['denominacion'];
$fecha_antiguedad = $row_empleado2['fecha_antiguedad'];
$fecha_baja = '';
$IDmatriz = $row_empleado2['IDmatriz'];
$IDsucursal = $row_empleado2['IDsucursal'];
$IDarea = $row_empleado2['IDarea'];
$IDpuesto = $row_empleado2['IDpuesto'];
if ($fecha_baja == '') {$estado = 1; } else {$estado = 0; }
$excluye_antiguedad = 0;
$descripcion_nomina = $row_empleado2['descripcion_nomina'];


} else {

$la_curp = utf8_decode($row_empleado['curp']);
$emp_paterno = utf8_decode($row_empleado['emp_paterno']);
$emp_materno = utf8_decode($row_empleado['emp_materno']);
$emp_nombre = utf8_decode($row_empleado['emp_nombre']);
$denominacion = $row_empleado['descripcion_puesto'];
$fecha_antiguedad = $row_empleado['fecha_antiguedad'];
$fecha_baja = $row_empleado['fecha_baja'];
$IDmatriz = $row_empleado['IDmatriz'];
$IDsucursal = $row_empleado['IDsucursal'];
$IDarea = $row_empleado['IDarea'];
$IDpuesto = $row_empleado['IDpuesto'];
//$estado = $row_empleado['estado'];
if ($fecha_baja == '') {$estado = 1; } else {$estado = 0; }

$date_a = new DateTime($fecha_antiguedad);
$date_b = new DateTime($fecha_baja);
$diff_c = $date_a->diff($date_b);
$diff_d =  $diff_c->days;
$descripcion_nomina = $row_empleado['descripcion_nomina'];


$excluye_antiguedad = 0;
if ($diff_d < 8) {$excluye_antiguedad = 1;}

}

$query_IDpuesto_capa = "SELECT * FROM vac_puestos WHERE IDpuesto = '$IDpuesto'";
$IDpuesto_capa = mysql_query($query_IDpuesto_capa, $vacantes) or die(mysql_error());
$row_IDpuesto_capa = mysql_fetch_assoc($IDpuesto_capa);
$IDpuesto_capa_carga = $row_IDpuesto_capa['IDC_tipo_puesto'];

$query_compania = "SELECT * FROM incapacidades_companias WHERE IDllave_compania = '$descripcion_nomina'";
$compania = mysql_query($query_compania, $vacantes) or die(mysql_error());
$row_compania = mysql_fetch_assoc($compania);
$totalRows_compania = mysql_num_rows($compania);
$IDcompania = $row_compania['IDcompania'];

}


// fecha curso
$y1 = substr( $fecha_evento , 8, 2 );
$m1 = substr( $fecha_evento , 3, 2 );
$d1 = substr( $fecha_evento , 0, 2 );
$fecha_evento_ = "20".$y1."-".$m1."-".$d1;
$anio_evento = "20".$y1; 
$mes_evento = $m1; 

if ($programado == 'Programado') {$IDC_programado = 1;} else if ($programado == 'No Programado') {$IDC_programado = 0;} else {$IDC_programado = '-';}

//insertamos
$query = "insert into capa_avance_temp (IDempleado, emp_paterno, emp_materno, emp_nombre, curp, IDC_capa_cursos, duracion, fecha_evento, anio, mes, calificacion, IDC_tipo_curso, IDtematicaSTPS, IDC_programado, IDmatriz, IDsucursal, IDarea, IDpuesto, denominacion, nombre_cargado, IDC_tipo_puesto, fecha_antiguedad, fecha_baja, excluye_antiguedad, estatus, IDusuario, IDcompania) values ('".$IDempleado."', '".$emp_paterno."', '".$emp_materno."', '".$emp_nombre."', '".$la_curp."', '".$IDcapa_eventos."', '".$duracion."', '".$fecha_evento_."', '". $anio_evento."', '". $mes_evento."', '". $calificacion."', '". $IDC_tipo_curso."', '". $IDtematicaSTPS."', '". $IDC_programado."','". $IDmatriz."','". $IDsucursal."','". $IDarea."','". $IDpuesto."','". $denominacion."','". $nombre_curso_."','". $IDpuesto_capa_carga."','". $fecha_antiguedad."','". $fecha_baja."','". $excluye_antiguedad."','". $estado."','". $IDusuario."','". $IDcompania."')";
$result = mysqli_query($conn, $query) or die(mysql_error());
					
if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()==NULL) //pregunto que si ha encontrado un valor null en una columna inicie un parametro en 1 que indicaria el fin del ciclo while
{
$param=1; //para detener el ciclo cuando haya encontrado un valor NULL
}
$i++;
$contador=$contador+1;
}
$totalIngresados=$contador-1; //(porque se para con un NULL y le esta registrando como que tambien un dato)
//echo "Total elementos subidos: $totalIngresados "; 

//borramos el ultimo que es error
$query_ultimo = "SELECT MAX(IDC_capa) as Mayor FROM capa_avance_temp";
$ultimo = mysql_query($query_ultimo, $vacantes) or die(mysql_error());
$row_ultimo = mysql_fetch_assoc($ultimo);
$IDultimo = $row_ultimo['Mayor']; //echo $IDultimo;
$deleteSQLB = "DELETE FROM capa_avance_temp WHERE IDC_capa = $IDultimo";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQLB, $vacantes) or die(mysql_error());

//header("Location: capa_cargaa.php?info=1"); 
}
else//si no se ha cargado el bak
{
//echo "Necesitas primero importar el archivo"; 
//header("Location: capa_cargaa.php?info=2"); 
}
unlink($destino); //desenlazar a destino el lugar donde salen los datos(archivo)
}

$query_meses = "SELECT * FROM vac_meses";
mysql_query("SET NAMES 'utf8'");
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_amatriz = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

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

	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/1picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
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
				
<?php if(isset($_GET['info'])) {$info = $_GET['info']; } else {$info = 0;} ?>

						<?php if($info == 1) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han pre cargado correctamente los cursos.</a>
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
							Se ha borrado correctamente.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 4) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo Cargado no es tiene el formato correcto.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 6) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Los datos se cargaron correctamente.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 5) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Los regustros seleccionados han sido borrados correctamente.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Importar Capacitación</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group"><b>Instrucciones:</b></br>
                                    1. Descarga y completa el <a href="capa/layout.xlsx">Layout de Carga.</a></br>
                                    2. Importar Excel (archivo .xlsx)</br>
                                    3. Validar datos cargados (repetidos, cursos no encontrados, etc).</br>
                                    4. Confirmar resultado y dar clic en "Cargar" para acumular los registros en el histórico.</p>

                             <!-- Basic text input -->
                             <form action="capa_cargaa.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
							 
							 <fieldset class="content-group">
							  <div class="form-group">
								  <label class="control-label col-lg-3">Archivo (.xlsx):</label>
								  <div class="col-lg-9">
									<input type="file" name="file" id="file" accept=".xlsx" class="form-control" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->
  							 </fieldset>
						<?php
						$query_capa = "SELECT capa_avance_temp.*, capa_cursos.nombre_curso, capa_tipos_cursos.tipo_evento, vac_areas.area, vac_matriz.matriz, vac_sucursal.sucursal FROM capa_avance_temp LEFT JOIN capa_cursos ON capa_avance_temp.IDC_capa_cursos = capa_cursos.IDC_capa_cursos LEFT JOIN capa_tipos_cursos ON capa_cursos.IDC_tipo_curso = capa_tipos_cursos.ID_tipo_evento LEFT JOIN vac_areas ON capa_avance_temp.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON capa_avance_temp.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON capa_avance_temp.IDsucursal = vac_sucursal.IDsucursal";
						mysql_query("SET NAMES 'utf8'");
						$capa = mysql_query($query_capa, $vacantes) or die(mysql_error());
						$row_capa = mysql_fetch_assoc($capa);
						$totalRows_capa = mysql_num_rows($capa);
						?>							  
                            <div>
						 <input type="hidden" name="importar" id="importar" value="">
                         <button type="submit" id="submit" name="import" class="btn btn-info">Importar</button>  &nbsp;
						 						 
						 <?php if ($totalRows_capa > 0) { ?>
						 <button type="button" data-target="#modal_theme_terminar"  data-toggle="modal" class="btn btn-success">Cargar</button>  &nbsp;
						 <?php } ?>
						 
						 <?php if ($totalRows_capa > 0 ) { ?>
						 <button type="button" data-target="#modal_theme_borrar"  data-toggle="modal" class="btn btn-danger">Borrar</button>  &nbsp;
						 <?php } ?>
						 
						<a href="capa_cargac_reporte.php" class="btn btn-primary">Descargar Excel</a> &nbsp;

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
                          <th>Obs.</th>
                          <th>Nombre</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
                          <th>Area</th>
                          <th>Evento</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_capa > 0) { ?>

                        <?php do { 
						

						//validar duplicados
						$valida1 = $row_capa['IDempleado'];
						$valida2 = $row_capa['fecha_evento'];
						$valida3 = $row_capa['IDC_capa_cursos'];

						$query_duplicados1 = "SELECT COUNT(IDempleado) AS Duplicados1 FROM capa_avance_temp WHERE IDempleado = $valida1 AND fecha_evento = '$valida2' AND IDC_capa_cursos = $valida3";
						$duplicados1 = mysql_query($query_duplicados1, $vacantes) or die(mysql_error());
						$row_duplicados1 = mysql_fetch_assoc($duplicados1); 
						
						$query_duplicados2 = "SELECT COUNT(IDempleado) AS Duplicados2 FROM capa_avance WHERE IDempleado = $valida1 AND fecha_evento = '$valida2' AND IDC_capa_cursos = $valida3";
						$duplicados2 = mysql_query($query_duplicados2, $vacantes) or die(mysql_error());
						$row_duplicados2 = mysql_fetch_assoc($duplicados2); 
						$totalRows_duplicados2 = mysql_num_rows($duplicados2); 

						$nombre = $row_capa['emp_paterno']." ".$row_capa['emp_materno']." ".$row_capa['emp_nombre']; 
						?>
                          <tr>
                            <td><a href="capa_detalle_empleado.php?IDempleado=<?php echo $row_capa['IDempleado']; ?>"><?php echo $row_capa['IDempleado']; ?></a></td>
                            <td><?php if ($row_duplicados1['Duplicados1'] > 1) { echo "<span class='text text-danger text-semibold'>Repetido al importar</span>"; } ?>
							<?php if ($row_duplicados2['Duplicados2'] > 0) { echo "<span class='text text-danger text-semibold'>Repetido en Historico</span>"; } ?>
							<?php if ($row_capa['fecha_evento'] == '0000-00-00') { echo "<span class='text text-warning text-semibold'>Fecha evento incorrecta</span>"; } ?>
								<?php if (strlen($nombre) <= 3) { echo "<span class='text text-warning text-semibold'>Empleado no existe</span>";} ?>
							</td>
                            <td>
							<?php if ($row_capa['IDC_capa_cursos'] == 999) { ?><a class="text text-warning" href="capa_catalogos_1.php?IDC_capa=<?php echo $row_capa['IDC_capa']; ?>"><i class="icon icon-xs icon-notification2"></i></a><?php } ?> &nbsp;
							<?php echo $nombre;   ?>
							</td>
                            <td><?php echo $row_capa['denominacion']; ?></td>
                            <td><?php echo $row_capa['matriz']; ?></td>
                            <td><?php echo $row_capa['area']; ?></td>
                            <td><a <?php if ($row_capa['IDC_capa_cursos'] == 999) {echo "class='collapsed text-warning'";} ?>
							data-toggle="collapse" href="#collapse-group<?php echo $row_capa['IDC_capa']; ?>">
							<?php if ($row_capa['IDC_capa_cursos'] == 999) {echo $row_capa['nombre_cargado']; } else { echo $row_capa['nombre_curso']; } ?>
							<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_capa['IDC_capa']; ?>" class="panel-collapse collapse">
								<ul>
								<li><strong>Fecha: </strong><?php echo $row_capa['fecha_evento']; ?></li>
								<li><strong>Calificacion: </strong><?php echo $row_capa['calificacion']; ?></li>
								<li><strong>Tipo: </strong><?php echo $row_capa['tipo_evento']; ?></li>
								<li><strong>Programado: </strong><?php if ($row_capa['IDC_programado'] == 1) {echo "SI";} else {echo "NO";} ?></li>
								</ul>
							</div>
							</td>
                           </tr>                         
						
                		 <?php } while ($row_capa = mysql_fetch_assoc($capa)); ?>

                         <?php } else { ?>
                         <td colspan="6">Importa primero el archivo. Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
				    </table>
				</div> 
	</div>
	</div>
    </div>
					<!-- /Contenido -->
					

					                <!-- danger modal -->
									<div id="modal_theme_borrar" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Borrado</h6>
												</div>
												<div class="modal-body">
												<p>¿Estas seguro que quieres borrar la información?</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="capa_cargaa.php?borrar=1">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->

					                <!-- danger modal -->
									<div id="modal_theme_terminar" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Importación</h6>
												</div>
												<div class="modal-body">
												<p>¿Estas seguro que quieres cargar la información?</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-success" href="capa_cargaa_importar.php">Si cargar</a>
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