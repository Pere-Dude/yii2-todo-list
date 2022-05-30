$(function () {
    $('#modalButton').click(function () {
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('value'));
    });

    $('.not-button').click(function () {
        $('#modalUpdate').modal('show')
            .find('#modalContent1')
            .load($(this).attr('value'));
    });
});