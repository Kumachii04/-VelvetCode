<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']); 

    $sql = "SELECT * FROM productos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $producto = $stmt->fetch();
    } else {
        echo "<h2>Producto no encontrado</h2>";
        exit; 
    }

    $sql_recommended = "SELECT * FROM productos WHERE id != :id ORDER BY RAND() LIMIT 4";
    $stmt_recommended = $pdo->prepare($sql_recommended);
    $stmt_recommended->bindParam(':id', $id_producto, PDO::PARAM_INT);
    $stmt_recommended->execute();
    $productos_recomendados = $stmt_recommended->fetchAll();

} else {
    echo "<h2>ID de producto no proporcionado</h2>";
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['iniciar_compra'])) {
    $producto_id = $id_producto;
    $cantidad = intval($_POST['cantidad']);

    if (!isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id] = [
            'id' => $producto_id,
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => $cantidad,
            'imagen' => $producto['imagen']
        ];
    } else {
        $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
    }

    header("Location: carrito.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link rel="stylesheet" href="detalle.css">
</head>

<body>
    <header>
        <img src="imagenes\loogo.gif" alt="Logo" class="logo">
        <h1>Comercio Electrónico NAEI Market</h1>

    </header>
    <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
    <section id="descripcion">
        <h2>Descripción del Producto</h2>
        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>"
            alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="producto-imagen">
        <p><?php echo nl2br(htmlspecialchars($producto['descripcion_larga'])); ?></p>
        <p>Descripción corta: <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
        <p>Precio: $<?php echo htmlspecialchars($producto['precio']); ?></p>
        <p>Stock: <?php echo htmlspecialchars($producto['stock']); ?> unidades</p>

        <form method="post">
            <label for="cantidad">Cantidad:</label>
            <input type="number" name="cantidad" id="cantidad" min="1" max="<?php echo $producto['stock']; ?>" required>
            <button type="submit" name="iniciar_compra" class="btn btn-comprar">Comprar</button>
        </form>
    </section>

    <section id="productos-recomendados">
        <h2>Productos que quizás quieras ver</h2>
        <div class="productos-grid">
            <?php foreach ($productos_recomendados as $producto_recomendado): ?>
            <div class="producto-item">
                <a href="descripcion_producto.php?id=<?php echo $producto_recomendado['id']; ?>">
                    <img src="<?php echo htmlspecialchars($producto_recomendado['imagen']); ?>"
                        alt="<?php echo htmlspecialchars($producto_recomendado['nombre']); ?>" class="producto-imagen">
                    <h3><?php echo htmlspecialchars($producto_recomendado['nombre']); ?></h3>
                    <p>Precio: $<?php echo htmlspecialchars($producto_recomendado['precio']); ?></p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="footer py-5 bg-dark text-light">
        <div class="container">
            <div class="row justify-content-between">
                <!-- Logo y Contacto -->
                <div class="col-md-3 text-center mb-4">
                    <h3 class="text-uppercase text-success">I.N.A.E</h3>
                    <img src="imagenes\loogo.gif" alt="Logo" class="logo">
                    <p><strong>Tienes alguna pregunta?</strong></p>
                    <p><i class="fas fa-phone-alt"></i> (507) 6551-5025</p>
                    <p><i class="fas fa-phone-alt"></i> (507) 383-7799</p>
                    <p><i class="fas fa-envelope"></i> ventasweb@loltec.com</p>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-light me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="text-uppercase text-success">Categorías Destacadas</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Accesorios</a></li>
                        <li><a href="#" class="text-light">Componentes</a></li>
                        <li><a href="#" class="text-light">Computadoras</a></li>
                        <li><a href="#" class="text-light">Monitores & Proyectores</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="text-uppercase text-success">Atención al Cliente</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Mi Cuenta</a></li>
                        <li><a href="#" class="text-light">Consulta tu Orden</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="text-uppercase text-success">Información</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Sobre Nosotros</a></li>
                        <li><a href="#" class="text-light">Políticas Y Privacidad</a></li>
                        <li><a href="#" class="text-light">Términos y Condiciones</a></li>
                        <li><a href="#" class="text-light">Preguntas Frecuentes</a></li>
                        <li><a href="#" class="text-light">Políticas De Garantías</a></li>
                    </ul>
                </div>
            </div>

            <hr class="bg-light">

            <div class="row mt-4">
                <div class="col text-center">
                    <p>&copy; 2024 N.A.E.I Market - Todos los Derechos Reservados</p>
                    <a href="#" class="btn btn-success">
                        <i class="fab fa-whatsapp"></i> Escríbenos por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </footer>

    </footer>
</body>

</html>