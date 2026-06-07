<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: libros.php");
    exit;
}

$id = intval($_GET['id']);
$libro = $conn->query("SELECT * FROM libros WHERE id = $id")->fetch_assoc();

if (!$libro) {
    header("Location: libros.php");
    exit;
}

$categorias = $conn->query("SELECT * FROM categorias");

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $descripcion = trim($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $isbn = trim($_POST['isbn']);
    $categoria_id = (int)$_POST['categoria_id'];
    $stock = (int)$_POST['stock'];
    $fecha_publicacion = $_POST['fecha_publicacion'];
    
    // Procesar imagen si se subió una nueva
    $imagen = $libro['imagen'];
    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        // Configuración para la subida de imágenes
        $directorio_destino = '../img/producto/';
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid('libro_') . '.' . $extension;
        $ruta_destino = $directorio_destino . $nombre_archivo;
        
        // Validar tipo de archivo
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($extension), $extensiones_permitidas)) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                // Eliminar la imagen anterior si existe y no es la predeterminada
                if ($imagen && $imagen !== 'default.jpg' && file_exists($directorio_destino . $imagen)) {
                    unlink($directorio_destino . $imagen);
                }
                $imagen = $nombre_archivo;
            } else {
                $_SESSION['error'] = "Error al subir la imagen";
            }
        } else {
            $_SESSION['error'] = "Formato de imagen no permitido. Use JPG, PNG o GIF";
        }
    }
    
    $stmt = $conn->prepare("
        UPDATE libros SET 
        titulo = ?, 
        autor = ?, 
        descripcion = ?, 
        precio = ?, 
        isbn = ?, 
        categoria_id = ?, 
        stock = ?, 
        fecha_publicacion = ?, 
        imagen = ? 
        WHERE id = ?
    ");
    
    $stmt->bind_param(
        "sssdssissi", 
        $titulo, 
        $autor, 
        $descripcion, 
        $precio, 
        $isbn, 
        $categoria_id, 
        $stock, 
        $fecha_publicacion, 
        $imagen, 
        $id
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Libro actualizado correctamente";
        header("Location: libros.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al actualizar el libro";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
<body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
<?php include '../includes/header.php'; ?>
    
    <main class="container">
        <h2>Editar Libro</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <form action="libro-editar.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($libro['autor']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="5" required><?php echo htmlspecialchars($libro['descripcion']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" value="<?php echo $libro['precio']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($libro['isbn']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="categoria_id">Categoría:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <?php while ($categoria = $categorias->fetch_assoc()): ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php echo $categoria['id'] == $libro['categoria_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo $libro['stock']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_publicacion">Fecha de publicación:</label>
                <input type="date" id="fecha_publicacion" name="fecha_publicacion" value="<?php echo $libro['fecha_publicacion']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p>Imagen actual: <?php echo htmlspecialchars($libro['imagen']); ?></p>
                <?php if ($libro['imagen'] && $libro['imagen'] !== 'default.jpg'): ?>
                    <img src="../img/producto/<?php echo htmlspecialchars($libro['imagen']); ?>" alt="Imagen actual" style="max-width: 200px;">
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Actualizar libro</button>
            <a href="libros.php" class="btn btn-cancelar">Cancelar</a>
        </form>
    </main>

<?php include '../includes/footer.php'; ?>
</body>
</html>