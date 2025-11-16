<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$botToken = '8514217047:AAGB3mpTrTIHtOKGf5XBePnx-qF6EAGLBRQ'; // Замените на токен вашего бота
$chatId = '7337792719'; // Замените на ваш chat_id в Telegram

$username = $_POST['username'] ?? '';

if (empty($username)) {
    echo json_encode(['success' => false, 'error' => 'Username не может быть пустым']);
    exit;
}

if (!preg_match('/^@[a-zA-Z0-9_]{5,}$/', $username)) {
    echo json_encode(['success' => false, 'error' => 'Некорректный формат username']);
    exit;
}

$message = "🎯 *Новая заявка на бесплатный сайт!*\n\n";
$message .= "👤 *Пользователь:* " . $username . "\n";
$message .= "⏰ *Время:* " . date('Y-m-d H:i:s') . "\n";
$message .= "🔗 *Ссылка:* https://t.me/" . substr($username, 1) . "\n\n";
$message .= "_Не забудьте написать пользователю!_";

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown',
    'disable_web_page_preview' => true
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    if ($responseData['ok']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка Telegram API: ' . $responseData['description']]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка сети при отправке сообщения']);
}
?> 
