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
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$las_matrizes = $row_usuario['IDmatrizes'];
$capturador = $row_usuario['IDusuario'];

$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);

if (isset($_GET['IDusuario'])) {$IDcandidato = $_GET['IDusuario']; 
mysql_select_db($database_vacantes, $vacantes);
$query_candidatos = "SELECT * FROM cv_activos WHERE IDusuario = '$IDcandidato'";
$candidatos = mysql_query($query_candidatos, $vacantes) or die(mysql_error());
$row_candidatos = mysql_fetch_assoc($candidatos);
$totalRows_candidatos = mysql_num_rows($candidatos);
$su_IDmatriz = $row_candidatos['IDmatriz'];

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$IDrecibe = $row_matriz['correo_JRH'];
$IDcopia = $row_matriz['correo_RRH'];
$IDcopiaSINO = $row_matriz['copiar_correo'];

$query_recibe = "SELECT vac_matriz.matriz, vac_usuarios.usuario_correo, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, vac_usuarios.IDusuario, vac_matriz.correo_JRH  FROM vac_matriz left JOIN vac_usuarios ON vac_matriz.IDmatriz = vac_usuarios.IDmatriz WHERE vac_usuarios.IDusuario = '$IDrecibe'";
$recibe  = mysql_query($query_recibe, $vacantes) or die(mysql_error());
$row_recibe  = mysql_fetch_assoc($recibe);
$totalRows_recibe  = mysql_num_rows($recibe);

$query_copia = "SELECT vac_matriz.matriz, vac_usuarios.usuario_correo, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, vac_usuarios.IDusuario, vac_matriz.correo_JRH  FROM vac_matriz left JOIN vac_usuarios ON vac_matriz.IDmatriz = vac_usuarios.IDmatriz WHERE vac_usuarios.IDusuario = '$IDcopia'";
$copia  = mysql_query($query_copia, $vacantes) or die(mysql_error());
$row_copia  = mysql_fetch_assoc($copia);
$totalRows_copia  = mysql_num_rows($copia);


$recibe_nombre = $row_recibe['usuario_nombre']." ".$row_recibe['usuario_parterno']." ".$row_recibe['usuario_materno'];
$recibe_correo = $row_recibe['usuario_correo'];

$envia_nombre = $row_usuario['usuario_nombre']." ".$row_usuario['usuario_parterno']." ".$row_usuario['usuario_materno'];
$envia_correo = $row_usuario['usuario_correo'];

$copia_nombre = $row_copia['usuario_nombre']." ".$row_copia['usuario_parterno']." ".$row_copia['usuario_materno'];
$copia_correo = $row_copia['usuario_correo'];

	
// WhaptApp	
$IDempleado = $_GET['IDusuario'];	
mysql_select_db($database_vacantes, $vacantes);
$query_candidatos_whats = "SELECT cv_activos.IDusuario, cv_activos.a_paterno, cv_activos.a_materno, cv_activos.a_nombre,  cv_activos.fecha_captura, cv_activos.fecha_entrevista, cv_activos.hora_entrevista, cv_activos.IDentrevista, cv_activos.IDmatriz, cv_activos.IDpuesto, cv_activos.estatus, cv_activos.tipo, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM cv_activos left JOIN vac_puestos ON vac_puestos.IDpuesto = cv_activos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = vac_puestos.IDarea  WHERE cv_activos.IDusuario = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$candidatos_whats = mysql_query($query_candidatos_whats, $vacantes) or die(mysql_error());
$row_candidatos_whats = mysql_fetch_assoc($candidatos_whats);
$totalRows_candidatos_whats = mysql_num_rows($candidatos_whats);

$IDmatriz_whats = $row_candidatos['IDmatriz'];
$query_ubiacion_whats = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz_whats'";
mysql_query("SET NAMES 'utf8'");
$ubiacion_whats = mysql_query($query_ubiacion_whats, $vacantes) or die(mysql_error());
$row_ubiacion_whats = mysql_fetch_assoc($ubiacion_whats);
$totalRows_ubiacion_whats = mysql_num_rows($ubiacion_whats);


$telefonoP = str_replace(array("(", ")", "-"), "", $row_candidatos['telefono_1']);
$telefono = str_replace(" ", "", $telefonoP);
$nombreP = $row_candidatos_whats['a_paterno']."%20".$row_candidatos_whats['a_materno']."%20".$row_candidatos_whats['a_nombre'];
$nombre = str_replace(" ", "%20", $nombreP);
$puesto = $row_candidatos_whats['denominacion'];
$fecha_entrevista = date('d/m/Y', strtotime($row_candidatos_whats['fecha_entrevista']));
$hora_entrevista = $row_candidatos_whats['hora_entrevista'];
$fecha = $row_candidatos_whats['fecha_entrevista'];
$fecha = $row_candidatos_whats['fecha_entrevista'];
$fecha = $row_candidatos_whats['fecha_entrevista'];
$fecha = $row_candidatos_whats['fecha_entrevista'];
$direccion = $row_ubiacion_whats['direccion'];
$ubicacion = $row_ubiacion_whats['ubicacion'];
$salto = "%0A";

$mensaje = "*Nombre*:%20".$nombre.$salto."*Dia*:%20".$fecha_entrevista.$salto."*Hora*:%20".$hora_entrevista.$salto."*Vacante*:%20".$puesto.$salto.$salto."*Documentos*".$salto."-%20Solicitud%20elaborada".$salto."-%20IFE".$salto."-%20CURP".$salto."-%20RFC".$salto."-%20IMSS".$salto."-%20Licencia%20(solo%20si%20aplica)".$salto."-%20Acta%20de%20nacimiento".$salto."-%20Acta%20de%20matrimonio%20(solo%20si%20aplica)".$salto."-%20Comp.%20Estudios".$salto."-%20Comp.%20Domicilio".$salto."-%202%20cartas%20laborales%20(membretadas,%20firmadas%20y%20selladas)".$salto."-%202%20cartas%20personales%20(amigos%20o%20vecinos%20-%205%20años%20de%20conocer%20-%20copia%20de%20INE)".$salto."-%20Certificado%20Medico".$salto."-%204%20fotos%20tama&ntilde;o%20infantil%20a%20color.".$salto."-%20Correo%20electrónico".$salto."-%20Solicitud%20elaborada".$salto.$salto."*Dirección:*%20".$direccion.$salto.$ubicacion.$salto.$salto."_Obligatorio%20uso%20de%20cubrebocas._".$salto."_No%20es%20necesario%20acudir%20a%20entrevista%20con%20todos%20los%20Documentos._";
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$fecha1 = date('Y-m-d');	
$y1 = substr( $_POST['fecha_contacto'], 8, 2 );
$m1 = substr( $_POST['fecha_contacto'], 3, 2 );
$d1 = substr( $_POST['fecha_contacto'], 0, 2 );
$fecha2 =  $y1."-".$m1."-".$d1;

$y2 = substr( $_POST['fecha_entrevista'], 8, 2 );
$m2 = substr( $_POST['fecha_entrevista'], 3, 2 );
$d2 = substr( $_POST['fecha_entrevista'], 0, 2 );
$fecha3 =  $y2."-".$m2."-".$d2;


if (substr(_POST['a_curp'], 10, 1) == 'M') {$sexo = 2;} else { $sexo = 1;}

$updateSQL = sprintf("UPDATE cv_activos SET IDvacante=%s, a_paterno=%s, a_materno=%s, a_nombre=%s, a_correo=%s, telefono_1=%s, telefono_2=%s, a_rfc=%s, a_curp=%s, a_imss=%s, a_sexo=%s, IDescolaridad=%s,  fecha_contacto=%s, fecha_entrevista=%s, fecha_captura=%s, hora_entrevista=%s, IDentrevista=%s, IDdistancia=%s, IDpuesto=%s, IDmatriz=%s, IDfuente=%s, sueldo_anterior=%s, observaciones=%s, estatus=%s  WHERE IDusuario=%s",
                       GetSQLValueString($_POST['IDvacante'], "int"),
                       GetSQLValueString(htmlentities($_POST['a_paterno'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_materno'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_nombre'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString($_POST['a_correo'], "text"),
                       GetSQLValueString($_POST['telefono_1'], "text"),
                       GetSQLValueString($_POST['telefono_2'], "text"),
                       GetSQLValueString($_POST['a_rfc'], "text"),
                       GetSQLValueString($_POST['a_curp'], "text"),
                       GetSQLValueString($_POST['a_imss'], "text"),
                       GetSQLValueString($sexo, "int"),
                       GetSQLValueString($_POST['IDescolaridad'], "int"),
                       GetSQLValueString($fecha2, "text"),
                       GetSQLValueString($fecha3, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($_POST['hora_entrevista'], "text"),
                       GetSQLValueString($_POST['IDentrevista'], "text"),
                       GetSQLValueString($_POST['IDdistancia'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "text"),
                       GetSQLValueString($_POST['IDmatriz'], "text"),
                       GetSQLValueString($_POST['IDfuente'], "text"),
                       GetSQLValueString($_POST['sueldo_anterior'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($_POST['estatus'], "text"),
                       GetSQLValueString($_POST['IDusuario'], "text"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, 	$vacantes) or die(mysql_error());
echo $_POST['hora_entrevista'];
   header('Location:candidatos_nuevo.php?IDusuario='.$IDcandidato.'&info=2');
}

//insertar
else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$fecha1 = date('Y-m-d');	

$y1 = substr( $_POST['fecha_contacto'], 8, 2 );
$m1 = substr( $_POST['fecha_contacto'], 3, 2 );
$d1 = substr( $_POST['fecha_contacto'], 0, 2 );
$fecha2 =  $y1."-".$m1."-".$d1;

$y2 = substr( $_POST['fecha_entrevista'], 8, 2 );
$m2 = substr( $_POST['fecha_entrevista'], 3, 2 );
$d2 = substr( $_POST['fecha_entrevista'], 0, 2 );
$fecha3 =  $y2."-".$m2."-".$d2;
echo $fecha3;

//$fecha3 = date('Y-m-d', strtotime($_POST['fecha_entrevista']));	

if (substr($_POST['a_curp'], 10, 1) == 'M') {$sexo = 2;} else { $sexo = 1;}

$insertSQL = sprintf("INSERT INTO cv_activos (IDvacante, a_paterno, a_materno, a_nombre, a_correo,  telefono_1, telefono_2, a_rfc, a_curp, a_imss, a_sexo, IDescolaridad, fecha_contacto,  fecha_entrevista,  fecha_captura, capturador, hora_entrevista, IDentrevista, IDdistancia, IDpuesto, IDmatriz, IDfuente, sueldo_anterior, tipo, observaciones, estatus) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDvacante'], "int"),
                       GetSQLValueString(htmlentities($_POST['a_paterno'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_materno'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_nombre'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString($_POST['a_correo'], "text"),
                       GetSQLValueString($_POST['telefono_1'], "text"),
                       GetSQLValueString($_POST['telefono_2'], "text"),
                       GetSQLValueString($_POST['a_rfc'], "text"),
                       GetSQLValueString($_POST['a_curp'], "text"),
                       GetSQLValueString($_POST['a_imss'], "text"),
                       GetSQLValueString($sexo, "int"),
                       GetSQLValueString($_POST['IDescolaridad'], "int"),
                       GetSQLValueString($fecha2, "text"),
                       GetSQLValueString($fecha3, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($capturador, "text"),
                       GetSQLValueString($_POST['hora_entrevista'], "text"),
                       GetSQLValueString($_POST['IDentrevista'], "text"),
                       GetSQLValueString($_POST['IDdistancia'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "text"),
                       GetSQLValueString($_POST['IDmatriz'], "text"),
                       GetSQLValueString($_POST['IDfuente'], "text"),
                       GetSQLValueString($_POST['sueldo_anterior'], "text"),
                       GetSQLValueString($_POST['tipo'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($_POST['estatus'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

 $captura = mysql_insert_id();
  header('Location: candidatos_nuevo.php?IDusuario='.$captura.'&info=1');
}

$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);

$query_puestos = "SELECT DISTINCT * FROM vac_puestos";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);

$query_escolaridad = "SELECT * FROM sed_estudios";
$escolaridad = mysql_query($query_escolaridad, $vacantes) or die(mysql_error());
$row_escolaridad = mysql_fetch_assoc($escolaridad);

$query_fuentes = "SELECT * FROM vac_fuentes";
$fuentes = mysql_query($query_fuentes, $vacantes) or die(mysql_error());
$row_fuentes = mysql_fetch_assoc($fuentes);

$query_vacantes = "SELECT vac_puestos.denominacion, vac_vacante.IDvacante,  vac_vacante.IDarea,  vac_vacante.IDpuesto,  vac_vacante.IDusuario FROM vac_vacante INNER JOIN vac_puestos ON  vac_vacante.IDpuesto = vac_puestos.IDpuesto WHERE IDmatriz = '$la_matriz' AND vac_vacante.IDestatus = 1";
$vacantes = mysql_query($query_vacantes, $vacantes) or die(mysql_error());
$row_vacantes = mysql_fetch_assoc($vacantes);


// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDusuario'];
  $deleteSQL = "UPDATE cv_activos SET borrado = 1  WHERE IDusuario ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: candidatos.php?info=3");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
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
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/2picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
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
            
            			<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el candidato.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

            			<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el candidato.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


            			<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-info-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha enviado correctamente el correo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

              
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Candidatos</h5>
						</div>

					<div class="panel-body">
					<p><strong>Instrucciones</strong>: ingrese la información solicitada. </br>
                    Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>
                           <p>  <?php if (isset($row_candidatos['enviado_msg']) && $row_candidatos['enviado_msg'] == 1) { echo "<strong>* El candidato ya ha recibido mensaje</strong>";} ?></p>
                           <p>  <?php if (isset($row_candidatos['enviado_mail'])  && $row_candidatos['enviado_mail']  == 1) { echo "<strong>* El candidato ya ha recibido correo</strong>";} ?></p>
                      
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">

                                
					<?php if(isset($_GET['IDusuario'])) { ?>     
                    
                         <div class="text-right">
                            <div>
                    
					<div onClick="loadDynamicContentModal('<?php echo $row_candidatos['IDusuario']; ?>')" class="btn btn-info">Ver mensaje</div>
						 <button type="button" class="btn btn-success btn-icon" onClick="window.location.href='whatsapp://send?phone=<?php echo '521'.$telefono.'&text='.$mensaje ?>'" >WhatsApp</button>
                         <?php if( $row_candidatos['a_correo'] != '') { ?>   
 						 <button type="button" data-target="#modal_theme_success"  data-toggle="modal" class="btn btn-info">Enviar correo</button>
 						 <?php } ?>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                         <button type="button" onClick="window.location.href='candidatos.php'" class="btn btn-default btn-icon">Regresar</button>
                            </div>
                          </div>
                          <p>&nbsp;</p>

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">IDcandidato:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="IDusuario" id="IDusuario" readonly="readonly" class="form-control"  value="<?php echo htmlentities($row_candidatos['IDusuario'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="estatus" id="estatus" class="form-control"  required="required">
                                                <option value="1" <?php if (!(strcmp(1, htmlentities($row_candidatos['estatus'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>En proceso</option>
                                                <option value="2" <?php if (!(strcmp(2, htmlentities($row_candidatos['estatus'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Declinado</option>
                                                <option value="3" <?php if (!(strcmp(3, htmlentities($row_candidatos['estatus'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Contratado</option>
                                            </select>
										</div>
									</div>
									<!-- /basic select -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_paterno" id="a_paterno" class="form-control" placeholder="Apellido Paterno" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo $row_candidatos['a_paterno']; ?>"  required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Materno:</label>
										<div class="col-lg-9">
											<input type="text" name="a_materno" id="a_materno" class="form-control" placeholder="Apellido Materno" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo $row_candidatos['a_materno']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre(s):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_nombre" id="a_nombre" class="form-control" placeholder="Nombres" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo $row_candidatos['a_nombre']; ?>"  required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo:</label>
										<div class="col-lg-9">
											<input type="email" name="a_correo" id="a_correo" class="form-control"  placeholder="Correo electrónico"  value="<?php echo htmlentities($row_candidatos['a_correo'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono_1" id="telefono_1" class="form-control" placeholder="Teléfono" value="<?php echo htmlentities($row_candidatos['telefono_1'], ENT_COMPAT, ''); ?>" required="required" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono adicional:</label>
										<div class="col-lg-9">
											<input type="text" name="telefono_2" id="telefono_2" class="form-control" placeholder="Teléfono adicional o de recados" value="<?php echo htmlentities($row_candidatos['telefono_2'], ENT_COMPAT, ''); ?>" >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">RFC:</label>
										<div class="col-lg-9">
											<input type="text" name="a_rfc" id="a_rfc" class="form-control" placeholder="RFC a 13 posiciones" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_candidatos['a_rfc'], ENT_COMPAT, ''); ?>"  maxlength="13" minlength="10">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">CURP:</label>
										<div class="col-lg-9">
											<input type="text" name="a_curp" id="a_curp" class="form-control" placeholder="CURP a 18 posiciones" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_candidatos['a_curp'], ENT_COMPAT, ''); ?>"  maxlength="18" minlength="18" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">NSS:</label>
										<div class="col-lg-9">
											<input type="text" name="a_imss" id="a_imss" class="form-control" placeholder="NSS a 11 posiciones" value="<?php echo htmlentities($row_candidatos['a_imss'], ENT_COMPAT, ''); ?>"  maxlength="11" minlength="11" >
										</div>
									</div>
									<!-- /basic text input -->
                      

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Edad:</label>
										<div class="col-lg-9">
											<input type="number" name="edad" id="edad" class="form-control" placeholder="Edad en años cumplidos" value="<?php echo htmlentities($row_candidatos['edad'], ENT_COMPAT, ''); ?>"  maxlength="2">
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Escolaridad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDescolaridad" id="IDescolaridad" class="select-search" required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_candidatos['IDescolaridad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Primaria</option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_candidatos['IDescolaridad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Secundaria</option>
                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_candidatos['IDescolaridad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Preparatoria / Técnico</option>
                            <option value="4" <?php if (!(strcmp(4, htmlentities($row_candidatos['IDescolaridad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Universidad</option>
                            <option value="5" <?php if (!(strcmp(5, htmlentities($row_candidatos['IDescolaridad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Especialidad / Diplomado</option>
                            <option value="6" <?php if (!(strcmp(6, htmlentities($row_candidatos['IDescolaridad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Maestría</option>
                            <option value="7" <?php if (!(strcmp(7, htmlentities($row_candidatos['IDescolaridad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Doctorado</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Contacto:</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_contacto" id="fecha_contacto" value="<?php  if ($row_candidatos['fecha_contacto'] == "")
										{ echo "";} else { echo date('d/m/Y', strtotime($row_candidatos['fecha_contacto'])) ; }?>">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Entrevista:</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_entrevista" id="fecha_entrevista" value="<?php  if ($row_candidatos['fecha_entrevista'] == "")
										{ echo "";} else { echo date('d/m/Y', strtotime($row_candidatos['fecha_entrevista'])) ; }?>" >
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->
                                

									<!-- Hora -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Hora de Entrevista:</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-alarm"></i></span>
                                    	<input type="text" class="form-control  pickatime-format" name="hora_entrevista" id="hora_entrevista" value="<?php  if ($row_candidatos['hora_entrevista'] == "")
										{ echo "";} else { echo $row_candidatos['hora_entrevista']; }?>" placeholder="Selecciona hora">
									</div>
                                   </div>
                                  </div> 
								<!-- Hora -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Distancia:</label>
										<div class="col-lg-9">
											<select name="IDdistancia" id="IDdistancia" class="form-control">
												<option value ="">No definido</option> 
                                                <option value="1" <?php if (!(strcmp(1, htmlentities($row_candidatos['IDdistancia'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>10-30 minutos</option>
                                                <option value="2" <?php if (!(strcmp(2, htmlentities($row_candidatos['IDdistancia'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>40-60 minutos</option>
                                                <option value="3" <?php if (!(strcmp(3, htmlentities($row_candidatos['IDdistancia'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>1 hr-1:30 hr</option>
                                                <option value="4" <?php if (!(strcmp(4, htmlentities($row_candidatos['IDdistancia'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>2 hrs o más</option>
                                            </select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus Entrevista:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDentrevista" id="IDentrevista" class="form-control"  required="required">
                                                <option value="1" <?php if (!(strcmp(1, htmlentities($row_candidatos['IDentrevista'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Si es viable</option>
                                                <option value="2" <?php if (!(strcmp(2, htmlentities($row_candidatos['IDentrevista'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>No es viable</option>
                                                <option value="3" <?php if (!(strcmp(3, htmlentities($row_candidatos['IDentrevista'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>No contestó</option>
                                            </select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto" id="IDpuesto" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
                                            	<option value=999>OTRO (INDICADO POR EL CANDIDATO)</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if (!(strcmp($row_puestos['IDpuesto'], $row_candidatos['IDpuesto']))) {echo "SELECTED";} ?>><?php echo $row_puestos['denominacion']?></option>
													  <?php
													 } while ($row_puestos = mysql_fetch_assoc($puestos));
													 $rows = mysql_num_rows($puestos);
													 if($rows > 0) {
													 mysql_data_seek($puestos, 0);
													 $row_puestos = mysql_fetch_assoc($puestos);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->
                                    
									<!-- Basic select -->
									<div class="form-group">
									<label class="control-label col-lg-3">Puesto no registrado (capturado por el candidato):</label>
										<div class="col-lg-9">
											<input type="text" name="IDpuesto_texto" id="IDpuesto_texto" class="form-control" placeholder="Otro puesto (en su caso)" value="<?php echo htmlentities($row_candidatos['IDpuesto_texto'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_matrizes['IDmatriz']?>"<?php if (!(strcmp($row_matrizes['IDmatriz'], $row_candidatos['IDmatriz']))) {echo "SELECTED";} ?>><?php echo $row_matrizes['matriz']?></option>
													  <?php
													 } while ($row_matrizes = mysql_fetch_assoc($matrizes));
													 $rows = mysql_num_rows($matrizes);
													 if($rows > 0) {
													 mysql_data_seek($matrizes, 0);
													 $row_matrizes = mysql_fetch_assoc($matrizes);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->		

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">ID de la Vacante:</label>
										<div class="col-lg-9">
											<select name="IDvacante" id="IDvacante" class="select-search">
                                            	<option value="">Selecciona la vacante</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_vacantes['IDvacante']?>"<?php if (!(strcmp($row_vacantes['IDvacante'], $row_candidatos['IDvacante']))) {echo "SELECTED";} ?>><?php echo $row_vacantes['denominacion']?> (<?php echo $row_vacantes['IDvacante']?>)</option>
													  <?php
													 } while ($row_vacantes = mysql_fetch_assoc($vacantes));
													 $rows = mysql_num_rows($vacantes);
													 if($rows > 0) {
													 mysql_data_seek($vacantes, 0);
													 $row_vacantes = mysql_fetch_assoc($vacantes);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->



									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fuente de Reclutamiento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDfuente" id="IDfuente" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_fuentes['IDfuente']?>"<?php if (!(strcmp($row_fuentes['IDfuente'], $row_candidatos['IDfuente']))) {echo "SELECTED";} ?>><?php echo $row_fuentes['fuente']?></option>
													  <?php
													 } while ($row_fuentes = mysql_fetch_assoc($fuentes));
													 $rows = mysql_num_rows($fuentes);
													 if($rows > 0) {
													 mysql_data_seek($fuentes, 0);
													 $row_fuentes = mysql_fetch_assoc($fuentes);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo semanal anterior:</label>
										<div class="col-lg-9">
											<input type="number" name="sueldo_anterior" id="sueldo_anterior" class="form-control" placeholder="Sueldo semanal anterior" value="<?php echo htmlentities($row_candidatos['sueldo_anterior'], ENT_COMPAT, ''); ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                            <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Observaciones:</label>
										<div class="col-lg-9">
                                          <textarea name="observaciones" rows="3" class="form-control" id="observaciones" placeholder="Observaciones."><?php echo htmlentities($row_candidatos['observaciones'], ENT_COMPAT, ''); ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->


                          <div class="text-right">
                            <div>
                            <div onClick="loadDynamicContentModal('<?php echo $row_candidatos['IDusuario']; ?>')" class="btn btn-info">Ver mensaje</div>
                         <?php if( $row_candidatos['a_correo'] != '') { ?>   
 						 <button type="button" data-target="#modal_theme_success"  data-toggle="modal" class="btn btn-info">Enviar correo</button>
 						 <?php } ?>
                         <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger">Borrar</button>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                         <button type="button" onClick="window.location.href='candidatos.php'" class="btn btn-default btn-icon">Regresar</button>
                            </div>
                          </div>

                      <input type="hidden" name="MM_update" value="form1">
                      <input type="hidden" name="IDusuario" id="IDusuario" value="<?php echo $row_candidatos['IDusuario']; ?>">
                      
                    
                     <?php } else { ?>           
                     
                     
                          <div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-success">Agregar</button>
                         <button type="button" onClick="window.location.href='candidatos.php'" class="btn btn-default btn-icon">Regresar</button>
                            </div>
                          </div>
						<p>&nbsp;</p>

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="estatus" id="estatus" class="form-control"  required="required">
                                                <option value="1">En proceso</option>
                                                <option value="2">Declinado</option>
                                                <option value="3">Contratado</option>
                                            </select>
										</div>
									</div>
									<!-- /basic select -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_paterno" id="a_paterno" class="form-control" placeholder="Apellido Paterno" onKeyUp="this.value=this.value.toUpperCase()" value=""  required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Materno:</label>
										<div class="col-lg-9">
											<input type="text" name="a_materno" id="a_materno" class="form-control" placeholder="Apellido Materno" onKeyUp="this.value=this.value.toUpperCase()" value="" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre(s):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_nombre" id="a_nombre" class="form-control" placeholder="Nombres" onKeyUp="this.value=this.value.toUpperCase()" value=""  required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo:</label>
										<div class="col-lg-9">
											<input type="email" name="a_correo" id="a_correo" class="form-control" placeholder="Correo electrónico"  value="">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono_1" id="telefono_1" class="form-control" placeholder="Teléfono" value="" required="required" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono adicional:</label>
										<div class="col-lg-9">
											<input type="text" name="telefono_2" id="telefono_2" class="form-control" placeholder="Teléfono adicional" value="" >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">RFC:</label>
										<div class="col-lg-9">
											<input type="text" name="a_rfc" id="a_rfc" class="form-control" placeholder="RFC a 13 posiciones" onKeyUp="this.value=this.value.toUpperCase()" value=""  maxlength="13" minlength="10" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">CURP:</label>
										<div class="col-lg-9">
											<input type="text" name="a_curp" id="a_curp" class="form-control" placeholder="CURP a 18 posiciones" onKeyUp="this.value=this.value.toUpperCase()" value=""  maxlength="18" minlength="18" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">NSS:</label>
										<div class="col-lg-9">
											<input type="text" name="a_imss" id="a_imss" class="form-control" placeholder="NSS a 11 posiciones" value=""  maxlength="11" minlength="11" >
										</div>
									</div>
									<!-- /basic text input -->
                      

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Edad:</label>
										<div class="col-lg-9">
											<input type="number" name="edad" id="edad" class="form-control" placeholder="Edad en años cumplidos" value=""  maxlength="2">
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sexo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_sexo" id="a_sexo" class="form-control" required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1">HOMBRE</option>
                            <option value="2">MUJER</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Escolaridad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDescolaridad" id="IDescolaridad" class="select-search" required="required">
												<option value ="">Seleccione una opción</option> 
                                                    <option value="1">Primaria</option>
                                                    <option value="2">Secundaria</option>
                                                    <option value="3">Preparatoria / Técnico</option>
                                                    <option value="4">Universidad</option>
                                                    <option value="5">Especialidad / Diplomado</option>
                                                    <option value="6">Maestría</option>
                                                    <option value="7">Doctorado</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Contacto:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_contacto" id="fecha_contacto" value="">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Entrevista:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_entrevista" id="fecha_entrevista" value="">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->
                                

									<!-- Hora -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Hora de Entrevista:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-alarm"></i></span>
                                    	<input type="text" class="form-control  pickatime-format" name="hora_entrevista" id="hora_entrevista" value="09:00" placeholder="Selecciona hora">
									</div>
                                   </div>
                                  </div> 
								<!-- Hora -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus Entrevista:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDentrevista" id="IDentrevista" class="form-control">
                                                <option value="1">Si</option>
                                                <option value="2">No</option>
                                                <option value="3">No contestó</option>
                                            </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Distancia:</label>
										<div class="col-lg-9">
											<select name="IDdistancia" id="IDdistancia" class="form-control">
												<option value ="">No definido</option> 
                                                <option value="1">10-30 minutos</option>
                                                <option value="2">40-60 minutos</option>
                                                <option value="3">1 hr-1:30 hr</option>
                                                <option value="4">2 hrs o más</option>
                                            </select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto" id="IDpuesto" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_puestos['IDpuesto']?>"><?php echo $row_puestos['denominacion']?></option>
													  <?php
													 } while ($row_puestos = mysql_fetch_assoc($puestos));
													 $rows = mysql_num_rows($puestos);
													 if($rows > 0) {
													 mysql_data_seek($puestos, 0);
													 $row_puestos = mysql_fetch_assoc($puestos);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->
                                    

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_matrizes['IDmatriz']?>"><?php echo $row_matrizes['matriz']?></option>
													  <?php
													 } while ($row_matrizes = mysql_fetch_assoc($matrizes));
													 $rows = mysql_num_rows($matrizes);
													 if($rows > 0) {
													 mysql_data_seek($matrizes, 0);
													 $row_matrizes = mysql_fetch_assoc($matrizes);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">ID de la Vacante:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDvacante" id="IDvacante" class="select-search" required="required">
                                            	<option value="">Selecciona la vacante</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_vacantes['IDvacante']?>"><?php echo $row_vacantes['denominacion']?> (<?php echo $row_vacantes['IDvacante']?>)</option>
													  <?php
													 } while ($row_vacantes = mysql_fetch_assoc($vacantes));
													 $rows = mysql_num_rows($vacantes);
													 if($rows > 0) {
													 mysql_data_seek($vacantes, 0);
													 $row_vacantes = mysql_fetch_assoc($vacantes);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fuente de Reclutamiento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDfuente" id="IDfuente" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_fuentes['IDfuente']?>"><?php echo $row_fuentes['fuente']?></option>
													  <?php
													 } while ($row_fuentes = mysql_fetch_assoc($fuentes));
													 $rows = mysql_num_rows($fuentes);
													 if($rows > 0) {
													 mysql_data_seek($fuentes, 0);
													 $row_fuentes = mysql_fetch_assoc($fuentes);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo semanal anterior:</label>
										<div class="col-lg-9">
											<input type="number" name="sueldo_anterior" id="sueldo_anterior" class="form-control" placeholder="Sueldo semanal anterior" value="" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                            <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Observaciones:</label>
										<div class="col-lg-9">
                                          <textarea name="observaciones" rows="3" class="form-control" id="observaciones" placeholder="Observaciones."></textarea>

										</div>
									</div>
									<!-- /basic text input -->

                          <div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-success">Agregar</button>
                         <button type="button" onClick="window.location.href='candidatos.php'" class="btn btn-default btn-icon">Regresar</button>
                            </div>
                          </div>

                      <input type="hidden" name="MM_insert" value="form1">
                      <input type="hidden" name="tipo" id="tipo" value="1">

					<?php }  ?>                                
                      
                       </fieldset>
                      </form>

					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el candidato <?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno'] . " " . $row_candidatos['a_nombre']; ?>?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="candidatos_nuevo.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
                    

					<div id="modal_theme_success" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de correo</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres enviar la información de la Entrevista por correo al candidato <?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno'] . " " . $row_candidatos['a_nombre'] ." (".$row_candidatos['a_correo'].") "; ?>?.</p>
                                    <p>	Se enviará una copia a <?php echo $recibe_nombre. " (".$recibe_correo .")";  echo ", ".$copia_nombre . " (".$copia_correo .") y a tu correo"; ?>.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-primary" href="candidatos_mail.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>">Si enviar</a>
								</div>
							</div>
						</div>
					</div>

                   <!-- Inline form modal -->
					<div id="bootstrap-modal" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content text-center">
								<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Confirmaci&oacute;n de Entrevista</h5>
								</div>
							<div class="modal-body">
			              <div id="conte-modal"></div>
							</div>
						</div>
					</div>
					<!-- /inline form modal -->
                    

				  </div>

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
function loadDynamicContentModal(modal){
	var options = {
			modal: true
		};
	$('#conte-modal').load('candidatos_mdl.php?IDusuario='+ modal, function() {
		$('#bootstrap-modal').modal({show:true});
    });    
}
</script> 

</body>
</html>