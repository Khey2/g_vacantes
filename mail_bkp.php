<?php require_once('Connections/vacantes.php'); ?>
<?php

$zip = new ZipArchive();
$fecha = date("Ymd");

$nombreArchivoZip = '/home/h4ukf1t6padv/backup_bds/'.$fecha.'.zip';
$path = '/home/h4ukf1t6padv/backup_bds/'.$fecha.'.zip';

if (!$zip->open($nombreArchivoZip, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
    exit("Error abriendo ZIP en $nombreArchivoZip");
} else {echo "correcto";}
$rutaAbsoluta = '/home/h4ukf1t6padv/backup_bds/respaldo.sql';
$nombre = basename($rutaAbsoluta);
$zip->addFile($rutaAbsoluta, $nombre);

// No olvides cerrar el archivo
$resultado = $zip->close();
?>