<?php
session_start();
require 'config.php'; // Asegúrate de tener un archivo de configuración con detalles de la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
            $stmt->execute(['username' => $username, 'password_hash' => $password_hash]);
    
            $_SESSION['message'] = 'Registro exitoso, por favor inicie sesión.';
            header('Location: login.php');
            exit();
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Error al registrar el usuario. Por favor, inténtelo de nuevo más tarde.';
            header('Location: error.php');
            exit();
        }

    } else {
        $_SESSION['error'] = 'Todos los campos son requeridos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro usuario</title>
    <link rel="stylesheet" href="../pruebaclima/css/estilos.css">
</head>
<body>
    <h1>Registro de Usuarios</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <form method="post">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username">
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password">
        <br>
        <input type="submit" value="Registrar">
    </form>
    <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>