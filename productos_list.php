<?php
require 'config.php';

$busqueda = $_GET['q'] ?? '';

if ($busqueda !== '') {
    $stmt = $pdo->prepare("
        SELECT p.*, r.nombre AS rubro_nombre
        FROM productos p
        LEFT JOIN rubros r ON r.id = p.rubro_id
        WHERE p.nombre LIKE :q
        ORDER BY p.id DESC
    ");
    $stmt->execute([':q' => "%$busqueda%"]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, r.nombre AS rubro_nombre
        FROM productos p
        LEFT JOIN rubros r ON r.id = p.rubro_id
        ORDER BY p.id DESC
    ");
}
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>JD Repuestos - Catálogo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
        }

        .container {
            width: 1100px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 4px;
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }

        th {
            background: #eee;
            text-align: left;
        }

        .codigo {
            font-weight: bold;
        }

        .precio {
            text-align: right;
            white-space: nowrap;
        }

        .stock-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .stock-ok {
            background: #2ecc71;
        }

        .stock-low {
            background: #f1c40f;
        }

        .stock-none {
            background: #e74c3c;
        }

        .acciones a {
            display: inline-block;
            padding: 3px 6px;
            font-size: 12px;
            border-radius: 3px;
            text-decoration: none;
            border: 1px solid #555;
            color: #333;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        form.busqueda input[type="text"] {
            padding: 4px 6px;
            width: 250px;
        }

        form.busqueda button,
        .btn {
            padding: 4px 8px;
            font-size: 13px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="top-bar">
            <h1>JD Repuestos - Catálogo</h1>
            <a class="btn" href="producto_form.php">➕ Nuevo producto</a>
        </div>

        <form class="busqueda" method="get" action="productos_list.php">
            <label>Buscar por nombre:
                <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda); ?>">
            </label>
            <button type="submit">Buscar</button>
            <?php if ($busqueda !== ''): ?>
                <a href="productos_list.php">Limpiar</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Rubro</th>
                    <th>Precio venta</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$productos): ?>
                    <tr>
                        <td colspan="6">No hay productos cargados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                        <?php
                        $codigo = 'JD-' . str_pad($p['id'], 5, '0', STR_PAD_LEFT); // JD-00001
                        $precio = $p['precio_venta'] !== null ? number_format($p['precio_venta'], 2, ',', '.') : '-';

                        if ($p['stock'] > 10) {
                            $stockClass = 'stock-ok';
                            $stockText  = $p['stock'] . ' u.';
                        } elseif ($p['stock'] > 0) {
                            $stockClass = 'stock-low';
                            $stockText  = $p['stock'] . ' u.';
                        } else {
                            $stockClass = 'stock-none';
                            $stockText  = 'Sin stock';
                        }
                        ?>
                        <tr>
                            <td class="codigo"><?php echo $codigo; ?></td>
                            <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($p['rubro_nombre'] ?? '-'); ?></td>
                            <td class="precio">$ <?php echo $precio; ?></td>
                            <td>
                                <span class="stock-dot <?php echo $stockClass; ?>"></span>
                                <?php echo $stockText; ?>
                            </td>
                            <td class="acciones">
                                <a href="producto_form.php?id=<?php echo $p['id']; ?>">Editar</a>
                                <a href="producto_eliminar.php?id=<?php echo $p['id']; ?>"
                                    onclick="return confirm('¿Eliminar este producto?');">
                                    Borrar
                                </a>
                                <!-- Más adelante: Ver proveedores -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>