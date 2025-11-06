<?php
require_once 'config.php'; // Подключение конфигурации

// Определение страницу для отображения
$page = isset($_GET['page']) ? $_GET['page'] : 'catalog';

// Массив доступных страниц
$allowed_pages = ['catalog', 'search', 'upload', 'login', 'register', 'profile'];

// Валидация страницы
if (!in_array($page, $allowed_pages)) {
    $page = 'catalog';
}

//  Проверка существования файла страницы
$page_file = "pages/$page.php";
if (!file_exists($page_file)) {
    $page = 'catalog';
    $page_file = "pages/catalog.php";
}

//  Подключение заголовка
include 'includes/header.php';

// Вывод сообщения об ошибках/успехе
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

//  Подключение содержимого страницы
include $page_file;

//  футер (нижняя часть веб-страницы)
include 'includes/footer.php';
?>