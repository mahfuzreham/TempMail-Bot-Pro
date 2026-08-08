<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env;
Env::load(dirname(__DIR__));
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{$pdo=TempMail\Core\Database::connect(); $sql=file_get_contents(__DIR__.'/database.sql'); $pdo->exec($sql); $message='Database schema installed successfully.';}catch(Throwable $e){$message='Installation failed: '.htmlspecialchars($e->getMessage());}
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install TempMail</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><main class="container py-5"><div class="card mx-auto" style="max-width:650px"><div class="card-body"><h2>TempMail Bot Pro Installer</h2><?php if($message):?><div class="alert alert-info"><?=$message?></div><?php endif;?><p>Configure <code>.env</code> first, then run the installer.</p><form method="post"><button class="btn btn-primary">Install Database</button></form></div></div></main></body></html>
