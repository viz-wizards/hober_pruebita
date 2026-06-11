<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requiere_login();

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $datos = [
            $_POST['id_cliente'] ?? null,
            $_POST['id_empleado'] ?? null,
            $_POST['id_servicio'] ?? null,
            $_POST['fecha'] ?? null,
            $_POST['hora'] ?? null,
            trim($_POST['observaciones'] ?? ''),
            usuario_actual()['id_usuario'],
        ];

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO citas (id_cliente, id_empleado, id_servicio, fecha, hora, observaciones, creada_por)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute($datos);
            $mensaje = 'Cita creada correctamente.';
        } catch (PDOException $exception) {
            $error = 'Ese barbero ya tiene una cita en esa fecha y hora.';
        }
    }

    if ($accion === 'estado') {
        $stmt = $pdo->prepare('UPDATE citas SET estado = ? WHERE id_cita = ?');
        $stmt->execute([$_POST['estado'], $_POST['id_cita']]);
        $mensaje = 'Estado actualizado.';
    }

    if ($accion === 'pago') {
        $stmt = $pdo->prepare(
            'INSERT INTO pagos (id_cita, metodo, monto, estado, pagado_en)
             VALUES (?, ?, ?, "pagado", NOW())
             ON DUPLICATE KEY UPDATE metodo = VALUES(metodo), monto = VALUES(monto), estado = "pagado", pagado_en = NOW()'
        );
        $stmt->execute([$_POST['id_cita'], $_POST['metodo'], $_POST['monto']]);
        $mensaje = 'Pago registrado.';
    }
}

$fecha = $_GET['fecha'] ?? date('Y-m-d');
$clientes = $pdo->query('SELECT id_cliente, nombre FROM clientes ORDER BY nombre')->fetchAll();
$empleados = $pdo->query('SELECT id_empleado, nombre FROM empleados WHERE activo = 1 ORDER BY nombre')->fetchAll();
$servicios = $pdo->query('SELECT id_servicio, nombre, precio FROM servicios WHERE activo = 1 ORDER BY nombre')->fetchAll();

$stmt = $pdo->prepare(
    'SELECT c.*, cl.nombre cliente, e.nombre empleado, s.nombre servicio, s.precio, p.estado pago_estado
     FROM citas c
     JOIN clientes cl ON cl.id_cliente = c.id_cliente
     JOIN empleados e ON e.id_empleado = c.id_empleado
     JOIN servicios s ON s.id_servicio = c.id_servicio
     LEFT JOIN pagos p ON p.id_cita = c.id_cita
     WHERE c.fecha = ?
     ORDER BY c.hora ASC'
);
$stmt->execute([$fecha]);
$citas = $stmt->fetchAll();

render_header('Agenda');
?>
<section class="split">
    <form class="content-card form-card" method="post">
        <input type="hidden" name="accion" value="crear">
        <div class="section-title">
            <h1>Nueva cita</h1>
            <span>Reserva</span>
        </div>
        <?php if ($mensaje): ?><div class="notice"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <label>Cliente
            <select name="id_cliente" required>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?php echo $cliente['id_cliente']; ?>"><?php echo htmlspecialchars($cliente['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Barbero
            <select name="id_empleado" required>
                <?php foreach ($empleados as $empleado): ?>
                    <option value="<?php echo $empleado['id_empleado']; ?>"><?php echo htmlspecialchars($empleado['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Servicio
            <select name="id_servicio" required>
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?php echo $servicio['id_servicio']; ?>">
                        <?php echo htmlspecialchars($servicio['nombre']); ?> - $<?php echo number_format((float) $servicio['precio'], 0, ',', '.'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="grid-2">
            <label>Fecha <input type="date" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>" required></label>
            <label>Hora <input type="time" name="hora" required></label>
        </div>
        <label>Observaciones
            <textarea name="observaciones" rows="3"></textarea>
        </label>
        <button type="submit">Guardar cita</button>
    </form>

    <section class="content-card">
        <div class="section-title">
            <h1>Agenda</h1>
            <form class="date-filter" method="get">
                <input type="date" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
                <button type="submit">Ver</button>
            </form>
        </div>
        <div class="appointment-list">
            <?php foreach ($citas as $cita): ?>
                <article class="appointment-item">
                    <div>
                        <strong><?php echo substr($cita['hora'], 0, 5); ?> - <?php echo htmlspecialchars($cita['cliente']); ?></strong>
                        <span><?php echo htmlspecialchars($cita['servicio']); ?> con <?php echo htmlspecialchars($cita['empleado']); ?></span>
                        <?php echo estado_badge($cita['estado']); ?>
                    </div>
                    <form method="post">
                        <input type="hidden" name="accion" value="estado">
                        <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">
                        <select name="estado">
                            <?php foreach (['pendiente', 'confirmada', 'atendida', 'cancelada'] as $estado): ?>
                                <option value="<?php echo $estado; ?>" <?php echo $estado === $cita['estado'] ? 'selected' : ''; ?>><?php echo ucfirst($estado); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                    <form method="post" class="pay-form">
                        <input type="hidden" name="accion" value="pago">
                        <input type="hidden" name="id_cita" value="<?php echo $cita['id_cita']; ?>">
                        <input type="hidden" name="monto" value="<?php echo $cita['precio']; ?>">
                        <select name="metodo">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                        <button type="submit"><?php echo $cita['pago_estado'] === 'pagado' ? 'Pagado' : 'Cobrar'; ?></button>
                    </form>
                </article>
            <?php endforeach; ?>
            <?php if (!$citas): ?>
                <p class="empty">No hay citas en esta fecha.</p>
            <?php endif; ?>
        </div>
    </section>
</section>
<?php render_footer(); ?>
