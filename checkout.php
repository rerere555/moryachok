<?php
session_start();
require 'bdconnect.php';

// Если корзина пуста, отправляем в меню
if (empty($_SESSION['basket'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');

    if ($name && $phone) {
        try {
            $pdo->beginTransaction();

            // 1. Считаем общую сумму
            $total_price = 0;
            $ids = array_keys($_SESSION['basket']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $products = $stmt->fetchAll();

            foreach ($products as $p) {
                $total_price += $p['price'] * $_SESSION['basket'][$p['id']];
            }

            // 2. Создаем заказ
            $sql = "INSERT INTO orders (customer_name, customer_phone, total_price, status, created_at) 
                    VALUES (:name, :phone, :total, 'новый', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                    ':name' => $name,
                    ':phone' => $phone,
                    ':total' => $total_price
            ]);

            $orderId = $pdo->lastInsertId();

            // 3. Вставляем товары
            $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                        VALUES (:order_id, :product_id, :quantity, :price_at_purchase)";
            $stmtItem = $pdo->prepare($sqlItem);

            foreach ($products as $p) {
                $stmtItem->execute([
                        ':order_id' => $orderId,
                        ':product_id' => $p['id'],
                        ':quantity' => $_SESSION['basket'][$p['id']],
                        ':price_at_purchase' => $p['price']
                ]);
            }

            $pdo->commit();
            unset($_SESSION['basket']); // Очищаем корзину
            $success = true;
            $orderMessage = "Заказ №{$orderId} успешно оформлен! Ждем вас в морячке.";

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Ошибка при оформлении: " . $e->getMessage();
        }
    } else {
        $error = "Пожалуйста, заполните все поля.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Завершение заказа</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <h2><?= $orderMessage ?></h2>
            <a href="index.php" class="btn btn-primary">Вернуться в меню</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Ваше имя:</label>
                <input type="text" name="customer_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Телефон:</label>
                <input type="text" name="customer_phone" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Подтвердить заказ</button>
            <a href="basket.php" class="btn btn-secondary">Назад в корзину</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

