<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "👉 Старт<br>";

$file = __DIR__ . '/vendor/autoload.php';

if (!file_exists($file)) {
    echo "❌ Файл autoload.php не найден по пути: $file";
    exit;
}

require $file;

echo "✅ autoload.php загружен<br>";

use Google\Client;
use Google\Service\Sheets;

$client = new Client();

$credentialsPath = __DIR__ . '/google-credentials.json';

if (!file_exists($credentialsPath)) {
    echo "❌ Файл google-credentials.json не найден по пути: $credentialsPath";
    exit;
}

$client->setAuthConfig($credentialsPath);
$client->setScopes([Sheets::SPREADSHEETS]);

echo "✅ Google Client полностью настроен<br>";

// === Чтение данных из таблицы ===
$service = new Sheets($client);

$spreadsheetId = '$spreadsheetId = '1AbcD23EfGhIjKlMnOpQrStUvWxYz'; // твой ID
'; // ⬅️ Вставь сюда свой ID таблицы
$range = 'Лист1'; // Или другое название листа в таблице

try {
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (empty($values)) {
        echo "⚠️ Данные не найдены или таблица пустая";
    } else {
        echo "✅ Данные получены из таблицы:<br>";
        echo "<pre>" . print_r($values, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Ошибка при чтении таблицы:<br>" . $e->getMessage();
}

exit;
