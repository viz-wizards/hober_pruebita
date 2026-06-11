<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requiere_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO servicios (nombre, descripcion, duracion_minutos, precio) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['nombre']),
        trim($_POST['descripcion'] ?? ''),
        (int) $_POST['duracion_minutos'],
        (float) $_POST['precio'],
    ]);
}

$servicios = $pdo->query('SELECT * FROM servicios ORDER BY nombre')->fetchAll();

render_header('Servicios');
?>
<section class="split">
    <form class="content-card form-card" method="post">
        <div class="section-title"><h1>Nuevo servicio</h1><span>Catalogo</span></div>
        <label>Nombre <input name="nombre" required></label>
        <label>Descripcion <input name="descripcion"></label>
        <div class="grid-2">
            <label>Duracion <input type="number" name="duracion_minutos" min="10" required></label>
            <label>Precio <input type="number" name="precio" min="0" step="100" required></label>
        </div>
        <button type="submit">Guardar servicio</button>
    </form>
    <section class="content-card">
        <div class="section-title"><h1>Servicios</h1><span><?php echo count($servicios); ?> activos</span></div>
        <div class="service-grid">
            <?php foreach ($servicios as $servicio): ?>
                <article class="service-card">
                    <h2><?php echo htmlspecialchars($servicio['nombre']); ?></h2>
                    <p><?php echo htmlspecialchars($servicio['descripcion']); ?></p>
                    <div>
                        <span><?php echo (int) $servicio['duracion_minutos']; ?> min</span>
                        <strong>$<?php echo number_format((float) $servicio['precio'], 0, ',', '.'); ?></strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</section>
<?php render_footer(); ?>
