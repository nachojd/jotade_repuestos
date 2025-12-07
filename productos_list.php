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
    <title>Catálogo</title>
    <!-- CSS propio -->
    <link rel="stylesheet" href="css/catalogo.css">
</head>

<body>
    <div class="container">
        <div class="top-bar">
            <h1>Catálogo</h1>
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
                        $codigoBase = $p['cod_scanner'] !== null && $p['cod_scanner'] !== ''
                            ? $p['cod_scanner']
                            : $p['id'];
                        $codigo = 'JD-' . str_pad((string) $codigoBase, 5, '0', STR_PAD_LEFT);
                        $precio = $p['precio_venta'] !== null ? number_format($p['precio_venta'], 2, ',', '.') : '-';

                        
                        ?>
                        <tr>
                            <td class="codigo"><?php echo $codigo; ?></td>
                            <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($p['rubro_nombre'] ?? '-'); ?></td>
                            <td class="precio">$ <?php echo $precio; ?></td>
                            <td class="acciones">
                                <a href="producto_form.php?id=<?php echo $p['id']; ?>">Editar</a>
                                <a href="producto_eliminar.php?id=<?php echo $p['id']; ?>"
                                    onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>