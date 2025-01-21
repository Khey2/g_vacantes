<?php

$fecha_corte_ = '01/01/2023'; 
$y1 = substr( $fecha_corte_, 6, 4 );
$m1 = substr( $fecha_corte_, 3, 2 );
$d1 = substr( $fecha_corte_, 0, 2 );
$fecha_corte = $y1."-".$m1."-".$d1; echo $fecha_corte_; echo $fecha_corte;


?>