<?php
session_start();
require '../bdconnect.php';

// Проверка авторизации
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

// Получаем статистику
$stats = [
        'orders_total' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'orders_new' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'новый'")->fetchColumn(),
        'orders_today' => $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
        'revenue_today' => $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
        'revenue_month' => $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE MONTH(created_at) = MONTH(CURDATE())")->fetchColumn(),
        'products_total' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'products_active' => $pdo->query("SELECT COUNT(*) FROM products WHERE is_available = 1")->fetchColumn(),
        'avg_check' => $pdo->query("SELECT COALESCE(AVG(total_price), 0) FROM orders")->fetchColumn(),
];

// Последние 5 заказов
$recent_orders = $pdo->query("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
    FROM orders o 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Панель управления | Морячок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../main.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Боковое меню -->
        <div class="col-md-3 col-lg-2 px-0 sidebar">
            <div class="p-3 text-white">
                <h4 class="text-center">
                    <i class="bi bi-water"></i> Морячок
                </h4>
                <p class="text-center small text-white-50">Админ-панель</p>
                <hr class="bg-secondary">
            </div>

            <nav class="nav flex-column">
                <a class="nav-link active" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i> Дашборд
                </a>
                <a class="nav-link" href="orders.php">
                    <i class="bi bi-bag me-2"></i> Заказы
                    <?php if ($stats['orders_new'] > 0): ?>
                        <span class="badge bg-danger float-end"><?= $stats['orders_new'] ?></span>
                    <?php endif; ?>
                </a>
                <a class="nav-link" href="products.php">
                    <i class="bi bi-cup me-2"></i> Меню
                </a>
                <a class="nav-link" href="settings.php">
                    <i class="bi bi-gear me-2"></i> Настройки
                </a>
                <hr class="bg-secondary mx-2">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="bi bi-shop me-2"></i> Открыть сайт
                </a>
                <a class="nav-link text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Выйти
                </a>
            </nav>

            <div class="position-absolute bottom-0 p-3 text-white-50 small">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_username']) ?>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-speedometer2 me-2"></i>Панель управления</h2>
                    <div>
                        <span class="text-muted">
                            <?= date('d.m.Y H:i') ?>
                        </span>
                    </div>
                </div>

                <!-- Карточки статистики -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-white-50">ВСЕГО ЗАКАЗОВ</h6>
                                        <h2><?= $stats['orders_total'] ?></h2>
                                    </div>
                                    <i class="bi bi-bag" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <small>Новых: <?= $stats['orders_new'] ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-white-50">ВЫРУЧКА ЗА МЕСЯЦ</h6>
                                        <h2><?= number_format($stats['revenue_month'], 0, ',', ' ') ?> ₽</h2>
                                    </div>
                                    <i class="bi bi-cash-stack" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <small>Сегодня: <?= number_format($stats['revenue_today'], 0, ',', ' ') ?> ₽</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-white-50">СРЕДНИЙ ЧЕК</h6>
                                        <h2><?= number_format($stats['avg_check'], 0, ',', ' ') ?> ₽</h2>
                                    </div>
                                    <i class="bi bi-receipt" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <small>За всё время</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-white-50">БЛЮД В МЕНЮ</h6>
                                        <h2><?= $stats['products_active'] ?> / <?= $stats['products_total'] ?></h2>
                                    </div>
                                    <i class="bi bi-egg-fried" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <small>Активных / Всего</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Последние заказы -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock-history me-2"></i>Последние заказы
                                </h5>
                                <a href="orders.php" class="btn btn-sm btn-outline-primary">
                                    Все заказы <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>№</th>
                                            <th>Клиент</th>
                                            <th>Сумма</th>
                                            <th>Статус</th>
                                            <th>Время</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <a href="order_view.php?id=<?= $order['id'] ?>">
                                                        #<?= $order['id'] ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($order['customer_name']) ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($order['customer_phone']) ?></small>
                                                </td>
                                                <td><?= $order['total_price'] ?> ₽</td>
                                                <td>
                                                    <?php
                                                    $statuses = [
                                                            'new' => ['Новый', 'danger'],
                                                            'preparing' => ['Готовится', 'warning'],
                                                            'ready' => ['Готов', 'success'],
                                                            'completed' => ['Выдан', 'secondary'],
                                                            'cancelled' => ['Отменён', 'dark'],
                                                    ];
                                                    $s = $statuses[$order['status']] ?? ['Неизвестно', 'secondary'];
                                                    ?>
                                                    <span class="badge bg-<?= $s[1] ?>"><?= $s[0] ?></span>
                                                </td>
                                                <td>
                                                    <?= date('H:i', strtotime($order['created_at'])) ?>
                                                    <br><small class="text-muted"><?= date('d.m', strtotime($order['created_at'])) ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php if (empty($recent_orders)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                    <p class="mt-2">Пока нет заказов</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Быстрые действия
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="orders.php?status=new" class="btn btn-outline-danger">
                                        <i class="bi bi-bell"></i> Новые заказы
                                        <?php if ($stats['orders_new'] > 0): ?>
                                            <span class="badge bg-danger"><?= $stats['orders_new'] ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a href="products.php?action=add" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Добавить блюдо
                                    </a>
                                    <a href="orders.php?date=<?= date('Y-m-d') ?>" class="btn btn-outline-info">
                                        <i class="bi bi-calendar-check"></i> Заказы за сегодня
                                    </a>
                                </div>

                                <hr>

                                <h6 class="text-muted">Статистика сегодня</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-bag-check text-success"></i>
                                        Заказов: <strong><?= $stats['orders_today'] ?></strong>
                                    </li>
                                    <li>
                                        <i class="bi bi-cash text-success"></i>
                                        Выручка: <strong><?= number_format($stats['revenue_today'], 0, ',', ' ') ?> ₽</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>