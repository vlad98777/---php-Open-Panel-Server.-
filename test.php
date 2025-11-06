<?php
// test.php - расширенная диагностика
echo "<h2>🔧 Диагностика подключения</h2>";

// Проверка разных вариантов подключения
$hosts = [
    'localhost',
    '127.0.0.1',
    'localhost:3306'
];

foreach ($hosts as $host) {
    echo "<h3>Пробуем подключиться к: <code>$host</code></h3>";
    
    try {
        $pdo = new PDO("mysql:host=$host", "root", "");
        echo "<p style='color: green;'>✅ Успешно подключились к серверу MySQL</p>";
        
        // Проверка баз данных
        $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Найдено баз данных: " . count($databases) . "</p>";
        
        if (in_array('soundcatalog', $databases)) {
            echo "<p style='color: green;'>✅ База 'soundcatalog' существует</p>";
            
            // Подключаемся к конкретной базе
            $pdo_db = new PDO("mysql:host=$host;dbname=soundcatalog", "root", "");
            $tables = $pdo_db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "<p>Таблиц в базе: " . count($tables) . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ База 'soundcatalog' не найдена</p>";
        }
        
        break; // Если один хост сработал, остальные не проверяются
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Ошибка: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";

// Проверка статуса сервера
echo "<h3>📊 Статус сервера:</h3>";
echo "<ul>";
echo "<li>Open Server иконка: " . (checkServer() ? "🟢 Зелёный" : "🔴 Не зелёный") . "</li>";
echo "<li>Папка Open Server: " . (file_exists('C:\OpenServer') ? "✅ Существует" : "❌ Не найдена") . "</li>";
echo "</ul>";

echo "<p><a href='http://localhost/openserver/phpmyadmin/' target='_blank'>🔗 Попробовать открыть phpMyAdmin</a></p>";

function checkServer() {
    // Простая проверка -  подключиться к localhost
    $sock = @fsockopen('localhost', 80, $errno, $errstr, 5);
    if ($sock) {
        fclose($sock);
        return true;
    }
    return false;
}
?>