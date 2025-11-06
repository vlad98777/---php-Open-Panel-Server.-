<?php
//  авторизация пользователя
$is_logged_in = isset($_SESSION['user_id']); // Проверка авторизации
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : ''; // Роль пользователя
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог Звуков</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h1><a href="index.php">Каталог Звуков</a></h1>
            </div>
            <div class="nav-links">
                <a href="index.php?page=catalog">Каталог</a>
                <a href="index.php?page=search">Поиск</a>
                
                <?php if ($is_logged_in): ?>
                    <a href="index.php?page=upload">Загрузить звук</a>
                    <a href="index.php?page=profile">Профиль</a>
                    <?php if ($user_role == 'admin'): ?>
                        <a href="includes/admin/">Админ-панель</a>
                    <?php endif; ?>
                    <a href="includes/auth.php?action=logout">Выйти</a>
                <?php else: ?>
                    <a href="index.php?page=login">Войти</a>
                    <a href="index.php?page=register">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
        <div class="container">