<?php // система аутентификации
// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';
require_once 'functions.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'login') {
    // Проверка CAPTCHA
    if (!verifyCaptcha($_POST['captcha'])) {
        $_SESSION['error'] = 'Неверная CAPTCHA';
        header('Location: ../index.php?page=login');
        exit;
    }
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Поиск пользователя
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        //// Успешный вход
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['success'] = 'Добро пожаловать!';
        header('Location: ../index.php');
    } else {
        $_SESSION['error'] = 'Неверные данные для входа';
        header('Location: ../index.php?page=login');
    }
    
} elseif ($action == 'register') {
    // Проверка CAPTCHA
    if (!verifyCaptcha($_POST['captcha'])) {
        $_SESSION['error'] = 'Неверная CAPTCHA';
        header('Location: ../index.php?page=register');
        exit;
    }
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Проверка пароля
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Пароли не совпадают';
        header('Location: ../index.php?page=register');
        exit;
    }
    
    // Проверка  имени
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Пользователь с таким именем или email уже существует';
        header('Location: ../index.php?page=register');
        exit;
    }
    
    // Создание 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$username, $email, $hashed_password])) {
        $_SESSION['success'] = 'Регистрация успешна! Теперь войдите в систему.';
        header('Location: ../index.php?page=login');
    } else {
        $_SESSION['error'] = 'Ошибка регистрации';
        header('Location: ../index.php?page=register');
    }
    
} elseif ($action == 'logout') {
    session_destroy();
    header('Location: ../index.php');
}
?>