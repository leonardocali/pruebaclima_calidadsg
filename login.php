<?php
session_start();
require 'config.php'; // Asegúrate de tener un archivo de configuración con detalles de la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: protected.php');
            exit();
        } else {
            $_SESSION['error'] = 'Nombre de usuario o contraseña incorrectos.';
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
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../pruebaclima/css/estilos.css">
</head>
<body>
    <h1>Inicio de Sesión</h1>
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
        <input type="submit" value="Iniciar sesión">
    </form>
    <p>¿No tienes una cuenta? <a href="register.php">Registrarse</a></p>
</body>
</html>
