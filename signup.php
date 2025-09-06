<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO Clientes (nombre, email, telefono, direccion, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$nombre, $email, $telefono, $direccion]);

        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = 'iniciar.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error al registrarte: {$e->getMessage()}');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - NAEI Market</title>
</head>
<style>
body {
    font-family: 'Arial', sans-serif;
    background: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

form {
    background: #fff;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 20px;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 16px;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus {
    border-color: #2e7d32;
    outline: none;
    box-shadow: 0 0 5px rgba(46, 125, 50, 0.5);
}

button {
    background-color: #2e7d32;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #388e3c;
}

script {
    text-align: center;
}

input[type="text"],
input[type="email"],
input[type="password"],
button {
    margin-bottom: 15px;
}
</style>

<body>
    <form action="signup.php" method="POST">
        <h2>Registro</h2>
        <input type="text" name="nombre" placeholder="Nombre Completo" required>
        <input type="email" name="email" placeholder="Correo Electrónico" required>
        <input type="text" name="telefono" placeholder="Teléfono">
        <input type="text" name="direccion" placeholder="Dirección">
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
    </form>
</body>

</html>