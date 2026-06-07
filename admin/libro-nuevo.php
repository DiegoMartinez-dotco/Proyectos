<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$categorias = $conn->query("SELECT * FROM categorias");

// Procesar creación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $descripcion = trim($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $isbn = trim($_POST['isbn']);
    $categoria_id = (int)$_POST['categoria_id'];
    $stock = (int)$_POST['stock'];
    $fecha_publicacion = $_POST['fecha_publicacion'];
    
    // Procesar imagen
    $imagen = 'default.jpg';
    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        // Definir ruta de destino correcta
        $directorio_destino = $_SERVER['DOCUMENT_ROOT'] . '/libreria/img/producto/';
        
        // Verificar si el directorio existe, si no, crearlo
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }
        
        // Generar nombre único para el archivo
        $nombre_archivo = uniqid() . '-' . basename($_FILES['imagen']['name']);
        $ruta_destino = $directorio_destino . $nombre_archivo;
        
        // Verificar que es una imagen válida
        $check = getimagesize($_FILES['imagen']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                $imagen = $nombre_archivo;
            } else {
                $_SESSION['error'] = "Hubo un error al subir la imagen.";
            }
        } else {
            $_SESSION['error'] = "El archivo no es una imagen válida.";
        }
    }
    
    $stmt = $conn->prepare("
        INSERT INTO libros (
            titulo, 
            autor, 
            descripcion, 
            precio, 
            isbn, 
            categoria_id, 
            stock, 
            fecha_publicacion, 
            imagen
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        "sssdssiss", 
        $titulo, 
        $autor, 
        $descripcion, 
        $precio, 
        $isbn, 
        $categoria_id, 
        $stock, 
        $fecha_publicacion, 
        $imagen
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Libro creado correctamente.";
        header("Location: libros.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al crear el libro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Libro</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
<?php include '../includes/header.php'; ?>
    <main class="container">
        <h2>Añadir Nuevo Libro</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <form action="libro-nuevo.php" method="post" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" id="isbn" name="isbn" required>
            </div>
            
            <div class="form-group">
                <label for="categoria_id">Categoría:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <?php while ($categoria = $categorias->fetch_assoc()): ?>
                        <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_publicacion">Fecha de publicación:</label>
                <input type="date" id="fecha_publicacion" name="fecha_publicacion" required>
            </div>
            
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <small>Formatos aceptados: JPG, JPEG, PNG</small>
            </div>
            
            <button type="submit" class="btn">Crear libro</button>
            <a href="libros.php" class="btn btn-cancelar">Cancelar</a>
        </form>
    </main>
<?php include '../includes/footer.php'; ?>
</body>
</html>