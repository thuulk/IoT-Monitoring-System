<?php
session_start();
include "conexion.php";

if (!isset($_SESSION["username"])) {
    header("Location: cerrar_sesion.php?Acceso-Denegado");
    exit();
}

$fecha = $_POST['fecha'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fin = $_POST['hora_fin'];

$sql = "SELECT * FROM datosgenerales 
        WHERE fecha = ? 
        AND hora BETWEEN ? AND ?
        ORDER BY hora ASC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $fecha, $hora_inicio, $hora_fin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de consulta</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial;
            background: #e6f2f5;
            padding: 20px;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #219EBC;
            color: white;
        }
        h2 {
            text-align: center;
        }
        .volver {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            background: orange;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Resultados de la consulta</h2>

<table>
    <tr>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Humedad</th>
        <th>Temperatura</th>
        <th>Calidad Aire</th>
        <th>Presión</th>
        <th>CO2</th>
        <th>TVOC</th>
    </tr>

    <?php
    if (mysqli_num_rows($result) == 0) {
        echo "<tr><td colspan='6'>No hay datos en ese rango</td></tr>";
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['fecha']}</td>";
            echo "<td>{$row['hora']}</td>";
            echo "<td>{$row['humedad']}</td>";
            echo "<td>{$row['temperatura']}</td>";
            echo "<td>{$row['calidad_Aire']}</td>";
            echo "<td>{$row['presion']}</td>";
            echo "<td>{$row['c02']}</td>";
            echo "<td>{$row['tvoc']}</td>";
            echo "</tr>";
        }
    }
    ?>
</table>

<a href="formulario_consulta.php" class="volver">Volver</a>

</body>
</html>