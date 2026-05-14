<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="add_product.php">
    <label>Имя товара:</label><br>
    <input type="text" name="name" required><br>
    
    <label>описание товара:</label><br>
    <input type="text" name="description" required><br>

    <label>категория:</label><br>
    <select name="category_id" required><br>
        <option value="">Выберите категорию</option>
        <?php
        require "bdconnect.php";
        $stmt = $pdo->query("SELECT id, name FROM categories");
        $categories = $stmt->fetchAll();
        
        foreach ($categories as $categorie) {
            echo "<option value='{$categorie['id']}'>{$categorie['name']}</option>";
        }
        ?>
    </select><br>

    <label>цена ₽ </label><br>
    <input type="text" name="price" required><br><br>
    <button type="submit">Добавить</button>
</form>

</body>
</html>