<?php //профиль пользователя
// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

//  информация о пользователе
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

//  звуки 
$stmt = $pdo->prepare("
    SELECT s.*, c.name as category_name 
    FROM sounds s 
    LEFT JOIN categories c ON s.category_id = c.id 
    WHERE s.user_id = ? 
    ORDER BY s.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$user_sounds = $stmt->fetchAll();
?>

<div class="profile-page">
    <h2>Профиль пользователя</h2>
    
    <div class="user-info">
        <h3>Информация</h3>
        <p><strong>Имя пользователя:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Роль:</strong> <?= $user['role'] == 'admin' ? 'Администратор' : 'Пользователь' ?></p>
        <p><strong>Статус:</strong> <?= $user['status'] == 'active' ? 'Активен' : 'Заблокирован' ?></p>
        <p><strong>Дата регистрации:</strong> <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
    </div>
    
    <div class="user-sounds">
        <h3>Мои звуки</h3>
        
        <?php if (empty($user_sounds)): ?>
            <p>Вы еще не загрузили ни одного звука</p>
        <?php else: ?>
            <div class="sounds-grid">
                <?php foreach ($user_sounds as $sound): ?>
                    <div class="sound-card">
                        <h4><?= htmlspecialchars($sound['title']) ?></h4>
                        <p class="category">Категория: <?= htmlspecialchars($sound['category_name']) ?></p>
                        <p class="description"><?= htmlspecialchars($sound['description']) ?></p>
                        <p class="status">
                            Статус: 
                            <span class="status-<?= $sound['status'] ?>">
                                <?= $sound['status'] == 'pending' ? 'На модерации' : 
                                    ($sound['status'] == 'approved' ? 'Одобрен' : 'Отклонен') ?>
                            </span>
                        </p>
                        
                        <?php if ($sound['status'] == 'approved'): ?>
                            <audio controls class="audio-player">
                                <source src="<?= UPLOAD_DIR . $sound['filename'] ?>" type="audio/mpeg">
                                Ваш браузер не поддерживает аудио элементы.
                            </audio>
                            
                            <div class="sound-stats">
                                <span>Прослушиваний: <?= $sound['plays'] ?></span>
                                <span>Скачиваний: <?= $sound['downloads'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>