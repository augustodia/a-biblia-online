# A Bíblia Online

Sistema web para leitura e comparação de versões da Bíblia em português.

## 🚀 Deploy na Hostinger

### 1. Preparação dos Arquivos

- Remova pastas de desenvolvimento:
  ```
  .git/
  .vscode/
  tests/
  ```

### 2. Configuração do Banco de Dados

1. Acesse o painel da Hostinger
2. Vá em "Banco de Dados MySQL"
3. Crie um novo banco de dados
4. Anote as credenciais:
   - Nome do banco
   - Usuário
   - Senha

### 3. Configuração do `env.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nome_do_banco');
define('DB_USER', 'usuario_do_banco');
define('DB_PASS', 'senha_do_banco');
```

### 4. Upload dos Arquivos

1. Acesse o Gerenciador de Arquivos da Hostinger
2. Navegue até `public_html`
3. Faça upload de todos os arquivos do projeto

### 5. Configuração do `.htaccess`

```apache
RewriteEngine On
RewriteBase /

# Redirecionar www para non-www
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

# Permitir acesso aos sitemaps
RewriteRule ^sitemap\.xml$ public/sitemap.xml [L]
RewriteRule ^sitemaps/sitemap-([^/]+)\.xml$ public/sitemaps/sitemap-$1.xml [L]

# Proteção da pasta commands
RewriteCond %{REQUEST_URI} ^/commands/
RewriteCond %{REQUEST_URI} !^/commands/generate-sitemap\.php$
RewriteRule ^ - [F]

# Redirecionar todas as requisições para index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

### 6. Permissões de Pasta

```bash
chmod 755 public/sitemaps
chmod 755 public/assets
chmod 755 logs # se existir
```

### 7. Requisitos do PHP

- Versão: 7.4 ou superior
- Extensões necessárias:
  - PDO
  - PDO MySQL
  - mbstring
  - xml

### 8. Importação do Banco de Dados

1. Acesse o phpMyAdmin
2. Selecione o banco criado
3. Importe o arquivo SQL

### 9. SSL e HTTPS

1. Ative o SSL no painel da Hostinger
2. Aguarde a propagação (até 24h)

### 10. Configuração de Domínio

1. Aponte o domínio para a Hostinger
2. Configure os nameservers:
   ```
   ns1.hostinger.com
   ns2.hostinger.com
   ```

### 11. Checklist de Testes

- [ ] Página inicial carrega
- [ ] Busca funciona
- [ ] Navegação entre versículos
- [ ] Sitemaps acessíveis
- [ ] robots.txt acessível
- [ ] SSL funcionando

### 12. SEO e Monitoramento

1. Google Search Console

   - Adicione a propriedade
   - Verifique o domínio
   - Envie o sitemap

2. Google Analytics (opcional)
   - Configure a propriedade
   - Adicione o código de tracking

### 13. Backup

- Configure backup automático:
  - Banco de dados
  - Arquivos
  - Frequência recomendada: diária

### 14. Cron Jobs

Configure o gerador de sitemap:

```bash
php /home/u123456789/public_html/generate-sitemap.php
```

- Frequência: a cada 12 horas
- Ajuste o caminho `/home/u123456789/public_html` conforme seu diretório na Hostinger

### 15. Checklist de Segurança

- [ ] Arquivos de instalação removidos
- [ ] Pastas sensíveis protegidas
- [ ] Arquivos de configuração seguros
- [ ] SSL ativo
- [ ] Backups configurados

## ⚠️ Importante

- Faça backup completo antes do deploy
- Teste em subdomínio antes do domínio principal
- Guarde credenciais em local seguro
- Configure email de contato válido

## 📁 Estrutura do Projeto

```
/
├── commands/           # Scripts de comando
├── controllers/        # Controladores MVC
├── models/            # Modelos de dados
├── public/            # Arquivos públicos
│   ├── assets/        # CSS, JS, imagens
│   └── sitemaps/      # Arquivos de sitemap
├── views/             # Views MVC
└── templates/         # Templates do sistema
```

## 🔧 Manutenção

- Monitore os logs de erro
- Verifique geração de sitemaps
- Atualize o PHP quando necessário
- Mantenha backups atualizados

## 📝 Licença

Este projeto está sob a licença MIT.

## 👥 Suporte

Para suporte, envie um email para contato@abibliaonline.com
