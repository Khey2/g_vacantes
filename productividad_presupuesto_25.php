<table border="1">
      							              <thead> 
                                                <tr class="bg-teal-400">
                                                  <th>Puesto</th>
                                                  <th>Área</th>
                                                  <th>Autorizados</th>
                                                  <th>Activos</th>
                                                  <th>Sueldo</th>
                                                  <th>Productividad</th>
                                                  <th>Asistencia</th>
                                                  <th>Presupuesto</th>
                                                </tr>
                                                </thead>
                                                <tbody>
<?php 
require_once('Connections/vacantes.php'); 
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$desfase = $row_variables['dias_desfase'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 

$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$semana_previa = $semana - 1;
if ($semana == 1) {$semana_previa = 52;}

$anio = $row_variables['anio'];
$anio_previo = $anio;
if ($semana == 1) {$anio_previo = $anio - 1;}

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = $colname_usuario";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
if (isset($_GET['IDmatriz'])) {$IDmatriz = $_GET['IDmatriz'];} else {$IDmatriz = $row_usuario['IDmatriz'];} 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$fecha_filtro = date('Y/m/d', strtotime('monday -1 week'));
$chofers = array(42, 43, 44, 45, 57, 372);

$MontoZ = 0;
$AutorizadosZ = 0;

// RECORRER TODOS LOS PUESTOS 
$query_puestos_aplicablesB = "SELECT vac_puestos.denominacion, vac_puestos.IDaplica_PROD, prod_plantilla.IDplantilla, prod_plantilla.IDmatriz, prod_plantilla.IDsucursal, prod_plantilla.IDpuesto, prod_plantilla.IDestatus, prod_plantilla.IDtipo_plaza  FROM prod_plantilla LEFT JOIN vac_puestos ON prod_plantilla.IDpuesto = vac_puestos.IDpuesto  WHERE prod_plantilla.IDmatriz = $IDmatriz AND vac_puestos.IDaplica_PROD = 1  AND prod_plantilla.IDestatus = 1  AND ( DATE ( fecha_inicio ) <= '$fecha_filtro' )  AND ( DATE ( fecha_fin ) > '$fecha_filtro' OR DATE ( fecha_fin ) = '0000-00-00' OR DATE ( fecha_fin ) IS NULL )  AND ( DATE ( fecha_congelada ) > '$fecha_filtro' OR DATE ( fecha_congelada ) = '0000-00-00' OR DATE ( fecha_congelada ) IS NULL ) GROUP BY prod_plantilla.IDpuesto";
$puestos_aplicablesB = mysql_query($query_puestos_aplicablesB, $vacantes) or die(mysql_error());
$row_puestos_aplicablesB = mysql_fetch_assoc($puestos_aplicablesB);
$totalRows_puestos_aplicablesB = mysql_num_rows($puestos_aplicablesB);

do {

$IDpuestoB = $row_puestos_aplicablesB['IDpuesto']; 

// detalles del puesto para pintar el área
$query_el_puestoB = "SELECT vac_puestos.*, vac_areas.area FROM vac_puestos LEFT JOIN vac_areas ON  vac_puestos.IDarea = vac_areas.IDarea WHERE IDpuesto = $IDpuestoB";
$el_puestoB = mysql_query($query_el_puestoB, $vacantes) or die(mysql_error());
$row_el_puestoB = mysql_fetch_assoc($el_puestoB);
$totalRows_el_puestoB = mysql_num_rows($el_puestoB);
$el_areaB = $row_el_puestoB['area'];

// activos para mostrar activos
$query_activosB = "SELECT prod_activos.*  FROM prod_activos WHERE IDmatriz = $IDmatriz AND IDpuesto IN ($IDpuestoB)";
$activosB = mysql_query($query_activosB, $vacantes) or die(mysql_error());
$row_activosB = mysql_fetch_assoc($activosB);
$totalRows_activosB = mysql_num_rows($activosB);

// plantilla autorizada
$query_plantillaB = "SELECT Count(prod_plantilla.IDplantilla) AS Plantilla FROM prod_plantilla WHERE IDmatriz = $IDmatriz AND IDpuesto IN ($IDpuestoB) AND prod_plantilla.IDestatus = 1 AND ( DATE ( fecha_inicio ) <= '$fecha_filtro' ) AND ( DATE ( fecha_fin ) > '$fecha_filtro' OR DATE ( fecha_fin ) = '0000-00-00' OR DATE ( fecha_fin ) IS NULL ) AND ( DATE ( fecha_congelada ) > '$fecha_filtro' OR DATE ( fecha_congelada ) = '0000-00-00' OR DATE ( fecha_congelada ) IS NULL )";
$plantillaB = mysql_query($query_plantillaB, $vacantes) or die(mysql_error());
$row_plantillaB = mysql_fetch_assoc($plantillaB);
$totalRows_plantillaB = mysql_num_rows($plantillaB);

// monto segun activos y garantia
$query_presupuesto_cajasB = "SELECT SUM(vac_tabulador.variable_mensual / 30 ) * 7 As MontoA, SUM(vac_tabulador.asistencia_mensual / 30 ) * 7 As MontoB FROM prod_activos LEFT JOIN vac_tabulador ON prod_activos.IDpuesto = vac_tabulador.IDpuesto AND prod_activos.IDmatriz = vac_tabulador.IDmatriz AND prod_activos.IDnivel_antiguedad = vac_tabulador.IDnivel WHERE prod_activos.IDmatriz = $IDmatriz AND prod_activos.IDpuesto = $IDpuestoB";
$presupuesto_cajasB = mysql_query($query_presupuesto_cajasB, $vacantes) or die(mysql_error()); 
$row_presupuesto_cajasB = mysql_fetch_assoc($presupuesto_cajasB);
$totalRows_presupuesto_cajasB = mysql_num_rows($presupuesto_cajasB);
$Monto_sueldosB = $row_presupuesto_cajasB['MontoA'];
$Monto_asistenciaB = $row_presupuesto_cajasB['MontoB'];
	
// nivel minimo para la sucursal
$query_minimo_tabuladorB = "SELECT * FROM prod_valor_antiguedad WHERE IDmatriz = $IDmatriz AND IDpuesto = $IDpuestoB AND meses_inicio = 0";
$minimo_tabuladorB = mysql_query($query_minimo_tabuladorB, $vacantes) or die(mysql_error());
$row_minimo_tabuladorB = mysql_fetch_assoc($minimo_tabuladorB);
$totalRows_minimo_tabuladorB = mysql_num_rows($minimo_tabuladorB);
$Nivel_minimoB = $row_minimo_tabuladorB['IDnivel'];

// Monto de la garantia en porcentaje para recien ingresos
$query_garantia_tabuladorB = "SELECT * FROM prod_garantias WHERE IDmatriz = $IDmatriz AND IDpuesto = $IDpuestoB AND IDnivel = '$Nivel_minimoB'";
$garantia_tabuladorB = mysql_query($query_garantia_tabuladorB, $vacantes) or die(mysql_error());
$row_garantia_tabuladorB = mysql_fetch_assoc($garantia_tabuladorB);
$totalRows_garantia_tabuladorB = mysql_num_rows($garantia_tabuladorB);
//$Monto_garantiaB = $row_garantia_tabuladorB['garantia']; 

// Tabulador autorizado
$query_tabuladorB = "SELECT * FROM vac_tabulador WHERE IDmatriz = $IDmatriz AND IDpuesto = $IDpuestoB AND IDnivel = 'A'";
$tabuladorB = mysql_query($query_tabuladorB, $vacantes) or die(mysql_error()); 
$row_tabuladorB = mysql_fetch_assoc($tabuladorB);
$totalRows_tabuladorB = mysql_num_rows($tabuladorB);
$Monto_garantiaB = ($row_tabuladorB['variable_mensual']/30)*7;
$Monto_sueldo_tabuladorB = $row_tabuladorB['sueldo_diario'] * 7;
$Monto_asistencia_tabuladorB =  ($row_tabuladorB['asistencia_mensual']/30)*7;

// diferencia de plazas
$Monto_2B = 0;
if ($row_plantillaB['Plantilla'] > $totalRows_activosB ) {
$diferencia_plazasB = $row_plantillaB['Plantilla'] - $totalRows_activosB; 
$Monto_2B = $diferencia_plazasB * $Monto_garantiaB;
$Monto_3B = $row_plantillaB['Plantilla'] * $Monto_asistenciaB;
} else {
$Monto_2B = 0;
$Monto_3B = $row_plantillaB['Plantilla'] * $Monto_asistenciaB;
}

$Monto_3B = $Monto_sueldosB + $Monto_asistenciaB;
$Monto_4B = $Monto_2B + $Monto_3B;
echo "<tr><td>".$row_puestos_aplicablesB['denominacion']."</td>";
echo "<td>".$el_areaB."</td>";
echo "<td>".$row_plantillaB['Plantilla']."</td>";
echo "<td>".$totalRows_activosB."</td>";
echo "<td>$" .number_format($Monto_sueldo_tabuladorB,2)."</td>";
echo "<td>$" .number_format($Monto_garantiaB,2)."</td>";
echo "<td>$" .number_format($Monto_asistencia_tabuladorB,2)."</td>";
echo "<td>$" .number_format($Monto_4B,2)."</td></tr>";
$MontoZ = $MontoZ + $Monto_4B;
$AutorizadosZ = $AutorizadosZ + $row_plantillaB['Plantilla'];

} while ($row_puestos_aplicablesB = mysql_fetch_assoc($puestos_aplicablesB)); 

mysql_select_db($database_vacantes, $vacantes);
$query_adicional = "SELECT prod_meses_presupesto_adicional.*, SUM(prod_meses_presupesto_adicional.resultado) as Adicional FROM prod_meses_presupesto_adicional WHERE IDmatriz = '$IDmatriz' AND IDanio = '$anio' AND IDsemana = $semana AND IDestatus = 1";
$adicional = mysql_query($query_adicional, $vacantes) or die(mysql_error());
$row_adicional = mysql_fetch_assoc($adicional);
$totalRows_adicional = mysql_num_rows($adicional);
$aplica_adicional = $row_adicional['Adicional'];
?>
												
         							              <tfoot> 
                                                <tr>
                                                  <th colspan="2">Total Semanal</th>
                                                  <th><?php echo $AutorizadosZ; ?></th>	
                                                  <th colspan="4"></th>
                                                  <th><?php  if ($aplica_adicional != 0) { $MontoZ = $MontoZ + $aplica_adicional;  echo "$".number_format(round($MontoZ,2)); }  else {  echo "$".number_format(round($MontoZ,2)); }?></th>
                                                </tr>
                                                </tfoot>
                                             </tbody>
                                              </table>
