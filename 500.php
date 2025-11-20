<?php
include("template/topo.php");
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<?php include("template/menu.php"); ?>

<!-- Start 500 Banner Area -->
<div class="banner-area bg-img pb-0" style="background-image: url('<?= PORTAL_URL; ?>assets/img/500.jpng'); background-size: cover; background-position: center;">
    <div class="container position-relative z-1">
        <div class="banner-content text-center pb-75">
            <h1 class="fs-60 mb-3 pb-md-3 text-white">500 - Erro Interno</h1>
            <p class="fs-18 m-auto mb-3 pb-md-3 mw-740 text-white">Estamos enfrentando um problema técnico. Pedimos desculpas pelo inconveniente.</p>
        </div>
    </div>
</div>
<!-- End 500 Banner Area -->

<!-- Start 500 Content Area -->
<div class="content-area pt-125 pb-125" style="background: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg border-0 rounded-4 p-4 text-center">
                    <div class="card-body">
                        <i class="ri-bug-line fs-60 text-warning mb-4"></i>
                        <h3 class="mb-3">Erro Interno do Servidor</h3>
                        <p class="text-muted mb-4">Parece que algo deu errado no nosso servidor. Nossa equipe já foi notificada e está trabalhando para resolver o problema o mais rápido possível.</p>
                        <a href="<?= PORTAL_URL; ?>" class="btn btn-primary w-100 py-3 fw-semibold fs-16">
                            <i class="ri-home-line me-2"></i> Voltar para a Página Inicial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End 500 Content Area -->

<!-- Start CSS Inline -->
<style>
    .banner-area {
        min-height: 400px;
        display: flex;
        align-items: center;
    }
    .content-area .card {
        border-radius: 20px;
    }
    .btn-primary {
        background-color: #0000C0;
        border-color: #0000C0;
    }
    .btn-primary:hover {
        background-color: #fd5812;
        border-color: #fd5812;
    }
</style>

<?php include("template/footer.php"); ?>
<?php include("template/rodape.php"); ?>