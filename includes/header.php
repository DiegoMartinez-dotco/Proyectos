<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librería Online</title>
    <link rel="stylesheet" href="/libreria/assets/css/estilo.css">
    <!-- Font Awesome para el icono de lupa -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos adicionales para el nuevo diseño */
        .site-header {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .main-logo {
            text-align: center;
            padding: 20px 0;
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #333;
        }
        
        .main-nav {
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 0 20px;
        }
        
        .main-nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        
        .main-nav li {
            background-color: transparent; /* Asegura que el LI no tenga color */
            margin: 5px;                   /* Espaciado entre botones */
            padding: 0;                    /* Quita cualquier padding extra */
            border: none;                 /* Elimina bordes si los hubiera */
        }

        .main-nav a {
            display: block;
            padding: 15px 20px;
            background-color: white;     /* Fondo blanco */
            color: black;                /* Texto negro */
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            white-space: nowrap;
            border: 1px solid #ccc;      /* Borde gris claro */
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .main-nav a:hover {
            background-color: #f1f1f1;   /* Fondo gris claro al pasar el mouse */
            color: black;
        }


        
        /* ESTILOS CORREGIDOS PARA EL BUSCADOR */
        .header-right {
            text-align: center;
            padding: 15px 0;
        }
        
        .search-form {
            display: inline-flex;
            margin: 0 auto;
        }
        
        .search-form input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 0.9rem;
            width: 250px;
            border-right: none;
        }
        
        .search-form {
            display: inline-flex;
            align-items: center;
            margin: 0 auto;
        }

        .search-form input {
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
            outline: none;
            border-right: none;
        }
        
        @media (max-width: 768px) {
            .main-nav ul {
                flex-direction: column;
                align-items: center;
            }
            
            .search-form input {
                width: 180px;
            }
        }
        section,
        .container,
        .box,
        .card {
            background-color: #ffffff; /* Fondo blanco */
            color: #000000;            /* Texto negro */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

    </style>
</head>
<body>
    <header class="site-header">
        <div class="main-logo">
            <a href="/libreria/" style="color: inherit; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <img src="/libreria/img/logo.jpg" alt="Logo de MABBAN" style="height: 100px;">
                <span style="font-size: 2.5rem; font-weight: bold; letter-spacing: 2px; color: #333;">KINGDOME</span>
            </a>
        </div>

        
        <nav class="main-nav">
            <ul>
                <li><a href="/libreria/buscar.php">BUSCAR</a></li>
                <li><a href="/libreria/">INICIO</a></li>
                <li><a href="/libreria/productos.php">CATÁLOGO</a></li>
                <li><a href="/libreria/promociones.php">PROMOCIONES</a></li>
                <li><a href="/libreria/admin/cliente.php">CLIENTES</a></li>
                <li><a href="/libreria/admin/pedido.php">PEDIDOS</a></li>
                <li><a href="/libreria/admin/dashboard.php">ADMINISTRACIÓN GENERAL</a></li>
                <li><a href="/libreria/admin/logout.php">CERRAR SESIÓN</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="container">