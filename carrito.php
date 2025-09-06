<?php

session_start();
include 'db.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            if (isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
                $producto_id = intval($_POST['producto_id']);
                $cantidad = intval($_POST['cantidad']);

                $stmt = $pdo->prepare("SELECT * FROM Productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch();

                if ($producto) {
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
                }
            }
            break;

        case 'update':
            if (isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
                $producto_id = intval($_POST['producto_id']);
                $cantidad = intval($_POST['cantidad']);

                if ($cantidad > 0) {
                    $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
                } else {
                    unset($_SESSION['carrito'][$producto_id]);
                }
            }
            break;

        case 'remove':
            if (isset($_POST['producto_id'])) {
                $producto_id = intval($_POST['producto_id']);
                unset($_SESSION['carrito'][$producto_id]);
            }
            break;
    }


    header('Location: carrito.php');
    exit;
}

$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <title>Carrito de Compras</title>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f3f4f6;
        margin: 0;
        padding: 0;
    }

    .cart-container {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        font-size: 2.5rem;
        color: #333333;
        text-align: center;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }


    .cart-item {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        padding: 15px 0;
    }

    .cart-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-right: 20px;
    }

    .cart-item-details {
        flex-grow: 1;
    }

    .cart-item-details h3 {
        font-size: 1.4rem;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .cart-item-details p {
        color: #4a5568;
        margin: 0;
        font-size: 1.2rem;
    }

    .cart-actions {
        margin-top: 10px;
    }

    .quantity-input {
        width: 60px;
        padding: 5px;
        border: 1px solid #cbd5e0;
        border-radius: 4px;
        font-size: 1rem;
        text-align: center;
        margin-right: 10px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 1rem;
        color: #ffffff;
        background-color: #0046be;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #003599;
    }

    .btn-remove {
        background-color: #e53e3e;
        margin-left: 10px;
    }

    .btn-remove:hover {
        background-color: #c53030;
    }

    .cart-item-total {
        font-size: 1.2rem;
        color: #2d3748;
        font-weight: 600;
        text-align: right;
    }

    .cart-summary {
        margin-top: 20px;
        text-align: center;
    }

    .cart-summary h2 {
        font-size: 2rem;
        color: #0046be;
        margin-bottom: 10px;
    }

    .cart-summary p {
        font-size: 1.5rem;
        color: #1a202c;
        margin-bottom: 20px;
    }

    .cart-summary .btn {
        padding: 12px 30px;
        font-size: 1.2rem;
        background-color: #ffc220;
        color: #0046be;
        font-weight: 600;
    }

    .cart-summary .btn:hover {
        background-color: #f5b900;
    }

    a {
        color: #0046be;
        text-decoration: none;
        font-weight: 600;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="cart-container">
        <h1>Carrito de Compras</h1>

        <?php if (empty($_SESSION['carrito'])): ?>
        <p>Tu carrito está vacío</p>
        <?php else: ?>
        <?php foreach ($_SESSION['carrito'] as $item): ?>
        <div class="cart-item">
            <img src="<?php echo htmlspecialchars($item['imagen']); ?>"
                alt="<?php echo htmlspecialchars($item['nombre']); ?>">

            <div class="cart-item-details">
                <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                <p>Precio: $<?php echo number_format($item['precio'], 2); ?></p>

                <div class="cart-actions">
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                        <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1"
                            class="quantity-input">
                        <button type="submit" class="btn btn-update">Actualizar</button>
                    </form>

                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="btn btn-remove">Eliminar</button>
                    </form>
                </div>
            </div>

            <div class="cart-item-total">
                <strong>Subtotal: $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></strong>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="cart-summary">
            <h2>Resumen del Carrito</h2>
            <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
            <button onclick="window.location='checkout.php'" class="btn btn-update">
                Proceder al Pago
            </button>
        </div>
        <?php endif; ?>

        <p><a href="principal.php">Continuar Comprando</a></p>
    </div>
</body>

</html>