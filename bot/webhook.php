<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env;
use TempMail\Services\TelegramService;
Env::load(dirname(__DIR__));
$secret=getenv('TELEGRAM_WEBHOOK_SECRET') ?: '';
if ($secret && !hash_equals($secret, $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '')) { http_response_code(403); exit('Forbidden'); }
$update=json_decode(file_get_contents('php://input'),true);
if (!is_array($update)) { http_response_code(400); exit('Invalid update'); }
if (isset($update['message']['chat']['id'])) {
    $chatId=$update['message']['chat']['id']; $text=trim((string)($update['message']['text'] ?? ''));
    $bot=new TelegramService();
    if ($text==='/start') $bot->sendMessage($chatId,"Welcome to TempMail Bot Pro.\nUse /new to create a temporary mailbox.\nUse /inbox to check messages.");
    elseif ($text==='/new') $bot->sendMessage($chatId,'Mailbox creation engine is ready; configure an active domain first.');
    elseif ($text==='/inbox') $bot->sendMessage($chatId,'Your inbox is currently empty.');
}
echo 'OK';
