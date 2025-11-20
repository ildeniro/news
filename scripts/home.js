//------------------------------------------------------------------------------------------------------
//Cadastrar
$(document).ready(function () {

    $('select').each(function () {
        $(this).select2();
        $('.select2').attr('style', 'width: 100%');
    });

    $('#form_contato').submit(function () {

        $("#submit").attr("disabled", true);

        var nome = $('#nome').val();
        var email = $('#email').val();
        var telefone = $('#telefone').val();
        var assunto = $('#assunto').val();
        var mensagem = $('#mensagem').val();

        $.post(PORTAL_URL + "dao/contact_handler", {nome: nome, email: email, telefone: telefone, assunto: assunto, mensagem: mensagem}, function (data) {
            if (isNaN(data)) {
                swal({
                    title: "Formulário de Contato",
                    html: data,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonColor: "#8CD4F5",
                    confirmButtonText: "OK",
                    closeOnConfirm: false
                });
                $("#submit").attr("disabled", false);
                return false;
            } else {
                swal({
                    title: "Formulário de Contato",
                    text: "Mensagem enviada com sucesso!<br/>Responderemos em breve.",
                    type: "success",
                    confirmButtonClass: "btn btn-success",
                    confirmButtonText: "Ok"
                }).then(function () {
                    postToURL(PORTAL_URL);
                });
            }
        }
        , "html");
        return false;

    });

});
//------------------------------------------------------------------------------

// Funções da Home (similar a events.js)
document.addEventListener('DOMContentLoaded', function () {
    // Init Swiper pra Equipe (se houver slides)
    if (document.getElementById('team-swiper')) {
        const teamSwiper = new Swiper('#team-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            navigation: {
                nextEl: '.next',
                prevEl: '.prev',
            },
            breakpoints: {
                768: {slidesPerView: 2},
                1024: {slidesPerView: 3}
            }
        });
    }

    // Bind Form Contato (AJAX sem reload)
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            projetouniversal.util.getjson({
                url: this.dataset.action,
                type: 'POST',
                data: formData,
                success: function (obj) {
                    if (obj.msg === 'success') {
                        swal('Sucesso!', obj.retorno, 'success').then(function () {
                            contactForm.reset();
                        });
                    } else {
                        swal('Erro!', obj.retorno, 'error');
                    }
                },
                error: function () {
                    swal('Erro!', 'Falha na conexão. Tente novamente.', 'error');
                }
            });
        });
    }

    // Smooth Scroll pros Menu Links (reforça Bootstrap spy)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        });
    });
});