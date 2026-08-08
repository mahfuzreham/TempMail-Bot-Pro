<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env;
Env::load(dirname(__DIR__));
header('Content-Type: text/html; charset=utf-8');
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>TempMail Bot Pro</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><main class="container py-5"><div class="card shadow-sm"><div class="card-body"><h1>TempMail Bot Pro</h1><p class="text-secondary">Telegram temporary email platform.</p><a class="btn btn-primary" href="/admin/">Admin</a></div></div></main></body></html>
