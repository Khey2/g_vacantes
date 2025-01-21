<?php require_once('Connections/vacantes.php'); ?>
<?php 

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$IDempleado = $_GET['IDempleado'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el a�o anterior 
$semana = date("W", strtotime($la_fecha));

mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.descripcion_nomina, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDpuesto, prod_activos.IDarea, vac_areas.area, inc_vacaciones.IDdias_pendientes, inc_vacaciones.IDdias_asignados, inc_vacaciones.fecha_inicio, inc_vacaciones.fecha_fin FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN inc_vacaciones ON prod_activos.IDempleado = inc_vacaciones.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'"; 
mysql_query("SET NAMES 'utf8'");
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);


?>
<head>
<script type="text/javascript">
    $('.daterange-basic').daterangepicker({
        applyClass: 'btn-primary',
        cancelClass: 'btn-default',
		locale: {monthNames: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ags', 'Sep', 'Oct', 'Nov', 'Dic']},
		startDate: moment().add(1, 'days'),
        endDate: moment().add(2, 'days')
		});
</script>
</head>
<body>

                                  No. Emp. : <?php echo $row_detalle['IDempleado']; ?><br />
								  Nombre: <?php echo $row_detalle['emp_paterno']; ?> <?php echo $row_detalle['emp_materno']; ?> <?php echo $row_detalle['emp_nombre']; ?><br />
								  &Aacute;rea:  <?php echo $row_detalle['area']; ?><br />
								  Puesto:  <?php echo $row_detalle['denominacion']; ?><br />

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="vacaciones.php" >
									<div class="modal-body">
                                                                       
                                    <input type="hidden" name="emp_paterno" value="<?php echo $row_detalle['emp_paterno']; ?>" >
                                    <input type="hidden" name="emp_materno" value="<?php echo $row_detalle['emp_materno']; ?>" >
                                    <input type="hidden" name="emp_nombre" value="<?php echo $row_detalle['emp_nombre']; ?>" >
                                    <input type="hidden" name="IDempleado" value="<?php echo $row_detalle['IDempleado']; ?>" >
                                    <input type="hidden" name="IDpuesto" value="<?php echo $row_detalle['IDpuesto']; ?>" >
                                    <input type="hidden" name="denominacion" value="<?php echo $row_detalle['denominacion']; ?>" >
                                    <input type="hidden" name="fecha_alta" value="<?php echo $row_detalle['fecha_alta']; ?>" >
                                    <input type="hidden" name="IDmatriz" value="<?php echo $row_detalle['IDmatriz']; ?>" >
                                    <input type="hidden" name="IDsucursal" value="<?php echo $row_detalle['IDsucursal']; ?>" >
                                    <input type="hidden" name="IDarea" value="<?php echo $row_detalle['IDarea']; ?>" >
                                    <input type="hidden" name="area" value="<?php echo $row_detalle['area']; ?>" >									
                                	<input type="hidden" name="MM_insert" value="form1">


									<div class="form-group">
										<div class="row">
										<label class="control-label col-sm-3">Periodo:<span class="text-danger">*</span></label>
			                        <div class="col-sm-9">
									<select name="IDperiodo" class="form-control" required="required">
										<option value="4">2024</option>
										<option value="2">2023</option>
										<option value="1">2022</option>
										<option value="3">2021</option>
									</select>
										</div>
									</div>
									</div>



									<!-- Fecha -->
                                    <div class="form-group">
			                            <div class="row">
										<label class="control-label col-sm-3">Fechas:<span class="text-danger">*</span></label>
			                        <div class="col-sm-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control daterange-basic" name="fecha_inicio" id="fecha_inicio" value="" required="required">
									</div>
									</div>
									</div> 
									</div> 
									<!-- Fecha -->
									</div>									
									
									
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
										<input type="submit" class="btn btn-primary" value="Capturar">										
									</div>
                                
								</form>
                                          