<?php
require_once 'includes/config.php';
$conn = conectar();

$msg    = '';
$tipo   = '';
$accion = $_GET['accion'] ?? 'listar';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($conn->real_escape_string($_POST['nombre']));
    $contacto  = trim($conn->real_escape_string($_POST['contacto']));
    $telefono  = trim($conn->real_escape_string($_POST['telefono']));
    $email     = trim($conn->real_escape_string($_POST['email']));
    $direccion = trim($conn->real_escape_string($_POST['direccion']));

    if (empty($nombre)) {
        $msg  = 'El nombre es obligatorio.';
        $tipo = 'danger';
        $accion = $_POST['edit_id'] ? 'editar' : 'nuevo';
        $id = (int)$_POST['edit_id'];
    } elseif (!empty($_POST['edit_id'])) {
        $eid = (int)$_POST['edit_id'];
        $conn->query("UPDATE proveedores SET nombre='$nombre', contacto='$contacto', telefono='$telefono', email='$email', direccion='$direccion' WHERE id=$eid");
        $msg  = 'Proveedor actualizado.';
        $tipo = 'success';
        $accion = 'listar';
    } else {
        $conn->query("INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES ('$nombre','$contacto','$telefono','$email','$direccion')");
        $msg  = 'Proveedor creado.';
        $tipo = 'success';
        $accion = 'listar';
    }
}

if ($accion === 'eliminar' && $id > 0) {
    $uso = $conn->query("SELECT COUNT(*) AS n FROM productos WHERE proveedor_id=$id")->fetch_assoc()['n'];
    if ($uso > 0) {
        $msg  = "No se puede eliminar: hay $uso producto(s) de este proveedor.";
        $tipo = 'danger';
    } else {
        $conn->query("DELETE FROM proveedores WHERE id=$id");
        $msg  = 'Proveedor eliminado.';
        $tipo = 'warning';
    }
    $accion = 'listar';
}

$proveedor = null;
if ($accion === 'editar' && $id > 0) {
    $proveedor = $conn->query("SELECT * FROM proveedores WHERE id=$id")->fetch_assoc();
    if (!$proveedor) $accion = 'listar';
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0"><i class="bi bi-truck"></i> Proveedores</h2>
    <?php if ($accion === 'listar'): ?>
    <a href="proveedores.php?accion=nuevo" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo Proveedor</a>
    <?php else: ?>
    <a href="proveedores.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $tipo ?> alert-dismissible fade show">
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($accion === 'listar'): ?>
<?php $lista = $conn->query("SELECT pv.*, COUNT(p.id) AS total FROM proveedores pv LEFT JOIN productos p ON p.proveedor_id = pv.id GROUP BY pv.id ORDER BY pv.nombre"); ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Nombre</th><th>Contacto</th><th>Teléfono</th><th>Email</th><th class="text-center">Productos</th><th class="text-center">Acciones</th></tr></thead>
            <tbody>
                <?php if ($lista->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Sin proveedores.</td></tr>
                <?php else: ?>
                <?php while ($r = $lista->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted small"><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['nombre']) ?></td>
                    <td><?= htmlspecialchars($r['contacto']) ?></td>
                    <td><?= htmlspecialchars($r['telefono']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td class="text-center"><span class="badge bg-info text-dark"><?= $r['total'] ?></span></td>
                    <td class="text-center">
                        <a href="proveedores.php?accion=editar&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary btn-action"><i class="bi bi-pencil"></i></a>
                        <a href="proveedores.php?accion=eliminar&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger btn-action btn-delete"><i class="bi bi-trash"></i></a>
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
<div class="card" style="max-width:580px;">
    <div class="card-header bg-primary text-white"><?= $accion === 'editar' ? 'Editar Proveedor' : 'Nuevo Proveedor' ?></div>
    <div class="card-body">
        <form method="POST" action="proveedores.php">
            <?php if ($accion === 'editar'): ?>
            <input type="hidden" name="edit_id" value="<?= $proveedor['id'] ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre *</label>
                <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($proveedor['nombre'] ?? '') ?>">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contacto</label>
                    <input type="text" name="contacto" class="form-control" value="<?= htmlspecialchars($proveedor['contacto'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($proveedor['email'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($proveedor['direccion'] ?? '') ?>">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
                <a href="proveedores.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php $conn->close(); require_once 'includes/footer.php'; ?>
