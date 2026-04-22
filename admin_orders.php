<?php
session_start();
require '../bdconnect.php';

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

// Обработка изменения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header('Location: orders.php?success=1');
    exit;
}

// Фильтры
$status = $_GET['status'] ?? '';
$date = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT o.*, 
        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
        FROM orders o WHERE 1=1";
$params = [];

if ($status) {
    $sql .= " AND o.status = ?";
    $params[] = $status;
}

if ($date) {
    $sql .= " AND DATE(o.created_at) = ?";
    $params[] = $date;
}

if ($search) {
    $sql .= " AND (o.customer_name LIKE ? OR o.customer_phone LIKE ? OR o.id = ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = $search;
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Управление заказами | Морячок</title>
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
                <a class="nav-link active" href="orders.php">
                    <i class="bi bi-bag me-2"></i> Заказы
                </a>
                <a class="nav-link" href="products.php">
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
                <h2 class="mb-4"><i class="bi bi-bag me-2"></i>Управление заказами</h2>

                <!-- Фильтры -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Статус</label>
                                <select name="status" class="form-select">
                                    <option value="">Все</option>
                                    <option value="new" <?= $status == 'новый' ? 'selected' : '' ?>>Новые</option>
                                    <option value="preparing" <?= $status == 'готовится' ? 'selected' : '' ?>>Готовятся</option>
                                    <option value="ready" <?= $status == 'заказ готов' ? 'selected' : '' ?>>Готовы</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Дата</label>
                                <input type="date" name="date" class="form-control" value="<?= $date ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Поиск</label>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Имя, телефон или № заказа" value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Найти
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Таблица заказов -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>№ заказа</th>
                                    <th>Клиент</th>
                                    <th>Телефон</th>
                                    <th>Позиций</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?= $order['id'] ?></strong></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                        <td><?= $order['items_count'] ?></td>
                                        <td><strong><?= $order['total_price'] ?> ₽</strong></td>
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
                                            <div class="dropdown">
                                                <span class="badge bg-<?= $s[1] ?> dropdown-toggle status-badge"
                                                      data-bs-toggle="dropdown">
                                                    <?= $s[0] ?>
                                                </span>
                                                <ul class="dropdown-menu">
                                                    <?php foreach ($statuses as $key => $val): ?>
                                                        <li>
                                                            <form method="POST" class="dropdown-item p-0">
                                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                                <input type="hidden" name="status" value="<?= $key ?>">
                                                                <button type="submit" class="dropdown-item">
                                                                    <?= $val[0] ?>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                        </td>
                                        <td>
                                            <a href="order_view.php?id=<?= $order['id'] ?>"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-3">Заказов не найдено</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
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
