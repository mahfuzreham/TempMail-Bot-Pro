<?php
declare(strict_types=1);
namespace TempMail\Core;
use Dotenv\Dotenv;
final class Env { public static function load(string $root):void{ if(is_file($root.'/.env')) Dotenv::createImmutable($root)->safeLoad(); }}
