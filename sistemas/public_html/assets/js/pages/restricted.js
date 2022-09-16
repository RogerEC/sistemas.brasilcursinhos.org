$(function(){

    let originalValue = [];
    let originalCpf = null;
    let forms = [];

    $("form").each(function(){
        if($(this).attr('id') !== 'form-check-participant')
        forms.push($(this).validate({
                rules : {
                    "name":{required: true, maxlength: 64},
                    "local":{required: true, maxlength: 64},
                    "cup":{required: true, maxlength: 64},
                    "start":{required: true},
                    "final":{required: true},
                    "cpf":{required: true, cpfBR: true, remote:{
                        url:"/verificar-cpf-participante",
                        method:"post",
                        data: {
                            'original-cpf': function(){
                                console.log('original: '+originalCpf);
                                return originalCpf;
                            }
                        }
                    }},
                    "participant-id":{
                        required:true,
                        number:true,
                        min:101,
                        max:999
                    }
                },
                messages:{
                    "cpf":{remote:'Esse CPF já foi cadastrado.'}
                },
                errorClass: "invalid-feedback",
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass("is-invalid").addClass("is-valid");
                },
            })
        );
    });

    $("#form-check-participant").validate({
        rules : {
            "cpf":{required: true, cpfBR: true}
        },
        errorClass: "invalid-feedback",
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("is-invalid").addClass("is-valid");
        },
    });

    $(".cpf").mask("999.999.999-99", {autoclear: false});
    $("#participant-id").mask("999", {autoclear: false});

    $(".btn-edit-save").on('click', function(event){
        event.preventDefault();
        if($(this).children().text() === 'edit') {
            $(".btn-delete-cancel").each(function(){
                if($(this).children().text() === 'cancel') {
                    $(this).click();
                }
            });
            $("#btn-cancel-new-insertion").click();
            $(".form-"+$(this).val()+" input:not([type='hidden'])").each(function(){
                $(this).prop('disabled', false).prop('readonly', false);
                if($(this).attr('name') === 'cpf') {
                    originalCpf = $(this).val();
                }
                originalValue.push($(this).val());
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
            $("#deletion-id").val($("#id-"+$(this).val()).val());
            $("#btn-modal").click();
        } else {
            originalCpf = null;
            forms.forEach(function(element){
                element.resetForm();
            });
            $("form input").removeClass('is-valid').removeClass('is-invalid');
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

    $("#btn-new-insertion").on("click", function(event){
        event.preventDefault();
        originalCpf = null;
        $(".btn-delete-cancel").each(function(){
            if($(this).children().text() === 'cancel') {
                $(this).click();
            }
        });
        $("#new-insertion-field").removeClass('d-none');
        $(this).addClass('d-none');
    });

    $("#btn-cancel-new-insertion").on("click", function(){
        forms.forEach(function(element){
            element.resetForm();
        });
        $("form input").removeClass('is-valid').removeClass('is-invalid');
        $("#btn-new-insertion").removeClass('d-none');
        $("#new-insertion-field").addClass('d-none');
    });
});