$(function(){

    let originalValue = [];
    let originalCpf = null;
    let forms = [];

    $("form").validate({
        rules : {
            "name":{required: true, maxlength: 64},
            "cpf":{required: true, cpfBR: true},
            "email":{required: true, maxlength: 128, email: true},
            "cup":{required: true},
            "role":{required: true}
        },
        messages:{
            "name":{required:"É obrigatório informar o seu nome completo!"},
            "cpf":{required:"É obrigatório informar o seu CPF!"},
            "email":{required:"É obrigatório informar o seu endereço de e-mail!"},
            "cup":{required:"É obrigatório informar o seu CUP!"},
            "role":{required:"É obrigatório informar o seu cargo no CUP!"}
        },
        errorClass: "invalid-feedback",
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("is-invalid").addClass("is-valid");
        },
    });

    $("#cpf").mask("999.999.999-99", {autoclear: false});
    
});