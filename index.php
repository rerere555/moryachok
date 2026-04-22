<?php
session_start();
require 'bdconnect.php';

// Получаем все доступные товары из базы
$stmt = $pdo->prepare("SELECT * FROM products ");
$stmt->execute();
$products = $stmt->fetchAll();

// Проверяем, залогинен ли админ
$is_admin = isset($_SESSION['admin']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Меню морячка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .product-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .product-card .description {
            color: #7f8c8d;
            flex-grow: 1;
            margin-bottom: 15px;
        }
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 15px;
        }
        .btn-add {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background 0.3s;
            width: 100%;
        }
        .btn-add:hover {
            background: #2980b9;
            color: white;
        }
        .cart-badge {
            position: relative;
            top: -8px;
            right: -5px;
        }
        .admin-btn {
            border: 2px solid #ffc107;
            color: #ffc107;
            transition: all 0.3s;
        }
        .admin-btn:hover {
            background: #ffc107;
            color: #000;
        }
        .welcome-section {
            background: rgba(255,255,255,0.9);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .empty-menu {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 15px;
        }
    </style>
</head>
<body>
<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-water"></i> Морячок
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house"></i> Главная
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link position-relative" href="basket.php">
                        <i class="bi bi-cart3"></i> Корзина
                        <?php
                        $basket_count = isset($_SESSION['basket']) ? array_sum($_SESSION['basket']) : 0;
                        if ($basket_count > 0):
                            ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                                <?= $basket_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if ($is_admin): ?>
                    <li class="nav-item me-3">
                        <a class="nav-link btn btn-outline-warning admin-btn" href="admin/admin_dashboard.php">
                            <i class="bi bi-shield-lock"></i> Админ-панель
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="admin/admin_logout.php">
                            <i class="bi bi-box-arrow-right"></i> Выйти
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/admin_index.php">
                            <i class="bi bi-key"></i> Вход для персонала
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Основной контент -->
<div class="container my-4">
    <!-- Приветственная секция -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 mb-3">
                Добро пожаловать в Морячок!
                </h1>
                <p class="lead">Свежие морепродукты, домашняя кухня и уютная атмосфера.</p>
                <p><i class="bi bi-clock"></i> Работаем ежедневно с 8:00 до 15:00</p>
                <p><i class="bi bi-telephone"></i> +7 (996) 471-83-62</p>
            </div>
            <div class="col-md-4 text-center">
                <?php if ($basket_count > 0): ?>
                    <a href="basket.php" class="btn btn-success btn-lg">
                        <i class="bi bi-cart-check"></i> Перейти к заказу
                        <span class="badge bg-light text-dark ms-2"><?= $basket_count ?></span>
                    </a>
                <?php else: ?>
                    <div class="text-muted">
                        <i class="bi bi-cart" style="font-size: 3rem;"></i>
                        <p class="mt-2">Корзина пуста</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Меню -->
    <h2 class="mb-4">
        <i class="bi bi-menu-button-wide"></i> Наше меню
        <?php if ($is_admin): ?>
            <a href="admin/admin_products.php" class="btn btn-sm btn-outline-primary ms-3">
                <i class="bi bi-pencil"></i> Управлять меню
            </a>
        <?php endif; ?>
    </h2>

    <?php if (empty($products)): ?>
        <div class="empty-menu">
            <i class="bi bi-cup" style="font-size: 4rem; color: #95a5a6;"></i>
            <h3 class="mt-3 text-muted">Меню пока пусто</h3>
            <?php if ($is_admin): ?>
                <p class="text-muted">Добавьте блюда в админ-панели</p>
                <a href="admin/admin_products.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Добавить блюдо
                </a>
            <?php else: ?>
                <p class="text-muted">Загляните позже, мы обновляем меню</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $item): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="product-card">
                        <h3>
                            <!-- <i class="bi bi-egg-fried"></i> -->
                            <?= htmlspecialchars($item['name']) ?>
                        </h3>

                        <div class="description">
                            <?= htmlspecialchars($item['description'] ?? 'Вкуснейшее блюдо от нашего шеф-повара') ?>
                        </div>

                        <div class="price">
                            <?= number_format($item['price'], 0, ',', ' ') ?> ₽
                        </div>

                        <form action="add_basket.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn btn-add">
                                <i class="bi bi-cart-plus"></i> В корзину
                            </button>
                        </form>

                        <?php if ($is_admin): ?>
                            <div class="mt-2 text-center">
                                <a href="admin/admin_products.php?edit=<?= $item['id'] ?>" class="text-muted small">
                                    <i class="bi bi-pencil-square"></i> Редактировать
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Информация о доставке -->
    <div class="row mt-5">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-alarm-fill" style="font-size: 2.5rem; color: #3498db;"></i>
                    <h5 class="mt-3">Быстрая готовим</h5>
                    <p class="text-muted"> за 10-15 минут</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-star-fill" style="font-size: 2.5rem; color: #f1c40f;"></i>
                    <h5 class="mt-3">Качество продуктов</h5>
                    <p class="text-muted">Только свежие ингредиенты</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check" style="font-size: 2.5rem; color: #27ae60;"></i>
                    <h5 class="mt-3">Гарантия вкуса</h5>
                    <p class="text-muted">Вернём деньги, если не понравится</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Футер -->
<footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="bi bi-water"></i> Морячок</h5>
                <p class="text-muted">Кафе-морячок — место, где вкусно и уютно!</p>
            </div>
            <div class="col-md-3">
                <h6>Контакты</h6>
                <p class="text-muted">
                    <i class="bi bi-geo-alt"></i> улица Богдана Хмельницкого, 8В<br>
                    <i class="bi bi-telephone"></i> +7 (999) 123-45-67<br>
                    <i class="bi bi-envelope"></i> info@moryachok.ru
                </p>
            </div>
            <div class="col-md-3">
                <h6>Режим работы</h6>
                <p class="text-muted">
                    Пн-Вс: 8:00 - 15:00<br>
                    Без выходных
                </p>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="text-center text-muted">
            <small>© <?= date('Y') ?> Морячок. Все права защищены.</small>
            <?php if ($is_admin): ?>
                <br>
                <a href="admin/dashboard.php" class="text-warning small">
                    <i class="bi bi-shield-lock"></i> Панель управления
                </a>
            <?php endif; ?>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Небольшая анимация для кнопок добавления в корзину
    document.querySelectorAll('form[action="add_basket.php"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Добавлено!';
            btn.style.background = '#27ae60';

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '#3498db';
            }, 1000);
        });
    });

    // Плавное появление карточек при загрузке
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.product-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
</body>
</html>