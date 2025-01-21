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

//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

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
mysql_query("SET NAMES 'utf8'"); 
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

if(isset($_GET['IDdireccion'])) { $_SESSION['IDdireccion'] = $_GET['IDdireccion']; } else { $_SESSION['IDdireccion'] = 0;}
if(isset($_GET['IDDarea'])) { $_SESSION['IDDarea'] = $_GET['IDDarea']; } else { $_SESSION['IDDarea'] = 0;}
if(isset($_GET['IDsubarea'])) { $_SESSION['IDsubarea'] = $_GET['IDsubarea']; } else { $_SESSION['IDsubarea'] = 0;}
if(isset($_GET['IDtipo'])) { $_SESSION['IDtipo'] = $_GET['IDtipo']; } else { $_SESSION['IDtipo'] = 0;}
if(isset($_GET['IDvisible'])) { $_SESSION['IDvisible'] = $_GET['IDvisible']; } else { $_SESSION['IDvisible'] = 0;}

$IDDarea = $_SESSION['IDDarea'];
$IDdireccion  = $_SESSION['IDdireccion'];
$IDsubarea  = $_SESSION['IDsubarea'];
$IDtipo  = $_SESSION['IDtipo'];
$IDvisible  = 1;

$filtroIDdireccion = 0;
$filtroIDDarea = 0;
$filtroIDsubarea = 0;
$filtroIDtipo = 0;
$filtroIDvisible = 0;

if($IDdireccion == 0) { $filtroIDdireccion = ''; } else { $filtroIDdireccion = ' AND proced_documentos.IDdireccion = '.$IDdireccion;}
if($IDDarea == 0) { $filtroIDDarea = ''; } else { $filtroIDDarea = ' AND proced_documentos.IDDarea = '.$IDDarea;}
if($IDsubarea == 0) { $filtroIDsubarea = ''; } else { $filtroIDsubarea = ' AND proced_documentos.IDsubarea = '.$IDsubarea;}
if($IDtipo == 0) { $filtroIDtipo = ''; } else { $filtroIDtipo = ' AND proced_documentos.IDtipo = '.$IDtipo;}
if($IDdireccion != 0 OR $IDDarea != 0 OR $IDsubarea != 0 OR $IDtipo != 0) { $IDvisible = 1; } 

if($IDdireccion == 0) {	$totalRows_direcciona = 0; } else {
		$query_direcciona = "SELECT * FROM proced_direcciones WHERE IDdireccion = $IDdireccion";
		$direcciona = mysql_query($query_direcciona, $vacantes) or die(mysql_error());
		$row_direcciona = mysql_fetch_assoc($direcciona);
		$totalRows_direcciona = mysql_num_rows($direcciona);
}

if($IDDarea == 0) {	$totalRows_areaa = 0; } else {
		$query_areaa = "SELECT * FROM proced_areas WHERE IDDarea = $IDDarea";
		$areaa = mysql_query($query_areaa, $vacantes) or die(mysql_error());
		$row_areaa = mysql_fetch_assoc($areaa);
		$totalRows_areaa = mysql_num_rows($areaa);
}

if($IDsubarea == 0) {	$totalRows_subareaa = 0; } else {
		$query_subareaa = "SELECT * FROM proced_subareas WHERE IDsubarea = $IDsubarea";
		$subareaa = mysql_query($query_subareaa, $vacantes) or die(mysql_error());
		$row_subareaa = mysql_fetch_assoc($subareaa);
		$totalRows_subareaa = mysql_num_rows($subareaa);
}

if (isset($_POST['buscado'])) {	
$arreglo = '';
$array = explode(" ", $_POST['buscado']);
$contar = substr_count($_POST['buscado'], ' ') + 1;
$i = 0;
while($contar > $i) {
$arreglo .= " AND (proced_documentos.documento LIKE '%" . $array[$i] . "%'"; 
$arreglo .= " OR proced_documentos.descripcion LIKE '%" . $array[$i] . "%' )"; 
    $i++; } }
	
if (!isset($_POST['buscado'])) { $filtroBuscado = ''; }  else { $filtroBuscado = $arreglo; $IDvisible = 1;}

//nuevo!!
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_documentos = 10;
$pageNum_documentos = 0;
if (isset($_GET['pageNum_documentos'])) {
  $pageNum_documentos = $_GET['pageNum_documentos'];
}
$startRow_documentos = $pageNum_documentos * $maxRows_documentos;

mysql_select_db($database_vacantes, $vacantes);
$query_documentos = "SELECT proced_documentos.IDdocumento, proced_documentos.IDDarea, proced_documentos.IDdireccion, proced_documentos.IDsubarea, proced_documentos.IDtipo, proced_documentos.IDvisible, proced_documentos.documento, proced_documentos.descripcion, proced_documentos.file, proced_documentos.version, proced_documentos.anio, proced_documentos.vistas, proced_subareas.subarea, proced_direcciones.direccion, proced_areas.area, proced_tipos.tipo FROM proced_documentos LEFT JOIN proced_subareas ON proced_documentos.IDsubarea = proced_subareas.IDsubarea LEFT JOIN  proced_direcciones ON proced_documentos.IDdireccion = proced_direcciones.IDdireccion LEFT JOIN proced_areas ON proced_documentos.IDDarea = proced_areas.IDDarea LEFT JOIN proced_tipos ON proced_documentos.IDtipo = proced_tipos.IDtipo WHERE proced_documentos.IDvisible = $IDvisible".$filtroBuscado.$filtroIDdireccion.$filtroIDDarea.$filtroIDsubarea." ORDER BY proced_documentos.anio desc";
mysql_query("SET NAMES 'utf8'");
$query_limit_documentos = sprintf("%s LIMIT %d, %d", $query_documentos, $startRow_documentos, $maxRows_documentos);
$documentos = mysql_query($query_limit_documentos, $vacantes) or die(mysql_error());
$row_documentos = mysql_fetch_assoc($documentos);

if (isset($_GET['totalRows_documentos'])) {
  $totalRows_documentos = $_GET['totalRows_documentos'];
} else {
  $all_documentos = mysql_query($query_documentos);
  $totalRows_documentos = mysql_num_rows($all_documentos);
}
$totalPages_documentos = ceil($totalRows_documentos/$maxRows_documentos)-1;

$queryString_documentos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_documentos") == false && 
        stristr($param, "totalRows_documentos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_documentos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_documentos = sprintf("&totalRows_documentos=%d%s", $totalRows_documentos, $queryString_documentos);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_doc_totales = "SELECT * FROM proced_documentos WHERE IDvisible = 1";
$doc_totales = mysql_query($query_doc_totales, $vacantes) or die(mysql_error());
$row_doc_totales = mysql_fetch_assoc($doc_totales);
$totalRows_doc_totales = mysql_num_rows($doc_totales);

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
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<link href="global_assets/css/extras/animate.min.css" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
	<script src="global_assets/js/demo_pages/animations_css3.js"></script>    
	<!-- /theme JS files -->
	
	<script>
	 setTimeout(function(){
    $('.alert').fadeTo("slow", 0.1, function(){
        $('.alert').alert('close')
    });     
    }, 3000)    
    </script>

</head>

<body class="has-detached-right <?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>">

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

					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
            <?php if (empty($filtroBuscado) && empty($filtroIDdireccion) && empty($filtroIDDarea) && empty($filtroIDsubarea)) { ?>
									<div class="alert alert-success alert-styled-left alert-arrow-left" id="saveAlert">
										<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
										Actualmente tenemos <span class="text-bold"><?php echo $totalRows_doc_totales ?> </span>documentos publicados.
								    </div>                
            <?php  } ?>


				<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Normatividad Interna</h5>
						</div>
					<div class="panel-body">
							Bienvenido, en este apartado podrás consultar los manuales, códigos y reglamentos que rigen a la empresa.</br>
							Utiliza el buscador para encontrar el documento que necesitas, o bien, el menú de la derecha para buscarlo por área.
					</div>
				</div>


                    <!-- Search field -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Buscar</h5>
						</div>

						<div class="panel-body">
							<form action="procedimientos.php" method="post" class="main-search">
								<div class="input-group content-group">
									<div class="has-feedback has-feedback-left">
										<input type="text" class="form-control input-xlg" name="buscado" required id="buscado" value="" placeholder="<?php 
										if (isset($_POST['buscado'])) {echo $_POST['buscado']; } else {echo "Ingresa nombre o tema del documento a buscar..."; } ?>">
										<div class="form-control-feedback">
											<i class="icon-search4 text-muted text-size-base"></i>
										</div>
									</div>

									<div class="input-group-btn">
										<button type="submit" class="btn btn-primary btn-xlg">Buscar</button>
									</div>
								</div>
                                
                               <?php 		if (isset($_POST['buscado']) && $totalRows_documentos  > 0) { ?> 
							   
    							<ul class="list-inline list-inline-condensed no-margin-bottom">
								<li><a href="#" class="btn btn-default"><i class="icon-filter4"></i><strong>  <?php echo $totalRows_documentos; ?></strong> Documentos encontrados.</a></li>
								<li><a href="procedimientos.php" class="btn btn-danger btn-xs">Borrar Filtro</a></li>
								</ul>


							   <?php } else if (isset($_POST['buscado']) && $totalRows_documentos == 0) {  ?> 
							   <div class="alert alert-warning no-border">
										<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
										<span class="text-semibold">Ups!</span> No se encontraron documentos con el filtro seleccionado.
								    </div>
									<?php } ?>

							</form>
							<ul class="list-inline list-inline-condensed no-margin-bottom">
							<?php if ($totalRows_direcciona > 0 OR $totalRows_areaa > 0 OR $totalRows_subareaa > 0) { ?>
								<li><a href="#" class="btn btn-default"><i class="icon-filter4"></i><strong> Filtro Actual:</strong> <?php echo $totalRows_documentos; ?> Documentos </a></li>
							<?php } ?>
							
							<?php if ($totalRows_direcciona > 0) { ?>
								<li><a href="#" class="btn btn-default"><strong>Dirección:</strong> <?php echo $row_direcciona['direccion']; ?></a></li>
							<?php } ?>
							<?php if ($totalRows_areaa > 0) { ?>
								<li><a href="#" class="btn btn-default"><strong>Área:</strong> <?php echo $row_areaa['area']; ?></a></li>
							<?php } ?>
							<?php if ($totalRows_subareaa > 0) { ?>
								<li><a href="#" class="btn btn-default"><strong>Subárea:</strong> <?php echo $row_subareaa['subarea']; ?></a></li>
							<?php } ?>
							</ul>
							
						</div>
						</div>
					<!-- /search field -->
					
					

						<?php 
						
						if ($totalRows_documentos > 0) {
						
						do {
                        $valores1 = explode(".", $row_documentos['file']);
                        $resultado_file1 = $valores1[count($valores1)-1];
                        ?>
							<!-- List -->
							<ul class="media-list">
								<li class="media panel panel-body stack-media-on-mobile">
                                <div class="media-left">
											<?php if ($resultado_file1 == 'pdf')  { ?>
                                             <button class="btn bg-danger-400 btn-block mt-15 btn-float btn-float-lg" type="button"><i class="icon-file-pdf"></i><span>Pdf</span></button>
											<?php } else if ($resultado_file1 == 'doc' or $resultado_file1 == 'docx')  { ?>                                            
                                             <button class="btn bg-primary-400 btn-block mt-15 btn-float btn-float-lg" type="button"><i class="icon-file-word"></i><span>Doc</span></button>
                                            <?php } else if ($resultado_file1 == 'xls' or $resultado_file1 == 'xlsx')  { ?>
                                             <button class="btn bg-success-400 btn-block mt-15 btn-float btn-float-lg" type="button"><i class="icon-file-excel"></i><span>Xls</span></button>
                                            <?php } else if ($resultado_file1 == 'ppt' or $resultado_file1 == 'pptx')  { ?>
                                             <button class="btn bg-warning-400 btn-block mt-15 btn-float btn-float-lg" type="button"><i class=" icon-file-presentation"></i><span>Ppt</span></button>
                                            <?php } else if ($resultado_file1 == 'jpg' or $resultado_file1 == 'jepg' 
										    			  or $resultado_file1 == 'png' or $resultado_file1 == 'gif')  { ?>
                                             <button class="btn bg-info btn-block mt-15 btn-float btn-float-lg" type="button"><i class="icon-file-picture"></i><span>Img</span></button>
                                            <?php } else if ($resultado_file1 == 'zip' or $resultado_file1 == 'rar')  { ?>
                                             <button class="btn bg-teal-400 btn-block mt-15 btn-float btn-float-lg" type="button"><i class="icon-file-zip"></i><span>Zip</span></button>
                                            <?php }?>
									</div>

									<div class="media-body">
										<h6 class="media-heading text-bold text-danger">
											<?php echo $row_documentos['documento']; ?>
										</h6>

										<ul class="list-inline list-inline-separate mb-10">
											<?php if ($row_documentos['IDdireccion'] != 0) { ?><li><a href="procedimientos.php?IDdireccion=<?php echo $row_documentos['IDdireccion'] ?>" class="text-muted"><?php echo $row_documentos['direccion']; ?></a></li>
											<?php } else { ?><li>&nbsp;</li><?php } ?>
											<?php if ($row_documentos['IDDarea'] != 0) { ?><li><a href="procedimientos.php?IDdireccion=<?php echo $row_documentos['IDdireccion'] ?>&IDDarea=<?php echo $row_documentos['IDDarea'] ?>" class="text-muted"><?php echo $row_documentos['area']; ?></a></li>
											<?php } else { ?><li>&nbsp;</li><?php } ?>
											<?php if ($row_documentos['IDsubarea'] != 0) { ?><li><a href="procedimientos.php?IDdireccion=<?php echo $row_documentos['IDdireccion'] ?>&IDDarea=<?php echo $row_documentos['IDDarea'] ?>&IDsubarea=<?php echo $row_documentos['IDsubarea'] ?>" class="text-muted"><?php echo $row_documentos['subarea']; ?></a></li>
											<?php } else { ?><li>&nbsp;</li><?php } ?>
										</ul>

										<p class="content-group-sm"><strong>Descripción:</strong> <?php echo $row_documentos['descripcion']; ?></p>

										<ul class="list-inline list-inline-separate">
											<li><strong>Versión: </strong><a href="#"><?php echo $row_documentos['version']; ?></a></li>
											<li><strong>Año: </strong> <a href="#"><?php echo date('Y', strtotime($row_documentos['anio'])); ?></a></li>
											<li><strong>Tipo de Documento: </strong> <a href="#"><?php echo $row_documentos['tipo']; ?></a></li>
										</ul>
									</div>

									<div class="media-right text-center">
									
									<?php if ($resultado_file1 == 'pdf')  { ?>
									<button type="button" data-target="#modal_theme_danger2<?php echo $row_documentos['IDdocumento']; ?>" data-toggle="modal" class="btn bg-primary-400 mt-15 btn-block"><i class="icon-file-eye2"></i> Visualizar</a></button>
									<?php } ?>

									<a href="proced/<?php echo $row_documentos['IDdireccion'] ?>/<?php echo $row_documentos['IDDarea'] ?>/<?php echo $row_documentos['IDsubarea'] ?>/<?php echo $row_documentos['file'] ?>" class="btn bg-success-400 mt-15 btn-block"><i class="icon-file-download2 position-left"></i>Descargar</a>
									
									
											<?php
		// menu A
		$si_anexos = $row_documentos['IDdocumento'];
		$query_anexos = "SELECT * FROM proced_anexos WHERE IDdocumento = $si_anexos";
		$anexos = mysql_query($query_anexos, $vacantes) or die(mysql_error());
		$row_anexos = mysql_fetch_assoc($anexos);
		$totalRows_anexos = mysql_num_rows($anexos);
		if ($totalRows_anexos > 0) {
		 ?>

									<button type="button" data-target="#modal_theme_danger<?php echo $row_documentos['IDdocumento']; ?>" data-toggle="modal" class="btn bg-warning-400 mt-15 btn-block"><i class="icon-folder-search"></i> Anexos</a></button>

					<!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_documentos['IDdocumento']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-warning">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Anexos</h6>
								</div>

								<div class="modal-body text-justify">
								<p><strong>Documento:</strong> <?php echo $row_documentos['documento']; ?>.</p>
								<p><strong>Tipo de documento:</strong> <?php echo $row_documentos['tipo']; ?>.</p>
								<p><strong>Anexos:</strong></p>
						
										<ul class="media-list">
						<?php do {
                        $valores2 = explode(".", $row_anexos['file']);
                        $resultado_file2 = $valores2[count($valores2)-1];
						?>
						
											<li><a href="proced/anexos/<?php echo $row_anexos['file'] ?>" target="_blank"> &nbsp; 
											
											<?php if ($resultado_file2 == 'pdf')  { ?>
                                            <i class="icon-file-pdf"></i>
											<?php } else if ($resultado_file2 == 'doc' or $resultado_file2 == 'docx')  { ?>                                            
                                            <i class="icon-file-word"></i>
                                            <?php } else if ($resultado_file2 == 'xls' or $resultado_file2 == 'xlsx')  { ?>
                                            <i class="icon-file-excel"></i>
                                            <?php } else if ($resultado_file2 == 'ppt' or $resultado_file2 == 'pptx')  { ?>
                                            <i class=" icon-file-presentation"></i>
                                            <?php } else if ($resultado_file2 == 'jpg' or $resultado_file2 == 'jepg' 
										    			  or $resultado_file2 == 'png' or $resultado_file2 == 'gif')  { ?>
                                            <i class="icon-file-picture"></i>
                                            <?php } else if ($resultado_file2 == 'zip' or $resultado_file2 == 'rar')  { ?>
                                            <i class="icon-file-zip"></i>

                                            <?php }?>

											&nbsp;<?php echo $row_anexos['documento']; ?></a>:&nbsp; <?php echo $row_anexos['descripcion']; ?>.</li> 
						<?php } while ($row_anexos = mysql_fetch_assoc($anexos)); ?>
										</ul>
						
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
		<?php } ?>
									
									
										
									</div>
								</li>
							</ul>
							
							
					<!-- danger modal -->
					<div id="modal_theme_danger2<?php echo $row_documentos['IDdocumento']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Lectura de Documento</h6>
								</div>

								<div class="modal-body">
								<object data="proced/<?php echo $row_documentos['IDdireccion'] ?>/<?php echo $row_documentos['IDDarea'] ?>/<?php echo $row_documentos['IDsubarea'] ?>/<?php echo $row_documentos['file'] ?>" type="application/pdf"  width="100%" height="600">
								  
								</object>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


							<!-- /list -->
						<?php } while ($row_documentos = mysql_fetch_assoc($documentos)); }?>


							<!-- Pagination -->
							<div class="text-center content-group-lg pt-20">
								<ul class="pagination">
                                
                                		<?php if ($pageNum_documentos > 0) { ?>
                                        <li><a href="<?php printf("%s?pageNum_documentos=%d%s", $currentPage, 0, $queryString_documentos); ?>"><< Inicio </a></li>
                                        <?php } ?><?php if ($pageNum_documentos > 0) { ?>
                                        <li><a href="<?php printf("%s?pageNum_documentos=%d%s", $currentPage, max(0, $pageNum_documentos - 1), $queryString_documentos); ?>">< Anterior </a></li>
                                        <?php } ?><?php if ($pageNum_documentos < $totalPages_documentos) { ?>
                                        <li><a href="<?php printf("%s?pageNum_documentos=%d%s", $currentPage, min($totalPages_documentos, $pageNum_documentos + 1), $queryString_documentos); ?>">Siguiente > </a></li>
                                        <?php } ?><?php if ($pageNum_documentos < $totalPages_documentos) {  ?>
                                        <li><a href="<?php printf("%s?pageNum_documentos=%d%s", $currentPage, $totalPages_documentos, $queryString_documentos); ?>">Último >> </a></li>
                                        <?php } ?>
									
								</ul>
							</div>
							<!-- /pagination -->

                        

						</div>
					</div>
					<!-- /detached content -->



					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">
                            
                            
                            
	<!-- Categories -->
	<div class="sidebar-category">
		<div class="category-title">
			<span>Áreas</span>
		</div>
		<p>&nbsp;</p>
											
		<ul><li style="list-style: none;"><a style="color:#352C2C;" href="procedimientos.php"><i class="icon-home2"></i> <strong> Inicio</a></strong></li></ul>
		
		<?php
		// menu A
		$query_direcciones = "SELECT * FROM proced_direcciones";
		$direcciones = mysql_query($query_direcciones, $vacantes) or die(mysql_error());
		$row_direcciones = mysql_fetch_assoc($direcciones);
		$totalRows_direcciones = mysql_num_rows($direcciones);
		 ?>
			<ul>
				<?php do {  
		$la_direccion = $row_direcciones['IDdireccion'];
		$query_direcciones_count = "SELECT * FROM proced_documentos WHERE IDdireccion = $la_direccion AND IDDarea = '' AND IDsubarea = '' AND IDvisible = 1";
		$direcciones_count = mysql_query($query_direcciones_count, $vacantes) or die(mysql_error());
		$row_direcciones_count = mysql_fetch_assoc($direcciones_count);
		$totalRows_direcciones_count = mysql_num_rows($direcciones_count);
				?>

						<li style="list-style: none;">
							<strong><a style="color:#352C2C;" href="procedimientos.php?IDdireccion=<?php echo $row_direcciones['IDdireccion'] ?>"><i class="icon-forward3"></i> <?php echo $row_direcciones['direccion'] ?></a> (<?php echo $totalRows_direcciones_count; ?>)</strong>
							
			<?php
			// menu B
			$la_direccion = $row_direcciones['IDdireccion'];
			$query_areas = "SELECT * FROM proced_areas WHERE IDdireccion = $la_direccion";
			$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
			$row_areas = mysql_fetch_assoc($areas);
			$totalRows_areas = mysql_num_rows($areas);
			 ?>
							<ul>
					<?php if ($totalRows_areas > 0) { do { 
		$la_area = $row_areas['IDDarea'];
		$query_areas_count = "SELECT * FROM proced_documentos WHERE IDdireccion = $la_direccion AND IDDarea = $la_area AND IDsubarea = ''  AND IDvisible = 1";
		$areas_count = mysql_query($query_areas_count, $vacantes) or die(mysql_error());
		$row_areas_count = mysql_fetch_assoc($areas_count);
		$totalRows_areas_count = mysql_num_rows($areas_count);
					?>
									<li style="list-style: none;">
					<?php if ($row_areas['IDDarea'] == 93) { echo "<strong>"; } ?>
					<a href="procedimientos.php?IDdireccion=<?php echo $row_direcciones['IDdireccion'] ?>&IDDarea=<?php echo $row_areas['IDDarea'] ?>"><i class="icon-arrow-right5"></i> <?php echo $row_areas['area'] ?> (<?php echo $totalRows_areas_count; ?>)</a>
					<?php if ($row_areas['IDDarea'] == 93) { echo "</strong>"; } ?>					
									
				<?php
				// menu C
				$el_area = $row_areas['IDDarea'];
				$query_subarea = "SELECT * FROM proced_subareas WHERE IDDarea = $el_area";
				$subarea = mysql_query($query_subarea, $vacantes) or die(mysql_error());
				$row_subarea = mysql_fetch_assoc($subarea);
				$totalRows_subarea = mysql_num_rows($subarea);
				 ?>
						<ul><?php if ($totalRows_subarea > 0) { do {  
		$la_subarea = $row_subarea['IDsubarea'];
		$query_subareas_count = "SELECT * FROM proced_documentos WHERE IDdireccion = $la_direccion AND IDDarea = $la_area AND IDsubarea = $la_subarea  AND IDvisible = 1";
		$subareas_count = mysql_query($query_subareas_count, $vacantes) or die(mysql_error());
		$row_subareas_count = mysql_fetch_assoc($subareas_count);
		$totalRows_subareas_count = mysql_num_rows($subareas_count);
						?>
									
									<li style="list-style: none;"><i class="icon-arrow-right22"></i> <a href="procedimientos.php?IDdireccion=<?php echo $row_direcciones['IDdireccion'] ?>&IDDarea=<?php echo $row_areas['IDDarea'] ?>&IDsubarea=<?php echo $row_subarea['IDsubarea'] ?>"><?php echo $row_subarea['subarea'] ?> (<?php echo $totalRows_subareas_count; ?>)</a></li>
									
						<?php }  while ($row_subarea = mysql_fetch_assoc($subarea));  }?></ul>
									
								</li>
					<?php } while ($row_areas = mysql_fetch_assoc($areas)); ?>
							</ul>
							
						</li>
						
					<?php }  } while ($row_direcciones = mysql_fetch_assoc($direcciones)); ?>
			</ul>
		
		</div>
	<!-- /categories -->
	
								<!-- Assigned users -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-users position-left"></i>Contacto</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">
									<ul class="media-list">
										<li class="media">
											<div class="media-body media-middle text-semibold">
												Susana López Pérez
												<div class="media-annotation">Jefe de Métodos y Procedimientos</div>
											</div>
											<div>
												<ul style="list-style: none;">
													<li><a href="#"><i class="icon-phone2 "></i> 1223</a></li>
													<li><a href="mailto:slopezp@sahuayo.mx"><i class="icon-mail5"></i> slopezp@sahuayo.mx</a></li>
												</ul>
											</div>
										</li>


									</ul>
								</div>
							</div>
							<!-- /assigned users -->
                                
	</div>

						</div>
					</div>
		            <!-- /detached sidebar -->


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