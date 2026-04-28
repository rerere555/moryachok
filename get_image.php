<?php
require 'bdconnect.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product && !empty($product['image'])) {
        // Определяем тип изображения
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($product['image']);

        header("Content-Type: " . $mime_type);
        echo $product['image'];
        exit;
    }
}

// Если изображения нет - отдаём заглушку
header("Content-Type: image/svg+xml");
echo '<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="300" fill="#f0f0f0"/>
    <text x="200" y="150" font-family="Arial" font-size="20" fill="#999" text-anchor="middle">Нет фото</text>
</svg>';
?>