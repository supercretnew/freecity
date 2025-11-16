<?php
// env.php - простой загрузчик .env файла
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Пропускаем комментарии
        if (strpos(trim($line), '#') === 0) continue;
        
        // Разделяем ключ и значение
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Сохраняем в переменные окружения
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    return true;
}

// Автоматически загружаем .env при подключении файла
loadEnv(__DIR__ . '/.env');
?>
