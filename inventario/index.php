<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$conn = conectar();

// Totales para el dashboard
$total_productos  = $conn->query("SELECT COUNT(*) AS n FROM productos")->fetch_assoc()['n'];
$total_categorias = $conn->query("SELECT COUNT(*) AS n FROM categorias")->fetch_assoc()['n'];
$total_proveedores = $conn->query("SELECT COUNT(*) AS n FROM proveedores")->fetch_assoc()['n'];
$stock_bajo = $conn->query("SELECT COUNT(*) AS n FROM productos WHERE stock <= stock_minimo")->fetch_assoc()['n'];

// Productos con stock bajo
$bajos = $conn->query(
    "SELECT p.nombre, p.stock, p.stock_minimo, c.nombre AS categoria
     FROM productos p
     JOIN categorias c ON p.categoria_id = c.id
     WHERE p.stock <= p.stock_minimo
     ORDER BY p.stock ASC
     LIMIT 10"
);

// Últimos movimientos
$ultimos = $conn->query(
    "SELECT m.tipo, m.cantidad, m.observacion, m.fecha, p.nombre AS producto
     FROM movimientos m
     JOIN productos p ON m.producto_id = p.id
     ORDER BY m.fecha DESC
     LIMIT 8"
);
?>

<h2 class="page-title mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>

<!-- Tarjetas de resumen -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card text-white bg-primary">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-2 fw-bold"><?= $total_productos ?></div>
                    <div>Productos</div>
                </div>
                <i class="bi bi-box-seam stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card text-white bg-success">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-2 fw-bold"><?= $total_categorias ?></div>
                    <div>Categorías</div>
                </div>
                <i class="bi bi-tags stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card text-white bg-info">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-2 fw-bold"><?= $total_proveedores ?></div>
                    <div>Proveedores</div>
                </div>
                <i class="bi bi-truck stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card text-white bg-danger">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-2 fw-bold"><?= $stock_bajo ?></div>
                    <div>Stock Bajo</div>
                </div>
                <i class="bi bi-exclamation-triangle stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Stock bajo -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle"></i> Productos con Stock Bajo
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($bajos->num_rows === 0): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Sin alertas 🎉</td></tr>
                        <?php else: ?>
                            <?php while ($r = $bajos->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['nombre']) ?></td>
                                <td><?= htmlspecialchars($r['categoria']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-danger"><?= $r['stock'] ?></span>
                                </td>
                                <td class="text-center"><?= $r['stock_minimo'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Últimos movimientos -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-arrow-left-right"></i> Últimos Movimientos
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th class="text-center">Cant.</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = $ultimos->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['producto']) ?></td>
                            <td>
                                <?php if ($r['tipo'] === 'entrada'): ?>
                                    <span class="badge bg-success">Entrada</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Salida</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $r['cantidad'] ?></td>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($r['fecha'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/footer.php';
?>
