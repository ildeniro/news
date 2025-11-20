<?php
include("template/admin_topo.php");
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
                                <h3 class="mb-0">Eventos Cancelados</h3>
                                <div class="d-flex align-items-center gap-3">
                                    <form class="position-relative table-src-form">
                                        <input type="text" class="form-control" placeholder="Pesquisar eventos...">
                                        <i class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y">search</i>
                                    </form>
                                    <select class="form-select month-select form-control" aria-label="Filtro de período">
                                        <option selected>Todos</option>
                                        <option value="1">Hoje</option>
                                        <option value="2">Semanal</option>
                                        <option value="3">Mensal</option>
                                    </select>
                                </div>
                            </div>

                            <div class="default-table-area">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nome do Evento</th>
                                                <th scope="col">Categoria</th>
                                                <th scope="col">Data</th>
                                                <th scope="col">Local</th>
                                                <th scope="col">Cadastro</th>
                                                <th scope="col">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $cont = 1;
                                            $result = $db->prepare("SELECT e.*, c.nome as categoria_nome 
                                                                    FROM eve_eventos e 
                                                                    LEFT JOIN eve_categorias c ON e.categoria_id = c.id
                                                                    WHERE e.status = 0  
                                                                    ORDER BY e.data_evento");
                                            $result->execute();
                                            while ($evento = $result->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                <tr>
                                                    <td><?= $cont; ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if ($evento['img']): ?>
                                                                <img src="<?= PORTAL_URL; ?>assets/img/eventos/<?= $evento['img']; ?>" class="wh-40 rounded-3 me-2" alt="evento">
                                                            <?php endif; ?>
                                                            <div class="ms-2 ps-1">
                                                                <h6 class="fw-medium fs-14"><?= htmlspecialchars($evento['nome']); ?></h6>
                                                                <span class="fs-12 text-body"><?= substr($evento['descricao'], 0, 50); ?>...</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($evento['categoria_nome'] ?? 'Sem Categoria'); ?></td>
                                                    <td><?= date('d/m/Y', strtotime($evento['data_evento'])) . " às " . $evento['hora_evento']; ?></td>
                                                    <td><?= htmlspecialchars($evento['local']); ?></td>
                                                    <td><?= obterDataBRTimestamp($evento['data_cadastro']); ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-1">
                                                            <a id="ativar" class="dropdown-item" rel="<?= $evento['id']; ?>" aria-label="Ativar Evento">
                                                                <button title="Ativar Evento" class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                                    <i class="material-symbols-outlined fs-20 text-success">check</i>
                                                                </button>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                $cont++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap">
                                    <span class="fs-12 fw-medium">Mostrando <?= $cont - 1; ?> Resultados</span>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/eventos/cancelados.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>