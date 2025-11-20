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

            <div class="main-content-container overflow-hidden">
                <!-- Resumo Geral -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-3 welcome-box mb-4" style="background-color: #43556f;">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div class="border-bottom position-relative top-5">
                                            <h3 class="text-white fw-semibold mb-1">Bem-vindo, <span style="color: #fd5812;"><?= $_SESSION['nome'] ?? ''; ?>!</span></h3>
                                            <p class="text-light">Resumo dos seus eventos de corrida hoje.</p>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap gap-4 gap-xxl-5">
                                            <div class="d-flex align-items-center welcome-status-item">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined">event_available</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 id="total_eventos_count" class="text-white fw-semibold mb-0">0 Eventos Ativos</h5>
                                                    <p class="text-light">Aguardando inscrições</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center welcome-status-item">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined icon-bg">how_to_reg</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 id="total_inscritos_count" class="text-white fw-semibold mb-0">0 Inscritos</h5>
                                                    <p class="text-light">Total no sistema</p>
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
                        <!-- Gráfico: Inscritos por Período -->
                        <div class="card bg-white border-0 rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 mb-lg-30">
                                    <h3 class="mb-0">Inscritos por Período</h3>
                                    <select class="form-select month-select form-control" aria-label="Default select example">
                                        <option selected>Mensal</option>
                                        <option value="1">Semanal</option>
                                        <option value="2">Hoje</option>
                                        <option value="3">Anual</option>
                                    </select>
                                </div>
                                <div style="margin-bottom: -15px; margin-left: -10px; margin-top: -10px;">
                                    <div id="inscritos_por_periodo"></div>
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
                                                    <span>Total de Inscritos</span>
                                                    <span id="total_inscritos_pct" class="count" style="color: #fd5812;">0%</span>
                                                </div>
                                                <h3 id="total_inscritos_h3" class="fs-20 mt-1 mb-5">0</h3>
                                            </div>
                                            <span class="fs-12">Últimos 7 dias</span>
                                        </div>
                                        <div style="max-width: 153px; margin: auto; margin-top: -27px; margin-bottom: -18px;">
                                            <div id="total_inscritos"></div>
                                        </div>
                                        <ul class="ps-0 mb-0 list-unstyled stats-list">
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Confirmados</span>
                                                <span id="confirmados_pct">0%</span>
                                            </li>
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Pendentes</span>
                                                <span id="pendentes_pct">0%</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Outros cards -->
                            <div class="col-md-4 col-lg-12">
                                <div class="card bg-white border-0 rounded-3 mb-4 stats-box">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <div>
                                                <div class="d-flex">
                                                    <span>Total de Usuários</span>
                                                    <span id="total_usuarios_pct" class="count up" style="color: #0000C0;">+0%</span>
                                                </div>
                                                <h3 id="total_usuarios_h3" class="fs-20 mt-1 mb-5">0</h3>
                                            </div>
                                            <span class="fs-12">Últimos 7 dias</span>
                                        </div>
                                        <div style="max-width: 290px; margin: auto; margin-top: -37px; margin-bottom: -24px;">
                                            <div id="total_usuarios"></div>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <span class="fs-12">1 Out</span>
                                            <span class="fs-12">09 Out</span>
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
                                                    <span>Pagamentos Pendentes</span>
                                                    <span id="pagamentos_pendentes_pct" class="count up" style="color: #0000C0;">+0%</span>
                                                </div>
                                                <h3 id="pagamentos_pendentes_h3" class="fs-20 mt-1 mb-5">0</h3>
                                            </div>
                                            <span class="fs-12">Últimos 30 dias</span>
                                        </div>
                                        <div style="max-width: 196px; margin: auto; margin-top: -24px; margin-bottom: -15px;">
                                            <div id="pagamentos_pendentes"></div>
                                        </div>
                                        <ul class="ps-0 mb-0 list-unstyled stats-list">
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Pagos</span>
                                                <span id="pagos_pct">0%</span>
                                            </li>
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span class="title">Pendentes</span>
                                                <span id="pendentes_pct_pag">0%</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ... (resto do HTML igual ao original) ... -->

            </div>
            <?php include("template/admin_footer.php"); ?>
        </div>
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/admin/painel.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>