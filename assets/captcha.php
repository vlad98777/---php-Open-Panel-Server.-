<?php
session_start();

//  CAPTCHA изображения
function generateCaptchaImage($text) {
    $width = 200;
    $height = 50;
    
    //  изображение
    $image = imagecreate($width, $height);
    
    // Цвета
    $bg_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    $line_color = imagecolorallocate($image, 200, 200, 200);
    $pixel_color = imagecolorallocate($image, 150, 150, 150);
    
    //  фон
    imagefill($image, 0, 0, $bg_color);
    
    //  случайные линии
    for ($i = 0; $i < 5; $i++) {
        imageline($image, 0, rand() % $height, $width, rand() % $height, $line_color);
    }
    
    //  случайные точки
    for ($i = 0; $i < 100; $i++) {
        imagesetpixel($image, rand() % $width, rand() % $height, $pixel_color);
    }
    
    //  текст
    $font = 5; // Встроенный шрифт
    $text_width = imagefontwidth($font) * strlen($text);
    $text_height = imagefontheight($font);
    
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font, $x, $y, $text, $text_color);
    
    // Вывод изображения
    header('Content-type: image/png');
    imagepng($image);
    imagedestroy($image);
}

//  текст CAPTCHA
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
$captcha_text = '';
for ($i = 0; $i < 6; $i++) {
    $captcha_text .= $chars[rand(0, strlen($chars) - 1)];
}

// Сохранение
$_SESSION['captcha'] = $captcha_text;

//  изображение
generateCaptchaImage($captcha_text);
?>