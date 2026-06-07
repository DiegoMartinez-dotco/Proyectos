<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Estadísticas
$total_libros = contarLibros();
$total_pedidos = contarPedidos();
$total_clientes = contarClientes();
$pedidos_recientes = obtenerPedidosRecientes(5);

// Promociones
$promociones_activas = obtenerPromocionesActivas(5);
$promociones_destacadas = obtenerPromocionesDestacadas(3);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <style>
        /* Estilos para promociones */
        .promo-card {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background: white;
            max-width: 300px;
        }
        
        .promo-image-container {
            width: 100%;
            height: 200px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        
        .promo-image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .promociones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .promo-info h3 {
            margin-top: 0;
            color: #333;
        }
        
        .promo-info small {
            color: #666;
            font-size: 0.9em;
        }
        
        /* Estilos para pedidos recientes - Texto negro */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            color: #000 !important;
        }
        
        .admin-table th,
        .admin-table td,
        .admin-table a {
            color: #000 !important;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .admin-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .admin-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .admin-table tr:hover td,
        .admin-table tr:hover th {
            color: #000 !important;
        }
        
        /* Estilo para botón de búsqueda - Texto negro */
        .form-busqueda button[type="submit"] {
            color: #000 !important;
        }
    </style>
</head>
<body>
<body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
<?php include '../includes/header.php'; ?>

<main class="container">
    <h2>Resumen</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Libros</h3>
            <p><?php echo $total_libros; ?></p>
            <a href="libros.php" class="btn">Gestionar</a>
        </div>
        
        <div class="stat-card">
            <h3>Pedidos</h3>
            <p><?php echo $total_pedidos; ?></p>
            <a href="pedidos.php" class="btn">Gestionar</a>
        </div>
        
        <div class="stat-card">
            <h3>Clientes</h3>
            <p><?php echo $total_clientes; ?></p>
            <a href="clientes.php" class="btn">Gestionar</a>
        </div>

        <div class="stat-card">
            <h3>Promos</h3>
            <a href="/libreria/agregar_promocion.php" class="btn">Gestionar</a>
        </div>
    </div>
    
    <h2>Pedidos recientes</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($pedido = $pedidos_recientes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $pedido['id']; ?></td>
                    <td><?php echo $pedido['cliente']; ?></td>
                    <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                    <td><?php echo ucfirst($pedido['estado']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?></td>
                    <td>
                        <a href="pedido.php?id=<?php echo $pedido['id']; ?>" class="btn">Ver</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Promociones Destacadas</h2>
    <div class="promociones-destacadas">
        <?php if ($promociones_destacadas && $promociones_destacadas->num_rows > 0): ?>
            <div class="promociones-grid">
                <?php while ($promo = $promociones_destacadas->fetch_assoc()): ?>
                    <div class="promo-card">
                        <?php if (!empty($promo['imagen'])): ?>
                            <div class="promo-image-container">
                                <img src="/libreria/img/promociones/<?php echo htmlspecialchars($promo['imagen']); ?>" alt="<?php echo htmlspecialchars($promo['titulo']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="promo-info">
                            <h3><?php echo htmlspecialchars($promo['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($promo['descripcion']); ?></p>
                            <small>Desde <?php echo date('d/m/Y', strtotime($promo['fecha_inicio'])); ?> hasta <?php echo date('d/m/Y', strtotime($promo['fecha_fin'])); ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No hay promociones destacadas activas actualmente.</p>
        <?php endif; ?>
    </div>

    <h2>Promociones Activas</h2>
    <div class="promociones-activas">
        <?php if ($promociones_activas && $promociones_activas->num_rows > 0): ?>
            <div class="promociones-grid">
                <?php while ($promo = $promociones_activas->fetch_assoc()): ?>
                    <div class="promo-card">
                        <?php if (!empty($promo['imagen'])): ?>
                            <div class="promo-image-container">
                                <img src="/libreria/img/promociones/<?php echo htmlspecialchars($promo['imagen']); ?>" alt="<?php echo htmlspecialchars($promo['titulo']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="promo-info">
                            <h3><?php echo htmlspecialchars($promo['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($promo['descripcion']); ?></p>
                            <small>Desde <?php echo date('d/m/Y', strtotime($promo['fecha_inicio'])); ?> hasta <?php echo date('d/m/Y', strtotime($promo['fecha_fin'])); ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No hay promociones activas actualmente.</p>
        <?php endif; ?>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
</body>
</html>