#!/bin/bash

# Configurações
LOCK_FILE="/tmp/sitemap_generator.lock"
SITE_PATH="/caminho/para/seu/projeto"
LOG_FILE="$SITE_PATH/logs/sitemap_generator.log"
MAX_EXECUTION_TIME=3600  # 1 hora em segundos

# Função para logging
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Verifica se já está rodando
if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        log "Processo já está em execução (PID: $PID)"
        exit 1
    else
        log "Arquivo de lock encontrado mas processo não existe. Removendo lock."
        rm -f "$LOCK_FILE"
    fi
fi

# Cria arquivo de lock
echo $$ > "$LOCK_FILE"

# Garante que o lock será removido ao sair
trap 'rm -f "$LOCK_FILE"; log "Script finalizado"; exit' EXIT INT TERM

# Cria diretório de logs se não existir
mkdir -p "$(dirname "$LOG_FILE")"

# Vai para o diretório do projeto
cd "$SITE_PATH" || {
    log "Erro: Não foi possível acessar o diretório $SITE_PATH"
    exit 1
}

# Executa com timeout para evitar execução infinita
log "Iniciando geração do sitemap"
timeout $MAX_EXECUTION_TIME php commands/GenerateSitemap.php >> "$LOG_FILE" 2>&1

# Verifica se gerou com sucesso
if [ $? -eq 0 ]; then
    log "Sitemap gerado com sucesso"
    
    # Ping Google
    GOOGLE_RESPONSE=$(curl -s "http://www.google.com/ping?sitemap=https://www.bibliasagrada.net.br/sitemap.xml")
    log "Resposta do Google: $GOOGLE_RESPONSE"
    
    # Ping Bing
    BING_RESPONSE=$(curl -s "http://www.bing.com/ping?sitemap=https://www.bibliasagrada.net.br/sitemap.xml")
    log "Resposta do Bing: $BING_RESPONSE"
else
    log "Erro na geração do sitemap"
    exit 1
fi 