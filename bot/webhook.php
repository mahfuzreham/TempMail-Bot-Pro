<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env;
use TempMail\Core\Database;
use TempMail\Services\TelegramService;
use TempMail\Services\MailboxService;
Env::load(dirname(__DIR__));
$secret=getenv('TELEGRAM_WEBHOOK_SECRET')?:'';
if($secret && !hash_equals($secret,$_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN']??'')){http_response_code(403);exit;}
$u=json_decode(file_get_contents('php://input'),true);
if(!is_array($u)){http_response_code(400);exit;}
if(isset($u['message']['chat']['id'])){
 $chat=(int)$u['message']['chat']['id']; $msg=$u['message']; $text=trim((string)($msg['text']??''));
 $bot=new TelegramService();
 try {
  $db=Database::connect();
  $q=$db->prepare('INSERT INTO telegram_users(telegram_id,username,first_name) VALUES(?,?,?) ON DUPLICATE KEY UPDATE username=VALUES(username),first_name=VALUES(first_name),updated_at=CURRENT_TIMESTAMP');
  $q->execute([$chat,$msg['from']['username']??null,$msg['from']['first_name']??null]);
  if($text==='/start') $reply="Welcome to TempMail Bot Pro.\n\n/new - create a temporary mailbox\n/inbox - list your active mailboxes\n/help - show commands";
  elseif($text==='/help') $reply="Commands:\n/new - create mailbox\n/inbox - list active mailboxes";
  elseif($text==='/new') {
   $d=$db->query("SELECT id,domain FROM domains WHERE active=1 ORDER BY id LIMIT 1")->fetch();
   if(!$d) $reply='No active domain is configured by the administrator.';
   else { $m=(new MailboxService())->createForTelegram($chat,(int)$d['id'],(int)(getenv('MAILBOX_LIFETIME')?:3600)); $reply="Mailbox created.\n\nEmail: {$m['email']}\nPassword: {$m['password']}\nExpires: {$m['expires_at']}"; }
  } elseif($text==='/inbox') {
   $items=(new MailboxService())->activeForTelegram($chat);
   if(!$items) $reply='You have no active mailboxes. Use /new.';
   else {$lines=[];foreach($items as $m)$lines[]="{$m['email']}\nExpires: {$m['expires_at']}";$reply="Your active mailboxes:\n\n".implode("\n\n",$lines);}
  } else $reply='Unknown command. Use /help.';
  $bot->sendMessage($chat,$reply);
 } catch(\Throwable $e) { $bot->sendMessage($chat,'Sorry, the mailbox service is temporarily unavailable.'); }
}
echo 'OK';
