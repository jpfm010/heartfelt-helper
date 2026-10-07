# Instalação

Requisitos recomendados:
- Ubuntu 24.04 LTS
- PHP 8.3+
- MariaDB 10.6+ ou MySQL 8+
- Apache 2.4 ou Nginx
- extensões PHP: mysqli, mbstring, curl, openssl, json, zip, xml

Passos:
1. criar banco e usuário dedicados;
2. importar o SQL original;
3. executar sql/migracao_modernizacao.sql;
4. criar .env a partir de .env.example;
5. configurar o servidor web;
6. impedir acesso público a .env, backups/ e arquivos de teste;
7. executar php bin/healthcheck.php.
