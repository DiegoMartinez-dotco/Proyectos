<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Procesar creación de nueva promoción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $imagen = null;
    
    // Validaciones básicas
    if (empty($titulo) || empty($descripcion)) {
        $error = "Título y descripción son obligatorios";
    } elseif ($fecha_inicio > $fecha_fin) {
        $error = "La fecha de inicio debe ser anterior a la fecha de fin";
    } else {
        // Procesamiento de imagen si se subió
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array(strtolower($extension), $extensiones_permitidas)) {
                $nombre_archivo = uniqid('promo_', true) . '.' . $extension;
                $ruta_destino = $_SERVER['DOCUMENT_ROOT'] . "/libreria/img/promociones/" . $nombre_archivo;
                
                // Verificar y crear carpeta si no existe
                if (!file_exists(dirname($ruta_destino))) {
                    mkdir(dirname($ruta_destino), 0777, true);
                }
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                    $imagen = $nombre_archivo;
                } else {
                    $error = "Error al subir la imagen. Verifica permisos de carpeta.";
                }
            } else {
                $error = "Formato de imagen no permitido. Use JPG, PNG o GIF";
            }
        }
        
        // Si no hay errores, procedemos con la inserción
        if (empty($error)) {
            try {
                if ($imagen) {
                    $stmt = $conn->prepare("INSERT INTO promociones (titulo, descripcion, imagen, fecha_inicio, fecha_fin, destacado) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssi", $titulo, $descripcion, $imagen, $fecha_inicio, $fecha_fin, $destacado);
                } else {
                    $stmt = $conn->prepare("INSERT INTO promociones (titulo, descripcion, fecha_inicio, fecha_fin, destacado) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha_inicio, $fecha_fin, $destacado);
                }
                
                if ($stmt->execute()) {
                    $success = "Promoción creada exitosamente";
                    // Limpiar los campos del formulario
                    $titulo = $descripcion = $fecha_inicio = $fecha_fin = '';
                    $destacado = 0;
                } else {
                    $error = "Error al crear promoción: " . $stmt->error;
                }
                
                $stmt->close();
            } catch (Exception $e) {
                $error = "Error en la base de datos: " . $e->getMessage();
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
    <title>Nueva Promoción</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <style>
        body {
            background-image: url('../img/fondo.jpeg');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            padding: 20px;
            font-family: Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        
        .main-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        
        .header-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .back-arrow {
            display: inline-block;
            margin-right: 15px;
            font-size: 24px;
            color: #2196F3;
            text-decoration: none;
            vertical-align: middle;
        }
        
        h2 {
            margin: 0;
            color: #333;
            flex-grow: 1;
        }
        
        .btn {
            padding: 8px 15px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            border: none;
            cursor: pointer;
            margin-left: 10px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0b7dda;
        }
        
        .btn-cancelar {
            background-color: #6c757d;
        }
        
        .btn-cancelar:hover {
            background-color: #5a6268;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid transparent;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
<body style="background-image: url('img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

<div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <div class="main-container">
        <?php include 'includes/header.php'; ?>
        
        <div class="header-section">
            <a href="agregar_promocion.php" class="back-arrow">←</a>
            <h2>Nueva Promoción</h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="create" value="1">
            
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo isset($titulo) ? htmlspecialchars($titulo) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required><?php echo isset($descripcion) ? htmlspecialchars($descripcion) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" required>
                <small>Formatos permitidos: JPG, PNG, GIF. Máx. 2MB</small>
            </div>
            
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo isset($fecha_inicio) ? htmlspecialchars($fecha_inicio) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo isset($fecha_fin) ? htmlspecialchars($fecha_fin) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="destacado" id="destacado" <?php echo (isset($destacado) && $destacado) ? 'checked' : ''; ?>> Destacar esta promoción
                </label>
            </div>
            
            <div class="form-actions">
                <a href="admin_promociones.php" class="btn btn-cancelar">Cancelar</a>
                <button type="submit" class="btn">Crear Promoción</button>
            </div>
        </form>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Validar tamaño de imagen antes de subir
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // in MB
                if (fileSize > 2) {
                    alert('El archivo es demasiado grande (máximo 2MB)');
                    e.target.value = '';
                }
            }
        });
    </script>
</body>
</html>