<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (usuario_actual()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE correo = ? LIMIT 1');
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        $_SESSION['usuario'] = [
            'id_usuario' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol'],
        ];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Correo o contrasena incorrectos.';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso | Corte Urbano</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="login-body">
    <section class="login-shell">
        <div class="login-panel">
            <p class="eyebrow">Agenda profesional</p>
            <h1>Barberia Corte Urbano</h1>
            <p>Controla reservas, barberos, servicios y pagos desde una sola pantalla.</p>
            <div class="login-stats">
                <span>Agenda diaria</span>
                <span>Pagos</span>
                <span>Clientes</span>
            </div>
        </div>
        <form class="login-card" method="post">
            <h2>Iniciar sesion</h2>
            <?php if ($error): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <label>
                Correo
                <input type="email" name="correo" value="admin@corteurbano.com" required>
            </label>
            <label>
                Contrasena
                <input type="password" name="password" value="admin123" required>
            </label>
            <button type="submit">Entrar al sistema</button>
        </form>
    </section>
</body>
</html>
