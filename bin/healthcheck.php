<?php
declare(strict_types=1);
require_once __DIR__.'/../config/conexao.php';
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok'=>true,'service'=>'myrouter-erp','php'=>PHP_VERSION,'database'=>'connected','time'=>date(DATE_ATOM)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
