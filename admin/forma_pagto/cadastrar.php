<?php
include("template/admin_topo.php");

// Handler POST
$sucesso = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = strip_tags($_POST['nome']);
    $codigo = strtolower(preg_replace('/[^a-z0-9]/', '', $_POST['codigo'])); // Sanitiza código
    $ordem = intval($_POST['ordem']);

    // Verifica duplicado
    $check = $db->prepare("SELECT id FROM eve_formas_pagamento WHERE codigo = ?");
    $check->execute([$codigo]);
    if ($check->rowCount() > 0) {
        $erro = 'Código já em uso.';
    } elseif (empty($nome) || empty($codigo)) {
        $erro = 'Preencha nome e código.';
    } else {
        $stmt_ins = $db->prepare("INSERT INTO eve_formas_pagamento (nome, codigo, ordem, status) VALUES (?, ?, ?, 1)");
        if ($stmt_ins->execute([$nome, $codigo, $ordem])) {
            $sucesso = 'Forma de pagamento cadastrada!';
            $_POST = []; // Limpa
        } else {
            $erro = 'Erro ao cadastrar.';
        }
    }
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

            <div class="main-content-container overflow-hidden">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card border-0 rounded-3">
                            <div class="card-header">
                                <h3 class="mb-0">Cadastrar Nova Forma de Pagamento</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($sucesso): ?>
                                    <div class="alert alert-success"><?= $sucesso; ?></div>
                                <?php endif; ?>
                                <?php if ($erro): ?>
                                    <div class="alert alert-danger"><?= $erro; ?></div>
                                <?php endif; ?>

                                <form action="" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Nome (ex.: Cartão de Crédito) *</label>
                                        <input type="text" name="nome" class="form-control" value="<?= $_POST['nome'] ?? ''; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Código Interno (ex.: cartao) *</label>
                                        <input type="text" name="codigo" class="form-control" value="<?= $_POST['codigo'] ?? ''; ?>" placeholder="Sem espaços, só letras/números" required>
                                        <small class="text-muted">Usado no código da inscrição.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ordem de Exibição</label>
                                        <input type="number" name="ordem" class="form-control" value="<?= $_POST['ordem'] ?? '0'; ?>" min="0">
                                        <small class="text-muted">Menor número aparece primeiro no select.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ícone (Opcional)</label>
                                        <input type="file" name="icone" class="form-control" accept="image/*">
                                        <small class="text-muted">Pra mostrar no form de pagamento.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                                    <a href="<?= PORTAL_URL; ?>admin/forma_pagto" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                                </form>
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