<?php
$host = '127.0.0.1';
$usuario = 'root';
$contrasena = '1234';
$bd = 'libreria';
$puerto = 3307;

$conn = new mysqli($host, $usuario, $contrasena, $bd, $puerto);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
