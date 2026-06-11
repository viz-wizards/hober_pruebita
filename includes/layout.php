<?php

function render_header(string $titulo): void
{
    $usuario = usuario_actual();
    ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlspecialchars($titulo); ?> | Corte Urbano</title>
        <link rel="stylesheet" href="assets/css/styles.css">
    </head>
    <body>
    <header class="topbar">
        <a class="brand" href="dashboard.php">
            <span class="brand-mark">CU</span>
            <span>Corte Urbano</span>
        </a>
        <?php if ($usuario): ?>
            <nav class="nav">
                <a href="dashboard.php">Inicio</a>
                <a href="appointments.php">Agenda</a>
                <a href="customers.php">Clientes</a>
                <a href="services.php">Servicios</a>
                <a href="logout.php">Salir</a>
            </nav>
        <?php endif; ?>
    </header>
    <main class="page">
    <?php
}

function render_footer(): void
{
    ?>
    </main>
    <script src="assets/js/app.js"></script>
    </body>
    </html>
    <?php
}

function estado_badge(string $estado): string
{
    return '<span class="badge badge-' . htmlspecialchars($estado) . '">' . htmlspecialchars(ucfirst($estado)) . '</span>';
}
