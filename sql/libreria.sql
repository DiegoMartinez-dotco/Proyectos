-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS libreria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE libreria;

-- Tabla de categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de libros
CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    imagen VARCHAR(255) DEFAULT 'default.jpg',
    categoria_id INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    fecha_publicacion DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'procesando', 'enviado', 'completado', 'cancelado') DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Tabla de detalles de pedido
CREATE TABLE detalles_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    libro_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (libro_id) REFERENCES libros(id)
);

-- Tabla de administradores
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar datos iniciales
INSERT INTO categorias (nombre, descripcion) VALUES
('Literatura', 'Novelas y obras literarias clásicas y contemporáneas'),
('Ciencia Ficción', 'Libros de ciencia ficción y fantasía'),
('Terror', 'Novelas de terror y suspenso'),
('Autoayuda', 'Libros de desarrollo personal y autoayuda'),
('Infantil', 'Libros para niños y jóvenes'),
('Tecnología', 'Libros sobre programación, tecnología y ciencia');

INSERT INTO libros (titulo, autor, descripcion, precio, isbn, imagen, categoria_id, stock, fecha_publicacion) VALUES
('Cien años de soledad', 'Gabriel García Márquez', 'Una obra maestra de la literatura hispanoamericana', 25.99, '9780307474728', 'cien-anos-soledad.jpg', 1, 50, '1967-05-30'),
('1984', 'George Orwell', 'Una distopía clásica sobre vigilancia y control', 18.50, '9780451524935', '1984.jpg', 2, 30, '1949-06-08'),
('El resplandor', 'Stephen King', 'Una novela de terror psicológico', 22.75, '9780307743657', 'resplandor.jpg', 3, 25, '1977-01-28'),
('El poder del ahora', 'Eckhart Tolle', 'Una guía para el despertar espiritual', 15.99, '9781577314806', 'poder-ahora.jpg', 4, 40, '1997-01-01'),
('Harry Potter y la piedra filosofal', 'J.K. Rowling', 'El primer libro de la serie Harry Potter', 19.99, '9788478884456', 'harry-potter.jpg', 5, 60, '1997-06-26'),
('Clean Code', 'Robert C. Martin', 'Un manual para escribir código limpio', 35.50, '9780132350884', 'clean-code.jpg', 6, 20, '2008-08-01');

INSERT INTO administradores (usuario, password, nombre) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Principal');
-- Contraseña: password