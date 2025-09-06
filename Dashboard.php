<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$total_sales_stmt = $pdo->query("
    SELECT COUNT(*) AS total_sales, SUM(precio_total) AS total_revenue 
    FROM Ventas
");
$total_sales_data = $total_sales_stmt->fetch(PDO::FETCH_ASSOC);

$new_clients_stmt = $pdo->query("
    SELECT COUNT(*) AS new_clients 
    FROM Clientes 
    WHERE fecha_registro >= CURDATE() - INTERVAL 30 DAY
");
$new_clients = $new_clients_stmt->fetchColumn();

$top_product_stmt = $pdo->query("
    SELECT Productos.nombre, SUM(Ventas.cantidad) AS total_sold
    FROM Ventas
    INNER JOIN Productos ON Ventas.id_producto = Productos.id
    GROUP BY Ventas.id_producto
    ORDER BY total_sold DESC
    LIMIT 1
");
$top_product = $top_product_stmt->fetch(PDO::FETCH_ASSOC);

$pending_orders_stmt = $pdo->query("
    SELECT COUNT(*) AS pending_orders 
    FROM Pedidos 
    WHERE estado = 'pendiente'
");
$pending_orders = $pending_orders_stmt->fetchColumn();

$active_clients_stmt = $pdo->query("
    SELECT COUNT(*) AS active_clients 
    FROM Clientes
");
$active_clients = $active_clients_stmt->fetchColumn();

$equipment_status_stmt = $pdo->query("
    SELECT
        SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) AS available,
        SUM(CASE WHEN estado = 'en uso' THEN 1 ELSE 0 END) AS in_use,
        SUM(CASE WHEN estado = 'fuera de servicio' THEN 1 ELSE 0 END) AS out_of_service
    FROM Equipos
");
$equipment_status = $equipment_status_stmt->fetch(PDO::FETCH_ASSOC);

$tarjeta_stmt = $pdo->query("
    SELECT metodo_pago, tipo_tarjeta, monto 
    FROM pagos 
    WHERE metodo_pago = 'tarjeta'
");
$tarjeta_payments = $tarjeta_stmt->fetchAll(PDO::FETCH_ASSOC);

$paypal_stmt = $pdo->query("
    SELECT metodo_pago, tipo_tarjeta, monto 
    FROM pagos 
    WHERE metodo_pago = 'paypal'
");
$paypal_payments = $paypal_stmt->fetchAll(PDO::FETCH_ASSOC);

$personal_data_stmt = $pdo->prepare("
    SELECT direccion, telefono, fecha_nacimiento, nacionalidad 
    FROM datos_personales 
    WHERE usuario_id = ?
");
$personal_data_stmt->execute([$user_id]);
$personal_data = $personal_data_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Dashboard</title>
</head>

<body>
    <div class="container">
        <img src="imagenes\loogo.gif" alt="Logo" class="logo">
        <header>
            <h1>Dashboard</h1>
            <p>Bienvenido</p>

            <?php if (!empty($user['foto_perfil'])): ?>
            <div class="profile-picture">
                <img src="imagenes/<?php echo htmlspecialchars($user['foto_perfil']); ?>" alt="Foto de perfil"
                    class="profile-img">
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['nombre']) . ' ' . htmlspecialchars($user['apellido']); ?></h2>
                <p>Rol: <?php echo htmlspecialchars($user['rol']); ?></p>
            </div>
            <?php else: ?>
            <p>No se ha establecido una foto de perfil.</p>
            <?php endif; ?>

            <div class="personal-data">
                <h3>Datos Personales</h3>
                <?php if ($personal_data): ?>

                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($personal_data['telefono']); ?></p>
                <p><strong>Fecha de Nacimiento:</strong>
                    <?php echo htmlspecialchars($personal_data['fecha_nacimiento']); ?></p>
                <p><strong>Nacionalidad:</strong> <?php echo htmlspecialchars($personal_data['nacionalidad']); ?></p>
                <?php else: ?>
                <p>No se han encontrado datos personales para este usuario.</p>
                <?php endif; ?>
            </div>


            <nav>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="stats">
                <h2>Zona Administrativas</h2>
                <div class="stat-card">
                    <h3>Comercio Electrónico NAEI Market</h3>
                </div>
            </section>

            <section class="user-list">
                <h2>Datos del Usuario</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Rol</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($user['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($user['correo']); ?></td>
                            <td><?php echo htmlspecialchars($user['rol']); ?></td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <?php if ($user['rol'] === 'admin'): ?>
            <section class="admin-actions">
                <h2>Acciones Administrativas</h2>
                <a href="pedidos.php" class="button">Ver Pedidos</a>
            </section>
            <?php endif; ?>

            <?php if ($user['rol'] === 'contador'): ?>
            <section class="sales-action">
                <h2>Acciones para Contadores</h2>
                <a href="ventas.php" class="button">Ver Ventas</a>
            </section>
            <?php endif; ?>

            <?php if ($user['rol'] === 'ayudante'): ?>
            <section class="product-action">
                <h2>Acciones para Ayudantes</h2>
                <a href="equipos.php" class="button">Ver acciones</a>
            </section>
            <section class="entregas-action">
                <section class="entregas-action">
                    <h2>Entregas después del 18 de noviembre de 2024</h2>
                    <?php
                    $entregas_stmt = $pdo->prepare("
                    SELECT id_entrega, id_venta, metodo_entrega, fecha_entrega, hora_entrega, tipo_envio 
                    FROM Entregas 
                    WHERE fecha_entrega > '2024-11-18' ");
                    $entregas_stmt->execute();
                    $entregas = $entregas_stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php if ($entregas): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Entrega</th>
                                <th>ID Venta</th>
                                <th>Método de Entrega</th>
                                <th>Fecha Entrega</th>
                                <th>Hora Entrega</th>
                                <th>Tipo Envío</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entregas as $entrega): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entrega['id_entrega']); ?></td>
                                <td><?php echo htmlspecialchars($entrega['id_venta']); ?></td>
                                <td><?php echo htmlspecialchars($entrega['metodo_entrega']); ?></td>
                                <td><?php echo htmlspecialchars($entrega['fecha_entrega']); ?></td>
                                <td><?php echo htmlspecialchars($entrega['hora_entrega']); ?></td>
                                <td><?php echo htmlspecialchars($entrega['tipo_envio']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No hay entregas programadas después del 18 de noviembre de 2024.</p>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <?php if ($user['rol'] === 'admin' && $user['correo'] === 'Nayelis.Gilbot@gmail.com'): ?>
                <section class="stats">
                    <h2>Resumen General</h2>
                    <div class="stat-card">
                        <p>Ventas Totales: <span><?php echo $total_sales_data['total_sales']; ?></span></p>
                        <p>Ingresos Totales:
                            <span>$<?php echo number_format($total_sales_data['total_revenue'], 2); ?></span>
                        </p>
                        <p>Clientes Nuevos: <span><?php echo $new_clients; ?></span></p>
                        <p>Productos Más Vendidos: <span><?php echo $top_product['nombre']; ?></span></p>
                        <p>Pedidos Pendientes: <span><?php echo $pending_orders; ?></span></p>
                    </div>
                </section>
                <section class="clients-panel">
                    <h2>Panel de Clientes</h2>
                    <div class="stat-card">
                        <p>Clientes Activos: <span><?php echo $active_clients; ?></span></p>
                    </div>
                </section>

                <section>
                    <h2>Monitoreo de Pagos con Tarjeta</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Método de Pago</th>
                                <th>Accion de Pago</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tarjeta_payments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['metodo_pago']); ?></td>
                                <td><?php echo htmlspecialchars($payment['tipo_tarjeta']); ?></td>
                                <td>$<?php echo number_format($payment['monto'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
                <?php endif; ?>

                <?php if ($user['rol'] === 'admin' && $user['correo'] === 'Enier.Arauz@gmail.com'): ?>
                <section>
                    <h2>Monitoreo de Pagos con Paypal</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Método de Pago</th>
                                <th>Accion de Pago</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paypal_payments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['metodo_pago']); ?></td>
                                <td><?php echo htmlspecialchars($payment['tipo_tarjeta']); ?></td>
                                <td>$<?php echo number_format($payment['monto'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
                <?php endif; ?>
        </main>
    </div>
</body>

</html>