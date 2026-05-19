<?php
require_once 'includes/config.php';
$conn = conectar();

$msg   = '';
$tipo  = '';
$accion = $_GET['accion'] ?? 'listar';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── CREAR / ACTUALIZAR ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = trim($conn->real_escape_string($_POST['nombre']));
    $descripcion = trim($conn->real_escape_string($_POST['descripcion']));
    $precio      = (float) $_POST['precio'];
    $stock       = (int)   $_POST['stock'];
    $stock_min   = (int)   $_POST['stock_minimo'];
    $cat_id      = (int)   $_POST['categoria_id'];
    $prov_id     = (int)   $_POST['proveedor_id'];

    if (empty($nombre) || $precio < 0 || $stock < 0 || $cat_id === 0 || $prov_id === 0) {
        $msg  = 'Por favor completa todos los campos correctamente.';
        $tipo = 'danger';
        $accion = ($_POST['edit_id'] ?? 0) ? 'editar' : 'nuevo';
        $id = (int)($_POST['edit_id'] ?? 0);
    } elseif (!empty($_POST['edit_id'])) {
        // UPDATE
        $eid = (int)$_POST['edit_id'];
        $conn->query(
            "UPDATE productos SET nombre='$nombre', descripcion='$descripcion',
             precio=$precio, stock=$stock, stock_minimo=$stock_min,
             categoria_id=$cat_id, proveedor_id=$prov_id
             WHERE id=$eid"
        );
        $msg  = 'Producto actualizado correctamente.';
        $tipo = 'success';
        $accion = 'listar';
    } else {
        // INSERT
        $conn->query(
            "INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, categoria_id, proveedor_id)
             VALUES ('$nombre', '$descripcion', $precio, $stock, $stock_min, $cat_id, $prov_id)"
        );
        $msg  = 'Producto creado correctamente.';
        $tipo = 'success';
        $accion = 'listar';
    }
}

// ── ELIMINAR ───────────────────────────────────────────────────────────────
if ($accion === 'eliminar' && $id > 0) {
    $conn->query("DELETE FROM productos WHERE id=$id");
    $msg  = 'Producto eliminado.';
    $tipo = 'warning';
    $accion = 'listar';
}

// ── CARGAR DATOS PARA EDITAR ───────────────────────────────────────────────
$producto = null;
if ($accion === 'editar' && $id > 0) {
    $producto = $conn->query("SELECT * FROM productos WHERE id=$id")->fetch_assoc();
    if (!$producto) { $accion = 'listar'; }
}

// ── LISTAS AUXILIARES ──────────────────────────────────────────────────────
$categorias  = $conn->query("SELECT id, nombre FROM categorias ORDER BY nombre");
$proveedores = $conn->query("SELECT id, nombre FROM proveedores ORDER BY nombre");

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0"><i class="bi bi-box-seam"></i> Productos</h2>
    <?php if ($accion === 'listar'): ?>
    <a href="productos.php?accion=nuevo" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nuevo Producto
    </a>
    <?php else: ?>
    <a href="productos.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($accion === 'listar'): ?>
<!-- ── LISTA ───────────────────────────────────────────────────────────── -->
<?php
$productos = $conn->query(
    "SELECT p.*, c.nombre AS categoria, pv.nombre AS proveedor
     FROM productos p
     JOIN categorias c ON p.categoria_id = c.id
     JOIN proveedores pv ON p.proveedor_id = pv.id
     ORDER BY p.nombre"
);
?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($productos->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No hay productos registrados.</td></tr>
                <?php else: ?>
                <?php while ($p = $productos->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted small"><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                    <td><?= htmlspecialchars($p['proveedor']) ?></td>
                    <td class="text-end">$<?= number_format($p['precio'], 2) ?></td>
                    <td class="text-center">
                        <?php if ($p['stock'] <= $p['stock_minimo']): ?>
                            <span class="badge bg-danger"><?= $p['stock'] ?></span>
                        <?php else: ?>
                            <span class="badge bg-success"><?= $p['stock'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="productos.php?accion=editar&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary btn-action">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="productos.php?accion=eliminar&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger btn-action btn-delete">
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
<!-- ── FORMULARIO CREAR / EDITAR ──────────────────────────────────────── -->
<div class="card" style="max-width:680px;">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-<?= $accion === 'editar' ? 'pencil' : 'plus-lg' ?>"></i>
        <?= $accion === 'editar' ? 'Editar Producto' : 'Nuevo Producto' ?>
    </div>
    <div class="card-body">
        <form method="POST" action="productos.php">
            <?php if ($accion === 'editar'): ?>
            <input type="hidden" name="edit_id" value="<?= $producto['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre *</label>
                <input type="text" name="nombre" class="form-control" required
                    value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Precio *</label>
                    <input type="number" step="0.01" min="0" name="precio" class="form-control" required
                        value="<?= $producto['precio'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock *</label>
                    <input type="number" min="0" name="stock" class="form-control" required
                        value="<?= $producto['stock'] ?? 0 ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock Mínimo *</label>
                    <input type="number" min="0" name="stock_minimo" class="form-control" required
                        value="<?= $producto['stock_minimo'] ?? 5 ?>">
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Categoría *</label>
                    <select name="categoria_id" class="form-select" required>
                        <option value="">-- Seleccionar --</option>
                        <?php $categorias->data_seek(0); while ($c = $categorias->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= isset($producto) && $producto['categoria_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Proveedor *</label>
                    <select name="proveedor_id" class="form-select" required>
                        <option value="">-- Seleccionar --</option>
                        <?php $proveedores->data_seek(0); while ($pv = $proveedores->fetch_assoc()): ?>
                        <option value="<?= $pv['id'] ?>"
                            <?= isset($producto) && $producto['proveedor_id'] == $pv['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pv['nombre']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar
                </button>
                <a href="productos.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
$conn->close();
require_once 'includes/footer.php';
?>
