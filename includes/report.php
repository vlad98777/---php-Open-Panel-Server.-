<?php // обработка жалоб
require_once 'config.php';

header('Content-Type: application/json');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sound_id = intval($_POST['sound_id']);
    $reason = trim($_POST['reason']);
    
    if ($sound_id <= 0 || empty($reason)) {
        echo json_encode(['success' => false, 'error' => 'Неверные данные']);
        exit;
    }
    
    // Проверка существование звука
    $stmt = $pdo->prepare("SELECT id FROM sounds WHERE id = ? AND status = 'approved'");
    $stmt->execute([$sound_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Звук не найден']);
        exit;
    }
    
    // Сохранение жалоб
    $stmt = $pdo->prepare("INSERT INTO reports (sound_id, user_id, reason) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$sound_id, $_SESSION['user_id'], $reason])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка базы данных']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
}
?>