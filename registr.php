
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=`device-width`, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css?v=<?= time() ?>">
</head>
<body>
    <form action="" method="POST">
    <h1>Регистрация</h1><br>
    <form action="/" method="POST">
        <label>имя</label><br>
        <input type="name" name="name" required><br>
        <label>телефон</label><br>
        <input type="phone" name="phone" required><br>
        
        <label>Пароль</label><br>
        <input type="password" name="password" required><br>
        
        <a href="index.registr.php" type="submit"  name="login">Зарегистрироваться</a><br>

        <a href="authorization.php">у вас уже есть аккаунт? Войдите</a>
    </form>
    </form>
</body>
</html>