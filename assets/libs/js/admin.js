
$(function () {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
    }, function (start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
});

$('.collapse').on('shown.bs.collapse', function () {
    $(this).parent().find(".fa-angle-down").removeClass("fa-angle-down").addClass("fa-angle-up");
}).on('hidden.bs.collapse', function () {
    $(this).parent().find(".fa-angle-up").removeClass("fa-angle-up").addClass("fa-angle-down");
});
$('.panel-heading a').click(function () {
    $('.panel-heading').removeClass('active');
    //If the panel was open and would be closed by this click, do not active it
    if (!$(this).closest('.panel').find('.panel-collapse').hasClass('in'))
        $(this).parents('.panel-heading').addClass('active');
});


$('#my-select, #pre-selected-options').multiSelect()


$('#callbacks').multiSelect({
    afterSelect: function (values) {
        alert("Select value: " + values);
    },
    afterDeselect: function (values) {
        alert("Deselect value: " + values);
    }
});


$('#keep-order').multiSelect({
    keepOrder: true
});


$('#public-methods').multiSelect();

$('#select-all').click(function () {
    $('#public-methods').multiSelect('select_all');
    return false;
});

$('#deselect-all').click(function () {
    $('#public-methods').multiSelect('deselect_all');
    return false;
});

$('#select-100').click(function () {
    $('#public-methods').multiSelect('select', ['elem_0', 'elem_1', 'elem_99']);
    return false;
});

$('#deselect-100').click(function () {
    $('#public-methods').multiSelect('deselect', ['elem_0', 'elem_1', 'elem_99']);
    return false;
});

$('#refresh').on('click', function () {
    $('#public-methods').multiSelect('refresh');
    return false;
});

$('#add-option').on('click', function () {
    $('#public-methods').multiSelect('addOption', {
        value: 42,
        text: 'test 42',
        index: 0
    });
    return false;
});


$('#optgroup').multiSelect({
    selectableOptgroup: true
});


$('#disabled-attribute').multiSelect();


$('#custom-headers').multiSelect({
    selectableHeader: "<div class='custom-header'>Selectable items</div>",
    selectionHeader: "<div class='custom-header'>Selection items</div>",
    selectableFooter: "<div class='custom-header'>Selectable footer</div>",
    selectionFooter: "<div class='custom-header'>Selection footer</div>"
});


$('#form').parsley();
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
    'use strict';
    window.addEventListener('load', function () {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
