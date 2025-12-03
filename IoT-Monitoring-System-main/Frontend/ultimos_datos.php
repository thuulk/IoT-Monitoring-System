<?php
include "conexion.php";

$sql = "SELECT * FROM datosgenerales ORDER BY fecha DESC, hora DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

echo json_encode($row);
?>
