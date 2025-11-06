<?php //Загрузка звуков
// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Обработка загрузки
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category_id']);
    
    // Валидация
    if (empty($title) || empty($description) || $category_id <= 0) {
        $_SESSION['error'] = 'Заполните все поля';
    } elseif (!isset($_FILES['sound_file']) || $_FILES['sound_file']['error'] != UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Ошибка загрузки файла';
    } else {
        // Загрузка файла
        $upload_result = uploadSound($_FILES['sound_file']);
        
        if ($upload_result['success']) {
            // Сохранение в базу данных
            $stmt = $pdo->prepare("
                INSERT INTO sounds (title, description, filename, category_id, user_id) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$title, $description, $upload_result['filename'], $category_id, $_SESSION['user_id']])) {
                $_SESSION['success'] = 'Звук успешно загружен и ожидает модерации';
                header('Location: index.php?page=profile');
                exit;
            } else {
                $_SESSION['error'] = 'Ошибка сохранения в базу данных';
            }
        } else {
            $_SESSION['error'] = $upload_result['error'];
        }
    }
}

$categories = getCategories($pdo);
?>

<div class="upload-page">
    <h2>Загрузка звука</h2>
    
    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="form-group">
            <label for="title">Название звука:</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="category_id">Категория:</label>
            <select id="category_id" name="category_id" required>
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="sound_file">Звуковой файл (макс. <?= round(MAX_FILE_SIZE / 1024 / 1024) ?>MB):</label>
            <input type="file" id="sound_file" name="sound_file" accept=".mp3,.wav,.ogg" required>
        </div>
        
        <button type="submit" class="btn">Загрузить звук</button>
    </form>
</div>