<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Obtener clientes y libros disponibles
$clientes = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre");
$libros = $conn->query("SELECT id, titulo, precio FROM libros WHERE stock > 0 ORDER BY titulo");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $libros_seleccionados = $_POST['libros'];
    $cantidades = $_POST['cantidades'];
    
    // Validar datos
    if (empty($cliente_id) || empty($libros_seleccionados)) {
        $error = "Debe seleccionar al menos un libro y un cliente";
    } else {
        // Calcular total
        $total = 0;
        $detalles_pedido = [];
        
        foreach ($libros_seleccionados as $index => $libro_id) {
            $cantidad = (int)$cantidades[$index];
            if ($cantidad <= 0) continue;
            
            $libro = $conn->query("SELECT precio FROM libros WHERE id = $libro_id")->fetch_assoc();
            $subtotal = $libro['precio'] * $cantidad;
            $total += $subtotal;
            
            $detalles_pedido[] = [
                'libro_id' => $libro_id,
                'cantidad' => $cantidad,
                'precio_unitario' => $libro['precio']
            ];
        }
        
        if ($total <= 0) {
            $error = "Debe seleccionar al menos un libro con cantidad válida";
        } else {
            // Iniciar transacción
            $conn->begin_transaction();
            
            try {
                // Insertar pedido
                $stmt = $conn->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (?, ?)");
                $stmt->bind_param("id", $cliente_id, $total);
                $stmt->execute();
                $pedido_id = $conn->insert_id;
                
                // Insertar detalles del pedido
                foreach ($detalles_pedido as $detalle) {
                    $stmt = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, libro_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiid", $pedido_id, $detalle['libro_id'], $detalle['cantidad'], $detalle['precio_unitario']);
                    $stmt->execute();
                    
                    // Actualizar stock
                    $conn->query("UPDATE libros SET stock = stock - {$detalle['cantidad']} WHERE id = {$detalle['libro_id']}");
                }
                
                $conn->commit();
                $success = "Pedido #$pedido_id registrado exitosamente. Total: $" . number_format($total, 2);
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Error al registrar el pedido: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Pedido</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <style>
        .libro-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            position: relative;
        }
        
        .btn-eliminar {
            background: #ff4444;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            position: absolute;
            right: 10px;
            top: 10px;
        }
        
        .btn-eliminar:hover {
            background: #cc0000;
        }
        
        .form-group {
            flex: 1;
        }
        
        .btn-agregar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

    <div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <?php include '../includes/header.php'; ?>
    
    <main class="container">
        <h2>Agregar Nuevo Pedido</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="agregar_pedido.php">
            <div class="form-group">
                <label for="cliente_id">Cliente:</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php while ($cliente = $clientes->fetch_assoc()): ?>
                        <option value="<?php echo $cliente['id']; ?>">
                            <?php echo htmlspecialchars($cliente['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div id="libros-container">
                <div class="libro-item">
                    <div class="form-group">
                        <label>Libro:</label>
                        <select name="libros[]" required>
                            <option value="">Seleccione un libro</option>
                            <?php while ($libro = $libros->fetch_assoc()): ?>
                                <option value="<?php echo $libro['id']; ?>" data-precio="<?php echo $libro['precio']; ?>">
                                    <?php echo htmlspecialchars($libro['titulo']); ?> ($<?php echo number_format($libro['precio'], 2); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Cantidad:</label>
                        <input type="number" name="cantidades[]" min="1" value="1" required>
                    </div>
                    
                    <button type="button" class="btn-eliminar" onclick="eliminarLibro(this)">×</button>
                </div>
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-agregar" onclick="agregarLibro()">+ Agregar otro libro</button>
            </div>
            
            <div class="form-group">
                <h3>Total: <span id="total" style="color: green;">$0.00</span></h3>
            </div>

            
            <button type="submit" class="btn btn-primary">Registrar</button>
            <a href="pedidos.php" class="btn">Cancelar</a>
        </form>
    </main>
    
    <script>
        // Función para agregar un nuevo campo de libro
        function agregarLibro() {
            const container = document.getElementById('libros-container');
            const nuevoLibro = container.firstElementChild.cloneNode(true);
            
            // Limpiar selección
            const select = nuevoLibro.querySelector('select');
            select.selectedIndex = 0;
            
            // Resetear cantidad
            const input = nuevoLibro.querySelector('input[type="number"]');
            input.value = 1;
            
            container.appendChild(nuevoLibro);
            actualizarTotal();
        }
        
        // Función para eliminar un campo de libro
        function eliminarLibro(btn) {
            const container = document.getElementById('libros-container');
            if (container.children.length > 1) {
                btn.parentElement.remove();
                actualizarTotal();
            } else {
                alert('Debe haber al menos un libro en el pedido');
            }
        }
        
        // Función para calcular el total
        function actualizarTotal() {
            let total = 0;
            
            document.querySelectorAll('.libro-item').forEach(item => {
                const select = item.querySelector('select');
                const cantidad = item.querySelector('input[type="number"]').value;
                
                if (select.selectedIndex > 0 && cantidad > 0) {
                    const precio = parseFloat(select.options[select.selectedIndex].dataset.precio);
                    total += precio * cantidad;
                }
            });
            
            document.getElementById('total').textContent = '$' + total.toFixed(2);
        }
        
        // Event listeners para actualizar el total cuando cambian los valores
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('libros-container').addEventListener('change', (e) => {
                if (e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT') {
                    actualizarTotal();
                }
            });
            
            document.getElementById('libros-container').addEventListener('input', (e) => {
                if (e.target.tagName === 'INPUT' && e.target.type === 'number') {
                    actualizarTotal();
                }
            });
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>