<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//start Trigger_WelcomeEmail trigger
//remove this line if you want to edit the code by hand
function Trigger_WelcomeEmail(&$tNG) {
  $emailObj = new tNG_Email($tNG);
  $emailObj->setFrom("{KT_defaultSender}");
  $emailObj->setTo("{usuario_correo}");
  $emailObj->setCC("");
  $emailObj->setBCC("");
  $emailObj->setSubject("Welcome");
  //FromFile method
  $emailObj->setContentFile("includes/mailtemplates/welcome.html");
  $emailObj->setEncoding("ISO-8859-1");
  $emailObj->setFormat("HTML/Text");
  $emailObj->setImportance("Normal");
  return $emailObj->Execute();
}
//end Trigger_WelcomeEmail trigger

//start Trigger_ActivationEmail trigger
//remove this line if you want to edit the code by hand
function Trigger_ActivationEmail(&$tNG) {
  $emailObj = new tNG_Email($tNG);
  $emailObj->setFrom("{KT_defaultSender}");
  $emailObj->setTo("{usuario_correo}");
  $emailObj->setCC("");
  $emailObj->setBCC("");
  $emailObj->setSubject("Activation");
  //FromFile method
  $emailObj->setContentFile("includes/mailtemplates/activate.html");
  $emailObj->setEncoding("ISO-8859-1");
  $emailObj->setFormat("HTML/Text");
  $emailObj->setImportance("Normal");
  return $emailObj->Execute();
}
//end Trigger_ActivationEmail trigger

// Start trigger
$formValidation = new tNG_FormValidation();
$formValidation->addField("password", true, "text", "", "", "", "");
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
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
// Make an insert transaction instance
$userRegistration = new tNG_insert($conn_vacantes);
$tNGs->addTransaction($userRegistration);
// Register triggers
$userRegistration->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$userRegistration->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$userRegistration->registerTrigger("END", "Trigger_Default_Redirect", 99, "{kt_login_redirect}");
$userRegistration->registerTrigger("AFTER", "Trigger_WelcomeEmail", 40);
$userRegistration->registerTrigger("AFTER", "Trigger_ActivationEmail", 40);
// Add columns
$userRegistration->setTable("vac_usuarios");
$userRegistration->addColumn("IDmatrizes", "STRING_TYPE", "POST", "IDmatrizes");
$userRegistration->addColumn("IDusuario", "NUMERIC_TYPE", "POST", "IDusuario");
$userRegistration->addColumn("usuario", "STRING_TYPE", "POST", "usuario");
$userRegistration->addColumn("password", "STRING_TYPE", "POST", "password");
$userRegistration->addColumn("usuario_folio", "NUMERIC_TYPE", "POST", "usuario_folio");
$userRegistration->addColumn("usuario_correo", "STRING_TYPE", "POST", "usuario_correo");
$userRegistration->addColumn("usuario_nombre", "STRING_TYPE", "POST", "usuario_nombre");
$userRegistration->addColumn("usuario_parterno", "STRING_TYPE", "POST", "usuario_parterno");
$userRegistration->addColumn("usuario_materno", "STRING_TYPE", "POST", "usuario_materno");
$userRegistration->addColumn("usuario_telefono", "STRING_TYPE", "POST", "usuario_telefono");
$userRegistration->addColumn("nivel_acceso", "NUMERIC_TYPE", "POST", "nivel_acceso");
$userRegistration->addColumn("activo", "NUMERIC_TYPE", "POST", "activo");
$userRegistration->addColumn("borrado", "NUMERIC_TYPE", "POST", "borrado");
$userRegistration->addColumn("IDusuario_puesto", "NUMERIC_TYPE", "POST", "IDusuario_puesto");
$userRegistration->addColumn("IDmatriz", "STRING_TYPE", "POST", "IDmatriz");
$userRegistration->setPrimaryKey("IDusuario", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsvac_usuarios = $tNGs->getRecordset("vac_usuarios");
$row_rsvac_usuarios = mysql_fetch_assoc($rsvac_usuarios);
$totalRows_rsvac_usuarios = mysql_num_rows($rsvac_usuarios);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script src="includes/common/js/base.js" type="text/javascript"></script>
<script src="includes/common/js/utility.js" type="text/javascript"></script>
<script src="includes/skins/style.js" type="text/javascript"></script>
<?php echo $tNGs->displayValidationRules();?>
</head>

<body>
<?php
	echo $tNGs->getErrorMsg();
?>
<form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
  <table cellpadding="2" cellspacing="0" class="KT_tngtable">
    <tr>
      <td class="KT_th"><label for="IDmatrizes">IDmatrizes:</label></td>
      <td><input type="text" name="IDmatrizes" id="IDmatrizes" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['IDmatrizes']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("IDmatrizes");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "IDmatrizes"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="IDusuario">IDusuario:</label></td>
      <td><input type="text" name="IDusuario" id="IDusuario" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['IDusuario']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("IDusuario");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "IDusuario"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="usuario">Usuario:</label></td>
      <td><input type="text" name="usuario" id="usuario" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['usuario']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("usuario");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "usuario"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="password">Password:</label></td>
      <td><input type="text" name="password" id="password" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['password']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("password");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "password"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="usuario_folio">Usuario_folio:</label></td>
      <td><input type="text" name="usuario_folio" id="usuario_folio" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['usuario_folio']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("usuario_folio");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "usuario_folio"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="usuario_correo">Usuario_correo:</label></td>
      <td><input type="text" name="usuario_correo" id="usuario_correo" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['usuario_correo']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("usuario_correo");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "usuario_correo"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="usuario_nombre">Usuario_nombre:</label></td>
      <td><input type="text" name="usuario_nombre" id="usuario_nombre" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['usuario_nombre']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("usuario_nombre");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "usuario_nombre"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="usuario_parterno">Usuario_parterno:</label></td>
      <td><input type="text" name="usuario_parterno" id="usuario_parterno" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['usuario_parterno']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("usuario_parterno");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "usuario_parterno"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="usuario_materno">Usuario_materno:</label></td>
      <td><input type="text" name="usuario_materno" id="usuario_materno" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['usuario_materno']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("usuario_materno");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "usuario_materno"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="usuario_telefono">Usuario_telefono:</label></td>
      <td><input type="text" name="usuario_telefono" id="usuario_telefono" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['usuario_telefono']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("usuario_telefono");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "usuario_telefono"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="nivel_acceso">Nivel_acceso:</label></td>
      <td><input type="text" name="nivel_acceso" id="nivel_acceso" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['nivel_acceso']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("nivel_acceso");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "nivel_acceso"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="activo">Activo:</label></td>
      <td><input type="text" name="activo" id="activo" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['activo']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("activo");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "activo"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="borrado">Borrado:</label></td>
      <td><input type="text" name="borrado" id="borrado" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['borrado']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("borrado");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "borrado"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="IDusuario_puesto">IDusuario_puesto:</label></td>
      <td><input type="text" name="IDusuario_puesto" id="IDusuario_puesto" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['IDusuario_puesto']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("IDusuario_puesto");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "IDusuario_puesto"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="IDmatriz">IDmatriz:</label></td>
      <td><input type="text" name="IDmatriz" id="IDmatriz" value="<?php echo KT_escapeAttribute($row_rsvac_usuarios['IDmatriz']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("IDmatriz");?> <?php echo $tNGs->displayFieldError("vac_usuarios", "IDmatriz"); ?></td>
    </tr>
    <tr class="KT_buttons">
      <td colspan="2"><input type="submit" name="KT_Insert1" id="KT_Insert1" value="Register" /></td>
    </tr>
  </table>
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($variables);
?>
