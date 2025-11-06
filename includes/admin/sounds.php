<?php
// Проверка прав администратора
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../../index.php');
    exit;
}

require_once '../config.php';

// Одобрение звука
if (isset($_GET['approve'])) {
    $sound_id = intval($_GET['approve']);
    $stmt = $pdo->prepare("UPDATE sounds SET status = 'approved' WHERE id = ?");
    $stmt->execute([$sound_id]);
    $_SESSION['success'] = 'Звук одобрен';
    header('Location: sounds.php');
    exit;
}

// Отклонение звука
if (isset($_GET['reject'])) {
    $sound_id = intval($_GET['reject']);
    $stmt = $pdo->prepare("UPDATE sounds SET status = 'rejected' WHERE id = ?");
    $stmt->execute([$sound_id]);
    $_SESSION['success'] = 'Звук отклонен';
    header('Location: sounds.php');
    exit;
}

// Удаление звука
if (isset($_GET['delete'])) {
    $sound_id = intval($_GET['delete']);
    
    //  получение имени файла для удаления
    $stmt = $pdo->prepare("SELECT filename FROM sounds WHERE id = ?");
    $stmt->execute([$sound_id]);
    $sound = $stmt->fetch();
    
    if ($sound) {
        // Удаление файла
        $file_path = '../../' . UPLOAD_DIR . $sound['filename'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Удаление записи из базы
        $stmt = $pdo->prepare("DELETE FROM sounds WHERE id = ?");
        $stmt->execute([$sound_id]);
        $_SESSION['success'] = 'Звук удален';
    }
    
    header('Location: sounds.php');
    exit;
}

//  звуки для модерации
$stmt = $pdo->query("
    SELECT s.*, c.name as category_name, u.username 
    FROM sounds s 
    LEFT JOIN categories c ON s.category_id = c.id 
    LEFT JOIN users u ON s.user_id = u.id 
    ORDER BY s.status, s.created_at DESC
");
$sounds = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация звуков - Админ-панель</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h1><a href="index.php">Модерация звуков</a></h1>
            </div>
            <div class="nav-links">
                <a href="index.php">Главная админ-панели</a>
                <a href="../../index.php">На сайт</a>
            </div>
        </nav>
    </header>
    
    <main>
        <div class="container">
            <h2>Модерация звуков</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <div class="sounds-list">
                <?php if (empty($sounds)): ?>
                    <p>Звуки не найдены</p>
                <?php else: ?>
                    <div class="sounds-grid">
                        <?php foreach ($sounds as $sound): ?>
                            <div class="sound-card">
                                <h4><?= htmlspecialchars($sound['title']) ?></h4>
                                <p><strong>Категория:</strong> <?= htmlspecialchars($sound['category_name']) ?></p>
                                <p><strong>Пользователь:</strong> <?= htmlspecialchars($sound['username']) ?></p>
                                <p><strong>Описание:</strong> <?= htmlspecialchars($sound['description']) ?></p>
                                <p><strong>Статус:</strong> 
                                    <span class="status-<?= $sound['status'] ?>">
                                        <?= $sound['status'] == 'pending' ? 'На модерации' : 
                                            ($sound['status'] == 'approved' ? 'Одобрен' : 'Отклонен') ?>
                                    </span>
                                </p>
                                <p><strong>Дата загрузки:</strong> <?= date('d.m.Y H:i', strtotime($sound['created_at'])) ?></p>
                                
                                <audio controls class="audio-player">
                                    <source src="../../<?= UPLOAD_DIR . $sound['filename'] ?>" type="audio/mpeg">
                                    Ваш браузер не поддерживает аудио элементы.
                                </audio>
                                
                                <div class="sound-actions">
                                    <?php if ($sound['status'] == 'pending'): ?>
                                        <a href="?approve=<?= $sound['id'] ?>" class="btn">Одобрить</a>
                                        <a href="?reject=<?= $sound['id'] ?>" class="btn btn-report">Отклонить</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?= $sound['id'] ?>" 
                                       class="btn btn-report" 
                                       onclick="return confirm('Удалить звук?')">
                                        Удалить
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>