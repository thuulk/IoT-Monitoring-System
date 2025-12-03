<?php
session_start();
include "conexion.php";
if(!isset($_SESSION["username"])){
    header("Location: login.html");
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$temperatura = $data["temperatura"];
$humedad = $data["humedad"];
$presion = $data["presion"];
$calidad_aire = $data["calidad_aire"];

$sql = "INSERT INTO datosgenerales (fecha, hora, humedad, temperatura, calida_aire, presion) VALUES (CURDATE(), CURTIME(), ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_fetch_assoc($stmt, "dddd", $humedad, $temperatura, $calidad_aire, $presion);
mysqli_stmt_execute($stmt);

if(mysqli_stmt_affected_rows($stmt) == 0){
    mysqli_stmt_close($stmt);
    echo "<script>alert('Fallo en la insercion de datos')";
}  else{
    mysqli_stmt_close($stmt);
    echo("se registro bien");
}

?>