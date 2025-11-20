// JavaScript Document
// FUNÇÃO RESPONSÁVEL DE CONECTAR A UMA PAGINA EXTERNA NO NOSSO CASO A BUSCA_NOME.PHP
// E RETORNAR OS RESULTADOS

function ajax(url)
{
    req = null;
    // Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = processReqChange;
        req.open("GET", url, true);
        req.send(null);
        // Procura por uma versão ActiveX (IE)
    } else if (window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {

            req.onreadystatechange = processReqChange;
            req.open("GET", url, true);

            req.send();
        }
    }
}

function processReqChange()
{
    if (req.readyState == 4) {

        if (req.status == 200) {

            document.getElementById('ajax_pesquisa').innerHTML = "";//Acrecentado 09-06-2015
            document.getElementById('ajax_pesquisa').innerHTML = req.responseText;

        } else {
            alert("Houve um problema ao obter os dados:" + req.statusText);
        }
    }
}

function ajax2(url)
{
    req2 = null;
    // Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req2 = new XMLHttpRequest();
        req2.onreadystatechange = processReqChange2;
        req2.open("GET", url, true);
        req2.send(null);
        // Procura por uma versão ActiveX (IE)
    } else if (window.ActiveXObject) {
        req2 = new ActiveXObject("Microsoft.XMLHTTP");
        if (req2) {

            req2.onreadystatechange = processReqChange2;
            req2.open("GET", url, true);

            req2.send();
        }
    }
}

function processReqChange2()
{
    if (req2.readyState == 4) {

        if (req2.status == 200) {

            document.getElementById('tbody_complementares').innerHTML = "";//Acrecentado 09-06-2015
            document.getElementById('tbody_complementares').innerHTML = req2.responseText;

        } else {
            alert("Houve um problema ao obter os dados:" + req2.statusText);
        }
    }
}

function ajax3(url)
{
    req3 = null;
    // Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req3 = new XMLHttpRequest();
        req3.onreadystatechange = processReqChange3;
        req3.open("GET", url, true);
        req3.send(null);
        // Procura por uma versão ActiveX (IE)
    } else if (window.ActiveXObject) {
        req3 = new ActiveXObject("Microsoft.XMLHTTP");
        if (req3) {

            req3.onreadystatechange = processReqChange3;
            req3.open("GET", url, true);

            req3.send();
        }
    }
}

function processReqChange3()
{
    if (req3.readyState == 4) {

        if (req3.status == 200) {

            document.getElementById('tbody_decretos').innerHTML = "";//Acrecentado 09-06-2015
            document.getElementById('tbody_decretos').innerHTML = req3.responseText;

        } else {
            alert("Houve um problema ao obter os dados:" + req3.statusText);
        }
    }
}

function ajax4(url)
{
    req4 = null;
    // Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req4 = new XMLHttpRequest();
        req4.onreadystatechange = processReqChange4;
        req4.open("GET", url, true);
        req4.send(null);
        // Procura por uma versão ActiveX (IE)
    } else if (window.ActiveXObject) {
        req4 = new ActiveXObject("Microsoft.XMLHTTP");
        if (req4) {

            req4.onreadystatechange = processReqChange4;
            req4.open("GET", url, true);

            req4.send();
        }
    }
}

function processReqChange4()
{
    if (req4.readyState == 4) {

        if (req4.status == 200) {

            document.getElementById('tbody_emendas').innerHTML = "";//Acrecentado 09-06-2015
            document.getElementById('tbody_emendas').innerHTML = req4.responseText;

        } else {
            alert("Houve um problema ao obter os dados:" + req4.statusText);
        }
    }
}

function ajax5(url)
{
    req5 = null;
    // Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req5 = new XMLHttpRequest();
        req5.onreadystatechange = processReqChange5;
        req5.open("GET", url, true);
        req5.send(null);
        // Procura por uma versão ActiveX (IE)
    } else if (window.ActiveXObject) {
        req5 = new ActiveXObject("Microsoft.XMLHTTP");
        if (req5) {

            req5.onreadystatechange = processReqChange5;
            req5.open("GET", url, true);

            req5.send();
        }
    }
}

function processReqChange5()
{
    if (req5.readyState == 4) {

        if (req5.status == 200) {

            document.getElementById('tbody_todos').innerHTML = "";//Acrecentado 09-06-2015
            document.getElementById('tbody_todos').innerHTML = req5.responseText;

        } else {
            alert("Houve um problema ao obter os dados:" + req5.statusText);
        }
    }
}

function ajax6(url)
{
    req6 = null;
    // Procura por um objeto nativo (Mozilla/Safari)
    if (window.XMLHttpRequest) {
        req6 = new XMLHttpRequest();
        req6.onreadystatechange = processReqChange6;
        req6.open("GET", url, true);
        req6.send(null);
        // Procura por uma versão ActiveX (IE)
    } else if (window.ActiveXObject) {
        req6 = new ActiveXObject("Microsoft.XMLHTTP");
        if (req6) {

            req6.onreadystatechange = processReqChange6;
            req6.open("GET", url, true);

            req6.send();
        }
    }
}

function processReqChange6()
{
    if (req6.readyState == 4) {

        if (req6.status == 200) {

            document.getElementById('tbody_covid19').innerHTML = "";//Acrecentado 09-06-2015
            document.getElementById('tbody_covid19').innerHTML = req6.responseText;

        } else {
            alert("Houve um problema ao obter os dados:" + req6.statusText);
        }
    }
}