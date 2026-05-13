<?php
session_start();
require 'bdconnect.php';

$stmt = $pdo->prepare("SELECT id, name, description, price, image FROM products ");
$stmt->execute();
$products = $stmt->fetchAll();

$basket_count = isset($_SESSION['basket']) ? array_sum($_SESSION['basket']) : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Моречечек</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css?v=<?= time() ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-shop"></i>  Морячок
        </a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house"></i> Главная
                    </a>
                </li>
                <li class="nav-item">
                    
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>
                    <i class="bi bi-basket3-fill"></i> Добро пожаловать в Морячок!
                </h1>
                <p class="lead">Свежие продукты, домашняя кухня и уютная атмосфера.</p>
                <p><i class="bi bi-clock"></i> Работаем ежедневно с 08:00 до 15:00</p>
                <p><i class="bi bi-telephone"></i> +7 (996) 471-83-62</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="info-card">
                <a href="add_product.php">добавить товар</a>
                </div>
            </div>
        </div>
    </div>

    <h2 class="section-title">
        <i class="bi bi-menu-button-wide"></i> Наше меню
    </h2>
    <?php if (empty($products)): ?>
            <h3 class="text-muted">Меню пока пусто</h3>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" >
                    <div class="product-image-wrapper">
                        <?php if (!empty($product['image'])): ?>
                            <img src="get_image.php?id=<?= $product['id'] ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="product-image">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="bi bi-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-content">
                        <h3 class="product-title">
                            <?= htmlspecialchars($product['name']) ?>
                        </h3>
                        <div class="product-description">
                            <?= htmlspecialchars($product['description'] ) ?>
                        </div>
                        <div class="product-price">
                            <?= number_format($product['price']) ?> ₽
                        </div>
                        <a href="delete.product.php">удалить</a>
                        <a href="edit.product.php">изменить</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="info-grid">
        <div class="info-card">
            <div class="info-icon" style="color: #3498db;">
                <i class="bi bi-alarm-fill"></i>
            </div>
            <h5>Быстро готовим</h5>
            <p class="text-muted mb-0">Приготовим за 10-15 минут</p>
        </div>
        <div class="info-card">
            <div class="info-icon" style="color: #f1c40f;">
                <i class="bi bi-star-fill"></i>
            </div>
            <h5>Качество продуктов</h5>
            <p class="text-muted mb-0">Только свежие ингредиенты</p>
        </div>
        <div class="info-card">
            <div class="info-icon" style="color: #27ae60;">
                <i class="bi bi-shield-check"></i>
            </div>
            <h5>Гарантия вкуса</h5>
            <p class="text-muted mb-0">Не вернём деньги, если не понравится</p>
            </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="bi bi-water"></i> Морячок</h5>
                <p class="footer-text">Морячок — место, где вкусно и уютно!</p>
            </div>
            <div class="col-md-3">
                <h6>Контакты</h6>
                <p class="footer-text">
                    <i class="bi bi-geo-alt"></i> ул. Богдана хмельницкого, 8В<br>
                    <i class="bi bi-telephone"></i> +7 (996) 471-83-62<br>
                    <i class="bi bi-envelope"></i> info@moryachok.ru
                </p>
            </div>
            <div class="col-md-3">
                <h6>Режим работы</h6>
                <p class="footer-text">
                    Пн-Сб: 08:00 - 15:00<br>
                    Без перерывов
                </p>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="text-center footer-copyright">
            <small>©  Морячок. Все права защищены.</small>
        </div>
    </div>
</footer>
</body>
</html>