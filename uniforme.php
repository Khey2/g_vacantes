<?php require_once('Connections/vacantes.php'); ?> 
<?php

require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$actualusuario = $_SESSION['kt_login_id'];
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$actualusuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$IDusuario = $row_usuario['IDusuario'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDempleado = $_GET['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);
$IDpuesto = $row_activos['IDpuesto']; 
$curp = $row_activos['curp'];
$sexo = substr($curp, 10, 1);

//pantalon_ventas
$pantalon_ventas = array(88, 13, 83, 134, 162, 163, 164, 165, 168, 169, 171, 172, 173, 175, 176, 181, 183, 188, 189, 190, 192, 193, 194, 197, 211, 215, 216, 226, 239, 287, 297, 335, 338, 339, 343, 352, 354, 360, 366, 370, 381, 400, 419, 512, 541, 184, 212, 229, 235, 236, 308, 309, 328, 359, 484);
//pantalon_operaciones
$pantalon_operaciones = array(1, 2, 3, 4, 6, 7, 9, 10, 11, 14, 15, 16, 17, 52, 270, 273, 281, 289, 290, 291, 336, 337, 371, 392, 511, 18, 20, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 49, 50, 53, 54, 55, 56, 223, 313, 322, 327, 348, 372, 373, 376, 377, 378, 402, 57, 58);
//camisa_ventas
$camisa_ventas = array(8, 13, 83, 134, 162, 163, 164, 165, 168, 169, 171, 172, 173, 175, 176, 181, 183, 188, 189, 190, 192, 193, 194, 197, 211, 215, 216, 226, 239, 287, 297, 335, 338, 339, 343, 352, 354, 360, 366, 370, 381, 400, 419, 512, 541, 184, 212, 229, 235, 236, 308, 309, 328, 359, 484);
//playera_polo_distribucion
$playera_polo_distribucion = array(38, 39, 40, 42, 43, 44, 45, 46, 47, 49, 50, 53, 54, 55, 56, 223, 322, 327, 348, 372, 373, 376, 377, 378, 402, 57, 58);
//playera_roja_almacen
$playera_roja_almacen = array(1, 2, 3, 4, 6, 7, 9, 10, 11, 14, 15, 16, 17, 41, 52, 270, 273, 281, 289, 290, 291, 313, 336, 337, 371, 392, 511, 18, 20);
//equipo_proteccion
$equipo_proteccion = array(1, 2, 3, 4, 6, 7, 9, 10, 11, 14, 15, 16, 17, 52, 270, 273, 281, 289, 290, 291, 336, 337, 371, 392, 511, 18, 20, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 49, 50, 53, 54, 55, 56, 223, 313, 322, 327, 348, 372, 373, 376, 377, 378, 402, 57, 58);
//todos para mostrar no aplica
$todos = array(1, 2, 3, 4, 6, 7, 9, 10, 11, 14, 15, 16, 17, 52, 270, 273, 281, 289, 290, 291, 336, 337, 371, 392, 511, 18, 20, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 49, 50, 53, 54, 55, 56, 223, 313, 322, 327, 348, 372, 373, 376, 377, 378, 402, 57, 58, 8, 13, 83, 134, 162, 163, 164, 165, 168, 169, 171, 172, 173, 175, 176, 181, 183, 188, 189, 190, 192, 193, 194, 197, 211, 215, 216, 226, 239, 287, 297, 335, 338, 339, 343, 352, 354, 360, 366, 370, 381, 400, 419, 512, 541, 184, 212, 229, 235, 236, 308, 309, 328, 359, 484);
//licencia
$licencia = array(1, 2, 3, 4, 6, 7, 9, 10, 11, 14, 15, 16, 17, 52, 270, 273, 281, 289, 290, 291, 336, 337, 371, 392, 511, 18, 20, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 49, 50, 53, 54, 55, 56, 223, 313, 322, 327, 348, 372, 373, 376, 377, 378, 402, 57, 58);


mysql_select_db($database_vacantes, $vacantes);
$query_encuesta = "SELECT * FROM sed_uniformes WHERE IDempleado = $IDempleado";
$encuesta = mysql_query($query_encuesta, $vacantes) or die(mysql_error());
$row_encuesta = mysql_fetch_assoc($encuesta);
$totalRows_encuesta = mysql_num_rows($encuesta);
?>


<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="uniformes.php">
	<div class="modal-body">                                   

									<p>Captura la información solicitada:<br/>
										a) En uniforme, la fecha solicitada es la de la última entrega.<br/>
										b) En Licencia de Manejo, la fecha solicitada es la de vigencia.<br/>
									</p>
									<p>&nbsp;</p>

							<div class="form-group">
								<label class="control-label col-lg-3">Nombre:</label>
								<div class="col-lg-9">
									<b><?php echo $row_activos['emp_paterno']; ?> <?php echo $row_activos['emp_materno']; ?> <?php echo $row_activos['emp_nombre']; ?> (<?php echo $row_activos['IDempleado']; ?>)</b>
									</div>
								</div>


				<?php if ($totalRows_encuesta == 0) { ?>


								<?php if (in_array($IDpuesto, $pantalon_ventas))  { ?>


										<?php if ($sexo == 'H')  { ?>

										<p class="text text-semibold">Pantalón Ventas Hombre (2: Dockers Beige)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_pantalon_ventas" id="T_pantalon_ventas" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="26">26</option>
											  	<option value="28">28</option>
											  	<option value="30">30</option>
											  	<option value="32">32</option>
											  	<option value="34">34</option>
											  	<option value="36">36</option>
											  	<option value="38">38</option>
											  	<option value="40">40</option>
											  	<option value="42">42</option>
											  	<option value="44">44</option>
											  	<option value="46">46</option>
											  	<option value="48">48</option>
											  	<option value="50">50</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_pantalon_ventas" id="F_pantalon_ventas" >
										</div>

									</div>
									<br/>
										
										<?php } else { ?>
											
										<p class="text text-semibold">Pantalón Ventas Mujer (2: Dockers Beige)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_pantalon_ventas" id="T_pantalon_ventas" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="26">26</option>
											  	<option value="28">28</option>
											  	<option value="30">30</option>
											  	<option value="32">32</option>
											  	<option value="34">34</option>
											  	<option value="36">36</option>
											  	<option value="38">38</option>
											  	<option value="40">40</option>
											  	<option value="42">42</option>
											  	<option value="44">44</option>
											  	<option value="46">46</option>
											  	<option value="48">48</option>
											  	<option value="50">50</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_pantalon_ventas" id="F_pantalon_ventas" >
										</div>
										</div>


										<?php } ?>  


								<?php } ?>   


								<?php if (in_array($IDpuesto, $pantalon_operaciones))  { ?>



										<?php if ($sexo == 'H')  { ?>
										
										<p class="text text-semibold">Pantalón Mezclilla Hombre (2 azul)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_pantalon_operaciones" id="T_pantalon_operaciones" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="26">26</option>
											  	<option value="28">28</option>
											  	<option value="30">30</option>
											  	<option value="32">32</option>
											  	<option value="34">34</option>
											  	<option value="36">36</option>
											  	<option value="38">38</option>
											  	<option value="40">40</option>
											  	<option value="42">42</option>
											  	<option value="44">44</option>
											  	<option value="46">46</option>
											  	<option value="48">48</option>
											  	<option value="50">50</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_pantalon_operaciones" id="F_pantalon_operaciones" >
										</div>
										</div>

										<?php } else { ?>
											
										<p class="text text-semibold">Pantalón Mezclilla Mujer (2 azul)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_pantalon_operaciones" id="T_pantalon_operaciones" class="form-control" >
												<option value="">Seleccione...</option> 
											  	<option value="26">26</option>
											  	<option value="28">28</option>
											  	<option value="30">30</option>
											  	<option value="32">32</option>
											  	<option value="34">34</option>
											  	<option value="36">36</option>
											  	<option value="38">38</option>
											  	<option value="40">40</option>
											  	<option value="42">42</option>
											  	<option value="44">44</option>
											  	<option value="46">46</option>
											  	<option value="48">48</option>
											  	<option value="50">50</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_pantalon_operaciones" id="F_pantalon_operaciones" >
										</div>
										</div>


										<?php } ?>  
	
	
	
								<?php } ?>   

								
								<?php if (in_array($IDpuesto, $camisa_ventas))  { ?>



										<?php if ($sexo == 'H')  { ?>
										
										<p class="text text-semibold">Camisa Ventas Hombre (1 gris manga larga, 1 gris manga corta, 1 blanca manga corta)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_camisa_ventas" id="T_camisa_ventas" class="form-control" >
												<option value="">Seleccione...</option> 
											  	<option value="26">26</option>
											  	<option value="28">28</option>
											  	<option value="30">30</option>
											  	<option value="32">32</option>
											  	<option value="34">34</option>
											  	<option value="36">36</option>
											  	<option value="38">38</option>
											  	<option value="40">40</option>
											  	<option value="42">42</option>
											  	<option value="44">44</option>
											  	<option value="46">46</option>
											  	<option value="48">48</option>
											  	<option value="50">4X</option>
											  	<option value="52">5X</option>
											  	<option value="54">7X</option>
											  	<option value="56">9X</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_camisa_ventas" id="F_camisa_ventas" >
										</div>		
										</div>

										<?php } else { ?>
											
										<p class="text text-semibold">Camisa Ventas Mujer (1 gris manga larga, 1 gris manga corta, 1 blanca manga corta)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_camisa_ventas" id="T_camisa_ventas" class="form-control" >
												<option value="">Seleccione...</option> 
											  	<option value="26">26</option>
											  	<option value="28">28</option>
											  	<option value="30">30</option>
											  	<option value="32">32</option>
											  	<option value="34">34</option>
											  	<option value="36">36</option>
											  	<option value="38">38</option>
											  	<option value="40">40</option>
											  	<option value="42">42</option>
											  	<option value="44">44</option>
											  	<option value="46">46</option>
											  	<option value="48">48</option>
											  	<option value="50">4X</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_camisa_ventas" id="F_camisa_ventas" >
										</div>										
										</div>

										<?php } ?>  
	
	
	
								<?php } ?>   


								<?php if (in_array($IDpuesto, $playera_polo_distribucion))  { ?>


										<?php if ($sexo == 'H')  { ?>
										
										<p class="text text-semibold">Camisa Polo Hombre (2 roja manga corta, 1 azul manga corta)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_playera_polo_distribucion" id="T_playera_polo_distribucion" class="form-control" >
												<option value="">Seleccione...</option> 
											  	<option value="CH">CH</option>
											  	<option value="M">M</option>
											  	<option value="G">G</option>
											  	<option value="XG">XG</option>
											  	<option value="XXG">XXG</option>
											  	<option value="XXXG">XXXG</option>
											  	<option value="XXXXG">XXXXG</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_playera_polo_distribucion" id="F_playera_polo_distribucion" >
										</div>										
										</div>


										<?php } else { ?>
											
										<p class="text text-semibold">Camisa Polo Mujer (2 roja manga corta, 1 azul manga corta)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_playera_polo_distribucion" id="T_playera_polo_distribucion" class="form-control" >
												<option value="">Seleccione...</option> 
											  	<option value="CH">CH</option>
											  	<option value="M">M</option>
											  	<option value="G">G</option>
											  	<option value="XG">XG</option>
											  	<option value="XXG">XXG</option>
											  	<option value="XXXG">XXXG</option>
											  	<option value="XXXXG">XXXXG</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_playera_polo_distribucion" id="F_playera_polo_distribucion" >
										</div>										
										</div>


										<?php } ?>  
	
	
	
								<?php } ?>   


								<?php if (in_array($IDpuesto, $playera_roja_almacen))  { ?>


										<?php if ($sexo == 'H')  { ?>
										
										<p class="text text-semibold">Camiseta Roja Hombre (3 rojas, cuello redondo)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_playera_roja_almacen" id="T_playera_roja_almacen" class="form-control" >
												<option value="">Seleccione...</option> 
											  	<option value="CH">CH</option>
											  	<option value="M">M</option>
											  	<option value="G">G</option>
											  	<option value="XG">XG</option>
											  	<option value="XXG">XXG</option>
											  	<option value="XXXG">XXXG</option>
											  	<option value="XXXXG">XXXXG</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_playera_roja_almacen" id="F_playera_roja_almacen" >
										</div>										
										</div>


										<?php } else { ?>
											
										<p class="text text-semibold">Camiseta Roja Mujer (3 rojas, cuello redondo)</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_playera_roja_almacen" id="T_playera_roja_almacen" class="form-control" >
												<option value="">Seleccione...</option> 
											  	<option value="CH">CH</option>
											  	<option value="M">M</option>
											  	<option value="G">G</option>
											  	<option value="XG">XG</option>
											  	<option value="XXG">XXG</option>
											  	<option value="XXXG">XXXG</option>
											  	<option value="XXXXG">XXXXG</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_playera_roja_almacen" id="F_playera_roja_almacen" >
										</div>										
										</div>

										<?php } ?>  
	
	
	
								<?php } ?>   


								<?php if (in_array($IDpuesto, $equipo_proteccion))  { ?>

									<?php if ($sexo == 'H')  { ?>

										<p class="text text-semibold">Botas Hombre</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_botas" id="T_botas" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="21">21</option>
											  	<option value="22">22</option>
											  	<option value="23">23</option>
											  	<option value="24">24</option>
											  	<option value="25">25</option>
											  	<option value="26">26</option>
											  	<option value="27">27</option>
											  	<option value="28">28</option>
											  	<option value="29">29</option>
											  	<option value="30">30</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_botas" id="F_botas" >
										</div>										
										</div>


									<?php } else  { ?>

										<p class="text text-semibold">Botas Mujer</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_botas" id="T_botas" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="21">21</option>
											  	<option value="22">22</option>
											  	<option value="23">23</option>
											  	<option value="24">24</option>
											  	<option value="25">25</option>
											  	<option value="26">26</option>
											  	<option value="27">27</option>
											  	<option value="28">28</option>
											  	<option value="29">29</option>
											  	<option value="30">30</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_botas" id="F_botas" >
										</div>										
										</div>


									<?php } ?>



									<?php if ($sexo == 'H')  { ?>

										<p class="text text-semibold">Faja Hombre</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_faja" id="T_faja" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="CH">CH</option>
											  	<option value="M">M</option>
											  	<option value="G">G</option>
											  	<option value="XG">XG</option>
											  	<option value="XXG">XXG</option>
											  	<option value="XXXG">XXXG</option>
											  	<option value="XXXXG">XXXXG</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_faja" id="F_faja" >
										</div>										
										</div>


										<?php } else  { ?>

										<p class="text text-semibold">Faja Mujer</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Talla:</label>
										<div class="col-lg-4">	
										<select name="T_faja" id="T_faja" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="CH">CH</option>
											  	<option value="M">M</option>
											  	<option value="G">G</option>
											  	<option value="XG">XG</option>
											  	<option value="XXG">XXG</option>
											  	<option value="XXXG">XXXG</option>
											  	<option value="XXXXG">XXXXG</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="F_faja" id="F_faja" >
										</div>										
										</div>

										<?php } ?>

	
								<?php } ?>   


								<?php if (in_array($IDpuesto, $licencia))  { ?>


										<p class="text text-semibold">Licencia Manejo</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Tipo:</label>
										<div class="col-lg-4">	
										<select name="Licencia" id="Licencia" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="1">Estatal A</option>
												<option value="2">Estatal B</option>
												<option value="3">Estatal C</option>
												<option value="4">Estatal D</option>
												<option value="5">Estatal E</option>
												<option value="6">Federal A</option>
												<option value="7">Federal B</option>
												<option value="8">Federal C</option>
												<option value="9">Federal D</option>
												<option value="10">Federal E</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="Licencia_vigencia" id="Licencia_vigencia">
										</div>										
										</div>


										<p class="text text-semibold">Licencia Manejo adicional</p>											
										<div class="form-group">
										<label class="control-label col-lg-2">Tipo:</label>
										<div class="col-lg-4">	
										<select name="Licencia2" id="Licencia2" class="form-control" >
												<option value="">Seleccione...</option> 
												<option value="1">Estatal A</option>
												<option value="2">Estatal B</option>
												<option value="3">Estatal C</option>
												<option value="4">Estatal D</option>
												<option value="5">Estatal E</option>
												<option value="6">Federal A</option>
												<option value="7">Federal B</option>
												<option value="8">Federal C</option>
												<option value="9">Federal D</option>
												<option value="10">Federal E</option>
										</select>
										</div>

										<label class="control-label col-lg-2">Fecha:</label>
										<div class="col-lg-4">
										<input type="date" class="form-control" name="Licencia_vigencia2" id="Licencia_vigencia2">
										</div>										
										</div>

										<?php } ?>  



								<p class="text text-semibold">Observaciones</p>											
									<div class="form-group">
									<div class="col-lg-12">
											<textarea rows="2" cols="3" name="Observaciones" id="Observaciones" class="form-control"></textarea>
										</div>
									</div>


								<div class="modal-footer">
									<input type="hidden" name="MM_insert" value="form1" />
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<input type="submit" name="KT_Insert1" class="btn btn-warning" id="KT_Insert1" value="Capturar" />
									<input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">
									<input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
									<input type="hidden" name="Sexo" value="<?php echo $sexo; ?>">
								</div>
    </div>
</form>
			<?php // actualizar...................................... 
		} else { ?>
								

								<?php if (in_array($IDpuesto, $pantalon_ventas))  { ?>


									<?php if ($sexo == 'H')  { ?>

									<p class="text text-semibold">Pantalón Ventas Hombre (2: Dockers Beige)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_pantalon_ventas" id="T_pantalon_ventas" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
											<option value="32" <?php if (!(strcmp(32, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>32</option>
											<option value="34" <?php if (!(strcmp(34, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>34</option>
											<option value="36" <?php if (!(strcmp(36, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>36</option>
											<option value="38" <?php if (!(strcmp(38, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>38</option>
											<option value="40" <?php if (!(strcmp(40, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40</option>
											<option value="42" <?php if (!(strcmp(42, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>42</option>
											<option value="44" <?php if (!(strcmp(44, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>44</option>
											<option value="46" <?php if (!(strcmp(46, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>46</option>
											<option value="48" <?php if (!(strcmp(48, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>48</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_pantalon_ventas" id="F_pantalon_ventas"  value="<?php echo htmlentities($row_encuesta['F_pantalon_ventas'], ENT_COMPAT, 'utf-8'); ?>">
									</div>
									</div>


									<?php } else { ?>
										
									<p class="text text-semibold">Pantalón Ventas Mujer (2: Dockers Beige)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_pantalon_ventas" id="T_pantalon_ventas" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
											<option value="32" <?php if (!(strcmp(32, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>32</option>
											<option value="34" <?php if (!(strcmp(34, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>34</option>
											<option value="36" <?php if (!(strcmp(36, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>36</option>
											<option value="38" <?php if (!(strcmp(38, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>38</option>
											<option value="40" <?php if (!(strcmp(40, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40</option>
											<option value="42" <?php if (!(strcmp(42, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>42</option>
											<option value="44" <?php if (!(strcmp(44, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>44</option>
											<option value="46" <?php if (!(strcmp(46, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>46</option>
											<option value="48" <?php if (!(strcmp(48, htmlentities($row_encuesta['T_pantalon_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>48</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_pantalon_ventas" id="F_pantalon_ventas"  value="<?php echo htmlentities($row_encuesta['F_pantalon_ventas'], ENT_COMPAT, 'utf-8'); ?>">
									</div>
									</div>

									<?php } ?>  


								<?php } ?>   


								<?php if (in_array($IDpuesto, $pantalon_operaciones))  { ?>



									<?php if ($sexo == 'H')  { ?>

									<p class="text text-semibold">Pantalón Mezclilla Hombre (2 azul)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_pantalon_operaciones" id="T_pantalon_operaciones" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
											<option value="32" <?php if (!(strcmp(32, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>32</option>
											<option value="34" <?php if (!(strcmp(34, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>34</option>
											<option value="36" <?php if (!(strcmp(36, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>36</option>
											<option value="38" <?php if (!(strcmp(38, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>38</option>
											<option value="40" <?php if (!(strcmp(40, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40</option>
											<option value="42" <?php if (!(strcmp(42, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>42</option>
											<option value="44" <?php if (!(strcmp(44, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>44</option>
											<option value="46" <?php if (!(strcmp(46, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>46</option>
											<option value="48" <?php if (!(strcmp(48, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>48</option>
											<option value="50" <?php if (!(strcmp(50, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>50</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_pantalon_operaciones" id="F_pantalon_operaciones"  value="<?php echo $row_encuesta['F_pantalon_operaciones']; ?>">
									</div>
									</div>

									<?php } else { ?>
										
									<p class="text text-semibold">Pantalón Mezclilla Mujer (2 azul)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_pantalon_operaciones" id="T_pantalon_operaciones" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
											<option value="32" <?php if (!(strcmp(32, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>32</option>
											<option value="34" <?php if (!(strcmp(34, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>34</option>
											<option value="36" <?php if (!(strcmp(36, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>36</option>
											<option value="38" <?php if (!(strcmp(38, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>38</option>
											<option value="40" <?php if (!(strcmp(40, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40</option>
											<option value="42" <?php if (!(strcmp(42, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>42</option>
											<option value="44" <?php if (!(strcmp(44, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>44</option>
											<option value="46" <?php if (!(strcmp(46, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>46</option>
											<option value="48" <?php if (!(strcmp(48, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>48</option>
											<option value="50" <?php if (!(strcmp(50, htmlentities($row_encuesta['T_pantalon_operaciones'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>50</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_pantalon_operaciones" id="F_pantalon_operaciones"  value="<?php echo htmlentities($row_encuesta['F_pantalon_operaciones'], ENT_COMPAT, 'utf-8'); ?>">
									</div>
									</div>


									<?php } ?>  



								<?php } ?>   


								<?php if (in_array($IDpuesto, $camisa_ventas))  { ?>



									<?php if ($sexo == 'H')  { ?>

									<p class="text text-semibold">Camisa Ventas Hombre (1 gris manga larga, 1 gris manga corta, 1 blanca manga corta)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_camisa_ventas" id="T_camisa_ventas" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
											<option value="32" <?php if (!(strcmp(32, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>32</option>
											<option value="34" <?php if (!(strcmp(34, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>34</option>
											<option value="36" <?php if (!(strcmp(36, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>36</option>
											<option value="38" <?php if (!(strcmp(38, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>38</option>
											<option value="40" <?php if (!(strcmp(40, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40</option>
											<option value="42" <?php if (!(strcmp(42, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>42</option>
											<option value="44" <?php if (!(strcmp(44, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>44</option>
											<option value="46" <?php if (!(strcmp(46, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>46</option>
											<option value="48" <?php if (!(strcmp(48, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>48</option>
											<option value="50" <?php if (!(strcmp(50, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>4X</option>
											<option value="50" <?php if (!(strcmp(50, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>5X</option>
											<option value="50" <?php if (!(strcmp(50, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>7X</option>
											<option value="50" <?php if (!(strcmp(50, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>9X</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_camisa_ventas" id="F_camisa_ventas"  value="<?php echo htmlentities($row_encuesta['F_camisa_ventas'], ENT_COMPAT, 'utf-8'); ?>">
									</div>		
									</div>

									<?php } else { ?>
										
									<p class="text text-semibold">Camisa Ventas Mujer (1 gris manga larga, 1 gris manga corta, 1 blanca manga corta)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_camisa_ventas" id="T_camisa_ventas" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
											<option value="32" <?php if (!(strcmp(32, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>32</option>
											<option value="34" <?php if (!(strcmp(34, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>34</option>
											<option value="36" <?php if (!(strcmp(36, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>36</option>
											<option value="38" <?php if (!(strcmp(38, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>38</option>
											<option value="40" <?php if (!(strcmp(40, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>40</option>
											<option value="42" <?php if (!(strcmp(42, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>42</option>
											<option value="44" <?php if (!(strcmp(44, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>44</option>									
											<option value="46" <?php if (!(strcmp(46, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>46</option>									
											<option value="48" <?php if (!(strcmp(48, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>48</option>									
											<option value="50" <?php if (!(strcmp(50, htmlentities($row_encuesta['T_camisa_ventas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>50</option>									
										</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_camisa_ventas" id="F_camisa_ventas"  value="<?php echo htmlentities($row_encuesta['F_camisa_ventas'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>

									<?php } ?>  



								<?php } ?>   


								<?php if (in_array($IDpuesto, $playera_polo_distribucion))  { ?>


									<?php if ($sexo == 'H')  { ?>

									<p class="text text-semibold">Camisa Polo Hombre (2 roja manga corta, 1 azul manga corta)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_playera_polo_distribucion" id="T_playera_polo_distribucion" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="CH" <?php if (!(strcmp("CH", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CH</option>
											<option value="M" <?php if (!(strcmp("M", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>M</option>
											<option value="G" <?php if (!(strcmp("G", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>G</option>
											<option value="XG" <?php if (!(strcmp("XG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XG</option>
											<option value="XXG" <?php if (!(strcmp("XXG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXG</option>
											<option value="XXXG" <?php if (!(strcmp("XXXG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXG</option>
											<option value="XXXXG" <?php if (!(strcmp("XXXXG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXXG</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_playera_polo_distribucion" id="F_playera_polo_distribucion"  value="<?php echo htmlentities($row_encuesta['F_playera_polo_distribucion'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>


									<?php } else { ?>
										
									<p class="text text-semibold">Camisa Polo Mujer (2 roja manga corta, 1 azul manga corta)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_playera_polo_distribucion" id="T_playera_polo_distribucion" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="CH" <?php if (!(strcmp("CH", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CH</option>
											<option value="M" <?php if (!(strcmp("M", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>M</option>
											<option value="G" <?php if (!(strcmp("G", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>G</option>
											<option value="XG" <?php if (!(strcmp("XG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XG</option>
											<option value="XXG" <?php if (!(strcmp("XXG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXG</option>
											<option value="XXXG" <?php if (!(strcmp("XXXG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXG</option>
											<option value="XXXXG" <?php if (!(strcmp("XXXXG", htmlentities($row_encuesta['T_playera_polo_distribucion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXXG</option>

									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_playera_polo_distribucion" id="F_playera_polo_distribucion"  value="<?php echo htmlentities($row_encuesta['F_playera_polo_distribucion'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>


									<?php } ?>  



								<?php } ?>   


								<?php if (in_array($IDpuesto, $playera_roja_almacen))  { ?>


									<?php if ($sexo == 'H')  { ?>

									<p class="text text-semibold">Camisa Roja Hombre (3 rojas, cuello redondo)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_playera_roja_almacen" id="T_playera_roja_almacen" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="CH" <?php if (!(strcmp("CH", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CH</option>
											<option value="M" <?php if (!(strcmp("M", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>M</option>
											<option value="G" <?php if (!(strcmp("G", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>G</option>
											<option value="XG" <?php if (!(strcmp("XG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XG</option>
											<option value="XXG" <?php if (!(strcmp("XXG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXG</option>
											<option value="XXXG" <?php if (!(strcmp("XXXG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXG</option>
											<option value="XXXXG" <?php if (!(strcmp("XXXXG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXXG</option>

									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_playera_roja_almacen" id="F_playera_roja_almacen"  value="<?php echo htmlentities($row_encuesta['F_playera_roja_almacen'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>


									<?php } else { ?>
										
									<p class="text text-semibold">Camisa Roja Mujer (3 rojas, cuello redondo)</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_playera_roja_almacen" id="T_playera_roja_almacen" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="CH" <?php if (!(strcmp("CH", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CH</option>
											<option value="M" <?php if (!(strcmp("M", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>M</option>
											<option value="G" <?php if (!(strcmp("G", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>G</option>
											<option value="XG" <?php if (!(strcmp("XG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XG</option>
											<option value="XXG" <?php if (!(strcmp("XXG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXG</option>
											<option value="XXXG" <?php if (!(strcmp("XXXG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXG</option>
											<option value="XXXXG" <?php if (!(strcmp("XXXXG", htmlentities($row_encuesta['T_playera_roja_almacen'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXXG</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_playera_roja_almacen" id="F_playera_roja_almacen"  value="<?php echo htmlentities($row_encuesta['F_playera_roja_almacen'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>

									<?php } ?>  



								<?php } ?>   


								<?php if (in_array($IDpuesto, $equipo_proteccion))  { ?>

									<?php if ($sexo == 'H')  { ?>

									<p class="text text-semibold">Botas Hombre</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_botas" id="T_botas" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="21" <?php if (!(strcmp(21, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>21</option>
											<option value="22" <?php if (!(strcmp(22, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>22</option>
											<option value="23" <?php if (!(strcmp(23, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>23</option>
											<option value="24" <?php if (!(strcmp(24, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>24</option>
											<option value="25" <?php if (!(strcmp(25, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>25</option>
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="27" <?php if (!(strcmp(27, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>27</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="29" <?php if (!(strcmp(29, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>29</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_botas" id="F_botas"  value="<?php echo htmlentities($row_encuesta['F_botas'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>


									<?php } else  { ?>

									<p class="text text-semibold">Botas Mujer</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_botas" id="T_botas" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="21" <?php if (!(strcmp(21, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>21</option>
											<option value="22" <?php if (!(strcmp(22, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>22</option>
											<option value="23" <?php if (!(strcmp(23, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>23</option>
											<option value="24" <?php if (!(strcmp(24, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>24</option>
											<option value="25" <?php if (!(strcmp(25, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>25</option>
											<option value="26" <?php if (!(strcmp(26, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>26</option>
											<option value="27" <?php if (!(strcmp(27, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>27</option>
											<option value="28" <?php if (!(strcmp(28, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>28</option>
											<option value="29" <?php if (!(strcmp(29, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>29</option>
											<option value="30" <?php if (!(strcmp(30, htmlentities($row_encuesta['T_botas'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>30</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_botas" id="F_botas"  value="<?php echo htmlentities($row_encuesta['F_botas'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>


									<?php } ?>



									<?php if ($sexo == 'H')  { ?>

									<p class="text text-semibold">Faja Hombre</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_faja" id="T_faja" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="CH" <?php if (!(strcmp("CH", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CH</option>
											<option value="M" <?php if (!(strcmp("M", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>M</option>
											<option value="G" <?php if (!(strcmp("G", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>G</option>
											<option value="XG" <?php if (!(strcmp("XG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XG</option>
											<option value="XXG" <?php if (!(strcmp("XXG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXG</option>
											<option value="XXXG" <?php if (!(strcmp("XXXG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXG</option>
											<option value="XXXXG" <?php if (!(strcmp("XXXXG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXXG</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_faja" id="F_faja"  value="<?php echo htmlentities($row_encuesta['F_faja'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>


									<?php } else  { ?>

									<p class="text text-semibold">Faja Mujer</p>											
									<div class="form-group">
									<label class="control-label col-lg-2">Talla:</label>
									<div class="col-lg-4">	
									<select name="T_faja" id="T_faja" class="form-control" >
											<option value="">Seleccione...</option> 
											<option value="CH" <?php if (!(strcmp("CH", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CH</option>
											<option value="M" <?php if (!(strcmp("M", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>M</option>
											<option value="G" <?php if (!(strcmp("G", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>G</option>
											<option value="XG" <?php if (!(strcmp("XG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XG</option>
											<option value="XXG" <?php if (!(strcmp("XXG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXG</option>
											<option value="XXXG" <?php if (!(strcmp("XXXG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXG</option>
											<option value="XXXXG" <?php if (!(strcmp("XXXXG", htmlentities($row_encuesta['T_faja'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>XXXXG</option>
									</select>
									</div>

									<label class="control-label col-lg-2">Fecha:</label>
									<div class="col-lg-4">
									<input type="date" class="form-control" name="F_faja" id="F_faja"  value="<?php echo htmlentities($row_encuesta['F_faja'], ENT_COMPAT, 'utf-8'); ?>">
									</div>										
									</div>

								<?php } ?>

								<?php } ?>   



								<?php if (in_array($IDpuesto, $licencia))  { ?>

								<p class="text text-semibold">Licencia Manejo</p>											
								<div class="form-group">
								<label class="control-label col-lg-2">Tipo:</label>
								<div class="col-lg-4">	
								<select name="Licencia" id="Licencia" class="form-control" >
										<option value="">Seleccione...</option> 
										<option value="1"  <?php if (!(strcmp("1", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal A</option>
										<option value="2"  <?php if (!(strcmp("2", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal B</option>
										<option value="3"  <?php if (!(strcmp("3", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal C</option>
										<option value="4"  <?php if (!(strcmp("4", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal D</option>
										<option value="5"  <?php if (!(strcmp("5", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal E</option>
										<option value="6"  <?php if (!(strcmp("6", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal A</option>
										<option value="7"  <?php if (!(strcmp("7", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal B</option>
										<option value="8"  <?php if (!(strcmp("8", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal C</option>
										<option value="9"  <?php if (!(strcmp("9", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal D</option>
										<option value="10" <?php if (!(strcmp("10", htmlentities($row_encuesta['Licencia'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal E</option>
								</select>
								</div>

								<label class="control-label col-lg-2">Fecha:</label>
								<div class="col-lg-4">
								<input type="date" class="form-control" name="Licencia_vigencia" id="Licencia_vigencia" value="<?php echo htmlentities($row_encuesta['Licencia_vigencia'], ENT_COMPAT, 'utf-8'); ?>">
								</div>										
								</div>


								<p class="text text-semibold">Licencia Manejo Adicional</p>											
								<div class="form-group">
								<label class="control-label col-lg-2">Tipo:</label>
								<div class="col-lg-4">	
								<select name="Licencia2" id="Licencia2" class="form-control" >
										<option value="">Seleccione...</option> 
										<option value="1"  <?php if (!(strcmp("1", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal A</option>
										<option value="2"  <?php if (!(strcmp("2", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal B</option>
										<option value="3"  <?php if (!(strcmp("3", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal C</option>
										<option value="4"  <?php if (!(strcmp("4", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal D</option>
										<option value="5"  <?php if (!(strcmp("5", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Estatal E</option>
										<option value="6"  <?php if (!(strcmp("6", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal A</option>
										<option value="7"  <?php if (!(strcmp("7", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal B</option>
										<option value="8"  <?php if (!(strcmp("8", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal C</option>
										<option value="9"  <?php if (!(strcmp("9", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal D</option>
										<option value="10" <?php if (!(strcmp("10", htmlentities($row_encuesta['Licencia2'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Federal E</option>
								</select>
								</div>

								<label class="control-label col-lg-2">Fecha:</label>
								<div class="col-lg-4">
								<input type="date" class="form-control" name="Licencia_vigencia2" id="Licencia_vigencia2" value="<?php echo htmlentities($row_encuesta['Licencia_vigencia2'], ENT_COMPAT, 'utf-8'); ?>">
								</div>										
								</div>

								<?php } ?>  


									<p class="text text-semibold">Observaciones</p>											
									<div class="form-group">
									<div class="col-lg-12">
											<textarea rows="2" cols="3" name="Observaciones" id="Observaciones" class="form-control"><?php echo htmlentities($row_encuesta['Observaciones'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>


									<?php if (!in_array($IDpuesto, $todos))  { ?> Este empelado no tiene asignado Uniforme.<?php } ?>  


								<div class="modal-footer">
								<input type="hidden" name="MM_update" value="form1">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="submit" name="KT_Update1" class="btn btn-warning">Actualizar</button>
									<input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">
									<input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
									<input type="hidden" name="Sexo" value="<?php echo $sexo; ?>">
								</div>
    </div>
</form>

<?php } ?>  				
