<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env;
use TempMail\Services\MailboxService;
Env::load(dirname(__DIR__));
header('Content-Type: application/json; charset=utf-8');
try {
 $in=json_decode(file_get_contents('php://input'),true) ?: $_POST;
 $telegramId=(int)($in['telegram_id']??0); $domainId=(int)($in['domain_id']??0);
 if($telegramId<=0 || $domainId<=0){http_response_code(422);echo json_encode(['ok'=>false,'error'=>'telegram_id and domain_id are required']);exit;}
 $ttl=(int)($in['ttl']??(getenv('MAILBOX_LIFETIME')?:3600));
 $m=(new MailboxService())->createForTelegram($telegramId,$domainId,$ttl);
 echo json_encode(['ok'=>true,'data'=>$m],JSON_UNESCAPED_SLASHES);
} catch(Throwable $e){http_response_code(500);echo json_encode(['ok'=>false,'error'=>'Mailbox creation failed']);}
