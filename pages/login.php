<?php
// Если пользователь уже авторизован, перенаправление
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<div class="auth-page">
    <h2>Вход в систему</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <form method="POST" action="includes/auth.php?action=login" class="auth-form">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="captcha">CAPTCHA:</label>
            <div class="captcha-container">
                <img src="assets/captcha.php" id="captchaImage" alt="CAPTCHA">
                <button type="button" onclick="refreshCaptcha()" class="btn btn-small">Обновить</button>
            </div>
            <input type="text" id="captcha" name="captcha" required>
        </div>
        
        <button type="submit" class="btn">Войти</button>
    </form>
    
    <p>Нет аккаунта? <a href="index.php?page=register">Зарегистрируйтесь</a></p>
</div>