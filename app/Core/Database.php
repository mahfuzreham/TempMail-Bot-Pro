<?php
declare(strict_types=1);
namespace TempMail\Core;
use PDO;
final class Database { private static ?PDO $pdo=null; public static function connect():PDO { if(self::$pdo)return self::$pdo; $dsn='mysql:host='.(getenv('DB_HOST')?:'127.0.0.1').';port='.(getenv('DB_PORT')?:'3306').';dbname='.(getenv('DB_NAME')?:'tempmail').';charset=utf8mb4'; return self::$pdo=new PDO($dsn,getenv('DB_USER')?:'root',getenv('DB_PASS')?:'', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); }}
