<?php

session_start();

$db_configs = [
    ['127.0.0.1', 3306, 'root', ''],           // Стандартные настройки
    ['localhost', 3306, 'root', ''],           // Localhost
    ['127.0.0.1', 3306, 'root', 'root'],      // MAMP пароль
    ['localhost', 3306, 'root', 'root'],      // MAMP localhost
    ['127.0.0.1', 8889, 'root', 'root'],      // MAMP порт
    ['localhost', 8889, 'root', 'root'],      // MAMP localhost + порт
    ['127.0.0.1', 3307, 'root', ''],          // Альтернативный порт
];

// Настройки загрузки файлов
define('MAX_FILE_SIZE', 10 * 1024 * 1024);
define('ALLOWED_TYPES', ['mp3', 'wav', 'ogg']);
define('UPLOAD_DIR', 'uploads/sounds/');

//  Создание директории для загрузок
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Функция для создания базы и таблиц
function setupDatabase($pdo) {
    try {
        //  SQL для создания всех таблиц
        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('user', 'admin') DEFAULT 'user',
                status ENUM('active', 'blocked') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
            "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",
            
            "CREATE TABLE IF NOT EXISTS sounds (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                filename VARCHAR(255) NOT NULL,
                category_id INT,
                user_id INT,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                plays INT DEFAULT 0,
                downloads INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB",
            
            "CREATE TABLE IF NOT EXISTS reports (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sound_id INT,
                user_id INT,
                reason TEXT NOT NULL,
                status ENUM('pending', 'resolved') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (sound_id) REFERENCES sounds(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB"
        ];

        foreach ($tables as $tableSql) {
            $pdo->exec($tableSql);
        }
        
        //  начальные категории
        $categories = [
            ['Животные', 'Звуки животных'],
            ['Город', 'Городские звуки'],
            ['Природа', 'Природные звуки'],
            ['Транспорт', 'Звуки транспорта'],
            ['Музыка', 'Музыкальные звуки'],
            ['Аплодисменты', 'Аплодисменты и овации']
        ];
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
        foreach ($categories as $category) {
            $stmt->execute([$category[0], $category[1]]);
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Setup error: " . $e->getMessage());
        return false;
    }
}

// Подключение с разными конфигурациями
$pdo = null;
$used_config = null;

foreach ($db_configs as $config) {
    list($host, $port, $user, $pass) = $config;
    
    try {
        //  подключение к серверу без выбора базы
        $temp_pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
        $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Создание базы, если не существует
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS soundcatalog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Подключение к конкретной базе
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=soundcatalog", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        $used_config = $config;
        break;
        
    } catch (PDOException $e) {
        //  Полключение следующей конфигурации
        continue;
    }
}

// Если ни одна конфигурация не сработала
if (!$pdo) {
    //  ошибка только при первом запуске
    if (!isset($_SESSION['db_error_shown'])) {
        $_SESSION['db_error_shown'] = true;
        die("
        <html>
        <head><title>Ошибка базы данных</title></head>
        <body style='font-family: Arial; padding: 20px;'>
            <h2>❌ Ошибка подключения к MySQL</h2>
            <p><strong>Проблема:</strong> Не удалось подключиться к базе данных</p>
            
            <h3>🔧 Решения:</h3>
            <ol>
                <li><strong>Запустите MySQL сервер</strong>
                    <ul>
                        <li>XAMPP: Запустите Apache и MySQL в панели управления</li>
                        <li>OpenServer: Запустите MySQL в панели</li>
                        <li>Денвер: Запустите MySQL</li>
                    </ul>
                </li>
                <li><strong>Проверьте настройки MySQL</strong>
                    <ul>
                        <li>Логин: root</li>
                        <li>Пароль: (пустой) или 'root'</li>
                        <li>Порт: 3306 или 8889</li>
                    </ul>
                </li>
                <li><strong>Создайте базу вручную:</strong>
                    <pre>mysql -u root -p
CREATE DATABASE soundcatalog;
exit;</pre>
                </li>
            </ol>
            
            <p><em>После исправления ошибки обновите страницу</em></p>
        </body>
        </html>
        ");
    } else {
        //  временное решение
        class MockDB {
            public function prepare($sql) { return new MockStmt(); }
            public function query($sql) { return new MockStmt(); }
            public function lastInsertId() { return rand(1, 1000); }
            public function exec($sql) { return 1; }
        }
        
        class MockStmt {
            public function execute($params = []) { return true; }
            public function fetch() { 
                return ['id' => 1, 'name' => 'Тест', 'username' => 'demo']; 
            }
            public function fetchAll() { 
                return [
                    ['id' => 1, 'title' => 'Демо звук', 'description' => 'Это демонстрационный звук', 
                     'filename' => 'demo.mp3', 'category_name' => 'Животные', 'username' => 'demo']
                ]; 
            }
            public function fetchColumn() { return 1; }
            public function rowCount() { return 1; }
        }
        
        $pdo = new MockDB();
        
        // Сохранение в сессии, что используем демо-режим
        $_SESSION['demo_mode'] = true;
    }
} else {
    // Настройка базы если подключение успешно
    setupDatabase($pdo);
    $_SESSION['demo_mode'] = false;
}

// Сохранение использованной конфигурации для отладки
if ($used_config && !isset($_SESSION['db_config'])) {
    $_SESSION['db_config'] = $used_config;
}

// Функции для работы с базой
function getCategories($pdo) {
    if ($_SESSION['demo_mode']) {
        return [
            ['id' => 1, 'name' => 'Животные', 'description' => 'Звуки животных'],
            ['id' => 2, 'name' => 'Город', 'description' => 'Городские звуки'],
            ['id' => 3, 'name' => 'Природа', 'description' => 'Природные звуки']
        ];
    }
    
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}
?>