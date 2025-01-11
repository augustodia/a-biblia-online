<div class="error-container">
    <div class="error-content">
        <div class="error-icon">
            <i class="fas fa-book-open"></i>
        </div>
        <h1>Página não encontrada</h1>
        <p>Desculpe, mas a página que você está procurando não existe.</p>
        <div class="error-actions">
            <a href="<?php echo BASE_URL; ?>" class="home-button">
                <i class="fas fa-home"></i>
                Voltar para a página inicial
            </a>
        </div>
    </div>
</div>

<style>
.error-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.error-content {
    background: #fff;
    padding: 40px;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    width: 100%;
}

.error-icon {
    font-size: 4em;
    color: var(--primary-color);
    margin-bottom: 20px;
    animation: float 3s ease-in-out infinite;
}

.error-content h1 {
    color: var(--primary-color);
    margin-bottom: 15px;
    font-size: 2em;
}

.error-content p {
    color: var(--text-light);
    margin-bottom: 30px;
    font-size: 1.1em;
}

.error-actions {
    margin-top: 20px;
}

.home-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--primary-color);
    color: #fff;
    padding: 12px 24px;
    border-radius: var(--radius-sm);
    text-decoration: none;
    transition: all 0.3s ease;
}

.home-button:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

@media (max-width: 768px) {
    .error-container {
        padding: 20px;
    }
    
    .error-content {
        padding: 30px 20px;
    }
    
    .error-icon {
        font-size: 3em;
    }
    
    .error-content h1 {
        font-size: 1.8em;
    }
}
</style> 