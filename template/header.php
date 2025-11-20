<header class="header-area bg-white mb-4 rounded-bottom-15" id="header-area">
    <div class="row align-items-center">
        <div class="col-lg-4 col-sm-6">
            <div class="left-header-content">
                <ul class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
                    <li>
                        <button class="header-burger-menu bg-transparent p-0 border-0" id="header-burger-menu">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                    </li>
                    <li>
                        <form class="src-form position-relative">
                            <input type="text" class="form-control" onkeyup="buscar(this)" id="pesquisar" name="pesquisar" placeholder="Pesquisar eventos, usuários...">
                            <button type="submit" class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0">
                                <span class="material-symbols-outlined">search</span>
                            </button>
                            <div id="search-results" class="dropdown-menu p-0 border-0 dropdown-menu-start" style="display: none; position: absolute; z-index: 1000;"></div>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-8 col-sm-6">
            <div class="right-header-content mt-2 mt-sm-0">
                <ul class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
                    <li class="header-right-item">
                        <div class="light-dark">
                            <button class="switch-toggle settings-btn dark-btn p-0 bg-transparent border-0" id="switch-toggle">
                                <span class="dark"><i class="material-symbols-outlined">light_mode</i></span>
                                <span class="light"><i class="material-symbols-outlined">dark_mode</i></span>
                            </button>
                        </div>
                    </li>
                    <li class="header-right-item">
                        <button class="fullscreen-btn bg-transparent p-0 border-0" id="fullscreen-button">
                            <i class="material-symbols-outlined text-body">fullscreen</i>
                        </button>
                    </li>
                    <li class="header-right-item">
                        <div class="dropdown notifications noti">
                            <button class="btn btn-secondary border-0 p-0 position-relative badge" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="material-symbols-outlined">notifications</span>
                                <span id="noti-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                            </button>
                            <div class="dropdown-menu dropdown-lg p-0 border-0 p-0 dropdown-menu-end">
                                <div class="d-flex justify-content-between align-items-center title">
                                    <span class="fw-semibold fs-15 text-secondary">Notificações <span id="noti-count" class="fw-normal text-body fs-14">(0)</span></span>
                                    <button id="limpar-noti" onclick="limpar_notificacao()" class="p-0 m-0 bg-transparent border-0 fs-14 text-primary">Limpar Todas</button>
                                </div>
                                <div class="max-h-217" data-simplebar>
                                    <div id="noti-list" class="notification-menu">
                                        <!-- Notificações carregadas aqui -->
                                    </div>
                                </div>
                                <a href="<?= PORTAL_URL; ?>admin/notificacoes" class="dropdown-item text-center text-primary d-block view-all fw-medium rounded-bottom-3">
                                    <span>Ver Todas as Notificações</span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="header-right-item">
                        <div class="dropdown admin-profile">
                            <div class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle" data-bs-toggle="dropdown">
                                <div class="flex-shrink-0">
                                    <img class="rounded-circle wh-40 administrator" src="<?= PORTAL_URL; ?>assets/img/<?= $_SESSION['foto'] != "" && strlen($_SESSION['foto']) > 5 ? "users/" . $_SESSION['foto'] : 'picture.jpg'; ?>" alt="admin">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-none d-xxl-block">
                                            <div class="d-flex align-content-center">
                                                <h3><?= $_SESSION['nome'] ?? ''; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                                <div class="d-flex align-items-center info">
                                    <div class="flex-shrink-0">
                                        <img class="rounded-circle wh-30 administrator" src="<?= PORTAL_URL; ?>assets/img/<?= $_SESSION['foto'] != "" && strlen($_SESSION['foto']) > 5 ? "users/" . $_SESSION['foto'] : 'picture.jpg'; ?>" alt="admin">
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h3 class="fw-medium"><?= $_SESSION['nome'] ?? ''; ?></h3>
                                    </div>
                                </div>
                                <ul class="admin-link ps-0 mb-0 list-unstyled">
                                    <li>
                                        <a class="dropdown-item admin-item-link d-flex align-items-center text-body" href="<?= PORTAL_URL; ?>admin/usuario/cadastrar/<?= $_SESSION['id']; ?>">
                                            <i class="material-symbols-outlined">account_circle</i>
                                            <span class="ms-2">Meu Perfil</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item admin-item-link d-flex align-items-center text-body" href="<?= PORTAL_URL; ?>admin/configuracoes">
                                            <i class="material-symbols-outlined">settings</i>
                                            <span class="ms-2">Configurações</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item admin-item-link d-flex align-items-center text-body" href="<?= PORTAL_URL; ?>admin/logout">
                                            <i class="material-symbols-outlined">logout</i>
                                            <span class="ms-2">Sair</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/header.js"></script>