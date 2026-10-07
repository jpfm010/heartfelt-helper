# Arquitetura modernizada

## Camadas
- Apresentação: PHP/HTML/JS/CSS legado, preservado para reduzir risco.
- Aplicação: módulos em app/, cliente/, mobile/ e endpoints existentes.
- Infraestrutura: config/, variáveis de ambiente e conexão mysqli.
- Dados: MySQL/MariaDB + sql/.
- Integrações: MikroTik, Ubiquiti, Juniper, FiberHome, FreeRADIUS, financeiro e fiscal.

## Estratégia
A modernização é incremental. APIs SQL legadas são isoladas e serão migradas módulo a módulo, com testes de regressão.

## Prioridade
1. autenticação e autorização;
2. webservices;
3. clientes/assinaturas;
4. financeiro/boletos;
5. integrações de rede;
6. central do assinante;
7. interface responsiva/API REST.
