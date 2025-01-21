<?php
// Get the current date and time in a specific format
$today = date("YmdHi");

// Set HTTP headers for file download
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"".$today."_Acumulado_Capa.csv\"");

// Create a MySQL database connection
$conn = new mysqli("localhost", "jacardenas", "Sahuayo2024A", "g_vacantes"); // Replace with your database credentials

// Check if the database connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the SQL query to select data from the `mytable` table
$query = "SELECT capa_avance.IDempleado, capa_avance.emp_nombre, capa_avance.emp_paterno, capa_avance.emp_materno, capa_avance.fecha_antiguedad, capa_avance.fecha_baja, capa_avance.fecha_evento, capa_avance.anio, capa_avance.mes, capa_avance.calificacion, capa_avance.denominacion, capa_avance.fecha, capa_cursos.nombre_curso, vac_areas.area, vac_matriz.matriz, vac_sucursal.sucursal  FROM capa_avance LEFT JOIN vac_matriz ON  capa_avance.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON capa_avance.IDsucursal = vac_sucursal.IDsucursal LEFT JOIN vac_areas ON capa_avance.IDarea = vac_areas.IDarea LEFT JOIN capa_cursos ON capa_avance.IDC_capa_cursos = capa_cursos.IDC_capa_cursos";

// Execute the SQL query
$result = $conn->query($query);

$list = array ('IDempleado', 'emp_nombre', 'emp_paterno', 'emp_materno', 'fecha_antiguedad', 'fecha_baja', 'fecha_evento', 'anio', 'mes', 'calificacion', 'denominacion', 'fecha', 'nombre_curso', 'area', 'matriz', 'sucursal');



    // Check if there are any rows returned
if ($result->num_rows > 0) {
    // Prepare the output file handle for writing
    $output = fopen('php://output', 'w');
        fputcsv($output, $list);

    // Fetch and process the data rows
    while ($row = $result->fetch_assoc()) {
        // Output each row as a CSV line
        fputcsv($output, $row);
    }

    // Close the output file handle
    fclose($output);
} else {
    // If no data is found, display a message
    echo "No data found";
}


// Close the MySQL database connection
$conn->close();
?>