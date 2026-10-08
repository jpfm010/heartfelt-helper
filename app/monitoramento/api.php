<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
session_start();
require_once __DIR__.'/../../config/conexao.php';
require_once __DIR__.'/../../config/mikrotik.class.php';
function mr_out(array $d,int $s=200):void{http_response_code($s);echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
if(!isset($_SESSION['login']))mr_out(['ok'=>false,'error'=>'Não autenticado'],401);
$db=$mysqli??($bd??null);if(!($db instanceof mysqli))mr_out(['ok'=>false,'error'=>'Banco de dados indisponível'],500);
$id=(int)($_GET['id']??0);$action=trim((string)($_GET['action']??'summary'));if($id<1)mr_out(['ok'=>false,'error'=>'Servidor inválido'],400);
$q=$db->prepare("SELECT id,empresa,servidor,ip,porta,login,senha,interface,tiporouter FROM servidores WHERE id=? LIMIT 1");if(!$q)mr_out(['ok'=>false,'error'=>'Falha no banco'],500);$q->bind_param('i',$id);$q->execute();$s=$q->get_result()->fetch_assoc();$q->close();
if(!$s)mr_out(['ok'=>false,'error'=>'Servidor não encontrado'],404);
if(isset($_SESSION['empresa'])&& (string)$s['empresa']!==(string)$_SESSION['empresa'])mr_out(['ok'=>false,'error'=>'Acesso negado'],403);
$port=(int)($s['porta']?:8728);if($port<1||$port>65535)$port=8728;$t=microtime(true);$api=new routeros_api();$api->debug=false;$api->port=$port;
if(!$api->connect($s['ip'],$s['login'],$s['senha']))mr_out(['ok'=>true,'online'=>false,'latency_ms'=>round((microtime(true)-$t)*1000,1),'server'=>['id'=>(int)$s['id'],'name'=>$s['servidor'],'ip'=>$s['ip']],'error'=>'Falha de autenticação/conexão RouterOS']);
$lat=round((microtime(true)-$t)*1000,1);
if($action==='traffic'){ $name=trim((string)($_GET['interface']??''));if($name===''){ $api->disconnect();mr_out(['ok'=>false,'error'=>'Interface não informada'],400);} $api->write('/interface/monitor-traffic',false);$api->write('=interface='.$name,false);$api->write('=once=',true);$a=$api->read();$api->disconnect();$x=$a[0]??[];mr_out(['ok'=>true,'interface'=>$name,'rx_bps'=>(int)($x['rx-bits-per-second']??0),'tx_bps'=>(int)($x['tx-bits-per-second']??0),'time'=>date(DATE_ATOM)]); }
try{$resource=$api->comm('/system/resource/print');$identity=$api->comm('/system/identity/print');$ppp=$api->comm('/ppp/active/print');$interfaces=$api->comm('/interface/print');$api->disconnect();}catch(Throwable $e){if($api->connected)$api->disconnect();mr_out(['ok'=>false,'error'=>'Erro ao consultar RouterOS'],502);}
$r=$resource[0]??[];$ident=$identity[0]??[];$total=(int)($r['total-memory']??0);$free=(int)($r['free-memory']??0);$mem=$total?round((1-$free/$total)*100,1):null;
$ifs=[];foreach($interfaces as $i)$ifs[]=['id'=>$i['.id']??null,'name'=>$i['name']??'','type'=>$i['type']??'','running'=>($i['running']??'false')==='true','disabled'=>($i['disabled']??'false')==='true'];
$users=[];foreach($ppp as $p)$users[]=['id'=>$p['.id']??null,'name'=>$p['name']??'','service'=>$p['service']??'','address'=>$p['address']??'','caller_id'=>$p['caller-id']??'','uptime'=>$p['uptime']??''];
mr_out(['ok'=>true,'online'=>true,'latency_ms'=>$lat,'server'=>['id'=>(int)$s['id'],'name'=>$s['servidor'],'ip'=>$s['ip'],'port'=>$port,'identity'=>$ident['name']??$s['servidor']],'resource'=>['version'=>$r['version']??null,'board'=>$r['board-name']??null,'cpu'=>$r['cpu']??null,'cpu_load'=>isset($r['cpu-load'])?(int)$r['cpu-load']:null,'memory_used_percent'=>$mem,'free_memory'=>$free,'total_memory'=>$total,'uptime'=>$r['uptime']??null,'hdd_free'=>(int)($r['free-hdd-space']??0),'hdd_total'=>(int)($r['total-hdd-space']??0)],'pppoe_online'=>count($users),'pppoe'=>$users,'interfaces'=>$ifs,'checked_at'=>date(DATE_ATOM)]);
