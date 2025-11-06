<?php
require_once 'config.php';

echo "<h2>Статус системы</h2>";

if ($_SESSION['demo_mode']) {
    echo "<p style='color: orange;'>⚠️ Режим демо: База данных недоступна</p>";
} else {
    echo "<p style='color: green;'>✅ База данных подключена</p>";
    
    // Вывод использованной конфигурации
    if (isset($_SESSION['db_config'])) {
        list($host, $port, $user, $pass) = $_SESSION['db_config'];
        echo "<p>Конфигурация: $host:$port (пользователь: $user)</p>";
    }
}

// Проверка категории
$categories = getCategories($pdo);
echo "<p>Категории: " . count($categories) . " найдено</p>";

echo "<p><a href='index.php'>Перейти на главную</a></p>";
?>