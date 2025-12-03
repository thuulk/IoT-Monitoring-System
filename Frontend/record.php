<?php
session_start();
include "conexion.php";

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: cerrar_sesion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $matricula   = $_POST["matricula"];
    $contraseña  = $_POST["password"];

    // ----- Validación de campos vacíos -----
    if (empty($matricula) || empty($contraseña)) {
        header("Location: index.php?error=campos-vacios");
        exit();
    }

    // ----- Verificar duplicado -----
    $sql = "SELECT COUNT(*) AS contador FROM usuarios WHERE matricula = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $matricula);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row["contador"] > 0) {
        mysqli_stmt_close($stmt);
        header("Location: index.php?error=usuario-existe");
        exit();
    }
    mysqli_stmt_close($stmt);

    // ----- Insertar usuario -----
    $sqlInsert = "INSERT INTO usuarios(matricula, contraseña, rol) VALUES (?, ?, 'empleado')";
    $stmt2 = mysqli_prepare($conn, $sqlInsert);
    mysqli_stmt_bind_param($stmt2, 'ss', $matricula, $contraseña);
    mysqli_stmt_execute($stmt2);

    if (mysqli_stmt_affected_rows($stmt2) == 0) {
        mysqli_stmt_close($stmt2);
        header("Location: index.php?error=fallo-insercion");
        exit();
    }

    mysqli_stmt_close($stmt2);

    // ----- Registro exitoso -----
    header("Location: index.php?good=se-registro-bien");
    exit();
}
?>
