<?php
require 'config.php';

$id           = !empty($_POST['id']) ? (int)$_POST['id'] : null;
$nombre       = trim($_POST['nombre'] ?? '');
$descripcion  = trim($_POST['descripcion'] ?? '');
$rubro_id     = ($_POST['rubro_id'] !== '') ? (int)$_POST['rubro_id'] : null;
$precio_venta = ($_POST['precio_venta'] !== '') ? (float)str_replace(',', '.', $_POST['precio_venta']) : null;

if ($nombre === '') {
    exit('El nombre es obligatorio');
}

if ($id) {
    $sql = "UPDATE productos
            SET nombre = ?, descripcion = ?, rubro_id = ?, precio_venta = ?
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $descripcion, $rubro_id, $precio_venta, $id]);
} else {
    $sql = "INSERT INTO productos (nombre, descripcion, rubro_id, precio_venta)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $descripcion, $rubro_id, $precio_venta]);
}

header('Location: productos_list.php');
exit;
