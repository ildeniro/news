<?php
include("template/admin_topo.php");
?>

<?php
$perfil = "";

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0 ) );
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if (!ver_nivel(1) && $_SESSION['id'] != $param) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/painel');
}

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT *  
                 FROM eve_categorias c 
                 WHERE c.id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_categoria = $result->fetch(PDO::FETCH_ASSOC);

    $categoria_id = $dados_categoria['id'];
    $categoria_nome = $dados_categoria['nome'];
} else {
    $categoria_id = "";
    $categoria_nome = "";
}
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Sidebar Area -->
<div class="sidebar-area" id="sidebar-area">
    <!-- Menu -->
    <?php include("template/admin_menu.php"); ?>
    <!-- Fim Menu -->

    <!-- Start Main Content Area -->
    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <!-- Start Header Area -->
            <?php include("template/header.php"); ?>
            <!-- End Header Area -->

            <!-- Formulário de Cadastro de Usuários -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                                <h3 class="mb-0">Informações da Categoria</h3>
                            </div>

                            <form id="form_categoria" name="form_categoria" action="#" method="POST">

                                <input type="hidden" id="id" name="id" value="<?= $categoria_id ?>"/>

                                <div class="row">
                                    <!-- Nome da Categoria -->
                                    <div id="div_nome" class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex.: Maratoma dos Amigos" required aria-required="true" value="<?= $categoria_nome; ?>">
                                        <div class="invalid-feedback">Por favor, insira o nome da categoria.</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.location.href = '<?= PORTAL_URL; ?>admin/categoria/'" aria-label="Cancelar cadastro">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary" aria-label="Salvar evento">
                                        <i class="material-symbols-outlined" style="color: white;">save</i> Salvar
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fim Formulário de Cadastro de Eventos -->

        <div class="flex-grow-1"></div>

        <!-- Start Footer Area -->
        <?php include("template/admin_footer.php"); ?>
        <!-- End Footer Area -->
    </div>
</div>
<!-- End Main Content Area -->

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/categorias/cadastrar.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>