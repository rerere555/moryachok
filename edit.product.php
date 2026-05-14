<?php
require "bdconnect.php";
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();


$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();

?>

<form method="POST" action="update_product.php">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css?v=<?= time() ?>">
    <div class="body-registr ">
        <div class="registr">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    
    <label>Имя:</label><br>
    <input type="text" name="name" value="<?= $product['name'] ?>" required><br>

    <label>описание:</label><br>
    <input type="text" name="description" value="<?= $product['description'] ?>" required><br>
    
    <label>категория:</label><br>
    <select name="category_id" required><br>
        <?php foreach ($categories as $categorie): ?>
            <option value="<?= $categorie['id'] ?>" 
                <?= $categorie['id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= $categorie['name'] ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    
    <label>Цена:</label><br>
    <input type="text" name="price" value="<?= $product['price'] ?>" required><br>
    
    <button type="submit">Сохранить</button>
</div>
        </div>
</form>

