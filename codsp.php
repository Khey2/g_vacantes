<?php require_once('Connections/vacantes.php'); ?>
<?php
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

if (isset($_GET["ID"])) {
	
$ID = $_GET['ID'];	
mysql_select_db($database_vacantes, $vacantes);
$query_personales = "SELECT * FROM ct_datos_generales WHERE ID_datos_generales = '$ID'";
$personales = mysql_query($query_personales, $vacantes) or die(mysql_error());
$row_personales = mysql_fetch_assoc($personales);
$totalRows_personales = mysql_num_rows($personales);
$cps_ok = $row_personales['dt_cp'];

mysql_select_db($database_vacantes, $vacantes);
$query_codigos_postales = "SELECT * FROM codigos_postales WHERE cp = '$cps_ok'";
mysql_query("SET NAMES 'utf8'");
$codigos_postales = mysql_query($query_codigos_postales, $vacantes) or die(mysql_error());
$row_codigos_postales = mysql_fetch_assoc($codigos_postales);
$totalRows_codigos_postales = mysql_num_rows($codigos_postales);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE ct_datos_generales SET idusuario=%s, dt_paterno=%s, dt_materno=%s, dt_nombres=%s, dt_domicilio=%s, dt_aseguradora=%s, dt_cp=%s, dt_colonia=%s, dt_estado=%s, dt_municipio=%s, dt_correo=%s WHERE ID_datos_generales=%s",
                       GetSQLValueString($_POST['idusuario'], "text"),
                       GetSQLValueString($_POST['dt_paterno'], "text"),
                       GetSQLValueString($_POST['dt_materno'], "text"),
                       GetSQLValueString($_POST['dt_nombres'], "text"),
                       GetSQLValueString($_POST['dt_domicilio'], "text"),
                       GetSQLValueString($_POST['dt_aseguradora'], "text"),
                       GetSQLValueString($_POST['dt_cp'], "text"),
                       GetSQLValueString($_POST['dt_colonia'], "text"),
                       GetSQLValueString($_POST['dt_estado'], "text"),
                       GetSQLValueString($_POST['dt_municipio'], "text"),
                       GetSQLValueString($_POST['dt_correo'], "text"),
                       GetSQLValueString($ID, "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  
$insertGoTo = "codsp.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
		
  $insertSQL = sprintf("INSERT INTO ct_datos_generales (idusuario, dt_paterno, dt_materno, dt_nombres, dt_domicilio, dt_aseguradora, dt_cp, dt_colonia, dt_estado, dt_municipio, dt_correo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idusuario'], "text"),
                       GetSQLValueString($_POST['dt_paterno'], "text"),
                       GetSQLValueString($_POST['dt_materno'], "text"),
                       GetSQLValueString($_POST['dt_nombres'], "text"),
                       GetSQLValueString($_POST['dt_domicilio'], "text"),
                       GetSQLValueString($_POST['dt_aseguradora'], "text"),
                       GetSQLValueString($_POST['dt_cp'], "text"),
                       GetSQLValueString($_POST['dt_colonia'], "text"),
                       GetSQLValueString($_POST['dt_estado'], "text"),
                       GetSQLValueString($_POST['dt_municipio'], "text"),
                       GetSQLValueString($_POST['dt_correo'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

$idingreso = mysql_insert_id($vacantes);
$insertGoTo = "codsp.php?ID=$idingreso";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title>CPs</title>

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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_input_groups.js"></script>
	<!-- /theme JS files -->

</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="#"><img src="global_assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>

			</ul>

			<p class="navbar-text">
				<span class="label bg-success">Online</span>
			</p>

		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left"><img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-sm" alt=""></a>
								<div class="media-body">
									<span class="media-heading text-semibold">Usuario</span>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<a href="#"><i class="icon-cog3"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<!-- Main -->
								<li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages"></i></li>
								<li><a href="codsp.php"><i class="icon-home4"></i> <span>Dashboard</span></a></li>

							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header page-header-default">
					<div class="page-header-content">

					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="codsp.php"><i class="icon-home2 position-left"></i> Home</a></li>
						</ul>

					</div>
				</div>				
				<!-- /page header -->
                <p>&nbsp;</p>
				<!-- Content area -->
				<div class="content">

					<!-- Input group addons -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Registro de Usuarios.</h5>
							<div class="heading-elements">
		                	</div>
						</div>

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<div class="panel-body">
                          <p>&nbsp;</p>

			<?php if(!isset($_GET['ID'])) { ?>

			<form class="form-horizontal" name="form1" action="<?php echo $editFormAction; ?>" method="post" >
			<div class="form-group">
			  <label class="control-label col-lg-3">Usuario:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="number" name="idusuario" id="idusuario" class="form-control"  required="required"  value="">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Paterno:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_paterno" id="dt_paterno" class="form-control" required="required" value="">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Materno:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_materno" id="dt_materno" class="form-control" required="required" value="">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Nombres:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_nombres" id="dt_nombres" class="form-control" required="required" value="">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Domicilio (Calle y Numero):<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_domicilio" id="dt_domicilio" class="form-control" required="required" value="">
			  </div>
		    </div> 
	        <p>&nbsp;</p>


			<div class="form-group">
			  <label class="control-label col-lg-3">C.P.:<span class="text-danger">*</span></label>
			  <div class="col-lg-6">
				<input type="number" name="dt_cp" id="dt_cp" class="form-control" required="required" value="">
			  </div>
			  <div class="col-lg-3">
                <input type="submit" name="form1" class="btn btn-info" id="form1" value="Validar C.P." />
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Colonia:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<select name="dt_colonia" id="dt_colonia" class="form-control"  >
                          <option value="" >Indique su C.P.</option>
                </select>
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Estado:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<select name="dt_estado" id="dt_estado" class="form-control"  >
                          <option value="" >Indique su C.P.</option>
                </select>
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Municipio:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<select name="dt_municipio" id="dt_municipio" class="form-control"  >
                          <option value="" >Indique su C.P.</option>
                </select>
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Aseguradora:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_aseguradora" id="dt_aseguradora" class="form-control" value="">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="email" name="dt_correo" id="dt_correo" class="form-control"  value="">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

            <div class="text-right">
              <div>
                <input type="hidden" name="MM_insert" value="form1">
                <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Agregar" />
              </div>
            </div>
                        
            </form>
            
			<?php } else { ?>
            

			<form class="form-horizontal" name="form1" action="<?php echo $editFormAction; ?>" method="post" >
			<div class="form-group">
			  <label class="control-label col-lg-3">Usuario:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="number" name="idusuario" id="idusuario" class="form-control"  required="required"  value="<?php echo htmlentities($row_personales['idusuario'], ENT_COMPAT, ''); ?>">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Paterno:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_paterno" id="dt_paterno" class="form-control"  required="required"  value="<?php echo htmlentities($row_personales['dt_paterno'], ENT_COMPAT, ''); ?>">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Materno:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_materno" id="dt_materno" class="form-control"  required="required"  value="<?php echo htmlentities($row_personales['dt_materno'], ENT_COMPAT, ''); ?>">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Nombres:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_nombres" id="dt_nombres" class="form-control"  required="required"  value="<?php echo htmlentities($row_personales['dt_nombres'], ENT_COMPAT, ''); ?>">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Domicilio (Calle y Numero):<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_domicilio" id="dt_domicilio" class="form-control"  required="required"  value="<?php echo htmlentities($row_personales['dt_domicilio'], ENT_COMPAT, ''); ?>">
			  </div>
		    </div> 
	        <p>&nbsp;</p>


			<div class="form-group">
			  <label class="control-label col-lg-3">C.P.:<span class="text-danger">*</span></label>
			  <div class="col-lg-6">
				<input type="number" name="dt_cp" id="dt_cp" class="form-control"  value="<?php echo htmlentities($row_personales['dt_cp'], ENT_COMPAT, ''); ?>">
			  </div>
			  <div class="col-lg-3">
                <input type="submit" name="form1" class="btn btn-info" id="form1" value="Validar C.P." />
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Colonia:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<select name="dt_colonia" id="dt_colonia" class="form-control"  >
                          <?php do { ?>
                          <option value="<?php echo $row_codigos_postales['cp']?>" ><?php echo $row_codigos_postales['colonia']?></option>
                </select>
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Estado:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<select name="dt_estado" id="dt_estado" class="form-control"  >
                          <option value="<?php echo $row_codigos_postales['cp']?>" ><?php echo $row_codigos_postales['estado']?></option>
                </select>
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Municipio:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<select name="dt_municipio" id="dt_municipio" class="form-control"  >
                          <option value="<?php echo $row_codigos_postales['cp']?>" ><?php echo $row_codigos_postales['municipio']?></option>
                          <?php } while ($row_codigos_postales = mysql_fetch_assoc($codigos_postales)); ?>
                </select>
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Aseguradora:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="text" name="dt_aseguradora" id="dt_aseguradora" class="form-control"  required="required"  value="<?php echo htmlentities($row_personales['dt_aseguradora'], ENT_COMPAT, ''); ?>">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

			<div class="form-group">
			  <label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
			  <div class="col-lg-9">
				<input type="email" name="dt_correo" id="dt_correo" class="form-control"  required="required"  value="<?php echo htmlentities($row_personales['dt_correo'], ENT_COMPAT, ''); ?>">
			  </div>
		    </div> 
	        <p>&nbsp;</p>

            <div class="text-right">
              <div>
                <input type="hidden" name="MM_update" value="form1">
                <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Agregar" />
              </div>
            </div>
                        
            </form>

			<?php }?>

<p>&nbsp;</p>
                     
                     
                     
                      </div>
					<!-- Footer -->
				  <div class="footer text-muted">
						&copy; <?php echo $anio; ?>... 
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /content wrapper -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->

</body>
</html>