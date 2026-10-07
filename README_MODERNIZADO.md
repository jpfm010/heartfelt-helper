# MyRouter ERP — edição modernizada

Base legada preparada para PHP 8.3/8.4 e MySQL/MariaDB atuais, preservando os módulos originais.

## Modernização
- configuração por .env;
- mysqli/utf8mb4;
- prepared statements nos pontos modernizados;
- CSRF e sessão segura;
- migração MD5 para password_hash();
- headers de segurança;
- healthcheck;
- migration do banco;
- validação de sintaxe PHP 8+.

## Instalação
1. Copie .env.example para .env.
2. Configure banco e APP_KEY.
3. Importe o SQL original.
4. Execute sql/migracao_modernizacao.sql.
5. Aponte o DocumentRoot para o sistema.
6. Teste bin/healthcheck.php.

Não use credenciais padrão em produção. Faça backup antes das migrations.
