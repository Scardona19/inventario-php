<?php
require_once 'includes/config.php';
$conn = conectar();

$msg    = '';
$tipo   = '';
$accion = $_GET['accion'] ?? 'listar';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = trim($conn->real_escape_string($_POST['nombre']));
    $descripcion = trim($conn->real_escape_string($_POST['descripcion']));

    if (empty($nombre)) {
        $msg  = 'El nombre es obligatorio.';
        $tipo = 'danger';
        $accion = $_POST['edit_id'] ? 'editar' : 'nuevo';
        $id = (int)$_POST['edit_id'];
    } elseif (!empty($_POST['edit_id'])) {
        $eid = (int)$_POST['edit_id'];
        $conn->query("UPDATE categorias SET nombre='$nombre', descripcion='$descripcion' WHERE id=$eid");
        $msg  = 'Categoría actualizada.';
        $tipo = 'success';
        $accion = 'listar';
    } else {
        $conn->query("INSERT INTO categorias (nombre, descripcion) VALUES ('$nombre', '$descripcion')");
        $msg  = 'Categoría creada.';
        $tipo = 'success';
        $accion = 'listar';
    }
}

if ($accion === 'eliminar' && $id > 0) {
    $uso = $conn->query("SELECT COUNT(*) AS n FROM productos WHERE categoria_id=$id")->fetch_assoc()['n'];
    if ($uso > 0) {
        $msg  = "No se puede eliminar: hay $uso producto(s) en esta categoría.";
        $tipo = 'danger';
    } else {
        $conn->query("DELETE FROM categorias WHERE id=$id");
        $msg  = 'Categoría eliminada.';
        $tipo = 'warning';
    }
    $accion = 'listar';
}

$categoria = null;
if ($accion === 'editar' && $id > 0) {
    $categoria = $conn->query("SELECT * FROM categorias WHERE id=$id")->fetch_assoc();
    if (!$categoria) $accion = 'listar';
}

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0"><i class="bi bi-tags"></i> Categorías</h2>
    <?php if ($accion === 'listar'): ?>
    <a href="categorias.php?accion=nuevo" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nueva Categoría
    </a>
    <?php else: ?>
    <a href="categorias.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $tipo ?> alert-dismissible fade show">
    <?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($accion === 'listar'): ?>
<?php $lista = $conn->query("SELECT c.*, COUNT(p.id) AS total_productos FROM categorias c LEFT JOIN productos p ON p.categoria_id = c.id GROUP BY c.id ORDER BY c.nombre"); ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Nombre</th><th>Descripción</th><th class="text-center">Productos</th><th class="text-center">Acciones</th></tr></thead>
            <tbody>
                <?php if ($lista->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Sin categorías.</td></tr>
                <?php else: ?>
                <?php while ($r = $lista->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted small"><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['nombre']) ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($r['descripcion']) ?></td>
                    <td class="text-center"><span class="badge bg-primary"><?= $r['total_productos'] ?></span></td>
                    <td class="text-center">
                        <a href="categorias.php?accion=editar&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary btn-action"><i class="bi bi-pencil"></i></a>
                        <a href="categorias.php?accion=eliminar&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger btn-action btn-delete"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<div class="card" style="max-width:500px;">
    <div class="card-header bg-primary text-white">
        <?= $accion === 'editar' ? 'Editar Categoría' : 'Nueva Categoría' ?>
    </div>
    <div class="card-body">
        <form method="POST" action="categorias.php">
            <?php if ($accion === 'editar'): ?>
            <input type="hidden" name="edit_id" value="<?= $categoria['id'] ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre *</label>
                <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($categoria['nombre'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
                <a href="categorias.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php $conn->close(); require_once 'includes/footer.php'; ?>
