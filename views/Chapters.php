
<div class="chapters-container">
    <div class="chapters-header">
        <h2><?php echo $data['books'][array_search($data['book'], array_column($data['books'], 'sigla'))]['nome']; ?></h2>
        <p class="chapters-info">
        <?php echo $data['versions'][array_search($data['selectedVersion'], array_column($data['versions'], 'sigla'))]['nome']; ?> - Selecione um capítulo
        </p>
    </div>

    <div class="chapters-grid">
        <?php foreach ($data['chapters'] as $chapter) : ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['book'] . '/' . $chapter['capitulo']; ?>"
               class="chapter-card">
                <span class="chapter-number"><?php echo $chapter['capitulo']; ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .chapters-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .chapters-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .chapters-header h2 {
        color: #2e4a7b;
        margin-bottom: 10px;
    }

    .chapters-info {
        color: #666;
        font-size: 0.9em;
    }

    .chapters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 15px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .chapter-card {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 60px;
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 8px;
        color: #2e4a7b;
        text-decoration: none;
        font-size: 1.2em;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .chapter-card:hover {
        background: #2e4a7b;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .chapter-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    @media (max-width: 768px) {
        .chapters-container {
            padding: 10px;
        }

        .chapters-grid {
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            padding: 15px;
        }

        .chapter-card {
            height: 50px;
            font-size: 1.1em;
        }
    }

    @media (max-width: 480px) {
        .chapters-grid {
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 8px;
            padding: 10px;
        }

        .chapter-card {
            height: 45px;
            font-size: 1em;
        }
    }
</style>
