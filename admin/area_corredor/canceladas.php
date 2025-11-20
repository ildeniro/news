<?php
include("template/admin_topo.php");

$usuario_id = (int) $_SESSION['id'];

// Query para inscrições canceladas
$stmt_inscricoes_cancelados = $db->prepare("
    SELECT ei.id, ei.data_inscricao, ei.data_cancelamento, ei.motivo_cancelamento, ei.valor, ei.taxa_cobrada,
           ee.nome as evento_nome, ee.data_evento, ee.local, ei.status as inscricao_status 
    FROM eve_inscricoes ei 
    JOIN eve_eventos ee ON ei.evento_id = ee.id 
    WHERE ei.usuario_id = ? AND ei.status = 3
    ORDER BY ei.data_cancelamento DESC
");
$stmt_inscricoes_cancelados->execute([$usuario_id]);
$inscricoes_canceladas = $stmt_inscricoes_cancelados->fetchAll(PDO::FETCH_ASSOC);

$total_inscricoes_canceladas = count($inscricoes_canceladas);
?>

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
                            <div class="card-header bg-transparent border-0 pb-0">
                                <h3 class="mb-0">Minhas Inscrições Canceladas (<?= $total_inscricoes_canceladas; ?>)</h3>
                            </div>
                            <div class="card-body p-4">
                                <div class="default-table-area">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0 bg-white">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Evento</th>
                                                    <th>Data do Evento</th>
                                                    <th>Data Cancelamento</th>
                                                    <th>Motivo</th>
                                                    <th>Valor Original</th>
                                                    <th>Taxa Cobrada</th>
                                                    <th>Status Cancelamento</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($inscricoes_canceladas)): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">Nenhuma inscrição cancelada encontrada.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($inscricoes_canceladas as $inscricao): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($inscricao['evento_nome']); ?></td>
                                                            <td><?= date('d/m/Y', strtotime($inscricao['data_evento'])); ?></td>
                                                            <td><?= !empty($inscricao['data_cancelamento']) ? date('d/m/Y H:i', strtotime($inscricao['data_cancelamento'])) : 'N/A'; ?></td>
                                                            <td><?= htmlspecialchars($inscricao['motivo_cancelamento'] ?? 'Não especificado'); ?></td>
                                                            <td>R$ <?= number_format((float) $inscricao['valor'], 2, ',', '.'); ?></td>
                                                            <td>R$ <?= number_format((float) ($inscricao['taxa_cobrada'] ?? 0), 2, ',', '.'); ?></td>
                                                            <td><span class="badge bg-danger">Cancelado</span></td>
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
            <?php include("template/admin_footer.php"); ?>
        </div>
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>