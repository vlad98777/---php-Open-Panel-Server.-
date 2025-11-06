<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

echo "Проверка подключения к MySQL...<br>";

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    echo "✅ Успешное подключение к MySQL<br>";
    
    // Проверяка базы данных
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Доступные базы данных: " . implode(', ', $databases) . "<br>";
    
    if (in_array('soundcatalog', $databases)) {
        echo "✅ База данных soundcatalog существует<br>";
    } else {
        echo "❌ База данных soundcatalog не существует<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage() . "<br>";
    echo "Проверьте:<br>";
    echo "1. Запущен ли MySQL сервер<br>";
    echo "2. Правильный ли пароль<br>";
    echo "3. Доступен ли порт 3306<br>";
}
?>