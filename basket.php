<?php
session_start();
require 'bdconnect.php';

$basket_items = [];
$total_sum = 0;

if (!empty($_SESSION['basket'])) {
    $ids = array_keys($_SESSION['basket']);

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $basket_items = $stmt->fetchAll(); 
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
</head>
<body>
<div class="container">
    <h1>Ваша корзина</h1>

    <?php if (empty($basket_items)): ?>
        <p>Корзина пуста. <a href="index.registr.php">Вернуться в меню</a></p>
    <?php else: ?>
        <table class="table table-bordered">
            <tr>
                <th>Название</th>
                <th>Цена</th>
                <th>Кол-во</th>
                <th>Сумма</th>
            </tr>
            <?php foreach ($basket_items as $item):
                $qty = $_SESSION['basket'][$item['id']];
                $sum = $item['price'] * $qty;
                $total_sum += $sum;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['price'] ?> руб.</td>
                    <td><?= $qty ?></td>
                    <td><?= $sum ?> руб.</td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Итого: <?= $total_sum ?> руб.</h3>

        <a href="clear_basket.php" class="btn btn-danger">Очистить корзину</a>
        <a href="checkout.php" class="btn btn-success">Оформить заказ</a>
    <?php endif; ?>
</div>
</body>
</html>
