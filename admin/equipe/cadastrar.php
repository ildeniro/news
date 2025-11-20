<?php
include("template/admin_topo.php");
?>

<?php
$perfil = "";

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0 ));
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if (!ver_nivel(1) && $_SESSION['id'] != $param) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/painel');
}

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT *  
                            FROM eve_equipes e 
                            WHERE e.id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_equipe = $result->fetch(PDO::FETCH_ASSOC);

    $membro_id = $dados_equipe['id'];
    $membro_nome = $dados_equipe['nome'];
    $membro_cargo = $dados_equipe['cargo'];
    $membro_descricao = $dados_equipe['descricao'];
    $membro_instagram = $dados_equipe['instagram'];
    $membro_facebook = $dados_equipe['facebook'];
    $membro_linkedin = $dados_equipe['linkedin'];
    $membro_foto = $dados_equipe['foto'];
} else {
    $membro_id = "";
    $membro_nome = "";
    $membro_cargo = "";
    $membro_descricao = "";
    $membro_instagram = "";
    $membro_facebook = "";
    $membro_linkedin = "";
    $membro_foto = "";
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
                                <h3 class="mb-0">Informações do Membro</h3>
                            </div>

                            <form id="form_equipe" name="form_equipe" action="#" method="POST" enctype="multipart/form-data">

                                <input type="hidden" id="id" name="id" value="<?= $membro_id ?>"/>

                                <div class="row">
                                    <!-- Nome da Membro -->
                                    <div id="div_nome" class="col-md-7 mb-3">
                                        <label for="nome" class="form-label">Nome do Membro da Equipe <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex.: Maratoma dos Amigos" required aria-required="true" value="<?= $membro_nome; ?>">
                                        <div class="invalid-feedback">Por favor, insira o nome do membro.</div>
                                    </div>


                                    <div id="div_cargo" class="col-md-5 mb-3">
                                        <label for="cargo" class="form-label">Cargo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Ex.: Coordenador" required aria-required="true" value="<?= $membro_cargo; ?>">
                                        <div class="invalid-feedback">Por favor, insira o cargo do membro.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div id="div_cargo" class="col-md-4 mb-3">
                                        <label for="cargo" class="form-label">Instagram</label>
                                        <input type="text" class="form-control" id="instagram" name="instagram" placeholder="Ex.: https://www.instagram.com/jorgeoliveira" value="<?= $membro_instagram; ?>">
                                        <div class="invalid-feedback">Por favor, insira o instagram do membro.</div>
                                    </div>

                                    <div id="div_cargo" class="col-md-4 mb-3">
                                        <label for="cargo" class="form-label">Facebook</label>
                                        <input type="text" class="form-control" id="facebook" name="facebook" placeholder="Ex.: https://www.facebook.com/jorge.oliveira" value="<?= $membro_facebook; ?>">
                                        <div class="invalid-feedback">Por favor, insira o facebook do membro.</div>
                                    </div>

                                    <div id="div_cargo" class="col-md-4 mb-3">
                                        <label for="cargo" class="form-label">Linkedin</label>
                                        <input type="text" class="form-control" id="linkedin" name="linkedin" placeholder="Ex.: https://www.linkedin.com/in/jorge-01b2a333" value="<?= $membro_linkedin; ?>">
                                        <div class="invalid-feedback">Por favor, insira o linkedin do membro.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div id="div_descricao" class="col-md-12 mb-3">
                                        <label for="descricao" class="form-label">Descrição</label>
                                        <textarea id="descricao" name="descricao" class="form-control"><?= $membro_descricao; ?></textarea>
                                        <div class="invalid-feedback">Por favor, insira uma descrição.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="foto" class="form-label">Foto do Membro</label>
                                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                        <div id="foto-preview" class="mt-2"></div>
                                        <?php if ($membro_foto): ?>
                                            <img src="<?= PORTAL_URL; ?>assets/img/equipe/<?= htmlspecialchars($membro_foto); ?>" class="img-thumbnail mt-2" style="max-width: 200px;" alt="Foto atual">
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.location.href = '<?= PORTAL_URL; ?>admin/equipe/'" aria-label="Cancelar cadastro">
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/equipes/cadastrar.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>