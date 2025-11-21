<?php
include("template/topo.php");
?>
<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->
<?php include("template/menu.php"); ?>

<!-- ============================================= -->
<!-- TICKER BREAKING NEWS (estilo G1 / CNN Brasil) -->
<!-- ============================================= -->
<div class="breaking-ticker bg-danger text-white py-2 overflow-hidden">
    <div class="container">
        <div class="d-flex align-items-center">
            <span class="badge bg-white text-danger fw-bold me-3 px-3 py-2 rounded-0">ÚLTIMAS</span>
            <div class="ticker-content">
                <?php
                $ticker = $db->prepare("SELECT title, slug FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 8");
                $ticker->execute();
                $items = $ticker->fetchAll(PDO::FETCH_ASSOC);
                foreach ($items as $item):
                ?>
                    <a href="<?= PORTAL_URL; ?>artigo/<?= $item['slug'] ?>" class="text-white text-decoration-none mx-4">
                        <?= htmlspecialchars($item['title']) ?>
                    </a>
                    <span class="text-white-50">•</span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.ticker-content {
    display: inline-block;
    white-space: nowrap;
    animation: ticker 45s linear infinite;
}
@keyframes ticker {
    0%   { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
.ticker-content:hover { animation-play-state: paused; }
</style>

<!-- ============================================= -->
<!-- HERO CARROSSEL PRINCIPAL (estilo G1 + CNN)   -->
<!-- ============================================= -->
<div class="hero-area position-relative overflow-hidden" style="min-height: 70vh; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url('<?= PORTAL_URL; ?>assets/img/hero-bg.jpg') center/cover no-repeat;">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-10 mx-auto text-center text-lg-start">
                <?php
                $stmt = $db->prepare("
                    SELECT a.content, a.id, a.title, a.slug, a.excerpt, a.featured_image, a.published_at, c.name as category_name, c.slug as category_slug
                    FROM articles a
                    JOIN categories c ON a.category_id = c.id
                    WHERE a.status = 'published'
                    ORDER BY a.published_at DESC, a.id DESC
                    LIMIT 5
                ");
                $stmt->execute();
                $destaques = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6000">
                    <div class="carousel-indicators">
                        <?php foreach ($destaques as $i => $art): ?>
                            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($destaques as $i => $art): ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <div class="text-white">
                                    <div class="d-inline-block bg-primary text-white px-3 py-1 rounded mb-3 fs-14 fw-bold">
                                        <?= strtoupper(htmlspecialchars($art['category_name'])) ?>
                                    </div>
                                    <h1 class="display-3 fw-bold mb-4 text-shadow">
                                        <?= htmlspecialchars($art['title']) ?>
                                    </h1>
                                    <p class="lead mb-4 opacity-90" style="max-width: 700px;">
                                        <?= htmlspecialchars($art['excerpt'] ?: substr(strip_tags($art['content']), 0, 200) . '...') ?>
                                    </p>
                                    <div class="d-flex flex-wrap gap-3 align-items-center">
                                        <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>" class="btn btn-lg btn-warning text-dark fw-bold px-5">
                                            <i class="ri-arrow-right-line"></i> LER AGORA
                                        </a>
                                        <small class="text-white-50 ms-3">
                                            <i class="ri-time-line"></i> <?= date('d/m/Y \à\s H\hi', strtotime($art['published_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- GRID DE NOTÍCIAS ASSIMÉTRICO (UOL + VEJA)    -->
<!-- ============================================= -->
<div class="blog-area ptb-100 bg-light">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="display-5 fw-bold text-dark">Últimas Notícias</h2>
            <p class="lead text-muted">Fique por dentro do que está acontecendo agora</p>
        </div>

        <div class="row g-4">
            <?php
            $stmt = $db->prepare("
                SELECT a.id, a.title, a.slug, a.featured_image, a.published_at, a.views,
                       c.name as category_name, c.slug as category_slug,
                       u.username as author_name
                FROM articles a
                JOIN categories c ON a.category_id = c.id
                JOIN users u ON a.author_id = u.id
                WHERE a.status = 'published'
                ORDER BY a.published_at DESC
                LIMIT 18
            ");
            $stmt->execute();
            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $count = 0;
            foreach ($noticias as $art):
                $count++;
                $isBig = ($count % 7 == 1); // A cada 7 notícias, 1 grande
            ?>
                <div class="col-lg-<?= $isBig ? '8' : '4' ?> col-md-6">
                    <article class="single-blog-card style-2 h-100 shadow-sm rounded overflow-hidden <?= $isBig ? 'border-start border-primary border-5' : '' ?>">
                        <div class="blog-image position-relative overflow-hidden">
                            <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>">
                                <img src="<?= $art['featured_image'] ? PORTAL_URL . htmlspecialchars($art['featured_image']) : PORTAL_URL . 'assets/img/default-news.jpg' ?>"
                                     alt="<?= htmlspecialchars($art['title']) ?>"
                                     class="img-fluid w-100 transition-scale"
                                     loading="lazy"
                                     style="height: <?= $isBig ? '400px' : '240px' ?>; object-fit: cover;">
                            </a>
                            <div class="tag position-absolute top-0 start-0 m-3">
                                <a href="<?= PORTAL_URL; ?>categoria/<?= $art['category_slug'] ?>" class="badge bg-<?= $art['category_slug'] === 'politica' ? 'danger' : 'primary' ?> text-white">
                                    <?= htmlspecialchars($art['category_name']) ?>
                                </a>
                            </div>
                        </div>
                        <div class="blog-content p-4">
                            <ul class="meta list-unstyled d-flex text-muted small mb-3">
                                <li class="me-4"><i class="ri-calendar-line me-1"></i> <?= date('d/m', strtotime($art['published_at'])) ?></li>
                                <li><i class="ri-eye-line me-1"></i> <?= number_format($art['views']) ?></li>
                            </ul>
                            <h3 class="<?= $isBig ? 'fs-2' : 'fs-4' ?> fw-bold mb-3">
                                <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>" class="text-dark text-decoration-none hover-primary">
                                    <?= htmlspecialchars($art['title']) ?>
                                </a>
                            </h3>
                            <?php if (!$isBig): ?>
                                <p class="text-muted small d-none d-lg-block">
                                    Por <strong><?= htmlspecialchars($art['author_name']) ?></strong>
                                </p>
                            <?php endif; ?>
                            <a href="<?= PORTAL_URL; ?>artigo/<?= $art['slug'] ?>" class="read-more text-primary fw-bold">
                                Ler notícia completa <i class="ri-arrow-right-line align-middle"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Botão Carregar Mais -->
        <div class="text-center mt-5 mb-5">
            <a href="<?= PORTAL_URL; ?>todas-as-noticias" class="btn btn-outline-danger btn-lg px-5 mb-5">
                <i class="ri-refresh-line"></i> Ver Todas as Notícias
            </a>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- ESTILOS PERSONALIZADOS DA HOME               -->
<!-- ============================================= -->
<style>
.text-shadow { text-shadow: 2px 2px 8px rgba(0,0,0,0.8); }
.transition-scale img { transition: transform 0.4s ease; }
.transition-scale:hover img { transform: scale(1.05); }
.hover-primary:hover { color: #CC0000 !important; }
.border-start { border-left-width: 8px !important; }
@media (max-width: 768px) {
    .display-3 { font-size: 2.5rem; }
    .hero-area { min-height: 60vh !important; }
    .col-lg-8 { order: -1; }
}
</style>

<!-- ============================================= -->
<!-- FIM DO CONTEÚDO DA HOME                      -->
<!-- ============================================= -->
<?php include("template/footer.php"); ?>
<?php include("template/rodape.php"); ?>

<!-- Scripts da Home -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/home.js"></script>
<script>
// Incrementa views do carrossel principal
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#mainCarousel a[href*="artigo"]').forEach(link => {
        const slug = link.href.split('/').pop();
        fetch(PORTAL_URL + 'ajax/view.php?slug=' + slug, { method: 'GET', cache: 'no-store' });
    });
});
</script>