<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env; use TempMail\Core\Database; use TempMail\Services\CpanelService;
Env::load(dirname(__DIR__)); header('Content-Type: application/json');
try {
 $in=json_decode(file_get_contents('php://input'),true) ?: $_POST; $domainId=(int)($in['domain_id']??0); $ttl=max(300,min(86400,(int)($in['ttl']??getenv('MAILBOX_LIFETIME')?:3600)));
 $db=Database::connect(); $q=$db->prepare('SELECT id,domain FROM domains WHERE id=? AND active=1'); $q->execute([$domainId]); $domain=$q->fetch();
 if(!$domain){http_response_code(422); echo json_encode(['ok'=>false,'error'=>'Invalid domain']); exit;}
 $local='tm'.bin2hex(random_bytes(5)); $email=$local.'@'.$domain['domain']; $password=bin2hex(random_bytes(12));
 (new CpanelService())->createMailbox($email,$password);
 $expires=(new DateTimeImmutable())->modify('+'.$ttl.' seconds')->format('Y-m-d H:i:s');
 $s=$db->prepare('INSERT INTO mailboxes(domain_id,email,password_hash,expires_at) VALUES(?,?,?,?)'); $s->execute([$domainId,$email,password_hash($password,PASSWORD_DEFAULT),$expires]);
 echo json_encode(['ok'=>true,'data'=>['email'=>$email,'password'=>$password,'expires_at'=>$expires]]);
} catch(Throwable $e){http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Mailbox creation failed']);}
