<?php
include("template/admin_topo.php");

$usuario_id = (int) $_SESSION['id'];

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0));
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

$inscricao_id = $param;

if ($inscricao_id <= 0) {
    header('Location: ' . PORTAL_URL . 'admin/area_corredor/inscricoes');
    exit;
}

// Query atualizada: JOIN com subcategorias e categorias
$stmt_inscricao = $db->prepare("
    SELECT 
        ei.id, ei.data_inscricao, ei.data_cancelamento, ei.motivo_cancelamento, ei.valor, ei.taxa_cobrada, ei.status as inscricao_status, ei.forma_pagto, ei.data_finalizacao,
        ee.id as evento_id, ee.nome as evento_nome, ee.data_evento, ee.hora_evento, ee.local, ee.descricao, ee.img,
        ec.nome as categoria_evento_nome,
        ic.nome as categoria_inscricao_nome, ic.qtd as vagas_categoria,
        isc.nome as subcategoria_nome, isc.valor as valor_subcategoria,
        ep.status as pagamento_status, ep.data_pagto, ep.gateway, ep.valor as valor_pago, ep.transacao_id,
        su.nome as usuario_nome, su.cpf, su.telefone_celular, su.cep, su.rua, su.numero,
        bb.NM_BAIRRO as bairro_nome, bm.nome as municipio_nome, be.nome as estado_nome
    FROM eve_inscricoes ei 
    JOIN eve_eventos ee ON ei.evento_id = ee.id 
    JOIN eve_inscricao_subcategorias isc ON ei.inscricao_subcategoria_id = isc.id
    JOIN eve_inscricao_categorias ic ON isc.categoria_id = ic.id
    LEFT JOIN eve_pagamentos ep ON ei.id = ep.inscricao_id
    LEFT JOIN eve_categorias ec ON ee.categoria_id = ec.id
    LEFT JOIN seg_usuarios su ON ei.usuario_id = su.id
    LEFT JOIN bsc_bairros bb ON su.bairro_id = bb.ID
    LEFT JOIN bsc_municipios bm ON su.cidade_id = bm.id
    LEFT JOIN bsc_estados be ON su.estado_id = be.id
    WHERE ei.id = ? AND ei.usuario_id = ?
");
$stmt_inscricao->execute([$inscricao_id, $usuario_id]);
$inscricao = $stmt_inscricao->fetch(PDO::FETCH_ASSOC);

if (!$inscricao) {
    header('Location: ' . PORTAL_URL . 'admin/area_corredor/inscricoes');
    exit;
}

// Cálculo de dias para taxa
$data_inscr = new DateTime($inscricao['data_inscricao']);
$hoje = new DateTime();
$dias_desde_inscricao = $hoje->diff($data_inscr)->days;

// Verifica se o evento foi concluído
$evento_concluido = $inscricao['data_finalizacao'] && (new DateTime($inscricao['data_finalizacao'])) < $hoje;
$depoimento_enviado = false;
if ($evento_concluido) {
    $stmt_depoimento = $db->prepare("SELECT id FROM eve_depoimentos WHERE usuario_id = ? AND evento_id = ? LIMIT 1");
    $stmt_depoimento->execute([$usuario_id, $inscricao['evento_id']]);
    $depoimento_enviado = $stmt_depoimento->fetchColumn() !== false;
}

// Mapeamento de forma de pagamento
$forma_pagto_labels = [
    'cartao' => 'Cartão de Crédito',
    'pix' => 'PIX',
    'boleto' => 'Boleto Bancário',
    'gratuito' => 'Gratuito'
];
?>

<style type="text/css">
    .timeline { position: relative; padding: 0; list-style: none; }
    .timeline::before { content: ''; position: absolute; top: 0; bottom: 0; left: 20px; width: 4px; background: #e9ecef; }
    .timeline-item { position: relative; margin-bottom: 20px; }
    .timeline-item::before { content: ''; position: absolute; top: 5px; left: 15px; width: 14px; height: 14px; border-radius: 50%; background: #fd5812; border: 2px solid #fff; }
    .timeline-item .timeline-content { margin-left: 40px; }
    .timeline-item .timeline-content h6 { margin-bottom: 5px; color: #fd5812; }
    .card-img-top { transition: transform 0.3s ease; }
    .card-img-top:hover { transform: scale(1.05); }
    .btn-voltar { display: flex; align-items: center; justify-content: center; height: 40px; }
    .btn-danger, .btn-danger:hover { color: white; }
    .bg-light { --bs-bg-opacity: 1; background-color: rgb(43 102 232) !important; }
    h5 { color: #ffffff; }
    h3 { color: #3A4252; }
</style>

<?php include("template/preloader.php"); ?>

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
                                <h3 class="mb-0">Inscrição #<?= sprintf('%06d', $inscricao['id']); ?> - <?= htmlspecialchars($inscricao['evento_nome']); ?></h3>
                                <div class="d-flex gap-2">
                                    <a href="<?= PORTAL_URL; ?>admin/area_corredor/inscricoes" class="btn btn-voltar btn-outline-secondary" data-bs-toggle="tooltip" title="Voltar">
                                        <i class="ri-arrow-left-line"></i> Voltar
                                    </a>
                                    <?php if ($inscricao['pagamento_status'] == 'pendente'): ?>
                                        <a href="<?= PORTAL_URL; ?>pagamento?id=<?= $inscricao['id']; ?>" class="btn btn-warning" data-bs-toggle="tooltip" title="Pagar">
                                            <i class="ri-money-dollar-circle-line"></i> Pagar Agora
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Imagem do Evento -->
                                    <div class="col-lg-4">
                                        <div class="card shadow-sm h-100">
                                            <img src="<?= PORTAL_URL; ?>assets/img/eventos/<?= htmlspecialchars($inscricao['img'] ?? 'default-corrida.jpg'); ?>" class="card-img-top rounded-3" alt="<?= htmlspecialchars($inscricao['evento_nome']); ?>" style="height: 250px; object-fit: cover;">
                                            <div class="card-body">
                                                <h5 class="card-title fs-16 mb-2">Sobre o Evento</h5>
                                                <p class="text-muted small"><?= htmlspecialchars($inscricao['descricao']); ?></p>
                                                <p class="mb-0"><strong>Vagas na Categoria:</strong> <?= (int) $inscricao['vagas_categoria']; ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detalhes -->
                                    <div class="col-lg-8">
                                        <div class="row g-4">
                                            <!-- Informações do Evento -->
                                            <div class="col-md-6">
                                                <div class="card shadow-sm h-100">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Informações do Evento</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="list-unstyled">
                                                            <li class="mb-2"><strong>Nome:</strong> <?= htmlspecialchars($inscricao['evento_nome']); ?></li>
                                                            <li class="mb-2"><strong>Data:</strong> <?= date('d/m/Y', strtotime($inscricao['data_evento'])) . " às " . $inscricao['hora_evento']; ?></li>
                                                            <li class="mb-2"><strong>Local:</strong> <?= htmlspecialchars($inscricao['local']); ?></li>
                                                            <li class="mb-2"><strong>Categoria Geral:</strong> <?= htmlspecialchars($inscricao['categoria_evento_nome'] ?? 'N/A'); ?></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Informações da Inscrição -->
                                            <div class="col-md-6">
                                                <div class="card shadow-sm h-100">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Informações da Inscrição</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="list-unstyled">
                                                            <li class="mb-2"><strong>Código:</strong> #<?= sprintf('%06d', $inscricao['id']); ?></li>
                                                            <li class="mb-2"><strong>Data Inscrição:</strong> <?= date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?></li>
                                                            <li class="mb-2"><strong>Status:</strong> 
                                                                <span class="badge bg-<?= $inscricao['inscricao_status'] == 2 ? 'success' : ($inscricao['inscricao_status'] == 3 ? 'danger' : 'warning'); ?>">
                                                                    <?= $inscricao['inscricao_status'] == 2 ? 'Confirmado' : ($inscricao['inscricao_status'] == 3 ? 'Cancelado' : 'Pendente'); ?>
                                                                </span>
                                                            </li>
                                                            <li class="mb-2"><strong>Tipo:</strong> <?= htmlspecialchars($inscricao['categoria_inscricao_nome']); ?></li>
                                                            <li class="mb-2"><strong>Valor:</strong> <?= htmlspecialchars($inscricao['subcategoria_nome']); ?> - R$ <?= number_format((float) $inscricao['valor'], 2, ',', '.'); ?></li>
                                                            <li class="mb-2"><strong>Forma:</strong> <?= $forma_pagto_labels[$inscricao['forma_pagto']] ?? 'N/A'; ?></li>
                                                            <li class="mb-2"><strong>Status Pagamento:</strong> 
                                                                <span class="badge bg-<?= $inscricao['pagamento_status'] == 'pago' ? 'success' : ($inscricao['pagamento_status'] == 'pendente' ? 'warning' : 'danger'); ?>">
                                                                    <?= ucfirst($inscricao['pagamento_status'] ?? 'Não Iniciado'); ?>
                                                                </span>
                                                            </li>
                                                            <li class="mb-2"><strong>Data Pagamento:</strong> <?= $inscricao['data_pagto'] ? date('d/m/Y H:i', strtotime($inscricao['data_pagto'])) : 'N/A'; ?></li>
                                                            <li class="mb-2"><strong>Gateway:</strong> <?= htmlspecialchars($inscricao['gateway'] ?? 'N/A'); ?></li>
                                                            <li class="mb-2"><strong>Transação ID:</strong> <?= htmlspecialchars($inscricao['transacao_id'] ?? 'N/A'); ?></li>
                                                            <?php if ($inscricao['inscricao_status'] == 3): ?>
                                                                <li class="mb-2"><strong>Motivo Cancelamento:</strong> <?= htmlspecialchars($inscricao['motivo_cancelamento'] ?? 'Não especificado'); ?></li>
                                                                <li class="mb-2"><strong>Taxa Cobrada:</strong> R$ <?= number_format((float) $inscricao['taxa_cobrada'], 2, ',', '.'); ?></li>
                                                            <?php endif; ?>
                                                            <?php if ($inscricao['data_finalizacao']): ?>
                                                                <li class="mb-2"><strong>Data Finalização:</strong> <?= date('d/m/Y H:i', strtotime($inscricao['data_finalizacao'])); ?></li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Informações do Usuário -->
                                            <div class="col-md-6">
                                                <div class="card shadow-sm h-100">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Informações do Corredor</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="list-unstyled">
                                                            <li class="mb-2"><strong>Nome:</strong> <?= htmlspecialchars($inscricao['usuario_nome']); ?></li>
                                                            <li class="mb-2"><strong>CPF:</strong> <?= htmlspecialchars($inscricao['cpf'] ?? 'N/A'); ?></li>
                                                            <li class="mb-2"><strong>Telefone:</strong> <?= htmlspecialchars($inscricao['telefone_celular'] ?? 'N/A'); ?></li>
                                                            <li class="mb-2"><strong>Endereço:</strong> 
                                                                <?= htmlspecialchars($inscricao['rua'] . ', ' . $inscricao['numero'] . ', ' . ($inscricao['bairro_nome'] ?? 'N/A') . ', ' . ($inscricao['municipio_nome'] ?? 'N/A') . ' - ' . ($inscricao['estado_nome'] ?? 'N/A')); ?>
                                                            </li>
                                                            <li class="mb-2"><strong>CEP:</strong> <?= htmlspecialchars($inscricao['cep'] ?? 'N/A'); ?></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Timeline -->
                                            <div class="col-md-6">
                                                <div class="card shadow-sm h-100">
                                                    <div class="card-header bg-light">
                                                        <h5 class="mb-0">Progresso da Inscrição</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="timeline">
                                                            <li class="timeline-item">
                                                                <div class="timeline-content">
                                                                    <h6>Inscrição Realizada</h6>
                                                                    <p class="text-muted small"><?= date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?></p>
                                                                </div>
                                                            </li>
                                                            <?php if ($inscricao['pagamento_status'] == 'pago'): ?>
                                                                <li class="timeline-item">
                                                                    <div class="timeline-content">
                                                                        <h6>Pagamento Confirmado</h6>
                                                                        <p class="text-muted small"><?= date('d/m/Y H:i', strtotime($inscricao['data_pagto'])); ?></p>
                                                                    </div>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php if ($inscricao['inscricao_status'] == 2): ?>
                                                                <li class="timeline-item">
                                                                    <div class="timeline-content">
                                                                        <h6>Inscrição Confirmada</h6>
                                                                        <p class="text-muted small"><?= date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?></p>
                                                                    </div>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php if ($inscricao['inscricao_status'] == 3): ?>
                                                                <li class="timeline-item">
                                                                    <div class="timeline-content">
                                                                        <h6>Inscrição Cancelada</h6>
                                                                        <p class="text-muted small"><?= date('d/m/Y H:i', strtotime($inscricao['data_cancelamento'])); ?></p>
                                                                        <p class="text-muted small"><?= htmlspecialchars($inscricao['motivo_cancelamento'] ?? 'Não especificado'); ?></p>
                                                                    </div>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php if ($inscricao['data_finalizacao']): ?>
                                                                <li class="timeline-item">
                                                                    <div class="timeline-content">
                                                                        <h6>Evento Concluído</h6>
                                                                        <p class="text-muted small"><?= date('d/m/Y H:i', strtotime($inscricao['data_finalizacao'])); ?></p>
                                                                    </div>
                                                                </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ações -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <?php if ($inscricao['inscricao_status'] != 3): ?>
                                            <input type="hidden" id="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()); ?>">
                                            <button class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#cancelModal" data-inscricao-id="<?= (int) $inscricao['id']; ?>" data-evento-id="<?= (int) $inscricao['evento_id']; ?>" data-dias="<?= $dias_desde_inscricao; ?>">
                                                Cancelar Inscrição
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($evento_concluido && !$depoimento_enviado): ?>
                                            <button class="btn btn-success text-white" data-bs-toggle="modal" data-bs-target="#depoimentoModal" data-inscricao-id="<?= (int) $inscricao['id']; ?>" data-evento-id="<?= (int) $inscricao['evento_id']; ?>">
                                                Deixar Depoimento
                                            </button>
                                        <?php endif; ?>
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

<!-- Modal de Cancelamento -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">Cancelar Inscrição</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Deseja realmente cancelar esta inscrição? <?php if ($dias_desde_inscricao >= 7): ?>Será cobrada uma taxa administrativa de 20%.<?php else: ?>O reembolso será integral.<?php endif; ?></p>
                <div class="form-group">
                    <label for="motivo_cancelamento" class="form-label">Motivo do Cancelamento (opcional):</label>
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

<!-- Modal de Depoimento -->
<div class="modal fade" id="depoimentoModal" tabindex="-1" aria-labelledby="depoimentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="depoimentoModalLabel">Deixar Depoimento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="depoimentoForm" name="depoimentoForm" method="POST">
                    <input type="hidden" id="depoimento_inscricao_id" name="depoimento_inscricao_id">
                    <input type="hidden" id="depoimento_evento_id" name="depoimento_evento_id">
                    <input type="hidden" id="depoimento_usuario_id" name="depoimento_usuario_id" value="<?= $usuario_id; ?>">
                    <div class="form-group mb-3">
                        <label for="depoimento_texto" class="form-label">Seu Depoimento</label>
                        <textarea class="form-control" id="depoimento_texto" name="depoimento_texto" rows="4" maxlength="160" placeholder="Compartilhe sua experiência no evento" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="cargo_texto" class="form-label">Seu Cargo</label>
                        <input type="text" class="form-control" id="cargo_texto" name="cargo_texto" placeholder="Compartilhe seu cargo" value="" required/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="depoimento_estrelas" class="form-label">Nota (1 a 5 estrelas)</label>
                        <select class="form-select" id="depoimento_estrelas" name="depoimento_estrelas" required>
                            <option value="1">1 estrela</option>
                            <option value="2">2 estrelas</option>
                            <option value="3">3 estrelas</option>
                            <option value="4">4 estrelas</option>
                            <option value="5" selected>5 estrelas</option>
                        </select>
                    </div>
                    <input type="hidden" id="depoimento_csrf_token" name="token" value="<?= htmlspecialchars(generateCsrfToken()); ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success text-white" id="submitDepoimentoBtn">Enviar Depoimento</button>
            </div>
        </div>
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/inscricoes.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializa tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle-tooltip="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Configura modal de cancelamento
        var cancelModal = document.getElementById('cancelModal');
        cancelModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var inscricao_id = button.getAttribute('data-inscricao-id');
            var evento_id = button.getAttribute('data-evento-id');
            var dias = button.getAttribute('data-dias');
            var confirmBtn = document.getElementById('confirmCancelBtn');
            confirmBtn.setAttribute('data-inscricao-id', inscricao_id);
            confirmBtn.setAttribute('data-evento-id', evento_id);
            confirmBtn.setAttribute('data-dias', dias);
        });

        // Ação do botão de confirmar cancelamento
        document.getElementById('confirmCancelBtn').addEventListener('click', function () {
            var inscricao_id = this.getAttribute('data-inscricao-id');
            var evento_id = this.getAttribute('data-evento-id');
            var dias = parseInt(this.getAttribute('data-dias'));
            var motivo = document.getElementById('motivo_cancelamento').value.trim();
            cancelar_inscricao(inscricao_id, evento_id, dias, motivo);
            bootstrap.Modal.getInstance(cancelModal).hide();
        });

        // Configura modal de depoimento
        var depoimentoModal = document.getElementById('depoimentoModal');
        depoimentoModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var inscricao_id = button.getAttribute('data-inscricao-id');
            var evento_id = button.getAttribute('data-evento-id');
            document.getElementById('depoimento_inscricao_id').value = inscricao_id;
            document.getElementById('depoimento_evento_id').value = evento_id;
        });

        // Ação do botão de enviar depoimento
        document.getElementById('submitDepoimentoBtn').addEventListener('click', function () {

            var usuario_id = $("#depoimento_usuario_id").val();
            var evento_id = $("#depoimento_evento_id").val();
            var texto = $("#depoimento_texto").val();
            var estrelas = $("#depoimento_estrelas").val();
            var cargo = $("#cargo_texto").val();

            projetouniversal.util.getjson({
                url: PORTAL_URL + "dao/area_corredor/salvar_depoimento",
                type: "POST",
                data: {
                    usuario_id: usuario_id,
                    evento_id: evento_id,
                    texto: texto,
                    estrelas: estrelas,
                    cargo: cargo
                },
                enctype: 'multipart/form-data',
                success: function (obj) {
                    if (obj.msg == 'success') {
                        swal({
                            title: "Sucesso!",
                            text: obj.retorno,
                            type: "success",
                            confirmButtonClass: "btn btn-success",
                            confirmButtonText: "Ok"
                        }).then(function () {
                            bootstrap.Modal.getInstance(depoimentoModal).hide();
                            window.location.reload();
                        });
                    } else {
                        swal("Erro!", obj.retorno, "error");
                    }
                },
                error: function (obj) {
                    swal("Erro!", obj.retorno || "Erro ao salvar o depoimento.", "error");
                }
            });
        });
    });

    function cancelar_inscricao(inscricao_id, evento_id, dias, motivo) {
        var obs = dias < 7
                ? "Obs: Caso escolha cancelar, você terá reembolso integral, pois está no prazo de 7 dias."
                : "Obs: Caso escolha cancelar, será cobrado uma taxa administrativa de 20%.";
        swal({
            title: "Confirmar Cancelamento?",
            text: obs,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-success",
            cancelButtonClass: "btn btn-danger m-l-10",
            confirmButtonText: "Sim, cancelar!",
            cancelButtonText: "Não"
        }).then(function (result) {
            if (motivo == "") {
                swal("Erro!", "O motivo do cancelamento é obrigatório!.", "error");
            } else {
                if (result) {
                    projetouniversal.util.getjson({
                        url: PORTAL_URL + "dao/cancelar_inscricao",
                        type: "POST",
                        data: {
                            id: inscricao_id,
                            evento_id: evento_id,
                            motivo: motivo,
                            token: document.getElementById('csrf_token').value
                        },
                        enctype: 'multipart/form-data',
                        success: function (obj) {
                            if (obj.msg == 'success') {
                                swal({
                                    title: "Sucesso!",
                                    text: obj.retorno,
                                    type: "success",
                                    confirmButtonClass: "btn btn-success",
                                    confirmButtonText: "Ok"
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                swal("Erro!", obj.retorno, "error");
                            }
                        },
                        error: function (obj) {
                            swal("Erro!", obj.retorno || "Erro ao processar o cancelamento.", "error");
                        }
                    });
                }
            }
        });
    }
</script>