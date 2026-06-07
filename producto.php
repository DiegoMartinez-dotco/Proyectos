<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/funciones.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /libreria/");
    exit;
}

$libro_id = intval($_GET['id']);
$libro = obtenerLibroPorId($libro_id);

if (!$libro) {
    header("Location: /libreria/");
    exit;
}
?>
<body style="background-image: url('img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
<?php include 'includes/header.php'; ?>

<div class="producto-detalle">
    <div class="producto-imagen">
        <?php if (!empty($libro['imagen'])): ?>
            <img 
                src="/libreria/img/producto/<?php echo htmlspecialchars($libro['imagen']); ?>" 
                alt="<?php echo htmlspecialchars($libro['titulo']); ?>"
                class="img-producto"
            >
        <?php else: ?>
            <img 
                src="/libreria/img/producto/sin-imagen.jpg" 
                alt="Imagen no disponible"
                class="img-producto"
            >
        <?php endif; ?>
    </div>
    <div class="producto-info">
        <h1><?php echo htmlspecialchars($libro['titulo']); ?></h1>
        <p class="autor">Autor: <?php echo htmlspecialchars($libro['autor']); ?></p>
        <p class="categoria">Categoría: <?php echo htmlspecialchars($libro['categoria_nombre']); ?></p>
        <p class="isbn">ISBN: <?php echo htmlspecialchars($libro['isbn']); ?></p>
        <p class="descripcion"><?php echo nl2br(htmlspecialchars($libro['descripcion'])); ?></p>
        <p class="precio">$<?php echo number_format($libro['precio'], 2); ?></p>
        
        <form action="/libreria/carrito.php" method="post">
            <input type="hidden" name="libro_id" value="<?php echo $libro['id']; ?>">
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>