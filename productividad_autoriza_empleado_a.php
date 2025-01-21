<?php require_once('Connections/vacantes.php'); ?>
<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado
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
$IDmatrizes = $row_usuario['IDmatrizes'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$el_puesto = $_GET['IDpuesto'];

//las variables de sesion para el filtrado
if(isset($_GET['IDmatriz'])) { $_SESSION['la_matriz'] = $_GET['IDmatriz']; } 
else if(isset($_POST['la_matriz'])) { $_SESSION['la_matriz'] = $_POST['la_matriz']; } 
else { $_SESSION['la_matriz'] = $IDmatriz; }

//las variables de sesion para el filtrado
if(isset($_GET['IDpuesto'])) {$_SESSION['el_puesto'] = $_GET['IDpuesto']; } 
else if(isset($_POST['el_puesto'])) {$_SESSION['el_puesto'] = $_POST['el_puesto']; } 
else { $_SESSION['el_puesto'] = 2; }

$IDarea = 1;
if(isset($_GET['IDarea'])) {$_SESSION['el_area'] = $_GET['IDarea']; } 
else if(isset($_POST['el_area'])) { $_SESSION['el_area'] = $_POST['el_area']; } 
else { $_SESSION['el_area'] = $IDarea; }

$la_matriz = $_SESSION['la_matriz'];
$el_area = $_SESSION['el_area'];
$el_puesto = $_SESSION['el_puesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea in (1,2,3,4)";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_bmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$bmatriz = mysql_query($query_bmatriz, $vacantes) or die(mysql_error());
$row_bmatriz = mysql_fetch_assoc($bmatriz);
$totalRows_bmatriz = mysql_num_rows($bmatriz);

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_POST['IDcaptura'];
if ($_POST['a1'] == ''){$_a1 = 0;} else {$_a1 = $_POST['a1'];}
if ($_POST['a2'] == ''){$_a2 = 0;} else {$_a2 = $_POST['a2'];}
if ($_POST['a3'] == ''){$_a3 = 0;} else {$_a3 = $_POST['a3'];}
if ($_POST['a4'] == ''){$_a4 = 0;} else {$_a4 = $_POST['a4'];}
if ($_POST['a5'] == ''){$_a5 = 0;} else {$_a5 = $_POST['a5'];}
if ($_POST['a6'] == ''){$_a6 = 0;} else {$_a6 = $_POST['a6'];}
if ($_POST['a7'] == ''){$_a7 = 0;} else {$_a7 = $_POST['a7'];}
if ($_POST['a8'] == ''){$_a8 = 0;} else {$_a8 = $_POST['a8'];}
if ($_POST['a9'] == ''){$_a9 = 0;} else {$_a9 = $_POST['a9'];}
if ($_POST['a10'] == ''){$_a10 = 0;} else {$_a10 = $_POST['a10'];}
if ($_POST['a11'] == ''){$_a11 = 0;} else {$_a11 = $_POST['a11'];}
if ($_POST['a12'] == ''){$_a12 = 0;} else {$_a12 = $_POST['a12'];}
if ($_POST['a13'] == ''){$_a13 = 0;} else {$_a13 = $_POST['a13'];}
if ($_POST['a14'] == ''){$_a14 = 0;} else {$_a14 = $_POST['a14'];}
if ($_POST['a15'] == ''){$_a15 = 0;} else {$_a15 = $_POST['a15'];}
if ($_POST['a16'] == ''){$_a16 = 0;} else {$_a16 = $_POST['a16'];}
if ($_POST['a17'] == ''){$_a17 = 0;} else {$_a17 = $_POST['a17'];}
if ($_POST['a18'] == ''){$_a18 = 0;} else {$_a18 = $_POST['a18'];}
if ($_POST['a19'] == ''){$_a19 = 0;} else {$_a19 = $_POST['a19'];}
if ($_POST['a20'] == ''){$_a20 = 0;} else {$_a20 = $_POST['a20'];}
if ($_POST['a21'] == ''){$_a21 = 0;} else {$_a21 = $_POST['a21'];}
if ($_POST['a22'] == ''){$_a22 = 0;} else {$_a22 = $_POST['a22'];}
if ($_POST['a23'] == ''){$_a23 = 0;} else {$_a23 = $_POST['a23'];}
if ($_POST['a24'] == ''){$_a24 = 0;} else {$_a24 = $_POST['a24'];}
if ($_POST['a25'] == ''){$_a25 = 0;} else {$_a25 = $_POST['a25'];}
if ($_POST['a26'] == ''){$_a26 = 0;} else {$_a26 = $_POST['a26'];}
if ($_POST['a27'] == ''){$_a27 = 0;} else {$_a27 = $_POST['a27'];}
if ($_POST['a28'] == ''){$_a28 = 0;} else {$_a28 = $_POST['a28'];}
if ($_POST['adicional'] == ''){$adicional = 0;} else {$adicional = $_POST['adicional'];}

$updateSQL = sprintf("UPDATE prod_captura SET IDempleado=%s, emp_paterno=%s,  emp_materno=%s,  emp_nombre=%s, denominacion=%s, sueldo_total=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, anio=%s, IDmatriz=%s, IDsucursal=%s, IDarea=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, a6=%s, a7=%s, a8=%s, a9=%s, a10=%s, a11=%s, a12=%s, a13=%s, a14=%s, a15=%s, a16=%s, a17=%s, a18=%s, a19=%s, a20=%s, a21=%s, a22=%s, a23=%s, a24=%s, a25=%s, a26=%s, a27=%s, a28=%s, autorizador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s, lun_g=%s, mar_g=%s, mie_g=%s, jue_g=%s, vie_g=%s, sab_g=%s WHERE IDcaptura='$captura'",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_a1, "text"),
                       GetSQLValueString($_a2, "text"),
                       GetSQLValueString($_a3, "text"),
                       GetSQLValueString($_a4, "text"),
                       GetSQLValueString($_a5, "text"),
                       GetSQLValueString($_a6, "text"),
                       GetSQLValueString($_a7, "text"),
                       GetSQLValueString($_a8, "text"),
                       GetSQLValueString($_a9, "text"),
                       GetSQLValueString($_a10, "text"),
                       GetSQLValueString($_a11, "text"),
                       GetSQLValueString($_a12, "text"),
                       GetSQLValueString($_a13, "text"),
                       GetSQLValueString($_a14, "text"),
                       GetSQLValueString($_a15, "text"),
                       GetSQLValueString($_a16, "text"),
                       GetSQLValueString($_a17, "text"),
                       GetSQLValueString($_a18, "text"),
                       GetSQLValueString($_a19, "text"),
                       GetSQLValueString($_a20, "text"),
                       GetSQLValueString($_a21, "text"),
                       GetSQLValueString($_a22, "text"),
                       GetSQLValueString($_a23, "text"),
                       GetSQLValueString($_a24, "text"),
                       GetSQLValueString($_a25, "text"),
                       GetSQLValueString($_a26, "text"),
                       GetSQLValueString($_a27, "text"),
                       GetSQLValueString($_a28, "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['garantizado'], "int"),
                       GetSQLValueString($adicional, "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString(isset($_POST['lun']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['lun_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab_g']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "productividad_autoriza_puesto_uptdate_a.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//filtrado por sucursal
if(isset($_SESSION['la_sucursal'])) { $la_sucursal = $_SESSION['la_sucursal']; }  else {$la_sucursal = 0;}

if($la_sucursal > 0) {
$s1 = " AND prod_activos.IDsucursal = '$la_sucursal'"; 
} else {
$s1 = " "; 
$la_sucursal = 0;
} 

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado,  prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.bono_asistencia, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.adicional, prod_captura.adicional2, prod_captura.semana, prod_captura.capturador, prod_captura.validador, prod_captura.autorizador, prod_captura.observaciones, prod_captura.fecha_captura, prod_captura.reci, prod_captura.carg, prod_captura.esti, prod_captura.dist FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana'  AND prod_captura.anio = '$anio' WHERE prod_activos.IDpuesto = '$el_puesto' AND prod_activos.IDmatriz = '$la_matriz'" . $s1;
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDsucursal = '$la_sucursal'";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

$query_tipo_captura = "SELECT * FROM vac_puestos WHERE IDpuesto = $el_puesto";
$tipo_captura = mysql_query($query_tipo_captura, $vacantes) or die(mysql_error());
$row_tipo_captura = mysql_fetch_assoc($tipo_captura);
$prod_captura_tipo = $row_tipo_captura['prod_captura_tipo'];

$query_faltas = "SELECT * FROM prod_garantias WHERE IDmatriz = '$la_matriz' AND IDpuesto = '$el_puesto'";
$faltas = mysql_query($query_faltas, $vacantes) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$Faltas = $row_faltas['asistencia'];

if($la_sucursal > 0) {
$query_monto_bono = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz";
$monto_bono = mysql_query($query_monto_bono, $vacantes) or die(mysql_error());
$row_monto_bono = mysql_fetch_assoc($monto_bono);
} else {
$query_monto_bono = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = '$la_matriz'";
$monto_bono = mysql_query($query_monto_bono, $vacantes) or die(mysql_error());
$row_monto_bono = mysql_fetch_assoc($monto_bono);
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
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html59.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Autoriza Productividad</h5></br>
						 	
                        <!-- Basic alert -->
                        <?php if($Faltas == 1) { ?>
					    <div class="alert bg-info-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Si el empleado faltó una sola vez en la semana, pierde la totalidad del bono semanal.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


										<div class="form-group">
											<label class="control-label no-margin text-semibold">Sucursal:</label>
											<?php echo $row_bmatriz['matriz']; ?>
                                            <?php if ($la_sucursal > 0) { echo "/ " . $row_sucursal['sucursal']; }?>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<?php echo $row_tipo_captura['denominacion']; ?>
										</div>							
					</div>
					<div class="panel-body"> 
            <p>Selecciona el nombre del empelados para ver su histórico de pago de productividad. Da clic en el botón para capturar su productividad.</p>
            <p><strong>Bono Asistencia:</strong> $<?php if($row_monto_bono['monto_asistencia'] != 0) {echo $row_monto_bono['monto_asistencia'];} else { echo "0";}?></p>
            <p><strong>Semana:</strong> <?php echo $semana;?></p>
						<p>El colaborador debe cubrir un mínimo de <strong><?php echo $row_matriz['minimas'];?></strong> cajas diarias.</p>
						<p>El tope máximo de pago es del <strong>99%</strong>, en su caso el excedente está sujeto a autorización del Gerente Regional de Operaciones.</p>


                    <form method="POST" action="productividad_autoriza_empleado_a.php?IDpuesto=<?php echo $el_puesto; ?>">
					<table class="table">
						<tbody>							  
							<tr>
							<td> <div class="col-lg-9 no-prints">
										<select name="la_matriz" class="form-control">
										  <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>Sucursal: Todas</option>
                                          <?php do {  ?>
                                           <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
										   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz'];?></option>
											<?php
                                            } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                                              $rows = mysql_num_rows($lmatriz);
                                              if($rows > 0) {
                                                  mysql_data_seek($lmatriz, 0);
                                                  $row_lmatriz = mysql_fetch_assoc(lmatriz);
                                              } ?></select>
										</div>
                                    </td>
									<td>
                                <button type="submit" class="btn btn-success">Filtrar <i class="icon-filter3  position-right"></i></button>	
                            <button type="button" class="btn btn-default" onClick="window.location.href='productividad_autoriza_puesto.php?IDmatriz=<?php echo $la_matriz; ?>&IDarea=<?php echo $el_area; ?>'"> Regresar</button>
							</td>
					      </tr>
					    </tbody>
				    </table>
                    </form>	


					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>Acciones</th>
                          <th>No. Empleado</th>
                          <th>Empleado</th>
                          <th>Sueldo Base Calc.</th>
                          <th>Calculado (%)</th>
                          <th>Pago ($)</th>
                          <th>Garantizado</th>
                          <th>Asistencia</th>
                          <th>Adicional ($)</th>
                          <th>Total ($)</th>
                          <th>Reci</th>
                          <th>Carg</th>
                          <th>Esti</th>
                          <th>Dist</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { ?>
                          <tr>
                          <td>
                         <?php if ($row_puestos['validador'] == "") { ?>
                         Sin validación
                         <?php } elseif ($row_puestos['autorizador'] == "") { ?>
                         <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-success btn-icon"><i class="icon-arrow-right6"></i> Autorizar</div>
                        <?php } else { ?>
                         <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-primary btn-icon"><i class="icon-arrow-right6"></i> Autorizado</div>
                        <?php } ?>
                           </td>  
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><a href="prod_empleado_detalle.php?IDempleado=<?php echo $row_puestos['IDempleado']; ?>">
							<?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></a></td>
                            <td><?php echo "$" .number_format(($row_puestos['sueldo_total_productividad'] / 30) * 7); ?>
							<?php if ($row_puestos['sueldo_total_productividad'] != $row_puestos['sueldo_total'] AND $row_puestos['sueldo_total_productividad'] != 0) 	{ echo "<div class='label label-default'>V</div>"; } ?></td>
                            <td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo $row_puestos['pago']. "%";} ?></td>
                            <td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['pago_total']);} ?></td>
                            <td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
                            <td><?php if ($row_puestos['bono_asistencia'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['bono_asistencia']);} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional']);} ?></td>
                            <td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['pago_total'] + $row_puestos['adicional']);} ?></td>
                            <td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo $row_puestos['reci'];} ?></td>
                            <td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo $row_puestos['carg'];} ?></td>
                            <td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo $row_puestos['esti'];} ?></td>
                            <td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo $row_puestos['dist'];} ?></td>
                           </tr>
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>

                         <?php } else { ?>
                         <td colspan="12">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
					    </tbody>
				    </table>
                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Autoriza indicadores de productividad</h5>
								</div>
							<div class="modal-body">
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->
					</div>
					</div>

					<!-- /panel heading options -->

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
    var IDpuesto = <?php echo $el_puesto; ?>;
    var IDmatriz = <?php echo $la_matriz; ?>;
    var semana = <?php echo $semana; ?>;
    var IDsucursal = <?php echo $la_sucursal; ?>;

function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('productividad_autoriza_empleado_amdl.php?IDpuesto=' + IDpuesto + '&semana=' + semana + '&IDsucursal=' + IDsucursal + '&IDmatriz=' + IDmatriz + '&IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
</body>
</html>
<?php
mysql_free_result($variables);

mysql_free_result($puestos);
?>
