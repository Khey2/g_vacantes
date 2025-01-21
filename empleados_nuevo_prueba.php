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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$IDempleado = $_POST['IDempleado'];
$a_correo = $_POST['a_correo'];

mysql_select_db($database_vacantes, $vacantes);
$query_repetidos = "SELECT * FROM con_empleados WHERE IDempleado = '$IDempleado' or a_correo = '$a_correo'";
$repetidos = mysql_query($query_repetidos, $vacantes) or die(mysql_error());
$row_repetidos = mysql_fetch_assoc($repetidos);
$totalRows_repetidos = mysql_num_rows($repetidos);
if($totalRows_repetidos > 0) { 	header("Location: empleados_consulta.php?info=4"); } else {

$y1 = substr( $_POST['c_fecha_nacimiento'], 8, 2 );
$m1 = substr( $_POST['c_fecha_nacimiento'], 3, 2 );
$d1 = substr( $_POST['c_fecha_nacimiento'], 0, 2 );
if ($y1 < 10) {$c_fecha_nacimiento = "20".$y1."-".$m1."-".$d1;} else {$c_fecha_nacimiento = "19".$y1."-".$m1."-".$d1;}

$y2 = substr( $_POST['fecha_alta'], 8, 2 );
$m2 = substr( $_POST['fecha_alta'], 3, 2 );
$d2 = substr( $_POST['fecha_alta'], 0, 2 );
if ($y2 < 50) {$fecha_alta = "20".$y2."-".$m2."-".$d2;} else {$fecha_alta = "19".$y2."-".$m2."-".$d2;}

$d_colonia = htmlentities($_POST['d_colonia'], ENT_COMPAT, '');
$d_delegacion_municipio = htmlentities($_POST['d_delegacion_municipio'], ENT_COMPAT, '');
$d_estado = htmlentities($_POST['d_estado'], ENT_COMPAT, '');
$d_calle = htmlentities($_POST['d_calle'], ENT_COMPAT, '');
if (isset($_POST['cancion_1'])) {$d_colonia = htmlentities($_POST['cancion_1'], ENT_COMPAT, '');} else {$d_colonia = htmlentities($_POST['d_colonia'], ENT_COMPAT, '');}

$insertSQL = sprintf("INSERT INTO con_empleados (IDempleado, a_paterno, a_materno, a_nombre, a_rfc, a_correo, a_curp, a_sexo, a_imss, IDnacionalidad, a_estado_civil, fecha_alta, c_fecha_nacimiento, d_calle, d_numero_calle, d_colonia, d_delegacion_municipio, d_estado, estatus, IDmatriz, password, IDcuenta, IDsubcuenta, d_codigo_postal ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "text"),
                       GetSQLValueString($_POST['a_paterno'], "text"),
                       GetSQLValueString($_POST['a_materno'], "text"),
                       GetSQLValueString($_POST['a_nombre'], "text"),
                       GetSQLValueString($_POST['a_rfc'], "text"),
                       GetSQLValueString($_POST['a_correo'], "text"),
                       GetSQLValueString($_POST['a_curp'], "text"),
                       GetSQLValueString($_POST['a_sexo'], "int"),
                       GetSQLValueString($_POST['a_imss'], "int"),
                       GetSQLValueString($_POST['IDnacionalidad'], "int"),
                       GetSQLValueString($_POST['a_estado_civil'], "int"),
                       GetSQLValueString($fecha_alta, "text"),
                       GetSQLValueString($c_fecha_nacimiento, "text"),
                       GetSQLValueString($d_calle, "text"),
                       GetSQLValueString($_POST['d_numero_calle'], "text"),
                       GetSQLValueString($d_colonia, "text"),
                       GetSQLValueString($d_delegacion_municipio, "text"),
                       GetSQLValueString($d_estado, "text"),
                       GetSQLValueString($_POST['estatus'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "text"),
                       GetSQLValueString($_POST['IDempleado'], "text"),
                       GetSQLValueString($_POST['IDcuenta'], "int"),
                       GetSQLValueString($_POST['IDsubcuenta'], "int"),
                       GetSQLValueString($_POST['d_codigo_postal'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "empleados_consulta.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  // header(sprintf("Location: %s", $insertGoTo));
}
}

if (isset($_GET["IDempleado"])) {
$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT * FROM con_empleados WHERE IDempleado = '$IDempleado'";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
}

if (isset($_GET["IDempleado"])) {
$_SESSION['IDempleado'] = $_GET['IDempleado'];
}else{
$_SESSION['IDempleado'] = 0;
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

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

if (!isset($_SESSION['la_empresa'])) {  $_SESSION['la_empresa'] =  $IDmatriz; } 
$la_empresa = $_SESSION['la_empresa'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_empresas = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$empresas = mysql_query($query_empresas, $vacantes) or die(mysql_error());
$row_empresas = mysql_fetch_assoc($empresas);
$totalRows_empresas = mysql_num_rows($empresas);

mysql_select_db($database_vacantes, $vacantes);
$query_bancos = "SELECT * FROM con_bancos ORDER BY con_bancos.banco ASC";
$bancos = mysql_query($query_bancos, $vacantes) or die(mysql_error());
$row_bancos = mysql_fetch_assoc($bancos);
$totalRows_bancos = mysql_num_rows($bancos);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

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
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
    <script>
	function showUser(str) {
	  if (str == 0) {
	  } else {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		  if (this.readyState == 4 && this.status == 200) {
			document.getElementById("txtHint").innerHTML = this.responseText;
		  }
		};
		xmlhttp.open("GET","get_user.php?q="+str,true);
		xmlhttp.send();
	  }
	}
	</script>
<script>
function addCancion(){
        var div = document.createElement('div');
        div.setAttribute('class', 'form-group');
            div.innerHTML = '<input class="form-control" name="cancion_1" id="cancion_1" type="text" value="" placeholder="Colonia -Captura manual"/>';
            document.getElementById('canciones').appendChild(div);document.getElementById('canciones').appendChild(div);}
</script>
<script>
function addCancion2(){
        var div = document.createElement('div');
        div.setAttribute('class', 'form-group');
            div.innerHTML = '<input class="form-control" name="cancion_2" id="cancion_2" type="text" value="" placeholder="Alcandia o Municipio - Captura manual"/>';
            document.getElementById('canciones2').appendChild(div);document.getElementById('canciones2').appendChild(div);}
</script>
<script>
function addCancion3(){
        var div = document.createElement('div');
        div.setAttribute('class', 'form-group');
            div.innerHTML = '<input class="form-control" name="cancion_3" id="cancion_3" type="text" value="" placeholder="Estado - Captura manual"/>';
            document.getElementById('canciones3').appendChild(div);document.getElementById('canciones3').appendChild(div);}
</script>
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?> onLoad="showUser()">
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


	                <!-- Content area -->
				<div class="content">
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el contrato.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Agregar Empleado</h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
					<p>Si no aparece la Colonia en el menú de selección, puedes realizar la <a href="empleados_nuevo_manual.php">Captura Manual</a> (se tendrán que capturar nuevamente todos los campos.)</p>
                    <p>&nbsp;</p>
                    <div>
                    <div>
                    
                    
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">IDEmpleado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="IDempleado" id="IDempleado"  onKeyUp="this.value=this.value.toUpperCase()" class="form-control" placeholder="ID del Empleado" value="" required>
                                            <span class="help-block">AAAAA9999</span>
										</div>
									</div>
									<!-- /basic text input -->

                                    

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_paterno" id="a_paterno" class="form-control" placeholder="Apellido Paterno" value="" required>
										</div>
									</div>
									<!-- /basic text input -->
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Materno:</label>
										<div class="col-lg-9">
											<input type="text" name="a_materno" id="a_materno" class="form-control" placeholder="Apellido Materno" value="">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre(s):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_nombre" id="a_nombre" class="form-control" placeholder="Nombres" value="" required>
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input name="a_correo" type="email" class="form-control" id="a_correo" value="" placeholder="correo" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">RFC:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input name="a_rfc" type="text" class="form-control" id="a_rfc" onKeyUp="this.value=this.value.toUpperCase()" value="" maxlength="13" placeholder="RFC a 13 posiciones" required="required">
										</div>
									</div>
									<!-- /basic text input -->
 
                                       <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">CURP:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input name="a_curp" type="text" class="form-control" id="a_curp" onKeyUp="this.value=this.value.toUpperCase()" value="" maxlength="20" placeholder="CURP" required="required">
										</div>
									</div>

                      
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sexo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_sexo" id="a_sexo" class="form-control" required="required">
												<option value = "" >Seleccione una opción</option> 
												<option value = "1" >Hombre</option> 
												<option value = "2" >Mujer</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Alta:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_alta" id="fecha_alta" value="" required>
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Nacimiento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="c_fecha_nacimiento" id="c_fecha_nacimiento" value="" required>
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cedula IMSS:</label>
										<div class="col-lg-9">
											<input type="number" name="a_imss" id="a_imss" class="form-control" placeholder="Cedula IMSS a 11 posiciones" value="" maxlength="11" required>
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nacionalidad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDnacionalidad" id="IDnacionalidad" class="form-control" required="required">
												<option value = "" >Seleccione una opción</option> 
												<option value = "1" >Mexicana</option> 
												<option value = "2" >Extranjera</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->
                         
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estado Civil:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_estado_civil" id="a_estado_civil" class="form-control" required="required">
												<option value = "" >Seleccione una opción</option> 
												<option value = "1" >Soltero</option> 
												<option value = "2" >Casado</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Calle:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_calle" id="d_calle" class="form-control" placeholder="Calle" value="" required>
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Número:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_numero_calle" id="d_numero_calle" class="form-control" placeholder="Numero" value="" required>
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Código Postal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" id="d_codigo_postal" name="d_codigo_postal" onKeyUp="showUser(this.value)" class="form-control" placeholder="Codigo Postal" value=""  maxlength="5" required>
										</div>
									</div>
									<!-- /basic text input -->

									<div id="txtHint"></div>

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Registro Patronal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_empresas['IDmatriz']?>"><?php echo $row_empresas['matriz']?></option>
												  <?php
												 } while ($row_empresas = mysql_fetch_assoc($empresas));
												   $rows = mysql_num_rows($empresas);
												   if($rows > 0) {
												   mysql_data_seek($estado, 0);
												   $row_empresas = mysql_fetch_assoc($empresas);
												 } ?>
											</select>
										</div>
									</div>


                          <div class="text-right">
                            <div>
                                <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Capturar" />
                    			<input type="hidden" name="fecha_cambio" value="<?php echo date("Y-m-d"); ?>">
                    			<input type="hidden" name="estatus" value="1">
                       		    <input type="hidden" name="MM_insert" value="form1">
                    			<input type="hidden" name="IDcuenta" value="0">
                    			<input type="hidden" name="IDsubcuenta" value="0">
                            </div>
                          </div>

                       </fieldset>
                      </form>



                      <p>&nbsp;</p>
                    </div>
                    </div>
                    </div>
				  </div>


<!-- Footer -->
					<div class="footer text-muted">
	&copy; 2020. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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
?>
