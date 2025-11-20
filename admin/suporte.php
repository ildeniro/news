<?php
include("template/admin_topo.php");
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Sidebar Area -->
<div class="sidebar-area" id="sidebar-area">
    <?php include("template/corredor_menu.php"); ?>

    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <?php include("template/header_corredor.php"); ?>

            <div class="main-content-container overflow-hidden">
                <!-- Resumo Geral -->
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card bg-white border-0 rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                    <h3 class="mb-0">Contato de Suporte</h3>
                                </div>
                                <div class="contact-info">
                                    <p><strong>Nome:</strong> Ildeniro de Oliveira Lima</p>
                                    <p><strong>Celular/WhatsApp:</strong> 
                                        <a href="https://wa.me/55689992418392" target="_blank" rel="noopener noreferrer">
                                            (68) 999241-8392
                                        </a>
                                    </p>
                                    <p><strong>E-mail:</strong> 
                                        <a href="mailto:ildeniroo@gmail.com">ildeniroo@gmail.com</a>
                                    </p>
                                    <p><small>Clique no número do WhatsApp para iniciar uma conversa diretamente.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include("template/admin_footer.php"); ?>
    </div>
</div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>