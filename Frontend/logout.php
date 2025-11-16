<?php
session_start();
session_destroy(); // Borra la sesión
header("Location: login.html"); // Redirige al login
exit();
?>
