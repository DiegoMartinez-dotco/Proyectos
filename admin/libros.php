<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Obtener todos los libros con categorías
$libros = $conn->query("
    SELECT l.*, c.nombre as categoria 
    FROM libros l 
    JOIN categorias c ON l.categoria_id = c.id
    ORDER BY l.titulo
");

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM libros WHERE id = $id");
    header("Location: libros.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
<body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
<?php include '../includes/header.php'; ?>
    
    <main class="container">
        <a href="/libreria/admin/dashboard.php" class="back-arrow">←</a>
        <h2>Gestión de Libros</h2>
        
        <div class="admin-actions">
            <a href="libro-nuevo.php" class="btn">Añadir nuevo libro</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($libro = $libros->fetch_assoc()): ?>
                    <tr>
                        <td><?= $libro['id'] ?></td>
                        <td><?= htmlspecialchars($libro['titulo']) ?></td>
                        <td><?= htmlspecialchars($libro['autor']) ?></td>
                        <td><?= htmlspecialchars($libro['categoria']) ?></td>
                        <td>$<?= number_format($libro['precio'], 2) ?></td>
                        <td><?= $libro['stock'] ?></td>
                        <td>
                            <a href="libro-editar.php?id=<?= $libro['id'] ?>" class="btn">Editar</a>
                            <a href="libros.php?eliminar=<?= $libro['id'] ?>" class="btn btn-eliminar" 
                               onclick="return confirm('¿Eliminar este libro?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

<?php include '../includes/footer.php'; ?>
</body>
</html>