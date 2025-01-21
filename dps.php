<?php require_once('Connections/sahuayo.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');
// Make unified connection variable
$conn_sahuayo = new KT_connection($sahuayo, $database_sahuayo);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_sahuayo, "");
//Grand Levels: Level
$restrict->addLevel("3");
$restrict->addLevel("2");
$restrict->addLevel("1");
$restrict->Execute();
//End Restrict Access To Page

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works


//Ampliamos la session
if (!isset($_SESSION)) {
ini_set("session.cookie_lifetime", 10800);
ini_set("session.gc_maxlifetime", 10800); 
  session_start();
}

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

mysql_select_db($database_sahuayo, $sahuayo);
$query_variables = "SELECT * FROM sed_variables";
$variables = mysql_query($query_variables, $sahuayo) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$anio = $row_variables['anio'];
$annio = $row_variables['anio'];
if (isset($_GET['aniox']) == true) {
    $aniox = $_GET['aniox'];
} else {
    $aniox = $anio;
}
$colname_usuario = "-1";
if (isset($_SESSION['kt_login_user'])) {
  $colname_usuario = $_SESSION['kt_login_user'];
}
mysql_select_db($database_sahuayo, $sahuayo);
$query_usuario = sprintf("SELECT * FROM sed_usuarios WHERE usuario = %s", GetSQLValueString($colname_usuario, "text"));
$usuario = mysql_query($query_usuario, $sahuayo) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

$user = $_SESSION['kt_login_user'];
mysql_select_db($database_sahuayo, $sahuayo);
$query_puesto_1 = "SELECT sed_puestos.IDPuesto, sed_puestos.denominacion, sed_direcciones.direccion, sed_puestos.descrito, sed_puestos.tipo, sed_subdirecciones.subdireccion FROM sed_puestos left JOIN sed_usuarios ON sed_usuarios.IDPuesto = sed_puestos.IDPuesto LEFT JOIN sed_direcciones ON sed_direcciones.IDdireccion = sed_puestos.IDDireccion left JOIN sed_subdirecciones ON sed_subdirecciones.IDSubdireccion = sed_puestos.IDSubdireccion WHERE sed_usuarios.usuario = '$user'";
mysql_query("SET NAMES 'utf8'");
$puesto_1 = mysql_query($query_puesto_1, $sahuayo) or die(mysql_error());
$row_puesto_1 = mysql_fetch_assoc($puesto_1);
$totalRows_puesto_1 = mysql_num_rows($puesto_1);


mysql_select_db($database_sahuayo, $sahuayo);
$query_puesto_2 = "SELECT sed_puestos.IDPuesto, sed_puestos.denominacion, sed_direcciones.direccion, sed_puestos.descrito, sed_subdirecciones.subdireccion, sed_puestos.tipo, ocupante.usuario_nombre AS ocupador FROM sed_puestos left JOIN sed_usuarios ON sed_usuarios.IDPuesto = sed_puestos.IDPlaza_jefe LEFT JOIN sed_direcciones ON sed_direcciones.IDdireccion = sed_puestos.IDdireccion LEFT JOIN sed_subdirecciones ON sed_subdirecciones.IDSubdireccion = sed_puestos.IDSubdireccion INNER JOIN sed_usuarios AS ocupante ON ocupante.IDPuesto = sed_puestos.IDPuesto WHERE sed_usuarios.usuario = '$user'";
mysql_query("SET NAMES 'utf8'");
$puesto_2 = mysql_query($query_puesto_2, $sahuayo) or die(mysql_error());
$row_puesto_2 = mysql_fetch_assoc($puesto_2);
$totalRows_puesto_2 = mysql_num_rows($puesto_2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $row_variables['empresa']; ?> | <?php echo $row_variables['nombre_sistema']; ?></title>
<link rel="stylesheet" type="text/css" href="css/960.css" />
<link rel="stylesheet" type="text/css" href="css/reset.css" />
<link rel="stylesheet" type="text/css" href="css/text.css" />
<link rel="stylesheet" type="text/css" href="css/red.css" />
<link type="text/css" href="css/smoothness/ui.css" rel="stylesheet" />  
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/blend/jquery.blend.js"></script>
	<script type="text/javascript" src="js/ui.core.js"></script>
	<script type="text/javascript" src="js/ui.sortable.js"></script>    
    <script type="text/javascript" src="js/ui.dialog.js"></script>
    <script type="text/javascript" src="js/ui.datepicker.js"></script>
    <script type="text/javascript" src="js/effects.js"></script>
    <script type="text/javascript" src="js/flot/jquery.flot.pack.js"></script>
    <!--[if IE]>
    <script language="javascript" type="text/javascript" src="js/flot/excanvas.pack.js"></script>
    <![endif]-->
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="css/iefix.css" />
	<script src="js/pngfix.js"></script>
    <script>
        DD_belatedPNG.fix('#menu ul li a span span');
    </script>        
    <![endif]-->
<script id="source" language="javascript" type="text/javascript" src="js/graphs.js"></script>
<link href="includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="includes/common/js/base.js" type="text/javascript"></script>
<script src="includes/common/js/utility.js" type="text/javascript"></script>
<script src="includes/skins/style.js" type="text/javascript"></script>
<script src="includes/nxt/scripts/list.js" type="text/javascript"></script>
<script src="includes/nxt/scripts/list.js.php" type="text/javascript"></script>
<script type="text/javascript">
$NXT_LIST_SETTINGS = {
  duplicate_buttons: false,
  duplicate_navigation: false,
  row_effects: false,
  show_as_buttons: true,
  record_counter: false
}
</script>
<style type="text/css">
  /* Dynamic List row settings */
  .KT_col_ID {width:50px; overflow:hidden;}
  .KT_col_denominacion {width:225px; overflow:hidden;}
  .KT_col_sucursal {width:225px; overflow:hidden;}
  .KT_col_area {width:150px; overflow:hidden;}
  .KT_col_acciones {width:150px; overflow:hidden;}
</style>

</head>

<body>
<!-- WRAPPER START -->
<div class="container_16" id="wrapper">	
  <!--LOGO-->              <div class="grid_8" id="logo"><?php echo $row_variables['nombre_sistema']; ?></a></div>
<div class="grid_8">
     <!-- USER TOOLS START -->       <div id="user_tools"><span>Bienvenido <a href="mis_datos.php"><?php echo $row_usuario['usuario_nombres']; ?></a> | <a href="avisos.php">Ayuda</a> | <a href="logout.php">Salir</a> 
<?php if (@$_SESSION['kt_login_level'] == 3) {?> | <a href="admin.php">Administración</a>  <?php } ?>| Ver: <?php echo $row_variables['version']; ?></div>
    </div>
<!-- USER TOOLS END -->  <div class="grid_16" id="header">
<!-- MENU START -->
<div id="menu">
	<ul class="group" id="menu_group_main">
		<li class="item first" id="one"><a href="panel.php" class="main"><span class="outer"><span class="inner dashboard">
        Inicio</span></span></a></li>
        <li class="item middle" id="two"><a href="evaluar.php" class="main"><span class="outer"><span class="inner users"> Evaluar <?php echo ($anio - 1) ?></span></span></a></li>
        <li class="item middle" id="tree"><a href="capturar.php?usuario=<?php echo $row_usuario['usuario']; ?>&anio=<?php echo $anio; ?>&tipo=1" class="main"><span class="outer"><span class="inner content">
        Capturar <?php echo $anio; ?></span></span></a></li>
        <li class="item middle" id="four"><a href="dps.php" class="main current"><span class="outer"><span class="inner newsletter">
        Perfil de Puestos</span></span></a></li><li class="item middle" id="five"><a href="indicadores.php" class="main"><span class="outer"><span class="inner media_library">
        KPIs Empresa</span></span></a></li>        
		<li class="item middle" id="six"><a href="competencias.php" class="main"><span class="outer"><span class="inner event_manager">
        Evaluar 360°</span></span></a></li>    <li class="item middle" id="seven"><a href="mi_capacitacion.php" class="main"><span class="outer"><span class="inner reports">
        Mi Capacitación </span></span></a></li>  <li class="item last" id="eight"><a href="clima.php"><span class="outer"><span class="inner settings">
        Mis Encuestas</span></span></a></li>
        </ul>
</div>
<!-- MENU END -->
</div>
<div class="grid_16">
    <div id="tabs">
         <div class="container">
            <ul>
                      <li><a href="dps.php" class="current"><span>Descargar</span></a></li>
           </ul>
        </div>
    </div>
</div>
<!-- CONTENT START -->
    <div class="grid_16" id="content">
    <!--  TITLE START  --> 
    <div class="grid_9">
    <h1 class="dashboard">Perfil de Puestos</h1>
    </div><div class="clear">
    </div>
    <!--  TITLE END  -->    
    <!-- #PORTLETS START -->
    <div id="portlets">
    <!-- FIRST SORTABLE COLUMN START -->
    <!-- FIRST SORTABLE COLUMN END -->
      <!-- SECOND SORTABLE COLUMN START -->
      <div class="column">
        <!--THIS IS A PORTLET-->
        <!--THIS IS A PORTLET-->
        <div class="portlet">
          <div class="portlet-header"><img src="images/icons/lightbulb_off.gif" width="16" height="16" alt="Instrucciones" />Instrucciones:</div>
          <div class="portlet-content">
            <p> <?php if (isset($_GET['info'])) {?>
              <p class='info' id='success'><a href='#'><span class='info_inner'><strong>Se ha enviado la notificación con éxito.</strong></span></a></p>
              <?php } ?>
              La descripción de un puesto permite ubicarlo dentro de la organización, así como determinar los requisitos y cualificaciones personales mínimas que deben exigirse para un cumplimiento satisfactorio de las tareas: nivel de estudios, experiencia, requerimientos personales, entre otros.</p>
            <p>Solo los Descriptivos completos y validados por Recursos Humanos se pueden descargar.</p>
            <p>Estatus: <br />
            Capturar = El Descriptivo se puede capturar y/o actualizar.<br />
            En Revisión de RH = La revisión se realiza por parte de RH.<br />
            Revisar | Imprimir = El formato ha sido validado por RH y está listo para descargarse y/o realizar adecuaciones finales.
            <br />
            Puesto Tipo = 
            Los puestos tipo no están disponibles en el sistema, ya que al aplicar a más de dos ocupantes ya están elaborados y validados, por lo que deberás solicitar el formato a RH.<br />
            Descarga<a href="GUIA.pdf" target="_blank"> aquí</a>, la guia de usuario. </p>
            <table cellpadding="2" cellspacing="0" class="KT_tngtable" >
              <thead>
                <tr class="KT_row_order">
                <th>IDpuesto</th>
                <th>Denominación</th>
                <th>Ocupante</th>
                <th>Subárea</th>
                <th>Acciones</th>
                </tr>
              </thead>
		            <tbody>
              <?php do { ?>
                <tr>
                  <td><div class="KT_col_ID"><?php echo $row_puesto_1['IDPuesto']; ?></div></td>
                  <td><div class="KT_col_denominacion"><?php echo $row_puesto_1['denominacion']; ?></div></td>
                  <td><div class="KT_col_sucursal">(MI PUESTO)</div></td>
                  <td><div class="KT_col_area"><?php echo $row_puesto_1['subdireccion']; ?>	</div></td>
                  <td><div class="KT_col_area">
				      <?php if (@$row_puesto_1['descrito'] == 4) {?>
                      <a href="dps/imprimir.php?IDPuesto=<?php echo $row_puesto_1['IDPuesto']; ?>">Imprimir</a>
                      
				      <?php } else if (@$row_puesto_1['descrito'] == 3) {?>
                      <a href="dps_a.php?IDPuesto=<?php echo $row_puesto_1['IDPuesto']; ?>">Revisar</a> 
                      
                      <?php } else if (@$row_puesto_1['descrito'] == 1) { ?>
                      En Revisión de RH
                      
                      <?php } else if (@$row_puesto_1['tipo'] == 1) { ?>
                      En Revisión de RH
                      
                      <?php } else if (@$row_puesto_1['tipo'] > 1) { ?>
                      <a href="dps/<?php echo $row_puesto_1['tipo']; ?>.pdf">Imprimir Puesto Tipo</a>  
                      
                     <?php } else {?>
                       <a href="dps_a.php?IDPuesto=<?php echo $row_puesto_1['IDPuesto']; ?>">Capturar</a>  
                       
               <?php } ?></div></td>
               </tr>
            <?php } while ($row_puesto_1 = mysql_fetch_assoc($puesto_1)); ?>
			<?php do { ?>
			  <?php if ($totalRows_puesto_2 > 0) { // Show if recordset not empty ?>
             <tr>
               <td><div class="KT_col_ID"><?php echo $row_puesto_2['IDPuesto']; ?></div></td>
               <td><div class="KT_col_denominacion"><?php echo $row_puesto_2['denominacion']; ?></div></td>
               <td><div class="KT_col_sucursal"><?php echo $row_puesto_2['ocupador']; ?></div></td>
               <td><div class="KT_col_area"><?php echo $row_puesto_2['subdireccion']; ?></div></td>
               <td><div class="KT_col_area">
			      <?php if (@$row_puesto_2['descrito'] == 3) {?>
                  <a href="dps/imprimir.php?IDPuesto=<?php echo $row_puesto_2['IDPuesto']; ?>">Imprimir</a>
			      <?php } else if (@$row_puesto_2['descrito'] == 2) {?>
                  <a href="dps_a.php?IDPuesto=<?php echo $row_puesto_2['IDPuesto']; ?>">Revisar</a>  
                      <?php } else if (@$row_puesto_2['descrito'] == 1) { ?>
                      En Revisión de RH
                      <?php } else if (@$row_puesto_2['tipo'] > 1) { ?>
                      <a href="dps/<?php echo $row_puesto_2['tipo']; ?>.pdf">Imprimir Puesto Tipo</a>  
                    <?php } else {?>
                       <a href="dps_a.php?IDPuesto=<?php echo $row_puesto_2['IDPuesto']; ?>">Capturar</a> 
                    <?php } ?></div></td>
            </tr>
  <?php } // Show if recordset not empty ?>
  <?php } while ($row_puesto_2 = mysql_fetch_assoc($puesto_2)); ?>
            </tbody>
          </table> 
            <blockquote>
              <p>&nbsp;</p>
              <p>Para cualquier 
            duda o aclaración, favor de contactar con Linda Ruvalcaba a la red 1220 o correo <a href="mailto:lgruvalcaba@sahuayo.mx">lgruvalcaba@sahuayo.mx</a>.<br />
              </p>
            </blockquote>
          </div>
        </div>
      </div>
      <!--  SECOND SORTABLE COLUMN END -->
      <!--THIS IS A WIDE PORTLET-->
    <!--  END #PORTLETS -->  
   </div>
    <div class="clear"> </div>
<!-- END CONTENT-->    
  </div>
<div class="clear"> </div>
</div>
<!-- WRAPPER END -->
<!-- FOOTER START -->
<div class="container_16" id="footer">
  <a href="mailto:<?php echo $row_variables['contacto']; ?>">Contacto</a> | <a href="<?php echo $row_variables['web_empresa']; ?>"><?php echo $row_variables['empresa']; ?></a> 2019</div>
<div><p class="tu-turno"><img src="images/rrhh.png" alt="" /></p></div>
<!-- FOOTER END -->
</body>
</html>
<?php
mysql_free_result($usuario);

mysql_free_result($puesto_1);

mysql_free_result($puesto_2);

mysql_free_result($variables);
?>
