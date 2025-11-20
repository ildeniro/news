<?php
include("template/admin_topo.php");

// Verifica se logado
if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('Location: ' . PORTAL_URL . 'admin');
    exit;
}
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Sidebar Area -->
<div class="sidebar-area" id="sidebar-area">
    <?php include("template/corredor_menu.php"); ?>

    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <?php include("template/header_corredor.php"); ?>

            <div class="main-content-container overflow-hidden">
                <!-- Resumo Geral -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-3 welcome-box mb-4" style="background-color: #43556f;">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div class="border-bottom position-relative top-5">
                                            <h3 class="text-white fw-semibold mb-1">Bem-vindo de volta, <span id="nome_usuario" style="color: #fd5812;"></span>!</h3>
                                            <p class="text-light">Acompanhe suas corridas e inscrições aqui.</p>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap gap-4 gap-xxl-5">
                                            <div class="d-flex align-items-center welcome-status-item">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined">how_to_reg</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="text-white fw-semibold mb-0" id="total_inscricoes">0 Inscrições</h5>
                                                    <p class="text-light">Total realizadas</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center welcome-status-item">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined icon-bg">event_available</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="text-white fw-semibold mb-0" id="eventos_proximos">0 Próximos</h5>
                                                    <p class="text-light">Eventos agendados</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="welcome-img text-center text-sm-end mt-4 mt-sm-0">
                                            <img src="<?= PORTAL_URL; ?>assets/img/8.jpeg" alt="welcome" loading="lazy">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Suas Inscrições Recentes (tabela) -->
                        <div class="row rounded-3 mb-4">
                            <div class="col-12">
                                <div class="card bg-white border-0 rounded-3">
                                    <div class="card-header bg-transparent border-0 pb-0">
                                        <h3 class="mb-0">Suas Inscrições Recentes</h3>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="table-responsive">
                                            <table class="table align-middle mb-0 bg-white">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Evento</th>
                                                        <th>Status</th>
                                                        <th>Valor</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="inscricoes_table"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico: Suas Inscrições por Período -->
                        <div class="card bg-white border-0 rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 mb-lg-30">
                                    <h3 class="mb-0">Suas Inscrições por Período</h3>
                                    <select class="form-select month-select form-control" id="periodo_select" aria-label="Default select example">
                                        <option value="mensal" selected>Mensal</option>
                                        <option value="semanal">Semanal</option>
                                        <option value="hoje">Hoje</option>
                                        <option value="anual">Anual</option>
                                    </select>
                                </div>
                                <div style="min-height: 350px;">
                                    <div id="inscricoes_usuario"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="row justify-content-center">
                            <div class="col-md-4 col-lg-12">
                                <div class="card bg-white border-0 rounded-3 mb-4 stats-box">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <div>
                                                <div class="d-flex">
                                                    <span>Total de Inscrições</span>
                                                    <span class="count" id="confirmados_pct_user" style="color: #fd5812;">0%</span>
                                                </div>
                                                <h3 class="fs-20 mt-1 mb-5" id="total_inscricoes_count">0</h3>
                                            </div>
                                            <span class="fs-12">Últimos 6 meses</span>
                                        </div>
                                        <div style="max-width: 153px; margin: auto; margin-top: -27px; margin-bottom: -18px;">
                                            <div id="total_inscricoes_user"></div>
                                        </div>
                                        <ul class="ps-0 mb-0 list-unstyled stats-list">
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Confirmadas</span>
                                                <span id="confirmadas_pct">0%</span>
                                            </li>
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Pendentes</span>
                                                <span id="pendentes_pct">0%</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-12">
                                <div class="card bg-white border-0 rounded-3 mb-4 stats-box">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <div>
                                                <div class="d-flex">
                                                    <span>Inscrições Semanais</span>
                                                    <span class="count up" id="inscricoes_semanal_pct" style="color: #0000C0;">+0%</span>
                                                </div>
                                                <h3 class="fs-20 mt-1 mb-5" id="inscricoes_semanal_user">0</h3>
                                            </div>
                                            <span class="fs-12">Últimos 7 dias</span>
                                        </div>
                                        <div style="max-width: 290px; margin: auto; margin-top: -37px; margin-bottom: -24px;">
                                            <div id="inscricoes_semanal_user"></div>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <span class="fs-12">Semana Passada</span>
                                            <span class="fs-12">Atual</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-12">
                                <div class="card bg-white border-0 rounded-3 mb-4 stats-box">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <div>
                                                <div class="d-flex">
                                                    <span>Próximos Eventos</span>
                                                    <span class="count up" id="eventos_proximos_count" style="color: #0000C0;">+0</span>
                                                </div>
                                                <h3 class="fs-20 mt-1 mb-5" id="eventos_proximos_total">0</h3>
                                            </div>
                                            <span class="fs-12">Agendados</span>
                                        </div>
                                        <div style="max-width: 196px; margin: auto; margin-top: -24px; margin-bottom: -15px;">
                                            <div id="proximos_eventos_user"></div>
                                        </div>
                                        <ul class="ps-0 mb-0 list-unstyled stats-list">
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Confirmados</span>
                                                <span id="inscricoes_confirmadas">0</span>
                                            </li>
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Pendentes</span>
                                                <span id="inscricoes_pendentes">0</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-12">
                                <div class="card bg-white border-0 rounded-3 mb-4 stats-box">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <div>
                                                <div class="d-flex">
                                                    <span>Pagamentos Confirmados</span>
                                                    <span class="count" id="pagamentos_confirmados" style="color: #0000C0;">R$ 0,00</span>
                                                </div>
                                                <h3 class="fs-20 mt-1 mb-5" id="pagamentos_pendentes">Tudo OK!</h3>
                                            </div>
                                            <span class="fs-12">Total processado</span>
                                        </div>
                                        <div style="max-width: 196px; margin: auto; margin-top: -24px; margin-bottom: -15px;">
                                            <div id="pagamentos_user" style="height: 200px;"></div>
                                        </div>
                                        <ul class="ps-0 mb-0 list-unstyled stats-list">
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Confirmados</span>
                                                <span id="pagamentos_confirmados_total">R$ 0,00</span>
                                            </li>
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Pendentes</span>
                                                <span id="pagamentos_pendentes_total">R$ 0,00</span>
                                            </li>
                                        </ul>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/admin/corredor.js"></script>