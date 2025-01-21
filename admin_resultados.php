<?php require_once('Connections/examenesz.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Load the required classes
require_once('includes/tfi/TFI.php');
require_once('includes/tso/TSO.php');
require_once('includes/nav/NAV.php');

// Make unified connection variable
$conn_examenesz = new KT_connection($examenesz, $database_examenesz);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_examenesz, "");
//Grand Levels: Level

$restrict->addLevel("3");
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

// Filter
$tfi_listresultados1 = new TFI_TableFilter($conn_examenesz, "tfi_listresultados1");
$tfi_listresultados1->addColumn("ex_usuarios.IDusuario", "NUMERIC_TYPE", "IDusuario", "%");
$tfi_listresultados1->addColumn("ex_usuarios.asignado_por", "NUMERIC_TYPE", "asignado_por", "=");
$tfi_listresultados1->addColumn("ex_resultados.mes", "NUMERIC_TYPE", "mes", "=");
$tfi_listresultados1->addColumn("ex_usuarios.usuario_folio", "STRING_TYPE", "usuario_folio", "%");
$tfi_listresultados1->addColumn("ex_usuarios.usuario_nombre", "STRING_TYPE", "usuario_nombre", "%");
$tfi_listresultados1->addColumn("ex_usuarios.usuario_parterno", "STRING_TYPE", "usuario_parterno", "%");
$tfi_listresultados1->addColumn("ex_usuarios.usuario_materno", "STRING_TYPE", "usuario_materno", "%");
$tfi_listresultados1->addColumn("ex_examenes.examen_nombre", "STRING_TYPE", "examen_nombre", "%");
$tfi_listresultados1->addColumn("ex_resultados.total_puntos", "NUMERIC_TYPE", "total_puntos", "=");
$tfi_listresultados1->addColumn("ex_resultados.resultado", "STRING_TYPE", "resultado", "%");
$tfi_listresultados1->Execute();

// Sorter
$tso_listresultados1 = new TSO_TableSorter("resultados", "tso_listresultados1");
$tso_listresultados1->addColumn("IDusuario");
$tso_listresultados1->addColumn("asignado_por");
$tso_listresultados1->addColumn("mes");
$tso_listresultados1->addColumn("usuario_folio");
$tso_listresultados1->addColumn("usuario_nombre");
$tso_listresultados1->addColumn("usuario_parterno");
$tso_listresultados1->addColumn("usuario_materno");
$tso_listresultados1->addColumn("examen_nombre");
$tso_listresultados1->addColumn("total_puntos");
$tso_listresultados1->addColumn("resultado");
$tso_listresultados1->setDefault("usuario_folio");
$tso_listresultados1->Execute();

// Navigation
$nav_listresultados1 = new NAV_Regular("nav_listresultados1", "resultados", "", $_SERVER['PHP_SELF'], 1000);

//Ampliamos la session
if (!isset($_SESSION)) {
ini_set("session.cookie_lifetime", 10800);
ini_set("session.gc_maxlifetime", 10800); 
  session_start();
}

// variables del sistema para todos
mysql_select_db($database_examenesz, $examenesz);
$query_variables = "SELECT * FROM ex_variables";
mysql_query("SET NAMES 'utf8'");
$variables = mysql_query($query_variables, $examenesz) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$elmes = date("m");

//utilerias
mysql_query("SET NAMES 'utf8'");
$kt_nombres = $_SESSION['kt_login_user'];


//usuario logeado
$colname_usuario = "-1";
if (isset($_SESSION['kt_login_user'])) {
  $colname_usuario = $_SESSION['kt_login_user'];
}
mysql_select_db($database_examenesz, $examenesz);
$query_usuario = sprintf("SELECT * FROM ex_usuarios WHERE usuario = %s", GetSQLValueString($colname_usuario, "text"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $examenesz) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

//NeXTenesio3 Special List Recordset
$maxRows_resultados = $_SESSION['max_rows_nav_listresultados1'];
$pageNum_resultados = 0;
if (isset($_GET['pageNum_resultados'])) {
  $pageNum_resultados = $_GET['pageNum_resultados'];
}
$startRow_resultados = $pageNum_resultados * $maxRows_resultados;

$el_mes = date("m");

// Defining List Recordset variable
$NXTFilter_resultados = "ex_resultados.mes = ".$el_mes;
if (isset($_SESSION['filter_tfi_listresultados1']) AND $_SESSION['filter_tfi_listresultados1'] != '1=1') {
  $NXTFilter_resultados = $_SESSION['filter_tfi_listresultados1'];
}

// Defining List Recordset variable
$NXTSort_resultados = "IDusuario";
if (isset($_SESSION['sorter_tso_listresultados1'])) {
  $NXTSort_resultados = $_SESSION['sorter_tso_listresultados1'];
}
mysql_select_db($database_examenesz, $examenesz);

$query_resultados = "SELECT ex_usuarios.asignado_por, ex_usuarios.IDusuario, ex_usuarios.usuario_nombre, ex_usuarios.usuario_folio, ex_usuarios.usuario_parterno, ex_usuarios.usuario_materno, ex_resultados.IDexamen,  ex_resultados.IDresultado, ex_resultados.abiertas, ex_examenes.examen_revision, ex_examenes.examen_impresion, ex_resultados.total_puntos, ex_resultados.mes, ex_resultados.obtenidos_puntos, ex_resultados.resultado, ex_examenes.examen_nombre, asignador.IDusuario AS IDusuarioA FROM ex_usuarios RIGHT JOIN ex_resultados ON ex_resultados.IDusuario = ex_usuarios.IDusuario LEFT JOIN ex_examenes ON ex_examenes.IDexamen = ex_resultados.IDexamen inner JOIN ex_usuarios AS asignador ON ex_usuarios.asignado_por = asignador.IDusuario WHERE  {$NXTFilter_resultados} ORDER BY  {$NXTSort_resultados} "; 
mysql_query("SET NAMES 'utf8'");
$query_limit_resultados = sprintf("%s LIMIT %d, %d", $query_resultados, $startRow_resultados, $maxRows_resultados);
$resultados = mysql_query($query_limit_resultados, $examenesz) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$abiertas = $row_resultados['abiertas'];
$lusuario = $row_resultados['IDusuario'];

if (isset($_GET['totalRows_resultados'])) {
  $totalRows_resultados = $_GET['totalRows_resultados'];
} else {
  $all_resultados = mysql_query($query_resultados);
  $totalRows_resultados = mysql_num_rows($all_resultados);
}
$totalPages_resultados = ceil($totalRows_resultados/$maxRows_resultados)-1;
//End NeXTenesio3 Special List Recordset

$nav_listresultados1->checkBoundries();
$anio = $row_variables['anio'];
?>
<!DOCTYPE html>
<head>
<?php include_once('menus/header.php'); ?>
  <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css">

	<script type="text/javascript" language="javascript" src="js/jquery-1.12.4.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" language="javascript" src="js/jquery-confirm.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/templatemo_script.js"></script>

	<script type="text/javascript" class="init">
    $(document).ready(function() {
	$('#examenes').DataTable();
      } );
	</script>
	<link href="includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
	<script src="includes/common/js/base.js" type="text/javascript"></script>
	<script src="includes/common/js/utility.js" type="text/javascript"></script>
	<script src="includes/skins/style.js" type="text/javascript"></script>
	<script src="includes/nxt/scripts/list.js" type="text/javascript"></script>
	<script src="includes/nxt/scripts/list.js.php" type="text/javascript"></script>
</head>
<body>
<div id="main-wrapper">
 <div class="navbar navbar-inverse" role="navigation">
      <div class="navbar-header">
        <div class="logo"><h1><?php echo $row_variables['nombre_sistema']; ?> | <?php echo $row_variables['empresa']; ?></h1></div>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Menu de Navegacion</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button> 
      </div>   
  </div>
  <div class="template-page-wrapper">
<div class="navbar-collapse collapse templatemo-sidebar"><img src="images/<?php echo $row_variables['logo_file']; ?>" width="240" height="107" alt="su logo" />
 <ul class="templatemo-sidebar-menu">
          <li><a href="admin.php"><i class="fa fa-cog"></i>Inicio</a></li>
          <li><a href="admin_tipos.php"><i class="fa  fa-tag"></i><span class="badge pull-right"></span>Tipos </a></li>
          <li><a href="admin_examen.php"><i class="fa  fa-file-text-o"></i><span class="badge pull-right"></span>Exámenes</a></li>
          <li><a href="admin_preguntas.php"><i class="fa  fa-question"></i><span class="badge pull-right"></span>Preguntas</a></li>
          <li><a href="admin_usuarios.php"><i class="fa  fa-users"></i><span class="badge pull-right"></span>Usuarios</a></li>
          <li class="active"><a href="admin_resultados.php"><i class="fa  fa-check-square-o"></i><span class="badge pull-right"></span>Resultados</a></li>
          <li><a href="javascript:;" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-sign-out"></i>Salir</a></li>
      </ul>
    </div><!--/.navbar-collapse --> <div class="templatemo-content-wrapper">
        <div class="templatemo-content">
          <ol class="breadcrumb">
            <li><a href="admin.php">Administración</a></li>
            <li class="active">Resultados</li>
          </ol>
          <h1>Administración Resultados</h1>
          <p>A continuación seleccione la sección que requiere administrar. </p>
		  
		  
          <p><?php if (isset($_GET['info']) &&  $_GET['info'] == 1) {?>
          <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
          <strong>Correcto! </strong> Se ha borrado el resultado.</div><?php } ?>
          <div>
            <h4> Resultados
              <?php
  $nav_listresultados1->Prepare();
  require("includes/nav/NAV_Text_Statistics.inc.php");
?>
            </h4>
            <div class="KT_tnglist">
              <form action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" method="post" id="form1">
                <div class="KT_options"> <a href="<?php echo $nav_listresultados1->getShowAllLink(); ?>"><?php echo NXT_getResource("Show"); ?>
                  <?php 
  // Show IF Conditional region1
  if (@$_GET['show_all_nav_listresultados1'] == 1) {
?>
                  <?php echo $_SESSION['default_max_rows_nav_listresultados1']; ?>
                   <?php 
  // else Conditional region1
  } else { ?>
                   todos
                    <?php } 
  // endif Conditional region1
?>
</a> &nbsp;
                  &nbsp;
                  <?php 
  // Show IF Conditional region2
  if (@$_SESSION['has_filter_tfi_listresultados1'] == 1) {
?>
                    <a href="<?php echo $tfi_listresultados1->getResetFilterLink(); ?>">Borrar Filtro</a>
                    <?php 
  // else Conditional region2
  } else { ?>
                    <a href="<?php echo $tfi_listresultados1->getShowFilterLink(); ?>"><?php echo NXT_getResource("Show filter"); ?></a>
                    <?php } 
  // endif Conditional region2
?>
                </div>
                <table class="table table-striped table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>&nbsp;</th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('IDusuario'); ?>">IDusuario</a> </th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('asignado_por'); ?>">Asignado por</a> </th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('mes'); ?>">Mes</a> </th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('usuario_folio'); ?>">Folio</a> </th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('usuario_nombre'); ?>">Nombre</a> </th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('usuario_parterno'); ?>">Parterno</a> </th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('usuario_materno'); ?>">Materno</a> </th>
                      <th> <a href="<?php echo $tso_listresultados1->getSortLink('examen_nombre'); ?>">Examen</a> </th>
                      <th>&nbsp;</th>
                    </tr>
                    <?php 
  // Show IF Conditional region3
  //if (@$_SESSION['has_filter_tfi_listresultados1'] == 1) {
?>
                      <tr>
                        <td>&nbsp;</td>
                        <td><input type="text" name="tfi_listresultados1_IDusuario" id="tfi_listresultados1_IDusuarioo" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listresultados1_IDusuario']); ?>" size="10" maxlength="10" /></td>
                        <td><select name="tfi_listresultados1_asignado_por" id="tfi_listresultados1_asignado_por">
                          <option value="" <?php if (!(strcmp('', KT_escapeAttribute(@$_SESSION['tfi_listresultados1_asignado_por'])))) {echo "SELECTED";} ?>>Seleccione Uno...</option>
                          <option value="1" <?php if (!(strcmp(1, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_asignado_por'])))) {echo "SELECTED";} ?>>Tania</option>
                          <option value="50" <?php if (!(strcmp(50, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_asignado_por'])))) {echo "SELECTED";} ?>>Cinthia</option>
                        </select></td>
                        <td><select name="tfi_listresultados1_mes" id="tfi_listresultados1_mes">
                          <option value="" <?php if (!(strcmp('', KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Seleccione Uno...</option>
                          <option value="1" <?php if (!(strcmp(1, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Enero</option>
                          <option value="2" <?php if (!(strcmp(2, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Febrero</option>
                          <option value="3" <?php if (!(strcmp(3, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Marzo</option>
                          <option value="4" <?php if (!(strcmp(4, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Abril</option>
                          <option value="5" <?php if (!(strcmp(5, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Mayo</option>
                          <option value="6" <?php if (!(strcmp(6, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Junio</option>
                          <option value="7" <?php if (!(strcmp(7, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Julio</option>
                          <option value="8" <?php if (!(strcmp(8, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Agosto</option>
                          <option value="9" <?php if (!(strcmp(9, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Septiembre</option>
                          <option value="10" <?php if (!(strcmp(10, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Octubre</option>
                          <option value="11" <?php if (!(strcmp(11, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Noviembre</option>
                          <option value="12" <?php if (!(strcmp(12, KT_escapeAttribute(@$_SESSION['tfi_listresultados1_mes'])))) {echo "SELECTED";} ?>>Diciembre</option>
                        </select></td>
                        <td><input type="text" name="tfi_listresultados1_usuario_folio" id="tfi_listresultados1_usuario_folio" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listresultados1_usuario_folio']); ?>" size="10" maxlength="20" /></td>
                        <td><input type="text" name="tfi_listresultados1_usuario_nombre" id="tfi_listresultados1_usuario_nombre" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listresultados1_usuario_nombre']); ?>" size="10" maxlength="20" /></td>
                        <td><input type="text" name="tfi_listresultados1_usuario_parterno" id="tfi_listresultados1_usuario_parterno" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listresultados1_usuario_parterno']); ?>" size="10" maxlength="20" /></td>
                        <td><input type="text" name="tfi_listresultados1_usuario_materno" id="tfi_listresultados1_usuario_materno" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listresultados1_usuario_materno']); ?>" size="10" maxlength="20" /></td>
                        <td><input type="text" name="tfi_listresultados1_examen_nombre" id="tfi_listresultados1_examen_nombre" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listresultados1_examen_nombre']); ?>" size="20" maxlength="20" /></td>
                        <td><input type="submit" class="btn btn-warning" name="tfi_listresultados1" value="<?php echo NXT_getResource("Filter"); ?>" /></td>
                      </tr>
                      <?php // } 
  // endif Conditional region3
?>
                  </thead>
                  <tbody>
                    <?php if ($totalRows_resultados == 0) { // Show if recordset empty ?>
                      <tr>
                        <td colspan="7"><?php echo NXT_getResource("The table is empty or the filter you've selected is too restrictive."); ?></td>
                      </tr>
                      <?php } // Show if recordset empty ?>
                    <?php if ($totalRows_resultados > 0) { // Show if recordset not empty ?>
                    <?php do { ?>
                        <tr class="<?php echo @$cnt1++%2==0 ? "" : "KT_even"; ?>">
						  <td><input type="checkbox" name="kt_pk_ex_resultados" class="id_checkbox" value="<?php echo $row_resultados['IDusuario']; ?>" />
                            <input type="hidden" name="IDusuario" class="id_field" value="<?php echo $row_resultados['IDusuario']; ?>" /></td>
                         <td><?php echo number_format($row_resultados['IDusuario']); ?></td>
                          <td>
							<div class="KT_col_asignado_por">
								<?php if ($row_resultados['asignado_por'] == 2) { echo 'Thania'; } 
									else if ($row_resultados['asignado_por'] == 50) { echo 'Cinthia'; } 
								?>
							</div>
						  </td>
                          <td>
							<div class="KT_col_mes">
								<?php	 if ($row_resultados['mes'] == 1) { echo 'Enero'; } 
									else if ($row_resultados['mes'] == 2) { echo 'Febrero'; } 
									else if ($row_resultados['mes'] == 3) { echo 'Marzo'; } 
									else if ($row_resultados['mes'] == 4) { echo 'Abril'; } 
									else if ($row_resultados['mes'] == 5) { echo 'Mayo'; } 
									else if ($row_resultados['mes'] == 6) { echo 'Junio'; } 
									else if ($row_resultados['mes'] == 7) { echo 'Julio'; } 
									else if ($row_resultados['mes'] == 8) { echo 'Agosto'; } 
									else if ($row_resultados['mes'] == 9) { echo 'Septiembre'; } 
									else if ($row_resultados['mes'] == 10) { echo 'Octubre'; } 
									else if ($row_resultados['mes'] == 11) { echo 'Noviembre'; } 
									else if ($row_resultados['mes'] == 12) { echo 'Diciembre'; } 
								?>
							</div>
						  </td>
                          <td><?php echo KT_FormatForList($row_resultados['usuario_folio'], 60); ?></td>
                          <td><a href="admin_usuarios_edit.php?IDusuario=<?php echo $row_resultados['IDusuario']; ?>"><?php echo KT_FormatForList($row_resultados['usuario_nombre'], 200); ?></a></td>
                         <td><?php echo KT_FormatForList($row_resultados['usuario_parterno'], 200); ?></td>
                         <td><?php echo KT_FormatForList($row_resultados['usuario_materno'], 200); ?></td>
                         <td><?php echo KT_FormatForList($row_resultados['examen_nombre'], 200); ?></td>
                         <td>
                          <?php 
						  $usuario = $row_resultados['IDusuario'];
						  if ($row_resultados['IDexamen'] == 1) { 
                          mysql_select_db($database_examenesz, $examenesz);
                          $query_res_disc = "SELECT * FROM disc_resultados WHERE IDUsuario = '$usuario'";
                          $res_disc = mysql_query($query_res_disc, $examenesz) or die(mysql_error());
                          $row_res_disc = mysql_fetch_assoc($res_disc);
                          $totalRows_res_disc = mysql_num_rows($res_disc);
						   ?>
                           <a class="btn btn-success" href="admin_disc_edit.php?IDResultado=<?php echo $row_res_disc['IDResultado']; ?>">Resultado</a>
                           <?php } else if ($row_resultados['IDexamen'] == 2) {
						  mysql_select_db($database_examenesz, $examenesz);
                          $query_res_disc = "SELECT * FROM tm_resultados WHERE IDUsuario = '$usuario'";
                          $res_disc = mysql_query($query_res_disc, $examenesz) or die(mysql_error());
                          $row_res_disc = mysql_fetch_assoc($res_disc);
                          $totalRows_res_disc = mysql_num_rows($res_disc);
						   ?>
                           <a class="btn btn-success" href="admin_terman_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Resultado</a>
                           <?php } else if ($row_resultados['IDexamen'] == 3) {
						  mysql_select_db($database_examenesz, $examenesz);
                          $query_res_disc = "SELECT * FROM zv_resultados WHERE IDUsuario = '$usuario'";
                          $res_disc = mysql_query($query_res_disc, $examenesz) or die(mysql_error());
                          $row_res_disc = mysql_fetch_assoc($res_disc);
                          $totalRows_res_disc = mysql_num_rows($res_disc);
						   ?>
                           <a class="btn btn-success" href="admin_zavic_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Resultado</a>
                           <?php } else if ($row_resultados['IDexamen'] == 4) {
						  mysql_select_db($database_examenesz, $examenesz);
                          $query_res_disc = "SELECT * FROM jk_resultados WHERE IDUsuario = '$usuario'";
                          $res_disc = mysql_query($query_res_disc, $examenesz) or die(mysql_error());
                          $row_res_disc = mysql_fetch_assoc($res_disc);
                          $totalRows_res_disc = mysql_num_rows($res_disc);
						   ?>
                           <a class="btn btn-success" href="admin_jackson_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Resultado</a>
                           <?php } else if ($row_resultados['IDexamen'] == 7) {
						  mysql_select_db($database_examenesz, $examenesz);
                          $query_res_disc = "SELECT * FROM rd_resultados WHERE IDUsuario = '$usuario'";
                          $res_disc = mysql_query($query_res_disc, $examenesz) or die(mysql_error());
                          $row_res_disc = mysql_fetch_assoc($res_disc);
                          $totalRows_res_disc = mysql_num_rows($res_disc);
						   ?>
                           <a class="btn btn-success" href="admin_reddit_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Resultado</a>
                           <?php } else if ($row_resultados['IDexamen'] == 5) {
						  mysql_select_db($database_examenesz, $examenesz);
                          $query_res_disc = "SELECT * FROM ipv_resultados WHERE IDUsuario = '$usuario'";
                          $res_disc = mysql_query($query_res_disc, $examenesz) or die(mysql_error());
                          $row_res_disc = mysql_fetch_assoc($res_disc);
                          $totalRows_res_disc = mysql_num_rows($res_disc);
						   ?>
                           <a class="btn btn-success" href="admin_ipv_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Resultado</a>
                           <?php } else if ($row_resultados['IDexamen'] == 6) {?> 
                           <a class="btn btn-success" href="admin_moss_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Resultado</a>
                           <?php } else { ?> 
                           <a class="btn btn-success" href="admin_resultados_edit.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Resultado</a>
                           <a class="btn btn-default" href="admin_editar_respuesta_print.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Imprimir</a> 
                           <?php if ($abiertas > 0) { ?>
                           <a class="btn btn-info" href="admin_resultados_preguntas.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Preguntas</a>
                           <?php } else {?> 
                           <a class="btn btn-info" href="admin_resultados_preguntas.php?IDresultado=<?php echo $row_resultados['IDresultado']; ?>">Preguntas</a>
                           <?php } }  ?>  
                    <!-- Metodo de borrado-->
  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#message<?php echo $row_resultados['IDresultado']; ?>">Borrar</button>
  <div id="message<?php echo $row_resultados['IDresultado']; ?>" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Confirmar borrado</h4>
          </div>
        <div class="modal-body">
          <p>¿Estas seguro de que quieres borrar el resultado?</p>
          <p> Se borrarán las respuestas de usuario también.</p>
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          <a class="btn btn-success" href="admin_resultados_edit.php?IDresultado_=<?php echo $row_resultados['IDresultado']; ?>">Si borrar</a>
          </div>
        </div>
      </div>
  </div>                   
                           
                          <!--  salida --></td>
                        </tr>
                        <?php } while ($row_resultados = mysql_fetch_assoc($resultados)); ?>
                      <?php } // Show if recordset not empty ?>
                  </tbody>
                </table>
                <div class="KT_bottomnav">
                  <div>
                    <?php
            $nav_listresultados1->Prepare();
            require("includes/nav/NAV_Text_Navigation.inc.php");
          ?>
                  </div>
                </div>
                <div class="KT_bottombuttons">
                  <div class="KT_operations"></div>
                </div>
              </form>
            </div>
            <br class="clearfixplain" />
          </div>
          <p>&nbsp;</p>
          </p>
<p>&nbsp;</p>
      </div>
</div>
      <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
              <h4 class="modal-title" id="myModalLabel">Estas seguro de querer salir del sistema?</h4>
            </div>
            <div class="modal-footer"> <a href="logout.php" class="btn btn-primary">Si</a>
              <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
          </div>
        </div>
    </div>
    <?php include_once('menus/footer.php'); ?>
  </div>
</div>
</body>
</html>