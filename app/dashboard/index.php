<?php
/** MyRouter ERP - Dashboard modernizada */
?>
<div class="breadcrumb clearfix">
  <ul><li><a href="">Dashboard</a></li><li class="active">Bem-vindo - <?php echo htmlspecialchars($logado['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></li></ul>
</div>

<?php if ($permissao['home'] == S) {
$dataAno=date('Y'); $dataMes=date('m');
function mr_count($db,$sql){$r=$db->query($sql);return $r?(int)$r->num_rows:0;}
function mr_sum($db,$sql){$r=$db->query($sql);if(!$r)return 0;$x=$r->fetch_assoc();return (float)($x['total']??0);}
function mr_money($v){return 'R$ '.number_format((float)$v,2,',','.');}

$clientes=mr_count($mysqli,"SELECT id FROM clientes");
$ativos=mr_count($mysqli,"SELECT id FROM clientes WHERE ativo='S'");
if(!$ativos)$ativos=mr_count($mysqli,"SELECT id FROM clientes WHERE situacao='A'");
$os=mr_count($mysqli,"SELECT id FROM ordemservicos WHERE encerrado='N'");
$abertas=mr_count($mysqli,"SELECT id FROM financeiro WHERE situacao='N' AND ano='$dataAno' AND mes='$dataMes'");
$aReceber=mr_sum($mysqli,"SELECT SUM(valor) total FROM financeiro WHERE situacao='N' AND ano='$dataAno' AND mes='$dataMes'");
$recebido=mr_sum($mysqli,"SELECT SUM(valor) total FROM financeiro WHERE situacao='P' AND ano='$dataAno' AND mes='$dataMes'");
$vencidos=mr_count($mysqli,"SELECT id FROM financeiro WHERE situacao='B'");
$diasBloc=0;$e=$mysqli->query("SELECT dias_bloc FROM empresa WHERE id='1' LIMIT 1");if($e&&($x=$e->fetch_assoc()))$diasBloc=(int)$x['dias_bloc'];

$meses=array('01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr','05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez');
$graf=array();$max=1;
for($i=5;$i>=0;$i--){$t=strtotime("-$i months");$y=date('Y',$t);$m=date('m',$t);$p=mr_sum($mysqli,"SELECT SUM(valor) total FROM financeiro WHERE situacao='P' AND ano='$y' AND mes='$m'");$a=mr_sum($mysqli,"SELECT SUM(valor) total FROM financeiro WHERE situacao='N' AND ano='$y' AND mes='$m'");$graf[]=array($meses[$m],$p,$a);$max=max($max,$p,$a);}
?>
<style>
.mr-dashboard{--p:#1683ff;--dark:#17283f;--muted:#718096;--border:#e5ebf3;margin:-5px 0 20px;font-family:Arial,Helvetica,sans-serif}
.mr-dashboard *{box-sizing:border-box}.mr-hero{background:linear-gradient(135deg,#17283f,#1769bd);color:#fff;border-radius:14px;padding:24px 28px;margin-bottom:16px;box-shadow:0 8px 24px #17283f18}.mr-hero h1{margin:0 0 5px;font-size:27px}.mr-hero p{margin:0;opacity:.8}.mr-date{float:right;margin-top:-35px;font-size:12px;opacity:.9}
.mr-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px}.mr-card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:17px;box-shadow:0 3px 12px #17283f0d}.mr-kpi .ico{width:40px;height:40px;border-radius:10px;background:#eaf4ff;color:var(--p);display:flex;align-items:center;justify-content:center;font-size:19px;margin-bottom:10px}.mr-kpi h4{margin:0;color:var(--muted);font-size:12px}.mr-kpi strong{display:block;font-size:24px;color:var(--dark);margin:5px 0}.mr-kpi small{color:#8a95a5}.ok{color:#129665!important}.danger{color:#dc4545!important}.warn{color:#c88900!important}
.mr-main{display:grid;grid-template-columns:2fr 1fr;gap:14px;margin-bottom:16px}.mr-title{display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);padding-bottom:11px;margin-bottom:12px}.mr-title h3{font-size:16px;margin:0;color:var(--dark)}.mr-title a{font-size:12px;color:var(--p)}.bars{height:190px;display:flex;align-items:flex-end;gap:10px;border-bottom:1px solid #dfe6ef;padding:5px 5px 0}.bar-g{height:100%;flex:1;display:flex;align-items:flex-end;gap:3px;position:relative}.bar{width:50%;border-radius:4px 4px 0 0;min-height:3px}.bar.p{background:#22b779}.bar.a{background:#328df0}.month{position:absolute;bottom:-21px;width:100%;text-align:center;font-size:10px;color:#7a8797}.legend{font-size:11px;color:#657287;margin-top:25px}.dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px}.dot.p{background:#22b779}.dot.a{background:#328df0}
.status-row{display:flex;justify-content:space-between;padding:11px 0;border-bottom:1px solid #eef2f6;font-size:13px}.status-row:last-child{border:0}.pill,.tag{font-size:10px;padding:4px 8px;border-radius:15px;background:#e8f8f1;color:#159263}.pill.w{background:#fff3d7;color:#b97800}
.mr-panels{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:16px}.list{list-style:none;margin:0;padding:0}.list li{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #eef2f6;font-size:13px}.list li:last-child{border:0}
.mr-table{width:100%;border-collapse:collapse;font-size:12px}.mr-table th{background:#f7f9fc;color:#64748b;padding:9px;text-align:left}.mr-table td{padding:9px;border-bottom:1px solid #eef2f6}.st{font-size:10px;padding:4px 8px;border-radius:14px}.st.p{background:#e8f8f1;color:#159263}.st.a{background:#fff3d7;color:#b97800}.st.b{background:#ffe8e8;color:#d13e3e}
@media(max-width:1000px){.mr-grid{grid-template-columns:repeat(2,1fr)}.mr-main,.mr-panels{grid-template-columns:1fr}}@media(max-width:600px){.mr-grid{grid-template-columns:1fr}.mr-date{float:none;margin:10px 0 0}}
</style>

<div class="mr-dashboard">
<section class="mr-hero"><div class="mr-date"><i class="fa fa-calendar"></i> <?php echo date('d/m/Y H:i'); ?></div><h1>Bem-vindo ao MyRouter ERP</h1><p>Visão geral da sua operação de provedor de internet.</p></section>

<section class="mr-grid">
<div class="mr-card mr-kpi"><div class="ico"><i class="fa fa-users"></i></div><h4>Clientes cadastrados</h4><strong><?php echo number_format($clientes,0,',','.'); ?></strong><small><?php echo number_format($ativos,0,',','.'); ?> ativos</small></div>
<div class="mr-card mr-kpi"><div class="ico"><i class="fa fa-wrench"></i></div><h4>Ordens de serviço abertas</h4><strong><?php echo $os; ?></strong><small>Atendimentos pendentes</small></div>
<div class="mr-card mr-kpi"><div class="ico"><i class="fa fa-money"></i></div><h4>A receber no mês</h4><strong><?php echo mr_money($aReceber); ?></strong><small><?php echo $abertas; ?> faturas em aberto</small></div>
<div class="mr-card mr-kpi"><div class="ico"><i class="fa fa-exclamation-triangle"></i></div><h4>Bloqueados / vencidos</h4><strong class="danger"><?php echo $vencidos; ?></strong><small>Acima de <?php echo $diasBloc; ?> dias</small></div>
</section>

<section class="mr-main">
<div class="mr-card"><div class="mr-title"><h3><i class="fa fa-bar-chart"></i> Recebimentos — últimos 6 meses</h3><span class="tag">Financeiro</span></div><div class="bars">
<?php foreach($graf as $g){$hp=max(3,round($g[1]/$max*160));$ha=max(3,round($g[2]/$max*160));?><div class="bar-g"><div class="bar p" style="height:<?php echo $hp;?>px" title="Pago <?php echo mr_money($g[1]);?>"></div><div class="bar a" style="height:<?php echo $ha;?>px" title="Aberto <?php echo mr_money($g[2]);?>"></div><span class="month"><?php echo $g[0];?></span></div><?php } ?>
</div><div class="legend"><span class="dot p"></span>Recebido <?php echo mr_money($recebido);?> &nbsp;&nbsp; <span class="dot a"></span>A receber <?php echo mr_money($aReceber);?></div></div>
<div class="mr-card"><div class="mr-title"><h3><i class="fa fa-server"></i> Status</h3><a href="?app=Equipamentos">Ver</a></div>
<div class="status-row"><span><i class="fa fa-circle ok"></i> ERP</span><span class="pill">Online</span></div><div class="status-row"><span><i class="fa fa-circle ok"></i> Banco de dados</span><span class="pill">Conectado</span></div><div class="status-row"><span><i class="fa fa-circle ok"></i> Financeiro</span><span class="pill">Operando</span></div><div class="status-row"><span><i class="fa fa-circle warn"></i> Bloqueios</span><span class="pill w"><?php echo $vencidos;?> pendentes</span></div></div>
</section>

<section class="mr-panels">
<div class="mr-card"><div class="mr-title"><h3>Faturas</h3><a href="?app=Financeiro">Ver todas</a></div><ul class="list"><li>Pagas <b class="ok"><?php echo mr_count($mysqli,"SELECT id FROM financeiro WHERE situacao='P' AND ano='$dataAno' AND mes='$dataMes'");?></b></li><li>Em aberto <b class="warn"><?php echo $abertas;?></b></li><li>Bloqueadas <b class="danger"><?php echo $vencidos;?></b></li></ul></div>
<div class="mr-card"><div class="mr-title"><h3>Ordens de serviço</h3><a href="?app=OrdemServicos">Ver todas</a></div><ul class="list"><li>Pendentes <b><?php echo $os;?></b></li><li>Prioridade <span class="tag">Acompanhar</span></li><li>Última atualização <span class="tag">Hoje</span></li></ul></div>
<div class="mr-card"><div class="mr-title"><h3>Ações rápidas</h3></div><ul class="list"><li><a href="?app=Clientes">Clientes</a><i class="fa fa-chevron-right"></i></li><li><a href="?app=Assinaturas">Assinaturas</a><i class="fa fa-chevron-right"></i></li><li><a href="?app=OrdemServicos">Nova O.S.</a><i class="fa fa-chevron-right"></i></li><li><a href="?app=Planos">Planos</a><i class="fa fa-chevron-right"></i></li></ul></div>
</section>

<section class="mr-card"><div class="mr-title"><h3><i class="fa fa-file-text-o"></i> Faturas do mês</h3><a href="?app=Financeiro">Abrir financeiro</a></div><div style="overflow-x:auto"><table class="mr-table"><thead><tr><th>Fatura</th><th>Cliente</th><th>Plano</th><th>Valor</th><th>Vencimento</th><th>Status</th><th>Ações</th></tr></thead><tbody>
<?php $q=$mysqli->query("SELECT * FROM financeiro WHERE mes='$dataMes' AND ano='$dataAno' ORDER BY id DESC LIMIT 10"); if($q) while($f=$q->fetch_assoc()){ $cr=$mysqli->query("SELECT nome FROM clientes WHERE id='".(int)$f['cliente']."' LIMIT 1");$c=$cr?$cr->fetch_assoc():array();$pr=$mysqli->query("SELECT nome FROM planos WHERE id='".(int)$f['plano']."' LIMIT 1");$p=$pr?$pr->fetch_assoc():array();$s=$f['situacao'];$sc=$s==='P'?'p':($s==='B'?'b':'a');$st=$s==='P'?'PAGO':($s==='B'?'BLOQUEADO':($s==='C'?'CANCELADO':'ABERTO'));?>
<tr><td>#<?php echo (int)$f['id'];?></td><td><?php echo htmlspecialchars($c['nome']??'',ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars($p['nome']??'',ENT_QUOTES,'UTF-8');?></td><td><?php echo mr_money($f['valor']);?></td><td><?php echo htmlspecialchars($f['dia'].'/'.$f['mes'].'/'.$f['ano'],ENT_QUOTES,'UTF-8');?></td><td><span class="st <?php echo $sc;?>"><?php echo $st;?></span></td><td><a class="btn btn-xs btn-info" href="?app=FaturaEDT&id=<?php echo base64_encode($f['id']);?>" title="Alterar"><i class="fa fa-pencil"></i></a> <?php if(!empty($f['linkGerencia'])){?><a class="btn btn-xs btn-success" target="_blank" href="<?php echo htmlspecialchars($f['linkGerencia'],ENT_QUOTES,'UTF-8');?>"><i class="fa fa-print"></i></a><?php }else{?><a class="btn btn-xs btn-warning" target="_blank" href="boleto.php?cliente=<?php echo base64_encode($f['cliente']);?>&fatura=<?php echo base64_encode($f['id']);?>&tipo=1"><i class="fa fa-money"></i></a><?php }?></td></tr>
<?php }?></tbody></table></div></section>
</div>
<?php } else { ?><div class="page-header"><h1>Permissão <small>Negada!</small></h1></div><div class="alert alert-danger"><strong>Atenção!</strong> Você não possui permissão para esse módulo.</div><?php } ?>