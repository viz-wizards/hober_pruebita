<?php

session_start();

function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function requiere_login(): void
{
    if (!usuario_actual()) {
        header('Location: index.php');
        exit;
    }
}

function cerrar_sesion(): void
{
    $_SESSION = [];
    session_destroy();
}
