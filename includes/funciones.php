<?php
// Función para obtener libros destacados
function obtenerLibrosDestacados($limite = 5) {
    global $conn;
    $sql = "SELECT * FROM libros ORDER BY fecha_publicacion DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    return $stmt->get_result();
}

// Función para obtener libros por categoría
function obtenerLibrosPorCategoria($categoria_id) {
    global $conn;
    $sql = "SELECT * FROM libros WHERE categoria_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Función para obtener un libro por ID
function obtenerLibroPorId($id) {
    global $conn;
    $sql = "SELECT l.*, c.nombre as categoria_nombre FROM libros l 
            JOIN categorias c ON l.categoria_id = c.id 
            WHERE l.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Función para buscar libros
function buscarLibros($termino) {
    global $conn;
    $termino = "%$termino%";
    $sql = "SELECT * FROM libros WHERE titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $termino, $termino, $termino);
    $stmt->execute();
    return $stmt->get_result();
}

// Función para agregar al carrito
function agregarAlCarrito($libro_id, $cantidad = 1) {
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    if (isset($_SESSION['carrito'][$libro_id])) {
        $_SESSION['carrito'][$libro_id] += $cantidad;
    } else {
        $_SESSION['carrito'][$libro_id] = $cantidad;
    }
}

// Función para obtener productos del carrito
function obtenerProductosCarrito() {
    if (empty($_SESSION['carrito'])) {
        return [];
    }
    
    global $conn;
    $ids = implode(",", array_keys($_SESSION['carrito']));
    $sql = "SELECT * FROM libros WHERE id IN ($ids)";
    $resultado = $conn->query($sql);
    
    $productos = [];
    while ($libro = $resultado->fetch_assoc()) {
        $libro['cantidad'] = $_SESSION['carrito'][$libro['id']];
        $productos[] = $libro;
    }
    
    return $productos;
}

// Función para obtener el conteo de libros
function contarLibros() {
    global $conn;
    $resultado = $conn->query("SELECT COUNT(*) as total FROM libros");
    return $resultado->fetch_assoc()['total'];
}

// Función para obtener el conteo de pedidos
function contarPedidos() {
    global $conn;
    $resultado = $conn->query("SELECT COUNT(*) as total FROM pedidos");
    return $resultado->fetch_assoc()['total'];
}

// Función para obtener el conteo de clientes
function contarClientes() {
    global $conn;
    $resultado = $conn->query("SELECT COUNT(*) as total FROM clientes");
    return $resultado->fetch_assoc()['total'];
}

// Función para obtener pedidos recientes
function obtenerPedidosRecientes($limite = 5) {
    global $conn;
    return $conn->query("
        SELECT p.*, c.nombre as cliente 
        FROM pedidos p 
        JOIN clientes c ON p.cliente_id = c.id 
        ORDER BY p.fecha_pedido DESC 
        LIMIT $limite
    ");
}

// Función para verificar si un usuario ya existe
function usuarioExiste($usuario) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM administradores WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Función para obtener todos los administradores
function obtenerAdministradores() {
    global $conn;
    return $conn->query("SELECT id, usuario, nombre, created_at FROM administradores ORDER BY nombre");
}

// Función para eliminar un administrador (excepto el superadmin)
function eliminarAdministrador($id) {
    if ($id == 1) return false; // No se puede eliminar al superadmin
    
    global $conn;
    $stmt = $conn->prepare("DELETE FROM administradores WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Función para obtener los libros de un pedido
function obtenerLibrosPedido($pedido_id) {
    global $conn;
    return $conn->query("
        SELECT d.*, l.titulo, l.autor, l.imagen
        FROM detalles_pedido d
        JOIN libros l ON d.libro_id = l.id
        WHERE d.pedido_id = $pedido_id
    ");
}

// Función para crear un nuevo pedido
function crearPedido($cliente_id, $libros, $cantidades) {
    global $conn;
    
    $conn->begin_transaction();
    try {
        // Calcular total
        $total = 0;
        foreach ($libros as $index => $libro_id) {
            $libro = $conn->query("SELECT precio FROM libros WHERE id = $libro_id")->fetch_assoc();
            $total += $libro['precio'] * $cantidades[$index];
        }
        
        // Insertar pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (?, ?)");
        $stmt->bind_param("id", $cliente_id, $total);
        $stmt->execute();
        $pedido_id = $conn->insert_id;
        
        // Insertar detalles y actualizar stock
        foreach ($libros as $index => $libro_id) {
            $libro = $conn->query("SELECT precio FROM libros WHERE id = $libro_id")->fetch_assoc();
            
            $stmt = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, libro_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $pedido_id, $libro_id, $cantidades[$index], $libro['precio']);
            $stmt->execute();
            
            $conn->query("UPDATE libros SET stock = stock - {$cantidades[$index]} WHERE id = $libro_id");
        }
        
        $conn->commit();
        return $pedido_id;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

// Función para verificar si un email de cliente ya existe
function emailClienteExiste($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Función para registrar un nuevo cliente
function registrarCliente($nombre, $email, $telefono, $direccion, $password) {
    global $conn;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono, direccion, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $email, $telefono, $direccion, $password_hash);
    
    return $stmt->execute();
}

// Función para obtener promociones activas
function obtenerPromocionesActivas($limite = null) {
    global $conn;
    $sql = "SELECT * FROM promociones 
            WHERE fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()
            ORDER BY destacado DESC, fecha_inicio DESC";
    
    if ($limite) {
        $sql .= " LIMIT $limite";
    }
    
    return $conn->query($sql);
}

// Función para obtener promociones destacadas
function obtenerPromocionesDestacadas($limite = 3) {
    global $conn;
    $sql = "SELECT * FROM promociones 
            WHERE destacado = 1 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()
            ORDER BY fecha_inicio DESC
            LIMIT $limite";
    
    return $conn->query($sql);
}
// Función para agregar una nueva promoción
function agregarPromocion($titulo, $descripcion, $fecha_inicio, $fecha_fin, $destacado = 0) {
    global $conn;
    $sql = "INSERT INTO promociones (titulo, descripcion, fecha_inicio, fecha_fin, destacado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha_inicio, $fecha_fin, $destacado);
    return $stmt->execute();
}

// Función para obtener categorías
function obtenerCategorias() {
    global $conn;
    return $conn->query("SELECT * FROM categorias ORDER BY nombre");
}
?>