<?php
// Проверка прав администратора
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../../index.php'); //выход,если не admin
    exit;
}

require_once '../config.php';

// Статистика для админ-панели
$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['total_users'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM sounds");
$stats['total_sounds'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM sounds WHERE status = 'pending'");
$stats['pending_sounds'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM reports WHERE status = 'pending'");
$stats['pending_reports'] = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Каталог Звуков</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .admin-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .admin-link-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .admin-link-card:hover {
            transform: translateY(-5px);
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <h1><a href="../../index.php">Админ-панель</a></h1>
            </div>
            <div class="nav-links">
                <a href="../../index.php">На сайт</a>
                <a href="?logout">Выйти</a>
            </div>
        </nav>
    </header>
    
    <main>
        <div class="container">
            <h2>Административная панель</h2>
            
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_users'] ?></div>
                    <div class="stat-label">Пользователей</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['total_sounds'] ?></div>
                    <div class="stat-label">Всего звуков</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['pending_sounds'] ?></div>
                    <div class="stat-label">Ожидают модерации</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['pending_reports'] ?></div>
                    <div class="stat-label">Жалоб</div>
                </div>
            </div>
            
            <div class="admin-links">
                <a href="categories.php" class="admin-link-card">
                    <h3>Управление категориями</h3>
                    <p>Добавление, редактирование и удаление категорий</p>
                </a>
                
                <a href="sounds.php" class="admin-link-card">
                    <h3>Модерация звуков</h3>
                    <p>Одобрение и управление загруженными звуками</p>
                </a>
                
                <a href="users.php" class="admin-link-card">
                    <h3>Управление пользователями</h3>
                    <p>Блокировка и разблокировка пользователей</p>
                </a>
            </div>
        </div>
    </main>
</body>
</html>

<?php
// Выход из админ-панели
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}
?>