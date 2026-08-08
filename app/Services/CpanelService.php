<?php
declare(strict_types=1);
namespace TempMail\Services;

use GuzzleHttp\Client;

final class CpanelService {
    private Client $http; private string $user; private string $token;
    public function __construct(?string $host=null, ?string $user=null, ?string $token=null) {
        $host=$host ?: (getenv('CPANEL_HOST') ?: ''); $this->user=$user ?: (getenv('CPANEL_USER') ?: ''); $this->token=$token ?: (getenv('CPANEL_TOKEN') ?: '');
        $this->http=new Client(['base_uri'=>rtrim($host,'/').'/execute/','timeout'=>20,'verify'=>true,'headers'=>['Authorization'=>'cpanel '.$this->user.':'.$this->token]]);
    }
    public function execute(string $module,string $function,array $params=[]): array { if(!$this->user||!$this->token) throw new \RuntimeException('cPanel credentials are not configured.'); $r=$this->http->get($module.'/'.$function,['query'=>$params]); $d=json_decode((string)$r->getBody(),true); return is_array($d)?$d:[]; }
    public function createMailbox(string $email,string $password,int $quota=1024): array { [$local,$domain]=array_pad(explode('@',$email,2),2,''); return $this->execute('Email','add_pop',['email'=>$local,'domain'=>$domain,'password'=>$password,'quota'=>$quota]); }
    public function deleteMailbox(string $email): array { [$local,$domain]=array_pad(explode('@',$email,2),2,''); return $this->execute('Email','delete_pop',['email'=>$local,'domain'=>$domain]); }
}
