<?php
include("template/login_topo.php");

if (isset($_SESSION['id'])) {

    if (ver_nivel(1)) {
        header('Location: ' . PORTAL_URL . 'admin/painel');
        exit;
    } else {
        header('Location: ' . PORTAL_URL . 'admin/corredor');
        exit;
    }
}
?>

<style type="text/css">
    body
    {
        background-color: #3d3d3d;
    }
</style>

<!-- Start Preloader Area -->
<?php
include("template/preloader.php");
?>
<!-- End Preloader Area -->

<!-- Start Main Content Area -->
<div class="container">
    <div class="main-content d-flex flex-column p-0">
        <div class="m-auto m-1230">
            <div class="row align-items-center">
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="<?= PORTAL_URL; ?>assets/img/3.jpeg" class="rounded-3" alt="login-corrida">
                </div>
                <div class="col-lg-6">
                    <div class="mw-480 ms-lg-auto">
                        <div class="d-inline-block mb-4">
                            <a href="<?= PORTAL_URL; ?>"><img src="<?= PORTAL_URL; ?>assets/img/Logo_Cores_Orinais.png" class="rounded-3 for-light-logo" alt="logo-pace-run" style="height: 115px; margin-left: 45%;"></a>
                        </div>
                        <h3 class="fs-28 mb-2 text-white">Bem-vindo de volta à Pace Run!</h3>
                        <p class="fw-medium fs-16 mb-4 text-white">Faça login para gerenciar suas inscrições, acessar eventos de corrida e acompanhar seu progresso como corredor.</p>
                        <form id="form_login" name="form_login" method="post" action="#">
                            <div id="div_login" class="form-group mb-4">
                                <label class="label text-secondary" style="color: #fd5812 !important">Login</label>
                                <input type="text" id="login" name="login" class="form-control h-55" placeholder="exemplo.exemplo" required>
                            </div>
                            <div id="div_senha" class="form-group mb-4">
                                <label class="label text-secondary" style="color: #fd5812 !important">Senha</label>
                                <input type="password" id="senha" name="senha" class="form-control h-55" placeholder="Digite sua senha" required>
                            </div>
                            <div class="form-group mb-4">
                                <a href="<?= PORTAL_URL; ?>admin/recuperar" class="text-decoration-none text-primary fw-semibold" style="color: #fd5812 !important; text-decoration: underline !important;">Esqueceu a senha?</a>
                            </div>
                            <div class="form-group mb-4">
                                <button type="submit" class="btn btn-primary fw-medium py-2 px-3 w-100" style="background-color: #0000C0; border-color: #0000C0;">
                                    <div class="d-flex align-items-center justify-content-center py-1">
                                        <i class="material-symbols-outlined text-white fs-20 me-2">login</i>
                                        <span>Entrar</span>
                                    </div>
                                </button>
                            </div>
                            <div class="form-group text-white">
                                <p>Ainda não tem uma conta? <a href="<?= PORTAL_URL; ?>admin/cadastrar" class="fw-medium text-primary text-decoration-none" style="color: #fd5812 !important; text-decoration: underline !important;">Cadastre-se</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Start Main Content Area -->

<?php
include("template/login_rodape.php");
?>

<!-- JS DO LOGIN -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/admin/login.js"></script>