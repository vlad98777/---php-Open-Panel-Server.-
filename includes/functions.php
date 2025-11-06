<?php
// Функция для генерации CAPTCHA
function generateCaptcha() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    $_SESSION['captcha'] = $captcha;
    return $captcha;
}

// Функция для проверки CAPTCHA
function verifyCaptcha($input) {
    return isset($_SESSION['captcha']) && strtolower($input) == strtolower($_SESSION['captcha']);
}

// Функция для загрузки файла
function uploadSound($file) {
    $filename = uniqid() . '_' . basename($file['name']);
    $target_path = UPLOAD_DIR . $filename;
    
    // Проверка размера файла
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Файл слишком большой'];
    }
    
    // Проверка типа файла
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, ALLOWED_TYPES)) {
        return ['success' => false, 'error' => 'Недопустимый формат файла'];
    }
    
    // Загрузка файла
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }
}

// Функция для получения категорий
function getCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}
function getSoundFile($filename) {
    // Если файл не существует,  
    if (!file_exists(UPLOAD_DIR . $filename)) {
        // Можно вернуть путь к демо-файлу или сгенерировать заглушку
        return 'assets/silent.mp3'; //  пустой mp3 файл
    }
    return UPLOAD_DIR . $filename;
}
function uploadSound($file) {
    // Проверка ошибки загрузки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла: ' . $file['error']];
    }
    
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', basename($file['name']));
    $target_path = UPLOAD_DIR . $filename;
    
    // Проверка размера файла
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Файл слишком большой. Максимальный размер: ' . round(MAX_FILE_SIZE / 1024 / 1024) . 'MB'];
    }
    
    // Проверка типа файла
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, ALLOWED_TYPES)) {
        return ['success' => false, 'error' => 'Недопустимый формат файла. Разрешены: ' . implode(', ', ALLOWED_TYPES)];
    }
    
    // Загрузка файла
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }
}
?>