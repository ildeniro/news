<?php
include("template/admin_topo.php");

include_once('config/geral.php');
$db = Conexao::getInstance();

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('Location: ' . PORTAL_URL . 'admin');
    exit;
}

// Filtros
$busca = $_GET['busca'] ?? '';
$status_filter = $_GET['status'] ?? 'ativos';
$where = "WHERE 1=1";
$params = [];
if ($busca) {
    $where .= " AND nome LIKE ?";
    $params[] = "%$busca%";
}
if ($status_filter == 'inativos') {
    $where .= " AND status = 0";
} else {
    $where .= " AND status = 1";
}

// Lista
$stmt = $db->prepare("SELECT * FROM eve_formas_pagamento $where ORDER BY ordem ASC");
$stmt->execute($params);
$formas_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_formas = count($formas_result);
$cont = 1;
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Sidebar Area -->
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
                                <h3 class="mb-0">Formas de Pagamento Cadastradas</h3>
                                <div class="d-flex align-items-center gap-3">
                                    <form class="position-relative table-src-form" method="GET">
                                        <input type="text" name="busca" class="form-control" placeholder="Pesquisar formas..." value="<?= htmlspecialchars($busca); ?>">
                                        <i class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y">search</i>
                                    </form>
                                    <select class="form-select month-select form-control" name="status" aria-label="Filtro de status" onchange="this.form.submit();">
                                        <option value="ativos" <?= $status_filter == 'ativos' ? 'selected' : ''; ?>>Ativas</option>
                                        <option value="inativos" <?= $status_filter == 'inativos' ? 'selected' : ''; ?>>Inativas</option>
                                    </select>
                                </div>
                            </div>

                            <?php if (isset($_GET['msg'])): ?>
                                <div class="alert <?= $_GET['msg'] == 'sucesso' ? 'alert-success' : 'alert-danger'; ?> mb-4"><?= $_GET['msg'] == 'sucesso' ? 'Ação realizada com sucesso!' : 'Erro ao processar ação.'; ?></div>
                            <?php endif; ?>

                            <div class="default-table-area">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nome da Forma</th>
                                                <th scope="col">Código</th>
                                                <th scope="col">Ordem</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Cadastro</th>
                                                <th scope="col">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($formas_result)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Nenhuma forma encontrada.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php
                                                foreach ($formas_result as $forma):
                                                    $status_label = $forma['status'] == 1 ? 'Ativa' : 'Inativa';
                                                    $status_color = $forma['status'] == 1 ? 'bg-success' : 'bg-danger';
                                                    ?>
                                                    <tr>
                                                        <td><?= $cont; ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <?php if (isset($forma['icone']) && $forma['icone']): ?>
                                                                    <img src="<?= PORTAL_URL; ?>assets/icons/<?= $forma['icone']; ?>" class="wh-40 rounded-3 me-2" alt="ícone">
                                                                <?php endif; ?>
                                                                <div class="ms-2 ps-1">
                                                                    <h6 class="fw-medium fs-14"><?= htmlspecialchars($forma['nome']); ?></h6>
                                                                    <span class="fs-12 text-body"><?= substr($forma['codigo'], 0, 20); ?>...</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?= htmlspecialchars($forma['codigo']); ?></td>
                                                        <td><?= $forma['ordem']; ?></td>
                                                        <td><span class="badge <?= $status_color; ?>"><?= $status_label; ?></span></td>
                                                        <td><?= date('d/m/Y H:i', strtotime($forma['data_cadastro'])); ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <a class="dropdown-item" href="<?= PORTAL_URL; ?>admin/forma_pagto/editar?id=<?= $forma['id']; ?>" aria-label="Editar Forma">
                                                                    <button title="Editar Forma" class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                                        <i class="material-symbols-outlined fs-16 text-body">edit</i>
                                                                    </button>
                                                                </a>
                                                                <?php if ($forma['status'] == 1): ?>
                                                                    <a id="remover" class="dropdown-item" rel="<?= $forma['id']; ?>" aria-label="Desativar Forma">
                                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Desativar esta forma?');">
                                                                            <input type="hidden" name="acao" value="desativar">
                                                                            <input type="hidden" name="id" value="<?= $forma['id']; ?>">
                                                                            <button title="Desativar Forma" type="submit" class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                                                <i class="material-symbols-outlined fs-16 text-danger">delete</i>
                                                                            </button>
                                                                        </form>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <a id="ativar" class="dropdown-item" rel="<?= $forma['id']; ?>" aria-label="Ativar Forma">
                                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Ativar esta forma?');">
                                                                            <input type="hidden" name="acao" value="ativar">
                                                                            <input type="hidden" name="id" value="<?= $forma['id']; ?>">
                                                                            <button title="Ativar Forma" type="submit" class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                                                <i class="material-symbols-outlined fs-16 text-success">check</i>
                                                                            </button>
                                                                        </form>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $cont++;
                                                endforeach;
                                                ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap">
                                    <span class="fs-12 fw-medium">Mostrando <?= $cont - 1; ?> de <?= $total_formas; ?> Resultados</span>
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination mb-0 justify-content-center">
                                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-grow-1"></div>
                <?php include("template/admin_footer.php"); ?>
            </div>
        </div>
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/forma_pagto/index.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>