<?php
declare(strict_types=1);
namespace TempMail\Services;

use GuzzleHttp\Client;

final class TelegramService {
    private Client $http;
    private string $token;
    public function __construct(?string $token=null) { $this->token=$token ?: (getenv('TELEGRAM_BOT_TOKEN') ?: ''); $this->http=new Client(['base_uri'=>'https://api.telegram.org/bot'.$this->token.'/','timeout'=>15]); }
    public function call(string $method, array $params=[]): array { if ($this->token==='') throw new \RuntimeException('Telegram bot token is not configured.'); $r=$this->http->post($method,['json'=>$params]); $data=json_decode((string)$r->getBody(),true); return is_array($data)?$data:[]; }
    public function sendMessage(int|string $chatId,string $text,array $extra=[]): array { return $this->call('sendMessage',array_merge(['chat_id'=>$chatId,'text'=>$text],$extra)); }
}
