<?php
require __DIR__.'/_common.php';
try{db()->query('SELECT 1');respond(['ok'=>true]);}catch(Throwable $e){respond(['ok'=>false,'error'=>'Database connection failed.'],500);}
