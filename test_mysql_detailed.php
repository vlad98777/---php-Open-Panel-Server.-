<?php
// test_mysql_detailed.php - детальная проверка
echo "<h3>Детальная проверка подключения MySQL</h3>";

$host = '127.0.0.1';
$user = 'root';
$pass = '';

// Тест 1: Проверка подключения без пароля
echo "<h4>Тест 1: Подключение без пароля</h4>";
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    echo "✅ Успешное подключение без пароля<br>";
} catch(PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "<br>";
}

// Тест 2: Проверка порта
echo "<h4>Тест 2: Проверка разных портов</h4>";
$ports = [3306, 3307, 8889, 3308];
foreach ($ports as $port) {
    try {
        $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
        echo "✅ Порт $port доступен<br>";
        break;
    } catch(PDOException $e) {
        echo "❌ Порт $port недоступен: " . $e->getMessage() . "<br>";
    }
}

// Тест 3: Проверка сокета (для Windows)
echo "<h4>Тест 3: Проверка через localhost</h4>";
try {
    $pdo = new PDO("mysql:host=localhost", $user, $pass);
    echo "✅ Успешное подключение через localhost<br>";
} catch(PDOException $e) {
    echo "❌ Ошибка localhost: " . $e->getMessage() . "<br>";
}

// Тест 4: Проверка существующих баз данных
echo "<h4>Тест 4: Проверка баз данных</h4>";
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Найдено баз данных: " . count($databases) . "<br>";
    echo "Список: " . implode(', ', $databases) . "<br>";
} catch(PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "<br>";
}

// Тест 5: Попытка создать базу данных
echo "<h4>Тест 5: Создание базы данных</h4>";
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS soundcatalog_test");
    echo "✅ База данных soundcatalog_test создана/существует<br>";
    
    // Проверяем подключение к новой базе
    $pdo2 = new PDO("mysql:host=$host;dbname=soundcatalog_test", $user, $pass);
    echo "✅ Успешное подключение к soundcatalog_test<br>";
    
    // Удаление тестовой базы
    $pdo->exec("DROP DATABASE soundcatalog_test");
    echo "✅ Тестовая база удалена<br>";
} catch(PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "<br>";
}

echo "<hr><a href='index.php'>Перейти на главную</a>";
?>