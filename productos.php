<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/funciones.php';

$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$titulo_pagina = $categoria_id ? "Libros por categoría" : "Todos nuestros libros";

if ($categoria_id) {
    $libros = obtenerLibrosPorCategoria($categoria_id);
    $stmt = $conn->prepare("SELECT nombre FROM categorias WHERE id = ?");
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $categoria = $stmt->get_result()->fetch_assoc();
    $titulo_pagina = "Categoría: " . htmlspecialchars($categoria['nombre']);
} else {
    $libros = $conn->query("SELECT * FROM libros");
}
?>
<body style="background-image: url('img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
<?php include 'includes/header.php'; ?>

<h1><?php echo htmlspecialchars($titulo_pagina); ?></h1>

<div class="libros-grid">
    <?php while ($libro = $libros->fetch_assoc()): ?>
        <div class="libro">
            <?php if (!empty($libro['imagen'])): ?>
                <img 
                    src="/libreria/img/producto/<?php echo htmlspecialchars($libro['imagen']); ?>" 
                    alt="<?php echo htmlspecialchars($libro['titulo']); ?>"
                >
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
            <p><?php echo htmlspecialchars($libro['autor']); ?></p>
            <p class="precio">$<?php echo number_format($libro['precio'], 2); ?></p>
            <a href="/libreria/producto.php?id=<?php echo $libro['id']; ?>" class="btn">Ver detalles</a>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>