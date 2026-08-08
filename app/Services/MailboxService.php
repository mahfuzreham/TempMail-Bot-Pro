<?php
declare(strict_types=1);
namespace TempMail\Services;
use TempMail\Core\Database;
final class MailboxService {
 public function __construct(private CpanelService $cpanel=new CpanelService()) {}
 public function createForTelegram(int $telegramId,int $domainId,int $ttl=3600):array {
  $db=Database::connect();
  $q=$db->prepare('SELECT id,domain FROM domains WHERE id=? AND active=1'); $q->execute([$domainId]); $domain=$q->fetch();
  if(!$domain) throw new \RuntimeException('Domain is unavailable.');
  $ttl=max(300,min(86400,$ttl)); $local='tm'.bin2hex(random_bytes(6)); $email=$local.'@'.$domain['domain']; $password=bin2hex(random_bytes(12));
  $u=$db->prepare('INSERT INTO telegram_users(telegram_id) VALUES(?) ON DUPLICATE KEY UPDATE updated_at=CURRENT_TIMESTAMP'); $u->execute([$telegramId]);
  $uid=(int)$db->lastInsertId(); if(!$uid){$q=$db->prepare('SELECT id FROM telegram_users WHERE telegram_id=?');$q->execute([$telegramId]);$uid=(int)$q->fetchColumn();}
  $this->cpanel->createMailbox($email,$password);
  $expires=(new \DateTimeImmutable('now'))->modify('+'.$ttl.' seconds')->format('Y-m-d H:i:s');
  $s=$db->prepare('INSERT INTO mailboxes(telegram_user_id,domain_id,email,password_hash,expires_at) VALUES(?,?,?,?,?)');
  $s->execute([$uid,$domain['id'],$email,$password,$expires]);
  return ['id'=>(int)$db->lastInsertId(),'email'=>$email,'password'=>$password,'expires_at'=>$expires];
 }
 public function activeForTelegram(int $telegramId):array {
  $db=Database::connect(); $s=$db->prepare("SELECT m.id,m.email,m.expires_at,d.domain FROM mailboxes m JOIN telegram_users u ON u.id=m.telegram_user_id JOIN domains d ON d.id=m.domain_id WHERE u.telegram_id=? AND m.status='active' AND m.expires_at>NOW() ORDER BY m.id DESC");$s->execute([$telegramId]);return $s->fetchAll();
 }
 public function expire():int { $db=Database::connect();$rows=$db->query("SELECT id,email FROM mailboxes WHERE status='active' AND expires_at<=NOW()")->fetchAll();$n=0;foreach($rows as $r){try{$this->cpanel->deleteMailbox($r['email']);}catch(\Throwable $e){}$s=$db->prepare("UPDATE mailboxes SET status='expired' WHERE id=?");$s->execute([$r['id']]);$n++;}return $n; }
}