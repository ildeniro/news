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
                    <a class="nav-link fs-18 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4 active" href="<?= PORTAL_URL; ?>" style="color: #F17012 !important;">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>events">Eventos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>#features">Funcionalidades</a> <!-- Adicionei # pra scroll, se quiser -->
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>#testimonials">Depoimentos</a> <!-- Scroll fix -->
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>#ourteam">Nossa Equipe</a> <!-- Scroll fix -->
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4" href="<?= PORTAL_URL; ?>#contact">Contato</a> <!-- FIX: Agora #contact pra scroll suave -->
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
                    <span class="ms-1"><?= isset($_SESSION['id']) ? $nome_exibicao : "Entrar"; ?></span>
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