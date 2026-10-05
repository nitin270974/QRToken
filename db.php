<?php
function app_config(): array {
    static $cfg = null;
    if ($cfg !== null) return $cfg;
    $file = __DIR__ . '/config.php';
    if (!file_exists($file)) throw new RuntimeException('Missing config.php. Copy config.example.php to config.php and edit it.');
    $cfg = require $file;
    return $cfg;
}
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $c = app_config();
    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;sslmode=%s', $c['db_host'],$c['db_port'],$c['db_name'],$c['db_sslmode'] ?? 'require');
    $pdo = new PDO($dsn,$c['db_user'],$c['db_pass'],[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false
    ]);
    return $pdo;
}
