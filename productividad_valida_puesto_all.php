<?php require_once('Connections/vacantes.php'); ?>
<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
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
$IDmatrizes = $row_usuario['IDmatrizes'];

$la_matriz = $row_usuario['IDmatriz'];
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
if ($_POST['a25'] == ''){$_a25 = 0;} else {$_a25 = $_POST['a25'];}
if ($_POST['a26'] == ''){$_a26 = 0;} else {$_a26 = $_POST['a26'];}
if ($_POST['a27'] == ''){$_a27 = 0;} else {$_a27 = $_POST['a27'];}
if ($_POST['a28'] == ''){$_a28 = 0;} else {$_a28 = $_POST['a28'];}

  $updateSQL = sprintf("UPDATE prod_captura SET IDempleado=%s, emp_paterno=%s,  emp_materno=%s,  emp_nombre=%s, denominacion=%s, sueldo_total=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, anio=%s, IDmatriz=%s, IDsucursal=%s,  IDarea=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, a6=%s, a7=%s, a25=%s, a26=%s, a27=%s, a28=%s, validador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s WHERE IDcaptura=%s",
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
                       GetSQLValueString($_a25, "text"),
                       GetSQLValueString($_a26, "text"),
                       GetSQLValueString($_a27, "text"),
                       GetSQLValueString($_a28, "text"),
                       GetSQLValueString($el_usuario, "text"),
                       GetSQLValueString($_POST['garantizado'], "int"),
                       GetSQLValueString($_POST['adicional'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString(isset($_POST['lun']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['IDcaptura'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "productividad_valida_puesto_update_all.php?IDcaptura=$captura";
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
$query_puestos = "SELECT prod_captura.bono_asistencia, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado,  prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.a25, prod_captura.a26, prod_captura.a27, prod_captura.a28, prod_captura.adicional, prod_captura.adicional2, prod_captura.semana, prod_captura.capturador, prod_captura.validador, prod_captura.observaciones, prod_captura.fecha_captura, prod_activos.denominacion FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio' WHERE prod_activos.IDpuesto = '$el_puesto' AND prod_activos.IDmatriz IN ($IDmatrizes)" . $s1;
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
mysql_select_db($database_vacantes, $vacantes);
$query_garantia = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz AND IDsucursal = $la_sucursal";
$garantia = mysql_query($query_garantia, $vacantes) or die(mysql_error());
$row_garantia = mysql_fetch_assoc($garantia);
$totalRows_garantia = mysql_num_rows($garantia);
$monto_asistencias = $row_garantia['monto_asistencia'];
} else {
mysql_select_db($database_vacantes, $vacantes);
$query_garantia = "SELECT * FROM prod_garantias WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz";
$garantia = mysql_query($query_garantia, $vacantes) or die(mysql_error());
$row_garantia = mysql_fetch_assoc($garantia);
$totalRows_garantia = mysql_num_rows($garantia);
$monto_asistencias = $row_garantia['monto_asistencia'];
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
   	<script src="global_assets/js/plugins/notifications/noty.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>

<?php if ($monto_asistencias > 0) { ?>
	<script>
      function load() {
       new Noty({
            text: 'Para este puesto aplica Bono Semanal de Asistencia, sujeto al indicador "Dias Laborados".',
            type: 'warning'
        }).show();
    }
	 window.onload = load;
     </script>
<?php } ?>
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
							<h6 class="panel-title">Valida Productividad. </h6></br>
                            
                            
                        <!-- Basic alert -->
                        <?php if($prod_captura_tipo == 2) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							La productividad de éste puesto, se reporta desde Corporativo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


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
											<?php echo $row_matriz['matriz']; ?>
                                            <?php if ($la_sucursal > 0) { echo "/ " . $row_sucursal['sucursal']; }?>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<?php echo $row_tipo_captura['denominacion']; ?>
										</div>							
						</div>

					<div class="panel-body"> 
                    <p>Selecciona el nombre del empelados para ver su histórico de pago de productividad. Da clic en el botón para capturar su productividad.</p>
					<p><div class='label label-default'>V</div> = Empleados que eran de Villosa. <strong>El sueldo semanal monstrado es el anterior y solo se usa para el pago de productividad.</strong></p> 

					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>Acciones</th>
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Sueldo Semanal</th>
                          <th>Calculado (%)</th>
                          <th>Pago ($)</th>
                          <th>Garantizado</th>
                          <th>Asistencia</th>
                          <th>Adicional ($)</th>
                          <th>Total ($)</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { ?>
                          <tr>
                          <td>
						   <?php if ($row_puestos['capturador'] == "" ) {  ?>  
                          Sin Captura
                           <?php } elseif ($row_puestos['validador'] == "") { ?>
                         <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-success btn-icon"><i class="icon-arrow-right6"></i> Validar</div>
                        <?php } else { ?>
                         <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-primary btn-icon"><i class="icon-arrow-right6"></i> Validada</div>
                        <?php } ?>
                          </td>  
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?></td>
                            <td><?php echo "$" . number_format(($row_puestos['sueldo_total_productividad'] / 30) * 7); ?>
							<?php if ($row_puestos['sueldo_total_productividad'] != $row_puestos['sueldo_total'] AND $row_puestos['sueldo_total_productividad'] != 0) 	{ echo "<div class='label label-default'>V</div>"; } ?></td>
                            <td><?php if ($row_puestos['pago'] == 0) 	{ echo "-"; } else { echo $row_puestos['pago']. "%";} ?></td>
                            <td><?php if ($row_puestos['pago_total'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['pago_total']);} ?></td>
                            <td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
							<td><?php if ($row_puestos['bono_asistencia'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['bono_asistencia']);} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional2']);} ?></td>
                            <td><?php $total = $row_puestos['pago_total'] + $row_puestos['adicional2']  + $row_puestos['bono_asistencia']; echo "$". number_format($total); ?></td>
                           </tr>                         
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>

                         <?php } else { ?>
                         <td colspan="10">Sin empleados con el filtro seleccionado.</td>
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
               					  <h5 class="modal-title">Captura de indicadores de productividad</h5>
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
    var IDmatriz = <?php echo $IDmatriz; ?>;
    var semana = <?php echo $semana; ?>;
    var IDsucursal = <?php echo $la_sucursal; ?>;

function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('productividad_valida_puesto_mdl_all.php?IDpuesto=' + IDpuesto + '&semana=' + semana + '&IDsucursal=' + IDsucursal + '&IDmatriz=' + IDmatriz + '&IDempleado='+ modal, function() {
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
