<?php
declare(strict_types=1);
namespace TempMail\Services;
use GuzzleHttp\Client;
final class TelegramService { private Client $http; public function __construct(){ $token=getenv('TELEGRAM_BOT_TOKEN')?:''; if(!$token)throw new \RuntimeException('Telegram bot token is not configured.'); $this->http=new Client(['base_uri'=>'https://api.telegram.org/bot'.$token.'/','timeout'=>15]); } public function call(string $method,array $params=[]):array{$r=$this->http->post($method,['json'=>$params]);return json_decode((string)$r->getBody(),true)?:[];} public function sendMessage(int|string $chat,string $text):array{return $this->call('sendMessage',['chat_id'=>$chat,'text'=>$text]);}}
