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
$el_usuario = $row_usuario['IDusuario'];

$IDempleado = $_GET['IDusuario'];	
mysql_select_db($database_vacantes, $vacantes);
$query_candidatos = "SELECT cv_activos.IDusuario, cv_activos.a_paterno, cv_activos.a_materno, cv_activos.a_nombre,  cv_activos.fecha_captura, cv_activos.fecha_entrevista, cv_activos.hora_entrevista, cv_activos.IDentrevista, cv_activos.IDmatriz, cv_activos.IDpuesto, cv_activos.estatus, cv_activos.tipo, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM cv_activos left JOIN vac_puestos ON vac_puestos.IDpuesto = cv_activos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = vac_puestos.IDarea  WHERE cv_activos.IDusuario = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$candidatos = mysql_query($query_candidatos, $vacantes) or die(mysql_error());
$row_candidatos = mysql_fetch_assoc($candidatos);
$totalRows_candidatos = mysql_num_rows($candidatos);


  $deleteSQL = "UPDATE cv_activos SET enviado_msg = 1  WHERE IDusuario ='$IDempleado'";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());


$IDmatriz = $row_candidatos['IDmatriz'];
$query_ubiacion = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
mysql_query("SET NAMES 'utf8'");
$ubiacion = mysql_query($query_ubiacion, $vacantes) or die(mysql_error());
$row_ubiacion = mysql_fetch_assoc($ubiacion);
$totalRows_ubiacion = mysql_num_rows($ubiacion);
?>
							<!-- Navigation -->
			    	  <div class="panel panel-flat">
                                    <div class="list-group no-border no-padding-top text-left">
                                        <a href="#" class="list-group-item"><i class="icon-user"></i><strong>Nombre</strong>: <?php echo $row_candidatos['a_nombre']. " " .$row_candidatos['a_paterno']." "
                                        . $row_candidatos['a_materno'];?></a>
                                        <a href="#" class="list-group-item"><i class="icon-calendar52"></i><strong>Dia</strong>: <?php echo date('d/m/Y', strtotime($row_candidatos['fecha_entrevista']));?></a>
                                        <a href="#" class="list-group-item"><i class="icon-alarm"></i><strong>Hora</strong>: <?php echo date('g:i A', strtotime($row_candidatos['hora_entrevista'])) ;?></a>
                                        <a href="#" class="list-group-item"><i class="icon-check"></i><strong>Vacante</strong>: <?php echo $row_candidatos['denominacion'];?></a>
                                        <div class="list-group-divider"></div>
                                    </div>
								<strong><p>Documentos</p></strong>
								<div class="text-left">
                                    <ul>
                                        <li>Solicitud elaborada</li>
                                        <li>IFE</li>
                                        <li>CURP</li>
                                        <li>RFC</li>
                                        <li>IMSS</a></li>
                                        <li>Licencia (solo si aplica)</li>
                                        <li>Acta de nacimiento</li>
                                        <li>Acta de matrimonio (solo si aplica)</li>
                                        <li>Comp. Estudios</li>
                                        <li>Comp. Domicilio</li>
                                        <li>2 cartas laborales (membretadas, firmadas y selladas)</li>
                                        <li>2 cartas personales (amigos o vecinos + 5 a&ntilde;os de conocer + copia de INE)</li>
                                        <li>Certificado Medico</li>
                                        <li>4 fotos tama&ntilde;o infantil a color.</li>
                                        <li>Correo electr&oacute;nico</li>
                                        <li>Solicitud elaborada</li>
                                        <div class="list-group-divider"></div>
                                    </ul>
								</div>
								<strong><p>Direcci&oacute;n</p></strong>
								<div class="text-left">
                                  <ul>
                                        <li><?php echo $row_ubiacion['direccion'];?> </li>
                                        <li><?php echo $row_ubiacion['ubicacion'];?></li>
                                    </ul>
                </div>
                                <i class=" icon-alert"></i> Obligatorio uso de cubre bocas</br>
                                <i class=" icon-alert"></i> No es necesario acudir a entrevista con todos los documentos
							</div>
							<!-- /navigation -->
                            <a href="candidatos_mdlp.php?IDusuario=<?php echo $row_candidatos['IDusuario']; ?>" target="_blank" class="btn btn-success btn-xs">Imprimir</a>
