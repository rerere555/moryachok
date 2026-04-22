<?php
session_start();
require '../bdconnect.php';

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['id'] ?? 0;

// Получаем информацию о заказе
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Получаем товары в заказе
$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ #<?= $order['id'] ?> | Морячок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Заказ #<?= $order['id'] ?></h4>
                <a href="orders.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Информация о клиенте</h5>
                    <p><strong>Имя:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                    <p><strong>Дата заказа:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Статус заказа</h5>
                    <form method="POST" action="orders.php">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="new" <?= $order['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                            <option value="preparing" <?= $order['status'] == 'preparing' ? 'selected' : '' ?>>Готовится</option>
                            <option value="ready" <?= $order['status'] == 'ready' ? 'selected' : '' ?>>Готов к выдаче</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Выдан</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Отменён</option>
                        </select>
                    </form>
                </div>
            </div>

            <h5>Состав заказа</h5>
            <table class="table">
                <thead>
                <tr>
                    <th>Блюдо</th>
                    <th>Цена</th>
                    <th>Кол-во</th>
                    <th>Сумма</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['price_at_purchase'] ?> ₽</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['price_at_purchase'] * $item['quantity'] ?> ₽</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Итого:</th>
                    <th><?= $order['total_price'] ?> ₽</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>
