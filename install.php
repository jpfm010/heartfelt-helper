<?php
declare(strict_types=1);
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
$root=__DIR__;
$lock=$root.'/.install.lock';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function db($h,$u,$p,$n='',$port=3306){
  mysqli_report(MYSQLI_REPORT_OFF);
  $x=@new mysqli($h,$u,$p,$n,$port);
  if($x->connect_errno)return [null,$x->connect_error];
  $x->set_charset('utf8mb4'); return [$x,null];
}
function envwrite($file,$d){
  $s="# MyRouter ERP\n";
  foreach($d as $k=>$v)$s.=$k."=".str_replace(["\\","\n","\r"],["\\\\","",""],$v)."\n";
  return @file_put_contents($file,$s,LOCK_EX)!==false;
}
function sqlfiles($root){
  $r=[];$dirs=[$root.'/sql',$root.'/install/sql'];
  foreach($dirs as $dir)if(is_dir($dir)){
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,FilesystemIterator::SKIP_DOTS));
    foreach($it as $f)if($f->isFile()&&strtolower($f->getExtension())==='sql')$r[]=$f->getPathname();
  }
  sort($r,SORT_NATURAL|SORT_FLAG_CASE);return array_values(array_unique($r));
}
$step=is_file($lock)?4:(int)($_POST['step']??1);$errors=[];$messages=[];
$req=['PHP 8.1+'=>version_compare(PHP_VERSION,'8.1','>='),'mysqli'=>extension_loaded('mysqli'),'mbstring'=>extension_loaded('mbstring'),'json'=>extension_loaded('json'),'openssl'=>extension_loaded('openssl'),'curl'=>extension_loaded('curl'),'Diretório gravável'=>is_writable($root)];
if($step===2&&!is_file($lock)){
  $h=trim($_POST['db_host']??'127.0.0.1');$port=(int)($_POST['db_port']??3306);$u=trim($_POST['db_user']??'');$p=(string)($_POST['db_pass']??'');$n=trim($_POST['db_name']??'myrouter');
  if(in_array(false,$req,true))$errors[]='Existem requisitos do servidor pendentes.';
  if($u===''||$n==='')$errors[]='Informe usuário e nome do banco.';
  if(!preg_match('/^[A-Za-z0-9_-]+$/',$n))$errors[]='Nome do banco inválido.';
  if(!$errors){
    [$x,$er]=db($h,$u,$p,'',$port);
    if(!$x)$errors[]='Falha na conexão MySQL/MariaDB: '.$er;
    else{
      if(!$x->query("CREATE DATABASE IF NOT EXISTS `".$x->real_escape_string($n)."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"))$errors[]='Não foi possível criar o banco: '.$x->error;
      $x->close();
    }
  }
  if(!$errors){
    [$x,$er]=db($h,$u,$p,$n,$port);
    if(!$x)$errors[]='Não foi possível abrir o banco: '.$er;
    else{
      $files=sqlfiles($root);
      if(!$files)$errors[]='Nenhum arquivo SQL foi encontrado em /sql. Use o pacote completo do MyRouter.';
      else{
        $total=0;$falhas=0;
        foreach($files as $file){
          $sql=@file_get_contents($file);
          if($sql===false){$falhas++;continue;}
          // O dump original pode conter CREATE DATABASE/USE myrouter.
          // O instalador deve importar sempre no banco escolhido pelo usuário.
          $sql=preg_replace('/^\\s*CREATE\\s+DATABASE(?:\\s+IF\\s+NOT\\s+EXISTS)?\\s+[^;]+;\\s*/im','',$sql);
          $sql=preg_replace('/^\\s*USE\\s+[^;]+;\\s*/im','',$sql);
          if(!$x->multi_query($sql))$falhas++;
          do{$res=$x->store_result();if($res)$res->free();if($x->errno)$falhas++;$total++;}while($x->more_results()&&$x->next_result());
        }
        $messages[]=$total.' blocos SQL processados.';
        if($falhas)$messages[]=$falhas.' arquivo(s)/bloco(s) retornaram aviso; confira a base após a instalação.';
      }
      if(!$errors){
        // Validação final: o schema principal precisa existir no banco escolhido.
        $check=$x->query("SHOW TABLES LIKE 'assinaturas'");
        if(!$check || $check->num_rows===0){
          $errors[]='A importação terminou, mas a tabela assinaturas não foi encontrada no banco selecionado.';
        }else{
        $key=bin2hex(random_bytes(32));
        $env=['MYROUTER_DB_HOST'=>$h,'MYROUTER_DB_PORT'=>(string)$port,'MYROUTER_DB_USER'=>$u,'MYROUTER_DB_PASS'=>$p,'MYROUTER_DB_NAME'=>$n,'MYROUTER_APP_KEY'=>$key];
        if(!envwrite($root.'/.env',$env))$errors[]='Não foi possível criar o arquivo .env. Verifique permissão de escrita.';
        else{
          @chmod($root.'/.env',0600);
          if(@file_put_contents($lock,date('c')."\n",LOCK_EX)===false)$errors[]='Não foi possível criar o bloqueio do instalador.';
          else{$messages[]='Instalação concluída com sucesso.';$step=4;}
        }
        }
      }
      $x->close();
    }
  }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Instalação — MyRouter ERP</title><style>
*{box-sizing:border-box}body{margin:0;background:#f2f5f9;font-family:Arial;color:#17283f}.wrap{max-width:820px;margin:45px auto;padding:18px}.head{background:linear-gradient(135deg,#10243e,#1c72c7);color:white;padding:28px;border-radius:16px 16px 0 0}.head h1{margin:0}.card{background:white;padding:26px;border-radius:0 0 16px 16px;box-shadow:0 12px 35px #10243e18}.steps{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}.step{padding:8px 12px;background:#edf1f6;border-radius:18px;font-size:12px}.on{background:#e5f1ff;color:#1264aa;font-weight:bold}.alert{padding:12px;border-radius:8px;margin:10px 0}.err{background:#fff0f0;color:#a52e2e}.ok{background:#ecfaf3;color:#13784f}.req{width:100%;border-collapse:collapse;margin:15px 0}.req td{padding:10px;border-bottom:1px solid #edf1f5}.good{color:#14885d}.bad{color:#c93434}.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field label{display:block;font-size:12px;font-weight:bold;margin-bottom:6px}.field input{width:100%;padding:11px;border:1px solid #d8e0ea;border-radius:8px}.full{grid-column:1/-1}.btn{display:inline-block;padding:11px 17px;border:0;border-radius:8px;background:#176fbe;color:white;text-decoration:none;cursor:pointer}.foot{margin-top:20px;color:#758297;font-size:12px}@media(max-width:600px){.grid{grid-template-columns:1fr}.full{grid-column:auto}}
</style></head><body><div class="wrap"><div class="head"><h1>MyRouter ERP</h1><p>Instalação automática pelo navegador</p></div><div class="card">
<div class="steps"><span class="step on">1 Requisitos</span><span class="step <?php echo $step>=2?'on':'';?>">2 Banco</span><span class="step <?php echo $step>=4?'on':'';?>">3 Instalação</span><span class="step <?php echo $step===4?'on':'';?>">4 Concluído</span></div>
<?php foreach($errors as $v):?><div class="alert err"><?php echo e($v);?></div><?php endforeach;?>
<?php foreach($messages as $v):?><div class="alert ok"><?php echo e($v);?></div><?php endforeach;?>
<?php if($step===1&&!is_file($lock)):?>
<h2>Verificação do servidor</h2><p>O instalador verifica os componentes antes de alterar qualquer configuração.</p>
<table class="req"><?php foreach($req as $k=>$v):?><tr><td><?php echo e($k);?></td><td class="<?php echo $v?'good':'bad';?>"><?php echo $v?'OK':'FALHA';?></td></tr><?php endforeach;?></table>
<?php if(!in_array(false,$req,true)):?><form method="post"><input type="hidden" name="step" value="2"><button class="btn">Continuar →</button></form><?php else:?><div class="alert err">Instale/corrija os requisitos marcados como FALHA e recarregue.</div><?php endif;?>
<?php elseif($step===2&&!is_file($lock)):?>
<h2>Banco de dados</h2><p>O banco será criado automaticamente se o usuário tiver permissão.</p><form method="post"><input type="hidden" name="step" value="2"><div class="grid">
<div class="field"><label>Host</label><input name="db_host" value="<?php echo e($_POST['db_host']??'127.0.0.1');?>"></div>
<div class="field"><label>Porta</label><input name="db_port" value="<?php echo e($_POST['db_port']??'3306');?>" type="number"></div>
<div class="field"><label>Usuário MySQL</label><input name="db_user" value="<?php echo e($_POST['db_user']??'root');?>" required></div>
<div class="field"><label>Senha MySQL</label><input name="db_pass" type="password"></div>
<div class="field full"><label>Nome do banco</label><input name="db_name" value="<?php echo e($_POST['db_name']??'myrouter');?>" required></div></div><br><button class="btn">Instalar agora</button></form>
<?php else:?>
<h2>Instalação concluída</h2><div class="alert ok">O MyRouter ERP foi instalado e o instalador está bloqueado pelo arquivo .install.lock.</div><p><strong>Recomendado:</strong> remova o <code>install.php</code> depois de confirmar o acesso ao sistema.</p><a class="btn" href="index.php">Abrir sistema →</a>
<?php endif;?><div class="foot">MyRouter ERP · <?php echo date('Y');?></div></div></div></body></html>