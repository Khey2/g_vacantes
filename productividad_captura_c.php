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

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if (isset($_GET['el_puesto'])) { $_SESSION['el_puesto'] = $_GET['el_puesto']; } 
//else if (!isset($_SESSION['el_puesto'])) { $_SESSION['el_puesto'] = 17;}
$el_puesto =  $_SESSION['el_puesto'];



//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");

// actualizar
if ((isset($_GET["MM_update"])) && ($_GET["MM_update"] == "form1")) { 
	
$count=count($_POST['IDcaptura']); echo $count;

for($i=0;$i<$count;$i++){

$sql1="UPDATE prod_captura SET fecha_captura='" . $_POST['fecha_captura'][$i] . "', capturador='" . $_POST['capturador'][$i] . "', a1='" . $_POST['a1'][$i] . "', a2='" . $_POST['a2'][$i] . "', a3='" . $_POST['a3'][$i] . "', a4='" . $_POST['a4'][$i] . "', a5='" . $_POST['a5'][$i]. "' WHERE IDcaptura='" . $_POST['IDcaptura'][$i] . "'";
$result1=mysql_query($sql1); 

}

header("Location: productividad_captura_puesto_uptdate_c.php?IDpuesto=".$el_puesto);

}


//insertar
if ((isset($_GET["MM_insert"])) && ($_GET["MM_insert"] == "form1")) { 
$count=count($_POST['IDempleado']);


  $deleteSQL = "DELETE FROM prod_captura WHERE IDpuesto = '$el_puesto' AND semana = '$semana' AND anio = $anio";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());



for($i=0;$i<$count;$i++){
$insertSQL = sprintf("INSERT INTO prod_captura (IDempleado, emp_paterno, emp_materno, emp_nombre, denominacion, sueldo_total, IDpuesto, fecha_captura, semana, anio, IDmatriz, IDsucursal, IDarea, a1, a2, a3, a4, a5, capturador, lun, mar, mie, jue, vie, sab) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'][$i], "int"),
                       GetSQLValueString($_POST['emp_paterno'][$i], "text"),
                       GetSQLValueString($_POST['emp_materno'][$i], "text"),
                       GetSQLValueString($_POST['emp_nombre'][$i], "text"),
                       GetSQLValueString($_POST['denominacion'][$i], "text"),
                       GetSQLValueString($_POST['sueldo_total'][$i], "text"),
                       GetSQLValueString($_POST['IDpuesto'][$i], "int"),
                       GetSQLValueString($_POST['fecha_captura'][$i], "date"),
                       GetSQLValueString($_POST['semana'][$i], "int"),
                       GetSQLValueString($_POST['anio'][$i], "int"),
                       GetSQLValueString($_POST['IDmatriz'][$i], "int"),
                       GetSQLValueString($_POST['IDsucursal'][$i], "int"),
                       GetSQLValueString($_POST['IDarea'][$i], "int"),
                       GetSQLValueString($_POST['a1'][$i], "text"),
                       GetSQLValueString($_POST['a2'][$i], "text"),
                       GetSQLValueString($_POST['a3'][$i], "text"),
                       GetSQLValueString($_POST['a4'][$i], "text"),
                       GetSQLValueString($_POST['a5'][$i], "text"),
                       GetSQLValueString($_POST['capturador'][$i], "text"),
                       GetSQLValueString($_POST['lun'][$i], "text"),
                       GetSQLValueString($_POST['mar'][$i], "text"),
                       GetSQLValueString($_POST['mie'][$i], "text"),
                       GetSQLValueString($_POST['jue'][$i], "text"),
                       GetSQLValueString($_POST['vie'][$i], "text"),
                       GetSQLValueString($_POST['sab'][$i], "text"));

                     //  echo $insertSQL;

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  //var_dump($_POST['IDempleado']);
}

header("Location: productividad_captura_puesto_uptdate_c.php?IDpuesto=".$el_puesto);
}


mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz NOT IN (27,7)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
//filtrado por sucursal

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDsucursal, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado,  prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.a1, prod_captura.a2, prod_captura.a3, prod_captura.a4, prod_captura.a5, prod_captura.a6, prod_captura.a7, prod_captura.adicional, prod_captura.adicional, prod_captura.semana, prod_captura.capturador, prod_captura.observaciones, prod_captura.fecha_captura, vac_puestos.denominacion, vac_puestos.modal, vac_matriz.matriz FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio' LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE vac_puestos.IDpuesto = '$el_puesto' ORDER BY prod_activos.IDmatriz ASC"; 
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos); 
$totalRows_puestos = mysql_num_rows($puestos);
$capturado = $row_puestos['capturador'];

$query_cap_up = "SELECT * FROM prod_captura WHERE prod_captura.IDpuesto = '$el_puesto' AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio'";
$cap_up = mysql_query($query_cap_up, $vacantes) or die(mysql_error());
$row_cap_up = mysql_fetch_assoc($cap_up);
$totalRows_cap_up = mysql_num_rows($cap_up);


mysql_select_db($database_vacantes, $vacantes);
$query_lpuesto = "SELECT * FROM vac_puestos WHERE vac_puestos.prod_captura_tipo = 2";
$lpuesto = mysql_query($query_lpuesto, $vacantes) or die(mysql_error());
$row_lpuesto = mysql_fetch_assoc($lpuesto);
$totalRows_lpuesto = mysql_num_rows($lpuesto);
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
    <script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect4.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/components_popups.js"></script>
	
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
              <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha guardado correctamente la productividad del puesto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
              <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la productividad del puesto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Captura semanal de Productividad - Coroporativo. </h5>
                        </div>

					<div class="panel-body"> 
                    <p>Selecciona el nombre del empelados para ver su histórico de pago de productividad.<br/>
                    Da clic en el botón para capturar su productividad.<br/>
                    El % de pago está sujeto a la validación de RH.<br/>
                    Utiliza el filtro para mostrar puestos Aplicables.</p>

                    <p><button type="button" class="btn btn-default" onClick="window.location.href='productividad_captura_corpo.php'">Regresar</button></p>

                    <p><strong>Puesto actual:</strong> <?php echo $row_puestos['denominacion']; ?>.</p>
                    <p><strong>Semana:</strong> <?php echo $semana; ?>.</p>
             
                    
            <?php if ( $totalRows_puestos == $totalRows_cap_up ) { ?>
              <form method="post" class="form-horizontal" name="form1" action="productividad_captura_c.php?IDpuesto=<?php echo $el_puesto;?>&MM_update=form1" >
						<?php } else { ?>
              <form method="post" class="form-horizontal" name="form1" action="productividad_captura_c.php?IDpuesto=<?php echo $el_puesto;?>&MM_insert=form1" >
						<?php } ?>


					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>Matriz</th>
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                        <?php
						$el_puesto =  $row_puestos['IDpuesto'];
						$la_matriz =  $row_puestos['IDmatriz'];
						$query_criterio2 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 1 AND prod_kpis.b = 1 AND prod_kpis.IDmatriz = $la_matriz";
						$criterio2 = mysql_query($query_criterio2, $vacantes) or die(mysql_error());
						$row_criterio2 = mysql_fetch_assoc($criterio2);

						$query_criterio3 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 2 AND prod_kpis.b = 1 AND prod_kpis.IDmatriz = $la_matriz";
						$criterio3 = mysql_query($query_criterio3, $vacantes) or die(mysql_error());
						$row_criterio3 = mysql_fetch_assoc($criterio3);

						$query_criterio4 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 3 AND prod_kpis.b = 1 AND prod_kpis.IDmatriz = $la_matriz";
						$criterio4 = mysql_query($query_criterio4, $vacantes) or die(mysql_error());
						$row_criterio4 = mysql_fetch_assoc($criterio4);

						$query_criterio5 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 4 AND prod_kpis.b = 1 AND prod_kpis.IDmatriz = $la_matriz";
						$criterio5 = mysql_query($query_criterio5, $vacantes) or die(mysql_error());
						$row_criterio5 = mysql_fetch_assoc($criterio5);
						?>
						  <th>% pago</th>
						  <th><?php echo $row_criterio2['c']; ?></th>
						 <?php if ( $row_criterio3['c'] != '') { ?><th><?php echo $row_criterio3['c']; ?></th><?php } ?>
						 <?php if ( $row_criterio4['c'] != '') { ?><th><?php echo $row_criterio4['c']; ?></th><?php } ?>
						 <?php if ( $row_criterio5['c'] != '') { ?><th><?php echo $row_criterio5['c']; ?></th><?php } ?>
                        </tr>
						</thead>
						<tbody>							  
                        <?php 
						$query_criterios2 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 1 AND prod_kpis.b = 3 AND prod_kpis.IDmatriz = $la_matriz";
						$criterios2 = mysql_query($query_criterios2, $vacantes) or die(mysql_error());
						$row_criterios2 = mysql_fetch_assoc($criterios2);

						$query_criterios3 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 2 AND prod_kpis.b = 3 AND prod_kpis.IDmatriz = $la_matriz";
						$criterios3 = mysql_query($query_criterios3, $vacantes) or die(mysql_error());
						$row_criterios3 = mysql_fetch_assoc($criterios3);

						$query_criterios4 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 3 AND prod_kpis.b = 3  AND prod_kpis.IDmatriz = $la_matriz";
						$criterios4 = mysql_query($query_criterios4, $vacantes) or die(mysql_error());
						$row_criterios4 = mysql_fetch_assoc($criterios4);

						$query_criterios5 = "SELECT * FROM prod_kpis WHERE prod_kpis.IDPuesto = '$el_puesto' AND prod_kpis.a = 4 AND prod_kpis.b = 3 AND prod_kpis.IDmatriz = $la_matriz";
						$criterios5 = mysql_query($query_criterios5, $vacantes) or die(mysql_error());
						$row_criterios5 = mysql_fetch_assoc($criterios5);
						do { ?>
						  <tr>
                          <input name="emp_paterno[]" type="hidden" id="emp_paterno" value="<?php echo $row_puestos['emp_paterno']; ?>">
                          <input name="emp_materno[]" type="hidden" id="emp_materno" value="<?php echo $row_puestos['emp_materno']; ?>">
                          <input name="emp_nombre[]" type="hidden" id="emp_nombre" value="<?php echo $row_puestos['emp_nombre']; ?>">
                          <input name="denominacion[]" type="hidden" id="denominacion" value="<?php echo $row_puestos['denominacion']; ?>">
                          <input name="sueldo_total[]" type="hidden" id="sueldo_total" value="<?php echo $row_puestos['sueldo_total_productividad']; ?>">
                          <input name="IDempleado[]" type="hidden" id="IDempleado" value="<?php echo $row_puestos['IDempleado']; ?>">
                          <input name="IDpuesto[]" type="hidden" id="IDpuesto" value="<?php echo $row_puestos['IDpuesto']; ?>">
                          <input name="IDmatriz[]" type="hidden" id="IDmatriz" value="<?php echo $row_puestos['IDmatriz']; ?>">
                          <input name="IDsucursal[]" type="hidden" id="IDsucursal" value="<?php echo $row_puestos['IDsucursal']; ?>">
                          <input name="IDarea[]" type="hidden" id="IDarea" value="<?php echo $row_puestos['IDarea']; ?>">
                          <input name="fecha_captura[]" type="hidden" id="fecha_captura" value="<?php echo $fecha; ?>">
               			      <input name="IDcaptura[]" type="hidden" id="IDcaptura" value="<?php echo $row_puestos['IDcaptura']; ?>">
                          <input name="semana[]" type="hidden" id="semana" value="<?php echo $semana; ?>">
                          <input name="anio[]" type="hidden" id="anio" value="<?php echo $anio; ?>">
                          <input name="a5[]" type="hidden" id="a5" value="<?php echo "0"; ?>">
                          <input name="capturador[]" type="hidden" id="capturador" value="<?php echo $el_usuario; ?>">
                          <input name="lun[]" type="hidden" id="lun" value="1">
                          <input name="mar[]" type="hidden" id="mar" value="1">
                          <input name="mie[]" type="hidden" id="mie" value="1">
                          <input name="jue[]" type="hidden" id="jue" value="1">
                          <input name="vie[]" type="hidden" id="vie" value="1">
                          <input name="sab[]" type="hidden" id="sab" value="1">
                          <input name="dom[]" type="hidden" id="dom" value="1">
                          
                          
                            <td><?php echo $row_puestos['matriz']; ?></td>
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></td>
							<th><?php if  ($row_puestos['pago'] == '') {echo 0;} else { echo round($row_puestos['pago'], 0)."% | $".number_format($row_puestos['pago_total'], 0);}; ?> </th>
                            <td> 
                             <select name="a1[]" id="a1" class="form-control">
                            <?php do { ?>
                               <option value="<?php echo $row_criterios2['p']?>"<?php if (!(strcmp($row_criterios2['p'], $row_puestos['a1']))) {echo "selected=\"selected\"";} ?>><?php echo $row_criterios2['c']?></option>
                               <?php
                              } while ($row_criterios2 = mysql_fetch_assoc($criterios2));
                              $rows = mysql_num_rows($criterios2);
                              if($rows > 0) {
                                  mysql_data_seek($criterios2, 0);
                                  $row_criterios2 = mysql_fetch_assoc($criterios2);
                              } ?> 
                              </select>
                      		</td>

						 <?php if ( $row_criterios3['c'] != '') { ?>

                            <td>
                             <select name="a2[]" id="a2" class="form-control">
                            <?php do { ?>
                               <option value="<?php echo $row_criterios3['p']?>"<?php if (!(strcmp($row_criterios3['p'], $row_puestos['a2']))) {echo "selected=\"selected\"";} ?>><?php echo $row_criterios3['c']?></option>
                               <?php
                              } while ($row_criterios3 = mysql_fetch_assoc($criterios3));
                              $rows = mysql_num_rows($criterios3);
                              if($rows > 0) {
                                  mysql_data_seek($criterios3, 0);
                                  $row_criterios3 = mysql_fetch_assoc($criterios3);
                              } ?> 
                              </select>
                      		</td>

 						 <?php } else { ?>
                          <input name="a2[]" type="hidden" id="a2" value="<?php echo '0'; ?>">
 						 <?php }  ?>

						 <?php if ( $row_criterios4['c'] != '') { ?>

                            <td>	
                             <select name="a3[]" id="a3" class="form-control">
                            <?php do { ?>
                               <option value="<?php echo $row_criterios4['p']?>"<?php if (!(strcmp($row_criterios4['p'], $row_puestos['a3']))) {echo "selected=\"selected\"";} ?>><?php echo $row_criterios4['c']?></option>
                               <?php
                              } while ($row_criterios4 = mysql_fetch_assoc($criterios4));
                              $rows = mysql_num_rows($criterios4);
                              if($rows > 0) {
                                  mysql_data_seek($criterios4, 0);
                                  $row_criterios4 = mysql_fetch_assoc($criterios4);
                              } ?> 
                              </select>
                      		</td>

 						 <?php } else { ?>
                          <input name="a3[]" type="hidden" id="a3" value="<?php echo '0'; ?>">
 						 <?php }  ?>


						 <?php if ( $row_criterios5['c'] != '') { ?>

                            <td>
                             <select name="a4[]" id="a4" class="form-control">
                            <?php do { ?>
                               <option value="<?php echo $row_criterios5['p']?>"<?php if (!(strcmp($row_criterios5['p'], $row_puestos['a4']))) {echo "selected=\"selected\"";} ?>><?php echo $row_criterios5['c']?></option>
                               <?php
                              } while ($row_criterios5 = mysql_fetch_assoc($criterios5));
                              $rows = mysql_num_rows($criterios5);
                              if($rows > 0) {
                                  mysql_data_seek($criterios5, 0);
                                  $row_criterios5 = mysql_fetch_assoc($criterios5);
                              } ?> 
                              </select>
                      		</td>
                            
 						 <?php } else { ?>
                          <input name="a4[]" type="hidden" id="a4" value="<?php echo '0'; ?>">
 						 <?php }  ?>


                           </tr>
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>
					    </tbody>
				    </table>
						 <?php if ( $totalRows_puestos == $totalRows_cap_up ) { ?>
                    <input type="hidden" name="MM_update" value="form1">
									  <input type="hidden" name="el_puesto" value="<?php echo $el_puesto; ?>">
                    <input type="submit" class="btn btn-primary" value="Actualizar">
						 <?php } else { ?>
                    <input type="hidden" name="MM_insert" value="form1">
									  <input type="hidden" name="el_puesto" value="<?php echo $el_puesto; ?>">
                    <input type="submit" class="btn btn-primary" value="Capturar">
						 <?php } ?>
                    <button type="button" class="btn btn-default" onClick="window.location.href='productividad_captura_corpo.php'">Regresar</button>
	</form>
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


</body>
</html>