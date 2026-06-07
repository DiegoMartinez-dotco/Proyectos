<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: clientes.php");
    exit;
}

$cliente_id = $_GET['id'];

// Obtener información del cliente
$cliente = $conexion->query("SELECT * FROM clientes WHERE id = $cliente_id")->fetch_assoc();

if (!$cliente) {
    header("Location: clientes.php");
    exit;
}

// Obtener pedidos del cliente
$pedidos = $conexion->query("
    SELECT * FROM pedidos 
    WHERE cliente_id = $cliente_id
    ORDER BY fecha_pedido DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Cliente</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main class="container">
        <h2>Detalles del Cliente</h2>
        
        <div class="cliente-info">
            <div>
                <h3>Información Personal</h3>
                <p><strong>Nombre:</strong> <?php echo $cliente['nombre']; ?></p>
                <p><strong>Email:</strong> <?php echo $cliente['email']; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $cliente['telefono'] ?: 'N/A'; ?></p>
                <p><strong>Dirección:</strong> <?php echo $cliente['direccion'] ?: 'N/A'; ?></p>
                <p><strong>Fecha de registro:</strong> <?php echo date('d/m/Y', strtotime($cliente['created_at'])); ?></p>
            </div>
        </div>
        
        <h3>Pedidos del Cliente</h3>
        <?php if ($pedidos->num_rows > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $pedido['id']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?></td>
                            <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                            <td><?php echo ucfirst($pedido['estado']); ?></td>
                            <td>
                                <a href="pedido.php?id=<?php echo $pedido['id']; ?>" class="btn">Ver</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Este cliente aún no ha realizado ningún pedido.</p>
        <?php endif; ?>
        
        <div class="admin-actions">
            <a href="clientes.php" class="btn">Volver a clientes</a>
        </div>
    </main>
</body>
</html>