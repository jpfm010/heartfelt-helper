<?php
if($permissao['a2'] == S){
$idempresa=(int)$_SESSION['empresa'];
$consultas=$mysqli->query("SELECT * FROM assinaturas WHERE empresa='$idempresa' ORDER BY id DESC");
$total=0;$inst=0;$bloq=0;$agu=0;
if($consultas){$total=$consultas->num_rows;$consultas->data_seek(0);while($z=$consultas->fetch_assoc()){if(($z['situacao']??'')==='I')$inst++;elseif(($z['situacao']??'')==='S')$agu++;if(($z['status']??'')!=='S')$bloq++;}$consultas->data_seek(0);}
function mrSituacao($s){$m=['S'=>'Aguardando instalação','O'=>'Orçamento','I'=>'Instalado','C'=>'Instalação cancelada','N'=>'Não encontrado','F'=>'Falta de equipamento','D'=>'Desinstalação'];return $m[$s]??$s;}
function mrClasse($s){return ['I'=>'ok','C'=>'bad','N'=>'warn','F'=>'warn','D'=>'violet','S'=>'wait'][$s]??'wait';}
?>
<style>
.mra{font-family:Arial,sans-serif}.mra-head{background:linear-gradient(135deg,#17283f,#1769bd);color:#fff;border-radius:12px;padding:22px;margin-bottom:15px;display:flex;justify-content:space-between}.mra-head h1{margin:0;font-size:25px}.mra-head p{margin:4px 0 0;opacity:.8}.mra-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:15px}.mra-stat{background:#fff;border:1px solid #e5ebf3;border-radius:11px;padding:15px}.mra-stat span{display:block;color:#718096;font-size:11px}.mra-stat b{font-size:23px;color:#17283f}.mra-table{background:#fff;border:1px solid #e5ebf3;border-radius:12px;padding:15px}.mra-table th{background:#f6f9fc;color:#526174;font-size:12px}.mra-badge{padding:5px 9px;border-radius:18px;font-size:10px;font-weight:bold;white-space:nowrap}.ok{background:#e7f8ef;color:#168a5d}.bad{background:#ffe9e9;color:#d23c3c}.warn{background:#fff3d8;color:#ad7200}.violet{background:#f0e5ff;color:#7b35b2}.wait{background:#e6f1ff;color:#2565a9}@media(max-width:800px){.mra-stats{grid-template-columns:repeat(2,1fr)}.mra-head{display:block}}@media(max-width:500px){.mra-stats{grid-template-columns:1fr}}
</style>
<div class="breadcrumb clearfix"><ul><li><a href="index.php?app=Dashboard">Dashboard</a></li><li class="active">Assinaturas</li></ul></div>
<div class="mra">
<div class="mra-head"><div><h1>Assinaturas</h1><p>Controle instalações, serviços e situação dos contratos.</p></div><a href="?app=CadastroAssinatura" class="btn btn-primary"><i class="fa fa-plus"></i> Nova assinatura</a></div>
<div class="mra-stats"><div class="mra-stat"><span>Total</span><b><?php echo $total;?></b></div><div class="mra-stat"><span>Instaladas</span><b class="text-success"><?php echo $inst;?></b></div><div class="mra-stat"><span>Aguardando instalação</span><b class="text-warning"><?php echo $agu;?></b></div><div class="mra-stat"><span>Bloqueadas</span><b class="text-danger"><?php echo $bloq;?></b></div></div>
<div class="mra-table"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px"><h3 style="margin:0">Contratos e serviços</h3><input id="mrAssBusca" class="form-control" style="max-width:330px" placeholder="Buscar login, cliente ou plano..."></div>
<div style="overflow:auto"><table class="table table-striped table-hover" id="table-1"><thead><tr><th>Login</th><th>Cliente</th><th>Plano</th><th>Servidor</th><th>Valor</th><th>Situação</th><th>Status</th><th>Ações</th></tr></thead><tbody>
<?php if($consultas) while($campo=$consultas->fetch_assoc()){
$ccid=(int)$campo['cliente'];$cliente=$mysqli->query("SELECT nome FROM clientes WHERE id='$ccid' LIMIT 1");$vcliente=$cliente?$cliente->fetch_assoc():[];
$ppid=(int)$campo['plano'];$plano=$mysqli->query("SELECT nome,preco FROM planos WHERE id='$ppid' LIMIT 1");$vplano=$plano?$plano->fetch_assoc():[];
$ssid=(int)$campo['servidor'];$servidor=$mysqli->query("SELECT servidor FROM servidores WHERE id='$ssid' LIMIT 1");$vservidor=$servidor?$servidor->fetch_assoc():[];
$valor=(float)($vplano['preco']??0);if(($campo['desconto']??'')!=='')$valor-=(float)$campo['desconto'];elseif(($campo['acrescimo']??'')!=='')$valor+=(float)$campo['acrescimo'];
$pedido=(int)$campo['pedido'];$ff=$mysqli->query("SELECT id FROM financeiro WHERE pedido='$pedido' AND situacao='N' LIMIT 1");$temAberta=$ff&&$ff->num_rows;
?>
<tr><td><strong><?php echo htmlspecialchars($campo['login']??'',ENT_QUOTES,'UTF-8');?></strong></td><td><?php echo htmlspecialchars($vcliente['nome']??'',ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars($vplano['nome']??'',ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars($vservidor['servidor']??'',ENT_QUOTES,'UTF-8');?></td><td>R$ <?php echo number_format($valor,2,',','.');?></td><td><span class="mra-badge <?php echo mrClasse($campo['situacao']??'');?>"><?php echo htmlspecialchars(mrSituacao($campo['situacao']??''),ENT_QUOTES,'UTF-8');?></span></td><td><?php if(($campo['status']??'')==='S'){?><span class="mra-badge ok">ATIVO</span><?php }else{?><span class="mra-badge bad">BLOQUEADO</span><?php }?></td><td>
<?php if(!$temAberta&&($campo['status']??'')==='S'&&($campo['insento']??'N')==='N'){?><a href="renovar.php?id=<?php echo base64_encode($campo['id']);?>" target="_blank" class="btn btn-warning btn-xs" title="Renovar"><i class="fa fa-money"></i></a><?php }?>
<?php if(($campo['situacao']??'')==='S'||($campo['situacao']??'')==='D'){?><a href="imprimir-ordem.php?id=<?php echo base64_encode($campo['pedido']);?>" target="_blank" class="btn btn-success btn-xs" title="Ordem"><i class="fa fa-print"></i></a><?php }?>
<a href="geradoc.php?id=<?php echo base64_encode($campo['id']);?>" target="_blank" class="btn btn-success btn-xs" title="Contrato"><i class="fa fa-file-text-o"></i></a>
<a href="?app=CadastroAssinatura&id=<?php echo base64_encode($campo['id']);?>" class="btn btn-info btn-xs" title="Editar"><i class="fa fa-pencil"></i></a>
<?php if($logado['nivel']=='1'){?><a href="javascript:void(0)" onclick="if(confirm('Deseja realmente excluir esse registro?'))location.href='?app=CadastroAssinatura&id=<?php echo base64_encode($campo['id']);?>&Ex=Del'" class="btn btn-danger btn-xs" title="Excluir"><i class="fa fa-trash"></i></a><?php }?>
</td></tr>
<?php } ?></tbody></table></div></div></div>
<script>document.getElementById('mrAssBusca')?.addEventListener('input',function(){var q=this.value.toLowerCase();document.querySelectorAll('#table-1 tbody tr').forEach(function(r){r.style.display=r.innerText.toLowerCase().indexOf(q)>=0?'':'none';});});</script>
<?php } else { ?><div class="alert alert-danger"><strong>Permissão negada!</strong> Você não possui permissão para este módulo.</div><?php } ?>