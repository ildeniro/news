<?php
include("template/admin_topo.php");
?>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Sidebar Area -->
<div class="sidebar-area" id="sidebar-area">
    <!-- Menu -->
    <?php include("template/admin_menu.php"); ?>
    <!-- Fim Menu -->

    <!-- Start Main Content Area -->
    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <!-- Start Header Area -->
            <?php include("template/header.php"); ?>
            <!-- End Header Area -->

            <!-- Lista de Usuários Cadastrados -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                                <h3 class="mb-0">Membros Cadastrados</h3>
                                <div class="d-flex align-items-center gap-3">
                                    <form class="position-relative table-src-form">
                                        <input type="text" class="form-control" placeholder="Pesquisar membro da equipe...">
                                        <i class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y">search</i>
                                    </form>
                                    <select class="form-select month-select form-control" aria-label="Filtro de período">
                                        <option selected>Todos</option>
                                        <option value="1">Hoje</option>
                                        <option value="2">Semanal</option>
                                        <option value="3">Mensal</option>
                                    </select>
                                    <a href="<?= PORTAL_URL; ?>admin/equipe/cadastrar" class="btn btn-primary" aria-label="Cadastrar novo membro">
                                        Novo Membro
                                    </a>
                                </div>
                            </div>

                            <div class="default-table-area">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nome do Membro</th>
                                                <th scope="col">Cargo</th>
                                                <th scope="col">Responsável</th>
                                                <th scope="col">Cadastro</th>
                                                <th scope="col">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>


                                            <?php
                                            $cont = 1;

                                            $result = $db->prepare("SELECT e.id, e.nome, e.cargo, e.foto, e.data_cadastro, u.nome AS usuario  
                                                                    FROM eve_equipes AS e  
                                                                    INNER JOIN seg_usuarios AS u ON u.id = e.usuario_id 
                                                                    WHERE e.status = 1  
                                                                    ORDER BY e.nome");

                                            $result->execute();

                                            while ($equipe = $result->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                <tr>
                                                    <td><?= $cont; ?></td>
                                                    <td>
                                                        <a href="<?= PORTAL_URL; ?>admin/equipe/cadastrar/<?= $equipe['id']; ?>" class="d-flex align-items-center">
                                                            <?php if ($equipe['foto']): ?>
                                                                <img src="<?= PORTAL_URL; ?>assets/img/equipe/<?= $equipe['foto']; ?>" class="wh-40 rounded-3 me-2" alt="evento">
                                                            <?php endif; ?>
                                                            <div class="ms-2 ps-1">
                                                                <h6 class="fw-medium fs-14"><?= $equipe['nome']; ?></h6>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td><?= $equipe['cargo']; ?></td>
                                                    <td><?= $equipe['usuario']; ?></td>
                                                    <td><?= obterDataBRTimestamp($equipe['data_cadastro']); ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-1">
                                                            <a class="dropdown-item" href="<?= PORTAL_URL; ?>admin/equipe/cadastrar/<?= $equipe['id']; ?>" aria-label="Editar Membro">
                                                                <button title="Editar Membro" class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                                    <i class="material-symbols-outlined fs-16 text-body">edit</i>
                                                                </button>
                                                            </a>
                                                            <a id="remover" class="dropdown-item" rel="<?= $equipe['id']; ?>" aria-label="Cancelar Membro">
                                                                <button title="Cancelar Membro" class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                                    <i class="material-symbols-outlined fs-16 text-danger">delete</i>
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
                                    <span class="fs-12 fw-medium">Mostrando 3 de 25 Resultados</span>
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination mb-0 justify-content-center">
                                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fim Lista de Membros Cadastrados -->

                <div class="flex-grow-1"></div>

                <!-- Start Footer Area -->
                <?php include("template/admin_footer.php"); ?>
                <!-- End Footer Area -->
            </div>
        </div>
        <!-- End Main Content Area -->
    </div>
</div>

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/equipes/index.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>