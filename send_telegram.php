<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Зашифрованный токен в base64
$encrypted_token = "ODUxNDIxNzA0NzphQUdCM21wVHJUSUh0T0tHZjVYQmVQbngtcUY2RUFHTEJSQQ==";
$chat_id = "7337792719"; // ЗАМЕНИ на свой chat_id!

// Расшифровываем токен
$bot_token = base64_decode($encrypted_token);

// Получаем данные из POST запроса
$username = $_POST['username'] ?? '';

// Валидация
if (empty($username)) {
    echo json_encode(['success' => false, 'error' => 'Username не может быть пустым']);
    exit;
}

if (!preg_match('/^@[a-zA-Z0-9_]{5,}$/', $username)) {
    echo json_encode(['success' => false, 'error' => 'Username должен начинаться с @ и содержать только буквы, цифры и подчеркивания']);
    exit;
}

// Формируем сообщение для Telegram
$message = "🎯 *Новая заявка на бесплатный сайт!*\n\n";
$message .= "👤 *Пользователь:* " . $username . "\n";
$message .= "⏰ *Время:* " . date('Y-m-d H:i:s') . "\n";
$message .= "🔗 *Ссылка:* https://t.me/" . substr($username, 1) . "\n\n";
$message .= "_Не забудьте написать пользователю!_";

// Отправляем сообщение в Telegram
$url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
$data = [
    'chat_id' => $chat_id,
    'text' => $message,
    'parse_mode' => 'Markdown',
    'disable_web_page_preview' => true
];

// Используем cURL для отправки запроса
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Проверяем результат
if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if ($responseData['ok']) {
        echo json_encode(['success' => true]);
    } else {
        error_log('Telegram API Error: ' . $responseData['description']);
        echo json_encode(['success' => false, 'error' => 'Ошибка отправки. Попробуйте позже.']);
    }
} else {
    error_log('Network Error: HTTP ' . $httpCode . ' - ' . $curlError);
    echo json_encode(['success' => false, 'error' => 'Ошибка сети. Попробуйте позже.']);
}
?>
