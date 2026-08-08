<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use TempMail\Core\Env;
use TempMail\Core\Database;
Env::load(dirname(__DIR__));
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!empty($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { $error = 'Invalid request.'; }
    else {
        $db = Database::connect(); $stmt = $db->prepare('SELECT id,password_hash FROM admins WHERE username=? LIMIT 1'); $stmt->execute([trim($_POST['username'] ?? '')]); $admin = $stmt->fetch();
        if ($admin && password_verify($_POST['password'] ?? '', $admin['password_hash'])) { session_regenerate_id(true); $_SESSION['admin_id']=(int)$admin['id']; header('Location: dashboard.php'); exit; }
        $error='Invalid username or password.';
    }
}
if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><div class="container py-5"><div class="row justify-content-center"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body p-4"><h3 class="mb-4">TempMail Bot Pro</h3><?php if($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'])?>"><div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required autocomplete="username"></div><div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required autocomplete="current-password"></div><button class="btn btn-primary w-100">Login</button></form></div></div></div></div></div></body></html>