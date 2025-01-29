$(function(){

    $("#btn-confirm-interview").on("click tap", function(event) {
        event.preventDefault();
        $("#confirmation").addClass("d-none");
        $("#loading").removeClass("d-none");
        $("#form-confirm-interview").submit();
    });

});