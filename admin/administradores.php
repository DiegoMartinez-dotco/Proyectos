<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['id'] != 1) {
    header("Location: login.php");
    exit;
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    if (eliminarAdministrador($id)) {
        $_SESSION['mensaje'] = "Administrador eliminado correctamente";
    } else {
        $_SESSION['error'] = "No se pudo eliminar al administrador";
    }
    header("Location: administradores.php");
    exit;
}

$administradores = obtenerAdministradores();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Administradores</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Inicio</a></li>
                    <li><a href="libros.php">Libros</a></li>
                    <li><a href="pedidos.php">Pedidos</a></li>
                    <li><a href="clientes.php">Clientes</a></li>
                    <li><a href="administradores.php">Administradores</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <h2>Gestión de Administradores</h2>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="admin-actions">
            <a href="registrar.php" class="btn">Registrar nuevo administrador</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Fecha de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($admin = $administradores->fetch_assoc()): ?>
                    <tr>
                        <td><?= $admin['id'] ?></td>
                        <td><?= htmlspecialchars($admin['usuario']) ?></td>
                        <td><?= htmlspecialchars($admin['nombre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($admin['created_at'])) ?></td>
                        <td>
                            <?php if ($admin['id'] != 1): // No permitir eliminar al superadmin ?>
                                <a href="administradores.php?eliminar=<?= $admin['id'] ?>" class="btn btn-eliminar"
                                   onclick="return confirm('¿Eliminar este administrador?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>