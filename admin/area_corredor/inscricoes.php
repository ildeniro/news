<?php
include("template/admin_topo.php");

$usuario_id = (int) $_SESSION['id'];

// Query para inscrições ativas
$stmt_inscricoes = $db->prepare("
    SELECT ei.id, ei.data_inscricao, ei.valor, ei.status as inscricao_status,
           ee.id as evento_id, ee.nome as evento_nome, ee.data_evento, ee.local,
           ep.status as pagamento_status, ep.data_pagto 
    FROM eve_inscricoes ei 
    JOIN eve_eventos ee ON ei.evento_id = ee.id 
    LEFT JOIN eve_pagamentos ep ON ei.id = ep.inscricao_id
    WHERE ei.usuario_id = ? AND ei.status IN (1, 2)
    ORDER BY ei.data_inscricao DESC
");
$stmt_inscricoes->execute([$usuario_id]);
$inscricoes = $stmt_inscricoes->fetchAll(PDO::FETCH_ASSOC);

$total_inscricoes = count($inscricoes);
?>

<style type="text/css">
    .btn-voltar{
        display: flex;
        align-items: center;
        justify-content: center;
        height: 40px;
    }

    .btn-danger{
        color: white;
    }

    .btn-danger:hover {
        color: white;
    }

    .bg-light {
        --bs-bg-opacity: 1;
        background-color: rgb(43 102 232) !important;
    }

    h5 {
        color: #ffffff;
    }

    h3 {
        color: #3A4252;
    }
</style>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<div class="sidebar-area" id="sidebar-area">
    <?php include("template/corredor_menu.php"); ?>

    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <?php include("template/header_corredor.php"); ?>

            <div class="main-content-container overflow-hidden">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-white border-0 rounded-3 mb-4">
                            <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Minhas Inscrições Ativas (<?= $total_inscricoes; ?>)</h3>
                                <a href="<?= PORTAL_URL; ?>events" class="btn btn-primary">Nova Inscrição</a>
                            </div>
                            <div class="card-body p-4">
                                <div class="default-table-area">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0 bg-white">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Evento</th>
                                                    <th>Data do Evento</th>
                                                    <th>Local</th>
                                                    <th>Status Inscrição</th>
                                                    <th>Status Pagamento</th>
                                                    <th>Valor</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($inscricoes)): ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">Nenhuma inscrição ativa encontrada.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php
                                                    foreach ($inscricoes as $inscricao):
                                                        $status_label = $inscricao['inscricao_status'] == 2 ? 'Confirmado' : 'Pendente';
                                                        $status_color = $inscricao['inscricao_status'] == 2 ? 'success' : 'warning';
                                                        $pag_status_label = $inscricao['pagamento_status'] == 2 ? 'Pago' : ($inscricao['pagamento_status'] == 1 ? 'Pendente' : 'Não Iniciado');
                                                        $pag_status_color = $inscricao['pagamento_status'] == 2 ? 'success' : ($inscricao['pagamento_status'] == 1 ? 'warning' : 'secondary');
                                                        $data_inscr = new DateTime($inscricao['data_inscricao']);
                                                        $hoje = new DateTime();
                                                        $dias_desde_inscricao = $hoje->diff($data_inscr)->days;
                                                        ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($inscricao['evento_nome']); ?></td>
                                                            <td><?= date('d/m/Y', strtotime($inscricao['data_evento'])); ?></td>
                                                            <td><?= htmlspecialchars($inscricao['local']); ?></td>
                                                            <td><span class="badge bg-<?= $status_color; ?>"><?= $status_label; ?></span></td>
                                                            <td><span class="badge bg-<?= $pag_status_color; ?>"><?= $pag_status_label; ?></span></td>
                                                            <td>R$ <?= number_format((float) $inscricao['valor'], 2, ',', '.'); ?></td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <a href="<?= PORTAL_URL; ?>admin/area_corredor/detalhes/<?= $inscricao['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                        <i class="ri-eye-line"></i> Detalhes
                                                                    </a>
                                                                    <?php if ($inscricao['inscricao_status'] != 3): ?>
                                                                        <button class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#cancelModal" data-inscricao-id="<?= (int) $inscricao['id']; ?>" data-evento-id="<?= (int) $inscricao['evento_id']; ?>" data-dias="<?= $dias_desde_inscricao; ?>" data-bs-toggle-tooltip="tooltip" title="Cancelar inscrição">
                                                                            Cancelar
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()); ?>">

            <!-- Modal de Cancelamento -->
            <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="cancelModalLabel">Cancelar Inscrição</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Deseja realmente cancelar esta inscrição? <span id="cancelTaxInfo"></span></p>
                            <div class="form-group">
                                <label for="motivo_cancelamento" class="form-label">Motivo do Cancelamento (obrigatório):</label>
                                <textarea class="form-control" id="motivo_cancelamento" rows="4" placeholder="Digite o motivo do cancelamento"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-danger" id="confirmCancelBtn">Confirmar Cancelamento</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php include("template/admin_footer.php"); ?>
        </div>
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/area_corredor/inscricoes.js"></script>