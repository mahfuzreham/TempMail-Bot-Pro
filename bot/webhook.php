<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env; use TempMail\Services\TelegramService;
Env::load(dirname(__DIR__)); $secret=getenv('TELEGRAM_WEBHOOK_SECRET')?:''; if($secret && !hash_equals($secret,$_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN']??'')){http_response_code(403);exit;}
$u=json_decode(file_get_contents('php://input'),true); if(!is_array($u)){http_response_code(400);exit;}
if(isset($u['message']['chat']['id'])){ $chat=$u['message']['chat']['id']; $text=trim((string)($u['message']['text']??'')); $bot=new TelegramService(); $reply=$text==='/start'?"Welcome to TempMail Bot Pro.\n/new - create mailbox\n/inbox - check inbox":($text==='/new'?'Use the web/API mailbox creator after configuring a domain.':($text==='/inbox'?'Inbox sync is available through the API.':'Unknown command.')); $bot->sendMessage($chat,$reply); }
echo 'OK';
