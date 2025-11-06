// Функция для отправки жалобы
function reportSound(soundId) {
    document.getElementById('reportSoundId').value = soundId;
    document.getElementById('reportModal').style.display = 'block';
}

// Закрытие модального окна
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('reportModal').style.display = 'none';
});

// Обработка формы жалобы
document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const soundId = document.getElementById('reportSoundId').value;
    const reason = document.getElementById('reportReason').value;
    
    fetch('includes/report.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `sound_id=${soundId}&reason=${encodeURIComponent(reason)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Жалоба отправлена');
            document.getElementById('reportModal').style.display = 'none';
            document.getElementById('reportForm').reset();
        } else {
            alert('Ошибка: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка отправки жалобы');
    });
});

// Закрытие модального окна при клике вне его
window.addEventListener('click', function(e) {
    const modal = document.getElementById('reportModal');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

// Обновление CAPTCHA
function refreshCaptcha() {
    const captchaImage = document.getElementById('captchaImage');
    if (captchaImage) {
        captchaImage.src = 'assets/captcha.php?t=' + new Date().getTime();
    }
}