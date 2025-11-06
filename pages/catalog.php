<?php //Каталог звуков
// Вывод категории если указана
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

//  Получение звуков
if ($category_id > 0) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.name as category_name, u.username 
        FROM sounds s 
        LEFT JOIN categories c ON s.category_id = c.id 
        LEFT JOIN users u ON s.user_id = u.id 
        WHERE s.category_id = ? AND s.status = 'approved' 
        ORDER BY s.created_at DESC
    ");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query("
        SELECT s.*, c.name as category_name, u.username 
        FROM sounds s 
        LEFT JOIN categories c ON s.category_id = c.id 
        LEFT JOIN users u ON s.user_id = u.id 
        WHERE s.status = 'approved' 
        ORDER BY s.created_at DESC 
        LIMIT 20
    ");
}
$sounds = $stmt->fetchAll();

//  категории для меню
$categories = getCategories($pdo);
?>

<div class="catalog-page">
    <h2>Каталог Звуков</h2>
    
    <div class="categories-menu">
        <h3>Категории</h3>
        <ul>
            <li><a href="index.php?page=catalog">Все звуки</a></li>
            <?php foreach ($categories as $category): ?>
                <li>
                    <a href="index.php?page=catalog&category_id=<?= $category['id'] ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <div class="sounds-grid">
        <?php if (empty($sounds)): ?>
            <p>Звуки не найдены</p>
        <?php else: ?>
            <?php foreach ($sounds as $sound): ?>
                <div class="sound-card">
                    <h4><?= htmlspecialchars($sound['title']) ?></h4>
                    <p class="category">Категория: <?= htmlspecialchars($sound['category_name']) ?></p>
                    <p class="description"><?= htmlspecialchars($sound['description']) ?></p>
                    
                    <audio controls class="audio-player">
                        <source src="<?= UPLOAD_DIR . $sound['filename'] ?>" type="audio/mpeg">
                        Ваш браузер не поддерживает аудио элементы.
                    </audio>
                    
                    <div class="sound-actions">
                        <a href="<?= UPLOAD_DIR . $sound['filename'] ?>" download class="btn">Скачать</a>
                        <button onclick="reportSound(<?= $sound['id'] ?>)" class="btn btn-report">Пожаловаться</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- окно для жалобы -->
<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Пожаловаться на звук</h3>
        <form id="reportForm">
            <input type="hidden" id="reportSoundId">
            <textarea id="reportReason" placeholder="Опишите причину жалобы..." required></textarea>
            <button type="submit" class="btn">Отправить жалобу</button>
        </form>
    </div>
</div>
