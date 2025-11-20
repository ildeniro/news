<?php
include("template/admin_topo.php");
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Sidebar Area -->
<div class="sidebar-area" id="sidebar-area">

    <?php
    if (ver_nivel(1)) {
        include("template/admin_menu.php");
    } else {
        include("template/corredor_menu.php");
    }
    ?>

    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            
            <?php
            if (ver_nivel(1)) {
                include("template/header.php");
            } else {
                include("template/header_corredor.php");
            }
            ?>

            <div class="main-content-container overflow-hidden">
                <!-- Resumo Geral -->
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card bg-white border-0 rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 mb-lg-30">
                                    <h3 class="mb-0">Configurações de Acesso</h3>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <form id="form_configuracoes" method="post">
                                            <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : ''; ?>">
                                            <div class="mb-3" id="div_senha_atual">
                                                <label for="senha_atual" class="form-label">Senha Atual</label>
                                                <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
                                            </div>
                                            <div class="mb-3" id="div_senha_nova">
                                                <label for="senha_nova" class="form-label">Nova Senha</label>
                                                <input type="password" class="form-control" id="senha_nova" name="senha_nova" required>
                                            </div>
                                            <div class="mb-3" id="div_confirmar_senha">
                                                <label for="confirmar_senha" class="form-label">Confirmação de Senha</label>
                                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                                            </div>
                                            <button type="submit" id="submit" class="btn btn-primary">Atualizar Senha</button>
                                        </form>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/admin/configuracoes.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>