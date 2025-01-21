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

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

if (!isset($_SESSION['la_empresa'])) {  $_SESSION['la_empresa'] =  $IDmatriz; } 
$la_empresa = $_SESSION['la_empresa'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_empleado = "SELECT exp_tipos.tipo, con_dependientes.nombre, con_empleados.IDempleado, con_empleados.a_paterno, con_empleados.a_materno, con_empleados.a_nombre, con_empleados.fecha_alta, con_empleados.a_rfc, vac_puestos.denominacion FROM con_empleados LEFT JOIN con_dependientes ON con_dependientes.IDempleado = con_empleados.IDempleado LEFT JOIN exp_tipos ON exp_tipos.IDTipo = con_dependientes.IDtipo LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = '$IDempleado'";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT * FROM sed_files_tipos";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

mysql_select_db($database_vacantes, $vacantes);
$query_files = "select nombre, coalesce(sum(case when IDtipo = 1 then 1 end), 0) as Doc1, coalesce(sum(case when IDtipo = 2 then 1 end), 0) as Doc2, coalesce(sum(case when IDtipo = 3 then 1 end), 0) as Doc3, coalesce(sum(case when IDtipo = 4 then 1 end), 0) as Doc4, coalesce(sum(case when IDtipo = 5 then 1 end), 0) as Doc5, coalesce(sum(case when IDtipo = 6 then 1 end), 0) as Doc6, coalesce(sum(case when IDtipo = 7 then 1 end), 0) as Doc7, coalesce(sum(case when IDtipo = 8 then 1 end), 0) as Doc8, coalesce(sum(case when IDtipo = 9 then 1 end), 0) as Doc9, coalesce(sum(case when IDtipo = 10 then 1 end), 0) as Doc10, coalesce(sum(case when IDtipo = 11 then 1 end), 0) as Doc11, coalesce(sum(case when IDtipo = 12 then 1 end), 0) as Doc12, coalesce(sum(case when IDtipo = 13 then 1 end), 0) as Doc13, coalesce(sum(case when IDtipo = 14 then 1 end), 0) as Doc14 from con_dependientes WHERE IDempleado = '$IDempleado'";
$files = mysql_query($query_files, $vacantes) or die(mysql_error());
$row_files = mysql_fetch_assoc($files);
$totalRows_files = mysql_num_rows($files);

mysql_select_db($database_vacantes, $vacantes);
$query_file1 = "SELECT * FROM con_dependientes WHERE IDtipo = 1 AND IDempleado = '$IDempleado'";
$file1 = mysql_query($query_file1, $vacantes) or die(mysql_error());
$row_file1 = mysql_fetch_assoc($file1);
$totalRows_file1 = mysql_num_rows($file1);

mysql_select_db($database_vacantes, $vacantes);
$query_file2 = "SELECT * FROM con_dependientes WHERE IDtipo = 2 AND IDempleado = '$IDempleado'";
$file2 = mysql_query($query_file2, $vacantes) or die(mysql_error());
$row_file2 = mysql_fetch_assoc($file2);
$totalRows_file2 = mysql_num_rows($file2);

mysql_select_db($database_vacantes, $vacantes);
$query_file3 = "SELECT * FROM con_dependientes WHERE IDtipo = 3 AND IDempleado = '$IDempleado'";
$file3 = mysql_query($query_file3, $vacantes) or die(mysql_error());
$row_file3 = mysql_fetch_assoc($file3);
$totalRows_file3 = mysql_num_rows($file3);

mysql_select_db($database_vacantes, $vacantes);
$query_file4 = "SELECT * FROM con_dependientes WHERE IDtipo = 4 AND IDempleado = '$IDempleado'";
$file4 = mysql_query($query_file4, $vacantes) or die(mysql_error());
$row_file4 = mysql_fetch_assoc($file4);
$totalRows_file4 = mysql_num_rows($file4);

mysql_select_db($database_vacantes, $vacantes);
$query_file5 = "SELECT * FROM con_dependientes WHERE IDtipo = 5 AND IDempleado = '$IDempleado'";
$file5 = mysql_query($query_file5, $vacantes) or die(mysql_error());
$row_file5 = mysql_fetch_assoc($file5);
$totalRows_file5 = mysql_num_rows($file5);

mysql_select_db($database_vacantes, $vacantes);
$query_file6 = "SELECT * FROM con_dependientes WHERE IDtipo = 6 AND IDempleado = '$IDempleado'";
$file6 = mysql_query($query_file6, $vacantes) or die(mysql_error());
$row_file6 = mysql_fetch_assoc($file6);
$totalRows_file6 = mysql_num_rows($file6);

mysql_select_db($database_vacantes, $vacantes);
$query_file7 = "SELECT * FROM con_dependientes WHERE IDtipo = 7 AND IDempleado = '$IDempleado'";
$file7 = mysql_query($query_file7, $vacantes) or die(mysql_error());
$row_file7 = mysql_fetch_assoc($file7);
$totalRows_file7 = mysql_num_rows($file7);

mysql_select_db($database_vacantes, $vacantes);
$query_file8 = "SELECT * FROM con_dependientes WHERE IDtipo = 8 AND IDempleado = '$IDempleado'";
$file8 = mysql_query($query_file8, $vacantes) or die(mysql_error());
$row_file8 = mysql_fetch_assoc($file8);
$totalRows_file8 = mysql_num_rows($file8);

mysql_select_db($database_vacantes, $vacantes);
$query_file9 = "SELECT * FROM con_dependientes WHERE IDtipo = 9 AND IDempleado = '$IDempleado'";
$file9 = mysql_query($query_file9, $vacantes) or die(mysql_error());
$row_file9 = mysql_fetch_assoc($file9);
$totalRows_file9 = mysql_num_rows($file9);

mysql_select_db($database_vacantes, $vacantes);
$query_file10 = "SELECT * FROM con_dependientes WHERE IDtipo = 10 AND IDempleado = '$IDempleado'";
$file10 = mysql_query($query_file10, $vacantes) or die(mysql_error());
$row_file10 = mysql_fetch_assoc($file10);
$totalRows_file10 = mysql_num_rows($file10);
$mira =  $row_file10['nombre'] ;

mysql_select_db($database_vacantes, $vacantes);
$query_file11 = "SELECT * FROM con_dependientes WHERE IDtipo = 11 AND IDempleado = '$IDempleado'";
$file11 = mysql_query($query_file11, $vacantes) or die(mysql_error());
$row_file11 = mysql_fetch_assoc($file11);
$totalRows_file11 = mysql_num_rows($file11);

mysql_select_db($database_vacantes, $vacantes);
$query_file12 = "SELECT * FROM con_dependientes WHERE IDtipo = 12 AND IDempleado = '$IDempleado'";
$file12 = mysql_query($query_file12, $vacantes) or die(mysql_error());
$row_file12 = mysql_fetch_assoc($file12);
$totalRows_file12 = mysql_num_rows($file12);

mysql_select_db($database_vacantes, $vacantes);
$query_file13 = "SELECT * FROM con_dependientes WHERE IDtipo = 13 AND IDempleado = '$IDempleado'";
$file13 = mysql_query($query_file13, $vacantes) or die(mysql_error());
$row_file13 = mysql_fetch_assoc($file13);
$totalRows_file13 = mysql_num_rows($file13);

mysql_select_db($database_vacantes, $vacantes);
$query_file14 = "SELECT * FROM con_dependientes WHERE IDtipo = 14 AND IDempleado = '$IDempleado'";
$file14 = mysql_query($query_file14, $vacantes) or die(mysql_error());
$row_file14 = mysql_fetch_assoc($file14);
$totalRows_file14 = mysql_num_rows($file14);

mysql_select_db($database_vacantes, $vacantes);
$query_beneficiarios = "SELECT * FROM con_dependientes WHERE emergencias IN (2,3) AND IDempleado = '$IDempleado'";
$beneficiarios = mysql_query($query_beneficiarios, $vacantes) or die(mysql_error());
$row_beneficiarios = mysql_fetch_assoc($beneficiarios);
$totalRows_beneficiarios = mysql_num_rows($beneficiarios);

mysql_select_db($database_vacantes, $vacantes);
$query_beneficiariosE = "SELECT * FROM con_dependientes WHERE emergencias IN (1,3) AND IDempleado = '$IDempleado'";
$beneficiariosE = mysql_query($query_beneficiariosE, $vacantes) or die(mysql_error());
$row_beneficiariosE = mysql_fetch_assoc($beneficiariosE);
$totalRows_beneficiariosE = mysql_num_rows($beneficiariosE);

mysql_select_db($database_vacantes, $vacantes);
$query_beneficiariosM = "SELECT SUM(observaciones) as Total FROM con_dependientes WHERE emergencias IN (1,3) AND IDempleado = '$IDempleado'";
$beneficiariosM = mysql_query($query_beneficiariosM, $vacantes) or die(mysql_error());
$row_beneficiariosM = mysql_fetch_assoc($beneficiariosM);
$totalRows_beneficiariosM = mysql_num_rows($beneficiariosM);
$monto = $row_beneficiariosM['Total'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$direccion = htmlentities($_POST['direccion'], ENT_COMPAT, '');
$nombre = htmlentities($_POST['nombre'], ENT_COMPAT, '');
//echo $_POST['fecha_nacimiento'];
$insertSQL = sprintf("INSERT INTO con_dependientes (IDempleado, nombre, IDtipo, emergencias, telefono, fecha_nacimiento, direccion, observaciones) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "text"),
                       GetSQLValueString($nombre, "text"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['emergencias'], "int"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['fecha_nacimiento'], "text"),
                       GetSQLValueString($direccion, "text"),
                       GetSQLValueString($_POST['observaciones'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

	header("Location: empleados_beneficiarios.php?IDempleado=$IDempleado&info=1"); 	
}

//borrado
if(isset($_GET['id'])) {
     $id = $_GET['id'];

    $query2 = "DELETE FROM con_dependientes WHERE id = '$id'"; 
    $result2 = mysql_query($query2) or die(mysql_error());  

	header("Location: empleados_beneficiarios.php?IDempleado=$IDempleado&info=3"); 	

	}
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
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>


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
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>

<script>
function showHint2(str) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint2").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "empleados_get_benficiarios.php?p=" + str, true);
        xmlhttp.send();
}
</script>
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?> onLoad="showHint2();">
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
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($monto != 100) { ?>
					    <div class="alert bg-warning-400 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Debes distribuir el 100% de la asignación a beneficiarios.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($totalRows_beneficiarios == 0 Or $totalRows_beneficiariosE == 0) { ?>
					    <div class="alert bg-warning-400 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El empleado debe contar con al menos un beneficiario y un contacto de Emergencia.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($totalRows_beneficiariosM > 4) { ?>
					    <div class="alert bg-danger-400 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El máximo de Beneficiarios es de Cinco.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Actualizar Empleado. Familiares y Beneficiarios</h5>
						</div>

					<div class="panel-body">
					<p>	Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
						Los beneficiarios y contactos de emergencia deben contar con teléfono, correo y dirección.</br>
						Si el beneficiario ya está en la base de datos, aparecerá el icono <i class="icon-warning text-danger"></i></p>
                  <p>&nbsp;</p>

					<h6>Datos del Empleado</h6>
                    <div>
                    
					<table class="table table-condensed">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>IDempleado</th>
                      <th>Nombre</th>
                      <th>Puesto</th>
                      <th>Fecha Ingreso</th>
                      <th>RFC</th>
               		 </tr>
                    </thead>
                    <tbody>
                          <tr>
                            <td><?php echo $row_empleado['IDempleado']; ?>&nbsp; </td>
                            <td><?php echo $row_empleado['a_paterno'] . " " . $row_empleado['a_materno'] . " " . $row_empleado['a_nombre']; ?>&nbsp; </td>
                            <td><?php echo $row_empleado['denominacion']; ?>&nbsp; </td>
                            <td><?php echo date('d/m/Y', strtotime( $row_empleado['fecha_alta'])); ?>&nbsp; </td>
                            <td><?php echo $row_empleado['a_rfc']; ?>&nbsp; </td>
                           </td>
                          </tr>
                     </tbody>
					</table>
                  <p>&nbsp;</p>
                  <p>&nbsp;</p>
					<h6>Agregar</h6>
                  
                  
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre Completo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre completo" value="" onKeyUp="this.value=this.value.toUpperCase()"  required>
                                            <span class="help-block">Paterno Materno Nombre.</span>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Parentesco:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo" id="IDtipo" class="form-control" required="required">
												<option value = "" >Seleccione una opción</option> 
												<option value = "1" >Esposo(a)</option> 
												<option value = "12">Concubino(a)</option> 
												<option value = "2" >Padre</option> 
												<option value = "3" >Madre</option> 
												<option value = "4" >Hijo(a)</option> 
												<option value = "5" >Abuelo(a)</option> 
												<option value = "6" >Nieto(a)</option> 
												<option value = "7" >Hermano(a)</option> 
												<option value = "8" >Tio(a)</option> 
												<option value = "9" >Sobrino(a)</option> 
												<option value = "10" >Suegro(a)</option> 
												<option value = "11" >Conocido(a)</option> 
												<option value = "12" >Concubino(a)</option> 
												<option value = "13" >Cuñado(a)</option> 
												<option value = "14" >Nuera o Yerno</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Contacto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="emergencias" id="emergencias" class="form-control" required="required" onchange="showHint2(this.value)">
												<option value = "" >Seleccione una opción</option> 
												<option value = "1" >Beneficiario de Seguro</option> 
												<option value = "2" >Contacto de Emergencias</option> 
												<option value = "3" >Beneficiario de Seguro y Contacto de Energencias</option> 
												<option value = "0" >Ninguno de los anteriores</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                    <span id="txtHint2">
									
									</span>


                          <div class="text-right">
                            <div>
								<input type="submit" name="submit" class="btn btn-primary" id="submit" value="Agregar" />
								<button type="button" onClick="window.location.href='empleados_consulta.php'" class="btn btn-default btn-icon">Regresar</button>
                    			<input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">
                    			<input type="hidden" name="estatus" value="1">
                       		    <input type="hidden" name="MM_insert" value="form1">
                            </div>
                          </div>

                       </fieldset>
                      </form>
	                    
                    
					<table class="table">
		          <thead>
                    <tr class="bg-success"> 
		              <th>Parentesco</th>
		              <th>Nombre Completo</th>
		              <th>Energencias</th>
		              <th>Beneficiario</th>
		              <th>Direccion</th>
		              <th>Teléfono</th>
		              <th>% de asignación</th>
		              <th>Acciones</th>
		            </tr>
		            </thead>
		          <tbody>
		        <tr>
                <?php if ($totalRows_file1 == 0) { ?>
                   <tr>
                     <td>Esposo(a)</td>
                     <td>No reportado</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                   </tr>
                <?php } else { ?> 
                 <?php  do {  
					$el_beneficiario = $row_file1['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido1 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario'";
					$beneficiariosRepetido1 = mysql_query($query_beneficiariosRepetido1, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido1 = mysql_fetch_assoc($beneficiariosRepetido1);
					$totalRows_beneficiariosRepetido1 = mysql_num_rows($beneficiariosRepetido1); 
				 ?>
		          <tr>
                     <td>Esposo(a)</td>
                    <td><?php echo $row_file1['nombre'];?><?php if ($totalRows_beneficiariosRepetido1 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file1['emergencias'] == 2  OR $row_file1['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file1['emergencias'] == 1  OR $row_file1['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file1['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file1['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file1['observaciones']; ?><?php if (($row_file1['emergencias'] == 1 OR $row_file1['emergencias'] == 3) AND ($row_file1['observaciones'] == 0 OR $row_file1['direccion'] == '' OR $row_file1['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file1['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file1['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file1['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file1 = mysql_fetch_assoc($file1)); ?>
                <?php }?>



                <?php if ($totalRows_file2 == 0) { ?>
                   <tr>
                     <td>Padre</td>
                     <td>No reportado</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                   </tr>
                <?php } else { ?> 
                 <?php  do {  
					$el_beneficiario2 = $row_file2['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido2 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario2'";
					$beneficiariosRepetido2 = mysql_query($query_beneficiariosRepetido2, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido2 = mysql_fetch_assoc($beneficiariosRepetido2);
					$totalRows_beneficiariosRepetido2 = mysql_num_rows($beneficiariosRepetido2); 
				 ?>
		          <tr>
                     <td>Padre</td>
                    <td><?php echo $row_file2['nombre'];?><?php if ($totalRows_beneficiariosRepetido2 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file2['emergencias'] == 2  OR $row_file2['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file2['emergencias'] == 1  OR $row_file2['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file2['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file2['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file2['observaciones']; ?><?php if (($row_file2['emergencias'] == 1 OR $row_file2['emergencias'] == 3) AND ($row_file2['observaciones'] == 0 OR $row_file2['direccion'] == '' OR $row_file2['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file2['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file2['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file2['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file2 = mysql_fetch_assoc($file2)); ?>
                <?php }?>

                <?php if ($totalRows_file14 == 0) { ?>
                   <tr>
                     <td>Padre</td>
                     <td>No reportado</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                   </tr>
                <?php } else { ?> 
                 <?php  do {  
					$el_beneficiario14 = $row_file14['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido14 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario14'";
					$beneficiariosRepetido14 = mysql_query($query_beneficiariosRepetido14, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido14 = mysql_fetch_assoc($beneficiariosRepetido14);
					$totalRows_beneficiariosRepetido14 = mysql_num_rows($beneficiariosRepetido14); 
				 ?>
		          <tr>
                     <td>Nuera o Yerno</td>
                    <td><?php echo $row_file14['nombre'];?><?php if ($totalRows_beneficiariosRepetido14 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file14['emergencias'] == 14  OR $row_file14['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file14['emergencias'] == 1  OR $row_file14['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file14['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file14['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file14['observaciones']; ?><?php if (($row_file14['emergencias'] == 1 OR $row_file14['emergencias'] == 3) AND ($row_file14['observaciones'] == 0 OR $row_file14['direccion'] == '' OR $row_file14['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file14['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file14['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file14['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file14 = mysql_fetch_assoc($file14)); ?>
                <?php }?>


                <?php if ($totalRows_file3 == 0) { ?>
                   <tr>
                     <td>Madre</td>
                     <td>No reportado</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                   </tr>
                <?php } else { ?> 
                 <?php  do {  
					$el_beneficiario3 = $row_file3['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido3 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario3'";
					$beneficiariosRepetido3 = mysql_query($query_beneficiariosRepetido3, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido3 = mysql_fetch_assoc($beneficiariosRepetido3);
					$totalRows_beneficiariosRepetido3 = mysql_num_rows($beneficiariosRepetido3); 
				 ?>
		          <tr>
                     <td>Madre</td>
                    <td><?php echo $row_file3['nombre'];?><?php if ($totalRows_beneficiariosRepetido3 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file3['emergencias'] == 2  OR $row_file3['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file3['emergencias'] == 1  OR $row_file3['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file3['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file3['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file3['observaciones']; ?><?php if (($row_file3['emergencias'] == 1 OR $row_file3['emergencias'] == 3) AND ($row_file3['observaciones'] == 0 OR $row_file3['direccion'] == '' OR $row_file3['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file3['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file3['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file3['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file3 = mysql_fetch_assoc($file3)); ?>
                <?php }?>


                <?php if ($totalRows_file4 == 0) { ?>
                   <tr>
                     <td>Hijo(a)</td>
                     <td>No reportado</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                     <td>-</td>
                   </tr>
                <?php } else { ?> 
                 <?php  do {  
					$el_beneficiario4 = $row_file4['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido4 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario4'";
					$beneficiariosRepetido4 = mysql_query($query_beneficiariosRepetido4, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido4 = mysql_fetch_assoc($beneficiariosRepetido4);
					$totalRows_beneficiariosRepetido4 = mysql_num_rows($beneficiariosRepetido4); 
				 ?>
		          <tr>
                     <td>Hijo(a)</td>
                    <td><?php echo $row_file4['nombre'];?><?php if ($totalRows_beneficiariosRepetido4 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file4['emergencias'] == 2  OR $row_file4['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file4['emergencias'] == 1  OR $row_file4['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file4['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file4['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file4['observaciones']; ?><?php if (($row_file4['emergencias'] == 1 OR $row_file4['emergencias'] == 3) AND ($row_file4['observaciones'] == 0 OR $row_file4['direccion'] == '' OR $row_file4['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file4['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file4['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file4['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file4 = mysql_fetch_assoc($file4)); ?>
                <?php }?>


                <?php if ($totalRows_file5 != 0) { ?> 
                 <?php  do {  
					$el_beneficiario5 = $row_file5['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido5 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario5'";
					$beneficiariosRepetido5 = mysql_query($query_beneficiariosRepetido5, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido5 = mysql_fetch_assoc($beneficiariosRepetido5);
					$totalRows_beneficiariosRepetido5 = mysql_num_rows($beneficiariosRepetido5); 
				 ?>
		          <tr>
                     <td>Abuelo(a)</td>
                    <td><?php echo $row_file5['nombre'];?><?php if ($totalRows_beneficiariosRepetido5 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file5['emergencias'] == 2  OR $row_file5['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file5['emergencias'] == 1  OR $row_file5['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file5['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file5['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file5['observaciones']; ?><?php if (($row_file5['emergencias'] == 1 OR $row_file5['emergencias'] == 3) AND ($row_file5['observaciones'] == 0 OR $row_file5['direccion'] == '' OR $row_file5['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file5['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file5['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file5['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file5 = mysql_fetch_assoc($file5)); ?>
                <?php }?>


                <?php if ($totalRows_file6 != 0) { ?> 
                 <?php  do {  
					$el_beneficiario6 = $row_file6['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido6 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario6'";
					$beneficiariosRepetido6 = mysql_query($query_beneficiariosRepetido6, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido6 = mysql_fetch_assoc($beneficiariosRepetido6);
					$totalRows_beneficiariosRepetido6 = mysql_num_rows($beneficiariosRepetido6); 
				 ?>
		          <tr>
                     <td>Nieto(a)</td>
                    <td><?php echo $row_file6['nombre'];?><?php if ($totalRows_beneficiariosRepetido6 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file6['emergencias'] == 2  OR $row_file6['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file6['emergencias'] == 1  OR $row_file6['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file6['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file6['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file6['observaciones']; ?><?php if (($row_file6['emergencias'] == 1 OR $row_file6['emergencias'] == 3) AND ($row_file6['observaciones'] == 0 OR $row_file6['direccion'] == '' OR $row_file6['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file6['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file6['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file6['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file6 = mysql_fetch_assoc($file6)); ?>
                <?php }?>


                <?php if ($totalRows_file7 != 0) { ?> 
                 <?php  do {  
					$el_beneficiario7 = $row_file7['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido7 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario7'";
					$beneficiariosRepetido7 = mysql_query($query_beneficiariosRepetido7, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido7 = mysql_fetch_assoc($beneficiariosRepetido7);
					$totalRows_beneficiariosRepetido7 = mysql_num_rows($beneficiariosRepetido7); 
				 ?>
		          <tr>
                     <td>Hermano(a)</td>
                    <td><?php echo $row_file7['nombre'];?><?php if ($totalRows_beneficiariosRepetido7 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file7['emergencias'] == 2  OR $row_file7['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file7['emergencias'] == 1  OR $row_file7['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file7['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file7['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file7['observaciones']; ?><?php if (($row_file7['emergencias'] == 1 OR $row_file7['emergencias'] == 3) AND ($row_file7['observaciones'] == 0 OR $row_file7['direccion'] == '' OR $row_file7['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file7['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file7['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file7['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
		          </tr>
                 <?php } while ($row_file7 = mysql_fetch_assoc($file7)); ?>
                <?php }?>


                <?php if ($totalRows_file8 != 0) { ?> 
                 <?php  do {  
					$el_beneficiario8 = $row_file8['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido8 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario8'";
					$beneficiariosRepetido8 = mysql_query($query_beneficiariosRepetido8, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido8 = mysql_fetch_assoc($beneficiariosRepetido8);
					$totalRows_beneficiariosRepetido8 = mysql_num_rows($beneficiariosRepetido8); 
				 ?>
		          <tr>
                     <td>Tio(a)</td>
                    <td><?php echo $row_file8['nombre'];?><?php if ($totalRows_beneficiariosRepetido8 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file8['emergencias'] == 2  OR $row_file8['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file8['emergencias'] == 1  OR $row_file8['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file8['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file8['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file8['observaciones']; ?><?php if (($row_file8['emergencias'] == 1 OR $row_file8['emergencias'] == 3) AND ($row_file8['observaciones'] == 0 OR $row_file8['direccion'] == '' OR $row_file8['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file8['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file8['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file8['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php  } while ($row_file8 = mysql_fetch_assoc($file8)); ?>
                <?php }?>


                <?php if ($totalRows_file9 != 0) { ?> 
                 <?php  do {  
					$el_beneficiario9 = $row_file9['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido9 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario9'";
					$beneficiariosRepetido9 = mysql_query($query_beneficiariosRepetido9, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido9 = mysql_fetch_assoc($beneficiariosRepetido9);
					$totalRows_beneficiariosRepetido9 = mysql_num_rows($beneficiariosRepetido9); 
				 ?>
		          <tr>
                     <td>Sobirno(a)</td>
                    <td><?php echo $row_file9['nombre'];?><?php if ($totalRows_beneficiariosRepetido9 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file9['emergencias'] == 2  OR $row_file9['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file9['emergencias'] == 1  OR $row_file9['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file9['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file9['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file9['observaciones']; ?><?php if (($row_file9['emergencias'] == 1 OR $row_file9['emergencias'] == 3) AND ($row_file9['observaciones'] == 0 OR $row_file9['direccion'] == '' OR $row_file9['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file9['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file9['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file9['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file9 = mysql_fetch_assoc($file9)); ?>
                <?php }?>


                <?php if ($totalRows_file10 != 0)  { ?> 
                 <?php  do {  
					$el_beneficiario10 = $row_file10['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido10 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario10'";
					$beneficiariosRepetido10 = mysql_query($query_beneficiariosRepetido10, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido10 = mysql_fetch_assoc($beneficiariosRepetido10);
					$totalRows_beneficiariosRepetido10 = mysql_num_rows($beneficiariosRepetido10); 
				 ?>
		          <tr>
                     <td>Suegro(a)</td>
                    <td><?php echo $row_file10['nombre'];?><?php if ($totalRows_beneficiariosRepetido10 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file10['emergencias'] == 2  OR $row_file10['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file10['emergencias'] == 1  OR $row_file10['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file10['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file10['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file10['observaciones']; ?><?php if (($row_file10['emergencias'] == 1 OR $row_file10['emergencias'] == 3) AND ($row_file10['observaciones'] == 0 OR $row_file10['direccion'] == '' OR $row_file10['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file10['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file10['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file10['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                    
		          </tr>
                 <?php } while ($row_file10 = mysql_fetch_assoc($file10)); ?>
                <?php }?>


                <?php if ($totalRows_file11 != 0) {?> 
                 <?php  do {  
					$el_beneficiario11 = $row_file11['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido11 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario'";
					$beneficiariosRepetido11 = mysql_query($query_beneficiariosRepetido11, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido11 = mysql_fetch_assoc($beneficiariosRepetido11);
					$totalRows_beneficiariosRepetido11 = mysql_num_rows($beneficiariosRepetido11); 
				 ?>
		          <tr>
                     <td>Otro (sin parentezco familiar)</td>
                    <td><?php echo $row_file11['nombre'];?><?php if ($totalRows_beneficiariosRepetido11 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file11['emergencias'] == 2  OR $row_file11['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file11['emergencias'] == 1  OR $row_file11['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file11['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file11['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file11['observaciones']; ?><?php if (($row_file11['emergencias'] == 1 OR $row_file11['emergencias'] == 3) AND ($row_file11['observaciones'] == 0 OR $row_file11['direccion'] == '' OR $row_file11['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file11['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file11['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file11['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


                    
		          </tr>
                 <?php } while ($row_file11 = mysql_fetch_assoc($file11)); ?>
                <?php }?>


                <?php if ($totalRows_file12 != 0) {?> 
                 <?php  do {  
					$el_beneficiario12 = $row_file12['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido12 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario'";
					$beneficiariosRepetido12 = mysql_query($query_beneficiariosRepetido12, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido12 = mysql_fetch_assoc($beneficiariosRepetido12);
					$totalRows_beneficiariosRepetido12 = mysql_num_rows($beneficiariosRepetido12); 
				 ?>
		          <tr>
                     <td>Concubino(a)</td>
                    <td><?php echo $row_file12['nombre'];?><?php if ($totalRows_beneficiariosRepetido12 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file12['emergencias'] == 2  OR $row_file12['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file12['emergencias'] == 1  OR $row_file12['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file12['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file12['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file12['observaciones']; ?><?php if (($row_file12['emergencias'] == 1 OR $row_file12['emergencias'] == 3) AND ($row_file12['observaciones'] == 0 OR $row_file12['direccion'] == '' OR $row_file12['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file12['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file12['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file12['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


                    
		          </tr>
                 <?php } while ($row_file12 = mysql_fetch_assoc($file12)); ?>
                <?php }?>


                <?php if ($totalRows_file13 != 0) {?> 
                 <?php  do {  
					$el_beneficiario13 = $row_file13['nombre'];
					mysql_select_db($database_vacantes, $vacantes);
					$query_beneficiariosRepetido13 = "SELECT * FROM con_dependientes WHERE nombre = '$el_beneficiario'";
					$beneficiariosRepetido13 = mysql_query($query_beneficiariosRepetido13, $vacantes) or die(mysql_error());
					$row_beneficiariosRepetido13 = mysql_fetch_assoc($beneficiariosRepetido13);
					$totalRows_beneficiariosRepetido13 = mysql_num_rows($beneficiariosRepetido13); 
				 ?>
		          <tr>
                     <td>Cuñado (a)</td>
                    <td><?php echo $row_file13['nombre'];?><?php if ($totalRows_beneficiariosRepetido13 > 1 ){ ?> <i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><?php if ($row_file13['emergencias'] == 2  OR $row_file13['emergencias'] == 3) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file13['emergencias'] == 1  OR $row_file13['emergencias'] == 3 ) {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file13['direccion'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php if ($row_file13['telefono'] != '') {echo "Si";} else {echo "No";}?></td>
                    <td><?php echo $row_file13['observaciones']; ?><?php if (($row_file13['emergencias'] == 1 OR $row_file13['emergencias'] == 3) AND ($row_file13['observaciones'] == 0 OR $row_file13['direccion'] == '' OR $row_file13['telefono'] == '')) { ?><i class="icon-warning text-danger"></i><?php } ?></td>
                    <td><button type="button" data-target="#modal_theme_danger<?php echo $row_file13['id']; ?>"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button></td>
                    
                                      <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_file13['id']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="empleados_beneficiarios.php?IDempleado=<?php echo $IDempleado; ?>&id=<?php echo $row_file13['id']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


                    
		          </tr>
                 <?php } while ($row_file13 = mysql_fetch_assoc($file13)); ?>
                <?php }?>
                
                  </tbody>


		          <tfoot>
                     <td colspan="6"></td>
                     <td><strong>Total: <?php echo $monto;?>%</strong></td>
                     <td></td>
		          </tfoot>

		          </table>                    

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
