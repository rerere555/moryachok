<?php
require 'bdconnect.php';  

$name = $_POST['name'];
$description = $_POST['description'];
$category_id = $_POST['category_id'];
$price = $_POST['price'];   

$stmt = $pdo->prepare("
    INSERT INTO products (name, description, category_id, price) 
    VALUES (:name, :description, :category_id, :price)
");

$stmt->execute([
    'name' => $name,
    'description' => $description,
    'category_id' => $category_id,
    'price' => $price
]);

header('Location: index.admin.php');  // перенаправление обратно
?>
