<?php
require_once 'includes/db.php';
require_once 'includes/funciones.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$promociones = $conn->query("
    SELECT * FROM promociones 
    WHERE fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()
    ORDER BY destacado DESC, fecha_inicio DESC
");
?>
<body style="background-image: url('img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

<?php include 'includes/header.php'; ?>

<section class="promociones">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Novedades y Promociones</h2>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
            <a href="agregar_promocion.php" class="btn-admin" style="
                background-color: #4CAF50;
                color: white;
                padding: 10px 15px;
                text-decoration: none;
                border-radius: 4px;
                font-weight: bold;
                transition: background-color 0.3s;
            ">Administrar Promociones</a>
        <?php endif; ?>
    </div>
    
    <?php if ($promociones->num_rows > 0): ?>
        <div class="promociones-grid">
            <?php while ($promocion = $promociones->fetch_assoc()): ?>
                <div class="promocion <?php echo $promocion['destacado'] ? 'destacada' : ''; ?>">
                    <?php if (!empty($promocion['imagen'])): ?>
                        <img 
                            src="img/promociones/<?php echo htmlspecialchars($promocion['imagen']); ?>" 
                            alt="<?php echo htmlspecialchars($promocion['titulo']); ?>"
                        >
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($promocion['titulo']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($promocion['descripcion'])); ?></p>
                    <div class="fechas">
                        <span>
                            Válido del <?php echo date('d/m/Y', strtotime($promocion['fecha_inicio'])); ?>
                            al <?php echo date('d/m/Y', strtotime($promocion['fecha_fin'])); ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Actualmente no hay promociones disponibles.</p>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="agregar_promocion.php" class="btn-admin" style="
                    background-color: #4CAF50;
                    color: white;
                    padding: 10px 15px;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: bold;
                    transition: background-color 0.3s;
                    display: inline-block;
                ">Agregar Nueva Promoción</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>