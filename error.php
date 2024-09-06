<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="../pruebaclima/css/estilos.css">
</head>
<body>
    <header>
        <h1>Error</h1>
    </header>
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php else: ?>
            <p style="color: red;">Se ha producido un error inesperado.</p>
        <?php endif; ?>
        <p><a href="login.php">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>
