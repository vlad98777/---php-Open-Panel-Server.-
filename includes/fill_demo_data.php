<?php
// includes/fill_demo_data.php - заполнение демо-данными

function fillDemoData($pdo) {
    // Проверяем, есть ли уже звуки
    $stmt = $pdo->query("SELECT COUNT(*) FROM sounds");
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        return; // Уже есть данные, не заполнять
    }
    
    // Демо-звуки
    $demo_sounds = [
        // Природа
        [
            'title' => 'Пение птиц в лесу',
            'description' => 'Красивое пение различных птиц в утреннем лесу',
            'filename' => 'birds_forest.mp3',
            'category_id' => 3,
            'status' => 'approved'
        ],
        [
            'title' => 'Шум водопада',
            'description' => 'Мощный звук падающей воды в горах',
            'filename' => 'waterfall.mp3', 
            'category_id' => 3,
            'status' => 'approved'
        ],
        [
            'title' => 'Дождь с грозой',
            'description' => 'Сильный дождь с отдаленными раскатами грома',
            'filename' => 'rain_thunder.mp3',
            'category_id' => 3,
            'status' => 'approved'
        ],
        
        // Животные
        [
            'title' => 'Кот мурлычет',
            'description' => 'Успокаивающее мурлыканье домашнего кота',
            'filename' => 'cat_purr.mp3',
            'category_id' => 1,
            'status' => 'approved'
        ],
        [
            'title' => 'Собака лает',
            'description' => 'Звонкий лай собаки на улице',
            'filename' => 'dog_bark.mp3',
            'category_id' => 1, 
            'status' => 'approved'
        ],
        [
            'title' => 'Пение соловья',
            'description' => 'Красивые трели соловья вечером',
            'filename' => 'nightingale.mp3',
            'category_id' => 1,
            'status' => 'approved'
        ],
        
        // Город
        [
            'title' => 'Уличное движение',
            'description' => 'Фоновый шум оживленной городской улицы',
            'filename' => 'city_traffic.mp3',
            'category_id' => 2,
            'status' => 'approved'
        ],
        [
            'title' => 'Метро',
            'description' => 'Звук приближающегося поезда метро',
            'filename' => 'subway.mp3',
            'category_id' => 2,
            'status' => 'approved'
        ],
        
        // Транспорт
        [
            'title' => 'Завод автомобиля',
            'description' => 'Звук запуска двигателя автомобиля',
            'filename' => 'car_start.mp3',
            'category_id' => 4,
            'status' => 'approved'
        ],
        [
            'title' => 'Самолет взлетает',
            'description' => 'Мощный рев реактивного двигателя при взлете',
            'filename' => 'airplane_takeoff.mp3',
            'category_id' => 4,
            'status' => 'approved'
        ],
        
        // Музыка
        [
            'title' => 'Акустическая гитара',
            'description' => 'Нежные переборы акустической гитары',
            'filename' => 'acoustic_guitar.mp3',
            'category_id' => 5,
            'status' => 'approved'
        ],
        [
            'title' => 'Джазовый саксофон',
            'description' => 'Глубокий звук саксофона в джазовой композиции',
            'filename' => 'jazz_saxophone.mp3',
            'category_id' => 5,
            'status' => 'approved'
        ]
    ];
    
    // Добавляем звуки в базу
    $stmt = $pdo->prepare("
        INSERT INTO sounds (title, description, filename, category_id, user_id, status, plays, downloads, created_at) 
        VALUES (?, ?, ?, ?, 1, ?, ?, ?, NOW())
    ");
    
    foreach ($demo_sounds as $sound) {
        $plays = rand(5, 150);
        $downloads = rand(2, 80);
        
        $stmt->execute([
            $sound['title'],
            $sound['description'], 
            $sound['filename'],
            $sound['category_id'],
            $sound['status'],
            $plays,
            $downloads
        ]);
    }
    
    // Создание демо-пользователей
    $users = [
        ['admin', 'admin@site.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin'],
        ['user1', 'user1@site.com', password_hash('user1123', PASSWORD_DEFAULT), 'user'],
        ['user2', 'user2@site.com', password_hash('user2123', PASSWORD_DEFAULT), 'user']
    ];
    
    $user_stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    foreach ($users as $user) {
        $user_stmt->execute($user);
    }
    
    return count($demo_sounds);
}

//  вызов в config.php после подключения к БД
function setupDatabase($pdo) {
    
    fillDemoData($pdo);
}
?>