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
	
  $updateSQL = sprintf("UPDATE prod_captura SET IDempleado=%s, emp_paterno=%s,  emp_materno=%s,  emp_nombre=%s, denominacion=%s, sueldo_total=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, IDmatriz=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, a6=%s, a7=%s, a25=%s, a26=%s, a27=%s, a28=%s, capturador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s WHERE IDcaptura=%s",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['a1'], "text"),
                       GetSQLValueString($_POST['a2'], "text"),
                       GetSQLValueString($_POST['a3'], "text"),
                       GetSQLValueString($_POST['a4'], "text"),
                       GetSQLValueString($_POST['a5'], "text"),
                       GetSQLValueString($_POST['a6'], "text"),
                       GetSQLValueString($_POST['a7'], "text"),
                       GetSQLValueString($_POST['a25'], "text"),
                       GetSQLValueString($_POST['a26'], "text"),
                       GetSQLValueString($_POST['a27'], "text"),
                       GetSQLValueString($_POST['a28'], "text"),
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

  $updateGoTo = "productividad_captura_puesto_uptdate.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//insertar
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO prod_captura (IDempleado, emp_paterno, emp_materno, emp_nombre, denominacion, sueldo_total, IDpuesto, fecha_captura, semana, IDmatriz, a1, a2, a3, a4, a5, a6, a7, a25, a26, a27, a28, capturador, garantizado, adicional, observaciones, lun, mar, mie, jue, vie, sab, dom) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['fecha_captura'], "date"),
                       GetSQLValueString($_POST['semana'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['a1'], "text"),
                       GetSQLValueString($_POST['a2'], "text"),
                       GetSQLValueString($_POST['a3'], "text"),
                       GetSQLValueString($_POST['a4'], "text"),
                       GetSQLValueString($_POST['a5'], "text"),
                       GetSQLValueString($_POST['a6'], "text"),
                       GetSQLValueString($_POST['a7'], "text"),
                       GetSQLValueString($_POST['a25'], "text"),
                       GetSQLValueString($_POST['a26'], "text"),
                       GetSQLValueString($_POST['a27'], "text"),
                       GetSQLValueString($_POST['a28'], "text"),
                       GetSQLValueString($_POST['capturador'], "text"),
                       GetSQLValueString($_POST['garantizado'], "int"),
                       GetSQLValueString($_POST['adicional'], "int"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString(isset($_POST['lun']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mar']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['mie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['jue']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['vie']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['sab']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['dom']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "productividad_captura_puesto_uptdate.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


//filtrado por sucursal
if(isset($_SESSION['la_sucursal'])) { $la_sucursal = $_SESSION['la_sucursal']; } 

if($la_sucursal > 0) {
$s1 = " AND IDsucursal = '$la_sucursal'"; 
} else {
$s1 = " "; 
$la_sucursal = 0;
} 

if (isset($_GET['borrado']) && $_GET['borrado'] == 1) {$borrado = 1; } else { $borrado = 0;}

if (isset($_POST['buscado'])) {	
$arreglo = '';
$array = explode(" ", $_POST['buscado']);
$contar = substr_count($_POST['buscado'], ' ') + 1;
$i = 0;
while($contar > $i) {
$arreglo .= " IDempleado LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR emp_paterno LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR emp_materno LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR emp_nombre LIKE '%" . $array[$i] . "%'"; 
    $i++;
}}

// abre filtro por empleado
if (isset($_POST['buscado']) && $_POST['buscado'] != '' && $borrado == 0 ) {
$buscado = $_POST['buscado'];
$query_puestos = "SELECT * FROM prod_captura WHERE semana = '$semana' AND IDpuesto = '$el_puesto' AND IDmatriz = '$la_matriz' AND (" . $arreglo . ")";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$cantidad = 0;
$capturados = 0;
$_SESSION['cantidad'] = $cantidad;
$_SESSION['capturados'] = $capturados;

// else filtro por empleado
} elseif ($borrado == 1) { 

$cantidad = 20;
$capturados = 0;
$_SESSION['cantidad'] = $cantidad;
$_SESSION['capturados'] = $capturados;

	 if( $cantidad == 50 ){$var1 = " LIMIT 50 ";} 
else if( $cantidad == 10 ){$var1 = " LIMIT 10 ";}
else if( $cantidad == 5 ) {$var1 = " LIMIT 5 ";}
else if( $cantidad == 0 ) {$var1 = " ";}
					 else {$var1 = "LIMIT 20 ";}

if( $capturados == 0 ){$var2 = "AND capturador IS NULL ";} else {$var2 = "AND capturador IS NOT NULL ";}

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM prod_captura WHERE semana = '$semana' AND IDpuesto = '$el_puesto' AND IDmatriz = '$la_matriz' ". $var2 .  $s1 . $var1;
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

} else {

if (isset($_POST['cantidad'])) {$cantidad = $_POST['cantidad'];} 
elseif (isset($_SESSION['cantidad'])) {$cantidad = $_SESSION['cantidad'];} 
else {$cantidad = 20;}

if (isset($_POST['capturados'])) {$capturados = $_POST['capturados'];} 
elseif (isset($_SESSION['capturados'])) {$capturados = $_SESSION['capturados'];} 
else {$capturados = 0;}

$_SESSION['cantidad'] = $cantidad;
$_SESSION['capturados'] = $capturados;

	 if( $cantidad == 50 ){$var1 = " LIMIT 50 ";} 
else if( $cantidad == 10 ){$var1 = " LIMIT 10 ";}
else if( $cantidad == 5 ) {$var1 = " LIMIT 5 ";}
else if( $cantidad == 0 ) {$var1 = " ";}
					 else {$var1 = "LIMIT 20 ";}

if( $capturados == 0 ){$var2 = "AND capturador IS NULL ";} else {$var2 = "AND capturador IS NOT NULL ";}

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM prod_captura WHERE semana = '$semana' AND IDpuesto = '$el_puesto' AND IDmatriz = '$la_matriz' ". $var2 .  $s1 . $var1;
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

// cierre filtro por empleado
}

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDsucursal = '$la_sucursal'";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

$query_tipo_captura = "SELECT * FROM vac_puestos WHERE IDpuesto = $el_puesto";
$tipo_captura = mysql_query($query_tipo_captura, $vacantes) or die(mysql_error());
$row_tipo_captura = mysql_fetch_assoc($tipo_captura);
$prod_captura_tipo = $row_tipo_captura['prod_captura_tipo'];
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

	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_basic.js"></script>	
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
							<h5 class="panel-title">Captura de Productividad. </h5></br>
                            
                            
                        <!-- Basic alert -->
                        <?php if($prod_captura_tipo == 2) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							La productividad de éste puesto, se reporta desde Corporativo.
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
                    
                    
                    
                           <form method="POST" action="productividad_captura_puesto.php?IDpuesto=<?php echo $el_puesto; ?>">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Filtro</h6>
								</div>

								<div class="panel-body">
									<div class="content-group-xs" id="bullets">Selecciona las opciones de filtrado. Mientras menos registros se muestren a la vez, más rápido cargará la página. <strong>Por defecto se muestran hasta 20 registros y sin captura.</strong></div>

									<ul class="media-list">
										<li class="media">
											<div class="media-body">Empleados:
                             <select class="form-control" name="cantidad" id="cantidad">
                               <option value="5"<?php if (!(strcmp($cantidad, 5))) {echo "selected=\"selected\"";} ?>>5 registros</option>
                               <option value="10"<?php if (!(strcmp($cantidad, 10))) {echo "selected=\"selected\"";} ?>>10 registros</option>
                               <option value="20"<?php if (!(strcmp($cantidad, 20))) {echo "selected=\"selected\"";} ?>>20 registros</option>
                               <option value="50"<?php if (!(strcmp($cantidad, 50))) {echo "selected=\"selected\"";} ?>>50 registros</option>
                               <option value="0"<?php if (!(strcmp($cantidad, 0))) {echo "selected=\"selected\"";} ?>>Todos los registros</option>
                              </select>
											</div>
										</li>

										<li class="media">
											<div class="media-body">Tipo:
                             <select class="form-control" name="capturados" id="capturados">
                               <option value="1"<?php if (!(strcmp($capturados, 1))) {echo "selected=\"selected\"";} ?>>Capturados</option>
                               <option value="0"<?php if (!(strcmp($capturados, 0))) {echo "selected=\"selected\"";} ?>>Sin captura</option>
                              </select>
											</div>
										</li>

										<li class="media">
											<div class="media-body">Buscar:
										<input type="text" class="form-control" name="buscado" id="buscado" value="" placeholder="<?php 
										if (isset($_POST['buscado']) && $_POST['buscado'] != '') {echo $_POST['buscado']; } else {echo "Ingrese Nombre en Mayusculas o No. de Empleado para buscar"; } ?>">
											</div>
										</li>


										<li class="media">
											<div class="media-body">
                         	 <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
							<button type="button" class="btn btn-default" onClick="window.location.href='productividad_captura_puesto.php?borrado=1&IDpuesto=<?php echo $el_puesto; ?>'">Borrar Filtro</button>
                            				</div>
										</li>
									</ul>

								</div>
							</div>
						</form>

                    
                    
                    
					<table class="table table-condensed datatable-basic">
						<thead>
						  <tr class="bg-blue">
                          <th>Acciones</th>
                          <th>No. Emp.</th>
                          <th>Empleado</th>
                          <th>Sueldo Semanal</th>
                          <th>Calculado (%)</th>
                          <th>Pago ($)</th>
                          <th>Garantizado</th>
                          <th>Adicional (%)</th>
                          <th>Adicional ($)</th>
                          <th>Total ($)</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { ?>
                          <tr>
                          <td>
                           <?php if ($row_puestos['capturador'] == "") { ?>
                          <button type="button" data-target="#modal_form_inline<?php echo $row_puestos['IDempleado']; ?>"  data-toggle="modal" class="btn btn-success btn-icon"><i class="icon-arrow-right6"></i> Capturar</button>
						   <?php } else {  ?>  
                          <button type="button" data-target="#modal_form_inline<?php echo $row_puestos['IDempleado']; ?>"  data-toggle="modal" class="btn btn-primary btn-icon"><i class="icon-arrow-right6"></i> Actualizar</button>
                           <?php } ?>
                           </td>  
                            <td><?php echo $row_puestos['IDempleado']; ?></td>
                            <td><a href="prod_empleado_detalle.php?IDempleado=<?php echo $row_puestos['IDempleado']; ?>">
							<?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></a></td>
                            <td><?php echo "$" . number_format(($row_puestos['sueldo_total'] / 30) * 7); ?></td>
                            <td><?php if ($row_puestos['pago'] == 0) 	{ echo "-"; } else { echo $row_puestos['pago']. "%";} ?></td>
                            <td><?php if ($row_puestos['pago_total'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['pago_total']);} ?></td>
                            <td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo $row_puestos['adicional'] . "%";} ?></td>
                            <td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional2']);} ?></td>
                            <td><?php $total = $row_puestos['pago_total'] + $row_puestos['adicional2']; echo  number_format($total); ?></td>
                           </tr>
                            <?php // agregamos el modal especifico
                           		  $modal = "assets/modals/100.php";
								  require($modal); ?>

                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>

                         <?php } else { ?>
                         <td colspan="10">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
					    </tbody>
				    </table>
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
<?php
mysql_free_result($variables);

mysql_free_result($puestos);
?>
