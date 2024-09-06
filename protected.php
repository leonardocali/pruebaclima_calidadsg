<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página Protegida</title>
    <link rel="stylesheet" href="../pruebaclima/css/estilos.css">
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Esta es una página protegida.</p>
    <?php header('Location: weater.php');?>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
