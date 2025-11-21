<?php
// views/artigo.php - Artigo único com layout jornalístico profissional
defined('INDEX_LOADED') or die('Acesso negado!');

// Sanitização segura do slug
$slug = preg_replace('/[^a-z0-9-]/', '', explode('/', $_GET['url'])[1] ?? '');
if (empty($slug)) {
    header('Location: ' . PORTAL_URL);
    exit;
}

// Busca o artigo com prepared statement (segurança total)
$stmt = $mysqli->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    JOIN users u ON a.author_id = u.id
    WHERE a.slug = ? AND a.status = 'published'
    LIMIT 1
");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$artigo = $result->fetch_assoc();
$stmt->close();

if (!$artigo) {
    http_response_code(404);
    include("template/topo.php");
    include("template/preloader.php");
    include("template/menu.php");
    echo '<div class="container py-5 text-center"><h1 class="display-4">404</h1><p class="lead">Artigo não encontrado.</p><a href="' . PORTAL_URL . '" class="btn btn-primary">Voltar à Home</a></div>';
    include("template/footer.php");
    include("template/rodape.php");
    exit;
}

// Incrementa visualizações (1 vez por sessão)
$view_key = 'viewed_article_' . $artigo['id'];
if (!isset($_SESSION[$view_key])) {
    $_SESSION[$view_key] = true;
    $upd = $mysqli->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
    $upd->bind_param('i', $artigo['id']);
    $upd->execute();
    $upd->close();
}

// Comentários aprovados
$comm_stmt = $mysqli->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.article_id = ? AND c.approved = 1 
    ORDER BY c.created_at DESC
");
$comm_stmt->bind_param('i', $artigo['id']);
$comm_stmt->execute();
$comentarios = $comm_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$comm_stmt->close();

// Notícias relacionadas (mesma categoria, exceto o atual)
$rel_stmt = $mysqli->prepare("
    SELECT id, title, slug, featured_image, published_at 
    FROM articles 
    WHERE category_id = ? AND id != ? AND status = 'published' 
    ORDER BY published_at DESC LIMIT 5
");
$rel_stmt->bind_param('ii', $artigo['category_id'], $artigo['id']);
$rel_stmt->execute();
$related = $rel_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$rel_stmt->close();
?>

<?php include("template/topo.php"); ?>
<?php include("template/preloader.php"); ?>
<?php include("template/menu.php"); ?>

<!-- ============================================= -->
<!-- HERO DO ARTIGO (estilo VEJA + CNN Brasil)    -->
<!-- ============================================= -->
<div class="article-hero position-relative text-white" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('<?= $artigo['featured_image'] ? PORTAL_URL . htmlspecialchars($artigo['featured_image']) : PORTAL_URL . 'assets/img/default-hero.jpg' ?>') center/cover no-repeat; min-height: 70vh; display: flex; align-items: end;">
    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent text-white-50">
                        <li class="breadcrumb-item"><a href="<?= PORTAL_URL ?>" class="text-white-50">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= PORTAL_URL ?>categoria/<?= $artigo['category_slug'] ?>" class="text-white-50"><?= htmlspecialchars($artigo['category_name']) ?></a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Artigo</li>
                    </ol>
                </nav>
                <span class="badge bg-danger fs-6 mb-3 px-4 py-2">EXCLUSIVO</span>
                <h1 class="display-3 fw-bold mb-4 text-shadow"><?= htmlspecialchars($artigo['title']) ?></h1>
                <div class="d-flex flex-wrap align-items-center gap-4 text-white-50 fs-5">
                    <div><i class="ri-user-line"></i> Por <strong><?= htmlspecialchars($artigo['author_name']) ?></strong></div>
                    <div><i class="ri-calendar-line"></i> <?= date('d \d\e F \d\e Y', strtotime($artigo['published_at'])) ?></div>
                    <div><i class="ri-eye-line"></i> <?= number_format($artigo['views'] + 1) ?> visualizações</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- CONTEÚDO PRINCIPAL + SIDEBAR                -->
<!-- ============================================= -->
<div class="blog-area ptb-100 bg-light">
    <div class="container">
        <div class="row">
            <!-- Artigo Principal -->
            <div class="col-lg-8">
                <article class="single-blog style-2 bg-white shadow-sm rounded-3 overflow-hidden">
                    <div class="p-5">
                        <!-- Autor com foto -->
                        <div class="author-box d-flex align-items-center mb-5 p-4 bg-light rounded">
                            <img src="<?= PORTAL_URL ?>assets/img/avatar-default.jpg" alt="Autor" class="rounded-circle me-4" width="80" height="80">
                            <div>
                                <h5 class="mb-1"><?= htmlspecialchars($artigo['author_name']) ?></h5>
                                <small class="text-muted">Redator(a) | Publicado em <?= date('d/m/Y \à\s H\hi', strtotime($artigo['published_at'])) ?></small>
                            </div>
                        </div>

                        <!-- Conteúdo do artigo -->
                        <div class="article-content lead fs-4 lh-lg text-dark">
                            <?= $artigo['content'] // Já permite HTML (use TinyMCE no admin) ?>
                        </div>

                        <!-- Tags (se implementar no futuro) -->
                        <div class="tags mt-5">
                            <span class="fw-bold me-2">Tags:</span>
                            <a href="#" class="badge bg-secondary text-white me-2">Política</a>
                            <a href="#" class="badge bg-secondary text-white me-2">Brasil</a>
                        </div>

                        <!-- Compartilhamento -->
                        <div class="share-section mt-5 p-4 bg-light rounded d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Compartilhe:</span>
                            <div class="d-flex gap-3">
                                <a href="https://wa.me/?text=<?= urlencode($artigo['title'] . ' - ' . PORTAL_URL . 'artigo/' . $slug) ?>" target="_blank" class="btn btn-success btn-lg rounded-circle"><i class="ri-whatsapp-line fs-4"></i></a>
                                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(PORTAL_URL . 'artigo/' . $slug) ?>&text=<?= urlencode($artigo['title']) ?>" target="_blank" class="btn btn-info btn-lg rounded-circle text-white"><i class="ri-twitter-line fs-4"></i></a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(PORTAL_URL . 'artigo/' . $slug) ?>" target="_blank" class="btn btn-primary btn-lg rounded-circle"><i class="ri-facebook-fill fs-4"></i></a>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- ============================================= -->
                <!-- SEÇÃO DE COMENTÁRIOS                        -->
                <!-- ============================================= -->
                <section class="comments-section mt-5 bg-white p-5 rounded-3 shadow-sm">
                    <h3 class="mb-4 border-bottom pb-3"><i class="ri-message-3-line"></i> Comentários (<?= count($comentarios) ?>)</h3>

                    <?php if (!empty($comentarios)): ?>
                        <?php foreach ($comentarios as $com): ?>
                            <div class="comment-item d-flex mb-4 pb-4 border-bottom">
                                <img src="<?= PORTAL_URL ?>assets/img/avatar-default.jpg" alt="Usuário" class="rounded-circle me-3 flex-shrink-0" width="50" height="50">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-0"><?= htmlspecialchars($com['username'] ?? 'Anônimo') ?></h6>
                                        <small class="text-muted"><?= date('d/m/Y \à\s H\hi', strtotime($com['created_at'])) ?></small>
                                    </div>
                                    <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($com['content'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted fst-italic">Seja o primeiro a comentar esta notícia!</p>
                    <?php endif; ?>

                    <!-- Formulário de Comentário -->
                    <?php if (isset($_SESSION['id'])): ?>
                        <div class="comment-form mt-5 p-4 bg-light rounded">
                            <h5>Deixe seu comentário</h5>
                            <form id="commentForm" action="<?= PORTAL_URL ?>ajax/comentario.php" method="POST">
                                <input type="hidden" name="article_id" value="<?= $artigo['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32)) ?>">
                                <div class="mb-3">
                                    <textarea name="content" class="form-control" rows="5" placeholder="Escreva seu comentário..." required maxlength="1000"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">Enviar Comentário</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-4">
                            <i class="ri-login-box-line"></i> <a href="<?= PORTAL_URL ?>admin/login" class="alert-link">Faça login</a> para comentar.
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- ============================================= -->
            <!-- SIDEBAR - NOTÍCIAS RELACIONADAS             -->
            <!-- ============================================= -->
            <div class="col-lg-4">
                <div class="sidebar-sticky" style="top: 100px;">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="ri-fire-line"></i> Notícias Relacionadas</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($related as $rel): ?>
                                <a href="<?= PORTAL_URL ?>artigo/<?= $rel['slug'] ?>" class="d-block p-3 border-bottom hover-bg-light text-decoration-none">
                                    <div class="d-flex">
                                        <?php if ($rel['featured_image']): ?>
                                            <img src="<?= PORTAL_URL . htmlspecialchars($rel['featured_image']) ?>" alt="" class="me-3 rounded" width="90" height="70" loading="lazy">
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1 text-dark"><?= htmlspecialchars($rel['title']) ?></h6>
                                            <small class="text-muted"><i class="ri-time-line"></i> <?= date('d/m/Y', strtotime($rel['published_at'])) ?></small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Banner lateral (futuro) -->
                    <div class="card bg-dark text-white text-center p-4">
                        <h5>Anuncie Aqui</h5>
                        <p>Entre em contato para publicidade</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos específicos do artigo -->
<style>
.article-hero { background-attachment: fixed; }
.text-shadow { text-shadow: 3px 3px 10px rgba(0,0,0,0.8); }
.article-content img { max-width: 100%; border-radius: 12px; margin: 20px 0; }
.comment-item:hover { background: #f8f9fa; }
.sidebar-sticky { position: sticky; top: 100px; }
.hover-bg-light:hover { background: #f8f9fa !important; transition: 0.3s; }
@media (max-width: 992px) {
    .article-hero { min-height: 50vh; background-attachment: scroll; }
    .display-3 { font-size: 2.5rem; }
}
</style>

<?php include("template/footer.php"); ?>
<?php include("template/rodape.php"); ?>

<!-- AJAX para comentários -->
<script>
document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Sucesso!', 'Seu comentário foi enviado e está aguardando aprovação.', 'success');
            this.reset();
        } else {
            Swal.fire('Erro', data.message || 'Falha ao enviar comentário.', 'error');
        }
    })
    .catch(() => {
        Swal.fire('Erro', 'Falha na conexão. Tente novamente.', 'error');
    });
});
</script>