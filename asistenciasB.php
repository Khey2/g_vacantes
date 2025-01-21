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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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
$IDusuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

if(isset($_POST['mi_mes'])) {$_SESSION['mi_mes'] = $_POST['mi_mes'];} 
if(!isset($_SESSION['mi_mes'])) {$_SESSION['mi_mes'] = date("n");}

if(isset($_POST['mi_anio'])) {$_SESSION['mi_anio'] = $_POST['mi_anio'];}
if(!isset($_SESSION['mi_anio'])) {$_SESSION['mi_anio'] = 2024;}

$mi_mes = $_SESSION['mi_mes'];
$mi_anio = $_SESSION['mi_anio'];

mysql_select_db($database_vacantes, $vacantes);
$query_update = "SELECT DATE(fecha_baja) AS fecha_baja FROM prod_activosfaltas GROUP BY DATE(fecha_baja) ORDER BY prod_activosfaltas.fecha_baja DESC LIMIT 1";
$update = mysql_query($query_update, $vacantes) or die(mysql_error());
$row_update = mysql_fetch_assoc($update);
$totalRows_update = mysql_num_rows($update);
$ultima = $row_update['fecha_baja'];

function CalendarioPHP($year, $month, $day_heading_length = 3){
	
		include('Connections/vacantes.php');
	
		$colname_usuario = $_SESSION['kt_login_id'];
		$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
		$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
		$row_usuario = mysql_fetch_assoc($usuario);
		$IDmatriz = $row_usuario['IDmatriz'];
		$mi_anio = $_SESSION['mi_anio'];

// Parametros de aspecto del calendario
$nombreFichero = basename($_SERVER['PHP_SELF']);

// ----------- INICIO Dias Festivos ----------
$DiasFestivos[0] = '1/1'; // 1 de enero
// ----------- FIN Dias Festivos ----------

//Calculo la fecha actual
$dia_actual=date("j",time());
$mes_actual=date("n",time());
$anio_actual=date("Y",time());

$first_of_month = mktime (0,0,0, $month, 1, $year);
#remember that mktime will automatically correct if invalid dates are entered
# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
# this provides a built in "rounding" feature to generate_calendar()

static $day_headings = array('LUN','MAR','MIE','JUE','VIE','SAB','DOM');
$maxdays = date('t', $first_of_month); #number of days in the month
$date_info = getdate($first_of_month); #get info about the first day of the month
$month = $date_info['mon'];
$year = $date_info['year'];

//Traduzco los meses de ingles a Español
switch ($month) {
case 1 : $elmes="Enero";break;
case 2 : $elmes="Febrero";break;
case 3 : $elmes="Marzo";break;
case 4 : $elmes="Abril";break;
case 5 : $elmes="Mayo";break;
case 6 : $elmes="Junio";break;
case 7 : $elmes="Julio";break;
case 8 : $elmes="Agosto";break;
case 9 : $elmes="Septiembre";break;
case 10 : $elmes="Octubre";break;
case 11 : $elmes="Noviembre";break;
case 12 : $elmes="Diciembre";break;
};

//Comienzo la tabla que contiene el calendario
$calendar = ("<table height='600' class='table table-bordered table-striped'> ");

//Cabecera de la tabla calendario
$calendar .= "<tr><th colspan='7'>".$elmes." de ".$mi_anio."</th></tr>";

// Imprime los dias de la semana "Lun", "Mar", etc.
if($day_heading_length > 0 and $day_heading_length <= 4){
$calendar .= "<tr>";
foreach($day_headings as $day_heading){
$calendar .= "<th class='bg-primary'>".$day_heading."</th>\n";
}
$calendar .= "</tr>";
}
$calendar .= "<tr>";

//$weekday = $date_info['wday']; //Para que sea el Domingo el primer dia de la semana
$weekday = $date_info['wday']-1; #weekday (zero based) of the first day of the month
if ($weekday==-1) $weekday=6; //Por si el Domingo es el dia 1 del mes
$day = 1; #starting day of the month

// Cuidadin con los primeros dias "vacios" del mes
if($weekday > 0){
$calendar .= "<td class='calendar-day-np' colspan='".$weekday."'></td>";
}

//Imprimimos los dias del mes
while ($day <= $maxdays){
if($weekday == 7){ //Empieza una nueva semana
$calendar .= "</tr><tr>";
$weekday = 0;
}

//Miro si el dia que voy a pintar es festivo
$esFestivo = 0;
$tmp_date=$day."/".$month;
for ($i=0;$i<1;$i++) {
if ($tmp_date==$DiasFestivos[$i]) {$esFestivo=1;break;}
}

		$query_activoscapturados = "SELECT prod_activosfaltas.*, ind_asistencia.* FROM prod_activosfaltas LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND ind_asistencia.IDcapturador is not null AND ind_asistencia.IDfecha = '$day' AND ind_asistencia.mes = '$month' AND ind_asistencia.anio = '$year'";
		$activoscapturados = mysql_query($query_activoscapturados, $vacantes) or die(mysql_error());
		$row_activoscapturados = mysql_fetch_assoc($activoscapturados);
		$totalRows_activoscapturados = mysql_num_rows($activoscapturados); 
		//echo "Capturados ".$totalRows_activoscapturados."<br/>"; 
		
		$query_activosvalidados = "SELECT prod_activosfaltas.*, ind_asistencia.* FROM prod_activosfaltas LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND ind_asistencia.IDvalidador is not null AND ind_asistencia.IDfecha = '$day' AND ind_asistencia.mes = '$month' AND ind_asistencia.anio = '$year'";
		$activosvalidados = mysql_query($query_activosvalidados, $vacantes) or die(mysql_error());
		$row_activosvalidados = mysql_fetch_assoc($activosvalidados);
		$totalRows_activosvalidados = mysql_num_rows($activosvalidados); 
		//echo "Validados ".$totalRows_activoscapturados."<br/>"; 
		

$calendar .= "<td height='100'  ";
$link = "asistencias_editB.php?anio=".$year."&mes=".$month."&IDfecha=".$day;
$calendar .= "'><div><a href='".$link."'>";

$calendar .= "<div style='height:100%;width:100%'>";


		if($totalRows_activoscapturados > 0) {
			if($totalRows_activoscapturados = $totalRows_activosvalidados) { 
			$calendar.= " <span class='text text-success'><i class='icon-user-check'></i></span>"; 
			} else { 
			$calendar.= " <span class='text text-warning'><i class='icon-user-cancel'></i></span>"; 
			} 
		}


$calendar .= $day."</div></a></div></td>";
$day++;
$weekday++;
}

//Cuidadin con los ultimos dias vacios del mes
if($weekday != 7){
$calendar .= '<td class="calendar-day-np" colspan="' . (7 - $weekday) . '"></td>';
}

//Chinnnnn pon, devolvemos toda la cadena calendario
return $calendar . "</tr></table>";
} // Fin de la funcion CalendarioPHP(año, mes, caracteres del dia)

 
 $meses = array("01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	
	<style>
	td.calendar-day-np    { background:#eee; min-height:80px; } * html div.calendar-day-np { height:80px; }
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


					<!-- Sorting data -->
					<div class="panel panel-flat">

						<div class="panel-body">
							<H3>Asistencia Operaciones</H3>
							<p class="content-group"><b>Instrucciones</b><br />
							Selecciona el mes y el año para Filtrar el Calendario. Da clic en el día para VALIDAR la asistencia de todos los Empleados.<br />
							Solamente aplica para puestos del área de Operaciones (Almacén y Distribución).<br />
							<span class="text text-success"><i class="icon-user-check"></i> </span> = días validados correctamente.<br />
							<span class="text text-danger"><i class="icon-user-cancel"></i> </span> = días con validacion pendiente.</p>			
							<p class="content-group"><b>Fecha de última actualización: <?php echo date('d/m/Y', strtotime($ultima)); ?></b></p>

							
								<form method="POST" action="asistenciasB.php">
								<div class="panel-body text-center alpha-grey">
								
                                <a href="asistencias_reporte.php?IDmatriz=<?php echo $IDmatriz?>&anio=<?php echo $mi_anio?>&mes=<?php echo $mi_mes?>" class="btn bg-info-400"><i class="icon-file-excel position-left"></i>Descargar Reporte</a>

								 <div class="form-group col-md-2">
								 <select name="mi_mes" class="form-control ">
								   <option value="1"  <?php if (!(strcmp(1, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Enero</option>
								   <option value="2"  <?php if (!(strcmp(2, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Febrero</option>
								   <option value="3"  <?php if (!(strcmp(3, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Marzo</option>
								   <option value="4"  <?php if (!(strcmp(4, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Abril</option>
								   <option value="5"  <?php if (!(strcmp(5, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Mayo</option>
								   <option value="6"  <?php if (!(strcmp(6, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Junio</option>
								   <option value="7"  <?php if (!(strcmp(7, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Julio</option>
								   <option value="8"  <?php if (!(strcmp(8, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Agosto</option>
								   <option value="9"  <?php if (!(strcmp(9, $mi_mes)))  {echo "selected=\"selected\"";} ?>>Septiembre</option>
								   <option value="10" <?php if (!(strcmp(10, $mi_mes))) {echo "selected=\"selected\"";} ?>>Octubre</option>
								   <option value="11" <?php if (!(strcmp(11, $mi_mes))) {echo "selected=\"selected\"";} ?>>Noviembre</option>
								   <option value="12" <?php if (!(strcmp(12, $mi_mes))) {echo "selected=\"selected\"";} ?>>Diciembre</option>
								</select>
									</div>

									<div class="form-group col-md-2">
								 <select name="mi_anio" class="form-control ">
								 <option value="2025"  <?php if (!(strcmp(2025, $mi_anio)))  {echo "selected=\"selected\"";} ?>>2025</option>
								 <option value="2024"  <?php if (!(strcmp(2024, $mi_anio)))  {echo "selected=\"selected\"";} ?>>2024</option>
								   <option value="2023"  <?php if (!(strcmp(2023, $mi_anio)))  {echo "selected=\"selected\"";} ?>>2023</option>
								</select>
									</div>


                                 <div class="form-group col-md-1">
                              <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                                 </div>   
								 
								 </div>                                    
								</form>							

<?php 
echo CalendarioPHP($mi_anio, $mi_mes, 2);
?>
							
						</div>
					</div>
					<!-- /sorting data -->

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
