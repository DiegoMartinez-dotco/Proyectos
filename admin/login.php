<?php
session_start();
require_once '../includes/db.php'; // Asegúrate que este archivo define $conn


if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

// Mostrar mensaje de registro exitoso si existe
$registro_exitoso = false;
if (isset($_SESSION['registro_exitoso'])) {
    $registro_exitoso = true;
    unset($_SESSION['registro_exitoso']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    
    // Verifica que $conn esté definida
    if (!isset($conn)) {
        die("Error de conexión a la base de datos");
    }
    
    $stmt = $conn->prepare("SELECT * FROM administradores WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $admin = $resultado->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin;
            header("Location: dashboard.php");
            exit;
        }
    }
    
    $error = "Usuario o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<!-- Resto del código HTML permanece igual -->
<!DOCTYPE html>
<html lang="es">
<head>
    <style>
        body {
            background-image: url('C:\xampp\htdocs\libreria\img');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
        }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>
    <body style="background-image: url('../img/fondo.jpeg'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat;">

    <div style="background-color: rgba(255, 255, 255, 0.9); padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <div class="container" style="max-width: 500px; margin-top: 50px;">
        <h1 style="text-align: center;">Inicio de Sesión</h1>
        
        <?php if ($registro_exitoso): ?>
            <div class="alert alert-success">¡Registro exitoso! Por favor inicia sesión.</div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="post" class="admin-form">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Iniciar sesión</button>
            <p style="text-align: center; margin-top: 25px;">
                ¿No tienes una cuenta? <a href="registrar.php">Regístrate aquí</a>
            </p>
        </form>
    </div>
</body>
</html>