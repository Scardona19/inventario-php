<?php
require_once 'includes/config.php';
$conn = conectar();

$msg    = '';
$tipo   = '';
$accion = $_GET['accion'] ?? 'listar';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── REGISTRAR MOVIMIENTO ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prod_id    = (int)$_POST['producto_id'];
    $tipo_mov   = in_array($_POST['tipo'], ['entrada','salida']) ? $_POST['tipo'] : '';
    $cantidad   = (int)$_POST['cantidad'];
    $observacion = trim($conn->real_escape_string($_POST['observacion']));

    if (!$prod_id || !$tipo_mov || $cantidad <= 0) {
        $msg  = 'Completa todos los campos con valores válidos.';
        $tipo = 'danger';
        $accion = 'nuevo';
    } else {
        // Verificar stock suficiente para salidas
        if ($tipo_mov === 'salida') {
            $stock_actual = (int)$conn->query("SELECT stock FROM productos WHERE id=$prod_id")->fetch_assoc()['stock'];
            if ($cantidad > $stock_actual) {
                $msg  = "Stock insuficiente. Disponible: $stock_actual unidades.";
                $tipo = 'danger';
                $accion = 'nuevo';
                goto renderizar;
            }
            $conn->query("UPDATE productos SET stock = stock - $cantidad WHERE id=$prod_id");
        } else {
            $conn->query("UPDATE productos SET stock = stock + $cantidad WHERE id=$prod_id");
        }
        $conn->query("INSERT INTO movimientos (producto_id, tipo, cantidad, observacion) VALUES ($prod_id, '$tipo_mov', $cantidad, '$observacion')");
        $msg  = 'Movimiento registrado y stock actualizado.';
        $tipo = 'success';
        $accion = 'listar';
    }
}

// ── ELIMINAR ───────────────────────────────────────────────────────────────
if ($accion === 'eliminar' && $id > 0) {
    $conn->query("DELETE FROM movimientos WHERE id=$id");
    $msg  = 'Movimiento eliminado.';
    $tipo = 'warning';
    $accion = 'listar';
}

$productos_select = $conn->query("SELECT id, nombre, stock FROM productos ORDER BY nombre");

renderizar:
require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0"><i class="bi bi-arrow-left-right"></i> Movimientos de Inventario</h2>
    <?php if ($accion === 'listar'): ?>
    <a href="movimientos.php?accion=nuevo" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Registrar Movimiento</a>
    <?php else: ?>
    <a href="movimientos.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $tipo ?> alert-dismissible fade show">
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($accion === 'listar'): ?>
<?php
$lista = $conn->query(
    "SELECT m.*, p.nombre AS producto
     FROM movimientos m
     JOIN productos p ON m.producto_id = p.id
     ORDER BY m.fecha DESC"
);
?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Producto</th><th class="text-center">Tipo</th><th class="text-center">Cantidad</th><th>Observación</th><th>Fecha</th><th class="text-center">Acc.</th></tr></thead>
            <tbody>
                <?php if ($lista->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Sin movimientos registrados.</td></tr>
                <?php else: ?>
                <?php while ($r = $lista->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted small"><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['producto']) ?></td>
                    <td class="text-center">
                        <?php if ($r['tipo'] === 'entrada'): ?>
                            <span class="badge bg-success"><i class="bi bi-arrow-down-circle"></i> Entrada</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><i class="bi bi-arrow-up-circle"></i> Salida</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center fw-bold"><?= $r['cantidad'] ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($r['observacion']) ?></td>
                    <td class="small"><?= date('d/m/Y H:i', strtotime($r['fecha'])) ?></td>
                    <td class="text-center">
                        <a href="movimientos.php?accion=eliminar&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger btn-action btn-delete">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php else: ?>
<div class="card" style="max-width:500px;">
    <div class="card-header bg-primary text-white">Registrar Movimiento</div>
    <div class="card-body">
        <form method="POST" action="movimientos.php">
            <div class="mb-3">
                <label class="form-label fw-semibold">Producto *</label>
                <select name="producto_id" class="form-select" required>
                    <option value="">-- Seleccionar --</option>
                    <?php while ($p = $productos_select->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?> (Stock: <?= $p['stock'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tipo de Movimiento *</label>
                <select name="tipo" class="form-select" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="entrada">📥 Entrada (aumenta stock)</option>
                    <option value="salida">📤 Salida (reduce stock)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Cantidad *</label>
                <input type="number" min="1" name="cantidad" class="form-control" required placeholder="Ej: 10">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Observación</label>
                <input type="text" name="observacion" class="form-control" placeholder="Ej: Compra a proveedor, venta, etc.">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Registrar</button>
                <a href="movimientos.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php $conn->close(); require_once 'includes/footer.php'; ?>
