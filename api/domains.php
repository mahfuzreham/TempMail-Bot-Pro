<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env; use TempMail\Core\Database;
Env::load(dirname(__DIR__)); header('Content-Type: application/json');
try{$rows=Database::connect()->query("SELECT id,domain FROM domains WHERE active=1 ORDER BY domain")->fetchAll(); echo json_encode(['ok'=>true,'data'=>$rows],JSON_UNESCAPED_SLASHES);}catch(Throwable $e){http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Internal server error']);}
