<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env; use TempMail\Core\Database;
Env::load(dirname(__DIR__)); header('Content-Type: application/json');
try{$email=trim((string)($_GET['email']??'')); if(!filter_var($email,FILTER_VALIDATE_EMAIL)){http_response_code(422);echo json_encode(['ok'=>false,'error'=>'Valid email is required']);exit;} $db=Database::connect(); $q=$db->prepare('SELECT e.id,e.sender,e.subject,e.body_text,e.body_html,e.received_at,e.is_read FROM emails e JOIN mailboxes m ON m.id=e.mailbox_id WHERE m.email=? AND m.status="active" ORDER BY e.received_at DESC');$q->execute([$email]);echo json_encode(['ok'=>true,'data'=>$q->fetchAll()],JSON_UNESCAPED_SLASHES);}catch(Throwable $e){http_response_code(500);echo json_encode(['ok'=>false,'error'=>'Inbox unavailable']);}
