<?php
require_once 'includes/db.php';
require_once 'includes/funciones.php';

$resultados = [];
$termino = '';

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $termino = trim($_GET['q']);
    $termino_like = "%$termino%";
    
    $stmt = $conn->prepare("
        SELECT l.*, c.nombre as categoria 
        FROM libros l
        JOIN categorias c ON l.categoria_id = c.id
        WHERE l.titulo LIKE ? OR l.autor LIKE ? OR l.isbn LIKE ? OR c.nombre LIKE ?
        ORDER BY l.titulo
    ");
    $stmt->bind_param("ssss", $termino_like, $termino_like, $termino_like, $termino_like);
    $stmt->execute();
    $resultados = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar libros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/estilo.css">
    <style>
        .form-busqueda button[type="submit"] {
            color: black;
        }
    </style>
</head>
<body>
<body style="background-image: url('img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

<?php include 'includes/header.php'; ?>

<section class="busqueda">
    <h2>Buscar en nuestra librería</h2>
    
    <form action="buscar.php" method="GET" class="form-busqueda">
        <input type="text" name="q" placeholder="Buscar libros por título, autor, categoría o ISBN..." value="<?php echo htmlspecialchars($termino); ?>">
        <button type="submit">Buscar</button>
    </form>
    
    <?php if (!empty($termino)): ?>
        <h3>Resultados para "<?php echo htmlspecialchars($termino); ?>"</h3>
        
        <?php if ($resultados->num_rows > 0): ?>
            <div class="resultados-grid">
                <?php while ($libro = $resultados->fetch_assoc()): ?>
                    <div class="libro">
                        <img src="/libreria/img/producto/<?php echo $libro['imagen']; ?>" alt="<?php echo htmlspecialchars($libro['titulo']); ?>">
                        <h4><?php echo htmlspecialchars($libro['titulo']); ?></h4>
                        <p><?php echo htmlspecialchars($libro['autor']); ?></p>
                        <p class="categoria"><?php echo htmlspecialchars($libro['categoria']); ?></p>
                        <p class="precio">$<?php echo number_format($libro['precio'], 2); ?></p>
                        <a href="producto.php?id=<?php echo $libro['id']; ?>" class="btn">Ver detalles</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No se encontraron resultados para tu búsqueda.</p>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>