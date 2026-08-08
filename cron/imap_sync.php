<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env;
use TempMail\Core\Database;
use TempMail\Services\ImapService;
Env::load(dirname(__DIR__));
$db=Database::connect();
$rows=$db->query("SELECT id,email,password_hash FROM mailboxes WHERE status='active' AND expires_at>NOW()")->fetchAll();
$imap=new ImapService();
foreach($rows as $m){
 try {
  $host=getenv('IMAP_HOST')?:''; $port=(int)(getenv('IMAP_PORT')?:993); $enc=getenv('IMAP_ENCRYPTION')?:'ssl';
  $box='{'.$host.':'.$port.'/'.strtolower($enc).'/novalidate-cert}INBOX';
  foreach($imap->fetch($box,$m['email'],$m['password_hash']) as $mail){
   $s=$db->prepare('INSERT IGNORE INTO emails(mailbox_id,message_uid,sender,subject,body_text,received_at) VALUES(?,?,?,?,?,?)');
   $s->execute([$m['id'],(string)$mail['uid'],$mail['from'],$mail['subject'],$mail['body'],$mail['date']?date('Y-m-d H:i:s',strtotime($mail['date'])):null]);
  }
 } catch(Throwable $e) { continue; }
}
echo "IMAP sync complete.\n";
