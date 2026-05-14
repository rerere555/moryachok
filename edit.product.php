<?php
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$products = $stmt->fetch();


$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();
?>

<form method="POST" action="update_product.php">
    <input type="hidden" name="id" value="<?= $products['id'] ?>">
    
    <label>Имя:</label>
    <input type="text" name="name" value="<?= $products['name'] ?>" required>

    <label>описание:</label>
    <input type="text" name="description" value="<?= $products['description'] ?>" required>
    
    <label>категория:</label>
    <select name="category_id" required>
        <?php foreach ($categories as $categorie): ?>
            <option value="<?= $categorie['id'] ?>" 
                <?= $categorie['id'] == $products['category_id'] ? 'selected' : '' ?>>
                <?= $categorie['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label>Имя:</label>
    <input type="text" name="price" value="<?= $products['price'] ?>" required>
    
    <button type="submit">Сохранить</button>
</form>
