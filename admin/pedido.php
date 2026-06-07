<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: pedidos.php");
    exit;
}

$pedido_id = $_GET['id'];

// Obtener información del pedido
$pedido = $conn->query("
    SELECT p.*, c.nombre as cliente_nombre, c.email, c.direccion, c.telefono
    FROM pedidos p
    JOIN clientes c ON p.cliente_id = c.id
    WHERE p.id = $pedido_id
")->fetch_assoc();

if (!$pedido) {
    header("Location: pedidos.php");
    exit;
}

// Obtener detalles del pedido
$detalles = $conn->query("
    SELECT d.*, l.titulo, l.autor, l.imagen
    FROM detalles_pedido d
    JOIN libros l ON d.libro_id = l.id
    WHERE d.pedido_id = $pedido_id
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
<body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <?php include '../includes/header.php'; ?>
    <main class="container">
        <h2>Detalles del Pedido #<?php echo $pedido_id; ?></h2>
        
        <div class="pedido-info">
            <div>
                <h3>Información del Cliente</h3>
                <p><strong>Nombre:</strong> <?php echo $pedido['cliente_nombre']; ?></p>
                <p><strong>Email:</strong> <?php echo $pedido['email']; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $pedido['telefono'] ?: 'N/A'; ?></p>
                <p><strong>Dirección:</strong> <?php echo $pedido['direccion'] ?: 'N/A'; ?></p>
            </div>
            
            <div>
                <h3>Información del Pedido</h3>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                <p><strong>Estado:</strong> <?php echo ucfirst($pedido['estado']); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 2); ?></p>
            </div>
        </div>
        
        <h3>Productos</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Libro</th>
                    <th>Precio unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($detalle = $detalles->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="/libreria/img/producto/<?php echo $detalle['imagen']; ?>" alt="<?php echo $detalle['titulo']; ?>" style="max-width: 50px; vertical-align: middle;">
                            <?php echo $detalle['titulo']; ?> - <?php echo $detalle['autor']; ?>
                        </td>
                        <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                        <td><?php echo $detalle['cantidad']; ?></td>
                        <td>$<?php echo number_format($detalle['precio_unitario'] * $detalle['cantidad'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="admin-actions">
            <a href="pedidos.php" class="btn">Volver a pedidos</a>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>