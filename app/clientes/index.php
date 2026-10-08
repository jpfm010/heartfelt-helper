<?php
if($permissao['c1'] == S){
$idempresa=(int)$_SESSION['empresa'];
$consultas=$mysqli->query("SELECT * FROM clientes WHERE empresa='$idempresa' ORDER BY nome");
$total=0;$ativos=0;$bloq=0;
if($consultas){$total=$consultas->num_rows;$consultas->data_seek(0);while($z=$consultas->fetch_assoc()){if(($z['status']??'')==='S')$ativos++;else $bloq++;}$consultas->data_seek(0);}
?>
<style>
.mr-page{font-family:Arial,sans-serif}.mr-head{display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,#17283f,#1769bd);color:#fff;padding:22px;border-radius:12px;margin-bottom:15px}.mr-head h1{margin:0;font-size:25px}.mr-head p{margin:4px 0 0;opacity:.8}.mr-actions{display:flex;gap:8px}.mr-stat{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid #e5ebf3;border-radius:10px;padding:12px 16px;margin:0 8px 14px 0;box-shadow:0 2px 10px #17283f10}.mr-stat b{font-size:20px}.mr-table{background:#fff;border:1px solid #e5ebf3;border-radius:12px;padding:15px;box-shadow:0 2px 12px #17283f0d}.mr-table .table{margin-bottom:0}.mr-table thead th{background:#f6f9fc;border:0;color:#526174;font-size:12px}.mr-table tbody td{vertical-align:middle}.mr-status{padding:5px 9px;border-radius:20px;font-size:10px;font-weight:bold}.mr-on{background:#e7f8ef;color:#168a5d}.mr-off{background:#ffe9e9;color:#d23c3c}.mr-toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:10px}.mr-search{max-width:340px}.mr-search input{border-radius:8px;border:1px solid #dbe3ed;padding:9px 12px;width:100%}@media(max-width:700px){.mr-head,.mr-toolbar{display:block}.mr-actions{margin-top:12px}}
</style>
<div class="breadcrumb clearfix"><ul><li><a href="index.php?app=Dashboard">Dashboard</a></li><li class="active">Clientes</li></ul></div>
<div class="mr-page">
<div class="mr-head"><div><h1>Clientes</h1><p>Gerencie sua base de clientes em um só lugar.</p></div><div class="mr-actions"><a class="btn btn-primary" href="?app=CadastroCliente"><i class="fa fa-plus"></i> Novo cliente</a></div></div>
<div><div class="mr-stat"><i class="fa fa-users"></i><span>Total</span><b><?php echo $total;?></b></div><div class="mr-stat"><i class="fa fa-check-circle text-success"></i><span>Ativos</span><b><?php echo $ativos;?></b></div><div class="mr-stat"><i class="fa fa-ban text-danger"></i><span>Bloqueados</span><b><?php echo $bloq;?></b></div></div>
<div class="mr-table"><div class="mr-toolbar"><h3 style="margin:0">Base de clientes</h3><div class="mr-search"><input id="mrClienteBusca" type="search" placeholder="Buscar cliente, CPF ou telefone..."></div></div>
<div style="overflow:auto"><table class="table table-striped table-hover" id="table-1"><thead><tr><th>Nome</th><th>CPF/CNPJ</th><th>Telefone</th><th>Endereço</th><th>Cidade</th><th>Status</th><th>Ações</th></tr></thead><tbody>
<?php if($consultas) while($campo=$consultas->fetch_assoc()){ ?>
<tr><td><strong><?php echo htmlspecialchars($campo['nome']??'',ENT_QUOTES,'UTF-8');?></strong></td><td><?php echo htmlspecialchars($campo['cpf']??'',ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars($campo['tel']??'',ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars(trim(($campo['endereco']??'').' '.($campo['numero']??'').' '.($campo['bairro']??'')),ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars(($campo['cidade']??'').' '.($campo['estado']??''),ENT_QUOTES,'UTF-8');?></td><td><?php if(($campo['status']??'')==='S'){?><span class="mr-status mr-on">ATIVO</span><?php }else{?><span class="mr-status mr-off">BLOQUEADO</span><?php }?></td><td>
<a href="?app=Historico&cliente=<?php echo base64_encode($campo['id']);?>" class="btn btn-success btn-xs" title="Histórico"><i class="fa fa-file"></i></a>
<a href="?app=CadastroCliente&id=<?php echo base64_encode($campo['id']);?>" class="btn btn-info btn-xs" title="Editar"><i class="fa fa-pencil"></i></a>
<a href="consulta_serasa.php?id=<?php echo base64_encode($campo['cpf']);?>" target="_blank" class="btn btn-warning btn-xs" title="Consulta SCPC"><i class="fa fa-search"></i></a>
<?php if($logado['nivel']=='1'){?><a href="javascript:void(0)" onclick="if(confirm('Deseja realmente excluir esse registro?'))location.href='?app=CadastroCliente&id=<?php echo base64_encode($campo['id']);?>&Ex=Del'" class="btn btn-danger btn-xs" title="Excluir"><i class="fa fa-trash"></i></a><?php }?>
</td></tr>
<?php } ?></tbody></table></div></div></div>
<script>document.getElementById('mrClienteBusca')?.addEventListener('input',function(){var q=this.value.toLowerCase();document.querySelectorAll('#table-1 tbody tr').forEach(function(r){r.style.display=r.innerText.toLowerCase().indexOf(q)>=0?'':'none';});});</script>
<?php } else { ?><div class="alert alert-danger"><strong>Permissão negada!</strong> Você não possui permissão para este módulo.</div><?php } ?>