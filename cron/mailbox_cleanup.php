<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env; use TempMail\Core\Database; use TempMail\Services\CpanelService;
Env::load(dirname(__DIR__)); $db=Database::connect();
$rows=$db->query("SELECT id,email FROM mailboxes WHERE status='expired'")->fetchAll();
$cp=new CpanelService(); foreach($rows as $row){ try{$cp->deleteMailbox($row['email']);}catch(Throwable $e){} $s=$db->prepare("UPDATE mailboxes SET status='deleted' WHERE id=?"); $s->execute([$row['id']]); }
echo "Cleaned ".count($rows)." mailbox(es).\n";
