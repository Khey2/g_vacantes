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
$el_puesto = $_GET['IDpuesto'];
$_menu = basename($_SERVER['PHP_SELF'])."?IDpuesto=".$el_puesto;
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

$la_matriz = $_GET['IDmatriz'];
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
if ($_POST['a29'] == ''){$_a29 = 0;} else {$_a29 = $_POST['a29'];}
if ($_POST['horas_extra'] == ''){$horas_extra = 0;} else {$horas_extra = $_POST['horas_extra'];}
if ($_POST['adicional'] == ''){$adicional = 0;} else {$adicional = $_POST['adicional'];}

$updateSQL = sprintf("UPDATE prod_captura SET IDempleado=%s, emp_paterno=%s,  emp_materno=%s,  emp_nombre=%s, denominacion=%s, sueldo_total=%s, IDpuesto=%s, fecha_captura=%s, semana=%s, IDmatriz=%s, IDsucursal=%s, IDturno=%s, IDarea=%s, a1=%s, a2=%s, a3=%s, a4=%s, a5=%s, a6=%s, a7=%s, a8=%s, a9=%s, a10=%s, a11=%s, a12=%s, a13=%s, a14=%s, a15=%s, a16=%s, a17=%s, a18=%s, a19=%s, a20=%s, a21=%s, a22=%s, a23=%s, a24=%s, a25=%s, a26=%s, a27=%s, a28=%s, a29=%s, horas_extra=%s, autorizador=%s, garantizado=%s, adicional=%s, observaciones=%s, lun=%s, mar=%s, mie=%s, jue=%s, vie=%s, sab=%s, dom=%s, lun_g=%s, mar_g=%s, mie_g=%s, jue_g=%s, vie_g=%s, sab_g=%s WHERE IDcaptura='$captura'",
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
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDturno'], "int"),
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
                       GetSQLValueString($_a29, "text"),
                       GetSQLValueString($horas_extra, "int"),
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

  $updateGoTo = "productividad_autoriza_puesto_uptdate_a_t.php?IDcaptura=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


//filtrado por sucursal
if(isset($_POST['IDturno']) && ($_POST["MM_update"] == "form3")) { $_SESSION['IDturno'] = $_POST['IDturno']; } 
if(!isset($_SESSION['IDturno'])) {$_SESSION['IDturno'] = 0;}
$IDturno = $_SESSION['IDturno'];

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT prod_captura.horas_extra,  prod_captura.horas_extra_monto, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.sueldo_diario, prod_activos.sueldo_total, prod_activos.sueldo_total_productividad, prod_activos.rfc, prod_activos.IDpuesto, prod_activos.IDsucursal, prod_activos.IDturno, prod_activos.IDarea, prod_activos.IDmatriz, prod_activos.IDempleado,  prod_activos.descripcion_nomina, prod_captura.IDcaptura, prod_captura.pago, prod_captura.pago_total, prod_captura.garantizado, prod_captura.bono_asistencia, prod_captura.lun, prod_captura.mar, prod_captura.mie, prod_captura.jue, prod_captura.vie, prod_captura.sab, prod_captura.dom, prod_captura.adicional, prod_captura.adicional2, prod_captura.adicional3, prod_captura.semana, prod_captura.validador, prod_captura.capturador, prod_captura.autorizador, prod_captura.observaciones, prod_captura.fecha_captura, prod_captura.reci, prod_captura.carg, prod_captura.esti, prod_captura.dist FROM prod_activos LEFT JOIN prod_captura ON prod_captura.IDempleado = prod_activos.IDempleado AND prod_captura.semana = '$semana' AND prod_captura.anio = '$anio' WHERE prod_activos.IDpuesto = '$el_puesto' AND prod_activos.IDmatriz = '$la_matriz'  AND prod_activos.IDturno = '$IDturno'";
mysql_query("SET NAMES 'utf8'"); 
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_turno = "SELECT * FROM vac_turnos_t";
$turno = mysql_query($query_turno, $vacantes) or die(mysql_error());
$row_turno = mysql_fetch_assoc($turno);
$totalRows_turno = mysql_num_rows($turno);

$query_tipo_captura = "SELECT * FROM vac_puestos WHERE IDpuesto = $el_puesto";
$tipo_captura = mysql_query($query_tipo_captura, $vacantes) or die(mysql_error());
$row_tipo_captura = mysql_fetch_assoc($tipo_captura);
$prod_captura_tipo = $row_tipo_captura['prod_captura_tipo'];

$query_faltas = "SELECT * FROM prod_garantias WHERE IDmatriz = '$la_matriz' AND IDpuesto = '$el_puesto'";
$faltas = mysql_query($query_faltas, $vacantes) or die(mysql_error());
$row_faltas = mysql_fetch_assoc($faltas);
$Faltas = $row_faltas['asistencia'];

$query_monto_bono = "SELECT * FROM prod_garantias WHERE IDpuesto = '$el_puesto' AND IDmatriz = '$la_matriz'";
$monto_bono = mysql_query($query_monto_bono, $vacantes) or die(mysql_error());
$row_monto_bono = mysql_fetch_assoc($monto_bono);

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {

  $IDempleado = $_POST["IDempleado"];
  
  $updateSQL = sprintf("UPDATE prod_activos SET IDturno=%s WHERE IDempleado=%s", GetSQLValueString($_POST['IDturno'], "int"), GetSQLValueString($_POST['IDempleado'], "int"));
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  
  mysql_select_db($database_vacantes, $vacantes);
  $query_turnoj = "SELECT * FROM prod_activosj WHERE IDempleado = '$IDempleado'";
  $turnoj = mysql_query($query_turnoj, $vacantes) or die(mysql_error());
  $row_turnoj = mysql_fetch_assoc($turnoj);
  $totalRows_turnoj = mysql_num_rows($turnoj);
  
  if ($totalRows_turnoj == 0) {
  $insertSQL = sprintf("INSERT INTO prod_activosj (IDturno, IDempleado) VALUES (%s, %s)", GetSQLValueString($_POST['IDturno'], "int"), GetSQLValueString($_POST['IDempleado'], "int"));
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  } else {
  $updateSQL = sprintf("UPDATE prod_activosj SET IDturno=%s WHERE IDempleado=%s", GetSQLValueString($_POST['IDturno'], "int"), GetSQLValueString($_POST['IDempleado'], "int"));
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  }
  
  $updateGoTo = "productividad_autoriza_puesto_a_t.php?IDpuesto=".$el_puesto."&info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html59.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/components_notifications_other.js"></script>


	<script>
      function load() {
       new Noty({
            text: 'Da clic en el nombre del empleado para ver su histórico de pagos.',
            type: 'info'
        }).show();
    }
	 window.onload = load;
     </script>

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
							<h6 class="panel-title">Captura de Productividad. </h6></br>
						 	
						<!-- Basic alert -->
            <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
						 Se ha actualizado correctamente el turno.
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
											<?php echo $row_matriz['matriz']; ?><br/>
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<?php echo $row_tipo_captura['denominacion']; ?><br/>
											<label class="control-label no-margin text-semibold">Semana:</label>
											<?php echo $semana; ?><br/>
											<label class="control-label no-margin text-semibold">Turno:</label>
											<?php if ($IDturno == 1){echo "Matutino"; } else if ($IDturno == 3){echo "Nocturno"; } else if ($IDturno == 0 OR $IDturno == '' OR $IDturno == 2){echo "No Aplica / Sin Definir"; }?>
										</div>							
					</div>	
					<div class="panel-body"> 
                    <p>Selecciona el nombre del empelado para ver su histórico de pago de productividad. Da clic en el botón para autorizar su productividad.</p>



      <form method="POST" name="form3" action="productividad_autoriza_puesto_a_t.php?IDmatriz=<?php echo $IDmatriz; ?>&IDpuesto=<?php echo $el_puesto; ?>">
    <input type="hidden" name="MM_update" value="form3">       Selecciona el turno para filtrar a los Empleados.                            
			<div class="panel-body text-center">								
          <div class="form-group col-md-2">
            <select name="IDturno" class="form-control ">
											<?php do { ?>
                    <option value="<?php echo $row_turno['IDturno']?>"<?php if (!(strcmp($row_turno['IDturno'], $IDturno))) 
											   {echo "selected=\"selected\"";} ?>><?php echo $row_turno['turno']?></option>
                        <?php
											  } while ($row_turno = mysql_fetch_assoc($turno));
											  $rows = mysql_num_rows($turno);
											  if($rows > 0) {
												  mysql_data_seek($turno, 0);
												  $row_turno = mysql_fetch_assoc($turno);
											  } 
                        ?> 
              </select>
            </div>
            <div class="form-group col-md-1">
              <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
            </div>   
					</div>                                    
			</form>

				
     <div class="table-responsive">
        <table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-blue">
                          <th>Acciones</th>
                          <th>No.Emp.</th>
                          <th>Empleado</th>
                          <th>Sueldo Sem.</th>
                          <th>Garant</th>
                          <th>Asist.</th>
                          <th>H.Ext.</th>
                          <th>Adicional</th>
                          <th>Excedido</th>
                          <th>Total($)</th>
                          <th>Total(%)</th>
                          <th>Rec</th>
                          <th>Car</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { ?>
            <tr>
							 <td>
               <?php if ($row_puestos['validador'] == "") { ?>
                <div> Sin validacion </div>
               <?php } elseif ($row_puestos['autorizador'] == "") { ?>
							 <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-success btn-icon"><i class="icon-arrow-right6"></i> Autorizar</div>
							<?php } else { ?>
							 <div onClick="loadDynamicContentModal('<?php echo $row_puestos['IDempleado']; ?>')" class="btn btn-primary btn-icon"><i class="icon-arrow-right6"></i> Autorizada</div>
							<?php } ?>
							 </td>  
								<td><?php echo $row_puestos['IDempleado']; ?></td>
								<td><a href="prod_empleado_detalle.php?IDempleado=<?php echo $row_puestos['IDempleado']; ?>">
								<?php echo $row_puestos['emp_paterno']; ?> <?php echo $row_puestos['emp_materno']; ?> <?php echo $row_puestos['emp_nombre']; ?></a></td>
								<td><?php echo "$" .number_format(($row_puestos['sueldo_total_productividad'] / 30) * 7); ?>
								<?php if ($row_puestos['sueldo_total_productividad'] != $row_puestos['sueldo_total'] AND $row_puestos['sueldo_total_productividad'] != 0) 	{ echo "<div class='label label-default'>V</div>"; } ?></td>
								<td><?php if ($row_puestos['garantizado'] == 0) { echo "-"; } else { echo "Si";} ?></td>
								<td><?php if ($row_puestos['bono_asistencia'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['bono_asistencia']);} ?></td>
								<td><?php if ($row_puestos['horas_extra_monto'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['horas_extra_monto']);} ?></td>
								<td><?php if ($row_puestos['adicional'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional']);} ?></td>
								<td><?php if ($row_puestos['adicional3'] == '') 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional3']);} ?></td>
								<td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo "$" . number_format($row_puestos['adicional3'] + $row_puestos['pago_total'] + $row_puestos['adicional']  + $row_puestos['bono_asistencia'] + $row_puestos['horas_extra_monto']);} ?></td>
								<td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo (round(($row_puestos['adicional3'] + $row_puestos['pago_total'] + $row_puestos['adicional']  + $row_puestos['bono_asistencia'] + $row_puestos['horas_extra_monto']) / (($row_puestos['sueldo_total_productividad'] / 30) * 7),2)*100)."%";} ?></td>
								<td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo $row_puestos['reci'];} ?></td>
								<td><?php if ($row_puestos['capturador'] == 0) 	{ echo "-"; } else { echo $row_puestos['carg'];} ?></td>
                </tr>
                		 <?php } while ($row_puestos = mysql_fetch_assoc($puestos)); ?>

                         <?php } else { ?>
                         <td colspan="12">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
				    </table>
        </div>


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
    var IDturno = <?php echo $IDturno; ?>;

function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('productividad_autoriza_puesto_amdl_t.php?IDpuesto=' + IDpuesto + '&semana=' + semana + '&IDturno=' + IDturno + '&IDmatriz=' + IDmatriz + '&IDempleado='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 
<script>
function loadDynamicContentModal_t(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('productividad_turno_autoriza_a.php?IDempleado='+ modal, function() {
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
