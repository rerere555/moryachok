<?php
require 'bdconnect.php';

$id = $_POST['id'];
$name = $_POST['name'];
$category_id = $_POST['category_id'];
$description = $_POST['description'];
$price = $_POST['price'];

$stmt = $pdo->prepare("
    UPDATE products 
    SET name = :name, category_id = :category_id, description = :description, price = :price
    WHERE id = :id
");

$stmt->execute([
    'id' => $id,
    'name' => $name,
    'category_id' => $category_id,
    'description' => $description,
    'price' => $price
]);

header('Location: index.admin.php');
?>
