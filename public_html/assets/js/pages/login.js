$(function(){
    let form = $("form").validate({
        rules : {
            "user":{required: true, minlength: 5, maxlength: 128},
            "password":{required: true, minlength: 6, maxlength: 32},
            "cpf":{required: true, cpfBR: true}
        },
        messages:{
            "user":{required:"É obrigatório informar o ser usuário!",
            minlength: "Informe pelo menos 5 caracteres!", maxlength: "Informe menos de 128 caracteres!"},
            "password":{required: "É obrigatório informar a sua senha!",
            minlength: "Informe pelo menos 6 caracteres!", maxlength: "Informe menos de 32 caracteres!"},
            "cpf":{required: "É obrigatório informar o seu CPF!",
            cpfBR: "O número de CPF informado é inválido!"}
        },
        errorClass: "invalid-feedback",
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("is-invalid").addClass("is-valid");
        },
        errorPlacement:function(error, element) {
            if(element.attr("id") === 'password') {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element)
            }
        }
    });
    $("#cpf").mask("999.999.999-99", {autoclear: false});
    $("#user").on('keydown', function(){
        if($("#userValidated").val() === "true") {
            form.element("#user");
        }
    });
    $("#password").on('keydown', function(){
        if($("#passwordValidated").val() === "true") {
            form.element("#password");
        }
    });
    $("#cpf").on('keydown', function(){
        if($("#cpfValidated").val() === "true") {
            form.element("#cpf");
        }
    });
    $("#btn-password-visibility").on("click", function(event){
        event.preventDefault();
        if($(this).children().text() === 'visibility') {
            $(this).children().text('visibility_off');
            $("#password").prop("type", "text");
            $("#btn-password-visibility").prop("title", "Esconder senha");
            $("#btn-password-visibility").attr("aria-label", "Esconder senha");
        } else {
            $(this).children().text('visibility');
            $("#password").prop("type", "password");
            $("#btn-password-visibility").prop("title", "Mostrar senha");
            $("#btn-password-visibility").attr("aria-label", "Mostrar senha");
        }
    });
    
})
