<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/funciones.php';

if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

$productos_carrito = obtenerProductosCarrito();

// Procesar el pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // En un sistema real, aquí validaríamos los datos del formulario
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    
    // Calcular total
    $total = 0;
    foreach ($productos_carrito as $producto) {
        $total += $producto['precio'] * $producto['cantidad'];
    }
    
    // Registrar el cliente (en un sistema real, verificaríamos si ya existe)
    $stmt = $conexion->prepare("INSERT INTO clientes (nombre, email, direccion, telefono) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $direccion, $telefono);
    $stmt->execute();
    $cliente_id = $conexion->insert_id;
    
    // Registrar el pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (?, ?)");
    $stmt->bind_param("id", $cliente_id, $total);
    $stmt->execute();
    $pedido_id = $conexion->insert_id;
    
    // Registrar los detalles del pedido
    foreach ($productos_carrito as $producto) {
        $stmt = $conexion->prepare("
            INSERT INTO detalles_pedido (pedido_id, libro_id, cantidad, precio_unitario) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiid", $pedido_id, $producto['id'], $producto['cantidad'], $producto['precio']);
        $stmt->execute();
        
        // Actualizar el stock (en un sistema real, deberíamos verificar que haya suficiente stock)
        $conexion->query("UPDATE libros SET stock = stock - {$producto['cantidad']} WHERE id = {$producto['id']}");
    }
    
    // Vaciar el carrito
    unset($_SESSION['carrito']);
    
    // Redirigir a página de confirmación
    header("Location: gracias.php?pedido_id=$pedido_id");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<h1>Finalizar compra</h1>

<div class="checkout-container">
    <div class="checkout-productos">
        <h2>Tu pedido</h2>
        <ul>
            <?php foreach ($productos_carrito as $producto): ?>
                <li>
                    <?php echo $producto['titulo']; ?> - 
                    <?php echo $producto['cantidad']; ?> x 
                    $<?php echo number_format($producto['precio'], 2); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="total">Total: $<?php 
            $total = 0;
            foreach ($productos_carrito as $producto) {
                $total += $producto['precio'] * $producto['cantidad'];
            }
            echo number_format($total, 2);
        ?></p>
    </div>
    
    <div class="checkout-form">
        <h2>Información de envío</h2>
        <form action="checkout.php" method="post">
            <div class="form-group">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <textarea id="direccion" name="direccion" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Confirmar pedido</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>