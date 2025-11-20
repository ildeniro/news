<?php
//------------------------------------------------------------------------------
// Gera token CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
//------------------------------------------------------------------------------
// Verifica token CSRF
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE CANDIDATOS POR PARTIDO
function qtd_candidatos_partido($ano, $partido, $municipio_id_tse) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs_registros = $con->prepare("SELECT COUNT(ID) AS qtd    
                                   FROM 2024_candidatos 
                                   WHERE ANO_ELEICAO = ? AND ID_PARTIDO = ? AND CAST(SG_UE AS UNSIGNED) = ?");
    $rs_registros->bindValue(1, $ano);
    $rs_registros->bindValue(2, $partido);
    $rs_registros->bindValue(3, $municipio_id_tse);
    $rs_registros->execute();
    while ($op = $rs_registros->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['qtd'];
    }

    $rs_registros = fdec($qtd);

    $rs_registros = explode(",", $rs_registros);

    return $rs_registros[0];
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE APTOS DE VOTAÇÃO POR REGIONAL
function qtd_candidatos($ano, $tipo, $municipio_id_tse) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs_registros = $con->prepare("SELECT COUNT(ID) AS qtd    
                                   FROM 2024_candidatos 
                                   WHERE ANO_ELEICAO = ? AND DS_CARGO = ? AND CAST(SG_UE AS UNSIGNED) = ?");
    $rs_registros->bindValue(1, $ano);
    $rs_registros->bindValue(2, $tipo);
    $rs_registros->bindValue(3, $municipio_id_tse);
    $rs_registros->execute();
    while ($op = $rs_registros->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['qtd'];
    }

    $rs_registros = fdec($qtd);

    $rs_registros = explode(",", $rs_registros);

    return $rs_registros[0];
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE APTOS DE VOTAÇÃO POR REGIONAL
function aptos_municipio($municipio) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs_municipio = $con->prepare("SELECT SUM(s.QT_ELEITOR_AGREGADO) AS qtd 
                         FROM 2024_secoes AS s
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                         WHERE s.TIPO = 'Principal' AND lv.MUNICIPIO_ID = ?");
    $rs_municipio->bindValue(1, $municipio);
    $rs_municipio->execute();
    while ($op = $rs_municipio->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['qtd'];
    }

    $rs_municipio = fdec($qtd);

    $rs_municipio = explode(",", $rs_municipio);

    return $rs_municipio[0];
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR O LOCAL DE VOTAÇÃO ATRAVÉS DA ZONA E SEÇÃO INFORMADA
function carregar_local_votacao($zona, $secao) {
    $con = Conexao::getInstance();
    $resultado = "";

    $rs = $con->prepare("SELECT lv.NM_LOCAL_VOTACAO         
                         FROM 2024_locais_votacao AS lv 
                         INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                         WHERE s.NR_ZONA = ? AND s.NR_SECAO = ?
                         GROUP BY lv.ID");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $secao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op["NM_LOCAL_VOTACAO"];
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR CARREGAR O BAIRRO ATRAVÉS DA ZONA E SEÇÃO INFORMADA
function carregar_bairro_votacao($zona, $secao) {
    $con = Conexao::getInstance();
    $resultado = "";

    $rs = $con->prepare("SELECT lv.NM_BAIRRO          
                         FROM 2024_locais_votacao AS lv 
                         INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = lv.ID 
                         WHERE s.NR_ZONA = ? AND s.NR_SECAO = ?
                         GROUP BY lv.ID");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $secao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op["NM_BAIRRO"];
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR CARREGAR A REGIONAL ATRAVÉS DA ZONA E SEÇÃO INFORMADA
function carregar_regional_votacao($zona, $secao) {
    $con = Conexao::getInstance();
    $resultado = "";

    $rs = $con->prepare("SELECT r.nome AS REGIONAL         
                         FROM 2024_secoes AS s
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                         INNER JOIN sys_regionais AS r ON r.id = lv.REGIONAL_ID  
                         WHERE s.NR_ZONA = ? AND s.NR_SECAO = ?
                         GROUP BY lv.ID");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $secao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op["REGIONAL"];
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE FISCAIS VINCULADOS A UMA ZONA E SESSAO
function vf_secao_fiscal($zona, $sessao, $fucao_id, $posicao, $campo) {

    $con = Conexao::getInstance();

    $qtd = 1;
    $resultado = "";

    $rs = $con->prepare("SELECT p.$campo        
                         FROM 2024_voluntarios p
                         WHERE p.zona_2 = ? AND p.secao_numero_2 = ? AND p.funcao_id = ?");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $sessao);
    $rs->bindValue(3, $fucao_id);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {

        if ($posicao == $qtd) {
            $resultado = $op[$campo];
        }
        $qtd++;
    }

    return $resultado;
}

//------------------------------------------------------------------------------
function removePrimeiraVirgula($campo) {
    // Remover apenas a primeira vírgula
    return preg_replace('/,/', '', $campo, 1);
}

//------------------------------------------------------------------------------
function encaminhamento_qtd($opcao) {
    $con = Conexao::getInstance();

    $qtd_encaminhamentos = 0;
    $qtd_ocorrencias = 0;
    $resultado = 0;

    $rs = $con->prepare("SELECT vo.id  
                         FROM 2024_voluntarios_ocorrencias AS vo 
                         WHERE vo.status = 1");
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {

        $qtd_ocorrencias++;

        $rs2 = $con->prepare("SELECT ve.id       
                              FROM 2024_voluntarios_encaminhamentos AS ve
                              WHERE ve.ocorrencia_id = ? AND ve.status = 1
                              GROUP BY ve.ocorrencia_id");
        $rs2->bindValue(1, $op['id']);
        $rs2->execute();

        while ($op2 = $rs2->fetch(PDO::FETCH_ASSOC)) {
            $qtd_encaminhamentos++;
        }
    }

    if ($opcao == 1) {
        $resultado = $qtd_encaminhamentos;
    } else {
        $resultado = $qtd_ocorrencias - $qtd_encaminhamentos;
    }

    return $resultado;
}

//------------------------------------------------------------------------------
function total_ocorrencias($op) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT COUNT(vo.id) AS QTD 
                         FROM 2024_voluntarios_ocorrencias AS vo 
                         WHERE vo.situacao = ?");
    $rs->bindValue(1, $op);
    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['QTD'];
    }

    return $qtd;
}

//------------------------------------------------------------------------------
function status_voluntario($op) {
    $rs = "";

    if ($op == 1) {
        $rs = "badge-success"; //Ativo
    } else if ($op == 0) {
        $rs = "badge-primary"; //Outro
    } else if ($op == 2) {
        $rs = "badge-danger"; //Desistência
    } else if ($op == 3) {
        $rs = "badge-warning"; //Saúde
    } else if ($op == 4) {
        $rs = "badge-info"; //Requisitado pelo TRE
    } else if ($op == 5) {
        $rs = "badge-not-d"; //Não Distribuido
    } else if ($op == 10) {
        $rs = "badge-info"; //Distribuidos 
    }

    return $rs;
}

//------------------------------------------------------------------------------
//Verifica se parametro contém alguma letra
function contemLetra($string) {
    // Verifica se a string contém pelo menos uma letra
    return preg_match('/[a-zA-Z]/', $string) === 1;
}

//------------------------------------------------------------------------------
//Remover Máscaras
function removerMascara($cpf) {
    // Remove pontos, traços e espaços do CPF
    return preg_replace('/[^\d]/', '', $cpf);
}

//------------------------------------------------------------------------------
function status_voluntario_nome($op) {
    $rs = "";

    if ($op == 1) {
        $rs = "Aitvo";
    } else if ($op == 0) {
        $rs = "Outros";
    } else if ($op == 2) {
        $rs = "Desistência";
    } else if ($op == 3) {
        $rs = "Saúde";
    } else if ($op == 4) {
        $rs = "TRE";
    } else if ($op == 5) {
        $rs = "Não Distribuido";
    } else if ($op == 10) {
        $rs = "Distribuido";
    }

    return $rs;
}

//------------------------------------------------------------------------------
function qtd_indicacao($indicador, $tipo) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT COUNT(vi.id) AS QTD 
                         FROM 2024_voluntarios_indicacoes AS vi 
                         INNER JOIN 2024_voluntarios AS v ON v.id = vi.voluntario_id 
                         INNER JOIN 2024_indicacoes AS i ON i.id = vi.indicacao_id 
                         WHERE i.id = ? AND v.funcao_id = ? AND v.status = 1   
                         GROUP BY i.id;");
    $rs->bindValue(1, $indicador);
    $rs->bindValue(2, $tipo);
    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['QTD'];
    }

    if ($qtd > 0) {
        $qtd = str_replace(",00", "", fdec($qtd));
    }

    return $qtd;
}

//------------------------------------------------------------------------------
function contato_supervisor($local, $zona, $secao, $tipo) {
    $con = Conexao::getInstance();

    $resultado = "";

    $rs = $con->prepare("SELECT v.nome, v.celular    
                         FROM 2024_voluntarios AS v  
                         WHERE v.funcao_id = 4 AND v.local_votacao_2 = ? OR
                         v.funcao_id = 4 AND v.zona_2 = ? AND v.secao_numero_2 = ?");
    $rs->bindValue(1, $local);
    $rs->bindValue(2, $zona);
    $rs->bindValue(3, $secao);
    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {

        if ($tipo == "nome") {
            $resultado = ctexto($op['nome'], "pri");
        } else {
            $resultado = $op['celular'];
        }
    }

    return $resultado;
}

//------------------------------------------------------------------------------
function resume($var, $limite) {
    // Verifica se o texto é maior que o limite, usando mb_strlen para suportar caracteres especiais
    if (mb_strlen($var, 'UTF-8') > $limite) {
        // Corta o texto adequadamente, sem quebrar caracteres com acento
        $var = mb_substr($var, 0, $limite, 'UTF-8');
        // Remove espaços extras e adiciona os três pontinhos
        $var = trim($var) . "...";
    }
    // Remove qualquer HTML que possa estar no texto e retorna
    return strip_tags($var);
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR AS REFERÊNCIAS DE INDICAÇÕES DE UM VOLUNTÁRIO
function vf_indicacao($voluntario_id) {
    $con = Conexao::getInstance();

    $resultado = "";

    $rs = $con->prepare("SELECT i.nome AS indicacao  
                         FROM 2024_voluntarios AS v  
                         INNER JOIN 2024_voluntarios_indicacoes AS vi ON vi.voluntario_id = v.id
                         INNER JOIN 2024_indicacoes as i ON i.id = vi.indicacao_id 
                         WHERE v.id = ?");
    $rs->bindValue(1, $voluntario_id);
    $rs->execute();

    $qtd = $rs->rowCount();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado .= "" . $op['indicacao'] . "" . ($qtd > 1 ? "," : "");
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNA SE A QUANTIDADE DE VOLUNTÁRIOS CADASTRADOS
function qtd_funcao($treinamento, $ano, $funcao_id, $pessoa_regional, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_situacao, $pessoa_local, $pessoa_indicacao) {

    $con = Conexao::getInstance();

    $resultado = 0;
    $contador2 = 3;
    $condicao = "";

    if ($pessoa_regional != "") {
        $condicao .= "AND lv.REGIONAL_ID = ? ";
    }

    if ($pessoa_zona != "") {
        $condicao .= "AND v.zona = ? ";
    }

    if ($pessoa_secao != "") {
        $condicao .= "AND v.secao_numero = ? ";
    }

    if ($pessoa_situacao != "") {
        $condicao .= "AND v.status = ? ";
    }

    if ($pessoa_local != "") {
        $condicao .= "AND s.LOCAL_VOTACAO_ID = ? ";
    }

    if ($pessoa_indicacao != "") {
        $condicao .= "AND i.indicacao_id = ? ";
    }

    if ($treinamento != "" && $treinamento != 2) {
        $condicao .= "AND v.treinamento = ? ";
    } else if ($treinamento != "" && $treinamento == 2) {
        $condicao .= "AND v.treinamento IS NULL";
    }

    $rs = $con->prepare("SELECT COUNT(v.id) AS qtd 
                         FROM 2024_voluntarios AS v
                         INNER JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona AND s.NR_SECAO = v.secao_numero 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                         LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = v.id
                         WHERE YEAR(v.data_cadastro) = ? AND v.funcao_id = ? $condicao");

    $rs->bindValue(1, $ano);
    $rs->bindValue(2, $funcao_id);

    if ($pessoa_regional != "") {
        $rs->bindValue($contador2, $pessoa_regional);
        $contador2++;
    }

    if ($pessoa_zona != "") {
        $rs->bindValue($contador2, $pessoa_zona);
        $contador2++;
    }

    if ($pessoa_secao != "") {
        $rs->bindValue($contador2, $pessoa_secao);
        $contador2++;
    }

    if ($pessoa_situacao != "") {
        $rs->bindValue($contador2, $pessoa_situacao);
        $contador2++;
    }

    if ($pessoa_local != "") {
        $rs->bindValue($contador2, $pessoa_local);
        $contador2++;
    }

    if ($pessoa_indicacao != "") {
        $rs->bindValue($contador2, $pessoa_indicacao);
        $contador2++;
    }

    if ($treinamento != "" && $treinamento != 2) {
        $rs->bindValue($contador2, $treinamento);
        $contador2++;
    }

    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op['qtd'];
    }

    if ($pessoa_funcao == 4 && $funcao_id != $pessoa_funcao || $pessoa_funcao == 5 && $funcao_id != $pessoa_funcao) {
        $resultado = 0;
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNA SE A QUANTIDADE DE VOLUNTÁRIOS CADASTRADOS
function qtd_funcao_distribuidos($ano, $funcao_id, $pessoa_regional, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_situacao, $pessoa_local, $pessoa_indicacao) {

    $con = Conexao::getInstance();

    $resultado = 0;
    $contador2 = 3;
    $condicao = "";

    if ($pessoa_regional != "") {
        $condicao .= "AND lv.REGIONAL_ID = ? ";
    }

    if ($pessoa_zona != "") {
        $condicao .= "AND v.zona = ? ";
    }

    if ($pessoa_secao != "") {
        $condicao .= "AND v.secao_numero = ? ";
    }

    if ($pessoa_situacao != "") {
        $condicao .= "AND v.status = ? ";
    }

    if ($pessoa_local != "") {
        $condicao .= "AND s.LOCAL_VOTACAO_ID = ? ";
    }

    if ($pessoa_indicacao != "") {
        $condicao .= "AND i.indicacao_id = ? ";
    }

    $rs = $con->prepare("SELECT COUNT(v.id) AS qtd 
                         FROM 2024_voluntarios AS v
                         INNER JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona AND s.NR_SECAO = v.secao_numero 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                         LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = v.id
                         WHERE v.secao_numero_2 IS NOT NULL AND v.bairro_2 IS NOT NULL AND v.regional_2 IS NOT NULL AND YEAR(v.data_cadastro) = ? AND v.funcao_id = ? $condicao");

    $rs->bindValue(1, $ano);
    $rs->bindValue(2, $funcao_id);

    if ($pessoa_regional != "") {
        $rs->bindValue($contador2, $pessoa_regional);
        $contador2++;
    }

    if ($pessoa_zona != "") {
        $rs->bindValue($contador2, $pessoa_zona);
        $contador2++;
    }

    if ($pessoa_secao != "") {
        $rs->bindValue($contador2, $pessoa_secao);
        $contador2++;
    }

    if ($pessoa_situacao != "") {
        $rs->bindValue($contador2, $pessoa_situacao);
        $contador2++;
    }

    if ($pessoa_local != "") {
        $rs->bindValue($contador2, $pessoa_local);
        $contador2++;
    }

    if ($pessoa_indicacao != "") {
        $rs->bindValue($contador2, $pessoa_indicacao);
        $contador2++;
    }

    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op['qtd'];
    }

    if ($pessoa_funcao == 4 && $funcao_id != $pessoa_funcao || $pessoa_funcao == 5 && $funcao_id != $pessoa_funcao) {
        $resultado = 0;
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNA SE A QUANTIDADE DE VOLUNTÁRIOS CADASTRADOS
function qtd_voluntarios($treinamento, $ano, $pessoa_regional, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_situacao, $pessoa_local, $pessoa_indicacao) {

    $con = Conexao::getInstance();

    $resultado = 0;
    $contador2 = 2;
    $condicao = "";

    if ($pessoa_regional != "") {
        $condicao .= "AND lv.REGIONAL_ID = ? ";
    }

    if ($pessoa_funcao != "") {
        $condicao .= "AND v.funcao_id = ? ";
    }

    if ($pessoa_zona != "") {
        $condicao .= "AND v.zona = ? ";
    }

    if ($pessoa_secao != "") {
        $condicao .= "AND v.secao_numero = ? ";
    }

    if ($pessoa_situacao != "") {
        $condicao .= "AND v.status = ? ";
    }

    if ($pessoa_local != "") {
        $condicao .= "AND s.LOCAL_VOTACAO_ID = ? ";
    }

    if ($pessoa_indicacao != "") {
        $condicao .= "AND i.indicacao_id = ? ";
    }

    if ($treinamento != "" && $treinamento != 2) {
        $condicao .= "AND v.treinamento = ? ";
    } else if ($treinamento != "" && $treinamento == 2) {
        $condicao .= "AND v.treinamento IS NULL";
    }

    $rs = $con->prepare("SELECT COUNT(v.id) AS qtd 
                         FROM 2024_voluntarios AS v
                         INNER JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona AND s.NR_SECAO = v.secao_numero 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = v.id
                         WHERE YEAR(v.data_cadastro) = ? $condicao");
    $rs->bindValue(1, $ano);

    if ($pessoa_regional != "") {
        $rs->bindValue($contador2, $pessoa_regional);
        $contador2++;
    }

    if ($pessoa_funcao != "") {
        $rs->bindValue($contador2, $pessoa_funcao);
        $contador2++;
    }

    if ($pessoa_zona != "") {
        $rs->bindValue($contador2, $pessoa_zona);
        $contador2++;
    }

    if ($pessoa_secao != "") {
        $rs->bindValue($contador2, $pessoa_secao);
        $contador2++;
    }

    if ($pessoa_situacao != "") {
        $rs->bindValue($contador2, $pessoa_situacao);
        $contador2++;
    }

    if ($pessoa_local != "") {
        $rs->bindValue($contador2, $pessoa_local);
        $contador2++;
    }

    if ($pessoa_indicacao != "") {
        $rs->bindValue($contador2, $pessoa_indicacao);
        $contador2++;
    }

    if ($treinamento != "" && $treinamento != 2) {
        $rs->bindValue($contador2, $treinamento);
        $contador2++;
    }

    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op['qtd'];
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNA SE A QUANTIDADE DE VOLUNTÁRIOS CADASTRADOS
function qtd_voluntarios_distribuidos($ano, $pessoa_regional, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_local, $pessoa_indicacao) {

    $con = Conexao::getInstance();

    $resultado = 0;
    $contador2 = 2;
    $condicao = "";

    if ($pessoa_regional != "") {
        $condicao .= "AND lv.REGIONAL_ID = ? ";
    }

    if ($pessoa_funcao != "") {
        $condicao .= "AND v.funcao_id = ? ";
    }

    if ($pessoa_zona != "") {
        $condicao .= "AND v.zona = ? ";
    }

    if ($pessoa_secao != "") {
        $condicao .= "AND v.secao_numero = ? ";
    }

    if ($pessoa_local != "") {
        $condicao .= "AND s.LOCAL_VOTACAO_ID = ? ";
    }

    if ($pessoa_indicacao != "") {
        $condicao .= "AND i.indicacao_id = ? ";
    }

    $rs = $con->prepare("SELECT COUNT(v.id) AS qtd 
                         FROM 2024_voluntarios AS v
                         INNER JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona AND s.NR_SECAO = v.secao_numero 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = v.id
                         WHERE v.secao_numero_2 IS NOT NULL AND v.bairro_2 IS NOT NULL AND v.regional_2 IS NOT NULL AND YEAR(v.data_cadastro) = ? $condicao");
    $rs->bindValue(1, $ano);

    if ($pessoa_regional != "") {
        $rs->bindValue($contador2, $pessoa_regional);
        $contador2++;
    }

    if ($pessoa_funcao != "") {
        $rs->bindValue($contador2, $pessoa_funcao);
        $contador2++;
    }

    if ($pessoa_zona != "") {
        $rs->bindValue($contador2, $pessoa_zona);
        $contador2++;
    }

    if ($pessoa_secao != "") {
        $rs->bindValue($contador2, $pessoa_secao);
        $contador2++;
    }

    if ($pessoa_local != "") {
        $rs->bindValue($contador2, $pessoa_local);
        $contador2++;
    }

    if ($pessoa_indicacao != "") {
        $rs->bindValue($contador2, $pessoa_indicacao);
        $contador2++;
    }

    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op['qtd'];
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNA SE A QUANTIDADE DE VOLUNTÁRIOS ATIVOS
function qtd_status($treinamento, $ano, $pessoa_regional, $status, $pessoa_funcao, $pessoa_zona, $pessoa_secao, $pessoa_local, $pessoa_indicacao) {

    $con = Conexao::getInstance();

    $resultado = 0;
    $contador2 = 3;
    $condicao = "";

    if ($pessoa_regional != "") {
        $condicao .= "AND lv.REGIONAL_ID = ? ";
    }

    if ($pessoa_funcao != "") {
        $condicao .= "AND v.funcao_id = ? ";
    }

    if ($pessoa_zona != "") {
        $condicao .= "AND v.zona = ? ";
    }

    if ($pessoa_secao != "") {
        $condicao .= "AND v.secao_numero = ? ";
    }

    if ($pessoa_local != "") {
        $condicao .= "AND s.LOCAL_VOTACAO_ID = ? ";
    }

    if ($pessoa_indicacao != "") {
        $condicao .= "AND i.indicacao_id = ? ";
    }

    if ($treinamento != "" && $treinamento != 2) {
        $condicao .= "AND v.treinamento = ? ";
    } else if ($treinamento != "" && $treinamento == 2) {
        $condicao .= "AND v.treinamento IS NULL";
    }

    $rs = $con->prepare("SELECT COUNT(v.id) AS qtd 
                         FROM 2024_voluntarios AS v
                         INNER JOIN 2024_secoes AS s ON s.NR_ZONA = v.zona AND s.NR_SECAO = v.secao_numero 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         LEFT JOIN 2024_voluntarios_indicacoes AS i ON i.voluntario_id = v.id
                         WHERE YEAR(v.data_cadastro) = ? AND v.status = ? $condicao");

    $rs->bindValue(1, $ano);
    $rs->bindValue(2, $status);

    if ($pessoa_regional != "") {
        $rs->bindValue($contador2, $pessoa_regional);
        $contador2++;
    }

    if ($pessoa_funcao != "") {
        $rs->bindValue($contador2, $pessoa_funcao);
        $contador2++;
    }

    if ($pessoa_zona != "") {
        $rs->bindValue($contador2, $pessoa_zona);
        $contador2++;
    }

    if ($pessoa_secao != "") {
        $rs->bindValue($contador2, $pessoa_secao);
        $contador2++;
    }

    if ($pessoa_local != "") {
        $rs->bindValue($contador2, $pessoa_local);
        $contador2++;
    }

    if ($pessoa_indicacao != "") {
        $rs->bindValue($contador2, $pessoa_indicacao);
        $contador2++;
    }

    if ($treinamento != "" && $treinamento != 2) {
        $rs->bindValue($contador2, $treinamento);
        $contador2++;
    }

    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado = $op['qtd'];
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNA SE A SEÇÃO ESCOLHIDA JÁ FOI APURADA
function vf_secaoo_apuracao_2024($zona, $secao) {

    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT * FROM 2024_resultados WHERE ZONA = ? AND SECAO = ?  AND COD_CARGO = 'prefeito'");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $secao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    return $qtd > 0 ? true : false;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function nome_regional_zona_secao($zona, $secao) {
    $con = Conexao::getInstance();

    $nome = "";

    $rs = $con->prepare("SELECT r.nome     
                         FROM eleicoes_localidades_2022 AS e 
                         LEFT JOIN regional AS r ON r.id = e.REGIONAL_RBO 
                         WHERE e.ZONA = ? AND e.NR_SECAO = ?  
                         GROUP BY r.id");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $secao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $nome = $op['nome'];
    }

    return $nome;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO
function buscar_votos_candidato_2($candidato_id, $municipio_id) {

    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT rc.votos   
                             FROM resultado_candidato rc 
                             LEFT JOIN resultado AS r ON r.id = rc.resultado_id 
                             LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao  
                             WHERE rc.candidato_id = ? AND e.MUNICIPIO = ?
                             GROUP BY rc.id");

    $result2->bindValue(1, $candidato_id);
    $result2->bindValue(2, $municipio_id);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO
// TIPO: 1 = POR MUNICÍPIO, 0 = GERAL
function buscar_votos_candidato($candidato_id, $municipio_id, $tipo, $ano) {

    $votos = 0;

    $db = Conexao::getInstance();

    if ($tipo == 1) {
        $result2 = $db->prepare("SELECT rc2022.QTD_VOTOS  
                                 FROM 2024_resultados AS rc2022  
                                 WHERE rc2022.NUM_CANDIDATO = ? AND rc2022.COD_MUNICIPIO_TSE = ? AND rc2022.ANO_ELEICAO = ? AND rc2022.COD_CARGO = 'prefeito'");
        $result2->bindValue(1, $candidato_id);
        $result2->bindValue(2, $municipio_id);
        $result2->bindValue(3, $ano);
        $result2->execute();
    } else {
        $result2 = $db->prepare("SELECT rc2022.QTD_VOTOS 
                                 FROM 2024_resultados AS rc2022  
                                 WHERE rc2022.NUM_CANDIDATO = ? AND rc2022.COD_CARGO = 'prefeito'");
        $result2->bindValue(1, $candidato_id);
        $result2->execute();
    }

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['QTD_VOTOS'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO
// TIPO: 1 = POR MUNICÍPIO, 0 = GERAL
function buscar_votos_brancos($municipio_id, $ano) {

    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT rc2022.QTD_VOTOS  
                                 FROM 2024_resultados AS rc2022  
                                 WHERE rc2022.COD_MUNICIPIO_TSE = ? AND rc2022.ANO_ELEICAO = ? AND rc2022.TIPO_VOTO = 'branco' AND rc2022.COD_CARGO = 'prefeito'");
    $result2->bindValue(1, $municipio_id);
    $result2->bindValue(2, $ano);
    $result2->execute();

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['QTD_VOTOS'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO
// TIPO: 1 = POR MUNICÍPIO, 0 = GERAL
function buscar_votos_nulos($municipio_id, $ano) {

    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT rc2022.QTD_VOTOS  
                                 FROM 2024_resultados AS rc2022  
                                 WHERE rc2022.COD_MUNICIPIO_TSE = ? AND rc2022.ANO_ELEICAO = ? AND rc2022.TIPO_VOTO = 'nulo' AND rc2022.COD_CARGO = 'prefeito'");
    $result2->bindValue(1, $municipio_id);
    $result2->bindValue(2, $ano);
    $result2->execute();

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['QTD_VOTOS'];
    }

    return $votos;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function tamanho_arquivo($arquivo) {
    $tamanhoarquivo = @filesize($arquivo);

    /* Medidas */
    $medidas = array('KB', 'MB', 'GB', 'TB');

    /* Se for menor que 1KB arredonda para 1KB */
    if ($tamanhoarquivo < 999) {
        $tamanhoarquivo = 1000;
    }

    for ($i = 0; $tamanhoarquivo > 999; $i++) {
        $tamanhoarquivo /= 1024;
    }

    return round($tamanhoarquivo) . $medidas[$i - 1];
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function extensao($arquivo) {
    $arquivo = strtolower($arquivo);
    $explode = explode(".", $arquivo);
    $arquivo = end($explode);

    return ($arquivo);
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function buscar_secoes($locais, $municipio) {
    $con = Conexao::getInstance();

    $dados = "";

    $rs = $con->prepare("SELECT NR_SECAO   
                         FROM eleicoes_localidades_2022 
                         WHERE LOCAL_VOTACAO = ? AND MUNICIPIO = ?");
    $rs->bindValue(1, $locais);
    $rs->bindValue(2, $municipio);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $dados .= $op['NR_SECAO'] . ", ";
    }

    return $dados;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function gerar_mascara_lat_long($mascara, $qtd) {

    if ($qtd == 1) {
        $guard = "-#.";
    } else if ($qtd == 2) {
        $guard = "-##.";
    } else {
        $guard = "-###.";
    }

    $masc = strlen($mascara) - $qtd;

    for ($i = 1; ($i <= $masc); $i++) {
        $guard .= "#";
    }

    return $guard;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function gerar_mascara_lat($mascara, $qtd) {

    if ($qtd == 2) {
        $guard = "##.";
    } else {
        $guard = "###.";
    }

    $masc = strlen($mascara) - $qtd;

    for ($i = 1; ($i <= $masc); $i++) {
        $guard .= "#";
    }

    return $guard;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function gerar_mascara_long($mascara, $qtd) {

    $guard = "###.";

    $masc = strlen($mascara) - $qtd;

    for ($i = 1; ($i <= $masc); $i++) {
        $guard .= "#";
    }

    return $guard;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function format_string($mask, $str, $ch = '#') {
    $c = 0;
    $rs = '';

    /*
      Aqui usamos strlen() pois não há preocupação com o charset da máscara.
     */
    for ($i = 0; $i < strlen($mask); $i++) {
        if ($mask[$i] == $ch) {
            $rs .= $str[$c];
            $c++;
        } else {
            $rs .= $mask[$i];
        }
    }

    return $rs;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function nome_cores($candidato_id) {
    if ($candidato_id == 36) {
        return "st11";
    } else if ($candidato_id == 44) {
        return "st17";
    } else if ($candidato_id == 13) {
        return "st16";
    } else if ($candidato_id == 15) {
        return "st15";
    } else if ($candidato_id == 11) {
        return "st13";
    } else if ($candidato_id == 50) {
        return "st22";
    } else if ($candidato_id == 55) {
        return "st14";
    } else {
        return "";
    }
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO  st12
//------------------------------------------------------------------------------
function cor_candidato($candidato_id) {
    if ($candidato_id == 22) {
        return "st22";
    } else if ($candidato_id == 30) {
        return "st17";
    } else if ($candidato_id == 40) {
        return "st16";
    } else if ($candidato_id == 15) {
        return "st11";
    } else {
        return "";
    }
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function nome_candidato($candidato_id) {
    if ($candidato_id == 22) {
        return "BOCALOM";
    } else if ($candidato_id == 30) {
        return "JARUDE";
    } else if ($candidato_id == 40) {
        return "JENILSON";
    } else if ($candidato_id == 15) {
        return "MARCUS";
    } else {
        return "";
    }
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function carregar_cor_regional($regional_id) {
    $cor = "st1";

    $cand_11 = carregar_votos_regional(11, $regional_id, 1);
    $cand_13 = carregar_votos_regional(13, $regional_id, 1);
    $cand_15 = carregar_votos_regional(15, $regional_id, 1);

    $cand_55 = carregar_votos_regional(55, $regional_id, 1);
    $cand_36 = carregar_votos_regional(36, $regional_id, 1);
    $cand_44 = carregar_votos_regional(44, $regional_id, 1);
    $cand_50 = carregar_votos_regional(50, $regional_id, 1);

    if ($cand_36 > $cand_44 && $cand_36 > $cand_15 && $cand_36 > $cand_11 && $cand_36 > $cand_13 && $cand_36 > $cand_50 && $cand_36 > $cand_55) {
        $cor = "num36";
    } else if ($cand_44 > $cand_36 && $cand_44 > $cand_15 && $cand_44 > $cand_11 && $cand_44 > $cand_13 && $cand_44 > $cand_50 && $cand_44 > $cand_55) {
        $cor = "num44";
    } else if ($cand_15 > $cand_44 && $cand_15 > $cand_36 && $cand_15 > $cand_11 && $cand_15 > $cand_13 && $cand_15 > $cand_50 && $cand_15 > $cand_55) {
        $cor = "num15";
    } else if ($cand_11 > $cand_44 && $cand_11 > $cand_36 && $cand_11 > $cand_15 && $cand_11 > $cand_13 && $cand_11 > $cand_50 && $cand_11 > $cand_55) {
        $cor = "num11";
    } else if ($cand_13 > $cand_44 && $cand_13 > $cand_36 && $cand_13 > $cand_15 && $cand_13 > $cand_11 && $cand_13 > $cand_50 && $cand_13 > $cand_55) {
        $cor = "num13";
    } else if ($cand_50 > $cand_44 && $cand_50 > $cand_36 && $cand_50 > $cand_15 && $cand_50 > $cand_11 && $cand_50 > $cand_13 && $cand_50 > $cand_55) {
        $cor = "num50";
    } else if ($cand_55 > $cand_44 && $cand_55 > $cand_36 && $cand_55 > $cand_15 && $cand_55 > $cand_11 && $cand_55 > $cand_13 && $cand_55 > $cand_50) {
        $cor = "num55";
    }

    return $cor;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function carregar_cor_bairro($cad22, $cad15, $cad30, $cad40, $id) {

    $cor = "st1";

    $card_22 = $cad22 == 1 ? carregar_votos_bairro(22, 'nominal', $id, 2024) : 0;
    $card_30 = $cad30 == 1 ? carregar_votos_bairro(30, 'nominal', $id, 2024) : 0;
    $cand_15 = $cad15 == 1 ? carregar_votos_bairro(15, 'nominal', $id, 2024) : 0;
    $cand_40 = $cad40 == 1 ? carregar_votos_bairro(40, 'nominal', $id, 2024) : 0;

    if ($cad22 == 1 && $card_22 > $card_30 && $card_22 > $cand_15 && $card_22 > $cand_40) {
        $cor = "num50";
    } else if ($cad30 == 1 && $card_30 > $card_22 && $card_30 > $cand_15 && $card_30 > $cand_40) {
        $cor = "num44";
    } else if ($cad15 == 1 && $cand_15 > $card_30 && $cand_15 > $card_22 && $cand_15 > $cand_40) {
        $cor = "num15";
    } else if ($cad40 == 1 && $cand_40 > $card_30 && $cand_40 > $card_22 && $cand_40 > $cand_15) {
        $cor = "num36";
    }

    return $cor;
}

//FUNÇÃO PARA RETORNAR A QUANTIDADE APTA URBANA
//------------------------------------------------------------------------------
function qtd_urbana($municipio) {
    $con = Conexao::getInstance();

    $dadoss = 0;

    $rs = $con->prepare("SELECT SUM(QT_ELEITOR) AS TOTAL   
                         FROM eleicoes_localidades_2022 
                         WHERE MUNICIPIO = ? AND REGIONAL_RBO <> 'ZONA RURAL'    
                         GROUP BY MUNICIPIO
                         ORDER BY MUNICIPIO");
    $rs->bindValue(1, $municipio);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $dadoss = $op['TOTAL'];
    }

    return $dadoss;
}

//FUNÇÃO PARA RETORNAR A QUANTIDADE APTA URBANA
//------------------------------------------------------------------------------
function qtd_rural($municipio) {
    $con = Conexao::getInstance();

    $dadoss = 0;

    $rs = $con->prepare("SELECT SUM(QT_ELEITOR) AS TOTAL   
                         FROM eleicoes_localidades_2022 
                         WHERE MUNICIPIO = ? AND REGIONAL_RBO = 'ZONA RURAL'   
                         GROUP BY MUNICIPIO
                         ORDER BY MUNICIPIO");
    $rs->bindValue(1, $municipio);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $dadoss = $op['TOTAL'];
    }

    return $dadoss;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function nome_regional($id) {
    $con = Conexao::getInstance();

    $nome = "";

    $rs = $con->prepare("SELECT bb.REGIONAL_NOME     
                         FROM bsc_bairros bb 
                         WHERE bb.ID = ?");
    $rs->bindValue(1, $id);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $nome = $op['REGIONAL_NOME'];
    }

    return $nome;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function nome_bairro($id) {
    $con = Conexao::getInstance();

    $nome = "";

    $rs = $con->prepare("SELECT bb.NM_BAIRRO    
                         FROM bsc_bairros bb 
                         WHERE bb.ID = ?");
    $rs->bindValue(1, $id);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $nome = $op['NM_BAIRRO'];
    }

    return $nome;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function carregar_secao_bairro($bairro) {
    $con = Conexao::getInstance();

    $cont = 1;
    $secoes = "";

    $rs = $con->prepare("SELECT s.NR_SECAO AS secao 
                         FROM 2024_secoes AS s 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         WHERE lv.CD_BAIRRO = ?
                         GROUP BY s.ID");
    $rs->bindValue(1, $bairro);
    $rs->execute();

    $qtd = $rs->rowCount();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        if ($cont < $qtd) {
            $secoes .= "" . $op['secao'] . ", ";
        } else {
            $secoes .= "" . $op['secao'];
        }
        $cont++;
    }

    return $secoes;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function carregar_votos_bairro_totoal($bairro, $tipo, $ano) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT r.QTD_VOTOS 
                             FROM 2024_resultados AS r
                             INNER JOIN 2024_secoes AS s ON s.NR_ZONA = r.ZONA AND s.NR_SECAO = r.SECAO
                             INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                             WHERE r.TIPO_VOTO = ? AND lv.CD_BAIRRO = ? AND r.ANO_ELEICAO = ? AND r.COD_CARGO = 'prefeito'
                             GROUP BY r.ID");
    $result2->bindValue(1, $tipo);
    $result2->bindValue(2, $bairro);
    $result2->bindValue(3, $ano);
    $result2->execute();

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['QTD_VOTOS'];
    }

    return $votos;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR BAIRRO
//------------------------------------------------------------------------------
function carregar_votos_bairro($candidato_id, $tipo, $bairro, $ano_eleicao) {
    $con = Conexao::getInstance();

    $votos = 0;

    $rs = $con->prepare("SELECT r.QTD_VOTOS 
                         FROM 2024_resultados AS r
                         INNER JOIN 2024_secoes AS s ON s.NR_ZONA = r.ZONA AND s.NR_SECAO = r.SECAO
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                         WHERE r.TIPO_VOTO = ? AND r.NUM_CANDIDATO = ? AND lv.CD_BAIRRO = ? AND r.ANO_ELEICAO = ? AND r.COD_CARGO = 'prefeito'
                         GROUP BY r.ID");
    $rs->bindValue(1, $tipo);
    $rs->bindValue(2, $candidato_id);
    $rs->bindValue(3, $bairro);
    $rs->bindValue(4, $ano_eleicao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $votos += $op['QTD_VOTOS'];
    }

    return $votos;
}

//FUNÇÃO PARA CARREGAR OS VOTOS DOS CANDIDATOS POR REGIONAL
//------------------------------------------------------------------------------
function carregar_votos_regional($candidato_id, $regional, $tipo, $ano_eleicao) {

    $con = Conexao::getInstance();

    $votos = 0;

    $rs = $con->prepare("SELECT r.QTD_VOTOS 
                         FROM 2024_resultados AS r
                         INNER JOIN 2024_secoes AS s ON s.NR_ZONA = r.ZONA AND s.NR_SECAO = r.SECAO
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID
                         WHERE r.TIPO_VOTO = ? AND r.NUM_CANDIDATO = ? AND lv.REGIONAL_ID = ? AND r.ANO_ELEICAO = ? AND r.COD_CARGO = 'prefeito'
                         GROUP BY r.ID");
    $rs->bindValue(1, $tipo);
    $rs->bindValue(2, $candidato_id);
    $rs->bindValue(3, $regional);
    $rs->bindValue(4, $ano_eleicao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $votos += $op['QTD_VOTOS'];
    }

    return $votos;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNA SE A SEÇÃO ESCOLHIDA JÁ FOI APURADA
function vf_secaoo_apuracao($zona, $secao) {

    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT * FROM resultado WHERE zona = ? AND secao = ? ");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $secao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    return $qtd > 0 ? true : false;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A REGIONAL PELA ZONA, SECAO E LOCAL
function carregar_regional_bairro($bairro) {
    $con = Conexao::getInstance();

    $regional = "";

    if ($bairro != "" && $bairro != NULL) {
        $rs = $con->prepare("SELECT REGIONAL_RBO FROM eleicoes_localidades_2022 WHERE BAIRRO = ?");
        $rs->bindValue(1, $bairro);
        $rs->execute();
    } else {
        return $regional;
    }

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $regional = $op['REGIONAL_RBO'];
    }

    return $regional;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A REGIONAL PELA ZONA, SECAO E LOCAL
function carregar_regional_2($regional_id, $secao, $local) {
    $con = Conexao::getInstance();

    $regional = "";

    if ($regional_id != "" && $regional_id != NULL && $secao != "" && $secao != NULL && $local != "" && $local != NULL) {
        $rs = $con->prepare("SELECT REGIONAL_RBO FROM eleicoes_localidades_2022 WHERE REGIONAL_RBO = ? AND NR_SECAO = ? AND LOCAL_VOTACAO = ?");
        $rs->bindValue(1, $regional_id);
        $rs->bindValue(2, $secao);
        $rs->bindValue(3, $local);
        $rs->execute();
    } else if ($regional_id != "" && $regional_id != NULL && $secao != "" && $secao != NULL) {
        $rs = $con->prepare("SELECT REGIONAL_RBO FROM eleicoes_localidades_2022 WHERE ZONA = ? AND NR_SECAO = ?");
        $rs->bindValue(1, $regional_id);
        $rs->bindValue(2, $secao);
        $rs->execute();
    } else if ($secao != "" && $secao != NULL && $local != "" && $local != NULL) {
        $rs = $con->prepare("SELECT REGIONAL_RBO FROM eleicoes_localidades_2022 WHERE NR_SECAO = ? AND LOCAL_VOTACAO = ?");
        $rs->bindValue(1, $secao);
        $rs->bindValue(2, $local);
        $rs->execute();
    } else if ($regional_id != "" && $regional_id != NULL) {
        return $regional;
    } else {
        return $regional;
    }

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $regional = $op['REGIONAL_RBO'];
    }

    return $regional;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A REGIONAL PELA ZONA, SECAO E LOCAL
function carregar_regional($zona, $secao, $local) {
    $con = Conexao::getInstance();

    $regional = "";

    if ($zona != "" && $zona != NULL && $secao != "" && $secao != NULL && $local != "" && $local != NULL) {
        $rs = $con->prepare("SELECT REGIONAL_RBO FROM eleicoes_localidades_2022 WHERE ZONA = ? AND NR_SECAO = ? AND LOCAL_VOTACAO = ?");
        $rs->bindValue(1, $zona);
        $rs->bindValue(2, $secao);
        $rs->bindValue(3, $local);
        $rs->execute();
    } else if ($zona != "" && $zona != NULL && $secao != "" && $secao != NULL) {
        $rs = $con->prepare("SELECT REGIONAL_RBO FROM eleicoes_localidades_2022 WHERE ZONA = ? AND NR_SECAO = ?");
        $rs->bindValue(1, $zona);
        $rs->bindValue(2, $secao);
        $rs->execute();
    } else if ($zona != "" && $zona != NULL) {
        return $regional;
    } else {
        return $regional;
    }

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $regional = $op['REGIONAL_RBO'];
    }

    return $regional;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE PORCENTAGEM APTOS DE VOTAÇÃO POR REGIONAL
function retornar_agregacao($zona, $secao) {
    $con = Conexao::getInstance();

    $resultado = "";

    $rs = $con->prepare("SELECT e.NR_SECAO AS SECAO    
                         FROM 2024_secoes e 
                         WHERE e.NR_ZONA = ? AND e.NR_SECAO_PRINCIPAL = ? AND e.TIPO = 'Agregada'
                         ORDER BY e.NR_SECAO ASC");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $secao);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado .= ", " . $op['SECAO'];
    }


    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE PORCENTAGEM APTOS DE VOTAÇÃO POR REGIONAL
function porcentagem_aptos($regional) {
    $con = Conexao::getInstance();

    $qtd = 0;
    $qtd_geral = 0;

    $rs = $con->prepare("SELECT SUM(QT_ELEITOR) AS qtd FROM eleicoes_localidades_2022 WHERE REGIONAL_RBO = ?");
    $rs->bindValue(1, $regional);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['qtd'];
    }

    $rs2 = $con->prepare("SELECT SUM(QT_ELEITOR) AS qtd FROM eleicoes_localidades_2022 WHERE 1");
    $rs2->bindValue(1, $regional);
    $rs2->execute();
    while ($op2 = $rs2->fetch(PDO::FETCH_ASSOC)) {
        $qtd_geral = $op2['qtd'];
    }

    return (($qtd / $qtd_geral) * 100);
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA FORMATAR TELEFONE
function formatarTelefone($telefone) {
    // Remove qualquer caractere que não seja número
    $telefone = preg_replace('/\D/', '', $telefone);

    // Verifica se é telefone fixo ou celular
    if (strlen($telefone) === 9) {
        // Telefone celular no formato: 99999-9999
        return preg_replace('/(\d{5})(\d{4})/', '$1-$2', $telefone);
    } elseif (strlen($telefone) === 8) {
        // Telefone fixo no formato: 9999-9999
        return preg_replace('/(\d{4})(\d{4})/', '$1-$2', $telefone);
    } elseif (strlen($telefone) === 10) {
        // Telefone fixo com DDD no formato: (99) 9999-9999
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    } elseif (strlen($telefone) === 11) {
        // Telefone celular com DDD no formato: (99) 99999-9999
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    } else {
        // Retorna o número sem formatação se não tiver tamanho esperado
        return $telefone;
    }
}

//------------------------------------------------------------------------------
function retorna_secoes($local_votacao_id) {
    $con = Conexao::getInstance();

    $resultado = "";

    $rs = $con->prepare("SELECT e.NR_SECAO AS SECAO    
                         FROM 2024_secoes e 
                         WHERE e.LOCAL_VOTACAO_ID = ? AND e.TIPO = 'Principal'
                         ORDER BY e.NR_SECAO ASC");
    $rs->bindValue(1, $local_votacao_id);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {

        if ($resultado == "") {
            $resultado .= $op['SECAO'];
        } else {
            $resultado .= ", " . $op['SECAO'];
        }
    }


    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE URNAS DE VOTAÇÃO POR REGIONAL
function qtd_urnas_local($local_id) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT COUNT(s.ID) AS qtd 
                         FROM 2024_secoes AS s 
                         WHERE s.TIPO = 'Principal' AND s.LOCAL_VOTACAO_ID = ?");
    $rs->bindValue(1, $local_id);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['qtd'];
    }

    return $qtd;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE URNAS DE VOTAÇÃO POR REGIONAL
function urnas($regional) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT COUNT(ID) AS qtd FROM eleicoes_localidades_2022 WHERE REGIONAL_RBO = ?");
    $rs->bindValue(1, $regional);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['qtd'];
    }

    return $qtd;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE APTOS DE VOTAÇÃO POR REGIONAL
function aptos($regional) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT SUM(QT_ELEITOR) AS qtd FROM eleicoes_localidades_2022 WHERE REGIONAL_RBO = ?");
    $rs->bindValue(1, $regional);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $op['qtd'];
    }

    $rs = fdec($qtd);

    $rs = explode(",", $rs);

    return $rs[0];
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR OS ID DOS LOCAIS QUE JÁ FORAM VINCULADOS A ALGUM FISCAL
function ids_pessoas_vinculadas() {

    $con = Conexao::getInstance();

    $resultado = "";

    $rs = $con->prepare("SELECT e.ID AS CODIGO    
                         FROM 2024_voluntarios p 
                         INNER JOIN eleicoes_localidades_2022 AS e ON e.ZONA = p.ZONA_2 AND e.NR_SECAO = p.secao_numero_2 
                         WHERE p.zona_2 IS NOT NULL AND p.secao_numero_2 IS NOT NULL AND p.funcao_id = 5 AND p.status = 1
                         GROUP BY CODIGO");
    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $resultado .= $op['CODIGO'] . ", ";
    }

    $resultado .= "0";

    return $resultado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE FISCAIS VINCULADOS A UMA ZONA E SESSAO
function qtd_pessoas_regional($regional_id, $fucao_id) {

    $con = Conexao::getInstance();

    $rs = $con->prepare("SELECT *      
                         FROM 2024_voluntarios p
                         WHERE p.regional_2 = ? AND p.funcao_id = ? 
                         GROUP BY p.id");
    $rs->bindValue(1, $regional_id);
    $rs->bindValue(2, $fucao_id);
    $rs->execute();

    $qtd = $rs->rowCount();

    return $qtd;
}

//------------------------------------------------------------------------------
function mascararCPF($cpf) {
    // Remove qualquer máscara já existente no CPF (pontos, traços)
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false; // Retorna falso se não for um CPF válido
    }

    // Aplica a máscara conforme o padrão solicitado: ***.596.***-*5
    $cpfMascarado = '***.' . substr($cpf, 3, 3) . '.***-*' . substr($cpf, -2, 1);

    return $cpfMascarado;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE FISCAIS VINCULADOS A UMA ZONA E SESSAO
function qtd_pessoas_local($pessoa_local) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT s.ID
                         FROM 2024_secoes AS s 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         WHERE lv.ID = ? AND s.TIPO = 'Principal' 
                         GROUP BY s.ID");
    $rs->bindValue(1, $pessoa_local);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    return $qtd;
}

//------------------------------------------------------------------------------
// CARREGAR SECAO VAZIA DE UM LOCAL DE VOTACAO
function carregar_secao_vazio($pessoa_local, $fucao_id) {
    $con = Conexao::getInstance();

    $resultado = "";

    $rs = $con->prepare("SELECT s.NR_ZONA, s.NR_SECAO 
                         FROM 2024_secoes AS s 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         WHERE lv.ID = ? AND s.TIPO = 'Principal' 
                         GROUP BY s.ID");
    $rs->bindValue(1, $pessoa_local);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {

        if (qtd_pessoas_sessao($op['NR_ZONA'], $op['NR_SECAO'], $fucao_id) == 0) {
            $resultado = $op['NR_SECAO'];
        }
    }

    return $resultado;
}

//------------------------------------------------------------------------------
// 1 - SE TIVER 1 SEÇÃO PRINCIPAL POR LOCAL DESCARTAR
// 2 - SE TIVER 2 SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 1 
// 3 - SE TIVER 3 A 5 SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 1 PARA CADA SEÇÃO PRINCIPAL
// 4 - SE TIVER 6 OU MAIS SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 2 PARA CADA SEÇÃO PRINCIPAL
function vf_regras_fiscais($pessoa_zona, $vf_distribuicao, $pessoa_local) {

    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT s.ID
                         FROM 2024_secoes AS s 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         WHERE lv.NR_ZONA = ? AND lv.ID = ? AND s.TIPO = 'Principal' 
                         GROUP BY s.ID");
    $rs->bindValue(1, $pessoa_zona);
    $rs->bindValue(2, $pessoa_local);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    if ($qtd == 1) {// 1 - SE TIVER 1 SEÇÃO PRINCIPAL POR LOCAL DESCARTAR
        return false;
    } else if ($qtd == 2) {// 2 - SE TIVER 2 SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 1 
        if (vf_distribuicao_local($pessoa_local, 5) == 0) {
            return true;
        } else {
            return false;
        }
    } else if ($qtd >= 3 && $qtd <= 5) {// 3 - SE TIVER 3 A 5 SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 1 PARA CADA SEÇÃO PRINCIPAL
        if (vf_distribuicao_local($pessoa_local, 5) <= 5 && $vf_distribuicao == 0) {
            return true;
        } else {
            return false;
        }
    } else {// 4 - SE TIVER 6 OU MAIS SEÇÕES PRINCIPAIS POR LOCAL PERMITIR 2 PARA CADA SEÇÃO PRINCIPAL
        return true;
    }
}

//------------------------------------------------------------------------------
function vf_distribuicao_local($pessoa_local, $fucao_id) {

    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT s.NR_ZONA, s.NR_SECAO           
                         FROM 2024_secoes AS s
                         WHERE s.TIPO = 'Principal' AND s.LOCAL_VOTACAO_ID = ?");
    $rs->bindValue(1, $pessoa_local);
    $rs->execute();

    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd += qtd_pessoas_sessao($op['NR_ZONA'], $op['NR_SECAO'], $fucao_id);
    }

    return $qtd;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA VERIFICAR SE O LOCAL DE VOTAÇÃO TEM 3 OU MAIS SEÇÕES PRINCIPAIS PARA ALOCAR UM SUPREVISOR
function vf_regra_sessao($pessoa_zona, $pessoa_local) {

    $con = Conexao::getInstance();

    $vf = true;

    $qtd = 0;

    $rs = $con->prepare("SELECT s.ID
                         FROM 2024_secoes AS s 
                         INNER JOIN 2024_locais_votacao AS lv ON lv.ID = s.LOCAL_VOTACAO_ID 
                         WHERE lv.NR_ZONA = ? AND lv.ID = ? AND s.TIPO = 'Principal' 
                         GROUP BY s.ID");
    $rs->bindValue(1, $pessoa_zona);
    $rs->bindValue(2, $pessoa_local);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    if ($qtd <= 2) {
        $vf = false;
    }

    return $vf;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE FISCAIS VINCULADOS A UMA ZONA E SESSAO
function qtd_pessoas_local_2024($local, $funcao_id) {

    $con = Conexao::getInstance();
    $qtd = 0;
    $teste123 = "";

    // Verifique os valores antes de executar a query
    echo("Local: " . $local . '<br/>');
    echo("Funcao ID: " . $funcao_id . '<br/>');

    $rs = $con->prepare("SELECT id, nome, local_votacao_2, funcao_id         
                         FROM 2024_voluntarios 
                         WHERE local_votacao_2 = ? AND funcao_id = ?");

    $rs->bindValue(1, (int) $local, PDO::PARAM_INT);
    $rs->bindValue(2, (int) $funcao_id, PDO::PARAM_INT);

    $rs->execute();

    // Verifique se a query retornou algo
    if ($rs->rowCount() > 0) {
        echo("Query retornou registros" . '<br/>');
    } else {
        echo("Query não retornou registros" . '<br/>');
    }

    while ($opcao = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;

        if ($local == 4 && $funcao_id == 4) {
            $teste123 = "ID: " . $opcao['id'] . " - N: " . $opcao['nome'] . " - L: " . $opcao['local_votacao_2'] . " - F: " . $opcao['funcao_id'];
        }
    }

    if ($local == 4 && $funcao_id == 4) {
        echo $teste123 . " = " . $qtd . "<br/><br/>";
    }

    return 0;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE FISCAIS VINCULADOS A UMA ZONA E SESSAO
function qtd_pessoas_sessao($zona, $sessao, $fucao_id) {

    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT *      
                         FROM 2024_voluntarios p
                         WHERE p.zona_2 = ? AND p.secao_numero_2 = ? AND p.funcao_id = ?");
    $rs->bindValue(1, $zona);
    $rs->bindValue(2, $sessao);
    $rs->bindValue(3, $fucao_id);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    return $qtd;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DE FISCAIS DE VOTAÇÃO POR REGIONAL
function fiscais($regional, $tipo) {
    $con = Conexao::getInstance();

    $qtd = 0;

    if ($tipo == 4) {
        $rs = $con->prepare("SELECT BAIRRO FROM eleicoes_localidades_2022 WHERE REGIONAL_RBO = ? GROUP BY BAIRRO");
        $rs->bindValue(1, $regional);
        $rs->execute();
        while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {

            $rs2 = $con->prepare("SELECT id FROM 2024_voluntarios WHERE bairro_2 = ? AND funcao_id = ?");
            $rs2->bindValue(1, $op['BAIRRO']);
            $rs2->bindValue(2, $tipo);
            $rs2->execute();

            $qtd = $rs2->rowCount();
        }
    } else {
        $rs = $con->prepare("SELECT id FROM 2024_voluntarios WHERE regional_2 = ? AND funcao_id = ?");
        $rs->bindValue(1, $regional);
        $rs->bindValue(2, $tipo);
        $rs->execute();

        $qtd = $rs->rowCount();
    }

    return $qtd;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR A QUANTIDADE DOS LOCAIS DE VOTAÇÃO POR REGIONAL
function locais($regional) {
    $con = Conexao::getInstance();

    $qtd = 0;

    $rs = $con->prepare("SELECT LOCAL_VOTACAO FROM eleicoes_localidades_2022 WHERE REGIONAL_RBO = ? GROUP BY LOCAL_VOTACAO");
    $rs->bindValue(1, $regional);
    $rs->execute();
    while ($op = $rs->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    return $qtd;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function permissoes_usuario($usuario_id) {
    $db = Conexao::getInstance();

    $cont = 1;
    $rs = "Nenhuma permissão";
    $rs2 = "";

    $result = $db->prepare("SELECT sn.nome, sn.descricao   
                            FROM seg_permissoes AS sp 
                            LEFT JOIN seg_niveis AS sn ON sn.nivel = sp.nivel 
                            WHERE sp.user_id = ?
                            ORDER BY sn.nivel ASC");
    $result->bindValue(1, $usuario_id);
    $result->execute();
    while ($permissoes = $result->fetch(PDO::FETCH_ASSOC)) {
        $rs2 .= $cont . " - " . $permissoes['nome'] . "<br/>";
        $cont++;
    }

    if ($rs2 != "") {
        $rs = $rs2;
    }

    return $rs;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function usuario_grupo($usuario_id, $grupo_id) {
    $db = Conexao::getInstance();

    $rs = false;

    $result = $db->prepare("SELECT sgu.usuario_id As codigo  
                            FROM seg_grupo_nivel AS sgn 
                            LEFT JOIN seg_grupo_usuario AS sgu ON sgu.grupo_nivel_id = sgn.id 
                            WHERE sgu.usuario_id = ? AND sgn.grupo_id = ?");
    $result->bindValue(1, $usuario_id);
    $result->bindValue(2, $grupo_id);
    $result->execute();
    while ($grupos = $result->fetch(PDO::FETCH_ASSOC)) {
        $rs = true;
    }

    return $rs;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function usuarios_vinculados($id) {

    $db = Conexao::getInstance();

    $cont = 1;
    $rs = "Nenhuma usuário vinculado";
    $rs2 = "";

    $result = $db->prepare("SELECT su.id As codigo, su.nome AS usuario   
                            FROM seg_grupo_nivel AS sgn 
                            LEFT JOIN seg_grupo_usuario AS sgu ON sgu.grupo_nivel_id = sgn.id 
                            INNER JOIN seg_usuarios AS su ON su.id = sgu.usuario_id 
                            WHERE sgn.grupo_id = ?   
                            GROUP BY sgu.usuario_id");
    $result->bindValue(1, $id);
    $result->execute();
    while ($permissoes = $result->fetch(PDO::FETCH_ASSOC)) {
        if ($permissoes['codigo'] == 1) {
            $rs2 .= $cont . " - " . $permissoes['usuario'] . "<br/>";
        } else {
            $rs2 .= $cont . " - <a target='_blank' href='" . PORTAL_URL . "view/permissoes/cadastrar/" . $permissoes['codigo'] . "' style='text-decoration: underline'>" . $permissoes['usuario'] . "</a><br/>";
        }

        $cont++;
    }

    if ($rs2 != "") {
        $rs = $rs2;
    }

    return $rs;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function carregar_grupo($id) {

    $db = Conexao::getInstance();

    $cont = 1;
    $rs = "Nenhum grupo";
    $rs2 = "";

    $result = $db->prepare("SELECT sg.nome    
                            FROM seg_grupo_nivel AS sp 
                            LEFT JOIN seg_grupo AS sg ON sg.id = sp.grupo_id 
                            LEFT JOIN seg_grupo_usuario AS sgu ON sgu.grupo_nivel_id = sp.id 
                            WHERE sgu.usuario_id = ?
                            GROUP BY sg.nome 
                            ORDER BY sg.nome ASC");
    $result->bindValue(1, $id);
    $result->execute();
    while ($permissoes = $result->fetch(PDO::FETCH_ASSOC)) {
        $rs2 .= $cont . " - " . $permissoes['nome'] . "<br/>";
        $cont++;
    }

    if ($rs2 != "") {
        $rs = $rs2;
    }

    return $rs;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function carregar_grupo_permissoes($id) {

    $db = Conexao::getInstance();

    $cont = 1;
    $rs = "Nenhuma permissão";
    $rs2 = "";

    $result = $db->prepare("SELECT sn.nome, sn.descricao   
                            FROM seg_grupo_nivel AS sp 
                            LEFT JOIN seg_niveis AS sn ON sn.nivel = sp.nivel_id  
                            WHERE sp.grupo_id = ?
                            ORDER BY sn.nivel ASC");
    $result->bindValue(1, $id);
    $result->execute();
    while ($permissoes = $result->fetch(PDO::FETCH_ASSOC)) {
        $rs2 .= $cont . " - " . $permissoes['nome'] . "<br/>";
        $cont++;
    }

    if ($rs2 != "") {
        $rs = $rs2;
    }

    return $rs;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function carregar_permissoes($id) {

    $db = Conexao::getInstance();

    $cont = 1;
    $rs = "Nenhuma permissão";
    $rs2 = "";

    $result = $db->prepare("SELECT sn.nome, sn.descricao   
                            FROM seg_permissoes AS sp 
                            LEFT JOIN seg_niveis AS sn ON sn.nivel = sp.nivel 
                            WHERE sp.user_id = ?
                            ORDER BY sn.nivel ASC");
    $result->bindValue(1, $id);
    $result->execute();
    while ($permissoes = $result->fetch(PDO::FETCH_ASSOC)) {
        $rs2 .= $cont . " - " . $permissoes['nome'] . "<br/>";
        $cont++;
    }

    if ($rs2 != "") {
        $rs = $rs2;
    }

    return $rs;
}

//------------------------------------------------------------------------------
// FUNÇÃO PARA RETORNAR O STATUS
function status($codigo) {
    if ($codigo == 1)
        return "Ativo";
    else
        return "Inativo";
}

//------------------------------------------------------------------------------
function vf_online($id) {
    // SE O USUÁRIO NÃO REALIZAR NENHUMA AÇÃO EM 30 MINUTOS, ENTÃO ELE É CONSIDERADO COMO OFFLINE
    $db = Conexao::getInstance();

    $rs = $db->prepare("SELECT id 
                      FROM seg_sessoes
                      WHERE usuario_id = ? AND DATE(atualizacao) = DATE(NOW()) AND HOUR(atualizacao) = HOUR(NOW())
                      AND MINUTE(atualizacao) >= (MINUTE(NOW())-30)"); // 30 MINUTOS PASSADO COMO PARÂMETRO
    $rs->bindValue(1, $id);
    $rs->execute();

    if (is_numeric($rs->rowCount()) && $rs->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

//------------------------------------------------------------------------------
function removeAcentos($string) {
    return preg_replace(array("/(ç)/", "/(Ç)/", "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "c C a A e E i I o O u U n N"), $string);
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA EXCLUIR UM DIRETÓRIO INFORMADO
function ExcluiDir($Dir) {
    if ($dd = opendir($Dir)) {
        while (false !== ($Arq = readdir($dd))) {
            if ($Arq != "." && $Arq != "..") {
                $Path = "$Dir/$Arq";
                if (is_dir($Path)) {
                    ExcluiDir($Path);
                } elseif (is_file($Path)) {
                    unlink($Path);
                }
            }
        }
        closedir($dd);
    }
    rmdir($Dir);
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA DETECTAR INJEÇÃO DE SQL
function antiSQL($campo, $adicionaBarras = false) {
    // Lista de padrões maliciosos comuns para detectar SQL Injection
    $padroesMaliciosos = "/(from| or | and |alter table|select|insert|delete|update|where|drop table|show tables|#|\*|--|\\\\)/i";

    // Verifica se há palavras maliciosas no campo
    if (preg_match($padroesMaliciosos, $campo)) {
        return false; // Possível SQL Injection detectado
    }

    // Limpa espaços em branco
    $campo = trim($campo);

    // Remove tags HTML e PHP
    $campo = strip_tags($campo);

    // Adiciona barras para escapar se necessário
    if ($adicionaBarras) {
        $campo = addslashes($campo);
    }

    return true; // Nenhum risco de SQL Injection detectado
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO QUE CALCULA A IDADE DE UMA PESSOA PELA DATA INFORMADA
function CalcularIdade($data) {

    // Separa em dia, mês e ano
    list($dia, $mes, $ano) = explode('/', $data);

    // Descobre que dia é hoje e retorna a unix timestamp
    $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    // Descobre a unix timestamp da data de nascimento do fulano
    $nascimento = mktime(0, 0, 0, $mes, $dia, $ano);

    // Depois apenas fazemos o cálculo já citado :)
    $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

    return $idade;
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO QUE RETORNA OS CAMPOS DE UM FORMULÁRIO PASSADO VIA POST
function retorna_campos($post) {
    $data = NULL;
    $fields = explode("&", $post);
    foreach ($fields as $field) {
        $field_key_value = explode("=", $field);
        $key = ($field_key_value[0]);
        $value = ($field_key_value[1]);
        $data[$key] = urldecode($value);
    }
    return $data;
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA REALIZAR QUALQUER TIPO DE PESQUISA NO BANCO E RETORNAR UM VALOR DESEJADO - PODE FAZER ATÉ 4 CONDIÇÕES
function pesquisa($retorno, $tabela, $campo1, $valor1, $campo2, $valor2, $campo3, $valor3, $campo4, $valor4, $operacao) {

    $db = Conexao::getInstance();

    $sql = $db->prepare("SELECT $retorno FROM $tabela WHERE $campo1 $campo2 $campo3 $campo4 $operacao");

    $sql->bindParam(1, $valor1);
    if ($valor2 != "")
        $sql->bindParam(2, $valor2);
    if ($valor3 != "")
        $sql->bindParam(3, $valor3);
    if ($valor4 != "")
        $sql->bindParam(4, $valor4);

    $sql->execute();

    if ($sql->rowCount() > 0) {
        $l = $sql->fetch(PDO::FETCH_BOTH);
        return $l[$retorno];
    }
    return "vazio"; //Retorna vazio caso não tenha encontrado o resultado desejado
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// NOME: VERIFICAR GRUPO NÍVEL
// DESCRIÇÃO: VERIFICA SE POSSUÍ ALGUM GRUPO COM NÍVEL DE ACESSO AO SISTEMA
function ver_grupo_nivel($nivel) {
    $db = Conexao::getInstance();

    $rs = false;

    $result = $db->prepare("SELECT sgu.usuario_id AS codigo   
                            FROM seg_grupo_nivel AS sgn 
                            LEFT JOIN seg_grupo_usuario AS sgu ON sgu.grupo_nivel_id = sgn.id 
                            WHERE sgu.usuario_id = ? AND sgn.nivel_id = ?");
    $result->bindValue(1, $_SESSION['id']);
    $result->bindValue(2, $nivel);
    $result->execute();
    while ($grupos = $result->fetch(PDO::FETCH_ASSOC)) {
        $rs = true;
    }

    return $rs;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// NOME: VERIFICAR NÍVEL
// DESCRIÇÃO: VERIFICA NÍVEL DE ACESSO AO SISTEMA
function ver_nivel($nivel, $redir = '') {
    if (isset($_SESSION['id'])) {
        $erro = false;
        if ($nivel == '')
            $erro = false;
        if (pesquisar('user_id', 'seg_permissoes', 'user_id', '=', $_SESSION['id'], ' AND nivel IN (SELECT sn.nivel FROM seg_niveis sn WHERE sn.nivel = ' . $nivel . ')')) {
            return true;
        } else {
            if ($redir == '')
                return false;
            else {
                msg('Você não possui permissão para acessar essa área.');
                url(PORTAL_URL . 'view/admin/dashboard');
            }
        }
    } else {
        msg('Você não possui permissão para acessar essa área.');
        url("" . PORTAL_URL . "logout");
    }
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// NOME: PESQUISAR TABELA
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO
function pesquisar($retorno, $tabela, $campo, $cond, $variavel, $add) {
    $db = Conexao::getInstance();

    $rs = $db->prepare("SELECT $retorno FROM $tabela WHERE $campo $cond ? $add");
    $rs->bindValue(1, $variavel);
    $rs->execute();
    $dados = $rs->fetch(PDO::FETCH_ASSOC);

    if ($rs->rowCount() > 0) {
        return $dados[$retorno];
    } else {
        return "";
    }
}

//------------------------------------------------------------------------------
// MÉTODO PARA REALIZAR UMA PESQUISA DE COMPARAÇÃO NO BANCO DE DADOS
function pesquisar2($retorno, $tabela, $campo, $cond, $variavel, $campo2, $cond2, $variavel2, $add) {
    $db = Conexao::getInstance();

    $rs = $db->prepare("SELECT $retorno FROM $tabela WHERE $campo $cond ? AND $campo2 $cond2 ? $add");
    $rs->bindValue(1, $variavel);
    $rs->bindValue(2, $variavel2);
    $rs->execute();
    $dados = $rs->fetch(PDO::FETCH_ASSOC);

    if ($rs->rowCount() > 0) {
        return $dados[$retorno];
    } else {
        return "";
    }
}

//------------------------------------------------------------------------------
// MÉTODO PARA REALIZAR UMA PESQUISA DE COMPARAÇÃO NO BANCO DE DADOS
function pesquisar3($retorno, $tabela, $campo, $cond, $variavel, $campo2, $cond2, $variavel2, $campo3, $cond3, $variavel3, $add) {
    $db = Conexao::getInstance();

    $rs = $db->prepare("SELECT $retorno FROM $tabela WHERE $campo $cond ? AND $campo2 $cond2 ? AND $campo3 $cond3 ? $add");
    $rs->bindValue(1, $variavel);
    $rs->bindValue(2, $variavel2);
    $rs->bindValue(3, $variavel3);
    $rs->execute();
    $dados = $rs->fetch(PDO::FETCH_ASSOC);

    if ($rs->rowCount() > 0) {
        return $dados[$retorno];
    } else {
        return "";
    }
}

//------------------------------------------------------------------------------
// MÉTODO PARA REALIZAR UMA PESQUISA DE COMPARAÇÃO NO BANCO DE DADOS
function pesquisar4($retorno, $tabela, $campo, $cond, $variavel, $campo2, $cond2, $variavel2, $add) {
    $db = Conexao::getInstance();

    $rs = $db->prepare("SELECT $retorno FROM $tabela WHERE $campo $cond '%$variavel,%' AND $campo2 $cond2 ? $add OR $campo $cond '%, $variavel%' AND $campo2 $cond2 ? $add");
    $rs->bindValue(1, $variavel2);
    $rs->bindValue(2, $variavel2);
    $rs->execute();
    $dados = $rs->fetch(PDO::FETCH_ASSOC);

    if ($rs->rowCount() > 0) {
        return $dados[$retorno];
    } else {
        return "";
    }
}

//------------------------------------------------------------------------------
// MÉTODO PARA REALIZAR UMA PESQUISA DE COMPARAÇÃO NO BANCO DE DADOS
function pesquisar5($retorno, $tabela, $campo, $cond, $variavel, $campo2, $cond2, $variavel2, $campo3, $cond3, $variavel3, $campo4, $cond4, $variavel4, $campo5, $cond5, $variavel5, $add) {
    $db = Conexao::getInstance();

    $rs = $db->prepare("SELECT $retorno FROM $tabela WHERE $campo $cond ? AND $campo2 $cond2 ? AND $campo3 $cond3 ? AND $campo4 $cond4 ? AND $campo5 $cond5 ? $add");
    $rs->bindValue(1, $variavel);
    $rs->bindValue(2, $variavel2);
    $rs->bindValue(3, $variavel3);
    $rs->bindValue(4, $variavel4);
    $rs->bindValue(5, $variavel5);
    $rs->execute();
    $dados = $rs->fetch(PDO::FETCH_ASSOC);

    if ($rs->rowCount() > 0) {
        return $dados[$retorno];
    } else {
        return "";
    }
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// NOME: ESTADO DO MUNICÍPIO
// DESCRIÇÃO: RETORNA O ESTADO DE UM MUNICÍPIO PELO ID
function estado_do_municipio($municipio) {
    if (is_numeric($municipio)) {
        $con = Conexao::getInstance();

        $rs = $con->prepare("SELECT estado FROM cidades WHERE id = ?");
        $rs->bindValue(1, $municipio);
        $rs->execute();
        $dados = $rs->fetch(PDO::FETCH_ASSOC);

        return $dados['estado'];
    } else {
        return "";
    }
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA REDIRECIONAR UM ENDEREÇO UTILIZANDO O WINDOW.LOCATION
function url($end) {
    echo "<script language='javaScript'>window.location.href='$end'</script>";
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA IMPRIMIR UMA MENSAGEM UTILIZANDO UM ALERT
function msg($msg) {
    echo "<script language='javaScript'>window.alert('" . $msg . "')</script>";
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA FORMATAR O VALOR PARA SALVAR NO BANCO
function real2float($num) {
    $num = str_replace(".", "", $num);
    $num = str_replace(",", ".", $num);
    return $num;
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA FORMATAR A HORA DE UMA DATA
function hora($hora) { // Deixa a hora 20:00
    $h = explode(':', $hora);
    return $h[0] . ':' . $h[1];
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA RETORNAR O DIA DA SEMANA
function getSemana($dia, $completo = 0) {
    switch ($dia) {
        case 1 :
            $r = 'Seg';
            $comp = 'Segunda-feira';
            break;
        case 2 :
            $r = 'Ter';
            $comp = 'Terça-feira';
            break;
        case 3 :
            $r = 'Qua';
            $comp = 'Quarta-feira';
            break;
        case 4 :
            $r = 'Qui';
            $comp = 'Quinta-feira';
            break;
        case 5 :
            $r = 'Sex';
            $comp = 'Sexta-feira';
            break;
        case 6 :
            $r = 'Sab';
            $comp = 'Sábado';
            break;
        case 7 :
            $r = 'Dom';
            $comp = 'Domingo';
            break;
    }
    if ($completo == 1)
        return $comp;
    else
        return $r;
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA RETORNAR A DATA POR EXTENSO
function dataExtenso($dt) {
    $da = explode('/', $dt);
    return $da[0] . ' de ' . getMes($da[1]) . ' de ' . $da[2];
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA FORMATAR O VALOR EM FORMATO DE DINHEIRO
function fdec($numero, $formato = NULL, $tmp = NULL) {
    switch ($formato) {
        case null:
            if ($numero != 0)
                $numero = number_format($numero, 2, ',', '.');
            else
                $numero = '0,00';
            break;
        case '%':
            if ($numero > 0)
                $numero = number_format((($numero / $tmp) * 100), 2, ',', '.') . '%';
            else
                $numero = '0%';
            break;
        case '-':
            $numero = "<font color='red'>" . fdec($numero) . "</font>";
            break;
    }
    return $numero;
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA VALIDAR O CNPJ INFORMADO
function valida_CNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
    // Valida tamanho
    if (strlen($cnpj) != 14)
        return false;
    // Valida primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
        return false;
    // Valida segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA VALIDAR O CPF INFORMADO
function valida_CPF($cpfx) {
    $cpf = "";
    $guard = "";

    for ($i = 0; ($i < 14); $i++) {
        if ($cpfx[$i] != '.' && $cpfx[$i] != '-') {
            $cpf .= $cpfx[$i];
            $guard = "$guard$cpfx[$i]";
        }
    }

    $cpf = $guard; // CPF SOMENTE COM OS NÚMEROS
// VERIFICA SE O CPF POSSUÍ NÚMEROS
    if (!is_numeric($cpf)) {
        $status = false;
    } else {
        if (($cpf == '11111111111') || ($cpf == '22222222222') || ($cpf == '33333333333') || ($cpf == '44444444444') || ($cpf == '55555555555') || ($cpf == '66666666666') || ($cpf == '77777777777') || ($cpf == '88888888888') || ($cpf == '99999999999') || ($cpf == '00000000000')) {
            $status = false;
        } else {
// PEGA O DIGITO VERIFIACADOR
            $dv_informado = substr($cpf, 9, 2);

            for ($i = 0; $i <= 8; $i++) {
                $digito[$i] = substr($cpf, $i, 1);
            }

// CALCULA O VALOR DO 10º DIGITO DE VERIFICAÇÂO
            $posicao = 10;
            $soma = 0;

            for ($i = 0; $i <= 8; $i++) {
                $soma = $soma + $digito[$i] * $posicao;
                $posicao = $posicao - 1;
            }

            $digito[9] = $soma % 11;

            if ($digito[9] < 2) {
                $digito[9] = 0;
            } else {
                $digito[9] = 11 - $digito[9];
            }

// CALCULA O VALOR DO 11º DIGITO DE VERIFICAÇÃO
            $posicao = 11;
            $soma = 0;

            for ($i = 0; $i <= 9; $i++) {
                $soma = $soma + $digito[$i] * $posicao;
                $posicao = $posicao - 1;
            }

            $digito[10] = $soma % 11;

            if ($digito[10] < 2) {
                $digito[10] = 0;
            } else {
                $digito[10] = 11 - $digito[10];
            }

// VERIFICA SE O DV CALCULADO É IGUAL AO INFORMADO
            $dv = $digito[9] * 10 + $digito[10];
            if ($dv != $dv_informado) {
                $status = false;
            } else
                $status = true;
        }
    }
    return $status;
}

//----------------------------------------------------------------------------------------------------------------------------
//RETORNA O VALOR POR EXTENSO
function valorPorExtenso($valor) {
    $singular = array("centavo", "litros", "mil", "milhõo", "bilhõo", "trilhõo", "quatrilhõo");
    $plural = array("centavos", "litros", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

    $z = 0;

    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    $rt = '';
    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++;
        elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    return($rt ? $rt : "zero");
}

//------------------------------------------------------------------------------
function hoje($data) {
    $dt = explode('/', $data);
    return getSemana(date("N", mktime(0, 0, 0, $dt[1], $dt[0], intval($dt[2]))), 1);
}

//------------------------------------------------------------------------------
function timeDiff($firstTime, $lastTime) {
    $firstTime = strtotime($firstTime);
    $lastTime = strtotime($lastTime);
    $timeDiff = $lastTime - $firstTime;
    return $timeDiff;
}

//------------------------------------------------------------------------------
function separa_hora($hora, $op) { // $op = minutos = 1; hora = 0
    $hr = explode(':', $hora);
    return $hr[$op];
}

//------------------------------------------------------------------------------
function dataExtensoTimeline($dt) {
    $da = explode('/', $dt);
    $diasemana = date("w", mktime(0, 0, 0, $da[1], $da[0], $da[2]));
    return getSemana2($diasemana, 0) . '  ' . getMes2($da[1]) . '  ' . $da[0] . ' ' . $da[2];
}

//----------------------------------------------------------------------------------------------------------------------------
//FUNÇÃO PARA RETORNAR O MÊS COMPLETO
function getMes($m) {
    switch ($m) {
        case 1 :
            $mes = "Janeiro";
            break;
        case 2 :
            $mes = "Fevereiro";
            break;
        case 3 :
            $mes = "Março";
            break;
        case 4 :
            $mes = "Abril";
            break;
        case 5 :
            $mes = "Maio";
            break;
        case 6 :
            $mes = "Junho";
            break;
        case 7 :
            $mes = "Julho";
            break;
        case 8 :
            $mes = "Agosto";
            break;
        case 9 :
            $mes = "Setembro";
            break;
        case 10 :
            $mes = "Outubro";
            break;
        case 11 :
            $mes = "Novembro";
            break;
        case 12 :
            $mes = "Dezembro";
            break;
    }
    return $mes;
}

//------------------------------------------------------------------------------
function getMes2($m) {
    $mes = "";
    switch ($m) {
        case 1 :
            $mes = "Jan";
            break;
        case 2 :
            $mes = "Fev";
            break;
        case 3 :
            $mes = "Mar";
            break;
        case 4 :
            $mes = "Abr";
            break;
        case 5 :
            $mes = "Mai";
            break;
        case 6 :
            $mes = "Jun";
            break;
        case 7 :
            $mes = "Jul";
            break;
        case 8 :
            $mes = "Ago";
            break;
        case 9 :
            $mes = "Set";
            break;
        case 10 :
            $mes = "Out";
            break;
        case 11 :
            $mes = "Nov";
            break;
        case 12 :
            $mes = "Dez";
            break;
    }
    return $mes;
}

//------------------------------------------------------------------------------
function getMes3($m) {
    switch ($m) {
        case 1 :
            $mes = "janeiro";
            break;
        case 2 :
            $mes = "fevereiro";
            break;
        case 3 :
            $mes = "marco";
            break;
        case 4 :
            $mes = "abril";
            break;
        case 5 :
            $mes = "maio";
            break;
        case 6 :
            $mes = "junho";
            break;
        case 7 :
            $mes = "julho";
            break;
        case 8 :
            $mes = "agosto";
            break;
        case 9 :
            $mes = "setembro";
            break;
        case 10 :
            $mes = "outubro";
            break;
        case 11 :
            $mes = "novembro";
            break;
        case 12 :
            $mes = "dezembro";
            break;
    }
    return $mes;
}

//------------------------------------------------------------------------------
function getMes4($m) {
    $mes = "";
    switch ($m) {
        case 1 :
            $mes = "JANEIRO";
            break;
        case 2 :
            $mes = "FEVEREIRO";
            break;
        case 3 :
            $mes = "MARÇO";
            break;
        case 4 :
            $mes = "ABRIL";
            break;
        case 5 :
            $mes = "MAIO";
            break;
        case 6 :
            $mes = "JUNHO";
            break;
        case 7 :
            $mes = "JULHO";
            break;
        case 8 :
            $mes = "AGOSTO";
            break;
        case 9 :
            $mes = "SETEMBRO";
            break;
        case 10 :
            $mes = "OUTUBRO";
            break;
        case 11 :
            $mes = "NOVEMBRO";
            break;
        case 12 :
            $mes = "DEZEMBRO";
            break;
    }
    return $mes;
}

//------------------------------------------------------------------------------
function ctexto($texto, $frase = 'pal') {
    switch ($frase) {
        case 'fra' : // Apenas a a primeira letra em maiusculo
            $texto = ucfirst(mb_strtolower($texto));
            break;
        case 'min' :
            $texto = mb_strtolower($texto);
            break;
        case 'mai' ://Todas as Letras maísuclo
            $texto = colocaAcentoMaiusculo((mb_strtoupper($texto)));
            break;
        case 'pal' : // Todas as palavras com a primeira em maiusculo
            $texto = ucwords(mb_strtolower($texto));
            break;
        case 'pri' : // Todos os primeiros caracteres de cada palavra em maiusuclo, menos as junções
            $texto = titleCase($texto);
            break;
    }
    return $texto;
}

//------------------------------------------------------------------------------
function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI")) {
    $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
    foreach ($delimiters as $dlnr => $delimiter) {
        $words = explode($delimiter, $string);
        $newwords = array();
        foreach ($words as $wordnr => $word) {
            if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtoupper($word, "UTF-8");
            } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                // check exceptions list for any words that should be in upper case
                $word = mb_strtolower($word, "UTF-8");
            } elseif (!in_array($word, $exceptions)) {
                // convert to uppercase (non-utf8 only)
                $word = ucfirst($word);
            }
            array_push($newwords, $word);
        }
        $string = join($delimiter, $newwords);
    } // foreach
    return $string;
}

//------------------------------------------------------------------------------
function colocaAcentoMaiusculo($texto) {
    $array1 = array(
        "á",
        "à",
        "â",
        "ã",
        "ä",
        "é",
        "è",
        "ê",
        "ë",
        "í",
        "ì",
        "î",
        "ï",
        "ó",
        "ò",
        "ô",
        "õ",
        "ö",
        "ú",
        "ù",
        "û",
        "ü",
        "ç"
    );

    $array2 = array(
        "Á",
        "À",
        "Â",
        "Ã",
        "Ä",
        "É",
        "È",
        "Ê",
        "Ë",
        "Í",
        "Ì",
        "Î",
        "Ï",
        "Ó",
        "Ò",
        "Ô",
        "Õ",
        "Ö",
        "Ú",
        "Ù",
        "Û",
        "Ü",
        "Ç"
    );
    return str_replace($array1, $array2, $texto);
}

//------------------------------------------------------------------------------
function retira_acentos($texto) {
    $array1 = array(
        "á",
        "à",
        "â",
        "ã",
        "ä",
        "é",
        "è",
        "ê",
        "ë",
        "í",
        "ì",
        "î",
        "ï",
        "ó",
        "ò",
        "ô",
        "õ",
        "ö",
        "ú",
        "ù",
        "û",
        "ü",
        "ç",
        "Á",
        "À",
        "Â",
        "Ã",
        "Ä",
        "É",
        "È",
        "Ê",
        "Ë",
        "Í",
        "Ì",
        "Î",
        "Ï",
        "Ó",
        "Ò",
        "Ô",
        "Õ",
        "Ö",
        "Ú",
        "Ù",
        "Û",
        "Ü",
        "Ç"
    );
    $array2 = array(
        "a",
        "a",
        "a",
        "a",
        "a",
        "e",
        "e",
        "e",
        "e",
        "i",
        "i",
        "i",
        "i",
        "o",
        "o",
        "o",
        "o",
        "o",
        "u",
        "u",
        "u",
        "u",
        "c",
        "A",
        "A",
        "A",
        "A",
        "A",
        "E",
        "E",
        "E",
        "E",
        "I",
        "I",
        "I",
        "I",
        "O",
        "O",
        "O",
        "O",
        "O",
        "U",
        "U",
        "U",
        "U",
        "C"
    );
    return str_replace($array1, $array2, $texto);
}

//------------------------------------------------------------------------------
// Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
function geraTimestamp($data) {
    $partes = explode('/', $data);
    return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
}

//------------------------------------------------------------------------------
function obterDataBRTimestamp($data) {
    if ($data != '') {
        $data = substr($data, 0, 10);
        $explodida = explode("-", $data);
        $dataIso = $explodida[2] . "/" . $explodida[1] . "/" . $explodida[0];
        return $dataIso;
    }
    return NULL;
}

//------------------------------------------------------------------------------
function convertDataBR2ISO($data) {
    if ($data == '')
        return false;
    $explodida = explode("/", $data);
    $dataIso = $explodida[2] . "-" . $explodida[1] . "-" . $explodida[0];
    return $dataIso;
}

//------------------------------------------------------------------------------
function obterHoraTimestamp($data) {
    return substr($data, 11, 5);
}

//------------------------------------------------------------------------------
function obterDiaTimestamp($data) {
    return substr($data, 8, 2);
}

//------------------------------------------------------------------------------
function obterMesTimestamp($data) {
    return substr($data, 5, 2);
}

//------------------------------------------------------------------------------
function obterAnoTimestamp($data) {
    return substr($data, 0, 4);
}

//------------------------------------------------------------------------------
function calculaDiferencaDatas($data_inicial, $data_final) {
    // Usa a função criada e pega o timestamp das duas datas:
    $time_inicial = geraTimestamp($data_inicial);
    $time_final = geraTimestamp($data_final);

    // Calcula a diferença de segundos entre as duas datas:
    $diferenca = $time_final - $time_inicial; // 19522800 segundos
    // Calcula a diferença de dias
    $dias = (int) floor($diferenca / (60 * 60 * 24)); // 225 dias
    // Exibe uma mensagem de resultado:
    // echo "A diferença entre as datas ".$data_inicial." e ".$data_final." é de <strong>".$dias."</strong> dias";
    return $dias;
}

//------------------------------------------------------------------------------
function apelidometadatos($variavel) {
    /*
     * $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ ,;:./';
     * $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr______';
     * //$string = ($string);
     * $string = strtr($string, ($a), $b); //substitui letras acentuadas por "normais"
     * $string = str_replace(" ","",$string); // retira espaco
     * $string = strtolower($string); // passa tudo para minusculo
     */
    $string = strtolower(ereg_replace("[^a-zA-Z0-9-]", "-", strtr((trim($variavel)), ("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"), "aaaaeeiooouuncAAAAEEIOOOUUNC-")));
    return ($string); // finaliza, gerando uma saída para a funcao
}

//------------------------------------------------------------------------------
function getExtensaoArquivo($extensao) {
    switch ($extensao) {
        case 'image/jpeg' :
            $ext = ".jpeg";
            break;
        case 'image/jpg' :
            $ext = ".jpg";
            break;
        case 'image/pjpeg' :
            $ext = ".pjpg";
            break;
        case 'image/JPEG' :
            $ext = ".JPEG";
            break;
        case 'image/gif' :
            $ext = ".gif";
            break;
        case 'image/png' :
            $ext = ".png";
            break;
        case 'video/webm' :
            $ext = ".webm";
            break;
        case 'video/mp4' :
            $ext = ".mp4";
            break;
        case 'video/flv' :
            $ext = ".flv";
            break;
        case 'video/webm' :
            $ext = ".webm";
            break;
        case 'audio/mp4' :
            $ext = ".acc";
            break;
        case 'audio/mpeg' :
            $ext = ".mp3";
            break;
        case 'audio/ogg' :
            $ext = ".ogg";
            break;
    }
    return $ext;
}

//------------------------------------------------------------------------------
function uploadArquivoPermitido($arquivo) {
    $tiposPermitidos = array(
        'image/gif',
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/png',
        'video/webm',
        'video/mp4',
        'video/ogv',
        'audio/mp3',
        'audio/mp4',
        'audio/mpeg',
        'audio/ogg'
    );
    if (array_search($arquivo, $tiposPermitidos) === false) {
        return false;
    } else {
        return true;
    } // end if
}

//------------------------------------------------------------------------------
function converteValorMonetario($valor) {
    $valor = str_replace('.', '', $valor);
    $valor = str_replace('.', '', $valor);
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return $valor;
}

//------------------------------------------------------------------------------
function valorMonetario($valor) {
    $valor = number_format($valor, 2, ',', '.');
    return $valor;
}

//------------------------------------------------------------------------------
// NOME: PESQUISAR TABELA
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO
function votos_apurados($regional_id, $zona) {

    $db = Conexao::getInstance();

    $qtd = 0;

    $result = $db->prepare("SELECT r.comparecimento 
                          FROM resultado r
                          LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                          WHERE s.regional_id = ? AND r.zona = ?");
    $result->bindValue(1, $regional_id);
    $result->bindValue(2, $zona);
    $result->execute();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $rs['comparecimento'];
    }

    return $qtd;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR TABELA
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO
function urnas_apuradas($regional_id, $zona) {

    $db = Conexao::getInstance();

    $qtd = 0;

    $result = $db->prepare("SELECT COUNT(r.id) AS qtd 
                          FROM resultado r
                          LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                          WHERE s.regional_id = ? AND r.zona = ?");
    $result->bindValue(1, $regional_id);
    $result->bindValue(2, $zona);
    $result->execute();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $rs['qtd'];
    }

    return $qtd;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR TABELA
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO
//function urnas_tipo($tipo) {
//
//    $db = Conexao::getInstance();
//
//    $qtd = 0;
//
//    if ($tipo == 1) {//RIO BRANCO
//        $result = $db->prepare("SELECT rc2022.id   
//                                 FROM 2024_resultados AS rc2022  
//                                 WHERE rc2022.municipio_nome = ?
//                                 GROUP BY rc2022.zona, rc2022.secao");
//        $result->bindValue(1, "RIO BRANCO");
//        $result->execute();
//    } else {//ESTADO DO ACRE GERAL
//        $result = $db->prepare("SELECT rc2022.id 
//                                FROM 2024_resultados AS rc2022  
//                                WHERE 1 
//                                GROUP BY rc2022.zona, rc2022.secao");
//        $result->execute();
//    }
//
//    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
//        $qtd++;
//    }
//
//    return $qtd;
//}
//-----------------------------------------------------------------------------------------------------
function secoes_tipo($tipo) {

    $db = Conexao::getInstance();

    $qtd = 0;

    if ($tipo == 1) {//RIO BRANCO
        $result = $db->prepare("SELECT COUNT(e.ID) AS qtd 
                                FROM 2024_secoes e 
                                INNER JOIN 2024_locais_votacao AS v ON v.ID = e.LOCAL_VOTACAO_ID 
                                WHERE 1 AND v.CD_MUNICIPIO = 1392 
                                GROUP BY e.NR_ZONA, e.NR_SECAO");
        $result->execute();
    } else {//ESTADO DO ACRE GERAL
        $result = $db->prepare("SELECT COUNT(e.ID) AS qtd 
                                FROM 2024_secoes e 
                                WHERE 1 
                                GROUP BY e.NR_ZONA, e.NR_SECAO");
        $result->execute();
    }

    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $qtd += $rs['qtd'];
    }

    return $qtd;
}

//-----------------------------------------------------------------------------------------------------
function urnas_tipo($municipio) {

    $db = Conexao::getInstance();

    $qtd = 0;

    if (is_numeric($municipio)) {//POR MUNICÍPIO
        $result = $db->prepare("SELECT COUNT(e.ID) AS qtd 
                                FROM 2024_secoes e 
                                INNER JOIN 2024_locais_votacao AS v ON v.ID = e.LOCAL_VOTACAO_ID 
                                WHERE e.TIPO = 'Principal' AND v.CD_MUNICIPIO = ? 
                                GROUP BY e.NR_ZONA, e.NR_SECAO");
        $result->bindValue(1, $municipio);
        $result->execute();
    } else {//ESTADO DO ACRE GERAL
        $result = $db->prepare("SELECT COUNT(e.ID) AS qtd 
                                FROM 2024_secoes e 
                                WHERE e.TIPO = 'Principal'
                                GROUP BY e.NR_ZONA, e.NR_SECAO");
        $result->execute();
    }

    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $qtd += $rs['qtd'];
    }

    return $qtd;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR TABELA
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO
function urnas_apuradas_municipio($municipio_tse) {

    $db = Conexao::getInstance();

    $qtd = 0;

    if (is_numeric($municipio_tse)) {//POR MUNICÍPIO
        $result = $db->prepare("SELECT rs.ID   
                                 FROM 2024_resultados AS rs  
                                 WHERE rs.COD_MUNICIPIO_TSE = ? AND rs.COD_CARGO = 'prefeito'
                                 GROUP BY rs.ZONA, rs.SECAO");
        $result->bindValue(1, $municipio_tse);
        $result->execute();
    } else {//ESTADO DO ACRE GERAL
        $result = $db->prepare("SELECT rs.ID 
                                FROM 2024_resultados AS rs 
                                WHERE rs.COD_CARGO = 'prefeito' 
                                GROUP BY rs.ZONA, rs.SECAO");
        $result->execute();
    }

    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $qtd++;
    }

    return $qtd;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR TABELA  PELO BAIRRO
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO PELO BAIRRO
function votos_apurados_bairro($bairro, $zona) {

    $db = Conexao::getInstance();

    $qtd = 0;

    $result = $db->prepare("SELECT SUM(r.comparecimento) AS comparecimento
                          FROM resultado r
                          LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                          WHERE s.municipio_id = 2 AND s.bairro = ? AND r.zona = ?
                          GROUP BY s.bairro");
    $result->bindValue(1, $bairro);
    $result->bindValue(2, $zona);
    $result->execute();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $rs['comparecimento'];
    }

    return $qtd;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR TABELA PELO BAIRRO
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO PELO BAIRRO
function urnas_apuradas_bairro($bairro, $zona) {

    $db = Conexao::getInstance();

    $qtd = 0;

    $result = $db->prepare("SELECT COUNT(r.id) AS qtd 
                          FROM resultado r
                          LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                          WHERE s.bairro = ? AND r.zona = ?");
    $result->bindValue(1, $bairro);
    $result->bindValue(2, $zona);
    $result->execute();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $qtd = $rs['qtd'];
    }

    return $qtd;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR TABELA
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR ALGUMA INFORMAÇÃO
function pesquisar_tabela($retorno, $tabela, $campo, $cond, $variavel, $add) {
    $db = Conexao::getInstance();

    $rs = $db->prepare("SELECT $retorno FROM $tabela WHERE $campo $cond ? $add");
    $rs->bindValue(1, $variavel);
    $rs->execute();
    $dados = $rs->fetch(PDO::FETCH_ASSOC);

    return $dados[$retorno];
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR VOTOS BRANCOS
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR VOTOS BRANCOS
function pesquisar_brancos($municipio_id) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT r.brancos AS votos
                                 FROM resultado_candidato AS r21t 
                                 LEFT JOIN resultado AS r ON r.id = r21t.resultado_id 
                                 LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao 
                                 WHERE e.MUNICIPIO = ?");

    $result2->bindValue(1, $municipio_id);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR VOTOS NULOS
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR VOTOS NULOS
function pesquisar_nulos($municipio_id) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT r.nulos AS votos
                                 FROM resultado_candidato AS r21t 
                                 LEFT JOIN resultado AS r ON r.id = r21t.resultado_id 
                                 LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao 
                                 WHERE e.MUNICIPIO = ?");

    $result2->bindValue(1, $municipio_id);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAS APTOS POR ZONA E LOCAL
// DESCRIÇÃO: RETORNA TODAS OS APTOS DA ZONA E LOCAL INFORMADOS
function carregar_aptos($zona, $local) {

    $qtd = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT s.QT_ELEITOR_AGREGADO     
                            FROM 2024_locais_votacao AS e
                            INNER JOIN 2024_secoes AS s ON s.LOCAL_VOTACAO_ID = e.ID  
                            WHERE e.NR_ZONA = ? AND e.ID = ? AND s.TIPO = 'Principal'");
    $result2->bindValue(1, $zona);
    $result2->bindValue(2, $local);
    $result2->execute();

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $qtd += $rs2['QT_ELEITOR_AGREGADO'];
    }

    return $qtd;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAS SEÇÕES POR ZONA E LOCAL
// DESCRIÇÃO: RETORNA TODAS AS SEÇÕES DA ZONA E LOCAL INFORMADOS
function carregar_secoes($zona, $local) {

    $contador = 1;
    $secoes = "";

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT e.NR_SECAO    
                            FROM eleicoes_localidades_2022 e
                            WHERE e.ZONA = ? AND e.LOCAL_VOTACAO = ?  
                            ORDER BY e.NR_SECAO ASC");
    $result2->bindValue(1, $zona);
    $result2->bindValue(2, $local);
    $result2->execute();

    $qtd = $result2->rowCount();

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {

        if ($contador < $qtd) {
            $secoes .= $rs2['NR_SECAO'] . ", ";
        } else {
            $secoes .= $rs2['NR_SECAO'];
        }

        $contador++;
    }

    return $secoes;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS PELA ZONA e SEÇÃO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS PELA ZONA E SEÇÃO
function buscar_votos_zona_local($candidato, $zona, $local) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT SUM(rc.votos) AS votos    
                            FROM resultado r
                            LEFT JOIN resultado_candidato AS rc ON rc.resultado_id = r.id
                            LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao 
                            WHERE e.ZONA = ? AND e.LOCAL_VOTACAO = ? AND rc.candidato_id = ?  
                            GROUP BY rc.id 
                            ORDER BY rc.id ASC");
    $result2->bindValue(1, $zona);
    $result2->bindValue(2, $local);
    $result2->bindValue(3, $candidato);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS PELA ZONA e SEÇÃO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS PELA ZONA E SEÇÃO
function buscar_votos_zona($zona, $secao, $candidato_id, $municipio_id) {

    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT rc.candidato_id, rc.votos   
                                                  FROM secao s
                                                  LEFT JOIN zona AS z ON z.id = s.zona_id
                                                  LEFT JOIN resultado AS r ON r.zona = z.numero AND r.secao = s.secao_numero 
                                                  LEFT JOIN resultado_candidato AS rc ON rc.resultado_id = r.id
                                                  WHERE rc.candidato_id = ? AND z.id = ? AND s.id = ? AND s.municipio_id = ? 
                                                  GROUP BY s.bairro
                                                  ORDER BY s.bairro ASC");
    $result2->bindValue(1, $candidato_id);
    $result2->bindValue(2, $zona);
    $result2->bindValue(3, $secao);
    $result2->bindValue(4, $municipio_id);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos = $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR AS SEÇÕES DO BAIRRO
// DESCRIÇÃO: RETORNA TODAS AS SEÇÕES DO BAIRRO
function buscar_secao($bairro, $municipio_id) {

    $secoes = 0;

    $db = Conexao::getInstance();

    $result = $db->prepare("SELECT s.secao_numero, z.numero AS zona_numero   
          FROM secao s
          LEFT JOIN zona AS z ON z.id = s.zona_id 
          WHERE s.bairro = ? AND s.municipio_id = ?");
    $result->bindValue(1, $bairro);
    $result->bindValue(2, $municipio_id);
    $result->execute();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $result2 = $db->prepare("SELECT secao FROM resultado
                                       WHERE secao = ? AND zona = ?");
        $result2->bindValue(1, $rs['secao_numero']);
        $result2->bindValue(2, $rs['zona_numero']);
        $result2->execute();
        while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
            //$secoes .= "".$rs2['secao'].", ";
            $secoes++;
        }
    }

    //return substr_replace($secoes, '', -2);
    return $secoes;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS PELO BAIRRO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS PELO BAIRRO
function buscar_votos($bairro, $candidato_id) {

    $votos = 0;

    $db = Conexao::getInstance();

    $result = $db->prepare("SELECT s.secao_numero, z.numero AS zona_numero   
                            FROM secao s
                            LEFT JOIN zona AS z ON z.id = s.zona_id 
                            WHERE s.bairro = ?");
    $result->bindValue(1, $bairro);
    $result->execute();
    while ($rs = $result->fetch(PDO::FETCH_ASSOC)) {
        $result2 = $db->prepare("SELECT (SELECT rc.votos FROM resultado_candidato rc WHERE rc.resultado_id = r.id AND rc.candidato_id = ?) AS votos
                                 FROM resultado r
                                 WHERE r.secao = ? AND r.zona = ?");
        $result2->bindValue(1, $candidato_id);
        $result2->bindValue(2, $rs['secao_numero']);
        $result2->bindValue(3, $rs['zona_numero']);
        $result2->execute();
        while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
            $votos += $rs2['votos'];
        }
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO NULOS
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO
// TIPO: 1 = POR MUNICÍPIO, 0 = GERAL
function buscar_votos_candidato_nulos($municipio_id, $tipo) {

    $votos = 0;

    $db = Conexao::getInstance();

    if ($tipo == 1) {
        $result2 = $db->prepare("SELECT r.nulos AS votos 
                                 FROM resultado_candidato AS r21t 
                                 LEFT JOIN resultado AS r ON r.id = r21t.resultado_id 
                                 LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao 
                                 WHERE e.MUNICIPIO = ?");
        $result2->bindValue(1, $municipio_id);
        $result2->execute();
    } else {
        $result2 = $db->prepare("SELECT r.nulos AS votos 
                                 FROM resultado_candidato AS r21t 
                                 LEFT JOIN resultado AS r ON r.id = r21t.resultado_id 
                                 LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao 
                                 WHERE 1");
        $result2->execute();
    }

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO BRANCOS
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO
// TIPO: 1 = POR MUNICÍPIO, 0 = GERAL
function buscar_votos_candidato_brancos($municipio_id, $tipo) {

    $votos = 0;

    $db = Conexao::getInstance();

    if ($tipo == 1) {
        $result2 = $db->prepare("SELECT r.brancos AS votos 
                                 FROM resultado_candidato AS r21t 
                                 LEFT JOIN resultado AS r ON r.id = r21t.resultado_id 
                                 LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao 
                                 WHERE e.MUNICIPIO = ?");
        $result2->bindValue(1, $municipio_id);
        $result2->execute();
    } else {
        $result2 = $db->prepare("SELECT r.brancos AS votos 
                                 FROM resultado_candidato AS r21t 
                                 LEFT JOIN resultado AS r ON r.id = r21t.resultado_id 
                                 LEFT JOIN eleicoes_localidades_2022 AS e ON e.ZONA = r.zona AND e.NR_SECAO = r.secao 
                                 WHERE 1");
        $result2->execute();
    }

    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO PELA REGIONAL
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO PELA REGIONAL
function buscar_votos_candidato_regioanl($candidato_id, $regional_id) {

    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT rc.votos
                            FROM resultado_candidato rc
                            LEFT JOIN resultado AS r ON r.id = rc.resultado_id 
                            LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                            LEFT JOIN zona AS z ON z.id = s.zona_id AND z.numero = r.zona
                            WHERE rc.candidato_id = ? AND s.regional_id IN (?) AND z.numero = r.zona AND r.secao = s.secao_numero
                            GROUP BY rc.id");
    $result2->bindValue(1, $candidato_id);
    $result2->bindValue(2, $regional_id);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR OS VOTOS DO CANDIDATO PELO BAIRRO
// DESCRIÇÃO: RETORNA TODAS OS VOTOS DO CANDIDATO PELO BAIRRO
function buscar_votos_candidato_bairro($candidato_id, $bairro) {

    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT SUM(rc.votos) AS votos
                            FROM resultado_candidato rc
                            LEFT JOIN resultado AS r ON r.id = rc.resultado_id 
                            LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                            LEFT JOIN zona AS z ON z.id = s.zona_id AND z.numero = r.zona
                            WHERE s.municipio_id = 2 AND rc.candidato_id = ? AND s.bairro IN (?) AND z.numero = r.zona AND r.secao = s.secao_numero
                            GROUP BY rc.id");
    $result2->bindValue(1, $candidato_id);
    $result2->bindValue(2, str_replace("_", " ", $bairro));
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// NOME: PESQUISAR VOTOS NULOS PELA REGIONAL
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR VOTOS NULOS PELA REGIONAL
function pesquisar_nulos_regioanl($regional_id) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT r.nulos AS votos
                                 FROM resultado r
                                 LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                                 LEFT JOIN zona AS z ON z.id = s.zona_id AND z.numero = r.zona
                                 WHERE s.regional_id = ? AND z.numero = r.zona AND r.secao = s.secao_numero");
    $result2->bindValue(1, $regional_id);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR VOTOS BRANCOS PELA REGIONAL
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR VOTOS BRANCOS PELA REGIONAL
function pesquisar_brancos_regioanl($regional_id) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT r.brancos AS votos
                                 FROM resultado r
                                 LEFT JOIN secao AS s ON s.secao_numero = r.secao
                                 LEFT JOIN zona AS z ON z.id = s.zona_id AND z.numero = r.zona
                                 WHERE s.regional_id = ? AND z.numero = r.zona AND r.secao = s.secao_numero");
    $result2->bindValue(1, $regional_id);
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR VOTOS NULOS PELO BAIRRO
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR VOTOS NULOS PELO BAIRRO
function pesquisar_nulos_bairro($bairro) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT SUM(r.nulos) AS votos
                                 FROM resultado r
                                 LEFT JOIN secao AS s ON s.secao_numero = r.secao 
                                 LEFT JOIN zona AS z ON z.id = s.zona_id AND z.numero = r.zona
                                 WHERE s.municipio_id = 2 AND s.bairro = ? AND z.numero = r.zona AND r.secao = s.secao_numero");
    $result2->bindValue(1, str_replace("_", " ", $bairro));
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
// NOME: PESQUISAR VOTOS BRANCOS PELO BAIRRO
// DESCRIÇÃO: PESQUISAR NO BANCO DE DADOS POR VOTOS BRANCOS PELO BAIRRO
function pesquisar_brancos_bairro($bairro) {
    $votos = 0;

    $db = Conexao::getInstance();

    $result2 = $db->prepare("SELECT SUM(r.brancos) AS votos
                                 FROM resultado r
                                 LEFT JOIN secao AS s ON s.secao_numero = r.secao
                                 LEFT JOIN zona AS z ON z.id = s.zona_id AND z.numero = r.zona
                                 WHERE s.municipio_id = 2 AND s.bairro = ? AND z.numero = r.zona AND r.secao = s.secao_numero");
    $result2->bindValue(1, str_replace("_", " ", $bairro));
    $result2->execute();
    while ($rs2 = $result2->fetch(PDO::FETCH_ASSOC)) {
        $votos += $rs2['votos'];
    }

    return $votos;
}

// -----------------------------------------------------------------------------
?>