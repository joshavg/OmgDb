$(document).ready(function () {
    var modalConfirm = $('#modal-confirm');
    var modalConfirmConfirm = $('#modal-confirm-confirm');
    var modalConfirmTitle = $('#modal-confirm-title');

    $('.btn-confirm-href').click(function (event) {
        event.preventDefault();

        var href = $(this).attr('href');
        modalConfirmConfirm.click(function () {
            window.location.href = href;
        });
        modalConfirm.on('hide.bs.modal', function() {
            modalConfirmConfirm.off('click');
        });
        modalConfirmTitle.text($(this).attr('data-confirm-title'));

        modalConfirm.modal('show');
        modalConfirmConfirm.focus();
    });
});
