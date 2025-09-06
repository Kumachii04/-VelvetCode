<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Cambié "correo" por "email"
    $password = $_POST['password'];

    // Consulta para obtener el cliente
    $sql = "SELECT * FROM Clientes WHERE email = ? AND contraseña = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email,$password]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el cliente existe y la contraseña es válida
    if ($cliente ) {
        $_SESSION['user_id'] = $cliente['id_cliente'];
        $_SESSION['nombre'] = $cliente['nombre'];
        

        echo "<script>alert('Inicio de sesión exitoso'); window.location.href = 'principal.php';</script>";
    } else {
        echo "<script>alert('Correo o contraseña incorrectos');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>
    <div class="container">
        <img src="imagenes/loogo.gif" alt="Logo" class="logo">
        <h2>Iniciar Sesión</h2>
        <form method="post">
            <label for="email">Correo Electrónico:</label> <!-- Cambié "correo" por "email" -->
            <input type="email" name="email" required>
            <br>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>