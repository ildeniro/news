<?php
include("template/admin_topo.php");
?>

<?php
$perfil = "";

$id = (!isset($_POST['id']) && isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0 ) );
$param = Url::getURL(3);
$param = $param == '' && $id != '' ? $id : $param;

if (!ver_nivel(1) && $_SESSION['id'] != $param) {
    msg('Você não possui permissão para acessar essa área.');
    url(PORTAL_URL . 'view/painel');
}

if ($param != null && $param != '' && $param != NULL && $param != 0) {
    $id = $param;

    $result = $db->prepare("SELECT *  
                 FROM seg_usuarios u 
                 WHERE u.id = ?");
    $result->bindValue(1, $id);
    $result->execute();
    $dados_usuario = $result->fetch(PDO::FETCH_ASSOC);

    $usuario_id = $dados_usuario['id'];
    $usuario_nome = $dados_usuario['nome'];
    $usuario_login = $dados_usuario['login'];
    $usuario_status = $dados_usuario['status'];
    $usuario_email = $dados_usuario['email'];
    $usuario_contato = $dados_usuario['telefone_celular'];
    $usuario_sexo = $dados_usuario['sexo'];
    $usuario_cpf = $dados_usuario['cpf'];
    $usuario_nascimento = $dados_usuario['nascimento'];
    $usuario_foto = $dados_usuario['foto'];
    $usuario_bairro = $dados_usuario['bairro_id'];
    $usuario_rua = $dados_usuario['rua'];
    $usuario_numero = $dados_usuario['numero'];
    $usuario_pais = $dados_usuario['pais_id'];
    $usuario_estado = $dados_usuario['estado_id'];
    $usuario_cidade = $dados_usuario['cidade_id'];
} else {
    $usuario_id = "";
    $usuario_nome = "";
    $usuario_login = "";
    $usuario_status = 1;
    $usuario_email = "";
    $usuario_foto = "";
    $usuario_contato = "";
    $usuario_sexo = 1;
    $usuario_cpf = "";
    $usuario_nascimento = "";
    $usuario_bairro = "";
    $usuario_rua = "";
    $usuario_numero = "";
    $usuario_pais = 30;
    $usuario_estado = 1;
    $usuario_cidade = 94;
}
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

            <!-- Formulário de Cadastro de Usuários -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                                <h3 class="mb-0">Informações do Usuário</h3>
                            </div>

                            <form id="form_usuario" name="form_usuario" action="#" method="POST" enctype="multipart/form-data">

                                <input type="hidden" id="id" name="id" value="<?= $usuario_id ?>"/>

                                <div class="row">
                                    <!-- Nome do Usuário -->
                                    <div id="div_nome" class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome do Usuário <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex.: José da Silva Mota" required aria-required="true" value="<?= $usuario_nome; ?>">
                                        <div class="invalid-feedback">Por favor, insira o nome do usuário.</div>
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label for="sexoUsuario" class="form-label">Sexo <span class="text-danger">*</span></label>
                                        <select class="form-select" id="sexoUsuario" name="sexoUsuario" required aria-required="true">
                                            <option value="" disabled selected>Selecione o sexo</option>
                                            <option <?= $usuario_sexo == 1 ? "selected='true'" : ""; ?> value="1">Masculino</option>
                                            <option <?= $usuario_sexo == 2 ? "selected='true'" : ""; ?> value="2">Feminino</option>
                                            <option <?= $usuario_sexo == 0 ? "selected='true'" : ""; ?> value="0">Outro</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o sexo.</div>
                                    </div>

                                    <!-- CPF do Usuário -->
                                    <div class="col-md-2 mb-3">
                                        <label for="cpfUsuario" class="form-label">CPF do Usuário <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="cpfUsuario" data-mask="999.999.999-99" placeholder="Ex.: 999.999.999-99" name="cpfUsuario" required aria-required="true" value="<?= $usuario_cpf; ?>">
                                        <div class="invalid-feedback">Por favor, informe o CPF do usuário.</div>
                                    </div>

                                    <!-- Data de Nascimento do Usuário -->
                                    <div class="col-md-2 mb-3">
                                        <label for="dataNascimento" class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="dataNascimento" name="dataNascimento" required aria-required="true" value="<?= $usuario_nascimento; ?>">
                                        <div class="invalid-feedback">Por favor, informe a data de nascimento.</div>
                                    </div>

                                    <!-- Foto do Usuário -->
                                    <div id="div_foto" class="col-md-4 mb-3">
                                        <label for="foto" class="form-label">Foto do Usuário</label>
                                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                        <div class="invalid-feedback">Por favor, selecione uma imagem válida (JPEG, PNG ou JPG).</div>
                                        <?php if (!empty($usuario_foto)) { ?>
                                            <div class="mt-2">
                                                <img src="<?= PORTAL_URL . "assets/img/users/".$usuario_foto; ?>" alt="Foto do Usuário" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-4">
                                    <h3 class="mb-0">Informações de Contato</h3>
                                </div>

                                <div class="row">
                                    <!-- E-mail -->
                                    <div id="div_email" class="col-md-4 mb-3">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Ex.: example@gmail.com" value="<?= $usuario_email; ?>">
                                        <div class="invalid-feedback">Por favor, o e-mail do usuário.</div>
                                    </div>

                                    <!-- Contato -->
                                    <div id="div_contato" class="col-md-2 mb-3">
                                        <label for="contato" class="form-label">Contato <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contato" name="contato" data-mask="(99) 99999-9999" placeholder="Ex.: (68) 9999-9999" required aria-required="true" value="<?= $usuario_contato; ?>">
                                        <div class="invalid-feedback">Por favor, insira o contato do usuário.</div> 
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-4">
                                    <h3 class="mb-0">Informações de Endereço</h3>
                                </div>

                                <div class="row">
                                    <!-- País -->
                                    <div class="col-md-4 mb-3">
                                        <label for="paisUsuario" class="form-label">País</label>
                                        <select class="form-select" id="paisUsuario" name="paisUsuario">
                                            <option value="" disabled selected>Selecione o país</option>
                                            <?php
                                            $result1 = $db->prepare("SELECT id, nome  
                                                                     FROM bsc_pais 
                                                                     WHERE 1  
                                                                     ORDER BY nome ASC");
                                            $result1->execute();
                                            while ($pais = $result1->fetch(PDO::FETCH_ASSOC)) {
                                                if ($usuario_pais == $pais['id']) {
                                                    ?>
                                                    <option selected="true" value='<?= $pais['id']; ?>'><?= $pais['nome']; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value='<?= $pais['id']; ?>'><?= $pais['nome']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o país.</div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="col-md-4 mb-3">
                                        <label for="estadoUsuario" class="form-label">Estado</label>
                                        <select class="form-select" id="estadoUsuario" name="estadoUsuario">
                                            <option value="" disabled selected>Selecione o estado</option>
                                            <?php
                                            $result2 = $db->prepare("SELECT id, nome 
                                                                     FROM bsc_estados 
                                                                     WHERE 1   
                                                                     ORDER BY nome ASC");
                                            $result2->execute();
                                            while ($estados = $result2->fetch(PDO::FETCH_ASSOC)) {
                                                if ($usuario_estado == $estados['id']) {
                                                    ?>
                                                    <option selected="true" value='<?= $estados['id']; ?>'><?= $estados['nome']; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value='<?= $estados['id']; ?>'><?= $estados['nome']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione o estado.</div>
                                    </div>

                                    <!-- Cidade -->
                                    <div class="col-md-4 mb-3">
                                        <label for="cidadeUsuario" class="form-label">Cidade</label>
                                        <select class="form-select" id="cidadeUsuario" name="cidadeUsuario">
                                            <option value="" disabled selected>Selecione a cidade</option>
                                            <?php
                                            $result3 = $db->prepare("SELECT id, nome 
                                                                     FROM bsc_municipios 
                                                                     WHERE 1   
                                                                     ORDER BY nome ASC");
                                            $result3->execute();
                                            while ($cidades = $result3->fetch(PDO::FETCH_ASSOC)) {
                                                if ($usuario_cidade == $cidades['id']) {
                                                    ?>
                                                    <option selected="true" value='<?= $cidades['id']; ?>'><?= $cidades['nome']; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value='<?= $cidades['id']; ?>'><?= $cidades['nome']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Por favor, selecione a cidade.</div>
                                    </div>

                                    <!-- Bairro -->
                                    <div class="col-md-4 mb-3">
                                        <label for="bairroUsuario" class="form-label">Bairro</label>
                                        <select class="form-select" id="bairroUsuario" name="bairroUsuario">
                                            <option value="" disabled selected>Selecione o bairro</option>
                                            <?php
                                            $result6 = $db->prepare("SELECT ID, NM_BAIRRO 
                                                                     FROM bsc_bairros 
                                                                     WHERE 1 
                                                                     ORDER BY NM_BAIRRO ASC");
                                            $result6->execute();
                                            while ($bairros = $result6->fetch(PDO::FETCH_ASSOC)) {
                                                if ($usuario_bairro == $bairros['ID']) {
                                                    ?>
                                                    <option selected="true" value='<?= $bairros['ID']; ?>'><?= $bairros['NM_BAIRRO']; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value='<?= $bairros['ID']; ?>'><?= $bairros['NM_BAIRRO']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="invalid-feedback">Por favor, insira o bairro do usuário.</div>
                                    </div>

                                    <!-- Rua -->
                                    <div class="col-md-6 mb-3">
                                        <label for="ruaUsuario" class="form-label">Rua</label>
                                        <input type="text" class="form-control" id="ruaUsuario" name="ruaUsuario" placeholder="Ex.: Rua da Baixada" value="<?= $usuario_rua; ?>">
                                        <div class="invalid-feedback">Por favor, insira a rua do usuário.</div>
                                    </div>

                                    <!-- Número -->
                                    <div class="col-md-2 mb-3">
                                        <label for="numeroUsuario" class="form-label">Número</label>
                                        <input type="number" class="form-control" id="numeroUsuario" name="numeroUsuario" placeholder="Ex.: Nº 165" value="<?= $usuario_numero; ?>">
                                        <div class="invalid-feedback">Por favor, insira o número do usuário.</div>
                                    </div>
                                </div>

                                <div style="<?= is_numeric($usuario_id) ? "display: none" : ""; ?>" class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-4">
                                    <h3 style="<?= is_numeric($usuario_id) ? "display: none" : ""; ?>" class="mb-0">Informações de Acesso</h3>
                                </div>

                                <div style="<?= is_numeric($usuario_id) ? "display: none" : ""; ?>" class="row">
                                    <!-- Login -->
                                    <div id="div_login" class="col-md-4 mb-3">
                                        <label for="login" class="form-label">Login <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="login" name="login" min="10" placeholder="Ex.: leandro.silva" value="<?= $usuario_login; ?>">
                                        <div class="invalid-feedback">Por favor, insira o login.</div>
                                    </div>

                                    <!-- Senha -->
                                    <div id="div_senha" class="col-md-4 mb-3">
                                        <label for="senha" class="form-label">Senha <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="senha" name="senha" min="10">
                                        <div class="invalid-feedback">Por favor, insira uma senha.</div>
                                    </div>

                                    <!-- Confirmar Senha -->
                                    <div id="div_conf_senha" class="col-md-4 mb-3">
                                        <label for="confirmar_senha" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" min="10">
                                        <div class="invalid-feedback">Por favor, insira a confirmação de senha.</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.location.href = '<?= PORTAL_URL; ?>admin/usuario/'" aria-label="Cancelar cadastro">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary" aria-label="Salvar usuário">
                                        <i class="material-symbols-outlined" style="color: white;">save</i> Salvar
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fim Formulário de Cadastro de Usuários -->

            <div class="flex-grow-1"></div>

            <!-- Start Footer Area -->
            <?php include("template/admin_footer.php"); ?>
            <!-- End Footer Area -->
        </div>
    </div>
    <!-- End Main Content Area -->

<?php include("template/admin_rodape.php"); ?>

<script type="text/javascript" src="<?= PORTAL_URL; ?>scripts/usuarios/cadastrar.js"></script>

<script type="text/javascript">
//Carregar notificações
    $(document).ready(function () {
        loadNotificacoes();
    });
</script>