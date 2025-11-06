<?php //Поиск звуков
$search_results = [];
$search_query = '';

// Обработка поискового запроса
if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);
    $stmt = $pdo->prepare("
        SELECT s.*, c.name as category_name, u.username 
        FROM sounds s 
        LEFT JOIN categories c ON s.category_id = c.id 
        LEFT JOIN users u ON s.user_id = u.id 
        WHERE s.status = 'approved' AND (s.title LIKE ? OR s.description LIKE ?)
        ORDER BY s.created_at DESC
    ");
    $search_term = "%$search_query%";
    $stmt->execute([$search_term, $search_term]);
    $search_results = $stmt->fetchAll();
}
?>

<div class="search-page">
    <h2>Поиск звуков</h2>
    
    <form method="GET" action="index.php" class="search-form">
        <input type="hidden" name="page" value="search">
        <input type="text" name="query" value="<?= htmlspecialchars($search_query) ?>" 
               placeholder="Введите название звука..." required>
        <button type="submit" class="btn">Найти</button>
    </form>
    
    <?php if (!empty($search_query)): ?>
        <div class="search-results">
            <h3>Результаты поиска для "<?= htmlspecialchars($search_query) ?>"</h3>
            
            <?php if (empty($search_results)): ?>
                <p>Звуки не найдены</p>
            <?php else: ?>
                <div class="sounds-grid">
                    <?php foreach ($search_results as $sound): ?>
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
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>