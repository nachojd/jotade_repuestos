<?php
require 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$producto = [
    'id'           => null,
    'nombre'       => '',
    'descripcion'  => '',
    'rubro_id'     => null,
    'precio_venta' => '',
    'stock'        => 0,
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();
    if (!$producto) {
        exit('Producto no encontrado');
    }
}

$rubros = $pdo->query("SELECT id, nombre FROM rubros ORDER BY nombre ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?php echo $id ? 'Editar producto' : 'Nuevo producto'; ?> - JD</title>
    <link rel="stylesheet" href="css/catalogo.css">
</head>

<body>
    <div class="container">
        <h1><?php echo $id ? 'Editar producto' : 'Nuevo producto'; ?></h1>

        <form method="post" action="guardar_producto.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">

            <label>
                Nombre
                <input type="text" name="nombre" required
                    value="<?php echo htmlspecialchars($producto['nombre']); ?>">
            </label>

            <label>
                Rubro
                <select name="rubro_id">
                    <option value="">-- Sin rubro --</option>
                    <?php foreach ($rubros as $r): ?>
                        <option value="<?php echo $r['id']; ?>"
                            <?php echo ($producto['rubro_id'] == $r['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($r['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Descripción
                <textarea name="descripcion"><?php
                                                echo htmlspecialchars($producto['descripcion']);
                                                ?></textarea>
            </label>

            <label>
                Precio de venta
                <input type="text" name="precio_venta"
                    value="<?php echo htmlspecialchars($producto['precio_venta']); ?>">
            </label>

            <label>
                Stock
                <input type="text" name="stock"
                    value="<?php echo htmlspecialchars($producto['stock']); ?>">
            </label>

            <div class="acciones">
                <a href="producto_eliminar.php?id=<?php echo $p['id']; ?>"
                    onclick="return confirm('¿Eliminar este producto?');">
                    Borrar
                </a>
                <button type="submit">Guardar</button>
                <a class="btn" href="productos_list.php">Volver</a>
            </div>
        </form>
    </div>
</body>

</html>