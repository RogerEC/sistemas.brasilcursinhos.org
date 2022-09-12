$(function(){
    let originalValue = [];
    $(".btn-edit-save").on('click', function(event){
        event.preventDefault();
        if($(this).children().text() === 'edit') {
            $(".form-"+$(this).val()+" input:not([type='hidden'])").each(function(){
                $(this).prop('disabled', false).prop('readonly', false);
                originalValue.push($(this).val());
                console.log($(this))
            });
            $(this).children().text('save');
            $("#btn-delete-cancel-"+$(this).val()).children().text('cancel');
        } else {
            $(".form-"+$(this).val()).submit();
        }
    });

    $(".btn-delete-cancel").on('click', function(event){
        event.preventDefault();
        if($(this).children().text() === 'delete') {
            $("#deletion-id").val($(this).val());
            $("#btn-modal").click();
        } else {
            $(".form-"+$(this).val()+" input:not([type='hidden'])").each(function(){
                $(this).prop('disabled', true).prop('readonly', true);
                $(this).val(originalValue[0]);
                originalValue.shift();
            });
            $(this).removeClass('btn-edit').addClass('btn-save');
            $(this).removeClass('btn-secondary-bc').addClass('btn-success');
            $(this).children().text('delete');
            $("#btn-edit-save-"+$(this).val()).children().text('edit');
        }
    });

    $("#btn-confirm-deletion").on('click', function(event){
        event.preventDefault();
        $("#deletion-form").submit();
    });

    $(".btn-cancel-deletion").on('click', function(event){
        event.preventDefault();
        $("#deletion-id").val('');
    })
});