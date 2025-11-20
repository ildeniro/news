<?php
// views/artigo.php - Artigo único com comentários
defined('INDEX_LOADED') or die('Acesso negado!');  // Anti-hotlink

// Sanitize slug (segurança: só alfanum + hífen)
$slug = preg_replace('/[^a-z0-9-]+/', '', explode('/', $_GET['url'])[1] ?? '');
if (empty($slug)) {
    header('Location: ' . PORTAL_URL); exit;
}

// Fetch artigo + joins (prepared pra slug)
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
    echo '<div class="container my-5"><h2>Artigo não encontrado.</h2><a href="' . PORTAL_URL . '">Voltar à Home</a></div>';
    include("template/footer.php"); include("template/rodape.php"); exit;
}

// Incrementa views (uma vez por IP/session, anti-abuso)
$view_key = 'viewed_' . $artigo['id'];
if (!isset($_SESSION[$view_key])) {
    $_SESSION[$view_key] = true;
    $update_stmt = $mysqli->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
    $update_stmt->bind_param('i', $artigo['id']);
    $update_stmt->execute();
    $update_stmt->close();
}

// Fetch comentários aprovados
$comm_stmt = $mysqli->prepare("SELECT com.*, u.username FROM comments com LEFT JOIN users u ON com.user_id = u.id WHERE com.article_id = ? AND com.approved = 1 ORDER BY com.created_at DESC");
$comm_stmt->bind_param('i', $artigo['id']);
$comm_stmt->execute();
$comentarios = $comm_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$comm_stmt->close();

// Fetch related: 3 da mesma categoria
$rel_stmt = $mysqli->prepare("
    SELECT id, title, slug, featured_image
    FROM articles 
    WHERE category_id = ? AND id != ? AND status = 'published'
    ORDER BY RAND() LIMIT 3
");
$rel_stmt->bind_param('ii', $artigo['category_id'], $artigo['id']);
$rel_stmt->execute();
$related = $rel_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$rel_stmt->close();
?>

<?php include("template/topo.php"); ?>
<?php include("template/preloader.php"); ?>
<?php include("template/menu.php"); ?>

<!-- Start Article Area -->
<div class="blog-area ptb-100">
    <div class="container">
        <article class="single-blog style-2">
            <!-- Breadcrumb (SEO como Veja) -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= PORTAL_URL; ?>categoria/<?= htmlspecialchars($artigo['category_slug']) ?>"><?= htmlspecialchars($artigo['category_name']) ?></a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($artigo['title']) ?></li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-8">
                    <div class="blog-details">
                        <?php if ($artigo['featured_image']): ?>
                            <img src="<?= PORTAL_URL . htmlspecialchars($artigo['featured_image']) ?>" alt="<?= htmlspecialchars($artigo['title']) ?>" class="img-fluid mb-4 rounded">
                        <?php endif; ?>

                        <ul class="meta list-unstyled d-flex flex-wrap mb-3">
                            <li class="me-3"><i class="ri-folder-line me-1"></i><?= htmlspecialchars($artigo['category_name']) ?></li>
                            <li class="me-3"><i class="ri-calendar-line me-1"></i><?= date('d/m/Y', strtotime($artigo['published_at'])) ?></li>
                            <li><i class="ri-eye-line me-1"></i><?= number_format($artigo['views']) ?> visualizações</li>
                        </ul>

                        <h1 class="mb-4 fw-bold"><?= htmlspecialchars($artigo['title']) ?></h1>
                        <div class="author-info d-flex align-items-center mb-4">
                            <img src="<?= PORTAL_URL; ?>assets/img/avatar-default.jpg" alt="Autor" class="rounded-circle me-3" width="50" height="50">  <!-- Placeholder -->
                            <div>
                                <h6>Por <?= htmlspecialchars($artigo['author_name']) ?></h6>
                                <small>Editor(a) | <?= date('d/m/Y H:i', strtotime($artigo['published_at'])) ?></small>
                            </div>
                        </div>

                        <div class="content">
                            <?= nl2br(htmlspecialchars_decode($artigo['content'])) ?>  <!-- Preserva HTML se rich text futuro -->
                        </div>

                        <!-- Compartilhamentos (criativo: WhatsApp pra BR) -->
                        <div class="share-buttons mt-4 d-flex gap-2">
                            <a href="https://wa.me/?text=<?= urlencode($artigo['title'] . ' ' . PORTAL_URL . 'artigo/' . $slug) ?>" class="btn btn-success" target="_blank"><i class="ri-whatsapp-line"></i> WhatsApp</a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(PORTAL_URL . 'artigo/' . $slug) ?>&text=<?= urlencode($artigo['title']) ?>" class="btn btn-info" target="_blank"><i class="ri-twitter-line"></i> Twitter</a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(PORTAL_URL . 'artigo/' . $slug) ?>" class="btn btn-primary" target="_blank"><i class="ri-facebook-line"></i> Facebook</a>
                        </div>
                    </div>

                    <!-- Comentários -->
                    <section class="comments-area mt-5">
                        <h3 class="mb-4">Comentários (<?= count($comentarios) ?>)</h3>
                        <?php if (!empty($comentarios)): ?>
                            <?php foreach ($comentarios as $com): ?>
                                <div class="comment-item mb-4 p-3 border rounded">
                                    <div class="d-flex">
                                        <img src="<?= PORTAL_URL; ?>assets/img/avatar-default.jpg" alt="Usuário" class="rounded-circle me-3" width="40" height="40">
                                        <div class="flex-grow-1">
                                            <h6><?= htmlspecialchars($com['username'] ?? 'Anônimo') ?></h6>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($com['created_at'])) ?></small>
                                            <p class="mt-2"><?= nl2br(htmlspecialchars($com['content'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Seja o primeiro a comentar!</p>
                        <?php endif; ?>

                        <!-- Form de Comentário (com CSRF simples) -->
                        <?php if (isset($_SESSION['id'])):  // Só logados comentam ?>
                            <div class="comment-form mt-5">
                                <h5>Deixe seu comentário</h5>
                                <form method="POST" action="<?= PORTAL_URL; ?>ajax/comentario.php" id="commentForm">
                                    <input type="hidden" name="article_id" value="<?= $artigo['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">  <!-- Anti-CSRF -->
                                    <textarea name="content" class="form-control mb-3" rows="4" placeholder="O que achou da notícia?" required maxlength="1000"></textarea>
                                    <button type="submit" class="btn btn-primary">Enviar Comentário</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <p class="mt-3"><a href="<?= PORTAL_URL; ?>admin/login">Faça login</a> para comentar.</p>
                        <?php endif; ?>
                    </section>
                </div>

                <div class="col-lg-4">
                    <!-- Sidebar: Related Articles -->
                    <div class="sidebar-widget">
                        <h4 class="widget-title mb-3">Notícias Relacionadas</h4>
                        <?php if (!empty($related)): ?>
                            <?php foreach ($related as $rel): ?>
                                <div class="related-item mb-3 d-flex">
                                    <?php if ($rel['featured_image']): ?>
                                        <img src="<?= PORTAL_URL . htmlspecialchars($rel['featured_image']) ?>" alt="" class="me-3 rounded" width="80" height="60">
                                    <?php endif; ?>
                                    <div>
                                        <h6><a href="<?= PORTAL_URL; ?>artigo/<?= htmlspecialchars($rel['slug']) ?>"><?= htmlspecialchars($rel['title']) ?></a></h6>
                                        <small><?= date('d/m/Y', strtotime($artigo['published_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
<!-- End Article Area -->

<?php include("template/footer.php"); ?>
<?php include("template/rodape.php"); ?>

<script>
// AJAX pra comentário (sem reload, SweetAlert feedback)
document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Sucesso!', 'Comentário enviado para aprovação.', 'success');
                this.reset();
                location.reload();  // Refresh pra mostrar
            } else {
                Swal.fire('Erro!', data.message, 'error');
            }
        });
});
</script>