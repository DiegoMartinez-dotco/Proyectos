<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Obtener todos los pedidos con información de cliente
$pedidos = $conn->query("
    SELECT p.*, c.nombre as cliente 
    FROM pedidos p 
    JOIN clientes c ON p.cliente_id = c.id
    ORDER BY p.fecha_pedido DESC
");

// Procesar cambio de estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_estado'])) {
    $pedido_id = $_POST['pedido_id'];
    $estado = $_POST['estado'];
    
    $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $pedido_id);
    $stmt->execute();
    
    header("Location: pedidos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
</head>
<body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <?php include '../includes/header.php';?>
    
    <main style="padding: 20px;">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <a href="dashboard.php" style="display: inline-block; margin-right: 15px; font-size: 24px; color: #2196F3; text-decoration: none; vertical-align: middle;">←</a>
            <h2 style="margin: 0;">Gestión de Pedidos</h2>
        </div>
        
        <?php while ($pedido = $pedidos->fetch_assoc()): ?>
            <div style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; padding: 15px; background-color: rgba(255, 255, 255, 0.8);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <div><strong>Pedido #<?= $pedido['id'] ?></strong></div>
                    <div><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></div>
                </div>
                
                <div style="margin-bottom: 8px;">
                    <strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?>
                </div>
                
                <div style="margin-bottom: 8px;">
                    <strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?>
                </div>
                
                <div style="margin-bottom: 8px;">
                    <strong>Estado:</strong>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                        <select name="estado" onchange="this.form.submit()" style="padding: 3px;">
                            <option value="pendiente" <?= $pedido['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="procesando" <?= $pedido['estado'] == 'procesando' ? 'selected' : '' ?>>Procesando</option>
                            <option value="enviado" <?= $pedido['estado'] == 'enviado' ? 'selected' : '' ?>>Enviado</option>
                            <option value="completado" <?= $pedido['estado'] == 'completado' ? 'selected' : '' ?>>Completado</option>
                            <option value="cancelado" <?= $pedido['estado'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <input type="hidden" name="actualizar_estado" value="1">
                    </form>
                </div>
                
                <div style="margin-bottom: 8px;">
                    <a href="pedido-detalle.php?id=<?= $pedido['id'] ?>" style="padding: 5px 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 3px; display: inline-block;">Ver Detalles</a>
                </div>
            </div>
        <?php endwhile; ?>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="agregar_pedido.php" style="padding: 8px 15px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 3px; display: inline-block;">+ Nuevo Pedido</a>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</div>

</body>
</html>