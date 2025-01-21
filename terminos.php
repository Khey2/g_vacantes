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

// Start trigger
$formValidation = new tNG_FormValidation();
$formValidation->addField("kt_login_user", true, "text", "", "", "", "Ingresa tu usuario");
$formValidation->addField("kt_login_password", true, "text", "", "", "", "Ingresa tu Password");
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
// Make a login transaction instance
$loginTransaction = new tNG_login($conn_vacantes);
$tNGs->addTransaction($loginTransaction);
// Register triggers
$loginTransaction->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "kt_login1");
$loginTransaction->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$loginTransaction->registerTrigger("END", "Trigger_Default_Redirect", 99, "{kt_login_redirect}");
// Add columns
$loginTransaction->addColumn("kt_login_user", "STRING_TYPE", "POST", "kt_login_user");
$loginTransaction->addColumn("kt_login_password", "STRING_TYPE", "POST", "kt_login_password");
// End of login transaction instance

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rscustom = $tNGs->getRecordset("custom");
$row_rscustom = mysql_fetch_assoc($rscustom);
$totalRows_rscustom = mysql_num_rows($rscustom);
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema'];?></title>

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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/login.js"></script>
	<!-- /theme JS files -->

</head>

<body class="login-container bg-slate-800">
	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.php"><img src="global_assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

	</div>
	<!-- /main navbar -->

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Content area -->
				<div class="content">

					<!-- Advanced login -->
					<!-- /advanced login -->
                  <h1>Términos y condiciones de uso del servicio</h1>
                    <p>1. Introducción</p>
                    <p>Esta página establece los &quot;términos y condiciones&quot; bajo los cuales usted acepta utilizar el Sistema de Gestión de Recursos Humanos de Sahuayo disponible online a través de cualquier dispositivo. Por favor lea esta página cuidadosamente. Si usted no está de acuerdo con los términos y condiciones establecidos aquí, no utilice este sitio de Internet,  ni los servicios proporcionados por el Sistema de Gestión de Recursos Humanos de Sahuayo.</p>
                    <p>2. Aceptación de Términos</p>
                    <p>Al utilizar el sitio de Internet, descargar e instalar la aplicación , usted acepta obligarse a estos términos y condiciones. Los términos y condiciones pueden ser actualizados por el Sistema de Gestión de Recursos Humanos de Sahuayo en cualquier momento y sin previo aviso por lo que debe visitar ésta página periódicamente para consultar la versión más reciente de los términos y condiciones. Los términos &quot;usted&quot; y &quot;usuario&quot; según se utilizan aquí hacen referencia a todas las personas y/o entidades que acceden a este sitio de Internet, su aplicación , widget o cualquier plataforma desarrollada por el Sistema de Gestión de Recursos Humanos de Sahuayo.</p>
                    <p>3. Acuerdo Obligatorio</p>
                    <p>Estos términos forman un acuerdo obligatorio entre usted y el Sistema de Gestión de Recursos Humanos de Sahuayo. el Sistema de Gestión de Recursos Humanos de Sahuayo se reserva el derecho de cambiar estas reglas en cualquier momento y sin necesidad de notificación previa. El acceso y uso a el Sistema de Gestión de Recursos Humanos de Sahuayo indica su aceptación de estos términos. Asimismo, usted acuerda utilizar el Sistema de Gestión de Recursos Humanos de Sahuayo a su propio riesgo. Tratándose de contratación electrónica de servicios, ésta se perfeccionará desde que se reciba la aceptación de la propuesta o las condiciones con que ésta fuere modificada.</p>
                    <p>4. Descripción del Servicio</p>
                    <p>El servicio que se presta a través del sitio web el Sistema de Gestión de Recursos Humanos de Sahuayo operado por Recursos Humanos de Grupo Sahuayo (en adelante el Sistema de Gestión de Recursos Humanos de Sahuayo) quién es su propietario, es exclusivamente proporcionar una licencia de uso limitada para que usuarios del Sistema de Gestión de Recursos Humanos de Sahuayo administren a empleados de Sahuayo en la plataforma, y a su vez candidatos publiquen sus solicitudes de empleo en el sistema. En virtud de lo anterior, el Sistema de Gestión de Recursos Humanos de Sahuayo no es responsable por el contenido o veracidad de las publicaciones realizadas por los usuarios en el Sitio Web.</p>
                    <p>5. Comunicaciones</p>
                    <p>Queda establecido que usted entiende y acepta que el servicio puede incluir ciertas comunicaciones del Sistema de Gestión de Recursos Humanos de Sahuayo, como mensajes, avisis y correos transaccionales, estas comunicaciones son consideradas como parte del servicio de el Sistema de Gestión de Recursos Humanos de Sahuayo y usted no puede excluir la recepción de dichas comunicaciones.<br />
                      En el caso de que un usuario reciba comunicaciones de este sitio web o su aplicación  sin haberse registrado, o sin haber dado su consentimiento expreso a dicho registro, puede cancelar la suscripción de conformidad al procedimiento establecido en el Aviso de Privacidad de el Sistema de Gestión de Recursos Humanos de Sahuayo.</p>
                    <p>Asimismo, la utilización de este sitio web o su aplicación  implica comunicaciones vía correos entre candidatos a un puesto y Jefes de Recursos Humanos, dichas comunicaciones podrían ser monitoreadas por el Sistema de Gestión de Recursos Humanos de Sahuayo a su entera discreción, por lo que no deben ser consideradas en ningún momento como comunicaciones privadas.</p>
                    <p>6. Reglas de Uso General del Sitio el Sistema de Gestión de Recursos Humanos de Sahuayo </p>
                    <p>El portal está previsto para individuos que buscan trabajo y para usuarios de diversas áreas de la Organización que tienen contacto con vandidatos o empleados. Usted debe usar este Sitio Web con intenciones legítimas y de acuerdo con las indicaciones de uso del Sitio. Recursos Humanos de Sahuayo es la única entidad que interpretará el uso aceptable del Sitio Web .</p>
                    <p>7. Licencia de uso para usuarios</p>
                    <p>El Sistema de Gestión de Recursos Humanos de Sahuayo otorga, durante la vigencia de los servicios contratados, una licencia de uso revocable, de responsabilidad limitada, terminable y con derechos de no exclusividad para accesar y usar el Sitio Web del Sistema de Gestión de Recursos Humanos de Sahuayo para el uso interno del área de Recursos Humanos en la búsqueda de candidatos para ofrecerles empleo. Esto le autoriza al área de Recursos Humanos a ver el material del Sitio Web con el único propósito de buscar  candidatos. el Sistema de Gestión de Recursos Humanos de Sahuayo se reserva el derecho de suspender temporal o definitivamente sus claves de acceso o dar por terminado su acceso si se determina que existe un incumplimiento de cualquiera de estos términos y condiciones. Usted no podrá comercializar ni revender en forma alguna los servicios o porciones de éstos. Estos términos se hacen extensibles al uso de todas las modalidades y herramientas del sitio.</p>
                    <p><br />
                      El Sistema de Gestión de Recursos Humanos de Sahuayo otorga al usuariouna clave de usuario, cuyo titular será responsable de la existencia y veracidad de la información de la cuenta, así como por el uso que dichas claves den al servicio utilizado.</p>
                    <p>8. Transferencia de datos personales</p>
                    <p>De conformidad con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares vigente en México, el Sistema de Gestión de Recursos Humanos de Sahuayo es responsable transferente y el área de Recursos Humanos se convierte en responsable receptor, por lo que, en cumplimiento con las disposiciones de la materia, el área de Recursos Humanos, se obliga a:</p>
                    <ul>
                      <li>Mantener la confidencialidad de los datos personales, utilizándolos únicamente para efectos de reclutamiento y selección de personal y administración de empelados con ningún otro propósito.</li>
                      <li>No transferir a ningún tercero, datos personales que recibe en su calidad de responsable receptor, salvo por lo dispuesto en el artículo 70 de reglamento de LFPDPPP.</li>
                      <li>Garantizar que solo sus empleados tendrán acceso a los datos transferidos y que ninguno de ellos dará un tratamiento distinto al de reclutamiento y selección de personal.</li>
                      <li>El Sistema de Gestión de Recursos Humanos de Sahuayo se deslinda de cualquier responsabilidad presente o futura, que surja del uso no convenido de los datos personales que le han sido transferidos a los usuarios, por lo que en caso de contravenir estas disposiciones se obligan a sacar en paz y a salvo al Sistema de Gestión de Recursos Humanos de Sahuayo, pagando los daños y perjuicios causados por esta situación.</li>
                    </ul>
                    <p>9. Licencia de uso para Candidatos</p>
                    <p>El Sistema de Gestión de Recursos Humanos de Sahuayo otorga un acuerdo de responsabilidad limitada, terminable y con derechos de no exclusividad para accesar y usar el Sitio Web para el uso personal en la búsqueda de oportunidades de empleo para usted mismo. Esto le autoriza a usted a ver el material del Sitio Web solamente para su uso personal y no para uso comercial con fines de lucro. El uso del Sitio Web es un privilegio. El Sistema de Gestión de Recursos Humanos de Sahuayo se reserva el derecho de suspender o terminar este privilegio por cualquier razón y en cualquier momento. </p>
                    <p>10. Otras Reglas Particulares de Uso del Sitio Web </p>
                    <p>Usted representa, garantiza y acepta que usted no usará (o planeará, motivará o ayudará a otros a usar) el Sitio Web para cualquier otro propósito o que en cualquier manera esté prohibido por los términos aquí mencionados o los que sean aplicables por ley. Es su responsabilidad asegurar que usted use el Sitio Web de acuerdo con los términos y condiciones aquí especificados.</p>
                    <p>11. Reglas de Publicación, Conducta y Seguridad</p>
                    <p>Usted acepta cumplir con las reglas de el Sistema de Gestión de Recursos Humanos de Sahuayo para la administración de empleados, de Conducta y de Seguridad en este Sitio Web . Los usuarios que violen dichas reglas tendrán el uso y el acceso del sitio suspendido o cancelado a discreción exclusiva de el Sistema de Gestión de Recursos Humanos de Sahuayo. </p>
                    <p>12. Opiniones y/o Comentarios:</p>
                    <p>Los comentarios y opiniones expresadas en los apartados disponibles son formulados por personas ajenas a el Sistema de Gestión de Recursos Humanos de Sahuayo, bajo su única y exclusiva responsabilidad. Todas las personas que accedan a este sitio web y su aplicación  asumen la calidad de usuarios, y por ende se comprometen a la observancia y cumplimiento de estas disposiciones. Los participantes de dichos espacios se comprometen a utilizar los mismos en conformidad con la ley, estas condiciones generales, así como con la moral y buenas costumbres generalmente aceptadas. el Sistema de Gestión de Recursos Humanos de Sahuayo se exime de cualquier tipo de responsabilidad derivada de la información, opiniones, comentarios, ideas u otros contenidos realizados por los visitantes en su sitio web . Las opiniones realizadas por los usuarios en este sitio web no podrán contener elementos obscenos y/o insultos, ni información que supongan una vulneración de derechos de terceros y, en particular, de su honor, intimidad o propia imagen. Tampoco se permitirán comentarios que fueren difamatorios, injuriosos, calumniosos, obscenos, amenazadores, discriminatorios, o bien que inciten a la violencia. el Sistema de Gestión de Recursos Humanos de Sahuayo no es responsable de la veracidad y exactitud de las opiniones expresadas en el sitio web por los usuarios, quedando exentos de cualquier responsabilidad contractual o extracontractual con la persona o empresa que haga uso de ellos. Se prohíbe el envío de cualquier contenido u opinión que vulnere la legislación vigente y/o derechos legítimos de otras personas. Asimismo, el Sistema de Gestión de Recursos Humanos de Sahuayo se reserva el derecho de admitir o dar de baja a cualquier usuario, así como de omitir, suprimir o editar parcial o totalmente, sin previo aviso y sin ningún tipo de responsabilidad con los usuarios, todos aquellos contenidos o comentarios que considere inadecuados o vayan contra la ley y/o las buenas costumbres. Los comentarios realizados por los usuarios en general pasan a formar parte del sitio web de el Sistema de Gestión de Recursos Humanos de Sahuayo, por lo que los usuarios otorgan a el Sistema de Gestión de Recursos Humanos de Sahuayo un derecho no exclusivo sobre los mismos, libre de regalías, perpetuo e irrevocable para usar, reproducir, modificar, y publicar su contenido por cualquier medio y para cualquier fin, no obstante, dichos comentarios no son ni serán responsabilidad de el Sistema de Gestión de Recursos Humanos de Sahuayo, si no de los usuarios que los realizan. el Sistema de Gestión de Recursos Humanos de Sahuayo no estará obligado en ningún caso a evaluar, editar o monitorear el contenido publicado en el sitio web o por los usuarios, por lo que no se responsabiliza ni asume responsabilidad alguna sobre el contenido publicado o cargado por los usuarios o por algún tercero, ni por errores, difamación, calumnias, exposiciones, falsedades o profanidades que pudiesen ser publicadas en el sitio web o su aplicación . Para la participación de los usuarios en el sitio web será requerido el correo electrónico, mismo que será tratado bajo los términos establecidos en el Aviso de Privacidad de el Sistema de Gestión de Recursos Humanos de Sahuayo.</p>
                    <p>13. Derechos de Propiedad Intelectual:</p>
                    <p>El Sitio Web, Aplicación  y todos sus derechos, título e intereses son propiedad única de Grupo Sahuayo y se encuentran protegidos por las leyes mexicanas de derechos de autor y de los tratados internacionales. A excepción de las licencias de uso limitado expresamente otorgadas a usted en estos términos, el Sistema de Gestión de Recursos Humanos de Sahuayo se reserva para el mismo y sus licenciatarios todos los derechos, títulos e intereses, sin límite de propiedad en lo aquí mencionado usted no puede reproducir, modificar, mostrar, vender o distribuir el contenido, o usarlo en cualquier otra forma para uso público o comercial. Esto incluye copiar o adaptar código HTML usado para generar páginas Web en el Sitio Web . El logotipo de el Sistema de Gestión de Recursos Humanos de Sahuayo y otros nombres y logotipos son marcas de servicio y marcas registradas de Grupo Sahuayo y todos los productos y nombres de servicio, diseño de marcas y slogans publicitarios son marcas registradas. </p>
                    <p>14. Limitación de responsabilidad de el Sistema de Gestión de Recursos Humanos de Sahuayo:</p>
                    <p>El Sistema de Gestión de Recursos Humanos de Sahuayo no asume ninguna responsabilidad por materiales publicados en el Sitio Web por los usuarios y no tiene responsabilidad por sus actividades, omisiones o conducta de los usuarios. <br />
                      Por ningún motivo los servicios prestados por el Sistema de Gestión de Recursos Humanos de Sahuayo deben entenderse como servicios conocidos como outsourcing, agencia de colocación, manejo de nóminas, ni ningún servicio similar, por lo que el Sistema de Gestión de Recursos Humanos de Sahuayo no puede ser considerado por ningún medio o forma como patrón sustituto en términos de la Ley Federal del Trabajo vigente en los Estados Unidos Mexicanos. Los anuncios de empleo publicados en el sitio web , y las relaciones individuales de trabajo que puedan derivar de ellas, son exclusiva responsabilidad de la persona que lleva a cabo su publicación, motivo por el cual, cualquier persona que publique un anuncio de empleo, libera desde este momento y en el futuro a el Sistema de Gestión de Recursos Humanos de Sahuayo de cualquier responsabilidad derivada de la Ley Federal del Trabajo vigente en los Estados Unidos Mexicanos.<br />
                    </p>
                    <p>15. Exclusión de Anexos por el Sistema de Gestión de Recursos Humanos de Sahuayo:</p>
                    <p>Nada en el Sitio Web debe ser considerado un anexo, representación o garantías con respecto a cualquier usuario o un tercero, ya sea en relación con el Sitio Web, a sus productos, servicios, contrataciones, experiencia, empleo, prácticas de reclutamiento u otras.</p>
                    <p>16. Exclusión de Garantías y de responsabilidad:</p>
                    <p>El Sistema de Gestión de Recursos Humanos de Sahuayo  no son una agencia de empleos ni una firma de reclutamiento, y no tiene ninguna representación ni garantiza la efectividad o el tiempo en la obtención de empleo para los usuarios. el Sistema de Gestión de Recursos Humanos de Sahuayo y HS GROUP S.R.L. no garantizan los materiales publicados en este sitio web y su aplicación  por usuarios que resulten candidatos contratados o por puestos por cubrir y no es responsable de ninguna decisión sobre un empleo, por cualquier razón hecho por cualquier usuario.</p>
                    <p>17. Exclusión de errores y precisión de los materiales publicados en el Sitio Web :</p>
                    <p>el Sistema de Gestión de Recursos Humanos de Sahuayo no garantiza la veracidad, exactitud, vigencia o confiabilidad de ninguno de los materiales publicados por los usuarios, o por cualquier otra forma de comunicación que sea comprometida por los usuarios. Los materiales pueden contener inexactitudes o errores tipográficos. Usted acepta que cualquier consecuencia en materiales publicados por los usuarios, o en cualquier otra forma de comunicación con los usuarios, será a su propio riesgo. Adicionalmente el Sistema de Gestión de Recursos Humanos de Sahuayo no garantiza el contenido del Sitio Web , incluyendo responsabilidad limitada hacia ligas que no funcionen, inexactitudes o errores tipográficos.</p>
                    <p>18. Ligas a otros sitios:</p>
                    <p>el Sistema de Gestión de Recursos Humanos de Sahuayo y sus agentes asociados contienen ligas o hipervínculos a Sitios de terceros. Estás ligas son proporcionadas para conveniencia de Usted y los contenidos no son avalados por el Sistema de Gestión de Recursos Humanos de Sahuayo. el Sistema de Gestión de Recursos Humanos de Sahuayo no es responsable por el contenido de esos sitios ni por la exactitud de sus materiales. Si usted decide accesar a esos sitios es bajo su responsabilidad.</p>
                    <p>19. Notificaciones:</p>
                    <p>El uso de la plataforma el Sistema de Gestión de Recursos Humanos de Sahuayo implica la aceptación a la recepción de notificaciones para los usuarios mediante diversos medios, tales como notificaciones de escritorio, vía WhatsApp o SMS, a través de correo electrónico, o cualquier otro medio con el cual sea posible contactar al usuario con la información proporcionada en el Sitio Web.</p>
                    <p>20. Enmiendas a este acuerdo y Cambios al Sitio Web:</p>
                    <p>El Sistema de Gestión de Recursos Humanos de Sahuayo podrá revisar estos términos en cualquier momento y actualizar su contenido. Cualquier uso del Sitio Web se considerará como aceptación de usted de los términos aquí mostrados. Si en cualquier momento usted encuentra los términos inaceptables, usted no deberá usar este Sitio Web . Cualquier término nuevo o diferente proporcionado por usted será específicamente rechazado por el Sistema de Gestión de Recursos Humanos de Sahuayo. El Sistema de Gestión de Recursos Humanos de Sahuayo podrá realizar cambios a estos términos sin previo aviso.<br />
                      El Sistema de Gestión de Recursos Humanos de Sahuayo se reserva el derecho de modificar o descontinuar, temporal o permanentemente, los Servicios o cualquier otra característica que forme parte de estos sin previo aviso. Usted acepta que el Sistema de Gestión de Recursos Humanos de Sahuayo no será responsable por ninguna modificación, suspensión, cambio de modelo o interrupción de los Servicios o cualquier parte de los mismos. En caso de cambio de modelo de los Servicios el Sistema de Gestión de Recursos Humanos de Sahuayo otorgará al usuario el equivalente de servicios en el nuevo modelo.</p>
                    <p>21. Indemnización de el Sistema de Gestión de Recursos Humanos de Sahuayo:</p>
                    <p>Usted acepta defender, indemnizar y declarar exenta de responsabilidad a la Empresa y sus agentes asociados, sus funcionarios, directores, empleados y agentes, frente y contra cualquier reclamación, acción judicial o demanda, incluido sin limitaciones los daños, costos legales y contables derivados o resultantes de cualquier alegato resultado o en conexión con su uso del Sitio Web , de cualquier material publicado por usted o por cualquier incumplimiento a éstos términos. el Sistema de Gestión de Recursos Humanos de Sahuayo le informará de la existencia de tales reclamaciones, demandas o procedimientos judiciales y le asistirá, a su costo, en la defensa de tales reclamaciones, demandas o procedimientos judiciales.</p>
                    <p>22. Terminación:</p>
                    <p>El Sistema de Gestión de Recursos Humanos de Sahuayo se reserva el derecho, a su entera discreción, a perseguir todos los remedios legales, incluyendo, sin limitación, la eliminación de sus publicaciones en este Sitio Web, la terminación inmediata de claves de acceso a este Sitio Web y/o cualesquier otros servicios que le brinde la empresa y sus agentes asociados, debido a cualquier incumplimiento suyo a estos términos y condiciones de uso o si la empresa es incapaz de verificar o autentificar cualquier información que usted presente al sitio de Internet u otros registros.</p>
                    <p>23. Información del usuario:</p>
                    <p>El Aviso de Privacidad de el Sistema de Gestión de Recursos Humanos de Sahuayo incorpora este acuerdo.</p>
                    <p>24. Contacto y preguntas:</p>
                    <p>Preguntas acerca del uso de este Sitio Web deberán ser dirigidas al correo electrónico <a href="jacardenas@sahuayo.mx">jacardenas@sahuayo.mx </a></p>
                  <H6>&nbsp;</H6>
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