<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');

// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->Execute();
//End Restrict Access To Page

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

// Start trigger
$formValidation = new tNG_FormValidation();
$tNGs->prepareValidation($formValidation);
// End trigger

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
$mis_areas = $row_usuario['IDmatrizes'];$la_matriz = $row_usuario['IDmatriz'];

$colname_usuario_ = "-1";
if (isset($_GET['IDusuario'])) {
  $colname_usuario_ = $_GET['IDusuario'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario_ = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario_, "int"));
$usuario_ = mysql_query($query_usuario_, $vacantes) or die(mysql_error());
$row_usuario_ = mysql_fetch_assoc($usuario_);
$totalRows_usuario_ = mysql_num_rows($usuario_);

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];


mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);
$la_area = $row_area['area']; 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz_ = "SELECT * FROM vac_matriz";
$matriz_ = mysql_query($query_matriz_, $vacantes) or die(mysql_error());
$row_matriz_ = mysql_fetch_assoc($matriz_);
$totalRows_matriz_ = mysql_num_rows($matriz_);
$la_matriz_ = $row_matriz_['matriz']; 

// Make an update transaction instance
$upd_vac_usuarios = new tNG_update($conn_vacantes);
$tNGs->addTransaction($upd_vac_usuarios);
// Register triggers
$upd_vac_usuarios->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_vac_usuarios->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_vac_usuarios->registerTrigger("END", "Trigger_Default_Redirect", 99, "master_admin_usuarios_edit.php?IDusuario=$colname_usuario_&info=2");
// Add columns
$upd_vac_usuarios->setTable("vac_usuarios");
$upd_vac_usuarios->addColumn("IDareas", "STRING_TYPE", "POST", "IDareas");
$upd_vac_usuarios->setPrimaryKey("IDusuario", "NUMERIC_TYPE", "GET", "IDusuario");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsvac_usuarios = $tNGs->getRecordset("vac_usuarios");
$row_rsvac_usuarios = mysql_fetch_assoc($rsvac_usuarios);
$totalRows_rsvac_usuarios = mysql_num_rows($rsvac_usuarios);
?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /theme JS files -->

	<script src="includes/common/js/base.js" type="text/javascript"></script>
	<script src="includes/common/js/utility.js" type="text/javascript"></script>
	<script src="includes/skins/style.js" type="text/javascript"></script>

	<script type="text/javascript" src="includes/common/js/sigslot_core.js"></script>
	<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js"></script>
	<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js.php"></script>
	<script type="text/javascript" src="includes/wdg/classes/JSRecordset.js"></script>
	<script type="text/javascript" src="includes/wdg/classes/CommaCheckboxes.js"></script>
	
<?php
//begin JSRecordset
$jsObject_area = new WDG_JsRecordset("area");
echo $jsObject_area->getOutput();
//end JSRecordset
?>
</head>

<body class="has-detached-right">	<?php require_once('assets/mainnav.php'); ?>
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
							<h5 class="panel-title">Asignar Áreas (usuarios vista)</h5>
						</div>

					<div class="panel-body">
							<p>Actualiza la información del usuario.</p>
                            <p>&nbsp;
                             
                             

                             
                      <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" class="form-horizontal form-validate-jquery">
                            
                                                               <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario:</label>
										<div class="col-lg-9">
						                  <p><strong><?php echo $row_usuario_['usuario']; ?> </strong></p>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:</label>
										<div class="col-lg-9">
						<?php echo $row_usuario_['usuario_nombre'] . " " . $row_usuario_['usuario_parterno'] . " ". $row_usuario_['usuario_materno']; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Áreas:</label>
										<div class="col-lg-9">
						<input name="IDareas" id="IDareas" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['IDareas']); ?>"
                        wdg:recordset="area" wdg:subtype="CommaCheckboxes" wdg:type="widget" wdg:displayfield="area" wdg:valuefield="IDarea" wdg:groupby="3" />
										</div>
									</div>
									<!-- /basic text input -->
                              
            <input type="submit" name="KT_Update1" id="KT_Update1"  value="Asigar Áreas" class="btn bg-indigo btn-icon" />
             <button type="button" onClick="window.location.href='master_admin_usuarios_edit.php?IDusuario=<?php echo $colname_usuario_; ?>'" class="btn btn-default btn-icon">Regresar</button>
                            </form>
                            <p>&nbsp;</p>
                            </p>
<p>&nbsp;</p>
                    </div>

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
	<?php
	echo $tNGs->getErrorMsg();
?>
</body>
</html>