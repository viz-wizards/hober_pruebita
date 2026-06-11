<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requiere_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO clientes (nombre, telefono, correo, notas) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['nombre']),
        trim($_POST['telefono']),
        trim($_POST['correo'] ?? ''),
        trim($_POST['notas'] ?? ''),
    ]);
}

$clientes = $pdo->query('SELECT * FROM clientes ORDER BY creado_en DESC')->fetchAll();

render_header('Clientes');
?>
<section class="split">
    <form class="content-card form-card" method="post">
        <div class="section-title"><h1>Nuevo cliente</h1><span>Registro</span></div>
        <label>Nombre <input name="nombre" required></label>
        <label>Telefono <input name="telefono" required></label>
        <label>Correo <input type="email" name="correo"></label>
        <label>Notas <textarea name="notas" rows="3"></textarea></label>
        <button type="submit">Guardar cliente</button>
    </form>
    <section class="content-card">
        <div class="section-title"><h1>Clientes</h1><span><?php echo count($clientes); ?> registros</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nombre</th><th>Telefono</th><th>Correo</th><th>Notas</th></tr></thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['correo']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['notas']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
<?php render_footer(); ?>
