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

$currentPage = $_SERVER["PHP_SELF"];

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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

$el_tipo = $_GET['IDtipo_plaza'];
$el_puesto  = $_GET['IDpuesto'];
$el_estatus = 1;

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_plantilla.IDpuesto, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDtipo_plaza, prod_plantilla.IDestatus, prod_plantilla.fecha_inicio, prod_plantilla.fecha_fin, prod_plantilla.fecha_congelada, vac_puestos.denominacion, vac_puestos.dias, vac_puestos.dias, vac_areas.IDarea, vac_areas.area FROM prod_plantilla LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_plantilla.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE prod_plantilla.IDmatriz = '$la_matriz' AND prod_plantilla.IDtipo_plaza = $el_tipo AND prod_plantilla.IDpuesto = $el_puesto AND prod_plantilla.IDestatus = $el_estatus"; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
?>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="inc_cap_puesto.php" >
									<div class="modal-body">

									

					<table class="table">
                    			<thead>
                                  <tr> 
                                    <th>Denominacion</th>
                                    <th>Tipo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Cierre</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_detalle['denominacion'];  ?>&nbsp; </td>
                                      <td><?php if($row_detalle['IDtipo_plaza'] == 1 ) { echo "Planta"; }
									  	   else if($row_detalle['IDtipo_plaza'] == 2 ) { echo "Temporal"; }
										   else if($row_detalle['IDtipo_plaza'] == 3 ) { echo "Congelada"; } ?></td>
                                      <td><?php echo date( 'd/m/Y', strtotime($row_detalle['fecha_inicio']));  ?>&nbsp; </td>
                                      <td><?php if($row_detalle['fecha_fin'] == '') { echo "Sin fecha";} else { echo date( 'd/m/Y', strtotime($row_detalle['fecha_fin'])); } ?>&nbsp; </td>
                                    </tr>
                                    <?php } while ($row_detalle = mysql_fetch_assoc($detalle)); ?>
                                </tbody>
                                </table>


                                           
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                 </form>

