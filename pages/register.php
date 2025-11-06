<?php
// Если пользователь уже авторизован, перенаправление
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<div class="auth-page">
    <h2>Регистрация</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <form method="POST" action="includes/auth.php?action=register" class="auth-form">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required minlength="3" maxlength="50">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required minlength="6">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Подтвердите пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <label for="captcha">CAPTCHA:</label>
            <div class="captcha-container">
                <img src="assets/captcha.php" id="captchaImage" alt="CAPTCHA">
                <button type="button" onclick="refreshCaptcha()" class="btn btn-small">Обновить</button>
            </div>
            <input type="text" id="captcha" name="captcha" required>
        </div>
        
        <button type="submit" class="btn">Зарегистрироваться</button>
    </form>
    
    <p>Уже есть аккаунт? <a href="index.php?page=login">Войдите</a></p>
</div>