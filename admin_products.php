<?php
session_start();
require '../bdconnect.php';

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

// Удаление товара
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: products.php?deleted=1');
    exit;
}

// Переключение доступности
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE products SET is_available = NOT is_available WHERE id = ?");
    $stmt->execute([$_GET['toggle']]);
    header('Location: products.php');
    exit;
}

// Добавление/редактирование товара
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $price]);
    }

    header('Location: products.php?saved=1');
    exit;
}

// Получение товаров
$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();

// Редактирование товара
$edit_product = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Управление меню | Морячок</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../main.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Боковое меню -->
        <div class="col-md-3 col-lg-2 px-0 sidebar">
            <div class="p-3 text-white">
                <h4 class="text-center"><i class="bi bi-water"></i> Морячок</h4>
                <hr class="bg-secondary">
            </div>
            <nav class="nav flex-column">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i> Дашборд
                </a>
                <a class="nav-link" href="orders.php">
                    <i class="bi bi-bag me-2"></i> Заказы
                </a>
                <a class="nav-link active" href="products.php">
                    <i class="bi bi-cup me-2"></i> Меню
                </a>
                <a class="nav-link text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Выйти
                </a>
            </nav>
        </div>

        <!-- Основной контент -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-cup me-2"></i>Управление меню</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i class="bi bi-plus-circle"></i> Добавить блюдо
                    </button>
                </div>

                <!-- Список товаров -->
                <div class="row">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card product-card-admin <?= !$product['is_available'] ? 'product-inactive' : '' ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($product['name']) ?>
                                        <?php if (!$product['is_available']): ?>
                                            <span class="badge bg-secondary">Недоступно</span>
                                        <?php endif; ?>
                                    </h5>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="?edit=<?= $product['id'] ?>">
                                                    <i class="bi bi-pencil"></i> Редактировать
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="?toggle=<?= $product['id'] ?>">
                                                    <i class="bi bi-eye<?= $product['is_available'] ? '-slash' : '' ?>"></i>
                                                    <?= $product['is_available'] ? 'Скрыть' : 'Показать' ?>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="?delete=<?= $product['id'] ?>"
                                                   onclick="return confirm('Удалить блюдо?')">
                                                    <i class="bi bi-trash"></i> Удалить
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars($product['description'] ?? 'Без описания') ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="text-success mb-0"><?= $product['price'] ?> ₽</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
