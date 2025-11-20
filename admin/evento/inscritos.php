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
                                <h3 class="mb-0">Inscritos por Evento</h3>
                                <div class="d-flex align-items-center gap-3">
                                    <form class="position-relative table-src-form">
                                        <input type="text" class="form-control" id="search_evento" placeholder="Pesquisar eventos...">
                                        <i class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y">search</i>
                                    </form>
                                </div>
                            </div>

                            <div class="accordion" id="eventosAccordion">
                                <!-- Eventos serão carregados aqui via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para Confirmar Conclusão -->
            <div class="modal fade" id="confirmarConclusaoModal" tabindex="-1" aria-labelledby="confirmarConclusaoLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmarConclusaoLabel">Confirmar Conclusão da Corrida</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="form_confirmar">
                                <input type="hidden" id="inscricao_id" name="inscricao_id">
                                <div class="mb-3">
                                    <label for="data_finalizacao" class="form-label">Data de Finalização</label>
                                    <input type="date" class="form-control" id="data_finalizacao" name="data_finalizacao" required>
                                </div>
                                <div class="mb-3">
                                    <label for="hora_finalizacao" class="form-label">Hora de Finalização</label>
                                    <input type="time" class="form-control" id="hora_finalizacao" name="hora_finalizacao" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btn_confirmar">Confirmar</button>
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

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/eventos/inscritos.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>