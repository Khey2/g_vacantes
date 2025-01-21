<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_vacantes = 'localhost';
$database_vacantes = 'g_vacantes';
$username_vacantes = 'root';
// print_r($hostname_vacantes);exit;
// $password_vacantes = 'Sahuayo2024A';
$vacantes = mysqli_connect($hostname_vacantes , $username_vacantes, null , $database_vacantes ) or die("Conexión fallida: " . mysqli_connect_error()); 
?>