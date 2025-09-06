<?php
include 'db.php';

if (isset($_GET['id'])) {
    $categoria_id = $_GET['id'];
    try {
        $sql_categoria = "SELECT * FROM Categorias WHERE id = :id";
        $stmt_categoria = $pdo->prepare($sql_categoria);
        $stmt_categoria->bindParam(':id', $categoria_id, PDO::PARAM_INT);
        $stmt_categoria->execute();

        if ($stmt_categoria->rowCount() > 0) {
            $categoria = $stmt_categoria->fetch();

            $sql_productos = "SELECT * FROM Productos WHERE categoria_id = :categoria_id";
            $stmt_productos = $pdo->prepare($sql_productos);
            $stmt_productos->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
            $stmt_productos->execute();

            $productos = $stmt_productos->fetchAll();
        } else {
            $error = "Categoría no encontrada.";
        }
    } catch (PDOException $e) {
        $error = "Error en la consulta: " . $e->getMessage();
    }
} else {
    $error = "ID de categoría inválido.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Categoría</title>
    <link rel="stylesheet" href="sycap.css">
</head>

<body>
    <header>
        <h1>Detalles de Categoría</h1>
    </header>
    <main>
        <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
        <div class="categoria-detalle">
            <img src="<?php echo htmlspecialchars($categoria['imagen']); ?>"
                alt="<?php echo htmlspecialchars($categoria['nombre']); ?>" class="categoria-detalle-imagen">
            <h2><?php echo htmlspecialchars($categoria['nombre']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($categoria['descripcion'])); ?></p>
        </div>
        <section class="productos-categoria">
            <h2>Productos en esta categoría</h2>
            <?php if (!empty($productos)): ?>
            <div class="productos-grid">
                <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>"
                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="producto-imagen">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                    <p class="precio">Precio: $<?php echo number_format($producto['precio'], 2); ?></p>
                    <a href="descripcion_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-detalle">Ver
                        Detalles</a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No hay productos disponibles en esta categoría.</p>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </main>


    <footer>
        <a href="principal.php" class="btn">Volver a Categorías</a>
    </footer>
</body>

</html>