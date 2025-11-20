<?php
include("template/topo.php");
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<?php include("template/menu.php"); ?>

<!-- ====================================
===== INÍCIO DO CONTEÚDO DA HOME =====
===================================== -->

<!-- Start Hero Area -->
<div class="hero-area hero-style-two bg-cover bg-center" style="background-image: url('<?= PORTAL_URL; ?>assets/img/hero-news.jpg');">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content">
                    <?php
                    // Destaques: 3 notícias mais recentes publicadas
                    $stmt = $db->prepare("
                        SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.published_at,
                               c.name as category_name, c.slug as category_slug
                        FROM articles a
                        JOIN categories c ON a.category_id = c.id
                        WHERE a.status = 'published'
                        ORDER BY a.published_at DESC, a.id DESC
                        LIMIT 3
                    ");
                    $stmt->execute();
                    $primeiro = true;
                    ?>

                    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php while ($art = $stmt->fetchAll(PDO::FETCH_ASSOC)): ?>
                                <div class="carousel-item <?= $primeiro ? 'active' : '' ?>">
                                    <span class="badge bg-primary mb-3"><?= htmlspecialchars($art['category_name']) ?></span>
                                    <h1 class="display-4 fw-bold text-white mb-3">
                                        <?= htmlspecialchars($art['title']) ?>
                                    </h1>
                                    <p class="lead text-white-50 mb-4">
                                        <?= htmlspecialchars($art['excerpt'] ?: substr(strip_tags($art['content']), 0, 180) . '...') ?>
                                    </p>
                                    <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>" class="btn btn-primary btn-lg">
                                        Ler Notícia Completa
                                    </a>
                                    <small class="text-white-50 d-block mt-3">
                                        <?= date('d/m/Y \à\s H\hi', strtotime($art['published_at'])) ?>
                                    </small>
                                </div>
                                <?php $primeiro = false; ?>
                            <?php endwhile; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Imagem lateral ou mini-destaques (opcional no futuro) -->
                <div class="hero-image d-none d-lg-block">
                    <img src="<?= PORTAL_URL; ?>assets/img/hero-news-side.png" alt="Notícias" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Hero Area -->

<!-- Start News Grid Area -->
<div class="blog-area ptb-100">
    <div class="container">
        <div class="section-title text-center">
            <h2>Últimas Notícias</h2>
            <p>As notícias mais recentes do Brasil e do mundo</p>
        </div>

        <div class="row justify-content-center">
            <?php
            // Notícias recentes (12 por página)
            $stmt = $db->prepare("
                SELECT a.id, a.title, a.slug, a.featured_image, a.published_at, a.views,
                       c.name as category_name, c.slug as category_slug,
                       u.username as author_name
                FROM articles a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.author_id = u.id
                WHERE a.status = 'published'
                ORDER BY a.published_at DESC
                LIMIT 12
            ");

            if ($stmt->rowCount() === 0): ?>
                <div class="col-12 text-center py-5">
                    <p>Nenhuma notícia publicada ainda.</p>
                </div>
            <?php else: ?>
                <?php while ($art = $stmt->fetchAll(PDO::FETCH_ASSOC)): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-blog-card style-2 h-100">
                            <div class="blog-image">
                                <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>">
                                    <img src="<?= $art['featured_image'] ? PORTAL_URL . htmlspecialchars($art['featured_image']) : PORTAL_URL . 'assets/img/default-news.jpg' ?>" 
                                         alt="<?= htmlspecialchars($art['title']) ?>" class="img-fluid">
                                </a>
                                <div class="tag">
                                    <a href="<?= PORTAL_URL; ?>categoria/<?= $art['category_slug'] ?>">
                                        <?= htmlspecialchars($art['category_name']) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="blog-content">
                                <ul class="meta list-unstyled d-flex">
                                    <li><i class="ri-calendar-line"></i> <?= date('d/m/Y', strtotime($art['published_at'])) ?></li>
                                    <li><i class="ri-eye-line"></i> <?= number_format($art['views']) ?> visualizações</li>
                                </ul>
                                <h3>
                                    <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>">
                                        <?= htmlspecialchars($art['title']) ?>
                                    </a>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <span class="author">Por <?= htmlspecialchars($art['author_name']) ?></span>
                                    <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>" class="read-more">
                                        Ler mais <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <!-- Botão Ver Mais -->
        <div class="text-center mt-4">
            <a href="<?= PORTAL_URL; ?>todas-as-noticias" class="btn btn-primary btn-lg">
                Ver Todas as Notícias
            </a>
        </div>
    </div>
</div>
<!-- End News Grid Area -->

<!-- ====================================
======= FIM DO CONTEÚDO DA HOME =======
===================================== -->

<?php include("template/footer.php"); ?>
<?php include("template/rodape.php"); ?>

<!-- Script personalizado da home -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/home.js"></script>

<!-- Atualização: Incrementar views automaticamente nos artigos do carrossel -->
<script>
// Atualiza views sem reload (via AJAX silencioso)
document.addEventListener('DOMContentLoaded', function () {
    const carouselItems = document.querySelectorAll('#heroCarousel .carousel-item a[href*="artigo"]');
    carouselItems.forEach(link => {
        const url = link.href;
        const slug = url.split('/').pop();
        fetch(PORTAL_URL + 'ajax/view.php?slug=' + slug, { method: 'GET' });
    });
});
</script>