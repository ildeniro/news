<?php
// Garantia de sessão (se não carregada no index)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Fetch categorias dinâmicas (apenas pais, pra menu principal)
$stmt = $db->prepare("
    SELECT id, name, slug 
    FROM categories 
    WHERE parent_id IS NULL AND id > 0  -- Evita categoria dummy se tiver
    ORDER BY name ASC 
    LIMIT 8
");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Start Navbar Area -->
<nav class="navbar navbar-expand-lg bg-white bg-opacity-25 fixed-top" id="navbar">
    <div class="container">
        <a class="navbar-brand me-xl-5 me-3" href="<?= PORTAL_URL; ?>">
            <img src="<?= PORTAL_URL; ?>assets/img/Logo_Cores_Orinais.png" alt="logo" style="height: 50px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link fs-18 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4 active" href="<?= PORTAL_URL; ?>" style="color: #F17012 !important;">
                        <i class="ri-home-line me-1"></i>Início
                    </a>
                </li>
                
                <!-- Categorias Dinâmicas: Loop seguro -->
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                        <li class="nav-item">
                            <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>categoria/<?= htmlspecialchars($cat['slug']) ?>">
                                <i class="ri-folder-line me-1"></i><?= htmlspecialchars($cat['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback se sem cats: Menu genérico (como seu original, mas com ícones) -->
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>#features">
                            <i class="ri-lightbulb-line me-1"></i>Funcionalidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>#testimonials">
                            <i class="ri-chat-3-line me-1"></i>Depoimentos
                        </a>
                    </li>
                <?php endif; ?>
                
                <!-- Mantém seus links fixos, mas com ícones pra consistência -->
                <li class="nav-item">
                    <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>events">
                        <i class="ri-calendar-event-line me-1"></i>Eventos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>#contact">
                        <i class="ri-mail-send-line me-1"></i>Contato
                    </a>
                </li>
            </ul>
            
            <div class="othres">
                <?php
                $nome_exibicao = "Entrar";
                if (isset($_SESSION['id']) && !empty($_SESSION['nome'])) {
                    $partes_nome = explode(' ', trim($_SESSION['nome']));
                    $nome_exibicao = $partes_nome[0]; // Pega apenas o primeiro nome
                }
                ?>
                <a href="<?= PORTAL_URL; ?>admin/painel" target="_blank" class="btn btn-outline-primary-div-entrar py-2 px-4 fw-medium fs-16 rounded-3">
                    <i class="ri-login-box-line fs-18 position-relative top-2"></i>
                    <span class="ms-1"><?= isset($_SESSION['id']) ? htmlspecialchars($nome_exibicao) : "Entrar"; ?></span>
                </a>
                <a style="<?= isset($_SESSION['id']) ? "" : "display: none"; ?>" href="<?= PORTAL_URL; ?>logout" class="btn btn-primary-div-cadastrar py-2 px-4 fw-medium fs-16 text-white ms-3 rounded-3">
                    <i class="ri-close-fill fs-18"></i>
                    <span class="ms-1">Sair</span>
                </a>
                <a style="<?= isset($_SESSION['id']) ? "display: none" : ""; ?>" href="<?= PORTAL_URL; ?>admin/cadastrar" target="_blank" class="btn btn-primary-div-cadastrar py-2 px-4 fw-medium fs-16 text-white ms-3 rounded-3">
                    <i class="ri-user-line fs-18"></i>
                    <span class="ms-1">Cadastrar</span>
                </a>
            </div>
        </div>
    </div>
</nav>
<!-- End Navbar Area -->