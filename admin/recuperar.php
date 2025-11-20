<?php
include("template/login_topo.php");
?>

<style type="text/css">
    body {
        background-color: #3d3d3d;
    }
    .alert-dismissible .btn-close {
        padding: 0.75rem 1.25rem;
    }
</style>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Main Content Area -->
<div class="container">
    <div class="main-content d-flex flex-column p-0">
        <div class="m-auto m-1230">
            <div class="row align-items-center">
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="<?= PORTAL_URL; ?>assets/img/2.webp" class="rounded-3" alt="recuperar-senha-corrida">
                </div>
                <div class="col-lg-6">
                    <div class="mw-480 ms-lg-auto">
                        <div class="d-inline-block mb-4">
                            <a href="<?= PORTAL_URL; ?>"><img src="<?= PORTAL_URL; ?>assets/img/Logo_Cores_Orinais.png" class="rounded-3 for-light-logo" alt="logo-pace-run" style="height: 115px; margin-left: 45%;"></a>
                        </div>
                        <h3 class="fs-28 mb-2 text-white">Recuperar Senha</h3>
                        <p class="fw-medium fs-16 mb-4 text-white">Digite o e-mail cadastrado na Pace Run. Enviaremos um link para redefinir sua senha e acessar seus eventos.</p>
                        <?php if (isset($_SESSION['recuperar_erro'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['recuperar_erro']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['recuperar_erro']); ?>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['recuperar_sucesso'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_SESSION['recuperar_sucesso']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['recuperar_sucesso']); ?>
                        <?php endif; ?>
                        <form id="form_senha" name="form_senha" action="#" method="POST">
                            <div class="form-group mb-4">
                                <label class="label text-secondary" style="color: #fd5812 !important">Endereço de E-mail</label>
                                <input type="email" id="email" name="email" class="form-control h-55" placeholder="exemplo@pacerun.com" required>
                            </div>
                            <div class="form-group mb-4">
                                <button type="submit" id="submit" class="btn btn-primary fw-medium py-2 px-3 w-100" style="background-color: #0000C0; border-color: #0000C0;">
                                    <div class="d-flex align-items-center justify-content-center py-1">
                                        <i class="material-symbols-outlined text-white fs-20 me-2">autorenew</i>
                                        <span>Enviar</span>
                                    </div>
                                </button>
                            </div>
                            <div class="form-group">
                                <p class="text-white">Voltar para <a href="<?= PORTAL_URL; ?>admin" class="fw-medium text-primary text-decoration-none" style="color: #fd5812 !important; text-decoration: underline !important;">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Main Content Area -->

<?php include("template/login_rodape.php"); ?>

<!-- JS DO RECUPERAR -->
<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/recuperar.js"></script>