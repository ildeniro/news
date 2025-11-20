<?php
include("template/login_topo.php");

if ($_POST) {
    $nome = strip_tags($_POST['nome'] ?? '');
    $sexo = $_POST['sexo'] ?? 1;
    $email = strip_tags($_POST['email'] ?? '');
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    $data_nasc = $_POST['data_nasc'] ?? '';
    $contato = strip_tags($_POST['contato'] ?? ''); // Telefone
    $login = strip_tags($_POST['login'] ?? '');
    $senha = sha1($_POST['senha'] ?? ''); // SHA1 para compatibilidade
    $confirma_senha = sha1($_POST['confirma_senha'] ?? '');

    // Validações
    $erros = [];
    if (empty($nome))
        $erros[] = 'Nome é obrigatório.';
    if (empty($email))
        $erros[] = 'E-mail é obrigatório.';
    if (!in_array($sexo, [1, 2]))
        $erros[] = 'Selecione o sexo válido.';
    if (empty($cpf) || strlen($cpf) !== 11)
        $erros[] = 'CPF inválido (11 dígitos).';
    if (empty($data_nasc))
        $erros[] = 'Data de nascimento é obrigatória.';
    if (empty($contato))
        $erros[] = 'Contato é obrigatório.';
    if (empty($login))
        $erros[] = 'Login é obrigatório.';
    if (strlen($_POST['senha']) < 6)
        $erros[] = 'Senha deve ter pelo menos 6 caracteres.';
    if ($_POST['senha'] !== $_POST['confirma_senha'])
        $erros[] = 'Senhas não coincidem.';

    if (empty($erros)) {
        try {
            // Verifica duplicatas (cpf, login)
            $check = $db->prepare("SELECT id FROM seg_usuarios WHERE cpf = ? OR login = ?");
            $check->execute([$cpf, $login]);
            if ($check->rowCount() > 0) {
                $erros[] = 'CPF ou login já cadastrado!';
            } else {
                // Insere com campos obrigatórios (defaults para outros)
                $stmt = $db->prepare("
                    INSERT INTO seg_usuarios 
                    (nome, email, sexo, cpf, nascimento, telefone_celular, login, senha, status, data_cadastro, online, usuario_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), 1, 1)
                ");
                if ($stmt->execute([$nome, $email, $sexo, $cpf, $data_nasc, $contato, $login, $senha])) {
                    $novo_id = $db->lastInsertId();
                    // Loga automaticamente
                    $_SESSION['timeout'] = time();
                    //CRIAR AS SESSÕES DO USUARIO
                    $_SESSION['id'] = $novo_id;
                    $_SESSION['nome'] = $nome;
                    $_SESSION['telefone1'] = $contato;
                    $_SESSION['email1'] = $email;
                    $_SESSION['login'] = $login;
                    $_SESSION['foto'] = "";
                    $_SESSION['online'] = 1;
                    $_SESSION['foto_cut'] = "";
                    $_SESSION['foto_origin'] = "";

                    //INSERINDO SESSÃO
                    $useragent = $_SERVER['HTTP_USER_AGENT'];
                    if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched)) {
                        $browser_version = $matched[1];
                        $browser = 'IE';
                    } elseif (preg_match('|Opera/([0-9].[0-9]{1,2})|', $useragent, $matched)) {
                        $browser_version = $matched[1];
                        $browser = 'Opera';
                    } elseif (preg_match('|Firefox/([0-9\.]+)|', $useragent, $matched)) {
                        $browser_version = $matched[1];
                        $browser = 'Firefox';
                    } elseif (preg_match('|Chrome/([0-9\.]+)|', $useragent, $matched)) {
                        $browser_version = $matched[1];
                        $browser = 'Chrome';
                    } elseif (preg_match('|Safari/([0-9\.]+)|', $useragent, $matched)) {
                        $browser_version = $matched[1];
                        $browser = 'Safari';
                    } else {
                        $browser_version = 0;
                        $browser = 'Desconhecido';
                    }
                    $separa = explode(";", $useragent);
                    $so = $separa[1];

                    $stmt1 = $db->prepare("INSERT INTO seg_sessoes
                     (usuario_id, usuario_pai_id, host, ip, navegador, sistema_operacional, numero_sessao)
                      VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt1->bindValue(1, $novo_id);
                    $stmt1->bindValue(2, 1);
                    $stmt1->bindValue(3, $_SERVER["SERVER_NAME"]);
                    $stmt1->bindValue(4, $_SERVER['REMOTE_ADDR']);
                    $stmt1->bindValue(5, $browser . " " . $browser_version);
                    $stmt1->bindValue(6, $so);
                    $stmt1->bindValue(7, session_id());
                    $stmt1->execute();

                    $stmt2 = $db->prepare("INSERT INTO seg_permissoes (user_id, nivel) VALUES (?, 2)"); //Nível de Usuário Corredor
                    $stmt2->bindValue(1, $novo_id);
                    $stmt2->execute();

                    // Email de confirmação (usa login como fallback se sem email)
                    //$assunto = "Bem-vindo à Pace Run!";
                    //$corpo = "Olá $nome!\n\nSua conta foi criada com sucesso.\nLogin: $login\n\nAcesse: " . PORTAL_URL . "area_corredor para gerenciar suas inscrições.\n\nObrigado por se juntar à Pace Run!\n\nEquipe Pace Run";
                    // mail($email, $assunto, $corpo); // Comente se sem email; ajuste para notificação via outro meio
                    //$sucesso = "Cadastro realizado! Você foi logado automaticamente. Redirecionando para a área do corredor...";
                    echo "<script>window.location.href = '" . PORTAL_URL . "admin/corredor';</script>";
                    $_POST = [];
                } else {
                    $erros[] = 'Erro ao cadastrar. Tente novamente.';
                }
            }
        } catch (PDOException $e) {
            $erros[] = 'Erro no banco: ' . $e->getMessage();
        }
    }

    if (!empty($erros)) {
        $erro = implode('<br>', $erros);
    }
}
?>

<style type="text/css">
    body {
        background-color: #3d3d3d;
    }

    .modal-title{
        color: #fff;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: none;
        color: #dc3545;
    }
    .form-control.is-invalid ~ .invalid-feedback,
    .form-select.is-invalid ~ .invalid-feedback {
        display: block;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 45px !important;
    }

</style>

<!-- Start Preloader Area -->
<?php include("template/preloader.php"); ?>
<!-- End Preloader Area -->

<!-- Start Main Content Area -->
<div class="container">
    <div class="main-content d-flex flex-column p-0">
        <div class="m-auto m-1230">
            <div class="row align-items-center">
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="<?= PORTAL_URL; ?>assets/img/1.jpeg" class="rounded-3" alt="cadastrar-corrida">
                </div>
                <div class="col-lg-6">
                    <div class="mw-480 ms-lg-auto">
                        <div class="d-inline-block mb-4">
                            <a href="<?= PORTAL_URL; ?>"><img src="<?= PORTAL_URL; ?>assets/img/Logo_Cores_Orinais.png" class="rounded-3 for-light-logo" alt="logo-pace-run" style="height: 115px; margin-left: 45%;"></a>
                        </div>
                        <h3 class="fs-28 mb-2 text-white">Cadastre-se na Pace Run!</h3>
                        <p class="fw-medium fs-16 mb-4 text-white">Crie sua conta para se inscrever e gerenciar seus eventos.</p>
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger"><?= $erro; ?></div>
                        <?php endif; ?>
                        <?php if (isset($sucesso)): ?>
                            <div class="alert alert-success"><?= $sucesso; ?></div>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-12 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" class="form-control h-49" placeholder="Digite seu nome completo" value="<?= $_POST['nome'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" class="form-control h-49" placeholder="000.000.000-00" data-mask="999.999.999-99" value="<?= htmlspecialchars($_POST['cpf'] ?? ''); ?>" maxlength="14" required>
                                </div>
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">Data de Nascimento *</label>
                                    <input type="date" name="data_nasc" id="data_nasc" class="form-control h-49" value="<?= $_POST['data_nasc'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">Contato (Telefone) *</label>
                                    <input type="text" name="contato" id="contato" class="form-control h-49" placeholder="(68) 99999-9999" data-mask="(99) 99999-9999" value="<?= htmlspecialchars($_POST['contato'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">Sexo *</label>
                                    <select name="sexo" id="sexo" class="form-select h-58" required>
                                        <option value="1" <?= ($_POST['sexo'] ?? 1) == 1 ? 'selected' : ''; ?>>Masculino</option>
                                        <option value="2" <?= ($_POST['sexo'] ?? '') == 2 ? 'selected' : ''; ?>>Feminino</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">E-mail *</label>
                                    <input type="email" name="email" id="email" class="form-control h-49" placeholder="Digite seu e-mail" value="<?= $_POST['email'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">Login *</label>
                                    <input type="text" name="login" id="login" class="form-control h-49" placeholder="exemplo.exemplo" value="<?= $_POST['login'] ?? ''; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">Senha *</label>
                                    <input type="password" name="senha" id="senha" class="form-control h-49" placeholder="Digite sua senha (mín. 6 chars)" required>
                                </div>
                                <div class="col-md-6 form-group mb-4">
                                    <label class="label text-secondary" style="color: #fd5812 !important">Confirme Senha *</label>
                                    <input type="password" name="confirma_senha" id="confirma_senha" class="form-control h-49" placeholder="Confirme sua senha" required>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <button type="submit" class="btn btn-primary fw-medium py-2 px-3 w-100" style="background-color: #0000C0; border-color: #0000C0;">
                                    <div class="d-flex align-items-center justify-content-center py-1">
                                        <i class="material-symbols-outlined text-white fs-20 me-2">person_4</i>
                                        <span>Cadastrar</span>
                                    </div>
                                </button>
                            </div>
                            <div class="form-group text-white">
                                <p>Ao confirmar, você concorda com nossos <a href="#" data-bs-toggle="modal" data-bs-target="#termosModal" class="fw-medium text-decoration-none" style="color: #fd5812 !important; text-decoration: underline !important;">Termos de Serviço</a> e confirma que leu e entendeu nossa <a href="#" data-bs-toggle="modal" data-bs-target="#privacidadeModal" class="fw-medium text-decoration-none" style="color: #fd5812 !important; text-decoration: underline !important;">Política de Privacidade</a>.</p>
                                <p>Já tem uma conta? <a href="<?= PORTAL_URL; ?>admin" class="fw-medium text-primary text-decoration-none" style="color: #fd5812 !important; text-decoration: underline !important;">Faça Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Main Content Area -->

<!-- Modal de Termos e Condições (Atualizado) -->
<div class="modal fade" id="termosModal" tabindex="-1" aria-labelledby="termosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="termosModalLabel">Termos e Condições de Uso - Pace Run</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <p><strong>Última atualização: 12 de outubro de 2025</strong></p>

                <h6>1. Introdução</h6>
                <p>Estes Termos e Condições ("Termos") regem o uso da plataforma Pace Run ("Plataforma"), operada pela Pace Run Eventos Ltda., com sede em [Endereço da Empresa], CNPJ [Número do CNPJ], doravante denominada "Pace Run". Ao se inscrever em eventos ou utilizar a Plataforma, você ("Usuário" ou "Corredor") concorda com estes Termos. Se não concordar, não utilize a Plataforma.</p>

                <h6>2. Cadastro e Conta de Usuário</h6>
                <p>Para realizar inscrições, o usuário deve criar uma conta fornecendo dados verdadeiros e atualizados. Você é responsável pela confidencialidade de sua senha e por todas as atividades na conta. A Pace Run pode negar ou cancelar cadastros por informações falsas ou violação destes Termos.</p>

                <h6>3. Participação nos Eventos e Responsabilidades</h6>
                <p>Ao se inscrever, você declara estar em plenas condições de saúde para atividades esportivas e assume responsabilidade por qualquer dano ou lesão. A Pace Run não se responsabiliza por acidentes durante os eventos.</p>

                <h6>4. Cessão de Direitos de Imagem e Voz</h6>
                <p>Ao confirmar a inscrição (marcando o checkbox correspondente), você autoriza, de forma gratuita e irrevogável, o uso de sua imagem, voz e nome em materiais promocionais da Pace Run, incluindo fotos, vídeos e divulgações em redes sociais e websites.</p>

                <h6>5. Condições de Cancelamento e Reembolso</h6>
                <p>Para inscrições pagas, você tem até 7 dias corridos após a confirmação para cancelar e receber reembolso integral (100%). Após esse período, será cobrada uma taxa de 20% sobre o valor pago para cancelamento. Reembolsos serão processados via meio de pagamento original, em até 30 dias. Para eventos gratuitos ou beneficentes, o cancelamento é livre, sem reembolso aplicável. A Pace Run pode cancelar eventos por força maior, com reembolso integral ou crédito para eventos futuros.</p>

                <h6>6. Propriedade Intelectual</h6>
                <p>Todos os conteúdos da Plataforma (logotipos, imagens, textos) são propriedade da Pace Run ou licenciados. O uso não autorizado é proibido.</p>

                <h6>7. Limitação de Responsabilidade</h6>
                <p>A Pace Run não garante a disponibilidade ininterrupta da Plataforma. Não seremos responsáveis por danos indiretos, lucros cessantes ou uso indevido por terceiros.</p>

                <h6>8. Alterações nos Termos</h6>
                <p>Podemos alterar estes Termos a qualquer momento, notificando via e-mail ou na Plataforma. O uso contínuo implica aceitação das mudanças.</p>

                <h6>9. Lei Aplicável e Foro</h6>
                <p>Estes Termos são regidos pela lei brasileira. Qualquer disputa será resolvida no foro da comarca de [Cidade da Empresa], com renúncia a qualquer outro.</p>

                <p><em>Ao aceitar estes Termos, você confirma ter lido e compreendido todas as cláusulas.</em></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Política de Privacidade (Atualizado) -->
<div class="modal fade" id="privacidadeModal" tabindex="-1" aria-labelledby="privacidadeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="privacidadeModalLabel">Política de Privacidade - Pace Run</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <p><strong>Última atualização: 12 de outubro de 2025</strong></p>

                <h6>1. Coleta de Dados</h6>
                <p>Coletamos dados como nome, e-mail, CPF, data de nascimento, dados de saúde (para aptidão em eventos esportivos) e preferências para gerenciar inscrições e envios (ex.: camisas). Dados de pagamento são processados por terceiros seguros.</p>

                <h6>2. Uso dos Dados</h6>
                <p>Usamos para: processar inscrições, enviar confirmações, marketing (com consentimento), análises e cumprimento legal. Não vendemos dados.</p>

                <h6>3. Compartilhamento</h6>
                <p>Compartilhamos com organizadores de eventos, processadores de pagamento e autoridades se exigido por lei. Usamos cookies para melhorar a experiência (veja Política de Cookies).</p>

                <h6>4. Seus Direitos</h6>
                <p>Você pode acessar, corrigir ou excluir seus dados via <a href="<?= PORTAL_URL; ?>admin/corredor">Área do Corredor</a> ou contato@pace-run.com. Respeitamos a LGPD (Lei Geral de Proteção de Dados).</p>

                <h6>5. Segurança</h6>
                <p>Adotamos medidas técnicas para proteger dados, mas nenhuma transmissão online é 100% segura.</p>

                <p><em>Continuando a usar a Plataforma, você concorda com esta Política.</em></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('select').each(function () {
            $(this).select2();
            $('.select2').attr('style', 'width: 100%');
        });
    });
</script>

<?php include("template/login_rodape.php"); ?>