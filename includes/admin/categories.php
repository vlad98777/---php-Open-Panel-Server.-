<<?php //Управление категориями
// Проверка прав администратора
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../../index.php');
    exit;
}

require_once '../config.php';

// Добавление категории
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        $_SESSION['success'] = 'Категория добавлена';
    } else {
        $_SESSION['error'] = 'Название категории не может быть пустым';
    }
}

// Удаление категории
if (isset($_GET['delete'])) {
    $category_id = intval($_GET['delete']);
    
    // Проверка, наличиея звуков в этой категории
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sounds WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $sound_count = $stmt->fetchColumn();
    
    if ($sound_count > 0) {
        $_SESSION['error'] = 'Нельзя удалить категорию, в которой есть звуки';
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $_SESSION['success'] = 'Категория удалена';
    }
    
    header('Location: categories.php');
    exit;
}

// вывод всех категорий
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление категориями - Админ-панель</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h1><a href="index.php">Управление категориями</a></h1>
            </div>
            <div class="nav-links">
                <a href="index.php">Главная админ-панели</a>
                <a href="../../index.php">На сайт</a>
            </div>
        </nav>
    </header>
    
    <main>
        <div class="container">
            <h2>Управление категориями</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <div class="admin-content">
                <div class="add-category-form">
                    <h3>Добавить категорию</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Название категории:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Описание:</label>
                            <textarea id="description" name="description"></textarea>
                        </div>
                        
                        <button type="submit" name="add_category" class="btn">Добавить категорию</button>
                    </form>
                </div>
                
                <div class="categories-list">
                    <h3>Существующие категории</h3>
                    
                    <?php if (empty($categories)): ?>
                        <p>Категории не найдены</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Описание</th>
                                    <th>Дата создания</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= $category['id'] ?></td>
                                        <td><?= htmlspecialchars($category['name']) ?></td>
                                        <td><?= htmlspecialchars($category['description']) ?></td>
                                        <td><?= date('d.m.Y H:i', strtotime($category['created_at'])) ?></td>
                                        <td>
                                            <a href="?delete=<?= $category['id'] ?>" 
                                               class="btn btn-report" 
                                               onclick="return confirm('Удалить категорию?')">
                                                Удалить
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>