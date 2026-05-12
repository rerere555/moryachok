<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['basket'])) {
        $_SESSION['basket'] = [];
    }

    if (isset($_SESSION['basket'][$product_id])) {
        $_SESSION['basket'][$product_id]++;
    } else {
        $_SESSION['basket'][$product_id] = 1;
    }
}


header('Location:/index.registr.php ');
exit;