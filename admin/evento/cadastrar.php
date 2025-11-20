<?php
include("template/admin_topo.php");

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0));
$param = Url::getURL(3); // Assuma função Url de config
$param = $param == '' && $id != '' ? $id : $param;

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;
    $result = $db->prepare("SELECT * FROM eve_eventos WHERE id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_evento = $result->fetch(PDO::FETCH_ASSOC);

    $evento_id = $dados_evento['id'];
    $evento_nome = $dados_evento['nome'];
    $evento_descricao = $dados_evento['descricao'];
    $evento_data = $dados_evento['data_evento'];
    $evento_hora = $dados_evento['hora_evento'];
    $evento_local = $dados_evento['local'];
    $evento_categoria = $dados_evento['categoria_id'];
    $evento_img = $dados_evento['img'];
    $evento_status = $dados_evento['status'];
} else {
    $evento_id = "";
    $evento_nome = "";
    $evento_descricao = "";
    $evento_data = "";
    $evento_data = "";
    $evento_local = "";
    $evento_categoria = "";
    $evento_img = "";
    $evento_status = 1;
}
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<div class="sidebar-area" id="sidebar-area">
    <?php include("template/admin_menu.php"); ?>

    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <?php include("template/header.php"); ?>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                                <h3 class="mb-0"><?= $id == 0 ? 'Cadastrar' : 'Editar' ?> Evento</h3>
                            </div>

                            <form id="form_evento" name="form_evento" action="#" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <input type="hidden" id="id" name="id" value="<?= $evento_id ?>"/>
                                <input type="hidden" id="csrf_token" name="csrf_token" value="<?= generateCsrfToken(); ?>"/> <!-- CSRF de funcoes.php -->

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome do Evento <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex.: Maratona de Rio Branco" required value="<?= htmlspecialchars($evento_nome); ?>">
                                        <div class="invalid-feedback">Nome é obrigatório.</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="categoria" class="form-label">Categoria <span class="text-danger">*</span></label>
                                        <select class="form-select" id="categoria" name="categoria" required>
                                            <option value="" disabled selected>Selecione a categoria</option>
                                            <?php
                                            $result_cat = $db->query("SELECT * FROM eve_categorias WHERE status = 1 ORDER BY nome");
                                            while ($cat = $result_cat->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = $evento_categoria == $cat['id'] ? 'selected' : '';
                                                echo "<option value='{$cat['id']}' $selected>{$cat['nome']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Selecione a categoria.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="descricao" class="form-label">Descrição</label>
                                        <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Descreva o evento..."><?= htmlspecialchars($evento_descricao); ?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2 mb-3">
                                        <label for="data_evento" class="form-label">Data do Evento <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="data_evento" name="data_evento" required value="<?= $evento_data; ?>">
                                        <div class="invalid-feedback">Data é obrigatória.</div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="hora_evento" class="form-label">Hora do Evento <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="hora_evento" name="hora_evento" required value="<?= $evento_hora; ?>">
                                        <div class="invalid-feedback">Hora é obrigatória.</div>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="local" class="form-label">Local <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="local" name="local" placeholder="Ex.: Parque de Rio Branco" required value="<?= htmlspecialchars($evento_local); ?>">
                                        <div class="invalid-feedback">Local é obrigatório.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="img" class="form-label">Imagem do Evento (JPG, PNG, GIF - Máx 5MB)</label>
                                        <input type="file" class="form-control" id="img" name="img" accept="image/jpeg, image/png, image/gif">
                                        <div id="img-preview" class="mt-2"></div>
                                        <?php if ($evento_img): ?>
                                            <img src="<?= PORTAL_URL; ?>assets/img/eventos/<?= htmlspecialchars($evento_img); ?>" class="img-thumbnail mt-2" style="max-width: 200px;" alt="Imagem atual">
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.location.href = '<?= PORTAL_URL; ?>admin/evento/'">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" id="submit">
                                        <i class="material-symbols-outlined" style="color: white;">save</i> Salvar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1"></div>
            <?php include("template/admin_footer.php"); ?>
        </div>
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/eventos/cadastrar.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>