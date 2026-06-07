<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Procesar eliminación
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Primero obtenemos la imagen para borrarla del servidor
    $stmt = $conn->prepare("SELECT imagen FROM promociones WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $promocion = $result->fetch_assoc();
    $stmt->close();
    
    if ($promocion && $promocion['imagen']) {
        $ruta_imagen = $_SERVER['DOCUMENT_ROOT'] . "/libreria/img/promociones/" . $promocion['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }
    
    // Luego eliminamos el registro de la base de datos
    $stmt = $conn->prepare("DELETE FROM promociones WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = "Promoción eliminada exitosamente";
    } else {
        $error = "Error al eliminar promoción: " . $stmt->error;
    }
    
    $stmt->close();
}

// Obtener todas las promociones para listar
$promociones = [];
$stmt = $conn->prepare("SELECT * FROM promociones ORDER BY fecha_inicio DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $promociones[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Promociones</title>
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
            max-width: 1200px;
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
        
        .btn-agregar {
            background-color: #4CAF50;
        }
        
        .btn-agregar:hover {
            background-color: #3e8e41;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .imagen-promo {
            max-width: 100px;
            max-height: 60px;
            display: block;
            border-radius: 3px;
        }
        
        .sin-imagen {
            color: #6c757d;
            font-style: italic;
            font-size: 13px;
        }
        
        .destacado {
            color: #28a745;
            font-weight: bold;
        }
        
        .no-destacado {
            color: #6c757d;
        }
        
        .actions {
            white-space: nowrap;
        }
        
        .btn-editar {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-editar:hover {
            background-color: #e0a800;
        }
        
        .btn-eliminar {
            background-color: #dc3545;
        }
        
        .btn-eliminar:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <body style="background-image: url('img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

        <div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <?php include 'includes/header.php'; ?>
        
        <div class="header-section">
            <a href="/libreria/admin/dashboard.php" class="back-arrow">←</a>
            <h2>Administrar Promociones</h2>
            <a href="nueva_promocion.php" class="btn btn-agregar">+ Nueva Promoción</a>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Imagen</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Destacado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promociones as $promocion): ?>
                <tr>
                    <td><?php echo htmlspecialchars($promocion['titulo']); ?></td>
                    <td>
                        <?php if ($promocion['imagen']): ?>
                            <img src="img/promociones/<?php echo htmlspecialchars($promocion['imagen']); ?>" alt="Imagen promoción" class="imagen-promo">
                        <?php else: ?>
                            <span class="sin-imagen">Sin imagen</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($promocion['fecha_inicio']); ?></td>
                    <td><?php echo htmlspecialchars($promocion['fecha_fin']); ?></td>
                    <td>
                        <?php echo $promocion['destacado'] ? '<span class="destacado">Sí</span>' : '<span class="no-destacado">No</span>'; ?>
                    </td>
                    <td class="actions">
                        <a href="editar_promocion.php?id=<?php echo $promocion['id']; ?>" class="btn btn-editar">Editar</a>
                        <a href="?delete=<?php echo $promocion['id']; ?>" class="btn btn-eliminar">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>