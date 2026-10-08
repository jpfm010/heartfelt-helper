<?php
/**
 * MyRouter ERP - API de monitoramento MikroTik
 * Retorna apenas dados básicos e não altera configuração do roteador.
 */
declare(strict_types=1);
require_once __DIR__.'/../../config/conexao.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$id=isset($_GET['id'])?(int)$_GET['id']:0;
if($id<1){http_response_code(400);echo json_encode(['ok'=>false,'error'=>'Servidor inválido']);exit;}
$r=$mysqli->query("SELECT id,servidor,ip,login,tiporouter FROM servidores WHERE id='$id' LIMIT 1");
$s=$r?$r->fetch_assoc():null;
if(!$s){http_response_code(404);echo json_encode(['ok'=>false,'error'=>'Servidor não encontrado']);exit;}

$host=$s['ip'];$port=8728;$started=microtime(true);$errno=0;$err='';
$socket=@fsockopen($host,$port,$errno,$err,2.0);
$latency=round((microtime(true)-$started)*1000,1);
$online=(bool)$socket;if($socket)fclose($socket);

echo json_encode([
 'ok'=>true,
 'server'=>['id'=>(int)$s['id'],'name'=>$s['servidor'],'ip'=>$s['ip'],'type'=>$s['tiporouter']],
 'connectivity'=>['online'=>$online,'latency_ms'=>$latency,'api_port'=>$port,'checked_at'=>date(DATE_ATOM)],
 'metrics'=>['cpu'=>null,'memory'=>null,'uptime'=>null,'pppoe_online'=>null],
 'note'=>$online?'Conectividade confirmada. Métricas RouterOS detalhadas dependem da API compatível instalada.':'Sem comunicação com a API do MikroTik.'
],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);