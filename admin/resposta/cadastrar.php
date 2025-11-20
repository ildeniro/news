<?php
include("template/admin_topo.php");

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0 ) );
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT *  
                 FROM eve_respostas u 
                 WHERE u.id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_resposta = $result->fetch(PDO::FETCH_ASSOC);

    $resposta_id = $dados_resposta['id'];
    $pergunta = $dados_resposta['pergunta'];
    $resposta = $dados_resposta['resposta'];
} else {
    $resposta_id = "";
    $pergunta = "";
    $resposta = "";
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

            <!-- Formulário de Cadastro/Edição -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <h3 class="mb-4"><?= is_numeric($resposta_id) ? 'Editar' : 'Cadastrar'; ?> Resposta</h3>
                            <form id="form_resposta" name="form_resposta" action="#" method="POST">

                                <input type="hidden" id="id" name="id" value="<?= $resposta_id ?>"/>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Pergunta *</label>
                                    <input type="text" id="pergunta" name="pergunta" class="form-control" value="<?= $pergunta; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Resposta *</label>
                                    <textarea id="resposta" name="resposta" class="form-control" rows="5" required><?= $resposta; ?></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                    <a href="<?= PORTAL_URL; ?>admin/resposta" class="btn btn-secondary">Voltar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Fim Formulário -->

                <div class="flex-grow-1"></div>

                <!-- Start Footer Area -->
                <?php include("template/admin_footer.php"); ?>
                <!-- End Footer Area -->
            </div>
        </div>
        <!-- End Main Content Area -->
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/respostas/cadastrar.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>