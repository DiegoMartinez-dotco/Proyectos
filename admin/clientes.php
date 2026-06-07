<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Obtener todos los clientes
$clientes = $conn->query("SELECT * FROM clientes ORDER BY nombre");

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM clientes WHERE id = $id");
    header("Location: clientes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
    <body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

    <div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <?php include '../includes/header.php'; ?>
    
    <main class="container">
        <a href="/libreria/admin/dashboard.php" class="back-arrow">←</a>
        <h2>Gestión de Clientes</h2>
            <div class="admin-actions">
            <a href="agregar_cliente.php" class="btn">Agregar Nuevo Cliente</a>
        </div>
        <table class="admin-table clientes-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cliente = $clientes->fetch_assoc()): ?>
                    <tr>
                        <td><?= $cliente['id'] ?></td>
                        <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                        <td><?= $cliente['telefono'] ?: 'N/A' ?></td>
                        <td><?= date('d/m/Y', strtotime($cliente['created_at'])) ?></td>
                        <td>
                            <a href="cliente-detalle.php?id=<?= $cliente['id'] ?>" class="btn">Ver</a>
                            <a href="clientes.php?eliminar=<?= $cliente['id'] ?>" class="btn btn-eliminar"
                               onclick="return confirm('¿Eliminar este cliente?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>