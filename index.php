<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/funciones.php';

// Obtener promociones destacadas
$promociones = obtenerPromocionesDestacadas(3);

// Obtener libros destacados
$libros_destacados = obtenerLibrosDestacados(6);
?>
<body style="background-image: url('img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

<?php include 'includes/header.php'; ?>

<!-- Sección de Promociones -->
<?php if ($promociones && $promociones->num_rows > 0): ?>
<section class="promociones-destacadas">
    <div class="container">
        <h2>Promociones Especiales</h2>
        <div class="promociones-slider">
            <?php while ($promocion = $promociones->fetch_assoc()): ?>
                <div class="promocion <?php echo $promocion['destacado'] ? 'destacada' : ''; ?>">
                    <?php if (!empty($promocion['imagen'])): ?>
                        <img 
                            src="/libreria/img/promociones/<?php echo htmlspecialchars($promocion['imagen']); ?>" 
                            alt="<?php echo htmlspecialchars($promocion['titulo']); ?>"
                            class="img-promocion"
                        >
                    <?php endif; ?>
                    <div class="promocion-info">
                        <h3><?php echo htmlspecialchars($promocion['titulo']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($promocion['descripcion'])); ?></p>
                        <div class="fechas">
                            <span>
                                Válido del <?php echo date('d/m/Y', strtotime($promocion['fecha_inicio'])); ?>
                                al <?php echo date('d/m/Y', strtotime($promocion['fecha_fin'])); ?>
                            </span>
                        </div>
                        <a href="promociones.php" class="btn">Ver todas las promociones</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="destacados">
    <h2>Novedades</h2>
    <div class="libros-grid">
        <?php while ($libro = $libros_destacados->fetch_assoc()): ?>
            <div class="libro">
                <?php if (!empty($libro['imagen'])): ?>
                    <img 
                        src="/libreria/img/producto/<?php echo htmlspecialchars($libro['imagen']); ?>" 
                        alt="<?php echo htmlspecialchars($libro['titulo']); ?>"
                        class="img-libro"
                    >
                <?php else: ?>
                    <img 
                        src="/libreria/img/producto/default.jpg" 
                        alt="Imagen no disponible"
                        class="img-libro"
                    >
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($libro['autor']); ?></p>
                <p class="precio">$<?php echo number_format($libro['precio'], 2); ?></p>
                <a href="/libreria/producto.php?id=<?php echo $libro['id']; ?>" class="btn">Ver detalles</a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<section class="categorias">
    <h2>Explora por categorías</h2>
    <div class="categorias-grid">
        <?php 
        $categorias = $conn->query("SELECT * FROM categorias LIMIT 6");
        while ($categoria = $categorias->fetch_assoc()): 
        ?>
            <div class="categoria">
                <a href="/libreria/productos.php?categoria=<?php echo $categoria['id']; ?>">
                    <h3><?php echo htmlspecialchars($categoria['nombre']); ?></h3>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>