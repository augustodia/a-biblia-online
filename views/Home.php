<div class="home-container">
    <div class="welcome-section">
        <h2>Bem-vindo à Bíblia Online</h2>
        <p class="welcome-text">
            Selecione um livro na barra lateral para começar sua leitura.
        </p>
    </div>
</div>

<style>
    .home-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .welcome-section {
        text-align: center;
        background: #fff;
        padding: 40px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        width: 100%;
    }

    .welcome-section h2 {
        color: var(--primary-color);
        margin-bottom: 20px;
        font-size: 1.8em;
    }

    .welcome-text {
        color: var(--text-light);
        line-height: 1.6;
        font-size: 1.1em;
    }

    @media (max-width: 768px) {
        .home-container {
            padding: 15px;
        }

        .welcome-section {
            padding: 30px 20px;
        }

        .welcome-section h2 {
            font-size: 1.5em;
        }

        .welcome-text {
            font-size: 1em;
        }
    }

    @media (max-width: 480px) {
        .home-container {
            padding: 10px;
        }

        .welcome-section {
            padding: 25px 15px;
        }

        .welcome-section h2 {
            font-size: 1.3em;
        }

        .welcome-text {
            font-size: 0.95em;
        }
    }
</style>