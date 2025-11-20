//------------------------------------------------------------------------------------------------------
// Busca dinâmica
function limpar_notificacao() {
    swal({
        title: "Limpar todas as notificações?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger m-l-10",
        confirmButtonText: "Sim, limpar!",
        cancelButtonText: "Não"
    }).then(function () {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/notificacoes",
            type: "POST",
            data: {action: 'limpar'},
            success: onSuccessLimparNoti,
            error: onError
        });
    });
}
//------------------------------------------------------------------------------------------------------
// Busca dinâmica
function buscar(obj) {

    var query = $(obj).val();

    if (query.length > 2) {
        projetouniversal.util.getjson({
            url: PORTAL_URL + "dao/busca",
            type: "GET",
            data: {query: query},
            success: onSuccessBusca,
            error: onError
        });
    } else {
        $('#search-results').hide();
    }
}

//------------------------------------------------------------------------------------------------------
function loadNotificacoes() {
    projetouniversal.util.getjson({
        url: PORTAL_URL + "dao/notificacoes",
        type: "GET",
        success: onSuccessLoadNoti,
        error: onError
    });
}
//------------------------------------------------------------------------------------------------------
function onSuccessLoadNoti(obj) {
    $('#noti-badge').text(obj.count);
    $('#noti-count').text('(' + obj.count + ')');
    var html = '';
    obj.itens.forEach(function (item) {
        html += `
            <a href="${item.link}" class="dropdown-item">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="material-symbols-outlined text-${item.color}">${item.icon}</i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p>${item.mensagem}</p>
                        <span class="fs-13">${item.data}</span>
                    </div>
                </div>
            </a>
        `;
    });
    $('#noti-list').html(html);
}
//------------------------------------------------------------------------------------------------------
function onSuccessLimparNoti(obj) {
    swal("Sucesso!", obj.retorno, "success").then(function () {
        loadNotificacoes();
    });
}
//------------------------------------------------------------------------------------------------------
function onSuccessBusca(obj) {
    var html = '';
    obj.resultados.forEach(function (res) {
        html += `
            <a href="${res.link}" class="dropdown-item">
                ${res.nome} (${res.tipo})
            </a>
        `;
    });
    $('#search-results').html(html).show();
}
//------------------------------------------------------------------------------------------------------
function onError(args) {
    swal("Error!", args.retorno, "error");
}
//------------------------------------------------------------------------------------------------------