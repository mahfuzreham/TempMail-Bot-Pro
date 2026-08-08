<?php
declare(strict_types=1); require dirname(__DIR__).'/vendor/autoload.php'; use TempMail\Core\Env;use TempMail\Core\Database; Env::load(dirname(__DIR__)); $n=Database::connect()->exec("UPDATE mailboxes SET status='expired' WHERE status='active' AND expires_at IS NOT NULL AND expires_at<=NOW()"); echo "Expired {$n} mailbox(es).\n";
