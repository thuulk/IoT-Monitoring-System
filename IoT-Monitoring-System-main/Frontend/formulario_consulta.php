<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Datos</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">
<header>
    <img src="img/BioAirSolutionsLogo.png" alt="BioAirSolution" class="logo">
</header>

<div class="login-container">
    <h2>Consulta de Registros</h2>

    <form action="consultaDatos.php" method="POST">

        <!-- FECHA -->
        <label>Seleccionar fecha:</label>
        <input type="date" name="fecha" required>

        <!-- HORA INICIAL -->
        <label>Hora inicial:</label>
        <select name="hora_inicio" required>
            <option value="">Selecciona</option>
            <?php
            for ($h = 0; $h < 24; $h++) {
                $hora = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00:00";
                echo "<option value='$hora'>$hora</option>";
            }
            ?>
        </select>

        <!-- HORA FINAL -->
        <label>Hora final:</label>
        <select name="hora_fin" required>
            <option value="">Selecciona</option>
            <?php
            for ($h = 0; $h < 24; $h++) {
                $hora = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00:00";
                echo "<option value='$hora'>$hora</option>";
            }
            ?>
        </select>

        <button type="submit">Consultar</button>
    </form>

</div>

</body>
</html>
