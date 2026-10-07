# Segurança

Nunca publique .env, backups, credenciais ou senhas padrão.

Antes da produção:
- altere todas as credenciais;
- use HTTPS;
- restrinja backups;
- desative arquivos de teste;
- use usuário de banco dedicado sem privilégios administrativos;
- faça backup antes das migrations.

Vulnerabilidades devem ser tratadas antes da exposição pública do painel.
