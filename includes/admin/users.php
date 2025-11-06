<?php // управдение пользователями

// Проверка прав администратора
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../../index.php');
    exit;
}

require_once '../config.php';

// Блокировка/разблокировка пользователя
if (isset($_GET['toggle_block'])) {
    $user_id = intval($_GET['toggle_block']);
    // Получение текущего статуса
    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        $new_status = $user['status'] == 'active' ? 'blocked' : 'active';
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $user_id]);
        
        $_SESSION['success'] = $new_status == 'blocked' ? 'Пользователь заблокирован' : 'Пользователь разблокирован';
    }
    
    header('Location: users.php');
    exit;
}

// Удаление пользователя
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    
    // Нельзя удалить самого себя
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = 'Нельзя удалить собственный аккаунт';
        header('Location: users.php');
        exit;
    }
    
    // Удаление звуков пользователя
    $stmt = $pdo->prepare("SELECT filename FROM sounds WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $sounds = $stmt->fetchAll();
    
    foreach ($sounds as $sound) {
        $file_path = '../../' . UPLOAD_DIR . $sound['filename'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Удаление пользователя из БД
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    $_SESSION['success'] = 'Пользователь удален';
    header('Location: users.php');
    exit;
}

// вывод всех пользователей
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями - Админ-панель</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h1><a href="index.php">Управление пользователями</a></h1>
            </div>
            <div class="nav-links">
                <a href="index.php">Главная админ-панели</a>
                <a href="../../index.php">На сайт</a>
            </div>
        </nav>
    </header>
    
    <main>
        <div class="container">
            <h2>Управление пользователями</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <div class="users-list">
                <?php if (empty($users)): ?>
                    <p>Пользователи не найдены</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя пользователя</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Статус</th>
                                <th>Дата регистрации</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= $user['role'] == 'admin' ? 'Администратор' : 'Пользователь' ?></td>
                                    <td>
                                        <span class="status-<?= $user['status'] ?>">
                                            <?= $user['status'] == 'active' ? 'Активен' : 'Заблокирован' ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <a href="?toggle_block=<?= $user['id'] ?>" 
                                           class="btn <?= $user['status'] == 'active' ? 'btn-report' : '' ?>">
                                            <?= $user['status'] == 'active' ? 'Заблокировать' : 'Разблокировать' ?>
                                        </a>
                                        
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="?delete=<?= $user['id'] ?>" 
                                               class="btn btn-report" 
                                               onclick="return confirm('Удалить пользователя? Все его звуки также будут удалены.')">
                                                Удалить
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>