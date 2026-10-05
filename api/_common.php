<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function respond($data, int $status=200): never { http_response_code($status); echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function body_json(): array { $x=json_decode(file_get_contents('php://input'),true); return is_array($x)?$x:[]; }
function token_from_query(): string { return trim((string)($_GET['token'] ?? '')); }
