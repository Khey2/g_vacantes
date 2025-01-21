<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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

$currentPage = $_SERVER["PHP_SELF"];
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fechapp = date("YmdHis"); // la fecha actual

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if(isset($_POST['IDestatus_f']) && ($_POST['IDestatus_f'] == '1,2,3,4,5,6')) {$_SESSION['IDestatus_f'] = '1,2,3,4,5,6'; } 
else if(isset($_POST['IDestatus_f']) && ($_POST['IDestatus_f'] == '6')) {$_SESSION['IDestatus_f'] = '6'; } 
else if(isset($_POST['IDestatus_f']) && ($_POST['IDestatus_f'] == '1,2,3,4,5')) {$_SESSION['IDestatus_f'] = '1,2,3,4,5'; } 
else { $_SESSION['IDestatus_f'] = '1,2,3,4,5,6'; } 

if(isset($_POST['IDmatriz']) && ($_POST['IDmatriz']  > 0)) {
$_SESSION['IDmatriz'] = $_POST['IDmatriz']; } else { $_SESSION['IDmatriz'] = $IDmatrizes;}


if(isset($_POST['el_anio'])) {$_SESSION['el_anio'] = $_POST['el_anio']; } 
if(!isset($_SESSION['el_anio'])) { $_SESSION['el_anio'] = $anio;}


$la_matriz = $_SESSION['IDmatriz'];
$IDestatus_f = $_SESSION['IDestatus_f']; 
$el_anio = $_SESSION['el_anio'];

mysql_select_db($database_vacantes, $vacantes);
$query_pprueba = "SELECT pp_prueba.file, pp_prueba.IDestatusv, pp_prueba.file2, pp_prueba.file3, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDarea, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, pp_prueba.IDmatriz_destino, pp_prueba.IDarea_destino, pp_prueba.fecha_fin, pp_prueba.fecha_inicio, pp_prueba.fecha_cierre, pp_prueba.IDestatus, pp_prueba.observaciones, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, puesto_origen.denominacion AS denominacion_origen, area_oringen.area AS area_origen, matriz_origen.matriz AS matriz_origen, matriz_destino.matriz as matriz_destino, area_destino.area AS area_destino, puesto_destino.denominacion AS denominacion_destino FROM pp_prueba LEFT JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos AS puesto_origen ON pp_prueba.IDpuesto = puesto_origen.IDpuesto LEFT JOIN vac_areas AS area_oringen ON puesto_origen.IDarea = area_oringen.IDarea LEFT JOIN vac_matriz AS matriz_origen ON pp_prueba.IDmatriz = matriz_origen.IDmatriz LEFT JOIN vac_matriz AS matriz_destino ON pp_prueba.IDmatriz_destino = matriz_destino.IDmatriz LEFT JOIN vac_puestos AS puesto_destino ON pp_prueba.IDpuesto_destino = puesto_destino.IDpuesto LEFT JOIN vac_areas AS area_destino ON puesto_destino.IDarea = area_destino.IDarea WHERE pp_prueba.IDmatriz IN ($la_matriz) AND YEAR(pp_prueba.fecha_inicio) = $el_anio AND pp_prueba.IDestatus IN ($IDestatus_f)"; 
mysql_query("SET NAMES 'utf8'");
$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);

$formatos_permitidos =  array('pdf', 'xlsx', 'xls', 'jpeg', 'png', 'jpg');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
	
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$IDpprueba = $_POST['IDpprueba'];
$IDempleado = $_POST['IDempleado'];
$IDempleado_carpeta = 'PPP/'.$IDempleado;
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}

$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDempleado.$fechapp."FORMATODESEMP".".".$extension;
$targetPath = 'PPP/'.$IDempleado.'/'.$name_new;

if(!in_array($extension, $formatos_permitidos) ) {
header("Location: admin_pprueba.php?info=9");
} else {
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
$updateSQL ="UPDATE pp_prueba SET file = '$name_new', IDestatus = 3 WHERE IDpprueba = $IDpprueba";
mysql_select_db($database_vacantes, $vacantes);
$Result = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location: admin_pprueba.php?info=4");
echo $targetPath;
} }

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
$IDpprueba = $_POST['IDpprueba'];
$IDempleado = $_POST['IDempleado'];
$IDempleado_carpeta = 'PPP/'.$IDempleado;
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}

$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDempleado.$fechapp."NOTIFICACION".".".$extension;
$targetPath = 'PPP/'.$IDempleado.'/'.$name_new;

if(!in_array($extension, $formatos_permitidos) ) {
header("Location: admin_pprueba.php?info=9");
} else {
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
$updateSQL ="UPDATE pp_prueba SET file2 = '$name_new', IDestatus = 4 WHERE IDpprueba = $IDpprueba";
mysql_select_db($database_vacantes, $vacantes);
$Result = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location: admin_pprueba.php?info=5");
echo $targetPath;
} }

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
$IDpprueba = $_POST['IDpprueba'];
$IDempleado = $_POST['IDempleado'];
$IDempleado_carpeta = 'PPP/'.$IDempleado;
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}

$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$IDempleado.$fechapp."AFECTACION".".".$extension;
$targetPath = 'PPP/'.$IDempleado.'/'.$name_new;

if(!in_array($extension, $formatos_permitidos) ) {
header("Location: admin_pprueba.php?info=9");
} else {
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
$updateSQL ="UPDATE pp_prueba SET file3 = '$name_new' WHERE IDpprueba = $IDpprueba";
mysql_select_db($database_vacantes, $vacantes);
$Result = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location: admin_pprueba.php?info=6");
echo $targetPath;
} }

//choferes
$os = array(42, 43, 44, 45, 57, 372);

// autorizado alternativo
if ((isset($_POST['IDpprueba_autorizar'])) && ($_POST['IDpprueba_autorizar'] != "")) {
  
  $IDmover = $_POST['IDestatusv'];
  $IDpp = $_POST['IDpprueba_autorizar'];
  $deleteSQL = "UPDATE pp_prueba SET IDestatusv = $IDmover WHERE IDpprueba = $IDpp";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  
	if ($IDmover == 1){
	  
	mysql_select_db($database_vacantes, $vacantes);
	$query_lpp = "SELECT pp_prueba_pagos.IDempleado, pp_prueba.fecha_inicio, pp_prueba_pagos.fecha_pago, pp_prueba_pagos.semana, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, vac_puestos.denominacion FROM pp_prueba_pagos INNER JOIN pp_prueba ON pp_prueba_pagos.IDpprueba = pp_prueba.IDpprueba INNER JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado INNER JOIN vac_puestos ON pp_prueba.IDpuesto_destino = vac_puestos.IDpuesto WHERE pp_prueba.IDpprueba = $IDpp";
	$lpp = mysql_query($query_lpp, $vacantes) or die(mysql_error());
	$row_lpp = mysql_fetch_assoc($lpp);
	$totalRows_lpp = mysql_num_rows($lpp);

		if (in_array($row_lpp['IDpuesto_destino'], $os)) {
			
			//se envia correo
			require 'assets/PHPMailer/PHPMailerAutoload.php';

	
			$mail = new PHPMailer;
			//$mail->isSMTP();
			$mail->SMTPDebug = 0;
			$mail->Debugoutput = 'html';
			$mail->Host = "smtp.office365.com";
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->SMTPAutoTLS = false;
			$mail->SMTPSecure = '';
			$mail->Username = "reporte_diario@gestionvacantes.com";
			$mail->Password = "parazoom2020!";
			$mail->setFrom('reporte_diario@gestionvacantes.com', 'Sistema de Gestion de Recursos Humanos');
			$mail->addReplyTo('reporte_diario@gestionvacantes.com', 'Recursos Humanos');
			$mail->AddAddress('jacardenas@sahuayo.mx');
			$mail->AddAddress('mahernandez@sahuayo.mx');
			$mail->AddAddress('nmarin@sahuayo.mx'); 
			$mail->Subject = 'Programación de Periodo de Prueba.';
			$mail->isHTML(true);
			$mail->CharSet = 'UTF-8';
			$mail->AltBody = 'Programación de Periodo de Prueba.';
			$body = '

			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns:v="urn:schemas-microsoft-com:vml">

			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
				<!--[if !mso]--><!-- -->
				<link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
				<link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet">
				<!--<![endif]-->
				<title>Sistema de Gestion de Recursos Humanos</title>

				<style type="text/css">
					body {
						width: 100%;
						background-color: #ffffff;
						margin: 0;
						padding: 0;
						-webkit-font-smoothing: antialiased;
						mso-margin-top-alt: 0px;
						mso-margin-bottom-alt: 0px;
						mso-padding-alt: 0px 0px 0px 0px;
					}

					p,
					h1,
					h2,
					h3,
					h4 {
						margin-top: 0;
						margin-bottom: 0;
						padding-top: 0;
						padding-bottom: 0;
					}

					span.preheader {
						display: none;
						font-size: 1px;
					}

					html {
						width: 100%;
					}

					table {
						font-size: 14px;
						border: 0;
					}
					/* ----------- responsivity ----------- */

					@media only screen and (max-width: 640px) {
						/*------ top header ------ */
						.main-header {
							font-size: 20px !important;
						}
						.main-section-header {
							font-size: 28px !important;
						}
						.show {
							display: block !important;
						}
						.hide {
							display: none !important;
						}
						.align-center {
							text-align: center !important;
						}
						.no-bg {
							background: none !important;
						}
						/*----- main image -------*/
						.main-image img {
							width: 440px !important;
							height: auto !important;
						}
						/* ====== divider ====== */
						.divider img {
							width: 440px !important;
						}
						/*-------- container --------*/
						.container590 {
							width: 440px !important;
						}
						.container580 {
							width: 400px !important;
						}
						.main-button {
							width: 220px !important;
						}
						/*-------- secions ----------*/
						.section-img img {
							width: 320px !important;
							height: auto !important;
						}
						.team-img img {
							width: 100% !important;
							height: auto !important;
						}
					}

					@media only screen and (max-width: 479px) {
						/*------ top header ------ */
						.main-header {
							font-size: 18px !important;
						}
						.main-section-header {
							font-size: 26px !important;
						}
						/* ====== divider ====== */
						.divider img {
							width: 280px !important;
						}
						/*-------- container --------*/
						.container590 {
							width: 280px !important;
						}
						.container590 {
							width: 280px !important;
						}
						.container580 {
							width: 260px !important;
						}
						/*-------- secions ----------*/
						.section-img img {
							width: 280px !important;
							height: auto !important;
						}
					}
					#customers {
					  border-collapse: collapse;
					  width: 100%;
					}
					
					#customers td, #customers th {
					  border: 1px solid #ddd;
					  padding: 4px;
					}
					
					#customers tr:nth-child(even){background-color: #f2f2f2;}
					
					#customers th {
					  padding-top: 12px;
					  padding-bottom: 12px;
					  text-align: left;
					  background-color: #C30F2D;
					  color: white;
					}
				</style>
				<!--[if gte mso 9]><style type="text/css">
					body {
					font-family: arial, sans-serif!important;
					}
					</style>
				<![endif]-->
				</head>


				<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
				<!-- header -->
				<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">

					<tr>
						<td align="center">
							<table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

								<tr>
									<td style="font-size: 25px; line-height: 25px;">&nbsp;</td>
								</tr>

							</table>
						</td>
					</tr>
				</table>
				<!-- end header -->

				<!-- big image section -->

				<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">

					<tr>
						<td align="center">
							<table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

								<tr>
									<td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;"
										class="main-header">
										<!-- section text ======-->

										<div style="line-height: 35px">

											Programaci&oacute;n de <span style="color: #C30F2D;">Periodo de Prueba</span>

										</div>
									</td>
								</tr>

								<tr>
									<td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
								</tr>

								<tr>
									<td align="center">
										<table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee">
											<tr>
												<td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;</td>
											</tr>
										</table>
									</td>
								</tr>

								<tr>
									<td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
								</tr>

								<tr>
									<td align="left">
										<table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590">
											<tr>
												<td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">
													<p style="line-height: 24px; margin-bottom:15px;">

														Estimado Usuario,

													</p>
													<p style="line-height: 24px;margin-bottom:15px;">
													   A continuaci&oacute;n se muestran los detalles de un nuevo Periodo de Prueba autorizado.
													</p>
													<p style="line-height: 24px; margin-bottom:20px;">
													   Puedes consultar los detalles accediendo al <strong>Sistema de Gesti&oacute;n de Recursos Humanos Sahuayo </strong> dando clic en el bot&oacute;n al final del presente correo.
													</p>
			<table id="customers"> 
			<thad>
			<tr>
			<th style="text-align:center" style="text-align:center">No. Emp</th>
			<th style="text-align:center" style="text-align:center">Empleado</th>
			<th style="text-align:center" style="text-align:center">Puesto a Promoverse</th>
			<th style="text-align:center" style="text-align:center">Fecha de inicio</th>
			<th style="text-align:center" style="text-align:center">Semana</th>
			</tr>
			</thead>';
			$body .= '<tbody>';
			$body .= '<tr>';
			$body .= '<td align="center">';
			$body .=  $row_lpp['IDempleado'];
			$body .= '</td>';
			$body .= '<td align="center">';
			$body .=  $row_lpp['emp_paterno']." ".$row_lpp['emp_materno']." ".$row_lpp['emp_nombre'];
			$body .= '</td>';
			$body .= '<td align="center">';
			$body .=  $row_lpp['denominacion'];
			$body .= '</td>';
			$body .= '<td align="center">';
			$body .=  date("d-m-Y", strtotime($row_lpp['fecha_inicio']));
			$body .= '</td>';
			$body .= '<td align="center">';
			$body .=  $row_lpp['semana'];
			$body .= '</td>';
			$body .= '</tbody></table>
											
													<p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
													<table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="C30F2D" style="margin-bottom:20px;">

														<tr>
															<td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
														</tr>

														<tr>
															<td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">
																<!-- main section button -->

																<div style="line-height: 22px;">
																	<a href="https://gestionvacantes.com/f_index.php" style="color: #ffffff; text-decoration: none;">Accede al SGRH</a>
																</div>
															</td>
														</tr>

														<tr>
															<td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
														</tr>

													</table>
													<p style="line-height: 24px">
														Saludos Cordiales,</br>
														<strong>Sistema de Gesti&oacute;n de Recursos Humanos </strong></br>
														Sahuayo 2024</br>
													</p>

												</td>
											</tr>
										</table>
									</td>
								</tr>

							</table>

						</td>
					</tr>
					<tr>
						<td><p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>Si recibiste por error este correo o no quieres recibirlo en adelante, solicita tu baja a <a href="mailto:jacardenas@sahuayo.mx">jacardenas@sahuayo.mx</a></td>
					</tr>
				</table>
			</body>
			</html>
			';
			$mail->Body = $body;
			//echo $body;
			if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo; }

		}
	  
	} 
	
    header("Location: admin_pprueba.php?info=5");
  
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<!-- /theme JS files -->
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


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Periodo de Prueba.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el Periodo de Prueba.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 5))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha autorizado correctamente el Periodo de Prueba.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Periodos de Prueba</h5>
								</div>

								<div class="panel-body">
								<p>A continuación se muestran los empleados de la Sucursal <strong><?php echo $row_matriz['matriz']; ?></strong> que están en Periodo de Prueba.</p>
								<p><strong>Instrucciones:</strong><br/>
								<ul>
								<li>Para poder cargar el formato de Desempeño, primero debes completar los datos de puesto destino.</li>
								<li>Para poder capturar los pagos, debes haber cargado el formato de desempeño (Excel o pdf).</li>
								<li>Una vez capturados los pagos, el <span class="text-semibold">Jefe de Desarrollo Organizacional </span>deberá validar el PP.</li>
								<li>Para poder imprimir la notificación, el periodo de prueba debe capturados los pagos.</li>
								<li>Para poder imprimir la afectación, debe estar cargada la notificación firmada (pdf) y Validado el movimiento por DO.</li>
								</ul>
								</p>
								
								<p><strong>Descargas:</strong></p>
								<ul>
								<li><a href="PPP/formato.xlsx"><i class="icon-file-download2 position-left"></i>Formato de Desempeño - Periodo de Prueba</a></li>
								<li><a href="PPP/Politica.pdf"><i class="icon-file-download2 position-left"></i>Política Promociones Internas</a></li>
								<li><a href="PPP/formato1.xlsx"><i class="icon-file-download2 position-left"></i>Formato de Notificación de Periodo de Prueba</a></li>
								<li><a href="PPP/formato2.xlsx"><i class="icon-file-download2 position-left"></i>Formato de Afectación de Nómina</a></li>
								</ul>
								</p>
								
								<p>&nbsp;</p>
								<p><strong>Nomenclaturas de los botones:</strong>
								<ul>
								<li style="list-style: none;"><span class="text-primary"><i class="icon-pencil4"></i> Editar</span></li>
								<li style="list-style: none;"><span class="text-warning"><i class="icon-file-upload2"></i> Cargar Formato Desempeño</span></li>
								<li style="list-style: none;"><span class="text-danger"><i class="icon-coin-dollar"></i> Capturar Pagos</span></li>
								<li style="list-style: none;"><span class="text-info"><i class="icon-printer2"></i> Imprimir Notificación</span></li>
								<li style="list-style: none;"><span class="text-danger"><i class="icon-file-upload2"></i> Cargar Notificacion Firmada</span></li>
								<li style="list-style: none;"><span class="text-info"><i class="icon-printer"></i> Imprimir Afectación</span></li>
								<li style="list-style: none;"><span class="text-success"><i class="icon-file-upload2"></i> Cargar Afectación Firmada</span></li>
								<li style="list-style: none;"><span class="text-success"><i class="icon-file-check"></i> Autorizar Periodo de Prueba</span></li>
								</ul>
								</p>
                                
                    <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    
                    </div>
					</div>
					<!-- /colored button -->
	

<form method="POST" action="admin_pprueba.php">
<table class="table">
<tbody>							  
	<tr>
	<td><div class="col-lg-9">Estatus
		 <select class="form-control" name="IDestatus_f">
		   <option value="1,2,3,4,5,6"<?php if (!(strcmp($IDestatus_f, "1,2,3,4,5,6"))) {echo "selected=\"selected\"";} ?>>TODOS</option>
		   <option value="6"<?php if (!(strcmp($IDestatus_f, "6"))) {echo "selected=\"selected\"";} ?>>TERMINADOS</option>
		   <option value="1,2,3,4,5"<?php if (!(strcmp($IDestatus_f, "1,2,3,4,5"))) {echo "selected=\"selected\"";} ?>>EN PROCESO</option>
		 </select>
	</div></td>
	<td><div class="col-lg-9">Matriz
		 <select name="IDmatriz" class="form-control">
         <option value="" <?php if (!(strcmp("", $la_matriz))) {echo "selected=\"selected\"";} ?>>TODAS</option>
		   <?php do {  ?>
		   <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
		   <?php
		  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
		  $rows = mysql_num_rows($lmatriz);
		  if($rows > 0) {
			  mysql_data_seek($lmatriz, 0);
			  $row_lmatriz = mysql_fetch_assoc($lmatriz);
		  } ?>
		  </select>
	</div></td>
	<td>
	<div class="col-lg-8">
		Año <select class="form-control"  name="el_anio">
		<option value="2025"<?php if (!(strcmp(2025, $el_anio))) {echo "selected=\"selected\"";} ?>>2025</option>
		<option value="2024"<?php if (!(strcmp(2024, $el_anio))) {echo "selected=\"selected\"";} ?>>2024</option>
		<option value="2023"<?php if (!(strcmp(2023, $el_anio))) {echo "selected=\"selected\"";} ?>>2023</option>
		<option value="2022"<?php if (!(strcmp(2022, $el_anio))) {echo "selected=\"selected\"";} ?>>2022</option>
	</select>
	</div>
	</td>
	<td>
	<button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right13 position-right"></i></button>
	<a class="btn btn-success" href="admin_pprueba_edit.php">Agregar Periodo de Prueba<i class="icon-arrow-right14 position-right"></i></a>									
	</td>
  </tr>
</tbody>
</table>
</form>



								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>No. Emp.</th>
                                    <th>Matriz</th>
                                    <th>Nombre</th>
                                    <th>Puesto Origen</th>
                                    <th>Puesto Destino</th>
                                    <th>Inicio</th>
                                    <th>Termino</th>
                                    <th>Entrega</th>
                                    <th>Estatus</th>
                                    <th>Autorizado</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_pprueba > 0 ) { ?>
								  <?php do { 
									$Elperiodo = $row_pprueba['IDpprueba'];
									$query_pagos = "SELECT * FROM pp_prueba_pagos WHERE IDpprueba = $Elperiodo"; 
									$pagos = mysql_query($query_pagos, $vacantes) or die(mysql_error());
									$row_pagos = mysql_fetch_assoc($pagos);
									$totalRows_pagos = mysql_num_rows($pagos); 
								  ?>
                                    <tr>
                                      <td><?php echo $row_pprueba['IDempleado']; $IDempleado_carpeta = 'PPP/'.$row_pprueba['IDempleado']."/".$row_pprueba['file']; if (!file_exists($IDempleado_carpeta)) {echo " <i class='icon-notification2 text-danger'></i>";}?></td>
                                      <td><?php echo $row_pprueba['matriz_origen'];?></td>
                                      <td><?php echo $row_pprueba['emp_paterno'] . " " . $row_pprueba['emp_materno'] . " " . $row_pprueba['emp_nombre'];?></td>
                                      <td><?php echo $row_pprueba['denominacion_origen']; ?></td>
                                      <td><?php echo $row_pprueba['denominacion_destino']; ?></td>
                                      <td><?php echo date("d-m-Y",strtotime($row_pprueba['fecha_inicio']));  ?></td>
                                      <td><?php echo date("d-m-Y",strtotime($row_pprueba['fecha_fin']));  ?></td>
                                      <td><?php if($row_pprueba['fecha_cierre'] != '') {echo date("d-m-Y",strtotime($row_pprueba['fecha_cierre'])); } else {echo "Sin fecha";}  ?></td>
                                      <td><?php if ($row_pprueba['IDestatus'] == 1) {echo "Captura incompleta";} 
									  else if ($row_pprueba['IDestatus'] == 2) {echo "Cargar desempeño";} 
									  else if ($row_pprueba['IDestatus'] == 3) {echo "Capturar pagos";} 
									  else if ($row_pprueba['IDestatus'] == 3 and $totalRows_pagos > 0) {echo "Imprimir notificación";} 
									  else if ($row_pprueba['IDestatus'] == 4 and $row_pprueba['file2'] != '') {echo "Cargar notificación firmada";} 
									  else if ($row_pprueba['IDestatus'] == 5 or $row_pprueba['IDestatus'] == 4) {echo "Imprimir afectación";} 
									  else if ($row_pprueba['IDestatus'] == 6) {echo "Terminado";} 
									   ?></td>
                                      <td><?php if ($row_pprueba['IDestatusv'] == 1) {echo "Autorizado";}
											elseif ($row_pprueba['IDestatusv'] == 2) {echo "En proceso";} 
											else   { echo "Pendiente";} ?></td>
									  <td>
									  <a class="btn btn-xs btn-primary" href="admin_pprueba_edit_2.php?IDpprueba=<?php echo $row_pprueba['IDpprueba']; ?>"><i class="icon-pencil4"></i></a>
									
  									  <?php if ($row_pprueba['IDestatus'] > 1) { ?>
									  <button type="button" data-target="#modal_theme_danger1<?php echo $row_pprueba['IDpprueba']; ?>"  data-toggle="modal" class="btn btn-xs btn-warning"><i class="icon-file-upload2"></i></button>
  									  <?php } ?>

									  <?php if ($row_pprueba['IDestatus'] > 2) { ?>
									  <a class="btn btn-xs btn-danger" href="admin_pprueba_edit_pagos.php?IDpprueba=<?php echo $row_pprueba['IDpprueba']; ?>"><i class="icon-coin-dollar"></i></a>
									  <?php } ?>

									  <?php if ($row_pprueba['IDestatus'] > 2 and $totalRows_pagos > 0 and $row_pprueba['IDestatusv'] == 1) { ?>	
									  <a class="btn btn-xs btn-info" href="pprueba_edit_print.php?IDpprueba=<?php echo $row_pprueba['IDpprueba']; ?>"><i class="icon-printer2"></i></a>								  
									  <button type="button" data-target="#modal_theme_danger2<?php echo $row_pprueba['IDpprueba']; ?>"  data-toggle="modal" class="btn btn-xs btn-danger"><i class="icon-file-upload2"></i></button>
									  <?php } ?>
									  
									  <?php if ($row_pprueba['IDestatusv'] == 1) { ?>	
									  <a class="btn btn-xs btn-info" href="pprueba_edit_print2.php?IDpprueba=<?php echo $row_pprueba['IDpprueba']; ?>"><i class="icon-printer"></i></a>								  
									  <?php } ?>

									  <?php if ($row_pprueba['IDestatus'] >= 4) { ?>	
									  <button type="button" data-target="#modal_theme_danger3<?php echo $row_pprueba['IDpprueba']; ?>"  data-toggle="modal" class="btn btn-xs btn-success"><i class="icon-file-upload2"></i></button>
									  <?php } ?>

									  <button type="button" data-target="#modal_theme_danger4<?php echo $row_pprueba['IDpprueba']; ?>"  data-toggle="modal" class="btn btn-xs btn-success"><i class="icon-file-check"></i></button>
									  </td>
                                    </tr>
									
									
					<!-- danger modal -->
					<div id="modal_theme_danger4<?php echo $row_pprueba['IDpprueba']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Autorización</h6>
								</div>
								
								<form method="post" id="form1" action="admin_pprueba.php">

								<div class="modal-body">
									<!-- Basic select -->
									<div class="form-group">
										<div class="col-lg-9">
											<select name="IDestatusv" id="IDestatusv" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												<option value="1"<?php if (!(strcmp($row_pprueba['IDestatusv'], 1))) {echo "SELECTED";} ?>>Autorizado 
												
												<?php if (in_array($row_pprueba['IDpuesto_destino'], $os)) {echo " y enviar correo a Nómina";} ?>
												
												</option>
												<option value="2"<?php if (!(strcmp($row_pprueba['IDestatusv'], 2))) {echo "SELECTED";} ?>>En Proceso</option>
												<option value="0"<?php if (!(strcmp($row_pprueba['IDestatusv'], 0))) {echo "SELECTED";} ?>>Pendiente</option></select>
										</div>
									</div>
									<!-- /basic select -->
								</div>

								<div class="modal-footer">
                         <button type="submit"  name="KT_Update1" class="btn btn-primary">Actualizar</button>
						 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
						 <input  type="hidden" name="IDpprueba_autorizar" id="IDpprueba_autorizar" value="<?php echo  $row_pprueba['IDpprueba']; ?>" />
                       </form>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->


									
									
					<!-- danger modal -->
					<div id="modal_theme_danger1<?php echo $row_pprueba['IDpprueba']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Formato de Desempeño</h6>
								</div>

								<div class="modal-body">
								
								<?php if ($row_pprueba['file'] != '') { ?>
								<legend class="text-semibold">Formato Cargado</legend>
								<a href="PPP/<?php echo $row_pprueba['IDempleado']; ?>/<?php echo $row_pprueba['file']; ?>"><i class="icon-file-download2"></i>   Descargar</a>
 								<p>&nbsp;</p>
								<?php } ?>

								<legend class="text-semibold">Agregar / Actualizar Formato</legend>
									<p>Selecciona el formato de Desempeño de <?php echo $row_pprueba['emp_paterno'] . " " . $row_pprueba['emp_materno'] . " " . $row_pprueba['emp_nombre'];?>.</p>

 								<p>&nbsp;</p>
										<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
											<fieldset class="content-group">
												
													<!-- Basic text input -->
													<div class="form-group">
														<div class="col-lg-12">
															<input type="file" name="file" id="file" class="form-control" value="">
														</div>
													</div>
													<!-- /basic text input -->
													
												</div>

												<div class="modal-footer">
													 <button type="submit"  name="KT_Update1" class="btn btn-warning">Cargar</button>
													 <input type="hidden" name="MM_update" value="form1">
													 <input type="hidden" name="IDpprueba" value="<?php echo $row_pprueba['IDpprueba']; ?>">
													 <input type="hidden" name="IDempleado" value="<?php echo $row_pprueba['IDempleado']; ?>">
													 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
												
											</fieldset>
										</form>
							</div>
						</div>
					</div>
					<!-- danger modal -->

					<!-- danger modal -->
					<div id="modal_theme_danger2<?php echo $row_pprueba['IDpprueba']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Notificación Firmada</h6>
								</div>

								<div class="modal-body">
								
								<?php if ($row_pprueba['file2'] != '') { ?>
								<legend class="text-semibold">Formato Cargado</legend>
								<a href="PPP/<?php echo $row_pprueba['IDempleado']; ?>/<?php echo $row_pprueba['file2']; ?>"><i class="icon-file-download2"></i> Descargar</a>
 								<p>&nbsp;</p>
								<?php } ?>

								<legend class="text-semibold">Agregar / Actualizar Formato</legend>
									<p>Selecciona el formato de Notificación firmada de <?php echo $row_pprueba['emp_paterno'] . " " . $row_pprueba['emp_materno'] . " " . $row_pprueba['emp_nombre'];?>.</p>

 								<p>&nbsp;</p>
										<form method="post" name="form2" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
											<fieldset class="content-group">
												
													<!-- Basic text input -->
													<div class="form-group">
														<div class="col-lg-12">
															<input type="file" name="file" id="file" class="form-control" value="">
														</div>
													</div>
													<!-- /basic text input -->
													
												</div>

												<div class="modal-footer">
													 <button type="submit"  name="KT_Update" class="btn btn-danger">Cargar</button>
													 <input type="hidden" name="MM_update" value="form2">
													 <input type="hidden" name="IDpprueba" value="<?php echo $row_pprueba['IDpprueba']; ?>">
													 <input type="hidden" name="IDempleado" value="<?php echo $row_pprueba['IDempleado']; ?>">
													 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
												
											</fieldset>
										</form>
							</div>
						</div>
					</div>
					<!-- danger modal -->
									
					<!-- danger modal -->
					<div id="modal_theme_danger3<?php echo $row_pprueba['IDpprueba']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Afectación Firmada</h6>
								</div>

								<div class="modal-body">
								
								<?php if ($row_pprueba['file3'] != '') { ?>
								<legend class="text-semibold">Formato Cargado</legend>
								<a href="PPP/<?php echo $row_pprueba['IDempleado']; ?>/<?php echo $row_pprueba['file3']; ?>"><i class="icon-file-download2"></i>   Descargar</a>
 								<p>&nbsp;</p>
								<?php } ?>

								<legend class="text-semibold">Agregar / Actualizar Formato</legend>
									<p>Selecciona la Afectación Firmada de <?php echo $row_pprueba['emp_paterno'] . " " . $row_pprueba['emp_materno'] . " " . $row_pprueba['emp_nombre'];?>.</p>

 								<p>&nbsp;</p>
										<form method="post" name="form3" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
											<fieldset class="content-group">
												
													<!-- Basic text input -->
													<div class="form-group">
														<div class="col-lg-12">
															<input type="file" name="file" id="file" class="form-control" value="">
														</div>
													</div>
													<!-- /basic text input -->
													
												</div>

												<div class="modal-footer">
													 <button type="submit"  name="KT_Update1" class="btn btn-success">Cargar</button>
													 <input type="hidden" name="MM_update" value="form3">
													 <input type="hidden" name="IDpprueba" value="<?php echo $row_pprueba['IDpprueba']; ?>">
													 <input type="hidden" name="IDempleado" value="<?php echo $row_pprueba['IDempleado']; ?>">
													 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
												
											</fieldset>
										</form>
							</div>
						</div>
					</div>
					<!-- danger modal -->
									
									
                                    <?php } while ($row_pprueba = mysql_fetch_assoc($pprueba)); ?>
 							  <?php } else { ?>
<tr>
                                      <td>No se tienen Periodos de Prueba .</td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                    </tr>
                              <?php } ?>
                                    
                                  </tbody>
                                </table>
								</div>
							</div>
						</div>
                                    
					<!-- /Contenido -->

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