<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requiere_login();

$hoy = date('Y-m-d');
$totalCitas = $pdo->query("SELECT COUNT(*) total FROM citas WHERE fecha = CURDATE()")->fetch()['total'];
$clientes = $pdo->query('SELECT COUNT(*) total FROM clientes')->fetch()['total'];
$ingresos = $pdo->query("SELECT COALESCE(SUM(monto), 0) total FROM pagos WHERE estado = 'pagado'")->fetch()['total'];

$stmt = $pdo->prepare(
    'SELECT c.*, cl.nombre cliente, e.nombre empleado, s.nombre servicio, s.precio
     FROM citas c
     JOIN clientes cl ON cl.id_cliente = c.id_cliente
     JOIN empleados e ON e.id_empleado = c.id_empleado
     JOIN servicios s ON s.id_servicio = c.id_servicio
     WHERE c.fecha = ?
     ORDER BY c.hora ASC'
);
$stmt->execute([$hoy]);
$citas = $stmt->fetchAll();

render_header('Dashboard');
?>
<section class="hero">
    <div>
        <p class="eyebrow">Panel de control</p>
        <h1>Agenda de hoy</h1>
        <p>Gestion rapida de reservas, estado de atencion y cobros de la barberia.</p>
    </div>
    <a class="button primary" href="appointments.php">Nueva cita</a>
</section>

<section class="metrics">
    <article>
        <span>Citas hoy</span>
        <strong><?php echo $totalCitas; ?></strong>
    </article>
    <article>
        <span>Clientes</span>
        <strong><?php echo $clientes; ?></strong>
    </article>
    <article>
        <span>Ingresos pagos</span>
        <strong>$<?php echo number_format((float) $ingresos, 0, ',', '.'); ?></strong>
    </article>
</section>

<section class="content-card">
    <div class="section-title">
        <h2>Citas del dia</h2>
        <span><?php echo htmlspecialchars($hoy); ?></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Barbero</th>
                    <th>Servicio</th>
                    <th>Precio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td><?php echo substr($cita['hora'], 0, 5); ?></td>
                        <td><?php echo htmlspecialchars($cita['cliente']); ?></td>
                        <td><?php echo htmlspecialchars($cita['empleado']); ?></td>
                        <td><?php echo htmlspecialchars($cita['servicio']); ?></td>
                        <td>$<?php echo number_format((float) $cita['precio'], 0, ',', '.'); ?></td>
                        <td><?php echo estado_badge($cita['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$citas): ?>
                    <tr><td colspan="6" class="empty">No hay citas para hoy.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php render_footer(); ?>
